<?php

declare(strict_types=1);

namespace Ricotta\App\Demo;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Module\Template\TemplateEngine;
use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Result\HtmlResult;

readonly class FrontpageController implements Controller
{
    public function dispatch(): HtmlResult
    {
        return new HtmlResult('front-page', 'ricotta/app');
    }
}
