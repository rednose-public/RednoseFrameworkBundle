<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\FormSection as BaseFormSection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Default ControlForm
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form_section")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "identity", "content", "controls"})
 */
class FormSection extends BaseFormSection
{
    /**
     * Unique id.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please enter a name.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $sortOrder = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a form caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $caption;

    /**
     * @ORM\ManyToOne(targetEntity="Form")
     *
     * @ORM\JoinColumn(
     *   name="form_id",
     *   referencedColumnName="id")
     */
    protected $form;

    /**
     * @ORM\OneToMany(
     *   targetEntity="FormControl",
     *   orphanRemoval=true,
     *   mappedBy="section",
     *   cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
     * @Serializer\SerializedName("controls")
     * @Serializer\Groups({"file", "details"})
     * @Serializer\XmlList(inline = false, entry = "control")
     */
    protected $controls;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $inline = false;

    // -- Serializer Methods ---------------------------------------------------

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        if ($this->controls) {
            $sortOrder = 0;

            foreach ($this->controls as $control) {
                $control->setSection($this);
                $control->setSortOrder($sortOrder++);
            }
        }
    }
}
