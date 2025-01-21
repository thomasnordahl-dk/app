<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Routing;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @implements \ArrayAccess<string, Definition>
 */
class Routes implements \ArrayAccess
{
    /**
     * @var array<string, Definition>
     */
    private array $definitions = [];

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->definitions);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->definitions[$offset] ??= new Definition($offset);

        return $this->definitions[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset): void
    {
        // TODO: Implement offsetUnset() method.
    }

    public function detect(ServerRequestInterface $request): ?Route
    {
        foreach ($this->definitions as $route => $definition) {
            $routeSubPaths = explode('/', $route);
            $pathSubPaths = explode('/', $request->getUri()->getPath());

            if (count($routeSubPaths) !== count($pathSubPaths)) {
                continue;
            }

            foreach ($routeSubPaths as $index => $routeSubPath) {
                if (preg_match('/^{(.*)}$/i', $routeSubPath)) {
                    continue;
                }

                if ($routeSubPath === '*' || $routeSubPath !== $pathSubPaths[$index]) {
                    continue 2;
                }
            }

            return $definition->createRoute();
        }

        return null;
    }
}
