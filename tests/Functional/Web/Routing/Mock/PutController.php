<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Routing\Mock;

class PutController extends GetController
{
    public string $message = 'Hello, Put!';
}
