<?php

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssignOrganizationListener implements EventSubscriberInterface
{
    /**
     * @var OrganizationManagerInterface
     */
    protected $manager;

    /**
     * @param OrganizationManagerInterface $manager
     */
    public function __construct(OrganizationManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onUserAutoCreate(UserEvent $event)
    {
        $organization = $this->manager->findOrganizationBy(array('name' => 'Rijkshuisstijl'));

        $event->getUser()->setOrganization($organization);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(Events::USER_AUTO_CREATE => 'onUserAutoCreate');
    }
}