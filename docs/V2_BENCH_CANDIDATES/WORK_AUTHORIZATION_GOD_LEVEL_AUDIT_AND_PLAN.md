# Work Authorization in Add Talent — GOD-Level Enterprise Audit & Implementation Plan

> **Scope:** Service Company Bench Candidate Creation (`resources/views/service_company/bench-candidates/create.blade.php`), candidate persistence, Elasticsearch indexing, and recruiter marketplace discovery.  
> **Audited Component:** "Additional Details" Card -> "Work Authorization (Optional)" Dropdown (`name="visastatu"`).  
> **Reference Screenshot:** `media_1790844284105.png` (Provided in Audit Request).  
> **Target Concurrency:** 10,000 concurrent users | Dedicated 32 GB RAM per tier | CQRS: MySQL for Writes, Elasticsearch for Reads | Zero Business Logic Mutations.

---

## 1. Executive Verdict: Is This Standard Enterprise Level?

### ❌ Direct Verdict: Absolutely NOT Enterprise Level (Score: 2.5 / 10)

The current Work Authorization card in the Add Talent page is **not standard enterprise grade**. It represents an MVP-level implementation reminiscent of legacy 2010 recruitment portals.

```
┌────────────────────────────────────────────────────────────────────────┐
│ Current JERO Work Authorization Implementation: 2.5 / 10               │
├────────────────────────────────────────────────────────────────────────┤
│ • Hardcoded 5 options (H1B, L1, F1, Green Card, Citizen)               │
│ • Domain errors: "F1" is a student visa (not work auth); "L1" is tied  │
│ • Zero country awareness: A candidate in India or UK sees US visas     │
│ • Zero recruiter commercial context: No C2C/W2 eligibility, no expiry  │
│ • Copywriting defect: Card banner references "Adding relevant links"   │
│ • Primitive unsearchable HTML <select> without Select2 grouping        │
└────────────────────────────────────────────────────────────────────────┘
```

In modern high-volume ATS platforms (Ceipal, JobDiva, Bullhorn, Workday, Greenhouse) and elite talent marketplaces (Dice, Turing, Toptal, Braintrust), **Work Authorization is the #1 or #2 primary filtration criteria for B2B contract and bench staffing**. Placing a candidate legally requires verifying right-to-work, tax engagement suitability (C2C vs. W-2), visa expiration countdowns, and employer transferability. 

---

## 2. Competitive Marketplace & Enterprise ATS Benchmark Matrix

| Capability / Dimension | JERO (Current) | Ceipal ATS (Staffing Benchmark) | Dice / TechFetch (Tech Marketplace) | Workday / Greenhouse (Enterprise ATS) | Turing / Toptal (Global Marketplace) | JERO GOD-Level Target |
|---|---|---|---|---|---|---|
| **Taxonomy Depth** | 5 flat options | 25+ granular visas & sub-categories | 18+ US visas + EAD classifications | Country-specific legal categories | Country of tax residency + remote right | Curated multi-country taxonomy + sub-types |
| **Multi-Country Support** | ❌ US-only codes | ⚠️ Configurable | ❌ US focused | ✅ 100+ countries with statutory logic | ✅ Global remote contractor compliance | ✅ Dynamic country-adaptive taxonomies |
| **Sponsorship Tracking** | ❌ None | ✅ Yes (Immediate / Future / None) | ✅ Yes (Requires Sponsorship: Y/N) | ✅ Mandatory statutory question | ⚠️ Relocation sponsorship flag | ✅ 3-way toggle (None / Immediate / Future) |
| **Engagement Model (C2C/W2)**| ❌ None | ✅ Yes (W2, C2C, 1099, 1099-Corp) | ✅ Yes (C2C, W2, Contract, Full-time) | ⚠️ Employee vs Contractor | ✅ Contractor vs EOR (Deel) | ✅ Multi-select (C2C, W2, 1099, Direct) |
| **Visa Expiry Tracking** | ❌ None | ✅ Critical (Expiry date + alert badge) | ✅ Month/Year of validity | ✅ I-9 re-verification date | ❌ Not tracked | ✅ Expiry Date with 60/90-day alert indicators |
| **EAD / Sub-category Clarity** | ❌ Missing | ✅ Explicit (GC-EAD, H4-EAD, L2-EAD, OPT) | ✅ Explicit (GC-EAD, OPT-EAD, STEM-OPT) | ✅ Form I-9 document list A/B/C | ⚠️ General permit | ✅ Explicit EAD breakdown |
| **Security Clearance** | ❌ None | ✅ Yes (Public Trust, Secret, TS) | ✅ Yes (Top Tier filter for Gov/Defense) | ✅ Clearance level field | ❌ None | ✅ Clearance level select (Public Trust/Secret/TS) |
| **Document Verification** | ❌ None | ✅ I-9, I-797, Passport attachment | ⚠️ Self-certified | ✅ Full Form I-9 & E-Verify workflow | ✅ ID & Background check | 🔄 Optional Right-to-Work document proof |
| **Search & ES Faceting** | ⚠️ Flat text | ✅ High-performance multi-select facet | ✅ Top-level filter panel | ✅ Enterprise talent pool filtering | ✅ Multi-geo talent filtering | ✅ Rich Elasticsearch facet with grouped queries |
| **UI Presentation** | Generic select | Specialized compliance tab | Filterable profile badges | Guided compliance stepper | Clean profile verification badge | Metronic modern card with grouped Select2 & tags |

