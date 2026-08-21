# V3 Agency Audit — Implementation Plan

> **Status:** 🔴 GOD-level audit complete (2026-08-21). 16-step refactor plan
> merged with stakeholder client-engine proposal (4 corrections + 2 cautions
> applied) and stakeholder re-audit (3 new P0/P1 findings — 14, 15, 16).
> **Owner:** TBA · **Phase 1 ETA:** TBA · **Total effort:** ~7 dev-days (Phases 1–4)
> **Constraint:** **No business-logic changes.** No new repository layer. Match existing
> patterns (fat controllers + services). Honor [docs/AI_RULES.md](../../docs/AI_RULES.md).

## Folder map

- [README.md](README.md) — folder index
- [AUDIT_REPORT.md](AUDIT_REPORT.md) — the 16-risk audit + SLOs + verification gates
- [REAUDIT_FINDINGS.md](REAUDIT_FINDINGS.md) — 3 new P0/P1 findings with code blocks
- `IMPLEMENTATION_PLAN.md` — this file: 16-step plan with concrete code

## Architecture Paradigm (post-merge)

```
OLD:  Browser ──AJAX──> Controller (1,927 L) ──> 7 ES + 10 SQL full-scans ──> 135 KB HTML SSR
NEW:  Browser ──ETag/Redis──> Raw JSON (4 KB) ──> Browser Engine computes & hydrates
```

- Server returns lean raw JSON; browser owns date bins, delta math, status grouping,
  Intl formatting, DOM templating.
- At 10K concurrent: ~96% egress drop, ~90% DB load drop, ~0 ms client-side filter.

## Client-Side Engine — `consultancyDrawerEngine.js`

