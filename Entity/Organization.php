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
use Rednose\FrameworkBundle\Model\DataDictionaryInterface;

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
     * @param mixed DataDictionaryInterface
     */
    public function setDataDictionary($dictionary)
    {
        $this->dictionary = $dictionary;
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
