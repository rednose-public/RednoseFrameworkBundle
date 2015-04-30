<?php

namespace Rednose\FrameworkBundle\DataDictionary\DataControl;

interface HasAttributesInterface
{
    /**
     * Get a attribute
     *
     * @param string $key
     * @return string
     */
    public function getAttribute($key);

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Create or overwrite a attribute
     *
     * @param string $key
     * @param string $name
     */
    public function setAttribute($key, $name);

    /**
     * Check if a attribute is set.
     *
     * @param string $key
     * @return boolean
     */
    public function hasAttribute($key);

    /**
     * Check if attributes are a set.
     *
     * @return boolean
     */
    public function hasAttributes();
}