---

## 3. Comprehensive Audit of Current Implementation

### 3.1 UX & Visual Presentation Flaws (Screenshot Review)
1. **Generic Card Hierarchy:**
   - The card is titled `Additional Details` with subtitle *"Provide any other relevant information about your professional background."*
   - In staffing, work authorization is a compliance requirement, not an incidental "additional detail".
2. **Defective Copywriting & Information Bleed:**
   - The blue alert strip at the bottom reads:  
     `"All fields in this section are optional. Adding relevant links and details can help clients better understand your expertise and background."`
   - This text is a literal copy-paste leftover from the *Professional Links* section directly above it. There are no links in this card.
3. **Weak Control Aesthetics:**
   - A single wide unsearchable dropdown `<select name="visastatu">`.
   - On desktop (1920×1080) and 15.6" screens (1366×768), a single wide dropdown stretched across a full card looks sparse, unpolished, and empty.
4. **Non-functional Tooltip:**
   - The info icon `<i class="fa-solid fa-circle-info addl-label-info"></i>` contains no initialized tooltip or popover explaining what work authorization implies.

### 3.2 Staffing Domain & Visa Taxonomy Flaws
1. **The "F1" Fallacy:**
   - `F-1` is an academic student visa issued by universities. An international student on F-1 status **cannot work in the United States** unless authorized under:
     - **OPT (Optional Practical Training)**
     - **STEM-OPT (24-Month Science, Tech, Engineering, Math Extension)**
     - **CPT (Curricular Practical Training)**
   - Displaying `F1` on a B2B bench marketplace signals lack of recruitment expertise to enterprise hiring managers.
2. **The "L1" Misplacement:**
   - `L-1` (Intra-company Transferee) visas are strictly employer-sponsored and non-transferable between unrelated corporations. An L-1 candidate cannot legally work on bench C2C for a third-party client.
   - However, their spouse on **L-2 / L-2S EAD** *is* legally authorized for open employment.
3. **Missing Critical US Statuses:**
   - **US Citizen (USC)**: Currently just "Citizen". Citizen of where?
   - **Permanent Resident (Green Card)**: Essential.
   - **GC-EAD**: Critical for candidates with pending I-485 applications.
   - **H-4 EAD**: Dependent spouse with independent work permit (no sponsorship required).
   - **TN-1 / TN-2**: NAFTA / USMCA professional visa for Canadian and Mexican citizens.
   - **E-3 Visa**: Specialty occupation visa specifically for Australian nationals.
   - **O-1 Visa**: Individuals with extraordinary ability in science, business, arts.
4. **Zero International Awareness:**
   - If a candidate's location country is India (e.g., Bangalore bench developer), UK, Canada, or UAE, they are forced to either leave it blank or pick "Citizen" (which is ambiguous).
   - India should have: `Indian Citizen`, `OCI / PIO Cardholder`, `Work Visa`.
   - Canada should have: `Canadian Citizen`, `Permanent Resident (PR)`, `Open Work Permit`, `PGWP`.
   - UK should have: `British Citizen`, `Indefinite Leave to Remain (ILR)`, `Skilled Worker Visa`.

### 3.3 Data Model & Architectural Analysis
1. **Legacy Column Name:**  
   The column is named `visastatu` (`text` in `job_applications` and `bench_applications`). Per **Rule 13**, we preserve this exact name to prevent database or indexing breaks.
