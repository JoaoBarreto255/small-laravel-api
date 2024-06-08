<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ResetController extends AbstractController
{
    public function index(): Response
    {
        if ($this->bankDataManagerService->clearBalances()) {
            return response('OK');
        }

        return response('0', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
