<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Unit\Routing\Mock;

class PutController extends GetController
{
    public string $message = 'Hello, Put!';
}
