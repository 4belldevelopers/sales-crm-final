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
│  │ Job Details     │ Comp & Location   │ Team           │ Agency          │ Hiring Pipeline   │  │
│  │ Logo, Title, ID,│ Salary, Exp, Mode,│ Spok/Lead,     │ Hired Agencies  │ 8 Stages Counters,│  │
│  │ Status Badge    │ Extra Cities      │ Member Count   │ Count (or "—")  │ Total Apps & Int  │  │
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
   - Background: Pure White (`#ffffff !important`)
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
     - Background: Light neutral (`#f8fafc`), transitions to `#ffffff` on focus.
     - Border: `1px solid #e2e8f0`, transitions to `#009ef7` with `box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12)`.
     - Border-Radius: `8px`.
     - Elements: Embedded `<i class="fas fa-search"></i>`, borderless `<input>` (12px Poppins), and interactive clear button `<button type="button" class="cd-*-search-clear">✕</button>`.
   - **Action Button (`.cd-*-add-btn`):**
     - Height: `36px`, Metronic primary blue (`btn-primary`), border-radius `8px`, font-size `12px`, font-weight `600`.

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
   - Entity ID (`#99991` in primary blue).
   - Department / Category name.
   - Client / Parent company name.
   - Canonical Status Badge (Hiring `#dcfce7`, Hold `#fef3c7`, Closed `#fee2e2`, Inactive `#f1f5f9`).
2. **Sub-Card 2: Compensation, Experience & Location (`.cd-subcard-comp-loc`)**
   - Salary range formatted in compact Indian Lakhs (`₹X.XX L`) or Western Thousands (`$X - $Y`).
   - Experience range (`Min - Max Yrs`).
   - Employment type badge (`Contract`, `Full-time`).
   - Primary City with extra cities counter pill (`+1`).
   - Work mode indicator (`Office`, `Remote`, `Hybrid`).
3. **Sub-Card 3: Ownership / Internal Team (`.cd-subcard-team`)**
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
8. **Multi-Tenant Scoping:** All client-side storage, caches, and requests must carry explicit tenant scoping (`company_id`).
