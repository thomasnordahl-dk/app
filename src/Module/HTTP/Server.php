<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Ricotta\App\Module\HTTP\Routing\RouteResult;
use Ricotta\Container\Container;

class Server
{
    public function __construct(
        private readonly Container $container,
        private readonly EmitterInterface $emitter,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RouteResult $routeResult
    ) {

    }

    public function dispatch(): void
    {
        if ($this->routeResult->isFound === false) {
            $response = $this->responseFactory->createResponse(404);
            $response->getBody()->write('Not Found');
        } else {
            try {
                /** @var class-string<Controller> $controller */
                $controller = $this->routeResult?->route->controller ?? '';
                $response = $this->container->get($controller)->dispatch();
            } catch (\Throwable) {
                $response = $this->responseFactory->createResponse(500);
                $response->getBody()->write('Internal Server Error');
            }
        }

        $this->emitter->emit($response);
    }
}
