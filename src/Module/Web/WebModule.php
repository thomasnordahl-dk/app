<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web;

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
use Ricotta\App\Module\Web\Middleware\CallbackHandlerFactory;
use Ricotta\App\Module\Web\Middleware\RequestHandler;
use Ricotta\App\Module\Web\Routing\Router;
use Ricotta\App\Module\Web\Routing\RouteResult;
use Ricotta\App\Module\Module;
use Ricotta\App\Module\Template\TemplateEngine;

/**
 * @internal
 */
readonly class WebModule implements Module
{
    public function __construct(private Router $routes)
    {
    }

    public function register(App $app): void
    {
        $app->bootstrap[TemplateEngine::class]->configure(
            fn (TemplateEngine $engine) => $engine->addPackagePath('ricotta/app', dirname(__DIR__, 3) . '/templates')
        );

        $app->bootstrap[App::MIDDLEWARE_STACK]->register()->value([]);
        $app->bootstrap[RequestHandler::class]->register()
            ->arguments(['middlewares' => $app->bootstrap[App::MIDDLEWARE_STACK]->reference()]);

        $app->bootstrap[CallbackHandlerFactory::class]->register();

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
