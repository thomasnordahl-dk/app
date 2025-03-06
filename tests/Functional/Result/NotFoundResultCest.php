<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Result;

use Ricotta\App\Module\Web\Result\JSONResult;
use Ricotta\App\Module\Web\Result\NotFoundResult;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;
use RuntimeException;

class NotFoundResultCest
{
    public function createsResponse(FunctionalTester $I): void
    {
        $container = new Container($I->getApp()->bootstrap);

        $result = new NotFoundResult();

        $response = $result->createResponse($container);
        $expectedBody = file_get_contents(codecept_root_dir('/templates/not-found.html'));

        $I->assertSame(404, $response->getStatusCode());
        $I->assertSame($expectedBody, $response->getBody()->getContents());
        $I->assertSame('text/html; charset=utf-8', $response->getHeaderLine('content-type'));
    }

    public function testBadData(FunctionalTester $I): void
    {
        $container = new Container($I->getApp()->bootstrap);

        $bad_array = [];

        $bad_array['self'] = &$bad_array; // Can not encode circular reference to JSON

        $result = new JSONResult($bad_array);

        $I->expectThrowable(RuntimeException::class, fn () => $result->createResponse($container));
    }
}
