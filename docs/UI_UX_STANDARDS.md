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
