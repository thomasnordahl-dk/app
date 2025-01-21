<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Unit\Mock;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\HTTP\Controller;

class MockController implements Controller
{
    public string $message = 'Hello, World!';

    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly StreamFactoryInterface $stream_factory
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $stream = $this->stream_factory->createStream($this->message);

        return $this->response_factory->createResponse(200)->withBody($stream);
    }
}
