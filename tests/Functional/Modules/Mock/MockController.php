<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Modules\Mock;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\HTTP\Controller;

class MockController implements Controller
{
    public function __construct(
        private Service $service,
        private ResponseFactoryInterface $response_factory,
        private StreamFactoryInterface $stream_factory,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $body = $this->stream_factory->createStream($this->service->message);

        return $this->response_factory->createResponse(200)->withBody($body);
    }
}
