# UI/UX Standards — Metronic, Density & Responsive Fit

> Mandatory rules for all screens. Goal: a **clean, beautiful, professional,
> Metronic-consistent, icon-led** ATS that **fits on a 15.6" laptop without zooming or
> losing data**, and **auto-adapts to every laptop resolution**.
> Hard rules: **Poppins font**, **max font size = 14px**, **no 3D / heavy graphics**.

## 0. Design direction (read first)
- **Style:** clean, modern, flat, **icon-based** beauty — crisp cards, clear spacing,
  consistent iconography, tasteful color. Professional SaaS look, not flashy.
- **❌ NO 3D.** No Three.js / WebGL / 3D scenes / 3D charts / GLTF / "talent globe" etc.
  Beauty comes from layout, whitespace, typography (Poppins), color, and **icons** —
  not from 3D or heavy graphics. Subtle 2D motion/transitions only (see §7).
- **Font:** **Poppins** everywhere. **Type cap:** nothing renders larger than **14px**.
- **Theme:** everything follows **Metronic** (below).

---

## 1. Theme: follow Metronic
- Use the existing **Metronic** components, layout, spacing, and color system
  (`metronic_assets/`, `resources/`). Do **not** introduce a parallel design language.
- Reuse Metronic cards, tables, forms, modals, buttons, toolbars, tabs, drawers,
  badges, menus. New UI = Metronic component + Tailwind tokens, not bespoke CSS.
- Keep Metronic's class conventions; extend via tokens (§3), never hardcoded px/colors.

**Icons (the visual backbone — this is an icon-led design):**
- Use **one** icon set consistently — Metronic's built-in icons (Keenicons) / the theme's
  existing set. Don't mix multiple icon libraries.
- Icons everywhere they add clarity: sidebar nav, page headers, buttons, table row
  actions, stat cards, empty states, status chips. Pair icon + short label (icon-only
  only for obvious row actions, with a tooltip).
- Consistent icon sizing (e.g. 16–20px), aligned to text baseline; color via theme tokens.
- Beautiful empty states = a friendly icon + one line + a primary action (no blank tables).
- ❌ No decorative 3D/illustration-heavy art. Clean flat icons + whitespace = the aesthetic.

---

## 2. The type scale — 14px is the CEILING
Define ONE scale; nothing exceeds 14px. Map it to Metronic + Tailwind so it's enforced.

| Token | Size | Use |
|---|---|---|
| `--fs-page-title` | **14px** | Page titles, primary headings (the max) |
| `--fs-section` | 13px | Section/card headers |
| `--fs-body` | 13px | Default body, table cells, form values |
| `--fs-label` | 12px | Field labels, table headers, secondary text |
| `--fs-meta` | 11px | Captions, hints, timestamps, chips |
| `--fs-micro` | 10.5px | Dense badges/counters (use sparingly) |

Rules:
- **No element may render > 14px**, including h1–h6 (override Metronic/Bootstrap heading
  sizes down to the scale). Add a global guard in CSS review.
- Use **`rem` with a controlled root**, not hardcoded `px`, so fluid scaling works (§4).
  With `:root{font-size:13px}`, `1rem=13px`, `14px≈1.077rem` (`--fs-page-title`).
- Line-height tight but readable: 1.35–1.45 for body, 1.25 for table rows.
- **Font: Poppins** for the whole app (load weights 400/500/600 only — keep it light).
  Set it once globally; do not mix in other families. Numeric columns →
  `font-variant-numeric: tabular-nums` for aligned figures.

```css
/* Load Poppins once (self-host or Google Fonts), weights 400/500/600. */
:root{
  --font-sans: 'Poppins', system-ui, -apple-system, "Segoe UI", sans-serif;
  --fs-page-title:14px-16px; --fs-section:13px; --fs-body:13px;
  --fs-label:12px; --fs-meta:11px; --fs-micro:10.5px;
  /* density */
  --space-1:4px; --space-2:8px; --space-3:12px; --row-h:32px; --control-h:30px;
}
html,body{font-family:var(--font-sans);font-size:var(--fs-body);}
h1,h2,h3,h4,h5,h6{font-size:var(--fs-page-title);font-weight:600;line-height:1.3;}
.table td,.table th{font-size:var(--fs-body);padding:6px 10px;height:var(--row-h);}
.form-label{font-size:var(--fs-label);} .badge{font-size:var(--fs-micro);}
```
> Also set Tailwind: `fontFamily.sans = ['Poppins', ...]` so utilities use Poppins too.

---

## 3. Density tokens (so data fits)
- **Compact spacing**: 4/8/12px scale; row height ~32px; control height ~30px.
- Buttons: small/`btn-sm` default; icon buttons for row actions.
- Cards: reduce default Metronic padding for data-dense pages (toolbar + table).
- Provide a **Comfortable / Compact** density toggle (persist per user) — Compact is
  default on ≤1366px width.

