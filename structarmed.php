<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Exception', 'src/Exception')
    ->layer('Adapter', 'src/Adapter')
    ->layer('PluginManager', 'src/AdapterPluginManager.php')
    ->layer('Factory', [
        'src/AdapterPluginManagerFactory.php',
        'src/GenericSerializerFactory.php',
    ])
    ->layer('Config', 'src/ConfigProvider.php')
    ->ruleset([
        'Exception'     => [],
        'Adapter'       => ['Exception'],
        'PluginManager' => ['+Adapter'],
        'Factory'       => ['+PluginManager'],
        'Config'        => ['+Factory'],
    ]);
