<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\Control as BaseControl;
use Symfony\Component\Validator\Constraints as Assert;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;

/**
 * Default control object
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form_control")
 */
class FormControl extends BaseControl implements ExtrinsicObjectInterface
{
    /**
     * Unique id.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $foreignId;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $required = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $protected = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $readonly = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $visible = true;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a field caption.")
     */
    protected $caption;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @ORM\Column(type="array")
     */
    protected $properties;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    protected $alignment = 'left';

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $help;

    /**
     * @ORM\ManyToOne(targetEntity="Form")
     *
     * @ORM\JoinColumn(
     *   name="controlform_id",
     *   referencedColumnName="id",
     *   onDelete="CASCADE")
     */
    protected $controlForm;

    public function getForeignId()
    {
        return $this->foreignId;
    }

    public function setForeignId($id)
    {
        $this->foreignId = $id;
    }
}
