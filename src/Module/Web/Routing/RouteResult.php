<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Routing;

/**
 * @property bool $isFound
 * @property ($isFound is true ? Route : null) $route
 */
readonly class RouteResult
{
    /**
     * TODO - the Route type is not being narrowed from asserting $isFound is true.
     *
     * @param bool $isFound
     * @param ($isFound is true ? Route : null) $route
     */
    public function __construct(
        public bool $isFound,
        public ?Route $route,
    ) {
    }
}
