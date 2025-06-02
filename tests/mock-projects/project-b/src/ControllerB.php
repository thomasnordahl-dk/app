<?php

declare(strict_types=1);

namespace Mock\ProjectB;

use Ricotta\App\Module\Web\Controller;
use Ricotta\App\Module\Web\Result;
use Ricotta\App\Module\Web\Result\JsonResult;

class ControllerB implements Controller
{
    public function dispatch(): Result
    {
        return new JsonResult('');
    }
}
