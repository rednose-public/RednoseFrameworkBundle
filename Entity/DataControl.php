<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\DataControlInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A data control object.
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_data_control")
 *
 * @UniqueEntity(fields={"name", "dictionary", "parent"}, message="This name is already in use, choose another name")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "type", "required"})
 */
class DataControl implements DataControlInterface
{
    /**
     * @Serializer\SerializedName("icon")
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getIcon")
     * @Serializer\Groups({"list", "details"})
     */
    protected $icon;

    /**
     * Unique id.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"list", "details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $required;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="DataControl", mappedBy="parent", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     *
     * @Serializer\Type("array<Rednose\FrameworkBundle\Entity\DataControl>")
     * @Serializer\XmlList(inline = true, entry = "control")
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="DataControl", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @Serializer\Accessor(getter="getPath")
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"list", "details"})
     */
    protected $path = '/';

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionary", inversedBy="controls")
     * @ORM\JoinColumn(name="dictionary_id", referencedColumnName="id", nullable=true)
     */
    protected $dictionary;

    public function __construct(DataDictionaryInterface $dictionary)
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \Rednose\FrameworkBundle\Model\DataControlInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param DataDictionaryInterface $dictionary
     */
    public function setDictionary(DataDictionaryInterface $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return DataDictionaryInterface
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        switch ($this->type) {
            case self::TYPE_COMPOSITE:
                return 'icon-list';

            case self::TYPE_BOOLEAN:
                return 'icon-ok-sign';

            case self::TYPE_DATE:
                return 'icon-calendar';

            case self::TYPE_NUMBER:
                return 'icon-th';

            case self::TYPE_COLLECTION:
                return 'icon-repeat';

            case self::TYPE_STRING:
                return 'rednose-icon-text';

            case self::TYPE_HTML:
                return 'rednose-icon-textarea';
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $path    = '';
        $control = $this;

        while ($control->getParent() !== null) {
            $control = $control->getParent();

            $path = $control->getName() . '.' . $path;
        }

        return $path . $this->getName();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasChild($name)
    {
        foreach ($this->getChildren() as $control) {
            if ($control->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        foreach ($this->getChildren() as $child) {
            $child->setParent($this);
        }
    }
}