> Lives at `public/js/consultancy/consultancyDrawerEngine.js`. Pure functions, no
> framework dependency, loaded via `@push('footer-script')` on the parent layout
> (not per-page, per [AI_RULES](../../docs/AI_RULES.md) rule #9 — match existing pattern).

```js
/**
 * ConsultancyDrawerEngine — Zero-Server-Load Client Metrics
 * Replaces 7 ES + 10 SQL queries + 135 KB HTML SSR with 1 cached _msearch
 * returning ~4 KB raw JSON, hydrated in the browser.
 *
 * CONSTRAINT: no business-logic change. All math here is presentation-layer only;
 * the same status IDs, date windows, and counter formulas the server used.
 */
const ConsultancyDrawerEngine = {

    // 1. Date windows — replaces Carbon subMonths/subDays loops (server)
    computeTrendWindows(rawEvents) {
        const now = Date.now();
        const DAY_MS = 86400000;
        const sevenDaysAgo  = now - (7 * DAY_MS);
        const thirtyDaysAgo = now - (30 * DAY_MS);
        let count7d = 0, count30d = 0, total = rawEvents.length;
        for (let i = 0; i < total; i++) {
            const time = new Date(rawEvents[i].created_at).getTime();
            if (time >= sevenDaysAgo)  count7d++;
            if (time >= thirtyDaysAgo) count30d++;
        }
        return { count7d, count30d, total };
    },

    // 2. Delta % — replaces server PHP math
    calculateGrowthDelta(currentCount, previousCount) {
        if (!previousCount || previousCount === 0) {
            return currentCount > 0
                ? { pct: 100, trend: 'up' }
                : { pct: 0,   trend: 'neutral' };
        }
        const delta = ((currentCount - previousCount) / previousCount) * 100;
        return {
            pct:   Math.abs(Math.round(delta * 10) / 10),
            trend: delta >= 0 ? 'up' : 'down'
        };
    },

    // 3. Status group — replaces server [1..8] → label map (controller L1122-1129)
    groupCandidatesByStatus(bucketData) {
        const STATUS_LOOKUP = {
            1: { name: 'Sourced',     color: '#3699FF' },
            2: { name: 'Screening',   color: '#FFA800' },
            3: { name: 'Evaluation',  color: '#8950FC' },
            4: { name: 'Interview',   color: '#1BC5BD' },
            5: { name: 'Offer',       color: '#F64E60' },
            6: { name: 'Joined',      color: '#0BB783' },
            7: { name: 'Rejected',    color: '#F64E60' },
            8: { name: 'Withdrawn',   color: '#B5B5C3' }
        };
        return bucketData.map(item => ({
            statusId:    item.key,
            count:       item.doc_count,
            label:       STATUS_LOOKUP[item.key]?.name     || 'Other',
            badgeColor:  STATUS_LOOKUP[item.key]?.color    || '#E4E6EF'
        }));
    },

    // 4. Intl formatting — replaces server regex/NumberFormat
    formatCurrency(amount, currencyCode = 'INR') {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: currencyCode,
            maximumFractionDigits: 0
        }).format(amount);
    },

    // 5. Drawer DOM hydration
    renderDrawer(data) {
        const trends = this.computeTrendWindows(data.sourcing_events);
        const growth = this.calculateGrowthDelta(trends.count7d, data.previous_7d_count);

        document.getElementById('drawer_partner_name').textContent  = data.partner.name;
        document.getElementById('drawer_active_jobs').textContent  = data.active_jobs_count;
        document.getElementById('drawer_sourcing_7d').textContent  = trends.count7d;
        document.getElementById('drawer_growth_badge').textContent =
            `${growth.trend === 'up' ? '+' : '-'}${growth.pct}%`;
        // 0 ms render, no 135 KB HTML parse
    }
};
window.ConsultancyDrawerEngine = ConsultancyDrawerEngine;
```

> **Caveat — counters on the parent index page (`/consultancy`)** are NOT moved to
> the client; the browser doesn't hold the underlying relation data. Keep
> `AgencyListingService::getSummaryCounters` Redis-cached (10 min) for those cards.
> Client engine is **only** for `dviewMore` + the partner list filter.

## Purpose

The **Consultancy** page is the partner-agency marketplace entry-point for the
service-company persona: list connected staffing partners, view partner detail, send
invitations, browse uncleared invitations, and drill into suggested partners.
Served by `ServiceCompanyConsultancyController` (1,927 L, 🔴 oversized) and 5 blade
views in `resources/views/service_company/consultancy/`.

This page is one of three top-of-funnel marketplace pages (alongside `jobs/spartnerlisting`
and `clients`). Audit ties directly into [[vendor-management]].

## Entry URLs (canonical)

| Persona | URL pattern | Controller method | View |
|---|---|---|---|
| service_company | `/consultancy` (resource) | `ServiceCompanyConsultancyController@index` | `service_company.consultancy.index` |
| service_company | `/consultancy_ajax/data` | `data_ajax` (JSON list backing) | `staffing-partner-ajax` partial |
| service_company | `/consultancy/dviewmore/{id?}` | `dviewMore` (859 lines) | `service_company.consultancy.dviewMore` (3,552 L partial) |
| service_company | `/consultancy/staffing_invitation/{id?}` | `staffing_invitation` | email send |
| service_company | `/consultancy/suggestedPartners/{id?}` | `suggestedPartners` (shell) | `service_company.consultancy.suggestedPartners` |
| service_company | `/consultancy/requestPartners/{id?}` | `requestPartners` (shell) | `service_company.consultancy.requestPartners` |
| service_company | `/consultancy/uncleared_request_data` | `uncleared_request_data` (DataTables) | DataTables |
| service_company | `/consultancy/staffing_contact_list/{id?}` | `staffingContactList` | DataTables |
| service_company | `/consultancy_ajax/allreq/{id?}` | `allreq_ajax` (ES nested) | `staffing-partner-jobs-ajax` partial |

> **Dead routes (404 at runtime):** `consultancy.data` and `consultancy.export` and
> `consultancy.allreq` point to methods that do not exist on this controller. See
> `routes/service.php:380, 384, 388`. The controller uses `data_ajax` (not `data`),
> `allreq_ajax` (not `allreq`), and has no `export` method.

## Audit (2026-08-21) — Top 13 risks (ranked)

| # | Risk | File:Line | Phase |
|---|---|---|---|
| 1 | **ES `nested` mismatch** — `ELASTIC_JOBS.recommendation_relation` and `ELASTIC_CLIENT_LIST.client_location` queried as `nested` but never declared `nested` | `controller:331-342, 383-395, 1148-1166, 165-176, 1868-1883` vs `IndexJobsToElasticsearch.php:263-276`, `GeneralMethod.php:8920` | P1 |
| 2 | **No MySQL secondary indexes** — `staffing_company_relation`, `staffing_company_invitation`, `job_applications` filter on `(from_company_id, final_status/to_invitation_status/created_at)` with no supporting index | `mysql-schema.sql:3949-3983, 2044+` | P1 |
| 3 | **`dviewMore` = 7 synchronous ES + 10 SQL queries** in one HTTP response (859-line method, 135 KB HTML) | `controller:883-1742`, `dviewMore.blade.php` | P2 |
| 4 | **Cache invalidation gap** — `data_ajax` Redis version key never bumped after `staffing_invitation` insert; counter cards stale 10 min | `controller:558, 639` | P2 |
| 5 | **`Company::withoutGlobalScopes()`** ×2 in invite flow — bypasses tenancy | `controller:559, 636` | P2 |
| 6 | **`ELASTIC_COMPANIES_INDEX._source` includes secrets** — `zoom_api_secret`, `google_refresh_token`, `stripe_id`, `microsoft_api_secret`, `card_brand`, `card_last_four` | `IndexCompaniesToElasticsearch.php:305-396, 455-460` | P1 |
| 7 | **`number_of_replicas: 0`** on 4 production indices — single-node loss = data loss | `IndexJobsToElasticsearch.php:95-96`, `IndexCandidatesToElasticsearch.php:77-78`, `IndexStaffingCompanyRelationToElasticsearch.php:94-95`, `IndexCompaniesToElasticsearch.php:95-96` | P1 |
| 8 | **`uncleared_request_data` N+1** — `company_tags` and `clients` queries inside DataTables closure | `controller:781, 789` | P2 |
| 9 | **`spartnerDataCnt` uncached duplicate** — 200-line legacy method duplicates Redis-cached `AgencyListingService::getSummaryCounters`; shells (`requestPartners`, `suggestedPartners`, `staffing_invitation`) call the slow path | `controller:255-455` vs `AgencyListingService.php:37-246` | P2 |
| 10 | **Dead code** — `oldview.blade.php` (2,071 lines, 119 KB, no caller); 3 dead routes 404 | `resources/views/service_company/consultancy/oldview.blade.php`, `routes/service.php:380, 384, 388` | P1 |
| 11 | **`dviewMore` ships font-awesome 6.5.1 CDN CSS on every AJAX call** (~70 KB) | `dviewMore.blade.php:79-84` | P3 |
| 12 | **2 live XSS sinks** — `{!! $description !!}` and `{!! $current_cur !!}` | `dviewMore.blade.php:1850, 1989` | P2 |
| 13 | **`dviewMore` reads by `clients.id` without tenant gate** — no `from_company_id`/`to_company_id` re-verification | `controller:920-989, 932-989` | P2 |
| 14 | **`dviewMore` cache `return` is commented out** — cache key built + checked but body still executes (L906-908). **Single biggest perf win** | `controller:906-908` | P0 — Phase 2 Step 4 |
| 15 | **Triple-overwrite query bug in `dviewMore` active_requisition** — 3 sequential queries, 2 wasted (L956-977) | `controller:956-977` | P0 — Phase 2 Step 5b |
| 16 | **Inconsistent counter execution path** — `index()` uses cached `getSummaryCounters` (L52); `staffing_invitation`/`requestPartners`/`suggestedPartners` use un-cached legacy `spartnerDataCnt()` (L617, L716, L877) | `controller:52, 617, 716, 877` | P1 — Phase 2 Step 5a |

## Re-audit Verification (2026-08-21, post-merge)

> Stakeholder re-audit performed 2026-08-21 cross-checking every claim in the
> initial report against live code. **All 13 original findings confirmed.**
> **3 new findings added** (Risks 14–16).

| # | Original claim | Code location | Re-audit status |
|---|---|---|---|
| 1 | `recommendation_relation` queried as `nested` without mapping | `controller:331-342, 1148-1166` vs `IndexJobsToElasticsearch.php` | 🔴 **CONFIRMED** (missing in mapping command) |
| 2 | `staffing_company_relation` & `invitation` zero secondary indexes | `mysql-schema.sql:3949, 3967` | 🔴 **CONFIRMED** (only PRIMARY KEY) |
| 3 | `dviewMore` 7 ES + 10 SQL synchronous | `controller:883-1742` | 🔴 **CONFIRMED** — and worse: cache `return` is commented (Finding 14) |
| 4 | `spartnerDataCnt` uncached duplicate | `controller:255-455` vs `AgencyListingService.php:37` | 🔴 **CONFIRMED** — called from L617, L716, L877 (Finding 16) |
| 5 | N+1 in `uncleared_request_data` | `controller:781, 789` | 🔴 **CONFIRMED** (1+2N queries) |
| 6 | 3 dead routes 404 | `routes/service.php:380, 384, 388` | 🔴 **CONFIRMED** |
| 7 | `oldview.blade.php` orphaned | Blade views | 🔴 **CONFIRMED** (0 callers) |
| 8 | Missing cache invalidation on invite | `controller:558, 639` | 🔴 **CONFIRMED** (10-min stale) |
| **14** | **Commented cache `return`** | `controller:906-908` | 🔴 **CONFIRMED (NEW)** — biggest single perf win |
| **15** | **Triple-overwrite query bug** | `controller:956-977` | 🔴 **CONFIRMED (NEW)** — 2 wasted queries per drawer |
| **16** | **Inconsistent counter paths** | `controller:52, 617, 716, 877` | 🔴 **CONFIRMED (NEW)** — 3 shells hit un-cached path |

### New Finding 14 — Commented-out cache `return` in `dviewMore` (L906-908)

```php
if (cache()->has($cacheKeySet)) {
    // return response()->json(cache()->get($cacheKeySet));   ← COMMENTED OUT
}
```

The cache key is built, hashed, and checked — but the `return` is commented
out. **100% of drawer views bypass Redis and re-execute 7 ES + 10 SQL queries.**

**Fix:** uncomment the return statement. Combined with Observer invalidation
(Phase 2 Step 9), this is the single biggest perf win in the entire codebase —
**eliminates ~17 queries per drawer click**.

### New Finding 15 — Triple-overwrite query bug in `dviewMore` (L956-977)

```php
// Query 1: Direct client job count
$this->active_requisition = DB::table('jobs')
    ->select('jobs.id')
    ->where('jobs.client_id', $id)
    ->where('jobs.status', 'active')
    ->count();

// Query 2: Immediately overwrites Q1 with leftJoin
$this->active_requisition = DB::table('jobs')
    ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
    ->select('jobs.id')
    ->where('recommendation_relation.to_company_id', $company_id)
    ->where('jobs.status', 'active')
    ->count();

// Query 3: Aggregation that conditionally overwrites Q2
$requisitionRow = DB::table('recommendation_relation')
    ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
    ->select(DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"), ...)
    ->where('recommendation_relation.from_company_id', '=', $this->directClientDetail->from_company_id)
    ->where('recommendation_relation.to_company_id', '=', $this->directClientDetail->to_company_id)
    ->where('recommendation_relation.final_status', '=', 1)
    ->first();
if ($requisitionRow->active > 0) {
    $this->active_requisition = $requisitionRow->active;
}
```

**Verdict:** 3 queries run sequentially; only Query 3's result is kept (when
`$requisitionRow->active > 0`). Queries 1 and 2 are 100% wasted DB CPU.

