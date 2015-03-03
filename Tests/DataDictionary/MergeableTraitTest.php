<?php

namespace Rednose\FrameworkBundle\Tests\DataDictionary;

use Rednose\FrameworkBundle\DataDictionary\DataControl\DataControlInterface;
use Rednose\FrameworkBundle\Entity\DataDictionary;
use Rednose\FrameworkBundle\Entity\DataControl;

class MergeableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param DataDictionary
     */
    protected $dictionary;

    public function testNoMerge()
    {
        $dictionary = $this->getMergeDictionary();

        $child = $dictionary->getChild('Ondertekenaar');
        $this->assertNull($child->getValue(), 'An unmerged control should have value `null` when it has no default value.');
    }

    public function testMergePrimitive()
    {
        $dictionary = $this->getMergeDictionary();
        $dictionary->merge($this->getData());

        $child = $dictionary->getChild('Ondertekenaar');

        $this->assertEquals('TestOndertekenaar', $child->getValue(), 'A merged control should have the same value as the XML element value.');
    }

    public function testMergeComplex()
    {
        $dictionary = $this->getMergeDictionary();
        $dictionary->merge($this->getData());

        $composite = $dictionary->getChild('Complex');
        $primitive1 = $composite->getChild('Primitive1');
        $primitive2 = $composite->getChild('Primitive2');

        $this->assertNull($composite->getValue());
        $this->assertEquals('Complex1', $primitive1->getValue());
        $this->assertEquals('Complex2', $primitive2->getValue());
    }

    public function testMergeCollection()
    {
        $dictionary = $this->getMergeDictionary();
        $dictionary->merge($this->getData());

        $collection = $dictionary->getChild('Tekstblokken');
        $value = $collection->getValue();

        $this->assertEquals(4, count($value));

        $names = array();
        $fields = array();
        $values = array();

        foreach ($value as $textBlockNode) {
            $names[] = $textBlockNode->getName();

            foreach ($textBlockNode->getChildren() as $fieldNode) {
                $fields[] = $fieldNode->getName();
                $values[] = $fieldNode->getValue();
            }
        };

        $this->assertEquals(array('Tekstblok1', 'Tekstblok2', 'Tekstblok1', 'Tekstblok2'), $names);
        $this->assertEquals(array('Content1', 'Content2', 'Content1', 'Content2'), $fields);
        $this->assertEquals(array('Value1', 'Value2', 'Value3', 'Value4'), $values);
    }

    public function getMergeDictionary()
    {
        $dictionary = new DataDictionary();
        $dictionary->setName('Correspondentie');

        $control = new DataControl($dictionary);
        $control->setName('Ondertekenaar');
        $control->setType(DataControlInterface::TYPE_TEXT);
        $dictionary->addControl($control);

        $composite = new DataControl($dictionary);
        $composite->setName('Complex');
        $composite->setType(DataControlInterface::TYPE_COMPOSITE);
        $dictionary->addControl($composite);

        $control = new DataControl($dictionary);
        $control->setName('Primitive1');
        $control->setType(DataControlInterface::TYPE_TEXT);
        $composite->addChild($control);

        $control = new DataControl($dictionary);
        $control->setName('Primitive2');
        $control->setType(DataControlInterface::TYPE_TEXT);
        $composite->addChild($control);

        $collection = new DataControl($dictionary);
        $collection->setName('Tekstblokken');
        $collection->setType(DataControlInterface::TYPE_COLLECTION);
        $dictionary->addControl($collection);

        $composite = new DataControl($dictionary);
        $composite->setName('Tekstblok1');
        $composite->setType(DataControlInterface::TYPE_COMPOSITE);
        $collection->addChild($composite);

        $control = new DataControl($dictionary);
        $control->setName('Content1');
        $control->setType(DataControlInterface::TYPE_TEXT);
        $composite->addChild($control);

        $composite = new DataControl($dictionary);
        $composite->setName('Tekstblok2');
        $composite->setType(DataControlInterface::TYPE_COMPOSITE);
        $collection->addChild($composite);

        $control = new DataControl($dictionary);
        $control->setName('Content2');
        $control->setType(DataControlInterface::TYPE_TEXT);
        $composite->addChild($control);

        return $dictionary;
    }

    /**
     * @return \DOMDocument
     */
    protected function getData()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;

        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Correspondentie>
  <Ondertekenaar>TestOndertekenaar</Ondertekenaar>
  <Complex>
    <Primitive1>Complex1</Primitive1>
    <Primitive2>Complex2</Primitive2>
  </Complex>
  <Tekstblokken>
    <Tekstblok1>
      <Content1>Value1</Content1>
    </Tekstblok1>
    <Tekstblok2>
      <Content2>Value2</Content2>
    </Tekstblok2>
    <Tekstblok1>
      <Content1>Value3</Content1>
    </Tekstblok1>
    <Tekstblok2>
      <Content2>Value4</Content2>
    </Tekstblok2>
  </Tekstblokken>
</Correspondentie>
EOF;

        $dom->loadXML($xml);

        return $dom;
    }
}