<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Serializer;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for serializer adapters.
 *
 * Enforces that adapters retrieved are instances of
 * Adapter\AdapterInterface. Additionally, it registers a number of default
 * adapters available.
 */
class AdapterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of adapters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'igbinary'     => 'Laminas\Serializer\Adapter\IgBinary',
        'json'         => 'Laminas\Serializer\Adapter\Json',
        'msgpack'      => 'Laminas\Serializer\Adapter\MsgPack',
        'phpcode'      => 'Laminas\Serializer\Adapter\PhpCode',
        'phpserialize' => 'Laminas\Serializer\Adapter\PhpSerialize',
        'pythonpickle' => 'Laminas\Serializer\Adapter\PythonPickle',
        'wddx'         => 'Laminas\Serializer\Adapter\Wddx',
    );

    /**
     * Validate the plugin
     *
     * Checks that the adapter loaded is an instance
     * of Adapter\AdapterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Adapter\AdapterInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Adapter\AdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
