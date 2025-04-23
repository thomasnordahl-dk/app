<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Web\Controller;

readonly class MockController implements Controller
{
    public function __construct(
        private ServerRequestInterface $request,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $attributes = $this->request->getAttributes();

        $body = $this->streamFactory->createStream("Attributes: " . json_encode($attributes));

        return $this->responseFactory
            ->createResponse(200)
            ->withBody($body);
    }
}
