# UI/UX Standards

> Mandatory rules for every screen. Goal: **clean, professional, data-dense** screens that
> **perfectly fit a standard 15.6" laptop screen (1366×768 & 1920×1080) and smaller screens** without page-level horizontal scrolling.
> Font: **Poppins**. Max font size: **14px** (strictly enforced). Theme: **Metronic**. Server load: **near zero**.

---

## 1. Theme: Metronic only

Use the existing **Metronic** components, layout, spacing, and colour system (`metronic_assets/`, `resources/`).
Reuse Metronic cards, tables, forms, modals, buttons, toolbars, tabs, drawers, badges, menus.

---

## 2. Font: Poppins everywhere (Strict 14px Cap)

| Element | Size | Weight |
|---|---|---|
| Page title / primary heading | **14px** (the max cap) | 700 |
| Section / card header | 14px | 600 |
| Body text, table cells, form values | 13px | 400 |
| Field labels, table headers, secondary text | 12px | 500 |
| Captions, timestamps, chips, hints | 11px | 400 |
| Dense badges / counters (sparingly) | 10.5px | 600 |

Rules:
- **Nothing renders above 14px** — including h1-h6, table headers, badges, and inputs.
- Visual hierarchy comes from **font-weight and colour**, never size jumps.
- Poppins weights **400 / 500 / 600 / 700** only.
- Numeric columns: `font-variant-numeric: tabular-nums` for aligned figures.
- Line-height: 1.35-1.45 body, 1.25 table rows.

---

## 3. Fit on 15.6" Screen & Smaller Screens (Mandatory Responsive Standard)

Recruiters predominantly work on 15.6-inch laptops with OS display scaling (125%–150%), reducing usable viewport to **1366×768** or **1280×720**. Every layout, table, modal, and drawer must fit seamlessly without broken layouts or clipped controls.

### Target resolutions (all must fit, no data loss)
| Class | Resolution | Usable Viewport | Notes |
|---|---|---|---|
| **15.6" budget / older (Primary)** | **1366 x 768** | **~1366 x 600** (after browser chrome) | **Primary baseline — design to THIS first** |
| 15.6" FHD (100% scaling) | 1920 x 1080 | ~1920 x 900 | High density |
| 15.6" FHD (125% scaling) | 1536 x 864 | ~1536 x 700 | Standard Windows default |
| 14"/13" compact laptops | 1280 x 720 / 1280 x 800 | ~1280 x 580 | Must reflow cleanly |
| 4K displays | 2560–3840 (scaled) | ~1920 x 1080 equivalent | Guard against excessive sprawl |

### Core Layout Constraints for 15.6" Screens:
1. **Zero Page-Level Horizontal Scroll:** The `<body>` and `#kt_content` container must **NEVER** scroll horizontally. Only the inner table wrapper (`table-responsive`) may scroll horizontally.
2. **Short Vertical Height Budget (~600px usable):**
   - Modals and Drawers must define `max-height: calc(100vh - 80px)` and use internal scrolling (`overflow-y: auto`).
   - Drawers must feature a sticky header and sticky footer (`position: sticky; bottom: 0; background: #fff`) so save/action buttons are always immediately visible and clickable on short 768px screens without requiring infinite scrolling.
   - Drawer width on 1366px screens must be responsive: e.g. `w-100 w-lg-650px` or `max-width: 85vw` so the background context remains visible.
3. **Sticky Action / Floating Selection Bars:**
   - Multi-select action trays (e.g. CV selection toolbar, bulk action bar) must float cleanly at `bottom: 20px; z-index: 105;` and never hide table pagination or trigger viewport overflows.
4. **Fluid Grid / Flexbox Architecture:**
   - Use `%` / `fr` / `minmax()` and Metronic responsive grid classes (`col-12 col-md-6 col-lg-4`). Never use hardcoded pixel widths (`width: 1400px;`) on containers.
5. **Data Tables on 15.6" Screens:**
   - Sticky first column (Candidate/Job name) and sticky last column (Actions).
   - Low-priority columns collapse or use compact widths.
   - Row heights locked to 32px–36px for high data density.

---

## 4. Data-dense tables

