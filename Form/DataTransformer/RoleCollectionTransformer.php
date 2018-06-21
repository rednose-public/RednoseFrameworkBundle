<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\RoleCollectionInterface;
use Symfony\Component\Form\DataTransformerInterface;

class RoleCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $organizations;

    /**
     * RoleCollectionTransformer constructor
     *
     * @param array $organizations
     */
    public function __construct(array $organizations)
    {
        $this->organizations = $organizations;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($roleCollections)
    {
        $formData = [];

        /** @var RoleCollectionInterface $roleCollection */
        foreach ($roleCollections as $roleCollection) {
            $organizationId = $roleCollection->getOrganization()->getId();

            if (isset($formData[$organizationId]) === false) {
                $formData[$organizationId] = [];
            }

            $formData[$organizationId][] = $roleCollection->getId();
        }

        return $formData;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($formData)
    {
        $roleCollections = new ArrayCollection();

        foreach ($formData as $organizationId => $selectedRoleCollections) {
            $organization = $this->findOrganization($organizationId);

            foreach ($selectedRoleCollections as $roleCollectionId) {
                $roleCollection = $organization->findRoleCollectionById($roleCollectionId);
                $roleCollections->add($roleCollection);
            }
        }

        return $roleCollections;
    }

    /**
     * @param $organizationId
     *
     * @return OrganizationInterface|null
     */
    protected function findOrganization($organizationId)
    {
        foreach ($this->organizations as $organization) {
            if ($organization->getId() === $organizationId) {
                return $organization;
            }
        }

        return null;
    }
}

