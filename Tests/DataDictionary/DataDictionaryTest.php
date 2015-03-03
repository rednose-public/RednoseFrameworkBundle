<?php

namespace Rednose\FrameworkBundle\Tests\DataDictionary;

use Rednose\FrameworkBundle\DataDictionary\DataControl\DataControlInterface;
use Rednose\FrameworkBundle\Entity\DataDictionary;
use Rednose\FrameworkBundle\Entity\DataControl;

class DataDictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataDictionary
     */
    protected $dictionary;

    /**
     * @var DataControl
     */
    protected $composite;

    /**
     * @var DataControl
     */
    protected $absolute;

    /**
     * @var DataControl
     */
    protected $collection;

    /**
     * @var DataControl
     */
    protected $relative;

    public function setUp()
    {
        $this->dictionary = new DataDictionary();
        $this->dictionary->setName('Root');

        $this->composite = new DataControl($this->dictionary);
        $this->composite->setName('Composite');
        $this->composite->setType('composite');
        $this->dictionary->addControl($this->composite);

        $this->absolute = new DataControl($this->dictionary);
        $this->absolute->setName('Absolute');
        $this->absolute->setType('text');
        $this->composite->addChild($this->absolute);

        $this->collection = new DataControl($this->dictionary);
        $this->collection->setName('Collection');
        $this->collection->setType('collection');
        $this->dictionary->addControl($this->collection);

        $this->relative = new DataControl($this->dictionary);
        $this->relative->setName('Relative');
        $this->relative->setType('text');
        $this->collection->addChild($this->relative);
    }

    public function testToArray()
    {
        $array = $this->dictionary->toArray();

        $this->assertEquals('Composite', $array[0]['label'], 'Label of the first entry should be `Composite`');
        $this->assertEquals('Absolute', $array[0]['children'][0]['label'], 'Label of the first child should be `Absolute`');
    }

    public function testGetAbsolutePath()
    {
        $this->assertEquals('/Root/Composite', $this->composite->getPath());
        $this->assertEquals('/Root/Composite/Absolute', $this->absolute->getPath());
    }

    public function testRelativePath()
    {
        $this->assertEquals('/Root/Collection', $this->collection->getPath());
        $this->assertEquals('Relative', $this->relative->getPath());
    }

    public function testIsRelative()
    {
        $this->assertFalse($this->collection->isRelative());
        $this->assertTrue($this->relative->isRelative());
    }

    public function testHasControlAtAbsolutePathRetrunsTrue()
    {
        $this->assertTrue($this->dictionary->hasControl('/Root/Composite'));
        $this->assertTrue($this->dictionary->hasControl('/Root/Composite/Absolute'));
        $this->assertTrue($this->dictionary->hasControl('/Root/Collection'));
    }

    public function testDictionaryHasChild()
    {
        $this->assertTrue($this->dictionary->hasChild('Composite'));
        $this->assertFalse($this->dictionary->hasChild('Invalid'));
        $this->assertFalse($this->dictionary->hasChild('Absolute'), 'Node absolute is a grand-child instead of a child');
    }

    public function testDataControlHasChild()
    {
        $this->assertTrue($this->composite->hasChild('Absolute'));
        $this->assertFalse($this->composite->hasChild('Invalid'));
    }

    public function testGetControl()
    {
        $this->assertSame($this->dictionary->getControl('/Root/Composite'), $this->composite);
        $this->assertSame($this->dictionary->getControl('/Root/Composite/Absolute'), $this->absolute);
        $this->assertSame($this->dictionary->getControl('/Root/Collection'), $this->collection);

        $this->assertNull($this->dictionary->getControl('/Invalid/Composite/Absolute'));
        $this->assertNull($this->dictionary->getControl('/Root/Invalid/Absolute'));
        $this->assertNull($this->dictionary->getControl('/Root/Composite/Invalid'));
    }

    public function testGetControlOnRoot()
    {
        $this->assertSame($this->dictionary->getControl('/Root'), $this->dictionary);
    }

    public function testHasControlAtAbsolutePathReturnsFalse()
    {
        $this->assertFalse($this->dictionary->hasControl('/Invalid/Composite/Absolute'));
        $this->assertFalse($this->dictionary->hasControl('/Root/Invalid/Absolute'));
        $this->assertFalse($this->dictionary->hasControl('/Root/Composite/Invalid'));
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

    public function getMergeDictionary()
    {
        $dictionary = new DataDictionary();
        $dictionary->setName('Correspondentie');

        $control = new DataControl($dictionary);
        $control->setType(DataControlInterface::TYPE_TEXT);
        $control->setName('Ondertekenaar');
        $dictionary->addControl($control);

        $collection = new DataControl($dictionary);
        $collection->setType(DataControlInterface::TYPE_COLLECTION);
        $collection->setName('Tekstblokken');
        $dictionary->addControl($collection);

        $control = new DataControl($dictionary);
        $control->setType(DataControlInterface::TYPE_COMPOSITE);
        $control->setName('Tekstblok1');
        $collection->addChild($control);

        $control = new DataControl($dictionary);
        $control->setType(DataControlInterface::TYPE_COMPOSITE);
        $control->setName('Tekstblok2');
        $collection->addChild($control);

        return $dictionary;
    }

    protected function getToXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Correspondentie>
  <Ondertekenaar/>
  <Tekstblokken/>
</Correspondentie>
EOF;

        $dom->loadXML($xml);

        return $dom;
    }
}