Tables are the heart of an ATS. Every table must:
- **Responsive column priority** — assign priorities; low-priority columns collapse into expandable row on narrow screens.
- **Sticky header + sticky action column**; horizontal scroll only inside the table.
- **Server-side pagination** — never load more than one page of rows into the DOM.
- **Truncate with tooltip** for long text (`text-overflow: ellipsis` + title attribute).
- **Compact row height** ~32-36px; right-align numbers; status as compact badges.
- **Column visibility menu** — let users show/hide columns.

---

## 5. Zero / low server load (mandatory)

Every feature must minimise server impact. A beautiful feature that wastes server resources is incomplete.

### Hard limits
| Metric | Target | Hard Limit |
|---|---|---|
| Page load | < 2s | 3s |
| API response | < 300ms | 800ms |
| SQL query | < 50ms | 200ms |
| Initial API calls per page | 3 | 5 |

### Rules
- **Cache everything read-heavy** — roles, permissions, config, dropdowns, dashboard stats, location data, repeated filters. Use event-driven cache invalidation.
- **No N+1 queries** — mandatory eager loading.
- **Never return unused fields** from APIs.
- **Always paginate** large datasets.
- **Queue heavy work** — resume parsing, AI scoring, indexing, report generation, email, file processing, bulk imports. Never block user requests.
- **No full table scans** on production tables.
- **Large dropdowns** (locations, skills, companies, vendors) must use AJAX search with debounce (300-500ms), minimum 2-3 chars, top results only. Never load 10,000+ options into a select.
- **Lazy load** non-critical modules. Tree-shake unused code. Minify JS/CSS.
- **No polling below 30 seconds**. Prefer event-driven (Event > Queue > Worker > Notification).

### Database indexing required on
`candidate_id`, `email`, `phone`, `skills`, `location`, `vendor_id`, `created_at`, `status`

### Search architecture
- Structured filtering > Database
- Full-text search > Elasticsearch
- Semantic / AI search > Vector DB
- `%keyword%` wildcard scans on large tables are **forbidden**
- Search latency target: **< 500ms**

---

## 6. Forms & modals

- Multi-column form grids (2-3 cols on >= 1366px, 1 col on small).
- Labels 12px above compact inputs (30px height).
- Heavy forms > tabs or drawers, not 2000px-tall pages.

---

## 7. Accessibility

- WCAG AA contrast (>= 4.5:1 body, >= 3:1 large/UI) even at small sizes.
- Min touch/click target 24x24px (use padding, not font size).
- Visible focus rings, full keyboard nav.
- Respect `prefers-reduced-motion` and `prefers-color-scheme`.

---

## 8. New screen checklist

Before shipping any new screen, verify:

- [ ] Fits 1366x768 without horizontal page scroll
- [ ] Covers the screen — no excessive blank margins
- [ ] No font above 16px
- [ ] Poppins font everywhere
- [ ] Metronic components used
- [ ] Table has sticky header, server-side pagination
- [ ] Responsive column priority set
- [ ] Page loads in < 3s, API responses < 800ms
- [ ] No N+1 queries, no full table scans
- [ ] Large datasets paginated, heavy work queued
- [ ] AA contrast met
- [ ] Every content section uses `.section-card` or `.cardshow` (white bg, border, shadow)
- [ ] No text/data rendered directly on the `#eef2f7` page background
- [ ] Card headers have icon + title (see §10 icon colour table)

---

## 10. Card + SubCard layout system (mandatory from 2026-08-01)

All content sections — in drawers, sidebars, modals, and full pages — **must** be placed
inside a white-surface card container. Never render data text directly on the page/drawer
background (which is `#eef2f7`, a slate-gray). Text on a non-white background is an
accessibility and readability violation.

### The two card classes

| Class | Purpose |
|---|---|
| `.section-card` | Primary container for a named content block (new, preferred) |
| `.cardshow` | Legacy equivalent — same visual spec, used in existing sections |

Both share the same spec: `background:#fff`, `border:1px solid #e2e8f0`, `border-radius:12px`,
`box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.03)`.

### Structure (new sections — use `.section-card`)

```html
<div class="section-card">
  <div class="section-card-header">
    <div class="card-icon" style="background:#eff6ff;color:#2563eb;"><!-- svg icon --></div>
    <h3 class="card-title">Section Title</h3>
  </div>
  <div class="section-card-body">
    <!-- content here -->
  </div>
</div>
```

### Structure (existing sections — use `.cardshow`)

```html
<section class="cardshow">
  <div class="card-header-sidebar">
    <div class="card-icon" style="background:…;color:…;"><!-- svg icon --></div>
    <h3 class="card-title">Section Title</h3>
  </div>
  <!-- content rows follow -->
</section>
```

