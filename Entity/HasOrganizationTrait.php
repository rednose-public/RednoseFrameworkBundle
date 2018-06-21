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
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Groups({"list", "details"})
     *
     * @Serializer\Accessor("getOrganizationName")
     */
    protected $organizationName;

    /**
     * Gets the name of the organization for this entity.
     *
     * @return string
     */
    public function getOrganizationName()
    {
        if (!$this->organization) {
            return '';

        }

        return $this->organization->getName();
    }

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