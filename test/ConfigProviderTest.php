<?php

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\ConfigProvider;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    /**
     * @param non-empty-string $serviceName
     * @dataProvider factoryServiceNames
     */
    public function testHasExpectedServiceNames(string $serviceName): void
    {
        $dependencies = (new ConfigProvider())->getDependencyConfig();
        self::assertArrayHasKey($serviceName, $dependencies['factories'] ?? []);
    }

    /**
     * @return iterable<non-empty-string,array{0:non-empty-string}>
     */
    public static function factoryServiceNames(): iterable
    {
        return [
            'SerializerAdapterManager'  => ['SerializerAdapterManager'],
            AdapterPluginManager::class => [AdapterPluginManager::class],
            AdapterInterface::class     => [AdapterInterface::class],
        ];
    }
}
