<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Notification;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Rednose\FrameworkBundle\Model\NotificationInterface;

abstract class AbstractNotificationType implements NotificationTypeInterface
{
    protected $container;

    protected $object;

    public function __construct(NotificationInterface $object, ContainerInterface $container)
    {
        $this->object    = $object;
        $this->container = $container;
    }

    public function getMessage()
    {
        return $this->getTemplate();
    }

    abstract public function getTemplate();
}
