<?php

declare(strict_types=1);

use Mock\ProjectA\ControllerA;
use Mock\ProjectA\TestCommand;
use Ricotta\App\App;
use Ricotta\App\Module\Console\Console;

/** @var App $app */

$app->bootstrap[TestCommand::class]->register();

$app->bootstrap[Console::class]->configure(fn (Console $console) => $console->register(TestCommand::class));
