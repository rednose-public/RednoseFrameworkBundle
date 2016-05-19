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

use FOS\UserBundle\Model\GroupManagerInterface as BaseGroupManagerInterface;

interface GroupManagerInterface extends BaseGroupManagerInterface
{
    /**
     * Returns a collection with all group instances.
     *
     * @return GroupInterface[]
     */
    public function findGroups();

    /**
     * Finds one asset by the given criteria filtered by organization.
     *
     * @param OrganizationInterface $organization
     *
     * @return GroupInterface[]
     */
    public function findGroupsByOrganization(OrganizationInterface $organization);
}
