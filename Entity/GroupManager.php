<?php

namespace Rednose\FrameworkBundle\Entity;

use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;
use Rednose\FrameworkBundle\Model\GroupManagerInterface;

class GroupManager extends BaseGroupManager implements GroupManagerInterface
{
}
