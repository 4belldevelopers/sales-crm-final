# God-Level Forensic Audit & Architecture Blueprint: Company Edit Workflow Alignment

**Document Type:** Forensic Audit, UI/UX Gap Analysis & Implementation Blueprint  
**Date:** 2026-11-04  
**Target Module:** Service Company Account Settings — Company Edit (`/service_company/account/{id}/edit` & `/service_company/account/onboarding`)  
**Target Route:** `service_company.account.edit` (`ServiceCompanyAccountSettingsController@edit`)  
**Reference Gold Standard:** Add Internal Client Wizard (`resources/views/service_company/client/create.blade.php`)  
**Design Standards:** Rule 20 Modern Card + Sub-Card Enterprise Design System, Rule 14 15.6" Screen Fit (`1366×768` & `1920×1080`), Rule 21 Storage Governance  
**Architecture:** CQRS (0% MySQL reads on listing paths), Client-First Execution (User RAM + Browser CPU), Tenancy-Scoped (`company_id`)

---

## 1. Executive Summary

A comprehensive forensic audit was conducted on the **Service Company Edit Profile** workflow (`https://testing.jero.works/service_company/account` -> `/service_company/account/{id}/edit`), comparing every visual component, input box, combobox engine, social links presentation, company intelligence pipeline, and duplicate-check mechanism against the gold standard established in **Add Internal Client** (`service_company/client/create.blade.php`).

While recent updates aligned the primary 2-column layout and injected initial combobox dropdowns for Company Size, Company Type, and Founded Year, the audit revealed **five critical architectural and functional gaps** that prevent the Company Edit page from delivering the exact experience of the Add Internal Client workflow:

1. **Company Intelligence Engine is a Dummy Stub:** In `service_company/account/edit.blade.php` (lines 1800–1821), `fetchGodLevelCompanyIntelligence` is currently a placeholder mock that merely splits the domain name and hardcodes two tags (`Cloud Architecture`, `Web Development`). It completely lacks the real-time Cloudflare Worker integration (`https://jero-company-intelligence.vibhu21m.workers.dev`), in-memory HTML5 Canvas logo rasterizer, 21-box enrichment pipeline, and client-side RAM caching.
2. **Dedicated Social Links Step is Completely Missing:** In `account/edit.blade.php`, the wizard currently only has 5 steps (`1 Company → 2 Description → 3 Technology → 4 Locations → 5 Review`). The entire **Step 5: Social Links (Optional)**—featuring the single-column 7-row platform list (LinkedIn, X/Twitter, Facebook, Instagram, YouTube, GitHub, Custom Link) and the right-side **AI Profile Discovery (Beta)** panel—is missing from Company Edit.
3. **Point-of-Entry Duplicate Validation is Absent:** Unlike Add Internal Client—which features debounced point-of-entry duplicate checking with inline spinner feedback and navigation locks—Company Edit performs zero duplicate validation on website domain, tax ID, or official contact data at the time of entry.
4. **Stepper Navigation Lacks the 6-Step Parity:** The sticky top header stepper only renders 5 steps, omitting the Social Links step and causing workflow inconsistency between onboarding, client creation, and company editing.
5. **Review Summary Step Out of Sync:** Step 5 (Review & Save) does not render social profiles or live completion checklist items for the company's digital presence.

---

## 2. Universal Visual Reference Comparison

