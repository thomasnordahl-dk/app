<?php

declare(strict_types=1);

namespace Ricotta\App\Module\HTTP\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\HTTP\Controller;

use function array_slice;
use function explode;
use function implode;
use function preg_match;
use function var_dump;

class Definition
{
    private Method $method;

    /**
     * @var class-string<Controller>
     */
    private string $controller;

    public function __construct(private readonly string $pattern)
    {
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function get(string $controller): void
    {
        $this->set(Method::GET, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function post(string $controller): void
    {
        $this->set(Method::POST, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function put(string $controller): void
    {
        $this->set(Method::PUT, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function patch(string $controller): void
    {
        $this->set(Method::PATCH, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function delete(string $controller): void
    {
        $this->set(Method::DELETE, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    public function options(string $controller): void
    {
        $this->set(Method::OPTIONS, $controller);
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return void
     */

    public function head(string $controller): void
    {
        $this->set(Method::HEAD, $controller);
    }

    /**
     * @param Method                   $method
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    private function set(Method $method, string $controller): void
    {
        $this->method = $method;
        $this->controller = $controller;
    }

    public function detectRoute(ServerRequestInterface $request): ?Route
    {
        if (strtoupper($request->getMethod()) !== $this->method->value) {
            return null;
        }

        $path = $request->getUri()->getPath();

        /** @var array<int, string> $subPatterns */
        $subPatterns = explode('/', $this->pattern);

        /** @var array<int, string> $subPaths */
        $subPaths = explode('/', $path);

        /** @var array<string, string> $parameters */
        $parameters = [];

        /** @var ?string $wildcard */
        $wildcard = null;

        foreach ($subPatterns as $index => $subPattern) {
            if (preg_match('/^{(.*)}$/i', $subPattern)) {
                $parameters[mb_substr($subPattern, 1, -1)] = $subPaths[$index];
                continue;
            }

            if ($subPattern === '*') {
                $wildcard = implode('/', array_slice($subPaths, $index));
                continue;
            }

            if ($subPattern !== $subPaths[$index]) {
                return null;
            }
        }

        return new Route(
            $this->pattern,
            $path,
            $this->controller,
            $this->method,
            $parameters,
            $wildcard
        );
    }
}
