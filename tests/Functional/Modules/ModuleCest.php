<?php

namespace Ricotta\App\Tests\Functional\Modules;

use Ricotta\App\Tests\Functional\Modules\Mock\MockModule;
use Ricotta\App\Tests\Support\FunctionalTester;

class ModuleCest
{
    public function appModules(FunctionalTester $I): void
    {
        $I->getApp()->add(new MockModule());
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Service!');
    }
}
