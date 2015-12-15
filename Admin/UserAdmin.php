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

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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
            ->add('organization')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('admin', 'boolean')
            ->add('superAdmin', 'boolean')
            ->add('lastLogin');

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
            ->end()
            ->with('Details')
                ->add('organization')
                ->add('groups')
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
            ->end()

            ->with('Details')
                ->add('organization', 'sonata_type_model', array('required' => false,'multiple' => false))
                ->add('groups', 'sonata_type_model', array('required' => false,'multiple' => true))
                ->add('enabled', 'checkbox', array('required' => false))
                ->add('locked', 'checkbox', array('required' => false))
                ->add('Admin', 'checkbox', array('required' => false))
                ->add('superAdmin', 'checkbox', array('required' => false))
            ->end()

            ->setHelps(array(
               'username' => $this->trans('help_user_username')
            ));
    }
}
