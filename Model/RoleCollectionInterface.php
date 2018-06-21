<?php

namespace Rednose\FrameworkBundle\Model;

use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;

interface RoleCollectionInterface extends HasOrganizationInterface
{
    /**
     * Get the id
     *
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param array<string> $roles
     */
    public function setRoles($roles);

    /**
     * @return array<string>
     */
    public function getRoles();
}