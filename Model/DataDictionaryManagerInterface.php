<?php

namespace Rednose\FrameworkBundle\Model;

interface DataDictionaryManagerInterface
{
    /**
     * @return DataDictionaryInterface[]
     */
    public function findDictionaries(OrganizationInterface $organization = null);

    /**
     * @param string $id
     *
     * @return DataDictionaryInterface
     */
    public function findDictionaryById($id);

    /**
     * @param array $criteria
     *
     * @return DataDictionaryInterface
     */
    public function findDictionaryBy(array $criteria);

    /**
     * @param DataDictionaryInterface $dictionary
     * @param bool $flush
     */
    public function updateDictionary(DataDictionaryInterface $dictionary, $flush = true);

    /**
     * @param DataDictionaryInterface $dictionary
     */
    public function deleteDictionary(DataDictionaryInterface $dictionary);
}