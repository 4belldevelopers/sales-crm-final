# 📐 Rule 03: UI/UX 15.6" Screen Fit & Enterprise Typography Standard

> **Scope**: Always active across all Blade templates, CSS files, modals, drawers, and layouts.
> **Purpose**: Guarantees zero horizontal scroll on standard 15.6" laptops (1366×768 & 1920×1080) and enforces Metronic enterprise styling.

---

### Non-Negotiable Directives

1. **Zero Horizontal Window Overflow**:
   - Every layout must be mathematically immune to horizontal window scroll (`overflow-x: hidden` on viewport wrappers).
   - Use CSS Grid with `minmax(0, 1fr)` rather than fixed pixel widths (`width: 350px` -> use `flex: 1 1 0; min-width: 0;`).
   - Drawers must feature dedicated internal vertical scrolling:
     ```css
     .drawer-body {
       overflow-y: auto;
       max-height: calc(100vh - 120px);
     }
     ```

2. **Typography Rules**:
   - **Font Family**: Poppins strictly throughout (`font-family: 'Poppins', sans-serif;`).
   - **Font Size Scale**:
     - Body & standard text: **Maximum 14px** (13px preferred for compact lists).
     - Subtext / metadata: 11px to 12px.
     - Section headers: 14px to 16px font-weight 600.
     - Main page hero titles: 18px to 20px font-weight 700.
   - **Numbers & Counters**: Always apply `font-variant-numeric: tabular-nums;` to prevent text jitter during count changes.

3. **Color & Design Semantics**:
   - Pure white background cards: `#ffffff` with border `1px solid #e2e8f0` and border-radius `10px`.
   - Primary Active Accent: `#009ef7` with light soft background `#f1faff`.
   - Subtle status borders: `border-left: 3.5px solid #10b981` (active) / `#cbd5e1` (inactive).
   - Strictly NO 3D elements, heavy drop shadows, or unstyled default HTML tables.
