<?php

namespace Rednose\FrameworkBundle\Model;


interface OrganizationInterface
{
    /**
     * Set the id
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Get the id
     *
     * @return string
     */
    public function getId();

    /**
     * Set organization name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get organization
     *
     * @return string
     */
    public function getName();

    /**
     * Set organization dictionary
     *
     * @param DataDictionaryInterface $dictionary
     */
    public function setDictionary(DataDictionaryInterface $dictionary);

    /**
     * Get organization dictionary
     *
     * @return DataDictionaryInterface
     */
    public function getDictionary();

    /**
     * Set organizations available locale
     *
     * @param ArrayCollection<LocaleInterface> $locale
     */
    public function setLocale(ArrayCollection $locale);

    /**
     * Get organizations available available
     *
     * @return ArrayCollection<LocaleInterface>
     */
    public function getLocale();

    // -- [ Additional ] -----------------------------------------------------------

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
     * A list of OR conditions to evaluate on a user object
     * when deciding to assign a user to this organization.
     *
     * @param string $condition
     */
    public function addCondition($condition);
}
