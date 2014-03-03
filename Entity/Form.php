<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\ControlForm as BaseControlForm;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Default ControlForm
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form")
 *
 * @UniqueEntity(fields={"foreignId"}, message="This id is already in use, choose another id.")
 *
 * @Serializer\XmlRoot("form")
 * @Serializer\AccessorOrder("custom", custom = {"xmlns", "xmlId", "name", "styleSetName", "content", "controls"})
 */
class Form extends BaseControlForm implements ExtrinsicObjectInterface
{
    /**
     * Transient property.
     *
     * @Serializer\Type("string")
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $xmlns = 'http://rednose.nl/schema/form';

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
     * @Serializer\SerializedName("id")
     * @Serializer\Type("string")
     * @Serializer\Groups({"file"})
     * @Serializer\Accessor(getter="getForeignId")
     */
    protected $xmlId;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(message="Please enter a name.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
     protected $name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a form caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $caption;

    /**
     * @ORM\OneToMany(
     *   targetEntity="FormControl",
     *   orphanRemoval=true,
     *   mappedBy="controlForm",
     *   cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
     * @Serializer\SerializedName("controls")
     * @Serializer\Groups({"file", "details"})
     * @Serializer\XmlList(inline = false, entry = "control")
     */
    protected $controls;

    public function getForeignId()
    {
        return $this->foreignId;
    }

    public function setForeignId($id)
    {
        $this->foreignId = $id;
    }

    // -- Serializer Methods ---------------------------------------------------

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        if ($this->xmlId) {
            $this->foreignId = $this->xmlId;
        }

        if ($this->controls) {
            $sortOrder = 0;

            foreach ($this->controls as $control) {
                $control->setControlForm($this);
                $control->setSortOrder($sortOrder++);
            }
        }
    }
}
