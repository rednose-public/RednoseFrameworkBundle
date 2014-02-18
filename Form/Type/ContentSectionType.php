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

use Rednose\FrameworkBundle\Form\DataTransformer\ContentSectionValueToArrayTransformer;
use Rednose\FrameworkBundle\Model\ContentDefinitionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionValueInterface;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ContentSectionType extends AbstractType
{
    protected $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator A translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['data']) === false && isset($options['form']) === false) {
            throw new \InvalidArgumentException('Data needs to be passed on form type construction, or option `form` needs to be specified.');
        }

        $contentSection = null;

        if (isset($options['form'])) {
            $contentSection = $options['form'];
        } else if (isset($options['data'])) {
            $data = $options['data'];

            if (!$data instanceof ContentSectionValueInterface) {
                throw new \InvalidArgumentException('Form data must implement ContentSectionValueInterface');
            }

            $contentSection = $data->getContentSection();
        }

        if (!$contentSection instanceof ContentSectionInterface) {
            throw new \InvalidArgumentException('Content section must implement ContentSectionInterface');
        }

        foreach ($contentSection->getDefinitions() as $contentDefinition) {
            $type    = null;
            $options = array();

            $baseOptions = array(
                'label'     => $contentDefinition->getCaption(),
                'required'  => $contentDefinition->isRequired(),
                'help'      => $contentDefinition->getHelp(),
                'read_only' => $contentDefinition->isProtected(),
                'disabled'  => $contentDefinition->isReadonly(),
            );

            if ($contentDefinition->isVisible() === false) {
                $baseOptions['attr'] = array('style' => 'display: none;');
            }

            if ($contentDefinition->getContentItem() instanceof ExtrinsicObjectInterface) {
                $baseOptions = array_merge_recursive($baseOptions, array(
                    'attr' => array(
                        'data-id' => $contentDefinition->getContentItem()->getForeignId(),
                    ),
                ));
            }

            switch ($contentDefinition->getType()) {
                case ContentDefinitionInterface::TYPE_TEXT:
                    $type = 'text';

                    break;

                case ContentDefinitionInterface::TYPE_HTML:
                    $type = 'rednose_widget_editor';

                    $options = array(
                        'required' => false,
                        'attr'     => array(
                            'data-id'       => $contentDefinition->getContentItem()->getForeignId(),
                            'placeholder'   => $this->translator->trans('Type here...'),
                            'data-required' => $contentDefinition->isRequired()
                        )
                    );

                    break;

                case ContentDefinitionInterface::TYPE_DROPDOWN:
                case ContentDefinitionInterface::TYPE_RADIO:
                    $type = 'choice';

                    $properties = $contentDefinition->getProperties();

                    $options = array(
                        'choices'     => $properties['choices'],
                        'empty_value' => $this->translator->trans('Choose an option...'),
                        'expanded'    => $contentDefinition->getType() === ContentDefinitionInterface::TYPE_RADIO,
                    );

                    break;
            }

            $builder->add((string) $contentDefinition->getContentId(), $type, array_merge($baseOptions, $options));
        }

        $builder->addViewTransformer(new ContentSectionValueToArrayTransformer);
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
        return 'content_section';
    }
}
