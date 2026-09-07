# Architecture — hire.jero.works (JERO ATS / Hiring SaaS)

> Generated for AI coding agents. Read this first. It is a map, not the territory —
> verify file paths before editing. Items marked **[INFERRED]** were deduced from
> code patterns, not confirmed by authoritative config. Items marked **[UNCERTAIN]**
> need human confirmation.

---

## 1. Product Overview

JERO is a **multi-tenant B2B hiring / Applicant Tracking System (ATS)** delivered as
SaaS. A single Laravel codebase serves several distinct **company personas**, each with
its own controller namespace, route file, dashboard, and feature set:

| Persona | Route prefix | Purpose |
|---|---|---|
| **Service Company** (staffing/recruitment agency) | `service_company/` (`routes/service.php`) | Core ATS: jobs/requisitions, candidate sourcing, CV tracker, bench candidates, interview scheduling, client management, placements |
| **Product Company** (employer/in-house hiring) | `product_company/` (`routes/product.php`) | Post jobs, manage applicants, interviews, bench |
| **Staffing Company** | (in `routes/web.php` + `StaffingCompany/`) | Vendor/agency submission workflows |
| **Super Admin** | `routes/superadmin.php` | Platform owner: tenant (company) management, packages, billing, custom packages, global settings |
| **Admin** | `Admin/` controllers | Tenant-level administration |
| **SaaS Front / Front** | `SaasFront/`, `Front/` | Public marketing site, careers/job board, public job apply |

Multi-tenancy is enforced by the canonical `TenantScope` and `BelongsToTenant` trait
(`app/Scopes/TenantScope.php`, `app/Traits/BelongsToTenant.php`), automatically scoping
Eloquent model queries by `company_id`. Raw database queries across the 93 tenant tables use the
`tenant_table('<table_name>')` helper function (`app/Helper/TenantDB.php`).

---

## 2. Tech Stack

- **Framework:** Laravel 11 (`laravel/framework ^11.31`), PHP 8.2+
- **App server:** Laravel **Octane** + **FrankenPHP** (`OCTANE_SERVER=frankenphp`),
  long-lived workers — beware static/leaked state between requests.
- **Database:** MySQL (`DB_DATABASE=dbmaster`, **remote** host). See note in §9.
- **Cache + Sessions + Queue backend:** **Redis** (`CACHE_STORE=redis`,
  `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`).
- **Queues:** **Laravel Horizon** over Redis. Custom jobs table name
  `custom_queue_jobs`.
- **Search / read store:** **Elasticsearch 7.17** via `laravel/scout` + direct HTTP
  (`Illuminate\Support\Facades\Http`). 60+ indices (see §6).
- **Realtime:** **Laravel Reverb** (websockets, `BROADCAST_CONNECTION=reverb`).
- **Auth:** Session auth (Breeze-derived) + **JWT** (`tymon/jwt-auth`, `firebase/php-jwt`)
  + **Socialite** (Google login). RBAC via **spatie/laravel-permission**.
- **Payments / Billing:** **Stripe** (`laravel/cashier`), **Paddle**
  (`laravel/cashier-paddle`), **Razorpay**, **PhonePe**, PayPal IPN.
- **Frontend:** Blade + Tailwind + Alpine.js + Vite; Metronic admin theme;
  yajra **DataTables** (server-side AJAX tables drive most list screens).
- **Files / Storage:** AWS S3 (flysystem) — resumes/documents stored on S3
  (`file.jero.works`).
- **Doc/Office:** dompdf, phpword, smalot/pdfparser (resume text extraction),
  maatwebsite/excel (imports/exports).
- **Other integrations:** Zoom (interviews), Google Sheets, WhatsApp (custom HTTP
  service), OpenAI/ChatGPT (`OPENAI_API_KEY`), LinkedIn.
- **Modules:** `nwidart/laravel-modules` is installed but **no `Modules/` directory
  exists** — modularity is by convention (controller namespaces), not by package. **[INFERRED]**

---

## 3. Major Modules

See [MODULE_MAP.md](MODULE_MAP.md) for file-level detail. High level:

