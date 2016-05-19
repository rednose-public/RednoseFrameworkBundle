<?php

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Model\UserInterface;

interface AssignerInterface
{
    /**
     * @param UserInterface $user
     */
    public function assign(UserInterface $user);
}