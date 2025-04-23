<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Ricotta\App\Module\Web\Controller;

use function array_slice;
use function explode;
use function implode;
use function preg_match;

/**
 * @internal
 */
class Definition
{
    private const string VALID_PATTERN_REGEX = '#^(/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@%]+|\{[A-Za-z0-9_]+\}))*/*\*?$#';

    /**
     * @var array<Method, class-string<Controller>> $controllers
     */
    private array $controllers = [];

    /**
     * @throws RouterException
     */
    public function __construct(private readonly string $pattern)
    {
        if (! preg_match(self::VALID_PATTERN_REGEX, $pattern)) {
            throw new RouterException("Invalid pattern - '{$pattern}'");
        }
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function get(string $controller): self
    {
        $this->set(Method::GET, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function post(string $controller): self
    {
        $this->set(Method::POST, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function put(string $controller): self
    {
        $this->set(Method::PUT, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function patch(string $controller): self
    {
        $this->set(Method::PATCH, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function delete(string $controller): self
    {
        $this->set(Method::DELETE, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */
    public function options(string $controller): self
    {
        $this->set(Method::OPTIONS, $controller);

        return $this;
    }

    /**
     * @param class-string<Controller> $controller
     *
     * @return self
     */

    public function head(string $controller): self
    {
        $this->set(Method::HEAD, $controller);

        return $this;
    }

    /**
     * @param Method                   $method
     * @param class-string<Controller> $controller
     *
     * @return void
     */
    private function set(Method $method, string $controller): void
    {
        $this->controllers[$method->value] = $controller;
    }

    /**
     * @throws RouterException
     */
    public function detectRoute(ServerRequestInterface $request): ?Route
    {
        $method = Method::fromString($request->getMethod());

        if (! isset($this->controllers[$method->value])) {
            return null;
        }

        $path = $request->getUri()->getPath();

        /** @var array<int, string> $subPatterns */
        $subPatterns = explode('/', trim($this->pattern, ' /'));

        /** @var array<int, string> $subPaths */
        $subPaths = explode('/', trim($path, ' /'));

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

            if ($subPattern !== ($subPaths[$index] ?? null)) {
                return null;
            }
        }

        return new Route(
            $this->pattern,
            $path,
            $this->controllers[$method->value],
            $method,
            $parameters,
            $wildcard
        );
    }
}
