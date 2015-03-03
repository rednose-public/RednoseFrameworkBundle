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
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_organization")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name" ,"DataDictionaryId"})
 */
class Organization implements OrganizationInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"list", "details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"list"})
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionary")
     * @ORM\JoinColumn(name="dictionary_id", referencedColumnName="id", nullable=true)
     */
    protected $dictionary;

    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @Serializer\XmlList(inline = false, entry = "conditions")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"file", "details"})
     */
    protected $conditions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->conditions = array();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("data_dictionary")
     * @Serializer\Groups({"list"})
     *
     * @return string
     */
    public function getDataDictionaryId()
    {
        if ($this->getDataDictionary() === null) {
            return null;
        }

        return $this->getDataDictionary()->getId();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return DataDictionaryInterface
     */
    public function getDataDictionary()
    {
        return $this->dictionary;
    }

    /**
     * @param \Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface $dictionary
     */
    public function setDataDictionary($dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @return string[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param string[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * Adds a condition.
     *
     * @param string $condition
     */
    public function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * Removes a condition.
     *
     * @param string $condition
     */
    public function removeCondition($condition)
    {
        $index = array_search($condition, $this->conditions);

        if ($index !== false) {
            unset($this->conditions[$index]);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
