# AI UI/UX & Search Technology Standard: Header Card, Micro-Search Bar, Filter Combobox Pills & JDCR Client-First Search Engine

> **Authoritative AI Coding Agent Specification (Clients Tracker Reference Pattern)**  
> **Document ID:** `JERO-AI-UIUX-SEARCH-2026-V1`  
> **Date:** 2026-11-12  
> **Reference Implementation:** Clients Tracker (`resources/views/service_company/client/index.blade.php`, `demo/css/clientList.css`, `public/assets/service_company/client/cl-data-worker.js`, `public/assets/service_company/client/cl-entity-db.js`, `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`)  
> **Compliance:** Rule 1–23 (`AGENTS.md` / `CLAUDE.md`), Metronic UI + Poppins Font, 15.6" Laptop Fit (`1366×768` & `1920×1080`), Complete Page/Blade Isolation (Rule 17), Zero MySQL Reads (CQRS), 10,000 Concurrent Users / 0% Server Strain  
> **Scope:** Service Company & Admin entity listing pages (Product Company & Staffing Company remain frozen per Rule 18)

---

## 1. Executive Summary & AI Mandate

When an AI coding agent designs, builds, or refactors **any entity listing or tracker page** in JERO, the **Top Header Card**, **Search Bar**, **Filter & Sort Combobox Pills**, and **Search/Filter Execution Technology** **MUST** follow the exact visual design system and client-first search architecture proven on the **Clients Tracker** page.

### Non-Negotiable Directives for AI Agents
1. **Header Card Standard:** Every tracker/listing page must render a pure white `.{prefix}-header-card` (`#ffffff`, `1px solid #e2e8f0`, `border-radius: 12px`) with a `44×44px` tinted icon squircle on the left, bold `20px` Poppins title + optional verified shield badge, concise `13px` muted subtitle, and right-aligned action buttons (secondary outline dropdown + primary amber/gold CTA).
2. **Search Bar Standard:** A full-flex `42px`-tall (`40px` at `<=1400px`) micro-search input (`border-radius: 10px`, `#ffffff` background, `#009ef7` focus ring) featuring a left search icon, clear placeholder enumerating searchable fields, and a right-affix cluster with a live match-count pill (`0 matches`), clear button (`✕`), and `Ctrl+K` keyboard shortcut badge.
3. **Uniform UI/UX Across ALL Filters (Sort, Type, Experience, Location, Industry, Activity, Pipeline, or Any Other Filter):** **Every** filter control—whether **Sort**, **Type**, **Experience**, **Status**, **Location**, **Industry**, **Activity**, **Pipeline**, or any module-specific filter—**MUST** use the exact same Clients Tracker `42px`-tall (`40px` at `<=1400px`) rounded combobox pill shell (`border-radius: 10px`, icon + 2-line `10px` label / `12px` bold value + inline quick-clear `✕` + chevron) and `WAI-ARIA 1.2` popover panel with live facet counts, active-state tint, and full keyboard navigation. Native `<select>` controls, Select2 dropdowns, or inconsistent filter styles are **strictly forbidden**.
4. **Complete Copy of Clients Tracker Search & Filter Technology (JDCR Client-First Engine):** Interactive typing, filtering (`Sort`, `Type`, `Experience`, and every other filter), and pagination **MUST completely replicate** the Clients Tracker engine (`ClientSearchEngine` + Web Worker OLAP + IndexedDB L2 persistence + bounded SWR `<Entity>Store` + ETag-cached Elasticsearch projection `active*Projection` + `30ms` micro-debounce + single-pass `O(N)` filter/facet/scoring pipeline), achieving **0ms perceived latency** and **0% MySQL/server strain per keystroke**.
5. **Complete Page/Blade Isolation (Zero Cross-Module Coupling — Rule 17):** Although the UI/UX and search/filter technology are **copied in full** from the Clients Tracker page, every new or refactored page **MUST keep its entire implementation 100% isolated to that page**. Clone and rename all CSS prefixes (`.{prefix}-*`), DOM IDs, JS engine/store objects (`<Entity>SearchEngine`, `<Entity>Store`), Web Worker files (`<prefix>-data-worker.js`), IndexedDB keys, `localStorage` ETag keys, and projection routes. **NEVER** import, reference, or share stateful JS globals, CSS classes, or Blade partials from Clients Tracker (`cl-*` / `ClientSearchEngine` / `cl-data-worker.js`) into another page. Changes to the new page must have **0% blast radius** on Clients Tracker or any other module.

---

## 2. Architecture & Data Flow (Client-First Search Technology)

