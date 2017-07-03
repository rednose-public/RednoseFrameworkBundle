<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\OrganizationInterface;

trait HasOrganizationTrait
{
    /**
     * @var OrganizationInterface
     *
     * @ORM\ManyToOne(
     *   targetEntity="Rednose\FrameworkBundle\Entity\Organization"
     * )
     *
     * @ORM\JoinColumn(
     *   name="organization_id",
     *   nullable=false,
     *   referencedColumnName="id")
     */
    protected $organization;

    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization)
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