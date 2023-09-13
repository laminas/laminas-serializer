<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\PhpSerialize;
use Psr\Container\ContainerInterface;

/**
 * @see ContainerInterface
 */
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
     * @return array{
     *     factories: array<
     *      string,
     *      callable(ContainerInterface,?string,?array<mixed>|null):object
     *     >,
     *     aliases: array<never,never>
     * }
     */
    // @phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
    public function getDependencyConfig()
    {
        return [
            // Legacy Zend Framework aliases
            'aliases'   => [],
            'factories' => [
                'SerializerAdapterManager'  => AdapterPluginManagerFactory::class,
                AdapterPluginManager::class => AdapterPluginManagerFactory::class,
                AdapterInterface::class     => new GenericSerializerFactory(PhpSerialize::class),
            ],
        ];
    }
}
