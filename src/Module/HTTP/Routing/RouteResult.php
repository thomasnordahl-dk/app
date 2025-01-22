<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP\Routing;

readonly class RouteResult
{
    /**
     * @param bool                              $isFound
     * @param ($isFound is true ? Route : null) $route
     */
    public function __construct(
        public bool $isFound,
        public ?Route $route,
    ) {
    }
}
