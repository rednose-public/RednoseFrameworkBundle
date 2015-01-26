<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Serializer\Construction;

use Rednose\FrameworkBundle\EventListener\SerializerListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Construction\ObjectConstructorInterface;

/**
 * Doctrine object constructor for new (or existing) objects during deserialization.
 */
class DoctrineObjectConstructor implements ObjectConstructorInterface
{
    private $managerRegistry;
    private $fallbackConstructor;
    private $listener;

    /**
     * Constructor.
     *
     * @param ManagerRegistry            $managerRegistry     Manager registry
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor
     */
    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor, SerializerListener $listener)
    {
        $this->managerRegistry     = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
        $this->listener            = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        // Locate possible ObjectManager
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if (!$objectManager) {
            // No ObjectManager found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        if (
            count($metadata->postDeserializeMethods) > 0 && ($metadata->postDeserializeMethods[0] instanceOf MethodMetadata) === false ||
            count($metadata->postDeserializeMethods) === 0
        ) {
            // Register a callback method to make sure the changes are applied to the object collections before
            // other serializer events are fired.
            $metadataMethod = new MethodMetadata(
                'Rednose\FrameworkBundle\Serializer\Construction\DoctrineObjectConstructor', 'onPostDeserialize'
            );

            $metadataMethod->setListener($this->listener);

            // Prepend to the beginning of the postDeserializeMethod array
            array_unshift($metadata->postDeserializeMethods, $metadataMethod);
        }

        $id = $context->attributes->get('id');

        // Id is supplied by the DeserializationContext
        if ($id->isDefined()) {
            $object = $objectManager->getRepository($metadata->name)->findOneById($id->get());

            if ($object) {
                $object = $objectManager->find($metadata->name, array('id' => $object->getId()));

                $objectManager->initializeObject($object);

                $context->attributes->set('id', null);

                return $object;
            }
        }

        // Id is null (new entity) or supplied by the entity (update existing entity)
        $classMetadata = $objectManager->getClassMetadata($metadata->name);
        $identifierList = array();

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            if (array_key_exists($name, $data) === false) {
                // No identifier present
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            $identifierList[$name] = $data[$name];
        }

        // Entity update, load it from database
        $object = $objectManager->find($metadata->name, $identifierList);

        // Entity update requested on a deleted object, create a new one instead.
        // This can happen if there is a undo action applied after the entity was saved.
        if (!$object) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        $objectManager->initializeObject($object);

        return $object;
    }

    private function onPostDeserialize() {
        // Dummy
    }
}
