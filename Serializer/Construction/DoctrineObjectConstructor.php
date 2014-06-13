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

    /**
     * Constructor.
     *
     * @param ManagerRegistry            $managerRegistry     Manager registry
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor
     */
    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor)
    {
        $this->managerRegistry     = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
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

        $id = $context->attributes->get('id');

        if ($id->isDefined()) {
            $object = $objectManager->getRepository($metadata->name)->findOneById($id->get());

            if ($object) {
                $object = $objectManager->find($metadata->name, array('id' => $object->getId()));

                $objectManager->initializeObject($object);

                $context->attributes->set('id', null);

                return $object;
            }
        }

        $class = new \ReflectionClass($metadata->name);

        // TODO: Remove
        if ($class->implementsInterface('Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface') && $context->getFormat() === 'xml') {
            // Try to find an id within the attributes that could hold a foreignId
            foreach ($data->attributes() as $k => $v) {
                if ($k === 'id') {
                    $object = $objectManager->getRepository($metadata->name)->findOneByForeignId($v);

                    if ($object) {
                        $object = $objectManager->find($metadata->name, array('id' => $object->getId()));

                        $objectManager->initializeObject($object);

                        return $object;
                    }
                }
            }
        }

        return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
//        // Locate possible ClassMetadata
//        $classMetadataFactory = $objectManager->getMetadataFactory();
//
//        if ($classMetadataFactory->isTransient($metadata->name)) {
//            // No ClassMetadata found, proceed with normal deserialization
//            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
//        }

        // Managed entity, check for proxy load
        // if (!is_array($data)) {
        //     // Single identifier, load proxy
        //     return $objectManager->getReference($metadata->name, $data);
        // }

        // Fallback to default constructor if missing identifier(s)
//        $classMetadata  = $objectManager->getClassMetadata($metadata->name);
//        $identifierList = array();
//
//        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
//            if ( ! array_key_exists($name, $data)) {
//                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
//            }
//
//            $identifierList[$name] = $data[$name];
//        }
//
//        // Entity update, load it from database
//        $object = $objectManager->find($metadata->name, $identifierList);
//
//        $objectManager->initializeObject($object);
//
//        return $object;
    }
}
