<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Unit\Routing\Mock;

class HeadController extends GetController
{
    public string $message = 'Hello, Head!';
}
