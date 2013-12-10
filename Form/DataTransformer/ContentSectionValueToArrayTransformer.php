<?php

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Rednose\FrameworkBundle\Model\ContentDefinitionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionInterface;
use Rednose\FrameworkBundle\Model\ContentSectionValueInterface;
use Rednose\FrameworkBundle\Model\ContentValueInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms all controls and their values within a content section to an array that can be parsed by
 * the ContentSectionType.
 */
class ContentSectionValueToArrayTransformer implements DataTransformerInterface
{
    /**
     * Stores a reference to the original value object.
     *
     * @var ContentSectionValueInterface
     */
    protected $contenSectionValue;

    /**
     * {@inheritdoc}
     */
    public function transform($contentSectionValue)
    {
        if ($contentSectionValue === null) {
            return array();
        }

        assert($contentSectionValue instanceof ContentSectionValueInterface);

        $this->contentSectionValue = $contentSectionValue;

        $data = array();

        // Transform all definitions that have a value assigned.
        foreach ($contentSectionValue->getContents() as $contentValue) {
            assert($contentValue instanceof ContentValueInterface);

            $data[$contentValue->getContentDefinition()->getContentId()] = $contentValue->getContent();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $contentSectionValue = $this->contentSectionValue;

        // Clear the contents.
        $contentSectionValue->clearContents();

        // Loop through all content definitions and add content if the form data contains a value for it.
        foreach ($contentSectionValue->getContentSection()->getDefinitions() as $contentDefinition) {

            // Check for existing form data for this definition.
            if (isset($data[$contentDefinition->getContentId()])) {

                // Add the content to the section value.
                $contentSectionValue->addContent($contentDefinition->getContentItem(), $data[$contentDefinition->getContentId()]);
            }
        }

        return $contentSectionValue;
    }
}
