<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Project;

use Mock\ProjectA\MockService;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;

class LoadingModulesCest
{
    public function loadModules(FunctionalTester $I): void
    {
        $I->getApp()->loadModules();

        $container = new Container($I->getApp()->bootstrap);

        $I->assertInstanceOf(MockService::class, $container->get(MockService::class));

        $I->amOnPage('/project-a');
        $I->seeResponseCodeIs(200);


        $I->amOnPage('/project-b');
        $I->seeResponseCodeIs(200);
    }
}
