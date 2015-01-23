<?php

namespace Rednose\FrameworkBundle\Tests\Util;

use Rednose\FrameworkBundle\Util\ArrayUtil;

class ArrayUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $array;

    public function setUp()
    {
        $this->array = array(
            'level1a' => 'test1',
            'level1b' => array(
                'level2a' => 'test2',
            ),
            'level1c' => array(
                'level2b' => array(
                    'level3a' => 'test3',
                )
            )
        );
    }

    public function testHasShouldReturnTrueForRootKeyWithScalar()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1a'));
    }

    public function testHasShouldReturnTrueForRootKeyWithArray()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1b'));
    }

    public function testHasShouldReturnTrueForNestedKeyWithValue()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1c.level2b.level3a'));
    }

    public function testHasShouldReturnFalseForRootKeyWithoutValue()
    {
        $this->assertFalse(ArrayUtil::has($this->array, 'level1x'));
    }

    public function testHasShouldReturnFalseForNestedKeyWithoutValue()
    {
        $this->assertFalse(ArrayUtil::has($this->array, 'level1c.level2b.level3x'));
    }

    public function testGetShouldReturnValueForRootKeyWithScalar()
    {
        $this->assertEquals('test1', ArrayUtil::get($this->array, 'level1a'));
    }

    public function testGetShouldReturnValueForRootKeyWithArray()
    {
        $this->assertTrue(is_array(ArrayUtil::get($this->array, 'level1b')));
    }

    public function testGetShouldReturnValueForNestedKeyWithValue()
    {
        $this->assertEquals('test3', ArrayUtil::get($this->array, 'level1c.level2b.level3a'));
    }

    public function testGetShouldReturnNullForRootKeyWithoutValue()
    {
        $this->assertNull(ArrayUtil::get($this->array, 'level1x'));
    }

    public function testGetShouldReturnNullForNestedKeyWithoutValue()
    {
        $this->assertNull(ArrayUtil::get($this->array, 'level1x.level2x.level3x'));
    }

    public function testSetShouldOverrideScalarWithValue()
    {
        ArrayUtil::set($this->array, 'level1a', 'test');

        $this->assertEquals('test', $this->array['level1a']);
    }

    public function testSetShouldOverrideArrayWithValue()
    {
        ArrayUtil::set($this->array, 'level1b', 'test');

        $this->assertEquals('test', $this->array['level1b']);
    }

    public function testSetShouldCreateNestedKeysWhenNonExistent()
    {
        ArrayUtil::set($this->array, 'level1x.level2x.level3x', 'test');

        $this->assertEquals('test', $this->array['level1x']['level2x']['level3x']);
    }
}
