# Add Candidate Create Page Design Replication Plan

## Purpose

Redesign the Service Company Add Candidate page at `service_company/job-applications/create` by taking visual inspiration from the Add Talent page at `service_company/bench-candidates/create`, while preserving all existing Add Candidate business logic, field names, IDs, validation hooks, AJAX submit routes, resume parsing, interview slot behavior, job-question behavior, and hidden job context.

This document is the approved design map and implementation record for the completed card redesign.

## Source Pages Reviewed

- Inspiration page: `resources/views/service_company/bench-candidates/create.blade.php`
- Target page: `resources/views/service_company/job-applications/create.blade.php`
- Add Talent visual reference stylesheet: `demo/css/candidate-form.css`
- Add Candidate isolated stylesheet: `demo/css/job-application-create.css`
- Add Talent style include: `resources/views/service_company/bench-candidates/partials/_form_styles.blade.php`
- Routes: `routes/service.php`

## Non-Negotiable Implementation Boundary

- No controller, route, request, model, observer, Elasticsearch, Redis, or database logic changes.
- No new SQL, Elasticsearch query, or backend read path.
- No field name changes.
- No ID changes for inputs/buttons used by JavaScript, validation, resume parsing, CKEditor, Tagify, phone code handling, salary calculation, notice period, interview slots, or AJAX submit.
- No removal of any existing Add Candidate field, hidden input, conditional block, or business-specific section.
- No copy of Add Talent-only business features into Add Candidate.

## Add Candidate Elements To Redesign

These Add Candidate areas should receive the Add Talent visual treatment because they are the same or strongly related form responsibilities:

| Add Candidate section | Current evidence | Redesign source from Add Talent | Implementation direction |
|---|---:|---|---|
| Page toolbar and submit action | `job-applications/create.blade.php:154-164` | Add Talent topbar at `bench-candidates/create.blade.php:110-138` | Use the cleaner topbar layout, but keep Add Candidate wording and button IDs: `save-form` / `update-form`. Do not add marketplace state. |
| Resume Upload | `job-applications/create.blade.php:202-388` | Add Talent `resume-card` at `bench-candidates/create.blade.php:204-349` | Copy the card header, icon, spacing, upload panel, progress, selected file, and uploaded-info presentation. Preserve `resume`, `resumeCheck`, `resume_session_uuid`, `fileInfo`, `upInfo`, retry/remove/replace IDs. |
| Career Summary | `job-applications/create.blade.php:395-406` | Add Talent `summary-card` at `bench-candidates/create.blade.php:350-368` | Use the professional summary card look. Keep `id="careersummary"` and `name="careersummary"`. |
| Basic Details | `job-applications/create.blade.php:450-547` | Add Talent `bd-card` at `bench-candidates/create.blade.php:414-550` | Convert to Candidate Profile card with icon controls, better field grouping, secondary contact collapse, and optional personal info area. Preserve all Add Candidate fields. |
| Primary Skills | `job-applications/create.blade.php:555-560` | Add Talent skills visual language around `bench-candidates/create.blade.php:1163-1194` | Keep this visible because Add Candidate has a direct `name="skills"` Tagify field. Do not hide it like Add Talent. |
| Professional Details | `job-applications/create.blade.php:564-768` | Add Talent Professional Profile and Experience cards at `bench-candidates/create.blade.php:561-899` | Split visually into Professional Profile, Experience Details, and Availability/Work Preferences. Keep job-title prefill and all original values. |
| Notice Period and Work Mode | `job-applications/create.blade.php:723-760` and `536-547` | Add Talent `aw-card` at `bench-candidates/create.blade.php:800-899` | Use the same availability card treatment. Preserve `serving_notice_period`, `np_id`, `ddl_np_id`, `calender`, and `wm` radio values. |
| Salary Details | `job-applications/create.blade.php:768-966` | Add Talent `br-card` visual structure at `bench-candidates/create.blade.php:900-1163` | Use only the rate-panel design pattern. Keep Add Candidate salary meaning: Current CTC, Expected CTC, salary currency/mode, offer holding, percentage hike, and comments. Do not copy Add Talent commercial model, free/paid service, pay-rate/bill-rate business copy, or marketplace margin logic unless the existing Add Candidate logic already supports it. |
| Job Skills Details | `job-applications/create.blade.php:973-1074` | Add Talent `se-card` table shell at `bench-candidates/create.blade.php:1163-1194` | Copy table styling, card header, and responsive scroll. Do not add Add Skill, Popular Skills, or delete actions because Add Candidate job skills are job-driven and readonly skill names. |
| Professional Links and Additional Details | Existing combined Add Candidate information section | Add Talent Professional Links and Additional Details cards | Split the visual card only. Keep `linkedin`, `github`, `behance`, `otherLink`, `visastatu`, and `referencename` unchanged. |
| Recruiter Note | `job-applications/create.blade.php:1270-1289` | Add Talent card framing style, not a direct feature copy | Improve visual framing around template selector and CKEditor textarea. Preserve `recruiter_note_template_select`, `recruiter_note`, and CKEditor setup. |

