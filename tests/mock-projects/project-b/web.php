<?php

declare(strict_types=1);

use Mock\ProjectB\ControllerB;
use Ricotta\App\App;

/** @var App $app */

$app->routes['/project-b']->get(ControllerB::class);
