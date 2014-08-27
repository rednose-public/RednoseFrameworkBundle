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

use Rednose\FrameworkBundle\Form\DataTransformer\ContentSectionValueToArrayTransformer;
use Rednose\FrameworkBundle\Model\ContentDefinitionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionValueInterface;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Rednose\DataProviderBundle\Provider\DataProviderFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ContentSectionType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var DataProviderFactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param TranslatorInterface          $translator
     * @param ObjectManager                $om
     * @param DataProviderFactoryInterface $factory
     */
    public function __construct(TranslatorInterface $translator, ObjectManager $om, DataProviderFactoryInterface $factory)
    {
        $this->translator = $translator;
        $this->om         = $om;
        $this->factory    = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentSection = $options['section'];
//        $dom            = $options['dom'];

        if (!$contentSection instanceof ContentSectionInterface) {
            throw new \InvalidArgumentException('Section must be instance of `ContentSectionInterface`');
        }

//        if (!$dom instanceof \DOMDocument) {
//            throw new \InvalidArgumentException('Dom must be instance of `DOMDocument`');
//        }

        // XXX
        foreach ($contentSection->getSections() as $section) {
            $builder->add($section->getName(), 'collection', array(
                'allow_add' => true,
                'type' => 'content_section',
                'label' => false,
                'options' => array(
                    'section' => $section,
//                    'dom'     => $dom,
                    'label'   => false,
                    'legend'  => $section->getCaption(),
                    'attr'    => array(
                        'data-section' => $section->getName()
                    )
                )
            ));
        }

//        $xpath = new \DOMXPath($dom);

        $builder->setAttribute('inline', $contentSection->getInline());

        foreach ($contentSection->getDefinitions() as $contentDefinition) {
//            if ($contentDefinition->getType() === 'table') {
//                $properties = $contentDefinition->getProperties();
//
//                $builder->add((string) $contentDefinition->getName(), 'rednose_table', array(
//                    'table'         => $properties['table'],
//                    'parent_value'  => isset($properties['parentField']) ? $this->getXpathValue($xpath, $properties['parentField']) : null,
//                    'related_field' => isset($properties['relatedField']) ? $properties['relatedField'] : null,
//                    'columns'       => $properties['columns'],
//                ));
//
//                continue;
//            }

            $properties = $contentDefinition->getProperties();
            $type       = null;
            $options    = array();

            $visible = $contentDefinition->isVisible();

//            if (array_key_exists('conditions', $properties) && array_key_exists('visible', $properties['conditions'])) {
//                $condition = $properties['conditions']['visible'];
//                $visible   = $this->evaluateCondition($this->getXpathValue($xpath, $condition['a']), $this->getXpathValue($xpath, $condition['b']), $condition['operator']);
//            }

            $baseOptions = array(
                'label'     => $contentDefinition->getCaption() ?: false,
                'visible'   => $visible,
                'required'  => $contentDefinition->isRequired(),
                'help'      => $contentDefinition->getHelp(),
                'read_only' => $contentDefinition->isProtected(),
                'disabled'  => $contentDefinition->isReadonly(),
                'conditions' => $properties['conditions'],
            );

            if ($contentDefinition->isVisible() === false) {
                $baseOptions['attr'] = array('style' => 'display: none;');
            }

            if ($contentDefinition->getContentItem() instanceof ExtrinsicObjectInterface) {
                // FIXME: Overriding the `attr` key in the $options array doens't merge correctly.
                $baseOptions = array_merge_recursive($baseOptions, array(
                    'attr' => array(
                    ),
                ));
            }

            switch ($contentDefinition->getType()) {
                case 'button':
                    $type = 'button';

                    $options = array(
                        'label' => $contentDefinition->getValue(),
                    );

                    $baseOptions = array();

                    break;

                case 'image':
                    $type = 'rednose_image';

                    $options = array(
                        'default' => $contentDefinition->getValue(),
                    );

                    break;

                case ContentDefinitionInterface::TYPE_TEXT:
                    $type = 'text';

                    break;

                case 'integer':
                    $type = 'integer';

                    break;

                case ContentDefinitionInterface::TYPE_TEXTAREA:
                    $type = 'textarea';

                    if (isset($properties['rows'])) {
                        // FIXME: See above.
                        $options = array(
                            'required' => false,
                            'attr' => array(
                                'rows' => $properties['rows'],
                            )
                        );
                    }

                    break;

                case ContentDefinitionInterface::TYPE_AUTOCOMPLETE:
                    $type = 'rednose_autocomplete';

                    // FIXME: See above.
                    $options = array(
                        'attr' => array(
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
                        'date_format'  => \IntlDateFormatter::SHORT,
                        'data'         => $date->format('Y-m-d H:i:s'),
                        'date_widget'  => 'single_text',
                        'time_widget'  => 'single_text',
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

                    $choices = isset($properties['choices']) ? $properties['choices'] : array();

                    // Server side execution.
                    if (isset($properties['datasource']) && !isset($properties['datasource']['parent'])) {
                        $source = $this->om->getRepository('Rednose\DataProviderBundle\Entity\DataSource')->findOneBy(array(
                            'foreignId' => $properties['datasource']['id']
                        ));

                        if ($source === null) {
                            throw new \RuntimeException(sprintf('Datasource `%s` not does not exist', $properties['datasource']['id']));
                        }

                        $provider = $this->factory->create($source);

                        $map = $properties['datasource']['map'];

                        $choices = array();

                        foreach ($provider->getData() as $record) {
                            if (!array_key_exists($map['value'], $record) || !array_key_exists($map['text'], $record)) {
                                throw new \RuntimeException(sprintf('Invalid mapping for datasource `%s`', $source->getName()));
                            }

                            $value = $record[$map['value']];
                            $text  = $record[$map['text']];

                            $choices[$value] = $text;
                        }
                    }

                    $options = array(
                        'choices'     => $choices,
                        'required'    => $contentDefinition->isRequired(),
                        'empty_value' => $contentDefinition->isRequired() || $contentDefinition->getType() === ContentDefinitionInterface::TYPE_RADIO ? false : '...',
                        'expanded'    => $contentDefinition->getType() === ContentDefinitionInterface::TYPE_RADIO,
                    );

//                    if ($properties['datasource']) {
//                        $options['attr']['data-datasource'] = json_encode($properties['datasource']);
//                    }

                    break;
            }

            $formOptions = array_merge($baseOptions, $options);

            $formOptions['attr']['data-id']       = $contentDefinition->getContentItem()->getId();
            $formOptions['attr']['data-type']     = $contentDefinition->getContentItem()->getType();
            $formOptions['attr']['data-name']     = $contentDefinition->getName();
            $formOptions['attr']['data-required'] = $contentDefinition->isRequired();
            $formOptions['attr']['data-path']     = $contentSection->getName().'.'.$contentDefinition->getName();

            // Initial data-binding implementation.
            if ($contentDefinition->getBinding()) {
                $formOptions['attr']['data-binding'] = $this->getKeyFromXPath($contentDefinition->getBinding());
            }

            if ($contentDefinition->getBindings()) {
                $formOptions['attr']['data-bindings'] = json_encode($contentDefinition->getBindings());
            }

            if ($properties['datasource']) {
                $formOptions['attr']['data-datasource'] = json_encode($properties['datasource']);
            }

            if (array_key_exists('conditions', $properties)) {
                $formOptions['attr']['data-conditions'] = json_encode($properties['conditions']);
            }

            $builder->add((string) $contentDefinition->getName(), $type, $formOptions);
        }

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

        $this->builder = $builder;

//        $builder->add('save', 'submit');
//        $builder->addViewTransformer(new ContentSectionValueToArrayTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['inline'] = $form->getConfig()->getAttribute('inline');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'section'    => null,
            'dom'        => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content_section';
    }

    protected function getXpathValue(\DOMXPath $xpath, $query)
    {
        $result = $xpath->evaluate($query);

        if ($result instanceof \DOMNodeList) {
            if ($result->length > 0) {
                return $result->item(0)->nodeValue;
            }

            return '';
        }

        return $result;
    }

    protected function evaluateCondition($a, $b, $operator)
    {
        switch ($operator) {
            case '==':
                return ($a === $b);

            case '!=':
                return ($a !== $b);
        }

        return false;
    }

    private function getKeyFromXPath($xpath)
    {
        return end(explode('/', $xpath));
    }
}
