<?php

declare(strict_types=1);

use Ricotta\App\Tests\Functional\Modules\Mock\MockController;
use Ricotta\App\Tests\Functional\Modules\Mock\Service;

/**
 * @var Ricotta\App\App $app
 */

 $app->bootstrap[Service::class]->register();
 $app->routes['/']->get(MockController::class);
