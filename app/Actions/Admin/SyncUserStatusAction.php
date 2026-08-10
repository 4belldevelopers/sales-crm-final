<?php

namespace App\Actions\Admin;

use App\Helper\GeneralMethod;
use App\Models\BlockLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * V2_login Phase 3.3 — toggle a single user's status (active ↔ inactive).
 *
 * Tenancy: enforced by company_id guard in WHERE + abort_if($affected !== 1, 403).
 * Epoch:   bumped on deactivation so active sessions are invalidated on next request.
 * ES sync: delegates to GeneralMethod::userIndexingupload() (full re-index, sync).
 *
 * Wiring target: ServiceCompanyTeamController::statusUpdate() and teamDelete(),
 * ProductCompanyTeamController::statusUpdate(), StaffingCompanyTeamController::statusUpdate().
 * Currently wired inline (epoch bump only); full replacement in a follow-up PR.
 */
class SyncUserStatusAction
{
    public function execute(int $userId, int $companyId, string $newStatus, ?string $reason = null): void
    {
        $affected = DB::table('users')
            ->where('id', $userId)
            ->where('company_id', $companyId)
            ->limit(1)
            ->update([
                'status'         => $newStatus,
                'working_status' => $newStatus === 'active' ? 1 : 0,
            ]);

        abort_if($affected !== 1, 403, 'Cross-tenant status update denied.');

        if ($newStatus !== 'active') {
            // Bump epoch — EnsureOneDeviceLogin kicks this user on their next request.
            Cache::put('user:epoch:' . $userId, now()->timestamp, 7 * 24 * 3600);
        }

        // Full ES re-index (preserves current behavior of synchronous full document update).
        GeneralMethod::userIndexingupload($userId);

        BlockLog::create([
            'target_type' => 'user',
            'target_id'   => $userId,
            'company_id'  => $companyId,
            'new_status'  => $newStatus,
            'actor_id'    => auth()->id(),
            'reason'      => $reason,
        ]);
    }
}
