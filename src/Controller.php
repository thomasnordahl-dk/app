<?php

declare(strict_types=1);

namespace Ricotta\App;

use Psr\Http\Message\ResponseInterface;

interface Controller
{
    public function dispatch(): ResponseInterface;
}
