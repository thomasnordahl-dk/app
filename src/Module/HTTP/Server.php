<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\HTTP\Middleware\RequestHandler;

readonly class Server
{
    public function __construct(
        private EmitterInterface $emitter,
        private RequestHandler $requestHandler,
        private ServerRequestInterface $request,
    ) {

    }

    public function dispatch(): void
    {
        $response = $this->requestHandler->handle($this->request);

        $this->emitter->emit($response);
    }
}
