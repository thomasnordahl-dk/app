<?php

declare(strict_types=1);

namespace Ricotta\App\Routing;

use Ricotta\App\Controller;

readonly class Route
{
    /**
     * @param string                   $route
     * @param string                   $path
     * @param class-string<Controller> $controller
     * @param Method                   $method
     */
    public function __construct(
        public string $route,
        public string $path,
        public string $controller,
        public Method $method,
    ) {
    }
}
