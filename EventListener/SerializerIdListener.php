<?php

namespace Rednose\FrameworkBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class SerializerIdListener implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param Reader        $reader
     * @param EntityManager $em
     */
    public function __construct(Reader $reader, EntityManager $em)
    {
        $this->reader = $reader;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
            array('event' => 'serializer.post_deserialize', 'method' => 'onPostDeserialize'),
        );
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $type = $event->getType();

        if (!class_exists($type['name'])) {
            return;
        }

        $class = new \ReflectionClass($type['name']);
        $object = $event->getObject();

        foreach ($class->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, 'Rednose\FrameworkBundle\Serializer\Annotation\SerializerId');

            if ($annotation) {
                $property->setAccessible(true);
                $property->setValue($object, $this->toProperty($annotation->type, $annotation->property,$property->getValue($object)));
            }
        }
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $type = $event->getType();

        if (!class_exists($type['name'])) {
            return;
        }

        $class = new \ReflectionClass($type['name']);
        $object = $event->getObject();

        foreach ($class->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, 'Rednose\FrameworkBundle\Serializer\Annotation\SerializerId');

            if ($annotation) {
                $property->setAccessible(true);
                $property->setValue($object, $this->toEntity($annotation->type, $annotation->property, $property->getValue($object)));
            }
        }
    }

    /**
     * @param string $type
     * @param string $property
     * @param object $value
     *
     * @return mixed
     */
    protected function toProperty($type, $property, $value)
    {
        if ($value === null) {
            return null;
        }

        $class = new \ReflectionClass($type);

        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($value);
    }

    /**
     * @param string $type
     * @param string $property
     * @param mixed  $value
     *
     * @return object
     */
    protected function toEntity($type, $property, $value)
    {
        if ($value === null) {
            return null;
        }

        return $this->em->getRepository($type)->findOneBy(array($property => $value));
    }
}
