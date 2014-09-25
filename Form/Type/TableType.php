<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\EventListener\DateTypeDataListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\DBAL\Connection;
use Rednose\FrameworkBundle\Model\Table;

/**
 * Experimental class
 */
class TableType extends AbstractType
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $columns = $options['columns'];

        $query = sprintf('SELECT %s FROM `%s`', implode(', ', $columns), $options['table']);

        if (isset($options['related_field'])) {
            $query .= sprintf(' WHERE `%s` = %s', $options['related_field'], $options['parent_value']);
        }

        $records = $this->connection->fetchAll($query);

        $table = new Table();
        $table->columns = $columns;
        $table->records = $records;

        $builder
            ->setAttribute('table', $table)
            ->setAttribute('allow_add', $options['allow_add'])
            ->setAttribute('allow_delete', $options['allow_delete'])
            ->setAttribute('allow_edit', $options['allow_edit'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['table'] = $form->getConfig()->getAttribute('table');
        $view->vars['allow_add'] = $form->getConfig()->getAttribute('allow_add');
        $view->vars['allow_delete'] = $form->getConfig()->getAttribute('allow_delete');
        $view->vars['allow_edit'] = $form->getConfig()->getAttribute('allow_edit');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'table' => null,
            'columns' => array(),
            'parent_value' => null,
            'related_field' => null,
            'allow_add' => true,
            'allow_delete' => true,
            'allow_edit' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_table';
    }
}
