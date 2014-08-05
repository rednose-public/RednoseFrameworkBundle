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

use Doctrine\Common\Collections\ArrayCollection;

abstract class FormSection implements ContentSectionInterface
{
    protected $id;
    protected $name;
    protected $caption;
    protected $controls;
    protected $sections;
    protected $section;

    /**
     * @var boolean
     */
    protected $inline;

    /**
     * @var integer
     */
    protected $sortOrder;

    /**
     * @var Form
     */
    protected $form;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->controls = new ArrayCollection();
        $this->sections = new ArrayCollection();
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
     * @param FormControl $control
     */
    public function addControl(FormControl $control)
    {
        $control->setControlForm($this);

        $this->controls->add($control);
    }

    /**
     * Gets the controls that this form contains
     *
     * @return FormControl[]
     */
    public function getControls()
    {
        return $this->controls;
    }

    public function addSection(FormSection $section)
    {
        $section->setSection($this);

        $this->sections->add($section);
    }

    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param FormSection $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * @return FormSection
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param \Rednose\FrameworkBundle\Model\Form $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return \Rednose\FrameworkBundle\Model\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param boolean $inline
     */
    public function setInline($inline)
    {
        $this->inline = $inline;
    }

    /**
     * @return boolean
     */
    public function getInline()
    {
        return $this->inline;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
