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
        $app->bootstrap[ClimateFactory::class]->register();
        $app->bootstrap[Console::class]->register()
            ->callback(function (ContainerInterface $container, ClimateFactory $factory) {
                return new Console($factory, fn ($class) => $container->get($class));
            });
    }
}
