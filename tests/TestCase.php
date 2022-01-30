<?php

namespace Barryvdh\HttpCache\Tests;

use Barryvdh\HttpCache\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
