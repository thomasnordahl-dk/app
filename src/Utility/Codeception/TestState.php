<?php

declare(strict_types=1);

namespace Ricotta\App\Utility\Codeception;

use HttpSoft\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\App;

class TestState
{
    private ?CaptureOutputEmitter $internalEmitter = null;

    private ?App $internalApp = null;

    public function getApp(): App
    {
        if ($this->internalApp === null) {
            $this->internalEmitter = new CaptureOutputEmitter();
            $this->internalApp = new App(new TestEnvironment(false));
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
