<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Console;

use League\CLImate\CLImate;

class ClimateFactory
{
    public function create(): CLImate
    {
        return new CLImate();
    }
}
