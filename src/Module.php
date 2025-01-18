<?php

declare(strict_types=1);

namespace Ricotta\App;

interface Module
{
    public function register(App $param): void;
}
