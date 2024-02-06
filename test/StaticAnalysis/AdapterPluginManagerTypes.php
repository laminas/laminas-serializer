<?php

declare(strict_types=1);

namespace LaminasTest\Serializer\StaticAnalysis;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\AdapterPluginManager;

/**
 * @psalm-api Avoid class being detected as unused class
 */
final class AdapterPluginManagerTypes
{
    public function willReturnAnAdapterUsingFQCN(AdapterPluginManager $manager): AdapterInterface
    {
        return $manager->get(Json::class);
    }

    public function validateWillAssertInstanceType(AdapterPluginManager $manager, object $instance): AdapterInterface
    {
        $manager->validate($instance);

        return $instance;
    }

    public function buildWillReturnAdapterUsingFQCN(AdapterPluginManager $manager): AdapterInterface
    {
        return $manager->build(Json::class);
    }

    public function buildWillReturnRequestedFQCNAdapter(AdapterPluginManager $manager): Json
    {
        return $manager->build(Json::class);
    }

    public function getWillReturnRequestedFQCNAdapter(AdapterPluginManager $manager): Json
    {
        return $manager->get(Json::class);
    }
}
