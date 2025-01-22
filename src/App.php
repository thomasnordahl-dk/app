<?php

declare(strict_types=1);

namespace Ricotta\App;

use Ricotta\App\Module\HTTP\HHTPModule;
use Ricotta\App\Module\HTTP\Routes;
use Ricotta\App\Module\HTTP\Routing\Router;
use Ricotta\App\Module\HTTP\Server;
use Ricotta\App\Module\Module;
use Ricotta\Container\Bootstrapping;
use Ricotta\Container\Container;

class App
{
    private(set) Bootstrapping $bootstrap;

    private(set) Routes $routes;

    public function __construct()
    {
        $router = new Router();
        $this->routes = $router;

        $this->bootstrap = new Bootstrapping();

        $this->add(new HHTPModule($router));
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
