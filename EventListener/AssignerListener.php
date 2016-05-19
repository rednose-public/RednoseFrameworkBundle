<?php

namespace Rednose\FrameworkBundle\EventListener;

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
     * @param OrganizationAssigner $organizationAssigner
     */
    public function __construct(OrganizationAssigner $organizationAssigner)
    {
        $this->organizationAssigner = $organizationAssigner;
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
        // Check static
//        $this->organizationAssigner->assign($event->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [Events::USER_LOGIN => [
            ['handleOrganizationAssign', 128],
//            ['handleGroupAssign', 0]
        ]];
    }
}