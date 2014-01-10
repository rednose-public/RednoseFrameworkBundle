<?php

namespace Rednose\FrameworkBundle\Model;

interface ControlInterface
{
    /**
     * Gets the id of the control
     *
     * @return integer
     */
    public function getId();

    /**
     * Gets the caption of the control
     *
     * @return string
     */
    public function getCaption();

    /**
     * Sets the caption of the control
     *
     * @param string $caption
     */
    public function setCaption($caption);

    /**
     * Sets the required flag
     *
     * @param boolean $required
     */
    public function setRequired($required);

    /**
     * Gets the required flag
     *
     * @param boolean
     */
    public function getRequired();

    /**
     * Sets the readonly flag
     *
     * @param boolean $readonly
     */
    public function setReadonly($readonly);

    /**
     * Gets the readonly flag
     *
     * @return boolean
     */
    public function getReadonly();

    /**
     * Is the readonly flag set?
     *
     * @return boolean $readonly
     */
    public function isReadonly();

    /**
     * Gets the handle of the control
     *
     * @return string
     */
    public function getHandle();

    /**
     * Sets the handle of the control
     *
     * @param string $handle
     */
    public function setHandle($handle);

    /**
     * Gets the control type
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the control type
     *
     * @param string $type
     */
    public function setType($type);

    /**
     * Gets the default value of the control
     *
     * @return string
     */
    public function getValue();

    /**
     * Sets the default value of the control
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Gets the various properties of this control
     *
     * @return Array
     */
    public function getProperties();

    /**
     * Gets the various properties of this control
     *
     * @param array $properties
     */
    public function setProperties($properties);

    /**
     * Gets the desired onscreen alignment of the control
     *
     * @return string
     */
    public function getAlignment();

    /**
     * Sets the desired onscreen alignment of the control
     *
     * @param string $alignment
     */
    public function setAlignment($alignment);

    /**
     * Gets the weight, for ordering
     *
     * @return integer
     */
    public function getWeight();

    /**
     * Sets the weight
     *
     * @param integer $weight
     */
    public function setWeight($weight);

    /**
     * Get parent ControlForm
     *
     * @return \ControlForm
     */
    public function getControlForm();

    /**
     * Sets the parent ControlForm
     *
     * @param \ControlForm $form
     */
    public function setControlForm(ControlFormInterface $form);
}
