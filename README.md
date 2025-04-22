[![Tests](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml/badge.svg)](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml)

# ricotta/app

**ricotta/app** is the core application of the Ricotta framework—a robust, extensible foundation for building modern PHP projects. Use it to bootstrap your new Ricotta project with ease.

- [License](LICENCE.md)
- [Contributing Guidelines](CONTRIBUTING.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [Roadmap](ROADMAP.md)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Basic Setup](#basic-setup)
  - [Routing](#routing)
  - [Controller Classes](#controller-classes)
  - [Dependency Injection](#dependency-injection)
  - [Modules](#modules)
  - [Configuration Files](#configuration-files)
  - [Advanced Routing](#advanced-routing)
  - [Middleware](#middleware)
  - [Templates](#templates)
  - [Result Models](#result-models)
  - [Error Handling](#error-handling)

## Installation

Install the framework via Composer:

```bash
composer require ricotta/app
``` 

## Usage

### Basic Setup

Create an `index.php` file in your webroot directory to bootstrap the application:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ricotta\App\App;

$app = new App();
$app->run();
``` 

### Routing

Define routes by mapping URL paths to controller classes via the application's `routes` property. Each route supports HTTP method binding:

```php
<?php
// {root}/webroot/index.php

require_once __DIR__ . '/vendor/autoload.php';

use Ricotta\App\App;
use MyModule\GetFrontPage;
use MyModule\PostComment;

$app = new App();

$app->routes['/']->get(GetFrontPage::class);
$app->routes['/post-comment']->post(PostComment::class);

$app->run();
``` 

### Controller Classes

Controllers must implement the `Controller` interface. They automatically receive the PSR-7 request and any services or components registered with the Dependency Injection Container (DIC) via dependency injection. Controllers are autowired—no explicit registration with the DIC is needed.

```php
<?php
namespace MyModule;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Web\Controller;

class GetFrontPage implements Controller
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory
    ) {}

    public function dispatch(): ResponseInterface
    {
        $stream = $this->streamFactory->createStream("Hello FrontPage");
        return $this->responseFactory->createResponse(200)
            ->withBody($stream);
    }
}
``` 

### Dependency Injection

Services and components can be bootstrapped and configured for dependency injection using the bootstrapping object on the `App` instance. These services are registered with the Dependency Injection Container (DIC). See the [ricotta/container documentation](https://github.com/thomasnordahl-dk/container) for further details.

```php
$app->bootstrap['MyService::class']->register();
``` 

### Modules

Encapsulate bootstrapping and routing logic in modules. Each module must implement the `Module` interface:

```php
<?php

use Ricotta\App\App;
use Ricotta\App\Module;
use MyModule\GetFrontPage;

class MyModule implements Module
{
    public function register(App $app): void
    {
        $app->routes['/']->get(GetFrontPage::class);
        $app->bootstrap['MyService::class']->register();
    }
}
``` 

### Configuration Files

Organize configuration and bootstrapping code in separate PHP files for a clean project structure. For example, create a `bootstrap.php`:

```php
<?php
// {root}/bootstrap.php

/** @var \Ricotta\App\App $app */
$app->add(new MyModule());
``` 

Then load this file in your index:

```php
<?php
// {root}/webroot/index.php

require_once __DIR__ . '/vendor/autoload.php';

use Ricotta\App\App;

$app = new App();
$app->load(dirname(__DIR__) . '/bootstrap.php');
$app->run();
``` 

### Advanced Routing

Define routes with named placeholders and wildcards. Placeholder values are available via the `RouteResult` object.

```php
$app->routes['/show-product/{id}/*']->get(\MyModule\ShowProduct::class);
``` 

Example controller using advanced routing:

```php
<?php
namespace MyModule\V1;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ricotta\App\Web\Controller;
use Ricotta\App\Web\Routing\RouteResult;

class ShowProduct implements Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly RouteResult $routeResult
    ) {}

    public function dispatch(): ResponseInterface
    {
        $id = $this->routeResult->route?->parameters['id'];
        $wildcard = $this->routeResult->route?->wildcard;
        
        // Product lookup and response generation logic goes here.
    }
}
``` 

### Middleware

Requests and responses are processed through a stack of PSR-15 middleware. Define middleware as instances or container references, and configure the middleware stack using the component referenced by the `App::MIDDLEWARE_STACK` constant:

```php
<?php
use Ricotta\App\App;
use MyModule\CookieMiddleware;
use MyModule\SessionMiddleware;

$app->bootstrap[App::MIDDLEWARE_STACK]
    ->register()
    ->value([
        new CookieMiddleware(),
        $app->bootstrap[SessionMiddleware::class]->reference(),
    ]);
``` 

### Templates

Ricotta provides a modular, PHP-based template engine.

#### Registering a Package Path

Define a template folder for a package (ideally using a Composer package name):

```php
<?php
use Ricotta\App\Template\TemplateEngine;

$app->bootstrap[TemplateEngine::class]->configure(
    function (TemplateEngine $templates) {
        $templates->addPackagePath('vendor/name', '/path/to/vendor/name/templates');
    }
);
``` 

#### Creating a Template

Templates can be plain HTML or PHP script files:

```html
<!-- path/to/vendor/name/templates/frontpage.html -->
<html>
  <body>
    <h1>Hello world</h1>
  </body>
</html>
``` 

#### Rendering a Template

Render templates within your controller:

```php
<?php
namespace MyModule;

use Ricotta\App\Web\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Ricotta\App\Template\TemplateEngine;

class ShowFrontPage implements Controller
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly TemplateEngine $templateEngine
    ) {}

    public function dispatch(): ResponseInterface
    {
        $content = $this->templateEngine->render('frontpage', 'ricotta/app');
        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write($content);
        return $response;
    }
}
``` 

#### Overriding Templates

Multiple paths can be registered for the same package. The template engine scans the paths in reverse order of registration, using the first matching file:

```php
$app->bootstrap[TemplateEngine::class]->configure(
    function (TemplateEngine $templates) {
        $templates->addPackagePath('vendor/name', '/path/to/vendor/name/templates');
    }
);

$app->bootstrap[TemplateEngine::class]->configure(
    function (TemplateEngine $templates) {
        $templates->addPackagePath('vendor/name', '/path/to/vendor/extension/templates');
    }
);
``` 

#### Injecting Variables

Pass variables to your templates via the third parameter of the `render()` method:

```php
<?php
public function dispatch(): ResponseInterface
{
    $view = new View();
    $view->message = 'Hello World';

    $content = $this->templateEngine->render('frontpage', 'ricotta/app', ['view' => $view]);

    $response = $this->responseFactory->createResponse(200);
    $response->getBody()->write($content);
    return $response;
}
``` 

In your template (e.g., `frontpage.php`):

```php
<?php
/** @var View $view */
?>
<html>
  <body>
    <h1><?= $view->message ?></h1>
  </body>
</html>
``` 

#### Callback Templates

Define templates as callbacks. Callback parameters are automatically injected, making nested template rendering straightforward:

```php
<?php
// path/to/vendor/extension/templates/frontpage.php
return function (View $view, TemplateEngine $templateEngine) {
?>
<html>
  <body>
    <h1><?= $view->message ?></h1>
    <content>
      <?= $templateEngine->render('content', 'vendor/name') ?>
    </content>
  </body>
</html>
<?php
};
``` 

### Result Models

Controllers can return either a PSR-7 response or an instance of the `Result` interface, streamlining common response flows.

The interface includes:

```php
public function createResponse(\Ricotta\Container\Container $container): \Psr\Http\Message\ResponseInterface;
``` 

Ricotta provides several result models:

#### HTMLResult

Render responses based on templates:

```php
<?php
namespace MyModule;

use Ricotta\App\Web\Controller;
use Ricotta\App\Web\Result\HTMLResult;

class ShowFrontPage implements Controller
{
    public function dispatch(): HTMLResult
    {
        $view = new View();
        $view->message = 'Hello World';
        return new HTMLResult('frontpage', 'ricotta/app', ['view' => $view]);
    }
}
``` 

#### JSONResult

Generate JSON responses from data:

```php
<?php
namespace MyModule;

use Ricotta\App\Web\Controller;
use Ricotta\App\Web\Result\JSONResult;

class GetData implements Controller
{
    public function dispatch(): JSONResult
    {
        return new JSONResult(['data' => 'is encoded as json']);
    }
}
``` 

#### NotFoundResult

Return a friendly 404 page using the default not-found template:

```php
<?php
namespace MyModule;

use Ricotta\App\Web\Controller;
use Ricotta\App\Web\Result\HTMLResult;
use Ricotta\App\Web\Result\NotFoundResult;
use Ricotta\App\Web\Routing\RouteResult;

class GetProduct implements Controller
{
    public function __construct(
        private ProductRepository $repository,
        private RouteResult $routeResult
    ) {}

    public function dispatch(): mixed
    {
        $id = $this->routeResult->route?->parameters['id'] ?? '';
        $product = $this->repository->get($id);

        if ($product === null) {
            return new NotFoundResult();
        }

        return new HTMLResult('product-page', 'vendor/name', ['product' => $product]);
    }
}
``` 

### Error Handling

By default, ricotta/app displays a user-friendly error page that suppresses internal errors. To override this behavior, implement the `ErrorHandler` interface. Your custom error handler should log errors and return an appropriate PSR-7 response.

```php
<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Ricotta\App\Web\Error\ErrorHandler;

class LogErrorHandler implements ErrorHandler
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly LoggerInterface $logger
    ) {}

    public function handle(\Throwable $error): ResponseInterface
    {
        $this->logger->error($error->getMessage());

        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write('Internal Server Error');

        return $response;
    }
}
``` 

Register your custom error handler as the implementation for the `ErrorHandler` interface:

```php
<?php
use Ricotta\App\App;
use Ricotta\App\Web\Error\ErrorHandler;

$app->bootstrap[ErrorHandler::class]
    ->register()
    ->type(LogErrorHandler::class);
``` 

#### Debug Error Handler

Register the `Ricotta\App\Web\Error\DebugErrorHandler` class as the error handler to render errors on error pages in development environments.


```php
<?php
use Ricotta\App\App;
use Ricotta\App\Web\Error\ErrorHandler;
use Ricotta\App\Web\Error\DebugErrorHandler;

$app->bootstrap[ErrorHandler::class]
    ->register()
    ->type(DebugErrorHandler::class);
``` 