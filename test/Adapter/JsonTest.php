<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Json\Json as LaminasJson;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\Adapter\JsonOptions;
use Laminas\Serializer\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Json::class)]
class JsonTest extends TestCase
{
    private Json $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Json();
    }

    public function testAdapterAcceptsOptions(): void
    {
        $adapter = new Json();
        $options = new JsonOptions([
            'cycle_check'             => true,
            'enable_json_expr_finder' => true,
            'object_decode_type'      => 1,
        ]);
        $adapter->setOptions($options);

        self::assertEquals(true, $adapter->getOptions()->getCycleCheck());
        self::assertEquals(true, $adapter->getOptions()->getEnableJsonExprFinder());
        self::assertEquals(1, $adapter->getOptions()->getObjectDecodeType());
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

    public function testSerializeNumeric(): void
    {
        $value    = 100;
        $expected = '100';

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

    public function testUnserializeString(): void
    {
        $value    = '"test"';
        $expected = 'test';

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeFalse(): void
    {
        $value    = 'false';
        $expected = false;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNull(): void
    {
        $value    = 'null';
        $expected = null;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeNumeric(): void
    {
        $value    = '100';
        $expected = 100;

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeAsArray(): void
    {
        $value    = '{"test":"test"}';
        $expected = ['test' => 'test'];

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserializeAsObject(): void
    {
        $value          = '{"test":"test"}';
        $expected       = new stdClass();
        $expected->test = 'test';

        $this->adapter->getOptions()->setObjectDecodeType(LaminasJson::TYPE_OBJECT);

        $data = $this->adapter->unserialize($value);
        self::assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid(): void
    {
        $value = 'not a serialized string';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unserialization failed: Decoding failed: Syntax error');
        $this->adapter->unserialize($value);
    }
}