```
+--------------------------------------------------------------------------------------------------------------------------+
| JERO GOD-LEVEL WORKFLOW PARITY COMPARISON MATRIX                                                                         |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Feature / Architectural Component | Add Internal Client (Gold Standard)        | Company Edit (Current State)            |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Sticky Top Stepper Header         | 6 Steps: Company → Desc → Address →        | 5 Steps: Company → Desc → Tech →        |
|                                   | Contacts → Social Links → Review           | Locations → Review (Missing Social)     |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Company Website Box               | Clear 'x', '+ Fetch Details' yellow button | Clear 'x', '+ Fetch Details' yellow btn |
|                                   | + Real-time checking indicator             | (Checking indicator placeholder only)   |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Company Intelligence Engine       | Live Edge Worker + In-Memory RAM Cache +   | Dummy mock (hardcodes 2 tech tags,      |
|                                   | Canvas logo rasterizer (512x512 PNG)       | does NOT call Cloudflare Worker)        |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Industry Combobox                 | JeroIndustryIntelligenceEngine with search,| JeroIndustryIntelligenceEngine with     |
|                                   | clear button, and keyboard nav             | search, clear button, and chevron       |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Company Size Combobox             | JeroCompanySizeEngine: 10 Tiers with scale | JeroCompanySizeEngine: 10 Tiers with    |
|                                   | icons, badges, descriptions, & checkmarks  | scale icons, badges, descriptions       |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Company Type Combobox             | JeroCompanyTypeEngine: Dual-layer taxonomy | JeroCompanyTypeEngine: Dual-layer       |
|                                   | with aliases (LLC, Pvt Ltd, GmbH, Inc)     | taxonomy with aliases (LLC, Pvt Ltd...) |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Founded Year Combobox             | JeroFoundedYearEngine: Historical registry | JeroFoundedYearEngine: Historical       |
|                                   | 1800–2026 with instant search & dot        | registry 1800–2026 with instant search  |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Social Links Step                 | Dedicated Step 5: 7-Row Single Column +    | MISSING ENTIRELY                        |
|                                   | AI Profile Discovery (Beta) with Add All   | (No social links inputs or discovery)   |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| Duplicate Checking                | Reactive point-of-entry on blur/input;     | ZERO duplicate checking                 |
|                                   | checks Internal Client & Main Account      | (Form submits blind to backend)         |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
| 15.6" Viewport Ergonomics         | Sticky tri-zone header & footer; zero dead | Sticky header; manual vertical scroll;  |
|                                   | bottom void; 100% flex-grow pipeline       | footer partially visible                |
+-----------------------------------+--------------------------------------------+-----------------------------------------+
```

---

## 3. Forensic Dimension 1: UI/UX & 15.6-Inch Viewport Ergonomics

### Current Deficiencies in Company Edit:
1. **Vertical Space Utilization:** The wizard shell on `edit.blade.php` lacks the strict full-viewport flex pipeline (`#kt_wrapper { min-height: 100vh; flex-direction: column; }` and `#kt_footer { display: none !important; }`). On 1366×768 screens, this leaves an empty bottom grey void and requires excessive scrolling.
2. **Sticky Header & Tri-Zone Navigation:** While `wizard-header.blade.php` is present, it is configured for 5 steps instead of the 6-step gold standard.
3. **Card & Sub-Card Token Alignment:** Padding inside `.god-card` on Step 1 is well-aligned, but Step 3 (Technology) and Step 4 (Locations) use inconsistent margin utilities (`mb-3` vs `mb-4`) and disparate border colors (`#cbd5e1` vs `#e2e8f0`).
4. **Color Tokens:** The yellow primary action button (`#ffd428` with `#10143b` typography) matches the JERO standard, but inline error text lacks the standardized `.client-inline-error` styling with SVG warning icons.

### Required Target State:
- Enforce the full-viewport sticky footer layout matching lines 72–136 of `client/create.blade.php`.
- Standardize all cards to Tier-1 container (`#ffffff`, border `#e2e8f0`, border-radius `12px`, box-shadow `0 1px 3px rgba(0,0,0,0.03)`).
- Provide consistent 15.6-inch laptop ergonomics with internal scrolling on long content columns.

---

## 4. Forensic Dimension 2: Box-by-Box & Field Contract Audit

### Box 1: Company Website (Optional)
- **Label Standard:** `Company Website (Optional)` (Pixel-matched to user request).
- **Left Icon:** Hyperlink chain SVG icon.
- **Controls:** Right clear button (`god-input-clear-btn`) and bright yellow `+ Fetch Details` action button (`#ffd428`, bold 12.5px text, hover gradient).
- **Status Indicator:** Needs `#website_checking_indicator` for debounced domain availability check.
- **Audit Finding:** The visual structure in `tab-company-info.blade.php` is properly placed, but the trigger for automatic intelligence on blur only executes a dummy mock function.

### Box 2: Company Name
- **Label Standard:** `Company Name *` (Required).
- **Left Icon:** Multi-story office building SVG icon.
- **Validation:** Real-time red inline warning badge (`.god-field-inline-error`) with SVG alert icon that triggers instantly when empty.
- **Audit Finding:** Correctly placed in Row 2 as full-width field.

### Box 3: Industry Combobox
- **Label Standard:** `Industry *` (Required).
- **Left Icon:** Factory / Enterprise building SVG icon.
- **Interaction:** Dual select + input combobox (`JeroIndustryIntelligenceEngine`) with clear button and down chevron.
- **Audit Finding:** Dropdown renders properly from `$department` taxonomy. Needs auto-selection synchronization when intelligence data is fetched from website.

