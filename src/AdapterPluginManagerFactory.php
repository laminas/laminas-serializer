<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
final class AdapterPluginManagerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): AdapterPluginManager {
        $options ??= [];
        /** @psalm-var ServiceManagerConfiguration $options */
        $pluginManager = new AdapterPluginManager($container, $options);

        // If this is in a laminas-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        // If we do not have a config service, nothing more to do
        if (! $container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        if (! is_array($config)) {
            return $pluginManager;
        }

        $serializers = $config['serializers'] ?? null;

        // If we do not have serializers configuration, nothing more to do
        if (! is_array($serializers)) {
            return $pluginManager;
        }

        /** @psalm-var ServiceManagerConfiguration $serializers */

        $pluginManager->configure($serializers);

        return $pluginManager;
    }
}
