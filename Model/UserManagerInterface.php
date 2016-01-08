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

use FOS\UserBundle\Model\UserManagerInterface as BaseUserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface UserManagerInterface extends BaseUserManagerInterface
{
    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username);

    /**
     * Return all users, sorted
     *
     * @param $ascending
     */
    public function findUsersSorted($ascending = true);
}
