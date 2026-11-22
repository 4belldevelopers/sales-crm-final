# Agency Bench Candidate Offer Lifecycle & Confidentiality Architecture

> **Module:** Service Company &bull; Offer Tracker (`applicationoffer`) & Create Offer (`create-offer`)  
> **Date:** 2026-11-22  
> **Topic:** Agency Bench Full-Time Offers, My Organization Support, Candidate Email Confidentiality & Educational Institution Exception  
> **Type:** Architectural Blueprint, Forensic Feedback & Phased Implementation Plan  
> **Author:** Antigravity AI  
> **Standards:** Rule 1 (MySQL Writes Only), Rule 2 (Elasticsearch Universal Reads), Rule 14 (15.6" Screen Fit), Rule 20 (Card + Sub-Card Design System), Rule 21 (Mandatory Audit Documentation), Rule 25 (Canonical Tags & Color Combinations)

---

## 1. Executive Summary

In JERO ATS and B2B Talent Marketplace, when a company offers an **Agency Bench Candidate**, the recruitment dynamics differ fundamentally from direct sourcing. The user has proposed four foundational business and architectural rules:

1. **Full-Time Offers for Agency Bench Candidates:**
   - Agency bench resources must **not** be restricted solely to subcontracting/contract terms. Hiring companies must have full flexibility to offer them **Full-Time Employment (FTE)** (e.g., permanent placement, contract-to-hire buyout, or talent acquisition) in addition to Contract / Hourly terms.
   - The Create Offer drawer must allow selecting **Full-Time** as well as **Contract**, seamlessly swapping between the *Fulltime Offer Letter* (`offer_terms`) and *Agency Offer Terms* (`agency_offer_terms`).

2. **"My Organization" Support:**
   - Sponsoring companies hiring for their own headcount (**"My Organization"** requisitions) must be fully authorized to make offers to agency bench talent (for both internal permanent roles and internal contract deployment).

3. **Strict Candidate Email Confidentiality (Anti-Poaching & Disintermediation Guard):**
   - When an agency uploads a bench candidate, their personal email and phone are explicitly marked: `Professional Email (Not Disclosed to Client)`.
   - During the offer process, the candidate's real personal email address **must remain strictly confidential** and **cannot be disclosed** to the hiring organization.
   - The offer communication must be addressed to the **Agency Relationship Manager (RM) / Vendor Contact** (`bench_assign.user_id -> users.email`). In the hiring company's UI, the candidate's personal email must be masked (`Confidential (Agency Bench Talent)`), preventing direct poaching.

4. **Educational & Training Institution Exception:**
   - An intelligent exception applies to **Universities, Colleges, and Professional Training Academies** (e.g., `td_industries` matching *Education Management*, *Higher Education*, *Professional Training & Coaching*):
   - These institutions place students and trained graduates seeking **direct full-time career opportunities**.
   - For these verified institutions, the candidate's contact email is **unmasked**, the default offer template is **Fulltime Offer Letter**, and the offer is dispatched directly to the candidate with the institution's Placement Officer / RM CC'd.

---

## 2. Architecture & Data Flow

```
                                [ CANDIDATE IN OFFER TRACKER ]
                                              │
                    ┌─────────────────────────┴─────────────────────────┐
                    ▼                                                   ▼
         [ Direct Sourced Candidate ]                             [ Bench Candidate ]
         (condidate_type != bench)                           (condidate_type == bench_condidate)
                    │                                                   │
                    │                               ┌───────────────────┴───────────────────┐
                    ▼                               ▼                                       ▼
             Direct Candidate              [ Internal Bench ]                      [ Agency Bench ]
             • Real email shown            (company_id == auth)                   (company_id != auth)
             • Full-Time / Contract                 │                                       │
             • Sent to Candidate                    ▼                                       │
                                           Own Company Bench                                │
                                           • Real email shown                               │
                                           • Full-Time / Contract                           │
                                           • Sent to Candidate                              │
                                                                                            ▼
                                                                           ┌────────────────────────────────┐
                                                                           │ Originating Company Category?  │
                                                                           └────────────────┬───────────────┘
                                                                                            │
                                                   ┌────────────────────────────────────────┴────────────────────────────────────────┐
                                                   ▼                                                                                 ▼
                                    [ Standard Staffing Agency ]                                                    [ Educational / Training Institute ]
                                    (Industries: IT / Staffing / Services)                                          (td_industries: Education / Training)
                                                   │                                                                                 │
                                                   ▼                                                                                 ▼
                                    STRICT CONFIDENTIALITY RULE                                                     EDUCATIONAL EXCEPTION RULE
                                    • Candidate Email: MASKED / HIDDEN                                              • Candidate Email: UNMASKED & VISIBLE
                                    • Primary "To": Agency Relationship Manager (RM)                                • Primary "To": Candidate Direct
                                    • Terms Options: Full-Time OR Agency Terms                                      • Primary "CC": Placement Officer / RM
                                    • Requisitions: My Organization + Clients                                       • Terms: Full-Time Offer Letter
```

---

## 3. Forensic Code Review of Current Implementation

### 3.1. Database Mapping Gap in Send Offer (Step 5)
In `resources/views/service_company/job-applications/create-offer.blade.php`:
```blade
{{-- CURRENT CODE (BROKEN) --}}
@if(@$application->condidate_type=='bench_condidate')
    $bench_assign = DB::table('users')
        ->join('bench_assign','bench_assign.user_id','=','users.id')
        ->select('users.email')
        ->where('bench_assign.bench_applications_id', @$application->id)
        ->first();
    $so_email = @$bench_assign->email ?? @$application->email;
@endif
```
- **Bug:** `@$application->id` is `job_applications.id`. But `bench_assign.bench_applications_id` references `bench_applications.id`.
- **Impact:** The query fails (returns `NULL`), causing `$so_email` to fall back to `@$application->email` (the candidate's private personal email), exposing confidential candidate data to the hiring company and violating marketplace confidentiality.
- **Fix:** Map through `rec_candidate_assign`:
  ```php
  $benchAppId = DB::table('rec_candidate_assign')
      ->where('source_application_id', $application->id)
      ->value('source_from_id');
  ```

### 3.2. Hardcoded Recipient Lock in Step 5
In `create-offer.blade.php` (line 4268):
- `#co_to_chips` is permanently marked `co-send-chips--locked`.
- The user cannot select whether to send to the Agency RM or the Candidate.
- The Add-Email drawer (`#co_send_drawer`) only lists "Internal Company Approvers" and "Client Company Approvers", completely omitting **Agency / Vendor Contacts**.

### 3.3. Bias Toward Contract Terms in Step 2
In `ServiceCompanyJobApplicationController.php` (lines 7571–7640):
- When `condidate_type == 'bench_condidate'`, the system defaults exclusively to `agency_offer_terms` (Contract type).
- There is no automated prompt or mechanism to support Full-Time buyout / permanent placement for agency bench candidates.

---

## 4. Phased Implementation Plan

### Phase 1: Institutional Sourcing & Confidentiality Engine (`CandidateOriginResolver`)
**Objective:** Provide a single-source-of-truth service to resolve candidate origin, confidentiality level, and allowed recipient emails.

- **Class:** `App\Services\CandidateOriginResolver`
- **Responsibilities:**
  1. Determine if a candidate is **Direct**, **Internal Bench**, or **Agency Bench**.
  2. For Agency Bench candidates, inspect the vendor's `companies.industries` in `td_industries`:
     - Education Management (`ind_id = 35`)
     - Higher Education (`ind_id = 56`)
     - Primary/Secondary Education (`ind_id = 109`)
     - Professional Training & Coaching (`ind_id = 111`)
  3. If vendor is an **Educational/Training Institution**:
     - `is_confidential = false`
     - `origin_type = 'education_bench'`
     - `allowed_terms = ['fulltime', 'internship', 'contract']`
     - `default_terms = 'fulltime'`
     - `primary_recipient = candidate_email`
     - `cc_recipient = agency_rm_email`
  4. If vendor is a **Standard Staffing Agency**:
     - `is_confidential = true`
     - `origin_type = 'agency_bench'`
     - `allowed_terms = ['agency', 'fulltime', 'contract']`
     - `default_terms = 'agency'`
     - `primary_recipient = agency_rm_email`
     - `display_email = 'Confidential (Agency Bench Talent)'`
     - `candidate_email = HIDDEN`

---

### Phase 2: Create Offer Step 1 & Step 2 — Full-Time & My Organization Integration
**Objective:** Allow recruiters to issue Full-Time offers to Agency Bench resources across Client and "My Organization" jobs.

- **Step 1 (Compensation Breakdown):**
  - Ensure Employment Type pills (**Full-time**, **Contract**, **Freelancer**, **Internship**) remain fully enabled for all bench candidates.
  - For **"My Organization"** jobs, display a badge: `Hiring for Internal Headcount` with clear indicator of whether this is direct payroll or vendor subcontracting.
- **Step 2 (Offer Letter Template):**
  - If `origin_type === 'agency_bench'`, populate `#co_tpl_select` with:
    1. **Agency Offer Terms** (`agency_offer_terms` — recommended for contract/C2C).
    2. **Fulltime Permanent Offer Letter** (`offer_terms` — for direct placement / buyout).
    3. **Contract Offer Letter** (`contract_offer_terms`).
  - If `origin_type === 'education_bench'`, pre-select **Fulltime Offer Letter** (`offer_terms`).

---

### Phase 3: Create Offer Step 5 — Confidentiality Guard & Smart Recipient Selector
**Objective:** Enforce zero candidate email leakage while providing transparent controls for recruiters.

1. **Candidate Contact Masking:**
   - In Step 5 Hero & Meta strip:
     - For Standard Agency Bench: Candidate Email displays as `••••••@agencypartner.com` with a shield icon: `<i class="fas fa-shield-alt text-warning"></i> Confidential Agency Talent`.
     - For Educational Bench: Displays real candidate email with a graduation icon: `<i class="fas fa-graduation-cap text-success"></i> Campus / Training Talent`.
2. **Dynamic "To" Recipient Resolution:**
   - For Agency Bench:
     - Pre-populate "To" with the **Agency Relationship Manager** (`$agencyRmEmail`).
     - Display a notice: *Offer letter will be delivered to the Agency Partner RM for contractual sign-off.*
     - Hide the candidate's personal email from the "To" and "CC" pickers.
   - For Educational Bench:
     - Pre-populate "To" with the **Candidate Email**.
     - Automatically add the **Placement Officer / Institute RM** to the "CC" chips.
3. **Agency Contacts in Add-Email Drawer:**
   - In `#co_send_drawer`, add a 3rd source tab: **"Agency Contacts"** alongside "Internal Company Approvers" and "Client Company Approvers", allowing recruiters to pick or CC additional vendor coordinators.

---

### Phase 4: Offer Tracker Subcard Tagging & Visual Parity (Rule 20 & 25)
**Objective:** Give recruiters immediate visual clarity on candidate classification on the Offer Tracker card stream.

- In `resources/views/service_company/ajax_pages/offer-tracker.blade.php`:
  - **Subcard 1 (Candidate):** Replace generic `Bench` pill with contextual pills:
    - **`Internal Bench`:** `#eff6ff` bg / `#2563eb` text / `#bfdbfe` border (`fa-users`).
    - **`Agency Bench`:** `#fef3c7` bg / `#92400e` text / `#fcd34d` border (`fa-handshake`).
    - **`Campus / Academy`:** `#ecfdf5` bg / `#059669` text / `#a7f3d0` border (`fa-graduation-cap`).
  - **Subcard 2 (Job Details):**
    - Preserve canonical Rule 25 badges: **`My Organization`** (`#fef3c7`), **`Internal Client`** (`#eff6ff`), **`Direct Client`** (`#ecfdf5`).

---

## 5. File Changes Matrix

| File Path | Component | Changes Required |
| :--- | :--- | :--- |
| `app/Services/CandidateOriginResolver.php` | Backend Service | **[NEW]** Evaluates candidate origin, vendor industry category, confidentiality rules, and authorized recipient email addresses. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | Controller (`createOffer`) | Uses `CandidateOriginResolver` to resolve `$is_confidential`, `$agency_rm_email`, and pre-selects Full-time or Agency terms dynamically. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | Blade View (Steps 1, 2, 5) | Masks candidate email for agency bench; loads RM email into Step 5; adds Agency tab in `#co_send_drawer`; enables Full-time template switching. |
| `resources/views/service_company/ajax_pages/offer-tracker.blade.php` | Blade Partial (Subcard 1) | Replaces generic `Bench` tag with `Internal Bench`, `Agency Bench`, or `Campus / Academy` badges. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | Controller (`fwd_offer_send`) | Enforces backend security check: validates that an agency bench candidate's email is never set as the raw recipient unless exempted by institution status. |

---

## 6. 15.6" Screen Fit & UI/UX Standards

All UI elements strictly adhere to:
- **Rule 14 & Rule 20:** Zero horizontal scroll on 1366×768 and 1920×1080 screens.
- **Typography:** Poppins font, tabular numerals for currency and metrics.
- **Color Discipline:**
  - Agency Bench Confidential: Amber (`#fef3c7` bg, `#92400e` text, `#fcd34d` border).
  - Education / Training: Emerald (`#ecfdf5` bg, `#059669` text, `#a7f3d0` border).
  - Internal Bench: Blue (`#eff6ff` bg, `#2563eb` text, `#bfdbfe` border).
- **Rule 26 (/unslop):** No glowing purple gradients, no emojis, clean SVG icons only.

---

## 7. Verification Evidence & Test Cases

1. **Agency Bench Candidate (Standard IT Vendor):**
   - Action: Open Create Offer drawer.
   - Verify Step 1: Allows selecting either "Full-time" or "Contract".
   - Verify Step 2: Allows switching between "Agency Offer terms" and "Fulltime Offer Letter".
   - Verify Step 5: Candidate email is displayed as `Confidential (Agency Bench Talent)`. "To" recipient is locked to Agency RM email (`bench_assign`). Candidate personal email is NOT leaked.
2. **Education / Training Institution Bench Candidate:**
   - Action: Open Create Offer drawer for a student from an educational institute (`ind_id` in 35, 56, 109, 111).
   - Verify Step 2: Defaults to "Fulltime Offer Letter".
   - Verify Step 5: Candidate's real email is displayed in "To" field. Institute Placement Officer is automatically added to "CC".
3. **"My Organization" Job Requisition:**
   - Action: Issue offer for an agency candidate on a "My Organization" job.
   - Verify: Offer creation proceeds smoothly with complete tenancy security and audit trail.
