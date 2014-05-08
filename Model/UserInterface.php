<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Sets the admin status
     *
     * @param boolean $boolean
     */
    public function setAdmin($boolean);

    public function getExpiresAt();

    /**
     * Gets the preferred locale for this user.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Sets the preferred locale for this user.
     *
     * @param string $locale
     */
    public function setLocale($locale);
}
