<?php

declare(strict_types=1);

namespace Ricotta\App\Utility\Codeception;

use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Psr\Http\Message\ResponseInterface;
use Ricotta\App\App;

class Functional extends Framework //implements MultiSession
{
    private TestState $state;

    // @codingStandardsIgnoreStart (codeception requires this naming convention)
    public function _before(TestInterface $test): void //@phpcs-ignore
    {
        // @codingStandardsIgnoreEnd

        parent::_before($test);

        $this->state = new TestState();
        $this->client = new Client($this->state);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->state->getResponse();
    }

    public function getApp(): App
    {
        return $this->state->getApp();
    }
}
