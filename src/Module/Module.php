<?php

declare(strict_types=1);

namespace Ricotta\App\Module;

use Ricotta\App\App;

interface Module
{
    public function register(App $app): void;
}
