<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\ControlForm as BaseControlForm;
use Rednose\FrameworkBundle\Model\ExtrinsicObjectInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Default ControlForm
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_metadata_control_form")
 *
 * @UniqueEntity(fields={"foreignId"}, message="This id is already in use, choose another id.")
 */
class Form extends BaseControlForm implements ExtrinsicObjectInterface
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
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a name.")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a form caption.")
     */
    protected $caption;

    /**
     * @ORM\OneToMany(
     *   targetEntity="FormControl",
     *   orphanRemoval=true,
     *   mappedBy="controlForm",
     *   cascade={"persist", "remove"})
     * @ORM\OrderBy({"weight" = "ASC"})
     */
    protected $controls;

    public function getForeignId()
    {
        return $this->foreignId;
    }

    public function setForeignId($id)
    {
        $this->foreignId = $id;
    }
}
