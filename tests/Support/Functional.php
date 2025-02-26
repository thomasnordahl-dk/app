<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use Codeception\Lib\Framework;
use Codeception\Lib\ModuleContainer;
use Codeception\TestInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\App;

class Functional extends Framework //implements MultiSession
{
    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        $this->client = new TestClient($this->getSupport());
    }

    public function __construct(ModuleContainer $moduleContainer, ?array $config = null)
    {
        parent::__construct($moduleContainer, $config);
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
        $this->getSupport()->resetApp();
    }

    public function getSupport(): TestState
    {
        static $support;

        $support ??= new TestState();

        return $support;
    }
}
