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

use Rednose\FrameworkBundle\Entity\User;

interface NotificationInterface
{
    public function getId();

    public function getOwner();

    public function setOwner(User $owner);

    public function getUser();

    public function setUser($user);

    public function getCreatedAt();

    public function getType();

    public function setRead();

    public function isRead();
}
