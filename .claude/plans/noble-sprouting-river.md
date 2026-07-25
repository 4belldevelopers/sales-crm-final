# Instant Field Parser Pipeline — Phase 9

## Context

Today the recruiter picks a file → upload to R2 runs → `ParseResumeJob`
lands on the Redis queue → a Horizon worker eventually extracts
fields → JS learns via slow Reverb polling (5 s) → empty fields fill.
Recruiter UX bottleneck: even on a phase-2 stub parser the gap is
~600 ms; with a real vendor it's 4–30 s. Recruiters lose focus.

This change introduces a **second, parallel pipeline** for instant
field auto-fill that runs **while the bytes are still uploading to
R2**:

```
Recruiter selects resume
        │
        ├── XHR PUT  ──► Cloudflare R2           (Phase 4–7, untouched)
        │       presign → PUT → finalize → Parsed row
        │       progresses the existing upload bar
        │
        └── fetch POST ──►  POST /api/resume-parser/parse
                in-process text extraction
                returns structured JSON in ~800 ms
                progresses a NEW parallel progress block
                populates only empty fields on the form
                does NOT save anything to the database
                does NOT touch ResumeUploadSession
                does NOT create JobApplication
```

The two pipelines are **strictly independent**. The upload pipeline
keeps every byte of its existing contract (Phase 4–8). The
parser pipeline owns only field extraction + JSON response.

## Architecture

### Change 1 — Lightweight parser endpoint

**New `app/Http/Controllers/Api/ResumeParserController.php`** —
sibling to `ResumeUploadSessionController`. Single action
`parse(Request): JsonResponse`. Accepts `multipart/form-data` with
`file`. Same tenant guard: company from `auth()->user()->company_id`.

**New route file edit `routes/service.php`** — register outside
the existing `service_company` middleware chain because:

- The parser must remain reachable when `EnsureOneDeviceLogin`
  expires an old session mid-Q&A (recruiter on a 2nd device where
  the first device already uploaded).
- We DON'T want this endpoint to require `custom-package` (it's a
  pure extraction service that ships on every plan anyway).
- **Auth + tenant guard only** (chosen). Mount under existing
  `auth` middleware, read `company_id` from
  `$request->user()->company_id`, fail with 403 if missing. The
  response is the user's own bytes — the user can't see other
  companies' data so test coverage can use the recruiter's session
  cookie directly. **No global scope** (CLAUDE.md BUG-03) — direct
  property comparison only.
- Anonymous-with-CSRF was rejected as cross-tenant leak risk.

```php
// routes/web.php (or routes/internal.php — see Plan) —
// mount BEFORE the service_company group so it inherits only 'web':
Route::middleware('auth')->prefix('api/resume-parser')->group(function () {
    Route::post('parse', [\App\Http\Controllers\Api\ResumeParserController::class, 'parse'])
        ->name('api.resume-parser.parse');
});
```

**New `app/Services/ResumeParserService.php`** — the in-process
extraction layer. Uses `smalot/pdfparser ^2.12` (already in
`composer.json` line 46 — verified by scout report) for PDF text,
attempts best-effort heuristics for name/email/phone/skills.

The service returns:
```
[
    'full_name' => ?'Smita Sharma',
    'email' => ?'smita@example.com',
    'phone' => ?'+91 ...',
    'skills' => ['php', 'laravel', ...] | null,
    'experience' => ?'5 years 4 months',
    'education' => ?'B.Tech, IIT Delhi',
    'location' => ?'Bangalore, India',
    'current_company' => ?'Popular Digital AI',
    'current_designation' => ?'Senior Engineer',
]
```

**Strict separation docblock** at the top of this service:

```
- NEVER writes to resume_uploads / upload_session_audit.
- NEVER dispatches a ParseResumeJob or any queue worker.
- NEVER calls headCheck() / presignPut() / uploadDirect().
- NEVER touches the resumable state machine.
- ONLY reads the request's file blob and returns structured hints.
- The existing PHPWord path (composer.json line 41) handles DOC/DOCX
  via the same service so the frontend doesn't branch.
```

