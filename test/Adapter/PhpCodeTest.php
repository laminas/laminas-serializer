<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer;
use Laminas\Serializer\Adapter\PhpCode;
use LaminasTest\Serializer\TestAsset\Dummy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function var_export;

#[CoversClass(PhpCode::class)]
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
    public function testSerializeObject(): void
    {
        $object = new Dummy();
        $data   = $this->adapter->serialize($object);

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
     * @param mixed $unserialized
     */
    #[DataProvider('serializedValuesProvider')]
    public function testSerialize($unserialized, string $serialized): void
    {
        $this->assertEquals($serialized, $this->adapter->serialize($unserialized));
    }

    /**
     * @param mixed $unserialized
     */
    #[DataProvider('serializedValuesProvider')]
    public function testUnserialize($unserialized, string $serialized): void
    {
        $this->assertEquals($unserialized, $this->adapter->unserialize($serialized));
    }

    /**
     * @return array<string, array<int, string|mixed>>
     */
    public static function serializedValuesProvider(): array
    {
        return [
            // Description => [unserialized, serialized]
            'String' => ['test', var_export('test', true)],
            'true'   => [true, var_export(true, true)],
            'false'  => [false, var_export(false, true)],
            'null'   => [null, var_export(null, true)],
            'int'    => [1, var_export(1, true)],
            'float'  => [1.2, var_export(1.2, true)],

            // Boolean as string
            '"true"'             => ['true', var_export('true', true)],
            '"false"'            => ['false', var_export('false', true)],
            '"null"'             => ['null', var_export('null', true)],
            '"1"'                => ['1', var_export('1', true)],
            '"1.2"'              => ['1.2', var_export('1.2', true)],
            'PHP Code with tags' => ['<?php echo "test"; ?>', var_export('<?php echo "test"; ?>', true)],
        ];
    }
}
