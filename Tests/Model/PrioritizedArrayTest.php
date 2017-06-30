<?php

namespace Rednose\FrameworkBundle\Tests\Model;

use Rednose\FrameworkBundle\Model\PrioritizedArray;

class PrioritizedArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrioritizedArray
     */
    protected $array;

    protected $expected =
        'a:2:{i:0;a:2:{i:0;s:4:"Rank";i:1;a:3:{i:0;s:7:"PRIVATE";i:1;s:8:"SERGEANT";i:2;s:8:"CORPORAL";}}' .
        'i:1;a:2:{i:0;s:8:"priority";i:1;a:3:{i:0;i:0;i:1;i:2;i:2;i:1;}}}';

    protected $expectedReverse =
        'a:2:{i:0;a:2:{i:0;s:8:"priority";i:1;a:3:{i:0;i:0;i:1;i:2;i:2;i:1;}}' .
        'i:1;a:2:{i:0;s:4:"Rank";i:1;a:3:{i:0;s:7:"PRIVATE";i:1;s:8:"SERGEANT";i:2;s:8:"CORPORAL";}}}';

    public function setUp()
    {
        $array = new PrioritizedArray('Rank');

        $array->addElement('PRIVATE', 0);
        $array->addElement('SERGEANT', 2);
        $array->addElement('CORPORAL', 1);

        $this->array = $array;
    }

    public function testLoad()
    {
        $this->array->load($this->expectedReverse);
        $serialized = ((string)($this->array));
        $this->assertSame($this->expected, $serialized);

        $this->array->load($this->expected);
        $serialized = ((string)($this->array));
        $this->assertSame($this->expected, $serialized);
    }

    public function testRemove()
    {
        $this->assertSame($this->array->current(), ['Rank' => 'PRIVATE', 'priority' => 0]);

        $this->array->removeElement('PRIVATE');

        $this->assertSame($this->array->current(), ['Rank' => 'SERGEANT', 'priority' => 2]);
    }

    public function testSerialize()
    {
        $serialized = ((string)($this->array));

        $this->assertSame($this->expected, $serialized);
    }

    public function testCurrentAndNext()
    {
        $array = $this->array;

        $this->assertSame($array->current(), ['Rank' => 'PRIVATE', 'priority' => 0]);
        $array->next();

        $this->assertSame($array->current(), ['Rank' => 'SERGEANT', 'priority' => 2]);
        $array->next();

        $this->assertSame($array->current(), ['Rank' => 'CORPORAL', 'priority' => 1]);
    }

    public function testRewind()
    {
        $this->assertSame(0, $this->array->key());

        $this->array->next();
        $this->array->next();
        $this->array->next();
        $this->array->next();
        $this->array->next();

        $this->assertSame(5, $this->array->key());
        $this->assertFalse($this->array->valid());

        $this->array->rewind();

        $this->assertSame(0, $this->array->key());
        $this->assertTrue($this->array->valid());
    }

    public function testValid()
    {
        $this->assertTrue($this->array->valid());

        $this->array->next();
        $this->array->next();

        $this->assertTrue($this->array->valid());

        $this->array->next();
        $this->array->next();

        $this->assertFalse($this->array->valid());
    }

    public function testArrayAccess()
    {
        $this->assertSame('PRIVATE', $this->array['Rank_0']);
        $this->assertSame('SERGEANT', $this->array['Rank_1']);
        $this->assertSame('CORPORAL', $this->array['Rank_2']);
        $this->assertSame(0, $this->array['priority_0']);
        $this->assertSame(2, $this->array['priority_1']);
        $this->assertSame(1, $this->array['priority_2']);
    }

    public function testArrayAccessSet()
    {
        $this->array['Rank_0'] = 'The dude';
        $this->array['priority_0'] = 999;

        $this->assertSame('The dude', $this->array['Rank_0']);
        $this->assertSame(999, $this->array['priority_0']);
    }

    public function testArrayAccessSetInvalid()
    {
        $this->setExpectedException('Exception', 'Invalid key name (expected: Rank or priority)');

        $this->array['NotRank_0'] = 'Cookie';
    }
}