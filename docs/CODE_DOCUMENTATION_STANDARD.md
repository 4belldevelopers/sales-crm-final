# Code Documentation Standard — "State the Purpose of Every New Code"

> **MANDATORY for all new and modified code.** Every new file, class, method, complex
> query, and non-obvious block must say **what it does and why**, in a short comment, so
> any AI agent or human understands it instantly without reading the whole codebase.
> This is the #1 way to keep a huge, fast-growing codebase understandable.

Principle: **comment the *intent* (the why), not the obvious *mechanics* (the what the
code already shows).** A reader should grasp a unit's purpose in one breath.

---

## 1. The rules (what must be documented)

| Unit of code | Required documentation |
|---|---|
| **New file** | A 1–3 line file/class header: purpose, where it fits, key dependencies |
| **Class** | Docblock: responsibility (one sentence) + collaborators |
| **Public method / function** | Docblock: purpose, `@param`, `@return`, side effects (queue? ES? mail?) |
| **Complex/raw query** | Inline comment: what it returns, why this shape, **tenancy note** (`company_id`?) |
| **Business logic** (ranking, matching, billing, status) | Comment the rule + a pointer to the spec/doc |
| **Elasticsearch sync** | Note which index is kept in sync and when |
| **Workaround / non-obvious choice** | Comment why + link issue/ADR; mark `// TODO:`/`// NOTE:` |
| **Blade component / partial** | Top comment: what it renders + expected data |

If a method is so big it needs a paragraph to explain, that's a signal to **extract it**
(strangler pattern) — see [AI_RULES.md](AI_RULES.md).

---

## 2. Templates (copy these)

### File / class (PHP)
```php
<?php
// CandidateIndexService — builds denormalized candidate docs from MySQL and writes them
// to the `ats_candidates_elastic` index (the read store for CV/App trackers).
// Source of truth: job_applications + add_job_wise_condidates. Called by ElasticCandidateIndex job.

namespace App\Services;

class CandidateIndexService
{
```

### Public method (PHP docblock)
```php
/**
 * Re-index a single candidate into Elasticsearch after a pipeline change.
 *
 * @param  int  $applicationId  job_applications.id (tenant-owned)
 * @return void
 * @sideeffect Writes to ES index `ats_candidates_elastic`; reads MySQL.
 *             Dispatched from JobApplicationObserver — do not call inline in a request.
 */
public function reindex(int $applicationId): void
{
```

### Complex / raw query (intent + tenancy)
```php
// Active candidates for this job, newest first, for the tracker grid.
// NOTE: raw query — Eloquent's company scope does NOT apply here, so we filter
// company_id explicitly to prevent cross-tenant leakage (see SECURITY.md SEC-02).
$rows = DB::table('job_applications')
    ->where('company_id', $companyId)   // tenant isolation (mandatory)
    ->where('job_id', $jobId)
    ->where('status', 'active')
    ->orderByDesc('created_at')
    ->get();
```

### Business rule
```php
// BUSINESS RULE: a candidate counts as "placed" only when an OfferJobApplication exists
// AND the bench guarantee window (placed_end_date) has not lapsed. Do not change without
// product sign-off — affects billing/marketplace settlement. Spec: docs/DATA_FLOW.md §6.
```

### Controller method (thin caller)
```php
/** Show the CV tracker for the current tenant. Route: service_company.cv-tracker.index */
public function index(Request $request)
{
    // Delegates to the service; controller stays thin (see AI_RULES.md #9).
    return view('service_company.cv-tracker.index', $this->tracker->forCompany($request));
}
```

### Queued job
```php
/**
 * Uploads a candidate resume to S3 and records the Document hashname.
 * Queued (Redis/Horizon, `uploads` queue) — heavy I/O must not block the request.
 */
```

### Observer
```php
// Keeps ES in sync: on JobApplication saved/deleted, dispatch ElasticCandidateIndex so
// `ats_candidates_elastic` reflects MySQL. Guard against save-loops (no model writes here).
```

### Blade component / view
```blade
{{-- candidate-row: one row in the CV tracker grid. Expects $candidate (ES doc shape).
     Follows Metronic table styles; max font 14px; truncates long text with tooltip. --}}
```

---

## 3. Style rules
- **English, present tense, concise.** One sentence beats a paragraph.
- **Why over what.** `// loop candidates` is noise; `// merge duplicate candidates before indexing` is signal.
- **Keep comments truthful & current** — update the comment when you change the code. A
  wrong comment is worse than none.
- **No commented-out code** left behind (we already have a `dd()`/dead-code problem — see
  [BAD_PATTERNS.md](BAD_PATTERNS.md)). Delete it; git remembers.
- **Tag actionable notes**: `// TODO:`, `// FIXME:`, `// NOTE:`, `// HACK:` (+ link).
- **Follow Metronic** in any UI code comment about styling; note ≤14px density
  ([UI_UX_STANDARDS.md](UI_UX_STANDARDS.md)).
- **Preserve baked-in identifier spellings** in code/comments (`condidate`, `commericial`,
  `Caompany`, `transection`, and `metronics` = *Metronic-theme* related) — see
  [NAMING_CONVENTIONS.md](NAMING_CONVENTIONS.md).

---

## 4. Definition of done (every change)
A change is not complete unless:
- [ ] New files/classes/methods have a purpose header/docblock.
- [ ] Complex/raw queries have an intent + tenancy comment.
- [ ] Business logic & ES-sync points are explained.
- [ ] Comments match the final code (no stale/contradictory notes).
- [ ] No leftover commented-out code or debug calls.

> This standard exists so the next reader — AI or human — understands *purpose* in
> seconds. Treat undocumented new code as an incomplete change.
