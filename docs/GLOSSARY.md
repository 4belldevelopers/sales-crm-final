# Glossary — Domain & System Vocabulary

> So any human or AI speaks the same language as the code. Terms map to real
> models/tables/indices where noted. ⚠️ = baked-in spelling that maps to a real identifier
> (don't "fix" casually — see [NAMING_CONVENTIONS.md](NAMING_CONVENTIONS.md)).

---

## Personas / actors
- **Service Company** — a staffing/recruitment **agency** tenant. Routes: `service_company/`.
- **Product Company** — an **employer** hiring in-house. Routes: `product_company/`.
- **Staffing Company** — vendor/agency acting in submission flows.
- **Super Admin** — the **platform owner**; manages all tenants. Routes: `superadmin/`.
- **Admin** — administrator **within** one tenant company.
- **Tenant / Company** — a customer organization. The isolation key is `company_id`.
  Model: `Company` (applies the `company` global scope).

## Hiring domain
- **Candidate** — a person being considered. Stored as a **`JobApplication`** when
  attached to a job/pipeline (not a standalone "candidate" table). ⚠️ spelled `condidate`
  in `add_job_wise_condidates`, `condidate_type`.
- **Application / JobApplication** — a candidate-in-a-pipeline; the **central entity** of
  the ATS. Carries contact, experience, CTC/ECTC (pay), skills, status, resume, etc.
- **Job / Requisition** — an open role. Model `Job`. "Requisition" variants: **custom**
  (agency-created) vs **client** (from a client). Public board via `Front`.
- **Bench** — candidates an agency has available but not yet placed. Model
  `BenchApplication`; ES `ats_candidates_bench_elastic`. Trading bench is core to the
  marketplace.
- **Pipeline / Tracker** — the stages a candidate moves through. **CV Tracker** /
  **App(lication) Tracker** are the main pipeline views. Stage = `master_status_id` /
  `CandidateStatus` / `ApplicationStatus`; `column_priority` orders within a stage.
- **Placement** — a successful hire/offer. Model `OfferJobApplication`; bench
  placed/assigned (`bench_assign`, `placed_end_date`).
- **Client** — an agency's customer (the company they recruit for). Model `Client`.
- **Onboard(ing)** — post-offer joining process. Models `Onboard`, `OnboardFiles`.
- **Interview Schedule** — a scheduled interview. Model `InterviewSchedule` (+ attendees
  `InterviewScheduleEmployee`); Zoom integration; ES `ats_interview_tracker_elastic`.
- **Skill match** — relevance score of a candidate to a job; computed in
  `ElasticCandidatesIndex` (`skill_match`) using `add_job_wise_condidates` + boosting
  (`boostkeywords`). Business logic — don't alter without sign-off.
- **CTC / ECTC** — Current / Expected Cost-To-Company (salary). Many variants on
  `job_applications` (`ctc`, `ectc`, `_hourly/_monthly/_yearly`, currency, mode).
- **Notice period** — time before a candidate can join (`np_id`, `serving_notice_period_mode`,
  `NoticePeriod`).
- **Bill rate** — contractor billing rate (`bill_rate_amount/currency/mode`).

## Marketplace domain
- **Marketplace / Talent exchange** — cross-tenant trading of talent & requirements.
  See [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md).
- **Listing** — a shareable talent profile or requirement published to the network.
- **Request / Offer** — "I want this candidate" / "I offer this candidate". Processed by
  `ProcessCandidateRequest`; ES `ats_candidates_request`, `ats_jobs_request`,
  `ats_clients_request`.
- **Relationship / Connection** — who may transact with whom (the trust graph).
  Tables: `staffing_company_relation`, `recommendation_relation`; ES
  `ats_client_and_agency_relation_index`.
- **Coins** — the platform's settlement currency for marketplace actions (unlocks,
  featured listings, fees). Tables: `company_coins`, `coin_orders`, `coin_events`,
  `coin_packages`, `coin_transection_logs` ⚠️ (transaction); ES `ats_coin_*`.
- **Unlock / reveal** — paying coins to view a candidate's full PII (progressive disclosure).
- **Reputation / rating** — trust signals on companies (`ClientFeedback`, `Report` → extend).

## Architecture / system
- **CQRS-style split** — **MySQL = write store / source of truth**, **Elasticsearch =
  denormalized read store** for lists/search/dashboards (60+ `ats_*` indices).
- **Index / indexing** — pushing MySQL data into ES (services `Elastic*Index`, jobs
  `Elastic*Index`, observers, artisan `Index*ToElasticsearch`). Keeps reads fast.
- **Drift** — when ES is out of sync with MySQL (failed index job) → wrong data shown.
- **Observer** — `app/Observers/*`; model events that trigger ES re-index / side effects.
- **Service** — `app/Services/*`; business logic (ES indexing, mail, payments).
- **Job** — `app/Jobs/*`; queued background work (Redis/Horizon).
- **Horizon** — dashboard/manager for Redis queues.
- **Octane / FrankenPHP** — long-lived PHP worker runtime (fast, but shared-state hazards).
- **Reverb** — Laravel websocket server for realtime push (use instead of polling).
- **Scout** — Laravel search abstraction (`laravel/scout`) used with Elasticsearch.
- **Persona route files** — `service.php`, `product.php`, `superadmin.php`, `web.php`,
  `auth.php`; grouped by `auth` → persona `prefix/name` → device/package middleware.
- **God controller** — an oversized controller (up to ~813 KB) mixing many concerns; grep,
  never read whole.
- **Vertical slice** — the standard way to build a feature here: Route + FormRequest +
  Action/Service + ES sync + Observer + Reverb event + Blade component + test.

## Spelling map (current ⚠️ → correct)
`condidate→candidate` · `commericial→commercial` · `Caompany/Campany→Company` ·
`transection→transaction` · `Remainder→Reminder`.
**`metronics` is NOT a typo — it refers to the Metronic UI theme** (e.g.
`job_metronics_skills`, `ElasticJobMetronicsSkills` = skills tied to Metronic-theme UI).
Leave it as-is. Metronic itself is correctly spelled.