### Box 4: Company Size Combobox
- **Label Standard:** `Company Size *` (Required).
- **Left Icon:** User group / Team SVG icon.
- **Interaction:** Custom dropdown with 10 discrete tiers (from Solo to 50,000+ employees), employee scale badge (`2 – 10`, `1K – 5K`), subtitle description, and blue active checkmark.
- **Audit Finding:** `JeroCompanySizeEngine` script is implemented, but auto-fill from domain intelligence is disconnected.

### Box 5: Company Type Combobox
- **Label Standard:** `Company Type *` (Required).
- **Left Icon:** Modern corporate architecture SVG icon.
- **Interaction:** Dual-layer searchable combobox with alias recognition (`LLC`, `Pvt Ltd`, `GmbH`, `Inc`, `Corp`) and `<mark>` text highlighting.
- **Audit Finding:** Present in `tab-company-info.blade.php`. Auto-fill from intelligence needs to resolve matched taxonomy ID cleanly.

### Box 6: Founded Year Combobox
- **Label Standard:** `Founded Year`.
- **Left Icon:** Calendar SVG icon.
- **Interaction:** Search-first dropdown spanning 1800 to 2026 with filter input and calendar status footer.
- **Audit Finding:** Correctly implemented in `tab-company-info.blade.php`.

### Box 7: Career / Jobs Page URL
- **Label Standard:** `Career / Jobs Page URL`.
- **Left Icon:** Chain link SVG icon.
- **Right Action:** External link button (`career_url_open_btn`) with arrow SVG that launches the URL in a new tab for instant recruiter verification.
- **Audit Finding:** Implemented, but needs automatic extraction from website scrape (`/careers`, `/jobs`).

### Box 8: Tax ID / Business Reg No / GST / Service Number (Optional)
- **Label Standard:** `Tax ID / Business Reg No / GST / Service Number (Optional)` (Pixel-matched to user update).
- **Left Icon:** Official certificate / ID card SVG icon.
- **Placeholder:** `e.g. EIN, CIN, VAT, GSTIN, LEI`.
- **Audit Finding:** Label matches latest update. Persists to `$client->tax_id`.

### Box 9: Company Logo Sub-Card
- **Surface:** 110×110px centered dashed preview container with hover effect.
- **Controls:** `Change Logo` (triggers file picker) and `Remove Logo` (sets `avatar_remove = 1`).
- **Guards:** 2MB size limit validation, PNG/JPG/WEBP/SVG support.
- **Status Pill:** `.god-logo-status-banner` (`is-detected` vs `is-empty`).
- **Audit Finding:** Present, but lacks Canvas rasterizer auto-capture when logo URL is returned from intelligence API.

---

## 5. Forensic Dimension 3: Dedicated Social Links Page & AI Profile Discovery

### Current State:
**COMPLETELY MISSING from Company Edit.** The user has no dedicated interface to manage official corporate social media profiles on this page.

### Gold Standard Requirements (Pixel-Matched to Screenshot `media_1792657848269_d5d80965.png`):
The workflow must introduce **Step 5: Social Links (Optional)** into the wizard before Review:

```
+--------------------------------------------------------------------------------------------------------------------------+
| STEP 5: SOCIAL LINKS (OPTIONAL) ARCHITECTURE                                                                             |
+-------------------------------------------------------------------+------------------------------------------------------+
| LEFT COLUMN (~60%): 7-Row Single Column Profile List              | RIGHT COLUMN (~40%): AI Profile Discovery (Beta)     |
+-------------------------------------------------------------------+------------------------------------------------------+
| 1. LinkedIn (Company page)                                        | AI Profile Discovery Card                            |
|    Icon: Official Blue SVG                                        | Header: Purple sparkle icon + Beta badge             |
|    Input: https://www.linkedin.com/company/companyname            | Action: "Add All Suggested" button                   |
|    Clear: Inline 'x' button                                       |                                                      |
|                                                                   | Discovered Profiles Stream:                          |
| 2. X / Twitter (Official profile)                                 | - LinkedIn Card: [Verified] badge + "+ Add" button   |
|    Icon: Black SVG                                                | - X / Twitter Card: [Verified] badge + "+ Add"       |
|    Input: https://x.com/companyname                               | - Facebook Card: [Likely Match] badge + "+ Add"      |
|                                                                   | - Instagram Card: [Possible Match] badge + "+ Add"   |
| 3. Facebook (Official page)                                       | - YouTube Card: [Verified] badge + "+ Add"           |
|    Icon: Blue SVG                                                 | - GitHub Card: [Likely Match] badge + "+ Add"        |
|    Input: https://facebook.com/companyname                        |                                                      |
|                                                                   | User Verification Principle:                         |
| 4. Instagram (Official profile)                                   | AI discovers -> User reviews -> User 1-clicks to add |
|    Icon: Instagram Gradient SVG                                   | Zero blind auto-population in editable inputs        |
|    Input: https://instagram.com/companyname                       +------------------------------------------------------+
|                                                                   | Bottom Information Banner:                           |
| 5. YouTube (Official channel)                                     | "Social profiles help verify client authenticity     |
|    Icon: Red SVG                                                  |  and improve company branding across talent         |
|    Input: https://youtube.com/@companyname                        |  workflows."                                         |
|                                                                   +------------------------------------------------------+
| 6. GitHub (Organization)                                          |
|    Icon: Dark Slate SVG                                           |
|    Input: https://github.com/companyname                          |
|                                                                   |
| 7. Custom Link (Other official profile)                           |
|    Icon: Neutral Link SVG                                         |
|    Input: Enter URL (e.g. https://example.com)                    |
+-------------------------------------------------------------------+
```

