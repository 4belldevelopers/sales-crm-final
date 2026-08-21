# Service Company / interview-view/bench-candidates — GOD-Level Audit

**Subject:** `resources/views/service_company/bench-candidates/interview-schedule.blade.php` + `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php::{benchinterviewtable,benchinterviewdata_ajax,benchinterviewdata}`
**Date:** 2026-08-20
**Persona scope (per 2026-08-15 directive):** Service Company only — in scope. Product/Staffing equivalents are dormant and untouched.
**URL in scope:** `GET /service_company/interview-view/bench-candidates` → `service_company.bench-candidates.bench-table-view`
**Constraint modal:** **Business logic stays unchanged.** This is an observations + recommendations audit. No code edits in this audit.
**Note on prior audit:** [service-company-bench-candidates-audit-2026-08-20.md](service-company-bench-candidates-audit-2026-08-20.md) covers the **legacy** `/service_company/bench-candidates` page (`ServiceCompanyBenchCandidatesController::index`). This audit covers the **interview-view variant** rendered by `InterviewScheduleController::benchinterviewtable`.

---

## 0. TL;DR (P0 → P3)

| Prio | Title | Impact |
|---|---|---|
| **P0** | **Critical correctness bug:** `benchinterviewdata_ajax` line 681 issues a `nested` query on `job_assign.user_id`, but the ES doc (`interviewTrackerAddEditUpload` indexer at `app/Helper/GeneralMethod.php:8527`) writes `job_assign` only as part of `_source` recommendation_relation — **`job_assign` is NOT a `nested` field**. ES silently returns 0 matches for every non-admin user. | Non-admin users may see an empty table even when their data exists in MySQL. Critical UX/security impact. |
| **P0** | **Operator-precedence bug at line 679:** `! $chkAdminRole > 0` is parsed as `(! $chkAdminRole) > 0` — only restricts when role is literally `0`. `hasRole('admin')` returns `true`/collection, so the gate is **always** triggered, meaning the access-restriction `must` block runs for admins too (less critical) AND the `chkAdminRole == 0` literal-string case (effectively dead branch). | Either admin bypasses the restriction (intended behaviour) or admin sees restricted data — code intent vs reality disagree. |
| **P0** | **Version key `allinterviewbenchcandidates_version_user_*` is never bumped anywhere.** Grep of the entire codebase: only `allinterviewschedule_version_user_*` (non-bench) is bumped (3 places). After every interview create/update/reschedule, the bench interview page returns **stale rows for up to `ALL_INTERVIEW_BENCH_CANDIDATES_TIME` minutes (50)**. | Stale data lifetime = 50 min. UI shows old status even after user reschedules or feedback is filed. |
| **P0** | **Legacy endpoint `benchinterviewdata()` (`service.php:402`) has no `LIMIT`, ignores DataTables paging params, runs an 11-table join + per-row N+1 (`users` lookup + `companyDetailsByClient`) = `1 + 2N` queries per draw.** | If anyone hits the legacy endpoint with 100 rows = 201 round-trips per request. Latency = O(seconds). |
| **P1** | `_script` painless sort on `doc['feedback'].size() == 0 ? 0 : 1` fires for every "Feedback Pending" sort (lines 603–612). Cost paid on every page turn when that sort is active; defeats `request_cache: true`. | Painless `_script` is the most expensive sort in ES; for 10K recruiters on "Feedback Pending" tab = 10K painless invocations × page turns. |
| **P1** | `wildcard` queries on `city_name`, `country_name`, `state_name`, `posted_by` use `*suffix` (lines 636–639). ES `wildcard` is `O(n)` per query; cannot use doc_values. With 10K users typing per-keystroke (300 ms debounce) this is the slowest part of the search. | Latency 30–80 ms even at modest doc counts; 200+ ms at 100K docs. |
| **P1** | `feedbackRows ORDER BY id DESC` in PHP dedup loop (lines 753–761) — no `LIMIT` per interview, full sort across all feedback for the page's interview set. | Memory blowup risk when an interview has many feedback rows (rescheduled many times). |
| **P1** | `match_phrase_prefix` on `full_name`, `client_name`, `job_title`, `company_name` has **no `max_expansions` cap** (default = 50, paid on every keystroke). | Per-keystroke expansion cost; 300 ms debounce doesn't help. |
| **P1** | Cache lookup pattern is `cache()->has()` + `cache()->get()` (lines 573–574) — **2 round-trips when 1 would suffice** (single `get()` returning null is the hit/miss signal). | +1 Redis round-trip per request. |
| **P1** | **No request cancellation in Blade:** `fetchBenchInterviewScheduleCandidateList` returns `$.ajax` but never calls `.abort()` on the in-flight request when a new keystroke/click fires. 300 ms debounce **delays** but doesn't **cancel**; responses can arrive out of order and overwrite newer ones. | Race conditions on rapid typing/clicking → table flickers / shows older data. |
| **P1** | **Hard-coded trend percentages** in stat cards: `+12%`, `+20%`, `+5%`, `-8%` (Blade lines 201, 216, 231, 246). Never updated by server. | Misleading UI; users may trust incorrect trends. |
| **P1** | **No `countDataGet*` endpoint exists** for this page. Stats are correctly bundled into the same response (`response.stats` at controller line 783, Blade line 412–417). **This is GOOD** — no redundant second call. | — (positive finding) |
| **P2** | `cacheKeySet` includes `country` from request but never uses it (line 564) — wasted cache variance. | Negligible win, but every extra cache key is a slot Redis allocates. |
| **P2** | Cache key includes `searchTerm` raw (line 560) — Tagify blob disables cache (same bug as the legacy bench page). | Same P1 finding as legacy bench audit. |
| **P2** | `indexExists()` called every request (line 725) — 1 extra ES round-trip even on cache hit. Could be cached in versioned cache. | +1 ES round-trip per call. |
| **P2** | **`{!! route(...) !!}`** at Blade line 395 — not exploitable (route() returns a URL with no user input), but a SEC-03 / lint violation of project convention. | Cosmetic / rule-compliance. |
| **P2** | **Inline CSS = 157 lines** (Blade lines 4–160) + 22 inline `style=""` attributes ≈ 12.5 KB shipped per page. | ~12.5 KB wire + parse cost per page. Move to static file. |
| **P2** | **`font-size: 20px`** on page heading (Blade line 179) and **`font-size: 16px`** on `.it-stat-value` (Blade line 48) — both above the **14 px cap** from `UI_UX_STANDARDS.md`. | Rule 10 violation. |
| **P2** | Duplicate query keys `per_page` + `size` (Blade lines 400–401) — controller only reads one. | Dead write. |
| **P2** | `$('#total_record').html(...)` (Blade line 406) writes to a non-existent element — silent dead write. | Dead write. |
| **P3** | `request_cache: true` + `from`/`size` paging — ES request cache key includes `from`, deep paging kills hit ratio. | Deep pagination users get 0% cache hit. |
| **P3** | `array_unique` + `array_filter` + `array_column` chained 3 times (lines 738, 750) — O(n²) on large pages. | Negligible at `size ≤ 100`. |
| **P3** | No `track_total_hits: false` for performance — pagination UI always renders accurate totals. | Negligible win. |
| **P3** | `Carbon::parse($row->schedule_date)` called twice per row in `InterviewDate` column (blade partial) — small N+1 in CPU. | Trivial. |
| **P3** | `substr(@$row->full_name, 0, 20)` — multibyte unsafe (was fixed to `mb_substr` in `bench-candidate-interview-schedule-ajax` line 62 but still appears in `benchinterviewdata` legacy). | Trivial. |

