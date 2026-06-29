# Dependency Graph & Static Analysis

> Derived from imports, relations, observers, and `DB::table()`/service usage.
> No formal static-analysis tool (PHPStan/Psalm) is configured. **[INFERRED]** —
> run `composer require --dev phpstan/phpstan` for an authoritative graph.

## Module coupling (high level)

```
                       ┌─────────────────────┐
                       │   Elasticsearch      │  (read store, 60+ indices)
                       └─────────▲───────────┘
              index jobs/observers│ reads
   ┌──────────────┐   ┌──────────┴───────────┐   ┌──────────────┐
   │  Candidate   │──>│  ElasticService /     │<──│   Job /      │
   │  Management  │   │  Elastic*Index svcs   │   │  Requisition │
   └──────┬───────┘   └──────────────────────┘   └──────┬───────┘
          │ uses                                         │ uses
          ▼                                              ▼
   ┌──────────────┐   ┌──────────────┐   ┌──────────────────────┐
   │  Interview   │   │  Vendor /    │   │  Mail / Notify /      │
   │  Module      │   │  Marketplace │   │  WhatsApp / Reverb    │
   └──────┬───────┘   └──────┬───────┘   └──────────▲───────────┘
          │ Zoom             │                       │ queued
          ▼                  ▼                       │
   ┌──────────────────────────────────────────────────────────┐
   │  Company (tenant) / User / RBAC  ── global scope company_id │
   └──────────────────────────────────────────────────────────┘
                          │ billing
                          ▼
   ┌──────────────────────────────────────────────────────────┐
   │  Billing / Packages / Coins → Stripe/Paddle/Razorpay/...   │
   └──────────────────────────────────────────────────────────┘
```

## Central hubs (highest fan-in — change with care)
1. **`Company`** — tenant root; referenced by almost every model (global scope). Any
   change ripples across all personas.
2. **`JobApplication`** — read/written by candidate, job, interview, vendor, mail flows;
   re-indexed to ES on most mutations.
3. **`ElasticService`** — every list/search/dashboard depends on it.
4. **`Job`** — linked from candidates, vendor requisitions, interviews, front board.
5. **Mail/Notification services** — invoked from candidate, interview, billing, vendor.

## Service dependency edges (observed)
- `Elastic*Index` services → `ElasticService` → ES (HTTP)
- `Elastic*Index` services → MySQL (`DB::table`, Eloquent), `Cache`
- `Queue*MailService` jobs → `MailService`/`MassMailService`/`NotificationMailService`
  → `SmtpSettings` trait → `smtp_settings`
- `UploadResumeToS3` job → S3 (flysystem) → `Document`
- `ZoomService` → `ZoomJWT` trait → Zoom API
- Observers (`app/Observers/*`) → `Elastic*Index` jobs (sync trigger)

## Hotspots (size × fan-in)
| File | Why hot |
|---|---|
| `HomeController.php` (~813 KB) | Catch-all incl. WhatsApp + debug; high fan-in |
| `ServiceCompanyJobsController.php` (~544 KB) | Jobs + vendor + requisition logic |
| `ServiceCompanyJobApplicationController.php` (~537 KB) | Core candidate flows |
| `SuperAdminCompanyController.php` (~510 KB) | Tenant lifecycle + billing |
| `ElasticCandidatesIndex.php` | Candidate write path → ES; ranking logic |

## Circular / risky coupling
- **MySQL ↔ Elasticsearch** form a logical cycle via observers (write MySQL → index ES)
  and reads (controllers read ES to render, then write back status to MySQL). No code
  cycle, but a **data-consistency cycle** with no transaction. **[RISK]**
- Observers calling indexing jobs that re-load models can re-trigger observers if not
  guarded — verify no save-loops when editing observers. **[INFERRED]**

## Dead / unused (candidates for removal — confirm first)
- `OLD_*`, `old_*`, `OLD_2_*` controllers; `oldBackUp/`.
- Root-level stray `ServiceCompanyJobApplicationController.php` (~526 KB).
- `composer-setup.php`, committed `frankenphp` binary (58 MB).
- `app/Models/error_log`, stray `index.blade.php` under controller folders.
- `nwidart/laravel-modules` dependency (no `Modules/` dir) — likely unused.
- Duplicate interview-reminder commands (only one is scheduled).
- `UploadResumeToTestS3` / `UploadResumeToTestRTwo` — test variants; confirm live one.

## Oversized files (top — see BAD_PATTERNS.md)
6 controllers > 250 KB; ~15 controllers > 100 KB. `web.php` ~158 KB,
`service.php` ~64 KB.

## Recommended tooling (not yet present)
- `phpstan/phpstan` (level 3+) for type/dead-code/null analysis.
- `composer require --dev nunomaduro/phpinsights` for coupling/complexity metrics.
- `laravel/pint` is present (`require-dev`) — use for style.
