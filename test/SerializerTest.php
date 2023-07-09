<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Serializer;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @group  Laminas_Serializer
 * @covers \Laminas\Serializer\Serializer
 */
class SerializerTest extends TestCase
{
    protected function tearDown(): void
    {
        Serializer::resetAdapterPluginManager();
    }

    public function testGetDefaultAdapterPluginManager()
    {
        $this->assertInstanceOf(AdapterPluginManager::class, Serializer::getAdapterPluginManager());
    }

    public function testChangeAdapterPluginManager()
    {
        $newPluginManager = new AdapterPluginManager(
            $this->getMockBuilder(ContainerInterface::class)->getMock()
        );
        Serializer::setAdapterPluginManager($newPluginManager);
        $this->assertSame($newPluginManager, Serializer::getAdapterPluginManager());
    }

    public function testDefaultAdapter()
    {
        $adapter = Serializer::getDefaultAdapter();
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }

    public function testFactoryValidCall()
    {
        $serializer = Serializer::factory('PhpSerialize');
        $this->assertInstanceOf(PhpSerialize::class, $serializer);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->expectException(ServiceNotFoundException::class);
        Serializer::factory('unknown');
    }

    public function testChangeDefaultAdapterWithString()
    {
        Serializer::setDefaultAdapter('Json');
        $this->assertInstanceOf(Json::class, Serializer::getDefaultAdapter());
    }

    public function testChangeDefaultAdapterWithInstance()
    {
        $newAdapter = new Adapter\PhpSerialize();

        Serializer::setDefaultAdapter($newAdapter);
        $this->assertSame($newAdapter, Serializer::getDefaultAdapter());
    }

    public function testFactoryPassesAdapterOptions()
    {
        $options = new Adapter\JsonOptions();
        self::assertFalse($options->getCycleCheck());
        $options = $options->setCycleCheck(true);
        $adapter = Serializer::factory('json', $options->toArray());
        self::assertInstanceOf(Json::class, $adapter);
        self::assertEquals(true, $adapter->getOptions()->getCycleCheck());
    }

    public function testSerializeDefaultAdapter()
    {
        $value    = 'test';
        $adapter  = Serializer::getDefaultAdapter();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value));
    }

    public function testSerializeSpecificAdapter()
    {
        $value    = 'test';
        $adapter  = new Adapter\Json();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value, $adapter));
    }

    public function testUnserializeDefaultAdapter()
    {
        $value    = 'test';
        $adapter  = Serializer::getDefaultAdapter();
        $value    = $adapter->serialize($value);
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value));
    }

    public function testUnserializeSpecificAdapter()
    {
        $adapter  = new Adapter\Json();
        $value    = '"test"';
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value, $adapter));
    }
}
