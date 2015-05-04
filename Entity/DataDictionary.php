<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\DataDictionary\DataControl\DataControlInterface;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface;
use Rednose\FrameworkBundle\DataDictionary\MergeableTrait;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_data_dictionary")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Serializer\XmlRoot("dictionary")
 * @Serializer\XmlNamespace(uri="http://rednose.nl/schema/framework/dictionary")
 */
class DataDictionary implements DataDictionaryInterface
{
    use MergeableTrait;

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
     * @Assert\NotBlank(message="Please enter a name.")
     * @Assert\NotNull(message="Please enter a name.")
     * @Assert\Regex("/^[a-zA-Z0-9_]*$/i")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="DataControl", mappedBy="dictionary", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
     * @Assert\Valid
     *
     * @Serializer\XmlList(inline = true, entry = "control")
     * @Serializer\Groups({"file", "details"})
     */
    protected $controls;

    /**
     * @ORM\Column(type="text", name="test_data", nullable=true)
     *
     * @Serializer\Groups({"file", "details"})
     */
    protected $testData;

    /**
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    protected $organization;

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
     * @return \DOMDocument|null
     */
    public function getTestData()
    {
        if (!$this->testData) {
            return null;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($this->testData);

        return $dom;
    }

    /**
     * @param \DOMDocument|null $data
     */
    public function setTestData($data)
    {
        if ($data instanceof \DOMDocument) {
            $this->testData = $data->saveXML();
        } else {
            $this->testData = null;
        }
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return DataControlInterface[]
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @param DataControlInterface $control
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
     * @param string $separator
     *
     * @return bool
     */
    public function hasControl($path, $separator = '/')
    {
        return ($this->getControl($path, $separator) instanceof DataControlInterface);
    }

    /**
     * @param string $path
     * @param string $separator
     *
     * @return DataControlInterface
     */
    public function getControl($path, $separator = '/')
    {
        $segments = array_filter(explode($separator, $path), function($segment) {
            return $segment !== '';
        });

        if (array_shift($segments) !== $this->getName()) {
            return null;
        }

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
     * @return DataControlInterface
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

        if ($this->getControls()) {
            foreach ($this->getControls() as $control) {
                $control->setDictionary($this);

                if ($control->getSortOrder() === null) {
                    $control->setSortOrder($sortOrder++);
                }
            }
        }
    }

    /**
     * Return the dictionary as an array tree structure.
     *
     * @param string $composite Only return collections containing the provided composite.
     *
     * @return array
     */
    public function toArray($composite = null)
    {
        $nodes = array();

        foreach ($this->getControls() as $control) {
            if ($composite && $control->hasChildren()) {
                foreach ($control->getChildren() as $childControl) {
                    if ($childControl->getName() === $composite) {
                        $nodes[] = $this->createTreeNode($control, $composite);
                    }
                }
            } else {
                $nodes[] = $this->createTreeNode($control);
            }
        }

        return $nodes;
    }

    /**
     * Return the dictionary as a list, filtered by control type.
     *
     * @param array  $types   Control types to include. If left empty, all types are returned.
     * @param string $context XPath location for the context node.
     *                          Can be used to include relative nodes. If no context location is specified,
     *                          only controls with absolute paths are returned.
     *
     * @return array
     */
    public function toList(array $types = array(), $context = null)
    {
        $nodes = array();

        foreach ($this->getControls() as $control) {
            $nodes = array_merge($nodes, $this->controlToList($control, $types, $context));
        }

        return $nodes;
    }

    /**
     * Return the dictionary as an object
     *
     * @return Object
     */
    public function toObject()
    {
        $object = new \stdClass();

        foreach ($this->getControls() as $control) {
            $object->{$control->getName()} = $control->toProperty();
        }

        return $object;
    }

    /**
     * Utility method.
     *
     * @param DataControlInterface $control
     *
     * @return \DOMDocument
     */
    public function toXml(DataControlInterface $control = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        if ($control) {
            $node = $this->createControlNode($dom, $control, true);

            if ($node) {
                $dom->appendChild($node);
            }

            return $dom;
        }

        // Create root element.
        $root = $dom->createElement($this->getName());
        $dom->appendChild($root);

        foreach ($this->getControls() as $control) {
            $node = $this->createControlNode($dom, $control);

            if ($node) {
                $root->appendChild($node);
            }
        }

        return $dom;
    }

    /**
     * Utility method.
     *
     * @param DataControlInterface $control
     *
     * @return \DOMDocument
     */
    public function toXsd(DataControlInterface $control = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $namespace = 'http://www.w3.org/2001/XMLSchema';

        $schema = $dom->createElementNS($namespace, 'xs:schema');
        $dom->appendChild($schema);

        $element = $dom->createElementNS($namespace, 'element');
        $element->setAttribute('name', $this->getName());
        $schema->appendChild($element);

        $complexType = $dom->createElementNS($namespace, 'complexType');
        $element->appendChild($complexType);

        $sequence = $dom->createElementNS($namespace, 'sequence');
        $complexType->appendChild($sequence);

        foreach ($this->getControls() as $control) {
            $sequence->appendChild($this->createDefinitionNode($dom, $control));
        }

        return $dom;
    }

    /**
     * @param \DOMDocument         $dom
     * @param DataControlInterface $control
     *
     * @return \DOMElement
     */
    protected function createDefinitionNode(\DOMDocument $dom, DataControlInterface $control)
    {
        $namespace = 'http://www.w3.org/2001/XMLSchema';

        $node = $currentNode = $dom->createElementNS($namespace, 'element');
        $node->setAttribute('name', $control->getName());

        if ($control->getType() === DataControlInterface::TYPE_STRING) {
            $node->setAttribute('type', 'xs:string');
        }

        elseif (in_array($control->getType(), array(DataControlInterface::TYPE_COLLECTION, DataControlInterface::TYPE_COMPOSITE))) {
            $complexType = $dom->createElementNS($namespace, 'complexType');
            $node->appendChild($complexType);

            $sequence = $dom->createElementNS($namespace, 'sequence');
            $complexType->appendChild($sequence);

            $currentNode = $sequence;
        }

        foreach ($control->getChildren() as $child) {
            $currentNode->appendChild($this->createDefinitionNode($dom, $child));
        }

        return $node;
    }

    /**
     * @param \DOMDocument         $dom
     * @param DataControlInterface $control
     * @param bool                 $relative
     *
     * @return \DOMElement
     */
    protected function createControlNode(\DOMDocument $dom, DataControlInterface $control, $relative = false)
    {
        // Don't create nodes for relative prototypes.
        if ($control->isRelative() && !$relative) {
            return null;
        }

        $node = $dom->createElement($control->getName());

        if ($control->hasAttributes() === true) {
            foreach ($control->getAttributes() as $key => $value) {
                $node->setAttribute($key, $value);
            }
        }

        if ($value = $control->getValue()) {
            if ($control->getType() === DataControlInterface::TYPE_COLLECTION && is_array($value)) {
                foreach ($value as $child) {
                    $childNode = $this->createControlNode($dom, $child, true);

                    if ($childNode) {
                        $node->appendChild($childNode);
                    }
                }

            } else {

                if ($value instanceof \DateTime) {
                    $value = $value->format(\DateTime::ISO8601);
                }

                $node->appendChild($dom->createCDATASection($value));
            }
        }

        if ($control->hasChildren()) {
            foreach ($control->getChildren() as $child) {
                $childNode = $this->createControlNode($dom, $child, $relative);

                if ($childNode) {
                    $node->appendChild($childNode);
                }
            }
        }

        return $node;
    }

    /**
     * @param DataControlInterface $control
     * @param array                $types
     * @param string               $context
     *
     * @return array
     */
    private function controlToList(DataControlInterface $control, array $types, $context)
    {
        $nodes = array();

        if ($context !== null && $control->isRelative() && !$control->isInContext($context)) {
            return $nodes;
        }

        if (empty($types) || in_array($control->getType(), $types)) {
            $nodes[] = $this->createListNode($control);
        }

        if ($control->hasChildren()) {
            foreach ($control->getChildren() as $child) {
                $nodes = array_merge($nodes, $this->controlToList($child, $types, $context));
            }
        }

        return $nodes;
    }

    /**
     * @param DataControlInterface  $control
     * @param string                $composite
     *
     * @return array
     */
    private function createTreeNode(DataControlInterface $control, $composite = null)
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
                if ($composite === null || $child->getName() === $composite) {
                    $node['children'][] = $this->createTreeNode($child);
                }
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

    public function __clone()
    {
        if ($this->getId()) {
            $controls = $this->getControls();

            $this->controls = new ArrayCollection();

            foreach ($controls as $control) {
                $this->addControl(clone $control);
            }
        }
    }
}
