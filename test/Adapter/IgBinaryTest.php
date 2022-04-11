<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Exception\ExtensionNotLoadedException;
use Laminas\Serializer\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use stdClass;

use function extension_loaded;
use function igbinary_serialize;

/**
 * @group      Laminas_Serializer
 * @covers \Laminas\Serializer\Adapter\IgBinary
 */
class IgBinaryTest extends TestCase
{
    /** @var Serializer\Adapter\IgBinary */
    private $adapter;

    protected function setUp(): void
    {
        if (! extension_loaded('igbinary')) {
            try {
                new Serializer\Adapter\IgBinary();
                $this->fail(
                    "Laminas\\Serializer\\Adapter\\IgBinary needs missing ext/igbinary but did't throw exception"
                );
            } catch (ExtensionNotLoadedException $e) {
            }
            $this->markTestSkipped('Laminas\\Serializer\\Adapter\\IgBinary needs ext/igbinary');
        }
        $this->adapter = new Serializer\Adapter\IgBinary();
    }

    protected function tearDown(): void
    {
        $this->adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = igbinary_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = igbinary_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = igbinary_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = igbinary_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value    = new stdClass();
        $expected = igbinary_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $expected = 'test';
        $value    = igbinary_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $expected = false;
        $value    = igbinary_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $expected = null;
        $value    = igbinary_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $expected = 100;
        $value    = igbinary_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $expected = new stdClass();
        $value    = igbinary_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid()
    {
        $value = "\0\1\r\n";
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unserialization failed');
        $this->adapter->unserialize($value);
    }
}
