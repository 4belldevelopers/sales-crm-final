# Public Career Apply: Mandatory Notice Period Validation Standard

**Document ID:** `docs/audit documents/candidate/2026-11-11-career-apply-mandatory-notice-period-standard.md`  
**Module:** Public Career Application (`career-apply`)  
**Scope:** Direct Candidate Application Pipeline  
**Author:** Antigravity Agent  
**Date:** 2026-11-11  
**Status:** Completed & Verified  

---

## 1. Executive Summary

On the public candidate career application page (`resources/views/career-apply.blade.php`), applications are submitted by direct candidates themselves. Unlike recruiter-facing workflows where early pipeline stages may accommodate incomplete notice period information, direct applicant submissions require complete joining availability upfront.

### Core Changes Implemented:
1. **Unconditional Visual Requirement:**
   Notice Period labels render with the mandatory red asterisk `<span class="rq">*</span>` permanently, without conditional hiding or `(Optional for Freshers)` notes.
2. **Universal Frontend Validation Enforcement:**
   In `validateCandidateRequiredFields()`, Notice Period & Joining Details validation is executed unconditionally for all direct applicants across all experience levels.
3. **Fresher State Error Preservation:**
   In `syncFresherUIState()`, error clearing for `np_id` was removed so that switching experience levels preserves mandatory notice period enforcement.
4. **Authoritative Backend Validation:**
   In `app/Services/JobApplyService.php`, server-side Notice Period validation is enforced unconditionally for all incoming applications.

---

## 2. File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `resources/views/career-apply.blade.php` | Rendered unconditional red asterisk `*` for Notice Period and LWD labels; moved Notice Period validation outside `if (!isFresher)` in `validateCandidateRequiredFields()`; updated `syncFresherUIState()` to retain Notice Period errors. |
| `app/Services/JobApplyService.php` | Enforced server-side validation for `serving_notice_period` (and `np_id` / `ddl_np_id`) unconditionally for all applicants submitted via the career application service. |
| `tests/Feature/CandidateCareerApplyNoticePeriodTest.php` | New automated feature test suite verifying unconditional markup, frontend validation rules, and backend service rejection when notice period is omitted. |

---

## 3. Verification Evidence

### PHP Lint Syntax Check:
```
php -l resources/views/career-apply.blade.php
-> No syntax errors detected

php -l app/Services/JobApplyService.php
-> No syntax errors detected
```

### Automated PHPUnit Suite Execution:
```
./vendor/bin/phpunit tests/Feature/CandidateCareerApplyNoticePeriodTest.php
-> OK (2 tests, 10 assertions)

./vendor/bin/phpunit tests/Feature/CandidateCareerApplyNoticePeriodTest.php tests/Feature/CandidateSalaryHikeCalculationTest.php tests/Feature/CandidateSalarySynchronizationTest.php tests/Feature/CandidateCommentsAndNoticePeriodRegressionTest.php
-> OK (16 tests, 180 assertions)
```
