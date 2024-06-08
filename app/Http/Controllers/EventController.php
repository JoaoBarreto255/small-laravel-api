<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends AbstractController
{
    public function index(Request $request): Response
    {
        $result = [];
        $amount = $request->json('amount');

        if ($origin = $request->json('origin')) {
            $result = [
                'origin' => [
                    'id' => $origin,
                    'balance' => $this->bankDataManagerService->decrementBalance($origin, $amount),
                ],
            ];
        }

        if ($destination = $request->json('destination')) {
            $result['destination'] = [
                'id' => $destination,
                'balance' => $this->bankDataManagerService->incrementBalance($destination, $amount),
            ];
        }

        return \response($result, Response::HTTP_CREATED);
    }
}
