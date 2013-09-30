<?php

namespace Rednose\FrameworkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
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

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('roles', '', array(), 'choice', array('choices' => array('ROLE_ADMIN' => 'Admin', 'ROLE_SUPER_ADMIN' => 'SuperAdmin')));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('admin', 'boolean')
            ->add('superAdmin', 'boolean')
            ->add('lastLogin')
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
                ->add('username')
                ->add('username_canonical')
                ->add('email')
                ->add('email_canonical')
            ->with('Details')
                ->add('enabled')
                ->add('locked')
                ->add('expired')
                ->add('expiresAt')
                ->add('passwordRequestedAt')
                ->add('credentialsExpired')
                ->add('credentialsExpireAt');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();

        $formMapper
            ->with('General')
                ->add('username', 'text', array('data' => $user->getUsername(false), 'required' => true))
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
            ->with('Details')
                ->add('groups', null, array('required' => false))
                ->add('enabled', 'checkbox', array('required' => false))
                ->add('locked', 'checkbox', array('required' => false))
                ->add('Admin', 'checkbox', array('required' => false))
                ->add('superAdmin', 'checkbox', array('required' => false))

            ->setHelps(array(
               'username' => $this->trans('help_user_username')
            ));
    }
}
