<?php

namespace Rednose\FrameworkBundle\Event;

use Rednose\FrameworkBundle\Entity\Group;
use Symfony\Component\EventDispatcher\Event;

class GroupEvent extends Event
{
    private $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }
}
