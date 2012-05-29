<?php

namespace Libbit\FrameworkBundle\Security;

use FOS\UserBundle\Entity\UserManager as BaseUserManager;

/**
 * Creates a user if it doesn't exist already.
 */
class UserManager extends BaseUserManager
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if ($this->findUserBy(array('username' => $username)) === null) {
            $user = $this->createUser();
            $user->setUserName($username);
            $user->setEnabled(true);

            $this->updateUser($user);
        }

        return parent::loadUserByUsername($username);
    }
}
