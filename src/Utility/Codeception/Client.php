<?php

declare(strict_types=1);

namespace Ricotta\App\Utility\Codeception;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

/**
 * @extends AbstractBrowser<Request, Response>
 */
class Client extends AbstractBrowser
{
    public function __construct(private readonly TestState $appState)
    {
        parent::__construct();
    }

    protected function doRequest(object $request): Response
    {
        $app = $this->appState->getApp();

        $app->bootstrap[ServerRequestInterface::class]->register()
            ->callback(
                fn(
                    ServerRequestFactoryInterface $requestFactory,
                    StreamFactoryInterface $streamFactory,
                ) => $requestFactory
                    ->createServerRequest($request->getMethod(), $request->getUri())
                    ->withBody($streamFactory->createStream($request->getContent() ?? ''))
            );

        $app->run();

        if ($this->appState->getResponse() === null) {
            throw new \RuntimeException('No response was found');
        }

        return new Response(
            $this->appState->getResponse()->getBody()->getContents(),
            $this->appState->getResponse()->getStatusCode(),
            $this->appState->getResponse()->getHeaders(),
        );
    }
}
