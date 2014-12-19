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

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class SerializerListener implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_deserialize', 'method' => 'onPostDeserialize'),
        );
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        // Changes must be applied at the end of the serialization process!
        // Only commit the changes if this event is fired for the parent entity (depth 0).
        if ($event->getContext()->getDepth() === 0) {
            $queue = $this->handler->getCollectionsTransactionQueue();

            if ($queue !== false) {
                $this->commitArrayCollectionChanges($queue);
            }
        }
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
            if ($item !== false) {
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
            call_user_func(array($entity, 'set' . $map), $owner);
        } else {
            throw new \Exception('No required mappedBy property present on ' . get_class($entity) . ' (' . $propertyName . ')');
        }

        // Add to the collection
        return $collection->add($entity);
    }

    private function getMappingFromEntity($owner, $propertyName) {
        $objectManager   = $this->managerRegistry->getManagerForClass(get_class($owner));
        $metadataFactory = $objectManager->getMetadataFactory();

        return $metadataFactory
            ->getMetadataFor(get_class($owner))
            ->getAssociationMapping($propertyName);
    }
}