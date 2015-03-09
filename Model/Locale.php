<?php

namespace Rednose\FrameworkBundle\Model;


class Locale implements LocaleInterface
{
    protected $id;
    protected $name;
    protected $binding;
    protected $organization;

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
     * @param string $name
     */
    public function setBinding($name)
    {
        $this->binding = $name;
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
     * @param OrganizationInterface $name
     */
    public function setOrganization(OrganizationInterface $name)
    {
        $this->organization = $name;
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
}
