<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class AuthController
{
    public function __construct(
        private Twig $twig,
        private Auth $auth
    ) {
    }

    public function showLoginForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->auth->check()) {
            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        $flash = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        return $this->twig->render($response, 'admin/login.twig', [
            'pageTitle' => 'Prihlásenie do administrácie',
            'flashError' => $flash,
            'oldUsername' => $_SESSION['old_username'] ?? '',
            'currentUser' => null,
        ]);
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $username = trim((string) ($data['username'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        $_SESSION['old_username'] = $username;

        if ($username === '' || $password === '') {
            $_SESSION['flash_error'] = 'Zadajte meno a heslo.';

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        if (!$this->auth->attempt($username, $password)) {
            $_SESSION['flash_error'] = 'Neplatné prihlasovacie údaje.';

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        unset($_SESSION['flash_error'], $_SESSION['old_username']);

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->auth->logout();

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }
}
