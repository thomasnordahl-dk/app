<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ricotta\App\App;

$app = new App();

$app->load(__DIR__ . "/bootstrapping.php");

$app->run();
