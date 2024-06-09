<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Service\BankDataManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Filter all event requests with invalid origin account
 */
readonly class EventFilterMissingOriginAccount
{
    public function __construct(
        protected BankDataManagerService $bankDataManagerService
    ) {
    }

    public function handle(Request $request, \Closure $next): Response
    {
        if (null === ($origin = $request->json('origin'))) {
            return $next($request);
        }
        
        $type = $request->json('type');
        if (!in_array($type, ['withdraw', 'transfer'], true)) {
            return \response('0', Response::HTTP_BAD_REQUEST);
        }

        if ($this->bankDataManagerService->accountExists($origin)) {
            return $next($request);
        }

        return \response('0', Response::HTTP_NOT_FOUND);
    }
}
