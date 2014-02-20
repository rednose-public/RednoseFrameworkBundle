<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\Control as BaseControl;
use Symfony\Component\Validator\Constraints as Assert;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Default control object
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form_control")
 *
 * @Serializer\AccessorOrder("custom", custom = {"foreignId", "caption", "required", "help"})
 */
class FormControl extends BaseControl implements ExtrinsicObjectInterface
{
    /**
     * Unique id.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("id")
     * @Serializer\Groups({"file"})
     */
    protected $foreignId;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $required = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $protected = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $readonly = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $visible = true;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a field caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("name")
     * @Serializer\Groups({"file"})
     */
    protected $caption;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $value;

    /**
     * @ORM\Column(type="array")
     */
    protected $properties;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    protected $alignment = 'left';

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
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
}
