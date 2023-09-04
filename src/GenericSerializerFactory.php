<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Psr\Container\ContainerInterface;

final class GenericSerializerFactory
{
    /**
     * @param class-string<AdapterInterface> $serializerName
     * @param array<string,mixed>|null       $options
     */
    public function __construct(private string $serializerName, private array|null $options = null)
    {
    }

    public function __invoke(ContainerInterface $container): AdapterInterface
    {
        $plugins = $container->get(AdapterPluginManager::class);

        return $plugins->build($this->serializerName, $this->options);
    }
}
