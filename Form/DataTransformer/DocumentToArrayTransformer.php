<?php

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class DocumentToArrayTransformer implements DataTransformerInterface
{
    public $dom;

    public $xpath;

    /**
     * @var XmlEncoder
     */
    protected $encoder;

    public function __construct(array $bindings)
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->xpath = new \DOMXPath($this->dom);

        $this->encoder  = new XmlEncoder('form');
        $this->bindings = $bindings;
    }

    public function transform($data)
    {
        if ($data === null) {
            return array();
        }

        $transformed = array();
        $bindings    = array_flip($this->bindings);

        foreach ($data as $source => $entries) {
            foreach ($entries as $key => $value) {
                $path = sprintf('%s.%s', $source, $key);

                if (array_key_exists($path, $bindings)) {
                    $this->arraySet($transformed, $bindings[$path], $value);
                }
            }
        }

        $xml = $this->encoder->encode($transformed, 'xml');
        $this->dom->loadXML($xml);

        return $transformed;
    }

    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $transformed = array();
        $bindings    = $this->bindings;

        foreach ($data as $section => $entries) {
            foreach ($entries as $key => $value) {
                $path = sprintf('%s.%s', $section, $key);

                if (array_key_exists($path, $bindings)) {
                    $this->arraySet($transformed, $bindings[$path], $value);
                }
            }
        }

        return $transformed;
    }

    protected function arrayGet($arr, $path)
    {
        if (!$path) {
            return null;
        }

        $segments = is_array($path) ? $path : explode('.', $path);

        $cur = &$arr;

        foreach ($segments as $segment) {
            if (!isset($cur[$segment])) {
                return null;
            }

            $cur = $cur[$segment];
        }

        return $cur;
    }

    protected function arraySet(&$arr, $path, $value)
    {
        if (!$path) {
            return null;
        }

        $segments = is_array($path) ? $path : explode('.', $path);

        $cur = &$arr;

        foreach ($segments as $segment) {
            if (!isset($cur[$segment])) {
                $cur[$segment] = array();
            }

            $cur = &$cur[$segment];
        }

        $cur = $value;
    }
}
