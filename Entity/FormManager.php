<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Model\ControlForm as ControlForm;

class FormManager
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
     * @param ControlForm $form
     * @param bool        $flush
     */
    public function updateForm(ControlForm $form, $flush = true)
    {
        $metadata = $this->em->getClassMetaData(get_class($form));

        if ($form->getId() !== null) {
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }

        $this->em->persist($form);

        if ($flush === true) {
            $this->em->flush();
        }

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_UUID);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\UuidGenerator());
    }
}
