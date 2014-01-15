<?php

namespace Rednose\FrameworkBundle\Admin;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('realname')
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

          if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
              $listMapper
                  ->add('impersonating', 'string', array('template' => 'RednoseFrameworkBundle:Admin\Field:impersonating.html.twig'))
              ;
          }
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('username_canonical')
                ->add('realname')
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
                ->add('realname')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
            ->with('Details')
                ->add('groups', null, array('required' => false, 'expanded' => true))
                ->add('enabled', 'checkbox', array('required' => false))
                ->add('locked', 'checkbox', array('required' => false))
                ->add('Admin', 'checkbox', array('required' => false))
                ->add('superAdmin', 'checkbox', array('required' => false))

            ->setHelps(array(
               'username' => $this->trans('help_user_username')
            ));
    }
}
