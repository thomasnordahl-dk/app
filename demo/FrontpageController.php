<?php

declare(strict_types=1);

namespace Ricotta\App\Demo;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\HTTP\Controller;

readonly class FrontpageController implements Controller
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $body = $this->streamFactory->createStream('Demo front page');

        return $this->responseFactory->createResponse(200)->withBody($body);
    }
}