Singleton pass-through: when the file extension is `.pdf` we use
`\Smalot\PdfParser\Parser::parseFile($tmpPath)` (smalot reads
straight from disk; we save the uploaded temp to a Symfony
`UploadedFile::getRealPath()` which IS already a tmp path).
`.docx` is a ZIP — pull `word/document.xml`, strip XML, hand to
the same regex heuristics. `.doc` (binary legacy) returns null
keys with `parse_status='doc_legacy_unsupported'`.

- **Return null on uncertain** (chosen). Regex/section cues that
  don't confidently match return `null` for that key. The JS
  module keeps the field empty and the recruiter types it
  manually — never wrong, never noisy with misplaced values.
- Confidence-score scheme was rejected; vendor-only was rejected.

### Change 2 — Dual progress UI in the partial

**Edit `resources/views/partials/resume_presign_setup.blade.php`**:

```blade
{{-- Existing upload bar --}}
<div id="resumeProgress" …>…</div>

{{-- Phase 9 — second progress block, completely independent --}}
<div id="resumeParseProgress" class="mt-2" style="display:none">
  <div class="small text-muted mb-1">Parsing resume</div>
  <ol class="list-unstyled mb-0 text-start" id="resumeParseProgressSteps"
      style="font-size: 12px;">
    <li data-step="uploading">Reading PDF…</li>
    <li data-step="extracting">Extracting text…</li>
    <li data-step="skills">Extracting skills…</li>
    <li data-step="done">Complete ✓</li>
  </ol>
</div>
```

Both bars render simultaneously and never interfere. IDs unique.

### Change 3 — JS module: parallel async upload + parser

**Edit `public/js/resume-upload-progress.js`** — extend the file pick
handler. Today's `handleFile()` (lines ~100-180-ish in the Phase 7
code) fires ONE network fan-out (presign → PUT → finalize). Add a
SECOND independent fan-out launched the moment the file is picked:

```js
// At the top of handleFile(), AFTER bounds validation, BEFORE
// the existing upload fan-out:
_abortPriorInflight('replaced');   // ALSO kills the parser XHR
_state.parseResult = null;
_state.parseStage = 'queued';
emitEvent('parse.started', { size: file.size });
this._dispatchParser(file)
    .then((res) => onParserResolved(res))
    .catch((err) => onParserRejected(err));

// Existing upload fan-out unchanged.
if (cfg.usePresigned) { _runPresignedFlow(file, cfg); }
               else { _runLegacyMultipartFlow(file, cfg); }
```

The parser uses `AbortController` so a re-pick aborts the previous
parser XHR immediately. Resolution handler:

```js
function _dispatchParser(file) {
    const ac = new AbortController();
    _state.parserAbortController = ac;
    return fetch(cfg.parserUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'X-Request-Id': requestId(),
        },
        body: makeMultipart(file),
        signal: ac.signal,
    }).then(function (r) {
        if (!r.ok) { throw new Error('HTTP ' + r.status); }
        return r.json();
    });
}
```

`whenParsed({timeoutMs: 1000})` — Phase 7's existing helper — also
gains a 1 s fast path against in-process parsed values:

```js
whenParsed: function (opts) {
    const timeoutMs = (opts && opts.timeoutMs) || 1000;
    if (_state.parseResult && _state.parseResult.sessionUuid === sessionUuid) {
        return Promise.resolve({ ok: true, parsed: _state.parseResult, via: 'inline' });
    }
    // ...existing Reverb + polling fallback below
}
```

The existing Reverb path stays exactly as it is — it remains the
mechanism for the slow AI/vendor enrichment flow from Phase 8.

### Change 4 — populate-if-empty extension

**Edit `public/js/resume-populate-if-empty.js`** — same no-overwrite
contract as Phase 8 (`fillable` projection). Add `parser.inline`
event handling: the dispatchPromise resolves with structured JSON
and writes only empty fields immediately.

```js
window.ResumeUpload.on('parseInline', function (e) {
    if (! e || ! e.values) { return; }
    Object.keys(e.values).forEach(function (column) {
        const field = document.querySelector('[name="' + column + '"]');
        if (! field || (field.value || '').trim() !== '') { return; }
        field.value = String(e.values[column] || '');
        field.dispatchEvent(new Event('input', { bubbles: true }));
    });
});
```

### Change 5 — Submit handler waits for BOTH

**Edit `resources/views/service_company/job-applications/create.blade.php`**:

