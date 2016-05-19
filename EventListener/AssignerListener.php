<?php

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Assigner\GroupAssigner;
use Rednose\FrameworkBundle\Assigner\OrganizationAssigner;
use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssignerListener implements EventSubscriberInterface
{
    /**
     * @var OrganizationAssigner
     */
    protected $organizationAssigner;

    /**
     * @var GroupAssigner
     */
    protected $groupAssigner;

    /**
     * @param OrganizationAssigner $organizationAssigner
     * @param GroupAssigner        $groupAssigner
     */
    public function __construct(OrganizationAssigner $organizationAssigner, GroupAssigner $groupAssigner)
    {
        $this->organizationAssigner = $organizationAssigner;
        $this->groupAssigner = $groupAssigner;
    }

    /**
     * @param UserEvent $event
     */
    public function handleOrganizationAssign(UserEvent $event)
    {
        $user = $event->getUser();

        if ($user->isStatic()) {
            return;
        }

        $this->organizationAssigner->assign($user);
    }

    /**
     * @param UserEvent $event
     */
    public function handleGroupAssign(UserEvent $event)
    {
        $user = $event->getUser();

        if ($user->isStatic()) {
            return;
        }

        $this->groupAssigner->assign($user);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [Events::USER_LOGIN => [
            ['handleOrganizationAssign', 128],
            ['handleGroupAssign', 0]
        ]];
    }
}