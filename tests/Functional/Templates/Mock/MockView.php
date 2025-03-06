<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Templates\Mock;

class MockView
{
    public function __construct(
        public string $message,
    ) {
    }
}
