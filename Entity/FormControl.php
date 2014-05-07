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
use Rednose\FrameworkBundle\Model\Control as BaseControl;
use Symfony\Component\Validator\Constraints as Assert;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Default control object
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form_control")
 *
 * @UniqueEntity(fields={"foreignId"}, message="This id is already in use, choose another id.")
 *
 * @Serializer\AccessorOrder("custom", custom = {"xmlId", "caption", "required", "help"})
 */
class FormControl extends BaseControl implements ExtrinsicObjectInterface
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
     * @ORM\Column(type="string", unique=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"details"})
     */
    protected $foreignId;

    /**
     * Transient property.
     *
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @Serializer\SerializedName("id")
     * @Serializer\Groups({"file"})
     * @Serializer\Accessor(getter="getForeignId")
     */
    protected $xmlId;

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
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a field caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $caption;

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
     * @ORM\Column(type="array")
     *
     * @Serializer\SerializedName("properties")
     * @Serializer\Groups({"details"})
     */
    protected $properties;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    protected $alignment = 'left';

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
     * @ORM\ManyToOne(targetEntity="Form")
     *
     * @ORM\JoinColumn(
     *   name="controlform_id",
     *   referencedColumnName="id",
     *   onDelete="CASCADE")
     */
    protected $controlForm;

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
    public function getForeignId()
    {
        return $this->foreignId;
    }

    /**
     * @param string $id
     */
    public function setForeignId($id)
    {
        $this->foreignId = $id;
    }

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

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        if ($this->xmlId) {
            $this->foreignId = $this->xmlId;
        }
    }
}
