<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\ServiceManager\ServiceManager;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return dependency mappings for this component.
     *
     * @return ServiceManagerConfiguration
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                'SerializerAdapterManager'  => AdapterPluginManagerFactory::class,
                AdapterPluginManager::class => AdapterPluginManagerFactory::class,
                AdapterInterface::class     => new GenericSerializerFactory(PhpSerialize::class),
            ],
        ];
    }
}
