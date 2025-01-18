<?php

declare(strict_types=1);

namespace Ricotta\App;

use Ricotta\App\Routing\Routes;
use Ricotta\Container\Bootstrapping;

class App
{
    public function __construct(
        private(set) readonly Bootstrapping $bootstrap = new Bootstrapping(),
        private(set) readonly Routes $routes = new Routes(),
    ) {
    }

    public function add(Module $module): void
    {
        $module->register($this);
    }
}
