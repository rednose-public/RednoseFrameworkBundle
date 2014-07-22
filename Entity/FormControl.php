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
use Rednose\FrameworkBundle\Model\FormControl as BaseFormControl;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Default control object
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form_control", uniqueConstraints={@UniqueConstraint(name="rednose_framework_form_control_unique", columns={"section_id", "name"})})
 *
 * @Serializer\AccessorOrder("custom", custom = {"name", "caption", "required", "help"})
 */
class FormControl extends BaseFormControl
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
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $required = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $protected = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $readonly = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $visible = true;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $caption;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $placeholder;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $value;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $binding;

    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @Serializer\XmlList(inline = false, entry = "binding")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"file", "details"})
     */
    protected $bindings;

    /**
     * @ORM\Column(type="array")
     *
     * @Serializer\SerializedName("properties")
     * @Serializer\Groups({"details"})
     */
    protected $properties;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $sortOrder = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $help;

    /**
     * @ORM\ManyToOne(targetEntity="FormSection")
     *
     * @ORM\JoinColumn(
     *   name="section_id",
     *   referencedColumnName="id")
     */
    protected $section;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("properties")
     * @Serializer\Accessor(getter="getPropertiesAsJson", setter="setPropertiesAsJson")
     * @Serializer\Groups({"file"})
     */
    protected $jsonProperties;

    /**
     * @return string
     */
    public function getPropertiesAsJson()
    {
        if (empty($this->properties)) {
            return null;
        }

        return json_encode($this->properties);
    }

    /**
     * @param string $properties
     */
    public function setPropertiesAsJson($properties)
    {
        if ($properties === null) {
            $this->properties = array();

            return;
        }

        // Decode as associative array.
        $this->properties = json_decode($properties, true);
    }
}
