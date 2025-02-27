<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class CallbackHandler implements RequestHandlerInterface
{
    public function __construct(private Closure $callback)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        static $response;

        $response ??= ($this->callback)($request);

        return $response;
    }
}