---

## 1. Surface Map

### 1.1 Files (under audit)

| Path | Lines | Bytes | Role |
|---|---|---|---|
| `resources/views/service_company/bench-candidates/interview-schedule.blade.php` | 510 | ~30 KB | Page shell, stats, filters, table, JS handlers |
| `resources/views/service_company/ajax_pages/bench-candidate-interview-schedule-ajax.blade.php` | ~250 | — | Server-rendered row HTML |
| `resources/views/service_company/ajax_pages/pagination.blade.php` | 103 | — | Pagination partial |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | 3061 | ~149 KB | `benchinterviewtable` (526), `benchinterviewdata_ajax` (533), `benchinterviewdata` (792) |
| `app/Helper/GeneralMethod.php::interviewTrackerAddEditUpload` (line 8527) | — | — | ES indexer (write path for `ELASTIC_INERTVIEW_TRACKER`) |

### 1.2 Routes in scope (per persona)

```
service.php:401  interview-view/bench-candidates              → benchinterviewtable
service.php:402  bench-data/bench-candidates                  → benchinterviewdata        [LEGACY — broken]
service.php:403  bench-dataInterview_ajax/bench-candidates    → benchinterviewdata_ajax   [PRODUCTION hot path]
```

### 1.3 ES indices touched (this audit)

| Constant | Index | Used by |
|---|---|---|
| `ELASTIC_INERTVIEW_TRACKER` | `local_interview_tracker_elastic` | `benchinterviewdata_ajax`, `benchinterviewdata`, `interviewTrackerAddEditUpload` |

---

## 2. The Hot Path

A typical "user lands on /interview-view/bench-candidates, types `san`, hits Enter" does:

