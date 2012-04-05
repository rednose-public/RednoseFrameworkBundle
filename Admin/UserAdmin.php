<?php

namespace Libbit\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class UserAdmin extends Admin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin')

            // add custom action links
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username')
            ->add('enabled')
            ->add('email')

            // you can define help messages like this
            ->setHelps(array(
               'username' => $this->trans('help_user_username')
            ))
        ;
    }
}
