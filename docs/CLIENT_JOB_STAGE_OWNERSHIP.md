# Client-Job Stage Ownership, Candidate Visibility & AppTracker Search/Sort/Filter Convention

> Captured from a live debugging/discussion session (2026-07-16) working on the
> AppTracker pages for the **service-company ↔ client-company** flow. §1–§6 are
> business logic that was **not written down anywhere before** — reconstructed from the
> product owner's explanation plus direct verification against `master_status` table
> data and the controller code that enforces (or, at the time, failed to enforce) it.
> §7 is a standing implementation convention for search/sort/filter UI across **all**
> AppTracker candidate-list pages, not just the client-job ones. Treat this as ground
> truth for the *rule*; always re-grep the referenced files before changing behavior,
> since line numbers and exact fix status drift over time.
>
> **§8 onward added 2026-07-19** — a follow-up session that redesigned the
> "Change Candidate Stage" drawer across all 5 candidate-list pages
> (AppTracker/CV Tracker/ClientApp/Offer Tracker/Joining Tracker) and fixed several
> confirmed bugs in the stage-transition and sub-stage machinery. §8 confirms §2's
> two-Rejected-ids table is accurate (it directly explains a bug found this session)
> and should be read as an extension of this doc, not a separate one.

## 1. The two pipelines, in one sentence each

- **Service company's own jobs** ("Total Candidates In Job" / `index.blade.php` /
  `ServiceCompanyAppTrackerController::allDataAjax()`) — the agency runs the whole
  hiring pipeline itself, end to end. No handoff, no cross-company ownership split.
- **Client jobs** (`clientapp` / `dsimilar_candidates` / `dinternal_bench` on the
  service-company side, the mirror pages under `ProductCompanyAppTrackerController` on
  the client side) — a **client company** owns the job; a **service company (agency)**
  sources candidates for it. Ownership of "who can move this candidate's stage" and
  "who can even see this candidate" **splits at a specific handoff point**. This doc is
  about that second pipeline.

## 2. The stage model (`master_status` table)

Every candidate on a client job carries a `master_status_id` (stored on
`add_job_wise_condidates.master_status_id`, mirrored onto the ES document as
`job_master_status_id`). Confirmed via direct query against `master_status`:

| id | name       | priority | `direct_client` | `custom_client` | Who owns it |
|----|------------|----------|:---:|:---:|---|
| 8  | InReview     | 0 | 1 | 1 | **Agency** (pre-handoff) |
| 1  | Sourcing     | 1 | 1 | 1 | **Handoff point** — agency hands off, client takes over |
| 2  | Screening    | 2 | 0 | 1 | Client only |
| 3  | Evaluation   | 3 | 0 | 1 | Client only |
| 4  | Negotiation  | 4 | 0 | 1 | Client only |
| 5  | Offered      | 5 | 0 | 1 | Client only |
| 6  | Joined       | 6 | 0 | 1 | Client only |
| 7  | Rejected     | 7 | 0 | 1 | **Client's own reject** |
| 9  | Rejected     | 7 | 1 | 0 | **Agency's own reject** (pre-sourcing only) |

There are **two different "Rejected" status rows** (7 and 9) — this is intentional, not
a data-entry duplicate. Which set of statuses a user's stage-change dropdown offers is
decided by `ServiceCompanyJobApplicationController::client_stage_body_content()`:
```php
if ($thirdparty_company_id == $current_company_id) {
    // agency viewing a candidate IT submitted → direct_client=1 set: InReview, Sourcing, Reject(9)
} else {
    // client viewing → custom_client=1 set: InReview...Joined, Reject(7)
}
```

## 3. The ownership rule (who can move what, from where)

**Agency-permitted transitions** (only valid while the candidate's *current* stage is
8 or 9 — i.e. before the client has taken over):
- `8 → 1` (InReview → Sourcing: hand the candidate to the client)
- `8 → 9` (InReview → Rejected: agency rejects before ever sourcing)
- `9 → 8` (Rejected → InReview: agency un-rejects, back to review)
- `9 → 1` is **explicitly not allowed** — must go back through InReview(8) first.
- Once the candidate's current stage is 1–7 (sourced or beyond), **the agency has no
  control at all** — not even to reject via status 9. Any attempt should be blocked
  with "This candidate has already been sourced — only the client company can change
  its stage now."