2. **Elasticsearch Indexing:**  
   `visastatu` is synced directly to Elasticsearch via `GeneralMethod.php` and `ProcessCandidateUpdateJob.php`. Storing standardized, normalized values allows recruiters to filter and facet talent seamlessly without painful wildcard matching.

---

## 4. What Recruiters Need for a "GOD-Level" Work Authorization Experience

To transform JERO into the premier B2B talent marketplace, recruiters need four distinct pillars:

### Pillar 1: Right to Work & Visa Taxonomy (Primary)
- Categorized dropdown with intuitive groups:
  - **United States (US):** US Citizen (USC), Green Card (Permanent Resident), GC-EAD, H-1B (Active / Transferrable), H-4 EAD, L-2 EAD, TN Permit, OPT-EAD, STEM-OPT, E-3, O-1.
  - **Canada (CA):** Canadian Citizen, Permanent Resident (PR), Open Work Permit, PGWP, LMIA Closed Work Permit.
  - **India (IN):** Indian Citizen / Domestic Right to Work, OCI / PIO Cardholder, India Employment Visa.
  - **United Kingdom (UK):** British Citizen, Indefinite Leave to Remain (ILR), Skilled Worker Visa, Graduate Visa.
  - **United Arab Emirates & GCC (Dubai / UAE):** UAE Golden Visa (10-Year), UAE Green Visa (5-Year), UAE Employment Residence Visa, UAE Freelance / Remote Visa, UAE / GCC National, GCC Resident Work Permit / Iqama.
  - **Singapore (SG):** Singapore Citizen (SC), Singapore PR, Employment Pass (EP), S Pass, ONE Pass / Tech.Pass / PEP, Letter of Consent (LOC / DP).
  - **Australia & New Zealand (AU / NZ):** Australian Citizen / PR, TSS Visa Subclass 482, Skilled Work Visa Subclass 189/190, Temporary Graduate 485, New Zealand Citizen / Resident.
  - **European Union & Global:** EU Citizen, EU Blue Card, Open Work Permit, Other / Requires Sponsorship.

### Pillar 2: Sponsorship Requirement (Crucial Decision Filter)
Recruiters immediately filter candidates based on whether their client company allows sponsorship:
- 🟢 **No Sponsorship Required** (USC, Green Card, EAD, Citizens)
- 🟡 **May Require Future Sponsorship** (OPT, STEM-OPT, TN)
- 🔴 **Requires Immediate Sponsorship / Transfer** (H-1B Transfer, E-3, O-1)

### Pillar 3: Engagement / Contract Type Eligibility
In B2B bench sharing, recruiters need to know if the talent can be placed on:
- **Corp-to-Corp (C2C)** (Standard for bench talent)
- **W-2** (Direct vendor payroll or client payroll)
- **1099** (Independent contractor)

### Pillar 4: Expiration Date & Clearance (Conditional Intelligence)
- If a temporary visa is chosen (e.g., H-1B, OPT, TN), an optional **Visa / EAD Validity Date** field allows recruiters to avoid candidates whose authorization expires before the project finishes.
- **Security Clearance:** Critical for government, defense, and high-security enterprise clients (`None`, `Public Trust`, `Secret`, `Top Secret`).

---

## 5. Technical Implementation Plan

### 5.1 Architecture & Guardrail Compliance
- **Rule 1 & 2 (CQRS):** Writes go to MySQL, reads in listings/drawers come from Elasticsearch.
- **Rule 7 & 17 (Zero Business Logic Mutation & Page Isolation):** Changes are completely scoped to `resources/views/service_company/bench-candidates/create.blade.php` and its associated controller. No shared components or other modules modified.
- **Rule 13 (Preserve Typos):** Column `visastatu` remains untouched and carries the primary standardized authorization string.
- **Rule 14 (UI Standards):** Metronic, Poppins typography, max font 14px, 100% responsive across 15.6" screens (1366×768 and 1920×1080).
- **Rule 18 (Code Freeze):** No edits to `product_company` or `staffing_company` folders.

### 5.2 UI Component Redesign (Blade Markup)

