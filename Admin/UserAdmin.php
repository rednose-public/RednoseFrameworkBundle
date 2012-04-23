<?php

namespace Libbit\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function preUpdate($user)
    {
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }
    
    public function prePersist($user)
    {
        $this->preUpdate($user);
    }
    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('superAdmin', 'boolean')
            ->add('lastLogin')

            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
            ->add('username_canonical')
            ->add('email')
            ->add('email_canonical')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('expiresAt')
            ->add('passwordRequestedAt')
            ->add('credentialsExpired')
            ->add('credentialsExpireAt')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
            ->with('Management')
                ->add('groups') 
                ->add('enabled')
                ->add('locked')
                ->add('superAdmin', 'checkbox')

            ->setHelps(array(
               'username' => $this->trans('help_user_username')
            ))
        ;
    }
}
