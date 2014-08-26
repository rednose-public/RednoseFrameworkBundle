<?php

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class DocumentToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var \DOMDocument
     */
    public $dom;

    /**
     * @var \DOMXPath
     */
    public $xpath;

    /**
     * @var XmlEncoder
     */
    protected $encoder;

    /**
     * Constructor.
     * 
     * @param array $bindings The configured form bindings.
     */
    public function __construct(array $bindings)
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->xpath = new \DOMXPath($this->dom);

        $this->encoder  = new XmlEncoder('form');
        $this->bindings = $bindings;
    }

    /**
     * Transforms an array of data based on the form's bindings.
     *
     * @param array $data
     *
     * @return array
     */
    public function transform($data)
    {
        if ($data === null) {
            return array();
        }

        $transformed = array();

        foreach ($this->bindings as $target => $source) {
            $value = $this->arrayGet($data, $source);

            // XXX
            if ($value === '0' || $value === '1') {
                $value = (bool) $value;
            }

            $this->arraySet($transformed, $target, $value);
        }

        // Store the data within a DOM document to evaluate server-side XPath expressions.
        $xml = $this->encoder->encode($transformed, 'xml');
        $this->dom->loadXML($xml);

        return $transformed;
    }

    /**
     * Transforms an array of data based on the form's bindings.
     *
     * @param array $data
     *
     * @return array
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $transformed = array();

        foreach ($this->bindings as $target => $source) {
            $value = $this->arrayGet($data, $target);

            $this->arraySet($transformed, $source, $value);
        }

        return $transformed;
    }

    /**
     * Gets a value from an array by specifying a path, ie. "Address.Street".
     *
     * @param array $arr
     * @param string $path
     *
     * @return mixed
     */
    protected function arrayGet(array $arr, $path)
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

    /**
     * Sets a value within an array by specifying a path, ie. "Address.Street".
     *
     * @param array  $arr
     * @param string $path
     * @param mixed  $value
     */
    protected function arraySet(array &$arr, $path, $value)
    {
        if (!$path) {
            return;
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
