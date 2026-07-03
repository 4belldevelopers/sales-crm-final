@php
    use App\Helper\GeneralMethod;
@endphp
<div class="card shadow-none rounded-0 w-100" id="offer_create_show_right_bar">
<style>
/* ═══════════════════════════════════════════════════════
   Create Offer Drawer — Full Redesign
   Font: Poppins (max 16px), clean two-column layout
═══════════════════════════════════════════════════════ */
#offer_create_show_right_bar {
    height: 100vh; display: flex; flex-direction: column;
    font-family: Poppins, sans-serif !important; overflow: hidden;
}
#offer_create_show_right_bar * { font-family: Poppins, sans-serif !important; box-sizing: border-box; }

/* ── Top title bar ── */
.co-titlebar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px; background: #fff; border-bottom: 1px solid #e8edf4;
    flex-shrink: 0;
}
.co-titlebar-left { display: flex; align-items: center; gap: 12px; }
.co-offer-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
.co-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 99px;
    background: #dcfce7; color: #16a34a;
}
.co-status-badge.draft { background: #fef3c7; color: #d97706; }
.co-titlebar-right { display: flex; align-items: center; gap: 8px; }
.co-tbtn {
    display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px;
    border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: none; font-family: Poppins, sans-serif !important; transition: all .15s;
}
.co-tbtn-ghost { background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; }
.co-tbtn-ghost:hover { background: #f1f5f9; }
.co-tbtn-primary { background: #6366f1; color: #fff; }
.co-tbtn-primary:hover { background: #4f46e5; }
.co-tbtn-success { background: #22c55e; color: #fff; }
.co-tbtn-success:hover { background: #16a34a; }
.co-tbtn-close {
    width: 32px; height: 32px; border-radius: 8px; background: #fee2e2;
    border: none; display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #dc2626; padding: 0;
}
.co-tbtn-close:hover { background: #fecaca; }
.co-tbtn svg { width: 14px; height: 14px; }
.co-tbtn-close svg { width: 16px; height: 16px; }

/* ── Breadcrumb + step nav row ── */
.co-navrow {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e8edf4;
    flex-shrink: 0;
}
.co-breadcrumb { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #94a3b8; }
.co-breadcrumb a { color: #6366f1; text-decoration: none; font-weight: 500; }
.co-breadcrumb span { color: #cbd5e1; }
.co-breadcrumb strong { color: #374151; font-weight: 600; }
.co-step-btns { display: flex; align-items: center; gap: 6px; }
.co-step-btn {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;
    border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;
    border: 1px solid #e2e8f0; background: #fff; color: #94a3b8;
    transition: all .15s; white-space: nowrap;
}
.co-step-btn.active { background: #6366f1; border-color: #6366f1; color: #fff; }
.co-step-btn.done  { background: #dcfce7; border-color: #86efac; color: #16a34a; }
.co-step-btn .co-step-num {
    width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; background: rgba(255,255,255,.3);
}
.co-step-btn.active .co-step-num { background: rgba(255,255,255,.25); }
.co-step-btn.done .co-step-num  { background: #22c55e; color: #fff; }
.co-step-sep { color: #cbd5e1; font-size: 14px; padding: 0 2px; }

/* ── Body: main + sidebar ── */
.co-body { display: flex; flex: 1; overflow: hidden; }
.co-main {
    flex: 1; overflow-y: auto; padding: 20px 22px;
    background: #f8fafc; min-width: 0;
}
.co-sidebar {
    width: 290px; flex-shrink: 0; overflow-y: auto;
    background: #fff; border-left: 1px solid #e8edf4; padding: 18px 16px;
}

/* ── Candidate info bar ── */
.co-cand-bar {
    display: flex; align-items: center; gap: 12px;
    background: #fff; border-radius: 12px; border: 1px solid #e8edf4;
    padding: 12px 16px; margin-bottom: 18px;
}
.co-cand-avatar {
    width: 42px; height: 42px; border-radius: 10px;
    background: #e0e7ff; color: #6366f1;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; flex-shrink: 0;
}
.co-cand-avatar img { width: 42px; height: 42px; border-radius: 10px; object-fit: cover; }
.co-cand-name { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
.co-cand-meta { font-size: 12px; color: #64748b; }
.co-cand-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-left: auto; }
.co-cand-tag {
    font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 5px;
    background: #f1f5f9; color: #475569;
}

/* ── Section card ── */
.co-section {
    background: #fff; border-radius: 12px; border: 1px solid #e8edf4;
    padding: 18px 20px; margin-bottom: 16px;
}
.co-section-head {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
}
.co-section-title { font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; }
.co-section-sub   { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* ── Form fields ── */
.co-form-label {
    display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;
}
.co-form-label.req::after { content: ' *'; color: #ef4444; }
.co-form-row { display: grid; gap: 14px; margin-bottom: 14px; }
.co-form-row.cols-2 { grid-template-columns: 1fr 1fr; }
.co-form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
.co-form-row.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

/* ── Stats summary bar (like the reference 100% / 0.82% / 100.82%) ── */
.co-stats-bar {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 18px;
}
.co-stat-card {
    background: #f8fafc; border-radius: 10px; border: 1px solid #e8edf4;
    padding: 12px 16px; text-align: center;
}
.co-stat-card.primary { background: #ede9fe; border-color: #c4b5fd; }
.co-stat-card.success { background: #dcfce7; border-color: #86efac; }
.co-stat-card.info    { background: #dbeafe; border-color: #93c5fd; }
.co-stat-num { font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1; }
.co-stat-card.primary .co-stat-num { color: #7c3aed; }
.co-stat-card.success .co-stat-num { color: #16a34a; }
.co-stat-card.info    .co-stat-num  { color: #2563eb; }
.co-stat-label { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 3px; }
.co-stat-hint  { font-size: 10px; margin-top: 2px; }
.co-stat-card.primary .co-stat-hint { color: #7c3aed; }
.co-stat-card.success .co-stat-hint { color: #16a34a; }
.co-stat-card.info    .co-stat-hint  { color: #2563eb; }
.co-stats-bar-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 18px; }
.co-stat-card.warning { background: #fef3c7; border-color: #fde68a; }
.co-stat-card.danger  { background: #fee2e2; border-color: #fecaca; }

/* ── Salary component section header + add button ── */
.co-comp-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
.co-comp-add-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; flex-shrink: 0;
    background: #6366f1; color: #fff; border: none; border-radius: 8px;
    font-size: 12px; font-weight: 600; cursor: pointer; font-family: Poppins, sans-serif !important;
}
.co-comp-add-btn:hover { background: #4f46e5; }

/* ── Validation banner under each component table ── */
.co-valid-banner { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; margin-top: 12px; }
.co-valid-banner svg { width: 16px; height: 16px; flex-shrink: 0; }
.co-valid-banner.ok   { background: #dcfce7; color: #16a34a; }
.co-valid-banner.warn { background: #fef3c7; color: #d97706; }
.co-valid-banner.info { background: #eff6ff; color: #2563eb; }

/* ── Clean up legacy yellow/green highlighted footer inputs ── */
#pointList tfoot input, #bonusList tfoot input, #final_pay tfoot input {
    background: #f8fafc !important; border: 1px solid #e8edf4 !important; border-radius: 6px;
    font-weight: 600; color: #0f172a;
}
#final_pay tfoot input { background: #f0fdf4 !important; border-color: #bbf7d0 !important; color: #16a34a; }

/* ── Action column: single delete icon only (adding is via the header "+Add" button) ── */
#pointList .addRowBtn, #bonusList .addRowBtnbonus { display: none !important; }
#pointList td[style*="width: 13%"], #bonusList td[style*="width: 13%"] { display: flex !important; justify-content: center; }
#pointList .remove_points, #bonusList .remove_points {
    width: 32px; height: 32px; padding: 0 !important; border-radius: 8px !important;
    background: #fee2e2 !important; border: none !important; color: #dc2626;
    display: flex !important; align-items: center; justify-content: center;
}
#pointList .remove_points:hover, #bonusList .remove_points:hover { background: #fecaca !important; }
#pointList .remove_points:disabled, #bonusList .remove_points:disabled { background: #f1f5f9 !important; color: #cbd5e1; cursor: not-allowed; }
#pointList .remove_points svg, #bonusList .remove_points svg { width: 16px; height: 16px; }

/* ── Donut chart (sidebar breakup) ── */
.co-donut-wrap { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
.co-donut { width: 76px; height: 76px; border-radius: 50%; flex-shrink: 0; position: relative; background: conic-gradient(#6366f1 0deg 360deg); }
.co-donut::after { content: ''; position: absolute; inset: 12px; background: #fff; border-radius: 50%; }
.co-donut-legend { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.co-donut-legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b; }
.co-donut-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ── Component table ── */
.co-comp-table { width: 100%; border-collapse: collapse; }
.co-comp-table th {
    padding: 8px 10px; font-size: 11px; font-weight: 600; color: #64748b;
    background: #f8fafc; text-align: left; border-bottom: 1px solid #e8edf4;
    white-space: nowrap;
}
.co-comp-table td {
    padding: 7px 10px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 12px;
}
.co-comp-table tfoot td,
.co-comp-table tfoot th {
    padding: 8px 10px; font-size: 12px; font-weight: 600; background: #fafbfc;
}
/* Preserve existing table IDs but make them use co-comp-table class */
#pointList, #bonusList, #insuranceList, #final_pay {
    width: 100%; border-collapse: collapse;
}
#pointList th, #bonusList th, #insuranceList th, #final_pay th {
    padding: 8px 10px; font-size: 11px; font-weight: 600; color: #64748b;
    background: #f8fafc; text-align: left; border-bottom: 1px solid #e8edf4;
}
#pointList td, #bonusList td, #insuranceList td, #final_pay td,
#pointList tfoot th, #bonusList tfoot th, #insuranceList tfoot th, #final_pay tfoot th {
    padding: 6px 8px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 12px;
}

/* ── Step bottom nav ── */
.co-step-nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; background: #fff; border-top: 1px solid #e8edf4;
    flex-shrink: 0; position: sticky; bottom: 0;
}
.co-nav-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 600;
    background: #f1f5f9; color: #374151; border: none; cursor: pointer; font-family: Poppins, sans-serif !important;
}
.co-nav-next {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border-radius: 9px; font-size: 13px; font-weight: 600;
    background: #6366f1; color: #fff; border: none; cursor: pointer; font-family: Poppins, sans-serif !important;
}
.co-nav-next.success { background: #22c55e; }
.co-nav-next.warning { background: #f59e0b; }
.co-nav-next:hover { opacity: .9; }

/* ── Tab content sections ── */
.tab-content-section { display: none; }
.tab-content-section.active { display: block; }

/* ── Sidebar cards ── */
.co-sb-card {
    background: #f8fafc; border-radius: 10px; border: 1px solid #e8edf4;
    padding: 14px; margin-bottom: 14px;
}
.co-sb-title {
    font-size: 11px; font-weight: 700; color: #0f172a; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: 12px;
}
.co-sb-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 5px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;
}
.co-sb-row:last-child { border-bottom: none; }
.co-sb-key { color: #64748b; font-weight: 500; }
.co-sb-val { color: #0f172a; font-weight: 600; text-align: right; max-width: 55%; }

/* Offer amount highlight */
.co-sb-amount {
    text-align: center; padding: 14px 0 10px;
}
.co-sb-amount-num { font-size: 24px; font-weight: 700; color: #0f172a; }
.co-sb-amount-label { font-size: 11px; color: #94a3b8; margin-top: 2px; }

<<<<<<< HEAD
        /* ──────────────────────────────────────────────
           Compensation drawer — Poppins + 16px hard cap
           Scoped to this drawer's root (#offer_create_show_right_bar) so it
           never leaks to the CKEditor toolbar or Swal popups (rendered outside
           this subtree). Non-blanket selector list to avoid shrinking icon glyphs.
        ────────────────────────────────────────────── */
        #offer_create_show_right_bar,
        #offer_create_show_right_bar .form-control,
        #offer_create_show_right_bar .form-select,
        #offer_create_show_right_bar .btn,
        #offer_create_show_right_bar input,
        #offer_create_show_right_bar select,
        #offer_create_show_right_bar textarea,
        #offer_create_show_right_bar p,
        #offer_create_show_right_bar span,
        #offer_create_show_right_bar td,
        #offer_create_show_right_bar a {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
        }
        #offer_create_show_right_bar h1,
        #offer_create_show_right_bar h2,
        #offer_create_show_right_bar h3 { font-size: 16px !important; font-weight: 700; }
        #offer_create_show_right_bar h4,
        #offer_create_show_right_bar h5 { font-size: 14px !important; font-weight: 600; }
        #offer_create_show_right_bar .form-label,
        #offer_create_show_right_bar label,
        #offer_create_show_right_bar th { font-size: 12px !important; }
        #offer_create_show_right_bar .badge { font-size: 11px; }
        /* Cap Metronic font-size utility classes that would otherwise exceed 16px */
        #offer_create_show_right_bar .fs-1,
        #offer_create_show_right_bar .fs-2,
        #offer_create_show_right_bar .fs-2x,
        #offer_create_show_right_bar .fs-3x,
        #offer_create_show_right_bar .fs-4x { font-size: 16px !important; }

        /* Drawer root is the positioning context for the floated close (X) button */
        #offer_create_show_right_bar { position: relative; }

        /* ── 5-step timeline stepper (acts as the header — kept compact) ── */
        .co-stepper { display: flex; align-items: flex-start; padding: 14px 44px 14px 24px; background: #fff; border-bottom: 1px solid #eff2f5; }
        .co-step { flex: 1 1 0; display: flex; flex-direction: column; align-items: center; position: relative; cursor: pointer; text-align: center; min-width: 0; }
        /* connector line between nodes */
        .co-step:not(:last-child)::after {
            content: ''; position: absolute; top: 15px; height: 2px; background: #e4e6ef; z-index: 1;
            left: calc(50% + 18px); right: calc(-50% + 18px);
        }
        .co-step--done:not(:last-child)::after { background: #50cd89; }
        .co-step-node {
            width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; z-index: 2; background: #fff; border: 2px solid #e4e6ef; color: #b5b5c3;
            transition: all .2s;
        }
        .co-step-check { display: none; }
        .co-step-num { display: inline; }
        .co-step--current .co-step-node { background: #3699ff; border-color: #3699ff; color: #fff; }
        .co-step--done .co-step-node { background: #50cd89; border-color: #50cd89; color: #fff; }
        .co-step--done .co-step-check { display: inline; }
        .co-step--done .co-step-num { display: none; }
        .co-step-label { margin-top: 6px; }
        .co-step-title { font-size: 12px; font-weight: 600; color: #b5b5c3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .co-step-status { font-size: 10px; color: #b5b5c3; margin-top: 1px; }
        .co-step--current .co-step-title { color: #181c32; }
        .co-step--current .co-step-status { color: #3699ff; }
        .co-step--done .co-step-title { color: #181c32; }
        .co-step--done .co-step-status { color: #50cd89; }
        @media (max-width: 640px) {
            .co-stepper { padding: 12px 10px; }
            .co-step-status { display: none; }
            .co-step-title { font-size: 11px; }
        }

        /* ── Components card section headings + validation banner ── */
        .co-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
        .co-section-title { font-size: 14px !important; font-weight: 700; color: #181c32; margin: 0; }
        .co-section-note { font-size: 12px; font-weight: 500; color: #a1a5b7; }
        .co-section-sub { font-size: 11px; color: #a1a5b7; margin-top: 2px; }
        .co-valid-banner {
            display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600; margin: 8px 0 12px;
        }
        .co-valid-banner .co-valid-ico { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
        /* Below 100% → red (per spec); exactly 100% → green */
        .co-valid-banner--warn { background: #fff5f8; color: #f1416c; }
        .co-valid-banner--warn .co-valid-ico { background: #f1416c; }
        .co-valid-banner--ok { background: #e8fff3; color: #17a673; }
        .co-valid-banner--ok .co-valid-ico { background: #50cd89; }
        /* Row numbering via CSS counter — no markup column added (keeps the
           salary-component array order + calc selectors untouched) */
        #pointList tbody, #bonusList tbody { counter-reset: co-rownum; }
        #pointList tbody tr.row-template:not(.d-none), #bonusList tbody tr.row-template:not(.d-none) { counter-increment: co-rownum; }

        /* ── Preview Salary Structure panel ── */
        .co-preview-card { background: #f9fafd !important; border: 1px solid #eef1f7; }
        .co-preview-eye { display: inline-block; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #7239ea; vertical-align: -2px; }
        .co-view-toggle { display: inline-flex; background: #eef1f7; border-radius: 8px; padding: 3px; gap: 2px; }
        .co-view-btn { border: none; background: transparent; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #7e8299; cursor: pointer; margin: 0; }
        .co-view-btn--active { background: #7239ea; color: #fff; }
        .co-preview-topbar { display: flex; align-items: flex-end; gap: 12px; margin: 12px 0 16px; flex-wrap: wrap; }
        .co-preview-amt { flex: 1; min-width: 160px; }
        .co-fmt-badge { background: #fff; border: 1px solid #e4e6ef; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-weight: 600; color: #3f4254; }
        .co-calc-btn { height: 40px; }
        .co-preview-grid { display: grid; grid-template-columns: 1.2fr 1fr 1.2fr; gap: 18px; }
        .co-preview-h { font-size: 12px; font-weight: 700; color: #181c32; margin-bottom: 8px; }
        .co-breakup-table { width: 100%; font-size: 12px; }
        .co-breakup-table th { color: #a1a5b7; font-weight: 600; font-size: 11px; padding: 4px 0; border-bottom: 1px solid #eef1f7; }
        .co-breakup-table td { padding: 5px 0; border-bottom: 1px dashed #eef1f7; color: #3f4254; }
        .co-breakup-total td { font-weight: 700; color: #181c32; border-top: 2px solid #e4e6ef; border-bottom: none; padding-top: 8px; }
        .co-preview-chart { display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .co-chart-legend { display: flex; flex-direction: column; gap: 4px; font-size: 12px; color: #3f4254; margin-top: 8px; }
        .co-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; vertical-align: 0; margin-right: 4px; }
        .co-dot--main { background: #7239ea; }
        .co-dot--add { background: #50cd89; }
        .co-checks { list-style: none; padding: 0; margin: 0; }
        .co-checks li { display: flex; align-items: flex-start; gap: 7px; font-size: 12px; color: #3f4254; padding: 4px 0; }
        .co-checks .co-chk { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; margin-top: 1px; background: #a1a5b7; position: relative; }
        .co-checks .co-chk--ok, .co-checks li.co-chk--pass .co-chk { background: #50cd89; }
        .co-checks li.co-chk--fail .co-chk { background: #f1416c; }
        .co-checks .co-chk::after { content: '\2713'; color: #fff; font-size: 9px; position: absolute; top: -1px; left: 3px; }
        .co-checks li.co-chk--fail .co-chk::after { content: '\21'; }
        @media (max-width: 900px) { .co-preview-grid { grid-template-columns: 1fr; } }

        /* ── Two-column layout: tables (left) + Preview panel (right sidebar) ── */
        .co-comp-layout { display: flex; flex-wrap: wrap; }
        .co-left-col { order: 1; }
        .co-preview-col { order: 2; }              /* panel is first in DOM, rendered on the right */
        .co-preview-sticky { position: sticky; top: 10px; }
        /* In the sidebar the preview panel stacks vertically (not the 3-across grid) */
        .co-preview-col .co-preview-grid { grid-template-columns: 1fr; gap: 14px; }
        .co-preview-col .co-preview-topbar { flex-direction: column; align-items: stretch; }
        .co-section-head--flex { display: flex; align-items: center; justify-content: space-between; }
        @media (max-width: 991px) {
            .co-left-col, .co-preview-col { flex: 0 0 100%; max-width: 100%; }
            .co-preview-sticky { position: static; }
            .co-preview-col .co-preview-grid { grid-template-columns: 1fr 1fr 1fr; }
        }

        /* ── Clean, modern component tables (calc reads by class, so restyle is safe) ── */
        .co-components-card { border: 1px solid #f1f1f4; border-radius: 12px; }
        #pointList, #bonusList { border-collapse: separate; border-spacing: 0; width: 100%; }
        #pointList thead th, #bonusList thead th {
            text-transform: none !important; color: #a1a5b7 !important; font-size: 11px !important;
            font-weight: 600 !important; padding: 8px 8px !important; background: transparent !important;
            border-bottom: 1px solid #f1f1f4 !important; letter-spacing: .2px;
        }
        #pointList tbody td, #bonusList tbody td {
            padding: 6px 8px !important; border-bottom: 1px solid #f4f4f6 !important; vertical-align: middle;
            width: revert !important;
        }
        #pointList tbody tr:hover, #bonusList tbody tr:hover { background: #fbfcfe; }
        /* inputs & selects read like clean inline cells */
        #pointList .form-control, #bonusList .form-control,
        #pointList .form-select, #bonusList .form-select {
            background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 7px !important;
            box-shadow: none !important; min-height: 36px; height: 36px; font-size: 13px !important;
            color: #3f4254 !important; padding: 4px 10px !important;
        }
        #pointList .offer_points, #bonusList .offer_points,
        #pointList .bonus_points, #bonusList .bonus_points {
            font-weight: 600 !important; border-color: transparent !important; background: transparent !important; padding-left: 2px !important;
        }
        /* Name cell becomes a flex row so the drag-handle/number/badge sit inline
           with the (block-level, 100%-width) name input instead of wrapping onto
           their own lines. */
        /* Give the name column generous room so # + drag + name + badge sit on ONE line,
           with a wide name input that shows the full component name (no truncation). */
        #pointList tbody td:first-child, #bonusList tbody td:first-child { min-width: 340px; }
        #pointList thead th:first-child, #bonusList thead th:first-child { min-width: 340px; }
        .co-name-cell { display: flex !important; align-items: center; gap: 6px; flex-wrap: nowrap; }
        .co-name-cell .form-control { flex: 1 1 auto; min-width: 170px; width: auto !important; }
        .co-name-cell .co-row-lead, .co-name-cell .co-comp-badge { flex: 0 0 auto; }
        .co-drag-handle { flex-shrink: 0; }
        /* Monthly / Annually preview inputs — muted, right-aligned */
        #pointList .offer_monthly, #pointList .offer_yearly,
        #bonusList .bonus_offer_monthly, #bonusList .bonus_offer_yearly {
            background: #f9fafd !important; color: #7e8299 !important; text-align: right;
        }
        /* delete → red trash button */
        #pointList .remove_points, #bonusList .remove_points {
            background: #fff5f8 !important; color: #f1416c !important; border: none !important;
            border-radius: 7px !important; width: 34px; height: 34px; padding: 0 !important; margin: 0 !important;
            display: inline-flex; align-items: center; justify-content: center;
        }
        #pointList .remove_points:hover, #bonusList .remove_points:hover { background: #f1416c !important; color: #fff !important; }
        #pointList .remove_points svg, #bonusList .remove_points svg { width: 16px; height: 16px; }
        /* Total Main/Additional (indigo tint) total rows */
        #pointList input[style*="ffeb00"], #bonusList input[style*="ffeb00"] { background: #eef3ff !important; color: #4f46e5 !important; font-weight: 700; border-color: transparent !important; }
        /* CTC — Cost to Company: render #final_pay as a distinct green CARD */
        #final_pay { background: #e8fff3; border: 1px solid #c3f0da; border-radius: 12px; margin-top: 6px; }
        #final_pay th { border: none !important; padding: 12px 10px !important; }
        #final_pay input[style*="7de96f"], #final_pay input { background: transparent !important; color: #0f9463 !important; font-weight: 700 !important; border-color: transparent !important; }
        #final_pay input[value*="CTC"] { font-size: 14px !important; }
        /* header "+ Add" buttons; hide the old in-table (+) add buttons (still click-triggered) */
        .co-add-btn { background: #eef3ff; color: #4f46e5; border: 1px solid #e0e7ff; border-radius: 8px; padding: 8px 14px !important; font-size: 12px !important; font-weight: 600; cursor: pointer; margin: 0 !important; white-space: nowrap; }
        .co-add-btn:hover { background: #4f46e5; color: #fff; }
        #pointList .addRowBtn, #bonusList .addRowBtnbonus { display: none !important; }
        /* ── Delete icon fix ──
           The button's SVG has a hardcoded fill="#000000" (and is actually a plain
           bar, not a trash can), so the CSS color override above never reached it —
           that's why it read as "missing". Hide that SVG and paint a real trash-can
           glyph via a CSS mask so it inherits currentColor (red) and hover-color (white). */
        #pointList .remove_points .svg-icon svg, #bonusList .remove_points .svg-icon svg { display: none; }
        #pointList .remove_points .svg-icon, #bonusList .remove_points .svg-icon {
            width: 16px; height: 16px; display: inline-block; background-color: currentColor;
            -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat;
            -webkit-mask-size: contain; mask-size: contain;
            -webkit-mask-position: center; mask-position: center;
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2024%2024'%3E%3Crect%20x='4'%20y='6'%20width='16'%20height='2'%20rx='1'/%3E%3Crect%20x='9'%20y='3'%20width='6'%20height='2'%20rx='1'/%3E%3Cpath%20d='M6%209h12l-1.2%2011.2A2%202%200%200114.8%2022H9.2a2%202%200%2001-2-1.8L6%209z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2024%2024'%3E%3Crect%20x='4'%20y='6'%20width='16'%20height='2'%20rx='1'/%3E%3Crect%20x='9'%20y='3'%20width='6'%20height='2'%20rx='1'/%3E%3Cpath%20d='M6%209h12l-1.2%2011.2A2%202%200%200114.8%2022H9.2a2%202%200%2001-2-1.8L6%209z'/%3E%3C/svg%3E");
        }
        /* Disabled (template) row's delete button stays visibly inert, not identical-looking-but-broken */
        #pointList .remove_points:disabled, #bonusList .remove_points:disabled { background: #f4f4f6 !important; color: #c1c5cc !important; cursor: not-allowed; }

        /* ── Row #, drag-handle, Main/Additional badge — injected by JS (coEnhanceRows) ── */
        .co-row-lead { display: inline-flex; align-items: center; gap: 6px; margin-right: 6px; vertical-align: middle; }
        .co-drag-handle { color: #c1c5cc; font-size: 12px; line-height: 1; cursor: grab; letter-spacing: -1px; user-select: none; }
        .co-row-num { color: #a1a5b7; font-size: 12px; font-weight: 600; min-width: 14px; display: inline-block; }
        .co-comp-badge { display: inline-block; font-size: 10px !important; font-weight: 700; padding: 2px 8px; border-radius: 99px; margin: 0; vertical-align: middle; white-space: nowrap; }
        .co-comp-badge--main { background: #eef3ff !important; color: #4f46e5 !important; }   /* Main = blue/indigo */
        .co-comp-badge--add  { background: #e8fff3 !important; color: #0f9463 !important; }   /* Additional = green */

        /* ── Additional-components info line ── */
        .co-info-line { font-size: 12px; color: #7239ea; font-weight: 600; margin: 6px 0 12px; }

        /* ── How it works? / Examples footer ── */
        .co-howit-box { display: flex; gap: 28px; background: #f9fafd; border: 1px solid #eef1f7; border-radius: 10px; padding: 14px 18px; margin: 14px 0; }
        .co-howit-col { flex: 1; min-width: 0; }
        .co-howit-title { font-size: 12px; font-weight: 700; color: #181c32; display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .co-howit-ico { width: 14px; height: 14px; border-radius: 4px; background: #4f46e5; display: inline-block; }
        .co-howit-text { font-size: 11px; color: #7e8299; line-height: 1.5; }
        .co-howit-formula { font-size: 11px; color: #3f4254; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .co-chip { background: #eef3ff; color: #4f46e5; border-radius: 6px; padding: 3px 8px; font-weight: 600; white-space: nowrap; }
        .co-chip--add { background: #fff3e0; color: #c2410c; }
        .co-chip--total { background: #e8fff3; color: #0f9463; }
        @media (max-width: 900px) { .co-howit-box { flex-direction: column; gap: 14px; } }
    </style>
=======
/* Validation checks */
.co-check { display: flex; align-items: center; gap: 7px; font-size: 12px; padding: 4px 0; }
.co-check svg { width: 14px; height: 14px; flex-shrink: 0; }
.co-check.ok   { color: #16a34a; }
.co-check.warn { color: #d97706; }
.co-check.err  { color: #dc2626; }

/* View toggle */
.co-view-toggle { display: flex; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 12px; }
.co-view-btn { flex: 1; padding: 6px 8px; font-size: 11px; font-weight: 600; background: #fff; border: none; cursor: pointer; color: #64748b; font-family: Poppins, sans-serif !important; }
.co-view-btn.active { background: #6366f1; color: #fff; }

/* How it works */
.co-how-works { padding: 12px 14px; background: #fff; border-radius: 10px; border: 1px solid #e8edf4; margin-top: 12px; }
.co-how-title { font-size: 11px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.co-how-flow { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
.co-how-pill { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 5px; }
.co-how-arr { color: #cbd5e1; font-size: 12px; }

/* ── Step 3: Preview ── */
#preview_offer_body_content { min-height: 300px; }

/* ── Step 4: Wait for Approval ── */
.co-approval-card {
    background: #fff; border-radius: 12px; border: 1px solid #e8edf4;
    padding: 30px 24px; text-align: center; margin-bottom: 16px;
}
.co-approval-icon {
    width: 60px; height: 60px; border-radius: 50%; background: #fef3c7;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.co-approval-title { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
.co-approval-desc  { font-size: 12px; color: #64748b; line-height: 1.6; margin-bottom: 20px; }

/* ── Step 5: Offer Sent ── */
.co-sent-card {
    background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
    border-radius: 14px; border: 1px solid #bbf7d0;
    padding: 36px 24px; text-align: center; margin-bottom: 16px;
}
.co-sent-icon {
    width: 68px; height: 68px; border-radius: 50%; background: #22c55e;
    color: #fff; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px; font-size: 30px;
}
.co-sent-title { font-size: 16px; font-weight: 700; color: #166534; margin-bottom: 8px; }
.co-sent-desc  { font-size: 13px; color: #4ade80; margin-bottom: 22px; }

/* Offer timeline (sidebar) */
.co-timeline { padding: 4px 0; }
.co-tl-item { display: flex; gap: 10px; padding-bottom: 14px; position: relative; }
.co-tl-item:not(:last-child)::before {
    content: ''; position: absolute; left: 9px; top: 20px; bottom: 0; width: 2px; background: #e2e8f0;
}
.co-tl-dot { width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #fff; }
.co-tl-dot.pending { background: #e2e8f0; color: #94a3b8; }
.co-tl-dot.active  { background: #6366f1; }
.co-tl-dot.done    { background: #22c55e; }
.co-tl-text { font-size: 12px; font-weight: 600; color: #374151; }
.co-tl-sub  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* Aliases for step 4/5 markup */
.co-section-header {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 6px;
}
.co-section-header svg { color: #6366f1; }
.co-tl-content { display: flex; flex-direction: column; }
.co-tl-label { font-size: 12px; font-weight: 600; color: #374151; }
.co-tl-dot { background: #e2e8f0; }
.co-tl-item.done .co-tl-dot { background: #22c55e; }
.co-tl-item.done .co-tl-dot::after { content: '✓'; color: #fff; font-size: 10px; }
.co-tl-item.done:not(:last-child)::before { background: #86efac; }

/* Preserve inner table CSS */
@media only screen and (min-width: 900px) and (max-width: 2560px) {
    #pointList td, #pointList th, #pointList tr,
    #bonusList td, #bonusList th, #bonusList tr,
    #insuranceList td, #insuranceList th, #insuranceList tr { width: max-content !important; }
    #pointList > :not(caption) > * > *, #bonusList > :not(caption) > * > *, #insuranceList > :not(caption) > * > * { padding: 0.6em 0.4rem !important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px) { .addRowBtnbonus { right: 17px; } }
@media only screen and (min-width: 1440px) and (max-width: 2560px) { .addRowBtnbonus { right: 39px; } }
</style>
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
    <script>
        $("timeline-item:last-child").addClass("timeline-item:last-child");
    </script>

    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
    <script>
        if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)

            CKEDITOR.tools.enableHtml5Elements(document);
        CKEDITOR.config.height = 350;
        CKEDITOR.config.width = 'auto';

        var initSample = (function() {
            var wysiwygareaAvailable = isWysiwygareaAvailable(),
                isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

            return function() {
                var editorElement = CKEDITOR.document.getById('editorterms1');

                if (isBBCodeBuiltIn) {
                    editorElement.setHtml(
                        'Hello world!\n\n' +
                        'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                    );
                }
                if (wysiwygareaAvailable) {
                    CKEDITOR.replace('editorterms1');
                } else {
                    editorElement.setAttribute('contenteditable', 'true');
                    CKEDITOR.inline('editorterms1');
                }
            };

            function isWysiwygareaAvailable() {
                if (CKEDITOR.revision == ('%RE' + 'V%')) {
                    return true;
                }
                return !!CKEDITOR.plugins.get('wysiwygarea');
            }

        })();


        initSample();
    </script>


    @php
        $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];
        $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
        $photo = ''; //url('public/avatar.png');
        if (!empty($application->photo)) {
            $photo = $application->photo;
        }
        $country_id = $country_name = $state_name = $state = $city_name = '';
        $country_id = @$application->country;
        $state = @$application->state;
        if (@$country_id > 0) {
            $country_name_Arr = GeneralMethod::getCountryName($country_id);
            foreach ($country_name_Arr as $cval) {
                $country_name = ucwords($cval['name']);
            }

            if ($country_id == 101) {
                $state_name = @$application->state_name;
                $city_name = @$application->city_name;
            } else {
                if ($state > 0) {
                    $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
                    foreach ($state_name_Arr as $sval) {
                        $state_name = ucwords($sval['name']);
                    }
                }
                $city_name = @$application->city;
            }
            $currencySymbole = GeneralMethod::currencySymbole();
            $current_ctc_cur = $current_ectc_cur = '';
            if ($application->condidate_type == 'other') {
                $current_ctc_cur = $currencySymbole[@$application->current_ctc_cur];
                $current_ectc_cur = $currencySymbole[@$application->current_ectc_cur];
            } else {
                $current_ctc_cur = $currencySymbole[@$application->bill_rate_currency];
                $current_ectc_cur = $currencySymbole[@$application->bill_rate_currency];
            }
        }
        //  rjobid from_company_id
        $currency = '';
        $confidential = '';
        $salary_ctc = '';
        $salary_mode = '';
        $designation = @$application->title;
        $joined_date = '';
        $monthly_gross = '';
        $yearly_gross = '';
        $monthly_bounus_gross = '';
        $yearly_bonus_gross = '';
        $monthly_insurence_gross = '';
        $yearly_insurence_gross = '';
        $yearly_final_gross = '';
        $monthly_final_gross = '';
        $employment_type = '';
        $view_type = '';

        if (isset($OfferJobApplication) && count($OfferJobApplication) > 0) {
            foreach ($OfferJobApplication as $offer) {
                $currency = $offer['currency'];
                $confidential = $offer['confidential'];
                $employment_type = $offer['employment_type'];
                $salary_ctc = $offer['salary_ctc'];
                $salary_mode = $offer['salary_mode'];
                $designation = $offer['designation'];
                $joined_date = $offer['joined_date'];
                $monthly_gross = $offer['monthly_gross'];
                $yearly_gross = $offer['yearly_gross'];
                $monthly_bounus_gross = $offer['monthly_bounus_gross'];
                $yearly_bonus_gross = $offer['yearly_bonus_gross'];
                $monthly_insurence_gross = $offer['monthly_insurence_gross'];
                $yearly_insurence_gross = $offer['yearly_insurence_gross'];
                $yearly_final_gross = $offer['yearly_final_gross'];
                $monthly_final_gross = $offer['monthly_final_gross'];
            }
        }
        // dd($offer_terms_conditions);
        $details = '';
       if(empty(@$offer_terms_conditions)){
         $details = "<div class=\"address-block\">
                <p><strong>To:</strong> $application->full_name </p>
                <p>Email: $application->email </p>
            </div>

            <h2>Dear  $application->full_name ,</h2>
            <p>We are excited to extend you an offer to join <strong> $application->company_name </strong> as a <strong> $application->title </strong>. We are confident that you will contribute significantly to our team and play a pivotal role in achieving our company's goals.</p>
            
            <div class=\"offer-details\">
                <p><strong>Position:</strong>   $application->title </p>
                <div class=\"start_date\"> </div>
            </div>";
       }
        

    @endphp




<<<<<<< HEAD
    {{-- Header title "Compensation" removed — the 5-step timeline is the header. The close
         (X) button is floated to the top-right corner so the header bar takes NO height. --}}
    <div class="card-header border-0" id="kt_activities_header" style="min-height:0;padding:0;position:absolute;top:8px;right:12px;z-index:6;">

        @php
            $dnone = 'd-none';
            if (
                $application->master_status_id == 8 &&
                $application->rjobid > 0 &&
                $application->from_company_id == auth()->user()->company_id
            ) {
                $dnone = '';
            } elseif (
                $application->master_status_id != 8 &&
                $application->rjobid > 0 &&
                $application->from_company_id == auth()->user()->company_id
            ) {
                $dnone = '';
            } elseif (in_array('add_schedule', $userPermissions) && $application->rjobid < 0) {
                $dnone = '';
            } elseif (
                in_array('add_schedule', $userPermissions) &&
                $application->company_id == auth()->user()->company_id
            ) {
                $dnone = '';
            } elseif (
                in_array('add_schedule', $userPermissions) &&
                $application->master_status_id == 8 &&
                $application->submitted_by == auth()->user()->id
            ) {
                $dnone = '';
            }
        @endphp

        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_offercreate_close">
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1">
                        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                            fill="currentColor">
                            <rect x="0" y="7" width="16" height="2" rx="1"></rect>
                            <rect opacity="0.5"
                                transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                x="0" y="7" width="16" height="2" rx="1"></rect>
                        </g>
                    </svg>
                </span> </button>
        </div>
    </div>

    {{-- ── 5-step timeline stepper (Compensation → Templates → Preview Offer → Approvals → Offer Sent) ──
         Purely reflects which .tab-content-section is .active; painted by syncStepHeader().
         Backward nav is free; forward nav is gated to the furthest step actually reached
         (via the real Save/Preview AJAX flow), so the header can't skip those steps. --}}
    <div class="co-stepper" id="co_offer_stepper">
        <div class="co-step co-step--current" data-step="1" onclick="goToStepNav(1)">
            <div class="co-step-node"><span class="co-step-num">1</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Compensation</div>
                <div class="co-step-status">In progress</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="2" onclick="goToStepNav(2)">
            <div class="co-step-node"><span class="co-step-num">2</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Template</div>
                <div class="co-step-status">Upcoming</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="3" onclick="goToStepNav(3)">
            <div class="co-step-node"><span class="co-step-num">3</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Preview Offer</div>
                <div class="co-step-status">Upcoming</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="4" onclick="goToStepNav(4)">
            <div class="co-step-node"><span class="co-step-num">4</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Approval</div>
                <div class="co-step-status">Upcoming</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="5" onclick="goToStepNav(5)">
            <div class="co-step-node"><span class="co-step-num">5</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Offer Sent</div>
                <div class="co-step-status">Upcoming</div>
            </div>
        </div>
    </div>

    <div class="card-body position-relative px-0 py-0" id="kt_offercreate_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="mb-5 mb-xl-5 mx-2">
                <div class="card-body pt-1 pb-0 px-1">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3 d-none">
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                                @php
                                    $str = trim(@$application->full_name);
                                    $strVal = strtolower($str[0]);
                                @endphp
                                @if (!empty($photo))
                                    <img src="{{ @$photo }}" alt="image">
                                @else
                                    <div class="symbol-label  border-radius-5">{{ ucwords(@$strVal) }}</div>
                                @endif
=======
    {{-- ── Title bar ── --}}
    <div class="co-titlebar" id="kt_activities_header">
        <div class="co-titlebar-left">
            <h1 class="co-offer-title">Create Offer</h1>
            @if($hasOffer)
                <span class="co-status-badge">
                    <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                    Active
                </span>
            @else
                <span class="co-status-badge draft">
                    <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                    Draft
                </span>
            @endif
            <span style="font-size:12px;color:#94a3b8;font-weight:500;">Create and manage the candidate offer letter</span>
        </div>
        <div class="co-titlebar-right">
            <button type="button" class="co-tbtn co-tbtn-ghost" id="kt_offercreate_close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                Close
            </button>
            @if($hasOffer)
            <button type="button" class="co-tbtn co-tbtn-ghost" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>
                Download
            </button>
            @endif
            <button type="button" class="co-tbtn co-tbtn-primary" id="co-save-draft-btn" onclick="$('#save-form').click()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Template
            </button>
            @if($hasOffer)
            <button type="button" class="co-tbtn co-tbtn-success co-send-offer-btn {{ $dnone }}"
                data-jobId="{{ @$application->job_id }}" data-candidate="{{ @$application->id }}"
                data-mtype="1" data-offerId="{{ $offer_id }}" data-toggle="modal" data-target="#fwd_offer"
                onclick="$('.fwd_offer[data-mtype=1]').first().click()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                Save &amp; Set as Default
            </button>
            @endif
        </div>
    </div>
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86

    {{-- ── Breadcrumb + 5-step nav ── --}}
    <div class="co-navrow">
        <div class="co-breadcrumb">
            <a href="{{ route('service_company.applicationoffer.index') }}">Offer Tracker</a>
            <span>›</span>
            <a href="#">Create Offer</a>
            <span>›</span>
            <strong>{{ ucwords($application->full_name ?? '') }}</strong>
        </div>
        <div class="co-step-btns" id="co-step-btns">
            <button class="co-step-btn active" data-goto="1" onclick="coGoStep(1)">
                <span class="co-step-num">1</span> Compensation
            </button>
            <span class="co-step-sep">›</span>
            <button class="co-step-btn" data-goto="2" onclick="coGoStep(2)">
                <span class="co-step-num">2</span> Offer Template
            </button>
            <span class="co-step-sep">›</span>
            <button class="co-step-btn" data-goto="3" onclick="coGoStep(3)">
                <span class="co-step-num">3</span> Preview Letter
            </button>
            <span class="co-step-sep">›</span>
            <button class="co-step-btn" data-goto="4" onclick="coGoStep(4)">
                <span class="co-step-num">4</span> Wait for Approval
            </button>
            <span class="co-step-sep">›</span>
            <button class="co-step-btn" data-goto="5" onclick="coGoStep(5)">
                <span class="co-step-num">5</span> Offer Sent
            </button>
        </div>
    </div>
    {{-- ── Two-column body ── --}}
    <div class="co-body" id="kt_offercreate_body">
        <div class="co-main" id="kt_activities_scroll">

            <!-- Step 1 -->
                            <div class="tab-content-section pt-0 active" id="step1">
                                <div class="tab-content pt-0">
                                    <form class="ajax-form" method="POST" id="createForm" name="createForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <input type="hidden" name="offer_id" value="{{ $offer_id }}">
                                        
                                            <div class="col-lg-12">
                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2">
                                                    <div class="row">
                                                         <div class="col-3 pe-0">
                                                            <label class="form-label required">Select Confidential:</label>
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" id="confidential"
                                                                name="confidential">
                                                                <option value="0"
                                                                    {{ $confidential == 0 ? 'selected' : '' }}>No</option>
                                                                <option value="1"
                                                                    {{ $confidential == 1 ? 'selected' : '' }}>Yes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3 pe-0">
                                                            <label class="form-label required">Select Currency:</label>
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" id="current_ctc_cur"
                                                                name="current_ctc_cur">
                                                                <option value="0"
                                                                    {{ $currency == 0 ? 'selected' : '' }}>INR</option>
                                                                <option value="1"
                                                                    {{ $currency == 1 ? 'selected' : '' }}>EUR</option>
                                                                <option value="2"
                                                                    {{ $currency == 2 ? 'selected' : '' }}>JPY</option>
                                                                <option value="3"
                                                                    {{ $currency == 3 ? 'selected' : '' }}>GBP</option>
                                                                <option value="4"
                                                                    {{ $currency == 4 ? 'selected' : '' }}>USD</option>
                                                                <option value="5"
                                                                    {{ $currency == 5 ? 'selected' : '' }}>AED</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3 px-2">
                                                            <label class="form-label required">Enter Amount:</label>
                                                            <input type="text" class="form-control form-control-solid"
                                                                name="ctc" id="ctc"
                                                                placeholder="Enter current CTC"
                                                                value="{{ @$salary_ctc }}">
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-3 pl-0">
                                                            <label class="form-label required">Select Salary Format:</label>
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" id="current_ctc_mode"
                                                                name="current_ctc_mode">
                                                                <option value="2" {{ $salary_mode == 2 ? 'selected' : '' }}>Hourly</option>
                                                                <option value="1" {{ $salary_mode == 1 ? 'selected' : '' }}>Monthly</option>
                                                                <option value="3" {{ $salary_mode == 3 ? 'selected' : '' }}>Annually</option>
                                                                <option value="0" {{ empty($salary_mode) || $salary_mode == 0 ? 'selected' : '' }}>Monthly/Annually</option>
                                                                <option value="4" {{ $salary_mode == 4 ? 'selected' : '' }}>Hourly/Annually</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 pe-0">
                                                            <label class="form-label required">Enter Offer Designation:</label>
                                                            <input type="text" class="form-control form-control-solid"
                                                                name="current_designation" id="current_designation"
                                                                placeholder="Enter designation"
                                                                value="{{ @$designation }}">
                                                        </div>
                                                        <div class="col-4 px-2">
                                                            <label class="form-label">Select Date & Time for Joining:</label>
                                                            <input type="datetime-local"
                                                                class="form-control form-control-solid" name="dob"
                                                                id="dob" placeholder="Enter dob"
                                                                value="{{ @$joined_date }}" onclick="this.showPicker()">
                                                        </div>
                                                        <div class="col-4 pe-0">
                                                            <label class="form-label required">Select Job Type</label>
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Job Type" id="employment_type"
                                                                name="employment_type">
                                                                <option value="0"
                                                                    {{ $employment_type == 0 ? 'selected' : '' }}>Full-time
                                                                </option>
                                                                <option value="1"
                                                                    {{ $employment_type == 1 ? 'selected' : '' }}>Contract
                                                                </option>
                                                                <option value="3"
                                                                {{ $employment_type == 3 ? 'selected' : '' }}>Part Time
                                                            </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
<<<<<<< HEAD
                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                                                    <div class="co-section-head co-section-head--flex">
                                                        <div>
                                                            <h5 class="co-section-title">Main Components <span class="co-section-note">(Must Total 100%)</span></h5>
                                                            <div class="co-section-sub">These are core components. Total percentage must be exactly 100%.</div>
                                                        </div>
                                                        <button type="button" class="co-add-btn" onclick="$('#pointList').find('.addRowBtn').first().trigger('click')">+ Add Main Component</button>
                                                    </div>
                                                    <div id="co_validation_banner" class="co-valid-banner co-valid-banner--warn">
                                                        <i class="co-valid-ico"></i>
                                                        <span id="co_validation_text">Total of main components: 0.00%</span>
                                                    </div>
                                                    <div class="row co-components-rows">
=======
                                                <div class="co-stats-bar-4" id="co-comp-stats">
                                                    <div class="co-stat-card primary">
                                                        <div class="co-stat-num" id="co-stat-main-pct">0.00%</div>
                                                        <div class="co-stat-label">Main Components</div>
                                                        <div class="co-stat-hint" id="co-stat-main-hint">Must be exactly 100%</div>
                                                    </div>
                                                    <div class="co-stat-card info">
                                                        <div class="co-stat-num" id="co-stat-add-pct">0.00%</div>
                                                        <div class="co-stat-label">Additional Components</div>
                                                        <div class="co-stat-hint">Above 100% (Allowed)</div>
                                                    </div>
                                                    <div class="co-stat-card success">
                                                        <div class="co-stat-num" id="co-stat-overall-pct">0.00%</div>
                                                        <div class="co-stat-label">Overall Structure</div>
                                                        <div class="co-stat-hint">Main + Additional</div>
                                                    </div>
                                                    <div class="co-stat-card" id="co-stat-valid-card">
                                                        <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                                            <svg id="co-valid-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                                            <span id="co-valid-text" style="font-size:13px;font-weight:700;color:#16a34a;">Valid</span>
                                                        </div>
                                                        <div class="co-stat-label" style="margin-top:6px;">Validation Status</div>
                                                        <div class="co-stat-hint" id="co-valid-hint" style="color:#16a34a;">Ready to save</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="co-section">
                                                    <div class="co-comp-header">
                                                        <div>
                                                            <div class="co-section-header" style="margin-bottom:2px;">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                                                Main Components <span style="font-weight:500;color:#94a3b8;font-size:11px;">(Must Total 100%)</span>
                                                            </div>
                                                            <div style="font-size:11px;color:#94a3b8;">These are core components. Total percentage must be exactly 100%.</div>
                                                        </div>
                                                        <button type="button" class="co-comp-add-btn" onclick="$('#pointList .addRowBtn').first().trigger('click')">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                                            Add Main Component
                                                        </button>
                                                    </div>
                                                    <div class="row">
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
                                                            role="grid" aria-describedby="myTable_info"
                                                            style="width: 100%;">
                                                            <thead>
                                                                <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                                                                    role="row">
                                                                    <th class="sorting_disabled" style="width: 25%!important">Component Name
                                                                    </th>
                                                                    <th class="sorting_disabled"
                                                                        style="width:10% !important">View</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 7% !important;">Percentage</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 3% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 12% !important;">Condition</th>
                                                                    <th class="sorting_disabled change_text_first"
                                                                        style="width: 15% !important;">Monthly</th>
                                                                    <th class="sorting_disabled change_text_second"
                                                                        style="width:15% !important">{{$first_text}}</th>
                                                                    <th class="sorting_disabled">Action</th>
                                                                </tr>
                                                                <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}"
                                                                    role="row">
                                                                    <th class="sorting_disabled"
                                                                        style="width: 47% !important;">Component Name</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 12% !important;">Percentage</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 3% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 10% !important;">Condition</th>
                                                                    <th class="sorting_disabled change_text"
                                                                        style="width:15% !important">{{$secont_text}}</th>
                                                                    <th class="sorting_disabled" style="width: 78px;">
                                                                        Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>


                                                                @if (isset($OfferJobApplication) && count($OfferJobApplication) > 0)
                                                                    @foreach ($OfferJobApplication as $key=> $offer)
                                                                        @foreach ($offer['Basic_offer_points'] as $key => $basic)
                                                                            <tr class="other_section other_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_other }}"
                                                                                role="row">
                                                                                <td class="" style="width: 25%!important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_points"
                                                                                        name="offer_point[]"
                                                                                        id="offer_point_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_poins }}"
                                                                                        placeholder="Enter Point">
                                                                                </td>
                                                                                <td class="" style="width:10% !important">
                                                                                    <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $basic->id }}" data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0" {{ $basic->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                    <option value="1" {{ $basic->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                    <option value="2" {{ $basic->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                    </select>
                                                                                </td>

                                                                                <td class="" style="width:7% !important">

                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-select-solid option_percentage"
                                                                                        name="option_percentage[]"
                                                                                        id="option_percentage_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_percentage }}"
                                                                                        placeholder="%">


                                                                                    {{-- <select &nbsp=""
                                                                                        class="form-select form-select-solid option_percentage"
                                                                                        arial-label="Select Percentage"
                                                                                        name="option_percentage[]" id="option_percentage_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}" data-offerid="{{ $basic->id }}"
                                                                                        style="width: 100px;display: initial; ">
                                                                                        <option value="">Select</option>
                                                                                        @php

                                                                                        for($i=0; $i<100; $i++){
                                                                                            @endphp 
                                                                                            <option value="{{ $i }}" {{ ($basic->basic_percentage==$i) ? 'selected'  : '' }}>{{ ($i==0) ? 'Custom' : $i.'%' }}</option>
                                                                                            @php    
                                                                                        }
                                                                                        @endphp 
                                                                                                                                                
                                                                                    </select> --}}
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                                                                <td class="" style="width:12% !important">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid option_salarymode"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="option_salarymode[]"
                                                                                        id="option_salarymode_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        style="width: 100px;display: initial; ">
                                                                                        <option value="0"
                                                                                            {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                                                                            Amount </option>
                                                                                            @if($key>0)
                                                                                            <option value="1"
                                                                                            {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                                                                            Basic</option>
                                                                                            @endif
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width:15% !important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_monthly"
                                                                                        name="offer_monthly[]"
                                                                                        id="offer_monthly_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_monthly }}"
                                                                                        placeholder="0.00">
                                                                                </td>
                                                                                <td class="" style="width:15% !important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_yearly {{ $key > 0 ? 'other_salary_get' : 'basic_salary_get' }}"
                                                                                        name="offer_yearly[]"
                                                                                        id="offer_yearly_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_yearly }}"
                                                                                        placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important;display: inline-flex;">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0"
                                                                                        {{ $key > 0 ? '' : 'disabled' }}>
                                                                                        <span
                                                                                            class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px"
                                                                                                height="24px"
                                                                                                viewBox="0 0 24 24"
                                                                                                version="1.1">
                                                                                                <g stroke="none"
                                                                                                    stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000"
                                                                                                        x="4" y="11"
                                                                                                        width="16"
                                                                                                        height="2"
                                                                                                        rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span></button>

                                                                                        <button type="button" id="addRowBtn" class="btn py-4 btn-sm btn-primary addRowBtn me-0" style="">
                                                                                            <span class="svg-icon svg-icon svg-icon me-0">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                                                        <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1">
                                                                                                        </rect>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </span>
                                                                                            </button>
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="hourly_section hourly_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                                                                role="row">
                                                                                <td class="" style="width: 47% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_point_hourly"
                                                                                        name="offer_point_hourly[]"
                                                                                        id="offer_point_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_poins }}"
                                                                                        placeholder="Enter Point">
                                                                                </td>
                                                                                <td class="" style="width: 12% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-select-solid option_percentage_hourly"
                                                                                        name="option_percentage_hourly[]"
                                                                                        id="option_percentage_hourly{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_percentage }}"
                                                                                        placeholder="%">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                </td>
                                                                                <td class="" style="width: 10% !important;">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid option_salarymode_hourly"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="option_salarymode_hourly[]"
                                                                                        id="option_salarymode_hourly{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        style="width: 100px;display: initial; ">
                                                                                        <option value="0"
                                                                                            {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                                                                            Amount </option>
                                                                                        <option value="1"
                                                                                            {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                                                                            Basic</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="hourly_section" style="width: 15% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_hourly {{ $key > 0 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                                                                        name="offer_hourly[]"
                                                                                        id="offer_hourly_{{ $basic->id }}"
                                                                                        data-id="{{ $basic->id }}"
                                                                                        data-offerid="{{ $basic->id }}"
                                                                                        value="{{ $basic->basic_yearly }}"
                                                                                        placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important ;display: inline-flex;">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0"
                                                                                        {{ $key > 0 ? '' : 'disabled' }}>
                                                                                        <span
                                                                                            class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px"
                                                                                                height="24px"
                                                                                                viewBox="0 0 24 24"
                                                                                                version="1.1">
                                                                                                <g stroke="none"
                                                                                                    stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000"
                                                                                                        x="4" y="11"
                                                                                                        width="16"
                                                                                                        height="2"
                                                                                                        rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span> 
                                                                                    </button>
                                                                                    <button type="button" id=""
                                                                                        class="btn btn-sm py-4 btn-primary addRowBtn me-0" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px" height="24px"
                                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                                        width="16" height="2"
                                                                                                        rx="1"></rect>
                                                                                                    <rect fill="#000000" 
                                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                        x="4" y="11" width="16"
                                                                                                        height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endforeach
                                                                @else
                                                                    @if ($offer_points)
                                                                        @foreach ($offer_points as $key => $value)
                                                                            <tr class="other_section other_section_row row-template {{$key > 1 ? 'remove_row' : '' }} {{ $section_other }}"
                                                                                role="row">
                                                                                <td class="" style="width: 25%!important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_points"
                                                                                        name="offer_point[]"
                                                                                        id="offer_point_{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="{{ $value->points }}"
                                                                                        placeholder="Enter Point">
                                                                                </td>
                                                                                <td class="" style="width:10% !important">
                                                                                    <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $value->id }}" data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0" {{ $value->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                    <option value="1" {{ $value->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                    <option value="2" {{ $value->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                    </select>
                                                                                </td>

                                                                                <td class="" style="width:7% !important">

                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-select-solid option_percentage"
                                                                                        name="option_percentage[]"
                                                                                        id="option_percentage_{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="{{ $value->percentage }}"
                                                                                        placeholder="%">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                                                                <td class="" style="width:12% !important">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid option_salarymode"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="option_salarymode[]"
                                                                                        id="option_salarymode_{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        style="width: 100%;display: initial; ">
                                                                                        <option value="0"
                                                                                            selected="selected">Amount
                                                                                        </option>
                                                                                        @if($key>0)
                                                                                        <option value="1">Basic</option>
                                                                                        @endif
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width:15% !important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_monthly"
                                                                                        name="offer_monthly[]"
                                                                                        id="offer_monthly_{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="" placeholder="0.00">
                                                                                </td>
                                                                                <td class="" style="width:15% !important">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_yearly {{ $key > 1 ? 'other_salary_get' : 'basic_salary_get' }}"
                                                                                        name="offer_yearly[]"
                                                                                        id="offer_yearly_{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="" placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important ;display: inline-flex;">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0"
                                                                                        {{ $key > 0 ? '' : 'disabled' }}>
                                                                                        <span
                                                                                            class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px"
                                                                                                height="24px"
                                                                                                viewBox="0 0 24 24"
                                                                                                version="1.1">
                                                                                                <g stroke="none"
                                                                                                    stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000"
                                                                                                        x="4" y="11"
                                                                                                        width="16"
                                                                                                        height="2"
                                                                                                        rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                        
                                                                                    </button>

                                                                                    <button type="button" id="addRowBtn" class="btn btn-sm btn-primary py-4 addRowBtn me-0" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                                                    <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                        </button>

                                                                                </td>
                                                                                
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    @if ($offer_point_hourly)
                                                                        @foreach ($offer_point_hourly as $key=> $value)
                                                                            <tr class="hourly_section hourly_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                                                                role="row">
                                                                                <td class=""
                                                                                    style="width: 47% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_point_hourly"
                                                                                        name="offer_point_hourly[]"
                                                                                        id="offer_point_hourly{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="{{ $value->points }}"
                                                                                        placeholder="Enter Point">
                                                                                </td>
                                                                                <td class=""
                                                                                    style="width: 12% !important;">

                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-select-solid option_percentage_hourly"
                                                                                        name="option_percentage_hourly[]"
                                                                                        id="option_percentage_hourly{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="{{ $value->percentage }}"
                                                                                        placeholder="%">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                </td>
                                                                                <td class=""
                                                                                    style="width: 10% !important;">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid option_salarymode_hourly"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="option_salarymode_hourly[]"
                                                                                        id="option_salarymode_hourly{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        style="width: 100%;display: initial; ">
                                                                                        <option value="0"
                                                                                            selected="selected">Amount
                                                                                        </option>
                                                                                        @if($key>0)
                                                                                        <option value="1">Basic
                                                                                        </option>
                                                                                        @endif
                                                                                    </select>
                                                                                </td>
                                                                                <td class=""
                                                                                    style="width: 15% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid offer_hourly {{ $key > 1 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                                                                        name="offer_hourly[]"
                                                                                        id="offer_hourly{{ $value->id }}"
                                                                                        data-id="{{ $value->id }}"
                                                                                        data-offerid="{{ $value->id }}"
                                                                                        value="" placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important ;display: inline-flex;">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0"
                                                                                        {{ $key > 0 ? '' : 'disabled' }}>
                                                                                        <span
                                                                                            class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px"
                                                                                                height="24px"
                                                                                                viewBox="0 0 24 24"
                                                                                                version="1.1">
                                                                                                <g stroke="none"
                                                                                                    stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000"
                                                                                                        x="4" y="11"
                                                                                                        width="16"
                                                                                                        height="2"
                                                                                                        rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                    <button type="button" id=""
                                                                                        class="btn btn-sm py-4 btn-primary addRowBtn me-0" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px" height="24px"
                                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                                        width="16" height="2"
                                                                                                        rx="1"></rect>
                                                                                                    <rect fill="#000000" 
                                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                        x="4" y="11" width="16"
                                                                                                        height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </button>

                                                                                </td>
                                                                                
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif

                                                                @endif


                                                            </tbody>


                                                            <tfoot class="other_section {{ $section_other }}">
                                                                <tr>
                                                                    <th colspan="8" class="sorting_disabled"
                                                                        style="width: 102px;">&nbsp; </th>
                                                                    
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input type="text" class="form-control form-control-solid" value="Total Main Components" readonly style="cursor: not-allowed;background: #ffeb0066;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalpercentage"
                                                                            name="totalpercentage"
                                                                            value=""
                                                                            id="totalpercentage" style="background: #ffeb0066;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #ffeb0066;"></th>
                                                                    <th class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalMonthly"
                                                                            name="totalMonthly"
                                                                            value="{{ @$monthly_gross }}"
                                                                            id="totalMonthly" style="background: #ffeb0066;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalYearly"
                                                                            name="totalYearly"
                                                                            value="{{ @$yearly_gross }}"
                                                                            id="totalYearly" style="background: #ffeb0066;">
                                                                    </th>
                                                                    <th class="sorting_disabled">&nbsp;</th>
                                                                </tr>
                                                            </tfoot>

                                                            <tfoot class="hourly_section {{ $section_hourly }}">
                                                                <tr>
                                                                    <th colspan="5" class="sorting_disabled"
                                                                        style="width: 102px;">&nbsp; </th>
                                                                </tr>
                                                                <tr>
                                                                    <th class="sorting_disabled">
                                                                        <input type="text" class="form-control form-control-solid" value="Total Main Components" readonly style="cursor: not-allowed;background: #ffeb0066;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalpercentagehouly"
                                                                            name="totalpercentagehouly" value="" id="totalpercentagehouly" style="background: #ffeb0066;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #ffeb0066;"></th>
                                                                    <th class="sorting_disabled" colspan="2">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalhourlyhourly"
                                                                            name="totalhourlyhourly"
                                                                            value="{{ @$yearly_gross }}"
                                                                            id="totalhourlyhourly" style="width: 88%;background: #ffeb0066;">
                                                                    </th>
                                                                </tr>
                                                            </tfoot>




                                                        </table>
                                                        <div class="co-valid-banner ok" id="co-main-valid-banner">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                                            <span id="co-main-valid-text">Total of main components is exactly 100.00%</span>
                                                        </div>
                                                    </div>
                                                </div>

<<<<<<< HEAD
                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                                                    <div class="co-section-head co-section-head--flex">
                                                        <div>
                                                            <h5 class="co-section-title">Additional Components <span class="co-section-note">(Above 100% - Optional)</span></h5>
                                                            <div class="co-section-sub">These components are additional and will be added on top of the main 100%.</div>
                                                        </div>
                                                        <button type="button" class="co-add-btn" onclick="$('#bonusList').find('.addRowBtnbonus').first().trigger('click')">+ Add Additional Component</button>
                                                    </div>
                                                    <div id="co_additional_total" class="co-info-line">Total additional components: 0.00% (Allowed above 100%)</div>
=======
                                                <div class="co-section">
                                                    <div class="co-comp-header">
                                                        <div>
                                                            <div class="co-section-header" style="margin-bottom:2px;">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                                                Additional Components <span style="font-weight:500;color:#94a3b8;font-size:11px;">(Above 100%) - Optional</span>
                                                            </div>
                                                            <div style="font-size:11px;color:#94a3b8;">These are additional and will be added on top of the main 100%.</div>
                                                        </div>
                                                        <button type="button" class="co-comp-add-btn" onclick="$('#bonusList .addRowBtnbonus').first().trigger('click')">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                                            Add Additional Component
                                                        </button>
                                                    </div>
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                                                    <div class="row">
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList"
                                                            role="grid" aria-describedby="bonusList_info"
                                                            style="width: 100%;">
                                                            <thead>
                                                                <tr class="other_section other_section_row row-template {{ $section_other }}">
                                                                    <th colspan="7" class="sorting_disabled"
                                                                        style="width: 102px;">Additional Component</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 102px;position: relative;">
                                                                        <button type="button" id="addRowBtnbonus"
                                                                        class="btn btn-primary btn-sm py-4 addRowBtnbonus me-0" style="position: absolute; top: -25px;">
                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="24px" height="24px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none"
                                                                                    fill-rule="evenodd">
                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                        width="16" height="2"
                                                                                        rx="1"></rect>
                                                                                    <rect fill="#000000"
                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                        x="4" y="11" width="16"
                                                                                        height="2" rx="1">
                                                                                    </rect>
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </button>
                                                                    </th>
                                                                </tr>
                                                                <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}">
                                                                    <th colspan="6" class="sorting_disabled other_text"
                                                                        style="width: 102px;">Additional Component</th>

                                                                        <th class="sorting_disabled"
                                                                        style="width: 102px;position: relative;">
                                                                        <button type="button" id=""
                                                                            class="btn btn-primary btn-sm py-4 me-0 addRowBtnbonus" style="position: absolute; top: -25px;">
                                                                                <span class="svg-icon svg-icon svg-icon me-0">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                        width="24px" height="24px"
                                                                                        viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1"
                                                                                            fill="none"
                                                                                            fill-rule="evenodd">
                                                                                            <rect fill="#000000" x="4" y="11"
                                                                                                width="16" height="2"
                                                                                                rx="1"></rect>
                                                                                            <rect fill="#000000" 
                                                                                                transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                x="4" y="11" width="16"
                                                                                                height="2" rx="1">
                                                                                            </rect>
                                                                                        </g>
                                                                                    </svg>
                                                                                </span>
                                                                        </button>
                                                                    </th>
                                                                </tr>
                                                            </thead>


                                                            <tbody>
                                                                @if(isset($OfferJobApplication) && count($OfferJobApplication) > 0)
                                                                    @foreach($OfferJobApplication as $offer)
                                                                        @if(isset($offer['Bonus_offer_points']) && count($offer['Bonus_offer_points']) > 0)
                                                                            @foreach($offer['Bonus_offer_points'] as $bonus)


                                                                            <tr class="other_section other_section_row row-template remove_row {{ $section_other }}" role="row">
                                                                                <td class="" style="width: 25% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid bonus_points"
                                                                                        name="bonus_points[]"
                                                                                        id="bonus_points_{{ $bonus->id }}"
                                                                                        data-id="{{ $bonus->id }}"
                                                                                        data-offerid="{{ $bonus->id }}"
                                                                                        value="{{ $bonus->bonus_poins }}" placeholder="Enter Point">
                                                                                </td>
                                                                                
                                                                                <td class="" style="width: 10% !important;">
                                                                                    <select &nbsp="" class="form-select form-select-solid bonus_view" arial-label="Select  View" name="bonus_view[]" id="bonus_view_{{ $bonus->id }}" data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0" {{ @$bonus->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                    <option value="1" {{ @$bonus->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                    <option value="2" {{ @$bonus->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width: 7% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid bonus_option_percentage"
                                                                                    name="bonus_option_percentage[]"
                                                                                    id="bonus_option_percentage{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_percentage }}" placeholder="%">
            
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;">
                                                                                    <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                </td>
                                                                            
                                                                                <td class="" style="width: 12% !important;">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid bonus_option_salarymode"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="bonus_option_salarymode[]" id="bonus_option_salarymode_{{ $bonus->id }}"
                                                                                        data-id="{{ $bonus->id }}" data-offerid="{{ $bonus->id }}"
                                                                                        style="width: 100%;display: initial; ">
                                                                                        <option value="0" {{ ($bonus->bonus_condition_s==0) ? 'selected'  : '' }}>Amount </option>
                                                                                        <option value="1" {{ ($bonus->bonus_condition_s==1) ? 'selected'  : '' }}>Basic</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width: 15% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_monthly"
                                                                                    name="bonus_offer_monthly[]"
                                                                                    id="bonus_offer_monthly_{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_monthly }}" placeholder="0.00">
                                                                                </td>
                                                                                <td class="" style="width: 15% !important;"> 
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_yearly"
                                                                                    name="bonus_offer_yearly[]"
                                                                                    id="bonus_offer_yearly{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_yearly }}" placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important;display: inline-flex;">
                                                                                    <button type="button" class="btn btn-sm btn-light-danger py-4 font-weight-bolder remove_points me-0"> 
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                                        </g>
                                                                                        </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                    <button type="button" id="addRowBtnbonus"
                                                                                        class="btn btn-sm py-4 btn-primary addRowBtnbonus me-0" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px" height="24px"
                                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                                        width="16" height="2"
                                                                                                        rx="1"></rect>
                                                                                                    <rect fill="#000000"
                                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                        x="4" y="11" width="16"
                                                                                                        height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
            
                                                                            <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}" role="row">
                                                                                <td class="" style="width: 47% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid bonus_point_hourly"
                                                                                        name="bonus_point_hourly[]"
                                                                                        id="bonus_points_hourly{{ $bonus->id }}"
                                                                                        data-id="{{ $bonus->id }}"
                                                                                        data-offerid="{{ $bonus->id }}"
                                                                                        value="{{ $bonus->bonus_poins }}" placeholder="Enter Point">
                                                                                </td>
                                                                                <td class="" style="width: 12% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid bonus_option_percentage_hourly"
                                                                                    name="bonus_option_percentage_hourly[]"
                                                                                    id="bonus_option_percentage_hourly{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_percentage }}" placeholder="%">
            
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                </td>

                                                                                <td class="" style="width: 10% !important;">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid bonus_option_salarymode_hourly"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="bonus_option_salarymode_hourly[]" id="bonus_option_salarymode_hourly{{ $bonus->id }}"
                                                                                        data-id="{{ $bonus->id }}" data-offerid="{{ $bonus->id }}"
                                                                                        style="width: 100px;display: initial; ">
                                                                                        <option value="0" {{ ($bonus->bonus_condition_s==0) ? 'selected'  : '' }}>Amount </option>
                                                                                        <option value="1" {{ ($bonus->bonus_condition_s==1) ? 'selected'  : '' }}>Basic</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width: 15% !important;"> 
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_hourly"
                                                                                    name="bonus_offer_hourly[]"
                                                                                    id="bonus_offer_hourly{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_yearly }}" placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important;display: inline-flex;">
                                                                                    <button type="button" class="btn btn-sm btn-light-danger py-4 font-weight-bolder remove_points me-0"> 
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                                        </g>
                                                                                        </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                    <button type="button" id=""
                                                                                        class="btn btn-sm py-4 btn-primary me-0 addRowBtnbonus" style="">
                                                                                            <span class="svg-icon svg-icon svg-icon me-0">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                    width="24px" height="24px"
                                                                                                    viewBox="0 0 24 24" version="1.1">
                                                                                                    <g stroke="none" stroke-width="1"
                                                                                                        fill="none"
                                                                                                        fill-rule="evenodd">
                                                                                                        <rect fill="#000000" x="4" y="11"
                                                                                                            width="16" height="2"
                                                                                                            rx="1"></rect>
                                                                                                        <rect fill="#000000" 
                                                                                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                            x="4" y="11" width="16"
                                                                                                            height="2" rx="1">
                                                                                                        </rect>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </span>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            @endforeach
                                                                            @endif
                                                                        @endforeach

                                                                @else                                                            
                                                                    @if(isset($offer_point_other) && count($offer_point_other) > 0)
                                                                        @foreach ($offer_point_other as $offer)
                                                                            <tr class="other_section other_section_row row-template remove_row {{ $section_other }}"
                                                                                role="row">
                                                                                <td class="" style="width: 25% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid bonus_points"
                                                                                        name="bonus_points[]"
                                                                                        id="bonus_points_{{ $offer->id }}"
                                                                                        data-id="{{ $offer->id }}"
                                                                                        data-offerid="{{ $offer->id }}"
                                                                                        value="{{ $offer->points }}"
                                                                                        placeholder="Enter Point">
                                                                                </td>
                                                                                <td class="" style="width: 10% !important;">
                                                                                    <select &nbsp="" class="form-select form-select-solid bonus_view" arial-label="Select  View" name="bonus_view[]" id="bonus_view_{{ $offer->id }}" data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0" {{ @$offer->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                    <option value="1" {{ @$offer->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                    <option value="2" {{ @$offer->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                    </select>
                                                                                </td>


                                                                                <td class="" style="width: 7% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-select-solid bonus_option_percentage"
                                                                                        name="bonus_option_percentage[]"
                                                                                        id="bonus_option_percentage{{ $offer->id }}"
                                                                                        data-id="{{ $offer->id }}"
                                                                                        data-offerid="{{ $offer->id }}"
                                                                                        value="{{ $offer->percentage }}"
                                                                                        placeholder="%">

                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;">
                                                                                    <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                </td>
                                                                                <td class="" style="width: 12% !important;">
                                                                                    <select &nbsp=""
                                                                                        class="form-select form-select-solid bonus_option_salarymode"
                                                                                        arial-label="Select Annual Salary"
                                                                                        name="bonus_option_salarymode[]"
                                                                                        id="bonus_option_salarymode_{{ $offer->id }}"
                                                                                        data-id="{{ $offer->id }}"
                                                                                        data-offerid="{{ $offer->id }}"
                                                                                        style="width: 100%;display: initial; ">
                                                                                        <option value="0"
                                                                                            {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                                                                            Amount </option>
                                                                                        <option value="1"
                                                                                            {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                                                                            Basic</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width: 15% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid bonus_offer_monthly"
                                                                                        name="bonus_offer_monthly[]"
                                                                                        id="bonus_offer_monthly_{{ $offer->id }}"
                                                                                        data-id="{{ $offer->id }}"
                                                                                        data-offerid="{{ $offer->id }}"
                                                                                        value=""
                                                                                        placeholder="0.00">
                                                                                </td>
                                                                                <td class="" style="width: 15% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                        class="form-control form-control-solid bonus_offer_yearly"
                                                                                        name="bonus_offer_yearly[]"
                                                                                        id="bonus_offer_yearly{{ $offer->id }}"
                                                                                        data-id="{{ $offer->id }}"
                                                                                        data-offerid="{{ $offer->id }}"
                                                                                        value=""
                                                                                        placeholder="0.00">
                                                                                </td>
                                                                                <td style="width: 13% !important">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-light-danger font-weight-bolder remove_points py-4 me-0">
                                                                                        <span
                                                                                            class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px"
                                                                                                height="24px"
                                                                                                viewBox="0 0 24 24"
                                                                                                version="1.1">
                                                                                                <g stroke="none"
                                                                                                    stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000"
                                                                                                        x="4" y="11"
                                                                                                        width="16"
                                                                                                        height="2"
                                                                                                        rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span></button>
                                                                                        <button type="button" id="addRowBtnbonus"
                                                                                        class="btn btn-sm btn-primary addRowBtnbonus py-4 me-0" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px" height="24px"
                                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                                        width="16" height="2"
                                                                                                        rx="1"></rect>
                                                                                                    <rect fill="#000000"
                                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                        x="4" y="11" width="16"
                                                                                                        height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    @if(isset($offer_point_other_hourly) && count($offer_point_other_hourly) > 0)
                                                                            @foreach ($offer_point_other_hourly as $offer)
                                                                                <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}"
                                                                                    role="row">
                                                                                    <td class=""
                                                                                        style="width: 47% !important;">
                                                                                        <input &nbsp="" type="text"
                                                                                            class="form-control form-control-solid bonus_point_hourly"
                                                                                            name="bonus_point_hourly[]"
                                                                                            id="bonus_points_hourly{{ $offer->id }}"
                                                                                            data-id="{{ $offer->id }}"
                                                                                            data-offerid="{{ $offer->id }}"
                                                                                            value="{{ $offer->points }}"
                                                                                            placeholder="Enter Point">
                                                                                    </td>
                                                                                    <td class=""
                                                                                        style="width: 12% !important;">
                                                                                        <input &nbsp="" type="text"
                                                                                            class="form-control form-select-solid bonus_option_percentage_hourly"
                                                                                            name="bonus_option_percentage_hourly[]"
                                                                                            id="bonus_option_percentage_hourly{{ $offer->id }}"
                                                                                            data-id="{{ $offer->id }}"
                                                                                            data-offerid="{{ $offer->id }}"
                                                                                            value="{{ $offer->percentage }}"
                                                                                            placeholder="%">
                                                                                    </td>
                                                                                    <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                                    </td>
                                                                                    <td class=""
                                                                                        style="width: 10% !important;">
                                                                                        <select &nbsp=""
                                                                                            class="form-select form-select-solid bonus_option_salarymode_hourly"
                                                                                            arial-label="Select Annual Salary"
                                                                                            name="bonus_option_salarymode_hourly[]"
                                                                                            id="bonus_option_salarymode_hourly{{ $offer->id }}"
                                                                                            data-id="{{ $offer->id }}"
                                                                                            data-offerid="{{ $offer->id }}"
                                                                                            style="width: 100px;display: initial; ">
                                                                                            <option value="0"
                                                                                                {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                                                                                CTC </option>
                                                                                            <option value="1"
                                                                                                {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                                                                                Basic</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class=""
                                                                                        style="width: 15% !important;">
                                                                                        <input &nbsp="" type="text"
                                                                                            class="form-control form-control-solid bonus_offer_hourly"
                                                                                            name="bonus_offer_hourly[]"
                                                                                            id="bonus_offer_hourly{{ $offer->id }}"
                                                                                            data-id="{{ $offer->id }}"
                                                                                            data-offerid="{{ $offer->id }}"
                                                                                            value=""
                                                                                            placeholder="0.00">
                                                                                    </td>
                                                                                    <td style="width: 13% !important">
                                                                                        <button type="button"
                                                                                            class="btn btn-sm btn-light-danger font-weight-bolder remove_points py-4 me-0">
                                                                                            <span
                                                                                                class="svg-icon svg-icon svg-icon me-0">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                    width="24px"
                                                                                                    height="24px"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    version="1.1">
                                                                                                    <g stroke="none"
                                                                                                        stroke-width="1"
                                                                                                        fill="none"
                                                                                                        fill-rule="evenodd">
                                                                                                        <rect fill="#000000"
                                                                                                            x="4" y="11"
                                                                                                            width="16"
                                                                                                            height="2"
                                                                                                            rx="1">
                                                                                                        </rect>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </span>
                                                                                        </button>
                                                                                        <button type="button" id=""
                                                                                            class="btn btn-sm btn-primary me-0 py-4 addRowBtnbonus" style="">
                                                                                                <span class="svg-icon svg-icon svg-icon me-0">
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                        width="24px" height="24px"
                                                                                                        viewBox="0 0 24 24" version="1.1">
                                                                                                        <g stroke="none" stroke-width="1"
                                                                                                            fill="none"
                                                                                                            fill-rule="evenodd">
                                                                                                            <rect fill="#000000" x="4" y="11"
                                                                                                                width="16" height="2"
                                                                                                                rx="1"></rect>
                                                                                                            <rect fill="#000000" 
                                                                                                                transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                                x="4" y="11" width="16"
                                                                                                                height="2" rx="1">
                                                                                                            </rect>
                                                                                                        </g>
                                                                                                    </svg>
                                                                                                </span>
                                                                                        </button>

                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                    @endif
                                                                
                                                                @endif

                                                            


                                                            </tbody>
                                                            <tfoot class="other_section {{ $section_other }}">

                                                                <tr class="co-total-row">
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input type="text" class="form-control form-control-solid" value="Total Additional Components" readonly style="cursor: not-allowed;background: #eef3ff;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input type="text" readonly
                                                                            class="form-control form-control-solid totalpercentagebonus"
                                                                            name="totalpercentagebonus"
                                                                            value=""
                                                                            id="totalpercentagebonus" style="background:#eef3ff;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled">&nbsp;</th>
                                                                    <th class="sorting_disabled" style="width: 78px;">
                                                                        <input type="text" readonly
                                                                            class="form-control form-control-solid totalMonthlybonus"
                                                                            name="totalMonthlybonus"
                                                                            id="totalMonthlybonus"
                                                                            value="{{ @$monthly_bounus_gross }}" style="background:#eef3ff;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input type="text" readonly
                                                                            class="form-control form-control-solid totalYearlybonus"
                                                                            name="totalYearlybonus"
                                                                            value="{{ @$yearly_bonus_gross }}"
                                                                            id="totalYearlybonus" style="background:#eef3ff;">
                                                                    </th>
                                                                    <th class="sorting_disabled">&nbsp;</th>
                                                                </tr>
                                                            </tfoot>

                                                            <tfoot class="hourly_section {{ $section_hourly }}">
                                                                <tr class="co-total-row">
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input type="text" class="form-control form-control-solid" value="Total Additional Components" readonly style="cursor: not-allowed;background: #eef3ff;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 86% !important;">
                                                                        <input type="text" readonly
                                                                            class="form-control form-control-solid totalhourlypercentagebonus"
                                                                            name="totalhourlypercentagebonus"
                                                                            value=""
                                                                            id="totalhourlypercentagebonus" style="background:#eef3ff;">
                                                                    </th>
                                                                    <th class="sorting_disabled" colspan="1">
                                                                        <input &nbsp="" type="text" readonly
                                                                            class="form-control form-control-solid totalhourlyybonus"
                                                                            name="totalhourlyybonus"
                                                                            value="{{ @$yearly_bonus_gross }}"
                                                                            id="totalhourlyybonus" style="background:#eef3ff;">
                                                                    </th>
                                                                </tr>
                                                            </tfoot>

                                                        </table>
                                                        <div class="co-valid-banner info" id="co-add-valid-banner">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
                                                            <span id="co-add-valid-text">Total additional components: 0.00% (Allowed above 100%)</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2">
                                                    <div class="row">
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer"
                                                            id="final_pay" role="grid"
                                                            aria-describedby="final_pay_info" style="width: 100%;">


                                                            <tfoot class="other_section_final other_section {{ $section_other }}">

                                                                <tr>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 35%;">
                                                                        <input type="text" class="form-control form-control-solid" value="CTC — Cost to Company" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled" style="width: 10% !important;">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalpercentagefinal"
                                                                            name="totalpercentagefinal"
                                                                            value=""
                                                                            id="totalpercentagefinal" style="background: #7de96f40;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 10% !important;">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled"  style="width: 17% !important;">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid finalpaytotalMonthly"
                                                                            name="finalpaytotalMonthly"
                                                                            id="finalpaytotalMonthly"
                                                                            value="{{ @$monthly_final_gross }}" style="background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled"  style="width: 15% !important;">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid finalpaytotalYearly"
                                                                            name="finalpaytotalYearly"
                                                                            id="finalpaytotalYearly"
                                                                            value="{{ @$yearly_final_gross }}" style="background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled">&nbsp;</th>
                                                                </tr>


                                                            </tfoot>
                                                            <tfoot class="hourly_section_final hourly_section {{ $section_hourly }}">

                                                                <tr>
                                                                    <th class="sorting_disabled" style="width: 47%;">
                                                                        <input type="text" class="form-control form-control-solid" value="CTC — Cost to Company" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled" style="width: 12% !important;">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalpercentagefinalhourly"
                                                                            name="totalpercentagefinalhourly"
                                                                            value=""
                                                                            id="totalpercentagefinalhourly" style="background: #7de96f40;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 13% !important;">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled" colspan="1">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid finalpaytotalhourly"
                                                                            name="finalpaytotalhourly"
                                                                            id="finalpaytotalhourly"
                                                                            value="{{ @$yearly_final_gross }}" style="width:88%;background: #7de96f40;">
                                                                    </th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div id="text_for_hourly_annually" class="d-none"><br>
                                                    <p><b>Note:</b>*The per-hour salary is calculated based on 8 hours per day, 5 days per week, and 2,080 hours per year. The annual salary is subject to adjustments based on the actual number of hours worked</p> </div>

<<<<<<< HEAD
                                                {{-- Static/decorative footer (no backing calc source beyond the live example numbers) --}}
                                                <div class="co-howit-box">
                                                    <div class="co-howit-col">
                                                        <div class="co-howit-title"><i class="co-howit-ico"></i> How it works?</div>
                                                        <div class="co-howit-text">Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</div>
                                                    </div>
                                                    <div class="co-howit-col">
                                                        <div class="co-howit-title">Examples</div>
                                                        <div class="co-howit-formula" id="co_howit_formula">
                                                            <span class="co-chip">CTC —</span> = <span class="co-chip">Main 100.00% = —</span> + <span class="co-chip co-chip--add">Additional —% = —</span> = <span class="co-chip co-chip--total">Total Cost to Company = —</span>
                                                        </div>
                                                    </div>
                                                </div>

=======
                                                <div class="co-how-works" style="margin-top:4px;">
                                                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                                                        <div style="flex:1;min-width:220px;">
                                                            <div class="co-how-title">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;color:#6366f1;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                                                                How it works
                                                            </div>
                                                            <p style="font-size:12px;color:#64748b;line-height:1.6;margin:0;">Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</p>
                                                        </div>
                                                        <div style="flex:1.4;min-width:280px;">
                                                            <div class="co-how-title">Examples</div>
                                                            <div class="co-how-flow" id="co-how-flow">
                                                                <span class="co-how-pill" style="background:#ede9fe;color:#6d28d9;">CTC <span id="co-ex-ctc">0</span></span>
                                                                <span class="co-how-arr">=</span>
                                                                <span class="co-how-pill" style="background:#dcfce7;color:#16a34a;">Main <span id="co-ex-mainpct">100.00%</span> = <span id="co-ex-mainamt">0</span></span>
                                                                <span class="co-how-arr">+</span>
                                                                <span class="co-how-pill" style="background:#fef3c7;color:#b45309;">Additional <span id="co-ex-addpct">0.00%</span> = <span id="co-ex-addamt">0</span></span>
                                                                <span class="co-how-arr">=</span>
                                                                <span class="co-how-pill" style="background:#e0e7ff;color:#4338ca;">Total Cost to Company = <span id="co-ex-total">0</span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                                                <hr>

                                                <div class="row justify-content-center">
                                                    <div class="col-lg-3 d-flex justify-content-center">
                                                        <input type="submit" name="save"
                                                            value="Save & {{($section_change_value) ? 'Next' : 'Generate'}}" id="save-form"
                                                            class="btn {{($section_change_value) ? 'btn-warning' : 'btn-primary'}}"
                                                            style="width: 100%;height: 40px;margin-top: 10px;">
                                                    </div>
                                                    {{-- <div class="col-lg-3  d-flex justify-content-center section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                                       <button class="next-btn btn btn-warning" onclick="nextStep(event,1)" style="width: 100%">Next</button>
                                                    </div> --}}
                                                </div>


                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="tab-content-section pt-5" id="step2">

                                <div class="" style="position: relative;margin: 0px 2px 15px 0px;">
                                    <div> 
                                         <h2>Offer Terms & Condition</h2>
                                    </div>
                                    <div style="position: absolute; right: -7px; top: -17px;"> 
                                        <button class="btn btn-primary me-2 setToDefaultCompanyTerms" type="button"
                                            id="setToDefaultCompanyTerms">Set to Default
                                        </button>
                                    </div>
                                </div>

                                <div class="row">

                                    <form class="ajax-form" method="POST" id="termConditioncreateForm"
                                        name="termConditioncreateForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-lg-12 col-xl-12">
                                            <textarea id="editorterms1" name="editorterms1">
                                                {{ $offer_terms }}
                                            </textarea>
                                            <span class="form-text text-muted">&nbsp; </span>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-lg-3  d-flex justify-content-center">
                                                <span class="prev-btn" onclick="prevStep(event,2)"
                                                    style="padding: 15px 32px; border-radius: 9px; font-weight: bold; margin: 10px;background: #000;color: #fff;cursor: pointer;">Back</span>
                                            </div>
                                            <div class="col-lg-3  d-flex justify-content-center">
                                                <input type="submit" name="save" value="Save & Preview"
                                                    id="" class="btn btn-primary"
                                                    style="margin-top: 10px; width: 100%; height: 43px;">
                                            </div>
                                            {{-- <div class="col-lg-3 d-flex justify-content-center">
                                                <button class="btn btn btn-warning next-btn"
                                                    onclick="nextStep(event,2)" style="width: 100%;">Preview Offer
                                                    Letter</button>
                                            </div> --}}
                                        </div>
                                    </form>

                                </div>

                               



                            </div>

                            <!-- Step 3 -->
                            <div class="tab-content-section pt-5" id="step3">

                                <div id="preview_offer_body_content"></div>
                                <div class="row justify-content-center">
                                    <div class="col-lg-3  d-flex justify-content-center">
                                        <span class="prev-btn" onclick="prevStep(event,3)"
                                            style="padding: 15px 32px; border-radius: 9px; font-weight: bold; margin: 10px;background: #000;color: #fff;cursor: pointer;">Back</span>
                                    </div>
                                    <div class="col-lg-3 d-flex justify-content-center section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                        <button type="button" class="btn btn-primary font-weight-bolder px-9 py-4" style="width:100%;margin-top:10px;" onclick="nextStep(event,3)">Continue to Approval</button>
                                    </div>
                                </div>
                                <br>
                            </div>

                            <!-- Step 4 : Approval -->
                            <div class="tab-content-section pt-5" id="step4">
                                <div class="card card-body py-7 mb-5 mx-2">
                                    <h5 class="co-section-title">Approval</h5>
                                    <div class="co-section-sub mb-4">Send this offer for internal approval before it is released to the candidate.</div>
                                    <div class="row justify-content-center section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                        <div class="col-lg-3  d-flex justify-content-center">
                                            <span class="prev-btn" onclick="prevStep(event,4)"
                                                style="padding: 15px 32px; border-radius: 9px; font-weight: bold; margin: 10px;background: #000;color: #fff;cursor: pointer;">Back</span>
                                        </div>
                                        <div class="col-lg-3  d-flex justify-content-center">
                                            <button type="button" data-jobId="{{ @$application->job_id }}"
                                                data-candidate="{{ @$application->id }}" data-mtype="2"
                                                data-offerId="{{ $offer_id }}" data-toggle="modal"
                                                data-target="#fwd_offer"
                                                class="btn btn-warning font-weight-bolder px-9 py-4 fwd_offer"
                                                data-wizard-type="action-next" style="width: 100%;">Approval Email
                                                Send</button>
                                        </div>
                                        <div class="col-lg-3 d-flex justify-content-center">
                                            <button type="button" class="btn btn-primary font-weight-bolder px-9 py-4" style="width:100%;margin-top:10px;" onclick="nextStep(event,4)">Continue to Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 5 : Offer Sent -->
                            <div class="tab-content-section pt-5" id="step5">
                                <div class="card card-body py-7 mb-5 mx-2">
                                    <h5 class="co-section-title">Offer Sent</h5>
                                    <div class="co-section-sub mb-4">Download the offer letter or email the final offer to the candidate.</div>
                                    <div class="row justify-content-center section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                        <div class="col-lg-3  d-flex justify-content-center">
                                            <span class="prev-btn" onclick="prevStep(event,5)"
                                                style="padding: 15px 32px; border-radius: 9px; font-weight: bold; margin: 10px;background: #000;color: #fff;cursor: pointer;">Back</span>
                                        </div>
                                        <div class="col-lg-3  d-flex justify-content-center">
                                            <button type="button" data-jobId="{{ @$application->job_id }}"
                                                data-candidate="{{ @$application->id }}" data-mtype="3"
                                                data-offerId="{{ $offer_id }}"
                                                class="btn btn-success font-weight-bolder px-9 py-4 download_offer"
                                                data-wizard-type="action-next" style="width: 100%;">Download</button>
                                        </div>
                                        <div class="col-lg-3  d-flex justify-content-center">
                                            <button type="button" data-jobId="{{ @$application->job_id }}"
                                                data-candidate="{{ @$application->id }}" data-mtype="1"
                                                data-offerId="{{ $offer_id }}" data-toggle="modal"
                                                data-target="#fwd_offer"
                                                class="btn btn-primary font-weight-bolder px-9 py-4 fwd_offer"
                                                data-wizard-type="action-next" style="width: 100%;">Candidate Email
                                                Send</button>
                                        </div>
                                    </div>
                                </div>
<<<<<<< HEAD
                            </div>
=======
                                <br>
                            </div>{{-- /step3 --}}
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86

                            <!-- Step 4: Wait for Approval -->
                            <div class="tab-content-section pt-5" id="step4">
                                <div class="co-section">
                                    <div class="co-section-header">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-6v2m0-8v4"/></svg>
                                        Wait for Approval
                                    </div>
                                    <div style="padding:24px 0 16px;">
                                        <div class="co-approval-card">
                                            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                                                <div style="width:48px;height:48px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-6v2m0-8v4"/></svg>
                                                </div>
                                                <div>
                                                    <div style="font-weight:600;font-size:14px;color:#1e293b;">Approval Email Send</div>
                                                    <div style="font-size:12px;color:#64748b;margin-top:2px;">Send the offer for internal approval before sending to candidate</div>
                                                </div>
                                            </div>
                                            <p style="font-size:13px;color:#64748b;margin-bottom:18px;">Once the offer letter is ready and reviewed, send an approval email to the concerned authority. The offer will not be sent to the candidate until approval is received.</p>
                                            <div class="section_change_value {{ $section_change_value ? '' : 'd-none' }}" style="display:flex;gap:12px;flex-wrap:wrap;">
                                                <button type="button"
                                                    data-jobId="{{ @$application->job_id }}"
                                                    data-candidate="{{ @$application->id }}"
                                                    data-mtype="2"
                                                    data-offerId="{{ $offer_id }}"
                                                    data-toggle="modal"
                                                    data-target="#fwd_offer"
                                                    class="co-tbtn co-tbtn-primary fwd_offer"
                                                    style="font-size:13px;">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                                                    Send Approval Email
                                                </button>
                                                <button type="button" class="co-tbtn co-tbtn-ghost" onclick="coGoStep(3)">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                                    Back to Preview
                                                </button>
                                            </div>
                                            @if(!$section_change_value)
                                            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;">
                                                <strong>Note:</strong> Save the offer template in Step 1 &amp; 2 before sending for approval.
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="display:flex;gap:10px;margin-top:8px;">
                                        <span class="co-nav-back" onclick="coGoStep(3)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                            Back
                                        </span>
                                        <span class="co-nav-next" onclick="coGoStep(5)">
                                            Next: Offer Sent
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>{{-- /step4 --}}

                            <!-- Step 5: Offer Sent -->
                            <div class="tab-content-section pt-5" id="step5">
                                <div class="co-section">
                                    <div class="co-section-header">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                                        Offer Sent
                                    </div>
                                    <div style="padding:24px 0 16px;">
                                        <div class="co-sent-card">
                                            <div style="text-align:center;margin-bottom:24px;">
                                                <div style="width:64px;height:64px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                                                </div>
                                                <div style="font-weight:700;font-size:15px;color:#1e293b;">Send Offer to Candidate</div>
                                                <div style="font-size:12px;color:#64748b;margin-top:4px;">Send the final approved offer letter directly to the candidate</div>
                                            </div>
                                            <p style="font-size:13px;color:#64748b;margin-bottom:20px;text-align:center;">Once approved, click the button below to send the official offer letter to <strong>{{ ucwords(@$application->full_name) }}</strong> at <strong>{{ @$application->email }}</strong>.</p>
                                            <div class="section_change_value {{ $section_change_value ? '' : 'd-none' }}" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                                                <button type="button"
                                                    data-jobId="{{ @$application->job_id }}"
                                                    data-candidate="{{ @$application->id }}"
                                                    data-mtype="1"
                                                    data-offerId="{{ $offer_id }}"
                                                    data-toggle="modal"
                                                    data-target="#fwd_offer"
                                                    class="co-tbtn co-tbtn-success fwd_offer"
                                                    style="font-size:13px;padding:10px 28px;">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                                                    Send Offer to Candidate
                                                </button>
                                                <button type="button"
                                                    data-jobId="{{ @$application->job_id }}"
                                                    data-candidate="{{ @$application->id }}"
                                                    data-mtype="3"
                                                    data-offerId="{{ $offer_id }}"
                                                    class="co-tbtn co-tbtn-ghost download_offer"
                                                    style="font-size:13px;padding:10px 28px;">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>
                                                    Download Offer Letter
                                                </button>
                                            </div>
                                            @if(!$section_change_value)
                                            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;text-align:center;">
                                                <strong>Note:</strong> Complete all previous steps before sending the offer.
                                            </div>
                                            @endif

                                            <!-- Offer timeline status -->
                                            <div style="margin-top:28px;border-top:1px solid #f1f5f9;padding-top:20px;">
                                                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Offer Timeline</div>
                                                <div class="co-timeline">
                                                    <div class="co-tl-item done">
                                                        <div class="co-tl-dot"></div>
                                                        <div class="co-tl-content">
                                                            <div class="co-tl-label">Compensation Added</div>
                                                            <div class="co-tl-sub">Salary structure defined</div>
                                                        </div>
                                                    </div>
                                                    <div class="co-tl-item done">
                                                        <div class="co-tl-dot"></div>
                                                        <div class="co-tl-content">
                                                            <div class="co-tl-label">Offer Template Ready</div>
                                                            <div class="co-tl-sub">Terms &amp; conditions applied</div>
                                                        </div>
                                                    </div>
                                                    <div class="co-tl-item done">
                                                        <div class="co-tl-dot"></div>
                                                        <div class="co-tl-content">
                                                            <div class="co-tl-label">Letter Previewed</div>
                                                            <div class="co-tl-sub">Offer letter reviewed</div>
                                                        </div>
                                                    </div>
                                                    <div class="co-tl-item {{ $hasOffer ? 'done' : '' }}">
                                                        <div class="co-tl-dot"></div>
                                                        <div class="co-tl-content">
                                                            <div class="co-tl-label">Approval Sent</div>
                                                            <div class="co-tl-sub">Awaiting authority approval</div>
                                                        </div>
                                                    </div>
                                                    <div class="co-tl-item">
                                                        <div class="co-tl-dot"></div>
                                                        <div class="co-tl-content">
                                                            <div class="co-tl-label">Offer Sent to Candidate</div>
                                                            <div class="co-tl-sub">{{ $hasOffer ? 'Waiting for candidate response' : 'Pending' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display:flex;gap:10px;margin-top:8px;">
                                        <span class="co-nav-back" onclick="coGoStep(4)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                            Back
                                        </span>
                                    </div>
                                </div>
                            </div>{{-- /step5 --}}

        </div>{{-- /co-main --}}

        {{-- ── Right Sidebar ── --}}
        <div class="co-sidebar" id="co-sidebar">

            {{-- Candidate Summary (always visible) --}}
            <div class="co-section" style="padding:14px 16px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    @php $str2 = trim(@$application->full_name); $strV2 = strtoupper($str2[0] ?? 'C'); @endphp
                    <div style="width:36px;height:36px;border-radius:50%;background:#e0e7ff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#6366f1;">{{ $strV2 }}</div>
                    <div>
                        <div style="font-weight:600;font-size:13px;color:#1e293b;">{{ ucwords(@$application->full_name) }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ @$application->title }}</div>
                    </div>
                </div>
                <div style="font-size:11px;color:#94a3b8;border-top:1px solid #f1f5f9;padding-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
                    <span>📧 {{ @$application->email }}</span>
                    @if(!empty($application->total_exp))
                    <span>⏱ {{ @$application->total_exp }} yrs exp</span>
                    @endif
                    @if(!empty($country_name))
                    <span>📍 {{ $city_name ? ucwords($city_name).', ' : '' }}{{ ucwords($country_name) }}</span>
                    @endif
                </div>
            </div>

            {{-- Step 1 Sidebar: Preview Salary Structure --}}
            <div class="co-section co-sb-step" id="co-sb-step1" style="padding:14px 16px;">
                <div class="co-section-header" style="font-size:12px;margin-bottom:12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview Salary Structure
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:12px;margin-top:-8px;">See how this structure calculates for the entered amount.</div>

                <div style="display:flex;border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;margin-bottom:14px;">
                    <button type="button" id="co-sb-monthly-btn" class="co-view-btn active"
                        onclick="coSbToggle('monthly')">Monthly View</button>
                    <button type="button" id="co-sb-annual-btn" class="co-view-btn"
                        onclick="coSbToggle('annual')">Annual View</button>
                </div>

                <div class="co-donut-wrap">
                    <div class="co-donut" id="co-donut"></div>
                    <div class="co-donut-legend" id="co-donut-legend">
                        <div class="co-donut-legend-item"><span class="co-donut-dot" style="background:#6366f1;"></span> Main <strong id="co-donut-main" style="color:#1e293b;">100%</strong></div>
                        <div class="co-donut-legend-item"><span class="co-donut-dot" style="background:#22c55e;"></span> Additional <strong id="co-donut-add" style="color:#1e293b;">0%</strong></div>
                    </div>
                </div>

                <div class="co-sb-card" id="co-breakup-card" style="padding:12px 14px;">
                    <div class="co-sb-title" style="margin-bottom:8px;">Breakup Preview (<span id="co-breakup-mode">Monthly</span>)</div>
                    <div id="co-breakup-rows"></div>
                    <div class="co-sb-row" style="border-top:1px solid #e8edf4;margin-top:4px;padding-top:8px;">
                        <span class="co-sb-key" style="font-weight:700;color:#0f172a;">Total Cost to Company</span>
                        <span class="co-sb-val" id="co-breakup-total" style="color:#16a34a;font-size:13px;">—</span>
                    </div>
                </div>

                <div class="co-sb-card" style="margin-bottom:0;">
                    <div class="co-sb-title">Validation Checks</div>
                    <div class="co-check ok" id="co-check-main">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span id="co-check-main-text">Main components total is exactly 100%</span>
                    </div>
                    <div class="co-check ok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        No circular dependencies found
                    </div>
                    <div class="co-check ok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        All formulas are valid
                    </div>
                    <div class="co-check warn d-none" id="co-check-add">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="10"/></svg>
                        <span id="co-check-add-text">Additional components exceed 100% (Allowed)</span>
                    </div>
                </div>
            </div>

            {{-- Step 2 Sidebar --}}
            <div class="co-section co-sb-step d-none" id="co-sb-step2" style="padding:14px 16px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Offer Template</div>
                <p style="font-size:12px;color:#64748b;line-height:1.6;">Define the offer terms &amp; conditions and letter body. This will be used to generate the official PDF offer letter.</p>
                <div style="background:#eff6ff;border-radius:8px;padding:10px 12px;font-size:12px;color:#1d4ed8;">
                    <strong>Tip:</strong> Use the "Set as Default" button to save your template for future offers.
                </div>
            </div>

            {{-- Step 3 Sidebar --}}
            <div class="co-section co-sb-step d-none" id="co-sb-step3" style="padding:14px 16px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Preview Offer Letter</div>
                <p style="font-size:12px;color:#64748b;line-height:1.6;">Review the generated offer letter. Ensure all details are correct before sending for approval.</p>
                <div style="background:#f0fdf4;border-radius:8px;padding:10px 12px;font-size:12px;color:#16a34a;">
                    <strong>Looks good?</strong> Proceed to Step 4 to send for approval.
                </div>
            </div>

            {{-- Step 4 Sidebar --}}
            <div class="co-section co-sb-step d-none" id="co-sb-step4" style="padding:14px 16px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Approval Status</div>
                <p style="font-size:12px;color:#64748b;line-height:1.6;">An approval email will be sent to the designated approver. Wait for their confirmation before proceeding.</p>
                <div style="background:#fef9c3;border-radius:8px;padding:10px 12px;font-size:12px;color:#92400e;">
                    <strong>Pending Approval:</strong> Approver has been notified.
                </div>
            </div>

            {{-- Step 5 Sidebar --}}
            <div class="co-section co-sb-step d-none" id="co-sb-step5" style="padding:14px 16px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">How It Works</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:22px;height:22px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:700;color:#16a34a;">1</div>
                        <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;">Click "Send Offer" — the candidate receives an email with the offer letter.</p>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:22px;height:22px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:700;color:#16a34a;">2</div>
                        <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;">The candidate can accept or reject the offer from their email.</p>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:22px;height:22px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:700;color:#16a34a;">3</div>
                        <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;">You will be notified once the candidate responds. Track status in Offer Tracker.</p>
                    </div>
                </div>
            </div>

        </div>{{-- /co-sidebar --}}

    </div>{{-- /co-body --}}



            <script>
                /* ── Step navigation ── */
                function coGoStep(n) {
                    if (n == 3) {
                        /* Load preview via AJAX when going to step 3 */
                        var id = "{{ $offer_id }}";
                        if (!id) { nextStep({preventDefault:function(){}}, 2); return; }
                        var url = "{{ route('service_company.cvtracker.previewOfferLettetr', ':id') }}".replace(':id', id);
                        $.ajax({
                            type: 'POST', url: url,
                            data: { '_token': "{{ csrf_token() }}", 'offerId': id },
                            cache: false,
<<<<<<< HEAD
                            success: function(response) {
                                if (response.status == "success") {
                                    // $('#titleSet').text(response.title)
                                    $('#preview_offer_body_content').html(response.view);
                                    document.getElementById("step" + currentStep).classList.remove("active");
                                    document.getElementById("step" + (currentStep + 1)).classList.add("active");
                                    if (typeof syncStepHeader === 'function') syncStepHeader();
                                } else if (response.status == "error") {
=======
                            success: function(r) {
                                if (r.status == "success") {
                                    $('#preview_offer_body_content').html(r.view);
                                    _coActivateStep(n);
                                } else {
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                                    Swal.fire("Warning", "Offer letter not ready", "warning");
                                }
                            }
                        });
<<<<<<< HEAD

                    } else {
                        document.getElementById("step" + currentStep).classList.remove("active");
                        document.getElementById("step" + (currentStep + 1)).classList.add("active");
                        if (typeof syncStepHeader === 'function') syncStepHeader();
=======
                        return;
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                    }
                    _coActivateStep(n);
                }

                function _coActivateStep(n) {
                    /* Hide all steps */
                    document.querySelectorAll('.tab-content-section').forEach(function(el){ el.classList.remove('active'); });
                    var target = document.getElementById('step' + n);
                    if (target) target.classList.add('active');

                    /* Update step nav buttons */
                    document.querySelectorAll('.co-step-btn').forEach(function(btn, i){
                        btn.classList.remove('active','done');
                        var g = parseInt(btn.dataset.goto);
                        if (g === n) btn.classList.add('active');
                        else if (g < n) btn.classList.add('done');
                    });

                    /* Update sidebar panels */
                    document.querySelectorAll('.co-sb-step').forEach(function(el){ el.classList.add('d-none'); });
                    var sb = document.getElementById('co-sb-step' + n);
                    if (sb) sb.classList.remove('d-none');

                    /* Scroll main panel to top */
                    var main = document.getElementById('kt_activities_scroll');
                    if (main) main.scrollTop = 0;

                    if (n === 1 && typeof coUpdateCompensationUI === 'function') coUpdateCompensationUI();
                }

                document.addEventListener("DOMContentLoaded", function() {
                    _coActivateStep(1);
                });

                /* Legacy wrappers kept for existing Back/Next buttons */
                function nextStep(event, currentStep) {
                    event.preventDefault();
                    coGoStep(currentStep + 1);
                }
                function prevStep(event, currentStep) {
                    event.preventDefault();
<<<<<<< HEAD
                    document.getElementById("step" + currentStep).classList.remove("active");
                    document.getElementById("step" + (currentStep - 1)).classList.add("active");
                    if (typeof syncStepHeader === 'function') syncStepHeader();
=======
                    coGoStep(currentStep - 1);
                }

                /* ── Compensation preview: stat cards, banners, donut, breakup, validation ── */
                var coSbMode = 'monthly';
                var coCurrencySymbols = ['₹','€','¥','£','$','د.إ'];

                function coIsHourlyMode() {
                    var m = $('#current_ctc_mode').val();
                    return (m == '1' || m == '2' || m == '3');
                }
                function coFmtNum(x) {
                    return (Math.round(x * 100) / 100).toLocaleString('en-IN', {maximumFractionDigits: 0});
                }
                function coCurSym() {
                    var idx = parseInt($('#current_ctc_cur').val()) || 0;
                    return coCurrencySymbols[idx] || '';
                }
                function coSbToggle(mode) {
                    coSbMode = mode;
                    $('#co-sb-monthly-btn').toggleClass('active', mode === 'monthly');
                    $('#co-sb-annual-btn').toggleClass('active', mode === 'annual');
                    $('#co-breakup-mode').text(mode === 'monthly' ? 'Monthly' : 'Annual');
                    coUpdateCompensationUI();
                }

                function coUpdateCompensationUI() {
                    var hourly = coIsHourlyMode();
                    var mainPct = parseFloat($(hourly ? '#totalpercentagehouly' : '#totalpercentage').val()) || 0;
                    var addPct  = parseFloat($(hourly ? '#totalhourlypercentagebonus' : '#totalpercentagebonus').val()) || 0;
                    var overallPct = mainPct + addPct;
                    var sym = coCurSym();

                    var mainMonthly = parseFloat($('#totalMonthly').val()) || 0;
                    var mainYearly  = parseFloat($('#totalYearly').val()) || 0;
                    var addMonthly  = parseFloat($('#totalMonthlybonus').val()) || 0;
                    var addYearly   = parseFloat($('#totalYearlybonus').val()) || 0;
                    var finalYearly = parseFloat($('#finalpaytotalYearly').val()) || 0;
                    if (hourly) {
                        mainMonthly = mainYearly = parseFloat($('#totalhourlyhourly').val()) || 0;
                        addMonthly = addYearly = parseFloat($('#totalhourlyybonus').val()) || 0;
                        finalYearly = parseFloat($('#finalpaytotalhourly').val()) || 0;
                    }

                    /* Stat cards */
                    $('#co-stat-main-pct').text(mainPct.toFixed(2) + '%');
                    $('#co-stat-add-pct').text(addPct.toFixed(2) + '%');
                    $('#co-stat-overall-pct').text(overallPct.toFixed(2) + '%');
                    var mainOk = Math.abs(mainPct - 100) < 0.01;
                    $('#co-stat-main-hint').text(mainOk ? 'Exactly 100%' : 'Must be exactly 100%').css('color', mainOk ? '#7c3aed' : '#dc2626');

                    if (mainOk) {
                        $('#co-valid-icon').attr('stroke', '#16a34a');
                        $('#co-valid-text').text('Valid').css('color', '#16a34a');
                        $('#co-valid-hint').text('Ready to save').css('color', '#16a34a');
                    } else {
                        $('#co-valid-icon').attr('stroke', '#dc2626');
                        $('#co-valid-text').text('Invalid').css('color', '#dc2626');
                        $('#co-valid-hint').text('Main must total 100%').css('color', '#dc2626');
                    }

                    /* Table footer banners */
                    if (mainOk) {
                        $('#co-main-valid-banner').removeClass('warn').addClass('ok');
                        $('#co-main-valid-text').text('Total of main components is exactly ' + mainPct.toFixed(2) + '%');
                    } else {
                        $('#co-main-valid-banner').removeClass('ok').addClass('warn');
                        $('#co-main-valid-text').text('Total of main components is ' + mainPct.toFixed(2) + '% — must equal exactly 100.00%');
                    }
                    $('#co-add-valid-text').text('Total additional components: ' + addPct.toFixed(2) + '% (Allowed above 100%)');

                    /* Sidebar validation checks */
                    $('#co-check-main').removeClass('ok err').addClass(mainOk ? 'ok' : 'err');
                    $('#co-check-main-text').text(mainOk ? 'Main components total is exactly 100%' : 'Main components total ' + mainPct.toFixed(2) + '% (needs to be 100%)');
                    if (addPct > 0) {
                        $('#co-check-add').removeClass('d-none');
                    } else {
                        $('#co-check-add').addClass('d-none');
                    }

                    /* How it works examples */
                    var ctcVal = parseFloat($('#ctc').val()) || 0;
                    $('#co-ex-ctc').text(sym + coFmtNum(ctcVal));
                    $('#co-ex-mainpct').text(mainPct.toFixed(2) + '%');
                    $('#co-ex-mainamt').text(sym + coFmtNum(mainYearly));
                    $('#co-ex-addpct').text(addPct.toFixed(2) + '%');
                    $('#co-ex-addamt').text(sym + coFmtNum(addYearly));
                    $('#co-ex-total').text(sym + coFmtNum(finalYearly));

                    /* Donut chart (Main vs Additional share) */
                    var mAmt = coSbMode === 'monthly' ? mainMonthly : mainYearly;
                    var aAmt = coSbMode === 'monthly' ? addMonthly : addYearly;
                    var totalAmt = mAmt + aAmt;
                    var mainSharePct = totalAmt > 0 ? (mAmt / totalAmt * 100) : 100;
                    var mainShareDeg = mainSharePct / 100 * 360;
                    $('#co-donut').css('background', 'conic-gradient(#6366f1 0deg ' + mainShareDeg + 'deg, #22c55e ' + mainShareDeg + 'deg 360deg)');
                    $('#co-donut-main').text(mainSharePct.toFixed(0) + '%');
                    $('#co-donut-add').text((100 - mainSharePct).toFixed(0) + '%');

                    /* Breakup preview rows */
                    var rows = '';
                    var mainSel = hourly ? '#pointList tbody .hourly_section_row' : '#pointList tbody .other_section_row';
                    var mainNameCls = hourly ? '.offer_point_hourly' : '.offer_points';
                    var mainAmtCls  = hourly ? '.offer_hourly' : (coSbMode === 'monthly' ? '.offer_monthly' : '.offer_yearly');
                    $(mainSel).each(function() {
                        var name = $(this).find(mainNameCls).val() || 'Component';
                        var amt = parseFloat($(this).find(mainAmtCls).val()) || 0;
                        rows += '<div class="co-sb-row"><span class="co-sb-key">' + $('<div>').text(name).html() + '</span><span class="co-sb-val">' + sym + coFmtNum(amt) + '</span></div>';
                    });
                    var addSel = hourly ? '#bonusList tbody .hourly_section_row' : '#bonusList tbody .other_section_row';
                    var addNameCls = hourly ? '.bonus_point_hourly' : '.bonus_points';
                    var addAmtCls  = hourly ? '.bonus_offer_hourly' : (coSbMode === 'monthly' ? '.bonus_offer_monthly' : '.bonus_offer_yearly');
                    $(addSel).each(function() {
                        var name = $(this).find(addNameCls).val() || 'Component';
                        var amt = parseFloat($(this).find(addAmtCls).val()) || 0;
                        rows += '<div class="co-sb-row"><span class="co-sb-key">' + $('<div>').text(name).html() + '</span><span class="co-sb-val">' + sym + coFmtNum(amt) + '</span></div>';
                    });
                    $('#co-breakup-rows').html(rows || '<div style="font-size:11px;color:#94a3b8;padding:6px 0;">No components added yet.</div>');
                    $('#co-breakup-total').text(sym + coFmtNum(totalAmt));
                }

                /* Standardize the per-row Action column to a single clean trash icon */
                function coStyleActionButtons() {
                    var trashSvg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6h16Z"/></svg>';
                    $('#pointList .remove_points, #bonusList .remove_points').html(trashSvg);
>>>>>>> e34a35a7696b7af5bb28670010331d7829172e86
                }

                $(document).ready(function() {

                    coUpdateCompensationUI();
                    coStyleActionButtons();

                    $(document).on('input change', '.option_percentage, .bonus_option_percentage, .offer_monthly, .offer_yearly, .bonus_offer_monthly, .bonus_offer_yearly, .offer_hourly, .bonus_offer_hourly, .offer_points, .bonus_points, #ctc, #current_ctc_cur, #current_ctc_mode', function() {
                        coUpdateCompensationUI();
                    });
                    $(document).on('click', '.addRowBtn, .addRowBtnbonus, .remove_points, .co-comp-add-btn', function() {
                        setTimeout(function() {
                            coUpdateCompensationUI();
                            coStyleActionButtons();
                        }, 50);
                    });

                    $(document).on('click', '#setToDefaultCompanyTerms', function() {

                        var data = new FormData();
                        var kt_docs_tinymce_hidden = 'setDefault';
                        data.append("description", kt_docs_tinymce_hidden);
                        var form_data = $('#termConditioncreateForm').serializeArray();
                        $.each(form_data, function(key, input) {
                            data.append(input.name, input.value);
                        });
                        CKEDITOR.replace('kt_docs_tinymce_hidden');
                        data.append("description", kt_docs_tinymce_hidden);
                        data.append("descriptiontype", "offer_terms_admin");

                        $.ajax({
                            url: '{{ route('service_company.account.update', auth()->user()->id) }}',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: "POST",
                            processData: false,
                            contentType: false,
                            data: data,
                            redirect: true,
                            success: function(data) {
                                if (data.status == 'success') {
                                    var editorterms1 = CKEDITOR.instances.editorterms1.setData(data.terms);
                                }
                            }
                        });
                    });



                    function calculateRowDetails(row) {

                        const option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;

                        if (basic_view == 0) {
                            row.find('.offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary
                        } else if (basic_view == 1) {
                            row.find('.offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }

                       

                        var currentSalary = 0;
                        if (option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.option_percentage').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }

                    }

                    function calculateRowDetailshourly(row) {

                        var option_salarymode = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;

                        var currentSalary = 0;

                        if (option_salarymode == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        // alert("currentSalary");
                        // alert("k");
                        const percentage = parseFloat(row.find('.option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;


                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly;
                        }

                        row.find('.offer_hourly').val(hourly.toFixed(2));
                    }


                    $(document).on('change', '.offer_hourly', function() {
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        finalPayupdateTotalAmountshourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_offer_hourly', function() {
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode_hourly').val()) || 0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.bonus_option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountsbonushourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        finalPayupdateTotalAmountshourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_offer_hourly', function() {
                        // return false;
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode_hourly').val()) ||
                            0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.insurence_option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });


                    $(document).on('change', '.offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.option_percentage').val(percentage.toFixed(2));
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly
                        let monthly =  yearly / 12;
                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }
                        row.find('.option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;
                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        // row.find('.bonus_offer_monthly').val(monthly.toFixed(2));

                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }

                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.bonus_option_percentage').val(percentage.toFixed(2));
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts()
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly
                        let monthly =  yearly / 12;

                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.bonus_offer_yearly').val(yearly.toFixed(2)); // Update yearly amount
                        row.find('.bonus_option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;
                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.insurence_option_percentage').val(percentage.toFixed(2));
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts()
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly

                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.insurence_offer_yearly').val(yearly.toFixed(2)); // Update yearly amount
                        row.find('.insurence_option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });








                    // Event listener for input changes in CTC
                     function updateTotalAmountsChanges() {
                  
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 2 || current_ctc_mode == 1 || current_ctc_mode == 3) {
                            $('#pointList tbody .hourly_section_row').each(function() {
                                calculateRowDetailshourly($(this));
                                updateTotalAmountshourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                finalPayupdateTotalAmountshourly();
                                totalpercentagefinalhourly();

                            });
                            $('#bonusList tbody .hourly_section_row').each(function() {
                                calculateRowDetailsbonushourly($(this));
                                updateTotalAmountsbonushourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                finalPayupdateTotalAmountshourly();
                                totalpercentagefinalhourly();

                            });
                            $('#insurenceList tbody .hourly_section_row').each(function() {
                                calculateRowDetailsinsurencehourly($(this));
                                updateTotalAmountsinsurencehourly();
                                finalPayupdateTotalAmountshourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                totalpercentagefinalhourly();

                            });

                        } else {
                            $('#pointList tbody .other_section_row').each(function() {
                                calculateRowDetails($(this));
                                updateTotalAmounts();
                                updateTotalpercentage();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                            $('#bonusList tbody .other_section_row').each(function() {
                                calculateRowDetailsbonus($(this));
                                updateTotalAmountsbonus();
                                updateTotalpercentagebonus();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                            $('#insurenceList tbody .other_section_row').each(function() {
                                calculateRowDetailsinsurence($(this));
                                updateTotalAmountsinsurence();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                        }

                    }

                     $(document).on('keyup', '#ctc', function() {
                            updateTotalAmountsChanges();
                     });


                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.option_percentage', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.basic_view', function() {
                        const row = $(this).closest('tr');
                        const basic_view = $(this).val();
                        if (basic_view == 0) {
                            row.find('.offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary
                        } else if (basic_view == 1) {
                            row.find('.offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});  
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }

                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_view', function() {
                        const row = $(this).closest('tr');
                        const basic_view = $(this).val();
                        if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary                            
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});  
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); 
                        }

                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_view', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });


                    $(document).on('change', '.option_salarymode', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.option_percentage_hourly', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailshourly(row);
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.option_salarymode_hourly', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailshourly(row);
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        totalpercentagefinalhourly();
                    });


                    function updateTotalAmounts() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#pointList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthly').val(totalMonthly.toFixed(2));
                        $('#totalYearly').val(totalYearly.toFixed(2));
                    }

                    function updateTotalpercentage() {
                        let totalPercentage = 0;

                        $('#pointList tbody .other_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.option_percentage').val()) || 0;
                            totalPercentage += percentage;
                        });

                        $('#totalpercentage').val(totalPercentage.toFixed(2));
                    }

                    function updateTotalpercentagebonus() {

                        let totalPercentage = 0;

                        $('#bonusList tbody .other_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.bonus_option_percentage').val()) || 0;
                            totalPercentage += percentage;
                        });

                        $('#totalpercentagebonus').val(totalPercentage.toFixed(2));

                    }

                    function updateTotalpercentagehourly() {
                        let totalPercentage = 0;

                        $('#pointList tbody .hourly_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.option_percentage_hourly').val()) || 0;
                            totalPercentage += percentage;
                        });
                        //    alert(totalPercentage);
                        $('#totalpercentagehouly').val(totalPercentage.toFixed(2));
                    }

                    function updateTotalpercentagebonushourly() {

                        let totalPercentage = 0;

                        $('#bonusList tbody .hourly_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.bonus_option_percentage_hourly').val()) || 0;
                            totalPercentage += percentage;
                            // alert(percentage);
                        });

                        $('#totalhourlypercentagebonus').val(totalPercentage.toFixed(2));

                    }



                    function updateTotalAmountshourly() {
                        let totaloffer_hourly = 0;

                        $('#pointList tbody .hourly_section_row').each(function() {
                            const offer_hourly = parseFloat($(this).find('.offer_hourly').val()) || 0;
                            totaloffer_hourly += offer_hourly;
                        });

                        // alert(totaloffer_hourly);
                        $('#totalhourlyhourly').val(totaloffer_hourly.toFixed(2));
                    }

                    function finalPayupdateTotalAmounts() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        const monthly = parseFloat($('#monthlyInput').val()) || 0; // Define how to get 'monthly'
                        const yearly = parseFloat($('#yearlyInput').val()) || 0; // Define how to get 'yearly'

                        const Monthly = parseFloat($('.totalMonthly').val()) || 0;
                        const Yearly = parseFloat($('.totalYearly').val()) || 0;
                        const MonthlyBonus = parseFloat($('.totalMonthlybonus').val()) || 0;
                        const YearlyBonus = parseFloat($('.totalYearlybonus').val()) || 0;
                        const MonthlyInsurance = parseFloat($('.totalMonthlyinsurence').val()) || 0;
                        const YearlyInsurance = parseFloat($('.totalYearlyinsurence').val()) || 0;

                        totalMonthly = monthly + Monthly + MonthlyBonus + MonthlyInsurance;
                        totalYearly = yearly + Yearly + YearlyBonus + YearlyInsurance;

                        $('#finalpaytotalMonthly').val(totalMonthly.toFixed(2));
                        $('#finalpaytotalYearly').val(totalYearly.toFixed(2));
                    }

                    function totalpercentagefinal() {
                        let totalpercentagefinal = 0;

                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        const totalpercentage = parseFloat($('#totalpercentage').val()) || 0; // Define how to get 'monthly'
                        const totalpercentagebonus = parseFloat($('#totalpercentagebonus').val()) || 0; // Define how to get 

                        totalpercentagefinal = totalpercentage + totalpercentagebonus;

                        $('#totalpercentagefinal').val(totalpercentagefinal.toFixed(2));
                    }

                    function totalpercentagefinalhourly() {
                        let totalpercentagefinal = 0;
                    
                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        var totalpercentage = parseFloat($('#totalpercentagehouly').val()) || 0; // Define how to get 'monthly'
                        var totalpercentagebonus = parseFloat($('#totalhourlypercentagebonus').val()) || 0; // Define how to get 
                         //alert(totalpercentage);
                        totalpercentagefinal = totalpercentage + totalpercentagebonus;
                        // alert(totalpercentagefinal+"-"+totalpercentage+"-"+totalpercentagebonus);

                        $('#totalpercentagefinalhourly').val(totalpercentagefinal.toFixed(2));
                    }



                    function finalPayupdateTotalAmountshourly() {
                        let totalhourly = 0;

                        const totalhourlyhourly = parseFloat($('.totalhourlyhourly').val()) || 0;
                        const totalhourlyybonus = parseFloat($('.totalhourlyybonus').val()) || 0;
                        const totalYearlyinsurencehourly = parseFloat($('.totalYearlyinsurencehourly').val()) || 0;

                        totalhourly = totalhourlyhourly + totalhourlyybonus + totalYearlyinsurencehourly;

                        $('#finalpaytotalhourly').val(totalhourly.toFixed(2));
                    }

                    function finalRow(){
                        const rowCountOther = $('#bonusList tbody .other_section_row').length;
                        const rowCountHourly = $('#bonusList tbody .hourly_section_row').length;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        if((rowCountOther == 0) && (addSubtract==4 || addSubtract==0)){
                            $('.other_section_final').addClass('d-none');
                            // $('.other_text').text('');
                        }else if((rowCountOther > 0) && (addSubtract==4 || addSubtract==0)){
                            $('.other_section_final').removeClass('d-none');
                            // $('.other_text').text('Other Component');
                        }
                        
                        if((rowCountHourly == 0) && (addSubtract==1 || addSubtract==2 || addSubtract==3)){ 
                            $('.hourly_section_final').addClass('d-none');
                            // $('.other_text').text('');
                        }else if((rowCountHourly > 0) && (addSubtract==1 || addSubtract==2 || addSubtract==3)){
                            $('.hourly_section_final').removeClass('d-none');
                            // $('.other_text').text('Other Component');
                        }
                    }


                    $(document).off('click', '.addRowBtn').on('click', '.addRowBtn', function() {
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                        }
                        newRow.find('input').val(''); // Clear input values
                        newRow.find('select').val('0'); // Reset selects to default values
                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        newRow.find('.remove_points').removeClass('btn-light-info');
                        newRow.find('.remove_points').addClass('btn-light-danger');
                        $('#pointList tbody').append(newRow); // Append the new row to the table body

                        // Attach event listeners for new row
                        newRow.find('.option_percentage').change(function() {
                            calculateRowDetails(newRow);
                            updateTotalAmounts(); // Update totals when percentage changes
                            updateTotalpercentage();
                        });
                    });

                    $(document).off('click', '.remove_points').on('click', '.remove_points', function() {
                        const removeRow = $(this).closest('.remove_row');
                        removeRow.remove();

                        finalRow();
                        // Attach event listeners for new row
                        updateTotalAmounts(); // Update totals when percentage changes
                        updateTotalpercentage();
                        updateTotalAmountsinsurence();
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        updateTotalAmountsbonushourly();
                        updateTotalAmountsinsurencehourly();
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();                        
                        finalPayupdateTotalAmountshourly();                        
                        finalPayupdateTotalAmounts();

                    });

                    $(document).off('click', '.addRowBtnbonus').on('click', '.addRowBtnbonus', function() {
                        
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_point_hourly').addClass('bonus_point_hourly').removeClass(
                                'offer_point_hourly');
                            newRow.find('.bonus_point_hourly').each(function(index) {
                                var bonus_point_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_point_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_point_hourly_' +
                                    bonus_point_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_point_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_point_hourlyIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage_hourly').addClass('bonus_option_percentage_hourly')
                                .removeClass('option_percentage_hourly');
                            newRow.find('.bonus_option_percentage_hourly').each(function(index) {
                                var bonus_option_percentage_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_percentage_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_percentage_hourly_' +
                                    bonus_option_percentage_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_percentage_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_percentage_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode_hourly').addClass('bonus_option_salarymode_hourly')
                                .removeClass('option_salarymode_hourly');
                            newRow.find('.bonus_option_salarymode_hourly').each(function(index) {
                                var bonus_option_salarymode_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_salarymode_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_salarymode_hourly_' +
                                    bonus_option_salarymode_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_salarymode_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_salarymode_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_hourly').addClass('bonus_offer_hourly').removeClass('offer_hourly');
                            newRow.find('.bonus_offer_hourly').each(function(index) {
                                var bonus_offer_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_hourly_' +
                                    bonus_offer_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_hourlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');

                            newRow.find('.remove_points').addClass('btn-light-danger').removeClass('btn-light-info');
                            
                            newRow.find('.bonus_offer_hourly').removeClass('basic_salary_get_hourly');

                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_points').addClass('bonus_points').removeClass('offer_points');
                            newRow.find('.bonus_points').each(function(index) {
                                var bonus_pointsIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_points[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_points_' +
                                    bonus_pointsIndex); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id', bonus_pointsIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_pointsIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_monthly').addClass('bonus_offer_monthly').removeClass(
                                'offer_monthly');
                            newRow.find('.bonus_offer_monthly').each(function(index) {
                                var bonus_offer_monthlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_monthly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_monthly_' +
                                    bonus_offer_monthlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_monthlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_monthlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.basic_view').addClass('bonus_view').removeClass(
                                'basic_view');
                            newRow.find('.bonus_view').each(function(index) {
                                var bonus_viewIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_view[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_view_' +
                                    bonus_viewIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_viewIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_viewIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage').addClass('bonus_option_percentage').removeClass(
                                'option_percentage');
                            newRow.find('.bonus_option_percentage').each(function(index) {
                                var bonus_option_percentageIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_percentage[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_percentage_' +
                                    bonus_option_percentageIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_percentageIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_percentageIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode').addClass('bonus_option_salarymode').removeClass(
                                'option_salarymode');
                            newRow.find('.bonus_option_salarymode').each(function(index) {
                                var bonus_option_salarymodeIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_salarymode[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_salarymode_' +
                                    bonus_option_salarymodeIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_salarymodeIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_salarymodeIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_yearly').addClass('bonus_offer_yearly').removeClass('offer_yearly');
                            newRow.find('.bonus_offer_yearly').each(function(index) {
                                var bonus_offer_yearlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_yearly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_yearly_' +
                                    bonus_offer_yearlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_yearlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_yearlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');
                           

                            newRow.find('.bonus_offer_yearly').removeClass('basic_salary_get');

                        }



                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        newRow.find('.remove_points').removeClass('btn-light-info');
                        newRow.find('.remove_points').addClass('btn-light-danger');
                        $('#bonusList tbody').append(newRow); // Append the new row to the table body
                        finalRow();
                        // Attach event listeners for new row
                    });

                    $(document).off('click', '.addRowBtninsurence').on('click', '.addRowBtninsurence', function() {

                        const insurence_offer_count = $('#insurenceList tbody tr').length

                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_point_hourly').addClass('insurence_point_hourly').removeClass(
                                'offer_point_hourly');
                            newRow.find('.insurence_point_hourly').each(function(index) {
                                const insurence_point_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_point_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_point_hourly_' +
                                    insurence_point_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_point_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_point_hourlyIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage_hourly').addClass('insurence_option_percentage_hourly')
                                .removeClass('option_percentage_hourly');
                            newRow.find('.insurence_option_percentage_hourly').each(function(index) {
                                const option_percentage_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_percentage_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_percentage_hourly_' +
                                    option_percentage_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    option_percentage_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    option_percentage_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode_hourly').addClass('insurence_option_salarymode_hourly')
                                .removeClass('option_salarymode_hourly');
                            newRow.find('.insurence_option_salarymode_hourly').each(function(index) {
                                const insurence_option_salarymode_hourlyIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_salarymode_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_salarymode_hourly_' +
                                    insurence_option_salarymode_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_salarymode_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_salarymode_hourlyIndex
                                ); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_hourly').addClass('insurence_offer_hourly').removeClass(
                                'offer_hourly');
                            newRow.find('.insurence_offer_hourly').each(function(index) {
                                const insurence_offer_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_offer_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_hourly_' +
                                    insurence_offer_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_hourlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.insurence_offer_hourly').removeClass('basic_salary_get_hourly');

                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_points').addClass('insurence_points').removeClass('offer_points');
                            newRow.find('.insurence_points').each(function(index) {
                                const insurence_pointsIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_points[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_points_' +
                                    insurence_pointsIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_pointsIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_pointsIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.basic_view').addClass('insurence_view').removeClass(
                                'basic_view');
                            newRow.find('.insurence_view').each(function(index) {
                                const insurence_viewIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_view[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_view_' +
                                    insurence_viewIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_viewIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_viewIndex); // Update 'data-offerid' attribute
                            });




                            newRow.find('.offer_monthly').addClass('insurence_offer_monthly').removeClass(
                                'offer_monthly');
                            newRow.find('.insurence_offer_monthly').each(function(index) {
                                const insurence_offer_monthlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_offer_monthly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_monthly_' +
                                    insurence_offer_monthlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_monthlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_monthlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_percentage').addClass('insurence_option_percentage').removeClass(
                                'option_percentage');
                            newRow.find('.insurence_option_percentage').each(function(index) {
                                const insurence_option_percentageIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_percentage[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_percentage_' +
                                    insurence_option_percentageIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_percentageIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_percentageIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode').addClass('insurence_option_salarymode').removeClass(
                                'option_salarymode');
                            newRow.find('.insurence_option_salarymode').each(function(index) {
                                const insurence_option_salarymodeIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_salarymode[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_salarymode_' +
                                    insurence_option_salarymodeIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_salarymodeIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_salarymodeIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_yearly').addClass('insurence_offer_yearly').removeClass(
                                'offer_yearly');
                            newRow.find('.insurence_offer_yearly').each(function(index) {
                                const insurence_offer_yearlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_offer_yearly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_yearly_' +
                                    insurence_offer_yearlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_yearlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_yearlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.insurence_offer_yearly').removeClass('basic_salary_get');
                        }


                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        newRow.find('.remove_points').removeClass('btn-light-info');
                        newRow.find('.remove_points').addClass('btn-light-danger');
                        $('#insurenceList tbody').append(newRow); // Append the new row to the table body

                        // Attach event listeners for new row
                    });



                    function calculateRowDetailsbonus(row) {
                        const bonus_option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        var currentSalary = 0;
                        if (bonus_option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.bonus_option_percentage').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary                            
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }


                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }
                    }

                    function calculateRowDetailsbonushourly(row) {
                        // alert("k");
                        const bonus_option_salarymode_hourly = parseFloat(row.find('.bonus_option_salarymode_hourly')
                            .val()) || 0;
                        var currentSalary = 0;
                        if (bonus_option_salarymode_hourly == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.bonus_option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
                        }

                        row.find('.bonus_offer_hourly').val(hourly.toFixed(2));
                    }

                   
                    


                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.bonus_option_percentage', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_option_salarymode', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_option_percentage_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsbonushourly(row);
                        updateTotalAmountsbonushourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        
                    });
                    $(document).on('change', '.bonus_option_salarymode_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsbonushourly(row);
                        updateTotalAmountsbonushourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });


                    function updateTotalAmountsbonus() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#bonusList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.bonus_offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.bonus_offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthlybonus').val(totalMonthly.toFixed(2));
                        $('#totalYearlybonus').val(totalYearly.toFixed(2));
                    }

                    function updateTotalAmountsbonushourly() {
                        let totalhourly = 0;

                        $('#bonusList tbody .hourly_section_row').each(function() {
                            const hourly = parseFloat($(this).find('.bonus_offer_hourly').val()) || 0;
                            totalhourly += hourly;
                        });

                        $('#totalhourlyybonus').val(totalhourly.toFixed(2));
                    }


                    function calculateRowDetailsinsurence(row) {
                        const insurence_option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        var currentSalary = 0;
                        if (insurence_option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.insurence_option_percentage').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                    }

                    function calculateRowDetailsinsurencehourly(row) {
                        const insurence_option_salarymode = parseFloat(row.find('.insurence_option_salarymode_hourly')
                            .val()) || 0;
                        var currentSalary = 0;
                        if (insurence_option_salarymode == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.insurence_option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
                        }
                        row.find('.insurence_offer_hourly').val(hourly.toFixed(2));

                    }

                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.insurence_option_percentage', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_option_salarymode', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_option_percentage_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsinsurencehourly(row);
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });
                    $(document).on('change', '.insurence_option_salarymode_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsinsurencehourly(row);
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });

                    function updateTotalAmountsinsurencehourly() {
                        let totalhourly = 0;

                        $('#insurenceList tbody .hourly_section_row').each(function() {
                            const hourly = parseFloat($(this).find('.insurence_offer_hourly').val()) || 0;
                            totalhourly += hourly;
                        });

                        $('#totalYearlyinsurencehourly').val(totalhourly.toFixed(2));
                    }

                    function updateTotalAmountsinsurence() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#insurenceList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.insurence_offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.insurence_offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthlyinsurence').val(totalMonthly.toFixed(2));
                        $('#totalYearlyinsurence').val(totalYearly.toFixed(2));
                    }

                    $(document).on('change', '#current_ctc_mode', function() {
                        
                        var current_ctc_mode = $(this).val();
                        finalRow();
                         $('.basic_view').each(function() {
                            const select = $(this);  // Get the current select element in the loop
                            const monthlyOption = select.find('option[value="1"]');  // Find the option with value 1 (Monthly)

                            // Assuming current_ctc_mode is dynamic or can vary per select element
                            // Change the option text based on current_ctc_mode
                            if (current_ctc_mode == 4) {
                                monthlyOption.text("Hourly");
                                $('#text_for_hourly_annually').removeClass('d-none');
                            } else {
                                monthlyOption.text("Monthly");                                
                                $('#text_for_hourly_annually').addClass('d-none');
                            }
                         });

                         $('.bonus_view').each(function() {
                            const select = $(this);  // Get the current select element in the loop
                            const monthlyOption = select.find('option[value="1"]');  // Find the option with value 1 (Monthly)

                            // Assuming current_ctc_mode is dynamic or can vary per select element
                            // Change the option text based on current_ctc_mode
                            if (current_ctc_mode == 4) {
                                monthlyOption.text("Hourly");
                            } else {
                                monthlyOption.text("Monthly");
                            }
                         });

                        

                      
                        if (current_ctc_mode == 0 || current_ctc_mode == 4) {

                            $('.basic_view').val(2).change();
                            $('.bonus_view').val(2).change();

                            var change_text_one = '';
                            var change_text_two = '';
                            if(current_ctc_mode==0){
                                 change_text_one = 'Monthly';
                                 change_text_two = 'Annually';
                            }else if(current_ctc_mode==4){
                                 change_text_one = 'Hourly';
                                 change_text_two = 'Annually';
                            }
                            $('.change_text_first').text(change_text_one);
                            $('.change_text_second').text(change_text_two);
                            $('.other_section').removeClass('d-none');
                            $('.hourly_section').addClass('d-none');
                        } else {
                            var change_text = '';
                            if(current_ctc_mode==1){
                                 change_text = 'Monthly';
                            }else if(current_ctc_mode==2){
                                 change_text = 'Hourly';
                            }else if(current_ctc_mode==3){
                                 change_text = 'Annually';
                            }
                            $('.change_text').text(change_text);
                            $('.hourly_section').removeClass('d-none');
                            $('.other_section').addClass('d-none');
                        }

                      updateTotalAmountsChanges();
                      
                        

                    });

                    setTimeout(() => {
                        changeCurrency();
                    }, 500);

                    $(document).on('change', '#current_ctc_cur', function() {
                        changeCurrency();
                    });

                    function changeCurrency(){
                        const val_currency = $('#current_ctc_cur').val();   
                        if(val_currency==0){
                            $('.show_currency').val('INR');
                        }else if(val_currency==1){
                            $('.show_currency').val('EUR');
                        }else if(val_currency==2){
                            $('.show_currency').val('JPY');
                        }else if(val_currency==3){
                            $('.show_currency').val('GBP');
                        }else if(val_currency==4){
                            $('.show_currency').val('USD');
                        }else if(val_currency==5){
                           $('.show_currency').val('AED');
                        }
                    }
                    

                    setTimeout(() => {
                        updateTotalpercentage();
                        updateTotalpercentagebonus()
                        totalpercentagefinal();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly()
                        totalpercentagefinalhourly();
                    }, 1500);
                    

                    




                    $('#createForm').on('submit', function(e) {
                        e.preventDefault();
                        //  var formData = new FormData($('#createForm')[0]);	
                        let formData = new FormData(this);
                        CKEDITOR.replace('kt_docs_tinymce_hidden');
                        $.ajax({
                            url: '{{ route('service_company.job-applications.save_create_offer') }}' +
                                '?_token=' + '{{ csrf_token() }}',
                            type: 'POST',
                            data: formData,
                            processData: false, // tell jQuery not to process the data
                            contentType: false, // tell jQuery not to set contentType
                            redirect: true,
                            success: function(response) {

                                if (response.status == 'success') {
                                    // Swal.fire("Success", response.message , "success").then(function(){
                                    //     $('.section_change_value').removeClass('d-none');
                                    //     nextStep(1);
                                    //     tableLoad('load');
                                    // });	
                                    $('.section_change_value').removeClass('d-none');
                                    var editorterms1 = CKEDITOR.instances.editorterms1.setData(response.terms);
                                    nextStep(event, 1);
                                    var page = $('#pagination .active_disabled').data('page');
                                      fetchCandidates(page);
                                    // tableLoad('load');
                                    return false;
                                } else if(response.status == 'errors') {
                                    Swal.fire("Warning", response.message, "warning");
                                }else if(response.status == 'permisionInfo') {
                                    Swal.fire("Warning", response.view, "warning");
                                }else{
                                    Swal.fire("Warning", response.message, "warning");
                                }

                            },
                            error: function(response) {
                                // displayErrors(response.responseJSON.errors);
                                // Handle validation errors and display in SweetAlert
                                let errors = response.responseJSON.errors;
                                let errorMessages = '';
                                let counter = 1;

                                $.each(errors, function(fieldName, errorMessage) {
                                    // Add error messages with red color using inline CSS
                                    errorMessages +=
                                        `<span style="color:red;">${counter}. ${errorMessage[0]}</span><br>`;
                                    counter++;
                                });
                                // Show error messages in SweetAlert
                                Swal.fire({
                                    title: "Validation Errors",
                                    html: errorMessages, // Display the errors with HTML content
                                    icon: "warning",
                                    customClass: {
                                        popup: 'text-left' // Align the text to the left
                                    }
                                });
                            }
                        });
                    });

                    $('#termConditioncreateForm').on('submit', function(e) {
                        e.preventDefault();
                        //  var formData = new FormData($('#createForm')[0]);	
                        let formData = new FormData(this);
                        var editorterms1 = CKEDITOR.instances.editorterms1.getData(); //$('#editorterms1').val();	
                        var offerId = "{{ $offer_id }}";
                        formData.append('editor1', editorterms1);
                        formData.append('offerId', offerId);
                        $.ajax({
                            url: '{{ route('service_company.job-applications.terms_save_create_offer') }}' +
                                '?_token=' + '{{ csrf_token() }}',
                            type: 'POST',
                            data: formData,
                            processData: false, // tell jQuery not to process the data
                            contentType: false, // tell jQuery not to set contentType
                            redirect: true,
                            success: function(response) {

                                if (response.status == 'success') {
                                    nextStep(event, 2);
                                    return false;
                                } else {
                                    Swal.fire("Warning", response.message, "warning");
                                }

                            },
                            error: function(response) {
                                let errors = response.responseJSON.errors;
                                let errorMessages = '';
                                let counter = 1;

                                $.each(errors, function(fieldName, errorMessage) {
                                    // Add error messages with red color using inline CSS
                                    errorMessages +=
                                        `<span style="color:red;">${counter}. ${errorMessage[0]}</span><br>`;
                                    counter++;
                                });
                                // Show error messages in SweetAlert
                                Swal.fire({
                                    title: "Validation Errors",
                                    html: errorMessages, // Display the errors with HTML content
                                    icon: "warning",
                                    customClass: {
                                        popup: 'text-left' // Align the text to the left
                                    }
                                });
                            }
                        });
                    });


                    $('body').on('click', '#cancel-note', function() {
                        $('#note_text').val('');
                    });

                    $('#add-note').click(function() {
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note";
                        var id = 1028;
                        var note = $('#note_text').val();
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'id': id,
                                'note': note
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    $('#applicant-notes').html(response.view);
                                    $('#note_text').val('');
                                }
                            }
                        });
                    });

                    $('body').on('click', '.delete-note', function() {
                        let noteId = $(this).data('note-id');


                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {



                                var url =
                                    "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note/:id";
                                url = url.replace(':id', noteId);
                                var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        '_token': token,
                                        '_method': 'DELETE'
                                    },
                                    success: function(response) {
                                        if (response.status == 'success') {
                                            $('#applicant-notes').html(response.view);

                                            swalWithBootstrapButtons.fire(
                                                'Deleted!',
                                                'Your text note has been deleted.',
                                                'success'
                                            )

                                        }
                                    }
                                });



                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your text note is safe :)',
                                    'error'
                                )
                            }
                        })


                    });

                    $('body').on('click', '.edit-note', function() {

                        $(this).hide();
                        let noteId = $(this).data('note-id');
                        $('body').find('#note-' + noteId + ' .note-text').hide();

                        let noteText = $('body').find('#note-' + noteId + ' .note-text').html();
                        let textArea = '<textarea id="edit-note-text-' + noteId +
                            '" class="form-control" row="4">' + noteText +
                            '</textarea><a class="update-note btn btn-sm btn-light-success py-1 mt-2" data-note-id="' +
                            noteId + '" href="javascript:void(0);">Save</a>';
                        $('body').find('#note-' + noteId + ' .note-textarea').html(textArea);

                    });


                    $('body').on('click', '.update-note', function() {

                        let noteId = $(this).data('note-id');
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note/:id";
                        url = url.replace(':id', noteId);
                        var note = $('#edit-note-text-' + noteId).val();
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'noteId': noteId,
                                'note': note,
                                '_method': 'PUT'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    $('#applicant-notes').html(response.view);
                                }
                            }
                        });


                    });

                    $('body').on('click', '.resume_delete', function() {


                        var application_id = $(this).attr('rel');
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';


                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {


                                var url =
                                    "http://localhost/MEECRUITUAT/index.php/service_company/job-applications/resume_delete";
                                var token = "Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        '_token': token,
                                        '_method': 'POST',
                                        'application_id': application_id
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {

                                            swalWithBootstrapButtons.fire(
                                                'Deleted!',
                                                'Your resume has been deleted.',
                                                'success'
                                            )


                                        }
                                    }
                                });

                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your text note is safe :)',
                                    'error'
                                )
                            }
                        })

                    });


                    $('.sendmaimodal').click(function() {


                        $('#application_send_mail_body_content').html('');
                        $('#application_mass_mail_body_content').html('');
                        $('#application_fwd_resume_body_content').html('');
                        var id = 29;
                        var sid = 1028;
                        var arr = [sid];
                        var jobIdArr = [id];
                        $('.chk_list_item:checked').each(function() {

                            var jobId = $(this).attr('data-jobId');
                            arr.push($(this).val());
                            jobIdArr.push(jobId);
                        });

                        console.log(jobIdArr);

                        var id = jobIdArr[0];
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/send-mail/send-mail/:id";
                        url = url.replace(':id', id);
                        var baseurl = "http://localhost/MEECRUITUAT/assets/images/Loading2.gif";
                        var token = "Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK";


                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'arr': arr,
                                'jobIdArr': jobIdArr
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    $('#application_send_mail_body_content').html(response.view);

                                    $('#kt_activities_scroll').removeAttr('style');
                                }
                            }
                        });

                    });


                });
            </script>

            <script>
                /* ── Stepper controller (global scope so onclick handlers resolve) ──
                   syncStepHeader() paints the stepper purely from which .tab-content-section
                   is .active. goToStepNav() lets the user click backward freely, but forward
                   only up to the furthest step actually reached via the real Save/Preview
                   AJAX flow — so header clicks can never skip the preview-letter population. */
                var coFurthestStep = 1;

                function coPaintStep(el, state, statusText) {
                    el.classList.remove('co-step--pending', 'co-step--current', 'co-step--done');
                    el.classList.add('co-step--' + state);
                    var s = el.querySelector('.co-step-status');
                    if (s && statusText != null) s.textContent = statusText;
                }

                function coActiveStep() {
                    var el = document.querySelector('.tab-content-section.active');
                    var n = el ? parseInt((el.id || '').replace('step', ''), 10) : 1;
                    return n > 0 ? n : 1;
                }

                function syncStepHeader() {
                    var active = coActiveStep();
                    if (active > coFurthestStep) coFurthestStep = active;
                    var steps = document.querySelectorAll('#co_offer_stepper .co-step');
                    steps.forEach(function (step) {
                        var n = parseInt(step.getAttribute('data-step'), 10);
                        if (n < active) coPaintStep(step, 'done', 'Completed');
                        else if (n === active) coPaintStep(step, 'current', 'In progress');
                        else coPaintStep(step, 'pending', 'Upcoming');
                    });
                }

                function goToStepNav(n) {
                    var active = coActiveStep();
                    if (n === active) return;
                    if (n > active && n > coFurthestStep) return;   // forward nav gated
                    var target = document.getElementById('step' + n);
                    if (!target) return;                            // step4/step5 added in a later piece
                    var current = document.getElementById('step' + active);
                    if (current) current.classList.remove('active');
                    target.classList.add('active');
                    syncStepHeader();
                }

                $(document).ready(function () { syncStepHeader(); });
            </script>

            <script>
                /* ── Preview Salary Structure — 100% client-side, read-only ──
                   Reads existing live fields (#ctc, #totalpercentage(bonus), #finalpaytotal*,
                   and each visible component row) and repaints the panel. It NEVER writes back
                   into any backend-bound field. New delegated listeners fire on the same events
                   as the existing calc engine; a 0ms setTimeout guarantees we read post-calc
                   values regardless of handler binding order. */
                (function () {
                    var coView = 'monthly';   // local-only Monthly/Annual toggle
                    var coChart = null;
                    var FORMAT_LABELS = { '0': 'Monthly / Annually', '1': 'Monthly', '2': 'Hourly', '3': 'Annually', '4': 'Hourly / Annually' };

                    function coNum(v) { v = (v == null ? '' : String(v)).replace(/[^0-9.\-]/g, ''); var n = parseFloat(v); return isNaN(n) ? 0 : n; }
                    function coMoney(n) { try { return Number(Math.round(n)).toLocaleString('en-IN'); } catch (e) { return Math.round(n); } }
                    function coEsc(s) { return $('<div>').text(s).html(); }
                    function coVisibleRows(sel) {
                        return $(sel + ' tr.row-template').filter(function () { return $(this).css('display') !== 'none' && !$(this).hasClass('d-none'); });
                    }

                    window.refreshPreviewPanel = function () {
                        $('#co_preview_amount').val($('#ctc').val() || '');
                        var mode = String($('#current_ctc_mode').val() || '0');
                        $('#co_preview_format').text(FORMAT_LABELS[mode] || '—');

                        var mainRows = coVisibleRows('#pointList tbody');
                        var bonusRows = coVisibleRows('#bonusList tbody');

                        // Sum percentages directly from the visible rows (reliable on page load,
                        // before the calc engine's #totalpercentage field is populated).
                        var sumPct = function (rows, c1, c2) {
                            var t = 0;
                            rows.each(function () { t += coNum($(this).find('.' + c1).val()) || coNum($(this).find('.' + c2).val()); });
                            return t;
                        };
                        var mainPct = sumPct(mainRows, 'option_percentage', 'option_percentage_hourly')
                                      || coNum($('#totalpercentage').val()) || coNum($('#totalpercentagehouly').val());
                        var addPct = sumPct(bonusRows, 'bonus_option_percentage', 'bonus_option_percentage_hourly')
                                      || coNum($('#totalpercentagebonus').val()) || coNum($('#totalhourlypercentagebonus').val());

                        var body = '';
                        var pushRows = function (rows, monClass, yrClass, nameClass) {
                            rows.each(function () {
                                var $r = $(this);
                                var name = ($r.find('.' + nameClass).val() || $r.find('input[type=text]').first().val() || '').trim();
                                if (!name) return;
                                var amt = coView === 'monthly' ? coNum($r.find('.' + monClass).val()) : coNum($r.find('.' + yrClass).val());
                                body += '<tr><td>' + coEsc(name) + '</td><td class="text-end">' + coMoney(amt) + '</td></tr>';
                            });
                        };
                        pushRows(mainRows, 'offer_monthly', 'offer_yearly', 'offer_points');
                        pushRows(bonusRows, 'bonus_offer_monthly', 'bonus_offer_yearly', 'bonus_points');
                        $('#co_breakup_preview tbody').html(body || '<tr><td colspan="2" style="color:#a1a5b7">No components yet</td></tr>');

                        var tcc = coView === 'monthly' ? coNum($('#finalpaytotalMonthly').val()) : coNum($('#finalpaytotalYearly').val());
                        if (!tcc) tcc = coNum($('#finalpaytotalhourly').val());
                        $('#co_tcc').text(coMoney(tcc));
                        $('#co_breakup_mode').text(coView === 'monthly' ? 'Monthly' : 'Annual');

                        $('#co_legend_main').text(Math.round(mainPct) + '%');
                        $('#co_legend_add').text(Math.round(addPct) + '%');
                        if (typeof ApexCharts !== 'undefined') {
                            var opts = {
                                series: [mainPct || 0, addPct || 0], chart: { type: 'donut', height: 150, width: 150 },
                                labels: ['Main', 'Additional'], colors: ['#7239ea', '#50cd89'],
                                legend: { show: false }, dataLabels: { enabled: false },
                                plotOptions: { pie: { donut: { size: '62%' } } },
                                tooltip: { y: { formatter: function (v) { return Math.round(v) + '%'; } } }
                            };
                            try {
                                if (coChart) { coChart.updateOptions(opts); }
                                else { coChart = new ApexCharts(document.querySelector('#co_breakup_chart'), opts); coChart.render(); }
                            } catch (e) { }
                        }

                        var main100 = Math.abs(mainPct - 100) < 0.01;
                        var curOk = ($('#current_ctc_cur').val() !== '' && $('#current_ctc_mode').val() !== '');
                        var setChk = function (sel, pass) { $(sel).removeClass('co-chk--pass co-chk--fail').addClass(pass ? 'co-chk--pass' : 'co-chk--fail'); };
                        setChk('#co_checks li[data-check=main100]', main100);
                        setChk('#co_checks li[data-check=currency]', curOk);
                        setChk('#co_checks li[data-check=additional]', addPct >= 0);

                        $('#co_validation_text').text('Total of main components: ' + mainPct.toFixed(2) + '%' + (main100 ? ' — exactly 100%' : ''));
                        $('#co_validation_banner').removeClass('co-valid-banner--warn co-valid-banner--ok').addClass(main100 ? 'co-valid-banner--ok' : 'co-valid-banner--warn');

                        var addAmtTotal = coNum($('#totalYearlybonus').val()) || coNum($('#totalMonthlybonus').val()) || (coNum($('#ctc').val()) * addPct / 100);
                        $('#co_additional_total').html('Total additional components: <b>' + addPct.toFixed(2) + '%</b> (Allowed above 100%)'
                            + (addAmtTotal ? ' &nbsp;•&nbsp; Amount: <b>' + coMoney(addAmtTotal) + '</b>' : ''));

                        var ctcVal = coNum($('#ctc').val());
                        var mainAmt = ctcVal * (mainPct / 100);
                        var addAmt = ctcVal * (addPct / 100);
                        $('#co_howit_formula').html(
                            '<span class="co-chip">CTC ' + (ctcVal ? coMoney(ctcVal) : '—') + '</span> = ' +
                            '<span class="co-chip">Main 100.00% = ' + (ctcVal ? coMoney(mainAmt) : '—') + '</span> + ' +
                            '<span class="co-chip co-chip--add">Additional ' + addPct.toFixed(2) + '% = ' + (ctcVal ? coMoney(addAmt) : '—') + '</span> = ' +
                            '<span class="co-chip co-chip--total">Total Cost to Company = ' + (ctcVal ? coMoney(mainAmt + addAmt) : '—') + '</span>'
                        );

                        coEnhanceRows();
                    };

                    // ── Injects row #, drag-handle (visual only — does not reorder), and a
                    // Main/Additional badge next to each visible component's name. Runs on
                    // every refresh so numbers stay correct after rows are added/removed.
                    // Idempotent: skips a row if it already has the lead markup.
                    function coEnhanceRows() {
                        var enhance = function (tbodySel, nameClasses, badgeClass, badgeText) {
                            var n = 0;
                            coVisibleRows(tbodySel).each(function () {
                                n++;
                                var $name = $(this).find(nameClasses).first();
                                if (!$name.length) return;
                                // Make the name cell a flex row so the injected elements sit
                                // inline with the input instead of wrapping under it.
                                var $td = $name.closest('td').addClass('co-name-cell');
                                var $lead = $td.find('.co-row-lead');
                                if (!$lead.length) {
                                    $lead = $('<span class="co-row-lead"><span class="co-drag-handle">&#8942;&#8942;</span><span class="co-row-num"></span></span>');
                                    $name.before($lead);
                                    $name.after('<span class="co-comp-badge ' + badgeClass + '">' + badgeText + '</span>');
                                }
                                $td.find('.co-row-num').text(n);
                            });
                        };
                        enhance('#pointList tbody', '.offer_points, .offer_point_hourly', 'co-comp-badge--main', 'Main');
                        enhance('#bonusList tbody', '.bonus_points, .bonus_point_hourly', 'co-comp-badge--add', 'Additional');
                    }

                    $(document).on('click', '#co_view_toggle .co-view-btn', function () {
                        $('#co_view_toggle .co-view-btn').removeClass('co-view-btn--active');
                        $(this).addClass('co-view-btn--active');
                        coView = $(this).data('view');
                        refreshPreviewPanel();
                    });
                    $(document).on('click', '#co_calc_btn', function () { refreshPreviewPanel(); });
                    $(document).on('change input',
                        '.option_percentage, .option_percentage_hourly, .bonus_option_percentage, .bonus_option_percentage_hourly, .offer_monthly, .offer_yearly, .bonus_offer_monthly, .bonus_offer_yearly, .offer_points, #ctc, #current_ctc_mode, #current_ctc_cur',
                        function () { setTimeout(refreshPreviewPanel, 0); });
                    $(document).on('click', '.addRowBtn, .addRowBtnbonus, .remove_points', function () { setTimeout(refreshPreviewPanel, 30); });
                    $(document).ready(function () { setTimeout(refreshPreviewPanel, 200); });
                })();
            </script>
        </div>
