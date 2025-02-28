<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;

class TestEmitter implements EmitterInterface
{
    private(set) ResponseInterface $response {
        get {
            if (! isset($this->response) || ! isset($this->withoutBody)) {
                throw new \LogicException('The response has not been emitted.');
            }

            return $this->response;
        }
    }

    private(set) bool $withoutBody;

    public function emit(ResponseInterface $response, bool $withoutBody = false): void
    {
        $this->response = $response;
        $this->withoutBody = $withoutBody;
    }
}
