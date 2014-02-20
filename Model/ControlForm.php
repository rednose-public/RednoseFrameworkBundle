<?php

namespace Rednose\FrameworkBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * The abstract controlform.
 */
abstract class ControlForm implements ContentSectionInterface, ControlFormInterface
{
    protected $id;
    protected $name;
    protected $caption;
    protected $controls;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->controls = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        return $this->controls;
    }

    /**
     * Gets the id of the form
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the name of the form
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the form
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the caption of the form
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the caption of the form
     *
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Adds a control to the form
     *
     * @param ControlInterface $control
     */
    public function addControl(ControlInterface $control)
    {
        $control->setControlForm($this);

        $this->controls->add($control);
    }

    /**
     * Gets the controls that this form contains
     *
     * @return ControlInterface[]
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
