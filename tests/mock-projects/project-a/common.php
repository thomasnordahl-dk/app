<?php

declare(strict_types=1);

use Mock\ProjectA\MockService;
use Ricotta\App\App;

/** @var App $app */

$app->bootstrap[MockService::class]->register();