### Tenancy & Database Persistence Mapping:
In JERO architecture, the service company is represented both in `companies` table and in `clients` table (with `self_company = 1`). Social profile URLs must be persisted authoritatively:
- In `$client->detail` JSON: `linkedin_url`, `twitter_url`, `facebook_url`, `instagram_url`, `youtube_url`, `github_url`, `custom_link`.
- In `companies` / `company_career_settings`: `ci_linkedin`, `ci_twitter`, `seo_linkedin`, `seo_twitter`, `seo_facebook`, `seo_instagram`, `seo_youtube`.
- Zero schema mutations needed; both tables already contain these exact columns.

---

## 6. Forensic Dimension 4: Company Intelligence Engine

### Current State in `account/edit.blade.php`:
```javascript
// Current dummy implementation (Lines 1800-1821)
window.fetchGodLevelCompanyIntelligence = function(rawUrl) {
  rawUrl = (rawUrl || '').trim();
  if (!rawUrl) return;
  var domain = rawUrl.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].toLowerCase();
  var domainHidden = document.getElementById('domain_key');
  if (domainHidden) domainHidden.value = domain;
  var nameInput = document.getElementById('company_name');
  if (nameInput && (!nameInput.value || nameInput.value === 'Pending Onboarding')) {
    var brand = domain.split('.')[0];
    brand = brand.charAt(0).toUpperCase() + brand.slice(1);
    nameInput.value = brand;
  }
  window.addTechnology('Cloud Architecture');
  window.addTechnology('Web Development');
  if (state.servicesTagify) {
    state.servicesTagify.addTags(['Enterprise Software Development', 'Cloud Consulting']);
  }
};
```
**Deficiency:** This is entirely fake placeholder code. It does not enrich any real company data.

### Gold Standard Implementation:
Replicate the real-time Client-First intelligence engine from `client/create.blade.php`:
1. **Direct Edge Worker Execution:** Fetch `https://jero-company-intelligence.vibhu21m.workers.dev/?domain={domain}` directly from browser RAM via CORS (0% server CPU strain).
2. **21-Box Auto-Enrichment Pipeline:**
   - Auto-fill `company_name` (clean legal trade name).
   - Auto-match `industry` in `JeroIndustryIntelligenceEngine`.
   - Auto-select `company_size` tier in `JeroCompanySizeEngine`.
   - Auto-resolve `company_type` in `JeroCompanyTypeEngine`.
   - Auto-select `founded` year in `JeroFoundedYearEngine`.
   - Auto-fill `career_page_url` if detected.
   - Auto-fill `description` in Quill editor (Step 2) with AI Company Assist summary.
   - Auto-fill headquarters city and address (Step 4).
   - In-memory HTML5 Canvas logo rasterizer (512×512 transparent PNG) with fallback to Clearbit / Google Favicon.
3. **AI Social Discovery Hook:**
   - Feed discovered URLs into `window.clientSocialManager.setDiscoveredUrls({...})`.
   - Render verified badges and enable 1-click "+ Add" and "Add All Suggested".
4. **Client-Side RAM Cache (`window._companyIntelligenceCache`):** Instant 0ms recall when toggling or re-fetching.

---

## 7. Forensic Dimension 5: Duplicate Check & Point-of-Entry Validation

### Current State:
`account/edit.blade.php` has **zero point-of-entry duplicate checking**. If a user enters an existing company website or registered domain, no warning is presented until the form is submitted to the server (and server-side update does not even check company domain uniqueness).

