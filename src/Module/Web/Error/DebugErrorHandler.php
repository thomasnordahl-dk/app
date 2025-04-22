<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;

class DebugErrorHandler implements ErrorHandler
{
    public function __construct(
        private readonly TemplateEngine $templateEngine,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function handle(Throwable $error): ResponseInterface
    {
        $errorPage = $this->templateEngine->render('debug-error-page', 'ricotta/app', ['error' => $error]);

        $body = $this->streamFactory->createStream($errorPage);
        $response = $this->responseFactory->createResponse(500)->withBody($body);

        return $response;
    }
}
