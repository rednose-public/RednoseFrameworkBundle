<?php

namespace Rednose\FrameworkBundle\Model;


interface LocaleInterface
{
    /**
     * Set the id
     *
     * @param integer $id
     */
    public function setId($id);

    /**
     * Get the id
     *
     * @return integer
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
     * Set the data-dictionary binding path
     *
     * @param string $name
     */
    public function setBinding($name);

    /**
     * Get the data-dictionary binding path
     *
     * @return string
     */
    public function getBinding();

    /**
     * Set the organization
     *
     * @param OrganizationInterface $name
     */
    public function setOrganization(OrganizationInterface $name);

    /**
     * Get the organization
     *
     * @return OrganizationInterface
     */
    public function getOrganization();
}
