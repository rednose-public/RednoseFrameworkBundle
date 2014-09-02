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
     * @ORM\OneToMany(targetEntity="DataControl", mappedBy="dictionary", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     *
     * @Serializer\Type("array<Rednose\FrameworkBundle\Entity\DataControl>")
     * @Serializer\XmlList(inline = true, entry = "control")
     * @Serializer\Groups({"file", "list", "details"})
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
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        foreach ($this->getControls() as $control) {
            $control->setDictionary($this);
        }
    }
}
