<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class RequireAuthMiddleware
{
    public function __construct(private Auth $auth)
    {
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->auth->check()) {
            return $handler->handle($request);
        }

        return (new Response())
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }
}
