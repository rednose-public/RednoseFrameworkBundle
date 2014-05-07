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

/**
 * A Usergroup.
 */
interface GroupInterface extends BaseGroupInterface
{
    /**
     * @param $users
     */
    public function setUsers($users);


    public function getUsers();
}
