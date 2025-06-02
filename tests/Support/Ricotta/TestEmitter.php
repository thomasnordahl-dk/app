<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support\Ricotta;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;

class TestEmitter implements EmitterInterface
{
    // @codingStandardsIgnoreStart - PHPCS has trouble with the new property hooks :(
    public private(set) ResponseInterface $response 
    {
        get {
            if (! isset($this->response) || ! isset($this->withoutBody)) {
                throw new \LogicException('The response has not been emitted.');
            }

            return $this->response;
        }
    }

    public private(set) bool $withoutBody;
    
    // @codingStandardsIgnoreEnd
    public function emit(ResponseInterface $response, bool $withoutBody = false): void
    {
        $this->response = $response;
        $this->withoutBody = $withoutBody;
    }
}
