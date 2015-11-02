<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;

class OrganizationManager implements OrganizationManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $name
     *
     * @return OrganizationInterface
     */
    public function createOrganization($name = null)
    {
        $organization = new Organization();

        if ($name !== null) {
            $organization->setName($name);
        }

        return $organization;
    }

    /**
     * @return OrganizationInterface[]
     */
    public function findOrganizations()
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\Organization')->findBy(array(), array('name' => 'ASC'));
    }

    /**
     * @param array $criteria
     *
     * @return OrganizationInterface
     */
    public function findOrganizationBy(array $criteria)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\Organization')->findOneBy($criteria);
    }

    /**
     * @param string $id
     *
     * @return OrganizationInterface
     */
    public function findOrganizationById($id)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\Organization')->findOneBy(array('id' => $id));
    }

    /**
     * @param string $name
     *
     * @return OrganizationInterface
     */
    public function findOrganizationByName($name)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\Organization')->findOneBy(array('name' => $name));
    }

    /**
     * @param OrganizationInterface $organization
     * @param bool                    $flush
     */
    public function updateOrganization(OrganizationInterface $organization, $flush = true)
    {
        $metadata = $this->em->getClassMetaData(get_class($organization));

        if ($organization->getId() !== null) {
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }

        $this->em->persist($organization);

        if ($flush === true) {
            $this->em->flush();
        }

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());
    }

    /**
     * @param OrganizationInterface $organization
     */
    public function deleteOrganization(OrganizationInterface $organization)
    {
        $this->em->remove($organization);
        $this->em->flush();
    }
}
