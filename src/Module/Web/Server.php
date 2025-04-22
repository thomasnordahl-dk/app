<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\Web\Error\ErrorHandler;
use Ricotta\App\Module\Web\Middleware\RequestHandler;
use Throwable;

readonly class Server
{
    public function __construct(
        private EmitterInterface $emitter,
        private RequestHandler $requestHandler,
        private ServerRequestInterface $request,
        private ErrorHandler $errorHandler,
    ) {
    }

    public function dispatch(): void
    {
        try {
            $response = $this->requestHandler->handle($this->request);
        } catch (Throwable $error) {
            $response = $this->errorHandler->handle($error);
        }
        
        $this->emitter->emit($response);
    }
}
