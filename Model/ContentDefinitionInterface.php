<?php

namespace Rednose\FrameworkBundle\Model;

/**
 * Defines an item's properties in the context of a human interface.
 */
interface ContentDefinitionInterface
{
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_HTML     = 'html';
    const TYPE_DATE     = 'date';
    const TYPE_DROPDOWN = 'dropdown';

    /**
     * Gets the unique identifier for the content item this object defines.
     *
     * @return mixed The id.
     */
    public function getContentId();

    /**
     * Gets the content item this object defines.
     *
     * @return mixed The item.
     */
    public function getContentItem();

    /**
     * Gets the caption for this item.
     *
     * @return string The caption.
     */
    public function getCaption();

    /**
     * Gets the help text for this item.
     *
     * @return string The caption.
     */
    public function getHelp();

    /**
     * Gets the default value for this item.
     *
     * @return string The caption.
     */
    public function getDefaultValue();

    /**
     * Gets the content type for this item.
     *
     * @return string The type.
     */
    public function getType();

    /**
     * Gets visual presentation properties for this specific item.
     *
     * @return array Array of rules.
     */
    public function getProperties();

    /**
     * Wether a value is required for this content item.
     *
     * @return bool Required.
     */
    public function isRequired();
}
