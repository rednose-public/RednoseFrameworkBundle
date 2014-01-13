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
    const TYPE_RADIO    = 'radio';

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
     * Whether a value is required for this content item.
     *
     * @return boolean
     */
    public function isRequired();

    /**
     * Whether a value is required for this content item.
     *
     * @param boolean $required.
     */
    public function setRequired($required);

    /**
     * Whether this value can be edited from the user-interface.
     *
     * @return boolean
     */
    public function isProtected();

    /**
     * Whether this value can be edited from the user-interface.
     *
     * @param boolean $protected
     */
    public function setProtected($protected);

    /**
     * Whether this control is visible from the user-interface.
     *
     * @return boolean
     */
    public function isVisible();

    /**
     * Whether this control is visible from the user-interface.
     *
     * @param boolean $visible
     */
    public function setVisible($visible);

    /**
     * Whether this value can be edited from the user-interface and will be submitted in forms.
     *
     * @return boolean
     */
    public function isReadonly();

    /**
     * Whether this value can be edited from the user-interface and will be submitted in forms.
     *
     * @param boolean $readonly
     */
    public function setReadonly($readonly);
}
