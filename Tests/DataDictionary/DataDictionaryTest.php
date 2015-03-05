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

    public function testToXml()
    {
        $this->assertEquals($this->getToXml()->saveXML(), $this->getDictionary()->toXml()->saveXML());
    }

    public function testToXmlWithValues()
    {
        $dictionary = $this->getDictionary();
        $dictionary->merge($this->getData());

        $this->assertEquals($this->getData()->saveXML(), $dictionary->toXml()->saveXML());
    }

    public function testToXsd()
    {
        $this->assertEquals($this->getToXsd()->saveXML(), $this->getDictionary()->toXsd()->saveXML());
    }

    public function testToXmlWithValuesValidatesToXsd()
    {
        $dictionary = $this->getDictionary();
        $dictionary->merge($this->getData());

        $xsd = sprintf('%s/%s.xsd', sys_get_temp_dir(), uniqid());
        $dictionary->toXsd()->save($xsd);

        $xml = $dictionary->toXml();

        $this->assertTrue($xml->schemaValidate($xsd));
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
  <Ondertekenaar><![CDATA[TestOndertekenaar]]></Ondertekenaar>
  <Tekstblokken>
    <Tekstblok>
      <Inhoud><![CDATA[TestInhoud]]></Inhoud>
    </Tekstblok>
  </Tekstblokken>
</Correspondentie>
EOF;

        $dom->loadXML($xml);

        return $dom;
    }

    protected function getDictionary()
    {
        $dictionary = new DataDictionary();
        $dictionary->setName('Correspondentie');

        $control = new DataControl($dictionary);
        $control->setType(DataControlInterface::TYPE_STRING);
        $control->setName('Ondertekenaar');
        $dictionary->addControl($control);

        $collection = new DataControl($dictionary);
        $collection->setType(DataControlInterface::TYPE_COLLECTION);
        $collection->setName('Tekstblokken');
        $dictionary->addControl($collection);

        $composite = new DataControl($dictionary);
        $composite->setType(DataControlInterface::TYPE_COMPOSITE);
        $composite->setName('Tekstblok');
        $collection->addChild($composite);

        $control = new DataControl($dictionary);
        $control->setType(DataControlInterface::TYPE_STRING);
        $control->setName('Inhoud');
        $composite->addChild($control);

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

    protected function getToXsd()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:element name="Correspondentie">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="Ondertekenaar" type="xs:string"/>
        <xs:element name="Tekstblokken">
          <xs:complexType>
            <xs:sequence>
              <xs:element name="Tekstblok">
                <xs:complexType>
                  <xs:sequence>
                    <xs:element name="Inhoud" type="xs:string"/>
                  </xs:sequence>
                </xs:complexType>
              </xs:element>
            </xs:sequence>
          </xs:complexType>
        </xs:element>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
</xs:schema>
EOF;

        $dom->loadXML($xml);

        return $dom;
    }
}
