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

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($user)
    {
        $this->preUpdate($user);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('organization')
            ->add('groups')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('lastLogin')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'RednoseFrameworkBundle:Admin\Field:impersonating.html.twig'))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $roles = [];

        foreach (array_keys($this->getConfigurationPool()->getContainer()->getParameter('security.role_hierarchy.roles')) as $role) {
            $roles[$role] = $this->trans($role, [], 'SonataAdminBundle');
        }

        $user = $this->getSubject();

        $formMapper
            ->with('General')
                ->add('username', 'text', array('data' => $user->getUsername(false), 'required' => true))
                ->add('realname')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => !$this->getSubject()->getId()))
            ->end()

            ->with('Details')
                ->add('organization', 'sonata_type_model', array('required' => false, 'multiple' => false))
                ->add('groups', 'entity', array(
                    'class' => 'RednoseFrameworkBundle:Group',
                    'property' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'choices' => $this->getGroups(),
                ))
                ->add('enabled', 'checkbox', array('required' => false))
                ->add('locked', 'checkbox', array('required' => false))
            ->end()

            ->with('Roles')
                ->add('roles', 'choice', array(
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                    'choices' => $roles
                ))
            ->end()
        ;
    }

    /**
     * @return array
     */
    protected function getGroups()
    {
        /** @var EntityManager $em */
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');

        $choices = [];

        /** @var GroupInterface $group */
        foreach ($em->getRepository('RednoseFrameworkBundle:Group')->findBy([], ['name' => 'ASC']) as $group) {
            if (!isset($choices[$group->getOrganization()->getName()])) {
                $choices[$group->getOrganization()->getName()] = [];
            }

            $choices[$group->getOrganization()->getName()][$group->getId()] = $group;
        }

        return $choices;
    }
}
