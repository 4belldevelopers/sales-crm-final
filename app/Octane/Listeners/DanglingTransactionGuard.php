<?php

namespace App\Octane\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DanglingTransactionGuard
 *
 * Listener for Octane worker lifecycle events (OperationTerminated / RequestTerminated).
 * Inspects PDO state when a request finishes to ensure no uncommitted DB::beginTransaction()
 * remains open on persistent worker connections. If an unhandled exception or early exit
 * leaves a transaction dangling, this guard safely rolls it back to prevent cross-request
 * or cross-tenant data leakage under high concurrency (10K concurrent SLA).
 */
class DanglingTransactionGuard
{
    /**
     * Handle the Octane operation/request terminated event.
     */
    public function handle(object $event): void
    {
        while (DB::transactionLevel() > 0) {
            $currentLevel = DB::transactionLevel();

            DB::rollBack();

            Log::warning('Octane Dangling Transaction Guard: Rolled back uncommitted database transaction', [
                'previous_level' => $currentLevel,
                'tenant_id' => auth()->user()?->company_id ?? null,
                'user_id' => auth()->id() ?? null,
                'request_uri' => request()?->getRequestUri() ?? 'N/A',
                'request_id' => request()?->headers->get('X-Request-Id') ?? null,
            ]);
        }

        // Flush request-scoped permission memoizer cache between Octane worker requests
        \App\Support\Auth\PermissionMemoizer::flush();
    }
}
