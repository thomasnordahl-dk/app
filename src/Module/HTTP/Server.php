<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\HTTP\Routing\Routes;
use Ricotta\Container\Container;

class Server
{
    public function __construct(
        private readonly Container $container,
        private readonly EmitterInterface $emitter,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Routes $routes,
        private readonly ServerRequestInterface $request,
    ) {
    }

    public function dispatch(): void
    {
        $route = $this->routes->detect($this->request);

        if ($route === null) {
            $response = $this->responseFactory->createResponse(404);
            $response->getBody()->write('Not Found');
        } else {
            try {
                $response = $this->container->get($route->controller)->dispatch();
            } catch (\Throwable) {
                $response = $this->responseFactory->createResponse(500);
                $response->getBody()->write('Internal Server Error');
            }
        }

        $this->emitter->emit($response);
    }
}
