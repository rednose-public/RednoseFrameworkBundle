<?php

namespace Libbit\FrameworkBundle\Model;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * A LiBBiT framework user
 */
class User extends BaseUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Tells if the the given user has super admin role.
     *
     * @return Boolean
     */
    public function isAdmin()
    {
        return $this->hasRole(static::ROLE_ADMIN);
    }

    /**
     * Sets the admin status
     *
     * @param Boolean $boolean
     */
    public function setAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_ADMIN);
        } else {
            $this->removeRole(static::ROLE_ADMIN);
        }
    }
}
