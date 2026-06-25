<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use ArrayIterator;
use JsonSerializable;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

use function json_encode;

use const JSON_UNESCAPED_UNICODE;

#[CoversClass(Json::class)]
final class JsonTest extends TestCase
{
    private Json $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Json();
    }

    public function testSerializeString(): void
    {
        $value    = 'test';
        $expected = '"test"';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeFalse(): void
    {
        $value    = false;
        $expected = 'false';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeNull(): void
    {
        $value    = null;
        $expected = 'null';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeInteger(): void
    {
        $value    = 100;
        $expected = '100';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeFloat(): void
    {
        $value    = 1.23;
        $expected = '1.23';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeArray(): void
    {
        $value    = [1, 2, 3];
        $expected = '[1,2,3]';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeAssocArray(): void
    {
        $value    = ['foo' => 'bar', 'baz' => 42];
        $expected = '{"foo":"bar","baz":42}';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeObject(): void
    {
        $value       = new stdClass();
        $value->test = "test";
        $expected    = '{"test":"test"}';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeUnicode(): void
    {
        $value    = 'žluťoučký kůň';
        $expected = '"žluťoučký kůň"';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeSpecialChars(): void
    {
        $value    = "line\nbreak\tand\"quote\"";
        $expected = '"line\nbreak\tand\"quote\""';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeWithCycleCheckFalseThrowsNativeExceptionOnCyclicArray(): void
    {
        $a         = [];
        $a['self'] = &$a;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Serialization failed: Recursion detected');
        $this->adapter->serialize($a);
    }

    public function testSerializeWithCycleCheckTrueThrowsOnCyclicArray(): void
    {
        $a         = [];
        $a['self'] = &$a;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Serialization failed: Recursion detected');
        $this->adapter->serialize($a);
    }

    public function testSerializeWithCycleCheckTrueThrowsOnCyclicObject(): void
    {
        $obj       = new stdClass();
        $obj->self = $obj;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Serialization failed: Recursion detected');
        $this->adapter->serialize($obj);
    }

    public function testSerializeWithCycleCheckTrueDoesNotThrowOnNonCyclicValue(): void
    {
        $value    = ['foo' => 'bar', 'nested' => ['a', 'b']];
        $expected = '{"foo":"bar","nested":["a","b"]}';

        $data = $this->adapter->serialize($value);
        self::assertEquals($expected, $data);
    }

    public function testSerializeObjectWithJsonSerializable(): void
    {
        $obj      = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['foo' => 'bar'];
            }
        };
        $expected = '{"foo":"bar"}';

        $data = $this->adapter->serialize($obj);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeArrayIterator(): void
    {
        $iterator = new ArrayIterator(['foo' => 'bar', 'baz' => 5]);
        $expected = '{"foo":"bar","baz":5}';

        $data = $this->adapter->serialize($iterator);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNestedArrayOfObjects(): void
    {
        $obj1      = new stdClass();
        $obj1->id  = 1;
        $obj2      = new stdClass();
        $obj2->foo = 2;
        $value     = [$obj1, $obj2];
        $expected  = '[{"id":1},{"foo":2}]';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObjectOfArrays(): void
    {
        $value    = [
            'codeDbVar' => ['age' => ['int', 5], 'prenom' => ['varchar', 50]],
            234         => [22, 'jb'],
            346         => [64, 'francois'],
            21          => [12, 'paul'],
        ];
        $expected = json_encode($value, JSON_UNESCAPED_UNICODE);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testDeserializeString(): void
    {
        $json     = '"test"';
        $expected = 'test';

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeFalse(): void
    {
        $json     = 'false';
        $expected = false;

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeNull(): void
    {
        $json     = 'null';
        $expected = null;

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeInteger(): void
    {
        $json     = '100';
        $expected = 100;

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeFloat(): void
    {
        $json     = '1.23';
        $expected = 1.23;

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeArray(): void
    {
        $json     = '[1,2,3]';
        $expected = [1, 2, 3];

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeAssocArray(): void
    {
        $json     = '{"foo":"bar","baz":42}';
        $expected = ['foo' => 'bar', 'baz' => 42];

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeWithAssocArrayTrueReturnsArray(): void
    {
        $this->adapter->getOptions()->setAssocArray(true);
        $json = '{"foo":"bar","baz":42}';

        $data = $this->adapter->unserialize($json);
        $this->assertIsArray($data);
        $this->assertEquals(['foo' => 'bar', 'baz' => 42], $data);
    }

    public function testDeserializeWithAssocArrayFalseReturnsObject(): void
    {
        $this->adapter->getOptions()->setAssocArray(false);
        $json          = '{"foo":"bar","baz":42}';
        $expected      = new stdClass();
        $expected->foo = 'bar';
        $expected->baz = 42;

        $data = $this->adapter->unserialize($json);
        $this->assertIsObject($data);
        $this->assertEquals($expected, $data);
    }

    public function testDeserializeWithAssocArrayTrueReturnsArrayForNestedObjects(): void
    {
        $this->adapter->getOptions()->setAssocArray(true);
        $json     = '{"outer":{"inner":"value"}}';
        $expected = ['outer' => ['inner' => 'value']];

        $data = $this->adapter->unserialize($json);
        $this->assertIsArray($data);
        $this->assertEquals($expected, $data);
    }

    public function testDeserializeWithAssocArrayFalseReturnsObjectForNestedObjects(): void
    {
        $this->adapter->getOptions()->setAssocArray(false);
        $json = '{"outer":{"inner":"value"}}';

        $data = $this->adapter->unserialize($json);
        $this->assertIsObject($data);
        $this->assertIsObject($data->outer);
        $this->assertEquals('value', $data->outer->inner);
    }

    public function testDeserializeUnicode(): void
    {
        $json     = '"žluťoučký kůň"';
        $expected = 'žluťoučký kůň';

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeSpecialChars(): void
    {
        $json     = '"line\nbreak\tand\"quote\""';
        $expected = "line\nbreak\tand\"quote\"";

        $data = $this->adapter->unserialize($json);
        self::assertEquals($expected, $data);
    }

    public function testDeserializeEmptyKey(): void
    {
        $json = '{"":"test"}';

        $data = $this->adapter->unserialize($json);
        $this->assertIsArray($data);
        $this->assertEquals(['' => 'test'], $data);
    }

    public function testDeserializeThrowsOnInvalidJson(): void
    {
        $json = '{invalid json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unserialization failed: Syntax error');
        $this->adapter->unserialize($json);
    }

    public function testDeserializeThrowsOnOctal(): void
    {
        $json = '010';

        $this->expectException(RuntimeException::class);
        $this->adapter->unserialize($json);
    }
}
