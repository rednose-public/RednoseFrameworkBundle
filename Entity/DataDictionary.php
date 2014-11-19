<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\DataControlInterface;
use Rednose\FrameworkBundle\Model\DataDictionaryInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Blueprint
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_data_dictionary")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Serializer\XmlRoot("dictionary")
 * @Serializer\XmlNamespace(uri="http://rednose.nl/schema/framework/dictionary")
 */
class DataDictionary implements DataDictionaryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @Serializer\Groups({"file", "list", "details"})
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
     * @ORM\OneToMany(targetEntity="DataControl", mappedBy="dictionary", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
     * @Serializer\Type("array<Rednose\FrameworkBundle\Entity\DataControl>")
     * @Serializer\XmlList(inline = true, entry = "control")
     * @Serializer\Groups({"file", "details"})
     */
    protected $controls;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->controls = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
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
     * @return \Rednose\FrameworkBundle\Model\DataControlInterface[]
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @param \Rednose\FrameworkBundle\Model\DataControlInterface $control
     */
    public function addControl(DataControlInterface $control)
    {
        $this->controls->add($control);
    }

    /**
     * @param DataControlInterface $control
     */
    public function removeControl(DataControlInterface $control)
    {
        $this->controls->removeElement($control);
    }

    /**
     * Returns whether a control for a given path exists or not.
     *
     * @param string $path
     *
     * @return bool
     */
    public function hasControl($path)
    {
        return ($this->getControl($path) instanceof DataControlInterface);
    }

    /**
     * @param string $path
     *
     * @return DataControlInterface
     */
    public function getControl($path)
    {
        $segments = explode('.', $path);

        $cur = $this;

        foreach ($segments as $segment) {
            if ($cur->hasChild($segment) === false) {
                return null;
            }

            $cur = $cur->getChild($segment);
        }

        return $cur;
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
        foreach ($this->getControls() as $control) {
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

        foreach ($this->getControls() as $control) {
            $control->setDictionary($this);
            $control->setSortOrder($sortOrder++);
        }
    }

    /**
     * Return the dictionary as an array tree structure.
     *
     * @return array
     */
    public function toArray()
    {
        $nodes = array();

        foreach ($this->getControls() as $control) {
            $nodes[] = $this->createTreeNode($control);
        }

        return $nodes;
    }

    /**
     * Return the dictionary as a list, filtered by control type.
     *
     * @param array $types
     *
     * @return array
     */
    public function toList(array $types = array())
    {
        $nodes = array();

        foreach ($this->getControls() as $control) {
            $nodes = array_merge($nodes, $this->controlToList($control, $types));
        }

        return $nodes;
    }

    /**
     * @param DataControlInterface $control
     * @param array                $types
     *
     * @return array
     */
    private function controlToList(DataControlInterface $control, array $types)
    {
        $nodes = array();

        if (empty($types) || in_array($control->getType(), $types)) {
            $nodes[] = $this->createListNode($control);
        }

        if ($control->hasChildren()) {
            foreach ($control->getChildren() as $child) {
                $nodes = array_merge($nodes, $this->controlToList($child, $types));
            }
        }

        return $nodes;
    }

    /**
     * @param DataControlInterface $control
     *
     * @return array
     */
    private function createTreeNode(DataControlInterface $control)
    {
        $node = array(
            'id'    => $control->getId(),
            'label' => $control->getName(),
            'icon'  => $control->getIcon(),
            'type'  => 'control',
            'data'  => $control,
        );

        if ($control->hasChildren()) {
            $node['children'] = array();

            foreach ($control->getChildren() as $child) {
                $node['children'][] = $this->createTreeNode($child);
            }
        }

        return $node;
    }

    /**
     * @param DataControlInterface $control
     *
     * @return array
     */
    private function createListNode(DataControlInterface $control)
    {
        return array(
            'id'    => $control->getId(),
            'label' => $control->getName(),
            'icon'  => $control->getIcon(),
            'type'  => 'control',
            'data'  => array(
                'name' => $control->getName(),
                'type' => $control->getType(),
                'path' => $control->getPath(),
            ),
        );
    }
}