**Client-permitted transitions** (once the candidate is at stage 1 or beyond):
- Normal forward/backward movement among 1→2→3→4→5→6 (no skipping ahead more than one
  stage; backward moves are one stage at a time).
- **Reject(7) is reachable directly from *any* of stages 1–6** — it is not gated to
  "only from the immediately preceding stage." (A flawed linear-order model used to
  make this only work from Joined; see §5.)
- The client **can never move a candidate back to InReview(8)** — that stage belongs to
  the agency; reverting to it is not the client's call to make, even though it's
  technically present in the client's own `custom_client=1` dropdown list.

All of this is enforced (where it's enforced) in
`ServiceCompanyJobApplicationController::client_stage_submit()` — search for
`$isAgencySubmission` / `$isSpecialAgencyEdge` in that method.

## 4. The visibility rule (who can even *see* the candidate)

Stages 8 (InReview) and 9 (agency-rejected-pre-sourcing) are **private to the
submitting agency**. The client company must not see these candidates at all — not in
the candidate list, not in counts — until the agency sources the candidate (moves it to
stage 1+). Reasoning straight from the product owner: these candidates were "uploaded
by the agency and their recruiters," and the client has no visibility into that
private review process.

This is enforced by excluding `master_status_id IN (8, 9)` from every client-facing
query:
- `ProductCompanyAppTrackerController::index()` / `loadcounter()` — the client's Kanban
  counters. **Already correctly excludes 8/9** in every query branch.
- `ProductCompanyAppTrackerController::data()` — the client's actual candidate table
  (the "All" tab and every stage tab). **This one had zero exclusion** — a real leak,
  since the counts were private but the underlying list wasn't. Fixed by wrapping the
  existing filter logic in one outer `where()` group and AND-ing a top-level
  `whereNotIn('add_job_wise_condidates.master_status_id', [8, 9])` onto it.

**Open question, not yet resolved:** is there a symmetric guard stopping the *client*
from acting on a candidate that's still at stage 8/9 (i.e., before the agency has
sourced it at all)? No such guard was found or added during this session — only the
read-visibility leak in `data()` was confirmed and fixed. If a future symptom looks
like "the client can somehow act on a candidate before it's been sourced," start there.

## 5. Bugs found in this exact area (context if similar symptoms come back)

These were confirmed by direct code reading (not assumption) during the session that
produced this doc. Some were fixed then reverted/reapplied at various points as part of
an unrelated production incident — **don't trust this list for current fix status,
re-grep the referenced code first** — but the *diagnosis* of each is solid and worth
not re-deriving from scratch:

- **`client_stage_submit()` skipped all stage-order validation whenever the target was
  status 9.** Combined with the dropdown always offering Reject(9) regardless of
  current stage, this meant the agency could reject a candidate from *any* stage, not
  just InReview — directly violating §3 above.
- **A live, uncommented `dd($currentOrder != $newOrder+1);`** sat inside the
  "going backward" branch of the same method — crashed (raw debug dump instead of JSON)
  on every backward stage move.
- **Reject(7) was only reachable from Joined**, not from any active client stage — a
  side effect of assigning Rejected the highest `order` value in a linear
  jump-ahead/backward-move model that was never designed to handle "reject from
  anywhere" as a special case.
- **`ProductCompanyAppTrackerController::data()`'s stage-8/9 visibility leak** — see §4.

Two more bugs live nearby but are **unrelated to ownership/visibility** — don't conflate
them with the above if re-investigating this area:
- `ServiceCompanyAppTrackerController::dinternal_bench_data_ajax()` has a `$boolQuery`
  auto-vivification bug in its `case 22` (Serving Notice sort) block — a plain
  PHP-initialization-order mistake (the filter is built and appended to `$boolQuery`
  *before* `$boolQuery` is ever assigned, so the real assignment three lines later wipes
  it out). Nothing to do with who owns what stage.
