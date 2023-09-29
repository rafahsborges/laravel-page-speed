<?php

namespace RafahSBorges\LaravelPageSpeed\Test\Middleware;

use Mockery as m;
use Illuminate\Http\Request;
use RafahSBorges\LaravelPageSpeed\Test\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use RafahSBorges\LaravelPageSpeed\Middleware\PageSpeed;
use RafahSBorges\LaravelPageSpeed\Middleware\CollapseWhitespace;

class ShouldNotProcessResponseTest extends TestCase
{
    /**
     * PageSpeed middleware instance.
     *
     * @var \RafahSBorges\LaravelPageSpeed\Middleware\PageSpeed
     */
    protected $middleware;

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    /**
     * Test that a BinaryFileResponse is ignored by any middleware.
     *
     * @return void
     */
    public function testSkipBinaryFileResponse()
    {
        $request = Request::create('/', 'GET', [], [], ['file' => new UploadedFile(__FILE__, 'foo.php')]);

        $response = $this->middleware->handle($request, $this->getNextBinaryFileResponse());

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    /**
     * Test that a StreamedResponse is ignored by any middleware.
     *
     * @return void
     */
    public function testSkipStreamedResponse()
    {
        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, $this->getNextStreamedResponse());

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /**
     * Test a LogicException is throw when trying to process a
     * BinaryFileResponse.
     *
     * @return void
     */
    public function testExpectLogicExceptionInBinaryFileResponse()
    {
        $this->expectException('LogicException');

        $request = Request::create('/', 'GET', [], [], ['file' => new UploadedFile(__FILE__, 'foo.php')]);

        $middleware = $this->mockMiddlewareWhichAllowsPageSpeedProcess();

        $middleware->handle($request, $this->getNextBinaryFileResponse());
    }

    /**
     * Test a LogicException is throw when trying to process a
     * StreamedResponse.
     *
     * @return void
     */
    public function testExpectLogicExceptionInStreamedResponse()
    {
        $this->expectException('LogicException');

        $request = Request::create('/', 'GET');

        $middleware = $this->mockMiddlewareWhichAllowsPageSpeedProcess();

        $middleware->handle($request, $this->getNextStreamedResponse());
    }

    /**
     * Mock a BinaryFileResponse.
     *
     * @return \Closure
     */
    protected function getNextBinaryFileResponse()
    {
        return function ($request) {
            return response()->download($request->file);
        };
    }

    /**
     * Mock a StreamedResponse.
     *
     * @return \Closure
     */
    protected function getNextStreamedResponse()
    {
        return function ($request) {
            $response = new StreamedResponse(function () {
                echo "I am Streamed";
            });

            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                'attachment',
                'foo.txt'
            ));

            return $response;
        };
    }

    /**
     * Return an instance of the middleware which always
     * allows processing of response.
     *
     * @return m\Mock|PageSpeed
     */
    protected function mockMiddlewareWhichAllowsPageSpeedProcess()
    {
        $mock = m::mock(CollapseWhitespace::class)
                 ->shouldAllowMockingProtectedMethods()
                 ->makePartial();

        $mock->shouldReceive('shouldProcessPageSpeed')
             ->once()
             ->andReturn(true);

        return $mock;
    }

    /**
     * Middleware used during this test.
     *
     * @return void
     */
    protected function getMiddleware()
    {
        $this->middleware = new CollapseWhitespace();
    }
}
