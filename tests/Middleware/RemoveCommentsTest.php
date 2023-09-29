<?php

namespace RafahSBorges\LaravelPageSpeed\Test\Middleware;

use RafahSBorges\LaravelPageSpeed\Middleware\RemoveComments;
use RafahSBorges\LaravelPageSpeed\Test\TestCase;

class RemoveCommentsTest extends TestCase
{
    protected $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->response = $this->middleware->handle($this->request, $this->getNext());
    }

    protected function getMiddleware()
    {
        $this->middleware = new RemoveComments();
    }

    public function testRemoveHtmlComments()
    {
        $this->assertStringNotContainsString(
            "<!-- Place favicon.ico in the root directory -->",
            $this->response->getContent()
        );

        $this->assertStringNotContainsString(
            "<!-- Add your site or application content here -->",
            $this->response->getContent()
        );

        $this->assertStringNotContainsString(
            "<!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<!--[if IE 8]> <html lang=\"en\" class=\"ie8 no-js\"> <![endif]-->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<!--[if IE 9]> <html lang=\"en\" class=\"ie9 no-js\"> <![endif]-->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<!--[if !IE]><!-->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<!--<![endif]-->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<!--[if lte IE 9]>
            <p class=\"browserupgrade\">You are using an <strong>outdated</strong> browser. Please <a href=\"https://browsehappy.com/\">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<p>Hello! I am /* not a comment */ at HTML context!</p>",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            "<p>Hello! I am // not a comment at HTML context!</p>",
            $this->response->getContent()
        );
    }

    public function testRemoveCssComments()
    {
        $this->assertStringNotContainsString(
            "/* before - css inline comment*/color: black;/* after - css inline comment*/",
            $this->response->getContent()
        );

        $this->assertStringNotContainsString(
            "/* This is
                a multi-line
                css comment */",
            $this->response->getContent()
        );

        $this->assertStringContainsString(
            '.laravel-page-speed {
                text-align: center;
                color: black;
            }',
            $this->response->getContent()
        );
    }

    public function testRemoveJsComments()
    {
        $this->assertStringNotContainsString(
            "// Single Line Comment",
            $this->response->getContent()
        );

        $this->assertStringNotContainsString(
            "/*

                Multi-line

                Comment

            */",
            $this->response->getContent()
        );

        $this->assertStringNotContainsString(
            "/* before - inline comment*/console.log('Speed!');// after - inline comment",
            $this->response->getContent()
        );

        $this->assertStringContainsString("console.log('Laravel');", $this->response->getContent());
        $this->assertStringContainsString("console.log('Page');", $this->response->getContent());
        $this->assertStringContainsString("console.log('Speed!');", $this->response->getContent());
    }
}
