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
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"details"})
     */
    protected $sortOrder = 0;

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
     * @ORM\OneToMany(targetEntity="DataControl", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
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
     * @Serializer\Type("string")
     * @Serializer\Groups({"list", "details"})
     */
    protected $path = '/';

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionary", inversedBy="controls")
     * @ORM\JoinColumn(name="dictionary_id", referencedColumnName="id", nullable=true)
     */
    protected $dictionary;

    /**
     * Constructor.
     *
     * @param DataDictionaryInterface $dictionary
     */
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
     * @param integer $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
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
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children && ($this->children->count() > 0);
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

            case self::TYPE_COLLECTION:
                return 'icon-repeat';

            case self::TYPE_DATE:
                return 'icon-calendar';

            case self::TYPE_STRING:
                return 'rednose-icon-text';

            case self::TYPE_TEXT:
                return 'rednose-icon-textarea';

            case self::TYPE_HTML:
                return 'icon-align-left';
        }

        return null;
    }

    /**
     * @param string $context XPath location
     *
     * @return bool
     */
    public function isInContext($context)
    {
        $control = $this;

        while ($control) {
            if ($control->getPath() === $context) {
                return true;
            }

            $control = $control->getParent();
        }

        return false;
    }

    /**
     * A control is relative when it has at least one ancestor of the type `collection`
     *
     * @return bool
     */
    public function isRelative()
    {
        $control = $this->getParent();

        while ($control) {
            if ($control->getType() === DataControlInterface::TYPE_COLLECTION) {
                return true;
            }

            $control = $control->getParent();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->isRelative() === true) {
            return $this->getName();
        }

        $control = $this;
        $path = array();

        while ($control) {
            $path[] = $control->getName();

            if (!$control->getParent()) {
                $path[] = $control->getDictionary()->getName();
            }

            $control = $control->getParent();
        }

        if (empty($path)) {
            return null;
        }

        return '/'.implode('/', array_reverse($path));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasChild($name)
    {
        return ($this->getChild($name) instanceof DataControlInterface);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function getChild($name)
    {
        foreach ($this->getChildren() as $control) {
            if ($control->getName() === $name) {
                return $control;
            }
        }

        return null;
    }

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        $sortOrder = 0;

        foreach ($this->getChildren() as $child) {
            $child->setParent($this);
            $child->setSortOrder($sortOrder++);
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function validate()
    {
        $reflectionClass = new \ReflectionClass($this);

        foreach ($reflectionClass->getConstants() as $constant) {
            if ($this->type === $constant) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Invalid data-control type `%s` for control `%s`', $this->type, $this->name));
    }
}
