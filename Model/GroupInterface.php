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

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use Rednose\FrameworkBundle\Entity\HasConditionsInterface;
use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;

/**
 * A User-group.
 */
interface GroupInterface extends BaseGroupInterface, HasOrganizationInterface, HasConditionsInterface
{
    /**
     * @param UserInterface[] $users
     */
    public function setUsers($users);

    /**
     * @return UserInterface[]
     */
    public function getUsers();
}
