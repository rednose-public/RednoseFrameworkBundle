<?php

namespace Rednose\FrameworkBundle\Model;

interface OrganizationManagerInterface
{
    /**
     * @param OrganizationInterface $organization
     */
    public function deleteOrganization(OrganizationInterface $organization);

    /**
     * @param string $id
     *
     * @return OrganizationInterface
     */
    public function findOrganizationById($id);

    /**
     * @param OrganizationInterface $organization
     * @param bool $flush
     */
    public function updateOrganization(OrganizationInterface $organization, $flush = true);

    /**
     * @return OrganizationInterface[]
     */
    public function findOrganizations();

    /**
     * @param array $criteria
     *
     * @return OrganizationInterface
     */
    public function findOrganizationBy(array $criteria);

    /**
     * @param string $name
     *
     * @return OrganizationInterface
     */
    public function createOrganization($name = null);

    /**
     * @param string $name
     *
     * @return OrganizationInterface
     */
    public function findOrganizationByName($name);
}
