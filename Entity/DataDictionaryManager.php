<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Model\DataControlInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryManagerInterface;
use Rednose\FrameworkBundle\Util\XpathUtil;
use Symfony\Component\Config\Util\XmlUtils;

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
     * @return DataDictionaryInterface
     */
    public function findDictionaryById($id)
    {
        return $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneBy(array('id' => $id));
    }

    /**
     * @param DataControlInterface $control
     * @param \DOMDocument         $data
     */
    protected function traverse(DataControlInterface $control, \DOMDocument $data)
    {
        // TODO: Move method to util class.
        if (in_array($control->getType(), array(DataControlInterface::TYPE_COMPOSITE, DataControlInterface::TYPE_COLLECTION))) {
            foreach ($control->getChildren() as $child) {
                $this->traverse($child, $data);
            }

            return;
        }

        $node = XpathUtil::getXpathNode($data, $control->getPath());

        if ($node !== null) {
            $value = $node->nodeValue;

            if ($control->getType() === DataControlInterface::TYPE_DATE) {
                $value = new \DateTime($value);
            } else  if ($control->getType() === DataControlInterface::TYPE_BOOLEAN) {
                $value = (boolean) XmlUtils::phpize($value);
            }

            $control->setValue($value);
        }
    }

    /**
     * Merges a data set into a data dictionary
     *
     * @param DataDictionaryInterface $dictionary
     * @param \DOMDocument $data
     *
     * @return DataDictionaryInterface
     */
    public function merge(DataDictionaryInterface $dictionary, \DOMDocument $data)
    {
        // TODO: Move method to util class.
        foreach ($dictionary->getControls() as $control) {
            $this->traverse($control, $data);
        }

        return $dictionary;
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
