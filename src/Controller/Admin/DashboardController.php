<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\Auth;
use Slim\Views\Twig;

final class DashboardController
{
    public function __construct(
        private Twig $twig,
        private PDO $pdo,
        private Auth $auth
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $statement = $this->pdo->query('SELECT site_name, updated_at FROM app_settings WHERE id = 1');
        $settings = $statement->fetch() ?: [
            'site_name' => 'Lavender Restaurant',
            'updated_at' => null,
        ];

        return $this->twig->render($response, 'admin/dashboard.twig', [
            'pageTitle' => 'Admin Dashboard',
            'siteName' => $settings['site_name'],
            'settingsUpdatedAt' => $settings['updated_at'],
            'currentUser' => $this->auth->user(),
        ]);
    }
}
