<?php

namespace Rednose\FrameworkBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @return ArrayCollection<ContentDefinitionInterface> The traversable defintions.
     */
    public function getDefinitions();
}
