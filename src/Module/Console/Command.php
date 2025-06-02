<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Console;

interface Command
{
    public static function getName(): string;

    public static function getDescription(): string;

    public function run(): void;
}
