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
     * Gets the unique identifier for this item.
     *
     * @return mixed The id.
     */
    public function getId();

    /**
     * Gets the caption for this item.
     *
     * @return string The caption.
     */
    public function getCaption();

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
}
