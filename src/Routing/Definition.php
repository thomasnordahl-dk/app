<?php

declare(strict_types=1);

namespace Ricotta\App\Routing;

class Definition
{
    protected Method $method;

    /**
     * @var class-string<Controller>
     */
    protected string $controller;

    public function __construct(protected readonly string $pattern)
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
}
