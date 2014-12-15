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

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Handler\SubscribingHandlerInterface;

class ArrayCollectionHandler implements SubscribingHandlerInterface
{
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

    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($collection->toArray(), $type, $context);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $propertyMetadata   = $context->getMetadataStack()->offsetGet(0);
        $reflectionProperty = $propertyMetadata->reflection;
        $currentCollection  = $reflectionProperty->getValue($visitor->getCurrentObject());

        $type['name'] = 'array';

        $items = $visitor->visitArray($data, $type, $context);

        // If there is a current collection on the object, we need to return the same collection instance,
        // or we end up with 2 collections in the database because the current collection isn't cleared.

        // Warning: If your entity has orphanRemoval and you move it from one collection to another doctrine will mark
        // it for removal!!

        if ($currentCollection instanceof Collection) {
            // Remove deleted items
            $existingIdList = array();

            foreach ($items as $item) {
                $existingIdList[] = $item->getId();
            }

            $currentCollection->forAll(function($key, $value) use ($existingIdList, &$currentCollection) {
                if (in_array($value->getId(), $existingIdList, true) === false) {
                    $currentCollection->remove($value);
                }

                return true;
            });

            // Add new items
            $existingIdList = array();

            foreach ($currentCollection as $item) {
                $existingIdList[] = $item->getId();
            }

            foreach ($items as $item) {
                if (in_array($item->getId(), $existingIdList, true) === false) {
                    $currentCollection->add($item);
                }
            }

            return $currentCollection;
        }

        return new ArrayCollection($items);
    }
}
