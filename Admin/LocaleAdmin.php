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

class LocaleAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('displayName')
            ->add('default', 'boolean')
            ->add('organization');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
            ->add('name')
            ->add('displayName')
            ->add('default', 'boolean');

    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('name')
            ->add('displayName')
            ->add('organization', 'sonata_type_model', array('required' => false,'multiple' => false))
            ->add('default', 'checkbox', array('required' => false));
    }
}
