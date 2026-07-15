@php
    use App\Helper\GeneralMethod;
@endphp
<div class="card shadow-none rounded-0 w-100" id="offer_create_show_right_bar">
    <style>
        .text-justify {
            text-align: justify !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {

            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0.75em 0rem !important;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
                border-bottom: 1px dashed #f5f8fa !important;
            }
        }

        /* .swal2-html-container{
        text-align: left !important;
        } */

        /* Step container fills the full drawer width */
        .tab-container {
            width: 100%;
        }

        /* Hide all tabs by default */
        .tab-content-section {
            display: none;
        }

        /* Display the active tab */
        .tab-content-section.active {
            display: block;
        }

        /* Button styling */
        button {
            padding: 10px 20px;
            margin: 10px 5px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .prev-btn {
            background-color: #6c757d;
        }

        .prev-btn:hover {
            background-color: #5a6268;
        }

        .submit-btn {
            background-color: #28a745;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .offer-details table {
            width: 100%;
            /* All tables will be 100% of the container width */
            border-collapse: collapse;
            /* Make the borders collapse for a cleaner look */
        }

         @media only screen and (min-width: 1024px) and (max-width: 1440px) {
           .addRowBtnbonus{
               right: 17px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            .addRowBtnbonus{
                right: 39px;
            }
        }

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

        /* ── 5-step timeline stepper = the header (circle + title/sub-label, minimal font) ── */
        .co-stepper { display: flex; align-items: center; padding: 12px 60px 12px 22px; background: #fff; border-bottom: 1px solid #eff2f5; }
        .co-step { flex: 1 1 0; display: flex; align-items: center; gap: 10px; cursor: pointer; min-width: 0; }
        .co-step:last-child { flex: 0 0 auto; }
        /* connector line fills the space after each step's label, up to the next circle */
        .co-step:not(:last-child)::after { content: ''; flex: 1 1 auto; height: 2px; background: #e4e6ef; min-width: 16px; margin: 0 4px; }
        .co-step--done:not(:last-child)::after { background: #50cd89; }
        .co-step-node {
            width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px !important; background: #fff; border: 2px solid #e4e6ef; color: #b5b5c3;
            flex-shrink: 0; transition: all .2s;
        }
        .co-step-check { display: none; }
        .co-step-num { display: inline; }
        .co-step--current .co-step-node { background: #3699ff; border-color: #3699ff; color: #fff; }
        .co-step--done .co-step-node { background: #50cd89; border-color: #50cd89; color: #fff; }
        .co-step--done .co-step-check { display: inline; }
        .co-step--done .co-step-num { display: none; }
        .co-step-label { flex-shrink: 0; min-width: 0; }
        .co-step-title { font-size: 13px !important; font-weight: 600; color: #b5b5c3; white-space: nowrap; line-height: 1.2; }
        .co-step-status { font-size: 10px !important; color: #a1a5b7; white-space: nowrap; margin-top: 1px; }
        .co-step--current .co-step-title { color: #3699ff; }
        .co-step--done .co-step-title { color: #181c32; }
        @media (max-width: 900px) {
            .co-stepper { padding: 12px 44px 12px 14px; }
            .co-step-status { display: none; }
            .co-step-title { font-size: 11px !important; }
        }

        /* ── Colored square field/component icons (closest-match line icons) ── */
        .co-ic { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0; margin-right: 6px; }
        .co-ic svg { width: 13px; height: 13px; }
        .co-ic--blue   { background: #e8f1fd; color: #3699ff; }
        .co-ic--green  { background: #e8fff3; color: #17a673; }
        .co-ic--indigo { background: #eef2ff; color: #4f46e5; }
        .co-ic--amber  { background: #fff4e0; color: #d9822b; }
        .co-ic--red    { background: #fdeef0; color: #e24b4a; }
        .co-ic--purple { background: #f3ecfe; color: #7239ea; }

        /* ── Compensation Details card ── */
        .co-details-card { border: 1px solid #f1f1f4; border-radius: 12px; }
        .co-details-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .co-conf-wrap { min-width: 240px; }
        .co-conf-hint { display: block; font-size: 11px; color: #3699ff; margin-top: 3px; text-align: right; text-decoration: none; }
        .co-conf-hint:hover { text-decoration: underline; }
        .co-field-grid { margin: 0; }
        .co-field { margin-bottom: 14px; }
        .co-flabel { font-size: 12px !important; font-weight: 600; color: #3f4254; display: flex; align-items: center; margin-bottom: 5px; }
        .co-field-hint { font-size: 10px; color: #a1a5b7; margin-top: 3px; }
        #offer_create_show_right_bar .co-details-card .form-control,
        #offer_create_show_right_bar .co-details-card .form-select {
            background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 8px !important;
            min-height: 42px; height: 42px; font-size: 13px !important; color: #3f4254 !important; box-shadow: none !important;
        }
        #offer_create_show_right_bar .co-details-card .form-control:focus,
        #offer_create_show_right_bar .co-details-card .form-select:focus { border-color: #3699ff !important; }
        @media (max-width: 700px) { .co-field { flex: 0 0 100%; max-width: 100%; } }

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
        /* Total Main (indigo tint) — whole row, incl. empty gap cells, so every box lines up */
        #pointList tfoot tr.co-total-row > th { background: #eef3ff !important; }
        #pointList tfoot tr.co-total-row input { background: transparent !important; color: #4f46e5 !important; font-weight: 700 !important; border-color: transparent !important; }
        /* Main Components percentage total: flip blue → red when it isn't exactly 100%, so the
           mismatch is obvious at a glance (JS toggles .co-pct-invalid in updateTotalpercentage*). */
        #pointList tfoot tr.co-total-row input.co-pct-invalid { color: #f1416c !important; }
        #pointList tfoot tr.co-total-row th:has(> input.co-pct-invalid)::after { color: #f1416c !important; }
        /* Total Additional (violet tint — distinct from Main's indigo) — whole row, incl. empty gap cells */
        #bonusList tfoot tr.co-total-row > th { background: #f5f0ff !important; }
        #bonusList tfoot tr.co-total-row input { background: transparent !important; color: #7c3aed !important; font-weight: 700 !important; border-color: transparent !important; }
        /* Final CTC: header sits OUTSIDE the colour block; only the total row is tinted GREEN.
           Same box height/font-size as Total Main/Additional (reuse their input + th rules). */
        .co-finalctc-card { border: 1px solid #b7ebc6 !important; border-radius: 12px; }
        /* table-layout:fixed makes the declared column widths authoritative (not content-based),
           so Allocation/Currency/Monthly/Annually line up exactly under Total Main/Additional's columns. */
        #final_pay { background: transparent; border: none; margin-top: 4px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
        #final_pay thead th { background: transparent !important; }
        #final_pay .form-control, #final_pay .form-select {
            background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 7px !important;
            box-shadow: none !important; min-height: 36px; height: 36px; font-size: 13px !important;
            color: #3f4254 !important; padding: 4px 10px !important;
        }
        #final_pay tfoot th { vertical-align: middle; background: #e3f7e9 !important; }
        /* Must out-specificity "#final_pay .form-control" above (id+class) — add tfoot to the
           selector so this transparent override actually wins and the green shows through. */
        #final_pay tfoot .form-control, #final_pay tfoot .form-select { background: transparent !important; color: #0f7a3d !important; font-weight: 700 !important; border-color: transparent !important; box-shadow: none !important; }
        #final_pay tfoot input[value*="Total CTC"] { color: #0b5c2e !important; }

        /* Applicable column: widen so "Annually & Monthly" shows fully */
        #pointList .basic_view, #bonusList .bonus_view { min-width: 150px !important; width: auto !important; }
        #pointList thead th:nth-child(2), #bonusList thead th:nth-child(2) { min-width: 150px; }

        /* "%" INSIDE the Allocation input box (data rows); "×" multiplier sits in the spacer cell */
        .co-mult-sign { pointer-events: none; }
        #pointList td:has(> .option_percentage), #pointList td:has(> .option_percentage_hourly),
        #bonusList td:has(> .bonus_option_percentage), #bonusList td:has(> .bonus_option_percentage_hourly) { position: relative; }
        #pointList td:has(> .option_percentage)::after, #pointList td:has(> .option_percentage_hourly)::after,
        #bonusList td:has(> .bonus_option_percentage)::after, #bonusList td:has(> .bonus_option_percentage_hourly)::after {
            content: '%'; position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #a1a5b7; font-size: 12px; font-weight: 600; pointer-events: none;
        }
        #pointList .option_percentage, #pointList .option_percentage_hourly,
        #bonusList .bonus_option_percentage, #bonusList .bonus_option_percentage_hourly { padding-right: 26px !important; }

        /* "%" appended inside the Total (Main/Additional) + Final CTC percentage cells — sits right next to the number */
        .co-pct-sign { pointer-events: none; }
        #pointList tfoot .totalpercentage, #pointList tfoot .totalpercentagehouly,
        #bonusList tfoot .totalpercentagebonus, #bonusList tfoot .totalhourlypercentagebonus,
        #final_pay tfoot .totalpercentagefinal, #final_pay tfoot .totalpercentagefinalhourly {
            text-align: right; padding-right: 22px !important;
        }
        #pointList tfoot th:has(> .totalpercentage), #pointList tfoot th:has(> .totalpercentagehouly),
        #bonusList tfoot th:has(> .totalpercentagebonus), #bonusList tfoot th:has(> .totalhourlypercentagebonus),
        #final_pay th:has(> .totalpercentagefinal), #final_pay th:has(> .totalpercentagefinalhourly) { position: relative; }
        #pointList tfoot th:has(> .totalpercentage)::after, #pointList tfoot th:has(> .totalpercentagehouly)::after,
        #bonusList tfoot th:has(> .totalpercentagebonus)::after, #bonusList tfoot th:has(> .totalhourlypercentagebonus)::after,
        #final_pay th:has(> .totalpercentagefinal)::after, #final_pay th:has(> .totalpercentagefinalhourly)::after {
            content: '%'; position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            font-weight: 700; font-size: 12px; color: inherit; pointer-events: none;
        }

        /* ── Additional Components empty state ── */
        .co-empty-state { text-align: center; padding: 26px 20px; }
        .co-empty-illus { width: 56px; height: 56px; border-radius: 14px; background: #f1f5ff; color: #93a4f4; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; }
        .co-empty-illus svg { width: 30px; height: 30px; }
        .co-empty-title { font-size: 14px !important; font-weight: 600; color: #3f4254; }
        .co-empty-sub { font-size: 12px; color: #a1a5b7; margin: 4px 0 14px; }
        .co-empty-state .co-add-btn { display: inline-block; }
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
        .co-comp-badge--add  { background: #f5f0ff !important; color: #7c3aed !important; }   /* Additional = violet, matches the Additional card's total-row theme */

        /* ── Additional-components info line ── */
        .co-info-line { font-size: 12px; color: #7239ea; font-weight: 600; margin: 6px 0 12px; }

        /* ── How it works? / footer instructions ── */
        .co-howit-box { display: flex; gap: 28px; background: #f9fafd; border: 1px solid #eef1f7; border-radius: 10px; padding: 14px 18px; margin: 14px 0; }
        .co-howit-col { flex: 1; min-width: 0; }
        .co-howit-title { font-size: 12px; font-weight: 700; color: #181c32; display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .co-howit-ico { width: 14px; height: 14px; border-radius: 4px; background: #4f46e5; display: inline-block; flex-shrink: 0; }
        .co-howit-ico--amber { background: #f1a204; }
        .co-howit-text { font-size: 11px; color: #7e8299; line-height: 1.5; }
        @media (max-width: 900px) { .co-howit-box { flex-direction: column; gap: 14px; } }

        /* ── Footer actions: Close (outline) + Save & Next (blue), right-aligned ── */
        .co-footer-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; }
        .co-btn-close { background: #fff !important; border: 1px solid #d7dae3 !important; color: #5e6278 !important; font-weight: 600 !important; min-width: 110px; height: 40px; border-radius: 8px; }
        .co-btn-close:hover { background: #f5f8fa !important; border-color: #c4c9d6 !important; }
        .co-btn-save { background: #1b84ff !important; border: 1px solid #1b84ff !important; color: #fff !important; font-weight: 600 !important; min-width: 160px; height: 40px; border-radius: 8px; }
        .co-btn-save:hover { background: #0b6cdb !important; border-color: #0b6cdb !important; }
        /* Step 3 footer: Download Offer Letter pinned to the lower-left, Back/Continue stay right */
        .co-step3-footer { justify-content: space-between; }
        .co-step3-footer-right { display: flex; align-items: center; gap: 10px; }

        /* ══════════════════════════ Step 2 — Offer Letter Template ══════════════════════════
           Enterprise nested-card hierarchy, matching Step 1's language:
             Main card   = .co-tpl-editor-col / .co-tpl-side (white, bordered, shadowed)
             Sub card    = .co-tpl-subcard  (light recessed panel inside a main card)
             Sub-sub card= .co-var-chip, .co-tpl-badge (small elements inside a sub card)
           Every rule below is scoped so nothing exceeds 14px (stricter than step 1's 16px cap).
           No extra server round-trips: search/tabs/zoom/preview/download all run client-side. */
        .co-tpl-title { font-size: 14px !important; font-weight: 700; color: #181c32; margin: 0; }
        .co-tpl-sub { font-size: 11px; color: #7e8299; margin-top: 2px; }
        .co-tpl-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 14px; flex-wrap: wrap; }

        /* Templates dropdown — same row as the "Offer Letter Template" heading (was a whole sidebar
           card with a search box for what is always exactly one template). */
        .co-tpl-select-wrap { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .co-tpl-select-label { font-size: 12px; font-weight: 700; color: #181c32; white-space: nowrap; }
        .co-tpl-select { font-size: 12px !important; height: 36px; min-width: 220px; border: 1px solid #e4e6ef; border-radius: 8px; padding: 4px 10px; background: #fff; color: #3f4254; }
        .co-tpl-select:focus { outline: none; border-color: #1b84ff; }

        /* Page + main cards are plain white — no grey/lavender fill anywhere */
        #step2, .co-tpl-layout { background: #fff; }
        .co-tpl-layout { display: flex; align-items: flex-start; gap: 16px; }
        .co-tpl-editor-col { flex: 1 1 auto; min-width: 0; }
        .co-tpl-side { flex: 0 0 210px; max-width: 210px; display: flex; flex-direction: column; }

        /* Main cards (level 1) — reuse Step 1's exact card language: white, bordered, shadowed */
        .co-tpl-editor-col, .co-tpl-side {
            background: #fff; border: 1px solid #eef1f7; border-radius: 14px;
            box-shadow: 0 1px 3px rgba(24,28,50,.05); padding: 14px;
        }

        /* Sub cards (level 2) — light recessed panel grouping related fields inside a main card */
        .co-tpl-subcard { background: #f9fafd; border: 1px solid #eef1f7; border-radius: 10px; padding: 10px 12px; margin-bottom: 10px; }

        /* Template editor card */
        .co-tpl-tabs { display: flex; gap: 4px; border-bottom: 1px solid #eef1f7; margin-bottom: 12px; }
        .co-tpl-tab { background: none; border: none; padding: 7px 12px; font-size: 12px !important; font-weight: 600; color: #a1a5b7; border-bottom: 2px solid transparent; cursor: pointer; }
        .co-tpl-tab--active { color: #1b84ff; border-bottom-color: #1b84ff; }
        .co-tpl-meta-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .co-tpl-meta-item { font-size: 10px; color: #7e8299; }
        .co-tpl-meta-item b { color: #181c32; font-weight: 600; margin-left: 4px; }

        /* Letterhead sub card — actual company logo (image, never text) left, address right;
           shown identically in the editor and the preview so what you see is what the candidate gets. */
        .co-letterhead { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .co-letterhead-logo img { max-height: 40px; max-width: 150px; object-fit: contain; display: block; }
        .co-letterhead-address { text-align: right; font-size: 10px; color: #7e8299; line-height: 1.5; max-width: 260px; }
        .co-letterhead-company { font-size: 12px; font-weight: 700; color: #181c32; }

        .co-tpl-zoom { display: flex; align-items: center; gap: 6px; }
        .co-tpl-zoom-btn { width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e4e6ef; background: #fff; color: #5e6278; font-weight: 700; font-size: 12px !important; cursor: pointer; line-height: 1; }
        .co-tpl-zoom-btn:hover { background: #f5f8fa; }
        .co-tpl-zoom-pct { font-size: 10px; color: #7e8299; min-width: 30px; text-align: center; display: inline-block; }
        .co-tpl-download-btn { font-size: 10px !important; font-weight: 600; color: #4f46e5; background: #eef3ff; border: none; border-radius: 7px; padding: 5px 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
        .co-tpl-download-btn:hover { background: #e0e7ff; }
        .co-tpl-download-btn svg { width: 11px; height: 11px; }
        .co-tpl-preview { border: 1px solid #eef1f7; border-radius: 10px; padding: 18px; min-height: 320px; max-height: 460px; overflow: auto; font-size: 12px; background: #fff; }

        /* Insert Variables + info panel (sub cards, level 2) */
        .co-tpl-side-title { font-size: 12px; font-weight: 700; color: #181c32; }
        .co-tpl-side-sub { font-size: 10px; color: #7e8299; margin: 3px 0 8px; line-height: 1.4; }
        .co-var-chips { display: flex; flex-wrap: wrap; gap: 5px; }
        .co-var-chip { font-size: 10px !important; font-weight: 600; color: #4f46e5; background: #eef3ff; border: 1px solid #e0e7ff; border-radius: 7px; padding: 4px 8px; cursor: pointer; white-space: nowrap; }
        .co-var-chip:hover { background: #e0e7ff; }
        .co-var-chip:disabled { opacity: .5; cursor: not-allowed; }
        .co-tpl-note { font-size: 10px; color: #7e8299; line-height: 1.5; }
        .co-tpl-note a { color: #1b84ff; font-weight: 600; }
        /* Reset sits directly under the "About this template" sub card, not floated to a corner */
        .co-btn-reset { width: 100%; background: #fff !important; border: 1px solid #f1416c !important; color: #f1416c !important; font-weight: 600 !important; height: 36px; border-radius: 8px; font-size: 11px !important; }
        .co-btn-reset:hover { background: #fff5f8 !important; }

        /* Shared page-level footer (Back / Save & Next) — one bar under all three cards, bottom-right,
           same position regardless of which tab (Editor/Preview) is active. */
        .co-tpl-footer { margin-top: 16px; }

        /* Responsive: collapse to a single column under ~1100px (fits smaller 15.6" / 13" laptops) */
        @media (max-width: 1100px) {
            .co-tpl-layout { flex-direction: column; }
            .co-tpl-side { flex: 1 1 auto; max-width: none; width: 100%; }
            .co-tpl-header { flex-direction: column; align-items: stretch; }
            .co-tpl-select-wrap { width: 100%; }
            .co-tpl-select { flex: 1 1 auto; min-width: 0; }
        }

        /* ══════════════════════════ Step 4 — Approval ══════════════════════════
           Same enterprise card language as steps 2/3. Capped at 12px, Poppins, white background. */
        #step4, #step4 * { font-family: 'Poppins', sans-serif; }
        #step4 { background: #fff; font-size: 12px; }
        #step4 h1, #step4 h2, #step4 h3, #step4 h4, #step4 h5 { font-size: 12px !important; font-weight: 700; color: #181c32; margin: 0; }

        .co-appr-info-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .co-appr-info-card { flex: 1 1 160px; background: #fff; border: 1px solid #eef1f7; border-radius: 12px; box-shadow: 0 1px 3px rgba(24,28,50,.05); padding: 12px; display: flex; gap: 10px; align-items: flex-start; }
        .co-appr-info-ico { width: 30px; height: 30px; border-radius: 8px; background: #eef3ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .co-appr-info-ico svg { width: 15px; height: 15px; }
        .co-appr-info-label { font-size: 10px; color: #7e8299; }
        .co-appr-info-value { font-size: 12px; font-weight: 700; color: #181c32; margin-top: 2px; }
        .co-appr-info-sub { font-size: 10px; color: #a1a5b7; margin-top: 1px; }

        .co-appr-layout { display: flex; align-items: flex-start; gap: 16px; }
        .co-appr-main { flex: 1 1 auto; min-width: 0; background: #fff; border: 1px solid #eef1f7; border-radius: 14px; box-shadow: 0 1px 3px rgba(24,28,50,.05); padding: 18px; }
        .co-appr-side { flex: 0 0 260px; max-width: 260px; display: flex; flex-direction: column; gap: 12px; }
        .co-appr-side-card { background: #fff; border: 1px solid #eef1f7; border-radius: 14px; box-shadow: 0 1px 3px rgba(24,28,50,.05); padding: 14px; }
        .co-appr-side-title { font-size: 12px !important; font-weight: 700; color: #181c32; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; }

        .co-appr-empty-state { text-align: center; padding: 30px 14px; }
        .co-appr-empty-ico { width: 64px; height: 64px; border-radius: 50%; border: 2px dashed #e0e7ff; background: #f5f7ff; color: #93a4f4; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .co-appr-empty-ico svg { width: 30px; height: 30px; }
        .co-appr-empty-title { font-size: 12px !important; font-weight: 700; color: #181c32; margin-bottom: 4px; }
        .co-appr-empty-sub { font-size: 11px; color: #7e8299; margin-bottom: 14px; }
        .co-appr-note { display: flex; gap: 8px; align-items: flex-start; background: #f3f8ff; border: 1px solid #dbe9ff; border-radius: 10px; padding: 10px 12px; font-size: 11px; color: #3f4254; margin-top: 14px; }
        .co-appr-note svg { width: 14px; height: 14px; color: #1b84ff; flex-shrink: 0; margin-top: 1px; }

        .co-appr-detail-row { display: flex; justify-content: space-between; gap: 10px; font-size: 11px; padding: 6px 0; border-bottom: 1px solid #f4f4f6; }
        .co-appr-detail-row:last-child { border-bottom: none; }
        .co-appr-detail-label { color: #7e8299; }
        .co-appr-detail-value { color: #181c32; font-weight: 600; text-align: right; }

        /* ── Approval Timeline ── */
        /* Shared column widths — used by BOTH the header row and each data card */
        .co-appr-tl-grid { display: grid; grid-template-columns: 1fr 130px 160px 190px; align-items: center; gap: 0; }
        /* Header row: same flex shell as data rows but no card styling */
        .co-appr-tl-cols { display: flex; align-items: center; padding-bottom: 10px; }
        .co-appr-tl-cols-spacer { width: 52px; flex-shrink: 0; }
        .co-appr-tl-col-hd { font-size: 10px; font-weight: 700; color: #a1a5b7; text-transform: uppercase; letter-spacing: .5px; padding: 0 14px 0 0; }
        .co-appr-tl-col-hd:first-child { padding-left: 0; }
        /* Card — fills the space right of the dot column */
        .co-appr-tl-card { background: #fff; border: 1.5px solid #eef1f7; border-radius: 12px; padding: 12px 14px; }
        .co-appr-tl-card--active   { border-color: #f59e0b; background: #fffbf3; }
        .co-appr-tl-card--approved { border-color: #eef1f7; background: #fff; }
        .co-appr-tl-card--rejected { border-color: #fca5a5; background: #fff7f7; }
        /* Dot column with connector line */
        .co-appr-tl-dot-wrap { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; height: 100%; position: relative; padding: 0; }
        .co-appr-tl-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; z-index: 1; }
        .co-appr-tl-dot svg { width: 18px; height: 18px; }
        .co-appr-tl-dot--approved { background: #22c55e; color: #fff; box-shadow: 0 0 0 4px #bbf7d0; }
        .co-appr-tl-dot--active   { background: #f59e0b; color: #fff; box-shadow: 0 0 0 4px #fde68a; }
        .co-appr-tl-dot--waiting  { background: #3b82f6; color: #fff; box-shadow: 0 0 0 4px #bfdbfe; }
        .co-appr-tl-dot--rejected { background: #ef4444; color: #fff; box-shadow: 0 0 0 4px #fca5a5; }
        /* Dashed vertical connector between cards */
        .co-appr-tl-connector { width: 2px; flex: 1; min-height: 14px; background: repeating-linear-gradient(to bottom, #d1d5db 0 4px, transparent 4px 8px); margin: 4px 0; }
        /* Approver cell */
        .co-appr-tl-appr { display: flex; align-items: center; gap: 10px; padding-right: 10px; }
        .co-appr-tl-avatar { width: 38px; height: 38px; border-radius: 50%; background: #e8ecf5; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
        .co-appr-tl-name { font-size: 12px; font-weight: 700; color: #181c32; line-height: 1.3; }
        .co-appr-tl-sub  { font-size: 10px; color: #7e8299; margin-top: 2px; }
        /* Role */
        .co-appr-tl-role { font-size: 11px; color: #5e6278; padding-right: 10px; }
        /* Status cell */
        .co-appr-tl-status { display: flex; flex-direction: column; gap: 4px; padding-right: 10px; }
        .co-appr-tl-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 3px 12px; border-radius: 20px; white-space: nowrap; }
        .co-appr-tl-badge--approved { background: #dcfce7; color: #15803d; }
        .co-appr-tl-badge--active   { background: #fef3c7; color: #d97706; }
        .co-appr-tl-badge--waiting  { background: #ede9fe; color: #7c3aed; }
        .co-appr-tl-badge--rejected { background: #fee2e2; color: #dc2626; }
        .co-appr-tl-date { font-size: 10px; color: #a1a5b7; }
        /* Reminder cell */
        .co-appr-tl-rem { display: flex; flex-direction: column; gap: 5px; }
        .co-appr-tl-rem-row { display: flex; align-items: center; gap: 6px; }
        .co-appr-tl-rem-btn { display: inline-flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 600; color: #1b84ff; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 7px; padding: 4px 10px; cursor: pointer; white-space: nowrap; }
        .co-appr-tl-rem-btn:hover { background: #dbeafe; }
        .co-appr-tl-rem-btn svg { width: 11px; height: 11px; }
        .co-appr-tl-kebab { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; color: #a1a5b7; cursor: pointer; border: 1px solid #eef1f7; background: #fff; flex-shrink: 0; }
        .co-appr-tl-kebab:hover { background: #f4f4f6; color: #181c32; }
        .co-appr-tl-kebab svg { width: 13px; height: 13px; }
        .co-appr-tl-last-sent { font-size: 10px; color: #a1a5b7; }
        /* Add Approver dashed button */
        .co-appr-add-dashed { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #1b84ff; background: transparent; border: 1.5px dashed #93c5fd; border-radius: 8px; padding: 6px 16px; cursor: pointer; white-space: nowrap; transition: background .15s; }
        .co-appr-add-dashed:hover { background: #eff6ff; border-color: #1b84ff; }
        .co-appr-add-dashed svg { width: 13px; height: 13px; }
        /* Rejection note */
        .co-appr-status-note { font-size: 10px; color: #5e6278; font-style: italic; margin-top: 8px; background: #fff5f5; border-radius: 8px; padding: 6px 10px; border: 1px solid #fca5a5; }

        /* Activity log — real entries from the updatetimeline table */
        .co-appr-activity-item { padding: 8px 0; border-bottom: 1px solid #f4f4f6; }
        .co-appr-activity-item:last-child { border-bottom: none; }
        .co-appr-activity-text { font-size: 11px; color: #181c32; font-weight: 600; }
        .co-appr-activity-time { font-size: 10px; color: #a1a5b7; margin-top: 2px; }

        @media (max-width: 1100px) {
            .co-appr-layout { flex-direction: column; }
            .co-appr-side { flex: 1 1 auto; max-width: none; width: 100%; }
        }

        /* ══════════════════════════ Step 5 — Send Offer Letter (native full composer) ══════════════════════════ */
        #step5 { background: #fff; }
        #step5, #step5 * { font-family: 'Poppins', sans-serif; }
        .co-step5-final { font-size: 10px !important; font-weight: 700; color: #7239ea; background: #f5f0ff; border-radius: 99px; padding: 2px 9px; vertical-align: middle; margin-left: 6px; }

        /* MAIN CARD wrapping the whole composer */
        .co-send-card { background: #fff; border: 1px solid #eef1f7; border-radius: 16px; box-shadow: 0 1px 3px rgba(24,28,50,.05); padding: 20px 22px; }
        /* SUB CARD: candidate hero — matches the "Krishna Murthy" reference */
        /* Hero fonts capped at 12px. Facts sit on their own full-width row below the avatar/status
           row (rather than sharing that row) so all 6 columns have room to stay in one single row
           on a 15.6" laptop panel instead of wrapping. */
        .co-send-hero { display: flex; flex-direction: column; gap: 12px; background: #fff; border: 1px solid #eaeefb; border-radius: 14px; padding: 14px 18px; margin-bottom: 20px; }
        .co-send-hero-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .co-send-hero-lead { display: flex; align-items: center; gap: 12px; flex: 1 1 200px; min-width: 180px; }
        .co-send-hero-avatar { width: 46px; height: 46px; border-radius: 50%; background: #e5eaff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 700; flex-shrink: 0; overflow: hidden; }
        .co-send-hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .co-send-hero-name { font-size: 12px !important; font-weight: 700; color: #181c32; }
        .co-send-hero-role { font-size: 11px; color: #1b84ff; font-weight: 600; margin-top: 2px; }
        .co-send-hero-meta { font-size: 10px; color: #7e8299; margin-top: 6px; display: flex; gap: 8px; flex-wrap: wrap; }
        .co-send-hero-meta span { display: inline-flex; align-items: center; gap: 5px; background: #eef1f7; border-radius: 6px; padding: 2px 8px; }
        .co-send-hero-meta svg { width: 12px; height: 12px; color: #a1a5b7; }
        /* Grid (not flex) so every column sits in a fixed slot — position doesn't shift between
           offers even when a value (e.g. Sent By) is blank or a different length. Fractional (fr)
           columns so it shrinks instead of overflowing, but always exactly one row of 6. */
        .co-send-hero-facts { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); column-gap: 12px; align-items: start; }
        .co-send-fact-l { font-size: 9px; color: #a1a5b7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .co-send-fact-v { font-size: 12px; font-weight: 700; color: #181c32; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .co-send-fact-v.co-send-fact-ctc { color: #0f9463; }
        .co-send-fact-v.co-send-fact-expiry { color: #d9214e; }
        .co-send-fact-v.co-send-fact-sent { color: #0f9463; }
        .co-send-fact-v.co-send-fact-notsent { color: #a1a5b7; }
        .co-send-fact-v.co-send-fact-sentby { display: flex; flex-direction: column; gap: 2px; min-height: 1.1em; }
        .co-send-sentby-name { font-size: 11px; font-weight: 700; color: #181c32; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .co-send-sentby-when { font-size: 9px; font-weight: 600; color: #a1a5b7; white-space: nowrap; }
        .co-send-hero-status { border-radius: 12px; padding: 8px 14px; text-align: center; min-width: 130px; }
        .co-send-hero-status--approved { background: #e3f7e9; border: 1px solid #b7ebc6; }
        .co-send-hero-status--pending { background: #fff8e1; border: 1px solid #f6e6b8; }
        .co-send-status-title { font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; justify-content: center; }
        .co-send-hero-status--approved .co-send-status-title { color: #0f7a3d; }
        .co-send-hero-status--pending .co-send-status-title { color: #8a6d00; }
        .co-send-status-title svg { width: 14px; height: 14px; }
        .co-send-status-sub { font-size: 10px; color: #5e6278; margin-top: 3px; }

        /* Form rows: icon label left, field right */
        .co-send-form { display: flex; flex-direction: column; }
        .co-send-row { display: flex; align-items: flex-start; gap: 20px; padding: 16px 0; border-bottom: 1px solid #f4f4f6; }
        .co-send-row:last-child { border-bottom: none; }
        .co-send-label { flex: 0 0 170px; font-size: 13px; font-weight: 600; color: #3f4254; display: flex; align-items: center; gap: 8px; padding-top: 10px; }
        .co-send-label svg { width: 16px; height: 16px; color: #4f46e5; flex-shrink: 0; }
        .co-send-req { color: #f1416c; }
        .co-send-field { flex: 1 1 auto; min-width: 0; }
        .co-send-input { width: 100%; height: 44px; border: 1px solid #e4e6ef; border-radius: 10px; padding: 8px 14px; font-size: 13px; color: #3f4254; background: #fff; }
        .co-send-input:focus { outline: none; border-color: #1b84ff; }
        .co-send-input[readonly] { background: #f9fafd; color: #5e6278; }
        .co-send-hint { font-size: 11px; color: #a1a5b7; margin-top: 7px; }

        /* Chip inputs (To / CC): one compact box holding chips + the input, kept as short as the chip row allows */
        .co-send-chips { min-width: 0; min-height: 38px; border: 1px solid #e4e6ef; border-radius: 10px; padding: 4px 8px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; background: #fff; }
        .co-send-chips:focus-within { border-color: #1b84ff; }
        .co-send-chip { display: inline-flex; align-items: center; gap: 6px; background: #eef3ff; color: #4f46e5; font-size: 12px; font-weight: 600; border-radius: 7px; padding: 5px 10px; }
        .co-send-chip .co-chip-x { cursor: pointer; font-style: normal; font-size: 14px; line-height: 1; opacity: .7; }
        .co-send-chip .co-chip-x:hover { opacity: 1; }
        .co-send-chip-input { flex: 1 1 100px; min-width: 60px; border: none; outline: none; font-size: 13px; padding: 4px; background: transparent; }
        /* "+ Add Email" reads as plain text, not a button, pinned to the box's right edge */
        .co-send-addemail { flex-shrink: 0; margin-left: auto; background: none; border: none; color: #4f46e5; font-size: 12px; font-weight: 700; padding: 2px 4px; cursor: pointer; white-space: nowrap; }
        .co-send-addemail:hover { text-decoration: underline; }
        /* To Email ID: locked to the candidate's address — no remove, no typing, no add control */
        .co-send-chips--locked { background: #f8f9fb; }
        .co-send-chip--locked { background: #eef1f7; color: #3f4254; cursor: default; }
        .co-chip-lock { width: 12px; height: 12px; opacity: .55; }
        .co-send-attach-count { font-size: 11px; color: #a1a5b7; font-weight: 600; }
        .co-send-attachrow { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
        .co-send-attachrow:empty { margin-bottom: 0; }
        .co-send-attach-chip { display: inline-flex; align-items: center; gap: 9px; background: #fff; border: 1px solid #e4e6ef; border-radius: 9px; padding: 8px 12px; min-width: 190px; }
        .co-send-attach-ico { width: 30px; height: 30px; border-radius: 7px; background: #fde8ec; color: #d9214e; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .co-send-attach-ico svg { width: 14px; height: 14px; }
        .co-send-attach-ico.co-send-attach-ico--doc { background: #e7edff; color: #3568e8; }
        .co-send-attach-meta { min-width: 0; }
        .co-send-attach-name { font-size: 12px; font-weight: 600; color: #181c32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
        .co-send-attach-size { font-size: 10px; color: #a1a5b7; }
        .co-send-attach-x { margin-left: 4px; cursor: pointer; color: #a1a5b7; font-size: 16px; line-height: 1; flex-shrink: 0; }
        .co-send-attach-x:hover { color: #f1416c; }
        .co-send-addattach { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1px dashed #c4cada; color: #4f46e5; font-size: 12px; font-weight: 600; border-radius: 10px; padding: 9px 16px; cursor: pointer; }
        .co-send-addattach:hover { background: #f8f9ff; }
        .co-send-addattach svg { width: 13px; height: 13px; }

        /* Signature toggle */
        .co-send-sigrow { display: flex; align-items: center; gap: 12px; padding-top: 10px; }
        .co-send-switch { position: relative; display: inline-block; width: 42px; height: 22px; flex-shrink: 0; }
        .co-send-switch input { opacity: 0; width: 0; height: 0; }
        .co-send-slider { position: absolute; inset: 0; background: #d7dae3; border-radius: 99px; transition: .2s; cursor: pointer; }
        .co-send-slider::before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; }
        .co-send-switch input:checked + .co-send-slider { background: #1b84ff; }
        .co-send-switch input:checked + .co-send-slider::before { transform: translateX(20px); }
        .co-send-sigtext { font-size: 13px; color: #3f4254; }

        /* Body / CKEditor */
        .co-send-row--body .co-send-field { padding-top: 0; }
        #step5 .cke_chrome { border-radius: 10px !important; border-color: #e4e6ef !important; }

        /* Footer */
        .co-send-footer { display: flex; align-items: center; justify-content: space-between; gap: 14px; border-top: 1px solid #eef1f7; padding: 16px 0 4px; margin-top: 8px; flex-wrap: wrap; }
        .co-send-footer-note { font-size: 11px; color: #7e8299; display: inline-flex; align-items: center; gap: 6px; }
        .co-send-footer-note svg { width: 14px; height: 14px; color: #a1a5b7; }
        .co-send-footer-actions { display: flex; align-items: center; gap: 10px; margin-left: auto; }
        .co-send-submit { background: linear-gradient(90deg,#4f46e5,#1b84ff) !important; border: none !important; color: #fff !important; font-weight: 600 !important; font-size: 13px !important; min-width: 180px; height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; }
        .co-send-submit:hover { filter: brightness(.95); }
        .co-send-submit:disabled { opacity: .7; cursor: not-allowed; }

        /* Add-Email drawer — anchored to #offer_create_show_right_bar (position:relative), full height,
           wide enough for the 4-column member table (translateX avoids magic offsets). */
        .co-send-drawer-bd { display: none; position: absolute; inset: 0; background: rgba(24,28,50,.4); z-index: 1000; }
        .co-send-drawer-bd.co-send-drawer-bd--open { display: block; }
        .co-send-drawer { position: absolute; top: 0; right: 0; bottom: 0; width: 560px; max-width: 96%; background: #fff; box-shadow: -8px 0 30px rgba(24,28,50,.22); z-index: 1001; display: flex; flex-direction: column; transform: translateX(100%); transition: transform .25s ease; visibility: hidden; }
        .co-send-drawer.co-send-drawer--open { transform: translateX(0); visibility: visible; }
        .co-send-drawer-head { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #eef1f7; flex-shrink: 0; }
        .co-send-drawer-title { font-size: 14px !important; font-weight: 700; color: #181c32; }
        .co-send-drawer-sub { font-size: 11px; color: #7e8299; margin-top: 2px; }
        .co-send-drawer-x { background: #f5f8fa; border: none; width: 32px; height: 32px; border-radius: 8px; font-size: 18px; color: #7e8299; cursor: pointer; line-height: 1; flex-shrink: 0; }
        .co-send-drawer-x:hover { background: #eef1f7; color: #f1416c; }
        .co-send-drawer-body { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; padding: 16px 20px 20px; }
        /* Source cards (Internal / Client) — the previous approver-picker design */
        .co-appr-source-row { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
        .co-appr-source-card { flex: 1 1 230px; display: flex; align-items: center; gap: 10px; cursor: pointer; border: 1px solid #e4e6ef; border-radius: 10px; padding: 10px 12px; background: #fff; margin: 0; min-width: 0; }
        .co-appr-source-card input { margin: 0 6px 0 0; flex-shrink: 0; }
        .co-appr-source-card--active { border-color: #1b84ff; background: #f3f8ff; }
        .co-appr-source-ico { width: 30px; height: 30px; border-radius: 8px; background: #eef3ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .co-appr-source-ico svg { width: 16px; height: 16px; }
        .co-appr-source-card > div { min-width: 0; }
        .co-appr-source-title { font-size: 12px; font-weight: 700; color: #181c32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .co-appr-source-sub { font-size: 10px; color: #7e8299; margin-top: 1px; }
        .co-appr-search { margin-bottom: 12px; }
        .co-appr-search input { width: 100%; height: 40px; font-size: 12px; border: 1px solid #e4e6ef; border-radius: 9px; padding: 6px 12px; }
        .co-appr-search input:focus { outline: none; border-color: #1b84ff; }
        .co-appr-empty { font-size: 11px; color: #a1a5b7; text-align: center; padding: 16px 4px; }
        /* Search bar + inline "add a custom email not in the list" action, one pill row */
        .co-send-searchrow { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .co-send-searchbox { flex: 1 1 auto; min-width: 0; display: flex; align-items: center; gap: 8px; height: 42px; background: #f8f9fb; border: 1px solid #e4e6ef; border-radius: 21px; padding: 0 16px; }
        .co-send-searchbox svg { width: 15px; height: 15px; color: #a1a5b7; flex-shrink: 0; }
        .co-send-searchbox:focus-within { border-color: #1b84ff; background: #fff; }
        .co-send-drawer-search-input { flex: 1 1 auto; min-width: 0; border: none; background: transparent; outline: none; font-size: 12px; height: 100%; }
        .co-send-addmore { flex-shrink: 0; border: none; background: none; color: #4f46e5; font-weight: 700; font-size: 12px; white-space: nowrap; cursor: pointer; padding: 0 4px; }
        .co-send-addmore:disabled { color: #c4c7d6; cursor: not-allowed; }
        /* Member table — table-layout fixed so 4 columns fit the drawer; long text truncates */
        #co_send_member_table { width: 100%; table-layout: fixed; font-size: 11px; }
        #co_send_member_table thead th { font-size: 10px !important; color: #a1a5b7; font-weight: 600; padding: 6px 6px; border-bottom: 1px solid #eef1f7; text-transform: none; text-align: left; }
        #co_send_member_table th:nth-child(1) { width: 42%; }
        #co_send_member_table th:nth-child(2) { width: 26%; }
        #co_send_member_table th:nth-child(3) { width: 20%; }
        #co_send_member_table th:nth-child(4) { width: 12%; }
        #co_send_member_table tbody td { padding: 9px 6px; border-bottom: 1px dashed #f1f1f4; vertical-align: middle; color: #5e6278; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #co_send_member_table tbody td:first-child { white-space: normal; }
        .co-send-mrow-av { width: 30px; height: 30px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-right: 8px; flex-shrink: 0; }
        #co_send_member_table .co-send-mrow-role { font-size: 9px !important; }
        .co-send-mrow-add { padding: 5px 12px !important; font-size: 11px !important; font-weight: 600; }
        .co-send-mrow-add.co-send-mrow--added { background: #e8fff3 !important; color: #0f9463 !important; }

        @media (max-width: 900px) {
            .co-send-row { flex-direction: column; gap: 8px; }
            .co-send-label { flex: none; padding-top: 0; }
            .co-send-hero-facts { margin-left: 0; }
            .co-send-hero-lead { min-width: 0; }
        }
    </style>
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
                    CKEDITOR.replace('editorterms1', {
                        toolbar: [
                            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                            { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                            { name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
                            { name: 'links', items: ['Link'] }
                        ]
                    });
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

        // Template switcher — swap CKEditor content when the dropdown changes
        (function () {
            var companyTpl = {!! json_encode($company_offer_terms ?? '') !!};
            var agencyTpl  = {!! json_encode($agency_offer_terms ?? '') !!};

            function setEditorContent(html) {
                if (CKEDITOR.instances['editorterms1']) {
                    CKEDITOR.instances['editorterms1'].setData(html);
                } else {
                    var el = document.getElementById('editorterms1');
                    if (el) el.value = html;
                }
            }

            document.addEventListener('change', function (e) {
                if (e.target && e.target.id === 'co_tpl_select') {
                    var val = e.target.value;
                    if (val === 'agency-offer-letter-template') {
                        setEditorContent(agencyTpl);
                    } else {
                        setEditorContent(companyTpl);
                    }
                }
            });
        })();
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




    {{-- Header title "Compensation" removed — the 5-step timeline is the header. The close
         (X) button is floated to the top-right corner so the header bar takes NO height. --}}
    <div class="card-header border-0" id="kt_activities_header" style="min-height:0;padding:0;position:absolute;top:8px;right:12px;z-index:6;">
          
        @php
            // Guard: $userPermissions is normally provided by middleware; default to []
            // so an undefined-variable warning can never become a 500 for this AJAX view.
            $userPermissions = $userPermissions ?? [];
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
                <div class="co-step-status">Build salary structure</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="2" onclick="goToStepNav(2)">
            <div class="co-step-node"><span class="co-step-num">2</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Template</div>
                <div class="co-step-status">Select offer template</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="3" onclick="goToStepNav(3)">
            <div class="co-step-node"><span class="co-step-num">3</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Preview Offer</div>
                <div class="co-step-status">Review before approval</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="4" onclick="goToStepNav(4)">
            <div class="co-step-node"><span class="co-step-num">4</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Approval</div>
                <div class="co-step-status">Get approver sign-off</div>
            </div>
        </div>
        <div class="co-step co-step--pending" data-step="5" onclick="goToStepNav(5)">
            <div class="co-step-node"><span class="co-step-num">5</span><span class="co-step-check">&#10003;</span></div>
            <div class="co-step-label">
                <div class="co-step-title">Offer Sent</div>
                <div class="co-step-status">Send to candidate</div>
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

                                <div
                                    class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2"> <span
                                            class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$application->full_name) }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);"
                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                            <span class="svg-icon svg-icon-4 me-1">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z"
                                                            fill="#000000" />
                                                        <path
                                                            d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                    </g>
                                                </svg>
                                            </span> {{ ucwords(@$application->total_exp) }} Years </a> <a
                                            href="javascript:void(0);"
                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                            <!--begin::Svg Icon-->
                                            <span class="svg-icon svg-icon-4 me-1">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="2" y="2" width="10"
                                                            height="12" rx="2" />
                                                        <path
                                                            d="M4,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,8 C2,6.8954305 2.8954305,6 4,6 Z M18,16 C19.1045695,16 20,15.1045695 20,14 C20,12.8954305 19.1045695,12 18,12 C16.8954305,12 16,12.8954305 16,14 C16,15.1045695 16.8954305,16 18,16 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            @if ($application->condidate_type == 'other')
                                                {!! @$current_ctc_cur !!}&nbsp;
                                                {!! @$application->ctc !!}-{!! @$application->ectc !!}
                                            @else
                                                {!! @$current_ctc_cur !!}&nbsp; {!! @$application->bill_rate_amount !!}
                                            @endif

                                            @php
                                                $current_ctc_mode = 0;
                                                if ($application->condidate_type == 'other') {
                                                    $current_ctc_mode = @$application->current_ctc_mode;
                                                } else {
                                                    $current_ctc_mode = @$application->bill_rate_mode;
                                                }

                                                echo $salaryMode[$current_ctc_mode];
                                            @endphp
                                        </a>

                                        <a href="javascript:void(0);"
                                            class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                            <span class="svg-icon svg-icon-4 me-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3"
                                                        d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z"
                                                        fill="black"></path>
                                                    <path
                                                        d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z"
                                                        fill="black"></path>
                                                </svg>
                                            </span> {{ ucwords(@$city_name) }}, {{ ucwords(@$state_name) }},
                                            {{ ucwords(@$country_name) }}
                                        </a>

                                    </div>
                                </div>

                            </div>
                            <div class="d-flex flex-wrap flex-stack">
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="row w-75"> <span class="">
                                            @php
                                                $skills = '';
                                                $skills = @$application->skills;
                                                if (!empty($skills)) {
                                                    $skillsArr = '';
                                                    $skillsArr = explode(',', $skills);
                                                    foreach ($skillsArr as $sval) {
                                                        echo '<span class="badge badge-light me-3">' .
                                                            @$sval .
                                                            '</span>';
                                                    }
                                                }
                                            @endphp
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                        
                            <!-- Step 1 -->
                            <div class="tab-content-section pt-0 active" id="step1">
                                <div class="tab-content pt-0">
                                    <form class="ajax-form" method="POST" id="createForm" name="createForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <input type="hidden" name="offer_id" value="{{ $offer_id }}">
                                        
                                            <div class="col-lg-12">
                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-details-card">
                                                    {{-- Header: title + subtitle (left) · Confidentiality Status (right) --}}
                                                    <div class="co-details-head">
                                                        <div>
                                                            <h5 class="co-section-title">Compensation Details</h5>
                                                            <div class="co-section-sub">Enter the offer details to generate the compensation structure</div>
                                                        </div>
                                                        <div class="co-conf-wrap">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/></svg></span> Confidentiality Status</label>
                                                            <select class="form-select form-select-solid" arial-label="Select Confidential" id="confidential" name="confidential">
                                                                <option value="0" {{ $confidential == 0 ? 'selected' : '' }}>No</option>
                                                                <option value="1" {{ $confidential == 1 ? 'selected' : '' }}>Yes</option>
                                                            </select>
                                                            <a href="javascript:void(0);" class="co-conf-hint">Who can view the Salary</a>
                                                        </div>
                                                    </div>

                                                    <div class="row co-field-grid">
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.5 9a2.5 2.5 0 0 0-2.5-1.5c-1.4 0-2.5.9-2.5 2s1.1 2 2.5 2 2.5.9 2.5 2-1.1 2-2.5 2A2.5 2.5 0 0 1 9.5 15M12 6v1.5M12 16.5V18"/></svg></span> Currency</label>
                                                            <select class="form-select form-select-solid" arial-label="Select Currency" id="current_ctc_cur" name="current_ctc_cur">
                                                                <option value="0" {{ $currency == 0 ? 'selected' : '' }}>INR - Indian Rupee</option>
                                                                <option value="1" {{ $currency == 1 ? 'selected' : '' }}>EUR</option>
                                                                <option value="2" {{ $currency == 2 ? 'selected' : '' }}>JPY</option>
                                                                <option value="3" {{ $currency == 3 ? 'selected' : '' }}>GBP</option>
                                                                <option value="4" {{ $currency == 4 ? 'selected' : '' }}>USD</option>
                                                                <option value="5" {{ $currency == 5 ? 'selected' : '' }}>AED</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.2"/></svg></span> Enter Amount (CTC)</label>
                                                            <input type="text" class="form-control form-control-solid" name="ctc" id="ctc" placeholder="Enter current CTC" value="{{ @$salary_ctc }}">
                                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span> Salary Breakdown Format</label>
                                                            <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                                                                <option value="2" {{ $salary_mode == 2 ? 'selected' : '' }}>Hourly</option>
                                                                <option value="1" {{ $salary_mode == 1 ? 'selected' : '' }}>Monthly</option>
                                                                <option value="3" {{ $salary_mode == 3 ? 'selected' : '' }}>Annually</option>
                                                                <option value="0" {{ empty($salary_mode) || $salary_mode == 0 ? 'selected' : '' }}>Monthly/Annually</option>
                                                                <option value="4" {{ $salary_mode == 4 ? 'selected' : '' }}>Hourly/Annually</option>
                                                            </select>
                                                            <div class="co-field-hint">Annually / Monthly</div>
                                                        </div>
                                                    </div>
                                                    <div class="row co-field-grid">
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span> Offered Designation</label>
                                                            <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{ @$designation }}">
                                                        </div>
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label co-flabel"><span class="co-ic co-ic--amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg></span> Date of Joining (DOJ)</label>
                                                            <input type="datetime-local" class="form-control form-control-solid" name="dob" id="dob" placeholder="Enter dob" value="{{ @$joined_date }}" onclick="this.showPicker()">
                                                        </div>
                                                        <div class="col-lg-4 co-field">
                                                            <label class="form-label required co-flabel"><span class="co-ic co-ic--blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20a6 6 0 0 1 12 0M16 11a3 3 0 0 0 0-6M21 20a5 5 0 0 0-3.5-4.8"/></svg></span> Employment Type</label>
                                                            <select class="form-select form-select-solid" arial-label="Select Job Type" id="employment_type" name="employment_type">
                                                                <option value="0" {{ $employment_type == 0 ? 'selected' : '' }}>Full Time</option>
                                                                <option value="1" {{ $employment_type == 1 ? 'selected' : '' }}>Contract</option>
                                                                <option value="3" {{ $employment_type == 3 ? 'selected' : '' }}>Part Time</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
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
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
                                                            role="grid" aria-describedby="myTable_info"
                                                            style="width: 100%;">
                                                            <thead>
                                                                <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                                                                    role="row">
                                                                    <th class="sorting_disabled" style="width: 25%!important">Component Name
                                                                    </th>
                                                                    <th class="sorting_disabled"
                                                                        style="width:10% !important">Applicable</th>
                                                                    <th class="sorting_disabled"
                                                                        style="width: 7% !important;">Allocation</th>
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
                                                                        style="width: 12% !important;">Allocation</th>
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
                                                                                    <option value="2" {{ $basic->view_type == 2 ? 'selected' : '' }}> Annually &amp; Monthly</option>
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
                                                                                        placeholder="0">


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
                                                                                <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> </td>
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
                                                                                        placeholder="0">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                                    <option value="2" {{ $value->view_type == 2 ? 'selected' : '' }}> Annually &amp; Monthly</option>
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
                                                                                        placeholder="0">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> </td>
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
                                                                                        placeholder="0">
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                <tr class="co-total-row">
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
                                                                    <th colspan="6" class="sorting_disabled"
                                                                        style="width: 102px;">&nbsp; </th>
                                                                </tr>
                                                                <tr class="co-total-row">
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
                                                    </div>
                                                </div>

                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                                                    <div class="co-section-head co-section-head--flex">
                                                        <div>
                                                            <h5 class="co-section-title">Additional Components <span class="co-section-note">(Above 100% - Optional)</span></h5>
                                                            <div class="co-section-sub">These components are additional and will be added on top of the main 100%.</div>
                                                        </div>
                                                        <button type="button" class="co-add-btn" onclick="$('#bonusList').find('.addRowBtnbonus').first().trigger('click')">+ Add Additional Component</button>
                                                    </div>
                                                    <div id="co_additional_total" class="co-info-line">Total additional components: 0.00% (Allowed above 100%)</div>

                                                    {{-- Empty state — shown (via JS) only when there are no additional components --}}
                                                    <div id="co_additional_empty" class="co-empty-state" style="display:none;">
                                                        <div class="co-empty-illus">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 8l9-4 9 4-9 4-9-4z"/><path d="M3 8v8l9 4 9-4V8"/><path d="M12 12v8"/>
                                                            </svg>
                                                        </div>
                                                        <div class="co-empty-title">No additional components added yet</div>
                                                        <div class="co-empty-sub">Add bonuses, reimbursements or other components if applicable.</div>
                                                        <button type="button" class="co-add-btn" onclick="$('#bonusList').find('.addRowBtnbonus').first().trigger('click')">+ Add Additional Component</button>
                                                    </div>

                                                    <div class="row">
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList"
                                                            role="grid" aria-describedby="bonusList_info"
                                                            style="width: 100%;">
                                                            <thead>
                                                                {{-- Column header row (like Main Components) --}}
                                                                <tr class="fw-bolder text-muted other_section {{ $section_other }}">
                                                                    <th class="sorting_disabled" style="width: 25%!important">Component Name</th>
                                                                    <th class="sorting_disabled" style="width:10% !important">Applicable</th>
                                                                    <th class="sorting_disabled" style="width: 7% !important;">Allocation</th>
                                                                    <th class="sorting_disabled" style="width: 3% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled" style="width: 12% !important;">Condition</th>
                                                                    <th class="sorting_disabled" style="width: 15% !important;">Monthly</th>
                                                                    <th class="sorting_disabled" style="width:15% !important">{{$first_text}}</th>
                                                                    <th class="sorting_disabled">Action</th>
                                                                </tr>
                                                                <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}">
                                                                    <th class="sorting_disabled" style="width: 47% !important;">Component Name</th>
                                                                    <th class="sorting_disabled" style="width: 12% !important;">Allocation</th>
                                                                    <th class="sorting_disabled" style="width: 3% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled" style="width: 10% !important;">Condition</th>
                                                                    <th class="sorting_disabled" style="width:15% !important">{{ $secont_text }}</th>
                                                                    <th class="sorting_disabled" style="width: 78px;">Action</th>
                                                                </tr>
                                                                {{-- Old compact "Additional Component" bar kept only to host the add-row trigger (hidden; replaced by column headers above) --}}
                                                                <tr class="other_section other_section_row row-template {{ $section_other }}" style="display:none;">
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
                                                                <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}" style="display:none;">
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
                                                                                    <option value="2" {{ @$bonus->view_type == 2 ? 'selected' : '' }}> Annually &amp; Monthly</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="" style="width: 7% !important;">
                                                                                    <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid bonus_option_percentage"
                                                                                    name="bonus_option_percentage[]"
                                                                                    id="bonus_option_percentage{{ $bonus->id }}"
                                                                                    data-id="{{ $bonus->id }}"
                                                                                    data-offerid="{{ $bonus->id }}"
                                                                                    value="{{ $bonus->bonus_percentage }}" placeholder="0">
            
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;">
                                                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                                    value="{{ $bonus->bonus_percentage }}" placeholder="0">
            
                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                                    <option value="2" {{ @$offer->view_type == 2 ? 'selected' : '' }}> Annually &amp; Monthly</option>
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
                                                                                        placeholder="0">

                                                                                </td>
                                                                                <td class="" style="position: relative; width: 3% !important;">
                                                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                                            placeholder="0">
                                                                                    </td>
                                                                                    <td class="" style="position: relative; width: 3% !important;"> <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span> 
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
                                                                <tr>
                                                                    <th colspan="8" class="sorting_disabled"
                                                                        style="width: 102px;">&nbsp; </th>
                                                                </tr>
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
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #eef3ff;"></th>
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
                                                                <tr>
                                                                    <th colspan="6" class="sorting_disabled"
                                                                        style="width: 102px;">&nbsp; </th>
                                                                </tr>
                                                                <tr class="co-total-row">
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input type="text" class="form-control form-control-solid" value="Total Additional Components" readonly style="cursor: not-allowed;background: #eef3ff;">
                                                                    </th>
                                                                    <th class="sorting_disabled">
                                                                        <input type="text" readonly
                                                                            class="form-control form-control-solid totalhourlypercentagebonus"
                                                                            name="totalhourlypercentagebonus"
                                                                            value=""
                                                                            id="totalhourlypercentagebonus" style="background:#eef3ff;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #eef3ff;"></th>
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
                                                    </div>
                                                </div>

                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-finalctc-card">
                                                    <div class="co-section-head">
                                                        <div>
                                                            <h5 class="co-section-title">Final CTC <span class="co-section-note">(Cost to Company)</span></h5>
                                                            <div class="co-section-sub">Summary of the total cost including main and additional components.</div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer"
                                                            id="final_pay" role="grid"
                                                            aria-describedby="final_pay_info" style="width: 100%;">

                                                            {{-- Column header row for the Final CTC values (like Main / Additional) --}}
                                                            <thead>
                                                                <tr class="fw-bolder text-muted other_section {{ $section_other }}">
                                                                    <th colspan="2" class="sorting_disabled" style="width: 35%;">Component</th>
                                                                    <th class="sorting_disabled" style="width: 7% !important;">Allocation</th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 15% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled" style="width: 15% !important;">Monthly</th>
                                                                    <th class="sorting_disabled" style="width: 15% !important;">{{ $first_text }}</th>
                                                                    <th class="sorting_disabled">&nbsp;</th>
                                                                </tr>
                                                                <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}">
                                                                    <th class="sorting_disabled" style="width: 47%;">Component</th>
                                                                    <th class="sorting_disabled" style="width: 12% !important;">Allocation</th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 13% !important;">&nbsp;</th>
                                                                    <th class="sorting_disabled" colspan="2">{{ $first_text }}</th>
                                                                </tr>
                                                            </thead>

                                                            <tfoot class="other_section_final other_section {{ $section_other }}">

                                                                <tr>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 35%;">
                                                                        <input type="text" class="form-control form-control-solid" value="Total CTC (Main + Additional)" readonly style="cursor: not-allowed;background: transparent;">
                                                                    </th>
                                                                    <th class="sorting_disabled" style="width: 7% !important;">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid totalpercentagefinal"
                                                                            name="totalpercentagefinal"
                                                                            value=""
                                                                            id="totalpercentagefinal" style="background: #7de96f40;">
                                                                    </th>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 15% !important;">
                                                                        <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid show_currency"
                                                                        name="show_currency"
                                                                        value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                    </th>
                                                                    <th class="sorting_disabled"  style="width: 15% !important;">
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
                                                                        <input type="text" class="form-control form-control-solid" value="Total CTC (Main + Additional)" readonly style="cursor: not-allowed;background: transparent;">
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
                                                                    <th class="sorting_disabled" colspan="2">
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

                                                {{-- Static/decorative footer (no backing calc source) --}}
                                                <div class="co-howit-box">
                                                    <div class="co-howit-col">
                                                        <div class="co-howit-title"><i class="co-howit-ico"></i> How it works?</div>
                                                        <div class="co-howit-text">Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</div>
                                                    </div>
                                                    <div class="co-howit-col">
                                                        <div class="co-howit-title"><i class="co-howit-ico co-howit-ico--amber"></i> Before you proceed</div>
                                                        <div class="co-howit-text">Double-check the currency, condition (Amount/Basic) and allocation of every component — these values drive the offer letter and cannot be edited once the offer is generated.</div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="co-footer-actions">
                                                    <button type="button" class="btn co-btn-close" onclick="$('#kt_offercreate_close').trigger('click')">Close</button>
                                                    <input type="submit" name="save"
                                                        value="Save & {{($section_change_value) ? 'Next' : 'Generate'}}" id="save-form"
                                                        class="btn co-btn-save">
                                                </div>


                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="tab-content-section pt-5" id="step2">
                                @php
                                    // Same mapping Step 1's Employment Type select uses — shown here purely as a
                                    // read-only label (this is not an editable "template type" — the backend has
                                    // exactly one offer-letter template per company, not a multi-template catalog).
                                    $co_emp_type_label = ['Full Time', 'Contract', null, 'Part Time'][$employment_type ?? 0] ?? 'Full Time';
                                @endphp

                                <div class="co-tpl-header">
                                    <div>
                                        <h5 class="co-tpl-title">Offer Letter Template</h5>
                                        <div class="co-tpl-sub">Personalize the offer letter before sending it for approval.</div>
                                    </div>
                                    {{-- Templates: was a whole sidebar card (search + one card) for what is
                                         always exactly one template (the company's default, set in Settings) —
                                         collapsed into a dropdown in the heading row so the editor gets that
                                         width back. Scales fine if a real multi-template catalog is added later. --}}
                                    <div class="co-tpl-select-wrap">
                                        <label class="co-tpl-select-label" for="co_tpl_select">Templates</label>
                                        <select id="co_tpl_select" class="co-tpl-select">
                                            <option value="company-offer-letter-template" {{ empty($is_bench_candidate) ? 'selected' : '' }}>Company Offer Letter Template</option>
                                            <option value="agency-offer-letter-template" {{ !empty($is_bench_candidate) ? 'selected' : '' }}>Agency Offer Letter Template</option>
                                        </select>
                                    </div>
                                </div>

                                @php
                                    // Company logo: always render an actual image (never a text fallback),
                                    // using the same app-wide default-logo asset the Company Profile page uses.
                                    $co_logo_src = !empty($co_company_logo) ? $co_company_logo : asset('assets/logo-not-found.png');
                                    $co_letterhead = '
                                        <div class="co-letterhead">
                                            <div class="co-letterhead-logo">
                                                <img src="'.e($co_logo_src).'" alt="'.e($co_company_name ?? 'Company').'" onerror="this.src=\''.asset('assets/logo-not-found.png').'\'">
                                            </div>
                                            <div class="co-letterhead-address">'
                                                . (!empty($co_company_name) ? '<div class="co-letterhead-company">'.e($co_company_name).'</div>' : '')
                                                . (!empty($co_company_address) ? '<div>'.e($co_company_address).'</div>' : '')
                                            . '</div>
                                        </div>';
                                @endphp

                                <div class="co-tpl-layout">
                                    {{-- MAIN CARD: Template editor — now gets the full width that used to be
                                         shared with the Templates sidebar card (moved to the heading row above). --}}
                                    <div class="co-tpl-editor-col">
                                        <div class="co-tpl-tabs">
                                            <button type="button" class="co-tpl-tab co-tpl-tab--active" data-co-tab="editor">Template Editor</button>
                                            <button type="button" class="co-tpl-tab" data-co-tab="preview">Preview</button>
                                        </div>

                                        {{-- Sub card: template type/status --}}
                                        <div class="co-tpl-subcard co-tpl-meta-row">
                                            <span class="co-tpl-meta-item">Type<b>{{ $co_emp_type_label }}</b></span>
                                            <span class="co-tpl-meta-item">Status<b style="color:#0f9463">Active</b></span>
                                        </div>

                                        <div class="co-tpl-tabpane" data-co-pane="editor">
                                            <form class="ajax-form" method="POST" id="termConditioncreateForm"
                                                name="termConditioncreateForm" enctype="multipart/form-data">
                                                @csrf
                                                {{-- Sub card: letterhead (logo + address) --}}
                                                <div class="co-tpl-subcard">{!! $co_letterhead !!}</div>
                                                <div class="col-lg-12 col-xl-12">
                                                    <textarea id="editorterms1" name="editorterms1">
                                                        {{ $offer_terms }}
                                                    </textarea>
                                                </div>

                                                {{-- Sub card: zoom + download tools --}}
                                                <div class="co-tpl-subcard co-tpl-toolbar-extra" style="margin-top:10px;margin-bottom:0;display:flex;">
                                                    <div class="co-tpl-zoom">
                                                        <button type="button" class="co-tpl-zoom-btn" id="co_tpl_zoom_out" aria-label="Zoom out">&minus;</button>
                                                        <span class="co-tpl-zoom-pct" id="co_tpl_zoom_pct">100%</span>
                                                        <button type="button" class="co-tpl-zoom-btn" id="co_tpl_zoom_in" aria-label="Zoom in">+</button>
                                                    </div>
                                                    <button type="button" class="co-tpl-download-btn" id="co_tpl_download_sample">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 19h16"/></svg>
                                                        Download Sample
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="co-tpl-tabpane d-none" data-co-pane="preview">
                                            {{-- Sub card: letterhead (logo + address), identical to the editor tab --}}
                                            <div class="co-tpl-subcard">{!! $co_letterhead !!}</div>
                                            <div id="co_tpl_preview_content" class="co-tpl-preview"></div>
                                        </div>
                                    </div>

                                    {{-- MAIN CARD 3: Insert Variables + template info --}}
                                    <div class="co-tpl-side">
                                        {{-- Sub card: variable chips --}}
                                        <div class="co-tpl-subcard">
                                            <div class="co-tpl-side-title">Insert Variables</div>
                                            <div class="co-tpl-side-sub">Click to insert this candidate's actual details at the cursor.</div>
                                            <div class="co-var-chips">
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_candidate_name ?? '' }}" {{ empty($co_candidate_name) ? 'disabled' : '' }}>Candidate Name</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_candidate_email ?? '' }}" {{ empty($co_candidate_email) ? 'disabled' : '' }}>Candidate Email</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_candidate_mobile ?? '' }}" {{ empty($co_candidate_mobile) ? 'disabled' : '' }}>Candidate Mobile</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_company_name ?? '' }}" {{ empty($co_company_name) ? 'disabled' : '' }}>Company Name</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_job_title ?? '' }}" {{ empty($co_job_title) ? 'disabled' : '' }}>Job Title</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_joining_date ?? '' }}" {{ empty($co_joining_date) ? 'disabled' : '' }}>Joining Date</button>
                                                <button type="button" class="co-var-chip" data-co-var="{{ $co_today_date ?? '' }}" {{ empty($co_today_date) ? 'disabled' : '' }}>Today's Date</button>
                                            </div>
                                        </div>
                                        {{-- Sub card: about this template --}}
                                        <div class="co-tpl-subcard" style="margin-bottom:0;">
                                            <div class="co-tpl-side-title">About this template</div>
                                            <div class="co-tpl-note" style="margin-top:6px;">This is your company's default Offer Letter Template, configured in <b>Settings &rarr; Company Profile &rarr; Terms &amp; Conditions</b>. Changes made here apply only to this candidate's offer.</div>
                                        </div>
                                        {{-- Reset sits directly below the sub card above it, not floated to a corner --}}
                                        <button type="button" class="co-btn-reset setToDefaultCompanyTerms" id="setToDefaultCompanyTerms" style="margin-top:10px;">Reset Template</button>
                                    </div>
                                </div>

                                {{-- Shared footer for both tabs: one Back / Save & Next bar, bottom-right --}}
                                <div class="co-footer-actions co-tpl-footer">
                                    <span class="btn co-btn-close" onclick="prevStep(event,2)">Back</span>
                                    <button type="button" class="btn co-btn-save" onclick="$('#termConditioncreateForm').trigger('submit')">Save & Next</button>
                                </div>

                            </div>

                            <!-- Step 3 -->
                            <div class="tab-content-section pt-5" id="step3">

                                <div id="preview_offer_body_content"></div>
                                <div class="co-footer-actions co-tpl-footer co-step3-footer">
                                    {{-- Direct download: navigates to a route that streams the PDF as an
                                         attachment (no AJAX/JSON/storage-URL — those all failed silently on
                                         the live host). target=_blank so the current drawer stays put. --}}
                                    <a href="{{ route('service_company.cvtracker.offer_letter_stream', $offer_id, false) }}"
                                       target="_blank" rel="noopener"
                                       class="btn co-btn-close co-step3-download">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;vertical-align:-2px;margin-right:5px;"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 19h16"/></svg>Download Offer Letter
                                    </a>
                                    <div class="co-step3-footer-right">
                                        <span class="btn co-btn-close" onclick="prevStep(event,3)">Back</span>
                                        <button type="button" class="btn co-btn-save section_change_value {{ $section_change_value ? '' : 'd-none' }}" onclick="nextStep(event,3)">Continue to Approval</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 : Approval -->
                            <div class="tab-content-section pt-5" id="step4">
                                <div class="co-tpl-header">
                                    <div>
                                        <h5 class="co-tpl-title">Approval</h5>
                                        <div class="co-tpl-sub">Send this offer for internal approval before it is released to the candidate.</div>
                                    </div>
                                </div>

                                <div class="co-appr-info-row">
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">Candidate</div>
                                            <div class="co-appr-info-value">{{ @$application->full_name }}</div>
                                            <div class="co-appr-info-sub">{{ @$application->email }}</div>
                                        </div>
                                    </div>
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">Role</div>
                                            <div class="co-appr-info-value">{{ @$designation }}</div>
                                        </div>
                                    </div>
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">CTC (Annual)</div>
                                            <div class="co-appr-info-value">{!! @$current_ctc_cur !!} {{ number_format((float) @$yearly_final_gross, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">Joining Date</div>
                                            <div class="co-appr-info-value">{{ @$joined_date ? date('d M Y', strtotime($joined_date)) : '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20a6 6 0 0 1 12 0M16 11a3 3 0 0 0 0-6M21 20a5 5 0 0 0-3.5-4.8"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">Requested By</div>
                                            <div class="co-appr-info-value">{{ auth()->user()->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="co-appr-info-card">
                                        <span class="co-appr-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span>
                                        <div>
                                            <div class="co-appr-info-label">Employment Type</div>
                                            <div class="co-appr-info-value">{{ $co_emp_type_label }}</div>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    // Real, persisted approval status — offer_create_main_tds.management_status:
                                    // 0/absent = never sent, 1 = pending, 2 = approved, 3 = rejected.
                                    // (Set by fwd_offer_send() when the request is emailed, and by the public
                                    // saveSignature() endpoint when the approver clicks Approve/Reject.)
                                    $co_mgmt_status = (int) (@$section_change_value->management_status ?? 0);
                                    $_co_tl_latest = ($co_appr_timeline instanceof \Illuminate\Support\Collection) ? $co_appr_timeline->first() : $co_appr_timeline;
                                    $co_approver_label = trim((string) (@$_co_tl_latest->name_hr ?: @$_co_tl_latest->email_id_hr ?? ''));
                                @endphp

                                <div class="co-appr-layout">
                                    <div class="co-appr-main">
                                        {{-- ── Approval Timeline header ── --}}
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                                            <div>
                                                <h5 class="co-tpl-title">Approval Timeline</h5>
                                                <div style="font-size:10px;color:#7e8299;margin-top:3px;">
                                                    @php $co_appr_all_tl = ($co_appr_timeline instanceof \Illuminate\Support\Collection) ? $co_appr_timeline : collect(); @endphp
                                                    @if($co_mgmt_status == 2) Offer approved — ready to send to candidate.
                                                    @elseif($co_mgmt_status == 3) Offer rejected.
                                                    @elseif($co_appr_all_tl->isNotEmpty()) Awaiting decision from approver.
                                                    @else No approver assigned yet.
                                                    @endif
                                                </div>
                                            </div>
                                            <button type="button"
                                                data-jobid="{{ @$application->job_id }}"
                                                data-candidate="{{ @$application->id }}"
                                                data-mtype="2"
                                                data-offerId="{{ $offer_id }}"
                                                class="co-appr-add-dashed fwd_offer {{ $section_change_value ? '' : 'd-none' }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                                Add Approver
                                            </button>
                                        </div>

                                        @if($co_appr_all_tl->isEmpty())
                                            {{-- Empty state --}}
                                            <div class="co-appr-empty-state">
                                                <div class="co-appr-empty-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M19 8v6M22 11h-6"/></svg></div>
                                                <div class="co-appr-empty-title">No approver assigned yet</div>
                                                <div class="co-appr-empty-sub">Choose an approver from your internal team, or the client's team if this job has one.</div>
                                            </div>
                                        @else
                                            {{-- Column headers — same flex shell as rows, 52px spacer + 4-col grid --}}
                                            <div class="co-appr-tl-cols">
                                                <div class="co-appr-tl-cols-spacer"></div>
                                                <div class="co-appr-tl-grid" style="flex:1;padding:0 14px;">
                                                    <div class="co-appr-tl-col-hd">Approver</div>
                                                    <div class="co-appr-tl-col-hd">Role</div>
                                                    <div class="co-appr-tl-col-hd">Status (Date &amp; Time)</div>
                                                    <div class="co-appr-tl-col-hd">Email Reminder</div>
                                                </div>
                                            </div>

                                            {{-- Timeline rows (ASC: oldest first, last row = current status) --}}
                                            @php $co_appr_asc = $co_appr_all_tl->reverse()->values(); @endphp
                                            @foreach($co_appr_asc as $co_tl_row)
                                            @php
                                                $row_label   = trim((string)(@$co_tl_row->name_hr ?: @$co_tl_row->email_id_hr ?? ''));
                                                $row_email   = trim((string)(@$co_tl_row->email_id_hr ?? ''));
                                                $row_initial = $row_label ? strtoupper(substr($row_label,0,1)) : '?';
                                                $row_role    = trim((string)(@$co_tl_row->designation_offer ?: @$co_tl_row->approver_designation ?? ''));
                                                $row_date    = !empty($co_tl_row->create_at) ? date('d M Y, h:i A', strtotime($co_tl_row->create_at)) : '';
                                                // Last row carries actual management_status; earlier rows = already-sent (waiting)
                                                if ($loop->last) {
                                                    $row_dot   = $co_mgmt_status == 2 ? 'approved' : ($co_mgmt_status == 3 ? 'rejected' : 'active');
                                                    $row_badge = $row_dot;
                                                    $row_card  = $row_dot;
                                                    $row_label_status = $co_mgmt_status == 2 ? 'Approved' : ($co_mgmt_status == 3 ? 'Rejected' : 'Pending');
                                                } else {
                                                    $row_dot = $row_badge = 'waiting';
                                                    $row_card = '';
                                                    $row_label_status = 'Pending';
                                                }
                                            @endphp
                                            {{-- Row: 52px dot column + flex-1 card --}}
                                            <div style="display:flex;align-items:stretch;margin-bottom:8px;" class="co-appr-tl-entry">
                                                <div style="width:52px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;">
                                                    <div class="co-appr-tl-dot co-appr-tl-dot--{{ $row_dot }}">
                                                        @if($row_dot==='approved')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                        @elseif($row_dot==='rejected')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                        @elseif($row_dot==='active')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                                        @else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                                        @endif
                                                    </div>
                                                    @if(!$loop->last)<div class="co-appr-tl-connector"></div>@endif
                                                </div>
                                                {{-- Card uses same 4-col grid as header --}}
                                                <div class="co-appr-tl-card co-appr-tl-grid {{ $row_card ? 'co-appr-tl-card--'.$row_card : '' }}" style="flex:1;">
                                                    <div class="co-appr-tl-appr">
                                                        <div class="co-appr-tl-avatar">{{ $row_initial }}</div>
                                                        <div>
                                                            <div class="co-appr-tl-name">{{ $row_label ?: $row_email ?: '—' }}</div>
                                                            @if($row_role)<div class="co-appr-tl-sub">{{ $row_role }}</div>@endif
                                                        </div>
                                                    </div>
                                                    <div class="co-appr-tl-role">{{ $row_role ?: '—' }}</div>
                                                    <div class="co-appr-tl-status">
                                                        <span class="co-appr-tl-badge co-appr-tl-badge--{{ $row_badge }}">{{ $row_label_status }}</span>
                                                        <div class="co-appr-tl-date">{{ $row_date ?: '—' }}</div>
                                                    </div>
                                                    <div class="co-appr-tl-rem">
                                                        <div class="co-appr-tl-rem-row">
                                                            <button type="button" class="co-appr-tl-rem-btn co-appr-remind-send" data-email="{{ $row_email }}" data-name="{{ $row_label }}" data-offerid="{{ $offer_id }}">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                                Send Reminder
                                                            </button>
                                                            <span class="co-appr-tl-kebab" title="More options"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg></span>
                                                        </div>
                                                        <div class="co-appr-tl-last-sent">Last sent: {{ $row_date ?: '—' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            @if($co_mgmt_status == 3 && !empty(@$_co_tl_latest->note))
                                                <div class="co-appr-status-note" style="margin-top:8px;">&ldquo;{{ $_co_tl_latest->note }}&rdquo;</div>
                                            @endif
                                        @endif

                                        <div class="co-appr-note" style="margin-top:14px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                                            <span>Approvals will be requested in the order shown above. You can reorder or remove approvers before sending the offer.</span>
                                        </div>
                                    </div>

                                    <div class="co-appr-side">
                                        <div class="co-appr-side-card">
                                            <div class="co-appr-side-title">Details &amp; Insights</div>
                                            <div class="co-appr-detail-row"><span class="co-appr-detail-label">Offer ID</span><span class="co-appr-detail-value">{{ $offer_id }}</span></div>
                                            <div class="co-appr-detail-row"><span class="co-appr-detail-label">Template</span><span class="co-appr-detail-value">Company Offer Letter Template</span></div>
                                            <div class="co-appr-detail-row"><span class="co-appr-detail-label">Employment Type</span><span class="co-appr-detail-value">{{ $co_emp_type_label }}</span></div>
                                            <div class="co-appr-detail-row"><span class="co-appr-detail-label">Designation</span><span class="co-appr-detail-value">{{ @$designation ?: '—' }}</span></div>
                                            @if(!empty(@$application->phone))
                                            <div class="co-appr-detail-row"><span class="co-appr-detail-label">Candidate Mobile</span><span class="co-appr-detail-value">{{ $application->phone }}</span></div>
                                            @endif
                                        </div>

                                        <div class="co-appr-side-card">
                                            <div class="co-appr-side-title">Activity Log</div>
                                            @forelse(($co_activity_log ?? []) as $log)
                                                @php
                                                    $log_label = 'Activity recorded';
                                                    $log_who = trim((string) ($log->name_hr ?: $log->email_id_hr ?? ''));
                                                    if ((int) $log->mtypeSection === 2) {
                                                        if ((int) $log->management_status === 1) { $log_label = 'Approval requested' . ($log_who ? ' from ' . $log_who : ''); }
                                                        elseif ((int) $log->management_status === 2) { $log_label = 'Approved' . ($log_who ? ' by ' . $log_who : ''); }
                                                        elseif ((int) $log->management_status === 3) { $log_label = 'Rejected' . ($log_who ? ' by ' . $log_who : ''); }
                                                    } elseif ((int) $log->mtypeSection === 1) {
                                                        $log_label = ((int) $log->candidate_status === 1) ? 'Offer sent to candidate' : 'Candidate offer activity';
                                                    }
                                                @endphp
                                                <div class="co-appr-activity-item">
                                                    <div class="co-appr-activity-text">{{ $log_label }}</div>
                                                    <div class="co-appr-activity-time">{{ !empty($log->create_at) ? date('d M Y, h:i A', strtotime($log->create_at)) : '' }}</div>
                                                </div>
                                            @empty
                                                <div class="co-appr-empty-sub" style="margin-bottom:0;">No activity yet. Activities will appear here once the offer moves forward.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="co-footer-actions co-tpl-footer section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                    <span class="btn co-btn-close" onclick="prevStep(event,4)">Back</span>
                                    <button type="button" class="btn co-btn-save" onclick="nextStep(event,4)">Save &amp; Next</button>
                                </div>
                            </div>

                            <!-- Step 5 : Send Offer Letter (candidate email composer, inline) -->
                            <div class="tab-content-section pt-5" id="step5">
                                <div class="co-tpl-header">
                                    <div>
                                        <h5 class="co-tpl-title">Send Offer Letter <span class="co-step5-final">Final Step</span></h5>
                                        <div class="co-tpl-sub">Review the details and send the final offer email to your candidate.</div>
                                    </div>
                                </div>

                                @php
                                    // Native composer — all values are real (from createOffer's scope); no AJAX loader. condidate_type
                                    $so_name    = trim((string) @$co_candidate_name) ?: (@$application->full_name ?: 'Candidate');
                                    $so_email   = @$co_candidate_email ?: @$application->email;
                                    if(@$application->condidate_type=='bench_condidate'){
                                         $bench_assign = DB::table('users')->join('bench_assign','bench_assign.user_id','=','users.id')->select('users.email')->where('bench_assign.bench_applications_id',@$application->id)->first();
                                        $so_email   = @$bench_assign->email ?? @$application->email;
                                    }
                                    $so_role    = @$designation ?: @$co_job_title;
                                    $so_exp     = @$application->total_exp;
                                    $so_location = trim((string) (@$application->current_location ?: @$application->city));
                                    $so_photo   = @$application->photo;
                                    $so_company = @$co_company_name ?: '';
                                    $so_initial = strtoupper(substr(trim($so_name), 0, 1) ?: 'C');
                                    $so_from_name  = auth()->user()->name ?? '';
                                    $so_from_email = auth()->user()->email ?? '';
                                    $so_currencyArr = ['INR','EUR','JPY','GBP','USD','AED'];
                                    $so_curr    = $so_currencyArr[(int) @$section_change_value->currency] ?? '';
                                    $so_ctc     = @$yearly_final_gross;
                                    $so_join    = @$joined_date ?: @$co_joining_date;
                                    $so_join_fmt = ($so_join && strtotime($so_join)) ? date('d M Y', strtotime($so_join)) : '';
                                    $so_mgmt    = (int) (@$section_change_value->management_status ?? 0);
                                    $_so_tl_latest = ($co_appr_timeline instanceof \Illuminate\Support\Collection) ? $co_appr_timeline->first() : $co_appr_timeline;
                                    $so_appr_name = trim((string) (@$_so_tl_latest->name_hr ?: @$_so_tl_latest->email_id_hr ?? ''));
                                    $so_appr_date = @$section_change_value->approved_date_time;
                                    // Not a stored field (no expiry column exists on offer_create_main_tds) — a standard
                                    // 15-day acceptance window computed off the offer's creation date, display-only.
                                    // offer_create_main_tds.created_at is not populated by the offer-save insert
                                    // (existing gap, out of scope here) so it's frequently NULL — fall back to
                                    // "today" (i.e. the send date), which is the standard offer-letter convention anyway.
                                    $so_valid_days = 15;
                                    $so_created = @$section_change_value->created_at ?: now();
                                    $so_valid_until = date('d M Y', strtotime($so_created.' +'.$so_valid_days.' days'));
                                    // Offer Status / Sent By — send_accept_date_time is set by fwd_offer_send() the
                                    // moment the candidate email actually goes out (mtypeId==1); co_send_timeline
                                    // carries who sent it (event_by_user_id joined to users), blank if never sent.
                                    $so_sent_date = @$section_change_value->send_accept_date_time;
                                    $so_sent = !empty($so_sent_date);
                                    $so_sent_by_name = trim((string) (@$co_send_timeline->sent_by_name ?? ''));
                                    $so_sent_by_when = ($so_sent && $so_sent_by_name) ? (date('j F y', strtotime($so_sent_date)).' | '.date('h:iA', strtotime($so_sent_date)).' (IST)') : '';
                                    $so_ctc_line = trim($so_curr.' '.($so_ctc ? number_format((float) $so_ctc) : ''));
                                    // Subject/body start blank by design — they should only ever be filled by
                                    // the sender typing them or by picking a Template (which fills both via
                                    // the email_template AJAX call), never a canned pre-fill nobody asked for.
                                    $so_subject = '';
                                    $so_default_body = '';
                                @endphp

                                {{-- MAIN CARD --}}
                                <div class="co-send co-send-card section_change_value {{ $section_change_value ? '' : 'd-none' }}">
                                    {{-- SUB CARD 1: candidate hero --}}
                                    <div class="co-send-hero">
                                        <div class="co-send-hero-top">
                                            <div class="co-send-hero-lead">
                                                <div class="co-send-hero-avatar">
                                                    @if(!empty($so_photo))<img src="{{ $so_photo }}" alt="{{ $so_name }}" onerror="this.parentNode.innerHTML='{{ $so_initial }}';">@else{{ $so_initial }}@endif
                                                </div>
                                                <div class="co-send-hero-ident">
                                                    <div class="co-send-hero-name">{{ ucwords($so_name) }}</div>
                                                    @if(!empty($so_role))<div class="co-send-hero-role">{{ ucwords($so_role) }}</div>@endif
                                                    <div class="co-send-hero-meta">
                                                        @if(!empty($so_location))<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ ucwords($so_location) }}</span>@endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($so_mgmt === 2 || $so_mgmt === 1)
                                            <div class="co-send-hero-status co-send-hero-status--{{ $so_mgmt === 2 ? 'approved' : 'pending' }}">
                                                <span class="co-send-status-title">
                                                    @if($so_mgmt === 2)<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg> Approved
                                                    @else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Pending
                                                    @endif
                                                </span>
                                                @if($so_mgmt === 2 && !empty($so_appr_name))<div class="co-send-status-sub">by {{ ucwords($so_appr_name) }}</div>@endif
                                                @if($so_mgmt === 2 && !empty($so_appr_date))<div class="co-send-status-sub">on {{ date('d M Y, h:i A', strtotime($so_appr_date)) }}</div>@endif
                                            </div>
                                            @endif
                                        </div>
                                        <div class="co-send-hero-facts">
                                            <div class="co-send-fact"><div class="co-send-fact-l">Offer ID</div><div class="co-send-fact-v">OFF-{{ $offer_id }}</div></div>
                                            <div class="co-send-fact"><div class="co-send-fact-l">Offered CTC</div><div class="co-send-fact-v co-send-fact-ctc">{{ $so_ctc_line }}</div></div>
                                            <div class="co-send-fact"><div class="co-send-fact-l">Joining Date</div><div class="co-send-fact-v">{{ $so_join_fmt }}</div></div>
                                            <div class="co-send-fact"><div class="co-send-fact-l">Offer Status</div><div class="co-send-fact-v {{ $so_sent ? 'co-send-fact-sent' : 'co-send-fact-notsent' }}">{{ $so_sent ? 'Sent' : 'Not Sent' }}</div></div>
                                            <div class="co-send-fact"><div class="co-send-fact-l">Offer Valid Until</div><div class="co-send-fact-v co-send-fact-expiry">{{ $so_valid_until }}</div></div>
                                            <div class="co-send-fact"><div class="co-send-fact-l">Sent By</div><div class="co-send-fact-v co-send-fact-sentby">@if($so_sent && $so_sent_by_name)<span class="co-send-sentby-name">{{ ucwords($so_sent_by_name) }}</span><span class="co-send-sentby-when">{{ $so_sent_by_when }}</span>@endif</div></div>
                                        </div>
                                    </div>

                                    {{-- SUB CARD 2: composer form (native; posts to fwd_offer_send) --}}
                                    <div class="co-send-form">
                                        <input type="hidden" id="co_send_offerid" value="{{ $offer_id }}">
                                        <input type="hidden" id="co_send_appid" value="{{ @$application->id }}">
                                        <input type="hidden" id="co_send_fromemail" value="{{ $so_from_email }}">
                                        <input type="hidden" id="co_send_candidateemail" value="{{ $so_email }}">

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> From Email ID</label>
                                            <div class="co-send-field">
                                                <input type="text" class="co-send-input" value="{{ $so_from_name ? $so_from_name.' <'.$so_from_email.'>' : $so_from_email }}" readonly>
                                                <div class="co-send-hint">This will be the email sender address</div>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20a7 7 0 0 1 14 0"/></svg> To Email ID <span class="co-send-req">*</span></label>
                                            <div class="co-send-field">
                                                <div class="co-send-chips co-send-chips--locked" id="co_to_chips">
                                                    @if(!empty($so_email))<span class="co-send-chip co-send-chip--locked">{{ $so_email }}<svg class="co-chip-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>@endif
                                                </div>
                                                <div class="co-send-hint">Primary candidate email address — fixed, can't be changed or removed</div>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="14" height="14" rx="2"/><path d="M3 15V5a2 2 0 0 1 2-2h10"/></svg> CC Email ID</label>
                                            <div class="co-send-field">
                                                <div class="co-send-chips" id="co_cc_chips">
                                                    <input type="text" class="co-send-chip-input" placeholder="Type an email…">
                                                    <span class="co-send-addemail" data-chip-target="co_cc_chips">+ Add Email</span>
                                                </div>
                                                <div class="co-send-hint">Add email addresses separated by commas</div>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3-1 6-8 8-8s2 3 4 3 3-2 6-2"/><path d="m15 5 4 4"/></svg> E-Signature</label>
                                            <div class="co-send-field">
                                                <div class="co-send-sigrow">
                                                    <label class="co-send-switch"><input type="checkbox" id="co_send_signature" checked><span class="co-send-slider"></span></label>
                                                    <span class="co-send-sigtext" id="co_send_sigtext">With E-Signature</span>
                                                </div>
                                                <div class="co-send-hint">On: candidate uploads a signature to accept, which appears on the offer letter copy. Off: candidate can only accept or reject, no signature required.</div>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg> Template</label>
                                            <div class="co-send-field">
                                                <select class="co-send-input" id="co_send_template">
                                                    <option value="">Select Email Template…</option>
                                                    @foreach($co_send_templates ?? [] as $co_tpl)
                                                        <option value="{{ $co_tpl->id }}" {{ (!empty($is_bench_candidate) && stripos($co_tpl->display_name, 'Relationship Manager') !== false) ? 'selected' : '' }}>{{ $co_tpl->display_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="co-send-hint">Choosing a template replaces the Subject and Body below</div>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 8.4 12 3 3.4 8.4M4 10v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="m4 10 8 5 8-5"/></svg> Subject <span class="co-send-req">*</span></label>
                                            <div class="co-send-field">
                                                <input type="text" class="co-send-input" id="co_send_subject" value="{{ $so_subject }}" placeholder="Enter email subject">
                                            </div>
                                        </div>

                                        <div class="co-send-row co-send-row--body">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg> Email Body</label>
                                            <div class="co-send-field">
                                                <textarea id="co_send_editor" name="co_send_editor">{!! $so_default_body !!}</textarea>
                                            </div>
                                        </div>

                                        <div class="co-send-row">
                                            <label class="co-send-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.4 11.5-8.9 8.9a5 5 0 0 1-7.1-7.1l8.9-8.9a3.5 3.5 0 0 1 5 5l-8.9 8.9a2 2 0 0 1-2.8-2.8l8.2-8.2"/></svg> Attachments <span class="co-send-attach-count" id="co_send_attach_count"></span></label>
                                            <div class="co-send-field">
                                                <div class="co-send-attachrow" id="co_send_attach_list"></div>
                                                <input type="file" id="co_send_attach_input" multiple style="display:none;">
                                                <button type="button" class="co-send-addattach" id="co_send_addattach">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg> Add Attachment
                                                </button>
                                                <div class="co-send-hint">Max 2 MB each, up to 3 files</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="co-send-footer">
                                        <div class="co-send-footer-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> Email will be sent automatically</div>
                                        <div class="co-send-footer-actions">
                                            <span class="btn co-btn-close" onclick="prevStep(event,5)">Back</span>
                                            <button type="button" class="btn co-send-submit" id="co_send_submit"><span class="co-send-submit-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;vertical-align:-2px;margin-right:6px;"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>Send Offer Letter</span><span class="co-send-submit-wait" style="display:none;">Sending…</span></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add-Email drawer: pick a teammate/client contact. Anchored to the create-offer
                                     root (#offer_create_show_right_bar is position:relative) to avoid transform/overflow issues. --}}
                                <div class="co-send-drawer-bd" id="co_send_drawer_bd"></div>
                                <div class="co-send-drawer" id="co_send_drawer" data-target="co_cc_chips">
                                    <div class="co-send-drawer-head">
                                        <div>
                                            <div class="co-send-drawer-title">Select Email</div>
                                            <div class="co-send-drawer-sub">Choose from your internal team or client company contacts.</div>
                                        </div>
                                        <button type="button" class="co-send-drawer-x" id="co_send_drawer_close" aria-label="Close">&times;</button>
                                    </div>
                                    <div class="co-send-drawer-body co-appr-picker">
                                        {{-- Choose source: internal always shown; client only when the job has an external client --}}
                                        <div class="co-appr-source-row">
                                            <label class="co-appr-source-card co-appr-source-card--active" data-source="internal">
                                                <input type="radio" name="coSendSource" value="internal" checked>
                                                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg></span>
                                                <div>
                                                    <div class="co-appr-source-title">Internal Company Approvers</div>
                                                    <div class="co-appr-source-sub">{{ count($co_send_internal ?? []) }} available</div>
                                                </div>
                                            </label>
                                            @if(!empty($co_send_client))
                                            <label class="co-appr-source-card" data-source="client">
                                                <input type="radio" name="coSendSource" value="client">
                                                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                                                <div>
                                                    <div class="co-appr-source-title">Client Company Approvers</div>
                                                    <div class="co-appr-source-sub">{{ count($co_send_client) }} available</div>
                                                </div>
                                            </label>
                                            @endif
                                        </div>
                                        <div class="co-send-searchrow">
                                            <div class="co-send-searchbox">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                                                <input type="text" class="co-send-drawer-search-input" id="co_send_drawer_search" placeholder="Search team members...">
                                            </div>
                                            <button type="button" class="co-send-addmore" id="co_send_addmore" disabled>+ Add More</button>
                                        </div>
                                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="co_send_member_table">
                                            <thead>
                                                <tr class="fw-bolder text-muted">
                                                    <th class="min-w-200px">Approver</th>
                                                    <th class="min-w-200px">Contact</th>
                                                    <th class="min-w-150px">Company</th>
                                                    <th class="min-w-90px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-bold">
                                                @foreach ([['data' => $co_send_internal ?? [], 'source' => 'internal'], ['data' => $co_send_client ?? [], 'source' => 'client']] as $group)
                                                    @foreach ($group['data'] as $teamVal)
                                                        @php
                                                            $bgTextDecore = $strVal = '';
                                                            $str = trim((string) @$teamVal->name);
                                                            $str = $strVal = strtolower($str[0] ?? 'c');
                                                            $str = ord($str);
                                                            if ($str >= 97 && $str <= 101) { $bgTextDecore = 'bg-light-danger text-danger'; }
                                                            else if ($str >= 102 && $str <= 106) { $bgTextDecore = 'bg-light-warning text-warning'; }
                                                            else if ($str >= 107 && $str <= 111) { $bgTextDecore = 'bg-light-primary text-primary'; }
                                                            else if ($str >= 112 && $str <= 116) { $bgTextDecore = 'bg-light-success text-success'; }
                                                            else if ($str >= 117 && $str <= 121) { $bgTextDecore = 'bg-dark text-secondary'; }
                                                            else { $bgTextDecore = 'bg-light-secondary text-dark'; }
                                                        @endphp
                                                        <tr class="co-send-mrow co-send-mrow--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                                                            data-email="{{ @$teamVal->email }}"
                                                            data-search="{{ strtolower(trim((string) @$teamVal->name).' '.@$teamVal->email.' '.@$teamVal->company_name) }}">
                                                            <td><div class="d-flex align-items-center">
                                                                <div class="co-send-mrow-av {{ $bgTextDecore }}">{{ strtoupper($strVal) }}</div>
                                                                <div class="d-flex flex-column"><span class="text-dark fw-bolder">{{ ucwords(substr((string) @$teamVal->name, 0, 22)) }}</span><span class="text-muted fw-bold d-block co-send-mrow-role">{{ ucwords(substr((string) @$teamVal->display_name, 0, 25)) }}</span></div>
                                                            </div></td>
                                                            <td>{{ @$teamVal->email }}</td>
                                                            <td>{{ @$teamVal->company_name }}</td>
                                                            <td><a href="javascript:void(0);" class="co-send-mrow-add btn btn-sm btn-light-primary">Add</a></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                                <tr class="co-send-mrow--internal co-send-mrow-empty {{ !empty($co_send_internal) ? 'd-none' : '' }}"><td colspan="4" class="co-appr-empty">No internal team members found.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                       
                   
                </div>
            </div>






            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Initially show the first step
                    document.getElementById("step1").classList.add("active");
                });

                // Function to move to the next step
                function nextStep(event, currentStep) {
                    event.preventDefault(); // Prevent form submission
                    if (currentStep == 2) {
                        var id = "{{ $offer_id }}";
                        var url = "{{ route('service_company.cvtracker.previewOfferLettetr', ':id') }}";
                        url = url.replace(':id', id);
                        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                        var token = "{{ csrf_token() }}";
                        var offerId = "{{ $offer_id }}";
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'offerId': offerId
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    // $('#titleSet').text(response.title)
                                    $('#preview_offer_body_content').html(response.view);
                                    document.getElementById("step" + currentStep).classList.remove("active");
                                    document.getElementById("step" + (currentStep + 1)).classList.add("active");
                                    if (typeof syncStepHeader === 'function') syncStepHeader();
                                } else if (response.status == "error") {
                                    Swal.fire("Warning", "Offer letter not ready", "warning");
                                }
                            }
                        });

                    } else {
                        document.getElementById("step" + currentStep).classList.remove("active");
                        document.getElementById("step" + (currentStep + 1)).classList.add("active");
                        if (typeof syncStepHeader === 'function') syncStepHeader();
                    }

                }

                // Called by offerSendMail after each approval email is sent — adds a new row to the timeline
                function co_updateStep4Timeline(email, name) {
                    var displayName = (name && name !== email) ? name : email;
                    var initial     = displayName ? displayName.charAt(0).toUpperCase() : '?';
                    var safeEmail   = $('<span/>').text(email).html();
                    var safeName    = $('<span/>').text(displayName).html();
                    var offId       = '{{ $offer_id }}';

                    var mailSvg  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
                    var kebabSvg = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>';
                    var activeSvg= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>';
                    var connSvg  = '<div class="co-appr-tl-connector"></div>';
                    var connectorHtml = '<div class="co-appr-tl-connector"></div>';

                    var newEntry =
                        '<div style="display:flex;align-items:stretch;margin-bottom:8px;" class="co-appr-tl-entry">' +
                            '<div style="width:52px;display:flex;flex-direction:column;align-items:center;flex-shrink:0;">' +
                                '<div class="co-appr-tl-dot co-appr-tl-dot--active">' + activeSvg + '</div>' +
                            '</div>' +
                            '<div class="co-appr-tl-card co-appr-tl-card--active" style="flex:1;display:grid;grid-template-columns:1fr 130px 160px 190px;align-items:center;gap:0;padding:12px 14px;">' +
                                '<div class="co-appr-tl-appr">' +
                                    '<div class="co-appr-tl-avatar">' + initial + '</div>' +
                                    '<div><div class="co-appr-tl-name">' + safeName + '</div></div>' +
                                '</div>' +
                                '<div class="co-appr-tl-role">—</div>' +
                                '<div class="co-appr-tl-status">' +
                                    '<span class="co-appr-tl-badge co-appr-tl-badge--active">Pending</span>' +
                                    '<div class="co-appr-tl-date">Sent just now</div>' +
                                '</div>' +
                                '<div class="co-appr-tl-rem">' +
                                    '<div class="co-appr-tl-rem-row">' +
                                        '<button type="button" class="co-appr-tl-rem-btn co-appr-remind-send" data-email="' + safeEmail + '" data-name="' + safeName + '" data-offerid="' + offId + '">' + mailSvg + ' Send Reminder</button>' +
                                        '<span class="co-appr-tl-kebab">' + kebabSvg + '</span>' +
                                    '</div>' +
                                    '<div class="co-appr-tl-last-sent">Last sent: just now</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';

                    var $main = $('#step4 .co-appr-main');
                    var $entries = $main.find('.co-appr-tl-entry');

                    if ($entries.length) {
                        // Add connector to previous last entry dot column before appending
                        $entries.last().find('.co-appr-tl-dot').parent().append(connectorHtml);
                        $entries.last().after(newEntry);
                    } else {
                        // Build full timeline layout for first add
                        var colHdr =
                            '<div class="co-appr-tl-cols" style="padding-left:52px;margin-bottom:4px;">' +
                                '<div class="co-appr-tl-col-hd">Approver</div>' +
                                '<div class="co-appr-tl-col-hd">Role</div>' +
                                '<div class="co-appr-tl-col-hd">Status (Date &amp; Time)</div>' +
                                '<div class="co-appr-tl-col-hd">Email Reminder</div>' +
                            '</div>';
                        var $emptyState = $main.find('.co-appr-empty-state');
                        if ($emptyState.length) { $emptyState.replaceWith(colHdr + newEntry); }
                        else { $main.find('.co-appr-tl-cols').length ? $main.find('.co-appr-note').before(newEntry) : $main.prepend(colHdr + newEntry); }
                    }
                    // Update header subtitle
                    $main.find('[style*="font-size:10px;color:#7e8299"]').first().text('Awaiting decision from approver.');
                }

                // Send Reminder handler
                $(document).on('click', '.co-appr-remind-send', function() {
                    var $btn = $(this);
                    var rEmail  = $btn.data('email');
                    var rOffId  = $btn.data('offerid');
                    if (!rEmail) return;
                    var mailIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
                    $btn.html(mailIcon + ' Sending…').css({'pointer-events':'none','opacity':'0.6'});
                    $.ajax({
                        type:'POST', url:"{{ route('service_company.cvtracker.fwd_offer_send') }}",
                        data:{ '_token':"{{ csrf_token() }}", 'template':'', 'from_email':"{{ auth()->user()->email ?? '' }}", 'to_email':rEmail, 'cc_email':'', 'subject':'', 'attached_cv':0, 'editor1':'', 'arr':[], 'mtypeId':8, 'offerId':rOffId, 'signature':0 },
                        success:function(r) {
                            $btn.html(mailIcon + ' Send Reminder').css({'pointer-events':'','opacity':'1'});
                            if (r.status === 'rateLimited') { Swal.fire('Already Sent', r.message, 'info'); }
                            else if (r.status === 'success') {
                                $btn.closest('.co-appr-tl-rem').find('.co-appr-tl-last-sent').text('Last sent: just now');
                                Swal.fire({icon:'success', title:'Reminder sent', timer:1500, showConfirmButton:false});
                            } else { Swal.fire('Error', r.message || 'Could not send reminder.', 'error'); }
                        },
                        error:function() { $btn.html(mailIcon + ' Send Reminder').css({'pointer-events':'','opacity':'1'}); Swal.fire('Error','Could not send reminder.','error'); }
                    });
                });

                // Function to move to the previous step
                function prevStep(event, currentStep) {
                    event.preventDefault();
                    document.getElementById("step" + currentStep).classList.remove("active");
                    document.getElementById("step" + (currentStep - 1)).classList.add("active");
                    if (typeof syncStepHeader === 'function') syncStepHeader();
                }

                $(document).ready(function() {

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

                    // ── Step 2 (Offer Letter Template) — everything below is client-side only,
                    // no extra server round-trips: tab switching, zoom, insert-variable, and the
                    // sample export all run against the existing CKEditor instance already loaded
                    // on the page. (The Templates dropdown in the heading row has exactly one real
                    // option today — no filter/switch logic needed unless a real catalog is added.)

                    // Template Editor / Preview tabs
                    $(document).on('click', '.co-tpl-tab', function() {
                        var tab = $(this).data('co-tab');
                        $('.co-tpl-tab').removeClass('co-tpl-tab--active');
                        $(this).addClass('co-tpl-tab--active');
                        $('.co-tpl-tabpane').addClass('d-none');
                        $('.co-tpl-tabpane[data-co-pane="' + tab + '"]').removeClass('d-none');
                        if (tab === 'preview' && CKEDITOR.instances.editorterms1) {
                            $('#co_tpl_preview_content').html(CKEDITOR.instances.editorterms1.getData());
                        }
                    });

                    // Insert Variables — inserts this offer's actual resolved value at the cursor
                    // (the template is merged once, server-side, before this page loads, so
                    // inserting a raw [PLACEHOLDER] token here would never get substituted again).
                    $(document).on('click', '.co-var-chip', function() {
                        var val = $(this).data('co-var');
                        if (!val) return;
                        if (CKEDITOR.instances.editorterms1) {
                            CKEDITOR.instances.editorterms1.insertText(String(val));
                            CKEDITOR.instances.editorterms1.focus();
                        }
                    });

                    // Zoom controls — scales the CKEditor content area's font-size only (view aid,
                    // does not touch the saved HTML).
                    var coTplZoom = 100;
                    function coApplyTplZoom() {
                        $('#co_tpl_zoom_pct').text(coTplZoom + '%');
                        if (CKEDITOR.instances.editorterms1) {
                            var doc = CKEDITOR.instances.editorterms1.document;
                            if (doc && doc.getBody()) {
                                doc.getBody().setStyle('font-size', (coTplZoom / 100 * 14) + 'px');
                            }
                        }
                    }
                    $(document).on('click', '#co_tpl_zoom_in', function() {
                        coTplZoom = Math.min(coTplZoom + 10, 150);
                        coApplyTplZoom();
                    });
                    $(document).on('click', '#co_tpl_zoom_out', function() {
                        coTplZoom = Math.max(coTplZoom - 10, 70);
                        coApplyTplZoom();
                    });

                    // Download Sample — exports the current (unsaved) editor content as a
                    // standalone .html file, entirely client-side (Blob), no server call.
                    $(document).on('click', '#co_tpl_download_sample', function() {
                        if (!CKEDITOR.instances.editorterms1) return;
                        var html = '<!DOCTYPE html><html><head><meta charset="utf-8">' +
                            '<title>Offer Letter Sample</title></head><body>' +
                            CKEDITOR.instances.editorterms1.getData() + '</body></html>';
                        var blob = new Blob([html], { type: 'text/html' });
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'offer-letter-sample.html';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
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
                        $('#totalpercentage').toggleClass('co-pct-invalid', Math.abs(totalPercentage - 100) > 0.01);
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
                        $('#totalpercentagehouly').toggleClass('co-pct-invalid', Math.abs(totalPercentage - 100) > 0.01);
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
                        newRow.find('.co-row-lead, .co-comp-badge').remove(); // Strip cloned row-number/badge markup — coEnhanceRows() re-injects the correct one for this table
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
                            newRow.find('.co-row-lead, .co-comp-badge').remove(); // Strip cloned row-number/badge markup — coEnhanceRows() re-injects the correct one for this table
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
                            newRow.find('.co-row-lead, .co-comp-badge').remove(); // Strip cloned row-number/badge markup — coEnhanceRows() re-injects the correct one for this table
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
                                    Swal.fire({
                                        title: "Error",
                                        text: "Main Components must add up to exactly 100%. Please adjust the allocation of your Main Components and try again.",
                                        icon: "warning"
                                    });
                                }else if(response.status == 'permisionInfo') {
                                    Swal.fire("Warning", response.view, "warning");
                                }else{
                                    Swal.fire("Warning", response.message, "warning");
                                }

                            },
                            error: function(response) {
                                // Backend returns one message per empty field (e.g. "Offer point is
                                // required.", "Bonus offer monthly is required.") — that reads like a
                                // wall of jargon to a normal user. Group the raw field names into plain,
                                // actionable sentences instead of dumping the field-level list.
                                let errors = response.responseJSON.errors || {};
                                let groups = { main: false, additional: false, details: false };
                                $.each(errors, function(fieldName) {
                                    if (/^(offer_point|option_percentage|option_salarymode|offer_monthly|offer_yearly)/.test(fieldName)) {
                                        groups.main = true;
                                    } else if (/^bonus_/.test(fieldName)) {
                                        groups.additional = true;
                                    } else if (/^(ctc|dob|current_designation|finalpaytotal)/.test(fieldName)) {
                                        groups.details = true;
                                    }
                                });

                                let messages = [];
                                if (groups.main) {
                                    messages.push('Main Components rows cannot be empty. Please fill in every field (Point, Allocation, Condition, Monthly, Annually) for each row you\'ve added, or remove the empty row.');
                                }
                                if (groups.additional) {
                                    messages.push('Additional Components rows cannot be empty. Please fill in every field for each row you\'ve added, or remove the empty row.');
                                }
                                if (groups.details) {
                                    messages.push('Please complete the Compensation Details section (CTC, Offered Designation and Date of Joining) before proceeding.');
                                }
                                if (!messages.length) {
                                    messages.push('Please review the highlighted fields and try again.');
                                }

                                let errorMessages = messages.map(function(msg, i) {
                                    return `<div style="margin-bottom:8px;">${i + 1}. ${msg}</div>`;
                                }).join('');

                                Swal.fire({
                                    title: "Error",
                                    html: `<div style="text-align:left;font-size:13px;color:#3f4254;">${errorMessages}</div>`,
                                    icon: "warning"
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

                            var jobId = $(this).attr('data-jobid');
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

                function coPaintStep(el, state) {
                    // Only toggle state classes — the sub-label text is a static description
                    // ("Build salary structure", etc.) and must not be overwritten.
                    el.classList.remove('co-step--pending', 'co-step--current', 'co-step--done');
                    el.classList.add('co-step--' + state);
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
                        if (n < active) coPaintStep(step, 'done');
                        else if (n === active) coPaintStep(step, 'current');
                        else coPaintStep(step, 'pending');
                    });
                    if (typeof coEnsureStep5Composer === 'function') coEnsureStep5Composer();
                }

                // Native Step 5 composer — initialises CKEditor on the body once Step 5 is active.
                // No AJAX loader: the composer markup is server-rendered with real values; only the
                // rich-text editor needs client-side init.
                var coSendEditorReady = false;
                function coEnsureStep5Composer() {
                    var step5 = document.getElementById('step5');
                    if (!step5 || !step5.classList.contains('active')) return;
                    if (coSendEditorReady) return;
                    if (!document.getElementById('co_send_editor')) return;
                    if (typeof CKEDITOR === 'undefined') return;
                    coSendEditorReady = true;
                    try {
                        CKEDITOR.replace('co_send_editor', {
                            height: 300,
                            toolbar: [
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                                { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                                { name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
                                { name: 'links', items: ['Link'] }
                            ]
                        });
                    } catch (e) { coSendEditorReady = false; }
                }

                // ── Chip inputs (To / CC) ──
                function coAddChip(containerId, email) {
                    email = (email || '').trim().replace(/,$/, '');
                    if (!email) return;
                    var $c = $('#' + containerId);
                    // de-dupe
                    var exists = false;
                    $c.find('.co-send-chip').each(function () { if ($(this).clone().children().remove().end().text().trim() === email) exists = true; });
                    if (exists) return;
                    $('<span class="co-send-chip"></span>').text(email).append('<i class="co-chip-x">&times;</i>').insertBefore($c.find('.co-send-chip-input'));
                }
                function coCollectChips(containerId) {
                    var out = [];
                    $('#' + containerId).find('.co-send-chip').each(function () { out.push($(this).clone().children().remove().end().text().trim()); });
                    return out;
                }
                // "+ Add Email" → open the teammate/client picker drawer for that field.
                $(document).on('click', '.co-send-addemail', function (e) {
                    e.preventDefault();
                    coOpenSendDrawer($(this).data('chip-target'));
                });
                // Typing an email + Enter/comma still adds it directly as a chip.
                $(document).on('keydown', '.co-send-chip-input', function (e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        var target = $(this).closest('.co-send-chips').attr('id');
                        coAddChip(target, $(this).val()); $(this).val('');
                    }
                });
                $(document).on('click', '.co-send-chip .co-chip-x', function () { $(this).closest('.co-send-chip').remove(); coSyncDrawerAddedState(); });

                // ── Add-Email drawer (previous "Select Approver" design: source cards + table) ──
                function coSendActiveSource() { return $('#co_send_drawer .co-appr-source-card--active').data('source') || 'internal'; }
                function coOpenSendDrawer(target) {
                    $('#co_send_drawer').attr('data-target', target).addClass('co-send-drawer--open');
                    $('#co_send_drawer_bd').addClass('co-send-drawer-bd--open');
                    coSyncDrawerAddedState();
                    $('#co_send_drawer_search').val('').trigger('input');
                }
                function coCloseSendDrawer() {
                    $('#co_send_drawer').removeClass('co-send-drawer--open');
                    $('#co_send_drawer_bd').removeClass('co-send-drawer-bd--open');
                }
                // Reflect which members are already chips in the current target field.
                function coSyncDrawerAddedState() {
                    var target = $('#co_send_drawer').attr('data-target') || 'co_to_chips';
                    var present = coCollectChips(target).map(function (e) { return e.toLowerCase(); });
                    $('#co_send_drawer .co-send-mrow').each(function () {
                        var em = String($(this).data('email') || '').toLowerCase();
                        var added = present.indexOf(em) !== -1;
                        var $btn = $(this).find('.co-send-mrow-add');
                        $btn.toggleClass('co-send-mrow--added', added).text(added ? 'Added' : 'Add');
                    });
                }
                $(document).on('click', '#co_send_drawer_close, #co_send_drawer_bd', function () { coCloseSendDrawer(); });
                // Source cards (Internal / Client)
                $(document).on('click', '.co-appr-source-card', function () {
                    var src = $(this).data('source');
                    $('#co_send_drawer .co-appr-source-card').removeClass('co-appr-source-card--active');
                    $(this).addClass('co-appr-source-card--active');
                    $(this).find('input[type=radio]').prop('checked', true);
                    $('#co_send_member_table .co-send-mrow, #co_send_member_table .co-send-mrow-empty').addClass('d-none');
                    $('#co_send_member_table .co-send-mrow--' + src + ', #co_send_member_table .co-send-mrow-empty.co-send-mrow--' + src).removeClass('d-none');
                    $('#co_send_drawer_search').trigger('input');
                });
                // Search within the active source
                $(document).on('input', '#co_send_drawer_search', function () {
                    var q = $(this).val().trim().toLowerCase();
                    var src = coSendActiveSource();
                    $('#co_send_member_table .co-send-mrow--' + src + ':not(.co-send-mrow-empty)').each(function () {
                        var hay = String($(this).data('search') || '');
                        $(this).toggleClass('d-none', q !== '' && hay.indexOf(q) === -1);
                    });
                    // "+ Add More" only lights up once what's typed looks like an email —
                    // it's for adding someone NOT in the searchable list below, not a filter action.
                    $('#co_send_addmore').prop('disabled', !/^\S+@\S+\.\S+$/.test(q));
                });
                // Manually add a typed email that isn't one of the searchable team members
                $(document).on('click', '#co_send_addmore', function () {
                    var email = $('#co_send_drawer_search').val().trim();
                    if (!/^\S+@\S+\.\S+$/.test(email)) return;
                    var target = $('#co_send_drawer').attr('data-target') || 'co_to_chips';
                    coAddChip(target, email);
                    $('#co_send_drawer_search').val('').trigger('input');
                });
                // Add / remove a member email from the current target field
                $(document).on('click', '.co-send-mrow-add', function () {
                    var $row = $(this).closest('.co-send-mrow');
                    var email = String($row.data('email') || '').trim();
                    if (!email) return;
                    var target = $('#co_send_drawer').attr('data-target') || 'co_to_chips';
                    if ($(this).hasClass('co-send-mrow--added')) {
                        $('#' + target).find('.co-send-chip').each(function () {
                            if ($(this).clone().children().remove().end().text().trim().toLowerCase() === email.toLowerCase()) $(this).remove();
                        });
                    } else {
                        coAddChip(target, email);
                    }
                    coSyncDrawerAddedState();
                });

                // ── E-Signature toggle text (the toggle itself already posts to `signature`, which
                //    fwd_offer_send() already reads — this only updates the label next to it) ──
                $(document).on('change', '#co_send_signature', function () {
                    $('#co_send_sigtext').text($(this).is(':checked') ? 'With E-Signature' : 'Without E-Signature');
                });

                // ── Template select (reuses the existing email_template endpoint/behavior from the
                //    original composer — fills Subject + Body, doesn't touch anything else) ──
                $(document).on('change', '#co_send_template', function () {
                    var template = $(this).val();
                    if (!template) return;
                    // mailtype 7 = candidate offer email (same code path offerSendMail() used for
                    // mtype==1) — it's what fills COMPANY_NAME/CANDIDATE_NAME/JOB_TITLE/OFFER_LETTER_LINK
                    // placeholders in the template using the real offer/candidate data server-side.
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('service_company.cvtracker.email_template') }}",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'template': template,
                            'selectedApplicantListing': [$('#co_send_appid').val()],
                            'offerId': $('#co_send_offerid').val(),  
                            'mailtype': 7,
                            'to_email': $('#co_send_candidateemail').val(),
                            'from_email': JSON.stringify([{ value: $('#co_send_fromemail').val() }])
                        },
                        success: function (response) {
                            if (!response) return;
                            if (response.subject) { $('#co_send_subject').val(response.subject); }
                            if (response.template_description) {
                                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.co_send_editor) {
                                    CKEDITOR.instances.co_send_editor.setData(response.template_description);
                                } else {
                                    $('#co_send_editor').val(response.template_description);
                                }
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Could not load the selected template. Please try again.', 'error');
                        }
                    });
                });

                @if(!empty($is_bench_candidate))
                // Auto-load the Relationship Manager template for bench candidates
                $(function () {
                    var $tpl = $('#co_send_template');
                    if ($tpl.val()) { $tpl.trigger('change'); }
                });
                @endif

                // ── Attachments ("+ Add Attachment" — real files, actually sent with the email) ──
                var coAttachFiles = [];
                function coFileSizeLabel(bytes) {
                    var kb = bytes / 1024;
                    return kb < 1024 ? Math.round(kb) + ' KB' : (kb / 1024).toFixed(1) + ' MB';
                }
                function coRenderAttachments() {
                    var $list = $('#co_send_attach_list').empty();
                    $('#co_send_attach_count').text(coAttachFiles.length ? '(' + coAttachFiles.length + ')' : '');
                    coAttachFiles.forEach(function (file, idx) {
                        var isDoc = /\.(docx?|xls|xlsx)$/i.test(file.name);
                        var $chip = $('<div class="co-send-attach-chip"></div>');
                        $chip.append('<span class="co-send-attach-ico' + (isDoc ? ' co-send-attach-ico--doc' : '') + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg></span>');
                        var $meta = $('<div class="co-send-attach-meta"></div>');
                        $meta.append($('<div class="co-send-attach-name"></div>').text(file.name));
                        $meta.append($('<div class="co-send-attach-size"></div>').text(coFileSizeLabel(file.size)));
                        $chip.append($meta).append($('<i class="co-send-attach-x">&times;</i>').data('idx', idx));
                        $list.append($chip);
                    });
                }
                $(document).on('click', '#co_send_addattach', function () { $('#co_send_attach_input').trigger('click'); });
                $(document).on('change', '#co_send_attach_input', function () {
                    Array.prototype.slice.call(this.files || []).forEach(function (f) {
                        if (coAttachFiles.length >= 3) { Swal.fire('Warning', 'You can attach a maximum of 3 files. ' + f.name + ' was skipped.', 'warning'); return; }
                        if (f.size > 2 * 1024 * 1024) { Swal.fire('Warning', f.name + ' exceeds 2 MB and was skipped.', 'warning'); return; }
                        coAttachFiles.push(f);
                    });
                    this.value = '';
                    coRenderAttachments();
                });
                $(document).on('click', '.co-send-attach-x', function () {
                    coAttachFiles.splice($(this).data('idx'), 1);
                    coRenderAttachments();
                });

                // ── Send Offer Letter (native → same fwd_offer_send endpoint) ──
                $(document).on('click', '#co_send_submit', function () {
                    var $btn = $(this);
                    // fold any half-typed chip text into a chip first (To has no input — it's locked to the candidate)
                    $('#co_cc_chips').each(function () {
                        var id = $(this).attr('id'); var $inp = $(this).find('.co-send-chip-input');
                        if ($inp.length && $inp.val().trim()) { coAddChip(id, $inp.val()); $inp.val(''); }
                    });
                    var toArr = coCollectChips('co_to_chips');
                    if (!toArr.length) { Swal.fire('Warning', 'Please add at least one recipient email address.', 'warning'); return; }
                    var subject = $('#co_send_subject').val().trim();
                    if (!subject) { Swal.fire('Warning', 'Please enter an email subject.', 'warning'); return; }
                    var body = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.co_send_editor) ? CKEDITOR.instances.co_send_editor.getData() : '';
                    var fromEmail = $('#co_send_fromemail').val();
                    var formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('template', '');
                    formData.append('from_email', JSON.stringify([{ value: fromEmail }]));   // backend json_decodes this
                    formData.append('to_email', toArr.join(','));
                    formData.append('cc_email', coCollectChips('co_cc_chips').join(','));
                    formData.append('subject', subject);
                    formData.append('attached_cv', 0);
                    formData.append('editor1', body);
                    formData.append('mtypeId', 1);
                    formData.append('offerId', $('#co_send_offerid').val());
                    formData.append('signature', $('#co_send_signature').is(':checked') ? 1 : 0);
                    coAttachFiles.forEach(function (f) { formData.append('attachments[]', f); });
                    $btn.prop('disabled', true);
                    $btn.find('.co-send-submit-label').hide(); $btn.find('.co-send-submit-wait').show();
                    $.ajax({
                        type: 'POST',
                        {{-- Relative URL, not route()'s default absolute one — the deployed .env's
                             APP_URL points at a different, unrelated domain (hire.jero.works), so an
                             absolute URL here silently made this a cross-origin POST that never actually
                             reached this server. Same root cause as the Download Offer Letter bug. --}}
                        url: "{{ route('service_company.cvtracker.fwd_offer_send') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (r) {
                            $btn.prop('disabled', false);
                            $btn.find('.co-send-submit-label').show(); $btn.find('.co-send-submit-wait').hide();
                            if (r && r.status === 'coinCheck') { Swal.fire('Failed', r.message || 'Insufficient coins.', 'warning'); return; }
                            if (r && r.status === 'rateLimited') { Swal.fire('Limit Reached', r.message || 'This offer letter has already been sent the maximum number of times recently.', 'warning'); return; }
                            Swal.fire('Success', 'Offer letter sent to the candidate.', 'success');
                            // The hero's Offer Status/Sent By were server-rendered at page load and don't
                            // otherwise refresh after a successful send — reflect it immediately instead of
                            // leaving "Not Sent" on screen until the whole drawer is reopened.
                            $('.co-send-fact-v.co-send-fact-notsent').removeClass('co-send-fact-notsent').addClass('co-send-fact-sent').text('Sent');
                            $('.co-send-fact-v.co-send-fact-sentby').html(
                                '<span class="co-send-sentby-name">{{ e($so_from_name) }}</span><span class="co-send-sentby-when">Sent just now</span>'
                            );
                        },
                        error: function () {
                            $btn.prop('disabled', false);
                            $btn.find('.co-send-submit-label').show(); $btn.find('.co-send-submit-wait').hide();
                            Swal.fire('Error', 'Could not send the offer letter. Please try again.', 'error');
                        }
                    });
                });

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
                        coToggleAdditionalEmpty();
                    }

                    // Show the empty-state (illustration + CTA) when there are no additional
                    // components; show the table + total line once at least one row exists.
                    // Counts only real data rows (those with a bonus name input), not the header.
                    function coToggleAdditionalEmpty() {
                        var $empty = $('#co_additional_empty');
                        if (!$empty.length) return;
                        // Count real data rows for the CURRENT salary-mode section (not :visible —
                        // the parent table may be hidden by this very function, which would make a
                        // freshly-added row read as invisible and the list never re-appear).
                        var dataRows = $('#bonusList tbody tr.row-template').filter(function () {
                            return !$(this).hasClass('d-none') && $(this).find('.bonus_points, .bonus_point_hourly').length > 0;
                        }).length;
                        if (dataRows === 0) {
                            $empty.show();
                            $('#bonusList').closest('.row').hide();
                            $('#co_additional_total').hide();
                        } else {
                            $empty.hide();
                            $('#bonusList').closest('.row').show();
                            $('#co_additional_total').show();
                        }
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
