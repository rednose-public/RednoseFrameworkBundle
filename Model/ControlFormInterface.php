<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Sets the caption of the form
     *
     * @param string $caption
     */
    public function setCaption($caption);

    /**
     * Adds a control to the form
     *
     * @param ControlInterface $control
     */
    public function addControl(ControlInterface $control);

    /**
     * Gets the controls that this form contains
     *
     * @return ControlInterface[]
     */
    public function getControls();
}
