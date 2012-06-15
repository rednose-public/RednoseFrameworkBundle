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
            $user->setEmail($username);
            $user->setPassword($this->randomPassword());
            $this->updateUser($user);
        }

        return parent::loadUserByUsername($username);
    }
    
    public function randomPassword($length = 9)
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
