<?php

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class DocumentToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var XmlEncoder
     */
    protected $encoder;

    public function __construct()
    {
        $this->encoder = new XmlEncoder('form');
    }

    /**
     * @param \DOMDocument $dom
     *
     * @return array
     */
    public function transform($dom)
    {
        if ($dom === null) {
            return array();
        }

        return $this->encoder->decode($dom->saveXML(), 'xml');
    }

    /**
     * @param array $data
     *
     * @return \DOMDocument
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->loadXML($this->encoder->encode($data, 'xml'));

        return $dom;
    }
}
