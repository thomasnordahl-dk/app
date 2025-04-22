<?php

declare(strict_types=1);

use Ricotta\App\Demo\FrontpageController;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\App;
use Ricotta\App\Module\Web\Error\DebugErrorHandler;
use Ricotta\App\Module\Web\Error\ErrorHandler;

/**
 * @var App $app
 */
$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $engine) => $engine->addPackagePath('ricotta/app', __DIR__ . '/templates')
);

$app->bootstrap[ErrorHandler::class]->register()->type(DebugErrorHandler::class);

$app->routes['/']->get(FrontpageController::class);