---

## 4. Fit on 15.6" + EVERY laptop (responsive strategy)

### Target resolutions (must all fit, no data loss)
| Class | Resolution | Notes |
|---|---|---|
| 15.6" budget / older | **1366 × 768** | the tightest common target — design to THIS first |
| 15.6" FHD | 1920 × 1080 | most common today |
| 14"/13" scaled | 1280–1536 wide (with 125–150% OS scaling → effective ~1024–1229) | OS scaling shrinks usable px |
| 16" / 4K scaled | 2560–3840 (scaled to ~1536–1920 effective) | don't let content sprawl |

**Design to 1366×768 (and 125% scaling ≈ 1093px effective) first.** If it fits there
with all columns, it fits everywhere.

### Techniques
1. **Fluid root font (auto-fit)** — scale the whole UI smoothly with viewport, capped at
   14px so it never exceeds the ceiling and never gets unreadably small:
   ```css
   :root{ font-size: clamp(11px, 0.55vw + 9px, 13px); } /* body scales 11→13 */
   ```
2. **Fluid layout, not fixed**: CSS Grid / flex, `%`/`fr`/`minmax()`, `max-width`
   containers; never fixed-px page widths.
3. **Sticky chrome, scroll the data**: fixed top toolbar + sticky table header +
   sticky first column; the **table body** scrolls within remaining viewport height
   (`height: calc(100vh - header - toolbar)`), so the page itself never overflows.
4. **`content-visibility:auto`** on long lists + **row virtualization** for big tables
   (render only visible rows) — essential at 100M-record scale.
5. **Container queries** for cards/widgets so they reflow by their own width, not just
   viewport.
6. **No horizontal page scroll** — only *tables* may scroll horizontally, with frozen
   key columns.

---

## 5. Data-dense tables (the heart of an ATS) — auto-fit data
DataTables/Metronic tables must show maximum useful data on any screen:
- **Responsive column priority**: assign priorities; on narrow screens, low-priority
  columns collapse into an expandable row (DataTables Responsive / `data-priority`).
- **Column visibility menu** + **per-user saved column sets** (persist).
- **Sticky header + sticky action column**; horizontal scroll only inside the table.
- **Server-side pagination + virtualization** (never load 100k rows into the DOM).
- **Truncate with tooltip** for long text (`text-overflow:ellipsis` + title), don't wrap
  and blow up row height.
- **Inline density**: tabular numerals, right-align numbers, status as compact badges.
- **Fixed, predictable row height** (`--row-h`) so virtual scroll math is stable.

---

## 6. Forms & modals
- Multi-column form grids (2–3 cols on ≥1366px, 1 col on small) to avoid long scroll.
- Labels 12px above compact inputs (30px height); group with Metronic sections/tabs.
- Heavy forms → tabs or drawers, not 2000px-tall pages.

---

## 7. Accessibility (still required at density)
- Even at 13px, maintain **WCAG AA contrast** (≥4.5:1 body, ≥3:1 large/UI).
- Min touch/click target 24×24px (use padding, not font size).
- Visible focus rings, full keyboard nav (the design:accessibility-review skill can audit).
- Respect `prefers-reduced-motion` and `prefers-color-scheme` (dark mode via Metronic).

---

## 8. Enforcement (so this doesn't drift)
- Centralize the scale/tokens in **Tailwind config + one CSS variables file**; ban
  hardcoded font px in review (`grep -rn "font-size:\s*1[5-9]px\|[2-9][0-9]px"` should be empty).
- A shared **`x-` Blade component library** (table, card, form-row, modal, toolbar, badge)
  encodes density + responsive behavior once; screens compose components.
- Add a visual-regression check at 1366×768 and 1920×1080 for key screens.
- New screen checklist: fits 1366×768 ✔ · no font >14px ✔ · table sticky+virtualized ✔ ·
  responsive column priority ✔ · AA contrast ✔.

> Net effect: one Metronic-consistent, ≤14px, ultra-dense design system that fluidly
> fits 1366×768 through 4K — data auto-fits via fluid root font, sticky chrome, scrolling
> virtualized tables, and responsive column priority.

# 9. Performance Engineering (Mandatory)

> Goal: Every screen and feature must deliver **maximum speed with minimum server load**, while maintaining a smooth UX across all supported devices.
> Performance is a **core product requirement**, not a post-development optimization.
> Any feature that increases unnecessary CPU, RAM, DB, network, browser, or queue load is considered **incomplete**.

---

## 9.1 Core Principle

All engineering decisions must optimize for:

* Minimum server CPU usage
* Minimum database load
* Minimum browser memory consumption
* Minimum network payload
* Minimum frontend rendering cost
* Maximum responsiveness under high concurrency

