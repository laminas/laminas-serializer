<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Json\Json;
use Laminas\Serializer;
use Laminas\Serializer\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Serializer\Adapter\Json::class)]
class JsonTest extends TestCase
{
    /** @var Serializer\Adapter\Json */
    private $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Serializer\Adapter\Json();
    }

    protected function tearDown(): void
    {
        $this->adapter = null;
    }

    public function testAdapterAcceptsOptions()
    {
        $adapter = new Serializer\Adapter\Json();
        $options = new Serializer\Adapter\JsonOptions([
            'cycle_check'             => true,
            'enable_json_expr_finder' => true,
            'object_decode_type'      => 1,
        ]);
        $adapter->setOptions($options);

        $this->assertEquals(true, $adapter->getOptions()->getCycleCheck());
        $this->assertEquals(true, $adapter->getOptions()->getEnableJsonExprFinder());
        $this->assertEquals(1, $adapter->getOptions()->getObjectDecodeType());
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = '"test"';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = 'false';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = 'null';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = '100';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value       = new stdClass();
        $value->test = "test";
        $expected    = '{"test":"test"}';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $value    = '"test"';
        $expected = 'test';

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $value    = 'false';
        $expected = false;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $value    = 'null';
        $expected = null;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $value    = '100';
        $expected = 100;

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeAsArray()
    {
        $value    = '{"test":"test"}';
        $expected = ['test' => 'test'];

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeAsObject()
    {
        $value          = '{"test":"test"}';
        $expected       = new stdClass();
        $expected->test = 'test';

        $this->adapter->getOptions()->setObjectDecodeType(Json::TYPE_OBJECT);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid()
    {
        $value = 'not a serialized string';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unserialization failed: Decoding failed: Syntax error');
        $this->adapter->unserialize($value);
    }
}
