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
