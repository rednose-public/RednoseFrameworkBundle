<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Serializer\Handler\ArrayCollectionHandler;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class SerializerListener
{
    /**
     * @var {ArrayCollectionHandler}
     */
    protected $handler = null;

    /**
     * @var {Registry}
     */
    protected $managerRegistry = null;

    public function __construct(ArrayCollectionHandler $handler, ManagerRegistry $managerRegistry)
    {
        $this->handler = $handler;
        $this->managerRegistry = $managerRegistry;
    }

    public function onPostDeserialize()
    {
        $queue = $this->handler->getCollectionsTransactionQueue();

        if ($queue !== false) {
            $this->commitArrayCollectionChanges($queue);
        }

        $this->handler->cleanTransactionState();
    }

    private function commitArrayCollectionChanges($queue)
    {
        // Move items from one collection to another collection
        foreach ($queue->movedItems as $item) {
            $map = $item['oldCollection']->getMapping();

            if (isset($map['orphanRemoval']) && $map['orphanRemoval']) {
                throw new \Exception(
                    'Unable to move entity ' . get_class($item['entity']) . ' to a different collection. ' .
                    'The orphanRemoval attribute is set on the OneToMany relation definition in ' . get_class($item['oldCollection']->getOwner())
                );
            }

            $item['oldCollection']->removeElement($item['entity']);

            $this->addEntityBidirectional($item['entity'], $item['owner'], $item['name'], $item['newCollection']);
        }

        // Add new items to a collection
        foreach ($queue->addedItems as $item) {
            if ($item !== false && !isset($item['skip'])) {
                $this->addEntityBidirectional($item['entity'], $item['owner'], $item['name'], $item['collection']);
            }
        }

        // Schedule removed items for deletion
        foreach ($queue->removedItems as $item) {
            if ($item !== false) {
                $objectManager = $this->managerRegistry->getManagerForClass(get_class($item['entity']));

                $item['collection']->removeElement($item['entity']);

                $ouw = $objectManager->getUnitOfWork();
                $ouw->scheduleForDelete($item['entity']);
            }
        }
    }

    private function addEntityBidirectional($entity, $owner, $propertyName, Collection $collection)
    {
        if ($collection instanceOf ArrayCollection) {
            // If this is a ArrayCollection there is no mapping available.
            $map = $this->getMappingFromEntity($owner, $propertyName);
        } else {
            $map = $collection->getMapping();
        }

        if (isset($map['mappedBy'])) {
            $map = $map['mappedBy'];
        } else {
            $map = false;
        }

        // Set bi-directional property based on mappedBy ORM attribute
        if ($map) {
            $reflect = new \ReflectionProperty(get_class($entity), $map);
            $reflect->setAccessible(true);
            $reflect->setValue($entity, $owner);
        } else {
            throw new \Exception('No required mappedBy property present on ' . get_class($entity) . ' (' . $propertyName . ')');
        }

        // Add to the collection
        if ($collection->indexOf($entity) === false) {
            return $collection->add($entity);
        }

        return false;
    }

    private function getMappingFromEntity($owner, $propertyName)
    {
        static $cache = array();

        if (isset($cache[get_class($owner) . '-' . $propertyName])) {
            return $cache[get_class($owner) . '-' . $propertyName];
        }

        $objectManager   = $this->managerRegistry->getManagerForClass(get_class($owner));
        $metadataFactory = $objectManager->getMetadataFactory();

        $map = $metadataFactory
            ->getMetadataFor(get_class($owner))
            ->getAssociationMapping($propertyName);

        $cache[get_class($owner) . '-' . $propertyName] = $map;

        return $map;
    }
}
