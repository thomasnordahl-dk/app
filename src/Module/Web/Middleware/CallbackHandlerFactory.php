<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Middleware;

use Closure;

/**
 * @internal
 *
 * Create callback handler instances
 */
class CallbackHandlerFactory
{
    public function create(Closure $closure): CallbackHandler
    {
        return new CallbackHandler($closure);
    }
}
