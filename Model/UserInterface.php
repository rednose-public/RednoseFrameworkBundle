<?php

namespace Rednose\FrameworkBundle\Model;

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use FOS\UserBundle\Model\GroupableInterface;

/**
 * A RedNose framework user
 */
interface UserInterface extends BaseUserInterface, GroupableInterface, EquatableInterface
{
    /**
     * Gets the realname (full name)
     *
     * @return string
     */
    public function getRealname();

    /**
     * Returns the realname if set, otherwise uses
     * the username
     *
     * @return string
     */
    public function getBestname();

    public function getCredentialsExpireAt();

    /**
     * Get the realname (full name)
     *
     * @param string $realName
     */
    public function setRealname($realName);

    /**
     * Tells if the the given user has super admin role.
     *
     * @return boolean
     */
    public function isAdmin();

    /**
     * Symfony\Component\Security\Core\User\EquatableInterface::isEqualTo()
     */
    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user);

    /**
     * Sets the admin status
     *
     * @param boolean $boolean
     */
    public function setAdmin($boolean);

    public function getExpiresAt();

    /**
     * Gets the username
     *
     * Will automatically return the username in lowercase for
     * framework compatibility.
     *
     * if forceLowercase is set to false it will return the
     * username as it has been set by setUsername().
     *
     * @param $forceLowercase
     *
     * @return string
     */
    public function getUsername($forceLowercase = true);
}
