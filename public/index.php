<?php

declare(strict_types=1);

$settings = require __DIR__ . '/../config/bootstrap.php';
$createApp = require __DIR__ . '/../config/app.php';
$app = $createApp($settings);

$app->run();
