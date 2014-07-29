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

use Rednose\FrameworkBundle\Form\DataTransformer\DocumentToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Rednose\FrameworkBundle\Model\Form;

class RednoseFormType extends AbstractType
{
    protected $conditions = array();

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->builder = $builder;

        $form = $options['form'];

        if (!$form instanceof Form) {
            throw new \InvalidArgumentException('Form must be instance of `Rednose\FrameworkBundle\Model\Form`');
        }

        // We need the data on form construction so we can set the initial state by processing from conditions.
        if (!array_key_exists('data', $options)) {
            throw new \InvalidArgumentException('Data must be specified on form construction');
        }

        $builder->addViewTransformer(new DocumentToArrayTransformer());
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

        foreach ($form->getSections() as $section) {
            $builder->add($section->getName(), 'content_section', array(
                'section' => $section,
                'dom'     => $options['data'],
                'label'   => false,
                'legend'  => $section->getCaption(),
                'attr'    => array(
                    'data-section' => $section->getName()
                )
            ));
        }

//        $builder->add('export', 'submit', array(
//            'label' => 'Export',
//        ));
//
//        $builder->add('generate', 'submit', array(
//            'label' => 'Generate',
//        ));

        $builder->add('save', 'submit', array(
            'label' => 'Save',
        ));

//        $builder->add('approve', 'submit', array(
//            'label' => 'Approve',
//        ));
//
//        $builder->add('reject', 'submit', array(
//            'label' => 'Reject',
//        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'form'       => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_form';
    }

    public function onPreSetData(FormEvent $event)
    {
//        $data = $event->getData();
//        $form = $event->getForm();
//
//        $field = $this->builder->get('AfzenderOndertekening')->get('On_Behalf');
//
//        $options = $field->getOptions();            // get the options
//        $type = $field->getType()->getName();       // get the name of the type
//        $options['label'] = "Login Name";           // change the label
//        $this->builder->get('AfzenderOndertekening')->add('On_Behalf', $type, $options);
    }
}
