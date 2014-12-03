<?php

namespace Rednose\FrameworkBundle\Util;

/**
 * Several XPath utility methods.
 */
class XpathUtil
{
    /**
     * Gets a single node at a given xpath location, within an optional context.
     *
     * @param \DOMDocument $data
     * @param string       $location
     * @param \DOMNode     $context
     *
     * @return \DOMNode
     */
    public static function getXpathNode(\DOMDocument $data, $location, $context = null)
    {
        if ($location === null) {
            return null;
        }

        $xpath = new \DOMXPath($data);

        $result = $xpath->query($location, $context);

        if ($result && $result->length > 0) {
            $node = $result->item(0);

            return $node;
        }

        return null;
    }
}
