<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

$envFile = $basePath . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

return [
    'base_path' => $basePath,
    'templates_path' => $basePath . '/templates',
    'cache_path' => $basePath . '/var/cache/twig',
    'database_path' => $basePath . '/var/data/database.sqlite',
    'frontend_index_path' => $basePath . '/public/index.html',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),
    'default_admin_username' => $_ENV['ADMIN_USERNAME'] ?? 'admin',
    'default_admin_password' => $_ENV['ADMIN_PASSWORD'] ?? '',
];
