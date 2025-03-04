[![Tests](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml/badge.svg)](https://github.com/thomasnordahl-dk/app/actions/workflows/tests.yml)

`ricotta/app`
=============

This is the main application for Ricotta. Use this to create a new Ricotta project.

## Installation

To install Ricotta, create a new composer project and require the `ricotta/app` package.

```bash
composer require ricotta/app
```

## Usage

Set up an index file in the root of your project with the following contents:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Ricotta\App();

$app->add(new UserModule());

$app->routes['/']->get(MyModule\GetFrontPage::class);
$app->routes['/api/v1/{collection}/{id}']->get(MyModule\V1\GetEntity::class);
$app->routes['/show-product/{id}/*']->get(MyModule\ShowProduct::class);

$app->bootstrap[App::MIDDLEWARE_STACK]->register()->value([
    $app->bootstrap[Cookiemiddleware::class]->reference(),
    $app->bootstrap[SessionMiddleware::class]->reference(),
]);

$app->run();
```

```php
namespace MyModule

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
```

```php
namespace MyModule\V1;

class GetEntity implements Controller
{
    public function __construct(
        private readonly EntityRepository $entityRepository
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly RouteResult $routeResult,
    ) {
    }

    public function dispatch(): ResponseInterface
    {
        $collection $this->routeResult->parameters['collection'];
        $id = $this->routeResult->parameters['id'];

        $entity = $this->entityRepository->get($collection, $id);

        $stream = $this->stream_factory->createStream(json_encode($entity));

        return $this->response_factory->createResponse(200)->withBody($stream);
    }
```

### Example nginx configuration

```
server {
    listen       127.0.0.1:80;
    server_name  localhost;
    root         /home/my-user/project-folder/demo;
    index        index.php;

    location /favicon.ico {
        try_files $uri =404;
    }
    
    location / {
        try_files $uri $uri/index.html /index.php?$query_string;
    }
    
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass   unix:/run/php/php8.4-fpm.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        fastcgi_buffering off;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        include fastcgi_params;
    }
}
```

## Roadmap

### 0.1.0

- [x] Basic application structure
- [x] Routing
- [x] Middleware
- [x] Controllers

### 0.2.0

- [x] Templating and view models
- [x] 404 and 500 pages
- [ ] Error handling

### 0.3.0

- [ ] Debug mode
- [ ] Input parsing
- [ ] Configuration

### 0.4.0
- [ ] CLI commands

### 0.5.0
- [ ] Skeleton project
- [ ] Configuration Seeding

### 1.0.0

- [ ] Stable release
- [ ] Contributing guidelines
- [ ] Documentation

### 2.0.0

- [ ] Swoole support
