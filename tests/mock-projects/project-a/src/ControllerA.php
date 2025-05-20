<?php

declare(strict_types=1);

namespace Mock\ProjectA;

use Psr\Http\Message\ResponseInterface;
use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Result;
use Ricotta\App\Module\Web\Result\JSONResult;

class ControllerA implements Controller
{
    public function dispatch(): Result
    {
        return new JSONResult('');
    }
}
