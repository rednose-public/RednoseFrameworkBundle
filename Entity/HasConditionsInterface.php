<?php

namespace Rednose\FrameworkBundle\Entity;

interface HasConditionsInterface
{
    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @return string[]
     */
    public function getConditions();

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param string[] $conditions
     */
    public function setConditions($conditions);

    /**
     * Adds a condition.
     *
     * @param string $condition
     */
    public function addCondition($condition, $priority = null);

    /**
     * Removes a condition.
     *
     * @param string $condition
     */
    public function removeCondition($condition);
}