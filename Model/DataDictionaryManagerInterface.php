<?php

namespace Rednose\FrameworkBundle\Model;

interface DataDictionaryManagerInterface
{
    /**
     * @param DataDictionaryInterface $dictionary
     */
    public function deleteDictionary(DataDictionaryInterface $dictionary);

    /**
     * @param string $id
     *
     * @return DataDictionaryInterface
     */
    public function findDictionaryById($id);

    /**
     * @param DataDictionaryInterface $dictionary
     * @param bool $flush
     */
    public function updateDictionary(DataDictionaryInterface $dictionary, $flush = true);

    /**
     * @return DataDictionaryInterface[]
     */
    public function findDictionaries(OrganizationInterface $organization = null);

    /**
     * @param array $criteria
     *
     * @return DataDictionaryInterface
     */
    public function findDictionaryBy(array $criteria);

    /**
     * Merges a data set into a data dictionary
     *
     * @param DataDictionaryInterface $dictionary
     * @param \DOMDocument $data
     *
     * @return DataDictionaryInterface
     */
    public function merge(DataDictionaryInterface $dictionary, \DOMDocument $data);
}