1. **Candidate / Application Management** — `JobApplication` is the central entity
   (a candidate-in-a-pipeline). CV Tracker, Bench Candidates, App Tracker.
2. **Job / Requisition Management** — `Job`, requisitions (custom / client),
   job questions, skills, categories.
3. **Vendor / Agency Marketplace** — client↔agency relations, requirement sending,
   candidate requests, recommendation relations.
4. **Interview Scheduling** — `InterviewSchedule`, Zoom integration, reminders
   (queued + scheduled commands).
5. **Search & Indexing** — Elasticsearch services + jobs + observers + artisan
   `Index*ToElasticsearch` commands.
6. **Billing / Packages / Coins** — packages, custom packages, subscriptions,
   coins economy (ES-backed), multiple payment gateways.
7. **Notifications / Mail / Messaging** — queued mail services, mass mail,
   WhatsApp, in-app notifications, Reverb broadcasts.
8. **Super Admin / Platform** — tenant lifecycle, global settings, CMS front.

---

## 4. External Integrations

| Integration | Used for | Code |
|---|---|---|
| AWS S3 | Resume/document storage | `app/Jobs/UploadResumeToS3*.php`, `config/filesystems.php` |
| Elasticsearch | Search + denormalized read store | `app/Services/Elastic*.php`, `config/elasticsearch.php` |
| Stripe / Cashier | Subscriptions, invoices | `app/Traits/StripeSettings.php`, `Models/Company.php` (Billable) |
| Paddle | Subscriptions (alt) | cashier-paddle |
| Razorpay | Payments (India) | `RazorpayWebhookController`, `Models/Razorpay*` |
| PhonePe | Payments (India) | `app/Services/PhonePeService.php`, `PhonePePaymentController` |
| PayPal | Payments (IPN) | `PaypalIPNController` |
| Zoom | Interview video links | `app/Services/ZoomService.php`, `app/Traits/ZoomJWT.php` |
| Google | OAuth login + Sheets export | `GoogleController`, `app/Services/GoogleSheet*.php` |
| WhatsApp | Candidate messaging (external Node service) | `app/Jobs/WhatsAppTextSend.php`, `HomeController` `/whatsapp/*` |
| OpenAI | AI assistance | `app/Services/ChatGptService.php` |
| Reverb | Realtime chat/notifications | `routes/channels.php`, `config/reverb.php` |

---

## 5. Queue Architecture

- **Driver:** Redis via Horizon. Config: `config/horizon.php`, `config/queue.php`.
- **Supervisors:** production runs effectively a **single `default` queue**
  (`supervisor-1`, `queue => ['default']`, `maxProcesses: 10` prod / `3` local).
  **[BOTTLENECK]** All job types share one queue — bulk resume uploads, mass mail,
  and ES re-indexing compete for the same workers. See [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).
- **Job catalog** (`app/Jobs/`): ES indexing (`Elastic*Index`), resume → S3 upload
  (`UploadResumeToS3`, `UploadResumeToTestS3`, `UploadResumeToTestRTwo`), bulk
  application import (`JobApplicationsNewUpload`), candidate request processing
  (`ProcessCandidateRequest`), queued mail wrappers (`Queue*MailService`,
  `SendEmailJob`), WhatsApp send.
- **Scheduled work** (`app/Console/Commands/` + `routes/console.php`): interview
  reminders, expired/auto-close jobs, licence expiry, featured-listing expiry,
  bulk ES re-index commands.

---

## 6. Search Architecture (the defining trait of this codebase)

Elasticsearch is used as a **denormalized read store**, not merely full-text search.
There are **60+ named indices** (see `.env` `ELASTIC_*` keys), e.g.
`ats_candidates_elastic`, `ats_jobs_elastic`, `ats_companies_elastic`,
`ats_candidates_bench_elastic`, `ats_client_and_agency_relation_index`, plus dozens
for coins, billing, notifications, skills, timelines, etc.

**Write path:** MySQL is the source of truth. Data is pushed to ES via:
- **Services** `app/Services/Elastic*Index.php` (build documents from MySQL rows),
- **Jobs** `app/Jobs/Elastic*Index.php` (async indexing),
- **Observers** `app/Observers/*` (model events trigger re-index),
- **Artisan** `Index*ToElasticsearch` commands (full backfill / rebuild).

