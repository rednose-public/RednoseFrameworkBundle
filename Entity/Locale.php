<?php

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
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }
}