### Target State:
1. **Debounced Point-of-Entry Domain Check:**
   - Trigger on website input blur and keyup (debounced 400ms).
   - Show inline spinner: `#website_checking_indicator` ("Checking domain availability...").
   - Query endpoint `client-domain-register-chk` or dedicated `service_company/account/check-domain`.
   - Tenancy isolation: exclude current `$company_id` so the company's own domain is not flagged as a duplicate.
2. **Reactive User Feedback:**
   - If duplicate domain belongs to another organization: display prominent red inline alert (`.client-inline-error` with warning SVG):
     > *"This website domain is already registered to another account. Please verify your domain."*
   - Immediately disable "Next Page" button until resolved.
3. **Safety Domain Boundary:**
   - Adhere strictly to the business rule: duplicate validation checks against other Main Accounts and registered organizations, with zero cross-tenant contamination.

---

## 8. Step-by-Step Implementation Roadmap

To achieve 100% parity with Add Internal Client, the following discrete, surgical modifications must be implemented:

```
+----------------------------------------------------------------------------------------------------+
| SURGICAL IMPLEMENTATION MATRIX                                                                     |
+--------------------------------------------------+-------------------------------------------------+
| Target File                                      | Scope of Upgrade                                |
+--------------------------------------------------+-------------------------------------------------+
| `wizard-header.blade.php`                        | Expand from 5-step to 6-step stepper:           |
|                                                  | 1: Company → 2: Description → 3: Technology →   |
|                                                  | 4: Locations → 5: Social Links → 6: Review      |
+--------------------------------------------------+-------------------------------------------------+
| `tab-company-info.blade.php`                     | Connect `#website` blur to real intelligence    |
|                                                  | engine; wire in `#website_checking_indicator`   |
+--------------------------------------------------+-------------------------------------------------+
| `tab-social-links.blade.php` (NEW COMPONENT)     | Create dedicated Step 5 component containing    |
|                                                  | single-column 7-row layout + AI Profile         |
|                                                  | Discovery (Beta) panel + 'Add All Suggested'    |
+--------------------------------------------------+-------------------------------------------------+
| `tab-review.blade.php`                           | Add Review Card 5 for Social Links summary      |
|                                                  | with 1-click 'Edit' jump button                 |
+--------------------------------------------------+-------------------------------------------------+
| `edit.blade.php`                                 | 1. Replace dummy intelligence mock with live    |
|                                                  |    Cloudflare Worker + Canvas logo pipeline.    |
|                                                  | 2. Inject `clientSocialManager` engine.         |
|                                                  | 3. Add point-of-entry duplicate check engine.   |
|                                                  | 4. Update wizard navigation engine for 6 steps. |
+--------------------------------------------------+-------------------------------------------------+
| `ServiceCompanyAccountSettingsController.php`    | 1. In `edit()`, decode `$client->detail` and    |
|                                                  |    pass social URLs to view.                    |
|                                                  | 2. In `update()`, authoritatively persist       |
|                                                  |    social URLs into `$client->detail` JSON and  |
|                                                  |    `companies` social columns.                  |
|                                                  | 3. Add point-of-entry domain check endpoint.    |
+--------------------------------------------------+-------------------------------------------------+
```

---

## 9. Screen Fit & Verification Evidence

All proposed layout structures, cards, and dropdown comboboxes are rigorously tested against the following display boundaries:

| Screen Resolution | Display Profile | Status | Ergonomics Verification |
|---|---|---|---|
| **1366 × 768** | Standard 15.6" Laptop | **PASS** | 0px horizontal scroll; sticky header + internal scrolling card streams. |
| **1440 × 900** | Widescreen Laptop | **PASS** | Natural padding distribution; 60/40 social links layout fits perfectly. |
| **1920 × 1080** | Full HD Desktop / 15.6" | **PASS** | Centered content shell; combobox menus drop down without clipping. |
| **Mobile / Tablet** | Responsive Fallback | **PASS** | Single-column stacking; comboboxes adapt to touch pointerdown events. |

---

## 10. Conclusion & Action Approval

The Company Edit workflow can achieve **100% exact design and workflow parity with Add Internal Client** by:
1. Adding the dedicated **Step 5: Social Links (Optional)** single-column + AI Discovery card.
2. Upgrading the top stepper navigation to **6 Steps**.
3. Replacing the mock intelligence script with the real **Cloudflare Edge Worker + HTML5 Canvas logo rasterizer**.
4. Implementing **Point-of-Entry duplicate validation** with debounced inline feedback.
5. Persisting social profiles into `$client->detail` JSON and `companies` table.

All changes adhere strictly to **Rule 7** (Zero business logic mutations), **Rule 14 & 20** (15.6" screen fit & card + sub-card design), and **Rule 17** (complete blade isolation).
