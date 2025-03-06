<?php

declare(strict_types=1);

namespace Ricotta\App;

use InvalidArgumentException;
use Ricotta\App\Module\Web\WebModule;
use Ricotta\App\Module\Web\Routes;
use Ricotta\App\Module\Web\Routing\Router;
use Ricotta\App\Module\Web\Server;
use Ricotta\App\Module\Module;
use Ricotta\App\Module\Template\TemplateModule;
use Ricotta\Container\Bootstrapping;
use Ricotta\Container\Container;

class App
{
    public const string MIDDLEWARE_STACK = 'ricotta.app.middleware_stack';

    public private(set) Bootstrapping $bootstrap;

    public private(set) Routes $routes;

    public function __construct()
    {
        $router = new Router();
        $this->routes = $router;

        $this->bootstrap = new Bootstrapping();

        $this->add(new WebModule($router));
        $this->add(new TemplateModule());
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

    /**
     * @throws InvalidArgumentException
     */
    public function load(string $path): void
    {
        if (! file_exists($path) || substr($path, -3) !== 'php') {
            throw new InvalidArgumentException("{$path} is not a PHP file");
        }

        $app = $this;

        include $path;
    }
}
