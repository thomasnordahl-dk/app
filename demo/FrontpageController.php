<?php

declare(strict_types=1);

namespace Ricotta\App\Demo;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Controller;

readonly class FrontpageController implements Controller
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private TemplateEngine $templateEngine,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $body = $this->streamFactory->createStream($this->templateEngine->render('front-page', 'ricotta/app'));
        
        return $this->responseFactory->createResponse(200)->withBody($body);
    }
}
