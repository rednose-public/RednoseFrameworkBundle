<?php

namespace Rednose\FrameworkBundle\Model;

/**
 * A section functioning as container for multiple section definitions.
 */
interface ContentSectionInterface
{
    /**
     * Gets the caption for this section.
     *
     * @return string The caption.
     */
    public function getCaption();

    /**
     * Gets the definitions within this section.
     *
     * @return ContentDefinitionInterface[] The traversable defintions.
     */
    public function getDefinitions();
}
