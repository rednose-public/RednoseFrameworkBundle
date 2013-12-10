<?php

namespace Rednose\FrameworkBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A data object containing multiple ContentValue objects for a single ContentSection.
 */
interface ContentSectionValueInterface
{
    /**
     * Gets the Content Section Definition for this Value instance.
     *
     * @return ContentDefinitionInterface A definition object.
     */
    public function getContentSection();

    /**
     * Gets the content values for this section.
     *
     * @return ArrayCollection<ContentValueInterface> The traversable values.
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
