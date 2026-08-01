# UI/UX Standards

> Mandatory rules for every screen. Goal: **clean, professional, data-dense** screens that
> **fit a 15.6" laptop without scrolling** and **cover every resolution** from 1366x768 to 4K.
> Font: **Poppins**. Max pixel size: **14-16px**. Theme: **Metronic**. Server load: **near zero**.

---

## 1. Theme: Metronic only

Use the existing **Metronic** components, layout, spacing, and colour system (`metronic_assets/`, `resources/`).
Reuse Metronic cards, tables, forms, modals, buttons, toolbars, tabs, drawers, badges, menus.

---

## 2. Font: Poppins everywhere

| Element | Size | Weight |
|---|---|---|
| Page title / primary heading | **16px** (the max) | 700 |
| Section / card header | 14px | 600 |
| Body text, table cells, form values | 13px | 400 |
| Field labels, table headers, secondary text | 12px | 500 |
| Captions, timestamps, chips, hints | 11px | 400 |
| Dense badges / counters (sparingly) | 10.5px | 600 |

Rules:
- **Nothing renders above 16px** — including h1-h6.
- Visual hierarchy comes from **font-weight and colour**, not size jumps.
- Poppins weights **400 / 500 / 600 / 700** only.
- Numeric columns: `font-variant-numeric: tabular-nums` for aligned figures.
- Line-height: 1.35-1.45 body, 1.25 table rows.

---

## 3. Fit on 15.6" + every laptop (responsive)

### Target resolutions (all must fit, no data loss)
| Class | Resolution | Notes |
|---|---|---|
| 15.6" budget / older | **1366 x 768** | design to THIS first |
| 15.6" FHD | 1920 x 1080 | most common today |
| 14"/13" scaled | 1280-1536 wide (125-150% OS scaling) | OS scaling shrinks usable px |
| 16" / 4K scaled | 2560-3840 (scaled to ~1536-1920) | don't let content sprawl |

### How to make it fit
1. **Fluid layout** — CSS Grid / flex, `%` / `fr` / `minmax()`, no fixed-px page widths.
2. **Sticky header + scroll the data** — fixed top toolbar, sticky table header, sticky first column. Table body scrolls within remaining viewport height (`height: calc(100vh - header - toolbar)`).
3. **No horizontal page scroll** — only *tables* may scroll horizontally, with frozen key columns.
4. **Container queries** for cards/widgets so they reflow by their own width.
5. **Cover the screen** — tables and data grids should expand to fill available width/height, no wasted whitespace margins.

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
