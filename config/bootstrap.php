<?php

declare(strict_types=1);

header_remove('X-Powered-By');

$settings = require __DIR__ . '/settings.php';

require $settings['base_path'] . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

foreach ([$settings['cache_path'], dirname($settings['database_path'])] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

return $settings;
