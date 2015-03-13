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


interface LocaleInterface
{
    /**
     * Set the id
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Get the id
     *
     * @return string
     */
    public function getId();

    /**
     * Set the locale name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get the locale name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the display name
     *
     * @param string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * Get the display name
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Set the data-dictionary binding path
     *
     * @param string $binding
     */
    public function setBinding($binding);

    /**
     * Get the data-dictionary binding path
     *
     * @return string
     */
    public function getBinding();

    /**
     * Set the organization
     *
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization);

    /**
     * Get the organization
     *
     * @return OrganizationInterface
     */
    public function getOrganization();

    /**
     * Set locale as default
     *
     * @param boolean $default
     */
    public function setDefault($default);

    /**
     * Get the default state
     *
     * @return boolean
     */
    public function getDefault();
}
