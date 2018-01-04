<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 0:05
 */
namespace Inhere\Library\Tests\Helpers;

use Inhere\Exceptions\InvalidArgumentException;
use Inhere\Library\Helpers\ArrayHelper as Arr;
use Inhere\Library\Helpers\Obj;
use Inhere\Library\Web\Environment;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayHelperTest
 *
 *
 * @group arr
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
        $arr1 = ['1', '2'];
        $arr2 = ['a', 'b'];
        $arr3 = ['l', 'm'];
        $arr4 = ['o', 'p'];

        $this->assertSame($arr1, Arr::merge2(null, $arr1));

        $this->assertSame(array_merge($arr2, $arr1, $arr3), Arr::merge2($arr2, $arr1, $arr3));

        $this->assertNotEquals(array_merge($arr1, $arr2, $arr4), Arr::merge2($arr2, $arr1, $arr4));

        $this->assertSame(array_merge_recursive($arr2, $arr1), Arr::merge2($arr2, $arr1));

        $this->assertSame([], Arr::merge2([], []));
        $this->assertNotSame([], Arr::merge2([], $arr1));
    }


    /**
     * @covers Arr::valueTrim()
     */
    public function testValueTrim()
    {
        $this->assertEquals(['abc'], Arr::valueTrim(['abc ']));

        $this->assertNotEquals(['a b' => 'abc'], Arr::valueTrim([
            'a b ' => 'abc ',
        ]));

        $this->assertEquals(['a b ' => 'abc'], Arr::valueTrim([
            'a b ' => 'abc ',
        ]));

        $this->assertEquals(['abc'], Arr::valueTrim([' abc ']));

        $this->assertEquals(['ab c'], Arr::valueTrim([' ab c ']));
    }

    /**
     * @covers Arr::keyExists()
     */
    public function testKeyExists()
    {
        $this->assertTrue(Arr::keyExists('abc', ['abc' => 123]));
        $this->assertTrue(Arr::keyExists('abc', ['abc' => null]));

        $this->assertTrue(Arr::keyExists(null, [null => null]));

        $this->assertTrue(Arr::keyExists('abc', ['ABC' => 123]));
        $this->assertTrue(Arr::keyExists('ABC', ['abc' => 123]));
        $this->assertTrue(Arr::keyExists('ABC', ['ABC' => 123]));

        $this->assertFalse(Arr::keyExists('ABC', [
            'xyz' => ['ABC' => 123],
        ]));

        $this->assertFalse(Arr::keyExists('ABC', [
            'cba' => [
                'opm' => ['ABC' => 123],
            ],
        ]));

    }

    /**
     * @covers Arr::valueToLower()
     */
    public function testValueToLower()
    {
        $this->assertSame(['abc'], Arr::valueToLower(['ABC']));
        $this->assertNotSame(['ABC'], Arr::valueToLower(['abc']));

        $this->assertSame(['abc'], Arr::valueToLower(['abc']));

        $this->assertSame(['abc' => 'abc'], Arr::valueToLower(['abc' => 'abc']));

        $this->assertSame(['abc ' => 'abc '], Arr::valueToLower(['abc ' => 'abc ']));

        $this->assertNotSame(['abc' => 'abc'], Arr::valueToLower(['ABC' => 'abc']));

        $this->assertSame(['abc'], Arr::valueToLower(['abc']));
        $this->assertSame(['abc'], Arr::valueToLower(['abc']));
    }

    /**
     * @covers  Arr::valueExistsAll()
     */
    public function testValueExistsAll()
    {
        $this->assertEquals([], array_diff([], []));
        $this->assertEquals([], array_diff([], []));
        $this->assertEquals(true, ! []);
        $this->assertEquals(false, ! ['abc']);
        $this->assertEquals(['abc'], array_diff(['abc'], []));
        $this->assertEquals([], array_diff(['abc'], ['abc']));

        $this->assertEquals(true, Arr::valueExistsAll('a,b,c', ['a', 'b', 'c']));

        $this->assertTrue(Arr::valueExistsAll(['a', 'b', 'c'], ['a', 'b', 'c']));
        $this->assertFalse(Arr::valueExistsAll(['a,b,c'], ['a', 'b', 'c']));
    }

    /**
     * @covers Arr::valueExistsOne()
     */
    public function testValueExistsOne()
    {
        $this->assertTrue(Arr::valueExistsOne(['a'], ['a', 'b', 'c']));
        $this->assertTrue(Arr::valueExistsOne('a', ['a', 'b', 'c']));

        $this->assertTrue(Arr::valueExistsOne('b,a', ['a', 'b', 'c']));
    }

    /**
     *
     * @covers Arr::existsAll()
     */
    public function testExistsAll()
    {
        $this->assertTrue(Arr::existsAll('m', ['m', 'n']));
        $this->assertTrue(Arr::existsAll('m,n', ['m', 'n']));
        $this->assertTrue(Arr::existsAll('m , n', ['m', 'n']));

        $this->assertNotTrue(Arr::existsAll('m n, n', ['m', 'n']));

        $this->assertNotTrue(Arr::existsAll('m n', ['m', 'n']));
    }


    /**
     * @covers Arr::existsOne()
     */
    public function testExistsOne()
    {
        $this->assertTrue(Arr::existsOne('.', [',', '.']));

        $this->assertFalse(Arr::existsOne('m,n', ['o', 'p', 'q']));

        $this->assertTrue(Arr::existsOne('m,n,o', ['o', 'p', 'q']));

        $this->assertTrue(Arr::existsOne('o', ['o', 'p', 'q']));

        $this->assertTrue(Arr::existsOne('Q', ['o', 'p', 'q']));

        $this->assertTrue(Arr::existsOne('o', ['O', 'p', 'q']));
    }

    /**
     * @covers Arr::getKeyMaxWidth()
     */
    public function testGetKeyMaxWidth()
    {
        $this->assertEquals('5', Arr::getKeyMaxWidth(['abcde' => 1]));
        $this->assertEquals(6, Arr::getKeyMaxWidth(['ab cde' => 1]));

        $this->assertEquals(1, Arr::getKeyMaxWidth(['1' => 1], false));
        $this->assertEquals(1, Arr::getKeyMaxWidth(['a' => 1]));
    }

    /**
     * @covers Arr::getByPath()
     */
    public function testGetByPath()
    {
        $this->assertSame(2, Arr::getByPath(['a' => ['b' => ['c' => 2]]], 'a.b.c'));
        $this->assertEquals('2', Arr::getByPath(['a' => ['b' => ['c' => 2]]], 'a.b.c'));

        $this->assertEquals(['c' => 2], Arr::getByPath(['a' => ['b' => ['c' => 2]]], 'a.b'));

        $this->assertSame('b', Arr::getByPath(['a' => 'b'], 'a', null, '|'));
        $this->assertSame('b', Arr::getByPath(['a' => ['b']], 'a|0', null, '|'));
        $this->assertSame(null, Arr::getByPath(['a' => ['b']], 'a|1', null, '|'));


        $obj = new \stdClass();

        $obj->a = ['b'];

        $this->assertSame('b', Arr::getByPath((array)$obj, 'a.0'));
    }

    /**
     * @covers Arr::setByPath()
     */
    public function testSetByPath()
    {
        $data = [];

        $this->assertTrue(Arr::setByPath($data, 'a.b.c.d', '9'));

        $this->assertSame('9', $data['a']['b']['c']['d']);
    }

    /**
     * @covers Arr::collapse()
     */
    public function testCollapse()
    {
        $this->assertTrue(is_array(Arr::collapse([
            'a' => 'b',
            'c' => 'd'
        ])));
        $this->markTestIncomplete('没有完成');
    }

    /**
     * @covers Arr::crossJoin()
     */
    public function testCrossJoin()
    {
        $this->assertSame(4, count(Arr::crossJoin([1,2], [3,4])));
        $this->assertSame(2, count(Arr::crossJoin([2], [3,4])));
        $this->markTestIncomplete('没有完成');
    }

    /**
     * 把一个数组分成两个数组，一个是建，一个是值
     *
     * @covers Arr::divide()
     */
    public function testDivide()
    {
        $arr = ['a' => 'b'];
        $arr1 = ['b','a'];
        $arr2 = [1, 2, 3 => ['aa', 'bb']];

        $this->assertSame([['a'], ['b']], Arr::divide($arr));
        $this->assertSame([[0,1], ['b', 'a']], Arr::divide($arr1));
        $this->assertSame([[0,1, 3], [1, 2, ['aa', 'bb']]], Arr::divide($arr2));
        $this->assertSame($arr2, array_combine(Arr::divide($arr2)[0], Arr::divide($arr2)[1]));
    }

    /**
     * @covers Arr::dot()
     */
    public function testDot()
    {
        $arr = ['a', 'b', 'c'];
        $this->assertSame($arr, Arr::dot($arr));
        $arr = ['a' => ['b' => 'c']];
        $this->assertSame(['a.b' => 'c'], Arr::dot($arr));
        $arr = ['a' => ['b' => ['c' => ['d' => []]]]];
        $this->assertSame(['a.b.c.d' => []], Arr::dot($arr));
        $arr = ['a' => 'b', ['a' => 'b']];
        $this->assertSame(['a' => 'b', '0.a' => 'b'], Arr::dot($arr));

        $arr = [
            'a' => 'm',
            'b' => 'n',
            'c' => 'o',
            'd' => 'p',
            [
                'a' => 'm',
                'b' => 'n',
                'c' => 'o',
                'd' => 'p',
            ],
        ];

        $result = [
            'a' => 'm',
            'b' => 'n',
            'c' => 'o',
            'd' => 'p',
            '0.a' => 'm',
            '0.b' => 'n',
            '0.c' => 'o',
            '0.d' => 'p',
        ];
        $this->assertSame($result, Arr::dot($arr));

        $this->assertSame([], Arr::dot([]));
        $this->assertSame([[]], Arr::dot([[]]));
        $this->assertSame([null], Arr::dot([null]));
    }

    /**
     * @covers Arr::except()
     */
    public function testExcept()
    {
        $this->assertSame([], Arr::except(['abc' => 'abc'], 'abc'));
        $this->assertSame([], Arr::except(['abc' => 'abc'], ['abc']));
        $this->assertSame([], Arr::except(['abc'], 0));

        $this->assertSame([1 => 'mon'], Arr::except(['abc', 'mon'], [0]));

        $this->assertSame(['abc' => 'mon'], Arr::except(['abc' => 'mon'], [null]));
    }

    /**
     * @covers Arr::wrap()
     */
    public function testWrap()
    {
        $this->assertSame(['string'], Arr::wrap('string'));
    }

}
