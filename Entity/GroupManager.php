<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;
use Rednose\FrameworkBundle\Model\GroupManagerInterface;

class GroupManager extends BaseGroupManager implements GroupManagerInterface
{
}
