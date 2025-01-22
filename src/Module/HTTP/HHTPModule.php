<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP;

use HttpSoft\Emitter\EmitterInterface;
use HttpSoft\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Ricotta\App\App;
use Ricotta\App\Module\HTTP\Routing\Router;
use Ricotta\App\Module\HTTP\Routing\RouteResult;
use Ricotta\App\Module\Module;

/**
 * @internal
 */
readonly class HHTPModule implements Module
{
    public function __construct(private Router $routes)
    {
    }

    public function register(App $app): void
    {
        $app->bootstrap[Router::class]->register()->value($this->routes);
        $app->bootstrap[RouteResult::class]->register()
            ->callback(function (Router $routes, ServerRequestInterface $request) {
                $route = $routes->detect($request);

                return new RouteResult($route !== null, $route);
            });

        $app->bootstrap->allowAutowiring(Controller::class);

        $app->bootstrap[Server::class]->register();

        $app->bootstrap[EmitterInterface::class]->register()->type(SapiEmitter::class);

        $app->bootstrap[Psr17Factory::class]->register();
        $app->bootstrap[RequestFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[ResponseFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[ServerRequestFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[StreamFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[UploadedFileFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[UriFactoryInterface::class]->register()
            ->callback(fn(Psr17Factory $factory) => $factory);

        $app->bootstrap[ServerRequestCreator::class]->register();

        $app->bootstrap[ServerRequestInterface::class]->register()
            ->callback(fn(ServerRequestCreator $creator) => $creator->fromGlobals());
    }
}
