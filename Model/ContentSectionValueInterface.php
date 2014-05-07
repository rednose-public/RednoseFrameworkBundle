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
 * A data object containing multiple ContentValue objects for a single ContentSection.
 */
interface ContentSectionValueInterface
{
    /**
     * Gets the Content Section Definition for this Value instance.
     *
     * @return ContentSectionInterface A definition object.
     */
    public function getContentSection();

    /**
     * Gets the content values for this section.
     *
     * @return ContentValueInterface[] The traversable values.
     */
    public function getContents();

    /**
     * Clears all content values for this section.
     */
    public function clearContents();

    /**
     * Adds a content value to this section.
     *
     * @param mixed  $contentItem The content item, defined by ContentDefinitionInterface::getContentItem.
     * @param string $value       The value to set for the content item.
     */
    public function addContent($contentItem, $value);
}
