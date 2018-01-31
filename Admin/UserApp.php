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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

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
     * @var []
     */
    protected $roles = [];

    /**
     * {@inheritdoc}
     */
    public function getData($start, $limit, $query = null, $itemId = null)
    {
        $repo = $this->em->getRepository('RednoseFrameworkBundle:User');

        if ($itemId) {
            return $repo->findOneBy(['id' => $itemId]);
        }

        if ($query) {
            return $this->generateSearchQuery($repo, ['username', 'realname'], $query, $start, $limit)->getQuery()->getResult();
        }

        return $repo->findBy([], null, $limit, $start);
    }


    /**
     * {@inheritdoc}
     */
    public function getDataLength($query = null)
    {
        $repo = $this->em->getRepository('RednoseFrameworkBundle:User');

        if ($query) {
            $query = $this->generateSearchQuery($repo, ['username', 'realname'], $query);

            return count($query->getQuery()->getResult()); // Optimize me!
        }

        return $repo
            ->createQueryBuilder('user')
            ->select('count(user.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

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
     * Provide a form factory
     *
     * @param FormFactoryInterface $ff
     */
    public function setFormFactory(FormFactoryInterface $ff)
    {
        $this->formFactory = $ff;
    }

    public function setRoleHierarchy($roleHierarchy)
    {
        foreach (array_keys($roleHierarchy) as $role) {
            // TODO: translate

            $this->roles[$role] = $role;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getFormDefinition()
    {
        $formDefinition = new FormDefinition();

        $formDefinition
            // General
            ->setSection('General')
            ->addField('Username', 'text', [ 'required' => true ])
            ->addField('Realname', 'text', [ 'required' => false ])
            ->addField('Email', 'email', [ 'required' => true ])
            ->addField('Plain_password', 'password', [ 'required' => false ])

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
            ->addField('Roles', 'choice', [
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices'  => $this->roles
            ]);

        return $formDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm($itemId)
    {
        $user = $this->em->getRepository('RednoseFrameworkBundle:User')->findOneBy(['id' => $itemId]);

        if ($user === null) {
            $user = new User();
        }

        return $this->formFactory->create('Doctanium\Bundle\DashboardBundle\Form\Type\DataGridFormType', $user, [
            'app_class' => $this
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handleForm(Form $form)
    {
        $user = $form->getData();

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getListColumns()
    {
        return ['id', 'username'];
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
}
