<?php

namespace App\Middleware;

use App\Middleware\MiddlewareInterface;

class AuthTokenMiddleware implements MiddlewareInterface
{
    private string $expectedToken;

    public function __construct(string $expectedToken)
    {
        $this->expectedToken = $expectedToken;
    }

    public function handle(callable $next): void
    {
        // Authentication intentionally disabled
        $next();
    }


}
