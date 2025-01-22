<?php

namespace Ricotta\App\Tests\Unit;

use Ricotta\App\App;
use Ricotta\App\Module\HTTP\Routes;
use Ricotta\App\Module\HTTP\Routing\RouterException;
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
    getApp()->routes['/tests/{subpath}/id/*']->post(MockController::class);
    getApp()->routes['/bad']->get('NonExistentController');

    getApp()->routes['/put']->put(MockController::class);
    getApp()->routes['/delete']->delete(MockController::class);
    getApp()->routes['/patch']->patch(MockController::class);
    getApp()->routes['/options']->options(MockController::class);
    getApp()->routes['/head']->head(MockController::class);

    expect(isset(getApp()->routes['/']))->toBeTrue();
    expect()->getPage("/")->responseCode(200)->responseBody('Hello, World!');
    expect()->postPage("/")->responseCode(404);

    expect()->getPage("/tests/value/id/wildcard/1234")->responseCode(404);
    expect()->postPage("/tests/value/id/wildcard/1234")->responseCode(200)
        ->responseBody('Hello, World!, subpath: value, wildcard: wildcard/1234');

    expect()->request('PUT', '/put')->responseCode(200);
    expect()->request('DELETE', '/delete')->responseCode(200);
    expect()->request('PATCH', '/patch')->responseCode(200);
    expect()->request('OPTIONS', '/options')->responseCode(200);
    expect()->request('HEAD', '/head')->responseCode(200);

    expect()->getPage("/not-found")->responseCode(404);

    expect()->getPage("/bad")->responseCode(500);

    expect(fn() => getApp()->routes['dont/set/directly'] = 'test')
        ->toThrow(RouterException::class);
});

test('App modules', function () {
    getApp()->add(new MockModule());
    getApp()->run();

    expect()->getPage('/')->responseCode(404);
    // TODO test actual module registration
});

