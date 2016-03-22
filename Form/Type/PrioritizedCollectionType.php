<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\DataEvent\PrioritizedArrayDataListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrioritizedCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add']    = $options['allow_add'];
        $view->vars['allow_delete'] = $options['allow_delete'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new PrioritizedArrayDataListener());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_prioritized_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add'          => false,
            'allow_delete'       => false,
            'by_reference'       => false,
            'allow_extra_fields' => true
        ));
    }
}
