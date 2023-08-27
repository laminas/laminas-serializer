<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Adapter\PhpSerialize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(PhpSerialize::class)]
class PhpSerializeTest extends TestCase
{
    private PhpSerialize $adapter;

    protected function setUp(): void
    {
        $this->adapter = new PhpSerialize();
    }

    public function testSerializeString(): void
    {
        $value    = 'test';
        $expected = 's:4:"test";';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeFalse(): void
    {
        $value    = false;
        $expected = 'b:0;';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNull(): void
    {
        $value    = null;
        $expected = 'N;';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNumeric(): void
    {
        $value    = 100;
        $expected = 'i:100;';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeObject(): void
    {
        $value    = new stdClass();
        $expected = 'O:8:"stdClass":0:{}';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeString(): void
    {
        $value    = 's:4:"test";';
        $expected = 'test';

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeFalse(): void
    {
        $value    = 'b:0;';
        $expected = false;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNull(): void
    {
        $value    = 'N;';
        $expected = null;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNumeric(): void
    {
        $value    = 'i:100;';
        $expected = 100;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeObject(): void
    {
        $value    = 'O:8:"stdClass":0:{}';
        $expected = new stdClass();

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function invalidStrings(): array
    {
        return [
            'not-serialized'        => ['foobar', 'Serialized data must be a string containing serialized PHP code'],
            'invalid-serialization' => ['a:foobar', 'Unserialization failed'],
        ];
    }

    #[DataProvider('invalidStrings')]
    public function testUnserializingInvalidStringRaisesException(string $string, string $expected): void
    {
        $this->expectException(Serializer\Exception\RuntimeException::class);
        $this->expectExceptionMessage($expected);
        $this->adapter->unserialize($string);
    }
}
