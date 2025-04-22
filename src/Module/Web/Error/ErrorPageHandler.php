<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Throwable;

class ErrorPageHandler implements ErrorHandler
{
    public function __construct(
        private readonly TemplateEngine $templateEngine,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function handle(Throwable $error): ResponseInterface
    {
        $errorPage = $this->templateEngine->render('error-page', 'ricotta/app');

        $body = $this->streamFactory->createStream($errorPage);
        $response = $this->responseFactory->createResponse(500)->withBody($body);

        return $response;
    }
}
