<?php

declare(strict_types=1);

namespace Ricotta\App\Utility;

/**
 * Check for the current SAPI environment
 */
class Environment
{
    public function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }
}
