<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

return [
    'base_path' => $basePath,
    'templates_path' => $basePath . '/templates',
    'cache_path' => $basePath . '/var/cache/twig',
    'database_path' => $basePath . '/var/data/database.sqlite',
    'frontend_index_path' => $basePath . '/public/index.html',
    'default_admin_username' => 'admin',
    'default_admin_password' => 'admin123',
];
