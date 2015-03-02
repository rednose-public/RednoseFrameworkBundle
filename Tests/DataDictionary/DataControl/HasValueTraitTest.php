<?php

namespace Rednose\FrameworkBundle\Tests\DataDictionary\DataControl;

use Rednose\FrameworkBundle\DataDictionary\DataControl\DataControlInterface;
use Rednose\FrameworkBundle\Entity\DataControl;
use Rednose\FrameworkBundle\Entity\DataDictionary;

class HasValueTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $mock;

    public function setUp()
    {
        $this->mock = new DataControl(new DataDictionary());
    }

    public function testDateShouldAcceptDateTimeAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_DATE);
        $value = new \DateTime();

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testCollectionShouldAcceptArrayAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_COLLECTION);
        $value = array();

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testNumberShouldAcceptIntegerAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_NUMBER);
        $value = 42;

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testNumberShouldAcceptFloatAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_NUMBER);
        $value = 42.5;

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testStringShouldAcceptStringAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_STRING);
        $value = 'Test';

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testTextShouldAcceptStringAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_TEXT);
        $value = 'Test1\nTest2';

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }

    public function testHtmlShouldAcceptStringAndNull()
    {
        $this->mock->setType(DataControlInterface::TYPE_HTML);
        $value = '<p>Test</p>';

        $this->mock->setValue($value);
        $this->assertEquals($value, $this->mock->getValue());

        $this->mock->setValue(null);
        $this->assertNull($this->mock->getValue());
    }
}