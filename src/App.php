<?php

declare(strict_types=1);

namespace Ricotta\App;

use Ricotta\App\Module\Web\WebModule;
use Ricotta\App\Module\Web\Routes;
use Ricotta\App\Module\Web\Routing\Router;
use Ricotta\App\Module\Web\Server;
use Ricotta\App\Module\Module;
use Ricotta\Container\Bootstrapping;
use Ricotta\Container\Container;

class App
{
    public const string MIDDLEWARE_STACK = 'ricotta.app.middleware_stack';

    private(set) Bootstrapping $bootstrap;

    private(set) Routes $routes;

    public function __construct()
    {
        $router = new Router();
        $this->routes = $router;

        $this->bootstrap = new Bootstrapping();

        $this->add(new WebModule($router));
    }

    public function run(): void
    {
        $container = new Container($this->bootstrap);

        $container->call(fn(Server $server) => $server->dispatch());
    }

    public function add(Module $module): void
    {
        $module->register($this);
    }
}
