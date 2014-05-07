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
        $propertyMetadata     = $context->getMetadataStack()->offsetGet(0);
        $reflectionProperty   = $propertyMetadata->reflection;
        $currentPropertyValue = $reflectionProperty->getValue($visitor->getCurrentObject());

        // See above.
        $type['name'] = 'array';

        $collection = new ArrayCollection($visitor->visitArray($data, $type, $context));

        if (!$currentPropertyValue instanceof Collection) {
            return $collection;
        }

        return $this->transferCollection($collection, $currentPropertyValue);
    }

    protected function transferCollection(Collection $source, Collection $destination)
    {
        // Add new objects.
        foreach ($source as $object) {
            if ($destination->contains($object) === false) {
                $destination->add($object);
            }
        }

        // Remove deleted objects.
        foreach ($destination as $object) {
            if ($source->contains($object) === false) {
                $destination->removeElement($object);
            }
        }

        return $destination;
    }
}
