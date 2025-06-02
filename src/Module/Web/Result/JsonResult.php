<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Result;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Result;
use Ricotta\Container\Container;
use RuntimeException;

class JsonResult implements Result
{
    /**
     * @param mixed $data
     * @param int $statusCode
     * @param array<string, string> $headers
     */
    public function __construct(
        private mixed $data,
        private int $statusCode = 200,
        private array $headers = [],
    ) {
    }

    public function createResponse(Container $container): ResponseInterface
    {
        /** @var ResponseInterface */
        return $container->call(function (
            ResponseFactoryInterface $responseFactory,
            StreamFactoryInterface $streamFactory,
        ) {
            $content = json_encode($this->data, JSON_PRETTY_PRINT);

            if ($content === false) {
                throw new RuntimeException("JSON error: " . json_last_error_msg());
            }

            $stream = $streamFactory->createStream($content);

            $response = $responseFactory
                ->createResponse($this->statusCode)
                ->withBody($stream)
                ->withHeader('content-type', 'application/json');

            foreach ($this->headers as $key => $value) {
                $response = $response->withHeader($key, $value);
            }

            return $response;
        });
    }
}
