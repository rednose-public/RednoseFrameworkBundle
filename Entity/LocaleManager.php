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

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Model\LocaleInterface;
use Rednose\FrameworkBundle\Model\LocaleManagerInterface;

class LocaleManager implements LocaleManagerInterface
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
     * @return LocaleInterface
     */
    public function createLocale()
    {
        return new Locale();
    }

    /**
     * @return LocaleInterface[]
     */
    public function findLocales()
    {
        return $this->em->getRepository($this->getClass())->findBy(array(), array('name' => 'ASC'));
    }

    /**
     * @param array $criteria
     *
     * @return LocaleInterface
     */
    public function findLocaleBy(array $criteria)
    {
        return $this->em->getRepository($this->getClass())->findOneBy($criteria);
    }

    /**
     * @param string $id
     *
     * @return LocaleInterface
     */
    public function findLocaleById($id)
    {
        return $this->em->getRepository($this->getClass())->findOneBy(array('id' => $id));
    }

    /**
     * @param LocaleInterface   $locale
     * @param bool              $flush
     */
    public function updateLocale(LocaleInterface $locale, $flush = true)
    {
        $metadata = $this->em->getClassMetaData(get_class($locale));

        if ($locale->getId() !== null) {
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }

        $this->em->persist($locale);

        if ($flush === true) {
            $this->em->flush();
        }

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());
    }

    /**
     * @param LocaleInterface $locale
     */
    public function deleteLocale(LocaleInterface $locale)
    {
        $this->em->remove($locale);
        $this->em->flush();
    }

    /**
     * Get entity namespace and className
     *
     * @return string
     */
    public function getClass()
    {
        return 'Rednose\FrameworkBundle\Entity\Locale';
    }
}
