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
 * @Serializer\AccessorOrder("custom", custom = {"xmlns", "foreignId", "name", "styleSetName", "content", "controls"})
 */
class Form extends BaseControlForm implements ExtrinsicObjectInterface
{
    /**
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
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(message="Please enter a name.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
     protected $name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a form caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file"})
     */
    protected $caption;

    /**
     * @ORM\OneToMany(
     *   targetEntity="FormControl",
     *   orphanRemoval=true,
     *   mappedBy="controlForm",
     *   cascade={"persist", "remove"})
     * @ORM\OrderBy({"weight" = "ASC"})
     *
     * @Serializer\SerializedName("controls")
     * @Serializer\Groups({"file"})
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
     * Set up the bidirectional entity-relations after deserializing.
     *
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        if ($this->controls) {
            foreach ($this->controls as $control) {
                $control->setControlForm($this);
            }
        }
    }
}
