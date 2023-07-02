<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\AdapterPluginManagerFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class AdapterPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new AdapterPluginManagerFactory();

        $serializers = $factory($container, AdapterPluginManagerFactory::class);
        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);

        $reflectionClass           = new ReflectionClass(AbstractPluginManager::class);
        $internalContainerProperty = $reflectionClass->getProperty('plugins');
        $internalContainer         = $internalContainerProperty->getValue($serializers);
        self::assertInstanceOf(ServiceManager::class, $internalContainer);

        $reflectionClass         = new ReflectionClass(ServiceManager::class);
        $creationContextProperty = $reflectionClass->getProperty('creationContext');
        $this->assertEquals($container, $creationContextProperty->getValue($internalContainer));
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container  = $this->createMock(ContainerInterface::class);
        $serializer = $this->createMock(AdapterInterface::class);

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, AdapterPluginManagerFactory::class, [
            'services' => [
                'test' => $serializer,
            ],
        ]);
        $this->assertSame($serializer, $serializers->get('test'));
    }

    public function testConfiguresSerializerServicesWhenFound(): void
    {
        $serializer = $this->createMock(AdapterInterface::class);
        $config     = [
            'serializers' => [
                'aliases'   => [
                    'test' => 'test-too',
                ],
                'factories' => [
                    'test-too' => function ($container) use ($serializer) {
                        return $serializer;
                    },
                ],
            ],
        ];

        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeast(2))
            ->method('has')
            ->will($this->returnValueMap([
                ['ServiceListener', false],
                ['config', true],
            ]));

        $container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertTrue($serializers->has('test'));
        $this->assertSame($serializer, $serializers->get('test'));
        $this->assertTrue($serializers->has('test-too'));
        $this->assertSame($serializer, $serializers->get('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenServiceListenerPresent(): void
    {
        $serializer = $this->createMock(AdapterInterface::class);
        $config     = [
            'serializers' => [
                'aliases'   => [
                    'test' => 'test-too',
                ],
                'factories' => [
                    'test-too' => function ($container) use ($serializer) {
                        return $serializer;
                    },
                ],
            ],
        ];

        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with('ServiceListener')
            ->willReturn(true);

        $container
            ->expects($this->never())
            ->method('get')
            ->with('config');

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('test'));
        $this->assertFalse($serializers->has('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeast(2))
            ->method('has')
            ->will($this->returnValueMap([
                ['ServiceListener', false],
                ['config', false],
            ]));

        $container
            ->expects($this->never())
            ->method('get')
            ->with('config');

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceDoesNotContainSerializersConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeast(2))
            ->method('has')
            ->will($this->returnValueMap([
                ['ServiceListener', false],
                ['config', true],
            ]));

        $container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn(['foo' => 'bar']);

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('foo'));
    }
}
