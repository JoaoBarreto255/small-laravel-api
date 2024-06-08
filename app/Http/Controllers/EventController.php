<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController
{
    use BalanceAwareTrait;

    public function index(Request $request): Response
    {
        return match ($request->json('type')) {
            'deposit' => $this->deposity(
                $request->json('destination'),
                $request->json('amount'),
            ),
            'withdraw' => $this->withdraw(
                $request->json('origin'),
                $request->json('amount'),
             ),
            'transfer' => $this->transfer(
                $request->json('origin'),
                $request->json('destination'),
                $request->json('amount'),
             ),
             default => \response('0', Response::HTTP_BAD_REQUEST),
        };
    }

    protected function deposity(string|int $account, int $income): Response
    {
        return response(['destination' => [
            'id' => $account,
            'balance' => $this->incrementBalance($account, $income),
        ]], Response::HTTP_CREATED);
    }

    protected function withdraw(string|int $account, int $outcome): Response
    {
        if (Response::HTTP_OK !== ($code = $this->validateAccount($account))) {
            return response('0', $code);
        }

        return response(['origin' => [
            'id' => $account,
            'balance' => $this->decrementBalance($account, $outcome),
        ]], Response::HTTP_CREATED);
    }
    
    protected function transfer($origin, $destination, $amount): Response
    {
        if (Response::HTTP_OK !== ($code = $this->validateAccount($origin))) {
            return response('0', $code);
        }

        return response([
            'origin' => [
                'id' => $origin,
                'balance' => $this->decrementBalance($origin, $amount),
            ],
            'destination' => [
                'id' => $destination,
                'balance' => $this->incrementBalance($destination, $amount),
            ],
        ], Response::HTTP_CREATED);
    }

    protected function validateAccount($account): int
    {
        if (false !== $this->getBalance($account)) {
            return Response::HTTP_OK;
        }

        return Response::HTTP_NOT_FOUND;
    }
}
