<?php

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
