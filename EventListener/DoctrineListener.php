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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Event\GroupEvent;
use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Fires events for the User and Group entities.
 */
class DoctrineListener
{
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $dispatcher Fires the events.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $event = new UserEvent($entity);

            $this->dispatcher->dispatch(Events::USER_POST_PERSIST, $event);

            return;
        }

        if ($entity instanceof Group) {
            $event = new GroupEvent($entity);

            $this->dispatcher->dispatch(Events::GROUP_POST_PERSIST, $event);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $event = new UserEvent($entity);

            $this->dispatcher->dispatch(Events::USER_POST_UPDATE, $event);

            return;
        }

        if ($entity instanceof Group) {
            $event = new GroupEvent($entity);

            $this->dispatcher->dispatch(Events::GROUP_POST_UPDATE, $event);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $event = new UserEvent($entity);

            $this->dispatcher->dispatch(Events::USER_POST_REMOVE, $event);

            return;
        }

        if ($entity instanceof Group) {
            $event = new GroupEvent($entity);

            $this->dispatcher->dispatch(Events::GROUP_POST_REMOVE, $event);
        }
    }
}
