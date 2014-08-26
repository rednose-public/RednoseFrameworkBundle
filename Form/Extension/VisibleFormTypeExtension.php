<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VisibleFormTypeExtension extends AbstractTypeExtension
{
    protected $visible = true;

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dom = $form->getConfig()->getOption('dom');

        if ($dom) {
            $result = $dom->getElementsByTagName('Recipient_Company')->item(0);

            if ($result && $result->nodeValue !== "" && $result->nodeValue !== null) {
                $this->visible = false;
            }
        }

        // TODO: Evaluate conditions here.
        $view->vars['visible'] = $this->visible;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'visible' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
