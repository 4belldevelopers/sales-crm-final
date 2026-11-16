# Candidate Edit: Job Skills Details 'Year' Field Optional Decoupling

## Metadata
- **Date:** 2026-11-16
- **Module:** Candidate Sourcing / Candidate Edit (`resources/views/service_company/job-applications/create.blade.php`, `clientcreate.blade.php`)
- **Type:** Architectural Audit & Decoupling Changelog
- **Compliance:** Rule 1 & Rule 2 (CQRS MySQL Writes / ES Reads), Rule 7 & Rule 17 (Page Isolation & Zero Business Logic Mutations), Rule 18 (Product/Staffing Frozen), Rule 21 (Audit Standard Layout)

---

## Executive Summary
When updating an existing candidate profile (`page_event == 'EDIT'`) in stages beyond InReview (e.g. Sourcing, Screening, Shortlist), client-side validation previously treated the **Experience (Years)** select dropdown (`select[name^="YearskillId"]`) under **Job Skills Details** as strictly mandatory (`val && val !== '0'`), triggering the `showCandidateRequiredFieldsModal` dialog and blocking profile updates unless years of experience were entered for every job skill.

In contrast, adding a new profile in InReview (stage 8) bypassed this validation check, creating an inconsistency where editing existing candidates forced experience year values. Furthermore, the table headers in both `create.blade.php` and `clientcreate.blade.php` displayed a red required asterisk (`<th>Experience (Years) <span class="rq">*</span></th>`), misrepresenting optional experience as a hard requirement.

This update decouples the 'Year' field from mandatory validation, keeping it strictly optional (defaulting to 0 / "Select Year"), while preserving mandatory **Proficiency Rating** (`select[name^="rating"]`) per ATS strict rating governance rules.

---

## Architecture & Data Flow

```
[Candidate Edit Submission (#update-form)]
                     │
                     ▼
       detectCandidateMissingFields()
                     │
     ┌───────────────┴───────────────┐
     │                               │
[Experience (Years)]       [Proficiency Rating]
 (Optional)                 (Mandatory: 1 to 5)
     │                               │
     │ Default: 0                    │ If missing / 0:
     ▼                               ▼
 [Allowed]                     addMissing('job_skills')
     │                               │
     ▼                               ▼
[executeCandidateSubmit()]     [Show Modal Dialog]
     │
     ▼
[ServiceCompanyJobApplicationController::update()]
     │
     ├── $valYear = (int) ($yearSkillId[$skillId] ?? 0);
     ├── $valMonth = (int) ($monthSkillId[$skillId] ?? 0);
     ├── $skillValue = $valYear.'.'.$valMonth.' '.$ss;
     └── $rating = (int) ($ratingArr[$skillId] ?? 0);
     │
     ▼
[candidate_job_skill_exp (MySQL SSOT)]
```

1. **Client-Side Validation Decoupling:**
   - In `detectCandidateMissingFields()`: removed the loop iterating over `$jobSkillYears` that flagged `missingJobSkills = true` when `val === '0'`.
   - Maintained `$jobSkillRatings` iteration requiring `rVal && rVal !== '0'` so ATS skill assessment remains strict and authoritative.
   - Updated `candidateFieldConfigMap['job_skills'].message` to `"Job skills proficiency ratings are required."`.

2. **Visual Affordance & UI Alignment:**
   - In `create.blade.php` (line 4107) and `clientcreate.blade.php` (line 4157): removed `<span class="rq">*</span>` from `<th>Experience (Years)</th>`.
   - The required asterisk `<span class="rq">*</span>` remains only on `Skill` and `Proficiency Rating`.

3. **Backend Persistence (SSOT):**
   - The controller already handles empty / zero experience cleanly via `$valYear = isset($yearSkillId[$skillId]) ? (int) $yearSkillId[$skillId] : 0;`, creating valid records in `candidate_job_skill_exp` (e.g. `'0.0 Year'`) with the candidate's rating.

---

## File Changes Matrix

| File Path | Component | Change Description |
|-----------|-----------|--------------------|
| `resources/views/service_company/job-applications/create.blade.php` | Job Skills Table Header | Removed required asterisk `<span class="rq">*</span>` from `<th>Experience (Years)</th>`. |
| `resources/views/service_company/job-applications/create.blade.php` | Config Map & Validation | Updated message in `candidateFieldConfigMap` and removed `$jobSkillYears` zero-check from `detectCandidateMissingFields()`. |
| `resources/views/service_company/job-applications/clientcreate.blade.php` | Job Skills Table Header | Removed required asterisk `<span class="rq">*</span>` from `<th>Experience (Years)</th>`. |
| `resources/views/service_company/job-applications/clientcreate.blade.php` | Config Map & Validation | Updated message in `candidateFieldConfigMap` and removed `$jobSkillYears` zero-check from `detectCandidateMissingFields()`. |
| `tests/Feature/StrictSkillRatingValidationTest.php` | Feature Test Suite | Added `test_candidate_job_skills_with_zero_years_experience_passes_validation_when_rated()` verifying zero experience years passes validation when rated. |

---

## 15.6" Screen Fit & UI Responsiveness
- The Job Skills table retains its responsive scroll wrapper (`.candidate-job-skills-scroll` / `.candidate-table-wrap`) ensuring zero horizontal overflow on standard 1366×768 and 1920×1080 displays.
- Select elements (`select[name^="YearskillId"]`, `select[name^="MonthskillId"]`, `select[name^="rating"]`) maintain uniform height and Metronic solid styling (`.form-select .form-select-solid`).

---

## Verification Evidence

### 1. Feature Test Execution
```bash
php artisan test --filter=StrictSkillRatingValidationTest
```
**Output:**
```
   PASS  Tests\Feature\StrictSkillRatingValidationTest
  ✓ get candidate skill rating status detects unrated skills                                                     1.11s  
  ✓ partial rating fails validation                                                                              0.30s  
  ✓ full rating passes validation                                                                                0.51s  
  ✓ check candidate sourcing missing fields includes skill rating                                                0.27s  
  ✓ salary details pass when yearly ctc is provided                                                              0.30s  
  ✓ salary details fail when both ctc are empty                                                                  0.28s  
  ✓ candidate job skills with zero years experience passes validation when rated                                 0.28s  

  Tests:    7 passed (16 assertions)
  Duration: 4.44s
```

### 2. PHP Syntax Lint
```bash
php -l tests\Feature\StrictSkillRatingValidationTest.php
# No syntax errors detected in tests\Feature\StrictSkillRatingValidationTest.php
```
