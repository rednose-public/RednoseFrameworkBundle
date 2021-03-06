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

use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Changes the current locale based on user preferences.
 */
class LocaleListener
{
    /**
     * @var TokenStorage
     */
    protected $storage;

    /**
     * Constructor.
     *
     * @param TokenStorage $storage
     */
    public function __construct(TokenStorage $storage)
    {
        $this->storage = $storage;
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
        if ($this->storage->getToken() === null) {
            return;
        }

        $user    = $this->storage->getToken()->getUser();
        $request = $event->getRequest();

        if (!$user instanceof UserInterface || $user->getLocale() === null || $user->getLocale() === $request->getLocale()) {
            return;
        }

        $request->setLocale($user->getLocale());
        $request->getSession()->set('_locale', $user->getLocale());
    }
}
