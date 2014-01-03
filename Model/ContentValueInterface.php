<?php

namespace Rednose\FrameworkBundle\Model;

/**
 * Stores a value for a content definition.
 */
interface ContentValueInterface
{
    /**
     * Gets the Content Definition for this Content Value instance.
     *
     * @return ContentDefinitionInterface A definition object.
     */
    public function getContentDefinition();

    /**
     * Gets the current content value this instance stores.
     *
     * @return string A value.
     */
    public function getContent();

    /**
     * Sets the current content value this instance stores.
     *
     * @param string $value A value.
     */
    public function setContent($value);
}
