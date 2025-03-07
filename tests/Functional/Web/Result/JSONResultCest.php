<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Result;

use Ricotta\App\Module\Web\Result\JSONResult;
use Ricotta\App\Tests\Support\FunctionalTester;
use Ricotta\Container\Container;
use RuntimeException;

class JSONResultCest
{
    public function createsResponse(FunctionalTester $I): void
    {
        $container = new Container($I->getApp()->bootstrap);

        $data = [
            'message' => 'HTML Result model',
            'list' => [
                'one',
                'two',
                'three',
            ],
        ];

        $result = new JSONResult(
            $data,
            201,
            ['x-header' => 'x-value']
        );

        $expectedBody = json_encode($data, JSON_PRETTY_PRINT);

        $response = $result->createResponse($container);

        $I->assertSame(201, $response->getStatusCode());
        $I->assertSame($expectedBody, $response->getBody()->getContents());
        $I->assertSame('application/json', $response->getHeaderLine('content-type'));
        $I->assertSame($response->getHeaderLine('x-header'), 'x-value');
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
