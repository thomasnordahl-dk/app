<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web;

use Psr\Http\Message\ResponseInterface;

interface Controller
{
    public function dispatch(): Result|ResponseInterface;
}
