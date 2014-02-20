<?php

namespace Rednose\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AssetAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');

        $collection->add('upload', 'upload');
        $collection->add('download',  $this->getRouterIdParameter().'/download');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'download' => array('template' => 'RednoseFrameworkBundle:AssetAdmin:list__action_download.html.twig'),
                    'delete'   => array(),
                )
            ));
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
            ->add('name');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('name');
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'RednoseFrameworkBundle:AssetAdmin:list.html.twig';
            case 'edit':
                return 'RednoseFrameworkBundle:AssetAdmin:edit.html.twig';
            case 'base_list_field':
                return 'RednoseFrameworkBundle:AssetAdmin:list_field.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {
            $actions['download']= array ('label' => 'Download', 'ask_confirmation'  => false );
        }

        return $actions;
    }
}
