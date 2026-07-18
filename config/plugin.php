<?php

return [
    'discovery' => [
        'paths' => [
            base_path('app/Plugins'),
        ],
        'scan_composer' => true,
        'composer_type' => 'nexus-plugin',
    ],

    'install' => [
        'base_path' => base_path('app/Plugins'),
    ],

    'assets' => [
        'public_path' => public_path('plugins'),
    ],

    'migration' => [
        'prefix' => 'plugin_',
    ],

    'lifecycle' => [
        'rollback_on_failure' => true,
        'backup_before_upgrade' => true,
    ],

    'filament' => [
        'auto_register' => true,
        'navigation_group' => 'Plugins',
        'navigation_group_icon' => 'heroicon-o-puzzle-piece',
        'navigation_group_sort' => 900,
    ],
];
