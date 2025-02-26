<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Modules\Mock;

use Ricotta\App\App;
use Ricotta\App\Module\Module;

class MockModule implements Module
{
    public function register(App $app): void
    {
        $app->bootstrap[Service::class]->register();
        $app->routes['/']->get(MockController::class);
    }
}