```js
const uploadReady = await window.ResumeUpload?.whenReady({ timeoutMs: 30_000 });
const parseReady  = await window.ResumeUpload?.whenParsedInline({ timeoutMs: 2_500 });

// upload: must complete OR timeout (35s total cap).
if (uploadReady && !uploadReady.ok && uploadReady.reason === 'timeout') {
    fail('Upload taking longer than expected. Please retry.');
    return;
}
// parse: 2.5s cap matches the user's "wait 2–3s then proceed" rule.
// If still running, we proceed with submit; the parser JS path is
// already empty-fields-friendly and won't stomp on submit-time DB writes.
```

### Change 6 — Server-side logs + metrics

**`App\Logging\ResumeLogContext`** already supports `event($name,
$payload)`. New event names landed across Phase 9:

- `resume.parse.started` — controller's parse() entrypoint logs size, mime, company_id, request_id.
- `resume.parse.completed` — service returns fields projected; logs duration_ms, fields_extracted.
- `resume.parse.failed` — service throws / controller 422.
- `resume.parse.cancelled` — separately caught via AbortController; logs filename + size.
- `resume.parse.duration` / `resume.parse.size` — composed from completed event payload, used by metrics scrapes.

**`App\Services\ResumeMetrics`** Phase-6 facade already supports
arbitrary counter names. New counter sites in the parser controller:

```
incrementCounter('parse_started_total', ['kind' => 'inline']);
incrementCounter('parse_completed_total', ['outcome' => 'success'|'fail']);
incrementCounter('parse_cancelled_total');
```

The duration is exposed via the existing `/api/internal/health/resume` Phase-6 probe:
add a `parser_inline_avg_ms` and `parser_inline_p95_ms` gauge derived
from a Redis rolling list we'd keep alongside the counter keys. Lua
PEXPIRE trim, but for Phase 9 we ship the counter-only path first
and add the gauge in Phase 10 to keep the diff scoped.

### Change 7 — Tenant/audit/observability

**Tenant guards in the new endpoint** — direct property check on
`$user->company_id`, NOT a global scope. Project rule (CLAUDE.md
BUG-03) forbids blanket scopes on parser routes.