```blade
<!-- Work Authorization & Compliance Card -->
<div class="fs addl-card addl-card--details" id="work_auth_card">
  <div class="addl-hero">
    <div class="addl-hero-title">
      <span class="addl-hero-icon" style="background: linear-gradient(135deg, #4F46E5, #06B6D4); color: #fff;">
        <i class="fa-solid fa-passport"></i>
      </span>
      <span>
        <strong>Work Authorization & Compliance</strong>
        <small>Specify legal right-to-work, visa status, and employment engagement eligibility.</small>
      </span>
    </div>
  </div>

  <div class="fsc addl-body">
    <section class="addl-section addl-section--details">
      <div class="row g-4 w-100 m-0">
        <!-- Primary Work Authorization Dropdown -->
        <div class="col-12 col-lg-7 p-0 pe-lg-3 addl-wide-field fv-row">
          <label class="d-flex align-items-center justify-content-between">
            <span>
              <i class="fa-solid fa-id-card text-primary me-1"></i> 
              Work Authorization / Visa Status 
              <span class="text-muted fw-normal fs-8">(Optional)</span>
            </span>
          </label>
          <div class="addl-select-wrap">
            <select name="visastatu" id="visastatu" class="form-select form-select-solid select2-work-auth">
              <option value="">Select work authorization or visa</option>
              
              <optgroup label="United States (US)">
                <option value="US Citizen" {{ @$application->visastatu == 'US Citizen' || @$application->visastatu == 'Citizen' ? 'selected' : '' }}>US Citizen (USC)</option>
                <option value="Green Card" {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Permanent Resident (Green Card)</option>
                <option value="GC-EAD" {{ @$application->visastatu == 'GC-EAD' ? 'selected' : '' }}>GC-EAD (Adjustment of Status)</option>
                <option value="H-1B" {{ @$application->visastatu == 'H1B' || @$application->visastatu == 'H-1B' ? 'selected' : '' }}>H-1B (Specialty Occupation)</option>
                <option value="H-4 EAD" {{ @$application->visastatu == 'H-4 EAD' ? 'selected' : '' }}>H-4 EAD (Spouse Dependent)</option>
                <option value="L-2 EAD" {{ @$application->visastatu == 'L-2 EAD' ? 'selected' : '' }}>L-2 EAD (Intracompany Dependent)</option>
                <option value="TN Permit" {{ @$application->visastatu == 'TN Permit' ? 'selected' : '' }}>TN Visa (USMCA - Canada/Mexico)</option>
                <option value="OPT-EAD" {{ @$application->visastatu == 'OPT-EAD' || @$application->visastatu == 'F1' ? 'selected' : '' }}>OPT / STEM-OPT EAD</option>
                <option value="CPT" {{ @$application->visastatu == 'CPT' ? 'selected' : '' }}>CPT (Student Authorized)</option>
                <option value="E-3" {{ @$application->visastatu == 'E-3' ? 'selected' : '' }}>E-3 (Australian Specialty)</option>
                <option value="O-1" {{ @$application->visastatu == 'O-1' ? 'selected' : '' }}>O-1 (Extraordinary Ability)</option>
                <option value="Other US EAD" {{ @$application->visastatu == 'Other US EAD' ? 'selected' : '' }}>Other US EAD (TPS/Asylee/DACA)</option>
              </optgroup>

              <optgroup label="Canada (CA)">
                <option value="Canadian Citizen" {{ @$application->visastatu == 'Canadian Citizen' ? 'selected' : '' }}>Canadian Citizen</option>
                <option value="Canada PR" {{ @$application->visastatu == 'Canada PR' ? 'selected' : '' }}>Canada Permanent Resident (PR)</option>
                <option value="Canada Open Work Permit" {{ @$application->visastatu == 'Canada Open Work Permit' ? 'selected' : '' }}>Canada Open Work Permit / PGWP</option>
                <option value="Canada Closed Work Permit" {{ @$application->visastatu == 'Canada Closed Work Permit' ? 'selected' : '' }}>Canada LMIA / Employer Closed Permit</option>
              </optgroup>

              <optgroup label="India (IN)">
                <option value="Indian Citizen" {{ @$application->visastatu == 'Indian Citizen' ? 'selected' : '' }}>Indian Citizen</option>
                <option value="OCI / PIO" {{ @$application->visastatu == 'OCI / PIO' ? 'selected' : '' }}>OCI / PIO Cardholder</option>
                <option value="India Work Visa" {{ @$application->visastatu == 'India Work Visa' ? 'selected' : '' }}>India Employment Visa</option>
              </optgroup>

              <optgroup label="United Kingdom (UK)">
                <option value="UK Citizen" {{ @$application->visastatu == 'UK Citizen' ? 'selected' : '' }}>British Citizen</option>
                <option value="UK ILR" {{ @$application->visastatu == 'UK ILR' ? 'selected' : '' }}>UK Indefinite Leave to Remain (ILR)</option>
                <option value="UK Skilled Worker" {{ @$application->visastatu == 'UK Skilled Worker' ? 'selected' : '' }}>UK Skilled Worker / Tier 2</option>
                <option value="UK Graduate Visa" {{ @$application->visastatu == 'UK Graduate Visa' ? 'selected' : '' }}>UK Graduate / Youth Mobility Visa</option>
              </optgroup>

              <optgroup label="United Arab Emirates & GCC (Dubai / UAE)">
                <option value="UAE Golden Visa" {{ @$application->visastatu == 'UAE Golden Visa' ? 'selected' : '' }}>UAE Golden Visa (10-Year Self-Sponsored)</option>
                <option value="UAE Green Visa" {{ @$application->visastatu == 'UAE Green Visa' ? 'selected' : '' }}>UAE Green Visa (5-Year Skilled / Freelance)</option>
                <option value="UAE Employment Visa" {{ @$application->visastatu == 'UAE Employment Visa' ? 'selected' : '' }}>UAE Employment Residence Visa (Company / Freezone)</option>
                <option value="UAE Freelance Visa" {{ @$application->visastatu == 'UAE Freelance Visa' ? 'selected' : '' }}>UAE Freelance / Remote Work Visa</option>
                <option value="UAE National" {{ @$application->visastatu == 'UAE National' ? 'selected' : '' }}>UAE / GCC National</option>
                <option value="GCC Iqama" {{ @$application->visastatu == 'GCC Iqama' ? 'selected' : '' }}>GCC Resident Work Permit / Iqama</option>
              </optgroup>

              <optgroup label="Singapore (SG)">
                <option value="Singapore Citizen" {{ @$application->visastatu == 'Singapore Citizen' ? 'selected' : '' }}>Singapore Citizen (SC)</option>
                <option value="Singapore PR" {{ @$application->visastatu == 'Singapore PR' ? 'selected' : '' }}>Singapore Permanent Resident (PR)</option>
                <option value="Singapore EP" {{ @$application->visastatu == 'Singapore EP' ? 'selected' : '' }}>Singapore Employment Pass (EP)</option>
                <option value="Singapore S Pass" {{ @$application->visastatu == 'Singapore S Pass' ? 'selected' : '' }}>Singapore S Pass (Skilled Staff)</option>
                <option value="Singapore ONE Pass" {{ @$application->visastatu == 'Singapore ONE Pass' ? 'selected' : '' }}>Singapore ONE Pass / Tech.Pass / PEP</option>
                <option value="Singapore LOC / DP" {{ @$application->visastatu == 'Singapore LOC / DP' ? 'selected' : '' }}>Singapore Letter of Consent (LOC / Dependant Pass)</option>
              </optgroup>

              <optgroup label="Australia & New Zealand (AU / NZ)">
                <option value="Australia Citizen / PR" {{ @$application->visastatu == 'Australia Citizen / PR' ? 'selected' : '' }}>Australian Citizen / Permanent Resident</option>
                <option value="Australia TSS 482" {{ @$application->visastatu == 'Australia TSS 482' ? 'selected' : '' }}>Australia TSS Visa (Subclass 482 - Temporary Skill)</option>
                <option value="Australia Skilled 189/190" {{ @$application->visastatu == 'Australia Skilled 189/190' ? 'selected' : '' }}>Australia Skilled Work Visa (Subclass 189 / 190)</option>
                <option value="Australia Graduate 485" {{ @$application->visastatu == 'Australia Graduate 485' ? 'selected' : '' }}>Australia Temporary Graduate Visa (Subclass 485)</option>
                <option value="New Zealand Citizen / PR" {{ @$application->visastatu == 'New Zealand Citizen / PR' ? 'selected' : '' }}>New Zealand Citizen / Resident</option>
              </optgroup>

              <optgroup label="European Union & Global (EU / Global)">
                <option value="EU Citizen" {{ @$application->visastatu == 'EU Citizen' ? 'selected' : '' }}>EU / EEA Citizen</option>
                <option value="EU Blue Card" {{ @$application->visastatu == 'EU Blue Card' ? 'selected' : '' }}>EU Blue Card</option>
                <option value="Authorized Work Permit" {{ @$application->visastatu == 'Authorized Work Permit' ? 'selected' : '' }}>Authorized Work Permit (Country Specific)</option>
                <option value="Requires Sponsorship" {{ @$application->visastatu == 'Requires Sponsorship' ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
              </optgroup>
            </select>
            <i class="fa-solid fa-chevron-down"></i>
          </div>
          <small class="text-muted fs-8 mt-1">Select the candidate's active legal right-to-work or visa category.</small>
        </div>

        <!-- Visa Sponsorship Requirement -->
        <div class="col-12 col-lg-5 p-0 ps-lg-3 addl-wide-field">
          <label class="d-flex align-items-center">
            <i class="fa-solid fa-handshake-angle text-info me-1"></i>
            Sponsorship Requirement
          </label>
          <div class="d-flex align-items-center gap-2 mt-1">
            <select name="visa_sponsorship" id="visa_sponsorship" class="form-select form-select-solid">
              <option value="not_required" {{ @$application->visa_sponsorship == 'not_required' ? 'selected' : '' }}>No Sponsorship Needed</option>
              <option value="future_required" {{ @$application->visa_sponsorship == 'future_required' ? 'selected' : '' }}>May Require Future Sponsorship</option>
              <option value="immediate_required" {{ @$application->visa_sponsorship == 'immediate_required' ? 'selected' : '' }}>Requires Immediate Sponsorship</option>
            </select>
          </div>
          <small class="text-muted fs-8 mt-1">Indicates if the client or employer must sponsor a visa petition.</small>
        </div>
      </div>

      <!-- Hidden Reference Name preserved for backward compatibility -->
      <input type="hidden" name="referencename" id="referencename" value="{{ @$application->referencename }}" />
    </section>

    <!-- Corrected & Professional Compliance Banner -->
    <div class="addl-info-strip">
      <span><i class="fa-solid fa-shield-halved text-primary"></i></span>
      <p>Providing accurate work authorization accelerates client placement approvals and prevents compliance disqualifications during submission.</p>
    </div>
  </div>
</div>
```

