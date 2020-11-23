<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use LaminasTest\Serializer\TestAsset\Dummy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\Serializer\Adapter\PhpCode
 */
class PhpCodeTest extends TestCase
{
    /** @var Serializer\Adapter\PhpCode */
    private $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Serializer\Adapter\PhpCode();
    }

    /**
     * Test when serializing a PHP object
     */
    public function testSerializeObject()
    {
        $object = new Dummy();
        $data = $this->adapter->serialize($object);

        $this->assertEquals(var_export($object, true), $data);
    }

    /* TODO: PHP Fatal error:  Call to undefined method stdClass::__set_state()
        public function testUnserializeObject()
        {
            $value    = "stdClass::__set_state(array(\n))";
            $expected = new stdClass();

            $data = $this->adapter->unserialize($value);
            $this->assertEquals($expected, $data);
        }
    */

    /**
     * @dataProvider serializedValuesProvider
     */
    public function testSerialize($unserialized, $serialized)
    {
        $this->assertEquals($serialized, $this->adapter->serialize($unserialized));
    }

    /**
     * @dataProvider serializedValuesProvider
     */
    public function testUnserialize($unserialized, $serialized)
    {
        $this->assertEquals($unserialized, $this->adapter->unserialize($serialized));
    }

    public function serializedValuesProvider()
    {
        return [
            // Description => [unserialized, serialized]
            'String' => ['test', var_export('test', true)],
            'true' => [true, var_export(true, true)],
            'false' => [false, var_export(false, true)],
            'null' => [null, var_export(null, true)],
            'int' => [1, var_export(1, true)],
            'float' => [1.2, var_export(1.2, true)],

            // Boolean as string
            '"true"' => ['true', var_export('true', true)],
            '"false"' => ['false', var_export('false', true)],
            '"null"' => ['null', var_export('null', true)],
            '"1"' => ['1', var_export('1', true)],
            '"1.2"' => ['1.2', var_export('1.2', true)],

            'PHP Code with tags' => ['<?php echo "test"; ?>', var_export('<?php echo "test"; ?>', true)]
        ];
    }

    public function testUnserializeInvalid()
    {
        if (version_compare(PHP_VERSION, '7', 'ge')) {
            $this->markTestSkipped('Cannot catch parse errors in PHP 7+');
        }
        $value = 'not a serialized string';

        $this->expectException('Laminas\Serializer\Exception\RuntimeException');
        $this->expectExceptionMessage('syntax error');
        $this->adapter->unserialize($value);
    }
}