**Audit trail** — no `upload_session_audit` row from the parser
endpoint. Reason: the parser endpoint doesn't own a session row,
doesn't dispatch queue work, and the session-level audit is
exactly the JSON the frontend POSTs back to it for correlation
(`request_id` is reused from the X-Request-Id header propagated
through Phase 6's `RequestContextMiddleware`).

**Phase-6 metrics tie-in** — all 5 events above reach the Redis
counter store via the existing `ResumeMetrics::bump()` path. The
Phase 6 health endpoint already exposes counter reads; we add
`/api/internal/health/resume` keys `parse_started_total` etc.
alongside the existing `orphan_uploads_current` etc.

## File Inventory

### NEW (3 files)
- `app/Services/ResumeParserService.php` — text extraction layer.
- `app/Http/Controllers/Api/ResumeParserController.php` — the endpoint.
- `tests/Feature/ResumeParserServiceTest.php` — extraction accuracy unit tests.
- `tests/Feature/ResumeParserEndpointTest.php` — HTTP / endpoint contract.
- `tests/Feature/ResumeParserJsFlowTest.php` — JS module wiring at the JS-side level (covered via PhpUnit process spawn of a test page). Actually simpler: we test the JS through inlined node-style mocks. Defer the full JS test to a manual smoke check and 1 unit test that the controller contract is parseable.

### MODIFIED (5 files)
- `routes/service.php` (route registration) — or new routes/resume-parser.php.
- `resources/views/partials/resume_presign_setup.blade.php` — second progress block + parserUrl in `JERO_RESUME_CONFIG`.
- `public/js/resume-upload-progress.js` — parallel parser fan-out + cancel-substitution.
- `public/js/resume-populate-if-empty.js` — subscribe to inline events.
- `resources/views/service_company/job-applications/create.blade.php` — submit-handler waits for both.

(Ultimate count: 8 files: 3 new + 5 modified.)

## Critical existing utilities reused

- `smalot/pdfparser ^2.12` (already in `composer.json:46`) — PDF text extraction.
- `phpoffice/phpword ^1.4` (line 41) — DOCX extraction (DOC legacy returns parse_status hint).
- `App\Logging\ResumeLogContext::event()` — structured log emission.
- `App\Services\ResumeMetrics::bump()` — counter increments, Redis-direct from Phase 6.
- `App\Http\Middleware\RequestContextMiddleware` — already installs X-Request-Id / X-Trace-Id on every HTTP request. The new endpoint reads these without further config.
- `App\Jobs\ParseResumeJob` / `App\Services\ResumeUploadSession` / `App\Services\ResumeCandidateSync` — **left completely untouched**. The new endpoint deliberately does not call any of them.

## Backward Compatibility

- No DB migration. The parser endpoint writes zero rows. The existing schema is enough.
- No state machine change in `ResumeUpload::STATUS_*`.
- The existing `ResumeUploadSession::createOrPresign → finalize
  → ParseResumeJob → ResumeCandidateSync` pipeline keeps all
  Phase-4–8 behavior. Recruiters who opt OUT of presign (fall back
  to legacy `/uploadresume`) still get the parser field on
  `change` event because the parser path runs independently and
  only relies on the file blob — independent of the upload path.
- The existing `attachResumePresign('#resumeFile', …)` legacy forms (`#resumeFile`) keep working. The new `#resume`-targeting module listens to change events on either input.
- The CSS selectors the populate-if-empty helper uses (e.g.
  `[name="full_name"]`, `[name="phone"]`) already exist in the
  Phase-2 create form — no form-layout change required.

## Verification

### Automated tests
```
php artisan test --filter=ResumeParserServiceTest          # NEW
php artisan test --filter=ResumeParserEndpointTest        # NEW
php artisan test --filter=ResumeHealthTest                # counters bumped
php artisan test --filter=ResumeLogContextTest            # regression
php artisan test --filter=ParseResumeJobHandleTest        # regression (vendor slot untouched)
php artisan test --filter="Resume"                        # full Phase 1-8 regression
bash scripts/ai-guards.sh
```

### Manual smoke (Day 0 staging)

```
1. Open service_company/job-applications/create/{jobId}
2. Pick a 2 MB PDF.
   - Expect: BOTH bars appear immediately at <200 ms.
   - Upload bar: percent advances to 100 in ~2 s.
   - Parser bar: advances through 4 stages in <1 s.
3. Mid-upload (e.g. at 60%), click Submit.
   - Expect: parsing done before the click resolves.
4. After both bars, click Submit.
   - Expect: "Submit Candidate" button → backend response ~250 ms.
```

### Multi-pod (regression for Phase 6)

```
- Hit /api/resume-parser/parse from each pod. No state, no DB writes, no race.
- Observe redis counters: parse_started_total increments monotonically across pods.
- Submit-time integration: pick file, click Submit during parse stage 2 — note that
  populate-if-empty filled 6/9 fields; recruiter-typed fields preserved.
```

## Why this implementation is the right shape

- **Smalot handles PDF natively** — no extra dependencies. PHPWord handles DOCX. Both are already approved vendor dependencies in this codebase.
- **Stateless endpoint** — no auth-2FA layer, no quota, no DB row, no FS write beyond Laravel's tmp path (free with every uploaded file).
- **Cancel-by-abort** is a one-line `ac.abort()` and the JS already has a `_abortPriorInflight('replaced')` pattern from Phase 7 to extend.
- **No API surface change to existing endpoints** — the new endpoint owns a separate URL and namespace, can be rolled back by removing the route without affecting any other passenger.
- **The Submit handler is the single integration point.** If we ship only the JS renewals and skip the controller, the form keeps working. If we ship only the controller, the JS's old e.preventDefault flow still functions. Both halves are independently deployable.

## Out of scope (Phase 10 candidates)

- A real vendor (Afternoon/Sovren) replaces the smalot heuristics — the service's `parse()` slot is the seam.
- A `parser_inline_avg_ms`/`p95_ms` Redis-rolling-list gauge in the Phase 6 health probe.
- An "offline" mode in the JS where the parser path can be retried from cached prior file if upload succeeded but parser network call failed.
- The "populate if empty" helper supporting multi-valued form fields (skill arrays). Today the column map is 1:1; skill arrays would need a richer DOM contract.
