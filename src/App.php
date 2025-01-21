<?php

declare(strict_types=1);

namespace Ricotta\App;

use Ricotta\App\Module\HTTP\HHTPModule;
use Ricotta\App\Module\HTTP\Routing\Routes;
use Ricotta\App\Module\HTTP\Server;
use Ricotta\Container\Bootstrapping;
use Ricotta\Container\Container;

class App
{
    public function __construct(
        private(set) readonly Bootstrapping $bootstrap = new Bootstrapping(),
        private(set) readonly Routes $routes = new Routes(),
    ) {
        $this->add(new HHTPModule($this->routes));
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
