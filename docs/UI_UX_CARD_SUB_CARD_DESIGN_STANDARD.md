# UI/UX Standard: Modern Enterprise Card + Sub-Card Design System

> **MANDATORY DESIGN STANDARD ACROSS ALL MODULES & SYSTEMS**  
> **Target Audience:** AI Coding Agents, Frontend Engineers, UI/UX Designers  
> **Status:** Authoritative / Gold Standard (Production Certified)  
> **Baseline:** Metronic UI, Poppins Font, 15.6" Laptop Screen Responsive (`1366×768` & `1920×1080`), Zero Horizontal Page Scroll (`overflow-x: hidden`), 0% MySQL Reads (Strict CQRS).

---

## 1. Executive Summary & Core Law

All future and refactored entity listings, drawer tabs, overview views, and operational management modules across JERO (Service Company, Admin, Marketplace, Bench Talent, Requisitions, Interviews, Offers & Joinings) **MUST AND ONLY** use the **Modern Card + Sub-Card Enterprise Design System**.

Raw, sprawling HTML tables, generic unstyled card heads, and unstructured flat lists are **strictly prohibited**.

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1: TOP HEADER TOOLBAR CARD (White Background #ffffff, Border #e2e8f0, Radius 10px)         │
│  [Icon] Entity Title ("Jobs") & Subtitle        [Search Input Sub-Card 🔍] [Clear ✕] [Action +]  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                ▼
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 2: SUB-TABS CARD (White Background #ffffff, Border #e2e8f0, Radius 10px)                   │
│  [All Jobs (4)]  [Hiring (4) Active Blue]  [On Hold (0)]  [Closed (0)]  [Inactive (0)]            │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                ▼
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 3: PRIMARY ENTITY CARD STREAM (Hover Elevation, Radius 10px, Border #e2e8f0)               │
│  ┌─────────────────┬───────────────────┬────────────────┬─────────────────┬───────────────────┐  │
│  │ SUB-CARD 1:     │ SUB-CARD 2:       │ SUB-CARD 3:    │ SUB-CARD 4:     │ SUB-CARD 5:       │  │
│  │ Job Details     │ Comp & Exp        │ Team           │ Agency          │ Hiring Pipeline   │  │
│  │ Logo, Title, ID,│ Row 1: Salary     │ Spok/Lead,     │ Hired Agencies  │ 8 Stages Counters,│  │
│  │ Dept, Dynamic   │ Row 2: Clock+Exp  │ Member Count   │ Count (or "—")  │ Total Apps & Int  │  │
│  │ Client • Full-  │ Row 3: Location • │                │                 │                   │  │
│  │ Time, Status    │ Work From Office  │                │                 │                   │  │
│  └─────────────────┴───────────────────┴────────────────┴─────────────────┴───────────────────┘  │
│  ┌─────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ SUB-CARD 6 (BOTTOM STRIP): Posted Date • Hiring Manager (Role) • [Skill] [Skill] [Skill]    │  │
│  └─────────────────────────────────────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. The 3-Tier Layered Architecture

### Tier 1: Top Header Toolbar Card (`.cd-*-header-card`)
A unified, high-density toolbar card that encapsulates the section's identity, dynamic full-text search, and primary call-to-action.

1. **Card Container:**
   - Background: Dedicated Header Background Color (`#f8fafc !important` or soft slate `#f1f5f9 !important`)
   - Border: `1px solid #e2e8f0 !important`
   - Border-Radius: `10px !important`
   - Padding: `12px 18px !important`
   - Margin-Bottom: `12px !important`
   - Elevation: `box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important`
   - Flexbox: `display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;`
2. **Left Branding Area:**
   - Icon Box: `38px × 38px`, `border-radius: 8px`, `background: #eff6ff`, `color: #2563eb`, `font-size: 16px`.
   - Title: `font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.25;`
   - Subtitle: `font-size: 11.5px; color: #64748b; margin-top: 2px;`
3. **Right Action Area:**
   - **Search Sub-Card (`.cd-*-search-subcard`):**
     - Dimensions: `height: 36px; width: 280px; max-width: 100%;`
     - Background: Light neutral (`#ffffff`), transitions to `#ffffff` on focus.
     - Border: `1px solid #e2e8f0`, transitions to `#009ef7` with `box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12)`.
     - Border-Radius: `8px`.
     - Elements: Embedded `<i class="fas fa-search"></i>`, borderless `<input>` (12px Poppins), and interactive clear button `<button type="button" class="cd-*-search-clear">✕</button>`.
   - **Action Button (`.cd-*-add-btn`):**
     - Height: `36px`, Metronic primary blue (`btn-primary`), border-radius `8px`, font-size `12px`, font-weight `600`.

---

### Mandatory Header Background Color Standard (Universal Law)
All headers across drawers, cards, sub-card sections, and tables **MUST ALWAYS** carry a dedicated background color to establish visual hierarchy, create crisp boundaries, and eliminate flat/unanchored floating text:
1. **Drawer Headers (`.sd-topbar`, modal headers):**
   - Background: `#f8fafc !important`
   - Border-Bottom: `1px solid #e2e8f0 !important`
   - Box-Shadow: `0 1px 3px rgba(15, 23, 42, 0.04) !important`
   - Action buttons sit on white pill backgrounds with subtle shadows.
2. **Card Headers (`.card-header`, `.jsd-card-header`):**
   - Background: `#f8fafc !important`
   - Border-Bottom: `1px solid #e2e8f0 !important`
   - Padding: `12px 18px !important`
   - Contrasts sharply against card body `#ffffff !important` to immediately communicate section intent.
3. **Sub-Card Section Headers:**
   - Background: `#f8fafc !important` or `#f1f5f9 !important` (or soft semantic tint: `#ecfdf5` for active, `#fffbeb` for warning).
   - Border-Bottom: `1px solid #e2e8f0 !important`
4. **Table & Capability Matrix Headers (`thead tr th`):**
   - Background: `#f1f5f9 !important`
   - Border-Bottom: `1px solid #e2e8f0 !important`
   - Typography: Uppercase, bold, `11px`, `#475569`, tracking `0.5px`.

---

### Tier 2: Sub-Tabs Card (`.cd-*-tabs-card`)
Sub-navigation and category filters must **NEVER** be loose, unstyled links or floating raw text. They must sit inside a dedicated white card.

1. **Card Container:**
   - Background: Pure White (`#ffffff !important`)
   - Border: `1px solid #e2e8f0 !important`
   - Border-Radius: `10px !important`
   - Padding: `8px 12px !important`
   - Margin-Bottom: `14px !important`
   - Elevation: `box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important`
2. **Individual Sub-Tab Buttons (`.cd-*-tab-btn`):**
   - Background: `#ffffff !important`
   - Border: `1px solid #e2e8f0 !important`
   - Border-Radius: `8px !important`
   - Padding: `6px 14px !important`
   - Font: `font-size: 11.5px !important; font-weight: 600 !important; color: #475569 !important;`
   - Elevation: `box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important`
   - Hover: `background: #f8fafc; border-color: #cbd5e1; color: #0f172a;`
3. **Active Tab Highlight Rule:**
   - **Strict Rule:** ONLY the single currently active tab receives the vibrant highlight.
   - Active Background: Metronic Primary Blue (`background: #009ef7 !important; border-color: #009ef7 !important; color: #ffffff !important;`)
   - Active Shadow: `box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important`
4. **Dynamic Count Badges Inside Tabs:**
   - Badge styling: Pill shape, `min-width: 20px; height: 18px; padding: 0 6px; border-radius: 10px; font-size: 10.5px; font-weight: 700;`
   - Inactive Badge: Semantic light background (e.g. `#dcfce7` green for hiring, `#fef3c7` amber for hold, `#f1f5f9` slate for neutral).
   - Active Badge: Inverted semi-transparent white (`background: rgba(255, 255, 255, 0.28) !important; color: #ffffff !important;`).

---

### Tier 3: Primary Entity Card → Discrete Sub-Cards Stream

Every record in the stream is represented as a **Primary Card** divided into discrete **Sub-Cards**.

#### A. Master Primary Card (`.cd-*-card`)
- Background: `#ffffff !important`
- Border: `1px solid #e2e8f0 !important`
- Border-Radius: `10px !important`
- Margin-Bottom: `12px !important`
- Hover: `border-color: #cbd5e1; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);`

#### B. Discrete Sub-Cards Grid (`.cd-*-card-grid`)
Constructed using fractional CSS Grid units with `minmax(0, ...)` to ensure mathematical immunity to horizontal overflow on 15.6" screens:
```css
display: grid !important;
grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.4fr) minmax(0, 1.05fr) minmax(0, 0.85fr) minmax(0, 1.6fr) !important;
gap: 8px !important;
padding: 10px 12px !important;
width: 100% !important;
align-items: stretch !important;
```

#### C. The Standard 6 Sub-Card Anatomy (Canonical Example: Jobs)
1. **Sub-Card 1: Primary Details (`.cd-subcard-details`)**
   - Company brand logo (`38px × 38px`, object-fit cover) or vibrant initial box with deterministic color mapping.
   - Entity Title (12.5px bold, with drawer trigger routing).
   - Entity ID (`#99991` in primary blue) & Department / Category name.
   - **Dynamic Client Name & Employment Type:** Third row displays the dynamically populated Client Name (never a hardcoded company name) immediately followed by the Employment Type badge (`CLIENT NAME • 💼 Full-Time`).
   - **Header Classification & Status Badges:** Sub-Card 1 header displays the unambiguous **Source / Scope Pill** (NEVER bare `"Internal"`) positioned beside the Canonical Status Badge (`HIRING` `#dcfce7`, `HOLD` `#fef3c7`, `CLOSED` `#fee2e2`, `INACTIVE` `#f1f5f9`):
     - **`My Organization`** (own-company / self hiring): `.type-my-org` — Warm Amber/Yellow `#fef3c7` bg, `#92400e` text, `#fcd34d` border, `#d97706` icon
     - **`Internal Client`** (managed non-marketplace client): `.type-internal` — Blue `#eff6ff` bg, `#2563eb` text, `#bfdbfe` border
     - **`Direct Client`** (marketplace client mandate): `.type-direct` — Emerald `#ecfdf5` bg, `#059669` text, `#a7f3d0` border
     - **Pending Job Requests:** Render BOTH **`Direct Client`** (`.type-direct`) AND **`Job Request`** (`.type-request` — Violet `#f5f3ff` bg, `#7c3aed` text, `#ddd6fe` border)
2. **Sub-Card 2: Compensation & Experience (`.cd-subcard-comp-loc`, Label: `COMPENSATION & EXP`)**
   - **Row 1:** Salary range formatted cleanly in bold (`₹5,000 - 2.50 L`).
   - **Row 2:** Experience range in clean typography with **no extra experience/clock icons** (`6 - 10 Yrs`).
   - **Row 3:** Location & Work Mode indicator (`📍 London +14 • Work From Office`). Standardized to **"Work From Office"** (never just "Office").
3. **Sub-Card 3: Ownership / Recruitment Team (`.cd-subcard-team`)**
   - Spok / Lead recruiter name.
   - Member count (`1 Member` / `X Members`, or `—` if none).
   - **Strict Negative Law:** NEVER render fake or misleading matching badges (e.g., "15 Matching" is strictly banned).
4. **Sub-Card 4: External Partner / Agency (`.cd-subcard-agency`)**
   - Displays authoritative hired agency count (`3 Agencies`, `1 Agency`, or `—` if 0).
   - **Strict Negative Law:** NEVER render matching badges in agency blocks.
5. **Sub-Card 5: Pipeline & Stage Distribution (`.cd-subcard-pipeline`)**
   - 8 Canonical recruitment stages (`INW`, `SOU`, `SCR`, `EVA`, `NEG`, `OFF`, `JOI`, `REJ`).
   - Stage micro-badges (`18px × 15px`, bold micro-typography).
   - Pipeline summary footer (`Apps: X • Interviews: Y`).
6. **Sub-Card 6: Bottom Activity Strip (`.cd-job-card-bottom`)**
   - Clean horizontal sub-strip with top hairline border (`1px solid #f1f5f9`).
   - Posted Date with calendar icon.
   - Hiring Manager name & role.
   - Responsive Skill Pills (`.cd-skill-pill`) with horizontal auto-scroll protection (`scrollbar-width: none`).

---

## 3. Canonical HTML & Blade Blueprint

```blade
<!--begin::Entity Content Container-->
<div class="cd-jobs-wrapper">

    <!-- 1. Top Header Toolbar Card with Dynamic Search -->
    <div class="cd-jobs-header-card">
        <div class="cd-jobs-header-left">
            <div class="cd-jobs-header-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div>
                <div class="cd-jobs-header-title">Jobs</div>
                <div class="cd-jobs-header-sub">Roles open with this client.</div>
            </div>
        </div>
        <div class="cd-jobs-header-right">
            <div class="cd-jobs-search-subcard">
                <i class="fas fa-search"></i>
                <input type="text" id="inputJobSearch" name="inputJobSearch" placeholder="Search jobs by title, skills, ID..." autocomplete="off">
                <button type="button" class="cd-jobs-search-clear" id="btnJobSearchClear" style="display: none;" title="Clear search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @if (in_array('add_jobs', $userPermissions))
                <a href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}" class="btn btn-sm btn-primary fw-bold cd-jobs-add-btn">
                    <i class="fas fa-plus me-1"></i> Add Jobs
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Sub-Tabs Card with White Background -->
    @if (in_array('view_jobs', $userPermissions))
        <div class="cd-jobs-tabs-card">
            <div class="cd-jobs-tabs-group">
                <button type="button" class="cd-jobs-tab-btn js-job-filter-btn active" data-status="all">
                    <span>All Jobs</span>
                    <span class="cd-jobs-tab-badge" id="filterCountAll">{{ $kpiActive ?? 0 }}</span>
                </button>
                <button type="button" class="cd-jobs-tab-btn js-job-filter-btn" data-status="hiring">
                    <span>Hiring</span>
                    <span class="cd-jobs-tab-badge badge-hiring" id="filterCountHiring">{{ $kpiActive ?? 0 }}</span>
                </button>
                <button type="button" class="cd-jobs-tab-btn js-job-filter-btn" data-status="hold">
                    <span>On Hold</span>
                    <span class="cd-jobs-tab-badge badge-hold" id="filterCountHold">0</span>
                </button>
                <button type="button" class="cd-jobs-tab-btn js-job-filter-btn" data-status="closed">
                    <span>Closed</span>
                    <span class="cd-jobs-tab-badge badge-closed" id="filterCountClosed">0</span>
                </button>
                <button type="button" class="cd-jobs-tab-btn js-job-filter-btn" data-status="inactive">
                    <span>Inactive</span>
                    <span class="cd-jobs-tab-badge badge-inactive" id="filterCountInactive">0</span>
                </button>
            </div>
        </div>

        <!-- 3. Primary Cards List Container (Populated via AJAX) -->
        <div class="jobx">
            <div id="clientJobsListajax" class="cd-jobs-stream"></div>
        </div>
        <div id="paginationclientjobs" class="mt-3"></div>
    @endif
</div>
<!--end::Entity Content Container-->
```

---

## 4. Canonical JavaScript Controller Blueprint

```javascript
// 1. Debounced Dynamic Search Event Handler
var jobSearchTimer = null;
$(document).on('keyup', '#inputJobSearch', function() {
    clearTimeout(jobSearchTimer);
    var searchVal = $(this).val();
    
    // Toggle interactive clear button
    if (searchVal.length > 0) {
        $('#btnJobSearchClear').show();
    } else {
        $('#btnJobSearchClear').hide();
    }
    
    // 300ms debounce prevents unnecessary network requests
    jobSearchTimer = setTimeout(function() {
        fetchJobsClient(1, searchVal, window.currentJobStatusInternal || 'all');
    }, 300);
});

// 2. Interactive Search Clear Button
$(document).on('click', '#btnJobSearchClear', function() {
    $('#inputJobSearch').val('');
    $(this).hide();
    fetchJobsClient(1, '', window.currentJobStatusInternal || 'all');
});

// 3. Dynamic Sub-Tab Switching (Single Active Highlight & Instant Filter)
$(document).on('click', '.js-job-filter-btn', function() {
    var status = $(this).data('status') || 'all';
    window.currentJobStatusInternal = status;
    
    // Switch single active state
    $('.js-job-filter-btn').removeClass('active');
    $(this).addClass('active');
    
    // Fetch data with active search and status
    fetchJobsClient(1, $('#inputJobSearch').val(), status);
});
```

---

## 5. Non-Negotiable Rules for AI Coding Models

1. **NO Raw Tables for Complex Entities:** Do not render entities with 5+ attributes as a flat `<table>` row. Always use the **Card + Sub-Card** hierarchy.
2. **NO Unstyled Plain Text Headers:** Never output naked headings like `<div class="cd-tabcard-title">Jobs</div>`. Always wrap in `.cd-*-header-card` with an icon and dynamic search bar.
3. **NO Loose Sub-Tabs:** Sub-tabs must be wrapped inside `.cd-*-tabs-card` with `background: #ffffff`.
4. **NO Hardcoded Pixel Widths:** Never hardcode widths like `width: 1400px` or `width: 300px` on grid cells. Always use CSS Grid with `minmax(0, ...fr)` to guarantee 15.6" screen responsiveness.
5. **Zero Horizontal Scrollbar (`overflow-x: hidden`):** The entire container must shrink gracefully without page-level or container horizontal scrollbars.
6. **NO Artificial Matching Badges:** Do not inject placeholder badges like "15 Matching" or "7 Matching" in Team or Agency sub-cards. Show real metrics or clean em-dashes (`—`).
7. **Strict CQRS Compliance:** The read pipeline for cards must execute with **0% MySQL reads**, drawing 100% of data from Elasticsearch read projections.
8. **Dynamic Client Name & Full-Time Placement:** In the Primary Details sub-card, always display the dynamically populated Client Name (never hardcode company names) with Employment Type (e.g. `Full-Time` / `Full-time` / `Contract`) placed in the same row immediately next to the client name.
9. **Mandatory Background Color for All Headers:** All drawer headers (`.sd-topbar`), Tier 1 toolbar cards, card headers (`.card-header`, `.jsd-card-header`), sub-card section headers, and comparison matrix / table header rows (`thead tr`) **MUST ALWAYS** have a dedicated background color (e.g., `#f8fafc` or `#f1f5f9` with border `#e2e8f0`). Never leave headers transparent, pure white, or unanchored floating text.

---

## 6. Mandatory Organization Naming, Colour & Sub-Card Standards (Application-Wide)

All modules, drawers, headers, and cards across JERO **MUST** reuse the following canonical AppTracker naming conventions, colour tokens, and sub-card field placements consistently going forward (never invent ad-hoc labels like `"Internal"` or unaligned hex palettes):

### A. Organization / Client Classification Badges & Palettes

| Classification Condition | Mandatory Label | Icon | Pill Background | Pill Border | Pill Text | Card / Accent Token |
|---|---|---|---|---|---|---|
| Own company internal requisition (`self_company == 1`) | **`My Organization`** | `fas fa-building` | `#fef3c7` | `1px solid #fcd34d` | `#92400e` | Bg `#fffbeb`, Border `#fde68a`, Accent `#d97706` |
| Own company client requisition (`self_company == 0`) | **`Internal Client`** *(never `"Internal"`)* | `fas fa-building` | `#eff6ff` | `1px solid #bfdbfe` | `#2563eb` | Bg `#eff6ff`, Border `#bfdbfe`, Accent `#2563eb` |
| External shared / partner requisition (`Direct Client`) | **`Direct Client`** | `fas fa-handshake` | `#d1fae5` | `1px solid #6ee7b7` | `#166534` | Bg `#ecfdf5`, Border `#a7f3d0`, Accent `#16a34a` |

### B. Canonical Sub-Card Field Mapping Rules

1. **Candidate Sub-Card:** Display Candidate Avatar, Full Name, Designation, and **Current Company** (`<i class="fas fa-building"></i>`, `current_company` with `–` fallback) instead of city/country location.
2. **Requisition Sub-Card:** Header carries `Requisition` + the standardized `My Organization` / `Internal Client` / `Direct Client` pill; body carries Job Title, `#ID · Client Name`, and **Employment Type** (`Full-time` / `Contract` / `Part Time`). Never render meeting platform badges (`Google Meet`, `Zoom`) inside Requisition.
3. **Schedule & Link Sub-Card:** Display the interview platform / sub-mode pill (`Google Meet`, `Zoom`, `MS Teams`, `Phone Call`, `In-Person`, etc.) alongside the scheduled date rather than a `Completed` / `Today` status tag.
4. **Ownership & Recruiter Sub-Card (`.aptk-subcard-owner` / `.it-subcard-owner`):** Header `<i class="fas fa-user-tie"></i> Ownership & Recruiter`; Row 1 blue `<i class="fas fa-user-circle"></i>` + Recruiter Name (`#0f172a`, 11.5px, 600); Row 2 muted `<i class="fas fa-building"></i>` + Company Name (`#64748b`, 10.5px); Row 3 muted `<i class="far fa-calendar-alt"></i>` + `Submitted: <relative time>` (`#94a3b8`, 10px).
9. **Compensation & Experience Sub-Card:** Sub-card 2 must be labeled `COMPENSATION & EXP` with Row 1 for salary, Row 2 for clean experience range without duplicate/extra experience icons (`X - Y Yrs`), and Row 3 for location & work mode.
10. **Consistent Work Mode Wording:** Always use `"Work From Office"` (never just `"Office"`) across all entity views and tabs.
11. **Client Scope & Source Classification (No Bare `"Internal"`):** Sub-Card 1 header must render the unambiguous pill beside the status badge: **`My Organization`** (`.type-my-org`, Warm Amber/Yellow `#fef3c7` / `#92400e` / `#fcd34d` for own-company hiring), **`Internal Client`** (`.type-internal`, Blue `#eff6ff` / `#2563eb` / `#bfdbfe` for managed clients), **`Direct Client`** (`.type-direct`, Emerald `#ecfdf5` / `#059669` / `#a7f3d0` for marketplace clients), and for pending requests both **`Direct Client`** + **`Job Request`** (`.type-request`, Violet `#f5f3ff` / `#7c3aed` / `#ddd6fe`). Never shorten or replace `My Organization` or `Internal Client` with bare `"Internal"`.
12. **Multi-Tenant Scoping:** All client-side storage, caches, and requests must carry explicit tenant scoping (`company_id`).
