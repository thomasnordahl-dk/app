<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Console;

use Psr\Container\ContainerInterface;
use Ricotta\App\App;
use Ricotta\App\Module\Module;

class ConsoleModule implements Module
{
    public function register(App $app): void
    {
        $app->bootstrap[Console::class]->register()
            ->callback(fn(ContainerInterface $container) => new Console(fn($class) => $container->get($class)));
    }
}
