<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web;

use ArrayAccess;
use Ricotta\App\Module\Web\Routing\Definition;

/**
 * @extends ArrayAccess<string, Definition>
 */
interface Routes extends ArrayAccess
{
    public function offsetGet(mixed $offset): Definition;
}
