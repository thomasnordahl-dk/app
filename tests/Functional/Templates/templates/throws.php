<?php

use PHPUnit\Event\Code\Throwable;

echo "Before throwing";

throw new Error("Bad template");
