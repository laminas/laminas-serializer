<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer;

use interop\container\containerinterface;
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\AdapterPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function method_exists;

class AdapterPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->createMock(containerinterface::class);
        $factory   = new AdapterPluginManagerFactory();

        $serializers = $factory($container, AdapterPluginManagerFactory::class);
        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);

        if (method_exists($serializers, 'configure')) {
            $reflectionClass         = new ReflectionClass($serializers);
            $creationContextProperty = $reflectionClass->getProperty('creationContext');
            $creationContextProperty->setAccessible(true);

            // laminas-servicemanager v3
            $this->assertEquals($container, $creationContextProperty->getValue($serializers));
        } else {
            // laminas-servicemanager v2
            $this->assertSame($container, $serializers->getServiceLocator());
        }
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container  = $this->createMock(containerinterface::class);
        $serializer = $this->createMock(AdapterInterface::class);

        $factory     = new AdapterPluginManagerFactory();
        $serializers = $factory($container, AdapterPluginManagerFactory::class, [
            'services' => [
                'test' => $serializer,
            ],
        ]);
        $this->assertSame($serializer, $serializers->get('test'));
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderServiceManagerV2(): void
    {
        $container  = $this->createMock(ServiceLocatorInterface::class);
        $serializer = $this->createMock(AdapterInterface::class);

        $factory = new AdapterPluginManagerFactory();
        $factory->setCreationOptions([
            'services' => [
                'test' => $serializer,
            ],
        ]);

        $serializers = $factory->createService($container);
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

        $container = $this->createMock(containerinterface::class);

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

        $container = $this->createMock(containerinterface::class);

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
        $container = $this->createMock(containerinterface::class);

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
        $container = $this->createMock(containerinterface::class);

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
