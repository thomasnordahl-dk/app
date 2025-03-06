<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Result\Mock;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\Module\Web\Result;
use Ricotta\Container\Container;

class MockResult implements Result
{
    public function createResponse(Container $container): ResponseInterface
    {
        return $container->call(function (ResponseFactoryInterface $responseFactory) {
            $response = $responseFactory->createResponse(200);
            $response->getBody()->write('Mock result');
            $response->getBody()->rewind();

            return $response;
        });
    }
}
