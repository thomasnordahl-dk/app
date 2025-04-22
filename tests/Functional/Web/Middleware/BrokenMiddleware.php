<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class BrokenMiddleware implements MiddlewareInterface
{
    public function __construct(private Throwable $error)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler->handle($request);

        throw $this->error;
    }
}
