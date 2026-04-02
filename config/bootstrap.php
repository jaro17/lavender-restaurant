<?php

declare(strict_types=1);

$settings = require __DIR__ . '/settings.php';

require $settings['base_path'] . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

foreach ([$settings['cache_path'], dirname($settings['database_path'])] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

return $settings;
