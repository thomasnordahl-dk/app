<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Result;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Result;
use Ricotta\Container\Container;

class HtmlResult implements Result
{
    /**
     * @param string $fileName
     * @param string $packageName
     * @param array<string, mixed> $injections
     * @param int $statusCode
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $fileName,
        private string $packageName,
        private array $injections = [],
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
            TemplateEngine $templateEngine,
        ) {
            $html = $templateEngine->render($this->fileName, $this->packageName, $this->injections);
            $stream = $streamFactory->createStream($html);

            $response = $responseFactory
                ->createResponse($this->statusCode)
                ->withBody($stream)
                ->withHeader('content-type', 'text/html; charset=utf-8');

            foreach ($this->headers as $key => $value) {
                $response = $response->withHeader($key, $value);
            }

            return $response;
        });
    }
}
