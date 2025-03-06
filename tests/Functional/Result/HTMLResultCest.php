<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Result;

use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Routing\Result\HTMLResult;
use Ricotta\App\Tests\Functional\Result\Mock\MockController;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;

class HTMLResultCest
{
    public function createsResponse(FunctionalTester $I): void
    {
        $bootstrapping = $I->getApp()->bootstrap;

        $bootstrapping[TemplateEngine::class]->configure(function (TemplateEngine $templateEngine) {
            $templateEngine->addPackagePath('ricotta/app', __DIR__ . '/templates');
        });

        $container = new Container($bootstrapping);

        $result = new HTMLResult(
            'template',
            'ricotta/app',
            ['message' => 'HTML Result model'],
            201,
            ['x-header' => 'x-value']
        );

        $response = $result->createResponse($container);

        $I->assertSame(201, $response->getStatusCode());
        $I->assertSame('HTML Result model', $response->getBody()->getContents());
        $I->assertSame('text/html; charset=utf-8', $response->getHeaderLine('content-type'));
        $I->assertSame($response->getHeaderLine('x-header'), 'x-value');
    }
}
