<?php

namespace Rednose\FrameworkBundle\DataDictionary;

use Rednose\FrameworkBundle\DataDictionary\DataControl\DataControlInterface;
use Rednose\FrameworkBundle\Util\XpathUtil;
use Symfony\Component\Config\Util\XmlUtils;

trait MergeableTrait
{
    /**
     * Merges a data set into a data dictionary
     *
     * @param \DOMDocument $data
     * @param string       $locator
     */
    public function merge(\DOMDocument $data, $locator = null)
    {
        if ($locator) {
            $control = $this->getControl($locator);

            if (!$control) {
                return;
            }

            $dom = $control->getDictionary()->toXml();
            $collection = XpathUtil::getXpathNode($dom, $control->getParent()->getPath());

            if ($collection && $data->documentElement) {
                $collection->appendChild($dom->importNode($data->documentElement, true));
            }

            $this->merge($dom);
        }

        else {
            foreach ($this->getControls() as $control) {
                $this->traverse($control, $data);
            }
        }
    }

    /**
     * @param DataControlInterface $control
     * @param \DOMDocument         $data
     * @param \DOMNode             $context
     */
    protected function traverse(DataControlInterface $control, \DOMDocument $data, \DOMNode $context = null)
    {
        $node = XpathUtil::getXpathNode($data, $control->getPath(), $context);

        if ($control->getType() === DataControlInterface::TYPE_COMPOSITE) {
            foreach ($control->getChildren() as $child) {
                $this->traverse($child, $data, $context);
            }
        }

        elseif ($control->getType() === DataControlInterface::TYPE_COLLECTION) {
            $value = array();

            if ($node) {
                foreach ($node->childNodes as $childNode) {
                    if (!$childNode instanceof \DOMElement) {
                        continue;
                    }

                    if ($child = $control->getChild($childNode->tagName)) {
                        $value[] = $clone = $child->cloneNode();
                        $this->traverse($clone, $data, $childNode);
                    }
                }

                $control->setValue($value);
            }
        }

        else {
            if ($node !== null) {
                $value = $node->nodeValue;

                // Don't write empty strings when merging.
                if (!$value) {
                    return;
                }

                if ($control->getType() === DataControlInterface::TYPE_DATE) {
                    $value = new \DateTime($value);
                }

                elseif ($control->getType() === DataControlInterface::TYPE_BOOLEAN) {
                    $value = (boolean) XmlUtils::phpize($value);
                }

                $control->setValue($value);
            }
        }
    }

    /**
     * @return DataControlInterface[]
     */
    abstract public function getControls();

    /**
     * @param string $path
     * @param string $separator
     *
     * @return DataControlInterface
     */
    abstract public function getControl($path, $separator = '/');
}