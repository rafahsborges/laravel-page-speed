<?php

namespace RafahSBorges\LaravelPageSpeed\Test;

use Illuminate\Http\Request;
use RafahSBorges\LaravelPageSpeed\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected $html;
    protected $middleware;
    protected $request;

    abstract protected function getMiddleware();

    public function setUp(): void
    {
        parent::setUp();

        $this->getMiddleware();

        $this->html = $this->getBoilerplateHTML();
        $this->request = new Request();
    }

    private function getBoilerplateHTML()
    {
        return file_get_contents(__DIR__ .'/Boilerplate/index.html');
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getNext()
    {
        $response = (new \Illuminate\Http\Response($this->html));
        return function () use ($response) {
            return $response;
        };
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        config(['laravel-page-speed.enable' => true]);
        config(['laravel-page-speed.skip' => []]);
    }
}
