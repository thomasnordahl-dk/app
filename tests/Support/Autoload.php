<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Support;

use Pest\Expectation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\App;

use function set_error_handler;

use const PHP_EOL;

expect()->extend('responseCode', function (int $expectedCode): Expectation {
    return expect(getResponse()?->getStatusCode())->toBe($expectedCode);
});

expect()->extend('responseBody', function (string $expectedBody): Expectation {
    return expect((string) getResponse()?->getBody()->getContents())->toBe($expectedBody);
});

expect()->extend('responseHeader', function (string $header, string $expectedValue): Expectation {
    return expect(getResponse()?->getHeaderLine($header))->toBe($expectedValue);
});

expect()->extend('getPage', function (string $path): void {
    getApp()->bootstrap[ServerRequestInterface::class]->register()
        ->callback(
            fn(ServerRequestFactoryInterface $requestFactory) => $requestFactory
                ->createServerRequest('GET', $path)
        );

    getApp()->run();
});

expect()->extend('postPage', function (string $path, array $data = []): void {
    getApp()->bootstrap[ServerRequestInterface::class]->register()
        ->callback(
            fn(ServerRequestFactoryInterface $requestFactory) => $requestFactory
                ->createServerRequest('POST', $path)
                ->withParsedBody($data)
        );

    getApp()->run();
});

expect()->extend('request', function (string $method, string $path, array $data = []): void {
    getApp()->bootstrap[ServerRequestInterface::class]->register()
        ->callback(
            fn(ServerRequestFactoryInterface $requestFactory) => $requestFactory
                ->createServerRequest($method, $path)
                ->withParsedBody($data)
        );

    getApp()->run();
});

function getResponse(): ?ResponseInterface
{
    return getSupport()->getResponse();
}

function getApp(): App
{
    return getSupport()->getApp();
}

function resetApp(): void
{
    getSupport()->resetApp();
}

function getSupport(): TestAppState
{
    global $support;

    $support ??= new TestAppState();

    return $support;
}

// Pest eats up errors and exceptions. Call this function before running tests to see them.
function useDebugErrorHandler(): void
{
    set_error_handler(function ($number, $message, $file, $line) {
        echo "Error [$number]: $message in $file on line $line" . PHP_EOL;

        return false; // Allow default error handling to proceed
    });
}