**Read path:** List/search/dashboard screens query ES directly via `ElasticService`
+ `Http`, then render (often through DataTables AJAX endpoints).

**[RISK]** Dual-write (MySQL + ES) with no transactional guarantee → eventual
consistency / drift. Ranking & matching logic (e.g. `skill_match`) lives inside the
large `Elastic*Index` services and controllers. See [DATA_FLOW.md](DATA_FLOW.md).

---

## 7. Caching Strategy

- Redis is the cache store. `Cache::` used inside ES index services and elsewhere
  for memoizing lookups. **[INFERRED]** No central cache-key registry exists; keys are
  ad-hoc. Document new keys in [MODULE_MAP.md](MODULE_MAP.md) when adding them.
- Octane keeps the framework booted between requests — do **not** cache
  request-specific state in static properties or singletons.

---

## 8. Deployment Architecture

- **Runtime:** FrankenPHP/Octane (binary `frankenphp` committed at repo root — large),
  Caddy config under `caddy/`. Hostinger deploy artifacts under `hostinger/`. **[INFERRED]**
- **Background:** Horizon worker process(es) + scheduler (`php artisan schedule:run`).
- **Services required:** MySQL, Redis, Elasticsearch 7.17, Reverb websocket server.
- See [VERIFY.md](VERIFY.md) for build/verify commands and [adr/](adr/) for rationale.

---

## 9. Performance Constraints & Known Bottlenecks

(Full detail in [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md) and [BAD_PATTERNS.md](BAD_PATTERNS.md).)

1. **God controllers** — e.g. `HomeController.php` (~813 KB),
   `ServiceCompany/ServiceCompanyJobsController.php` (~544 KB),
   `ServiceCompanyJobApplicationController.php` (~537 KB),
   `SuperAdmin/SuperAdminCompanyController.php` (~510 KB). These dominate token cost
   and are hard for agents to load. Never read them whole — grep for the method.
2. **~2,240 routes** (`web.php` alone ≈1,180) — routing is sprawling and AJAX-heavy.
3. **Single Horizon queue** — no isolation of heavy jobs.
4. **Dual MySQL/ES writes** — consistency & latency risk.
5. **Schema not in migrations** — only 3 migrations exist; the live schema is in the
   remote `dbmaster` DB. Schema changes are made out-of-band. **[HIGH RISK]**
6. **Mixed Eloquent + raw `DB::table`** with hand-listed columns; N+1 and unindexed
   query risk throughout services.
7. **Dead code** — multiple `OLD_`, `old_`, `OLD_2_` controllers, a `oldBackUp/`
   folder, stray `.blade.php` and `error_log` files inside `app/`.

---

## 10. Coding Conventions (observed — match these, don't "fix" them)

- **Controllers are fat**; business logic lives in controllers and `Services`, not in
  thin controller + repository layers. There is **no repository layer**.
- **Service classes** (`app/Services`) wrap Elasticsearch and mail/notification logic.
- **Multi-tenancy:** rely on the `company` global scope; when writing raw queries,
  remember to filter `company_id` yourself (the scope only applies to Eloquent).
- **Naming is inconsistent** and contains typos baked into the schema/code
  (`condidate`, `ElasticCaompanyIndex`, `ElasticCommericials`, `serving_notice_period`).
  **Preserve existing spellings** — they map to real tables/indices. Do not rename.
- **Observers** keep ES in sync — when adding a model write path, check whether an
  observer should fire (or you'll create drift).
- **DataTables**: list endpoints come in pairs — a page route and an `*_ajax` data
  route. Follow the existing pair pattern.
- **Validation:** `app/Http/Requests/*` (55 form requests) — reuse, don't inline.
- **Routes are grouped** by `auth` → persona `prefix()->name()` → `EnsureOneDeviceLogin`
  → `custom-package`/`active-package` middleware. Add new routes inside the matching group.
- Heavy operations (resume parse/upload, mail, ES bulk, imports) **must be queued**.

See [AI_RULES.md](AI_RULES.md) before making any change.
