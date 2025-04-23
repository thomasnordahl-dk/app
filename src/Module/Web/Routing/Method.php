<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Web\Routing;

/**
 * @internal
 */
enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';

    public static function fromString(string $method): Method
    {
        return match (strtoupper($method)) {
            Method::GET->value => Method::GET,
            Method::POST->value => Method::POST,
            Method::PUT->value => Method::PUT,
            Method::PATCH->value => Method::PATCH,
            Method::DELETE->value => Method::DELETE,
            Method::OPTIONS->value => Method::OPTIONS,
            Method::HEAD->value => Method::HEAD,
            default => throw new RouterException("Unknown method: {$method}"),
        };
    }
}
