<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Middleware;

use Ricotta\App\App;
use Ricotta\App\Tests\Support\FunctionalTester;
use RuntimeException;
use stdClass;

class MiddlewareCest
{
    public function testMiddleware(FunctionalTester $I): void
    {
        $I->getApp()->bootstrap[MockMiddleware::class]->register();
        $I->getApp()->bootstrap[App::MIDDLEWARE_STACK]
            ->register()
            ->value([
                    $I->getApp()->bootstrap[MockMiddleware::class]->reference(), // container reference
                    new MockMiddleware('custom-') // instance
                ]);

        $I->getApp()->routes['/']->get(MockController::class);

        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(
            'Attributes: {"mock-middleware":"mock value","custom-mock-middleware":"custom-mock value"}'
        );
        $I->seeHttpHeader(MockMiddleware::KEY, MockMiddleware::VALUE);
        $I->seeHttpHeader('custom-' . MockMiddleware::KEY, 'custom-' . MockMiddleware::VALUE);
    }

    public function badMiddlewareType(FunctionalTester $I): void
    {
        $I->getApp()->bootstrap[App::MIDDLEWARE_STACK]->register()->value([
            new stdClass(),
        ]);

        $I->getApp()->routes['/']->get(MockController::class);

        $I->amOnPage('/');

        $I->seeResponseCodeIs(500);
        $I->seeInSource('Oops! Something went wrong');
    }

    public function brokenMiddleware(FunctionalTester $I): void
    {
        $exception = new RuntimeException("Test exception");

        $I->getApp()->bootstrap[App::MIDDLEWARE_STACK]->register()->value([
            new BrokenMiddleware($exception),
        ]);

        $I->getApp()->routes['/']->get(MockController::class);

        $I->amOnPage('/');

        $I->seeResponseCodeIs(500);
        $I->seeInSource('Oops! Something went wrong');
    }
}
