<?php

declare(strict_types=1);

namespace Ricotta\App\Utility\Codeception;

use Ricotta\App\Utility\Environment;

class TestEnvironment extends Environment
{
    public function __construct(private bool $isCLI)
    {
    }

    public function isCli(): bool
    {
        return $this->isCLI;
    }
}
