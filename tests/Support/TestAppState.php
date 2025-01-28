<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\App;

class TestAppState
{
    private ?TestEmitter $internalEmitter = null;

    private ?App $internalApp = null;

    public function resetApp(): void
    {
        $this->internalApp = null;
    }

    public function getApp(): App
    {
        if ($this->internalApp === null) {
            $this->internalEmitter = new TestEmitter();
            $this->internalApp = new App();
            // Register the TestEmitter instance as the EmitterInterface implementation.
            $this->internalApp->bootstrap[EmitterInterface::class]
                ->register()
                ->value($this->internalEmitter);
        }

        return $this->internalApp;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->internalEmitter?->response;
    }
}
