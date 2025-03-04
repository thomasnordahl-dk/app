<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Error;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorHandler
{
    public function handle(Throwable $error): ResponseInterface;
}
