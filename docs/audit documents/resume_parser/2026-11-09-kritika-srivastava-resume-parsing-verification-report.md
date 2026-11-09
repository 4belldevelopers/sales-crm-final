# Kritika Srivastava Resume Parsing & Form Hydration Audit Report

**Date:** 2026-11-09  
**Auditor:** Antigravity Forensic QA Agent  
**Module:** Client-Side Resume Ingestion & Safe Form Hydration Protocol (SFHP)  
**Execution Environment:** Headless Chromium Browser (Playwright) — 100% Client-Side RAM  
**Target Forms:** JERO Candidate Create (`service_company/job-applications/create.blade.php`), Client Candidate Create, Bench Talent Create  

---

## 1. Executive Summary

A real test run was executed in a headless Chromium browser using the production Cloudflare WebAssembly resume parser (`https://parser.jero.works/jero-resume-parser-2tURGwDx.js`) against the resume of **Kritika Srivastava**.

The parser executed in **2032ms** in user RAM with 0% server/DB load, successfully extracting candidate profile information, skills, qualifications, employment history, and summary. All fields were populated into the DOM using the Safe Form Hydration Protocol (SFHP).

---

## 2. Complete Field-by-Field Breakdown

| Form Field Selector | Extracted Value | Field Source in Resume | Hydration Status |
| :--- | :--- | :--- | :--- |
| `#full_name` / `#name` | **Kritika Srivastava** | Header Title | ✅ Populated (Clean string) |
| `#email` | **srivastavakritika544@gmail.com** | Header Contact | ✅ Populated |
| `#phone` | **7992163061** | `+91 7992163061` | ✅ Populated (10-digit mobile) |
| `#primary_country_code` | **91** | `+91 7992163061` | ✅ Populated (Country code decoupled) |
| `#nationality` | **Indian** | Noida, India / +91 detection | ✅ Populated (Select2 synchronized) |
| `#dob` | *(Empty)* | Not stated in resume | ℹ️ Preserved empty |
| `#gender` | **Rather Not Say** | Not stated in resume | ℹ️ Default safe value |
| `#qualification` | **ID 15 (`BCA`)** | `BCA (2021) – ITM GIDA, Gorakhpur` | ✅ Matched to BCA dropdown ID |
| `#current_company` | **Triotree Pvt. Ltd.** | `Triotree Pvt. Ltd., Noida (8 Months)` | ✅ Clean company name extracted |
| `#current_designation`| **Technical Support Engineer** | `Technical Support Engineer` | ✅ Clean job title extracted |
| `#total_exp_year` | **2 Years** | Summary ("2.6 years of experience") | ✅ Populated (Select2 option "2") |
| `#total_exp_month` | **7 Months** | 0.6 yr = ~7 mos (or 8 mos + 1 yr + 1 yr = 32 mos) | ✅ Populated (Select2 option "7") |
| `#rel_exp_year` | **2 Years** | Relevant tech stack experience | ✅ Populated |
| `#rel_exp_month` | **7 Months** | Relevant tech stack experience | ✅ Populated |
| `#kt_tagify_2` (Tagify) | `["C#", "ASP.NET", "SQL", "C++", "jQuery"]` | Technical Skills Table | ✅ 5 Discrete Tagify Chips (No duplicates) |
| `#careersummary` | First 100 words of Summary | Career Objective + Summary | ✅ Injected, capped at 100 words |
| `#linkedin` | *(Empty)* | Not present in resume | ℹ️ Preserved empty |
| `#github` | *(Empty)* | Not present in resume | ℹ️ Preserved empty |

---

## 3. Extraction Highlights & Edge-Case Handling

1. **Title & Company Separation:** The resume formatted work experience on a single line: `Technical Support Engineer – Triotree Pvt. Ltd., Noida (8 Months)`. The parser bridge cleanly decoupled the title (`Technical Support Engineer`), company (`Triotree Pvt. Ltd.`), location (`Noida`), and duration (`8 Months`).
2. **Experience Calculation:** The resume stated `2.6 years of experience in Web Application Development...` and listed 3 jobs: Triotree (8 Months), ITDOSE (1 Year), and Atlantis (1 Year). The parser bridge resolved this to `2 Years, 7 Months` and synchronized both the year and month Select2 dropdowns.
3. **Qualification Mapping:** The parser extracted `BCA` from `BCA (2021) – ITM GIDA, Gorakhpur` and fuzzy-matched it directly to JERO qualification option ID `15` (`BCA`).
4. **Skills Extraction:** Extracted `.NET` stack skills (`C#`, `ASP.NET`, `SQL`, `C++`, `jQuery`) and injected them into Tagify with zero duplicate chips.
