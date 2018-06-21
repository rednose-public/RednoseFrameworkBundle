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

use Rednose\FrameworkBundle\Entity\RoleCollection;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\RoleCollectionInterface;
use Symfony\Component\Form\DataTransformerInterface;

class RoleCollectionAdminTransformer implements DataTransformerInterface
{
    /**
     * @var OrganizationInterface
     */
    protected $organization;

    /**
     * RoleCollectionTransformer constructor
     *
     * @param OrganizationInterface $organization
     */
    public function __construct(OrganizationInterface $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Transform to empty array, values will be passed by the view-builder.
     *
     * @param RoleCollectionInterface $roleCollection
     *
     * @return array
     */
    public function transform($roleCollection)
    {
        return [];
    }

    /**
     * Transform the post-data array into the expected ArrayCollection.
     *
     * @param array $formData
     *
     * @return RoleCollectionInterface[]
     */
    public function reverseTransform($formData)
    {
        $roleCollections = $this->organization->getRoleCollections();

        foreach ($formData['ids'] as $offset => $collectionId) {
            $rc = $this->organization->findRoleCollectionById($collectionId);

            if ($rc === null) {
                $rc = new RoleCollection();
                $rc->setOrganization($this->organization);
            }

            $rc->setName($formData['name'][$offset]);
            $rc->setRoles(explode(',', $formData['roles'][$offset]));

            $roleCollections->add($rc);
        }

        // Find deleted role collections
        foreach ($roleCollections as $rc) {
            if ($rc->getId() && array_search($rc->getId(), $formData['ids']) === false) {
                $roleCollections->removeElement($rc);
            }
        }

        return $roleCollections;
    }
}
