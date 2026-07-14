# Bug Report & Fix Plan

> Found by static scan of the working tree on 2026-06-27. Severities: 🟥 critical
> (breaks prod / data loss / security) · 🟧 high · 🟨 medium. Line numbers are
> point-in-time — **re-grep before fixing**. No code was changed.

---

## 🟥 BUG-01 — 63 active `dd()` / `die` / `exit` calls left in controllers
**Evidence:** `grep -rnE "^\s*(dd|die|exit)\(" app/` → 63 hits, e.g.
- `app/Http/Controllers/Front/SocialAuthController.php:18` → `dd($user)` — **Google/social
  login literally dumps and dies; the feature is broken in production.**
- `app/Http/Controllers/GoogleSheetsService.php:22` → `dd($values)`
- `app/Http/Controllers/HomeController.php:641, 3002, 3005, 3023` → `dd(...)`

**Impact:** Any user hitting these paths gets a white debug dump (or a 500), leaking
internal data and killing the request. **Why:** debugging code shipped.
**Fix:** remove every active `dd/dump/var_dump/die/exit`; replace genuine error paths
with `Log::error()` + a user-facing response. Add a CI guard:
`grep -rnE "^\s*(dd|var_dump|dump)\(" app/ && exit 1`. Also enforce via Pint/PHPStan.

---

## 🟥 BUG-02 — `env()` called outside `config/` (breaks under `config:cache` + Octane)
**Evidence:** 25 `env()` calls across 10 files: `app/Services/ElasticService.php`,
`app/Services/ZoomService.php`, `app/Services/ChatGptService.php`,
`app/Traits/StripeSettings.php`, `app/Traits/PaymentSettings.php`, `app/Traits/ZoomJWT.php`,
`app/Helper/GeneralMethod.php`, `HomeController.php`, `PhonePePaymentController.php`,
`ServiceCompany/ServiceCompanyJobApplicationController.php`.

**Impact (CRITICAL for this app):** the moment you run `php artisan config:cache`
(required for Octane performance — see [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md)),
**`env()` returns `null`** outside config files. Result: Elasticsearch host = null
(all search/reads fail), Zoom keys = null (interviews fail), payment keys = null
(checkout fails). This is a latent production outage.
**Fix:** move each `env('X')` into a `config/*.php` entry and read via `config('x')`.
ES host/keys → `config/elasticsearch.php`; Zoom/PhonePe/Stripe → `config/services.php`.
Then `config:cache` is safe. Add PHPStan rule / grep guard banning `env(` outside `config/`.

---

## 🟥 BUG-03 — Broken / inconsistent multi-tenant isolation (data-leak risk)
**Evidence:** `app/Models/JobApplication.php` — the `company` global scope is **commented
out** and `SoftDeletes` is imported but the trait is not applied. `Company` model *does*
scope. Many services use raw `DB::table('job_applications')` which **bypasses scopes**.
**Impact:** candidate data from one tenant can surface for another (cross-tenant leak) —
a severe data-protection bug at marketplace scale. **Fix:** (1) decide the canonical
tenant-isolation mechanism; (2) re-enable/standardize the scope or add an explicit
`where('company_id', …)` on every raw query; (3) wrap raw queries in a helper that
*requires* `company_id`; (4) add a test that asserts tenant A cannot read tenant B.
⚠️ Behavior change — confirm with product before enabling a scope that was off.

---

## 🟥 BUG-04 — 1,227 unescaped Blade outputs (`{!! !!}`) — stored XSS surface
**Evidence:** `grep -rn "{!!" resources/views` → 1,227 hits.
**Impact:** if any user-supplied value (candidate name, email, cover letter, notes,
client name, resume-parsed text) is rendered with `{!! $x !!}`, it executes as HTML/JS =
stored XSS, hijacking recruiter sessions. Some are legitimate (CMS/settings HTML), but
1,227 is far too many to be all intentional. **Fix:** audit each; switch user-data to
`{{ }}` (auto-escaped); for trusted rich text, sanitize server-side (HTMLPurifier)
before storing/printing. Prioritize candidate/client/notes views.

---

## 🟧 BUG-05 — Non-sargable date queries defeat indexes
**Evidence:** `app/Models/Company.php::isFeatured()` uses `DB::raw('DATE(featured_start_date)')`
in `WHERE`. **Impact:** full scan instead of index seek on hot tables; latency grows
with data. **Fix:** use range predicates: `featured_start_date <= ? AND featured_end_date >= ?`
(store/compare as datetime), keep indexes usable.

---

## 🟧 BUG-06 — Debug/test routes shipped in `routes/web.php`
**Evidence:** `/mail-test` (sends a real email to a hardcoded address), `/whatsapp/connect`,
`/whatsapp/*`. **Impact:** anyone can trigger mail sends / inspect integration state;
abuse + cost + info leak. **Fix:** delete or wrap in `if (app()->isLocal())` /
`Route::middleware('can:debug')`; never expose in prod.

---

## 🟧 BUG-07 — Dual MySQL↔Elasticsearch write with no failure handling = silent drift
**Evidence:** Observers dispatch `Elastic*Index` jobs after MySQL writes; no transaction,
no reconcile. **Impact:** a failed index job leaves ES stale → recruiters see wrong/old
candidate data; at scale this compounds. **Fix:** retries + backoff on index jobs,
failed-job alerting (Horizon), and a nightly **reconciliation command** that diffs
MySQL↔ES and repairs. Treat ES as eventually-consistent and surface "last synced" where it matters.

---

## 🟧 BUG-08 — Duplicate/ambiguous interview reminder commands
**Evidence:** `InterviewReminderCommand`, `SendInterviewReminder`, `SendInterviewReminders`
coexist. **Impact:** risk of double-sending reminders (annoying + spam-flagging) or none,
depending on which is scheduled. **Fix:** confirm the one wired in `routes/console.php`,
delete the others, add a test for the schedule.

---

## 🟨 BUG-09 — Test/experimental jobs in the live path
`UploadResumeToTestS3`, `UploadResumeToTestRTwo` alongside `UploadResumeToS3`.
**Fix:** confirm the canonical uploader; remove test variants or move behind config.

## 🟨 BUG-10 — Empty global exception handler
`bootstrap/app.php` `withExceptions()` is empty → no centralized logging/shaping; errors
may leak stack traces if `APP_DEBUG=true` in prod. **Fix:** report to Sentry/log, render
safe responses, ensure `APP_DEBUG=false` in prod.

## 🟨 BUG-11 — Stray non-class files inside `app/`
`app/Models/error_log`, `app/Models/constants.php`, `index.blade.php` under controller
dirs, root-level 526 KB stray controller. **Fix:** remove (confirm first); they confuse
autoload/agents and inflate scans.

---

## Suggested fix order (risk-first)
1. BUG-02 (env → config) — silent outage waiting to happen.
2. BUG-01 (active dd) — broken features in prod now.
3. BUG-03 (tenant isolation) — data-protection.
4. BUG-04 (XSS) — security, audit incrementally.
5. BUG-06 (debug routes), BUG-10 (exceptions/debug flag) — quick wins.
6. BUG-05, 07, 08, 09, 11 — hardening & cleanup.

Each fix: reproduce → fix behind a test → verify per [VERIFY.md](VERIFY.md) → ship.
