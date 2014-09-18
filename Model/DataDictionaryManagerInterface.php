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
}