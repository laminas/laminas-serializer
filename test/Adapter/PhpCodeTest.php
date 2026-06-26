<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\Adapter;

use Laminas\Serializer\Adapter\PhpCode;
use LaminasTest\Serializer\TestAsset\Dummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function var_export;

#[CoversClass(PhpCode::class)]
final class PhpCodeTest extends TestCase
{
    public function testSerializeObject(): void
    {
        $object  = new Dummy();
        $adapter = new PhpCode();
        $data    = $adapter->serialize($object);

        self::assertEquals(var_export($object, true), $data);
    }

    #[DataProvider('serializedValuesProvider')]
    public function testSerialize(mixed $unserialized, string $serialized): void
    {
        $adapter = new PhpCode();
        self::assertEquals($serialized, $adapter->serialize($unserialized));
    }

    #[DataProvider('serializedValuesProvider')]
    public function testUnserialize(mixed $unserialized, string $serialized): void
    {
        $adapter = new PhpCode();
        self::assertEquals($unserialized, $adapter->unserialize($serialized));
    }

    /**
     * @return array<non-empty-string, array{mixed,string}>
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
