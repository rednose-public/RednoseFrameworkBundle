<?php

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class AssignOrganizationListener implements EventSubscriberInterface
{
    /**
     * @var OrganizationManagerInterface
     */
    protected $manager;

    /**
     * @var ExpressionLanguage
     */
    protected $language;

    /**
     * @param OrganizationManagerInterface $manager
     * @param ExpressionLanguage           $language
     */
    public function __construct(OrganizationManagerInterface $manager, $language = null)
    {
        $this->language = $language ?: new ExpressionLanguage();
        $this->manager = $manager;
    }

    /**
     * @param UserEvent $event
     */
    public function onUserAutoCreate(UserEvent $event)
    {
        $user = $event->getUser();

        foreach ($this->manager->findOrganizations() as $organization) {
            if ($this->shouldAssign($user, $organization)) {
                $user->setOrganization($organization);

                return;
            }
        }
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

    /**
     * @param UserInterface         $user
     * @param OrganizationInterface $organization
     *
     * @return bool
     */
    protected function shouldAssign(UserInterface $user, OrganizationInterface $organization)
    {
        foreach ($organization->getConditions() as $condition) {
            try {
                if ($this->language->evaluate($condition, $this->createContext($user))) {
                    return true;
                }
            } catch (\Exception $e) {}
        }

        return false;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     */
    protected function createContext(UserInterface $user)
    {
        return array('user' => (object) array(
            'username' => $user->getUsername(false),
            'email' => $user->getEmail(),
        ));
    }
}