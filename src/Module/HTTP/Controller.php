<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP;

use Psr\Http\Message\ResponseInterface;

interface Controller
{
    public function dispatch(): ResponseInterface;
}
