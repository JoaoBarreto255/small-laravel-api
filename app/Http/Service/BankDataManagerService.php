<?php

declare(strict_types=1);

namespace App\Http\Service;

use Illuminate\Contracts\Cache\Repository;

class BankDataManagerService
{
    public function __construct(
        protected readonly Repository $cache
    ) { }
    
    public function getBalance(int|string $id): int|false
    {
        $value = $this->cache->get((string) $id);
        if (null === $value) {
            return false;
        }

        return $value;
    }
    
    public function setBalance(int|string $id, int $balance): void
    {
        $this->cache->put((string) $id, $balance, new \DateInterval('P1Y'));
    }
    
    public function incrementBalance(int|string $id, int $amount): int
    {
        $old = $this->getBalance($id) ?: 0;
        $new = $old + $amount;
        $this->setBalance($id, $new);
        
        return $new;
    }
    
    public function decrementBalance(int|string $id, int $amount): int
    {
        return $this->incrementBalance($id, -$amount);        
    }
    
    public function accountExists(string|int $account): bool
    {
        return false !== $this->getBalance($account);
    }

    public function clearBalances(): bool
    {
        return $this->cache->clear();
    }
}
