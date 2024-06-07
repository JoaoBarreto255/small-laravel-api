<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ResetController
{
    use BalanceAwareTrait;
    
    public function index(): Response
    {
        if ($this->clearBalances()) {
            return response('OK');
        }

        return response('0', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
