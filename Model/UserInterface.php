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

use FOS\UserBundle\Model\GroupableInterface;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * A RedNose framework user
 */
interface UserInterface extends BaseUserInterface, GroupableInterface, EquatableInterface
{
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

    /**
     * @return \DateTime
     */
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

    /**
     * Set the static status
     *
     * @param boolean $static
     */
    public function setStatic($static = false);

    /**
     * Tells if this is a static user
     */
    public function isStatic();

    /**
     * @return \DateTime
     */
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

    /**
     * Gets the preferred organization for this user.
     *
     * @return OrganizationInterface
     */
    public function getOrganization();

    /**
     * Get a list of organizations available to this user.
     *
     * @return OrganizationInterface[]
     */
    public function getAvailableOrganizations();

    /**
     * Gets the name of the preferred organization for this user.
     *
     * @return string
     */
    public function getOrganizationName();

    /**
     * Sets the preferred organization for this user.
     *
     * @param OrganizationInterface $organization
     */
    public function setOrganization($organization);

    /**
     * Set the RoleCollections for this user
     *
     * @param RoleCollectionInterface[] $roleCollections
     *
     * @return mixed
     */
    public function setRoleCollections($roleCollections);

    /**
     * Connect a role collection to this user
     *
     * @param RoleCollectionInterface $roleCollection
     */
    public function addRoleCollection(RoleCollectionInterface $roleCollection);

    /**
     * @return RoleCollectionInterface[]
     */
    public function getRoleCollections();

    /**
     * Create a list of my current permissions based on the combination of the current organization context and
     * the connected role collections.
     *
     * @return mixed
     */
    public function getPermissions();
}
