<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\DataTransformer\PrioritizedArrayDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrioritizedCollectionType extends AbstractType
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $name = $form->getName();

        $view->vars['priorities'] = $options['priorities'];

        foreach ($form->getData() as $offset => $item) {
            $form->add($name . '_' . $offset, 'text');

            if ($options['priorities'] === false) {
                $form->add('priority_' . $offset, 'hidden', [ 'data' => '0' ]);
            } else {
                $form->add('priority_' . $offset, 'choice', ['choices' => [
                    0 => 'Normal', 1 => 'High', 2 => 'Very High'
                ], 'attr' => ['select2-skip' => 'true']]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new PrioritizedArrayDataTransformer(
            $builder->getForm()->getName(), $this->request
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            // Add the form-fields before the data-mapper does its thing.
            $form = $event->getForm();

            if (is_array($event->getData()) === false) {
                return null;
            }

            foreach ($event->getData() as $key => $data) {
                $form->add($key, 'text', [ 'data' => $data ]);
            }
        }, 50);
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
            'allow_extra_fields' => false,
            'priorities' => true
        ));
    }
}
