<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Ricotta\App\Module\Web\Routing\RouteResult;
use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Result;
use Ricotta\App\Module\Web\Result\NotFoundResult;
use Ricotta\Container\Container;

/**
 * @internal
 */
class WebApp
{
    public function __construct(
        private readonly RouteResult $routeResult,
        private readonly Container $container,
        private readonly ErrorHandler $errorHandler,
    ) {
    }

    public function createResponse(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->routeResult->isFound === false) {
            $response = new NotFoundResult()->createResponse($this->container);
        } else {
            try {
                /** @var class-string<Controller> $controller */
                $controller = $this->routeResult?->route->controller ?? '';
                $response = $this->container
                    ->create($controller, [ServerRequestInterface::class => $request])
                    ->dispatch();

                $response = $response instanceof Result ? $response->createResponse($this->container) : $response;
            } catch (\Throwable $error) {
                return $this->errorHandler->handle($error);
            }
        }

        return $response;
    }
}
