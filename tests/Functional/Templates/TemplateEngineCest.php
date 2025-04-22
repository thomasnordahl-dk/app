<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Templates;

use Error;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Template\TemplateException;
use Ricotta\App\Tests\Functional\Templates\Mock\MockView;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;
use RuntimeException;
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

        $I->expectThrowable(TemplateException::class, fn() => $engine->render('does-not-exist', 'ricotta/app'));
        $I->expectThrowable(TemplateException::class, fn() => $engine->render('html-template', 'unknown/app'));

        $I->expectThrowable(RuntimeException::class, fn () => $engine->render('throws', 'ricotta/app'));
        $I->expectThrowable(RuntimeException::class, fn () => $engine->render('throws-from-nested', 'ricotta/app'));

        $I->assertSame(0, ob_get_level());

        $I->assertSame(
            'message',
            $engine->render('callback-template', 'ricotta/app', ['view' => new MockView('message')])
        );

        $engine->addPackagePath('ricotta/app', __DIR__ . '/override-templates');
        $I->assertSame('hello override', $engine->render('html-template', 'ricotta/app'));

        $I->assertSame('Message: hello override', $engine->render('nested-template', 'ricotta/app'));

        $I->assertSame(
            'message',
            $engine->render('callback-template', 'ricotta/app', ['view' => new MockView('message')]),
            'uses a non-callback override to test availability of injections directly in template'
        );
    }
}
