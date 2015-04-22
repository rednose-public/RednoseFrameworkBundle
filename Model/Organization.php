<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Organization implements OrganizationInterface
{
    protected $id;
    protected $name;
    protected $dictionary;
    protected $locale;
    protected $conditions;

    /**
     * Set the id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set organizations available locale
     *
     * @param ArrayCollection<LocaleInterface> $locale
     */
    public function setLocale(ArrayCollection $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get organizations available available
     *
     * @return ArrayCollection<LocaleInterface>
     */
    public function getLocale()
    {
        return $this->locale;
    }

    // -- [ Additional ] -----------------------------------------------------------

    /**
     * Add a locale
     *
     * @param LocaleInterface $locale
     */
    public function addLocale(LocaleInterface $locale)
    {
        $locale->setOrganization($this);
        
        $this->getLocale()->add($locale);
    }


    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @return string[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param string[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * Adds a condition.
     *
     * @param string $condition
     */
    public function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * Removes a condition.
     *
     * @param string $condition
     */
    public function removeCondition($condition)
    {
        $index = array_search($condition, $this->conditions);

        if ($index !== false) {
            unset($this->conditions[$index]);
        }
    }
}
