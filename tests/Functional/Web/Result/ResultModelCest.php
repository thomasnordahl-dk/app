<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Result;

use Ricotta\App\Tests\Functional\Web\Result\Mock\MockController;
use Ricotta\App\Tests\Support\FunctionalTester;

class ResultModelCest
{
    public function controllersCanReturnResult(FunctionalTester $I): void
    {
        $I->getApp()->routes['/']->get(MockController::class);

        $I->amOnPage('/');

        $I->seeResponseCodeIs(200);
        $I->see("Mock result");
    }
}
