<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventFilterRequestsMiddleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!$request->isJson()) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $request->validate([
                'type' => ['bail', 'required', 'in:deposit,withdraw,transfer'],
                'amount' => ['bail', 'required', 'integer', 'gte:0'],
                'origin' => ['bail', 'required_if:type,withdraw', 'required_if:type,transfer', 'alpha_num'],
                'destination' => ['bail', 'required_if:type,deposit', 'required_if:type,transfer', 'alpha_num'],
            ]);
        } catch (ValidationException $e) {
            return response('0', Response::HTTP_BAD_REQUEST);
        }
        
        return $next($request);
    }
}
