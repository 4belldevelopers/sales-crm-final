# 5-Field Resume Parser Frontend Hydration Forensic Remediation Report

**Date:** 2026-11-09  
**Module:** Resume Parser & Candidate Form Hydration (SFHP)  
**Environments:** `testing.jero.works` (Staging) & Local Chromium Playwright  
**Target Pages:** [`resources/views/service_company/job-applications/create.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php), [`resources/views/service_company/job-applications/clientcreate.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/clientcreate.blade.php), [`resources/views/service_company/job-applications/jero-client-parser.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/jero-client-parser.blade.php)  
**Deployment Run:** GitHub Actions Run `42635609862` (Commit `8263939c`) — **100% SUCCESS**

---

## 1. Executive Summary

During testing of the resume `JEROWORKS_Kritika_Srivastava.pdf`, the user reported that 5 candidate profile fields were not populating in the frontend UI:
1. **Current Company** (was showing placeholder `"Enter current company"`)
2. **Highest Qualification** (was showing `"Select qualification"`)
3. **Total Experience** (was defaulting to `"Fresher"` / `"Select Month"`)
4. **Primary Skills** (was showing `"No primary skills added yet."`)
5. **Career Summary** (was blank `"0 / 100"`)
*Note: Current Designation remained preserved as `"Lead Full Stack PHP Engineer (V7)"` per user instruction.*

A full forensic investigation identified the exact DOM, JavaScript, and PHP lifecycle reasons behind each of the 5 fields, corrected them across both Add Candidate and Client Candidate flows, and verified the end-to-end hydration with real Chromium browser automation against the user's PDF.

---

## 2. Forensic Root-Cause Analysis & Fix Matrix

| Field | User Symptom | Root Cause | Engineering Resolution |
| :--- | :--- | :--- | :--- |
| **Current Company** | Empty input | Company name was located inside `experience[0].description[0]` (`"Triotree Pvt. Ltd., Noida (8 Months)"`) rather than a top-level `company` property. | `extractCompanyFromExperience()` scans `cand.currentCompany`, `experience[i].company`, `experience[i].description[j]`, and `experience[i].title` patterns, sanitizing duration/city suffixes. Direct DOM assignment fallback added. |
| **Highest Qualification** | Showed `"Select qualification"` | The custom combobox `#jero-cs-qualification` did not update its visible `.jero-cs-value` span if the matched item was outside an `<optgroup>` or if the combobox change event did not find a pre-rendered item. | Added automatic fallback directly from native `<select id="qualification">` option text and badge in both the parser bridge and the native select `change` event listener. |
| **Total Experience** | Defaulted to `"Fresher"` | In PHP, loose equality `$selected_exp_year == 0` evaluated to `true` when `$selected_exp_year` was `null` on new candidate creation, pre-selecting `<option value="0" selected>Fresher</option>`. | Fixed PHP comparison to strict `(!is_null($selected_exp_year) && (int)$selected_exp_year === 0)`. In JS, added native dispatch and explicit exception to allow updating `"0"` default with parsed experience. |
| **Primary Skills** | Showed `"No primary skills added yet."` | `candidateSelectedSkills()` relied solely on `tagifyskill.value`. If Tagify had not yet synced or had strict object expectations, `refreshCandidatePrimarySkills()` cleared all visual chips. | Implemented 3-tier fallback in `candidateSelectedSkills()`: Tagify value $\to$ raw input value $\to$ existing DOM chips. `addCandidatePrimarySkillsBatch()` now instantly mounts visual chips and passes `{ value: resolved }` objects to Tagify. |
| **Career Summary** | Blank textarea (`0 / 100`) | Textarea element `#careersummary` character counter parent traversal failed to locate `.cc` in certain nested card layouts. | Updated parent lookup to `csEl.closest('.summary-card-body, .candidate-card-body, .fsc')`, set `csEl.value` unconditionally, and updated the word counter display directly. |
| **Current Designation** | Pre-filled title preserved | User explicitly requested: *"Job Title Replacement - do not change it, keep as it is"*. | Maintained strict fill-if-empty check so existing pre-filled job titles (`"Lead Full Stack PHP Engineer (V7)"`) are **never overwritten**. |

---

## 3. End-to-End Test Verification (Chromium + Real User PDF)

Executing `scripts/test-user-pdf-form-hydration.cjs` with `JEROWORKS_Kritika_Srivastava.pdf`:

```json
{
  "duration": 4031,
  "full_name": "Kritika Srivastava",
  "email": "srivastavakritika544@gmail.com",
  "phone": "7992163061",
  "current_company": "Triotree Pvt. Ltd.",
  "current_designation": "Lead Full Stack PHP Engineer (V7)",
  "total_exp_year": "2",
  "total_exp_month": "7",
  "qualification": "16",
  "qualification_combobox_text": "BCA (Computer Applications / Software)",
  "careersummary_text": "To establish myself as a skilled IT professional in a growth-oriented organization where I can utilize my technical support, development, coordination, and communication skills to contribute effectively towards organizational success and project execution. 2.6 years of experience in Web Application Development and Technical Support using Microsoft .NET technologies. Hands-on experience in C#.NET, ASP.NET MVC Core, Web API, SQL Server, ADO.NET, and Entity Framework . • Experience in Technical Support, issue resolution, client handling, and application troubleshooting • Worked on client coordination, task follow-ups, requirement understanding, and team coordination activities. Knowledge of SQL Server 2019 • Strong communication, interpersonal, problem-solving, and",
  "careersummary_counter": "100 / 100",
  "addedBatchSkills": [
    "C#",
    "ASP.NET",
    "SQL",
    "C++",
    "jQuery"
  ],
  "chipCount": 5
}
```

---

## 4. Staging Deployment Evidence

- **Commit:** `8263939c` (`fix(parser): robust 5-field hydration for company, qualification, experience, skills, and summary`)
- **Pushed To:** `origin/main` & `origin/staging`
- **GitHub Actions Run:** `42635609862`
- **Status:** **Completed Success** (1m 27s)
- **Octane / OPcache:** Freshly restarted with `php artisan view:clear` and `php artisan view:cache`.
