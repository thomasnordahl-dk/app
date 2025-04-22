<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Error;

use Ricotta\App\App;
use Ricotta\App\Module\Web\Error\DebugErrorHandler;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Ricotta\App\Tests\Functional\Web\Middleware\BrokenMiddleware;
use Ricotta\App\Tests\Support\FunctionalTester;

class DebugErrorHandlerCest
{
    public function debugErrorPage(FunctionalTester $I): void
    {
        $error = new \RuntimeException("Test error");

        $I->getApp()->bootstrap[App::MIDDLEWARE_STACK]->register()->value([new BrokenMiddleware($error)]);
        $I->getApp()->bootstrap[ErrorHandler::class]->register()->type(DebugErrorHandler::class);

        $I->amOnPage('/');
        $I->see("RuntimeException");
        $I->see("Type: RuntimeException");
        $I->see("Message: Test error");
        $I->see(__FILE__);
        $I->see("Line: 18");
    }
}
