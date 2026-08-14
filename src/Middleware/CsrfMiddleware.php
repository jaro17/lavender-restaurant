<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class CsrfMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $body = (array) $request->getParsedBody();
            $token = (string) ($body['_csrf'] ?? '');
            $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');

            if ($sessionToken === '' || !hash_equals($sessionToken, $token)) {
                $response = new Response();
                $response->getBody()->write('Neplatná požiadavka. Obnovte stránku a skúste znova.');

                return $response->withStatus(403);
            }
        }

        return $handler->handle($request);
    }
}
