<?php

declare(strict_types=1);

use Ricotta\App\Module\HTTP\Routing\RouterException;
use Ricotta\App\Tests\Unit\Routing\Mock\DeleteController;
use Ricotta\App\Tests\Unit\Routing\Mock\GetController;
use Ricotta\App\Tests\Unit\Routing\Mock\HeadController;
use Ricotta\App\Tests\Unit\Routing\Mock\OptionsController;
use Ricotta\App\Tests\Unit\Routing\Mock\PatchController;
use Ricotta\App\Tests\Unit\Routing\Mock\PostController;
use Ricotta\App\Tests\Unit\Routing\Mock\PutController;

use function Ricotta\App\Tests\Support\getApp;
use function Ricotta\App\Tests\Support\resetApp;

beforeEach(fn() => resetApp());

test('App with routing', function () {
    getApp()->routes['/']->get(GetController::class);
    getApp()->routes['/tests/{subpath}/{name}/*']->post(PostController::class);
    getApp()->routes['/bad']->get('NonExistentController');

    getApp()->routes['/put']->put(PutController::class);
    getApp()->routes['/delete']->delete(DeleteController::class);
    getApp()->routes['/patch']->patch(PatchController::class);
    getApp()->routes['/options']->options(OptionsController::class);
    getApp()->routes['/head']->head(HeadController::class);

    expect(isset(getApp()->routes['/']))->toBeTrue();
    expect()->getPage("/")->responseCode(200)->responseBody('Hello, World!');
    expect()->getPage("")->responseCode(200)->responseBody('Hello, World!');
    expect()->postPage("/")->responseCode(404);

    expect()->getPage("/tests/value/id/wildcard/1234")->responseCode(404);
    expect()->postPage("/tests/value/id/wildcard/1234")->responseCode(200)
        ->responseBody('Hello, Post!, subpath: value, name: id, wildcard: wildcard/1234');

    expect()->request('PUT', '/put')->responseCode(200)->responseBody('Hello, Put!');
    expect()->request('PUT', '/put/')->responseCode(200)->responseBody('Hello, Put!');
    expect()->request('DELETE', '/delete')->responseCode(200)->responseBody('Hello, Delete!');
    expect()->request('PATCH', '/patch')->responseCode(200)->responseBody('Hello, Patch!');
    expect()->request('OPTIONS', '/options')->responseCode(200)->responseBody('Hello, Options!');
    expect()->request('HEAD', '/head')->responseCode(200)->responseBody('Hello, Head!');

    expect()->request('PUT', '/delete')->responseCode(404);
    expect()->request('DELETE', '/patch')->responseCode(404);
    expect()->request('PATCH', '/options')->responseCode(404);
    expect()->request('OPTIONS', '/head')->responseCode(404);
    expect()->request('HEAD', '/put')->responseCode(404);

    expect()->getPage("/not-found")->responseCode(404);

    expect()->getPage("/bad")->responseCode(500);

    expect(fn() => getApp()->routes['dont/set/directly'] = 'test')
        ->toThrow(RouterException::class);

    expect(fn() => getApp()->routes['invalid\url@pattern']->get(GetController::class))
        ->toThrow(RouterException::class);
});

