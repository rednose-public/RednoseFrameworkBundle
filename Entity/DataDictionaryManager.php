<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryManagerInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Symfony\Component\Validator\ValidatorInterface;

class DataDictionaryManager implements DataDictionaryManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManager         $em
     * @param ValidatorInterface    $validator
     */
    public function __construct(EntityManager $em, ValidatorInterface $validator)
    {
        $this->em         = $em;
        $this->validator  = $validator;
        $this->repository = $em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary');
    }

    /**
     * @param OrganizationInterface $organization
     *
     * @return DataDictionaryInterface[]
     */
    public function findDictionaries(OrganizationInterface $organization = null)
    {
        if (!$organization) {
            return $this->repository->findAll();
        }

        return $this->repository->findBy(array('organization' => $organization));
    }

    /**
     * @param array $criteria
     *
     * @return DataDictionaryInterface
     */
    public function findDictionaryBy(array $criteria)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneBy($criteria);
    }

    /**
     * @param string $id
     *
     * @return \Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface
     */
    public function findDictionaryById($id)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneBy(array('id' => $id));
    }

    /**
     * @param DataDictionaryInterface $dictionary
     * @param bool                    $flush
     */
    public function updateDictionary(DataDictionaryInterface $dictionary, $flush = true)
    {
        $metadata = $this->em->getClassMetaData(get_class($dictionary));

        if ($dictionary->getId() !== null) {
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }

        $err = $this->validator->validate($dictionary);

        if (count($err) === 0) {
            $this->em->persist($dictionary);
        } else {
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());

            return $err;
        }

        if ($flush === true) {
            $this->em->flush();
        }

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());

        return true;
    }

    /**
     * @param \Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface $dictionary
     */
    public function deleteDictionary(DataDictionaryInterface $dictionary)
    {
        $this->em->remove($dictionary);
        $this->em->flush();
    }
}
