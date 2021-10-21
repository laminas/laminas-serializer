<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\ModuleManager\ModuleManager;

class Module
{
    /**
     * Return default laminas-serializer configuration for laminas-mvc applications.
     *
     * @return array{service_manager: mixed}
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }

    /**
     * Register a specification for the SerializerAdapterManager with the ServiceListener.
     */
    public function init(ModuleManager $moduleManager): void
    {
        $event           = $moduleManager->getEvent();
        $container       = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'SerializerAdapterManager',
            'serializers',
            'Laminas\ModuleManager\Feature\SerializerProviderInterface',
            'getSerializerConfig'
        );
    }
}
