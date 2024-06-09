<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EventFilterMissingOriginAccount;
use App\Http\Service\BankDataManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(EventFilterMissingOriginAccount::class)]
class EventFilterMissingOriginAccountTest extends TestCase
{
    protected function factoryMiddleare()
    {
        $cache = Cache::store('array');
        $cache->put('100', 100);

        return new EventFilterMissingOriginAccount(
            new BankDataManagerService($cache)
        );
    }
    
    #[DataProvider('handleDataProvider')]
    public function testHandle(array $requestBody, string $expectedContent, int $expectecStatus)
    {
        $middleware = $this->factoryMiddleare();
        $request = Request::create('/', content: json_encode($requestBody));
        $result = $middleware->handle($request, fn($input) => response('ok'));
        
        $this->assertSame($expectecStatus, $result->getStatusCode());
        $this->assertSame($expectedContent, $result->getContent());
    }
    
    public static function handleDataProvider(): array
    {

        
        return [
            // test events without "origin".
            [['type' => 'deposit'], 'ok', Response::HTTP_OK],
            [['type' => 'transfer'], '0', Response::HTTP_BAD_REQUEST],
            [['type' => 'withdraw'], '0', Response::HTTP_BAD_REQUEST],
            // test events with "origin".
            [['type' => 'deposit', 'origin' => 100], '0', Response::HTTP_BAD_REQUEST],

            [['type' => 'transfer', 'origin' => '100'], 'ok', Response::HTTP_OK],
            [['type' => 'transfer', 'origin' => 100], 'ok', Response::HTTP_OK],
            [['type' => 'transfer', 'origin' => 10], '0', Response::HTTP_NOT_FOUND],

            [['type' => 'withdraw', 'origin' => '100'], 'ok', Response::HTTP_OK],
            [['type' => 'withdraw', 'origin' => 100], 'ok', Response::HTTP_OK],
            [['type' => 'withdraw', 'origin' => 10], '0', Response::HTTP_NOT_FOUND],
        ];
    }
}
