<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\PhpSerialize;

class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return dependency mappings for this component.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            // Legacy Zend Framework aliases
            'aliases'   => [],
            'factories' => [
                'SerializerAdapterManager' => AdapterPluginManagerFactory::class,
                AdapterInterface::class    => new GenericSerializerFactory(PhpSerialize::class),
            ],
        ];
    }
}
