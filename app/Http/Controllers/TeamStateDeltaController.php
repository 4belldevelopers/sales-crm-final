<?php

namespace App\Http\Controllers;

use App\Services\TenantVersioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TeamStateDeltaController
 *
 * Implements INV-13: Strict Bounded Delta Reconciliation Protocol.
 *
 * Guarantees that clients reconnecting after being offline receive incremental state deltas,
 * bounded strictly by:
 * 1. MAX_DELTA_EVENTS = 50
 * 2. MAX_SERIALIZED_BYTES = 256 KB
 * 3. Immediate canonical snapshot fallback upon circuit breaker breach.
 */
class TeamStateDeltaController extends Controller
{
    public const MAX_DELTA_EVENTS = 50;
    public const MAX_SERIALIZED_BYTES = 262144; // 256 KB

    public function delta(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'UNAUTHENTICATED'], 401);
        }

        $companyId = (int) $user->company_id;
        $fromVersion = (int) $request->input('from_version', 0);
        $currentVersion = TenantVersioningService::getCurrentVersion($companyId, 'team');

        // Circuit Breaker 1: Version Gap > 50 or uninitialized version -> Snapshot fallback
        if ($fromVersion <= 0 || ($currentVersion - $fromVersion) > self::MAX_DELTA_EVENTS) {
            return response()->json([
                'status'          => 'snapshot',
                'current_version' => $currentVersion,
                'reason'          => 'GAP_EXCEEDS_CIRCUIT_BREAKER',
                'data'            => $this->getFullTeamSnapshot($companyId),
            ]);
        }

        // Fetch delta events from sys_outbox_events
        $events = DB::table('sys_outbox_events')
            ->where('tenant_id', $companyId)
            ->where('aggregate_type', 'team')
            ->where('aggregate_version', '>', $fromVersion)
            ->where('aggregate_version', '<=', $currentVersion)
            ->orderBy('aggregate_version', 'asc')
            ->limit(self::MAX_DELTA_EVENTS)
            ->get();

        $serialized = json_encode($events);

        // Circuit Breaker 2: Payload > 256 KB -> Snapshot fallback
        if (strlen((string) $serialized) > self::MAX_SERIALIZED_BYTES) {
            return response()->json([
                'status'          => 'snapshot',
                'current_version' => $currentVersion,
                'reason'          => 'PAYLOAD_BYTE_LIMIT_EXCEEDED',
                'data'            => $this->getFullTeamSnapshot($companyId),
            ]);
        }

        return response()->json([
            'status'          => 'delta',
            'from_version'    => $fromVersion,
            'current_version' => $currentVersion,
            'events'          => $events,
        ]);
    }

    protected function getFullTeamSnapshot(int $companyId): array
    {
        return [
            'members' => DB::table('users')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->select(['id', 'name', 'email', 'status', 'created_at'])
                ->get(),
            'invitations' => DB::table('team_invitations')
                ->where('company_id', $companyId)
                ->whereIn('status', ['PENDING', 'LINK_ACCESSED'])
                ->select(['id', 'email', 'role_id', 'status', 'expires_at'])
                ->get(),
        ];
    }
}
