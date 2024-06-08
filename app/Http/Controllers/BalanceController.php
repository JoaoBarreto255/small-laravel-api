<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceController extends AbstractController
{
    public function index(Request $request): Response
    {
        if (null === ($id = $request->query->get('account_id'))) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }

        if (false === ($balance = $this->bankDataManagerService->getBalance($id))) {
            return response('0', Response::HTTP_NOT_FOUND);
        }

        return response((string) $balance);
    }
}