The Clients Tracker search technology combines **4 coordinated layers** so that the initial page load hydrates once and every subsequent search, filter, sort, and page change executes locally in Browser RAM with zero network round-trips.

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ LAYER 1: INITIAL HYDRATION & ELASTICSEARCH PROJECTION (Server — 0% MySQL Reads)         │
│ • Initial page SSR seeds page 1 ($initialRows) + facet counts + tenant data_version     │
│ • Background fetch to projection endpoint (e.g. activeClientProjection, per_page=5000)  │
│ • Protected by Dragonfly/Redis cache + HTTP ETag (If-None-Match -> 304 Not Modified)    │
└────────────────────────────────────────────┬────────────────────────────────────────────┘
                                             ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ LAYER 2: TENANT-SCOPED PERSISTENT L2 CACHE (Browser IndexedDB)                          │
│ • Static bundles (countries, industries manifest) cached in IndexedDB                   │
│ • Full entity projection stored under tenant+user+schema scoped key                     │
│ • Cache-poisoning guards validate tenant_id, schema_version, and entity_version         │
└────────────────────────────────────────────┬────────────────────────────────────────────┘
                                             ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ LAYER 3: IN-BROWSER RAM SEARCH & OLAP ENGINE (ClientSearchEngine + Web Worker)          │
│ • Flat searchIndex[] + idSlotMap (Map) + activeBitmask (Uint8Array) + presentationStore │
│ • 30ms Micro-Debounce on input -> single-pass O(N) Filter + Facet Count + Scoring       │
│ • Multi-field weighted token matching + coherence bonus + deterministic sort + slice    │
│ • Web Worker offloads full OLAP queries & CSV serialization off the main UI thread      │
└────────────────────────────────────────────┬────────────────────────────────────────────┘
                                             ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ LAYER 4: BOUNDED SWR STORE & FALLBACK CIRCUIT BREAKER                                   │
│ • <Entity>Store (Map capped at 50 entries, 5s maxStaleAgeMs)                            │
│ • If engine not yet full/ready (e.g., >5,000 rows or pending tab): AJAX to ES endpoint  │
│ • Aborts stale XHR (currentXhr.abort()) + monotonic latestRequestId token guard         │
│ • Syncs URL query params via history.pushState/replaceState & supports browser popstate │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

### Mutation & Cache Invalidation Lifecycle
Whenever an entity is created, updated, accepted/declined, or deleted:
1. Update the server Elasticsearch index synchronously/via outbox and bump the tenant cache version (`tenant_{company_id}_{entity}_version`).
2. In the browser, execute delta updates on `<Entity>SearchEngine` (`upsertEntity(dto)` or `deleteEntity(id)`) — or `destroy()` + clear `<Entity>Store`, `localStorage` ETag, and IndexedDB projection key when a full state transition occurs.
3. Never leave stale records in `presentationStore` or `activeBitmask`.

---

## 3. Header Card UI/UX Design & Header Text Specification

