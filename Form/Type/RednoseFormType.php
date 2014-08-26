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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Rednose\FrameworkBundle\Model\Form;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class RednoseFormType extends AbstractType
{
    protected $conditions = array();

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->builder = $builder;

        if (!isset($options['form']) || !$options['form'] instanceof Form) {
            throw new \InvalidArgumentException('Form must be instance of `Rednose\FrameworkBundle\Model\Form`');
        }

        $form = $options['form'];

        $bindings = array();

        foreach ($form->getSections() as $section) {
            foreach ($section->getControls() as $control) {
                if ($control->getBinding()) {
                    $bindings[$control->getPath()] = $control->getBinding();
                }
            }
        }

        $this->transformer = new DocumentToArrayTransformer($bindings);

        $builder->addViewTransformer($this->transformer);

        // XXX
        $data = $options['data'] ?: array();

        foreach ($data as $key => $value) {
            $data[$key]['bijAfwezigheidVan'] = $data[$key]['bijAfwezigheidVan'] ? 'true' : 'false';
        }

        $data = $this->transformer->transform($data);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $encoder = new XmlEncoder('form');
        $dom->loadXML($encoder->encode($data, 'xml'));

        foreach ($form->getSections() as $section) {
            $builder->add($section->getName(), 'content_section', array(
                'section' => $section,
                'dom'     => $dom,
                'label'   => false,
                'legend'  => $section->getCaption(),
                'attr'    => array(
                    'data-section' => $section->getName()
                )
            ));
        }
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
