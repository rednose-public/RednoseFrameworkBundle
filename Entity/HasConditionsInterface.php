<?php

namespace Rednose\FrameworkBundle\Entity;

use Rednose\FrameworkBundle\Model\PrioritizedArray;

interface HasConditionsInterface
{
    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @return PrioritizedArray
     */
    public function getConditions();

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param PrioritizedArray $conditions
     */
    public function setConditions(PrioritizedArray $conditions);
}