<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\Web\Routes;

class Router implements Routes
{
    /**
     * @var array<string, Definition>
     */
    private array $definitions = [];

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->definitions);
    }

    public function offsetGet(mixed $offset): Definition
    {
        $this->definitions[$offset] ??= new Definition($offset);

        return $this->definitions[$offset];
    }

    /**
     * @throws RouterException
     */
    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RouterException('Cannot set a route definition directly.');
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->definitions[$offset]);
    }

    public function detect(ServerRequestInterface $request): ?Route
    {
        foreach ($this->definitions as $definition) {
            $route = $definition->detectRoute($request);
            if ($route !== null) {
                return $route;
            }
        }

        return null;
    }
}
