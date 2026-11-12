# UI/UX Audit & Enterprise Implementation Specification: Registration Company Type (v5.0.0 — 5 Enterprise Profiles + Dynamic Auto-Industry)

> **Document Version:** 5.0.0 (5 Enterprise Profiles, Service Company Accounts for Training & Education, Dynamic Taxonomy Resolution)  
> **Date:** November 12, 2026  
> **Module / Scope:** `auth_and_onboarding` / Registration (`resources/views/auth/login.blade.php`, `SaasFrontController.php`, `IndustryTaxonomyService.php`)  
> **Selected Pattern:** **Pattern A: Compact Enterprise Card Combobox** (Zero Scrollbar, Viewport Drop-Up Flip, 15.6" & Smaller Screen Fit)  
> **Compliance Standard:** Rule 20 (Modern Card + Sub-Card Design), Rule 21 (Audit Documents Storage Standard), 15.6" Screen Fit (`1366×768` & `1920×1080`), Strict CQRS (0% MySQL Reads, L1/L2 Cache Lookup).  

---

## 1. Executive Summary & v5.0.0 Upgrade

Based on stakeholder requirements, the Company Type combobox on the registration page (`resources/views/auth/login.blade.php`) has been expanded from 3 to **5 enterprise profiles** powered dynamically by `IndustryTaxonomyService::getSignupProfileCatalog()`:

1. **Product Company *(Direct Employer)*** (`company_type = 3`) — `[ATS Only]`
2. **IT Services & Consulting** (`company_type = 2` — Service Company) — `[ATS]` `[CRM]`
3. **Recruitment & Staffing** (`company_type = 1` — Staffing Company) — `[ATS]` `[CRM]`
4. **Training & Upskilling Academy** (`company_type = 2` — **Service Company Account**) — `[ATS]` `[CRM]`
   - Offers trained bench candidates directly to hiring companies.
   - Dynamically auto-selects **`Professional Training & Coaching`** (`ind_id: 111`) from cached `td_industries` manifest.
5. **Educational Institutions & Campuses** (`company_type = 2` — **Service Company Account**) — `[ATS]` `[CRM]`
   - Offers graduating students directly to corporate employers via bench listings.
   - Dynamically auto-selects **`Higher Education`** (`ind_id: 56`, with dynamic fallbacks) from cached `td_industries` manifest.

All redundant capability phrases (e.g., *"Pure ATS, no CRM"*, *"(Full ATS + CRM)"*) were stripped from option descriptions so every card communicates pure business value without repeating the pill badges.

---

## 2. Architecture & Dynamic Industry Flow

```
  User selects profile on Registration (/register or /login)
         │
         ▼
  IndustryTaxonomyService::getSignupProfileCatalog()
  (Reads L1 Redis/Dragonfly -> L2 JSON manifest; 0% MySQL reads)
         │
         ├─► 'training'    ──► company_type = 2 (Service Account) + dynamic industry match ('Professional Training & Coaching')
         ├─► 'education'   ──► company_type = 2 (Service Account) + dynamic industry match ('Higher Education')
         ├─► 'recruitment' ──► company_type = 1 (Staffing Account) + dynamic industry match ('Staffing & Recruiting')
         ├─► 'it_services' ──► company_type = 2 (Service Account) + manual industry selection in onboarding
         └─► 'product'     ──► company_type = 3 (Product Account) + manual industry selection in onboarding
         │
         ▼
  POST /company-register (SaasFrontController::companyRegister)
         ├─► Persists companies.company_type & resolved companies.industries
         ├─► Persists matching self_company=1 clients.industry
         ├─► Grants full ATS + CRM + Bench permissions for company_type = 2
         └─► Onboarding Step 2 (JeroIndustryIntelligenceEngine) auto-hydrates the selected industry immediately
```

---

## 3. UI/UX & Copy Specification (Zero Repetitive Text)

| Order | Profile Title | Account Type (`company_type`) | Badges | Concise Description | Auto-Selected Industry |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | **Product Company** *(Direct Employer)* | `3` | `ATS Only` | In-house hiring · Requisitions, interviews & offers | Manual (Onboarding) |
| **2** | **IT Services & Consulting** | `2` (Service) | `ATS` `CRM` | Bench talent deployment, client projects & billing | Manual (Onboarding) |
| **3** | **Recruitment & Staffing** | `1` (Staffing) | `ATS` `CRM` | Agency recruiting · Candidate sourcing, job orders & placements | *Staffing & Recruiting* |
| **4** | **Training & Upskilling Academy** | `2` (Service) | `ATS` `CRM` | Offer trained bench candidates directly to hiring companies | *Professional Training & Coaching* |
| **5** | **Educational Institutions & Campuses** | `2` (Service) | `ATS` `CRM` | Offer students directly to companies via bench listings | *Higher Education* |

---

## 4. Verification Checklist

1. **Syntax & Blade Lints (`php -l`):** Verified clean across `IndustryTaxonomyService.php`, `SaasFrontController.php`, and `login.blade.php`.
2. **Dynamic Manifest Resolution (`tinker`):** Verified `getSignupProfileCatalog()` resolves `131`, `111`, and `56` dynamically from cached taxonomy manifest without runtime SQL queries.
3. **15.6" & Smaller Viewport Fit:** Compact 5-item menu (~235px–250px height) with zero vertical scrollbar and smart `.jw-dropup` flip keeps all profiles 100% visible without clipping.
4. **Code Freeze & CQRS Integrity:** Zero modifications to `product_company/` or `staffing_company/` modules (Rule 18). Zero runtime DB reads introduced (Rule 1).
