<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function assert;
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

        $config = $container->has('config') ? $container->get('config') : [];
        assert(is_array($config));

        /** @psalm-var ServiceManagerConfiguration $serializers */
        $serializers = isset($config['serializers']) && is_array($config['serializers']) ? $config['serializers'] : [];

        /** @psalm-var ServiceManagerConfiguration $mergedConfig */
        $mergedConfig = array_replace_recursive($serializers, $options);

        return new AdapterPluginManager($container, $mergedConfig);
    }
}