## Add Candidate Unique Sections To Keep, Not Blind-Copy

These sections are not present on Add Talent or carry different business meaning. They should be redesigned only with the same card rhythm, spacing, and typography, not by copying Add Talent functionality.

| Add Candidate-only section | Current evidence | Rule |
|---|---:|---|
| Hidden job context | `job-applications/create.blade.php:191-198` | Preserve all hidden inputs: `job_id`, `appId`, `currentPage`, `currentSlug`, `AddJobWiseCondidateRankRow`, `resume_session_uuid`. |
| Job Questions | `job-applications/create.blade.php:1076-1132` | Keep generated `job_question[]` and `additional_detail[]` behavior exactly. Redesign as a compact questions card only. |
| Availability for Next 3 Days | `job-applications/create.blade.php:1139-1221` | Keep all three date/time/mode slots and `getInterviewModeChild(this.value, {{ $jobId }})`. Redesign as interview-slot cards. |
| Recruiter Note templates | `job-applications/create.blade.php:1276-1285` and JS around `4000-4011` | Keep template injection and CKEditor behavior unchanged. |
| Resume upload route and parser flow | JS references around `job-applications/create.blade.php:1342-1717` | Preserve route calls and DOM IDs used by `resume-presign.js` and upload parsing. |
| Add Candidate submit/update AJAX | JS references around `job-applications/create.blade.php:2575-2708` and `3443-3575` | Preserve URLs, buttons, FormData, validation, and redirect/current page behavior. |

## Add Talent Elements Not To Copy

These Add Talent elements should not be added to Add Candidate because the functionality is not present or has different business meaning:

- Talent Stage card: `talent_stage` Pool/Bench/Hold/Placed/Exited belongs to in-house talent lifecycle, not Add Candidate submit flow.
- Marketplace publish/status banner and marketplace privacy messaging as-is. Add Candidate is job submission focused; do not imply marketplace publishing.
- Add Talent commercial model choices: Free Service / Paid Service and marketplace margin summary should not replace Add Candidate Current CTC / Expected CTC logic.
- Relationship Manager card/drawer from Add Talent unless Add Candidate already has that field. It is not part of the reviewed Add Candidate create form.
- Popular Skills and Add Skill actions from Add Talent skills table. Add Candidate job skills come from the job requirement and have readonly skill names.
- In-House Talent badges/copy. Add Candidate should use Candidate / Job Candidate language.

## Implemented Target Card Order

1. Add Candidate toolbar with the existing Submit/Update action.
2. Resume Upload and Career Summary in the existing two-card layout.
3. Candidate Profile: Full Name, Primary Email, Primary Contact Number, Current Location, Highest Qualification, Nationality, plus collapsible secondary details for Gender, Date of Birth, Secondary Email, and Secondary Contact Number.
4. Experience Details: Total Experience and Relevant Experience.
5. Professional Profile: Current Company, Current Designation, and Employment Type; visually hidden when Total Experience is Fresher without disabling or clearing its fields.
6. Availability & Work Preferences: Notice Period Status, notice days or Last Working Date, and Reason for Job Change.
7. Preferred Work Mode as its own card: Remote, Hybrid, and Work From Office.
8. Primary Skills, preserving the original Tagify `skills` input.
9. Salary Details: Current Salary, Expected Salary, Salary Summary, Offer in Hand, Hike Percentage, and Comments using the Add Talent commercial-card visual language only.
10. Job Skills Details, preserving job-driven skill rows and values.
11. Screening Questions, preserving the original conditional job-question generator and submitted field names.
12. Availability for Next 3 Days, preserving all three interview slots and mode handlers.
13. Professional Links: LinkedIn, GitHub, Behance, and Portfolio / Website.
14. Additional Details: Current Visa Status and Professional Reference.
15. Recruiter Note, preserving template selection and CKEditor integration.
## Files Changed

- `resources/views/service_company/job-applications/create.blade.php`
- `demo/css/job-application-create.css`
- `docs/V2_Submit_Candidate/Add-Candidate-Create-Design-Replication-Plan.md`

All new styles are scoped under `.job-candidate-create`; no Add Talent, Bench Candidate, controller, route, request, model, query, or persistence file was changed.
## Verification Record

- PHP syntax: passed for `resources/views/service_company/job-applications/create.blade.php`.
- Laravel Blade compilation: passed with php artisan view:cache.
- Whitespace and patch validation: passed with `git diff --check`.
- Form contract comparison against `HEAD`: all 80 original submitted field names remain; no field name was added or removed.
- DOM hook comparison against `HEAD`: all 114 original IDs remain.
- Field signature comparison: no named input changed its element type, input type, or ID.
- Route comparison: no original Blade route expression was removed.
- Inline handler comparison: no original `onclick`, `onchange`, `oninput`, or `onsubmit` hook was removed.
- Added IDs are presentation-only: `candidateProfessionalProfile`, four salary auto-display IDs, and three salary summary IDs.
- Live authenticated browser verification remains for the testing server at 1366x768, 1920x1080, and mobile widths.