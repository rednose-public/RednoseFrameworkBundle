<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIntTransformer implements DataTransformerInterface
{
    protected $om;
    protected $entityClass;
    protected $entityRepository;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param mixed $entity
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return integer
     */
    public function transform($entity)
    {
        if ($entity === null) {
            return null;
        }

        if (!$entity instanceof $this->entityClass) {
            throw new TransformationFailedException('Incorrect entity class provided');
        }

        return $entity->getId();
    }

    /**
     * @param mixed $id
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            throw new TransformationFailedException('No id was submitted');
        }

        $entity = $this->om->getRepository($this->entityRepository)->findOneBy(array('id' => $id));

        if (null === $entity) {
            throw new TransformationFailedException(sprintf('Entity with id "%s" does not exist!', $id));
        }

        return $entity;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function setEntityRepository($entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }
}
