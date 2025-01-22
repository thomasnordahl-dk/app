<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Unit\Mock;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\HTTP\Controller;
use Ricotta\App\Module\HTTP\Routing\Route;
use Ricotta\App\Module\HTTP\Routing\RouteResult;

class MockController implements Controller
{
    public string $message = 'Hello, World!';

    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly StreamFactoryInterface $stream_factory,
        private readonly RouteResult $route_result,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $message = $this->message;

        foreach ($this->route_result->route->parameters as $name => $value) {
            $message .= ', ' . $name . ': ' . $value;
        }

        if ($this->route_result->route->wildcard !== null) {
            $message .= ', wildcard: ' . $this->route_result->route->wildcard;
        }

        $stream = $this->stream_factory->createStream($message);

        return $this->response_factory->createResponse(200)->withBody($stream);
    }
}
