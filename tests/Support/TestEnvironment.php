<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use Ricotta\App\Utility\Environment;

class TestEnvironment extends Environment
{
    public function __construct(private bool $overrideIsCli)
    {
    }

    public function isCli(): bool
    {
        return $this->overrideIsCli;
    }
}
