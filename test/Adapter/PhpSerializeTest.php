<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @group  Laminas_Serializer
 * @covers \Laminas\Serializer\Adapter\PhpSerialize
 */
class PhpSerializeTest extends TestCase
{
    /**
     * @var Serializer\Adapter\PhpSerialize
     */
    private $adapter;

    public function setUp()
    {
        $this->adapter = new Serializer\Adapter\PhpSerialize();
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = 's:4:"test";';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = 'b:0;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = 'N;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = 'i:100;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value    = new \stdClass();
        $expected = 'O:8:"stdClass":0:{}';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $value    = 's:4:"test";';
        $expected = 'test';

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $value    = 'b:0;';
        $expected = false;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $value    = 'N;';
        $expected = null;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $value    = 'i:100;';
        $expected = 100;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $value    = 'O:8:"stdClass":0:{}';
        $expected = new \stdClass();

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function invalidSerializationTypes()
    {
        return [
            'null'       => [null, 'NULL'],
            'true'       => [true, 'boolean'],
            'false'      => [false, 'boolean'],
            'zero'       => [0, 'int'],
            'int'        => [1, 'int'],
            'zero-float' => [0.0, 'double'],
            'float'      => [1.1, 'double'],
            'array'      => [['foo'], 'array'],
            'object'     => [(object) ['foo' => 'bar'], 'stdClass'],
        ];
    }

    /**
     * @dataProvider invalidSerializationTypes
     */
    public function testUnserializingNoStringRaisesException($value, $expected)
    {
        $this->expectException('Laminas\Serializer\Exception\RuntimeException');
        $this->expectExceptionMessage($expected);
        $this->adapter->unserialize($value);
    }

    public function invalidStrings()
    {
        return [
            'not-serialized'        => ['foobar', 'foobar'],
            'invalid-serialization' => ['a:foobar', 'Unserialization failed'],
        ];
    }

    /**
     * @dataProvider invalidStrings
     */
    public function testUnserializingInvalidStringRaisesException($string, $expected)
    {
        $this->expectException(Serializer\Exception\RuntimeException::class);
        $this->expectExceptionMessage($expected);
        $this->adapter->unserialize($string);
    }

    /**
     * @requires PHP 7.0
     */
    public function testPhp7WillNotUnserializeObjectsWhenUnserializeWhitelistedClassesIsFalse()
    {
        $value = 'O:8:"stdClass":0:{}';
        $this->adapter->getOptions()->setUnserializeClassWhitelist(false);

        $data = $this->adapter->unserialize($value);

        $this->assertNotInstanceOf(stdClass::class, $data);
        $this->assertInstanceOf('__PHP_Incomplete_Class', $data);
    }

    public function testWhenUnserializeClassWhiteListIsFalseButPHPIsPriorTo7AnExceptionIsRaised()
    {
        $value = 'O:8:"stdClass":0:{}';

        if (PHP_MAJOR_VERSION >= 7) {
            $this->markTestSkipped(sprintf('Test %s is only needed in PHP versions prior to 7.0', __FUNCTION__));
        }

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Class whitelist for unserialize() is only available on PHP 7.0 or higher.');
        $this->adapter->getOptions()->setUnserializeClassWhitelist(false);
    }

    /**
     * @requires PHP 7.0
     */
    public function testUnserializeWillNotUnserializeClassesThatAreNotInTheWhitelist()
    {
        $value = 'O:8:"stdClass":0:{}';

        $this->adapter->getOptions()->setUnserializeClassWhitelist([\My\Dummy::class]);

        $data = $this->adapter->unserialize($value);

        $this->assertNotInstanceOf(stdClass::class, $data);
        $this->assertInstanceOf('__PHP_Incomplete_Class', $data);
    }

    /**
     * @requires PHP 7.0
     */
    public function testUnserializeWillUnserializeAnyClassWhenUnserializeWhitelistedClassesIsTrue()
    {
        $value = 'O:8:"stdClass":0:{}';

        $this->adapter->getOptions()->setUnserializeClassWhitelist([stdClass::class]);

        $data = $this->adapter->unserialize($value);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertNotInstanceOf('__PHP_Incomplete_Class', $data);
    }
}
