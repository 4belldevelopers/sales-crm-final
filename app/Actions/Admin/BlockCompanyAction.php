<?php

namespace App\Actions\Admin;

use App\Jobs\Auth\SyncUserStatusEsJob;
use App\Models\BlockLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * V2_login Phase 3.3 — toggle all active users of a company (active ↔ inactive).
 *
 * Only called by super-admin routes (SuperAdminCompanyController::checkActive).
 * ES sync is async via SyncUserStatusEsJob (no refresh:true — eventual consistency is fine).
 * Epoch bump is at the company level; EnsureOneDeviceLogin checks both user and company epochs.
 *
 * Returns the number of affected users.
 */
class BlockCompanyAction
{
    public function execute(int $companyId, string $newStatus, ?string $reason = null): int
    {
        $affected = DB::table('users')
            ->where('company_id', $companyId)
            ->where('working_status', 1)
            ->update(['status' => $newStatus]);

        if ($newStatus !== 'active') {
            // Bump company epoch — all sessions for any user in this company become invalid.
            Cache::put('company:epoch:' . $companyId, now()->timestamp, 7 * 24 * 3600);
        }

        // Async ES updateByQuery (bulk, no refresh:true).
        SyncUserStatusEsJob::dispatch($companyId, $newStatus)->onConnection('emails');

        BlockLog::create([
            'target_type' => 'company',
            'target_id'   => $companyId,
            'company_id'  => $companyId,
            'new_status'  => $newStatus,
            'actor_id'    => auth()->id(),
            'reason'      => $reason,
        ]);

        return $affected;
    }
}
