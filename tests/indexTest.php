<?php

use Framework\Simplex;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class IndexTest extends TestCase
{
    protected Simplex $framework;

    protected function setUp(): void
    {
        $this->framework = new Simplex;
    }

    public function testHello()
    {
        $request = Request::create('/hello/lior');

        $response = $this->framework->handle($request);

        $this->assertEquals('Hello lior', $response->getContent());
    }

    public function testBye()
    {
        $request = Request::create('/bye');

        $response = $this->framework->handle($request);

        $this->assertEquals('<h1>Goodbye !</h1>', $response->getContent());
    }
}