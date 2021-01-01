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

class AdapterPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new AdapterPluginManagerFactory();

        $serializers = $factory($container, AdapterPluginManagerFactory::class);
        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);

        if (method_exists($serializers, 'configure')) {
            // laminas-servicemanager v3
            $this->assertAttributeSame($container, 'creationContext', $serializers);
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
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $serializer = $this->prophesize(AdapterInterface::class)->reveal();

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
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $serializer = $this->prophesize(AdapterInterface::class)->reveal();

        $factory = new AdapterPluginManagerFactory();
        $factory->setCreationOptions([
            'services' => [
                'test' => $serializer,
            ],
        ]);

        $serializers = $factory->createService($container->reveal());
        $this->assertSame($serializer, $serializers->get('test'));
    }

    public function testConfiguresSerializerServicesWhenFound(): void
    {
        $serializer = $this->prophesize(AdapterInterface::class)->reveal();
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

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container->reveal(), 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertTrue($serializers->has('test'));
        $this->assertSame($serializer, $serializers->get('test'));
        $this->assertTrue($serializers->has('test-too'));
        $this->assertSame($serializer, $serializers->get('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenServiceListenerPresent(): void
    {
        $serializer = $this->prophesize(AdapterInterface::class)->reveal();
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

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(true);
        $container->has('config')->shouldNotBeCalled();
        $container->get('config')->shouldNotBeCalled();

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container->reveal(), 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('test'));
        $this->assertFalse($serializers->has('test-too'));
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(false);
        $container->get('config')->shouldNotBeCalled();

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container->reveal(), 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
    }

    public function testDoesNotConfigureSerializerServicesWhenConfigServiceDoesNotContainSerializersConfig(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['foo' => 'bar']);

        $factory = new AdapterPluginManagerFactory();
        $serializers = $factory($container->reveal(), 'SerializerAdapterManager');

        $this->assertInstanceOf(AdapterPluginManager::class, $serializers);
        $this->assertFalse($serializers->has('foo'));
    }
}
