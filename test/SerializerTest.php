<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Exception;
use interop\container\containerinterface;
use Laminas\Serializer\Adapter;
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\Adapter\PythonPickle;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Exception\RuntimeException;
use Laminas\Serializer\Serializer;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

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
            $this->getMockBuilder(containerinterface::class)->getMock()
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

    public function testFactoryOnADummyClassAdapter()
    {
        $adapters = new AdapterPluginManager(new ServiceManager(), [
            'invokables' => [
                'dummy' => TestAsset\Dummy::class,
            ],
        ]);
        Serializer::setAdapterPluginManager($adapters);

        try {
            Serializer::factory('dummy');
            $this->fail('Expected exception when requesting invalid adapter type');
        } catch (InvalidServiceException $e) {
            $this->assertStringContainsString('Dummy is invalid', $e->getMessage());
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Dummy is invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->fail('Unexpected exception raised by plugin manager for invalid adapter type');
        }
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
        $options = new Adapter\PythonPickleOptions(['protocol' => 2]);
        /** @var Adapter\PythonPickle $adapter  */
        $adapter = Serializer::factory('pythonpickle', $options->toArray());
        $this->assertInstanceOf(PythonPickle::class, $adapter);
        $this->assertEquals(2, $adapter->getOptions()->getProtocol());
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
