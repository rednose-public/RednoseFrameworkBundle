<?php

namespace Rednose\FrameworkBundle\Tests\Util;

use Rednose\FrameworkBundle\Util\ArrayUtil;

class StyleTransformerTest extends \PHPUnit_Framework_TestCase
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

    public function test_Has_Should_Return_True_For_Root_Key_With_Scalar()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1a'));
    }

    public function test_Has_Should_Return_True_For_Root_Key_With_Array()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1b'));
    }

    public function test_Has_Should_Return_True_For_Nested_Key_With_Value()
    {
        $this->assertTrue(ArrayUtil::has($this->array, 'level1c.level2b.level3a'));
    }

    public function test_Has_Should_Return_False_For_Root_Key_Without_Value()
    {
        $this->assertFalse(ArrayUtil::has($this->array, 'level1x'));
    }

    public function test_Has_Should_Return_False_For_Nested_Key_Without_Value()
    {
        $this->assertFalse(ArrayUtil::has($this->array, 'level1c.level2b.level3x'));
    }

    public function test_Get_Should_Return_Value_For_Root_Key_With_Scalar()
    {
        $this->assertEquals('test1', ArrayUtil::get($this->array, 'level1a'));
    }

    public function test_Get_Should_Return_Value_For_Root_Key_With_Array()
    {
        $this->assertTrue(is_array(ArrayUtil::get($this->array, 'level1b')));
    }

    public function test_Get_Should_Return_Value_For_Nested_Key_With_Value()
    {
        $this->assertEquals('test3', ArrayUtil::get($this->array, 'level1c.level2b.level3a'));
    }

    public function test_Get_Should_Return_Null_For_Root_Key_Without_Value()
    {
        $this->assertNull(ArrayUtil::get($this->array, 'level1x'));
    }

    public function test_Get_Should_Return_Null_For_Nested_Key_Without_Value()
    {
        $this->assertNull(ArrayUtil::get($this->array, 'level1x.level2x.level3x'));
    }

    public function test_Set_Should_Override_Scalar_With_Value()
    {
        ArrayUtil::set($this->array, 'level1a', 'test');

        $this->assertEquals('test', $this->array['level1a']);
    }

    public function test_Set_Should_Override_Array_With_Value()
    {
        ArrayUtil::set($this->array, 'level1b', 'test');

        $this->assertEquals('test', $this->array['level1b']);
    }

    public function test_Set_Should_Create_Nested_Keys_When_Non_Existent()
    {
        ArrayUtil::set($this->array, 'level1x.level2x.level3x', 'test');

        $this->assertEquals('test', $this->array['level1x']['level2x']['level3x']);
    }
}
