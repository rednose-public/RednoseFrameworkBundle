<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryManagerInterface;

class DataDictionaryManager implements DataDictionaryManagerInterface
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
     * @param OrganizationInterface $organization
     *
     * @return DataDictionaryInterface[]
     */
    public function findDictionaries(OrganizationInterface $organization = null)
    {
        if ($organization) {
            return array($organization->getDataDictionary());
        } else {
            return $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findAll();
        }
    }

    /**
     * @param string $id
     *
     * @return DataDictionaryInterface
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

        $this->em->persist($dictionary);

        if ($flush === true) {
            $this->em->flush();
        }

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());
    }

    /**
     * @param DataDictionaryInterface $dictionary
     */
    public function deleteDictionary(DataDictionaryInterface $dictionary)
    {
        $this->em->remove($dictionary);
        $this->em->flush();
    }
}
