<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web;

use Psr\Http\Message\ResponseInterface;
use Ricotta\Container\Container;

interface Result
{
    public function createResponse(Container $container): ResponseInterface;
}
