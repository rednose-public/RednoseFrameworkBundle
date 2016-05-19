<?php

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;

interface AssignerInterface
{
    /**
     * @param UserInterface $user
     */
    public function assign(UserInterface $user);

    /**
     * @param string $username
     *
     * @return OrganizationInterface
     */
    public function resolve($username);
}