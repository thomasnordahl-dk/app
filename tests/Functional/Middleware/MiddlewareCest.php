<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Middleware;

use Ricotta\App\App;
use Ricotta\App\Tests\Support\FunctionalTester;

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
                ]
            );
        $I->getApp()->routes['/']->get(MockController::class);

        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals('Attributes: {"mock-middleware":"mock value","custom-mock-middleware":"custom-mock value"}');
        $I->seeHttpHeader(MockMiddleware::KEY, MockMiddleware::VALUE);
        $I->seeHttpHeader('custom-' . MockMiddleware::KEY, 'custom-' . MockMiddleware::VALUE);
    }
}
