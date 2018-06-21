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

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Rednose\FrameworkBundle\Model\GroupManagerInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;

class GroupManager extends BaseGroupManager implements GroupManagerInterface
{
    /**
     * Finds one asset by the given criteria filtered by organization.
     *
     * @param OrganizationInterface $organization
     *
     * @return GroupInterface[]
     */
    public function findGroupsByOrganization(OrganizationInterface $organization)
    {
        return $this->repository->findBy([
            'organization' => $organization
        ]);
    }

    public function search($name, OrganizationInterface $organization = null)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->objectManager;

        $qb = $em->createQueryBuilder();
        $qb->select('g')->from('RednoseFrameworkBundle:Group', 'g');
        $qb->where($qb->expr()->like('g.name', ':name'));
        $qb->setParameter('name', '%' . $name . '%');

        if ($organization !== null) {
            $qb->andWhere('g.organization = :organization');
            $qb->setParameter('organization', $organization);
        }

        return $qb->getQuery()->execute();
    }
}
