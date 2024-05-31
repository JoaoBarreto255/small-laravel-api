<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait BalanceAwareTrait
{
    protected function getBalance(int|string $id): int|false
    {
        $value = Cache::store('file')->get((string) $id);
        if (null === $value) {
            return false;
        }

        return $value;
    }

    protected function setBalance(int|string $id, int $balance): void
    {
        Cache::store('file')->put((string) $id, $balance, new \DateInterval('P1Y'));
    }
    
    protected function incrementBalance(int|string $id, int $amount): int
    {
        $old = $this->getBalance($id) ?: 0;
        $new = $old + $amount;
        $this->setBalance($id, $new);
        
        return $new;
    }
    
    protected function decrementBalance(int|string $id, int $amount): int
    {
        return $this->incrementBalance($id, -$amount);        
    }

    protected function clearBalances(): bool
    {
        return Cache::store('file')->clear();
    }
}
