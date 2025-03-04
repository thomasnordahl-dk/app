<?php

namespace Ricotta\App\Tests\Functional\Modules;

use InvalidArgumentException;
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
 
    public function configFile(FunctionalTester $I): void
    {
        $I->getApp()->load(__DIR__ . '/Mock/config-file.php');
     
        $I->expectThrowable(
            InvalidArgumentException::class, 
            fn() => $I->getApp()->load(__DIR__ . '/Mock/config-file.txt')
        );

        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Service!');
    }
}
