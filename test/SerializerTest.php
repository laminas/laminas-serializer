<?php

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Serializer;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(Serializer::class)]
class SerializerTest extends TestCase
{
    protected function tearDown(): void
    {
        Serializer::resetAdapterPluginManager();
    }

    public function testGetDefaultAdapterPluginManager(): void
    {
        self::assertInstanceOf(AdapterPluginManager::class, Serializer::getAdapterPluginManager());
    }

    public function testChangeAdapterPluginManager(): void
    {
        $newPluginManager = new AdapterPluginManager(
            $this->getMockBuilder(ContainerInterface::class)->getMock()
        );
        Serializer::setAdapterPluginManager($newPluginManager);
        self::assertSame($newPluginManager, Serializer::getAdapterPluginManager());
    }

    public function testDefaultAdapter(): void
    {
        $adapter = Serializer::getDefaultAdapter();
        self::assertInstanceOf(AdapterInterface::class, $adapter);
    }

    public function testFactoryValidCall(): void
    {
        $serializer = Serializer::factory('PhpSerialize');
        self::assertInstanceOf(PhpSerialize::class, $serializer);
    }

    public function testFactoryUnknownAdapter(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        Serializer::factory('unknown');
    }

    public function testChangeDefaultAdapterWithString(): void
    {
        Serializer::setDefaultAdapter('Json');
        self::assertInstanceOf(Json::class, Serializer::getDefaultAdapter());
    }

    public function testChangeDefaultAdapterWithInstance(): void
    {
        $newAdapter = new Adapter\PhpSerialize();

        Serializer::setDefaultAdapter($newAdapter);
        self::assertSame($newAdapter, Serializer::getDefaultAdapter());
    }

    public function testFactoryPassesAdapterOptions(): void
    {
        $options = new Adapter\JsonOptions();
        self::assertFalse($options->getCycleCheck());
        $options->setCycleCheck(true);
        $adapter = Serializer::factory('json', $options->toArray());
        self::assertInstanceOf(Json::class, $adapter);
        self::assertEquals(true, $adapter->getOptions()->getCycleCheck());
    }

    public function testSerializeDefaultAdapter(): void
    {
        $value    = 'test';
        $adapter  = Serializer::getDefaultAdapter();
        $expected = $adapter->serialize($value);
        self::assertEquals($expected, Serializer::serialize($value));
    }

    public function testSerializeSpecificAdapter(): void
    {
        $value    = 'test';
        $adapter  = new Adapter\Json();
        $expected = $adapter->serialize($value);
        self::assertEquals($expected, Serializer::serialize($value, $adapter));
    }

    public function testUnserializeDefaultAdapter(): void
    {
        $value    = 'test';
        $adapter  = Serializer::getDefaultAdapter();
        $value    = $adapter->serialize($value);
        $expected = $adapter->unserialize($value);
        self::assertEquals($expected, Serializer::unserialize($value));
    }

    public function testUnserializeSpecificAdapter(): void
    {
        $adapter  = new Adapter\Json();
        $value    = '"test"';
        $expected = $adapter->unserialize($value);
        self::assertEquals($expected, Serializer::unserialize($value, $adapter));
    }
}
