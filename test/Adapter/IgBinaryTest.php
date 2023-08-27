<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer\Adapter\IgBinary;
use Laminas\Serializer\Exception\ExtensionNotLoadedException;
use Laminas\Serializer\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

use function extension_loaded;
use function igbinary_serialize;

#[CoversClass(IgBinary::class)]
class IgBinaryTest extends TestCase
{
    private IgBinary $adapter;

    protected function setUp(): void
    {
        if (! extension_loaded('igbinary')) {
            try {
                new IgBinary();
                $this->fail(
                    "Laminas\\Serializer\\Adapter\\IgBinary needs missing ext/igbinary but did't throw exception"
                );
            } catch (ExtensionNotLoadedException $e) {
            }
            $this->markTestSkipped('Laminas\\Serializer\\Adapter\\IgBinary needs ext/igbinary');
        }
        $this->adapter = new IgBinary();
    }

    public function testSerializeString(): void
    {
        $value    = 'test';
        $expected = igbinary_serialize($value);
        self::assertNotFalse($expected);
        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeFalse(): void
    {
        $value    = false;
        $expected = igbinary_serialize($value);
        self::assertNotFalse($expected);
        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNull(): void
    {
        $value    = null;
        $expected = igbinary_serialize($value);
        self::assertNotFalse($expected);
        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNumeric(): void
    {
        $value    = 100;
        $expected = igbinary_serialize($value);
        self::assertNotFalse($expected);
        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeObject(): void
    {
        $value    = new stdClass();
        $expected = igbinary_serialize($value);
        self::assertNotFalse($expected);
        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeString(): void
    {
        $expected = 'test';
        $value    = igbinary_serialize($expected);
        self::assertNotFalse($value);
        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeFalse(): void
    {
        $expected = false;
        $value    = igbinary_serialize($expected);
        self::assertNotFalse($value);
        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNull(): void
    {
        $expected = null;
        $value    = igbinary_serialize($expected);
        self::assertNotFalse($value);
        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNumeric(): void
    {
        $expected = 100;
        $value    = igbinary_serialize($expected);
        self::assertNotFalse($value);

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeObject(): void
    {
        $expected = new stdClass();
        $value    = igbinary_serialize($expected);
        self::assertNotFalse($value);

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid(): void
    {
        $value = "\0\1\r\n";
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unserialization failed');
        $this->adapter->unserialize($value);
    }
}
