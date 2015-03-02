<?php

namespace Rednose\FrameworkBundle\DataDictionary;

interface MergeableInterface
{
    /**
     * Merges a data set into a data dictionary
     *
     * @param \DOMDocument $data
     */
    public function merge(\DOMDocument $data);
}