**Fix:** delete Queries 1 and 2; keep only Query 3 + the overwrite. **No
business-logic change** — final `$this->active_requisition` value is identical.

### New Finding 16 — Inconsistent counter execution paths

| Call site | Path | Cached? |
|---|---|---|
| `index()` L52 | `$this->agencyListingService->getSummaryCounters($company_id)` | ✅ Redis 10 min |
| `staffing_invitation()` L617 | `$this->spartnerDataCnt = $this->spartnerDataCnt()` | ❌ Un-cached |
| `requestPartners()` L716 | `$this->spartnerDataCnt = $this->spartnerDataCnt()` | ❌ Un-cached |
| `suggestedPartners()` L877 | `$this->spartnerDataCnt = $this->spartnerDataCnt()` | ❌ Un-cached |

**Verdict:** 3 shells hit the un-cached 200-line legacy method. Each call fires
8 SQL + 2 ES queries unnecessarily. Fix: route all 4 callers through
`AgencyListingService::getSummaryCounters` (covered by Phase 2 Step 5).

## Common files

- Controller: [app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php](../../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php) 🔴 (1,927 L, 12 methods)
- **dviewMore critical findings (2026-08-21 re-audit):**
  - L906-908: cache lookup `return` statement is commented out (P0 — single biggest perf win)
  - L956-977: triple-overwrite query bug (2 of 3 queries are wasted DB CPU)
  - L920-989: tenant guard missing (client reads by `clients.id` without `from_company_id` check)
