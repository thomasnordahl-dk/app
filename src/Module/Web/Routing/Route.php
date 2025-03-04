<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Routing;

use Ricotta\App\Module\Web\Controller;

/**
 * @internal
 */
readonly class Route
{
    /**
     * @param string                   $route
     * @param string                   $path
     * @param class-string<Controller> $controller
     * @param Method                   $method
     * @param array<string,string>     $parameters
     * @param string|null              $wildcard
     */
    public function __construct(
        public string $route,
        public string $path,
        public string $controller,
        public Method $method,
        public array $parameters,
        public ?string $wildcard,
    ) {
    }
}
