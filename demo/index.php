<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ricotta\App\App;
use Ricotta\App\Demo\FrontpageController;

$app = new App();

$app->routes['/']->get(FrontpageController::class);

$app->run();
