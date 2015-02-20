<?php

namespace Rednose\FrameworkBundle\DataDictionary;

use Rednose\FrameworkBundle\Model\DataDictionaryInterface;
use Rednose\FrameworkBundle\Model\DataControlInterface;
use Rednose\FrameworkBundle\Util\XpathUtil;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * DataDictionaryUtil provides several utility methods for modifying data dictionaries.
 */
class DataDictionaryUtil
{
    /**
     * Merges a data set into a data dictionary
     *
     * @param DataDictionaryInterface $dictionary
     * @param \DOMDocument $data
     */
    public static function merge(DataDictionaryInterface $dictionary, \DOMDocument $data)
    {
        foreach ($dictionary->getControls() as $control) {
            self::traverse($control, $data);
        }

        return $dictionary;
    }

    /**
     * @param DataControlInterface $control
     * @param \DOMDocument         $data
     */
    protected static function traverse(DataControlInterface $control, \DOMDocument $data)
    {
        if (in_array($control->getType(), array(DataControlInterface::TYPE_COMPOSITE, DataControlInterface::TYPE_COLLECTION))) {
            foreach ($control->getChildren() as $child) {
                self::traverse($child, $data);
            }

            return;
        }

        $node = XpathUtil::getXpathNode($data, $control->getPath());

        if ($node !== null) {
            $value = $node->nodeValue;

            if ($control->getType() === DataControlInterface::TYPE_DATE) {
                $value = new \DateTime($value);
            } else  if ($control->getType() === DataControlInterface::TYPE_BOOLEAN) {
                $value = (boolean) XmlUtils::phpize($value);
            }

            $control->setValue($value);
        }
    }
}