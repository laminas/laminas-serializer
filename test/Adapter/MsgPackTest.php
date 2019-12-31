<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Exception\ExtensionNotLoadedException;

/**
 * @group      Laminas_Serializer
 */
class MsgPackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer\Adapter\MsgPack
     */
    private $adapter;

    public function setUp()
    {
        if (!extension_loaded('msgpack')) {
            try {
                new Serializer\Adapter\MsgPack();
                $this->fail("Laminas\\Serializer\\Adapter\\MsgPack needs missing ext/msgpack but did't throw exception");
            } catch (ExtensionNotLoadedException $e) {}
            $this->markTestSkipped('Laminas\\Serializer\\Adapter\\MsgPack needs ext/msgpack');
        }
        $this->adapter = new Serializer\Adapter\MsgPack();
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = msgpack_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = msgpack_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = msgpack_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = msgpack_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value    = new \stdClass();
        $expected = msgpack_serialize($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $expected = 'test';
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $expected = false;
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $expected = null;
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $expected = 100;
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $expected = new \stdClass();
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialize0()
    {
        $expected = 0;
        $value    = msgpack_serialize($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid()
    {
        $value = "\0\1\r\n";
        $this->setExpectedException(
            'Laminas\Serializer\Exception\RuntimeException',
            'Unserialization failed'
        );
        $this->adapter->unserialize($value);
    }
}
