[![Tests](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml/badge.svg)](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml)

`ricotta/app`
=============

This is the main application for Ricotta. Use this to create a new Ricotta project.


## Roadmap

### 0.1.0

- [x] Basic application structure
- [x] Routing
- [x] Middleware
- [x] Controllers

### 0.2.0

- [x] Templating and view models
- [x] 404 and 500 pages
- [x] Error handling
- [ ] Result model
- [x] CodeSniffer
- [x] CodeBeautifier
- [x] Contributing guidelines
- [x] Code of conduct

### 0.3.0
- [ ] Input parsing
- [ ] Debug error handler
- [ ] Configuration

### 0.4.0
- [ ] CLI commands

### 0.5.0
- [ ] Skeleton project?
- [ ] Configuration Seeding

### 1.0.0

- [ ] Stable release
- [ ] Interface facades?
- [ ] Documentation

### 2.0.0

- [ ] Swoole support

## Installation

To install Ricotta, create a new composer project and require the `ricotta/app` package.

```bash
composer require ricotta/app
```

## Usage

### Index file

Set up an index file in your selected webroot folder of your project with the following contents:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Ricotta\App();

$app->run();
```

### Adding routes

Use the `App::$routes` property to register routes to controller classes:

```php
<?php
# {root}/webroot/index.php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Ricotta\App();

$app->routes['/']->get(MyModule\GetFrontPage::class);

$app->run();
```

### Controller classes

A controller must implement the `Ricotta\App\Web\Controller` interface.

The PSR-7 `ServerRequestInterface` object and the PSR-17 factory interfaces are available by default for
dependency injection to components and services.

Controllers do not need to be registered with the container, but are autowired.

```php
class GetFrontPage implements Controller
{
    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly StreamFactoryInterface $stream_factory,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $stream = $this->stream_factory->createStream("Hello FrontPage");

        return $this->response_factory->createResponse(200)->withBody($stream);
    }
}
```

### Dependency Injection

Services and components can be bootstrapped and configured for dependency injection using the bootstrapping
object on the `App` instance.

```php
$app->bootstrap['MyService::class']->register();
```

The bootstrapping object is an instance of the `ricotta/container` `Bootstrapping` class. See the [`ricotta/container` documentation](https://github.com/thomasnordahl-dk/container) for further information.

### Modules

Bootstrappings and routings can be gathered into module classes that implement the `Ricotta\App\Module` interface.

```php
<?php

class MyModule implements Ricotta\App\Module
{
    public function register(Ricotta\App\App $app): void
    {
        $app->routes['/']->get(MyModule\GetFrontPage::class);
        $app->bootstrap['MyService::class']->register();
    }
}
```

### Config files

Configurations can also be gathered in php script files that are loaded through the app. This is useful for
structuring project bootstrappings in a dedicated file instead of the index file.

```php
<?php
# {root}/bootstrap.php

/** @var Ricotta\App\App $app */

$app->add(new MyModule());

```

The bootstrapping file can then be loaded via the app.

```php
<?php
# {root}/webroot/index.php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Ricotta\App();

$app->load(dirname(__DIR__) . '/bootstrapping.php');

$app->run();
```

### Advanced routing

Routes can be defined with named placeholders for subpaths and a wildcard for the remainder of a dynamic url.

The placeholders are dynamic and the actual subpath is availabe under this name via the `Ricotta\App\Web\Routing\RouteResult` object's `$route` property.

The wildcard is also dynamic and can be looked up via the `$route` property on the `Ricotta\App\Web\Routing\RouteResult` object.

```php
$app->routes['/show-product/{id}/*']->get(MyModule\ShowProduct::class);
```


```php
namespace MyModule\V1;

class ShowProduct implements Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly Psr\Http\Message\ResponseFactoryInterface $responseFactory,
        private readonly Psr\Http\Factory\StreamFactoryInterface $streamFactory,
        private readonly Ricotta\App\Web\Routing\RouteResult $routeResult,
    ) {
    }

    public function dispatch(): Psr\Http\Message\ResponseInterface
    {
        $id = $this->routeResult->route?->parameters['id'];
        $wildcard = $this->routeResult->route?->wildcard;

        // Create response.
    }