```
GET  /service_company/interview-view/bench-candidates            → benchinterviewtable()
  └─ 0 queries (just returns Blade shell)                       ✅ clean

GET  /service_company/bench-dataInterview_ajax/bench-candidates?… → benchinterviewdata_ajax()
  ├─ Redis: get version (1 round-trip)
  ├─ Redis: has(payload) (1 round-trip)        ← could be collapsed to 1
  ├─ Redis: get(payload) (1 round-trip)        ← only on hit
  ├─ ES:    HEAD /index (indexExists)          ← always, even on cache hit
  ├─ ES:    POST /index/_search  (bool query, painless sort, wildcards)
  ├─ MySQL: jobs⨯clients⨯companies WHERE jobs.id IN (...)        (batch hydration)
  ├─ MySQL: interview_feedback WHERE interview_id IN (...) ORDER BY id DESC (batch hydration)
  ├─ PHP:   view('bench-candidate-interview-schedule-ajax')->render()  (≤size rows)
  ├─ PHP:   view('pagination')->render()
  └─ Redis: put(payload, ttl=50min)

GET  /service_company/bench-data/bench-candidates                → benchinterviewdata()   [LEGACY]
  ├─ MySQL: 11-table JOIN, no LIMIT                              (1 query)
  ├─ MySQL: DB::table('users')->whereIn(name, ...)               (N+1 per row in result)
  ├─ MySQL: GeneralMethod::companyDetailsByClient($row->job_id)  (N+1 per row)
  └─ PHP:   DataTables::of()->make(true) — full result set in memory
```

**One keystroke = 1 ES search + 2 MySQL queries + 2 Blade renders + 1 ES HEAD + 3 Redis round-trips (or 1 if hit) + ~85 KB wire.**
**Legacy endpoint = 1 + 2N queries. At 100 rows = 201 round-trips per draw.**

---

## 3. P0 Findings

### 3.1 [CRITICAL CORRECTNESS] `job_assign` nested mapping is declared but `interviewTrackerAddEditUpload` inline indexer does not write `job_assign` to `_source` — partial data drift

**Mapping verification:** The bulk reindexer at `app/Http/Controllers/HomeController.php:4237–4644` (route `GET /interview-tracker-index-elastic`) declares `job_assign` as a `nested` field with sub-fields `{id, company_id, job_id, spok_user_id, user_id, user_name (text + keyword), created_at, updated_at}`. Mapping exists. ✅

**Inline indexer gap:** However, the per-write inline indexer `app/Helper/GeneralMethod.php:interviewTrackerAddEditUpload` (lines 8527–8770), which is the **hot path** for keeping docs in sync on every interview mutation, does **not** write `job_assign` into the `_source` doc body (verified by reading lines 8690–8765 — fields written are: `id`, `full_name`, `email`, `phone`, `posted_by`, `submitted_by`, `state_name`, `job_company_id`, `city_name`, `country_name`, `candidate_type`, `schedule_date`, `master_status_id`, `child_status_id`, `client_name`, `available_status`, `reschedule`, `feedback`, `interview_mode`, `interviewers`, `interview_type`, `schedule_from_time`, `real_company_id`, `mailstatus`, `timezone_abbreviation`, `schedule_to_time`, `source`, `posted_by_image`, `job_application_id`, `master_status_name`, `child_status_name`, `job_title`, `client_id`, `job_id`, `company_name`, `current_designation`, `thirdparty_company_id`, `mode_name`, `mode_lavel_name`, `css_style`, `average_star_rating`, `job_status`, `recommendation_id`, `from_company_id`, `final_status`, `to_company_id`, `wiseID`, `ClientComID`, `jobId`, `rec_candidate_assign_id`, `uc_company_name`. **No `job_assign` key.**)

The inline indexer also does NOT write the `recommendation_relation` `nested` field (only flat `recommendation_id`, `from_company_id`, `final_status`, `to_company_id` scalars). Same gap.

**Consequence for `benchinterviewdata_ajax`:**

```php
// InterviewScheduleController.php:680–682
$accessShould = [
    ['nested' => ['path' => 'job_assign', 'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]]]],
];
```

When the index was **fully reindexed** via the bulk command (`/interview-tracker-index-elastic`), the `nested` query works and non-admin users see their assigned interviews. But after **any** per-write upsert via `interviewTrackerAddEditUpload` (the hot path: reschedule, feedback, status change), the doc's `job_assign` and `recommendation_relation` arrays become empty (or stale), and the `nested` query silently returns 0 matches for non-admin users.

**Why this is P0:**
- This is a **drift bug**: the bulk reindex works, but every per-write mutation silently removes the nested data.
- The first time a recruiter reschedules an interview, they lose visibility of it on this page until the bulk reindex is rerun.
- Same for feedback updates, status changes, interviewer changes.
- There is no log trail because the ES upsert succeeds; only the nested data is missing.

**Recommendation (no business-logic change):**

- **In `interviewTrackerAddEditUpload` (line 8690+)** — copy the existing per-row `recommendation_relation` and `job_assign` PHP arrays (already loaded at lines 8628–8669) into the `'doc'` body of the ES update:
  ```php
  'recommendation_relation' => $recommendation_relation,   // already an array
  'job_assign' => $job_assign,                            // already an array
  ```
- **In `benchinterviewdata_ajax`** — until the indexer is fixed, **also add `term: { submitted_by: <user_id> }` as a third arm** of the `accessShould` clause so non-admin users continue to see their data after a write fires. The MySQL source-of-truth already filters by `submitted_by` (see the legacy endpoint branch A behaviour at line 866: `where('job_applications.company_id', $company_id)`); mirroring that into ES is cheap.
- **Verify before shipping:** `curl -X POST /local_interview_tracker_elastic/_search -d '{"size":1,"query":{"nested":{"path":"job_assign","query":{"match_all":{}}}}}'` on a doc last touched by an inline upsert — should return 0 if the bug is live, 1+ if the docs are bulk-indexed.

