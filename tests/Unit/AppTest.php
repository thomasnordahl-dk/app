<?php

namespace Ricotta\App\Tests\Unit;

use Ricotta\App\App;
use Ricotta\App\Routing\Routes;
use Ricotta\App\Tests\Mock\MockController;
use Ricotta\App\Tests\Unit\Mock\MockModule;
use Ricotta\Container\Bootstrapping;

test('App bootstrapping', function () {
    $app = new App();

    expect($app)
        ->toBeInstanceOf(App::class)
        ->and($app->bootstrap)
        ->toBeInstanceOf(Bootstrapping::class)
        ->and($app->routes)
        ->toBeInstanceOf(Routes::class);
});

test('App with routing', function () {
    $app = new App();

    $app->routes['/']->get(MockController::class);

    // TODO assert routing
});

test('App modules', function () {
    $app = new App();

    $app->add(new MockModule());

    // TODO assert module registration
});

