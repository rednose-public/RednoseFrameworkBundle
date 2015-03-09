<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\Organization as BaseOrganization;

/**
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_organization")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name" ,"DataDictionaryId"})
 */
class Organization extends BaseOrganization
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locale = new ArrayCollection();
    }

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
     * @ORM\OneToMany(
     *     targetEntity="Locale",
     *     orphanRemoval=true,
     *     mappedBy="organization",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $locale;

    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @Serializer\XmlList(inline = false, entry = "conditions")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"file", "details"})
     */
    protected $conditions;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
