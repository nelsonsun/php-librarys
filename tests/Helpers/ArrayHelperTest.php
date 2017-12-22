<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 0:05
 */
namespace Inhere\Library\Tests\Helpers;

use Inhere\Library\Helpers\ArrayHelper as Arr;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayHelperTest
 *
 *
 * @group testing
 * @package Inhere\Library\Tests\Helpers
 * @covers Arr
 */
class ArrayHelperTest extends TestCase
{
    /**
     * @covers Arr::accessible()
     */
    public function testAccessible()
    {
        $this->assertTrue(Arr::accessible([]));

        $this->assertTrue(Arr::accessible(array_pad([], 5, 0)));

        $this->assertTrue(Arr::accessible(array_fill(99, 1, 11)));

        $this->assertTrue(Arr::accessible([
            "foo" => "bar",
            "bar" => "foo",
        ]));

        $this->assertFalse(Arr::accessible(null));

        $this->assertFalse(Arr::accessible(false));

        $this->assertFalse(Arr::accessible(3.141592));

        $this->assertFalse(Arr::accessible('string'));
    }

    /**
     * @covers Arr::isAssoc() 检测是否是关联数组
     */
    public function testIsAssoc()
    {
        $this->assertFalse(Arr::isAssoc([2, 3, 4]));

        $this->assertTrue(Arr::isAssoc(['2' => 2, 'key' => 3, 4]));

        $arr = array_fill(3, 3, 3);


        $this->assertTrue(Arr::isAssoc($arr));

        $arr2 = [3, 3, 3];

        $this->assertFalse(Arr::isAssoc($arr2));
    }

    /**
     * @covers       Arr::toIterator()
     * @dataProvider paramsProvider
     */
    public function testToIterator()
    {
        $arguments = func_get_args();

        foreach ($arguments as $row) {
            $this->assertTrue(Arr::toIterator($row) instanceof \Traversable);
        }
    }

    /**
     *提供各种类型的参数
     */
    public function paramsProvider()
    {
        return [
            'boolean' => [true, false],
            'integer' => [1, 2, 3],
            'float'   => [1.2, 2.3, 3.4],
            'string'  => ['first', 'second', 'third'],
            'array'   => [
                'fruit'     => ['apple', 'pear', 'banana'],
                'vegetable' => ['tomato', 'carrot', 'pea',],
                'flower'    => ['rose', 'tulip', 'lily'],
                'number'    => [1, 2, 3],
                'associate' => ['key1' => 'value1', 'key2' => 2, 3],
            ],
            'object'  => [new \stdClass()],
            'null'    => [null],
        ];
    }


    /**
     * @expectedException \TypeError
     * @covers Arr::gets()
     */
    public function testGets()
    {
        $data = ['name' => 'zhangSan', 'password' => 'ertefgdf ewrgy', 'status' => 89];

        $this->assertTrue(is_array(Arr::gets($data)));

        $this->assertTrue(is_array(Arr::gets($data, 'name')));


        $this->assertTrue(is_array(Arr::gets($data, ['name', 'status'])));
        $this->assertTrue(is_array(Arr::gets($data, ['name', 'status'], true)));
    }

    /**
     * @covers Arr::merge()
     */
    public function testMerge()
    {
        $arr1 = ['1', '2'];

        $arr2 = ['a', 'b'];

        $this->assertSame($arr1, Arr::merge(null, $arr1));

        $this->assertSame(array_merge($arr2, $arr1), Arr::merge($arr2, $arr1));

        $this->assertNotEquals(array_merge($arr1, $arr2), Arr::merge($arr2, $arr1));

        $this->assertSame(array_merge_recursive($arr2, $arr1), Arr::merge($arr2, $arr1));

        $this->assertSame([], Arr::merge(null, []));
        $this->assertNotSame([], Arr::merge(null, $arr1));
    }

    /**
     * @covers Arr::merge2()
     */
    public function testMerge2()
    {

    }

    /**
     * @covers Arr::wrap()
     */
    public function testWrap()
    {
        $this->assertSame(['string'], Arr::wrap('string'));
    }

}