```

### Middleware

The App passes requests and responses through a stack of PSR-15 middleware instances.

The list can be defined using actual instances or container references to the middleware class to resolve the middleware through the container when needed.

The list is referenced with the container component name defined with the constant `Ricotta\App\App::MIDDLEWARE_STACK`.

```php
$app->bootstrap[App::MIDDLEWARE_STACK]->register()->value([
    new CookieMiddleware(),
    $app->bootstrap[SessionMiddleware::class]->reference(),
]);
```

### Templates

The Ricotta App comes with a modular template engine. The engine is based on plain PHP.

#### Adding a package path

To render a template, a template folder needs to be registered in relation to a package name. The recommended
best practice is to use composer package names, but any string identifier is allowed.

```php
$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $templates) => $templates->addPackagePath('vendor/name', '/path/to/vendor/name/templates')
);
```

#### Creating a template

Templates can be plain HTML or a PHP script file.

```html
<!--path/to/vendor/name/templates/frontpage.html -->

<html>
    <body>
        <h1>Hello world</h1>
    </body>
</html>
```

#### Rendering a template

The template engine can then be used to render this file into a string result.

```php
class ShowFrontPage implements Controller
{
    public function __construct(
        private readonly Psr\Http\Message\ResponseFactoryInterface $responseFactory,
        private readonly Ricotta\App\Template\TemplateEngine $templateEngine,
    ) { }

    public function dispatch(): Psr\Http\Message\ResponseInterface
    {
        $content = $this->templateEngine->render('frontpage', 'ricotta/app');

        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write($content);

        return $response;
    }
```

#### Overriding templates

Multiple paths can be defined for searching for templates and the template engine will scan the paths for
the template file. The paths are scanned by the most recently added first. The first path to contain a matching file name is used.

```html
<!--path/to/vendor/extension/templates/frontpage.html -->

<html>
    <body>
        <h1>Hello override</h1>
    </body>
</html>
```

```php

$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $templates) => $templates->addPackagePath('vendor/name', '/path/to/vendor/name/templates')
);

$app->bootstrap[TemplateEngine::class]->configure(
    fn (TemplateEngine $templates) => $templates->addPackagePath('vendor/name', '/path/to/vendor/extension/templates')
);

```

#### Injecting variables

The variables can be injected via the 3rd argument to the `TemplateEngine::render()` method.

```php
public function dispatch(): Psr\Http\Message\ResponseInterface
{
    $view = new View();
    $view->message = 'Hello World';

    $content = $this->templateEngine->render('frontpage', 'ricotta/app', ['view' => $view]);

    $response = $this->responseFactory->createResponse(200);
    $response->getBody()->write($content);

    return $response;
}
```

```php
<?php
# path/to/vendor/extension/templates/frontpage.php
/** @var View $view */
?>
<html>
    <body>
        <h1><?=$view->message?></h1>
    </body>
</html>
```

#### Callback templates

Templates can be defined with callbacks. Callback functions have their arguments resolved via the injections
given to `TemplateEngine::render()` and the dependency injection container as well, which makes it useful for resolving services, like the `TemplateEngine` itself for nested templates.

```php
<?php
# path/to/vendor/extension/templates/frontpage.php
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

### Error Handling

By default the Ricotta app will display a nice error page that suppresses errors thrown via middleware or controllers.

This can be changed by registering a new implementation of the interface `Ricotta\App\Web\Error\ErrorHandler`.

The error handler should handle the error somehow and return a resulting PSR-7 response instance.

```php
class LogErrorHandler implements Ricotta\App\Web\Error\ErrorHandler
{
    public function __construct(
        private readonly Psr\Http\Factory\ResponseFactoryInterface $responseFactory,
        private readonly Psr\Log\LoggerInterface $logger,
    ) {}

    public function handle(\Throwable $error): Psr\Http\Message\ResponseInterface
    {
        $this->logger->error($error->getMessage());

        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write('Internal Server Error');

        return $response;
    }
}
```

The implementation should be registered as the actual type for the `Ricotta\App\Web\Error\ErrorHandler` interface.

```php
$app->bootstrap[Ricotta\App\Web\Error\ErrorHandler::class]->register()->type(LogErrorHandler::class);
```

### Example nginx configuration

```
server {
    listen       127.0.0.1:80;
    server_name  localhost;
    root         /home/my-user/project-folder/demo;
    index        index.php;

    location / {
        try_files $uri $uri/index.html /index.php?$query_string;
    }
    
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass   unix:/run/php/php8.4-fpm.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
