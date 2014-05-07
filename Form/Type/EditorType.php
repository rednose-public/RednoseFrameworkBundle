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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Rednose\FrameworkBundle\Form\EventListener\EditorTypeDataListener;

/**
 * WYSIWYG document editor wrapper form
 */
class EditorType extends AbstractType
{
    const TYPE_TINYMCE  = 'tinymce';
    const TYPE_CKEDITOR = 'ckeditor';

    protected $request;
    protected $listener;
    protected $type;

    /**
     * Constructor
     *
     * @param Request                $request  Request object
     * @param EditorTypeDataListener $listener Data listener
     * @param string                 $type     Editor type
     */
    public function __construct(Request $request, EditorTypeDataListener $listener, $type)
    {
        $this->request  = $request;
        $this->listener = $listener;
        $this->type     = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['purify']) {
            $builder->addEventSubscriber($this->listener);
        }

        parent::buildForm($builder, $options);

        $builder
            ->setAttribute('toolbar', $options['toolbar'])
            ->setAttribute('scayt', $options['scayt'])
            ->setAttribute('height', $options['height'])
            ->setAttribute('inline', $options['inline'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['toolbar'] = $form->getConfig()->getAttribute('toolbar');
        $view->vars['scayt']   = $form->getConfig()->getAttribute('scayt');
        $view->vars['height']  = $form->getConfig()->getAttribute('height');
        $view->vars['inline']  = $form->getConfig()->getAttribute('inline');
        $view->vars['locale']  = $this->request->getLocale();
        $view->vars['type']    = $this->type;
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
        // WYSIWYG features. Options are carefully chosen, keeping dictated corporate identity guidelines and multi-device support
        // in mind.

        if ($this->type === $this::TYPE_CKEDITOR) {
            $toolbar = array(
                array('name' => 'styles', 'items' => array('Styles')),

                // Standard bold, italic, underline. Remove format is only here to clean up the mess from pasted stuff.
                array('name' => 'basicstyles', 'items' => array('|','Bold', 'Italic', 'Underline', '-', 'RemoveFormat')),

                // Basic lists.
                array('name' => 'paragraph', 'items' => array('|','NumberedList', 'BulletedList')),

                // URL support, essential.
                array('name' => 'links', 'items' => array('|','Link')),

                // Essential and no reason to not implement them.
                array('name' => 'clipboard', 'items' => array('|','Undo', 'Redo')),

                // Cut / copy / paste. These features are limited to web browsers, as other devices have these implemented into their basic
                // text input API's.
                array('name' => 'clipboard', 'items' => array('|','Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord')),

                // Buildin spellchecker
                array('name' => 'spellchecker', 'items' => array('Scayt')),
            );
        } else {
            $toolbar = array(
                array('name' => 'styles', 'items' => array('styleselect')),

                // Standard bold, italic, underline. Remove format is only here to clean up the mess from pasted stuff.
                array('name' => 'basicstyles', 'items' => array('|','bold', 'italic', 'underline', '|', 'removeformat')),

                // Basic lists.
                array('name' => 'paragraph', 'items' => array('|','numlist', 'bullist')),

                // URL support, essential.
                array('name' => 'links', 'items' => array('|','link')),

                // Essential and no reason to not implement them.
                array('name' => 'clipboard', 'items' => array('|','undo', 'redo')),

                // Cut / copy / paste. These features are limited to web browsers, as other devices have these implemented into their basic
                // text input API's.
                array('name' => 'clipboard', 'items' => array('|','Cut', 'copy', 'paste', 'pastetext', 'pasteword')),
            );
        }

        $resolver->setDefaults(array(
            'required' => false,
            'inline'   => true,
            'purify'   => false,
            'scayt'    => true,
            'height'   => 250,
            'toolbar'  => $toolbar
        ));
    }
}
