<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ricotta\App\App;
use Ricotta\App\Demo\FrontpageController;
use Ricotta\App\Module\Template\TemplateEngine;

$app = new App();

$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $engine) => $engine->addPackagePath('ricotta/app', __DIR__ . '/templates')
);

$app->routes['/']->get(FrontpageController::class);

$app->run();