Every feature must be evaluated not only for UX but also for infrastructure impact.

---

## 9.2 Performance Budgets (Hard Limits)

Every screen must satisfy the following limits:

| Metric              |     Target | Hard Limit |
| ------------------- | ---------: | ---------: |
| Initial Page Load   |       < 2s |         3s |
| Time To Interactive |     < 1.5s |       2.5s |
| API Response        |    < 300ms |      800ms |
| SQL Query           |     < 50ms |      200ms |
| Initial API Calls   |        ≤ 3 |          5 |
| Browser RAM / Tab   |    < 250MB |      500MB |
| Initial JS Bundle   | < 250KB gz |      400KB |

Any screen exceeding hard limits must be optimized before release.

---

## 9.3 API Standards

Mandatory rules:

* Never return unused fields
* Always paginate large datasets
* Minimize payload size
* Use compression (gzip / brotli)
* Batch requests where possible

Forbidden:

* Over-fetching data
* Large nested payloads
* Excessive API chaining

---

## 9.4 Database Standards

Database efficiency is mandatory.

Rules:

* No N+1 queries
* Mandatory eager loading
* Mandatory indexing for searchable columns
* No full table scan on production tables
* All slow queries must be analyzed with EXPLAIN

Searchable fields must be indexed, including:

* candidate_id
* email
* phone
* skills
* location
* vendor_id
* created_at
* status

Heavy wildcard LIKE queries on large tables are forbidden.

---

## 9.5 Caching Standards

Cache must be used before hitting database whenever possible.

Mandatory cache for:

* roles
* permissions
* configuration
* dropdowns
* dashboard stats
* location data
* repeated filters

Rules:

* Cache read-heavy data
* Event-driven cache invalidation
* Prevent repetitive database reads

---

## 9.6 Search Standards

Large-scale ATS search must not rely entirely on SQL.

Required architecture:

* Structured filtering → Database
* Full-text search → Search engine
* Semantic / AI search → Vector DB

Forbidden on large datasets:

* `%keyword%` wildcard scans
* client-side filtering on huge payloads

Search latency target:
**< 500ms**

---

## 9.7 Frontend Rendering Standards

UI must remain lightweight.

Rules:

* Minimize DOM nodes
* Minimize re-renders
* Lazy load heavy modules
* Virtualize large lists
* Render only visible rows

Forbidden:

* Rendering thousands of rows
* Massive nested DOM trees
* Large synchronous computations in browser

---

## 9.8 Large Dataset UI Rules

Large dropdowns must never load full datasets.

Examples:

* locations
* skills
* companies
* vendors

Required:

* AJAX search
* Debounced requests (300–500ms)
* Minimum 2–3 characters before querying
* Load only top relevant results

Forbidden:

Loading 10,000+ options into selects.

---

## 9.9 Realtime & Notifications

Realtime systems are expensive and must be carefully controlled.

Allowed realtime use cases:

* critical notifications
* interview reminders
* chat
* live alerts

Rules:

* No polling below 30 seconds
* Prefer event-driven architecture
* Queue notifications instead of continuous polling

Preferred flow:

Event → Queue → Worker → Notification Delivery

---

## 9.10 Background Jobs

Heavy operations must never block user requests.

Queue mandatory for:

* resume parsing
* AI scoring
* indexing
* report generation
* email sending
* file processing
* bulk imports

All CPU-heavy tasks must run asynchronously.

---

## 9.11 Asset Optimization

Frontend assets must remain lean.

Rules:

* Tree-shake unused code
* Split large bundles
* Lazy load non-critical modules
* Minify JS/CSS
* Optimize images

Forbidden:

* Unused libraries
* Huge icon packs
* Large PNG assets
* Heavy animation libraries

---

## 9.12 Monitoring & Observability

Production must expose performance metrics.

Track:

* API latency
* DB latency
* cache hit ratio
* CPU usage
* memory usage
* queue delay
* websocket connections
* error rate

Alert thresholds:

* CPU > 80%
* DB latency spike
* queue delay > 30s
* cache hit ratio drop

---

## 9.13 CI/CD Performance Guardrails

No deployment without performance checks.

Mandatory automated checks:

* bundle size
* slow query detection
* Lighthouse
* memory regression
* latency regression

Deployment must fail if performance budgets are violated.

---

## 9.14 Scale Targets

System must support:

| Metric                |  Target |
| --------------------- | ------: |
| Concurrent Users      | 10,000+ |
| Candidates            |   100M+ |
| Search Latency        | < 500ms |
| Notification Delay    | < 3 sec |
| Resume Upload Success |  99.99% |

---

## Final Rule

Every feature must pass both:

* UI / UX Review
* Performance Engineering Review

A beautiful feature that increases unnecessary server load is considered incomplete.


