<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Exception\RuntimeException;
use Laminas\Serializer\Serializer;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Serializer
 * @covers \Laminas\Serializer\Serializer
 */
class SerializerTest extends TestCase
{
    protected function tearDown()
    {
        Serializer::resetAdapterPluginManager();
    }

    public function testGetDefaultAdapterPluginManager()
    {
        $this->assertInstanceOf('Laminas\Serializer\AdapterPluginManager', Serializer::getAdapterPluginManager());
    }

    public function testChangeAdapterPluginManager()
    {
        $newPluginManager = new AdapterPluginManager(
            $this->getMockBuilder('Interop\Container\ContainerInterface')->getMock()
        );
        Serializer::setAdapterPluginManager($newPluginManager);
        $this->assertSame($newPluginManager, Serializer::getAdapterPluginManager());
    }

    public function testDefaultAdapter()
    {
        $adapter = Serializer::getDefaultAdapter();
        $this->assertInstanceOf('Laminas\Serializer\Adapter\AdapterInterface', $adapter);
    }

    public function testFactoryValidCall()
    {
        $serializer = Serializer::factory('PhpSerialize');
        $this->assertInstanceOf('Laminas\Serializer\Adapter\PHPSerialize', $serializer);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->expectException('Laminas\ServiceManager\Exception\ServiceNotFoundException');
        Serializer::factory('unknown');
    }

    public function testFactoryOnADummyClassAdapter()
    {
        $adapters = new AdapterPluginManager(new ServiceManager, [
            'invokables' => [
                'dummy' => TestAsset\Dummy::class
            ]
        ]);
        Serializer::setAdapterPluginManager($adapters);

        try {
            Serializer::factory('dummy');
            $this->fail('Expected exception when requesting invalid adapter type');
        } catch (InvalidServiceException $e) {
            $this->assertContains('Dummy is invalid', $e->getMessage());
        } catch (RuntimeException $e) {
            $this->assertContains('Dummy is invalid', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('Unexpected exception raised by plugin manager for invalid adapter type');
        }
    }

    public function testChangeDefaultAdapterWithString()
    {
        Serializer::setDefaultAdapter('Json');
        $this->assertInstanceOf('Laminas\Serializer\Adapter\Json', Serializer::getDefaultAdapter());
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
        $this->assertInstanceOf('Laminas\Serializer\Adapter\PythonPickle', $adapter);
        $this->assertEquals(2, $adapter->getOptions()->getProtocol());
    }

    public function testSerializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Serializer::getDefaultAdapter();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value));
    }

    public function testSerializeSpecificAdapter()
    {
        $value = 'test';
        $adapter = new Adapter\Json();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value, $adapter));
    }

    public function testUnserializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Serializer::getDefaultAdapter();
        $value = $adapter->serialize($value);
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value));
    }

    public function testUnserializeSpecificAdapter()
    {
        $adapter = new Adapter\Json();
        $value = '"test"';
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value, $adapter));
    }
}
