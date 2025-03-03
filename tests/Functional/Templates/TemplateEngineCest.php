<?php

namespace Ricotta\App\Tests\Functional\Templates;

use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;

class TemplateEngineCest
{
    public function testTemplateEngine(FunctionalTester $I): void
    {
        $I->getApp()->bootstrap[TemplateEngine::class]->configure(function (TemplateEngine $engine) {
            $engine->addPackagePath('ricotta/app', __DIR__ . '/templates');
        });

        $container = new Container($I->getApp()->bootstrap);
        $engine = $container->get(TemplateEngine::class);

        $result = $engine->render('simple-template', 'ricotta/app');

        $I->assertSame('hello world', $result);
        // TODO test override template
        // TODO test injection of dependencies
        // TODO test nested templating
        // TODO no template found
        // TODO no package paths found
    }
}