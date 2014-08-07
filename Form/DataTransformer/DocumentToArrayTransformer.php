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

    public function __construct(array $bindings)
    {
        $this->encoder  = new XmlEncoder('Rijkshuistijl');
        $this->bindings = $bindings;
    }

    public function transform($data)
    {
        if ($data === null) {
            return array();
        }

//        var_dump($data);
//        exit;

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

//        var_dump($transformed);
//        exit;

        return $transformed;

//        return $this->encoder->decode($data->saveXML(), 'xml');
    }

    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->loadXML($this->encoder->encode($data, 'xml'));

        return $dom;
    }

    protected function arrayGet($arr, $path)
    {
        if (!$path)
            return null;

        $segments = is_array($path) ? $path : explode('.', $path);
        $cur =& $arr;
        foreach ($segments as $segment) {
            if (!isset($cur[$segment]))
                return null;

            $cur = $cur[$segment];
        }

        return $cur;
    }

    protected function arraySet(&$arr, $path, $value)
    {
        if (!$path)
            return null;

        $segments = is_array($path) ? $path : explode('.', $path);
        $cur =& $arr;
        foreach ($segments as $segment) {
            if (!isset($cur[$segment]))
                $cur[$segment] = array();
            $cur =& $cur[$segment];
        }
        $cur = $value;
    }
}