### Sub-cards (`.subcard`)

When items inside a card need row-level visual separation, wrap each in `.subcard`:

```html
<div class="section-card">
  <div class="section-card-header">…</div>
  <div class="subcard">Row 1 content</div>
  <div class="subcard">Row 2 content</div>
  {{-- last .subcard has no border-bottom automatically --}}
</div>
```

### Rules

- **White background (#fff) everywhere** — every card, every subcard, every section.
- **Never skip the header** — every card must have a `.section-card-header` or `.card-header-sidebar`
  with an icon (32×32, rounded-md, colored background) and a title.
- **Icon colour system** for card headers:

  | Accent | Icon bg | Icon colour | Use for |
  |---|---|---|---|
  | Blue | `#eff6ff` | `#2563eb` | Profile, Identity, Contact |
  | Green | `#ecfdf5` | `#059669` | Skills, Compensation, Positive |
  | Purple | `#f5f3ff` | `#7c3aed` | Ownership, Permissions |
  | Amber | `#fffbeb` | `#d97706` | Schedule, Warnings |
  | Red | `#fef2f2` | `#dc2626` | Errors, Mismatches |

- **Page background** — set the containing area to `background:#eef2f7` so white cards stand out.
- **Gaps** — use `16px` (`gap:16px`) between sibling cards in a flex/grid container.
- **Never** put raw paragraph text or data rows directly inside `.page-content` outside a card.

---

## 9. Data display casing conventions

Some fields have a fixed display casing regardless of how they were entered
or how they're stored — apply this **as CSS `text-transform`** (or an
uppercase transform at render time on a display-only copy), never by
mutating the underlying stored value. The value used for matching/filtering
(e.g. against `qualification_master.name`) must stay exactly as entered/
stored; only what the user *sees* changes.

