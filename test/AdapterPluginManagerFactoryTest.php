<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer;

use Interop\Container\ContainerInterface;
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\AdapterPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AdapterPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new AdapterPluginManagerFactory();

        $serializers = $factory($container, AdapterPluginManagerFactory::class);
        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);

        if (method_exists($serializers, 'configure')) {
            $reflectionClass = new ReflectionClass($serializers);
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
    public function testFactoryConfiguresPluginManagerUnderContainerInterop()
    {
        $container = $this->createMock(ContainerInterface::class);
        $serializer = $this->createMock(AdapterInterface::class);

        $factory = new AdapterPluginManagerFactory();
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
    public function testFactoryConfiguresPluginManagerUnderServiceManagerV2()
    {
        $container = $this->createMock(ServiceLocatorInterface::class);
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

    public function testConfiguresSerializerServicesWhenFound()
    {
        $serializer = $this->createMock(AdapterInterface::class);
        $config = [
            'serializers' => [
                'aliases' => [
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
                ['config', true]
            ]));

        $container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertTrue($serializers->has('test'));
        $this->assertSame($serializer, $serializers->get('test'));
        $this->assertTrue($serializers->has('test-too'));
        $this->assertSame($serializer, $serializers->get('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenServiceListenerPresent()
    {
        $serializer = $this->createMock(AdapterInterface::class);
        $config = [
            'serializers' => [
                'aliases' => [
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

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('test'));
        $this->assertFalse($serializers->has('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceNotPresent()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeast(2))
            ->method('has')
            ->will($this->returnValueMap([
                ['ServiceListener', false],
                ['config', false]
            ]));

        $container
            ->expects($this->never())
            ->method('get')
            ->with('config');

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceDoesNotContainSerializersConfig()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->atLeast(2))
            ->method('has')
            ->will($this->returnValueMap([
                ['ServiceListener', false],
                ['config', true]
            ]));

        $container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn(['foo' => 'bar']);

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container, 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('foo'));
    }
}
