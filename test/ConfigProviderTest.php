<?php

declare(strict_types=1);

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\ConfigProvider;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    private ConfigProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new ConfigProvider();
    }

    public function testWillProvideDefaultSerializer(): void
    {
        $dependencies = $this->provider->getDependencyConfig();
        $factories    = $dependencies['factories'] ?? [];
        self::assertArrayHasKey(AdapterInterface::class, $factories);
    }
}
