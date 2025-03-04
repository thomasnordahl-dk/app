<?php

declare(strict_types=1);

use Ricotta\App\Demo\FrontpageController;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\App;

/**
 * @var App $app
 */
$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $engine) => $engine->addPackagePath('ricotta/app', __DIR__ . '/templates')
);

$app->routes['/']->get(FrontpageController::class);