<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class OrganizationAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('name')
                ->add('conditions', 'array', array('label' => 'User assignment conditions'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('name', 'locale')
                ->add('conditions', 'collection', array(
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label'        => 'User assignment conditions',
                    'required'     => false,
                ))
            ->end()
            ->with('Localization')
                ->add('locale', 'locale')
                ->add('localizations', 'locale', array(
                    'multiple' => true,
                ))
        ;
    }
}
