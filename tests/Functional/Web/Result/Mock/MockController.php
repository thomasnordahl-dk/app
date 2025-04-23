<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Result\Mock;

use Psr\Http\Message\ResponseInterface;
use Ricotta\App\Module\Web\Controller;

class MockController implements Controller
{
    public function dispatch(): MockResult
    {
        return new MockResult();
    }
}
