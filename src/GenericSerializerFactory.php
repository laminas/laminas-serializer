<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class GenericSerializerFactory
{
    /**
     * @param class-string<AdapterInterface> $serializerName
     * @param array<string,mixed>|null       $options
     */
    public function __construct(private readonly string $serializerName, private readonly array|null $options = null)
    {
    }

    public function __invoke(ContainerInterface $container): AdapterInterface
    {
        $plugins = $container->get(AdapterPluginManager::class);
        assert($plugins instanceof AdapterPluginManager);

        return $plugins->build($this->serializerName, $this->options);
    }
}