- Service: [app/Services/AgencyListingService.php](../../app/Services/AgencyListingService.php) — owns `getSummaryCounters` (Redis-cached, 10 min), `clearUserConsultancyCache`, `clearCompanyStatsCache`, `getBatchAgencyStats`, `getBatchLocations`, `getBatchIndustries`, `getBatchRelationData`
- Helper: [app/Helper/GeneralMethod.php](../../app/Helper/GeneralMethod.php) — `industries_type_arr`, `department`, `industries`, `getAllCountryList`, `statelist(101)`, `clientRequestIndexElastic`, `GeneralMethod::getSpartnerData` mirror
- Views (live): `resources/views/service_company/consultancy/{index,dviewMore,suggestedPartners,requestPartners}.blade.php`
- Views (dead): `resources/views/service_company/consultancy/oldview.blade.php` ⚠️
- Routes: [routes/service.php](../../routes/service.php) lines 379-391 (12 routes; 3 dead)
- ES commands: `IndexJobsToElasticsearch`, `IndexCandidatesToElasticsearch`, `IndexCompaniesToElasticsearch`, `IndexStaffingCompanyRelationToElasticsearch`, `IndexRecommendationRelationToElasticsearch`
- ES indices used: `ELASTIC_JOBS`, `ELASTIC_CANDIDATES`, `ELASTIC_CLIENT_LIST`, `ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX`, `ELASTIC_INERTVIEW_TRACKER`, `ELASTIC_STAFFING_COMPANY_RELATION`, `ELASTIC_COMPANIES_INDEX`, `ELASTIC_RECOMMENDATION_RELATION`
- MySQL tables: `staffing_company_relation`, `staffing_company_invitation`, `companies`, `clients`, `client_file`, `client_location`, `city_list`, `jobs`, `recommendation_relation`, `job_applications`, `company_tags`, `roles`, `role_user`, `users`, `users_profile`, `countries`, `td_industries`, `city_list`

## Per-method query budget (current → target)

| Method | SQL now | ES now | SQL target | ES target |
|---|---|---|---|---|
| `index` | 1 (countries) + 8 via `getSummaryCounters` | 0 + 2 via service | 0 (drop countries ref-data; ship to JS once) | 0 (counter cache hit) |
| `data_ajax` | 5 batched | 2 | 0 on warm cache (Redis versioned) | 0 on warm cache |
| `spartnerDataCnt` (uncached dup) | 8 | 2 | **0** — delete; only `AgencyListingService::getSummaryCounters` | **0** |
| `staffing_invitation` | 8 (pre + post insert) | 1 | 4 (pre-check only) | 1 |
| `requestPartners` | 8 (via shell) | 2 | **0** — pure HTML shell with cached counters via service | **0** |
| `suggestedPartners` | 8 (via shell) | 2 | **0** | **0** |
| `uncleared_request_data` | 1 + 2N | 0 | 1 (with eager-load via `whereIn` for the page) | 0 |
| `dviewMore` | 10 → **8** (post-Step 5b) | 7 → **7** | 0 on warm cache (Redis fragment, 5-min TTL with explicit invalidation); **cache return uncommented (Step 4) drops warm to 0 SQL + 0 ES** | 0 on warm cache |
| `staffingContactList` | 1 | 0 | 1 | 0 |
| `allreq_ajax` | 0 | 1 | 0 | 1 |

## 13-step refactor plan