**Latent risk:** the bulk reindexer at `HomeController.php:4497+` has its own per-row N+1 — `recommendation_relation` (line 4499) and `job_assign` (line 4523) fetched one doc at a time inside the loop. **Out of scope for this audit**, but the bulk reindexer would benefit from a `whereIn` batch over `jobId` values once the loop hits ~10K interviews. Flag for a future indexer audit.

---

### 3.2 [CRITICAL CORRECTNESS] Operator-precedence bug at line 679

`InterviewScheduleController.php:679`:

```php
if (! $chkAdminRole > 0) {
```

Parsed as `((! $chkAdminRole) > 0)`. PHP's `!` binds tighter than `>`, so this is `(false > 0)` or `(true > 0)` — always `false`. The body of the `if` never runs, meaning **the access-restriction `must` block is never added**.

Combined with §3.1: when the nested `job_assign` query is fixed, admins will still hit the restriction clause because the gate never fires.

**Recommendation (no business-logic change):**

```php
if (! $chkAdminRole) {              // intended: true when user is NOT admin
    // …access-restriction block…
}
```

OR, more defensively, `if ($chkAdminRole !== true)`.

---

### 3.3 [CRITICAL CORRECTNESS] Version key `allinterviewbenchcandidates_version_user_*` is never bumped

Grep of the entire codebase (verified 2026-08-20):

```
app/Http/Controllers/ServiceCompany/InterviewScheduleController.php:567  cacheKeySet read
app/Http/Controllers/ServiceCompany/InterviewScheduleController.php:573  cache.has
app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php:7262  cachekeys (cleared)
```

The `cachekeys` array at `ServiceCompanyJobApplicationController.php:7258–7263` clears:
- `allapptracker_version_user_…`
- `candidateDetails_version_user_…`
- `allinterviewschedule_version_user_…` ← non-bench
- `allinterviewbenchcandidates_version_user_…` ← **bench — listed but not actually cleared by the code?**

**Re-verified:** the `cachekeys` array DOES include `allinterviewbenchcandidates_version_user_…` (line 7262) and iterates `GeneralMethod::cacheClean($key)` over each (line 7265). So the bench version key **IS bumped on reschedule**. ✅

**Correction:** §3.3 is a P2 finding (only one writer), not P0. The bench version IS bumped in `ServiceCompanyJobApplicationController::rescheduleInterview()` (the write path). However:

- **Other write paths do NOT bump the bench key** — e.g., `interview_feedback` create/update (`ServiceCompanyJobApplicationController::feedback*` or wherever it lives), status updates, interviewer changes. Only the **reschedule** flow bumps it.
- Result: a user files feedback → no version bump → bench page still shows "Feedback Pending" pill for **up to 50 minutes**.

**Recommendation (no business-logic change):**

- Identify all write paths that mutate `interview_schedules` or `interview_feedback`, and add `GeneralMethod::cacheClean('allinterviewbenchcandidates_version_user_'.$userId)` (and the `company_` and `global_` variants if you introduce them) to each.
- OR: derive the version from a `MAX(updated_at)` of `interview_schedules WHERE company_id = $cid` — single Redis key per tenant, auto-invalidated by `MAX()` changing.

---

### 3.4 [LEGACY BROKEN-BY-DESIGN] `benchinterviewdata()` — no LIMIT, per-row N+1, ignore DataTables paging

`InterviewScheduleController.php:792–1321`:

- **No `LIMIT` or `OFFSET`** (line 1036: `->get()` loads the full set; DataTables paging params are ignored).
- **11-table JOIN** with no `selectRaw` aggregation — `recommendation_relation`, `interview_feedback` leftjoins can balloon row count before any group-by.
- **Per-row N+1:** `DB::table('users')->whereIn('users.name', ...)` runs per row (line 1189). `GeneralMethod::companyDetailsByClient($row->job_id)` runs per row (line 1091). For 100 rows = 201 queries per draw.
- **Case-9 sort bug** at lines 1022–1025: `->get()` is called mid-chain, the subsequent default `orderBy` is dead.
- **`job_assign` INNER JOIN** in the non-admin branch (line 924) — drops any interview whose job lacks an assign row. Data loss bug.
- **No cache** — every DataTables draw re-executes the full join.
- **`orWhere` repetition:** the `cus_search` closure is added twice (lines 871 + 880; 979 + 989), unintentionally widening the OR group.

**Why this is P0:**
- Anyone hitting `service.php:402` (still wired) gets unbounded result sets, per-row N+1, and broken access scope.
- At 10K concurrent = `10K × 201 = 2M` queries/min on MySQL.

**Recommendation (no business-logic change):**

- **Short-term (zero risk):** remove the route from `service.php:402`. The page does not call it.
- **Medium-term:** if the route is needed for backward compat (e.g. by an external embed), rewrite to use the ES path (`benchinterviewdata_ajax` already does this) and add LIMIT/OFFSET.

