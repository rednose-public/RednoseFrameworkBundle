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


class Locale implements LocaleInterface
{
    protected $id;
    protected $name;
    protected $binding;
    protected $organization;
    protected $isDefault;

    /**
     * Set the id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the locale name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the locale name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the data-dictionary binding path
     *
     * @param string $binding
     */
    public function setBinding($binding)
    {
        $this->binding = $binding;
    }

    /**
     * Get the data-dictionary binding path
     *
     * @return string
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * Set the organization
     *
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get the organization
     *
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set locale as default
     *
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->isDefault = $default;
    }

    /**
     * Get the default state
     *
     * @return boolean
     */
    public function getDefault()
    {
        return $this->isDefault;
    }
}
