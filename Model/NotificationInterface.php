<?php

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