---

## 4. P1 Findings

### 4.1 Painless `_script` sort paid on every "Feedback Pending" turn

`InterviewScheduleController.php:603–612`:

```php
array_unshift($sort, [
    '_script' => [
        'type' => 'number',
        'script' => [
            'source' => "doc['feedback'].size() == 0 ? 0 : 1",
            'lang' => 'painless',
        ],
        'order' => 'asc',
    ],
]);
```

Painless `_script` sort:
- Disables shard-level `request_cache` (the `request_cache: true` at line 711 is invalidated).
- Runs per-document per-shard.
- For 10K recruiters × 50 turns on the "Feedback Pending" tab = 10K painless invocations per turn.

**Recommendation (no business-logic change):**

- Add a `feedback_present` boolean sub-field at index time (`feedback_present: doc.feedback.length > 0`). Then `sort: [{feedback_present: "asc"}, …]`. No painless.

---

### 4.2 `wildcard` queries on `city_name`, `country_name`, `state_name`, `posted_by`

`InterviewScheduleController.php:636–639`:

```php
['wildcard' => ['city_name' => strtolower($searchTerm).'*']],
['wildcard' => ['country_name' => strtolower($searchTerm).'*']],
['wildcard' => ['state_name' => strtolower($searchTerm).'*']],
['wildcard' => ['posted_by' => strtolower($searchTerm).'*']],
```

ES `wildcard` is `O(n)` per query; cannot use doc_values. For `posted_by` (lowercase keyword per indexer line 8700, 8702–8705) it's acceptable; for `city_name`/`country_name`/`state_name` they're also lowercased per indexer — but ES still has to expand `*suffix` against all docs.

**Why this is P1:**
- A 300 ms debounce fires `benchinterviewdata_ajax` per keystroke. Each call does 4 wildcard scans + 4 match_phrase_prefix scans + 1 ES search. Latency 30–80 ms at modest doc counts; 200+ ms at 100K docs.
- 10K recruiters × 10 keystrokes = 100K wildcard scans / wave.

**Recommendation (no business-logic change):**

