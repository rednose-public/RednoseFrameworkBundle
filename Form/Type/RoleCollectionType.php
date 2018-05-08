<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\DataTransformer\RoleCollectionTransformer;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\RoleCollectionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var OrganizationInterface $organization */
        foreach ($options['organizations'] as $organization) {
            $builder->add($organization->getId(), 'choice', [
                'choices' => $this->normalize($organization->getRoleCollections())
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'organizations' => null,
            'system_roles' => []
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_role_collection';
    }

    /**
     * @param $roleCollections
     *
     * @return array
     */
    private function normalize($roleCollections)
    {
        $array = [];

        foreach ($roleCollections as $roleCollection) {
            $array[$roleCollection->getId()] = $roleCollection->getName();
        }

        return $array;
    }
}