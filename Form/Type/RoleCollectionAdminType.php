<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\DataTransformer\RoleCollectionAdminTransformer;
use Rednose\FrameworkBundle\Model\RoleCollectionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleCollectionAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new RoleCollectionAdminTransformer($options['organization'])
        );

        $builder->add('ids', 'collection', [
            'allow_add' => true
        ]);

        $builder->add('name', 'collection', [
            'allow_add' => true
        ]);

        $builder->add('roles', 'collection', [
            'allow_add' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $ids   = [];
        $roles = [];
        $names = [];
        $data  = $form->getData();

        /** @var RoleCollectionInterface $rc */
        foreach ($data as $rc) {
            $ids[]   = $rc->getId();
            $names[] = $rc->getName();
            $roles[] = json_encode($rc->getRoles());
        }

        $view->vars = array_merge([
            'all_roles' => $options['roles'],
            'ids'       => $ids,
            'roles'     => $roles,
            'names'     => $names
        ], $view->vars);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'organization' => null,
            'roles' => []
        ));
    }

    public function getName()
    {
        return 'rednose_role_collection_admin';
    }
}