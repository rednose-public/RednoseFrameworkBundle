<?php

namespace Rednose\FrameworkBundle\Security;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

/**
 * Creates a user if it doesn't exist already.
 */
class UserManager extends BaseUserManager
{
    /**
     * Return all users, sorted
     *
     * @param $field
     * @param $ascending = true
     */
    public function findUsersSorted($ascending = true)
    {
        if ($ascending) {
            $direction = 'asc';
        } else {
            $direction = 'desc';
        }

        return $this->repository->findBy(array(), array('username' => $direction));
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if ($this->findUserBy(array('username' => $username)) === null) {
            $user = $this->createUser();
            $user->setUserName($username);
            $user->setEnabled(true);
            $user->setEmail($username);
            $user->setPassword($this->randomPassword());
            $this->updateUser($user);
        }

        return parent::loadUserByUsername($username);
    }

    protected function randomPassword($length = 9)
    {
        $vowels = 'aeuy';

        $consonants = 'bdghjmnpqrstvz';
        $consonants .= '@#$%';

        $password = '';
        $alt = time() % 2;

        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }

        return $password;
    }
}
