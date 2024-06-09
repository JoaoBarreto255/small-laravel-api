<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Service;

use App\Http\Service\BankDataManagerService;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(BankDataManagerService::class)]
class BankDataManagerServiceTest extends TestCase
{
    protected $cache;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cache = Cache::store('array');
    }
    
    #[TestDox('Test BankDataManagerService getBalance')]
    public function testGetBalance()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);
        
        $this->assertSame(50, $service->getBalance('100'));
        $this->assertSame(50, $service->getBalance(100));
        $this->assertFalse($service->getBalance('101'));
        $this->assertFalse($service->getBalance(101));
    }
}
