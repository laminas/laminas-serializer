<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Serializer;

/**
 * @group      Laminas_Serializer
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        Serializer::resetAdapterPluginManager();
    }

    public function testGetDefaultAdapterPluginManager()
    {
        $this->assertTrue(Serializer::getAdapterPluginManager() instanceof AdapterPluginManager);
    }

    public function testChangeAdapterPluginManager()
    {
        $newPluginManager = new AdapterPluginManager();
        Serializer::setAdapterPluginManager($newPluginManager);
        $this->assertTrue(Serializer::getAdapterPluginManager() === $newPluginManager);
    }

    public function testDefaultAdapter()
    {
        $adapter = Serializer::getDefaultAdapter();
        $this->assertTrue($adapter instanceof Adapter\AdapterInterface);
    }

    public function testFactoryValidCall()
    {
        $serializer = Serializer::factory('PhpSerialize');
        $this->assertTrue($serializer instanceof Adapter\PHPSerialize);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->setExpectedException('Laminas\ServiceManager\Exception\ServiceNotFoundException');
        Serializer::factory('unknown');
    }

    public function testFactoryOnADummyClassAdapter()
    {
        $adapters = new AdapterPluginManager();
        $adapters->setInvokableClass('dummy', 'LaminasTest\Serializer\TestAsset\Dummy');
        Serializer::setAdapterPluginManager($adapters);
        $this->setExpectedException('Laminas\\Serializer\\Exception\\RuntimeException', 'AdapterInterface');
        Serializer::factory('dummy');
    }

    public function testChangeDefaultAdapterWithString()
    {
        Serializer::setDefaultAdapter('Json');
        $this->assertTrue(Serializer::getDefaultAdapter() instanceof Adapter\Json);
    }

    public function testChangeDefaultAdapterWithInstance()
    {
        $newAdapter = new Adapter\PhpSerialize();

        Serializer::setDefaultAdapter($newAdapter);
        $this->assertTrue($newAdapter === Serializer::getDefaultAdapter());
    }

    public function testFactoryPassesAdapterOptions()
    {
        $options = new Adapter\PythonPickleOptions(array('protocol' => 2));
        /** @var Adapter\PythonPickle $adapter  */
        $adapter = Serializer::factory('pythonpickle', $options);
        $this->assertTrue($adapter instanceof Adapter\PythonPickle);
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
