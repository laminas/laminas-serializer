<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use __PHP_Incomplete_Class;
use Laminas\Serializer;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\Adapter\PhpSerializeOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(PhpSerialize::class)]
final class PhpSerializeTest extends TestCase
{
    public function testSerializeString(): void
    {
        $value    = 'test';
        $expected = 's:4:"test";';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeFalse(): void
    {
        $value    = false;
        $expected = 'b:0;';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNull(): void
    {
        $value    = null;
        $expected = 'N;';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNumeric(): void
    {
        $value    = 100;
        $expected = 'i:100;';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeObject(): void
    {
        $value    = new stdClass();
        $expected = 'O:8:"stdClass":0:{}';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeString(): void
    {
        $value    = 's:4:"test";';
        $expected = 'test';

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeFalse(): void
    {
        $value    = 'b:0;';
        $expected = false;

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNull(): void
    {
        $value    = 'N;';
        $expected = null;

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNumeric(): void
    {
        $value    = 'i:100;';
        $expected = 100;

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeObject(): void
    {
        $value    = 'O:8:"stdClass":0:{}';
        $expected = new stdClass();

        $adapter = new PhpSerialize();
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
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
        $adapter = new PhpSerialize();
        $adapter->unserialize($string);
    }

    public function testWhileListIsFalse(): void
    {
        $value = 'O:8:"stdClass":1:{s:7:"myProps";s:5:"hello";}';

        $options = new PhpSerializeOptions();
        $options->setUnserializeClassWhitelist(false);
        $adapter = new PhpSerialize($options);
        /** @var mixed $data */
        $data = $adapter->unserialize($value);
        $this->assertInstanceOf(__PHP_Incomplete_Class::class, $data);
        $props = (array) $data;

        self::assertSame('hello', $props['myProps'] ?? null);
    }

    public function testWhileListContainsClass(): void
    {
        $expected          = new stdClass();
        $expected->myProps = 'hello';

        $value = 'O:8:"stdClass":1:{s:7:"myProps";s:5:"hello";}';

        $options = new PhpSerializeOptions();
        $options->setUnserializeClassWhitelist([self::class]);
        $adapter = new PhpSerialize($options);
        /** @var mixed $data */
        $data = $adapter->unserialize($value);

        $this->assertInstanceOf(__PHP_Incomplete_Class::class, $data);
        $props = (array) $data;

        self::assertSame('hello', $props['myProps'] ?? null);
    }
}
