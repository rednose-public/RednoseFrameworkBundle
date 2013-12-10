<?php

namespace Rednose\FrameworkBundle\Model;

/**
 * Defines an item's properties in the context of a human interface.
 */
interface ContentDefinitionInterface
{
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';

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