### 5.3 Backward Compatibility & Persistence
1. **Existing Candidates:** Any candidate currently possessing `H1B`, `L1`, `F1`, `Green Card`, or `Citizen` continues to display properly because the Blade check maps both legacy and new values:
   `{{ @$application->visastatu == 'H1B' || @$application->visastatu == 'H-1B' ? 'selected' : '' }}`.
2. **Elasticsearch Indexing:**
   The value of `visastatu` is synced automatically into the ES index `ats_candidates_elastic` and `bench_applications` index. No mapping changes are required because `visastatu` is already mapped as a `keyword`/`text` field.
3. **Supplementary Metadata (`visa_sponsorship`):**
   Can be stored seamlessly into `bench_additional_detail` using existing key-value architecture (`custom_keys = 'visa_sponsorship'`, `custom_keys_value = 'not_required'`), or as an optional non-breaking attribute without altering the core 217 database table baseline.

---

## 6. Implementation Plan & Milestones

### Phase 1: Blade UI Polish & Taxonomy Upgrade
- Replace the legacy 5-item un-grouped select in `resources/views/service_company/bench-candidates/create.blade.php`.
- Introduce organized `<optgroup>` items for United States, Canada, India, United Kingdom, and Global.
- Update the card header from generic "Additional Details" to "Work Authorization & Compliance".
- Fix the misleading copy-pasted info strip text ("Adding relevant links...").
- Keep CSS strictly scoped under `#talent_create_app` and Metronic guidelines.

### Phase 2: Controller & Data Layer Alignment
- In `ServiceCompanyBenchCandidatesController@store` and `update`, ensure `$request->visastatu` persists cleanly.
- If supplementary fields (`visa_sponsorship`) are populated, record them into `bench_additional_detail` or request attributes without breaking existing schemas.

### Phase 3: Recruiter Discovery & Drawer Verification
- Verify that `visastatu` displays clearly in the candidate details drawer (`benchCandidateDetails/public_show`) and listing badges.
- Ensure that searching by work authorization in Elasticsearch filters correctly matches both legacy and modern values.

### Phase 4: Zero-Regression & 15.6" Screen Testing
- Verify responsive layout at 1366×768 (standard laptop) and 1920×1080 (full HD desktop).
- Execute PHP syntax validation (`php -l`) and code formatting (`pint`).
- Confirm zero impact on `product_company` or `staffing_company` code-frozen modules.
