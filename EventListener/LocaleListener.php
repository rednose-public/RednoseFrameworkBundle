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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\SecurityContext;
use Rednose\FrameworkBundle\Model\UserInterface;

/**
 * Changes the current locale based on user preferences.
 */
class LocaleListener
{
    /**
     * @var SecurityContext
     */
    protected $context;

    /**
     * Constructor.
     *
     * @param SecurityContext $context
     */
    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }

    /**
     * Sets the locale for the current user.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // Anonymous has no locale state
        if ($this->context->getToken() === null) {
            return;
        }

        $user    = $this->context->getToken()->getUser();
        $request = $event->getRequest();

        if (!$user instanceof UserInterface || $user->getLocale() === null || $user->getLocale() === $request->getLocale()) {
            return;
        }

        $request->setLocale($user->getLocale());
        $request->getSession()->set('_locale', $user->getLocale());
    }
}
