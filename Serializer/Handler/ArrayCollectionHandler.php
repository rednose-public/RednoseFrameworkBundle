<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Serializer\Handler;

use stdClass;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

class ArrayCollectionHandler implements SubscribingHandlerInterface
{
    /**
     * @var {array}
     */
    protected $addedItems = array();

    /**
     * @var {array}
     */
    protected $removedItems = array();

    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        $collectionTypes = array(
            'ArrayCollection',
            'Doctrine\Common\Collections\ArrayCollection',
            'Doctrine\ORM\PersistentCollection',
            'Doctrine\ODM\MongoDB\PersistentCollection',
            'Doctrine\ODM\PHPCR\PersistentCollection',
        );

        foreach ($collectionTypes as $type) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serializeCollection',
                );

                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                );
            }
        }

        return $methods;
    }

    public function getCollectionsTransactionQueue()
    {
        $movedItems = array();

        foreach ($this->addedItems as $objectHash => $value) {
            if (isset($this->removedItems[$objectHash])) {
                $movedItems[] = array(
                    'name'          => $this->addedItems[$objectHash]['name'],
                    'oldCollection' => $this->removedItems[$objectHash]['collection'],
                    'newCollection' => $this->addedItems[$objectHash]['collection'],
                    'entity'        => $this->addedItems[$objectHash]['entity'],
                    'owner'         => $this->addedItems[$objectHash]['owner'],
                );

                $this->addedItems[$objectHash] = false;
                $this->removedItems[$objectHash] = false;
            }
        }

        if (count($this->addedItems) > 0 || count($this->removedItems) > 0 || count($movedItems) > 0) {
            $return = new stdClass();

            $return->addedItems   = $this->addedItems;
            $return->removedItems = $this->removedItems;
            $return->movedItems   = $movedItems;

            return $return;
        }

        return false;
    }

    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($collection->toArray(), $type, $context);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $currentObject      = $visitor->getCurrentObject();
        $propertyMetadata   = $context->getMetadataStack()->offsetGet(0);
        $reflectionProperty = $propertyMetadata->reflection;
        $currentCollection  = $reflectionProperty->getValue($currentObject);

        $type['name'] = 'array';

        $items = $visitor->visitArray($data, $type, $context);

        // If there is a current collection on the object, we need to return the same collection doctrine managed instance.
        if (($currentCollection instanceof Collection) === false) {
            // This is a freshly created entity
            $currentCollection = new ArrayCollection();
        }

        // Index new items
        $existingIdList = array();

        foreach ($currentCollection as $item) {
            $existingIdList[] = $item->getId();
        }

        foreach ($items as $item) {
            if (in_array($item->getId(), $existingIdList, true) === false) {
                $this->addedItems[spl_object_hash($item) . get_class($item)] = array(
                    'name' => $propertyMetadata->name, 'entity' => $item, 'collection' => $currentCollection, 'owner' => $currentObject
                );
            }
        }

        // Index deleted items
        $existingIdList = array();

        foreach ($items as $item) {
            $existingIdList[] = $item->getId();
        }

        $self = $this;
        $currentCollection->forAll(function($key, $item) use ($existingIdList, $currentCollection, $propertyMetadata, $currentObject, &$self) {
            if (in_array($item->getId(), $existingIdList, true) === false) {
                $self->removedItems[spl_object_hash($item) . get_class($item)] = array(
                    'name' => $propertyMetadata->name, 'entity' => $item, 'collection' => $currentCollection, 'owner' => $currentObject
                );
            }

            return true;
        });

        return $currentCollection;
    }
}
