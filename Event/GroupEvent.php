<?php

namespace Rednose\FrameworkBundle\Event;

use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\Event;

class GroupEvent extends Event
{
    private $group;

    public function __construct(GroupInterface $group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }
}
