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
            $properties = $contentDefinition->getProperties();
            $type       = null;
            $options    = array();

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
                // FIXME: Overriding the `attr` key in the $options array doens't merge correctly.
                $baseOptions = array_merge_recursive($baseOptions, array(
                    'attr' => array(
                        'data-id'   => $contentDefinition->getContentItem()->getForeignId(),
                        'data-type' => $contentDefinition->getContentItem()->getType(),
                    ),
                ));
            }

            switch ($contentDefinition->getType()) {
                case ContentDefinitionInterface::TYPE_TEXT:
                    $type = 'text';

                    break;

                case ContentDefinitionInterface::TYPE_AUTOCOMPLETE:
                    $type = 'rednose_autocomplete';

                    // FIXME: See above.
                    $options = array(
                        'attr' => array(
                            'data-id'     => $contentDefinition->getContentItem()->getForeignId(),
                            'data-type'   => $contentDefinition->getContentItem()->getType(),
                            'placeholder' => $this->translator->trans('type_to_search_placeholder'),
                        )
                    );

                    if (isset($properties['choices'])) {
                        $options['choices'] = $properties['choices'];
                    }

                    if (isset($properties['datasource'])) {
                        $options['datasource'] = $properties['datasource'];
                    }

                    break;

                case ContentDefinitionInterface::TYPE_DATETIME:
                    $type = 'datetime';

                    $date = new \DateTime();

                    $options = array(
                        'input'        => 'string',
                        'with_seconds' => false,
                        'date_format'  => \IntlDateFormatter::LONG,
                        'data'         => $date->format('Y-m-d H:i:s'),
                    );

                    break;

                case ContentDefinitionInterface::TYPE_CHECKBOX:
                    $type = 'checkbox';

                    break;

                case ContentDefinitionInterface::TYPE_HTML:
                    $type = 'rednose_widget_editor';

                    // FIXME: See above.
                    $options = array(
                        'required' => false,
                        'attr'     => array(
                            'data-id'       => $contentDefinition->getContentItem()->getForeignId(),
                            'data-type'     => $contentDefinition->getContentItem()->getType(),
                            'placeholder'   => $this->translator->trans('type_here_placeholder'),
                            'data-required' => $contentDefinition->isRequired()
                        )
                    );

                    if (isset($properties['height'])) {
                        $options['height'] = $properties['height'];
                    }

                    if (isset($properties['inline'])) {
                        $options['inline'] = $properties['inline'];
                    }

                    if (isset($properties['purify'])) {
                        $options['purify'] = $properties['purify'];
                    }

                    if (isset($properties['scayt'])) {
                        $options['scayt']  = $properties['scayt'];
                    }

                    if (isset($properties['toolbar'])) {
                        $options['toolbar'] = $properties['toolbar'];
                    }

                    break;

                case ContentDefinitionInterface::TYPE_DROPDOWN:
                case ContentDefinitionInterface::TYPE_RADIO:
                    $type = 'choice';

                    $options = array(
                        'choices'     => $properties['choices'],
                        'empty_value' => $this->translator->trans('Choose an option...'),
                        'expanded'    => $contentDefinition->getType() === ContentDefinitionInterface::TYPE_RADIO,
                    );

                    break;
            }

            $formOptions = array_merge($baseOptions, $options);

            // Initial form-connection implementation.
            $connections = is_array($properties) && isset($properties['connections']) ? $properties['connections'] : null;

            if ($connections) {
                $formOptions['attr']['data-connections'] = json_encode($connections);
            }

            $builder->add((string) $contentDefinition->getContentId(), $type, $formOptions);
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
