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
    
    #[TestDox('Test getBalance')]
    public function testGetBalance()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);
        
        $this->assertSame(50, $service->getBalance('100'));
        $this->assertSame(50, $service->getBalance(100));
        $this->assertFalse($service->getBalance('101'));
        $this->assertFalse($service->getBalance(101));
    }

    #[TestDox('Test setBalance')]
    public function testSetBalance()
    {
        $service = new BankDataManagerService($this->cache);
        $service->setBalance('100', 50);
        $service->setBalance(10, 55);

        $this->assertSame(50, $this->cache->get('100'));
        $this->assertSame(55, $this->cache->get('10'));
    }

    #[TestDox('Test check if account exists')]
    public function testAccountExists()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);

        $this->assertTrue($service->accountExists('100'));
        $this->assertTrue($service->accountExists(100));
        $this->assertFalse($service->accountExists('10'));
        $this->assertFalse($service->accountExists(10));
    }

    #[TestDox('Test clear existent accounts')]
    public function testClearBalances()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);

        $this->assertSame(50, $this->cache->get('100'));
        $service->clearBalances();

        $this->assertNull($this->cache->get('100'));
    }

    #[TestDox('Test increment one account')]
    public function testIncrementBalance()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);

        $this->assertSame(50, $this->cache->get('100'));
        $service->incrementBalance(100, 25);
        $this->assertSame(75, $this->cache->get('100'));

        $this->assertNull($this->cache->get('10'));
        $service->incrementBalance(10, 25);
        $this->assertSame(25, $this->cache->get('10'));

        $this->assertSame(75, $this->cache->get('100'));
        $service->incrementBalance(100, -25);
        $this->assertSame(50, $this->cache->get('100'));

        $this->assertNull($this->cache->get('11'));
        $service->incrementBalance(11, -25);
        $this->assertSame(-25, $this->cache->get('11'));
    }

#[TestDox('Test decrement one account')]
    public function testDecrementBalance()
    {
        $this->cache->put('100', 50);
        $service = new BankDataManagerService($this->cache);

        $this->assertSame(50, $this->cache->get('100'));
        $service->decrementBalance(100, 25);
        $this->assertSame(25, $this->cache->get('100'));

        $this->assertNull($this->cache->get('10'));
        $service->decrementBalance(10, 25);
        $this->assertSame(-25, $this->cache->get('10'));

        $this->assertSame(25, $this->cache->get('100'));
        $service->decrementBalance(100, -25);
        $this->assertSame(50, $this->cache->get('100'));

        $this->assertNull($this->cache->get('11'));
        $service->decrementBalance(11, -25);
        $this->assertSame(25, $this->cache->get('11'));
    }
}
