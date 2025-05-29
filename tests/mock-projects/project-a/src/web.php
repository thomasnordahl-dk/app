<?php

declare(strict_types=1);

use Mock\ProjectA\ControllerA;
use Ricotta\App\App;

/** @var App $app */

$app->routes['/project-a']->get(ControllerA::class);
