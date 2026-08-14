<?php

declare(strict_types=1);

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\AuthController;
use App\Controller\Admin\MenuController;
use App\Controller\Site\HomeController;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RequireAuthMiddleware;
use App\Service\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return static function (
    App $app,
    HomeController $homeController,
    DashboardController $dashboardController,
    AuthController $authController,
    MenuController $menuController,
    RequireAuthMiddleware $requireAuthMiddleware,
    CsrfMiddleware $csrfMiddleware,
    Auth $auth
): void {
    $app->get('/', $homeController);

    $app->get('/admin', static function (ServerRequestInterface $request, ResponseInterface $response) use ($auth, $authController, $dashboardController): ResponseInterface {
        if ($auth->check()) {
            return $dashboardController($request, $response);
        }

        return $authController->showLoginForm($request, $response);
    });
    $app->post('/admin', [$authController, 'login'])->add($csrfMiddleware);
    $app->post('/logout', [$authController, 'logout'])->add($csrfMiddleware);

    $app->get('/admin/menu', [$menuController, 'index'])->add($requireAuthMiddleware);
    $app->post('/admin/menu', [$menuController, 'update'])->add($requireAuthMiddleware)->add($csrfMiddleware);

    $app->get('/api/health', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'timestamp' => gmdate(DATE_ATOM),
        ], JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/menu', [$menuController, 'apiShow']);
};
