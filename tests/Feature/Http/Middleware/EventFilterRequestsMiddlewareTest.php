<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EventFilterRequestsMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(EventFilterRequestsMiddleware::class)]
class EventFilterRequestsMiddlewareTest extends TestCase
{
    public function testHandlerNonJsonRequest()
    {
        $middleware = new EventFilterRequestsMiddleware();
        
        $result = $middleware->handle(Request::create('/event', content: 'OK'), fn($foo) => response('ok'));
        $this->assertSame('0', $result->getContent());
        $this->assertSame(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
    }
    
    #[DataProvider('successDataProvider')]
    public function testSuccessInHandle(Request $input)
    {
        $next = fn($req) => response('ok');
        $middleware = new EventFilterRequestsMiddleware();
        
        $result = $middleware->handle($input, $next);
        $this->assertSame('ok', $result->getContent());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }
    
    public static function successDataProvider(): array
    {
        return [
            [static::buildJsonRequest('{"type":"deposit", "destination":"100", "amount":10}')],
            [static::buildJsonRequest('{"type":"withdraw", "origin":"200", "amount":10}')],
            [static::buildJsonRequest('{"type":"transfer", "origin":"100", "amount":15, "destination":"300"}')],
        ];
    }
    
    public static function buildJsonRequest(string $content): Request
    {
        return Request::create(
            '/event',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $content,
        );
    }
}
