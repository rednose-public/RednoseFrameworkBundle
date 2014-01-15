<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * WYSIWYG document editor wrapper form
 */
class EditorType extends AbstractType
{
    protected $request;

    /**
     * Constructor
     *
     * @param Request $request Request object
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->setAttribute('toolbar', $options['toolbar'])
            ->setAttribute('scayt', $options['scayt'])
            ->setAttribute('height', $options['height']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view
            ->set('toolbar', $form->getAttribute('toolbar'))
            ->set('scayt', $form->getAttribute('scayt'))
            ->set('height', $form->getAttribute('height'))
            ->set('locale', $this->request->getLocale());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_widget_editor';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required'      => false,
            'scayt'         => true,
            'height'        => 250,

            // WYSIWYG features. Options are carefully chosen, keeping dictated corporate identity guidelines and multi-device support
            // in mind.
            'toolbar'       => array(
                array('name' => 'styles', 'items' => array('Styles')),

                // Standard bold, italic, underline. Remove format is only here to clean up the mess from pasted stuff.
                array('name' => 'basicstyles', 'items' => array('Bold', 'Italic', 'Underline', '-', 'RemoveFormat')),

                // Basic lists.
                array('name' => 'paragraph', 'items' => array('NumberedList', 'BulletedList')),

                // URL support, essential.
                array('name' => 'links', 'items' => array('Link')),

                // Essential and no reason to not implement them.
                array('name' => 'clipboard', 'items' => array('Undo', 'Redo')),

                // Cut / copy / paste. These features are limited to web browsers, as other devices have these implemented into their basic
                // text input API's.
                array('name' => 'clipboard', 'items' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord')),

                // Nice to have. All clients should implement this.
                array('name' => 'tools', 'items' => array('Maximize', 'Scayt')),
            ),
        ));
    }
}
