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
  --fs-page-title:14px; --fs-section:13px; --fs-body:13px;
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
