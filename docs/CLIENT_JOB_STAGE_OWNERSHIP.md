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
pipeline.

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
