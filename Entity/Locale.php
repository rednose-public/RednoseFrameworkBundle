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
 */
class Locale extends BaseLocale
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @Serializer\Groups("detail")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Groups("detail")
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
