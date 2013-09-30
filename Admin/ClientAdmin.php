<?php

namespace Rednose\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ClientAdmin extends Admin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view'    => array(),
                    'edit'    => array(),
                    'delete'  => array(),
                )
            ));
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('name')
            ->with('Identification')
                ->add('publicId', 'text', array('label' => 'Client key'))
                ->add('secret', 'text', array('label' => 'Client secret'))
            ->with('OAuth')
                ->add('redirect_uris', 'array', array('label' => 'Redirect URIs'))
                ->add('allowed_grant_types', 'array', array('label' => 'Grant types'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('name');

        if ($this->getSubject()->getId() !== null) {
            $formMapper
                ->with('Identification')
                    ->add('publicId', 'text', array('label' => 'Client key', 'disabled' => true, 'required' => false))
                    ->add('secret', 'text', array('label' => 'Client secret', 'disabled' => true, 'required' => false));
        }

        $formMapper
            ->with('OAuth')
                ->add('redirect_uris', 'collection', array(
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label'        => 'Redirect URIs',
                    'required'     => true,
                ))
                ->add('allowed_grant_types', 'choice', array(
                    'choices'           => array(
                        'authorization_code' => 'Authorization code',
                        'token'              => 'Token',
                        'password'           => 'Password'
                    ),
                    'label'             => 'Grant types',
                    'multiple'          => true,
                    'required'          => true,
                ));
    }
}
