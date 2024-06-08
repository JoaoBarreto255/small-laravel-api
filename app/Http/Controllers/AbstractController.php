<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Service\BankDataManagerService;

abstract class AbstractController
{
    public function __construct(protected BankDataManagerService $bankDataManagerService)
    {
        
    }
}
