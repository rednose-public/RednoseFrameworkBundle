<?php

namespace Rednose\FrameworkBundle\Tests\DataDictionary;

use Rednose\FrameworkBundle\Entity\DataControl;
use Rednose\FrameworkBundle\Entity\DataDictionary;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryUtil;

class DataDictionaryUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataDictionary
     */
    protected $dictionary;

    public function setUp()
    {
        $this->dictionary = new DataDictionary();
        $this->dictionary->setName('Correspondentie');

        $control = new DataControl($this->dictionary);
        $control->setName('Ondertekenaar');
        $this->dictionary->addControl($control);
    }

    public function testNoMerge()
    {
        $child = $this->dictionary->getChild('Ondertekenaar');
        $this->assertNull($child->getValue(), 'An unmerged control should have value `null` when it has no default value.');
    }

    public function testMerge()
    {
        $child = $this->dictionary->getChild('Ondertekenaar');

        DataDictionaryUtil::merge($this->dictionary, $this->getData());

        $this->assertEquals('TestOndertekenaar', $child->getValue(), 'A merged control should have the same value as the XML element value.');
    }

    /**
     * @return \DOMDocument
     */
    protected function getData()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Correspondentie>
  <Ondertekenaar>TestOndertekenaar</Ondertekenaar>
</Correspondentie>
EOF;

        $dom->loadXML($xml);

        return $dom;
    }
}