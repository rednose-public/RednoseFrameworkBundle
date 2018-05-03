<?php

namespace Rednose\FrameworkBundle\Model;

interface RoleCollectionInterface
{
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