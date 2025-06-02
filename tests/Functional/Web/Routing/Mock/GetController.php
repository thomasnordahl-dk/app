<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Routing\Mock;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Routing\RouteResult;

class GetController implements Controller
{
    public string $message = 'Hello, World!';

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly RouteResult $routeResult,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $message = $this->message;

        if ($this->routeResult->route === null) {
            throw new \RuntimeException('Route result route not found');
        }

        foreach ($this->routeResult->route->parameters as $name => $value) {
            $message .= ', ' . $name . ': ' . $value;
        }

        if ($this->routeResult->route->wildcard !== null) {
            $message .= ', wildcard: ' . $this->routeResult->route->wildcard;
        }

        $stream = $this->streamFactory->createStream($message);

        return $this->responseFactory->createResponse(200)->withBody($stream);
    }
}
