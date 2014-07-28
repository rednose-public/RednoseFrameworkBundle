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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Rednose\FrameworkBundle\Model\Form;

class RednoseFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $form = $options['form'];

        if (!$form instanceof Form) {
            throw new \InvalidArgumentException('Form must be instance of `Rednose\FrameworkBundle\Model\Form`');
        }

        foreach ($form->getSections() as $section) {
            $builder->add($section->getName(), 'content_section', array(
                'section' => $section,
                'label'   => false,
                'legend'  => $section->getCaption(),
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
}