| Field | Casing |
|---|---|
| Education / degree / qualification (anywhere it's shown: results tables, filter dropdowns, filter chips/badges, drawers, tooltips) | **UPPERCASE** |

When adding a new place education data is displayed, apply this rule there
too — it is not scoped to any single page.

---

## 11. Standard Search / Sort / Filter Toolbar Pattern (mandatory from 2026-08-01)

> **Reference implementation:** `resources/views/service_company/apptracker/similar_candidates.blade.php`
> (commit `b47bb88f` + `f08927e0`).
>
> Every candidate-listing page **must** use this exact toolbar pattern. Do not invent a
> new search bar, sort dropdown, or filter button design. Copy the CSS classes and HTML
> structure below verbatim; only swap out IDs/names to suit the specific page.

---

### 11.1 Visual overview

```
┌────────────────────────────────────────────────────────┬───────────────────────┬────────────────────┐
│ 🔍 Search by candidate name, company or recruiter...   │ ↕  Best Match      ▾  │  🔽 Filter (3)     │
└────────────────────────────────────────────────────────┴───────────────────────┴────────────────────┘
  .simf-search-wrap (flex: 1)                              .simf-sort-wrap (220px)   .simf-filter-btn
                                    ↓ after "Apply Filters"
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Active Filters  3                                                               🗑 Clear All  │
│  </> java ×   📍 Bangalore/Bengaluru | Karnataka | India ×   💼 0-6 Yrs ×                   │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
  .simf-applied-card  (hidden with d-none until Apply Filters is clicked)
```

---

### 11.2 CSS classes — copy these exactly into any new page `<style>` block

```css
/* ---- Toolbar card wrapper ---- */
.simf-toolbar-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 1px 2px rgba(16,24,40,.04);
    font-family: 'Poppins', sans-serif;
}

/* ---- Search + Sort shared height/border ---- */
.simf-search-wrap,
.simf-sort-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border: 1px solid #e4e6ef;
    border-radius: 10px;
    padding: 0 14px;
    height: 44px;
}
.simf-search-wrap { flex: 1 1 auto; min-width: 0; }
.simf-sort-wrap   { flex-shrink: 0; width: 220px; position: relative; }

/* ---- Search / Sort icons ---- */
.simf-search-icon,
.simf-sort-icon {
    color: #99a1b7;
    flex-shrink: 0;
    width: 16px; height: 16px;
    display: flex; align-items: center; justify-content: center;
}

/* ---- Search input ---- */
.simf-search-input.form-control {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
    padding: 0 !important; margin: 0 !important;
    font-size: 13.5px;
    line-height: 42px; height: 42px;
}

/* ---- Sort button (visible label + chevron) ---- */
.simf-sort-btn {
    display: flex; align-items: center; gap: 8px;
    width: 100%; height: 100%;
    background: transparent; border: none; padding: 0;
    font-size: 13px; font-weight: 500; color: #3f4254; text-align: left;
}
.simf-sort-btn:focus { outline: none; }
.simf-sort-btn span  { flex: 1 1 auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.simf-sort-btn .chevron { flex-shrink: 0; color: #99a1b7; transition: transform .15s; }
.simf-sort-btn.open .chevron { transform: rotate(180deg); }

/* ---- Sort dropdown panel ---- */
.simf-sort-panel {
    position: absolute; top: calc(100% + 6px); right: 0;
    min-width: 100%; width: max-content; max-width: 280px;
    background: #fff;
    border: 1px solid #eef0f4; border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(16,24,40,.12), 0 4px 6px -2px rgba(16,24,40,.06);
    padding: 6px; z-index: 60;
    max-height: 320px; overflow-y: auto;
}
.simf-sort-item { padding: 9px 12px; font-size: 12.5px; font-weight: 500; color: #3f4254; border-radius: 8px; cursor: pointer; white-space: nowrap; }
.simf-sort-item:hover  { background: #f8f9fb; }
.simf-sort-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }

/* ---- Filter button (primary blue, right-most) ---- */
.simf-filter-btn {
    height: 44px; border-radius: 10px !important;
    font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 7px;
    white-space: nowrap; padding: 0 16px !important; flex-shrink: 0;
    background: #2563eb !important;
    border: 1px solid #2563eb !important;
    color: #fff !important;
}
.simf-filter-btn:hover { background: #1d4ed8 !important; }

/* ---- Responsive breakpoints ---- */
@media (max-width: 992px) { .simf-sort-wrap { width: 160px; } }
@media (max-width: 768px)  { .simf-search-wrap { flex-basis: 100%; } .simf-sort-wrap { width: 140px; } }

/* ---- Applied Filters card (hidden until Apply is clicked) ---- */
.simf-applied-card   { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; padding: 16px 18px; margin-bottom: 16px; font-family: 'Poppins', sans-serif; }
.simf-applied-head   { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
.simf-applied-title  { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #181c32; }
.simf-applied-count  { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; min-width: 19px; height: 19px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }
.simf-applied-clearall { font-size: 12px; font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }
.simf-applied-clearall:hover { color: #b91c1c; }
.simf-applied-chips  { display: flex; flex-wrap: wrap; gap: 8px; }

/* ---- Filter chips — one colour per category ---- */
.simf-applied-chip {
    display: inline-flex; align-items: center; gap: 6px;
    border-radius: 8px; padding: 6px 10px;
    font-size: 12px; font-weight: 600;
    border: 1px solid transparent; white-space: nowrap;
}
.simf-applied-chip .ic          { display: inline-flex; align-items: center; flex-shrink: 0; }
.simf-applied-chip .star        { font-size: 12px; line-height: 1; }
.simf-applied-chip-remove       { cursor: pointer; opacity: .55; display: inline-flex; align-items: center; margin-left: 2px; }
.simf-applied-chip-remove:hover { opacity: 1; }
/* Category colour tokens — use these exact values everywhere: */
.simf-applied-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
.simf-applied-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
.simf-applied-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
.simf-applied-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.simf-applied-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
.simf-applied-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
.simf-applied-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
.simf-applied-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
.simf-applied-more { display: inline-flex; align-items: center; gap: 4px; border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600; background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; cursor: pointer; }
.simf-applied-more:hover { background: #e5e7eb; }
```

---

### 11.3 HTML structure — copy this block verbatim

```html
{{-- === Toolbar card: Search / Sort / Filter === --}}
<div class="card mb-4 simf-toolbar-card">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">

            {{-- Search --}}
            <div class="simf-search-wrap" style="position: relative;">
                <span class="simf-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </span>
                <input type="text" class="form-control simf-search-input"
                       placeholder="Search by candidate name, company or recruiter..."
                       id="kt_tagify_2" autocomplete="off" />
            </div>

            {{-- Sort (custom dropdown over a hidden <select>) --}}
            <div class="simf-sort-wrap" id="simf_sort_wrap">
                <span class="simf-sort-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/>
                    </svg>
                </span>
                <button type="button" class="simf-sort-btn" id="simf_sort_btn">
                    <span id="simf_sort_label">Best Match</span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                {{-- Real <select> stays hidden; fetchCandidates() reads its value --}}
                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                    <option value="10" selected>Best Match</option>
                    <option value="1">Newest to Oldest (Upload Date)</option>
                    <option value="2">Oldest to Newest (Upload Date)</option>
                    <option value="3">Low to High (Salary)</option>
                    <option value="4">High to Low (Salary)</option>
                    <option value="21">Notice Period (Immediate)</option>
                    <option value="22">Notice Period (Serving)</option>
                    <option value="7">High to Low (Experience)</option>
                    <option value="8">Low to High (Experience)</option>
                </select>
                <div class="simf-sort-panel d-none" id="simf_sort_panel"></div>
            </div>

            {{-- Filter button — opens the drawer --}}
            <button type="button" id="kt_similar_filter_toggle"
                    class="btn simf-filter-btn position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/>
                </svg>
                <span>Filter<span id="simf_filter_count_text"></span></span>
                <span class="badge badge-circle badge-primary simf-count-badge d-none"
                      id="simf_count_badge"
                      style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
            </button>

        </div>
    </div>
</div>

{{-- Applied Filters card — hidden (d-none) until Apply Filters is clicked --}}
<div class="simf-applied-card d-none" id="simf_applied_card">
    <div class="simf-applied-head">
        <span class="simf-applied-title">
            Active Filters
            <span class="simf-applied-count" id="simf_applied_count">0</span>
        </span>
        <a href="javascript:void(0);" class="simf-applied-clearall" id="simf_applied_clear_all">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/>
            </svg>
            Clear All
        </a>
    </div>
    <div class="simf-applied-chips" id="simf_applied_chips"></div>
</div>
```

---

### 11.4 JS wiring conventions

| Concern | Pattern |
|---|---|
| **Sort dropdown open/close** | `simfOpenSortPanel()` / `simfCloseSortPanel()` — toggle `.d-none` on `#simf_sort_panel`, add/remove `.open` on `#simf_sort_btn` |
| **Sort option selected** | Write to the hidden `<select id="sortingfilter">` value, update `#simf_sort_label` text, then call `filtersdataget()` |
| **Filter badge count** | `#simf_count_badge` — update its text and toggle `.d-none` when active filter count changes |
| **Applied Filters card** | `renderAppliedFiltersCard()` — reads filter state from hidden form fields/tagify instances, writes chips to `#simf_applied_chips`, shows/hides `#simf_applied_card` with `.d-none` |
| **Chip category icons** | Use `simfCategoryIcon(cat)` — inline SVG for skill/location/education/experience/notice/status/activity; `&#8377;` span for salary |
| **Mandatory skill star** | `&#9733;` (filled ★) for mandatory, `&#9734;` (hollow ☆) for optional — **always use HTML entities, never raw Unicode characters** (encoding safety) |
| **Free-text search trigger** | `Enter` key on search input → `fetchCandidates()` |
| **Filter apply** | Filter drawer "Apply Filters" button → `fetchCandidates(1)` + `renderAppliedFiltersCard()` |
| **Filter reset** | Reset button → clear all hidden fields → `fetchCandidates(1)` + `renderAppliedFiltersCard()` |

---

### 11.5 Filter chip category colour reference

| `cat-*` class | Background | Text | Border | Used for |
|---|---|---|---|---|
| `cat-skill` | `#f5f3ff` | `#7c3aed` | `#ede9fe` | Skill keywords |
| `cat-location` | `#ecfdf5` | `#059669` | `#d1fae5` | Cities / regions |
| `cat-education` | `#eff6ff` | `#2563eb` | `#dbeafe` | Degree / qualification (UPPERCASE) |
| `cat-experience` | `#fff7ed` | `#c2410c` | `#fed7aa` | Exp range (years) |
| `cat-salary` | `#ecfeff` | `#0e7490` | `#cffafe` | Salary range (₹ prefix via `&#8377;`) |
| `cat-notice` | `#fffbeb` | `#b45309` | `#fde68a` | Notice period / availability |
| `cat-status` | `#fdf2f8` | `#be185d` | `#fbcfe8` | Pipeline stage |
| `cat-activity` | `#f0f9ff` | `#0369a1` | `#bae6fd` | Activity / recruiter filter |

---

### 11.6 "Add to Job" button states — standard for all candidate action buttons

| State | Appearance | Notes |
|---|---|---|
| **Default** | Blue `btn btn-primary btn-sm` — "Add to Job" | Standard Metronic primary |
| **Processing** | Spinner + "Adding…" text, `disabled`, `pointer-events:none` | Bootstrap `spinner-border spinner-border-sm` |
| **Added (success)** | Green solid button — background `#16a34a`, "✓ Added", `disabled` | Never remove — swap in-place |
| **Error** | Restore to Default state | Show SweetAlert2 toast (top-end, auto-dismiss 2.6s) |

The `simfMarkCandidateRowAdded($btn)` function in the similar candidates page is the canonical implementation of this state machine.

---

### 11.7 "Added candidates stay visible" pattern

When a candidate is added to a job from a listing page, the row must remain visible in the
listing session (not disappear on pagination) using the `simfLocallyAdded[]` client-side
array pattern. Key rules:

- Track added rows in `var simfLocallyAdded = []` (in-memory, cleared on hard refresh)
- Snapshot row `outerHTML` **after** the green "Added" button is applied
- Use `unshift()` so newest added = index 0 (rendered first in the Added section)
- On every `fetchCandidates()` success: call `simfRenderAddedSection()` **after** pagination HTML is written
- Added rows appear **only on the last pagination page**, detected via `.pagi-arrow.pagi-disabled`
- Separate added rows with a `.simf-added-separator` `<tr>` (green top-border, `#f0fdf4` background)
- Added rows carry `.simf-added-row` (78% opacity ghost styling)
- **Never** include added rows in `similar_counter`, `simf_footer_number`, or any server count

---

### 11.8 Reuse checklist

Before shipping any new candidate-listing page, confirm:

- [ ] Toolbar uses `div.card.simf-toolbar-card` wrapper (not a raw div/form)
- [ ] Search input is `.simf-search-input.form-control` inside `.simf-search-wrap`
- [ ] Sort uses hidden `<select>` + custom `simf-sort-btn` / `simf-sort-panel` — no Select2 on this element
- [ ] Filter opens a drawer (not a modal) via a `.simf-filter-btn` button with badge counter
- [ ] Applied Filters card (`simf-applied-card`) hidden with `d-none` until Apply is clicked
- [ ] Filter chips use `simf-applied-chip cat-*` with the exact colour tokens from §11.5
- [ ] Star icons use HTML entities `&#9733;` / `&#9734;` — never raw Unicode `★`/`☆`
- [ ] Currency symbol uses `&#8377;` — never raw Unicode `₹`
- [ ] "Add to Job" button follows the three-state machine from §11.6
- [ ] `simfLocallyAdded[]` pattern implemented for added-row persistence (§11.7)

---

## 12. Complete Page & Blade Isolation Mandate (Strict Anti-Entanglement)

A critical failure pattern in SaaS applications is cross-module entanglement, where modifying code on one page inadvertently breaks another page. Moving forward, **strict page-level isolation is mandatory**:

1. **Dedicated Namespaces:** Every page must encapsulate its JavaScript state, handlers, and event listeners within a dedicated, page-scoped object or class (e.g., `window.CvTrackerManager`, `window.TeamViewMoreManager`, `window.ForwardResumeManager`). Never pollute global `window` scope with generic variables like `var table`, `var candidates`, or `var drawer`.
2. **Dedicated Blade Components & Drawers:** Do NOT couple or share interactive drawers or complex Blade views across disparate modules (e.g., CV Tracker, Application Tracker, Client Application, Bench Candidates). If a drawer or modal is needed on multiple pages, keep each page's implementation or controller completely isolated or interact via explicit, self-contained component APIs.
3. **No Cross-Page DOM Selectors:** Never query or bind listeners to generic DOM IDs across pages (e.g., `#kt_modal_1`, `#submit_btn`). Use namespaced IDs and classes (e.g., `#cv_tracker_submit_btn`, `#app_tracker_filter_drawer`).
4. **Zero Cross-Module Regressions:** Modifying, refactoring, or styling Page A must **NEVER** alter, break, or mutate the behavior, layout, or data flow of Page B. Before submitting any page-level changes, verify that neighboring modules remain 100% unaffected.


