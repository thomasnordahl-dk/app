<?php

namespace Ricotta\App\Tests\Unit;

use Ricotta\App\Tests\Unit\Modules\Mock\MockModule;

use function Ricotta\App\Tests\Support\getApp;
use function Ricotta\App\Tests\Support\resetApp;

beforeEach(fn() => resetApp());

test('App modules', function () {
    getApp()->add(new MockModule());
    getApp()->run();

    expect()->getPage('/')->responseCode(200)->responseBody('Hello, Service!');
});
