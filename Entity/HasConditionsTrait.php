<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\PrioritizedArray;

/**
 * Class HasConditionsTrait
 *
 * @package Rednose\FrameworkBundle\Entity
 */
trait HasConditionsTrait
{
    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @Serializer\XmlList(inline = false, entry = "conditions")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"file", "details"})
     */
    protected $conditions = null;

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @return PrioritizedArray
     */
    public function getConditions()
    {
        $arr = new PrioritizedArray('conditions');

        if (!$this->conditions) {
            return $arr;
        }

        $arr->load($this->conditions);

        return $arr;
    }

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param string[] $conditions
     */
    public function setConditions(PrioritizedArray $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist()
    {
        if (!$this->conditions) {
            $this->conditions = null;

            return;
        }

        $this->conditions  = unserialize((string)$this->conditions);
    }
}