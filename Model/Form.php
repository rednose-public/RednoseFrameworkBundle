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

/**
 * The abstract controlform.
 */
abstract class Form
{
    protected $id;
    protected $name;
    protected $identity;
    protected $caption;

    /**
     * @var FormSection[]
     */
    protected $sections;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection();
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
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
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
     * @param FormSection $section
     */
    public function addSection(FormSection $section)
    {
        $section->setForm($this);

        $this->sections->add($section);
    }

    /**
     * @return FormSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
