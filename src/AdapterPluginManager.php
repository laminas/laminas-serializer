<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;

/**
 * Plugin manager implementation for serializer adapters.
 *
 * Enforces that adapters retrieved are instances of
 * Adapter\AdapterInterface. Additionally, it registers a number of default
 * adapters available.
 *
 * @template-extends AbstractSingleInstancePluginManager<Adapter\AdapterInterface>
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
final class AdapterPluginManager extends AbstractSingleInstancePluginManager
{
    private const CONFIGURATION = [
        'aliases'   => [
            'igbinary'     => Adapter\IgBinary::class,
            'igBinary'     => Adapter\IgBinary::class,
            'IgBinary'     => Adapter\IgBinary::class,
            'json'         => Adapter\Json::class,
            'Json'         => Adapter\Json::class,
            'phpcode'      => Adapter\PhpCode::class,
            'phpCode'      => Adapter\PhpCode::class,
            'PhpCode'      => Adapter\PhpCode::class,
            'phpserialize' => Adapter\PhpSerialize::class,
            'phpSerialize' => Adapter\PhpSerialize::class,
            'PhpSerialize' => Adapter\PhpSerialize::class,
        ],
        'factories' => [
            Adapter\IgBinary::class     => InvokableFactory::class,
            Adapter\Json::class         => InvokableFactory::class,
            Adapter\PhpCode::class      => InvokableFactory::class,
            Adapter\PhpSerialize::class => InvokableFactory::class,
        ],
    ];

    /**
     * @param ServiceManagerConfiguration $config
     */
    public function __construct(ContainerInterface $creationContext, array $config = [])
    {
        $this->instanceOf = Adapter\AdapterInterface::class;
        /** @var ServiceManagerConfiguration $config */
        $config = array_replace_recursive(self::CONFIGURATION, $config);
        parent::__construct($creationContext, $config);
    }
}
