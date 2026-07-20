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
use App\Services\OwnershipEngine\Support\FeatureFlags;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The single, authoritative Ownership Engine. docs/OWNERSHIP_ENGINE_DESIGN.md.
 *
 * Status: Phases 1-8 implemented, all additive/non-authoritative. Phase 3 wired
 * attemptCompanyReservation()/claimForAgencySourcing() into live trigger points (isolated by
 * try/catch at every call site). Phase 4 added backfillCandidate() for historical data.
 * Phase 8 added the kill switch (stops live processing unconditionally) and the
 * rebuild/verify/orphan-detection family. Per Finding 7 (supersedes Finding 6): live
 * processing itself is NOT gated by the global/company/job flag hierarchy — that hierarchy
 * is reserved for the not-yet-built authoritative-cutover mechanism
 * (FeatureFlags::isAuthoritative(), currently checked by nothing). The engine keeps building
 * real ownership_events/ownership_clusters/cache history in production continuously, so the
 * observation period isn't starting from an empty dataset. This engine does not yet replace
 * the existing resolveDuplicateOwnership()/checkDuplicateCandidate() logic, which keeps
 * running unchanged as the production fallback until explicit cutover approval
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
        private readonly FeatureFlags $flags,
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

        // Phase 8, Finding 7 (supersedes Finding 6): the kill switch is the only flag that
        // stops live processing — the global/company/job hierarchy no longer does (it's
        // reserved for the not-yet-built authoritative-cutover mechanism). Processing stays
        // unconditional so the engine keeps building real history in production.
        if ($this->flags->isKillSwitchActive()) {
            return new OwnershipResult($row->id, null, null);
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

        // Phase 8, Finding 7 (supersedes Finding 6): the kill switch is the only flag that
        // stops live processing — the global/company/job hierarchy no longer does (it's
        // reserved for the not-yet-built authoritative-cutover mechanism). Processing stays
        // unconditional so the engine keeps building real history in production.
        if ($this->flags->isKillSwitchActive()) {
            return new OwnershipResult($row->id, null, null);
        }

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
     * Phase 4 (docs/OWNERSHIP_ENGINE_DESIGN.md, "Historical Backfill", Finding 4) — NOT the
     * same operation as the "rebuild" family described in §10 (which replays already-recorded
     * events to fix a corrupted projection; not built). This synthesizes brand-new events for
     * rows that have zero events yet, because they predate Phase 3's live wiring.
     *
     * For the given row's identity+scope cluster: finds every member row, computes each
     * member's qualifying historical timestamp (company rows: created_at; agency rows: their
     * own first-reached-Sourcing timestamp from `timeline`, if any — rows that never reached
     * Sourcing stay undetermined, exactly as ER1 says live), skips any member that already has
     * an event (idempotent — safe to re-run, safe against a live event racing in), sorts the
     * remainder chronologically with a deterministic (timestamp, id) tiebreak, and replays
     * each through evaluateAt() in that order. Returns the OwnershipResult for every row
     * actually processed in this call (empty if the cluster was already fully covered, or if
     * the scoping company has app_duplicate = 0).
     *
     * @return OwnershipResult[]
     */
    public function backfillCandidate(int $rowId): array
    {
        // Phase 8, Finding 6: backfill is a deliberate, scoped, operator-invoked action — not
        // gated by the global/company/job flags (which would block legitimate historical-data
        // prep before flipping the live switch on) — but the kill switch stops everything
        // unconditionally, including a running backfill, with no exceptions.
        if ($this->flags->isKillSwitchActive()) {
            return [];
        }

        $row = $this->loadSubjectRow($rowId);

        if (!$this->scopeRule->isEnabled((int) $row->scoping_company_id)) {
            return [];
        }

        $normalizedEmail = $row->email_normalized;
        $normalizedPhone = $row->phone_normalized;
        if ($normalizedEmail === null && $normalizedPhone === null) {
            return [];
        }

        [$scopeMode, $scopeKey] = $this->scopeRule->resolve((int) $row->scoping_company_id, (int) $row->job_id);

        $memberRows = $this->findClusterMembers((int) $row->scoping_company_id, $scopeMode, $scopeKey, $normalizedEmail, $normalizedPhone);
        $sourcedTimestamps = $this->batchFetchFirstSourcedTimestamps($memberRows);

        $eligible = [];
        foreach ($memberRows as $member) {
            // Already has an event — live processing or a prior backfill run already covered
            // it. This is the idempotency guarantee that makes re-running this method safe.
            if (OwnershipEvent::where('subject_row_id', $member->id)->exists()) {
                continue;
            }

            $isCompanyRow = (int) $member->thirdparty_company_id === 0;
            if ($isCompanyRow) {
                $eligible[] = ['row' => $member, 'is_company_row' => true, 'at' => Carbon::parse($member->created_at)];
                continue;
            }

            $sourcedAt = $sourcedTimestamps[$member->id] ?? null;
            if ($sourcedAt !== null) {
                $eligible[] = ['row' => $member, 'is_company_row' => false, 'at' => Carbon::parse($sourcedAt)];
            }
            // else: this agency row never reached Sourcing historically — stays undetermined,
            // exactly as ER1 says live rows do; no event synthesized for it.
        }

        // Deterministic chronological order (§9.4/§22): explicit secondary sort key (row id)
        // so a genuine historical timestamp tie — likely at this precision, since the source
        // columns are second-precision `timestamp`, not the engine's own microsecond ones —
        // has a reproducible winner, not accidental query-order.
        usort($eligible, function ($a, $b) {
            if ($a['at']->eq($b['at'])) {
                return $a['row']->id <=> $b['row']->id;
            }

            return $a['at']->lt($b['at']) ? -1 : 1;
        });

        $results = [];
        foreach ($eligible as $entry) {
            $results[] = $this->evaluateAt(
                row: $entry['row'],
                isCompanyRow: $entry['is_company_row'],
                hasReachedSourcing: true,
                triggerSource: 'manual_rebuild',
                establishRule: $entry['is_company_row'] ? 'ER2' : 'ER3',
                duplicateRule: $entry['is_company_row'] ? 'ER5' : 'ER6',
                eventUuid: null,
                asOf: $entry['at'],
            );
        }

        return $results;
    }

    /**
     * Phase 8 (docs/OWNERSHIP_ENGINE_DESIGN.md, "Recovery / Rebuild family", Finding 4/6) —
     * the operation deferred in Finding 4: regenerates a cluster's owner/decision_rule/
     * decided_at and every member row's cache column by replaying its ALREADY-RECORDED,
     * immutable ownership_events — never creates a new event, unlike backfillCandidate().
     * Use when the projection (cluster/cache) is suspected stale or corrupted relative to
     * the event log, which remains the source of truth.
     *
     * @return OwnershipResult[]
     */
    public function rebuildCluster(int $clusterId): array
    {
        return DB::transaction(function () use ($clusterId) {
            $cluster = OwnershipCluster::where('id', $clusterId)->lockForUpdate()->first();
            if (!$cluster) {
                return [];
            }

            $events = OwnershipEvent::where('cluster_id', $clusterId)
                ->orderBy('event_time')
                ->orderBy('id')
                ->get();

            $ownerRowId = null;
            $decisionRule = null;
            $decidedAt = null;
            $lastEventId = $cluster->last_event_id;
            $memberStates = [];

            foreach ($events as $event) {
                $lastEventId = $event->id;

                if ($event->event_type === 'OWNER_ESTABLISHED' && $ownerRowId === null) {
                    $ownerRowId = (int) $event->new_owner_row_id;
                    $decisionRule = $event->decision_rule;
                    $decidedAt = $event->event_time;
                    $memberStates[$ownerRowId] = OwnershipResult::STATE_OWNER;
                } elseif ($event->event_type === 'DUPLICATE_MARKED') {
                    $memberStates[(int) $event->subject_row_id] = OwnershipResult::STATE_DUPLICATE;
                }
            }

            $ownerType = match ($decisionRule) {
                'ER2' => 'company',
                'ER3' => 'agency',
                default => null,
            };

            $cluster->update([
                'owner_row_id' => $ownerRowId,
                'owner_type' => $ownerType,
                'decision_rule' => $decisionRule,
                'decided_at' => $decidedAt,
                'last_event_id' => $lastEventId,
            ]);

            $results = [];
            foreach ($memberStates as $rowId => $state) {
                $this->writeCache($rowId, $state, $clusterId, now());

                $results[] = new OwnershipResult(
                    rowId: $rowId,
                    state: $state,
                    clusterId: $clusterId,
                    ownerRowId: $ownerRowId,
                    ownerType: $ownerType,
                    decisionRule: $state === OwnershipResult::STATE_OWNER ? $decisionRule : null,
                    decidedAt: $decidedAt instanceof Carbon ? $decidedAt->toDateTimeString() : $decidedAt,
                );
            }

            return $results;
        });
    }

    /** Convenience wrapper: "rebuild single candidate" — resolves the row's cluster first. */
    public function rebuildClusterForRow(int $rowId): array
    {
        $clusterId = DB::table('add_job_wise_condidates')->where('id', $rowId)->value('ownership_cluster_id');
        if ($clusterId === null) {
            return [];
        }

        return $this->rebuildCluster((int) $clusterId);
    }

    /** Chunked "rebuild company" — replays every cluster scoped to this company. Returns the number of clusters rebuilt. */
    public function rebuildCompany(int $companyId): int
    {
        $count = 0;

        OwnershipCluster::where('scoping_company_id', $companyId)
            ->orderBy('id')
            ->chunkById(200, function ($clusters) use (&$count) {
                foreach ($clusters as $cluster) {
                    $this->rebuildCluster($cluster->id);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Read-only — compares a cluster's current owner/cache state against what a fresh
     * rebuildCluster() replay would produce, without writing anything. Empty array = fully
     * consistent.
     *
     * @return string[]
     */
    public function verifyClusterConsistency(int $clusterId): array
    {
        $cluster = OwnershipCluster::find($clusterId);
        if (!$cluster) {
            return ["Cluster #{$clusterId} does not exist."];
        }

        $events = OwnershipEvent::where('cluster_id', $clusterId)->orderBy('event_time')->orderBy('id')->get();

        $expectedOwnerRowId = null;
        $expectedDecisionRule = null;
        $expectedMemberStates = [];

        foreach ($events as $event) {
            if ($event->event_type === 'OWNER_ESTABLISHED' && $expectedOwnerRowId === null) {
                $expectedOwnerRowId = (int) $event->new_owner_row_id;
                $expectedDecisionRule = $event->decision_rule;
                $expectedMemberStates[$expectedOwnerRowId] = OwnershipResult::STATE_OWNER;
            } elseif ($event->event_type === 'DUPLICATE_MARKED') {
                $expectedMemberStates[(int) $event->subject_row_id] = OwnershipResult::STATE_DUPLICATE;
            }
        }

        $issues = [];

        if ((int) $cluster->owner_row_id !== (int) $expectedOwnerRowId) {
            $issues[] = "Cluster #{$clusterId} owner_row_id is {$cluster->owner_row_id}, event history implies {$expectedOwnerRowId}.";
        }
        if ($cluster->decision_rule !== $expectedDecisionRule) {
            $issues[] = "Cluster #{$clusterId} decision_rule is {$cluster->decision_rule}, event history implies {$expectedDecisionRule}.";
        }

        if (!empty($expectedMemberStates)) {
            $actualStates = DB::table('add_job_wise_condidates')
                ->whereIn('id', array_keys($expectedMemberStates))
                ->pluck('ownership_state', 'id');

            foreach ($expectedMemberStates as $rowId => $expectedState) {
                $actual = $actualStates[$rowId] ?? null;
                if ((int) $actual !== $expectedState) {
                    $actualLabel = $actual === null ? 'null' : (string) $actual;
                    $issues[] = "Row #{$rowId} cache ownership_state is {$actualLabel}, event history implies {$expectedState}.";
                }
            }
        }

        return $issues;
    }

    /** Events whose cluster_id doesn't resolve to an existing cluster row. */
    public function detectOrphanEvents(int $limit = 1000): \Illuminate\Support\Collection
    {
        return DB::table('ownership_events as oe')
            ->leftJoin('ownership_clusters as oc', 'oc.id', '=', 'oe.cluster_id')
            ->whereNull('oc.id')
            ->select('oe.id', 'oe.cluster_id', 'oe.subject_row_id', 'oe.event_type', 'oe.event_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Clusters with zero events — see docs/OWNERSHIP_ENGINE_DESIGN.md Phase 8 "Risks
     * discovered": a currently-unreachable but theoretically-possible edge case in
     * evaluateAt()'s defensive no-write branch, closed by having a detector for it.
     */
    public function detectOrphanClusters(int $limit = 1000): \Illuminate\Support\Collection
    {
        return DB::table('ownership_clusters as oc')
            ->leftJoin('ownership_events as oe', 'oe.cluster_id', '=', 'oc.id')
            ->whereNull('oe.id')
            ->select('oc.id', 'oc.scoping_company_id', 'oc.scope_mode', 'oc.scope_key', 'oc.created_at')
            ->limit($limit)
            ->get();
    }

    /** Rows whose cache columns disagree with their own cluster's owner_row_id. */
    public function detectCacheInconsistencies(int $limit = 1000): \Illuminate\Support\Collection
    {
        return DB::table('add_job_wise_condidates as ajwc')
            ->join('ownership_clusters as oc', 'oc.id', '=', 'ajwc.ownership_cluster_id')
            ->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->whereColumn('ajwc.id', '=', 'oc.owner_row_id')
                        ->where('ajwc.ownership_state', '!=', OwnershipResult::STATE_OWNER);
                })->orWhere(function ($qq) {
                    $qq->whereColumn('ajwc.id', '!=', 'oc.owner_row_id')
                        ->where('ajwc.ownership_state', '!=', OwnershipResult::STATE_DUPLICATE);
                });
            })
            ->select('ajwc.id', 'ajwc.ownership_state', 'oc.owner_row_id', 'oc.id as cluster_id')
            ->limit($limit)
            ->get();
    }

    /**
     * Shared write-path logic for both attemptCompanyReservation() and
     * claimForAgencySourcing() — the only place either method locks a row or writes an
     * event/cache update. Locks at most one ownership_clusters row per call (§8/§23).
     * Thin wrapper over evaluateAt() using "now" — unchanged Phase 3 behavior/signature.
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
        return $this->evaluateAt(
            $row, $isCompanyRow, $hasReachedSourcing, $triggerSource,
            $establishRule, $duplicateRule, $eventUuid, now(),
        );
    }

    /**
     * Phase 4 (docs/OWNERSHIP_ENGINE_DESIGN.md, Finding 4): the actual decision logic,
     * parameterized by an explicit timestamp — shared, unmodified, by both live processing
     * (via evaluate() above, always "now") and backfillCandidate() (a real historical
     * timestamp). This is what guarantees backfilled data can never disagree with what live
     * processing would have produced for the same historical facts — there is only one
     * implementation of ER2–ER6, not two.
     */
    private function evaluateAt(
        object $row,
        bool $isCompanyRow,
        bool $hasReachedSourcing,
        string $triggerSource,
        string $establishRule,
        string $duplicateRule,
        ?string $eventUuid,
        Carbon $asOf,
    ): OwnershipResult {
        // Idempotency (§25, broadened per Phase 4 Finding 4): a row's thirdparty_company_id
        // never changes after creation, so it only ever qualifies for exactly one of the two
        // trigger types in its whole lifetime — checking for ANY existing event (not just one
        // matching this exact trigger_source) is therefore safe for live callers (unchanged
        // observable behavior) and is what makes backfill-vs-live races safe: a row already
        // processed live is never re-processed by backfill, and vice versa.
        $existing = OwnershipEvent::where('subject_row_id', $row->id)->first();
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
            $duplicateRule, $eventUuid, $normalizedEmail, $normalizedPhone, $scopeMode, $scopeKey, $asOf
        ) {
            $cluster = $this->lockOrCreateCluster(
                (int) $row->scoping_company_id,
                $scopeMode,
                $scopeKey,
                $normalizedEmail,
                $normalizedPhone,
            );

            $uuidBytes = $eventUuid ?? Str::orderedUuid()->getBytes();
            $now = $asOf;

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
     * Phase 4 — every add_job_wise_condidates row sharing this identity within this scope,
     * mirroring exactly the OR-based email/phone match lockOrCreateCluster() uses live. Used
     * only by backfillCandidate(); the live write path never needs "all members," only its
     * own single cluster-row lock.
     */
    private function findClusterMembers(int $scopingCompanyId, int $scopeMode, int $scopeKey, ?string $email, ?string $phone): \Illuminate\Support\Collection
    {
        $query = DB::table('add_job_wise_condidates as ajwc')
            ->join('job_applications as ja', 'ja.id', '=', 'ajwc.job_application_id')
            ->select(
                'ajwc.id',
                'ajwc.job_id',
                'ajwc.company_id as scoping_company_id',
                'ajwc.thirdparty_company_id',
                'ajwc.job_application_id',
                'ajwc.created_at',
                'ja.email_normalized',
                'ja.phone_normalized',
            );

        if ($scopeMode === \App\Services\OwnershipEngine\Support\ScopeResolver::MODE_COMPANY_OVERALL) {
            $query->where('ajwc.company_id', $scopingCompanyId);
        } else {
            $query->where('ajwc.job_id', $scopeKey);
        }

        $query->where(function ($q) use ($email, $phone) {
            if ($email !== null) {
                $q->orWhere('ja.email_normalized', $email);
            }
            if ($phone !== null) {
                $q->orWhere('ja.phone_normalized', $phone);
            }
        });

        return $query->get();
    }

    /**
     * Phase 4 — batched "first ever reached Sourcing" lookup across every member of a
     * cluster, from `timeline` (the same historical source and the same batched-query shape
     * the original, pre-engine resolveDuplicateOwnership() always used — confirmed via
     * docs/OWNERSHIP_ENGINE_DESIGN.md's Revision 1 §3 index note). Resolved dynamically via
     * `master_status.id = 1`'s own name rather than assuming the literal string "Sourcing".
     *
     * @return array<int, string> add_job_wise_condidates.id => timestamp string
     */
    private function batchFetchFirstSourcedTimestamps(\Illuminate\Support\Collection $memberRows): array
    {
        if ($memberRows->isEmpty()) {
            return [];
        }

        $sourcingLabel = DB::table('master_status')->where('id', 1)->value('name');
        if (!$sourcingLabel) {
            return [];
        }

        $pairs = $memberRows->map(fn ($r) => [$r->job_application_id, $r->job_id])
            ->unique(fn ($p) => $p[0].':'.$p[1]);

        $timelineRows = DB::table('timeline')
            ->select('application_id', 'job_id', DB::raw('MIN(create_at) as first_sourced_at'))
            ->where('master_process_name', $sourcingLabel)
            ->where(function ($q) use ($pairs) {
                foreach ($pairs as [$appId, $jobId]) {
                    $q->orWhere(function ($qq) use ($appId, $jobId) {
                        $qq->where('application_id', $appId)->where('job_id', $jobId);
                    });
                }
            })
            ->groupBy('application_id', 'job_id')
            ->get()
            ->keyBy(fn ($r) => $r->application_id.':'.$r->job_id);

        $result = [];
        foreach ($memberRows as $member) {
            $key = $member->job_application_id.':'.$member->job_id;
            if (isset($timelineRows[$key])) {
                $result[$member->id] = $timelineRows[$key]->first_sourced_at;
            }
        }

        return $result;
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