- `ServiceCompanyAppTrackerController::dinternal_count()` (shared by `clientapp()`,
  `dsimilar_candidates()`, `dinternal_bench()`) reassigns `$this->job = Job::findOrFail($id)`,
  clobbering the `recommendation_relation`-joined job object the caller had already
  built — blanks the client name/location in the page header. Pure display-data bug,
  not a permissions issue.

## 6. Key files (starting points, not exhaustive)

- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` —
  `clientapp()` / `clientappdata_ajax()` (agency's read-mostly view of a client job's
  pipeline), `dsimilar_candidates()` / `dinternal_bench()` (agency's candidate-sourcing
  tabs — these fund into `rec_candidate_assign()`, which always inserts new candidates
  at `master_status_id = 8`).
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` —
  `client_stage_body_content($id)` (builds the stage-change dropdown; decides
  direct_client vs custom_client set), `client_stage_submit(Request $request)` (the
  actual transition validator — where §3's rules live).
- `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php` —
  `index()` / `loadcounter()` / `data()` (the client's own view of the same pipeline —
  where §4's visibility rule must hold).
- `master_status` table — the stage/id/direct_client/custom_client source of truth
  (§2). Query it directly rather than trusting this table if it's been edited since.

## 7. Standing convention: search bar / sort / filter design & logic

If a **future request asks for a search bar, sort dropdown, or filter drawer** on any
AppTracker candidate-list page — a brand-new page or a change to an existing one — the
**reference implementation is whatever's already built on these four pages**, in this
session's build order:

1. **Similar Candidates** (`similar_candidates.blade.php` /
   `similar_existing_data_ajax()`)
2. **In-house Bench Talents** (`internal_bench.blade.php` / `internal_bench_data_ajax()`)
3. **Agency Bench Talents** (`public_bench.blade.php` / `public_bench_data_ajax()`)
4. **Total Candidates In Job / App Tracker** (`index.blade.php` / `allDataAjax()`)

...and their client-job-flow mirrors (`dsimilar_candidates`, `dinternal_bench`,
`clientapp`), which were built by replicating exactly these four onto the client-job
pipeline. **CV Tracker** (`cvtracker/index.blade.php` / `ServiceCompanyCvTrackerController::allcandidates()`)
was later ported onto this same pattern too — it's the one page in the family that's
**not job-scoped** (a global, company-wide candidate list) and whose `allcandidates()`
method is shared with Offer Tracker/Joining Tracker via a `status_cus` branch — any
future change to CV Tracker's filter/sort must not touch those other two branches.

**Education field — one specific, easy-to-get-wrong sub-convention**: the Education
filter is a **checkbox checklist dropdown** (a button showing "Any Education" or the
selected values, opening a panel of checkboxes — `Any` / `Any Degree` / `Any Post
Graduation` / `Any Graduation` / `10th` / `12th` / etc from `qualificationTag`), **not**
a free-typed Tagify field — even though it drives the same hidden `#kt_tagify_education`
JSON-array input the backend already reads. Multi-select (checking a specific value
unchecks "Any" and vice versa). See `similar_candidates.blade.php`'s
`simf_edu_dd`/`simfEducationSelected` for the canonical implementation — CV Tracker's
port initially shipped with the old free-typed Tagify version by mistake; if you see
that pattern anywhere else in this family, it's the wrong one and should be fixed to
match this.

**Two other easy-to-drop details when porting this pattern to a new page**, both found
missing/broken on CV Tracker's first port and worth checking for on any future one:
- Location Tagify's autocomplete: `tagifylocation.dropdown.show.call(tagifylocation, query)`
  — the `query` argument is required (it's what the dropdown filters/matches against);
  dropping it silently breaks the location suggestion dropdown.
- Salary-in-words hints (`.minimum_salary_word`/`.maximum_salary_word` spans next to
  `#min_sal`/`#max_sal`): needs the full `Ones`/`Teens`/`Tens` word-conversion helper set
  and its `keyup` handlers copied over too, not just the empty `<span>` markup — see
  `candidates_request.blade.php`'s `brfConvertToWords`/`brfCapitalizeFirstLetter`.

**Both the design and the logic carry over as the default**, not just the visual
layout:
- Design: the right-side Metronic drawer (replacing any old Bootstrap modal), the
  restyled toolbar (search box + custom sort button/panel + Filter button with an
  active-count badge), the Active Filters chip summary, the loader/toast patterns, and
  the one-CSS/JS-prefix-per-page naming convention (`simf-`, `ibf-`, `abf-`, `plf-`,
  `caf-`, `dsf-`, `dib-`, …).
- Logic: the must(mandatory)+should(optional) skill hybrid (starred skills are a hard
  requirement, typed-but-unstarred skills stay additive), the NCR/metro location
  clustering, the free-text `multi_match` search, the "Best Match" sort (match-count →
  a page-appropriate tiebreak → notice-period-rank), the 3-tier skill-chip highlighting
  (mandatory-matched / optional-matched / unmatched), and comparing Active-Filters
  chips against each field's *actual default* rather than a plain truthiness check (a
  field defaulting to `"0"` is still "no filter" until the user changes it away from
  that default).

**What should be adjusted is the business logic specific to that page's own data and
functionality** — not copied blindly:
- Which ES index it queries (`ELASTIC_CANDIDATES` vs `ELASTIC_CANDIDATES_BENCH` vs
  `ELASTIC_CANDIDATES_REQUEST`) and that index's actual field names (e.g. `city` vs
  `city_name`/`country_name`).
- Which filter fields even exist on that page (Hiring Type only makes sense on Bench
  pages; Company Tagify is present-but-dead on every page it appears on — preserve that
  as dead, don't wire it up unprompted).
- The sort's tiebreak choice (`created_at` "uploaded first" vs `updated_at` "bench
  date" — pick whichever date field is that page's real freshness signal).
- Whether a stage/Kanban concept applies at all (Total Candidates In Job and `clientapp`
  have one; Similar Candidates, both Bench pages, and Bench Talent Requests don't).
- Anything from §2–§4 above if the page touches the client-job pipeline specifically.

In short: **reuse the pattern, don't reinvent it — but don't copy a field or a rule
onto a page whose actual data doesn't have it.**

---

## 8. The shared drawer architecture (2026-07-19)

Every stage change on **AppTracker, CV Tracker, ClientApp, Offer Tracker, and Joining
Tracker** goes through exactly one of two controller methods in
`ServiceCompanyJobApplicationController.php`, both persisting to
`add_job_wise_condidates.master_status_id`:

| | `stage_submit()` | `client_stage_submit()` |
|---|---|---|
| Body partial | `job-applications/stage_body_content.blade.php` | `job-applications/client_stage_body_content.blade.php` |
| Sub-statuses | `child_stage($id)` — **shared by both** |
| Coin deduction | Various eventIds elsewhere | Only on the InReview→Sourcing move (event 4) |

Both partials share one visual drawer (icon-coded stage pills, icon-coded sub-status
tiles, Quick Notes chips, a submit button that recolors/relabels to match the selected
stage) built this session as a single source of truth — fix one, fix it everywhere.
Reference/template: `resources/views/service_company/jobs/status.blade.php` (the Job
Status drawer built earlier, same `data-kt-drawer` mechanism).

### 8.1 Per-page row-trigger gating (which method actually fires)

This is the part easiest to get wrong when touching any of these pages — the row
partial decides which flow opens, independent of the drawer/partial itself:

- **AppTracker** (`all_apptracker.blade.php`) — **always** the plain flow
  (`stage_sub_stage` → `stage_submit()`), unconditionally, regardless of
  `thirdparty_company_id`. This page is the job-owner's aggregated view of every
  submitter on their own jobs; no client-flow branching exists here at all.
- **CV Tracker** (`all_candidates.blade.php`) — a **global, company-wide** list (not
  job-scoped). Trigger is `client_stage_sub_stage` (→ `client_stage_submit()`) only when
  `thirdparty_company_id > 0 && thirdparty_company_id == company_id` (viewing your own
  third-party submission) **and** `job_master_status_id == 8` (still InReview). Once
  that same row is sourced or beyond, the trigger becomes `client_stage_locked_notice`
  instead — clicking it shows a modal ("This Stage Is Managed by the Client") rather
  than opening anything; there is **no way to reopen the drawer on this row from this
  page once it's left InReview** (confirmed by reading the row partial directly — the
  `<a>`/class only exists for `job_master_status_id == 8`, a plain inert `<span>`
  otherwise, before this session added the locked-notice trigger onto it).
- **ClientApp** (`clientappdata_ajax.blade.php`) — same conditional logic as CV Tracker,
  but this page is **job-scoped** (one specific client-owned job at a time) rather than
  global. This is the page where an agency actually manages its own submissions to one
  client job, so it's also where the InReview→Sourcing "becomes visible to the client"
  moment structurally lives (§8.4).
- **Offer Tracker / Joining Tracker** — plain flow only; both pages hard-filter to
  `job_master_status_id` 5/6 (already past the sourcing handoff), so the client-gated
  branch is structurally unreachable there.

### 8.2 Two confirmed bugs in `child_stage($id)` / sub-stage visibility (fixed)

- **`requisition_owner==0` used to force-hide the entire Sub Stage section for every
  non-InReview stage**, including Rejected — meant to protect InReview specifically, but
  written broadly enough to also silently hide a recruiter's own rejection-reason tiles
  on a client-managed job, even though the recruiter is the one authorized to reject
  from InReview in the first place. **Fixed**: both the PHP initial-render check and the
  JS `csdcSelectStage()` gate now only suppress the section for `id == 8` (InReview) —
  every other stage's visibility is decided purely by whether the backend has data.
- **`child_stage($id)` didn't account for §2's two Rejected ids.** The normal
  agency-self-view drawer submits id **9** (per §2's table — the `direct_client=1`
  row), but Settings → Interview Process (`ServiceCompanyCandidateStatusController::store()`)
  only ever has a form field for **parent=7** — there is no UI path to ever create
  `candidate_status` rows with `parent=9`. So `child_stage(9)` always returned empty,
  even when a company genuinely had Rejected reasons configured (they're stored under
  `parent=7`). **Fixed**: `child_stage($id)` now folds `id === 9` to `7` before querying
  `candidate_status`, since both ids represent the same conceptual "Rejected reasons"
  list to the company — just gated to a different client-relationship type per §2.

### 8.3 Un-reject / resume-point bug (fixed)

`stage_point_tb` records where a candidate should resume if later un-rejected (matched
against `job_id` + `candidate_id`). The write inside `client_stage_submit()`'s
non-Rejected `else` branch used to record `'stage_id' => $master_status` — i.e. the
**target** stage — even when the target itself *was* Rejected(7). That means moving
InReview(8)→Rejected(7) recorded the resume point as `7`, not `8`, permanently blocking
any later attempt to un-reject back to InReview (the un-reject check requires the target
to exactly match the recorded `stage_id`). **Fixed**: the write now uses
`$resumeStageId = ($newStageId == 7) ? $currentStageId : $master_status` — recording the
stage being rejected *from*, not the Rejected stage itself. `stage_submit()` (plain
flow) never had this exact bug — it structurally skips the whole `stage_point_tb` write
when the target is 7, which happens to leave whatever the last real resume point was
already correctly in place as a side effect, not by original design.

Un-reject rule, confirmed: you can only move out of Rejected back to the *exact*
recorded resume stage — or, if the recorded stage was Offered(5)/Joined(6), the only
valid target is Sourcing(1) (a full restart), enforced identically in both
`stage_submit()` and `client_stage_submit()`.

### 8.4 Key files (this session's additions)

- `app/Helper/GeneralMethod.php` — `csdSubStatusMeta()` (sub-status icon/color mapping).
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` —
  `child_stage()` (§8.2 fold fix), `stage_submit()`/`client_stage_submit()` (§8.3
  resume-point fix, `stageSequenceGuardMessage()` — the shared helper behind the "you
  can only move to X/Y/Rejected" jump-violation message).
- `resources/views/service_company/job-applications/stage_body_content.blade.php` /
  `client_stage_body_content.blade.php` — the shared drawer body, icon-coded pills/tiles.
- `resources/views/service_company/ajax_pages/all_apptracker.blade.php` /
  `clientappdata_ajax.blade.php` / `all_candidates.blade.php` — row-level trigger
  gating (§8.1).
