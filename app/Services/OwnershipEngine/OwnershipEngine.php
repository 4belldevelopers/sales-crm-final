<?php

namespace App\Services\OwnershipEngine;

use App\Services\OwnershipEngine\DataTransferObjects\OwnershipExplanation;
use App\Services\OwnershipEngine\DataTransferObjects\OwnershipResult;
use App\Services\OwnershipEngine\Models\OwnershipCluster;
use App\Services\OwnershipEngine\Models\OwnershipEvent;
use App\Services\OwnershipEngine\Rules\ER1_PrivateUntilSourced;
use App\Services\OwnershipEngine\Rules\ER2_CompanyReservation;
use App\Services\OwnershipEngine\Rules\ER3_AgencyOwnershipByFirstSourced;
use App\Services\OwnershipEngine\Rules\ER4_OwnershipNeverFlips;
use App\Services\OwnershipEngine\Rules\ER5_CompanyRowsAlwaysCompared;
use App\Services\OwnershipEngine\Rules\ER6_AgencyRowsPrivacyShieldedUntilSourced;
use App\Services\OwnershipEngine\Rules\ER7_ScopeDeterminesComparisonUniverse;
use App\Services\OwnershipEngine\Rules\ER8_MultiContenderGeneralization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The single, authoritative Ownership Engine. docs/OWNERSHIP_ENGINE_DESIGN.md.
 *
 * Phase 2 (current): core services, events, locking, transactions — this class and its
 * collaborators. NOT yet wired into any controller/Blade call site (Phase 3) and does not
 * yet replace the existing resolveDuplicateOwnership()/checkDuplicateCandidate() logic,
 * which keeps running unchanged as the production fallback until Phase 6 validation passes
 * (Implementation Rules #4/#5).
 *
 * §5.2: this is the ONLY class permitted to write ownership_clusters/ownership_events or the
 * ownership_state/ownership_cluster_id/ownership_evaluated_at cache columns on
 * add_job_wise_condidates. Nothing outside app/Services/OwnershipEngine may do so directly.
 */
class OwnershipEngine
{
    public function __construct(
        private readonly ER7_ScopeDeterminesComparisonUniverse $scopeRule,
        private readonly ER1_PrivateUntilSourced $er1,
        private readonly ER2_CompanyReservation $er2,
        private readonly ER3_AgencyOwnershipByFirstSourced $er3,
        private readonly ER4_OwnershipNeverFlips $er4,
        private readonly ER5_CompanyRowsAlwaysCompared $er5,
        private readonly ER6_AgencyRowsPrivacyShieldedUntilSourced $er6,
        private readonly ER8_MultiContenderGeneralization $er8,
    ) {
    }

    /**
     * ER2: a Company Recruiter row (thirdparty_company_id = 0) reserves ownership at
     * creation time if no agency has established ownership first. Trigger: candidate
     * creation on any "add to job" path (Upload, Similar Candidates, Internal Bench,
     * Agency Bench, Career Page, Copy-to-Other-Job) — see docs §6 event catalog.
     */
    public function attemptCompanyReservation(int $rowId, ?string $eventUuid = null): OwnershipResult
    {
        $row = $this->loadSubjectRow($rowId);

        if ((int) $row->thirdparty_company_id !== 0) {
            throw new \LogicException(
                "attemptCompanyReservation() called on an agency-submitted row (#{$rowId}); ".
                'this method is only for company-owned rows (thirdparty_company_id = 0). '.
                'Agency rows only ever compete via claimForAgencySourcing() — see ER1/ER2.'
            );
        }

        if (!$this->scopeRule->isEnabled((int) $row->scoping_company_id)) {
            return new OwnershipResult($row->id, null, null);
        }

        return $this->evaluate(
            row: $row,
            isCompanyRow: true,
            hasReachedSourcing: true, // company rows are never privacy-shielded (ER5)
            triggerSource: 'candidate_creation',
            establishRule: 'ER2',
            duplicateRule: 'ER5',
            eventUuid: $eventUuid,
        );
    }

    /**
     * ER3: absent a valid Company Reservation, the first agency row to reach Sourcing
     * becomes owner. Trigger: the InReview(8)->Sourcing(1) handoff — the ONLY point in the
     * whole ATS this fires from (client_stage_submit()'s existing hook, Phase 3). Never
     * called for the plain flow (stage_submit()) — there is no equivalent handoff there.
     */
    public function claimForAgencySourcing(int $rowId, ?string $eventUuid = null): OwnershipResult
    {
        $row = $this->loadSubjectRow($rowId);

        if ((int) $row->thirdparty_company_id === 0) {
            throw new \LogicException(
                "claimForAgencySourcing() called on a company-owned row (#{$rowId}); company ".
                'rows only ever reserve at creation via attemptCompanyReservation() — see ER1/ER2.'
            );
        }

        $this->er1->assertMayEvaluate(isCompanyRow: false, hasReachedSourcing: true, rowId: $rowId);

        if (!$this->scopeRule->isEnabled((int) $row->scoping_company_id)) {
            return new OwnershipResult($row->id, null, null);
        }

        return $this->evaluate(
            row: $row,
            isCompanyRow: false,
            hasReachedSourcing: true,
            triggerSource: 'stage_transition',
            establishRule: 'ER3',
            duplicateRule: 'ER6',
            eventUuid: $eventUuid,
        );
    }

    /**
     * Zero-join, zero-computation read (§19/§20): every UI row partial should read this —
     * or, per the standing decision in docs/CLIENT_JOB_STAGE_OWNERSHIP.md §8.4, simply read
     * the row's own ownership_state column directly once it's part of the page's existing
     * query. This method only exists for callers that don't already have the row loaded.
     * Deliberately does NOT join ownership_clusters — that richer read is explain()'s job.
     */
    public function getCachedState(int $rowId): ?OwnershipResult
    {
        $row = DB::table('add_job_wise_condidates')
            ->where('id', $rowId)
            ->select('id', 'ownership_state', 'ownership_cluster_id')
            ->first();

        if (!$row || $row->ownership_state === null) {
            return null;
        }

        return new OwnershipResult(
            rowId: (int) $row->id,
            state: (int) $row->ownership_state,
            clusterId: $row->ownership_cluster_id !== null ? (int) $row->ownership_cluster_id : null,
        );
    }

    /**
     * On-demand only (§18.4/§19) — never called during page rendering. 3-4 point lookups,
     * no event replay. §11/§26.
     */
    public function explain(int $rowId): OwnershipExplanation
    {
        $row = DB::table('add_job_wise_condidates')
            ->where('id', $rowId)
            ->select('id', 'ownership_state', 'ownership_cluster_id')
            ->first();

        if (!$row || $row->ownership_cluster_id === null) {
            return new OwnershipExplanation(
                rowId: $rowId,
                state: null,
                ownerRowId: null,
                ownerType: null,
                decisionRule: null,
                scenarioRef: null,
                decidedAt: null,
                comparedAgainstTimestamp: null,
                reason: 'No ownership decision has been recorded for this row yet — it is '.
                    'either private (an agency row that has not reached Sourcing) or has never '.
                    'been evaluated by the engine.',
            );
        }

        $cluster = OwnershipCluster::find($row->ownership_cluster_id);

        // The specific event that decided THIS row's own state (owner-establishing event if
        // this row is the owner, duplicate-marking event otherwise) — indexed lookup by
        // subject_row_id, not a cluster-wide replay.
        $event = OwnershipEvent::where('subject_row_id', $rowId)
            ->orderByDesc('event_time')
            ->first();

        $contenderCount = OwnershipEvent::where('cluster_id', $cluster?->id)
            ->distinct('subject_row_id')
            ->count('subject_row_id');

        $multiContenderNote = $this->er8->describe($contenderCount);

        $reason = $event?->reason ?? 'No event recorded for this row.';
        if ($multiContenderNote !== '') {
            $reason .= ' '.$multiContenderNote;
        }

        return new OwnershipExplanation(
            rowId: $rowId,
            state: $row->ownership_state !== null ? (int) $row->ownership_state : null,
            ownerRowId: $cluster?->owner_row_id,
            ownerType: $cluster?->owner_type,
            decisionRule: $event?->decision_rule,
            scenarioRef: $event?->scenario_ref,
            decidedAt: $cluster?->decided_at?->toDateTimeString(),
            comparedAgainstTimestamp: $cluster?->decided_at?->toDateTimeString(),
            reason: $reason,
        );
    }

    /**
     * On-demand only — the full, ordered event history for one cluster (§7.1). This is the
     * ONE read path that legitimately reads more than a handful of rows; still bounded by
     * cluster size (k), never by total system size (§18.5/§21).
     */
    public function history(int $clusterId): \Illuminate\Support\Collection
    {
        return OwnershipEvent::where('cluster_id', $clusterId)
            ->orderBy('event_time')
            ->orderBy('id')
            ->get();
    }

    /**
     * Shared write-path logic for both attemptCompanyReservation() and
     * claimForAgencySourcing() — the only place either method locks a row or writes an
     * event/cache update. Locks at most one ownership_clusters row per call (§8/§23).
     */
    private function evaluate(
        object $row,
        bool $isCompanyRow,
        bool $hasReachedSourcing,
        string $triggerSource,
        string $establishRule,
        string $duplicateRule,
        ?string $eventUuid,
    ): OwnershipResult {
        // Idempotency (§25): a given row can only meaningfully fire one event per trigger
        // type. Checked before acquiring any lock — a cheap, indexed short-circuit for
        // queue-retry / duplicate-request replay.
        $existing = OwnershipEvent::where('subject_row_id', $row->id)
            ->where('trigger_source', $triggerSource)
            ->first();
        if ($existing) {
            return $this->getCachedState($row->id) ?? new OwnershipResult($row->id, null, null);
        }

        $normalizedEmail = $row->email_normalized;
        $normalizedPhone = $row->phone_normalized;

        if ($normalizedEmail === null && $normalizedPhone === null) {
            // Nothing to key a cluster on — leave undetermined, write nothing. Should not
            // happen in practice (a candidate always has at least one contact field), but
            // the engine must not fabricate a cluster out of no identity signal at all.
            return new OwnershipResult($row->id, null, null);
        }

        [$scopeMode, $scopeKey] = $this->scopeRule->resolve((int) $row->scoping_company_id, (int) $row->job_id);

        return DB::transaction(function () use (
            $row, $isCompanyRow, $hasReachedSourcing, $triggerSource, $establishRule,
            $duplicateRule, $eventUuid, $normalizedEmail, $normalizedPhone, $scopeMode, $scopeKey
        ) {
            $cluster = $this->lockOrCreateCluster(
                (int) $row->scoping_company_id,
                $scopeMode,
                $scopeKey,
                $normalizedEmail,
                $normalizedPhone,
            );

            $uuidBytes = $eventUuid ?? Str::orderedUuid()->getBytes();
            $now = now();

            $establishes = $isCompanyRow
                ? $this->er2->wouldEstablishOwnership($cluster->owner_row_id)
                : $this->er3->wouldClaimOwnership($cluster->owner_row_id);

            if ($establishes) {
                $this->er4->assertNotOverwritingOwner($cluster->owner_row_id, $cluster->id);

                $ownerType = $isCompanyRow ? 'company' : 'agency';

                $event = OwnershipEvent::create([
                    'event_uuid' => $uuidBytes,
                    'cluster_id' => $cluster->id,
                    'scoping_company_id' => $row->scoping_company_id,
                    'scope_mode' => $scopeMode,
                    'scope_key' => $scopeKey,
                    'normalized_email' => $normalizedEmail,
                    'normalized_phone' => $normalizedPhone,
                    'subject_row_id' => $row->id,
                    'event_type' => 'OWNER_ESTABLISHED',
                    'decision_rule' => $establishRule,
                    'previous_owner_row_id' => null,
                    'new_owner_row_id' => $row->id,
                    'trigger_source' => $triggerSource,
                    'reason' => $this->establishedReason($establishRule, $ownerType, $row->id),
                    'event_time' => $now,
                ]);

                $cluster->update([
                    'owner_row_id' => $row->id,
                    'owner_type' => $ownerType,
                    'decision_rule' => $establishRule,
                    'decided_at' => $now,
                    'last_event_id' => $event->id,
                ]);

                $this->writeCache($row->id, OwnershipResult::STATE_OWNER, $cluster->id, $now);

                return new OwnershipResult(
                    rowId: $row->id,
                    state: OwnershipResult::STATE_OWNER,
                    clusterId: $cluster->id,
                    ownerRowId: $row->id,
                    ownerType: $ownerType,
                    decisionRule: $establishRule,
                    decidedAt: $now->toDateTimeString(),
                );
            }

            if ((int) $cluster->owner_row_id === (int) $row->id) {
                // Already the owner (defensive — should be caught by the idempotency check
                // above in every real path). No-op, no new event.
                return new OwnershipResult(
                    rowId: $row->id,
                    state: OwnershipResult::STATE_OWNER,
                    clusterId: $cluster->id,
                    ownerRowId: $cluster->owner_row_id,
                    ownerType: $cluster->owner_type,
                    decisionRule: $cluster->decision_rule,
                    decidedAt: $cluster->decided_at?->toDateTimeString(),
                );
            }

            $isDuplicate = $isCompanyRow
                ? $this->er5->isDuplicateAgainstOwner($cluster->owner_row_id, $row->id)
                : $this->er6->isDuplicateAgainstOwner($cluster->owner_row_id, $row->id, $hasReachedSourcing);

            if (!$isDuplicate) {
                // Only reachable if hasReachedSourcing were false, which ER1 already guards
                // against upstream — leave undetermined defensively rather than guess.
                return new OwnershipResult($row->id, null, $cluster->id);
            }

            $event = OwnershipEvent::create([
                'event_uuid' => $uuidBytes,
                'cluster_id' => $cluster->id,
                'scoping_company_id' => $row->scoping_company_id,
                'scope_mode' => $scopeMode,
                'scope_key' => $scopeKey,
                'normalized_email' => $normalizedEmail,
                'normalized_phone' => $normalizedPhone,
                'subject_row_id' => $row->id,
                'event_type' => 'DUPLICATE_MARKED',
                'decision_rule' => $duplicateRule,
                'previous_owner_row_id' => $cluster->owner_row_id,
                'new_owner_row_id' => null,
                'trigger_source' => $triggerSource,
                'reason' => $this->duplicateReason($duplicateRule, $cluster->owner_row_id, $row->id),
                'event_time' => $now,
            ]);

            // ER4: owner_row_id itself is never touched here — only last_event_id moves.
            $cluster->update(['last_event_id' => $event->id]);

            $this->writeCache($row->id, OwnershipResult::STATE_DUPLICATE, $cluster->id, $now);

            return new OwnershipResult(
                rowId: $row->id,
                state: OwnershipResult::STATE_DUPLICATE,
                clusterId: $cluster->id,
                ownerRowId: $cluster->owner_row_id,
                ownerType: $cluster->owner_type,
                decisionRule: $duplicateRule,
                decidedAt: $cluster->decided_at?->toDateTimeString(),
            );
        });
    }

    private function loadSubjectRow(int $rowId): object
    {
        $row = DB::table('add_job_wise_condidates as ajwc')
            ->join('job_applications as ja', 'ja.id', '=', 'ajwc.job_application_id')
            ->where('ajwc.id', $rowId)
            ->select(
                'ajwc.id',
                'ajwc.job_id',
                'ajwc.company_id as scoping_company_id', // the job-owning company (§24)
                'ajwc.thirdparty_company_id',
                'ajwc.job_application_id',
                'ja.email_normalized',
                'ja.phone_normalized',
            )
            ->first();

        if (!$row) {
            throw new \RuntimeException("add_job_wise_condidates row #{$rowId} not found (or its job_application_id is missing/invalid).");
        }

        return $row;
    }

    /**
     * Finds-or-creates the one ownership_clusters row for this identity+scope and locks it
     * for the remainder of the caller's transaction (§5/§8/§23). Handles the unique-
     * constraint race (§5): two concurrent first-time inserts for the same brand-new
     * identity — the loser catches the violation and re-selects the winner's row instead
     * of erroring.
     */
    private function lockOrCreateCluster(
        int $scopingCompanyId,
        int $scopeMode,
        int $scopeKey,
        ?string $email,
        ?string $phone,
    ): OwnershipCluster {
        $matchQuery = fn () => OwnershipCluster::where('scoping_company_id', $scopingCompanyId)
            ->where('scope_mode', $scopeMode)
            ->where('scope_key', $scopeKey)
            ->where(function ($q) use ($email, $phone) {
                if ($email !== null) {
                    $q->orWhere('normalized_email', $email);
                }
                if ($phone !== null) {
                    $q->orWhere('normalized_phone', $phone);
                }
            });

        $cluster = $matchQuery()->lockForUpdate()->first();
        if ($cluster) {
            return $cluster;
        }

        try {
            return OwnershipCluster::create([
                'scoping_company_id' => $scopingCompanyId,
                'scope_mode' => $scopeMode,
                'scope_key' => $scopeKey,
                'normalized_email' => $email,
                'normalized_phone' => $phone,
            ]);
        } catch (QueryException $e) {
            if (!$this->isDuplicateKeyViolation($e)) {
                throw $e;
            }

            // Another concurrent transaction won the race and created this cluster first —
            // re-select and lock the row it created instead of treating this as an error.
            return $matchQuery()->lockForUpdate()->firstOrFail();
        }
    }

    private function isDuplicateKeyViolation(QueryException $e): bool
    {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }

    private function writeCache(int $rowId, int $state, int $clusterId, \Illuminate\Support\Carbon $evaluatedAt): void
    {
        DB::table('add_job_wise_condidates')->where('id', $rowId)->update([
            'ownership_state' => $state,
            'ownership_cluster_id' => $clusterId,
            'ownership_evaluated_at' => $evaluatedAt,
        ]);
    }

    private function establishedReason(string $rule, string $ownerType, int $rowId): string
    {
        return match ($rule) {
            'ER2' => "Row #{$rowId} (Company Recruiter) reserved ownership at creation — no agency had reached Sourcing yet. ER2.",
            'ER3' => "Row #{$rowId} (Agency) is the first contender to reach Sourcing for this identity — no Company Recruiter had reserved it first. ER3.",
            default => "Row #{$rowId} ({$ownerType}) established ownership under rule {$rule}.",
        };
    }

    private function duplicateReason(string $rule, ?int $ownerRowId, int $rowId): string
    {
        return match ($rule) {
            'ER5' => "Row #{$rowId} (Company Recruiter) is compared against the existing owner (row #{$ownerRowId}) immediately, regardless of its own stage. ER5.",
            'ER6' => "Row #{$rowId} (Agency) just reached Sourcing but row #{$ownerRowId} already owns this identity. Ownership never flips. ER6/ER4.",
            default => "Row #{$rowId} is a duplicate of owner row #{$ownerRowId} under rule {$rule}.",
        };
    }
}
