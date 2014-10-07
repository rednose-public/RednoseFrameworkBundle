<?php

namespace Rednose\FrameworkBundle\Util;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlUtil
{
    /**
     * @param array  $data
     * @param string $root
     * @param bool   $format
     *
     * @return \DOMDocument
     */
    public static function fromArray(array $data, $root = null, $format = false)
    {
        $dom     = new \DOMDocument('1.0', 'UTF-8');
        $encoder = new XmlEncoder($root);

        $dom->formatOutput = $format;
        $dom->loadXML($encoder->encode($data, 'xml'));

        return $dom;
    }
}