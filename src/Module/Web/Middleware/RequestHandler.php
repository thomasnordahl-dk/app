<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Routing\RouteResult;
use Ricotta\Container\Container;
use Ricotta\Container\Framework\Reference;

/**
 * Generate a PSR 7 response by passing it through the middleware stack to the web app.
 */
class RequestHandler implements RequestHandlerInterface
{
    private int $counter = 0;

    /**
     * @param list<MiddlewareInterface|Reference> $middlewares
     * @param RouteResult                         $routeResult
     * @param Container                           $container
     * @param ResponseFactoryInterface            $responseFactory
     * @param StreamFactoryInterface              $streamFactory
     */
    public function __construct(
        private readonly array $middlewares,
        private readonly RouteResult $routeResult,
        private readonly ContainerInterface $container,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly CallbackHandlerFactory $callbackHandlerFactory,
        private readonly TemplateEngine $templateEngine,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewares[$this->counter] ?? null;
        $this->counter++;

        if ($middleware instanceof Reference) {
            $middleware = $middleware->resolve($this->container);
        }

        if ($middleware instanceof MiddlewareInterface) {
            $callbackHandler = $this->callbackHandlerFactory->create($this->handle(...));

            $response = $middleware->process($request, $callbackHandler);
        } else {
            $response = $this->createResponse($request);
        }

        return $response;
    }

    private function createResponse(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->routeResult->isFound === false) {
            $notFoundPage = $this->templateEngine->render('not-found', 'ricotta/app');
            $response = $this->responseFactory->createResponse(404);
            $response->getBody()->write($notFoundPage);
        } else {
            try {
                /** @var class-string<Controller> $controller */
                $controller = $this->routeResult?->route->controller ?? '';
                $response = $this->container
                    ->create($controller, [ServerRequestInterface::class => $request])
                    ->dispatch();
            } catch (\Throwable $error) {
                $errorPage = $this->templateEngine->render('error-page', 'ricotta/app');
                $message = $this->streamFactory->createStream($errorPage);
                $response = $this->responseFactory->createResponse(500)->withBody($message);
            }
        }

        return $response;
    }
}
