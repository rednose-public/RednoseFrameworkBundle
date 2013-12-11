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

class ContentSectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Data needs to be passed on form type construction.
        assert(isset($options['data']));

        $data = $options['data'];

        assert($data instanceof ContentSectionValueInterface);

        $contentSection = $data->getContentSection();

        assert($contentSection instanceof ContentSectionInterface);

        foreach ($contentSection->getDefinitions() as $contentDefinition) {
            // Control implements ContentDefinitionInterface
            $type    = null;
            $options = array();

            $baseOptions = array(
                'label'    => $contentDefinition->getCaption(),
                'required' => $contentDefinition->isRequired(),
                'help'     => $contentDefinition->getHelp(),
            );

            if ($contentDefinition->getContentItem() instanceof ExtrinsicObjectInterface) {
                $baseOptions = array_merge($baseOptions, array(
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
                        'attr' => array(
                            'placeholder' => 'Type here...',
                        )
                    );

                    break;

                case ContentDefinitionInterface::TYPE_DROPDOWN:
                    $type = 'choice';

                    $properties = $contentDefinition->getProperties();

                    $options = array(
                        'choices'     => array_combine($properties['choices'], $properties['choices']),
                        'empty_value' => 'Choose an option...',
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
