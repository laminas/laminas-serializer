<?php

declare(strict_types=1);

namespace Laminas\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\ServiceManager\ServiceManager;

use function is_array;
use function is_string;
use function iterator_to_array;

// phpcs:ignore
final class Serializer
{
    /**
     * Plugin manager for loading adapters
     */
    protected static null|AdapterPluginManager $adapters = null;

    /**
     * The default adapter.
     */
    protected static string|AdapterInterface $defaultAdapter = 'PhpSerialize';

    /**
     * Create a serializer adapter instance.
     *
     * @param  string|AdapterInterface $adapterName Name of the adapter class
     * @param iterable|null $adapterOptions Serializer options
     */
    public static function factory(
        string|AdapterInterface $adapterName,
        iterable|null $adapterOptions = null
    ): AdapterInterface {
        if ($adapterName instanceof AdapterInterface) {
            return $adapterName; // $adapterName is already an adapter object
        }

        if ($adapterOptions !== null && ! is_array($adapterOptions)) {
            $adapterOptions = iterator_to_array($adapterOptions);
        }

        return self::getAdapterPluginManager()->build($adapterName, $adapterOptions);
    }

    /**
     * Change the adapter plugin manager
     */
    public static function setAdapterPluginManager(AdapterPluginManager $adapters): void
    {
        self::$adapters = $adapters;
    }

    /**
     * Get the adapter plugin manager
     */
    public static function getAdapterPluginManager(): AdapterPluginManager
    {
        if (self::$adapters === null) {
            self::$adapters = new AdapterPluginManager(new ServiceManager());
        }
        return self::$adapters;
    }

    /**
     * Resets the internal adapter plugin manager
     */
    public static function resetAdapterPluginManager(): AdapterPluginManager
    {
        self::$adapters = new AdapterPluginManager(new ServiceManager());
        return self::$adapters;
    }

    /**
     * Change the default adapter.
     *
     * @psalm-assert AdapterInterface self::$defaultAdapter
     */
    public static function setDefaultAdapter(string|AdapterInterface $adapter, iterable|null $adapterOptions = null): void
    {
        self::$defaultAdapter = self::factory($adapter, $adapterOptions);
    }

    /**
     * Get the default adapter.
     */
    public static function getDefaultAdapter(): AdapterInterface
    {
        if (! self::$defaultAdapter instanceof AdapterInterface) {
            self::setDefaultAdapter(self::$defaultAdapter);
        }
        return self::$defaultAdapter;
    }

    /**
     * Generates a storable representation of a value using the default adapter.
     * Optionally different adapter could be provided as second argument
     *
     * @param iterable|null $adapterOptions Adapter constructor options
     * only used to create adapter instance
     */
    public static function serialize(
        mixed $value,
        string|AdapterInterface|null $adapter = null,
        iterable|null $adapterOptions = null
    ): string {
        $adapter ??= self::getDefaultAdapter();

        if (is_string($adapter)) {
            $adapter = self::factory($adapter, $adapterOptions);
        }

        return $adapter->serialize($value);
    }

    /**
     * Creates a PHP value from a stored representation using the default adapter.
     * Optionally different adapter could be provided as second argument
     *
     * @param iterable|null $adapterOptions Adapter constructor options
     * only used to create adapter instance
     */
    public static function unserialize(
        string $serialized,
        string|AdapterInterface|null $adapter = null,
        iterable|null $adapterOptions = null
    ): mixed {
        $adapter ??= self::getDefaultAdapter();

        if (is_string($adapter)) {
            $adapter = self::factory($adapter, $adapterOptions);
        }

        return $adapter->unserialize($serialized);
    }
}
