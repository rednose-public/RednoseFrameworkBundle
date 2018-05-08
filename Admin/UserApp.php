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

use Doctanium\Bundle\DashboardBundle\Datagrid\DatagridApp;
use Doctanium\Bundle\DashboardBundle\Form\Definition\FormDefinition;
use Doctanium\Bundle\DashboardBundle\Query\QueryBuilderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Rednose\FrameworkBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserApp extends DatagridApp
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var FormFactoryInterface;
     */
    protected $formFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var OrganizationManagerInterface
     */
    protected $organizationManager;

    /**
     * {@inheritdoc}
     */
    public function getPrimaryColumn()
    {
        return 'username';
    }

    /**
     * {@inheritdoc}
     */
    public function getData(OrganizationInterface $organization, $itemId = null, $start = 0, $limit = 0, $sortBy = null, $sortOrder = 'ASC', $query = null, array $options = null)
    {
        $helper = new QueryBuilderHelper();
        $repo   = $this->em->getRepository('RednoseFrameworkBundle:User');

        return $helper->generateRecordsQuery(
            $repo, $itemId, null, $start, $limit, ['username', 'realname'], $query, $sortBy , $sortOrder
        )->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataLength(OrganizationInterface $organization, $query = null, array $options = null)
    {
        $repo = $this->em->getRepository('RednoseFrameworkBundle:User');
        $helper = new QueryBuilderHelper();

        return $helper->generateRecordsCountQuery($repo, null, $query, ['username', 'realname']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortableColumns(array $options = null)
    {
        return ['username', 'realname', 'last_login'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormDefinition(OrganizationInterface $organization, $subject)
    {
        $formDefinition = new FormDefinition($this->translator, 'RednoseFrameworkBundle');

        $formDefinition
            // General
            ->setSection('General')
            ->addField('Username', 'text', [ 'required' => true ])
            ->addField('Realname', 'text', [ 'required' => false ])
            ->addField('Email', 'email', [ 'required' => true ])
            ->addField('Plain_password', 'password', [ 'required' => !$subject->getId() ])

            // Details
            ->setSection('Details')
            ->addField('Organization', 'entity', [
                'property' => 'name',
                'class'    => 'RednoseFrameworkBundle:Organization',
                'required' => true,
            ])
            ->addField('Static', 'checkbox', [ 'required' => false ])
            ->addField('Groups', 'entity', [
                'property' => 'name',
                'class'    => 'RednoseFrameworkBundle:Group',
                'required' => false,
                'multiple' => true,
                'choices'  => $this->getGroups()
            ])
            ->addField('Enabled', 'checkbox', [ 'required' => false ])
            ->addField('Locked', 'checkbox', [ 'required' => false ])

            // Roles
            ->setSection('Roles')
            ->addField('RoleCollections', 'rednose_role_collection', [
                'organizations' => $this->organizationManager->findOrganizations()
            ]);

        return $formDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(OrganizationInterface $organization, $itemId, array $options = null)
    {
        if ($itemId === 'create') {
            /** @var UserInterface $user */
            $user = $this->userManager->createUser();

            // Defaults
            $user->setStatic(false);
            $user->setEnabled(true);
            $user->setLocked(false);
        } else {
            $user = $this->userManager->findUserBy(['id' => $itemId]);
        }

        return $this->formFactory->create('Doctanium\Bundle\DashboardBundle\Form\Type\DataGridFormType', $user, [
            'organization' => $organization,
            'app_class'    => $this
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handleForm(Form $form)
    {
        $user = $form->getData();

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getListColumns()
    {
        $transDom = 'RednoseFrameworkBundle';

        return [
            'username'          => $this->translator->trans('Username', [], $transDom),
            'realname'          => $this->translator->trans('Realname', [], $transDom),
            'organization_name' => $this->translator->trans('Organization', [], $transDom),
            'locale'            => $this->translator->trans('Locale', [], $transDom),
            'enabled'           => $this->translator->trans('Enabled', [], $transDom),
            'static'            => $this->translator->trans('Static', [], $transDom),
            'locked'            => $this->translator->trans('Locked', [], $transDom),
            'admin'             => $this->translator->trans('Admin', [], $transDom),
            'super_admin'       => $this->translator->trans('SuperAdmin', [], $transDom),
            'last_login'        => $this->translator->trans('LastLogin', [], $transDom),
        ];
    }

    /**
     * Retrieve and created groups organized by organization
     *
     * @return array
     */
    protected function getGroups()
    {
        $choices = [];

        /** @var GroupInterface $group */
        foreach ($this->em->getRepository('RednoseFrameworkBundle:Group')->findBy([], ['name' => 'ASC']) as $group) {
            if (isset($choices[$group->getOrganization()->getName()]) === false) {
                $choices[$group->getOrganization()->getName()] = [];
            }

            $choices[$group->getOrganization()->getName()][$group->getId()] = $group;
        }

        return $choices;
    }

    // -- [ Dependency injection methods

    /**
     * Provide the back-end entity manager
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Provide the organization manager
     *
     * @param OrganizationManagerInterface $orgaManager
     */
    public function setOrganizationManager(OrganizationManagerInterface $orgaManager)
    {
        $this->organizationManager = $orgaManager;
    }

    /**
     * Provide a form factory
     *
     * @param FormFactoryInterface $ff
     */
    public function setFormFactory(FormFactoryInterface $ff)
    {
        $this->formFactory = $ff;
    }

    /**
     * Set the translator service
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Set the user manager service
     *
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }
}
