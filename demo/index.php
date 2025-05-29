<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ricotta\App\App;

$app = new App();

$app->loadModules();

$app->load(__DIR__ . DIRECTORY_SEPARATOR . "web.php");

$app->run();
