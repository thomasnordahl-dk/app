<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Throwable;

class ErrorPageHandler implements ErrorHandler
{
    public function __construct(
        private readonly TemplateEngine $templateEngine,
        private readonly ResponseFactoryInterface $responseFactory,
    ) { }

    public function handle(Throwable $error): ResponseInterface
    {
        $errorPage = $this->templateEngine->render('error-page', 'ricotta/app');
 
        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write($errorPage);

        return $response;
    }
}