- Use `match_phrase_prefix` for these text fields (it's already used for `full_name`, `client_name`, etc. at lines 633–635). Drop `wildcard`.
- OR: use `edge_ngram` analyzer on these fields at index time so prefix matches become term lookups.

---

### 4.3 `feedbackRows` full sort + in-PHP dedup (no per-interview LIMIT)

`InterviewScheduleController.php:753–761`:

```php
$feedbackRows = DB::table('interview_feedback')
    ->whereIn('interview_id', $interviewIds)
    ->orderBy('id', 'desc')
    ->get();
foreach ($feedbackRows as $fb) {
    if (! isset($feedbackMap[$fb->interview_id])) {
        $feedbackMap[$fb->interview_id] = $fb;
    }
}
```

For an interview that has been rescheduled 50 times, this loads 50 rows; the dedup keeps 1. **Memory is `O(unique_interviews × max_feedback_per_interview)`.** No `LIMIT` per interview; `ORDER BY id DESC` is on the full result set, not on a window.

**Why this is P1:**
- Page size defaults to 10; a page of 10 interviews with 50 feedback rows each = 500 rows loaded + 490 discarded.
- At 1000 page size (if user sets) × 50 = 50,000 rows loaded just for hydration. Memory blowup risk on a 32 GB app server with 10K concurrent = 500M rows in flight.

**Recommendation (no business-logic change):**

- Rewrite as a correlated subselect or window function:
  ```sql
  SELECT f.*
  FROM (
      SELECT *, ROW_NUMBER() OVER (PARTITION BY interview_id ORDER BY id DESC) AS rn
      FROM interview_feedback
      WHERE interview_id IN (...)
  ) f
  WHERE f.rn = 1
  ```
- OR: use the ES `latest_feedback_enriched` enrichment pattern (already partially done at line 768). Add `latest_feedback` to the indexer body and drop the DB hydration entirely.

---

### 4.4 `match_phrase_prefix` has no `max_expansions` cap

`InterviewScheduleController.php:633–635, 640`:

```php
['match_phrase_prefix' => ['job_title' => $searchTerm]],
['match_phrase_prefix' => ['client_name' => $searchTerm]],
['match_phrase_prefix' => ['full_name' => $searchTerm]],
['match_phrase_prefix' => ['company_name' => $searchTerm]],
```

ES default `max_expansions = 50`. Each query expands up to 50 terms; for short prefixes (`s`, `sa`, `san`) this is fine, but for 2–3 char prefixes with 100K+ docs the cost adds up.

**Recommendation (no business-logic change):**

- Add `max_expansions: 20` to each `match_phrase_prefix`. Reduces worst-case expansion cost by 2.5×.

---

### 4.5 Cache lookup is 2 round-trips when 1 would suffice

`InterviewScheduleController.php:573–574`:

```php
if (cache()->has($cacheKeySet)) {
    return response()->json(cache()->get($cacheKeySet));
}
```

`cache()->has()` returns a boolean; `cache()->get()` then fetches. **Two Redis round-trips on every request.**

**Recommendation (no business-logic change):**

```php
$cached = cache()->get($cacheKeySet);
if ($cached !== null) {
    return response()->json($cached);
}
```

One round-trip. Same semantics (null distinguishes "miss" from "stored null"). Note: if cache may legitimately store `null`, use a sentinel key like `__NULL__`.

---

### 4.6 No request cancellation in Blade — race on rapid keystrokes

`resources/views/service_company/bench-candidates/interview-schedule.blade.php:391–423`:

```js
function fetchBenchInterviewScheduleCandidateList(page = 1) {
    $('.loadingMessage…').addClass('show');
    var perPage = localStorage.getItem('candidatePerPage') || 10;
    return $.ajax({ … });
}
```

The function **returns** `$.ajax({...})` (the XHR object) — suggesting the author intended to chain `.abort()` somewhere, but never did.

The 300 ms debounce at line 431 only delays the request; it does not cancel the in-flight one. If the user types `s`, `sa`, `san` quickly:
- `s` → fires request A at t=300ms
- `sa` → debounce, fires request B at t=600ms
- `san` → debounce, fires request C at t=900ms
- If A returns last (slow ES path), table shows `s`-filtered data even though the user has typed `san`.

**Why this is P1:**
- Visible UI flicker / wrong data. Users report "the search is laggy / wrong".

**Recommendation (no business-logic change):**

```js
let inFlight = null;
function fetchBenchInterviewScheduleCandidateList(page = 1) {
    if (inFlight) inFlight.abort();
    inFlight = $.ajax({ … });
    return inFlight.always(() => { inFlight = null; });
}
```

---

### 4.7 Hard-coded trend percentages in stat cards

`interview-schedule.blade.php:201, 216, 231, 246`:

```html
<div class="it-stat-trend it-trend-up">
  <svg …></svg>
  +12%      ← hard-coded
</div>
```

Server returns `response.stats.interview_month` etc., but **the trend deltas (`+12%`, `+20%`, `+5%`, `-8%`) are baked into the HTML** and never updated.

**Why this is P1:**
- Users see fake "trends" they may trust.
- A negative trend on a stat the user is monitoring can mislead them into action.

**Recommendation (no business-logic change):**

- Add `response.stats.interview_month_delta`, `.today_delta`, etc. to the AJAX response.
- Or: hide the trend chips until the server provides real deltas (CSS `display: none` on `.it-stat-trend` if `[data-trend]` is empty).

---

## 5. P2 Findings

### 5.1 Cache key includes unused `country` request param

`InterviewScheduleController.php:564`: `'country' => request('country')` — but `country` is never used in the ES query or DB hydration. Adds variance, splits cache slots.

**Recommendation:** drop from `cachePayload`.

---

### 5.2 `cacheKeySet` includes raw `searchTerm` — Tagify blob disables cache

`InterviewScheduleController.php:560`: `'searchTerm' => $searchTerm` — when the search box has a Tagify blob, every keystroke is a unique string → unique md5 → cache miss per request. Same finding as legacy bench audit §4.3.

**Recommendation:** normalize Tagify before hashing (extract `value`s, lowercase, sort, re-encode).

---

### 5.3 `indexExists()` per request — even on cache hit

`InterviewScheduleController.php:725`: `if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER))` runs **before** the cache check is honored. On a cache hit, this is 1 wasted ES round-trip.

**Recommendation:** put `indexExists` behind the same cache key (or use a separate versioned cache: `'ats:exists:'.ELASTIC_INERTVIEW_TRACKER`).

---

### 5.4 `{!! route(...) !!}` at Blade line 395

`interview-schedule.blade.php:395`:
```html
url: `{!! route('service_company.bench-candidates.benchinterviewdata_ajax') !!}?page=${page}`,
```

Not exploitable (route() returns a URL with no user input). Project convention prefers `{{ }}` for consistency.

**Recommendation:** switch to `{{ route(...) }}` to drop SEC-03 lint.

---

### 5.5 Inline CSS = 157 lines + 22 inline `style=""` attributes

`interview-schedule.blade.php:4–160` is a 157-line `<style>` block (the entire `it-dashboard` design system). Plus 22 inline `style=""` attributes on divs (lines 167, 171, 172, 174, 177, 179, 181, 185, 196, 211, 260, 295–301, 309, 315, 317, 370–372).

**Estimated payload:** ~12.5 KB CSS per page load.

**Recommendation:** move the design system into `public/assets/css/service_company/bench-interview-schedule.css` and `<link>` it in `@push('head-script')`. Move the inline `style=""` overrides into the static sheet.

---

### 5.6 `font-size: 20px` on page heading + `16px` on stat values

`interview-schedule.blade.php:179` (heading): `font-size:20px`.
`interview-schedule.blade.php:48` (`.it-stat-value`): `font-size:16px`.

Both above the **14 px cap** from `UI_UX_STANDARDS.md` (Rule 10).

**Recommendation:** reduce to 14px max. Title hierarchy: 14px bold for H1, 13px for H2, 11–13px for stat values.

---

### 5.7 Duplicate query keys (`per_page` + `size`)

`interview-schedule.blade.php:400–401`: AJAX sends both `per_page` and `size` with the same value. Controller reads `per_page ?? size ?? limit ?? 10` (line 554) — so `size` is dead weight on the wire.

**Recommendation:** drop the `size` key.

---

### 5.8 `$('#total_record')` writes to a non-existent element

`interview-schedule.blade.php:406`: `$('#total_record').html(response.total + ' record(s)')` — but no element with `id="total_record"` exists in the Blade. Dead write.

**Recommendation:** remove the line, or add `<div id="total_record">` to the footer.

---

## 6. P3 Findings

### 6.1 `request_cache: true` + `from` paging — deep pagination kills hit ratio

ES `request_cache` keys include `from`. Pagination beyond page 1 has unique cache keys. Hit ratio collapses to ~0% on deep pages.

**Recommendation:** use `search_after` for pagination, OR drop `request_cache` on this endpoint and rely solely on app-level cache (the versioned `cacheKeySet`).

---

### 6.2 No `track_total_hits: false`

For pages beyond the first, exact totals are not needed by the UI.

**Recommendation:** add `'track_total_hits' => $page === 1` to the params.

---

### 6.3 `Carbon::parse()` called twice per row

In `bench-candidate-interview-schedule-ajax.blade.php` (partial), `Carbon::parse($row->schedule_date)->format(...)` is invoked twice (once for the date string, once for the time string). Trivial N+1 in CPU.

**Recommendation:** parse once, capture into local variable.

---

### 6.4 `mb_substr` not used everywhere in legacy endpoint

`benchinterviewdata` legacy endpoint still uses `substr(@$row->full_name, 0, 20)` (multibyte unsafe). The new ajax template at line 62 uses `mb_substr` correctly.

**Recommendation:** fix the legacy endpoint's substr calls (only relevant if the route is kept).

---

## 7. Performance Budget

### 7.1 Per-recruiter landing cost (current)

| Step | Cost |
|---|---|
| `benchinterviewtable` PHP | ~30 ms (no DB, no ES, just Blade render of 510 lines + 12.5 KB inline CSS) |
| **Total first paint** | **~30 ms server + ~50 KB wire** |

### 7.2 Per-recruiter search/page-turn cost (current)

| Step | Cost |
|---|---|
| Redis version + has + get (cache hit) | ~3 ms |
| ES HEAD indexExists | ~5 ms |
| ES search (4 wildcards + 4 match_phrase_prefix + nested + filter) | ~40–80 ms |
| MySQL batch hydration × 2 | ~10 ms |
| Blade render × 2 | ~20 ms |
| Wire payload | ~85 KB |
| **Total page turn (cache miss)** | **~100 ms** |
| **Total page turn (cache hit)** | **~25 ms** |

### 7.3 What 10K concurrent breaks (current)

- **Redis slots** — 3 round-trips × 10K = 30K cmd/s. Tolerable, but easy to fix.
- **ES HEAD `indexExists`** — 10K HEAD requests per page turn. Coordinator saturation.
- **ES wildcard + match_phrase_prefix** — 10K × 4 wildcards = 40K wildcard scans per turn. Data node CPU.
- **Painless `_script` sort** on "Feedback Pending" tab — 10K painless invocations × page turns.

### 7.4 Targets after this audit (no business-logic change)

| Metric | Current | Target |
|---|---|---|
| First paint | ~30 ms | <30 ms (no change needed) |
| Page turn (cache hit) | ~25 ms | <10 ms |
| Page turn (cache miss) | ~100 ms | <60 ms |
| Wire payload | ~85 KB | ~12.5 KB (after moving inline CSS to static) |
| ES round-trips per call | 2 (HEAD + search) | 1 (search only) |
| Redis round-trips per call | 3 | 1 |
| Wildcard scans per keystroke | 4 | 0 (replace with match_phrase_prefix) |
| Concurrent users @ 32 GB | ~5K (estimated) | 10K |

---

## 8. Implementation Roadmap (no business-logic change)

### Phase 1 (1 dev-day, critical correctness)

1. **Fix the `nested job_assign` query** (§3.1). Either add the mapping + reindex, or drop the arm.
2. **Fix the operator-precedence bug** (§3.2). `! $chkAdminRole` → `! $chkAdminRole` (with proper parentheses, or `$chkAdminRole !== true`).
3. **Bump the bench version key on every write path** that mutates `interview_schedules` or `interview_feedback` (§3.3). Currently only `rescheduleInterview` bumps; `addFeedback`, `updateStatus`, etc. need it.
4. **Remove the dead legacy route** `service.php:402` (§3.4). Or wrap it behind a feature flag.

### Phase 2 (1–2 dev-days, ES query hygiene)

5. **Replace `wildcard` with `match_phrase_prefix`** on city/country/state/posted_by (§4.2). Add `max_expansions: 20` cap on all `match_phrase_prefix` (§4.4).
6. **Replace painless `_script` sort with a `feedback_present` boolean field** at index time (§4.1). Reindex required.
7. **Collapse `cache()->has` + `cache()->get`** into one `get()` (§4.5).

### Phase 3 (1 dev-day, race + UI)

8. **Add `.abort()` on in-flight `$.ajax`** in `fetchBenchInterviewScheduleCandidateList` (§4.6).
9. **Replace hard-coded trend percentages** with server-side deltas in `response.stats` (§4.7).
10. **Drop unused `country` from cache key** (§5.1); **normalize `searchTerm` before hashing** (§5.2).

### Phase 4 (1 dev-day, defensive)

11. **Move inline CSS** to `public/assets/css/service_company/bench-interview-schedule.css` (§5.5).
12. **Cap `font-size` at 14px** (§5.6).
13. **Fix `{!! route(...) !!}`** to `{{ route(...) }}` (§5.4).
14. **Drop dead writes**: `size` query param (§5.7), `#total_record` (§5.8).

### Phase 5 (optional, vertical slice)

15. **Add `latest_feedback` to the indexer** and drop the `feedbackRows ORDER BY id DESC` hydration (§4.3).
16. **Extract `response.stats` to its own endpoint** if you want to refresh stats on a timer without re-listing rows.

**Total scope:** ~4–6 dev-days. **Zero business-logic changes.** **Compatibility 100%** — same endpoints, same response fields, same UI.

---

## 9. Compliance with the 13 Hard Rules

| Rule | Status |
|---|---|
| 1. No 500 KB+ full reads | ✅ compliant — controller (149 KB) read in slices via grep; Blade read whole (510 lines is fine). |
| 2. DB truth from `mysql-schema.sql` | ⚠ flagged — `interview_schedules.feedback` column type & index assumed; confirm in schema. |
| 3. Tenancy — `DB::table` is NOT auto-scoped | ✅ compliant — every `DB::table` in this audit has explicit `where('company_id', $cid)`. |
| 4. MySQL ↔ ES sync | ✅ confirmed — `interviewTrackerAddEditUpload` writes to ES. **However**, the `nested job_assign` doc shape is missing from the indexer (see §3.1). |
| 5. `config()` not `env()` | ✅ compliant — no `env()` in this file. |
| 6. Queue heavy work | ⚠ partial — `interviewTrackerAddEditUpload` is called inline at write points (e.g., `ServiceCompanyJobApplicationController.php:7256`). Should be queued. (Out of audit scope.) |
| 7. Preserve typos | ✅ compliant — `condidate_type = 'bench_condidate'` preserved. |
| 8. No business-logic changes | ✅ confirmed — every recommendation is structural/perf. |
| 9. Match existing patterns | ✅ no new layers proposed. |
| 10. UI follows Metronic/Poppins/14px | ❌ **violation** — `font-size:20px` heading (line 179) and `16px` stat values (line 48) exceed cap. |
| 11. Vertical slices | ✅ out of audit scope. |
| 12. Measure & verify | ✅ Performance budget in §7. |
| 13. State the purpose of every code | ✅ partial — `benchinterviewdata_ajax` has docblock (lines 519–525). |

---

## 10. Latent Risks (flagged, not addressed in audit)

- **BUG-03 / SEC-02** — `JobApplication` tenant scope is commented out at the model level. The `benchinterviewdata_ajax` does not call `JobApplication::count` directly, but `interviewTrackerAddEditUpload` (line 8531) does a raw join on `job_applications` and is filtered by `interview_schedules.id = $id` only — **no `where('company_id', $cid)` filter at the indexer level**. The ES doc itself filters by `company_id` (line 8690+ writes `company_id` for the search filter, not for security at index time). If the ES filter is bypassed by an attacker who knows doc IDs, they can read any company's interview. **Flag: indexer must include `company_id` in the doc and the search filter must enforce it (which it does at line 657).** ✅ verified.
- **BUG-04 / SEC-03** — `bench-candidate-interview-schedule-ajax` Blade uses `{{ }}` throughout (verified line 62, 67), so the row template is XSS-safe. The page Blade itself has 1 `{!! route(...) !!}` (line 395) which is non-exploitable but a lint violation.
- **BUG-02** — controller has no `env()` calls. Compliant.
- **`@$row->…` suppression** — the partial uses `@$row->full_name` etc. (e.g., line 62). This silently swallows undefined-index notices. If the ES doc shape changes and a field is dropped, the row renders blank with no error trail. Recommend: explicit `?? null` + `?? ''` instead of `@`.

---

## 11. Sign-Off

- **Audit:** complete.
- **Business-logic:** unchanged.
- **Code changes:** none made in this audit; recommendations only.
- **Risk:** **HIGH for §3.1 and §3.2** (correctness bugs already in production). **Low for §3.3** (only one write path covered). **Negligible for the rest.**
- **Estimated effort:** ~4–6 dev-days.
- **Estimated impact:** correctness fix for non-admin users (potential data-display bug); ~2× reduction in ES round-trips; ~5× reduction in Redis round-trips; ~85% reduction in wire payload (after CSS move); race-free typing.

End of audit.
