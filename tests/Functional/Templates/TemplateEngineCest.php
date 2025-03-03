<?php

namespace Ricotta\App\Tests\Functional\Templates;

use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Template\TemplateException;
use Ricotta\App\Tests\Functional\Templates\Mock\MockView;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;
use stdClass;

class TemplateEngineCest
{
    public function testTemplateEngine(FunctionalTester $I): void
    {
        $I->getApp()->bootstrap[TemplateEngine::class]->configure(function (TemplateEngine $engine) {
            $engine->addPackagePath('ricotta/app', __DIR__ . '/templates');
        });

        $container = new Container($I->getApp()->bootstrap);
        $engine = $container->get(TemplateEngine::class);

        $I->assertSame('hello world', $engine->render('simple-template', 'ricotta/app'));
        $I->assertSame('hello world', $engine->render('html-template', 'ricotta/app'));

        $I->expectThrowable(TemplateException::class, fn () => $engine->render('does-not-exist', 'ricotta/app'));
        $I->expectThrowable(TemplateException::class, fn () => $engine->render('html-template', 'unknown/app'));
        
        $I->assertSame(
            'message', 
            $engine->render('callback-template', 'ricotta/app', [MockView::class => new MockView('message')])
        );
        
        // TODO test override template
        // TODO test nested templating
    }
}