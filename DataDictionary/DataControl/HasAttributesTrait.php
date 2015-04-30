<?php

namespace Rednose\FrameworkBundle\DataDictionary\DataControl;

trait HasAttributesTrait
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * Get a attribute
     *
     * @param string $key
     * @return string
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Create or overwrite a attribute
     *
     * @param string $key
     * @param string $name
     */
    public function setAttribute($key, $name)
    {
        $this->attributes[$key] = $name;
    }

    /**
     * Set the attributes
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Check if a attribute is set.
     *
     * @param string $key
     * @return boolean
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Check if attributes are a set.
     *
     * @return boolean
     */
    public function hasAttributes()
    {
        return (count($this->attributes) > 0);
    }
}