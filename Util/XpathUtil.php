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

    /**
     * @param \DOMDocument $data
     * @param string       $query
     * @param \DOMNode     $context
     *
     * @return mixed
     */
    public static function evaluate(\DOMDocument $data, $query, $context = null)
    {
        // Quickfix to avoid the need for single quote escaping.
        if (substr($query, 0, 1) === "'" && substr($query, -1, 1) === "'") {
            return trim($query, "'");
        }

        $xpath = new \DOMXPath($data);

        $result = $xpath->evaluate($query, $context);

        if ($result === false) {
            throw new \InvalidArgumentException(sprintf('Invalid XPath expression: `%s`', $query));
        }

        if ($result instanceof \DOMNodeList && $result->length > 0) {
            return $result->item(0)->nodeValue;
        }

        return null;
    }
}
