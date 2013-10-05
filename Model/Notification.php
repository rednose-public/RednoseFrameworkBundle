<?php

namespace Rednose\FrameworkBundle\Model;

use Rednose\FrameworkBundle\Entity\User;

abstract class Notification implements NotificationInterface
{
    public function __construct($type)
    {
        // TODO: Confirm that type is valid.
        $this->type   = $type;
        $this->status = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setRead()
    {
        $this->status = true;
        $this->readAt = new \DateTime();
    }

    public function isRead()
    {
        return $this->status;
    }
}
