# 10 — Current state (as of 2026-07-26)

> *Intent: ground v2 in the present pipeline so we don't drift into aspirational*
> *contracts (the trap the pre-rename memory file fell into). All claims are*
> *inventory-claims; line refs are unstable.*

## What runs in production today

### Public candidate apply (career page)

| Object | Path | LOC | Status |
|---|---|---|---|
| Apply blade | `resources/views/career-apply.blade.php` | ~3855 | XHR progress layer added, parser client-side (REMOVED 2026-07-27: this IS the only parser; no server-side parser) |
| Apply controller | `app/Http/Controllers/HomeController.php` | ~16K | god controller, two relevant methods: `careerApply` GET + `careerStore` POST |
| OpenAI side-channel | `app/Services/ChatGptService.php` | 37 | raw cURL, no retry, no idempotency |
| Upload temporary | `app/Http/Controllers/HomeController.php` — `uploadresume` route | — | 6 MB cap, field name=`file`, single endpoint |
| Upload → S3 job | `app/Jobs/UploadResumeToS3.php` | 85 | no tries/backoff, no idempotency |
| Index → ES job | `app/Jobs/JobApplicationsNewUpload.php` | 63 | rethrow on failure, but no `$tries` |
| ES service | `app/Services/ElasticService.php` | 69 | indexes ElasticCandidate — index name unverified |

### Recruiter add-candidate (service_company)

| Object | Path | LOC | Status |
|---|---|---|---|
| Apply blade | `resources/views/service_company/job-applications/create.blade.php` | ~3855 | legacy multipart fallback, CSS bears the Phase 9.2 progress |
| Controller | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | — | `store()` calls `ResumeUploadException` path |
| Resume service | `app/Services/ResumeUploadService.php` | — | sha1→r2-keymap; verify-on-submit shape |
| Resume exception | `app/Exceptions/ResumeUploadException.php` | 50+ | renderer registered in `bootstrap/app.php` |

## Where 10K concurrent users break today

These are the points where **current** behavior either fails or imposes
a load that does not survive doubling, ten-folding, etc.

### C1 — PHP receives every byte

The `<input type="file">` field still POSTs through PHP for the public
apply path (career-apply). At 10K concurrent × 6 MB avg = 60 GB/s of
bytes through FPM/Octane. Cloudflare → nginx → FrankenPHP → S3 is a
roundtrip of server bandwidth.

**v2 contract: PHP receives zero bytes for resume uploads.** Only presigned
PUT metadata.

### C2 — Single ES index write per candidate

`JobApplicationsNewUpload::handle()` makes one direct `ElasticService::index()`
call. At 10K writes/min, the ES index is at >150 writes/sec — under
the 1K/sec threshold but with no retry, no batch, no queue shaping.

**v2 contract: ES write dispatch through a batched queue** with a single
`bulkelasticindex` worker that fans out from Redis sorted set.

### C3 — REMOVED 2026-07-27 — No server-side parser to admission-control

> v2 has no `ChatGptService::parseResume()` call site. The browser-side
> `pdf.js` parser runs locally in 50–200 ms and never crosses the
> network. There is no provider-side 429 storm because there is no
> provider. The C3 gap that existed in v1 (raw cURL → OpenAI with no
> admission control) is solved by *removing the call site* rather
> than by adding a Redis semaphore around it.
>
> The v1-era `ChatGptService.php` file is left untouched on disk
> (no destructive delete) for the v1 path that still uses it.

### C4 — God controller state container

`HomeController` is ~16,633 LOC. The 6-layer `/login` 500 chain we just
debugged proved the cluster has cross-cutting concerns bootstrap on every
boot. Boot-time errors at 10K = every request 500s.

**v2 contract: resume + apply extracted into a vertical slice** (Route + FormRequest + Action + ES sync + Observer + Reverb event + Blade component + test) per CLAUDE.md rule 11.

### C5 — No session idempotency at the file level

`documents()->updateOrCreate(['name'=>'Resume'], ['hashname'=>$url])`
de-dupes the Document row, but a re-pick creates a *second* R2 temp
object and a *second* S3 object. At 10K × maybe 3 picks per attempt = 30K
orphan temp files in R2.

**v2 contract: SHA256(file content + company_id + safe_filename) as idempotency key.** Same key → reuse session UUID.

### C6 — Reverb channels multi-tenant

Tenant boundary is documented but not enforced: a JS subscriber listens
on `private-resume.{companyId}` but the auth is the same `Auth` middleware
that lets `Auth::user()->company_id` leak across tabs when restored from
localStorage.

**v2 contract: Reverb channel auth reads tenant from session, not from URL/headers/localStorage.** Same-tenant private channel is hard-enforced.

### C7 — `MigrateCheckCommand` on Controller construct (we hit this)

Every controller's constructor calls `checkMigrateStatus()` → `php artisan
migrate:check` (via Froiden\Envato). On Octane first-request bootstrap,
every FPM worker runs this. At 10K concurrent, Cache misses on first
request after scale-out = a stampede; PHP version warnings + deprecations
become visible.

**v2 contract: the Froiden commander is NOT called on hot paths.** Move it to a scheduled check command and a deployment check.

## Inventory claims — re-derive before relying

These were NOT typed out from direct grep this session:

| Item | Confidence | Re-derive command |
|---|---|---|
| `career-apply.blade.php` has XHR progress | High | grep `XMLHttpRequest` in file |
| `HomeController` LOC | Medium | `wc -l app/Http/Controllers/HomeController.php` |
| ES index name for candidates | Low | grep `ElasticService::index` |
| `ChatGptService` raw cURL | Medium | grep `curl_exec` |
| Public route `POST /uploadresume` "6 MB" cap | High | grep `'max:6048'` |

When a downstream doc cites one of these, it marks the confidence level so
re-derivation is a known step, not a new discovery task.
