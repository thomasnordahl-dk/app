<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP\Middleware;

use Closure;

class CallbackHandlerFactory
{
    public function create(Closure $closure): CallbackHandler
    {
        return new CallbackHandler($closure);
    }
}
