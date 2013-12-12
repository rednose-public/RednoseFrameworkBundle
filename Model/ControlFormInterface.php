<?php

namespace Rednose\FrameworkBundle\Model;

interface ControlFormInterface
{
    /**
     * Gets the id of the form
     *
     * @return integer
     */
    public function getId();

    /**
     * Gets the name of the form
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name of the form
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Gets the caption of the form
     *
     * @return string
     */
    public function getCaption();

    /**
     * Sets the caption of the form
     *
     * @param string $caption
     */
    public function setCaption($caption);

    /**
     * Adds a control to the form
     *
     * @param \Control $control
     */
    public function addControl(ControlInterface $control);

    /**
     * Gets the controls that this form contains
     *
     * @return ArrayCollection
     */
    public function getControls();
}
