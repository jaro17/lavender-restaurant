<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\Auth;
use App\Service\WeeklyMenu;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class MenuController
{
    public function __construct(
        private Twig $twig,
        private WeeklyMenu $weeklyMenu,
        private Auth $auth
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $menu = $this->weeklyMenu->get();
        $flash = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return $this->twig->render($response, 'admin/menu.twig', [
            'pageTitle' => 'Týždenné menu',
            'currentUser' => $this->auth->user(),
            'menu' => $menu,
            'flashSuccess' => $flash,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $days = (array) ($data['days'] ?? []);
        $this->weeklyMenu->save($days);

        $_SESSION['flash_success'] = 'Týždenné menu bolo uložené.';

        return $response
            ->withHeader('Location', '/admin/menu')
            ->withStatus(302);
    }

    public function apiShow(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $menu = $this->weeklyMenu->get();

        $response->getBody()->write(json_encode([
            'week_label' => $menu['week_label'],
            'days' => $menu['days'],
            'updated_at' => $menu['updated_at'],
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
