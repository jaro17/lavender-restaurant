<?php

declare(strict_types=1);

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\AuthController;
use App\Controller\Admin\MenuController;
use App\Controller\Site\HomeController;
use App\Infrastructure\Database;
use App\Middleware\RequireAuthMiddleware;
use App\Service\Auth;
use App\Service\WeeklyMenu;
use Slim\Factory\AppFactory;
use Slim\App;
use Slim\Views\Twig;

return static function (array $settings): App {
    $app = AppFactory::create();
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $debug = (bool) ($settings['debug'] ?? false);
    $app->addErrorMiddleware($debug, true, true);

    $pdo = Database::connect($settings['database_path']);
    $twig = Twig::create($settings['templates_path'], [
        'cache' => $settings['cache_path'],
        'auto_reload' => true,
    ]);

    $auth = new Auth($pdo);
    $weeklyMenu = new WeeklyMenu($pdo);
    $homeController = new HomeController($settings['frontend_index_path'], $weeklyMenu);
    $dashboardController = new DashboardController($twig, $pdo, $auth);
    $authController = new AuthController($twig, $auth);
    $menuController = new MenuController($twig, $weeklyMenu, $auth);
    $requireAuthMiddleware = new RequireAuthMiddleware($auth);
    $registerRoutes = require $settings['base_path'] . '/routes/web.php';
    $registerRoutes($app, $homeController, $dashboardController, $authController, $menuController, $requireAuthMiddleware, $auth);

    return $app;
};
