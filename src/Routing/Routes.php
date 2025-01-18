<?php

declare(strict_types=1);

namespace Ricotta\App\Routing;

class Routes implements \ArrayAccess
{
    /**
     * @var array<string, Definition>
     */
    private array $definitions = [];

    public function offsetExists(mixed $offset): bool
    {
        // TODO: Implement offsetExists() method.
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
}
