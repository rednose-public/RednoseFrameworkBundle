<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Admin;

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Sonata\AdminBundle\Admin\Pool;

class OrganizationAwarePool extends Pool
{
    /**
     * @return OrganizationInterface[]
     */
    public function getOrganizations()
    {
        return $this->container->get('rednose_framework.organization_manager')->findOrganizations();
    }
}