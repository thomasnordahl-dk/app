<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Result;

use Psr\Http\Message\ResponseInterface;
use Ricotta\App\Module\Web\Result;
use Ricotta\Container\Container;

class NotFoundResult implements Result
{
    public function createResponse(Container $container): ResponseInterface
    {
        return new HTMLResult('not-found', 'ricotta/app', statusCode: 404)->createResponse($container);
    }
}
