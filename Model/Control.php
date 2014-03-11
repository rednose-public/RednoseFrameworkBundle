<?php

namespace Rednose\FrameworkBundle\Model;

/**
 * The abstract control class.
 */
abstract class Control implements ContentDefinitionInterface, ControlInterface
{
    protected $id;
    protected $value;
    protected $handle;
    protected $caption;
    protected $type;
    protected $properties;
    protected $controlForm;
    protected $sortOrder;
    protected $required;
    protected $visible;
    protected $protected;
    protected $alignment;
    protected $readonly;
    protected $help;

    public function __construct()
    {
        $this->type       = ContentDefinitionInterface::TYPE_TEXT;
        $this->properties = array();
        $this->sortOrder  = 0;
        $this->required   = false;
        $this->visible    = true;
        $this->protected  = false;
        $this->readonly   = false;
    }

    /**
     * Gets the id of the control
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentId()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getContentItem()
    {
        return $this;
    }

    /**
     * Sets the id of the control
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return mixed
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @see ControlInterface
     */
    public function getReadonly()
    {
        return $this->readonly;
    }

    /**
     * @see ControlInterface
     */
    public function isReadonly()
    {
        return ($this->readonly === true);
    }

    /**
     * @see ControlInterface
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
    }

    /**
     * Gets the handle of the control
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Sets the handle of the control
     *
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Gets the control type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the control type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Gets the default value of the control
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the default value of the control
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Gets the various properties of this control
     *
     * @return Array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Gets the various properties of this control
     *
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Gets the desired onscreen alignment of the control
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Sets the desired onscreen alignment of the control
     *
     * @param string $alignment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * Gets the sortOrder
     *
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Sets the sortOrder
     *
     * @param integer $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * Get parent ControlForm
     *
     * @return ControlFormInterface
     */
    public function getControlForm()
    {
        return $this->controlForm;
    }

    /**
     * Sets the parent ControlForm
     *
     * @param ControlFormInterface $form
     */
    public function setControlForm(ControlFormInterface $form)
    {
        $this->controlForm = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return ($this->required === true);
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * {@inheritdoc}
     */
    public function setHelp($help)
    {
        $this->help = $help;
    }

    /**
     * Whether this value can be edited from the user-interface.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return $this->protected;
    }

    /**
     * Whether this value can be edited from the user-interface.
     *
     * @param boolean $protected
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;
    }

    /**
     * Whether this control is visible from the user-interface.
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Whether this control is visible from the user-interface.
     *
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    // TODO: Deprecate getValue and setValue in favor of these interface methods.

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultValue($value)
    {
        $this->value = $value;
    }
}
