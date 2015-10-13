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
use Rednose\FrameworkBundle\Model\Organization as BaseOrganization;

/**
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_organization")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name"})
 */
class Organization extends BaseOrganization
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
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Groups({"list", "details"})
     */
    protected $locale;

    /**
     * @ORM\Column(type="array")
     */
    protected $localizations;

    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @Serializer\XmlList(inline = false, entry = "conditions")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"file", "details"})
     */
    protected $conditions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->localizations = [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
