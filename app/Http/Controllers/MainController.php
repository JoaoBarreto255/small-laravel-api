<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MainController
{
    use BalanceAwareTrait;
    public function index(Request $request): Response
    {
        if (null === ($id = $request->query->get('account_id'))) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }

        if (false === ($balance = $this->getBalance($id))) {
            return response('0', Response::HTTP_NOT_FOUND);
        }

        return response((string) $balance);
    }

    public function event(Request $request): Response
    {
        if (!$request->isJson()) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }

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

    protected function deposity($account, $income): Response
    {
        if (Response::HTTP_OK !== $this->validateAccount($account, false) || !is_numeric($income)) {
            return response('0', Response::HTTP_BAD_REQUEST); 
        }

        return response(['destination' => [
            'id' => $account,
            'balance' => $this->incrementBalance($account, (int) $income),
        ]], Response::HTTP_CREATED);
    }

    protected function withdraw($account, $outcome): Response
    {
        if (Response::HTTP_OK !== ($code = $this->validateAccount($account))) {
            return response('0', $code);
        }

        if (!is_numeric($outcome)) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }

        return response(['origin' => [
            'id' => $account,
            'balance' => $this->decrementBalance($account, (int) $outcome),
        ]], Response::HTTP_CREATED);
    }
    
    protected function transfer($origin, $destination, $amount): Response
    {
        if (Response::HTTP_OK !== ($code = $this->validateAccount($origin))) {
            return response('0', $code);
        }
        
        if (Response::HTTP_OK !== ($code = $this->validateAccount($destination, false))) {
            return response('0', $code);
        }
        
        if (!is_numeric($amount)) {
            return response('0', Response::HTTP_BAD_REQUEST);
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

    protected function validateAccount($account, bool $checkIsCreated = true): int
    {
        if (!is_numeric($account) && !is_string($account)) {
            return Response::HTTP_BAD_REQUEST;
        }

        if (!$checkIsCreated || false !== $this->getBalance($account)) {
            return Response::HTTP_OK;
        }

        return Response::HTTP_NOT_FOUND;
    }
}
