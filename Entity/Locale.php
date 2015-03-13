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
use Rednose\FrameworkBundle\Model\Locale as BaseLocale;

/**
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_locale")
 *
 * @Serializer\XmlRoot("locale")
 */
class Locale extends BaseLocale
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"list", "details", "file"})
     * @Serializer\XmlAttribute
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @Serializer\Groups({"list", "details", "file"})
     * @Serializer\XmlAttribute
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @Serializer\Groups({"details", "file"})
     * @Serializer\XmlAttribute
     */
    protected $displayName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Groups({"details", "file"})
     * @Serializer\XmlAttribute
     */
    protected $binding;

    /**
     * @ORM\ManyToOne(targetEntity="Rednose\FrameworkBundle\Entity\Organization")
     *
     * @ORM\JoinColumn(
     *   name="organization_id",
     *   referencedColumnName="id",
     *   onDelete="CASCADE")
     */
    protected $organization;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @Serializer\SerializedName("default")
     * @Serializer\Groups({"list", "details", "file"})
     * @Serializer\XmlAttribute
     */
    protected $isDefault;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }
}
