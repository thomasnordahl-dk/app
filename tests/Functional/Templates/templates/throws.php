<?php

declare(strict_types=1);

use PHPUnit\Event\Code\Throwable;

echo "Before throwing";

throw new Error("Bad template");
