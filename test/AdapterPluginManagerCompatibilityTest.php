<?php

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\TestCase;

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
}
