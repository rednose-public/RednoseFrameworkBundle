<?php

namespace Rednose\FrameworkBundle\Context;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionContext
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param $username
     *
     * @return array
     */
    public function get($username)
    {
        $session = [];

        foreach ($this->session->all() as $key => $sessionItem) {
            if (is_object($sessionItem)) {
                $session[$key] = $sessionItem;
            }
        }

        return [
            'session' => (object) $session,
            'user' => (object) [
                'username' => $username
            ]
        ];
    }
}