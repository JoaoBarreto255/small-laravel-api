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
        $actionCantHaveOrigin = $this->actionCantHaveOrigin($request->json('type'));
        $origin = $request->json('origin');
        if ($actionCantHaveOrigin && null === $origin) {
            return $next($request);
        }
        
        if ($actionCantHaveOrigin) {
            return \response('0', Response::HTTP_BAD_REQUEST);
        }

        if (empty($origin)) {
            return \response('0', Response::HTTP_BAD_REQUEST);
        }

        if ($this->bankDataManagerService->accountExists($origin)) {
            return $next($request);
        }

        return \response('0', Response::HTTP_NOT_FOUND);
    }

    protected function actionCantHaveOrigin(string $type): bool
    {
        return !in_array($type, ['withdraw', 'transfer'], true);
    }
}
