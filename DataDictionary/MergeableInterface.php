<?php

namespace Rednose\FrameworkBundle\DataDictionary;

interface MergeableInterface
{
    /**
     * Merges a data set into a data dictionary
     *
     * @param \DOMDocument $data
     * @param string       $locator
     */
    public function merge(\DOMDocument $data, $locator = null);
}