<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Ricotta\App\Module\Web\Middleware\WebApp;
use Ricotta\Container\Container;
use Ricotta\Container\Framework\Reference;

/**
 * @internal
 * 
 * Generate a PSR 7 response by passing it through the middleware stack to the web app.
 */
class RequestHandler implements RequestHandlerInterface
{
    private int $counter = 0;

    /**
     * @param list<MiddlewareInterface|Reference> $middlewares
     * @param Container                           $container
     * @param WebApp                              $webApp
     * @param CallbackHandlerFactory              $callbackHandlerFactory
     */
    public function __construct(
        private readonly array $middlewares,
        private readonly Container $container,
        private readonly WebApp $webApp,
        private readonly ErrorHandler $errorHandler,
        private readonly CallbackHandlerFactory $callbackHandlerFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->createReponse($request);
        } catch(\Throwable $error)
        {
            $response = $this->errorHandler->handle($error);
        }

        $this->counter = 0;

        return $response;
    }

    private function createReponse(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getNext();

        if ($middleware === null) {
            return $this->webApp->createResponse($request);
        }

        $handler = $this->callbackHandlerFactory->create($this->handle(...));

        return $middleware->process($request, $handler);
    }

    private function getNext(): ?MiddlewareInterface
    {
        $middleware = $this->middlewares[$this->counter] ?? null;
        $this->counter++;

        if ($middleware instanceof Reference) {
            $middleware = $middleware->resolve($this->container);
        }

        if ($middleware !== null && ! $middleware instanceof MiddlewareInterface) {
            throw new MiddlewareException("All middleware must implement " . MiddlewareInterface::class);
        }

        return $middleware;
    }
}
