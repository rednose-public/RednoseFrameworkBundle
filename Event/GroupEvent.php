<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
