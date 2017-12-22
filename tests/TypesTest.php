<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/21
 * Time: 19:02
 */

namespace Inhere\Library\Tests;

use Inhere\Library\Types;

class TypesTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        $types = Types::all();

        $this->assertEquals(
            ['array', 'bool', 'boolean', 'double', 'float', 'int', 'integer', 'object', 'string', 'resource'],
            $types);
    }

    public function testScalars()
    {
        $this->assertEquals(
            ['bool', 'boolean', 'double', 'float', 'int', 'integer', 'string'],
            Types::scalars());
    }
}