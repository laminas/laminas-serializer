<?php

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\GenericSerializerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class GenericSerializerFactoryTest extends TestCase
{
    public function testWillRequestInstanceFromPluginManager(): void
    {
        $factory   = new GenericSerializerFactory(Json::class, []);
        $container = $this->createMock(ContainerInterface::class);
        $plugins   = new AdapterPluginManager($container);

        $container
            ->expects(self::once())
            ->method('get')
            ->with(AdapterPluginManager::class)
            ->willReturn($plugins);

        $adapter = $factory($container);
        self::assertInstanceOf(Json::class, $adapter);
    }
}