### 3.1 Visual Anatomy

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ ┌──────┐                                                                                        │
│ │ Icon │  Clients Tracker  [🛡️]                             [ ↓ Export ▾ ]  [ + Add Client ]    │
│ │ 44px │  Manage client relationships, hiring activity and recruitment performance.             │
│ └──────┘                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
  .cl-header-card (#ffffff, border: 1px solid #e2e8f0, radius: 12px, padding: 14px 20px)
```

### 3.2 Header Text Rules (Mandatory Copy & Typography)
| Element | Rule / Spec | Example (Clients Tracker) |
|---|---|---|
| **Title Tag & Font** | `<h1>` in `Poppins`, `20px`, `font-weight: 700`, `color: #0f172a`, `letter-spacing: -0.015em`, `line-height: 1.2`, `margin: 0` | `Clients Tracker` |
| **Title Copy Pattern** | 2–3 words, Title Case, plural entity noun + module role (e.g., `Jobs Tracker`, `Candidates Tracker`, `Bench Talent`). Never truncate or wrap into 3 lines. | `Clients Tracker` |
| **Verified Badge (Optional)** | `18×18px` green shield-check SVG (`#16a34a`) placed `8px` beside `<h1>` inside `.{prefix}-header-title-row` with `title="Verified ATS Module"`. | Shield Check Icon |
| **Subtitle Font & Copy** | `13px`, `font-weight: 400`, `color: #64748b`, `margin-top: 3px`, `line-height: 1.35`. Single concise sentence (< 85 characters) describing the operational scope. | `Manage client relationships, hiring activity and recruitment performance.` |
| **Icon Squircle** | `44×44px`, `border-radius: 10px`, `flex-shrink: 0`, semantic soft tint background + `1px` matching border + `22×22px` 2px-stroke Feather/Lucide SVG icon. | `#fef3c7` bg, `#fde68a` border, `#d97706` stroke |
| **Secondary Action (Export/Utility)** | White outline button (`#ffffff` bg, `1px solid #e2e8f0`, `border-radius: 8px`, `padding: 8px 16px`, `13px` weight `700` `#0f172a`), opens `270px` dropdown menu (`10px` radius) with `32×32px` tinted option icons (`csv` blue, `all` green, `pdf` red). | `Export ▾` |
| **Primary CTA Button** | JERO Amber/Gold (`#f59e0b` bg, hover `#d97706`, `border: none`, `border-radius: 8px`, `padding: 8px 20px`, `13px` weight `700`, dark ink `#0f172a` for WCAG AA contrast). | `+ Add Internal Client` |

### 3.3 Canonical Header HTML & CSS Blueprint (Replace `cl-` with Page Prefix)

```html
<!--begin::Header Card-->
<div class="cl-header cl-header-card">
    <div class="cl-header-left">
        <div class="cl-header-icon-box">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="cl-header-title-row">
                <h1 class="cl-header-title">{{ $pageTitle }}</h1>
                <span class="cl-verified-badge" title="Verified ATS Module">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4" stroke-width="2.5"/>
                    </svg>
                </span>
            </div>
            <div class="cl-header-subtitle">{{ $pageSubtitle }}</div>
        </div>
    </div>

    <div class="cl-header-actions">
        <!-- Optional Secondary Utility Dropdown (e.g. Export) -->
        <div class="cl-export-wrapper dropdown">
            <button type="button" class="cl-export-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Export</span>
                <svg class="cl-export-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm cl-export-dropdown-menu">
                <!-- Export option items -->
            </ul>
        </div>

        <!-- Primary CTA -->
        <a href="{{ $createUrl }}" class="cl-add-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>{{ $primaryCtaLabel }}</span>
        </a>
    </div>
</div>
<!--end::Header Card-->
```

```css
.cl-header-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 20px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.cl-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
.cl-header-icon-box {
  width: 44px; height: 44px; border-radius: 10px;
  background: #fef3c7; border: 1px solid #fde68a;
  display: flex; align-items: center; justify-content: center;
  color: #d97706; flex-shrink: 0;
  box-shadow: 0 1px 2px rgba(217, 119, 6, 0.06);
}
.cl-header-title-row { display: flex; align-items: center; gap: 8px; }
.cl-header-title {
  font-size: 20px; font-weight: 700; color: #0f172a;
  letter-spacing: -0.015em; margin: 0; line-height: 1.2;
}
.cl-header-subtitle {
  font-size: 13px; font-weight: 400; color: #64748b;
  margin-top: 3px; line-height: 1.35;
}
.cl-header-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.cl-export-btn {
  display: inline-flex; align-items: center; gap: 7px;
  background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;
  padding: 8px 16px; font-size: 13px; font-weight: 700; color: #0f172a;
  cursor: pointer; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04); white-space: nowrap;
  transition: all 0.15s ease;
}
.cl-export-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
.cl-add-btn-primary {
  display: inline-flex; align-items: center; gap: 6px;
  background: #f59e0b; border: none; border-radius: 8px;
  padding: 8px 20px; font-size: 13px; font-weight: 700; color: #0f172a;
  cursor: pointer; text-decoration: none; white-space: nowrap;
  box-shadow: 0 1px 3px rgba(245, 158, 11, 0.25);
  transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
}
.cl-add-btn-primary:hover {
  background: #d97706; color: #0f172a;
  box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35); transform: translateY(-0.5px);
}
```

---

## 4. Search Bar & Filter Toolbar UI/UX Design

### 4.1 Visual Layout (Single Horizontal Bar)

```
┌──────────────────────────────────────────────┬───────────────┬───────────────┬───────────────┬───────────────┬──────────────┐
│ 🔍 Search clients by name, industry...       │ 📍 Location   │ 💼 Industry   │ 📈 Activity   │ 🔽 Pipeline   │ ⇅ Sort       │
│                    [12 matches] [✕] [Ctrl+K] │ All Locs    ▾ │ All Inds    ▾ │ All Activity▾ │ All Pipeline▾ │ Recent     ▾ │
└──────────────────────────────────────────────┴───────────────┴───────────────┴───────────────┴───────────────┴──────────────┘
  .cl-search-box (flex: 1, min-width: 240px)     .cl-loc-...     .cl-ind-...     .cl-act-...     .cl-pipe-...    .cl-sort-...
  height: 42px (40px <= 1400px)                  height: 42px (40px <= 1400px), border-radius: 10px, border: 1px solid #e2e8f0
```

### 4.2 Search Bar Specification (`.{prefix}-search-box`)
* **Wrapper:** `position: relative; flex: 1; min-width: 240px;` inside `.{prefix}-filter-bar` (`display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 14px;`).
* **Left Icon:** `15×15px` SVG magnifying glass at `left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;`.
* **Input Field (`.{prefix}-search-inp`):**
  - `width: 100%; height: 42px;` (`40px` at `@media (max-width: 1400px)`).
  - `border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff; color: #0f172a; font-size: 12.5px;`.
  - Right-padding reservation: `padding: 0 100px 0 38px;` (`0 75px 0 36px` at `<=1400px`) so text never collides with the right-affix controls.
  - Focus state: `border-color: #009ef7; box-shadow: 0 0 0 2.5px rgba(0, 158, 247, 0.15);`.
  - Attributes: `autocomplete="off"`, `spellcheck="false"`, `role="searchbox"`, descriptive `aria-label`, and placeholder listing searchable fields (e.g. `"Search <entities> by name, industry, location, contact, ID..."`).
* **Right-Affix Cluster (`.{prefix}-search-right-affix`):**
  - Positioned at `right: 10px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 6px;`.
  1. **Live Match Count Pill:** `font-size: 10.5px; font-weight: 600; background: #eff6ff; color: #009ef7; border: 1px solid #dbeafe; padding: 2px 7px; border-radius: 6px;`. Hidden when query is empty; displays `"1 match"` / `"N matches"` when searching.
  2. **Interactive Clear Button (`✕`):** `19×19px` circular button (`background: #e2e8f0; color: #64748b;`, hover `#cbd5e1` / `#0f172a`). Shown only when input length > 0; clears input, hides count pill, restores `Ctrl+K` hint, triggers instant re-query, and re-focuses input.
  3. **Keyboard Shortcut Hint (`<kbd>`):** `"Ctrl+K"` (`font-size: 9.5px; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px 5px;`). Hidden while typing and hidden below `1400px` (`@media (max-width: 1400px)`).
* **Keyboard Bindings:**
  - `Ctrl+K` / `Cmd+K` (global, when focus is not inside another text input/textarea): focuses and selects search input.
  - `Escape` inside input: clears search and blurs.
  - `Enter` inside input: clears debounce timer and executes immediate query.
  - `input` event: updates clear/hint visibility immediately and invokes **30ms micro-debounce** when backed by in-RAM engine (280–300ms only if falling back to server AJAX).

### 4.3 Filter & Sort Combobox Pill Specification
Every filter pill in `.{prefix}-filter-bar` shares the exact same visual shell so the toolbar looks uniform and disciplined:

| Property | Value |
|---|---|
| **Height** | `42px` desktop (`40px` at `<=1400px`) |
| **Width** | `min-width: 145px–155px; max-width: 220px;` (`min-width: 125px` at `<=1400px`) |
| **Border & Radius** | `1px solid #e2e8f0; border-radius: 10px; background: #ffffff; padding: 0 12px; gap: 8px;` |
| **Left Icon (`.cl-pill-icon`)** | `15×15px` 2px-stroke SVG icon (`#64748b`), shifts to accent color when active |
| **Text Stack (`.cl-pill-texts`)** | Flex column (`line-height: 1.15; flex: 1; min-width: 0;`): Top label `.cl-pill-lbl` (`10px`, weight `500`, `#64748b`), Bottom value `.cl-pill-val` (`12px`, weight `600`, `#0f172a`, `text-overflow: ellipsis`) |
| **Quick Clear (`.cl-pill-clear`)** | `16×16px` circular `✕` badge shown only when filter != default; stops propagation and resets filter in 1 click |
| **Right Chevron (`.cl-pill-chevron`)** | `12×12px` downward chevron (`#94a3b8`) |
| **Active Filter State** | Blue accent (`.is-active-filter` / `.has-filter`): `border-color: #009ef7; background: #f0f9ff` (or `#f1faff`), `.cl-pill-val { color: #009ef7; font-weight: 600; }` (Location pill optionally uses warm amber `#f59e0b` / `#fffbeb` / `#b45309`) |
| **Popover Panel** | `position: absolute; top: calc(100% + 6px); width: 260px–320px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06); z-index: 1055;` |

### 4.4 Universal Filter Taxonomy & Popover Patterns (Sort, Type, Experience & Any Other Filter)
**Every filter on any applied page**—including **Sort**, **Type**, **Experience**, **Location**, **Industry**, **Activity**, **Pipeline**, **Status**, **Salary/Notice**, or **any other module filter**—**MUST** render using the exact pill shell in §4.3 and map to one of the four Clients Tracker popover archetypes below:

| Filter Dimension | Popover Archetype | Required UI/UX & Behavior (Copied from Clients Tracker) |
|---|---|---|
| **Sort** (e.g., Recently Added, Name A→Z, Experience, Salary) | **Custom WAI-ARIA Sort Combobox** | Replaces native `<select>` / Select2 completely. Uses `.{prefix}-sort-combobox-btn` + `.{prefix}-sort-dropdown-panel` (`260px` wide) with `10px` uppercase section headers (`Chronological`, `Alphabetical`, `Activity`), item icon, title, right-aligned direction pill (`Newest ↓`, `A → Z`, `High → Low`), `✓` check, `.is-custom-sorted` active pill state, and hidden `<input type="hidden">`. |
| **Type** (e.g., Internal vs Direct, Full-Time vs Contract, Bench vs Direct) | **Mutually Exclusive Facet Bucket Popover** | Uses `.{prefix}-type-combobox-btn` + `.{prefix}-type-dropdown-panel` (`285px` wide) with section header, semantic `7px` status dots, live facet count badge computed in RAM (`0`), `✓` check on `.selected`, `.is-active-filter` blue pill tint, and inline `✕` clear button. |
| **Experience** (e.g., All Exp, 0–2 Yrs, 3–5 Yrs, 6–10 Yrs, 10+ Yrs) | **Mutually Exclusive Facet Bucket Popover** | Identical shell to Activity/Pipeline/Type (`42px`/`40px` pill, 2-line label `"Experience"` + active range value, inline `✕` clear button, `285px` popover with `7px` semantic dots, live matching count badges per range bucket, and `✓` check). |
| **Location** (Country → City + Unspecified) | **Hierarchical Searchable Tree Popover** | Embedded `34px` inner search input (`border-radius: 7px`), inline match highlighting (`<b style="color:#d97706; text-decoration:underline;">`), *"All Locations"* reset + count badge, expandable hubs with nested city list, *"Location Not Specified"*, and global bundle search. |
| **Industry / Skills / High-Cardinality (>15 items)** | **Categorized Searchable Taxonomy Popover** | Embedded `34px` inner search input (`#009ef7` focus ring) + **Quick Category Chips** (`All`, top sectors) + keyword/acronym/synonym matching in RAM + `ArrowDown`/`ArrowUp`/`Enter`/`Escape` navigation with `.focused` scrollIntoView. |
| **Activity / Pipeline / Any Other Custom Filter** | **Mutually Exclusive Facet Bucket or Searchable Taxonomy Popover** | For `<= 10` options, use the Mutually Exclusive Bucket Popover (semantic dots + live RAM facet counts + `✓`). For `> 10` options, use the Searchable Taxonomy Popover (`34px` search input + quick chips). Never invent a different button height, border radius, or dropdown library. |

#### Mandatory Popover Coordination Rules
* **Peer Mutual Exclusion:** Opening any filter or sort popover automatically closes all other open popovers on the page.
* **Outside Click & `Escape` Dismissal:** Clicking outside the active wrapper or pressing `Escape` closes the popover and returns focus to its trigger button (`#...Btn`).
* **1-Click Quick Clear:** Every non-default filter pill displays the `16×16px` circular `✕` button (`.{prefix}-pill-clear`) inside the pill; clicking it calls `e.stopPropagation()` and resets that filter immediately without opening the dropdown.

---

## 5. Exact Search & Filter Technology Specification (Copied & Isolated from Clients Tracker)

All new or refactored listing pages **MUST completely copy** the client-first search and filter technology proven on the Clients Tracker page (`ClientSearchEngine` + `cl-data-worker.js` + `ClientLocationDB` / `ClientEntityDB` + `ClientStore` + `activeClientProjection`) and **strictly isolate every copied artifact to the target page** (own Blade view, own CSS file/prefix, own Web Worker file, own IndexedDB keys, and own JS namespace — zero shared mutable state with Clients Tracker).

### 5.0 Page Isolation Cloning Matrix (Mandatory When Copying to a New Page)

| Component Copied from Clients Tracker | Clients Tracker Original | Isolated Target Page Copy (Example: Jobs Tracker `jb`) |
|---|---|---|
| **CSS Prefix & Stylesheet** | `.cl-*` in `demo/css/clientList.css` | `.jb-*` in dedicated page stylesheet / `<style>` block |
| **Search Input ID & Events** | `#kt_tagify_2`, `#clSearchClearBtn`, `#clSearchMatchCount` | `#jbSearchInput`, `#jbSearchClearBtn`, `#jbSearchMatchCount` |
| **Filter & Sort Pill IDs** | `#clLocBtn`, `#clIndBtn`, `#clActBtn`, `#clPipeBtn`, `#clSortBtn` | `#jbTypeBtn`, `#jbExpBtn`, `#jbLocBtn`, `#jbSortBtn` (scoped per filter) |
| **In-RAM Search Engine** | `window.ClientSearchEngine` | `window.JobSearchEngine` (isolated IIFE, zero shared globals) |
| **Web Worker OLAP File** | `public/assets/service_company/client/cl-data-worker.js` | `public/assets/service_company/jobs/jb-data-worker.js` |
| **Bounded SWR Store** | `ClientStore` | `JobStore` (`maxEntries: 50`, `maxStaleAgeMs: 5000`) |
| **IndexedDB & ETag Keys** | `jero_client_projection:tenant=...`, `jero_cl_etag_tenant_...` | `jero_jobs_projection:tenant=...`, `jero_jb_etag_tenant_...` |
| **Elasticsearch Projection Endpoint** | `service_company.clients.activeClientProjection` | `service_company.jobs.activeJobProjection` |

> **Strict Rule:** Never point a new page at `cl-data-worker.js`, `window.ClientSearchEngine`, or `cl-*` selectors. Always clone and namespace-isolate to guarantee **0% cross-module regressions**.

### 5.1 Data Structures (User RAM)
Do **NOT** run `JSON.parse`, DOM scraping, or regex scans across raw HTML on keystrokes. Hydrate a normalized struct array once:

```javascript
// 1. Flat search index (compact primitives & pre-tokenized lowercase arrays)
let searchIndex = [];
// 2. O(1) ID -> array slot index lookup
const idSlotMap = new Map();
// 3. Tombstone bitmask (1 = active, 0 = soft-deleted) for O(1) deletes
let activeBitmask = new Uint8Array(0);
let tombstoneCount = 0;
// 4. Decoupled full DTO storage (retrieved ONLY for the final sliced page rows)
const presentationStore = new Map();
```

* **Tokenization (`tokenize(text)`):**
  ```javascript
  function tokenize(text) {
      if (!text) return [];
      return String(text)
          .toLowerCase()
          .replace(/[^\w\s-]/g, ' ')
          .split(/\s+/)
          .filter(t => t.length > 0);
  }
  ```
* **Deferred Tombstone Compaction:** When `deleteEntity(id)` pushes `tombstoneCount > Math.max(50, 0.10 * searchIndex.length)`, schedule `requestIdleCallback` (with `setTimeout(..., 100)` fallback) to rebuild `searchIndex`, `idSlotMap`, and `activeBitmask` cleanly off the critical path.

### 5.2 Single-Pass Query Pipeline (`query(params)`)
Every call to `SearchEngine.query(params)` executes these **6 deterministic stages** in one linear pass (~0.5–3ms for thousands of records):

1. **Stage 1 — Hard Filter Pruning:**
   - Skip `activeBitmask[i] === 0`.
   - Apply **all** dropdown filters (`Sort` excluded here as it runs in Stage 5) in RAM: e.g. `type`, `experience` (numeric min/max overlap or bucket), `country_id` / `city_id` / `unspecified_location`, `industry_id`, `activity` mutually exclusive age buckets (`<=7d`, `8-30d`, `31-90d`, `>90d`), `pipeline` (`active`/`none`), or any custom module filter. Any failed predicate `continue`s immediately.
2. **Stage 2 — Scoped Universe Facet & Tab Counters:**
   - For records passing the active filter predicates, increment all tab counters and **all filter popover facet counters** (e.g., `type_counts`, `experience_counts`, `recency_counts`, `pipeline_counts`) in the same single `O(N)` pass so every filter dropdown displays live authoritative counts.
3. **Stage 3 — Tab Boundary Predicate:**
   - Filter to the active KPI/stat tab (e.g., `all`, `internal`, `direct`, `hiring`, `inactive`, `request`).
4. **Stage 4 — Deterministic Multi-Field Weighted Relevance Scoring:**
   - Every query token **MUST** match at least one indexed field (`allTokensFound` strict AND across tokens).
   - Field Base Weights (Clients Tracker reference):
     - **Primary Name:** `100` (`exact: 1.00`, `startsWith: 0.80`, `tokenExact: 0.60`, `substring: 0.50`)
     - **Entity ID (`idStr`):** `90` (`exact: 1.00`, `startsWith: 0.80`)
     - **Contact / Owner:** `50` (`exact: 0.80`, `startsWith: 0.60`, `substring: 0.50`)
     - **Category / Industry:** `30` (`exact: 0.80`, `startsWith: 0.60`, `substring: 0.50`)
     - **Location (City / Country):** `20` (`exact: 0.80`, `startsWith: 0.60`, `substring: 0.50`)
     - **Entity Type / Scope:** `10` (`tokenMatch: 1.00`)
   - **Coherence Bonus (Multi-Token Queries):**
     - `+40` if all query tokens matched in Primary Name; else `+20` if all matched in Contact.
   - **Prior & Recency Tie-Breaker Boost:**
     - `+(activeJobs > 0 ? 10 : 0) + (pipelineActive > 0 ? 5 : 0) + (Math.max(0, 1 - ageDays / 180) * 0.01)`
5. **Stage 5 — Deterministic Sort:**
   - Tab-pinned priority items first if applicable (e.g., pending requests pinned to top on `all` tab).
   - If `isSearching` and `Math.abs(b.score - a.score) > 0`, sort descending by `score`.
   - Otherwise sort by selected comparator (`recent`, `oldest`, `name_asc`, `name_desc`, `jobs_desc`) with deterministic numeric `id` tie-breaker.
6. **Stage 6 — Pagination Slice & Decoupled DTO Retrieval:**
   - Slice `scoredCandidates.slice((page - 1) * perPage, from + perPage)` and map IDs via `presentationStore.get(id)`.
   - Return `{ rows, total, page, per_page, last_page, elapsed_ms, counts, recency_counts, pipeline_counts }`.

### 5.3 Guardrails: SWR Store, Monotonic Tokens, URL Sync & Degraded Mode
* **Zero-Network Guard:** When `SearchEngine.isReady && SearchEngine.size() > 0` (and current tab is hydrated locally), abort any in-flight `currentXhr`, execute `SearchEngine.query()`, store synthetic response in bounded `<Entity>Store` (`maxEntries: 50`, `maxStaleAgeMs: 5000`), render rows via `requestAnimationFrame`, and `return` with **0 HTTP calls**.
* **Circuit-Breaker AJAX Fallback:** When engine is cold or partial (e.g., dataset exceeds projection limit or server-only tab like pending invitations):
  - Increment `latestRequestId`, attach `If-None-Match` ETag header if cached, and discard out-of-order responses (`if (requestId !== Store.latestRequestId) return;`).
  - Handle `419`/`0` via silent session heartbeat recovery before showing retry UI.
* **URL State & History:** Sync `tab`, `search`, filter IDs, `sort`, and `page` via `syncUrlState()` (`window.history.pushState` / `replaceState`) and re-hydrate on `popstate`.
* **Honest Sync Banner:** If `response.projection_status === 'syncing'`, reveal `.cl-sync-banner` (`#fffbeb` background, `#fde68a` border, `#92400e` text) informing the user cached results are shown while live projection syncs.

---

## 6. File Changes & Reference Matrix

The table below maps every authoritative source file inspected for this standard (no workspace code was modified; this matrix serves as the canonical reference for AI agents).

| Layer | Reference File | Key Symbols / Selectors | Role |
|---|---|---|---|
| **Blade View & Search Engine** | `resources/views/service_company/client/index.blade.php` | `.cl-header-card`, `.cl-filter-bar`, `#kt_tagify_2`, `ClientSearchEngine`, `ClientLocationDB`, `ClientStore`, `loadClientList()` | Header card markup, filter comboboxes, keyboard shortcuts (`Ctrl+K`/`Esc`/`Enter`), 30ms debounce, in-RAM search engine |
| **Page Stylesheet** | `demo/css/clientList.css` | `.cl-header*`, `.cl-filter-bar`, `.cl-search-*`, `.cl-pill-*`, `.cl-sync-banner`, `@media (max-width: 1400px)` | Metronic Poppins styling, 42px/40px control geometry, active filter tints, 15.6" responsive rules |
| **Web Worker OLAP** | `public/assets/service_company/client/cl-data-worker.js` | `normalizeRow()`, `queryDataset()`, `tokenize()`, `EXPORT_CSV` | Off-main-thread dataset normalization, multi-field weighted scoring, and CSV export |
| **IndexedDB L2 Replica** | `public/assets/service_company/client/cl-entity-db.js` | `ClientEntityDB`, `Jero_ClientEntityDB_v2` | Hardware-adaptive LRU persistent entity cache with tenant & schema poisoning guards |
| **Backend Projection API** | `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | `activeClientProjection()`, `all_clients_data_ajax()` | Elasticsearch-backed 5,000-row projection endpoint with ETag (`304`) & Dragonfly/Redis cache |

---

## 7. 15.6" Screen Fit & Responsive Matrix

| Breakpoint / Viewport | Header Card | Search Box (`.cl-search-box`) | Filter & Sort Pills | Horizontal Overflow |
|---|---|---|---|---|
| **1920×1080 (Full HD)** | Single row: Icon + Title + Subtitle left; Export + Primary CTA right | `height: 42px`, `padding: 0 100px 0 38px`, `Ctrl+K` hint visible | `height: 42px`, `min-width: 155px`, single horizontal bar | `0px` (`overflow-x: hidden`) |
| **1366×768 / `<=1400px` (15.6" Laptop Baseline)** | Single row with tighter gaps; buttons stay inline | `height: 40px`, `padding: 0 75px 0 36px`, `Ctrl+K` hint hidden (`display: none`) to maximize typing space | `height: 40px`, `min-width: 125px`, `padding: 0 8px`, `gap: 7px` | `0px` (`flex-wrap: wrap` safety) |
| **`<=768px` (Tablet / Compact)** | Stacks `.cl-header-actions` cleanly below `.cl-header-left` | Full row width (`min-width: 240px`, `flex: 1`) | Wraps gracefully via `flex-wrap: wrap` | `0px` |

---

## 8. AI Agent Implementation & Verification Checklist

Before concluding any implementation using this standard, verify every item below:

### UI/UX & Isolation Compliance
- [ ] **Complete Copy + Page-Scoped Isolation (Rule 17):** Search/filter UI and technology are copied in full from Clients Tracker, while all CSS classes, DOM IDs, URL params, Web Worker files, IndexedDB keys, ETag keys, and JS engine objects (`<Entity>SearchEngine`, `<Entity>Store`) are strictly scoped to the target page (never reuse `cl-`, `ClientSearchEngine`, or `cl-data-worker.js` on another page).
- [ ] **Header Card:** White `#ffffff` card, `1px solid #e2e8f0`, `12px` radius, `44×44px` icon box, `20px` bold `#0f172a` title, `13px` `#64748b` subtitle, and `#f59e0b` primary CTA with `#0f172a` text.
- [ ] **Search Bar:** `42px` (`40px` at `<=1400px`) input, `#009ef7` focus ring, live match pill (`0 matches`), `✕` clear button, `Ctrl+K` hint (hidden `<=1400px`), `Escape`/`Enter`/`Ctrl+K` handlers.
- [ ] **Uniform UI/UX on ALL Filters (Sort, Type, Experience & Any Other Filter):** Every filter uses the matching `42px`/`40px` pill shell with 2-line `.pill-lbl` / `.pill-val`, inline quick-clear `✕`, `WAI-ARIA 1.2` `listbox`/`option` popovers (zero native `<select>` or Select2), peer-close behavior, ArrowUp/Down/Enter/Escape keyboard nav, and live RAM facet counters.
- [ ] **15.6" Fit:** Verified at `1366×768` and `1920×1080` with `0px` horizontal page overflow.

### Search Technology & Performance Verification
- [ ] **Zero MySQL Reads (CQRS):** Projection and fallback endpoints read exclusively from Elasticsearch + cache.
- [ ] **30ms Micro-Debounce:** Search input triggers local in-RAM query in `30ms` with **0 network requests** in Browser DevTools Network tab once hydrated.
- [ ] **ETag + IndexedDB Hydration:** Projection endpoint returns `304 Not Modified` on matching `If-None-Match` ETag; IndexedDB stores tenant-scoped snapshot safely.
- [ ] **Single-Pass Scoring & Facets:** `query()` performs hard filtering, facet counting, multi-field weighted scoring (`100/90/50/30/20/10` + coherence bonus), deterministic sort, and pagination slice in `< 5ms`.
- [ ] **Syntax & Guard Checks:** `php -l` passes with 0 errors on modified PHP files and `bash scripts/ai-guards.sh` passes before commit.
