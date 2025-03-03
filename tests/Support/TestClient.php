<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\App;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

class TestClient extends AbstractBrowser
{
    public function __construct(private TestState $appState)
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doRequest(object $request): Response
    {
        $app = $this->appState->getApp();

        $app->bootstrap[ServerRequestInterface::class]->register()
            ->callback(
                fn(
                    ServerRequestFactoryInterface $requestFactory,
                    StreamFactoryInterface $streamFactory
                ) => $requestFactory
                    ->createServerRequest($request->getMethod(), $request->getUri())
                    ->withBody($streamFactory->createStream($request->getContent() ?? ''))
            );

        $app->run();

        return new Response(
            $this->appState->getResponse()->getBody()->getContents(),
            $this->appState->getResponse()->getStatusCode(),
            $this->appState->getResponse()->getHeaders(),
        );
    }
}
