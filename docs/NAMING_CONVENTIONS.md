# Naming Conventions & Correction Plan

> The codebase has many baked-in misspellings and inconsistent names. They map to **real
> DB tables, ES indices, routes, and Blade references**, so **renaming is a breaking
> change** — never do a blind find/replace. This doc defines the *target* conventions and
> a **safe, staged** correction strategy.

---

## ⚠️ Golden rule
A name like `condidate`, `commericial`, `Caompany`, `coin_transection_logs`,
`add_job_wise_condidates` is wrong English **but correct as an identifier today**.
Until a rename is executed end-to-end (DB + ES + code + views + routes + cache), the
typo *is* the contract. Preserve it when editing; correct it only via the migration
process in §4.

---

## 1. Target conventions (for all NEW code)

| Thing | Convention | Example |
|---|---|---|
| DB tables | `snake_case`, plural | `job_applications`, `interview_schedules` |
| DB columns | `snake_case` | `company_id`, `master_status_id` |
| Models | `StudlyCase`, singular | `JobApplication`, `InterviewSchedule` |
| Controllers | `StudlyCase` + `Controller` | `JobApplicationController` |
| Services | `StudlyCase` + `Service` | `CandidateIndexService` |
| Jobs | `StudlyCase` verb-noun | `IndexCandidate`, `UploadResume` |
| Methods/vars | `camelCase` | `getCandidate()`, `$jobId` |
| Routes (name) | `dot.case`, persona-prefixed | `service_company.jobs.index` |
| ES indices | `snake_case`, domain-prefixed | `ats_candidates`, `ats_jobs` |
| Blade views | `kebab-case` | `candidate-list.blade.php` |
| Config keys | `snake_case` | `config('elasticsearch.hosts')` |
| Booleans | `is_/has_/can_` | `is_viewed`, `has_resume` |

---

## 2. Known misspellings → correct spelling (canonical glossary)

| Current (keep until migrated) | Correct target |
|---|---|
| `condidate`, `condidates` (e.g. `add_job_wise_condidates`, `condidate_type`) | `candidate` |
| `commericial`, `commericials`, `commericial_countries` | `commercial` |
| `Caompany` (`ElasticCaompanyIndex`, `ElasticCampany`) | `Company` |
| `Campany` (`ElasticCampany`, `ElasticCampanyCoins`) | `Company` |
| `transection` (`coin_transection_logs`) | `transaction` |
| `Remainder` (`InterviewRemainderMail`, `QueueInterviewRemainderMailService`) | `Reminder` |
| `serving_notice_period_mode` (OK) / mixed `np_id` | standardize `notice_period_*` |
| `client_city_jobs.php`, `jobCountry.php` (lowercase models) | `ClientCityJob`, `JobCountry` |
| `OLD_`, `old_`, `OLD_2_` prefixes | delete (dead code) |

> **`metronics` refers to the Metronic UI theme** (confirmed) — it is NOT a typo for
> "metrics". Tokens like `job_metronics_skills` / `ElasticJobMetronicsSkills` are
> Metronic-theme related; **do not rename them**. `Metronic` itself is correctly spelled.

---

## 3. Inconsistencies to standardize (new code only, until migration)
- **Persona controller duplication**: `ServiceCompany*` vs `ProductCompany*` vs
  `StaffingCompany*` repeat names — keep persona prefix, but extract shared logic to
  one Service (don't fork logic per persona).
- **Mixed Eloquent + `DB::table()`** with hand-listed columns — prefer Eloquent +
  query scopes for readability and tenant-safety.
- **Model file casing**: lowercase model files violate PSR-4/Eloquent expectations.
- **Reminder commands**: three near-duplicates (see BUG-08) — collapse to one.

---

## 4. Safe rename procedure (when you DO correct a name)

A rename is only "done" when **all six layers** are updated atomically and verified:

```
1. CODE      rename class/method/var (+ all references)         grep -rn "OldName"
2. DB        migration: RENAME TABLE / CHANGE COLUMN            (confirm live schema first!)
3. ES        create new index, reindex, alias swap, drop old    Index*ToElasticsearch
4. CONFIG    update .env ELASTIC_* / config keys                
5. VIEWS     update Blade references / route() names            grep -rn in resources/views
6. CACHE     config:clear route:clear view:clear; warm caches
```
Rules:
- **One name per PR**, behind a feature window, with rollback (keep ES alias to flip back).
- For ES, **use aliases** (`ats_candidates` → alias → real index) so you can reindex and
  swap with zero downtime and never hardcode physical index names.
- For DB, prefer **add-new-column + backfill + dual-write + cutover + drop-old** over an
  in-place rename when the column is hot (avoids long locks at 100M rows).
- Add a **redirect/compat shim** for renamed route names during transition.
- Verify with [VERIFY.md](VERIFY.md); add a test that the old behavior still passes.

---

## 5. Priority of corrections (low-risk → high-value)
1. **Now (zero migration):** name all *new* classes/columns correctly; stop spreading typos.
2. **Cheap:** delete dead `OLD_*`/`old_*` files; fix lowercase model filenames (code-only,
   no DB change) via classmap + careful reference update.
3. **Planned migration:** `condidate→candidate` and `commericial→commercial` are the
   highest-visibility; do them table-by-table with the §4 procedure, aliased ES, off-peak.
4. **Defer/confirm:** `metronics` (unclear intent), anything touching payment tables.

> Do not let "perfect naming" block delivery. Correct opportunistically through the safe
> process; never with a repo-wide blind replace.
