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

$app->run();
```

## Roadmap

### 0.1.0

- [x] Basic application structure
- [x] Routing
- [ ] Middleware
- [x] Controllers

### 0.2.0

- [ ] Error handling
- [ ] 404 and 500 pages
- [ ] Debug mode

### 0.3.0

- [ ] Input parsing
- [ ] Templating and view models
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
