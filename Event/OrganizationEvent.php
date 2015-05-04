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

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Symfony\Component\EventDispatcher\Event;

class OrganizationEvent extends Event
{
    /**
     * @var OrganizationInterface
     */
    private $organization;

    /**
     * @param OrganizationInterface $organization
     */
    public function __construct(OrganizationInterface $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
