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
use Rednose\FrameworkBundle\Model\GroupManagerInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

class GroupApp extends DatagridApp
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
     * @var GroupManagerInterface
     */
    protected $groupManager;

    /**
     * {@inheritdoc}
     */
    public function getPrimaryColumn()
    {
        return 'name';
    }

    /**
     * {@inheritdoc}
     */
    public function getData(OrganizationInterface $organization, $itemId = null, $start = 0, $limit = 0, $sortBy = null, $sortOrder = 'ASC', $query = null, array $options = null)
    {
        $helper = new QueryBuilderHelper();
        $repo   = $this->em->getRepository('RednoseFrameworkBundle:Group');

        return $helper->generateRecordsQuery(
            $repo, $itemId, $organization, $start, $limit, ['name'], $query, $sortBy , $sortOrder
        )->getQuery()->getResult();
    }


    /**
     * {@inheritdoc}
     */
    public function getDataLength(OrganizationInterface $organization, $query = null, array $options = null)
    {
        $repo = $this->em->getRepository('RednoseFrameworkBundle:Group');
        $helper = new QueryBuilderHelper();

        return $helper->generateRecordsCountQuery($repo, $organization, $query, ['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortableColumns(array $options = null)
    {
        return ['name'];
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
            ->addField('Name', 'text', [ 'required' => true ])

            // Automation
            ->setSection('Automation')
            ->addField('conditions', 'rednose_prioritized_collection', [
                'label'      => 'Assignment conditions',
                'required'   => false,
                'priorities' => false
            ]);

        return $formDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(OrganizationInterface $organization, $itemId, array $options = null)
    {
        if ($itemId === 'create') {
            $group = $this->groupManager->createGroup('');
            $group->setOrganization($organization);
        } else {
            $group = $this->groupManager->findGroupBy(['id' => $itemId]);
        }

        return $this->formFactory->create('Doctanium\Bundle\DashboardBundle\Form\Type\DataGridFormType', $group, [
            'organization' => $organization,
            'app_class'    => $this
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handleForm(Form $form)
    {
        $group = $form->getData();

        $this->groupManager->updateGroup($group);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTheme()
    {
        return '@RednoseFramework/AdminForm/form.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getListColumns()
    {
        $transDom = 'RednoseFrameworkBundle';

        return [
            'name' => $this->translator->trans('Name', [], $transDom),
            'organization_name' => $this->translator->trans('Organization', [], $transDom)
        ];
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
     * Set the group manager service
     *
     * @param GroupManagerInterface $groupManager
     */
    public function setGroupManager(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }
}
