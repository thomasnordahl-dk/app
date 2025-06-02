<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support\Ricotta;

use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\App;

class Functional extends Framework //implements MultiSession
{
    private ?TestState $state = null;

    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        $this->resetApp();
        $this->client = new TestClient($this->getSupport());
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->getSupport()->getResponse();
    }

    public function getApp(): App
    {
        return $this->getSupport()->getApp();
    }

    public function resetApp(): void
    {
        $this->state = new TestState();
    }

    public function getSupport(): TestState
    {
        $this->state ??= new TestState();

        return $this->state;
    }
}
