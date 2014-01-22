<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Rednose\FrameworkBundle\Model\ContentSectionValueInterface;
use Rednose\FrameworkBundle\Model\ContentValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Rednose\FrameworkBundle\Model\ContentDefinitionInterface;
use Rednose\FrameworkBundle\Model\Node\Value\OutputValueNodeInterface;
use Rednose\FrameworkBundle\Model\Node\Value\InputValueNodeInterface;

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
    protected $contentSectionValue;

    /**
     * @param ContentSectionValueInterface $contentSectionValue
     *
     * @return array
     */
    public function transform($contentSectionValue)
    {
        if ($contentSectionValue === null) {
            return array();
        }

        assert($contentSectionValue instanceof ContentSectionValueInterface);

        $this->contentSectionValue = $contentSectionValue;

        $data = array();

        // Initialize defaults.
        foreach ($contentSectionValue->getContentSection()->getDefinitions() as $contentDefinition) {
            $data[$contentDefinition->getContentId()] = $this->getValue($contentDefinition);
        }

        // Transform all definitions that have a value assigned.
        foreach ($contentSectionValue->getContents() as $contentValue) {
            assert($contentValue instanceof ContentValueInterface);

            $data[$contentValue->getContentDefinition()->getContentId()] = $contentValue->getContent();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return ContentSectionValueInterface
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

                // Check if the field is not flagged as readonly to prevent creating unwanted content
                if ($contentDefinition->getContentItem()->isReadonly() === false) {
                    // Add the content to the section value.
                    $contentSectionValue->addContent($contentDefinition->getContentItem(), $data[$contentDefinition->getContentId()]);
                }
            }
        }

        return $contentSectionValue;
    }

    /**
     * Gets an initial value, either from an input node graph or a default value.
     *
     * @param ContentDefinitionInterface $definition
     *
     * @return string
     */
    protected function getValue(ContentDefinitionInterface $definition)
    {
        if ($definition instanceof OutputValueNodeInterface) {
            $inputNode = $definition->getInput();

            if ($inputNode !== null) {
                if ($inputNode instanceof InputValueNodeInterface) {
                    return $inputNode->getOutputValue();
                }
            }
        }

        return $definition->getDefaultValue();
    }
}
