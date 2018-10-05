<?php
declare(strict_types=1);

namespace Middlewares\Tests;

use Exception;
use Middlewares\JsonExceptionHandler;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

use function WyriHaximus\throwable_json_decode;

class JsonExceptionHandlerTest extends TestCase
{
    public function testMiddleware()
    {
        $middleware = new JsonExceptionHandler();

        $handler = function ($request) {
            throw new Exception('Something went wrong');
        };

        $request = Factory::createServerRequest('GET', '/');
        $response = Dispatcher::run([
            $middleware,
            $handler,
        ], $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));

        $e = throwable_json_decode((string) $response->getBody());

        $this->assertSame('Something went wrong', $e->getMessage());
    }

    public function testContentType()
    {
        $middleware = new JsonExceptionHandler();
        $middleware->contentType($type = 'application/json;charset=utf-8');

        $handler = function ($request) {
            throw new Exception('Something went wrong');
        };

        $request = Factory::createServerRequest('GET', '/');
        $response = Dispatcher::run([
            $middleware,
            $handler,
        ], $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertSame($type, $response->getHeaderLine('Content-Type'));
    }

    public function testDisableTrace()
    {
        $middleware = new JsonExceptionHandler();
        $middleware->includeTrace(false);

        $handler = function ($request) {
            throw new Exception('Something went wrong');
        };

        $request = Factory::createServerRequest('GET', '/');
        $response = Dispatcher::run([
            $middleware,
            $handler,
        ], $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));

        $e = throwable_json_decode((string) $response->getBody());

        $this->assertEmpty($e->getTrace());
    }

    public function testJsonOptions()
    {
        $middleware = new JsonExceptionHandler();
        $middleware->jsonOptions(JSON_PRETTY_PRINT);

        $handler = function ($request) {
            throw new Exception('Something went wrong');
        };

        $request = Factory::createServerRequest('GET', '/');
        $response = Dispatcher::run([
            $middleware,
            $handler,
        ], $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJson((string) $response->getBody());
    }
}