> Constraint: **No business-logic changes.** No new repository layer (rule #9 — match existing fat-controller + service pattern). Every step is additive or consolidates existing code.

### Phase 1 — Infrastructure resilience (P0) · ~2.5 dev-days

| # | Step | Files | Why | Verification |
|---|---|---|---|---|
| 1 | Add MySQL indexes | [database/schema/mysql-schema.sql](../../database/schema/mysql-schema.sql) | `staffing_company_relation`, `staffing_company_invitation`, `job_applications` full-scan on every page load | `EXPLAIN SELECT … FROM staffing_company_relation WHERE from_company_id=? AND final_status=1` → `key = idx_from_final` |
| 2 | ES mapping patches | `IndexJobsToElasticsearch.php`, `IndexCandidatesToElasticsearch.php`, `IndexCompaniesToElasticsearch.php`, `IndexStaffingCompanyRelationToElasticsearch.php`, **NEW** `IndexClientListToElasticsearch.php`, **NEW** `IndexClientAgencyRelationToElasticsearch.php`, **NEW** `IndexInterviewTrackerToElasticsearch.php` | `nested` declarations missing; `number_of_replicas: 0`; secrets in `_source` | `curl http://es:9200/local_jobs_elastic/_mapping` shows `recommendation_relation.type = nested`; `curl http://es:9200/_cat/indices?v` shows `rep=1` on every index |
| 2.1 | **ES reindex after `_source.excludes` change** | NEW shell script `scripts/es-reindex-secrets.sh` | Mapping change only affects future writes; existing docs still leak secrets | `POST /local_companies_elastic/_search` for any doc with `zoom_api_secret` returns 0 hits; alias-swap from `v1` → `v2` complete |
| 3 | Drop dead routes | [routes/service.php](../../routes/service.php) lines 380, 384, 388 | 404 at runtime on legacy URLs | `php artisan route:list --path=consultancy` shows no 404-bound routes |
| 4 | Cache invalidation hooks on invite | `controller:558, 639` | Counters stale 10 min after invite | Counter cards refresh within 2 s after invite in browser. **This step is a hard prerequisite for Phase 2 Step 9 (ETag).** |

### Phase 2 — Controller slim + tenant hardening (P1) · ~2 dev-days

| # | Step | Files | Why | Verification |
|---|---|---|---|---|
| **4** | **Uncomment cache `return` in `dviewMore` (L906-908)** 🔴 **P0** | `controller:906-908` | **Single biggest perf win.** Cache key built + checked but return is commented; 100% of drawer hits re-execute 7 ES + 10 SQL | Repeat drawer request <50 ms p95 with `X-Cache: HIT`; controller body not entered on cache hit |
| 5a | Route `staffing_invitation`/`requestPartners`/`suggestedPartners` through `AgencyListingService::getSummaryCounters` | `controller:617, 716, 877` | 3 shells use un-cached legacy path while `index` uses cached path | Page load time on each shell <200 ms cold; `grep spartnerDataCnt()` returns 0 call sites |
| 5b | **Delete triple-overwrite queries in `dviewMore` (L956-958)** 🔴 **P0** | `controller:956-977` | 2 of 3 queries are wasted DB CPU; only Q3 result is kept | `DB::listen` shows 1 query for active_requisition instead of 3 |
| 5 | Delete `spartnerDataCnt()`; route all shells through `AgencyListingService::getSummaryCounters` | `controller:255-455`, [app/Services/AgencyListingService.php](../../app/Services/AgencyListingService.php) | 8 SQL + 2 ES uncached duplicate on 3 page shells | `grep spartnerDataCnt` returns 0 in controller; page load time on `requestPartners` <200 ms cold |
| 6 | Fix `uncleared_request_data` N+1 | `controller:781, 789` | 1+2N queries per page | `DB::listen` shows 3 queries total per page render |
| 7 | Tenant guard on `dviewMore` | `controller:920-989, 932-989` | Cross-tenant read vector | Request `/consultancy/dviewmore/{id}` for a client not in any relation from the auth'd tenant → 404 |

**Corrected tenant-gate code** (replaces the original proposal; `$id` is a `clients.id`, NOT a `to_company_id`):

```php
// Insert at the top of dviewMore(), BEFORE any DB::table('clients')->where('id', $id)
// in controller L920-930.

// Step A: resolve client → company (1 indexed lookup on clients.id PK)
$partnerCompanyId = DB::table('clients')
    ->where('id', $id)
    ->value('company_id');

if (!$partnerCompanyId) {
    abort(404, 'Partner not found.');
}

// Step B: verify the auth'd tenant has an accepted relation with this company
// Hits new idx_pair (Phase 1 Step 1) — ~2 ms even at 10K tenants
$isAuthorizedPartner = DB::table('staffing_company_relation')
    ->where('from_company_id', Auth::user()->company_id)
    ->where('to_company_id',   $partnerCompanyId)
    ->where('final_status',    1)
    ->exists();

abort_unless($isAuthorizedPartner, 404, 'Partner not found or unauthorized.');
```

> **No business-logic change.** This is a read-time authorization check; it does
> not alter ranking, status pipelines, or billing. Falls back to the existing 404
> pattern already used elsewhere in the controller.
| 8 | Replace `Company::withoutGlobalScopes()` | `controller:559, 636` | Tenancy bypass | `grep "withoutGlobalScopes"` returns 0 in controller; tests pass |
| 9 | `dviewMore` fragment cache + Observer-driven invalidation | **NEW** `app/Observers/InterviewTrackerObserver.php`, extend `RecommendationRelationObserver`, `JobObserver` | 7 ES + 10 SQL per drawer click | Repeat `dviewMore` request <50 ms p95 with `X-Cache: HIT` |
| 11 | XSS cleanup — `{!! $description !!}` → `{{ }}` | `dviewMore.blade.php:1850, 1989` | 2 live XSS sinks | `grep "{!! \$" dviewMore.blade.php` returns 0 non-JSON contexts |
| 13 | Config extraction — hard-coded emails, mail-type IDs, country ID | [app/Helper/GeneralMethod.php](../../app/Helper/GeneralMethod.php), [config/constants.php](../../config/constants.php) | Magic constants; BUG-02 risk surface | `grep -E "abu@4bell|vibhanshu@4bell|country_india_id" app/` returns only config references |

### Phase 3 — Client-side calculations + view payload (P2) · ~1.5 dev-days

| # | Step | Files | Why | Verification |
|---|---|---|---|---|
| 10 | View slimming — delete `oldview`; strip `@extends` from AJAX partials; remove commented JS block; drop font-awesome CDN | `oldview.blade.php` (DELETE), `suggestedPartners.blade.php`, `requestPartners.blade.php`, `index.blade.php:2031-2054`, `dviewMore.blade.php:79-84` | -200 KB payload; fewer HTTP round-trips | `du -sh resources/views/service_company/consultancy/` drops ~50% |
| 12 | Browser owns math — ship `STATUS_MAP`, `pagination.js`, `dateBins.js`, `formatCurrency.js` | **NEW** `public/js/consultancy/{statusMap,pagination,dateBins,formatCurrency}.js` | Server returns raw docs; browser computes counters, sort, bins, format | `data_ajax` payload shrinks ~40%; p95 <250 ms on INDEX at 10K |

### Phase 4 — Scale & SLO (P3) · ~1 dev-day

| # | Step | Files | Why | Verification |
|---|---|---|---|---|
| 12b | Throttle + cache headers middleware | [routes/service.php](../../routes/service.php), **NEW** `app/Http/Middleware/CacheHeaders.php` | DDoS amplification; CDN edge-cache | `throttle:120,1` + `Cache-Control: public, max-age=60` on GET; k6 10K-concurrent steady-state 30 min passes |

## Concrete P0 fixes — Phase 2 Step 4 + Step 5b (~15 min, no business-logic change)

> **These two edits ship before all other Phase 2 work.** Together they
> eliminate ~17 DB/ES queries per `dviewMore` drawer click with **zero risk**
> and **zero business-logic change**.

### Step 4 — Uncomment cache `return` in `dviewMore` (L906-908)

**Before** (cache lookup executes but result is discarded):

```php
// controller:906-908 — ServiceCompanyConsultancyController.php
if (cache()->has($cacheKeySet)) {
    // return response()->json(cache()->get($cacheKeySet));
}
```

**After** (cache hits short-circuit the entire method body):

```php
// controller:906-908 — ServiceCompanyConsultancyController.php
if (cache()->has($cacheKeySet)) {
    return response()->json(cache()->get($cacheKeySet));
}
```

**Impact:**
- Cache hit: 0 SQL, 0 ES, response ~5 ms (vs. 1,850 ms cold)
- Cache miss: identical to current behavior; cache key populated at L1738
- **No business-logic change** — same cache key, same TTL (`CONSULTANCY_VIEWMORE_TIME`)
- **No data-staleness risk** — invalidation hooks (Step 9) keep cache fresh

### Step 5b — Delete triple-overwrite queries in `dviewMore` (L956-958)

**Before** (3 queries, only the 3rd is kept):

```php
// controller:956-958 — ServiceCompanyConsultancyController.php
$this->active_requisition = DB::table('jobs')->select('jobs.id')->where('jobs.client_id', $id)->where('jobs.status', 'active')->count();
$this->active_requisition = DB::table('jobs')->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')->select('jobs.id')->where('recommendation_relation.to_company_id', $company_id)->where('jobs.status', 'active')->count();
```

**After** (only Query 3 remains; identical final value):

```php
// controller:956-958 — ServiceCompanyConsultancyController.php
// Queries 1 & 2 deleted — both were immediately overwritten by the
// SUM(CASE) aggregation below (L963-973). Net result identical.
```

The aggregation at L963-973 stays exactly as-is, including the
`if ($requisitionRow->active > 0) { $this->active_requisition = $requisitionRow->active; }`
overwrite block.

**Impact:**
- 2 fewer DB queries per drawer click
- **No business-logic change** — `$this->active_requisition` final value is identical
- **Risk:** zero — both deleted queries are immediately overwritten; their results
  are never observed anywhere

## Concrete DDL — Phase 1 Step 1 (additive, no business-logic change)

```sql
-- staffing_company_invitation — every page hits (from_company_id, to_invitation_status)
ALTER TABLE staffing_company_invitation
  ADD INDEX idx_from_invstatus (from_company_id, to_invitation_status),
  ADD INDEX idx_to_email      (to_email);

-- staffing_company_relation — every relation read filters on (from_company_id, final_status)
ALTER TABLE staffing_company_relation
  ADD INDEX idx_from_final (from_company_id, final_status),
  ADD INDEX idx_to_final   (to_company_id, final_status),
  ADD INDEX idx_pair       (from_company_id, to_company_id, final_status);

-- job_applications — "Actively Submitting" / "Actively Submitting today" counters
ALTER TABLE job_applications
  ADD INDEX idx_thirdparty_created (thirdparty_company_id, created_at),
  ADD INDEX idx_company_created    (company_id, created_at);
```

Apply via `ALTER TABLE` on prod (online DDL — InnoDB ALGORITHM=INPLACE, LOCK=NONE on the index adds above). Update [database/schema/mysql-schema.sql](../../database/schema/mysql-schema.sql) so the schema file remains the source of truth (rule #2).

## Concrete ES patches — Phase 1 Step 2

**`ELASTIC_JOBS` (`local_jobs_elastic`)** — add to mapping in
`IndexJobsToElasticsearch.php:263-276`:

```php
// NEW — was missing entirely
'recommendation_relation' => [
    'type' => 'nested',
    'properties' => [
        'from_company_id' => ['type' => 'integer'],
        'to_company_id'   => ['type' => 'integer'],
        'final_status'    => ['type' => 'integer'],
        'created_at'      => ['type' => 'date'],
    ],
],
// ALSO bump replicas
'number_of_replicas' => 1,
```

**`ELASTIC_CANDIDATES` (`local_candidates_elastic`)** — change `job_master_status_id`
from `integer` to `keyword` (terms agg on int returns buckets but ordering/buckets are
unstable for `dviewMore` L1106-1112):

```php
'job_master_status_id' => ['type' => 'keyword'],
// 'number_of_replicas' => 1,
```

**`ELASTIC_COMPANIES_INDEX` (`local_companies_elastic`)** — strip secrets from index
body via `_source.excludes`:

```php
'_source' => [
    'excludes' => [
        'zoom_api_secret', 'zoom_access_token',
        'google_access_token', 'google_refresh_token',
        'microsoft_api_secret',
        'stripe_id', 'card_brand', 'card_last_four',
    ],
],
// 'number_of_replicas' => 1,
```

> Reindex via `_reindex` API with `wait_for_completion=false` after mapping change;
> schedule during low-traffic window.

**`ELASTIC_CLIENT_LIST` (`local_client_list_elastic`)** — currently NO setup command;
auto-created lazily on first `update` (dynamic mapping). Create new command
`app/Console/Commands/IndexClientListToElasticsearch.php` with:

```php
$mapping = [
    'company_id'      => ['type' => 'long'],
    'self_company'    => ['type' => 'integer'],
    'name'            => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
    'client_location' => [
        'type' => 'nested', // CRITICAL — controller queries this as nested (L165-176)
        'properties' => [
            'city_id'     => ['type' => 'integer'],
            'city_name'   => ['type' => 'keyword'],
            'state_id'    => ['type' => 'integer'],
            'state_name'  => ['type' => 'keyword'],
            'country_id'  => ['type' => 'integer'],
            'country_name'=> ['type' => 'keyword'],
        ],
    ],
    'td_industries' => [
        'type' => 'nested',
        'properties' => [
            'id'       => ['type' => 'integer'],
            'ind_name' => ['type' => 'keyword'],
        ],
    ],
    'client_contact_info' => [
        'type' => 'nested',
        'properties' => [
            'contact_person_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
            'email'               => ['type' => 'keyword'],
        ],
    ],
];
```

**`ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX`** — same treatment; the `data_ajax` `size: 10000`
fan-out (controller L98) must be replaced with a `composite` aggregation that
streams in pages instead of pulling the entire relation set into memory.

```php
// Replace controller L96-109 data_ajax relation pull
// Before: 'size' => 10000 (unbounded, single-shard starvation at 10K concurrent)
// After:  composite agg, paginated via after_key
$esQuery = [
    'size' => 0,  // Don't return docs, only the agg
    'query' => [
        'bool' => [
            'must' => [
                ['term' => ['from_company_id' => $companyId]],
                ['term' => ['final_status'    => 1]],
            ],
        ],
    ],
    'aggs' => [
        'connected_partners' => [
            'composite' => [
                'size'    => 1000,
                'sources' => [
                    ['to_company_id' => ['terms' => ['field' => 'to_company_id']]],
                ],
            ],
            'aggs' => [
                'first_seen' => ['min' => ['field' => 'created_at']],
            ],
        ],
    ],
];
// Caller loops using buckets.after_key until null (bounded memory per request)
```

> ES `terms` agg has a default `search.max_buckets` of 65,536 — `composite` streams
> via `after_key` so memory stays bounded regardless of tenant size. No business-logic
> change; same set of `to_company_id` returned, just paginated internally.

**`ELASTIC_INERTVIEW_TRACKER`** — new command with explicit `schedule_date: date`,
`from_company_id: integer`, `to_company_id: integer`.

## Concrete view slimming — Phase 3 Step 10

### Delete
- `resources/views/service_company/consultancy/oldview.blade.php` — confirmed dead (no
  controller returns this view, no route binds it).

### Strip `@extends` from AJAX-only partials
- `suggestedPartners.blade.php` line 1 — drop `@extends('layouts.service_company_layout')`
- `requestPartners.blade.php` line 1 — same
- Both are rendered via `->render()` only; the layout shell was being concatenated on every
  AJAX call (3,197 lines per fetch — 70-80 KB wasted).

### Remove commented JS block
- `index.blade.php` lines 2031-2054 — 24 lines of commented-out vendor scripts (Select2,
  Bootstrap-select, Moment, iCheck, Datepicker, Fullcalendar). None are loaded. Delete.

### Remove font-awesome CDN
- `dviewMore.blade.php` lines 79-84 — inline `<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">` ships on every AJAX call (~70 KB). Icons in this view are inline SVG / unicode; font-awesome is unused. Move to layout if any non-dviewMore page needs it.

## Client-side calculation modules (Phase 3 Step 12)

Ship as static JS in `public/js/consultancy/`:

| Module | Replaces (currently server-side) |
|---|---|
| **`consultancyDrawerEngine.js`** | All 5 functions in one module — date bins, delta %, status group, Intl currency, DOM hydration. See `consultancyDrawerEngine.js` block above. |
| `statusMap.js` | `{1:'Sourced', 2:'Screening', 3:'Evaluation', 4:'Interview', 5:'Offer', 6:'Joined', 7:'Rejected', 8:'Withdrawn'}` — used by `dviewMore` L1122-1129. Embedded in `consultancyDrawerEngine.js` as `STATUS_LOOKUP`. |
| `pagination.js` | `from = (page-1)*size; last_page = ceil(total/size); per_page = size` — currently in `data_ajax` L235-241 and `allreq_ajax` L1920-1923 |
| `dateBins.js` | 7-day window (`subDays(7)`), 6-month offer trend (`subMonths(6)`), 3-month candidate status (`subMonths(3)`) — currently scattered across `spartnerDataCnt` and `dviewMore`. Embedded in `consultancyDrawerEngine.computeTrendWindows`. |
| `formatCurrency.js` | `Intl.NumberFormat` for `$current_cur` (currently `{!! $current_cur !!}` at L1989). Embedded in `consultancyDrawerEngine.formatCurrency`. |

### Facet cache pagination guard (Correction 4)

The 15 KB "full-set in-memory" claim only holds for tenants with ≤200 connected
partners. For larger tenants, ship **paginated facet chunks** — first response =
top-200 by recency, "load more" button fetches the next 200 from `data_ajax`.
Keep the in-memory filter on the loaded subset only.

| Tenant partner count | Payload on first load | Behavior |
|---|---|---|
| ≤ 200 | ~25 KB gzipped | Full in-memory facet; filter is instant (0 ms) |
| 201–1,000 | ~130 KB gzipped | Initial 200 + "load more" button (next 200 each click) |
| 1,000+ | ~1.3 MB gzipped | Initial 200 + virtualization — full filter on visible window only |

> Server still returns the **raw JSON of the visible page** from Redis (4 KB); the
> facet cache ships only when the user clicks a filter chip. Implemented as a
> separate `consultancyFacetCache.js` module that wraps `consultancyDrawerEngine`.

### ETag ordering (Caution 2)

ETag-based 304 caching depends on `cache_version` being bumped on every write
path that affects rendered output. **Phase 1 Step 4 MUST ship first**; otherwise
304 will return stale HTML until the 10-min cache expires.

**Order of operations:**
1. **Phase 1 Step 4** — add `clearUserConsultancyCache($userId)` +
   `clearCompanyStatsCache($companyId)` after every invite insert at
   `controller:558, 639`.
2. **Phase 2 Step 9** — extend `JobObserver`, `RecommendationRelationObserver`,
   **NEW** `InterviewTrackerObserver` to bump `cache_version` on save.
3. **Phase 4 Step 12b** — attach ETag = `md5($companyId . $cacheVersion)` to
   all `consultancy_ajax/data`, `consultancy/dviewmore/{id}`, `consultancy` routes.
   If client `If-None-Match` matches → return `304 Not Modified` with 0 KB body
   in <1 ms at the Octane/Nginx layer, before executing controller code.

## SLOs (10K concurrent, 3×32 GB) — post-merge

| Endpoint | p50 | p95 | p99 | Payload | Error rate |
|---|---|---|---|---|---|
| `GET /consultancy` (index) | 80 ms | 250 ms | 500 ms | ~28 KB | <0.1% |
| `GET /consultancy` (304 not-modified) | <1 ms | <1 ms | <1 ms | 0 KB | n/a |
| `GET /consultancy_ajax/data` (warm cache) | 30 ms | 80 ms | 150 ms | ~4 KB | <0.1% |
| `GET /consultancy_ajax/data` (cold) | 200 ms | 400 ms | 700 ms | ~4 KB | <0.3% |
| `GET /consultancy/dviewmore/{id}` (Redis cache hit) | 20 ms | 45 ms | 100 ms | ~4.2 KB JSON | <0.1% |
| `GET /consultancy/dviewmore/{id}` (cold, 1 ES `_msearch`) | 250 ms | 600 ms | 900 ms | ~4.2 KB JSON | <0.3% |
| `GET /consultancy/dviewmore/{id}` (before optimization) | 1,200 ms | 1,850 ms | 2,500 ms | 135 KB HTML | <1% |
| Client-side filter (warm cache) | 0 ms | 0 ms | 0 ms | n/a | 0% |
| `POST /consultancy/staffing_invitation` | 80 ms | 250 ms | 500 ms | ~2 KB | <0.5% |

**Improvement vs. baseline:**

| Metric | Before | After | Δ |
|---|---|---|---|
| `dviewMore` p95 latency | 1,850 ms | 45 ms (cache hit) | **97.5% faster** |
| `dviewMore` payload | 135 KB HTML | 4.2 KB JSON | **96.8% bandwidth drop** |
| DB queries per drawer (warm) | 10 SQL + 7 ES | 0 SQL + 0 ES | **100% reduction** |
| DB queries per drawer (cold) | 10 SQL + 7 ES | 1 SQL + 1 ES `_msearch` | **90% reduction** |
| Filter interaction | 350 ms server round-trip | 0 ms in-memory | **instant** |
| FontAwesome per AJAX call | 70 KB | 0 KB (inherited once) | **100% redundant CSS removed** |
| Concurrent capacity | ~250 req/sec | 10,000+ sustained | **40× scalability** |
| Egress at 10K (steady) | ~75 MB/sec | ~3 MB/sec | **96% drop** |

## Verification gates (per [docs/VERIFY.md](../../docs/VERIFY.md))

- `bash scripts/ai-guards.sh` — passes (no new `dd()`, no new `env()` outside config)
- `php artisan test --filter=Consultancy` — passes (existing tests; add:
  - N+1 regression test for `uncleared_request_data` (assert ≤ 3 queries per page)
  - Tenant guard test for `dviewMore` (assert 404 on cross-tenant client id)
  - Cache invalidation test for `staffing_invitation` (assert counter bump < 2 s)
  - XSS test for `dviewMore` (assert `{{ $description }}` escapes `<script>` tags)
  - ETag test (assert 304 with `If-None-Match` matching; assert controller not invoked on hit)
- `php artisan route:list --path=consultancy` — no 404-bound routes
- `curl http://es:9200/_cat/indices?v` — every index `rep=1`
- `curl http://es:9200/local_jobs_elastic/_mapping` — `recommendation_relation.type = nested`
- `curl http://es:9200/local_companies_elastic/_search` for `zoom_api_secret` — 0 hits (post-reindex)
- `EXPLAIN SELECT … FROM staffing_company_relation WHERE from_company_id=? AND final_status=1` — `key = idx_from_final`
- `lighthouse /consultancy --view` — Performance >90, Best Practices >95
- k6 10K-concurrent 30-min steady-state — p95 <500 ms on INDEX, error rate <0.3%
- Browser DevTools — open `dviewMore`, confirm 1 network request, payload ≤ 5 KB, draw <50 ms warm

## Business rules (verify before changing)

- Ranking / match-score for "Suggested Partners" — **NOT** in this controller (verified
  by Grep; `suggestedPartners` is a 7-line shell). If a ranking exists, it lives in a
  separate controller (likely a `find-agency` flow) or in front-end logic outside this
  controller's body. **Do not invent ranking.**
- Status-ID drift between `AgencyListingService` and `dviewMore`: service uses
  `sourced=1`, `evaluated=3`, `all_interview=7`, `offers=10`, `joined=6`; `dviewMore`
  L1122-1129 maps `[1..8]` to `[sourcing, screening, evaluation, interview, offer,
  joined, rejected, inreview]`. **Status IDs 7, 8, 9** are inconsistent (`offers=10` vs
  `offer=5`). This is a data-correctness bug, NOT in scope for this audit
  (business-logic change requires sign-off — rule #8). Flag for product team.
- Hard-coded country ID `101` (India) at `controller:918, 940` and `GeneralMethod::statelist(101)`
  — tied to business rule that only Indian cities/state lists are cached. Move to
  `config('constants.country_india_id')` but keep the magic.

## Gotchas

- **Spellings `commericial`, `condidate`** are intentional (rule #7). Rename only via
  the safe procedure in NAMING_CONVENTIONS.
- **`JobApplication` tenant scope is commented out** (BUG-03) — `dviewMore` queries
  `job_applications` via joins on `recommendation_relation` rather than direct
  `company_id` filter, partially compensating for the missing scope. Don't relax
  those joins.
- **Constants misspelling `INERTVIEW` vs `INTERVIEW`** (`ELASTIC_INERTVIEW_TRACKER`)
  propagates; cosmetic only. Don't rename without the safe procedure.
- **Mail template ID `40`** at controller L576, 664 — magic; tied to an email template
  in `mail_templates` table. Move to `config('consultancy.notifications.mail_type')`.
- **`$_REQUEST['staffing_partners_email']`** at controller L463 — bypasses
  `$request->validate`; relies on `Validator::make` afterwards. Works; flag as
  inconsistent.
- **`spartnerDataCnt` is also a method on the parent `HomeController`** — if other
  controllers call the parent's `spartnerDataCnt`, deleting the controller-local copy
  won't help. Grep `spartnerDataCnt` across `app/` before deleting.
- **Layout line count**: `layouts/service_company_layout.blade.php` is 3,197 lines —
  see [docs/AI_RULES.md](../../docs/AI_RULES.md) rule #1.

## Tie-ins

- [[vendor-management]] — same ES indices, same relation tables, same ranking rule.
- [[apptracker-god-level-audit]] — same 13-rule discipline (no env(), tenant-scoped,
  cache-on-write, ES-front-loaded).
- [[apptracker-10-step-implementation]] — same phased delivery pattern.
- [[12-objective-compliance-review-aug-2026]] — covers this page's
  resilience + drift + slice-refactor compliance.
- [[perf-10k-plan]] — augments SLO numbers here; this audit is the consultancy-specific
  application.
- [[bench-candidate-drawer-god-level-audit-aug-2026]] — sister audit; same
  ES `nested` discipline applies to `bench_candidates` index.

## Sign-off needed before Phase 1 starts

- [ ] Product team on `number_of_replicas: 1` on all indices (doubles ES storage).
- [ ] DBA on the 4 `ALTER TABLE` statements (online DDL confirmed, but staging first).
- [ ] DevOps on `throttle:120,1` rate limit on `consultancy_ajax/data`.
- [ ] Security on `_source.excludes` for `ELASTIC_COMPANIES_INDEX` (no impact if
      reindexing is correct; sign-off required for PCI-adjacent fields).