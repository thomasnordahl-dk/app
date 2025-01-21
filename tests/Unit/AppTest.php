<?php

namespace Ricotta\App\Tests\Unit;

use Ricotta\App\App;
use Ricotta\App\Routing\Routes;
use Ricotta\App\Tests\Unit\Mock\MockController;
use Ricotta\App\Tests\Unit\Mock\MockModule;
use Ricotta\Container\Bootstrapping;

use function Ricotta\App\Tests\Support\getApp;
use function Ricotta\App\Tests\Support\resetApp;

beforeEach(fn() => resetApp());

test('App bootstrapping', function () {
    expect(getApp())
        ->toBeInstanceOf(App::class)
        ->and(getApp()->bootstrap)
        ->toBeInstanceOf(Bootstrapping::class)
        ->and(getApp()->routes)
        ->toBeInstanceOf(Routes::class);
});

test('App with routing', function () {
    getApp()->routes['/']->get(MockController::class);
    getApp()->routes['/tests']->get(MockController::class);
    getApp()->routes['/bad']->get('NonExistentController');

    expect()->toGoToPage("/")->responseCode(200)->responseBody('Hello, World!');
    expect()->toGoToPage("/tests")->responseCode(200)->responseBody('Hello, World!');
    expect()->toGoToPage("/not-found")->responseCode(404);
    expect()->toGoToPage("/bad")->responseCode(500);

    // TODO test dynamic route resolution
});

test('App modules', function () {
    getApp()->add(new MockModule());
    getApp()->run();

    expect()->toGoToPage('/')->responseCode(404);
    // TODO test actual module registration
});

