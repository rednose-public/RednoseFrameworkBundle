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
 * @ORM\HasLifecycleCallbacks()
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
    protected $locale = 'nl_NL';

    /**
     * @ORM\Column(type="array")
     */
    protected $localizations = ['nl_NL', 'en_GB'];

    /**
     * @ORM\ManyToOne(targetEntity="Theme")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=true)
     **/
    protected $theme;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme = null)
    {
        $this->theme = $theme;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
