<?php

namespace Barryvdh\HttpCache\Tests;

use Barryvdh\HttpCache\Middleware\CacheRequests;
use Barryvdh\StackMiddleware\TerminableClosureMiddleware;

class CacheTest extends TestCase
{
    public function testResolveMiddleware(): void
    {
        /** @var CacheRequests $middleware */
        $middleware = app(CacheRequests::class);

        $this->assertInstanceOf(TerminableClosureMiddleware::class, $middleware);
    }

}
