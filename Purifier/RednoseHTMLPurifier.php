<?php

namespace Rednose\FrameworkBundle\Purifier;

use DOMXPath;
use DOMDocument;
use HTMLPurifier;

class RednoseHTMLPurifier extends HTMLPurifier
{
    public function purify($html, $config = null)
    {
        $html = $this->normalizeTables($html);

        $cleanHtml = parent::purify($html);

        return $cleanHtml;
    }

    protected function normalizeTables($html)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHtml($html);

        $xpath = new DOMXPath($dom);

        $tables = $xpath->query('//table');

        foreach ($tables as $table) {
            $tableElement = $xpath->query(
                $table->getNodePath() . '//tbody|' .
                $table->getNodePath() . '//th|' .
                $table->getNodePath() . '//td'
            );

            foreach ($tableElement as $element) {
                if ($style = $element->getAttribute('style')) {
                    $this->normalizeCss($element, $style);
                }
            }
        }

        return $dom->saveHtml();
    }

    protected function normalizeCss($node, $css)
    {
        $css = explode(';', $css);

        foreach ($css as $rule) {
            $rule = explode(':', $rule);

            switch (trim($rule[0])) {
                case "background-color":
                    $node->setAttribute('bgcolor', $this->normalizeColor($rule[1]));
                    break;
            }
        }
    }

    protected function normalizeColor($input)
    {
        if (strtolower($input, 'rgb') !== false) {
            $rgb = explode('(', $input);
            $rgb = explode(')', $rgb[1]);
            $rgb = explode(',', $rgb[0]);

            $hex  = "#";
            $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
            $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
            $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

            return $hex;
        }

        return $input;
    }
}
