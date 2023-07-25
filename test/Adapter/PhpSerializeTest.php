<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\Exception\RuntimeException;
use My\Dummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(PhpSerialize::class)]
class PhpSerializeTest extends TestCase
{
    /** @var Serializer\Adapter\PhpSerialize */
    private $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Serializer\Adapter\PhpSerialize();
    }

    protected function tearDown(): void
    {
        $this->adapter = null;
    }

    public function testSerializeString(): void
    {
        $value    = 'test';
        $expected = 's:4:"test";';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse(): void
    {
        $value    = false;
        $expected = 'b:0;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull(): void
    {
        $value    = null;
        $expected = 'N;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric(): void
    {
        $value    = 100;
        $expected = 'i:100;';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject(): void
    {
        $value    = new stdClass();
        $expected = 'O:8:"stdClass":0:{}';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString(): void
    {
        $value    = 's:4:"test";';
        $expected = 'test';

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse(): void
    {
        $value    = 'b:0;';
        $expected = false;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull(): void
    {
        $value    = 'N;';
        $expected = null;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric(): void
    {
        $value    = 'i:100;';
        $expected = 100;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject(): void
    {
        $value    = 'O:8:"stdClass":0:{}';
        $expected = new stdClass();

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    /**
     * @return array<string, array{0: mixed, 1: string}>
     */
    public static function invalidSerializationTypes(): array
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
     * @param mixed $value
     */
    #[DataProvider('invalidSerializationTypes')]
    public function testUnserializingNoStringRaisesException($value, string $expected): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expected);
        $this->adapter->unserialize($value);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function invalidStrings(): array
    {
        return [
            'not-serialized'        => ['foobar', 'foobar'],
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

    /**
     * @requires PHP 7.0
     */
    public function testPhp7WillNotUnserializeObjectsWhenUnserializeWhitelistedClassesIsFalse(): void
    {
        $value = 'O:8:"stdClass":0:{}';
        $this->adapter->getOptions()->setUnserializeClassWhitelist(false);

        $data = $this->adapter->unserialize($value);

        $this->assertNotInstanceOf(stdClass::class, $data);
        $this->assertInstanceOf('__PHP_Incomplete_Class', $data);
    }

    /**
     * @requires PHP 7.0
     */
    public function testUnserializeWillNotUnserializeClassesThatAreNotInTheWhitelist(): void
    {
        $value = 'O:8:"stdClass":0:{}';

        $this->adapter->getOptions()->setUnserializeClassWhitelist([Dummy::class]);

        $data = $this->adapter->unserialize($value);

        $this->assertNotInstanceOf(stdClass::class, $data);
        $this->assertInstanceOf('__PHP_Incomplete_Class', $data);
    }

    /**
     * @requires PHP 7.0
     */
    public function testUnserializeWillUnserializeAnyClassWhenUnserializeWhitelistedClassesIsTrue(): void
    {
        $value = 'O:8:"stdClass":0:{}';

        $this->adapter->getOptions()->setUnserializeClassWhitelist([stdClass::class]);

        $data = $this->adapter->unserialize($value);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertNotInstanceOf('__PHP_Incomplete_Class', $data);
    }
}
