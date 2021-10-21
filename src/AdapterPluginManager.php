<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use zend\serializer\adapter\igbinary;
use zend\serializer\adapter\json;
use zend\serializer\adapter\msgpack;
use zend\serializer\adapter\phpcode;
use zend\serializer\adapter\phpserialize;
use zend\serializer\adapter\pythonpickle;
use zend\serializer\adapter\wddx;

use function gettype;
use function is_object;
use function sprintf;

/**
 * Plugin manager implementation for serializer adapters.
 *
 * Enforces that adapters retrieved are instances of
 * Adapter\AdapterInterface. Additionally, it registers a number of default
 * adapters available.
 */
class AdapterPluginManager extends AbstractPluginManager
{
    /** @var array<string, string> */
    protected $aliases = [
        'igbinary'     => Adapter\IgBinary::class,
        'igBinary'     => Adapter\IgBinary::class,
        'IgBinary'     => Adapter\IgBinary::class,
        'json'         => Adapter\Json::class,
        'Json'         => Adapter\Json::class,
        'msgpack'      => Adapter\MsgPack::class,
        'msgPack'      => Adapter\MsgPack::class,
        'MsgPack'      => Adapter\MsgPack::class,
        'phpcode'      => Adapter\PhpCode::class,
        'phpCode'      => Adapter\PhpCode::class,
        'PhpCode'      => Adapter\PhpCode::class,
        'phpserialize' => Adapter\PhpSerialize::class,
        'phpSerialize' => Adapter\PhpSerialize::class,
        'PhpSerialize' => Adapter\PhpSerialize::class,
        'pythonpickle' => Adapter\PythonPickle::class,
        'pythonPickle' => Adapter\PythonPickle::class,
        'PythonPickle' => Adapter\PythonPickle::class,
        'wddx'         => Adapter\Wddx::class,
        'Wddx'         => Adapter\Wddx::class,

        // Legacy Zend Framework aliases
        igbinary::class     => Adapter\IgBinary::class,
        json::class         => Adapter\Json::class,
        msgpack::class      => Adapter\MsgPack::class,
        phpcode::class      => Adapter\PhpCode::class,
        phpserialize::class => Adapter\PhpSerialize::class,
        pythonpickle::class => Adapter\PythonPickle::class,
        wddx::class         => Adapter\Wddx::class,

        // v2 normalized FQCNs
        'zendserializeradapterigbinary'     => Adapter\IgBinary::class,
        'zendserializeradapterjson'         => Adapter\Json::class,
        'zendserializeradaptermsgpack'      => Adapter\MsgPack::class,
        'zendserializeradapterphpcode'      => Adapter\PhpCode::class,
        'zendserializeradapterphpserialize' => Adapter\PhpSerialize::class,
        'zendserializeradapterpythonpickle' => Adapter\PythonPickle::class,
        'zendserializeradapterwddx'         => Adapter\Wddx::class,
    ];

    /** @var array<string, string> */
    protected $factories = [
        Adapter\IgBinary::class     => InvokableFactory::class,
        Adapter\Json::class         => InvokableFactory::class,
        Adapter\MsgPack::class      => InvokableFactory::class,
        Adapter\PhpCode::class      => InvokableFactory::class,
        Adapter\PhpSerialize::class => InvokableFactory::class,
        Adapter\PythonPickle::class => InvokableFactory::class,
        Adapter\Wddx::class         => InvokableFactory::class,
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'laminasserializeradapterigbinary'     => InvokableFactory::class,
        'laminasserializeradapterjson'         => InvokableFactory::class,
        'laminasserializeradaptermsgpack'      => InvokableFactory::class,
        'laminasserializeradapterphpcode'      => InvokableFactory::class,
        'laminasserializeradapterphpserialize' => InvokableFactory::class,
        'laminasserializeradapterpythonpickle' => InvokableFactory::class,
        'laminasserializeradapterwddx'         => InvokableFactory::class,
    ];

    /** @var string */
    protected $instanceOf = Adapter\AdapterInterface::class;

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param mixed $instance
     * @throws InvalidServiceException
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                $this->instanceOf,
                is_object($instance) ? $instance::class : gettype($instance)
            ));
        }
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $instance
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
