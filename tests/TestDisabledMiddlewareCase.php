<?php

namespace RafahSBorges\LaravelPageSpeed\Test;

use RafahSBorges\LaravelPageSpeed\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestDisabledMiddlewareCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
