<?php

/**
 * @see https://github.com/laminas/laminas-serializer for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Traversable;

use function extension_loaded;

class AdapterPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected static function getPluginManager(array $config = []): AbstractSingleInstancePluginManager
    {
        return new AdapterPluginManager(new ServiceManager(), $config);
    }

    protected function getInstanceOf(): string
    {
        return Adapter\AdapterInterface::class;
    }

    /**
     * Overrides CommonPluginManagerTrait::aliasProvider
     *
     * Iterates through aliases, and for adapters that require extensions,
     * tests if the extension is loaded, skipping that alias if not.
     *
     * @return Traversable
     */
    public static function aliasProvider(): iterable
    {
        $pluginManager = self::getPluginManager();
        $r             = new ReflectionProperty($pluginManager, 'aliases');
        $r->setAccessible(true);
        $aliases = $r->getValue($pluginManager);

        foreach ($aliases as $alias => $target) {
            switch ($target) {
                case Adapter\IgBinary::class:
                    if (extension_loaded('igbinary')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                case Adapter\MsgPack::class:
                    if (extension_loaded('msgpack')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                case Adapter\Wddx::class:
                    if (extension_loaded('wddx')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                default:
                    yield $alias => [$alias, $target];
            }
        }
    }
}
