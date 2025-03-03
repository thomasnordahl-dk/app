<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MockMiddleware implements MiddlewareInterface
{
    public const string KEY   = 'mock-middleware';
    public const string VALUE = 'mock value';

    public function __construct(private readonly string $prefix = '')
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $request = $request->withAttribute($this->prefix . self::KEY, $this->prefix . self::VALUE);

        $handler->handle($request);
        // Make sure the handler can be called twice without breaking flow
        $response = $handler->handle($request);

        return $response->withHeader($this->prefix . self::KEY, $this->prefix . self::VALUE);
    }
}
