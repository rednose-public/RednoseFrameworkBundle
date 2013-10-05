<?php

namespace Rednose\FrameworkBundle\Notification;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Rednose\FrameworkBundle\Model\NotificationInterface;

class NotificationFactory
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(NotificationInterface $object)
    {
        $type = $object->getType();

        return new $type($object, $this->container);
    }
}
