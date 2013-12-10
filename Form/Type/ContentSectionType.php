<?php

namespace Rednose\FrameworkBundle\Form\Type;

use Rednose\FrameworkBundle\Form\DataTransformer\ContentSectionValueToArrayTransformer;
use Rednose\FrameworkBundle\Model\ContentDefinitionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionValueInterface;
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
                // TODO: Add required to ContentDefinitionInterface
                'required' => false,
            );

            switch ($contentDefinition->getType()) {
                case ContentDefinitionInterface::TYPE_TEXT:
                    $type = 'text';

                    break;

                case ContentDefinitionInterface::TYPE_DROPDOWN:
                    $type = 'choice';

                    $properties = $contentDefinition->getProperties();

                    $options = array(
                        'choices'     => array_combine($properties['choices'], $properties['choices']),
                        'empty_value' => 'Choose an option',
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
