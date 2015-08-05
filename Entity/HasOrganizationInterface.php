<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Rednose\FrameworkBundle\Model\OrganizationInterface;

interface HasOrganizationInterface
{
    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization);

    /**
     * @return OrganizationInterface
     */
    public function getOrganization();
}