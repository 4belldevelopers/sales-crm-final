@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        #send_email .modal-dialog {
            margin: .3rem auto !important;
        }

        #fwd_resume .modal-dialog {
            margin: .3rem auto !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {

            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0rem;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesClient {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClient {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }
        /* Tagify chip star toggle — visual properties are set as inline styles in the
           JS template (see the Skills tagify below) so they can't be overridden by
           plugins.bundle.css (loaded mid-body after this <style> block). Only the
           interactive checked state is here. */
        .tagify__tag .star {
            cursor: pointer;
        }
        .tagify__tag .star.checked {
            color: #eab308 !important;
        }
        .tagify__tag__removeBtn,
        .tagify__tag__removeBtn::before,
        .tagify__tag__removeBtn::after {
            mask-image: none !important;
            -webkit-mask-image: none !important;
            content: none !important;
        }
        .tagify__tag__removeBtn:hover {
            background: rgba(37,99,235,.28) !important;
            color: #1d4ed8 !important;
        }
        /* Location autocomplete dropdown is appended to <body> (appendTarget:document.body)
           so it needs a z-index above the filter drawer (z-index:1200) and its backdrop. */
        body > .tagify__dropdown {
            z-index: 1400 !important;
        }

        /* Print — the "Print" button clones the already-rendered checked
           rows (no extra server request, everything comes from the current
           page) into this hidden container, then triggers the browser's own
           print dialog. Hidden on screen; @media print flips it to the only
           visible thing on the page. */
        #cvh_print_area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #cvh_print_area, #cvh_print_area * { visibility: visible; }
            #cvh_print_area {
                display: block;
                position: absolute;
                top: 0; left: 0;
                width: 100%;
                padding: 20px;
            }
            #cvh_print_area h2 { font-size: 16px; margin: 0 0 4px; }
            #cvh_print_area .cvh-print-meta { font-size: 11px; color: #666; margin-bottom: 14px; }
            #cvh_print_area table { width: 100%; border-collapse: collapse; }
            #cvh_print_area th, #cvh_print_area td {
                border: 1px solid #ccc; padding: 6px 8px; font-size: 11px; text-align: left;
                vertical-align: top;
            }
            #cvh_print_area .form-check { display: none !important; }
        }

        /* ============================================================
           CV Tracker — Hero KPI stats
           Data source: Elasticsearch only, riding the existing
           allcandidates() fetch (no extra request, no MySQL load).
           Poppins per UI standard, 14px hard cap.
        ============================================================ */
        .cvh-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin: 14px 0; font-family: 'Poppins', sans-serif; }
        .cvh-card { background: #fff; border-radius: 14px; padding: 13px 16px; border: 1px solid #f1f5f9; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .cvh-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .cvh-icon svg { width: 18px; height: 18px; }
        .cvh-body { flex: 1; min-width: 0; }
        .cvh-label { font-size: 11px; color: #64748b; font-weight: 500; margin: 0 0 4px; }
        .cvh-value { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.15; }
        .cvh-delta { font-size: 11px; margin-top: 4px; font-weight: 500; }
        .cvh-delta.up   { color: #16a34a; }
        .cvh-delta.info { color: #64748b; }
        .cvh-delta.warn { color: #dc2626; }

        @media (max-width: 1440px) { .cvh-value { font-size: 16px; } }
        @media (max-width: 1200px) { .cvh-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .cvh-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }
        @media (max-width: 480px)  { .cvh-grid { grid-template-columns: 1fr; } }

        /* Stage tabs — own card, full-width stretch row (each tab takes an equal share
           of the row, edge to edge) with icon + label on one line and the count below.
           Colors match the app's own "Pipeline Stages" legend
           (resources/views/sections/service_company/pipeline.blade.php) exactly, so a
           given stage reads as the same color everywhere. Inactive tabs stay neutral
           gray; a tab only takes on its stage color once it's the active/selected tab. */
        .cvh-tabs-card { margin-bottom: 14px; border-width: 1px; transition: border-color .15s; }
        .cvh-tabs-card .card-body { padding: 4px 0; }
        .cvh-tabs { display: flex; align-items: stretch; font-family: 'Poppins', sans-serif; }
        .cvh-tab { flex: 1 1 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; padding: 11px 6px; border: none; border-right: 1px solid #f1f5f9; background: transparent; color: #64748b; white-space: nowrap; cursor: pointer; transition: background .15s, color .15s; }
        .cvh-tab:last-child { border-right: none; }
        .cvh-tab:hover { background: #f8fafc; }
        .cvh-tab-top { display: flex; align-items: center; gap: 5px; }
        .cvh-tab-icon { display: flex; }
        .cvh-tab-icon svg { width: 14px; height: 14px; }
        .cvh-tab-label { line-height: 1.1; font-size: 11px; font-weight: 500; }
        .cvh-tab-count { font-size: 13px; font-weight: 700; line-height: 1.1; color: inherit; }
        .cvh-tab.active .cvh-tab-label { font-weight: 700; }
        .cvh-tab.stage-total.active       { color: #2563eb; background: rgba(37,99,235,.10); }
        .cvh-tab.stage-inreview.active    { color: #bdbd26; background: rgba(189,189,38,.14); }
        .cvh-tab.stage-sourcing.active    { color: #7e8299; background: rgba(126,130,153,.14); }
        .cvh-tab.stage-screening.active   { color: #c000ff; background: rgba(192,0,255,.10); }
        .cvh-tab.stage-evaluation.active  { color: #2896ff; background: rgba(40,150,255,.12); }
        .cvh-tab.stage-negotiation.active { color: #ff4098; background: rgba(255,64,152,.12); }
        .cvh-tab.stage-offered.active     { color: #ffab00; background: rgba(255,171,0,.14); }
        .cvh-tab.stage-joined.active      { color: #06c732; background: rgba(6,199,50,.12); }
        .cvh-tab.stage-rejected.active    { color: #ff0202; background: rgba(255,2,2,.10); }
        @media (max-width: 768px) { .cvh-tabs { flex-wrap: wrap; } .cvh-tab { flex: 1 1 33%; border-right: none; border-bottom: 1px solid #f1f5f9; padding: 8px 4px; } .cvh-tab-label { font-size: 10.5px; } }

        /* Selected-rows action bar */
        .cvh-selection-bar { display: none; align-items: center; gap: 14px; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; padding: 10px 16px; margin-bottom: 14px; font-family: 'Poppins', sans-serif; flex-wrap: wrap; }
        .cvh-selection-bar.show { display: flex; }
        .cvh-selection-count { font-size: 13px; font-weight: 600; color: #334155; white-space: nowrap; }
        .cvh-selection-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .cvh-selection-actions .btn { font-size: 12px; font-weight: 500; background: #fff !important; border: 1px solid #bfdbfe !important; border-radius: 8px !important; padding: 6px 14px !important; box-shadow: none !important; }
        .cvh-selection-actions .btn:hover { background: #eff6ff !important; border-color: #93c5fd !important; }
        .cvh-selection-close { width: 28px; height: 28px; border-radius: 50%; border: none; background: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cvh-selection-close:hover { background: #f1f5f9; color: #334155; }

        /* The original Mass Email/Fwd Resume/Copy/Export buttons stay in the DOM (so all
           existing modal/ajax click handlers keep firing unmodified) but are permanently
           hidden here — their restyled equivalents now live in #cvh_selection_bar. */
        .cvh-legacy-hide { display: none !important; }

        /* Page header — title + subtext sized/worded to match the Offer Tracker page
           (resources/views/service_company/applicationoffer/index.blade.php .ot-page-head) */
        .cvh-page-title, .cvh-page-title a { font-size: 14px !important; font-weight: 700 !important; color: #0f172a !important; font-family: 'Poppins', sans-serif; }
        .cvh-page-sub { font-size: 11px; color: #64748b; margin: 2px 0 0; font-family: 'Poppins', sans-serif; }

        /* ==================================================================
           Enterprise toolbar redesign: search / sort / filter (cvh-tb-*).
           Mirrors the Similar Candidates / Bench Requests toolbar pattern —
           #kt_tagify_2 is a plain free-text field (name/skills/role/location),
           the Filter button is the drawer's only entry point, and the Active
           Filters card below is the single place structured filters summarize.
        ================================================================== */
        .cvh-tb-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .cvh-tb-search-wrap,
        .cvh-tb-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .cvh-tb-search-wrap { flex: 1 1 auto; min-width: 0; }
        .cvh-tb-sort-wrap { flex-shrink: 0; width: 220px; position: relative; }
        .cvh-tb-search-icon,
        .cvh-tb-sort-icon { color: #99a1b7; flex-shrink: 0; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; }
        .cvh-tb-search-icon svg,
        .cvh-tb-sort-icon svg { flex-shrink: 0; display: block; }
        .cvh-tb-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        /* Custom Sort control — the real <select id="sortingfilter"> stays hidden
           underneath purely so filtersdataget()/fetchCandidates() keep reading it
           unchanged; only the presentation is replaced with this button + panel. */
        .cvh-tb-sort-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3f4254;
            text-align: left;
        }
        .cvh-tb-sort-btn:focus { outline: none; }
        .cvh-tb-sort-btn span { flex: 1 1 auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cvh-tb-sort-btn .chevron { flex-shrink: 0; color: #99a1b7; transition: transform .15s; }
        .cvh-tb-sort-btn.open .chevron { transform: rotate(180deg); }
        .cvh-tb-sort-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 100%;
            width: max-content;
            max-width: 280px;
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(16,24,40,.12), 0 4px 6px -2px rgba(16,24,40,.06);
            padding: 6px;
            z-index: 60;
            max-height: 320px;
            overflow-y: auto;
        }
        .cvh-tb-sort-item { padding: 9px 12px; font-size: 12.5px; font-weight: 500; color: #3f4254; border-radius: 8px; cursor: pointer; white-space: nowrap; }
        .cvh-tb-sort-item:hover { background: #f8f9fb; }
        .cvh-tb-sort-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .cvh-tb-filter-btn {
            height: 44px;
            border-radius: 10px !important;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            padding: 0 16px !important;
            flex-shrink: 0;
            background: #2563eb !important;
            border: 1px solid #2563eb !important;
            color: #fff !important;
        }
        .cvh-tb-filter-btn:hover { background: #1d4ed8 !important; }
        @media (max-width: 992px) { .cvh-tb-sort-wrap { width: 160px; } }
        @media (max-width: 768px) { .cvh-tb-search-wrap { flex-basis: 100%; } .cvh-tb-sort-wrap { width: 140px; } }

        /* ==================================================================
           Applied Filters summary card (cvh-af-*) — lives on the main page
           above the results table, hidden until Apply Filters is clicked.
        ================================================================== */
        .cvh-af-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; padding: 16px 18px; margin-bottom: 16px; font-family: 'Poppins', sans-serif; }
        .cvh-af-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
        .cvh-af-title { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #181c32; }
        .cvh-af-count { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; min-width: 19px; height: 19px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }
        .cvh-af-clearall { font-size: 12px; font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }
        .cvh-af-clearall:hover { color: #b91c1c; }
        .cvh-af-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .cvh-af-chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
        .cvh-af-chip .ic { display: inline-flex; align-items: center; flex-shrink: 0; }
        .cvh-af-chip .star { font-size: 12px; line-height: 1; }
        .cvh-af-chip-remove { cursor: pointer; opacity: .55; display: inline-flex; align-items: center; margin-left: 2px; }
        .cvh-af-chip-remove:hover { opacity: 1; }
        .cvh-af-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .cvh-af-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .cvh-af-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .cvh-af-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .cvh-af-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .cvh-af-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .cvh-af-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
        .cvh-af-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .cvh-af-more { display: inline-flex; align-items: center; gap: 4px; border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600; background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; cursor: pointer; }
        .cvh-af-more:hover { background: #e5e7eb; }

        /* ==================================================================
           Filter drawer (cvh-flt-*) — replaces the old #apptrackerfilter
           Bootstrap modal. Force the drawer to a fixed, viewport-bound box so
           only .cvh-flt-body scrolls internally.
        ================================================================== */
        #kt_cvtracker_filter_drawer.cvh-flt-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: auto !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            z-index: 1200;
        }
        #cvh_flt_card.cvh-flt-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .cvh-flt-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 20px 20px 16px; border-bottom: 1px solid #eef0f4; flex-shrink: 0; }
        .cvh-flt-header-icon { width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-title { font-size: 17px; font-weight: 700; color: #181c32; margin: 0; }
        .cvh-flt-count-pill { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }
        .cvh-flt-subtitle { font-size: 12px; color: #7e8299; margin: 2px 0 0; }
        .cvh-flt-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: #7e8299; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-close:hover { background: #f1f5f9; color: #334155; }
        .cvh-flt-activebar { padding: 12px 0; border-bottom: 1px solid #eef0f4; }
        .cvh-flt-activebar-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .cvh-flt-activebar-label { font-size: 12px; font-weight: 600; color: #181c32; }
        .cvh-flt-clearall { font-size: 12px; font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }
        .cvh-flt-clearall:hover { color: #b91c1c; }
        .cvh-flt-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .cvh-flt-chip { display: inline-flex; align-items: center; gap: 6px; background: #eef2ff; color: #3730a3; border: 1px solid #e0e7ff; border-radius: 8px; padding: 5px 7px 5px 10px; font-size: 11.5px; font-weight: 500; }
        .cvh-flt-chip.mandatory { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
        .cvh-flt-chip-remove { width: 15px; height: 15px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(0,0,0,.06); cursor: pointer; flex-shrink: 0; }
        .cvh-flt-chip-remove:hover { background: rgba(0,0,0,.14); }
        #cvh_flt_card .cvh-flt-body { flex: 1 1 auto !important; min-height: 0 !important; overflow-y: auto !important; overflow-x: hidden !important; padding: 4px 20px 16px; }
        #cvh_flt_card .cvh-flt-header,
        #cvh_flt_card .cvh-flt-footer { flex-shrink: 0 !important; }
        .cvh-flt-section { padding: 16px 0; border-bottom: 1px solid #f1f2f6; }
        .cvh-flt-section-last { border-bottom: none; }
        .cvh-flt-section-head { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
        .cvh-flt-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-icon-blue { background: #eef2ff; color: #4f46e5; }
        .cvh-flt-icon-green { background: #ecfdf5; color: #059669; }
        .cvh-flt-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .cvh-flt-icon-amber { background: #fffbeb; color: #b45309; }
        .cvh-flt-icon-rose { background: #fff1f2; color: #be123c; }
        .cvh-flt-icon-pink { background: #fdf2f8; color: #be185d; }
        .cvh-flt-icon-sky { background: #f0f9ff; color: #0369a1; }
        .cvh-flt-section-title { font-size: 13.5px; font-weight: 600; color: #181c32; display: flex; align-items: center; gap: 6px; }
        .cvh-flt-section-desc { font-size: 11.5px; color: #99a1b7; margin-top: 1px; }
        .cvh-flt-badge-primary { font-size: 9.5px; font-weight: 600; color: #2563eb; background: #dbeafe; border-radius: 4px; padding: 1px 6px; }
        .cvh-flt-mini-label { font-size: 11px; font-weight: 500; color: #7e8299; margin-bottom: 4px; display: block; }
        .cvh-flt-salary-row { flex-wrap: nowrap; }
        .cvh-flt-salary-row > [class^="col-"] { padding-left: 4px; padding-right: 4px; min-width: 0; }
        .cvh-flt-salary-row .form-select,
        .cvh-flt-salary-row .form-control { font-size: 11px; padding-left: 8px; padding-right: 8px; }
        .cvh-flt-salary-row .form-select { padding-right: 20px; }
        .cvh-flt-pill-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .cvh-flt-select-dd { position: relative; }
        .cvh-flt-select-dd-btn {
            width: 100%; display: flex; align-items: center; justify-content: space-between;
            background: #f9f9f9; border: 1px solid #e4e6ef; border-radius: 8px;
            padding: 9px 12px; font-size: 12.5px; color: #3f4254; text-align: left;
        }
        .cvh-flt-select-dd-btn span {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-right: 8px;
            /* Education/qualification data is always displayed uppercase, regardless of
               how it was entered/stored — see docs/UI_UX_STANDARDS.md. Display-only. */
            text-transform: uppercase;
        }
        .cvh-flt-edu-name { text-transform: uppercase; }
        .cvh-flt-select-dd-btn svg { flex-shrink: 0; color: #99a1b7; }
        .cvh-flt-select-dd-panel {
            margin-top: 6px; border: 1px solid #e4e6ef; border-radius: 8px;
            background: #fff; max-height: 220px; overflow-y: auto;
        }
        .cvh-flt-select-dd-item {
            display: flex; align-items: center; gap: 8px; padding: 9px 12px;
            font-size: 12.5px; color: #3f4254; cursor: pointer; margin: 0;
            border-bottom: 1px solid #f5f6f8;
        }
        .cvh-flt-select-dd-item:last-child { border-bottom: none; }
        .cvh-flt-select-dd-item:hover { background: #f8f9fb; }
        .cvh-flt-select-dd-item input { accent-color: #2563eb; }
        /* Skills/Location suggestion dropdown — Tagify's own stable class names
           (already relied on elsewhere in this file for the tag-chip template),
           styled here purely at the CSS level so it works regardless of the
           bundled Tagify version's JS template-hook support. */
        .tagify__dropdown__wrapper {
            border: 1px solid #e4e6ef !important;
            border-radius: 10px !important;
            box-shadow: 0 8px 24px -4px rgba(24, 28, 50, 0.12), 0 2px 6px -2px rgba(24, 28, 50, 0.06) !important;
            overflow: hidden;
            margin-top: 6px !important;
        }
        .tagify__dropdown__itemsList {
            max-height: 260px;
            padding: 4px 0 !important;
        }
        .tagify__dropdown__item {
            padding: 9px 14px !important;
            margin: 0 !important;
            border-radius: 0 !important;
            font-size: 12.5px;
            color: #3f4254;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-bottom: 1px solid #f5f6f8;
            transition: background-color .12s ease;
        }
        .tagify__dropdown__item:last-child { border-bottom: none; }
        .tagify__dropdown__item--active,
        .tagify__dropdown__item:hover {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
        }
        .tagify__dropdown__item mark {
            background: none;
            color: inherit;
            font-weight: 700;
            padding: 0;
        }
        .cvh-flt-pill-row .btn-check { position: absolute; opacity: 0; pointer-events: none; }
        .cvh-flt-pill { display: inline-flex; align-items: center; background: #fff; border: 1px solid #e4e6ef; border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 500; color: #3f4254; cursor: pointer; transition: border-color .15s, background-color .15s, color .15s; }
        .cvh-flt-pill-row .btn-check:checked + .cvh-flt-pill { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .cvh-flt-footer { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 20px; border-top: 1px solid #eef0f4; background: #f8f9fb; flex-shrink: 0; flex-wrap: wrap; }
        .cvh-flt-footer-count { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .cvh-flt-footer-icon { width: 36px; height: 36px; border-radius: 8px; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-footer-number { font-size: 15px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .cvh-flt-footer-label { font-size: 10.5px; color: #7e8299; }
        .cvh-flt-footer-actions { display: flex; gap: 6px; }
        .cvh-flt-footer-actions .btn { font-size: 11.5px; padding: 6px 10px; display: inline-flex; align-items: center; gap: 5px; }
        @media (max-width: 576px) {
            .cvh-flt-footer { flex-direction: column; align-items: stretch; }
            .cvh-flt-footer-actions { justify-content: stretch; }
            .cvh-flt-footer-actions .btn { flex: 1 1 auto; justify-content: center; }
        }

    </style>
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="cvh-page-title d-flex align-items-center my-1"> <a href="javascript:void(0);"
                            class="text-primary-800">All Candidates Tracker</a> </h5>
                    <p class="cvh-page-sub">Track and Manage all your candidates of every job in one place</p>
                    {{-- Kept in the DOM (hidden) — fetchCandidates() still writes the live
                         total into it via $('#total_record').html(...); the number itself
                         is already shown prominently in the "Total Candidates" hero card. --}}
                    <span class="text-gray-600 d-none" id="total_record"></span>
                    {{-- {{ @$applicationCounter }} --}}
                </div>
            </div>
            <div class="d-flex align-items-center py-1">
                <!--  <a href="javascript:void(0);" class="me-3 daterange"><span class="svg-icon svg-icon-primary svg-icon-1x">-->
                <input type="hidden" class="form-control form-control-solid" placeholder="Pick date rage"
                    id="kt_daterangepicker" />
                <!--</span> </a>-->

                {{-- Mass Email / Fwd Resume / Copy / Export now live in #cvh_selection_bar
                     (a dedicated selection card shown on row selection, styled per the
                     approved design) — see just above the candidates table. Classes and
                     data-target attributes are unchanged so every existing modal/ajax
                     handler still fires from their new location. --}}
                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-3 d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#mass_mail"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                    fill="#000000" opacity="0.3" />
                                <path
                                    d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                    fill="#000000" />
                            </g>
                        </svg>
                    </span> Mass Email </a>


                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fwd_resume fw-bolder me-3 d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#fwd_resume"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                    fill="#000000" opacity="0.3" />
                                <path
                                    d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                    fill="#000000" />
                            </g>
                        </svg>
                    </span> Fwd Resume </a>
                {{-- <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 send_email d-none" data-toggle="modal" data-target="#send_email"> <span class="svg-icon svg-icon-primary svg-icon-1x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <rect x="0" y="0" width="24" height="24"/>
          <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
          <path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
        </g>
      </svg>
      </span> Send Email </a> --}}
                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 application_copy d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#application_copy">
                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                <path
                                    d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                    fill="#000000" opacity="0.3" />
                            </g>
                        </svg>
                    </span>
                    Copy </a>

                <div class="before-event downloadexcell d-flex align-items-center position-relative my-1 me-3 d-none cvh-legacy-hide">
                   
                    <button type="button"
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 exportRecord"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" style="width: 100px;">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                    fill="black" />
                            </svg>
                        </span>
                        Export </button>
                </div>

                <div class="before-event d-flex align-items-center position-relative my-1 me-3 custom_hide">

                    <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>




                <div class="card-toolbar">
                    <div class="before-event d-flex justify-content-end" data-kt-user-table-toolbar="base">

                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-user-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Role:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="role" data-hide-search="true">
                                        <option></option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Analyst">Analyst</option>
                                        <option value="Developer">Developer</option>
                                        <option value="Support">Support</option>
                                        <option value="Trial">Trial</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="two-step" data-hide-search="true">
                                        <option></option>
                                        <option value="Enabled">Enabled</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-bold px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                {{-- ── Hero KPI stats (ES-only, seeded server-side, refreshed live) ── --}}
                @php
                    $cvHeroCards = [
                        ['key'=>'total','label'=>'Total Candidates','bg'=>'#dbeafe','ic'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['key'=>'today','label'=>'Added Today','bg'=>'#dcfce7','ic'=>'#16a34a','tone'=>'info','text'=>'Since 12:00 AM',
                         'icon'=>'M12 4.5v15m7.5-7.5h-15'],
                        ['key'=>'this_week','label'=>'Added This Week','bg'=>'#ede9fe','ic'=>'#7c3aed','tone'=>'info','text'=>'Mon &ndash; Today',
                         'icon'=>'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
                        ['key'=>'immediate','label'=>'Immediate Joiners','bg'=>'#d1fae5','ic'=>'#059669','tone'=>'up','text'=>'Notice = 0 Days',
                         'icon'=>'M13 2 3 14h7l-1 8 10-12h-7l1-8Z'],
                        ['key'=>'without_resume','label'=>'Without Resume','bg'=>'#fee2e2','ic'=>'#dc2626','tone'=>'warn','text'=>'Needs Attention',
                         'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l4.414 4.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z'],
                    ];
                @endphp
                <div class="cvh-grid">
                    @foreach($cvHeroCards as $card)
                    <div class="cvh-card">
                        <div class="cvh-icon" style="background:{{ $card['bg'] }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $card['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $card['icon'] }}"/></svg>
                        </div>
                        <div class="cvh-body">
                            <p class="cvh-label">{{ $card['label'] }}</p>
                            <p class="cvh-value" data-cvh="{{ $card['key'] }}">{{ number_format($candidateStats[$card['key']] ?? 0) }}</p>
                            @if($card['key'] === 'total')
                                <div class="cvh-delta up">&uarr; <span data-cvh="this_week_delta">{{ number_format($candidateStats['this_week'] ?? 0) }}</span> this week</div>
                            @else
                                <div class="cvh-delta {{ $card['tone'] }}">{!! $card['text'] !!}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- ── Search + Sort + Filter toolbar (cvh-tb-* — mirrors the Similar Candidates /
                     Bench Requests enterprise toolbar redesign: bordered search box, a custom
                     sort dropdown skinned over the real #sortingfilter <select> (single source
                     of truth for filtersdataget()), and a primary Filter button with an
                     active-count badge that opens the cvh-flt-* drawer below. ── --}}
                <div class="card cvh-tb-card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="cvh-tb-search-wrap" style="position: relative;">
                                <span class="cvh-tb-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control cvh-tb-search-input" name="inputJobSearch"
                                    value="" placeholder="Search by name, skills, role, experience, location..." id="kt_tagify_2" autocomplete="off" />
                                <div style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%);">
                                    <div id="loader_search" class="loader_search hidden_search"></div>
                                </div>
                            </div>
                            <div class="cvh-tb-sort-wrap" id="cvh_sort_wrap">
                                <span class="cvh-tb-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="cvh-tb-sort-btn" id="cvh_sort_btn">
                                    <span id="cvh_sort_label">Sort</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                    id="sortingfilter" data-dropdown-css-class="w-150px" style="display:none;"
                                    onchange="filtersdataget()">
                                    <option value="Show All" selected="">Sort</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice Period (Serving)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                    <option value="30">Best Match</option>
                                </select>
                                <div class="cvh-tb-sort-panel d-none" id="cvh_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_cvtracker_filter_toggle"
                                class="btn cvh-tb-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="cvh_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary cvh-tb-count-badge d-none" id="cvh_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Applied Filters summary card — hidden until Apply Filters is clicked ── --}}
                <div class="cvh-af-card d-none" id="cvh_applied_card">
                    <div class="cvh-af-head">
                        <span class="cvh-af-title">
                            Active Filters
                            <span class="cvh-af-count" id="cvh_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="cvh-af-clearall" id="cvh_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="cvh-af-chips" id="cvh_applied_chips"></div>
                </div>
                {{-- ── End Applied Filters summary card ── --}}

                {{-- ── Filter Drawer (cvh-flt-* — right-side Metronic drawer, replaces the old
                     #apptrackerfilter Bootstrap modal). Every field id/name below is unchanged
                     from the previous modal — fetchCandidates() and the shared backend
                     allcandidates() controller method read these exact same ids. ── --}}
                <div id="kt_cvtracker_filter_drawer" class="bg-body cvh-flt-drawer" data-kt-drawer="true" data-kt-drawer-name="cvtracker-filter"
                    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_cvtracker_filter_toggle" data-kt-drawer-close="#kt_cvtracker_filter_close">
                    <div id="cvh_flt_card" class="card shadow-none rounded-0 w-100 h-100 cvh-flt-card">
                        <!--begin::Header-->
                        <div class="cvh-flt-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="cvh-flt-header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="cvh-flt-title">Filter Candidates</h3>
                                        <span class="cvh-flt-count-pill" id="cvh_header_count">0</span>
                                    </div>
                                    <p class="cvh-flt-subtitle">Refine your search and find the best talent</p>
                                </div>
                            </div>
                            <button type="button" class="cvh-flt-close" id="kt_cvtracker_filter_close" title="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="cvh-flt-body">
                            <form id="createSchedule" class="ajax-form" method="post">

                                <!--begin::Active filters — inside scroll area so it flows with the sections -->
                                <div class="cvh-flt-activebar d-none" id="cvh_active_bar">
                                    <div class="cvh-flt-activebar-head">
                                        <span class="cvh-flt-activebar-label">Active Filters <span id="cvh_active_count">(0)</span></span>
                                        <a href="javascript:void(0);" class="cvh-flt-clearall" id="cvh_clear_all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                            Clear All
                                        </a>
                                    </div>
                                    <div class="cvh-flt-chips" id="cvh_active_chips"></div>
                                </div>
                                <!--end::Active filters-->

                                <!--begin::Skills-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Skills <span class="cvh-flt-badge-primary">Primary</span></div>
                                            <div class="cvh-flt-section-desc">Add skills to find relevant candidates — click ★ to make a skill mandatory</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <input class="form-control form-control-solid d-flex align-items-center"
                                            name="kt_tagify_users" value="{{ @$default_skill }}" placeholder=""
                                            id="kt_tagify_users" />
                                        <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
                                        <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
                                        <script>
                                            const inputElm = document.querySelector('#kt_tagify_users');

                                            const tagify = new Tagify(inputElm, {
                                                templates: {
                                                    tag: function(tagData) {
                                                        tagData = tagData || {};
                                                        const value = tagData.value || '';
                                                        const isChecked = tagData.checked ? 'checked' : '';
                                                        const mandatoryClass = tagData.checked ? 'cvh-flt-mandatory-tag' : '';
                                                        const starGlyph = tagData.checked ? '★' : '☆';
                                                        const chipBg    = tagData.checked ? '#dbeafe' : '#eef2ff';
                                                        const chipBdr   = tagData.checked ? '#bfdbfe' : '#e0e7ff';
                                                        const starColor = tagData.checked ? '#eab308' : '#94a3b8';
                                                        const textColor = tagData.checked ? '#1d4ed8' : '#3730a3';
                                                        return `
                                                    <tag title="${value}" contenteditable='false' spellcheck="false"
                                                        class="tagify__tag ${mandatoryClass} ${tagData.class ? tagData.class : ""}"
                                                        ${this.getAttributes(tagData)}
                                                        style="display:inline-flex;align-items:center;gap:6px;background:${chipBg};border:1px solid ${chipBdr};border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                                                        <span class="star ${isChecked}" style="cursor:pointer;font-size:15px;line-height:1;color:${starColor};flex-shrink:0;margin: 1px 3px 0px 3px;">${starGlyph}</span>
                                                        <span class="tagify__tag-text" style="cursor:pointer;font-size:11.5px;font-weight:500;color:${textColor};line-height:1;white-space:nowrap;padding: 0px 0px 0px 6px;">${value}</span>
                                                        <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                                                            style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
                                                    </tag>
                                                `;
                                                    }
                                                },
                                                transformTag: function(tagData) {
                                                    tagData.checked = tagData.checked || false;
                                                    return tagData;
                                                }
                                            });

                                            // Toggle mandatory (star) on click of EITHER the star itself
                                            // OR the keyword text next to it.
                                            document.addEventListener('click', function(e) {
                                                const toggleTarget = e.target.closest('.star, .tagify__tag-text');
                                                if (toggleTarget) {
                                                    const tagElm = toggleTarget.closest('.tagify__tag');
                                                    if (!tagElm) return;
                                                    const starEl = tagElm.querySelector('.star');
                                                    const textEl = tagElm.querySelector('.tagify__tag-text');
                                                    if (!starEl || !textEl) return;
                                                    const value = textEl.textContent;

                                                    starEl.classList.toggle('checked');
                                                    const nowChecked = starEl.classList.contains('checked');

                                                    starEl.textContent = nowChecked ? '★' : '☆';
                                                    starEl.style.color = nowChecked ? '#eab308' : '#94a3b8';
                                                    tagElm.style.background = nowChecked ? '#dbeafe' : '#eef2ff';
                                                    tagElm.style.borderColor = nowChecked ? '#bfdbfe' : '#e0e7ff';
                                                    textEl.style.color = nowChecked ? '#1d4ed8' : '#3730a3';
                                                    tagElm.classList.toggle('cvh-flt-mandatory-tag', nowChecked);

                                                    const tagData = tagify.value.find(tag => tag.value === value);
                                                    if (tagData) {
                                                        setTimeout(() => {
                                                            tagData.checked = nowChecked;
                                                            tagify.updateValueByDOMTags();
                                                            updateCvActiveFiltersUI();
                                                        }, 500);
                                                    }
                                                }
                                            });

                                            function debounceskill(func, delay) {
                                                let timeoutskill;
                                                return function(...args) {
                                                    clearTimeout(timeoutskill);
                                                    timeoutskill = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            const fetchSuggestionsSkills = debounceskill(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            tagify.settings.whitelist = data;
                                                            tagify.dropdown.show.call(tagify, query);
                                                        } else {
                                                            console.error("Invalid API response format: Expected array.");
                                                        }
                                                    },
                                                    error: function(err) {
                                                        console.error("Error fetching Skill:", err);
                                                    }
                                                });
                                            }, 200);

                                            tagify.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 1) return;
                                                fetchSuggestionsSkills(query);
                                            });
                                            tagify.on('add', updateCvActiveFiltersUI);
                                            tagify.on('remove', updateCvActiveFiltersUI);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Skills-->

                                <!--begin::Location-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Location</div>
                                            <div class="cvh-flt-section-desc">Preferred cities or work locations</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <input class="form-control form-control-solid"
                                            value="" id="kt_tagify_location"
                                            placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
                                        <script>
                                            var inputloc = document.querySelector("#kt_tagify_location");
                                            var cities = @json($cities);
                                            var tagifylocation = new Tagify(inputloc, {
                                                whitelist: [],
                                                maxTags: 10,
                                                templates: {
                                                    tag: function(tagData) {
                                                        const value = tagData.value || '';
                                                        return `
                                                    <tag title="${value}" contenteditable='false' spellcheck="false"
                                                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                                                        ${this.getAttributes(tagData)}
                                                        style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #e0e7ff;border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                                                        <span class="tagify__tag-text" style="font-size:11.5px;font-weight:500;color:#3730a3;line-height:1;white-space:nowrap;">${value}</span>
                                                        <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                                                            style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
                                                    </tag>
                                                `;
                                                    }
                                                },
                                                dropdown: {
                                                    maxItems: 20,
                                                    classname: "",
                                                    enabled: 0,
                                                    closeOnSelect: true,
                                                    /* appendTarget prevents the dropdown from being
                                                       clipped by .cvh-flt-body's overflow:hidden/auto —
                                                       see the matching z-index rule on
                                                       body > .tagify__dropdown above. */
                                                    appendTarget: document.body
                                                }
                                            });
                                            tagifylocation.on('dropdown:select', function(e) {
                                                tagifylocation.addTags([e.detail.data.value]);
                                            });
                                            tagifylocation.on('add', updateCvActiveFiltersUI);
                                            tagifylocation.on('remove', updateCvActiveFiltersUI);

                                            function debounce(func, delay) {
                                                let timer;
                                                return function (...args) {
                                                    clearTimeout(timer);
                                                    timer = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            const fetchSuggestions = debounce(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            tagifylocation.settings.whitelist = data;
                                                            tagifylocation.dropdown.show.call(tagifylocation, query);
                                                        } else {
                                                            console.error("Invalid API response format: Expected array.");
                                                        }
                                                    },
                                                    error: function(err) {
                                                        console.error("Error fetching cities:", err);
                                                    }
                                                });
                                            }, 200);

                                            tagifylocation.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 2) return;
                                                fetchSuggestions(query);
                                            });
                                        </script>
                                    </div>
                                </div>
                                <!--end::Location-->

                                <!--begin::Education-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Education</div>
                                            <div class="cvh-flt-section-desc">Highest qualification</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <input type="hidden" id="kt_tagify_education" value="" />
                                        <div class="cvh-flt-select-dd" id="cvh_edu_dd">
                                            <button type="button" class="cvh-flt-select-dd-btn" id="cvh_edu_dd_btn">
                                                <span id="cvh_edu_dd_label">Any Education</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div class="cvh-flt-select-dd-panel d-none" id="cvh_edu_dd_panel">
                                                <label class="cvh-flt-select-dd-item">
                                                    <input type="checkbox" class="cvh-flt-edu-check" value="" checked>
                                                    <span>Any</span>
                                                </label>
                                                @foreach ($qualificationTag as $qval)
                                                    <label class="cvh-flt-select-dd-item">
                                                        <input type="checkbox" class="cvh-flt-edu-check" value="{{ $qval }}">
                                                        <span class="cvh-flt-edu-name">{{ $qval }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <script>
                                            // Drives the same #kt_tagify_education hidden field the backend
                                            // already reads (JSON array of {value:"..."} objects, matched
                                            // against qualification_master.name) — checklist dropdown,
                                            // multi-select, no job-scoped default (this page isn't job-scoped).
                                            var cvhEducationSelected = [];

                                            function cvhSyncEducationInput() {
                                                var payload = cvhEducationSelected.map(function(v) { return { value: v }; });
                                                $('#kt_tagify_education').val(JSON.stringify(payload));
                                                $('#cvh_edu_dd_label').text(cvhEducationSelected.length ? cvhEducationSelected.join(', ') : 'Any Education');
                                                updateCvActiveFiltersUI();
                                            }

                                            $(document).on('change', '.cvh-flt-edu-check', function() {
                                                var val = $(this).val();
                                                if (val === '') {
                                                    if ($(this).is(':checked')) {
                                                        $('.cvh-flt-edu-check').not(this).prop('checked', false);
                                                        cvhEducationSelected = [];
                                                    }
                                                } else {
                                                    $('.cvh-flt-edu-check[value=""]').prop('checked', false);
                                                    if ($(this).is(':checked')) {
                                                        if (cvhEducationSelected.indexOf(val) === -1) cvhEducationSelected.push(val);
                                                    } else {
                                                        cvhEducationSelected = cvhEducationSelected.filter(function(v) { return v !== val; });
                                                        if (cvhEducationSelected.length === 0) $('.cvh-flt-edu-check[value=""]').prop('checked', true);
                                                    }
                                                }
                                                cvhSyncEducationInput();
                                            });

                                            $(document).on('click', '#cvh_edu_dd_btn', function() {
                                                $('#cvh_edu_dd_panel').toggleClass('d-none');
                                            });
                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#cvh_edu_dd').length) {
                                                    $('#cvh_edu_dd_panel').addClass('d-none');
                                                }
                                            });

                                            $(document).ready(cvhSyncEducationInput);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Education-->

                                <!--begin::Experience-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Experience (Years)</div>
                                            <div class="cvh-flt-section-desc">Total work experience</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="cvh-flt-mini-label">Minimum Experience</label>
                                                <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                                    <option value="" selected="">Select Experience</option>
                                                    <option value="0">Freshers</option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}">{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                            <div class="col-6">
                                                <label class="cvh-flt-mini-label">Maximum Experience</label>
                                                <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                                    <option value="" selected="">Select Experience</option>
                                                    <option value="0">Freshers</option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}">{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container" id="experience_error" style="color:red !important;text-align:center;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Experience-->

                                <!--begin::Salary-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-amber">
                                            <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Salary (CTC)</div>
                                            <div class="cvh-flt-section-desc">Salary range with currency &amp; mode</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <div class="row g-2 cvh-flt-salary-row">
                                            <div class="col-3">
                                                <label class="cvh-flt-mini-label">Currency</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency">
                                                    <option value="">Select currency</option>
                                                    <option value="0">INR</option>
                                                    <option value="1">EUR</option>
                                                    <option value="2">JPY</option>
                                                    <option value="3">GBP</option>
                                                    <option value="4">USD</option>
                                                    <option value="5">AED</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="cvh-flt-mini-label">Mode</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                    <option value="">Select Mode</option>
                                                    <option value="0">Annual</option>
                                                    <option value="1">Monthly</option>
                                                    <option value="2">Hourly</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="cvh-flt-mini-label">Min Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Enter min salary" value="">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted minimum_salary_word"></span>
                                            </div>
                                            <div class="col-3">
                                                <label class="cvh-flt-mini-label">Max Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Enter max salary" value="">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted maximum_salary_word"></span>
                                            </div>
                                            <span class="form-text text-muted" id="salary_error" style="color:red !important;text-align:center;"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Salary-->

                                <!--begin::Notice Period-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-rose">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Notice Period / Availability</div>
                                            <div class="cvh-flt-section-desc">Time to join</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <div class="cvh-flt-pill-row" data-radio-group="app_int_notice">
                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="1" id="app_int_notice1" checked>
                                            <label class="cvh-flt-pill" for="app_int_notice1">Any</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="8" id="app_int_notice8">
                                            <label class="cvh-flt-pill" for="app_int_notice8">Immediate</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="2" id="app_int_notice2">
                                            <label class="cvh-flt-pill" for="app_int_notice2">0-15 Days</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="3" id="app_int_notice3">
                                            <label class="cvh-flt-pill" for="app_int_notice3">1 Month</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="4" id="app_int_notice4">
                                            <label class="cvh-flt-pill" for="app_int_notice4">2 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="5" id="app_int_notice5">
                                            <label class="cvh-flt-pill" for="app_int_notice5">3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="6" id="app_int_notice6">
                                            <label class="cvh-flt-pill" for="app_int_notice6">More than 3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="9" id="app_int_notice9">
                                            <label class="cvh-flt-pill" for="app_int_notice9">Served Notice Period</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="7" id="app_int_notice7">
                                            <label class="cvh-flt-pill" for="app_int_notice7">Currently Serving Notice</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Notice Period-->

                                <!--begin::Stages-->
                                <div class="cvh-flt-section">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-pink">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Stages</div>
                                            <div class="cvh-flt-section-desc">Current stage in the pipeline</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <div class="cvh-flt-pill-row" data-radio-group="app_int_status">
                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="0" id="app_int_status0" checked>
                                            <label class="cvh-flt-pill" for="app_int_status0">All</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="8" id="app_int_status8">
                                            <label class="cvh-flt-pill" for="app_int_status8">Inreview</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="1" id="app_int_status1">
                                            <label class="cvh-flt-pill" for="app_int_status1">Sourcing</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="2" id="app_int_status2">
                                            <label class="cvh-flt-pill" for="app_int_status2">Screening</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="3" id="app_int_status3">
                                            <label class="cvh-flt-pill" for="app_int_status3">Evaluation</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="5" id="app_int_status5">
                                            <label class="cvh-flt-pill" for="app_int_status5">Offer</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="6" id="app_int_status6">
                                            <label class="cvh-flt-pill" for="app_int_status6">Joined</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="4" id="app_int_status4">
                                            <label class="cvh-flt-pill" for="app_int_status4">Negotiation</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="7" id="app_int_status7">
                                            <label class="cvh-flt-pill" for="app_int_status7">Rejected</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Stages-->

                                <!--begin::Activity-->
                                <div class="cvh-flt-section cvh-flt-section-last">
                                    <div class="cvh-flt-section-head">
                                        <span class="cvh-flt-icon cvh-flt-icon-sky">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                                        </span>
                                        <div>
                                            <div class="cvh-flt-section-title">Activity</div>
                                            <div class="cvh-flt-section-desc">Last active on JeroATS</div>
                                        </div>
                                    </div>
                                    <div class="cvh-flt-section-body">
                                        <div class="cvh-flt-pill-row" data-radio-group="app_int_activity">
                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                                            <label class="cvh-flt-pill" for="app_int_activity1">All</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                            <label class="cvh-flt-pill" for="app_int_activity2">30 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                            <label class="cvh-flt-pill" for="app_int_activity3">60 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                            <label class="cvh-flt-pill" for="app_int_activity4">90 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                            <label class="cvh-flt-pill" for="app_int_activity5">180 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                            <label class="cvh-flt-pill" for="app_int_activity6">1 Year</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Activity-->

                            </form>
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="cvh-flt-footer">
                            <div class="cvh-flt-footer-count">
                                <span class="cvh-flt-footer-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </span>
                                <div>
                                    <div class="cvh-flt-footer-number" id="cvh_footer_number">0</div>
                                    <div class="cvh-flt-footer-label">Candidates Match</div>
                                </div>
                            </div>
                            <div class="cvh-flt-footer-actions">
                                <button type="button" class="btn btn-sm btn-light" id="cvh_close_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    Close
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="cvh_reset_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                    Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="cvh_apply_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
                <!--End Filter Drawer-->

                {{-- ── Stage Tabs (separate card, ES-backed counts) — sits directly above
                     the candidate list, below the search/filter bar. ── --}}
                @php
                    // Tab key => the ID(s) sent to the backend as `stage_tab` (comma-separated
                    // for "Rejected", which spans two master_status rows both named "Rejected"),
                    // the matching key in $candidateStats['tabs'] for the live badge count, an
                    // SVG icon path (inline, not an icon-font class — Keenicons is no longer
                    // mandatory per docs/ENTERPRISE_PLAYBOOK.md §4.2), and the stage's solid
                    // color — taken from the app's own "Pipeline Stages" legend
                    // (resources/views/sections/service_company/pipeline.blade.php) so this
                    // tab bar matches that legend exactly rather than inventing new colors.
                    $cvStageTabs = [
                        ['stage'=>'all', 'label'=>'All Candidates', 'countKey'=>'total', 'color'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['stage'=>'8',   'label'=>'InReview',       'countKey'=>'inreview', 'color'=>'#bdbd26',
                         'icon'=>'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                        ['stage'=>'1',   'label'=>'Sourcing',       'countKey'=>'sourcing', 'color'=>'#7e8299',
                         'icon'=>'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
                        ['stage'=>'2',   'label'=>'Screening',      'countKey'=>'screening', 'color'=>'#c000ff',
                         'icon'=>'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
                        ['stage'=>'3',   'label'=>'Evaluation',     'countKey'=>'evaluation', 'color'=>'#2896ff',
                         'icon'=>'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
                        ['stage'=>'4',   'label'=>'Negotiation',    'countKey'=>'negotiation', 'color'=>'#ff4098',
                         'icon'=>'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
                        ['stage'=>'5',   'label'=>'Offered',        'countKey'=>'offered', 'color'=>'#ffab00',
                         'icon'=>'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
                        ['stage'=>'6',   'label'=>'Joined',         'countKey'=>'joined', 'color'=>'#06c732',
                         'icon'=>'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4 4 0 0 0-7.533-2.348M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25-3 3-1.5-1.5'],
                        ['stage'=>'7,9', 'label'=>'Rejected',       'countKey'=>'rejected', 'color'=>'#ff0202',
                         'icon'=>'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ];
                @endphp
                <div class="card cvh-tabs-card" id="cvh_tabs_card">
                    <div class="card-body">
                        <div class="cvh-tabs">
                            @foreach($cvStageTabs as $i => $tab)
                                @php
                                    $tabCount = $tab['countKey'] === 'total'
                                        ? ($candidateStats['total'] ?? 0)
                                        : ($candidateStats['tabs'][$tab['countKey']] ?? 0);
                                @endphp
                                <button type="button" class="cvh-tab stage-{{ $tab['countKey'] }} {{ $i === 0 ? 'active' : '' }}" data-stage="{{ $tab['stage'] }}" data-color="{{ $tab['color'] }}">
                                    <span class="cvh-tab-top">
                                        <span class="cvh-tab-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $tab['icon'] }}"/></svg>
                                        </span>
                                        <span class="cvh-tab-label">{{ $tab['label'] }}</span>
                                    </span>
                                    <span class="cvh-tab-count" data-cvh-tab="{{ $tab['countKey'] }}">{{ number_format($tabCount) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ── Row selection action bar — hidden until candidates are checked.
                     Reuses the exact same trigger classes/data-target attributes as the
                     legacy toolbar buttons (mass_mail / fwd_resume / application_copy /
                     exportRecord), so every existing modal + ajax handler works unchanged;
                     only the presentation is new. ── --}}
                <div class="cvh-selection-bar" id="cvh_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="cvh_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="cvh-selection-count"><span id="cvh_selected_count">0</span> candidates selected</span>
                    <div class="cvh-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" data-toggle="modal" data-target="#mass_mail">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary fwd_resume" data-toggle="modal" data-target="#fwd_resume">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/><path d="m9 14 3 3 3-3"/><path d="M12 17v-6"/></svg>
                            </span> Forward Resume
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary application_copy" data-toggle="modal" data-target="#application_copy">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                            </span> Copy
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary exportRecord" title="Export the selected candidates using your browser's print dialog">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            </span> Export
                        </a>
                    </div>
                    <button type="button" class="cvh-selection-close" id="cvh_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="card">
                    <div class="card-body pt-3 px-5" style="position: relative">
                        @if (in_array('view_job_applications', $userPermissions))
                           {{-- Circle loader only (Bootstrap/Metronic's own spinner-border) — no text
                                label. position:fixed + viewport-centered (not absolute/inset:0 relative
                                to .card-body): the card body's height grows with the candidate list, so
                                centering "within the card" could place the spinner well below the
                                visible viewport on a long list or when scrolled. --}}
                           <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                               <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                           </div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                                id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input datatable_chk chk_list" type="checkbox"
                                                    data-kt-check="true" data-kt-check-target="#myTable .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Candidate Name</th>
                                        {{-- <th class="">Contacts</th> --}}
                                        <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="" data-toggle="tooltip" title="Notice Period">Notice</th>
                                        <th class="">Location</th>
                                        <th class="">Ownership</th>
                                        {{-- <th class="" data-toggle="tooltip" title="Upload Date">Date</th> --}}
                                        <th class="">Jobs</th>
                                        <th class="">Status</th>
                                        <th class="">Stage</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="candidatetList">
                                </tbody>
                            </table>
                            <div id="paginationcandidate"></div>
                            {{-- Print target — populated client-side from the checked rows already
                                 on this page (see the .exportRecord click handler); nothing here
                                 until the user clicks Print, no server round-trip involved. --}}
                            <div id="cvh_print_area"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
      #csd_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
      #csd_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
      #csd_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
      #csd_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
      #csd_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
      #csd_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
      #csd_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
      #csd_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
      #csd_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 8px; padding: 9px 20px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: filter .15s; }
      #csd_drawer .sd-btn-confirm:hover { filter: brightness(0.94); }
      #csd_drawer .sd-btn-confirm:disabled { background: #c2c9d6 !important; cursor: not-allowed; }
      #csd_drawer .sd-btn-confirm .indicator-progress { display: none; }
      #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e4e6ef; }
      #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
      #csd_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
    </style>
    <div id="csd_drawer_toggle" style="display:none;"></div>
    <div id="csd_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="candidate-stage"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#csd_drawer_toggle" data-kt-drawer-close="#csd_drawer_close">
      <div class="d-flex flex-column h-100 w-100">
        <div class="sd-topbar">
          <div class="sd-topbar-left">
            <span class="sd-topbar-ico"><i class="fa fa-random"></i></span>
            <div>
              <div class="sd-topbar-title">Change Candidate Stage</div>
              <div class="sd-topbar-sub" id="csd_drawer_subtitle">&nbsp;</div>
            </div>
          </div>
          <div class="sd-topbar-right">
            <button type="button" class="sd-btn-cancel" id="stage_sub_stage_close">Cancel</button>
            <button type="button" class="sd-btn-confirm" id="custom_stage_submit" style="display:none;">
              <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>
              <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <button type="button" class="sd-btn-confirm" id="int_stage_submit" style="display:none;">
              <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>
              <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <span class="sd-divider"></span>
            <span class="sd-close" id="csd_drawer_close"><i class="fa fa-times"></i></span>
          </div>
        </div>
        <div class="flex-grow-1 px-6 py-4" style="overflow-y:auto;">
          <div class="stage_body_content" id="stage_body_content"></div>
          <div class="client_stage_body_content" id="client_stage_body_content"></div>
        </div>
      </div>
    </div>


    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Select the job checkbox, then click "Save" to add the candidate</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">

                    <div class="">
                        <button type="button" id="submit-frm-btn" class="btn btn-primary copy_current_selected_applicant d-none"> 
                            <span class="indicator-label">Save to Jobs</span> 
                            <span class="indicator-progress">
                                Please wait...
                            </span> 
                        </button>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-danger me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>


    <!--begin::Fwd Resume - Modal-->
    <div class="modal fade" id="fwd_resume" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Forward Resume</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_fwd_resume_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Fwd Resume - Modal-->

    <!--begin::Mass Mail - Modal-->
    <div class="modal fade" id="mass_mail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Send Email</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_mass_mail_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Mass Mail - Modal-->



    <div id="kt_activitiesBA_toggle"></div>
    <div id="kt_activitiesBA" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBA_toggle" data-kt-drawer-close="#kt_activitiesBA_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Relationship Manager Listing</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesBA_body"> </div>
        </div>
    </div>


@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

   

    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
        }

        $("#kt_daterangepicker").daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);

        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
                $('.custom_hide').addClass("d-none");
            } else {
                $('.chk_list_item').prop('checked', false);	
                $('.custom_hide').removeClass("d-none");
            }
        });
    </script>

    <script>
        $(document).ready(function() {




          // Replaces the old Excel/CSV export — downloads a single combined,
          // Replaces the old server-side PDF download — purely client-side:
          // clones the checked rows' already-rendered markup (no new
          // request, no server load) into #cvh_print_area and hands off to
          // the browser's own print dialog (Save as PDF works the same way
          // from there, at the user's discretion, with zero server cost).
          $(document).on('click', '.exportRecord', function() {

                var $checked = $('.chk_list_item:checked');
                if ($checked.length === 0) {
                    cvhToast.fire({ icon: 'warning', title: 'Select at least one candidate first' });
                    return false;
                }

                var $table = $('<table></table>');
                var $thead = $('#myTable thead tr').clone();
                $thead.find('th').first().remove(); // drop the checkbox header column
                $table.append($('<thead></thead>').append($thead));

                var $tbody = $('<tbody></tbody>');
                $checked.each(function() {
                    var $row = $(this).closest('tr').clone();
                    $row.find('td').first().remove(); // drop the checkbox cell
                    $tbody.append($row);
                });
                $table.append($tbody);

                var $printArea = $('#cvh_print_area').empty();
                $printArea.append('<h2>Candidates Summary</h2>');
                $printArea.append('<div class="cvh-print-meta">Printed ' +
                    new Date().toLocaleString() + ' — ' + $checked.length + ' candidate(s)</div>');
                $printArea.append($table);

                window.print();
            });

            $('#inputSearch').keyup(function() {
                // tableLoad('filter');
            });


            $(document).on('click', '.fwd_resume', function(e) {

                e.preventDefault();
                // localStorage.setItem('toggleOpen', 'true');
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id = jobIdArr[0];
                var url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";


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
                            $('#application_fwd_resume_body_content').html(response.view);
                        }
                    }
                });

            });


            $(document).on('click', '.mass_mail', function(e) {

                e.preventDefault();
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id = jobIdArr[0];
                var url = "{{ route('service_company.cvtracker.mass_mail', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";


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
                            $('#application_mass_mail_body_content').html(response.view);
                        }
                    }
                });

            });



        });

        var table;
        
        // Free-text search box: Enter re-runs the same fetchCandidates() call the
        // sort dropdown and pagination already use — no separate endpoint, and no
        // auto-apply on every keystroke/blur (previously fired fetchCandidates() on
        // every keyup, and again onchange/blur — both removed in favor of this
        // single Enter-triggered handler, matching the rest of the redesigned pages).
        $(document).on('keydown', '#kt_tagify_2', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                fetchCandidates();
            }
        });

        function filtersdataget() {
            // Used by the sort dropdown's onchange — the filter drawer itself has
            // its own Apply/Reset buttons that call fetchCandidates() directly,
            // this just covers the sort-only auto-apply case.
            fetchCandidates();
        }

        function tableLoad(type) {

            var status = 'all';
            var jobs = 'all';
            var clients = 'all';
            var source = 'all';
            var search_passport = 'all';
            var search_np_id = 'all';
            var inputSearch = $('#inputSearch').val();
            var kt_daterangepicker = $('#kt_daterangepicker').text();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            // alert(kt_daterangepicker);
            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',
                //  ajax			: '{!! route('service_company.cvtracker.alldata') !!}?search='+inputSearch,
                ajax: {
                    'url': '{!! route('service_company.cvtracker.alldata') !!}',
                    "data": function(d) {
                        d.search = $('#inputSearch').val(),
                            d.sortingfilter = $('#sortingfilter').val(),
                            d.country = $('#country').val(),
                            d.cus_search = $('#kt_tagify_2').val()
                    }
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        width: '1%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        width: '15%',
                        orderable: true
                    },
                    {
                        data: 'Exp',
                        name: 'Exp',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'NoticePeriod',
                        name: 'NoticePeriod',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Qualifaction',
                        name: 'Qualifaction',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        width: '2%',
                        orderable: true
                    },
                    // { data: 'UploadDate', name: 'UploadDate', width: '10%', orderable: true },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        width: '2%',
                        orderable: true
                    }
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $(document).on('click', '.application_copy', function() {
            $('#application_copy_body_content').html('');
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            var wiseId = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $('.chk_list_item:checked').each(function() {
                wiseId.push($(this).data('wiseid'));
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'wiseId': wiseId
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
                                $("#kt_activitiesCopy_close").click();
                            });
                        return false;
                    }
                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#stage_sub_stage_close', function() {
            $('#csd_drawer_close').trigger('click');
        });

        $(document).on('click', '.client_stage_sub_stage', async function(e) {

            e.preventDefault();
            const connectedsubclient = await isInternetAvailable();
                if (!connectedsubclient) {
                    Swal.fire({
                        title: "No Internet",
                        text: "You are currently offline or the server is unreachable.",
                        icon: "warning",
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                    return; // ⛔ STOP EXECUTION
            }

            // This trigger is only ever active for a candidate already at
            // InReview(8) — see all_candidates.blade.php's row-level gating,
            // which inerts the .client_stage_sub_stage class entirely for
            // any other stage. The coin-spend confirmation ("Add to
            // In-Review!") + validateClientPackage check belonged to a
            // first-time submit-to-client action this trigger can no longer
            // reach (coins were already spent to get here), so it now opens
            // the drawer directly — matching what
            // apptracker/clientapp.blade.php's own equivalent trigger
            // already does.
            var app_id = $(this).data('row-id');

            $('#stage_body_content').html('').hide();
            $('#client_stage_body_content').html(
                '<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>'
            ).show();
            $('#custom_stage_submit').hide();
            $('#int_stage_submit').hide();
            $('#csd_drawer_toggle').trigger('click');

            var url = "{{ route('service_company.job-applications.client_stage_body_content', ':id') }}";
            url = url.replace(':id', app_id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#client_stage_body_content').html(response.view);
                        $('#csd_drawer_subtitle').text($(
                            '#client_stage_body_content .csd-info-name'
                        ).first().text());
                    }
                }
            });
        });

        $(document).on('click', '#int_stage_submit', function(e) {

            e.preventDefault();
            var master_status = $('input[name="master_status"]:checked').val();
            if ((master_status == 1) || (master_status == 7) || (master_status == 9)) {
                if ($('#main_substage').is(':visible') && $('input[name="sub_stages"]:checked').length === 0) {
                    Swal.fire("Warning", 'Please select a sub stage to continue.', "info");
                    return;
                }
                $.ajax({
                    url: '{{ route('service_company.job-applications.client_stage_submit') }}',
                    container: '#status_frm',
                    type: "POST",
                    data: $('#status_frm').serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success");
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // $.unblockUI();
                            // table._fnDraw();

                        } else if (response.status == "info") {
                            Swal.fire('Fail!', response.message, 'error')
                        }

                    }
                })
            } else {
                Swal.fire("Warning", 'Only Sourcing stage update!', "info");
            }

        });

        // Stage-pill click handling for the plain flow now lives in
        // stage_body_content.blade.php itself (csdSelectStage), the same way
        // Job Status's drawer keeps jsdSelectStatus with its own partial —
        // it's the single source of truth for all 5 pages that use it, so it
        // no longer needs a page-level .master_status_div handler here too.
        $(document).on('click', '.stage_sub_stage', async function(e) {
          e.preventDefault();
          const connectedsub = await isInternetAvailable();
              if (!connectedsub) {
                  Swal.fire({
                      title: "No Internet",
                      text: "You are currently offline or the server is unreachable.",
                      icon: "warning",
                      confirmButtonText: "Ok, got it!",
                      customClass: {
                          confirmButton: "btn btn-warning"
                      }
                  });
                  return; // ⛔ STOP EXECUTION
            }
            $('#client_stage_body_content').html('').hide();
            $('#stage_body_content').html(
                '<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>'
            ).show();
            $('#custom_stage_submit').show();
            $('#int_stage_submit').hide();
            $('#csd_drawer_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                        $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
                    }
                }
            });
        });

        $(document).on('click', '#custom_stage_submit', async function(e) {

            e.preventDefault();
            const connected = await isInternetAvailable();
              if (!connected) {
                  Swal.fire({
                      title: "No Internet",
                      text: "You are currently offline or the server is unreachable.",
                      icon: "warning",
                      confirmButtonText: "Ok, got it!",
                      customClass: {
                          confirmButton: "btn btn-warning"
                      }
                  });
                  return; // ⛔ STOP EXECUTION
            }
                  
            var master_status = $('input[name="master_status"]:checked').val();
            var comment = $('#comment').val();
            if (master_status == 8 && comment=='') {
                Swal.fire("Warning", 'InReview Comment Required !', "info");
            } else if ($('#main_substage').is(':visible') && $('input[name="sub_stages"]:checked').length === 0) {
                Swal.fire("Warning", 'Please select a sub stage to continue.', "info");
            } else {
                $("#custom_stage_submit").prop("disabled", true);
                $("#custom_stage_submit").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );
                $.ajax({
                    url: '{{ route('service_company.job-applications.stage_submit') }}',
                    container: '#status_frm',
                    type: "POST",
                    data: $('#status_frm').serialize(),
                    success: function(response) {
                        $("#custom_stage_submit").prop("disabled", false);
                        $("#custom_stage_submit").html(`<span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>`);
                        csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success")
                                .then(function() {
                                    // $('#stage_sub_stage').modal('hide'); 
                                    $("#stage_sub_stage_close").click();
                                });
                            // $('#myTable').DataTable().draw();
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // table._fnDraw();
                            load_interviewSchedule();
                        } else if (response.status == 'cvpending') {
                            Swal.fire("Warning", 'Resume pending this job application.', "warning")
                                .then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                        } else if (response.status == 'candidatevalidation') {
                            Swal.fire("Warning", response.message, "warning").then(function() {
                                $("#stage_sub_stage_close").click();
                            });
                        }
                    }
                })
            }

        });
    </script>

    <script>
        $(document).ready(function() {

          $(document).on('click', '.chk_list', function() {
           
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').prop('checked', true);
                } else {
                    $('.chk_list_item').prop('checked', false);	
                }
            });

            $(document).on('click', '.chk_list', function(e) {
                $('#application_fwd_resume_body_content').html('');

                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.fwd_resume').removeClass("d-none");
                    $('.custom_hide').addClass("d-none");
                    $('.mass_mail').removeClass("d-none");
                    $('.send_email').removeClass("d-none");
                    $('.downloadexcell').removeClass("d-none");
                    $('.app_copy').removeClass("d-none");
                    $('.application_copy').removeClass("d-none");

                } else {
                    $('.fwd_resume').addClass("d-none");
                    $('.send_email').addClass("d-none");
                    $('.mass_mail').addClass("d-none");
                    $('.app_copy').addClass("d-none");
                    $('.downloadexcell').addClass("d-none");
                    $('.application_copy').addClass("d-none");
                    $('.custom_hide').removeClass("d-none");
                }
                updateCvSelectionBar();
            });

            $(document).on('click', '.chk_list_item', function() {
                  
                $('#application_fwd_resume_body_content').html('');
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.fwd_resume').removeClass("d-none");
                        $('.mass_mail').removeClass("d-none");
                        $('.send_email').removeClass("d-none");
                        $('.custom_hide').addClass("d-none");
                        $('.downloadexcell').removeClass("d-none");
                        $('.app_copy').removeClass("d-none");
                        $('.application_copy').removeClass("d-none");
                    } else {
                        $('.fwd_resume').addClass("d-none");
                        $('.mass_mail').addClass("d-none");
                        $('.downloadexcell').addClass("d-none");
                        $('.send_email').addClass("d-none");
                        $('.app_copy').addClass("d-none");
                        $('.application_copy').addClass("d-none");
                        $('.custom_hide').removeClass("d-none");
                    }
                    updateCvSelectionBar();
                }, 500);

            });

            /* Selection bar: live count + clear-selection controls (checkbox + close X) */
            function updateCvSelectionBar() {
                var n = $('.chk_list_item:checked').length;
                $('#cvh_selected_count').text(n);
                $('#cvh_selection_bar').toggleClass('show', n > 0);
                $('#cvh_selection_master_chk').prop('checked', n > 0);
            }
            function clearCvSelection() {
                $('.chk_list, .chk_list_item').prop('checked', false);
                $('.fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
                $('.custom_hide').removeClass('d-none');
                updateCvSelectionBar();
            }
            $(document).on('click', '#cvh_selection_close', clearCvSelection);
            $(document).on('change', '#cvh_selection_master_chk', function() {
                if (!$(this).is(':checked')) clearCvSelection();
            });

            //  $.noConflict();
           
            fetchCandidates();
            // The pagination partial (ajax_pages/pagination.blade.php) renders
            // buttons with class .pagi-page/.pagi-arrow, not .page-link — this
            // handler was listening for a class that doesn't exist anywhere in
            // the actual markup, so clicking a page number did nothing at all.
            var cvhPaginationLoading = false;
            $(document).on('click', '#paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
                e.preventDefault();

                if (cvhPaginationLoading) return;
                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                cvhPaginationLoading = true;
                fetchCandidates(page, $('#search').val()).then(() => {
                    cvhPaginationLoading = false;
                }).catch(() => {
                    cvhPaginationLoading = false;
                });
            });

        });
        var page_no = localStorage.getItem('currentPage');
        if (!page_no) {
            page_no = 1;
            localStorage.setItem('currentPage', page_no);
        }
        var activeStageTab = 'all';
        function setCvTabsCardBorder(color) {
            $('#cvh_tabs_card').css('border-color', color || '#e4e6ef');
        }
        $(document).on('click', '.cvh-tab', function() {
            $('.cvh-tab').removeClass('active');
            $(this).addClass('active');
            activeStageTab = $(this).attr('data-stage');
            setCvTabsCardBorder($(this).attr('data-color'));
            fetchCandidates();
        });
        setCvTabsCardBorder($('.cvh-tab.active').attr('data-color'));
        function fetchCandidates(page = page_no, search = '') {
            var clientid = '1';

            $('.loadingMessage').css({ display: 'flex' });
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }

            // document.getElementById("loader_search").classList.remove("hidden");
            return $.ajax({
                url: `{!! route('service_company.cvtracker.allcandidates') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: search,
                    search : $('#inputSearch').val(),
                    sortingfilter : $('#sortingfilter').val(),
                    country : $('#country').val(),
                    cus_search : $('#kt_tagify_2').val(),
                    cus_search_skill : $('#kt_tagify_users').val(),
                    min_exp : $('#min_exp').val(),
                    max_exp : $('#max_exp').val(),
                    kt_tagify_location : $('#kt_tagify_location').val(),
                    min_sal : $('#min_sal').val(),
                    max_sal : $('#max_sal').val(),
                    min_salary_currency : $('#min_salary_currency').val(),
                    sal_mode : $('#sal_mode').val(),
                    kt_tagify_education : $('#kt_tagify_education').val(),
                    app_int_status : $('[name="app_int_status"]:checked').val(),
                    app_int_activity : $('[name="app_int_activity"]:checked').val(),
                    app_int_notice : $('[name="app_int_notice"]:checked').val(),
                    stage_tab : activeStageTab === 'all' ? '' : activeStageTab
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#total_record').html(response.total);
                    $('#candidatetList').html(response.html);
                    localStorage.setItem('currentPage', 1);
                    $('#paginationcandidate').html(response.pagination);
                    updateCvHeroStats(response.candidateStats);
                },
                error: function(xhr) {
                    // Never leave the loading overlay stuck or the list blank on
                    // a failed request — surface what went wrong instead.
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    console.error('allcandidates failed:', xhr.status, xhr.responseText);
                }
            });
        }

        /* Sync hero KPI cards + stage tab badges from the same ES aggregation the table fetch already returns */
        function updateCvHeroStats(stats) {
            if (!stats) return;
            document.querySelectorAll('[data-cvh]').forEach(function (el) {
                var k = el.getAttribute('data-cvh');
                if (k === 'this_week_delta') {
                    if (stats.this_week != null) el.textContent = stats.this_week.toLocaleString();
                } else if (stats[k] != null) {
                    el.textContent = stats[k].toLocaleString();
                }
            });
            document.querySelectorAll('[data-cvh-tab]').forEach(function (el) {
                var k = el.getAttribute('data-cvh-tab');
                if (k === 'total') {
                    if (stats.total != null) el.textContent = stats.total.toLocaleString();
                } else if (stats.tabs && stats.tabs[k] != null) {
                    el.textContent = stats.tabs[k].toLocaleString();
                }
            });
        }

       $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job Selected!', 'Selected');
            } else {
                showToastr('error', 'Job Removed!', 'Removed');
            }

            // Show/hide the bulk action button based on overall selection
            if (checkedCount > 0) {
                $('.copy_current_selected_applicant').removeClass("d-none");
            } else {
                $('.copy_current_selected_applicant').addClass("d-none");
            }
        });



    </script>
    <script>
        // Small top-right toast, auto-dismissing, pause-on-hover — SweetAlert2's own
        // documented toast mode. Used only for filter Apply/Clear feedback; every other
        // modal/confirmation dialog on this page (stage change, mass mail, etc.) is
        // untouched.
        const cvhToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Custom Sort dropdown. Reads options straight from the real #sortingfilter
        // <select> (single source of truth) and keeps that select's value in sync so
        // filtersdataget()/fetchCandidates() are unaffected — only the presentation of
        // the option list changes, since a native <select>'s open dropdown can't be
        // styled cross-browser.
        function cvhRenderSortPanel() {
            var $panel = $('#cvh_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="cvh-tb-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#cvh_sort_label').text(label);
                        cvhCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function cvhOpenSortPanel() {
            cvhRenderSortPanel();
            $('#cvh_sort_panel').removeClass('d-none');
            $('#cvh_sort_btn').addClass('open');
        }
        function cvhCloseSortPanel() {
            $('#cvh_sort_panel').addClass('d-none');
            $('#cvh_sort_btn').removeClass('open');
        }
        $(document).on('click', '#cvh_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#cvh_sort_panel').hasClass('d-none')) cvhOpenSortPanel();
            else cvhCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#cvh_sort_wrap').length) cvhCloseSortPanel();
        });
    </script>
    <script>
        /* Drawer width = 80% of the actual content area (i.e. 20% less than the full
           page width, excluding the vertical sidebar) — same sizing convention as the
           other redesigned filter drawers on this app. Recalculated on resize and right
           before the drawer opens, so it stays correct if the sidebar collapses/expands. */
        function cvhSetDrawerWidth() {
            var wrapper = document.getElementById('kt_wrapper');
            var contentWidth = wrapper ? wrapper.getBoundingClientRect().width : window.innerWidth;
            if (!contentWidth) contentWidth = window.innerWidth;
            var drawerWidth = Math.round(contentWidth * 0.8);
            var drawerEl = document.getElementById('kt_cvtracker_filter_drawer');
            if (drawerEl) {
                drawerEl.style.width = drawerWidth + 'px';
            }
        }
        $(document).ready(cvhSetDrawerWidth);
        $(window).on('resize', cvhSetDrawerWidth);
        $(document).on('click', '#kt_cvtracker_filter_toggle', cvhSetDrawerWidth);

        /* Metronic's base .drawer class ships its own (non-!important) "overflow: auto"
           rule, loaded after this page's inline <style> block — stylesheet-only
           overrides have proven unreliable here, so these are set as genuine inline
           !important styles via JS, guaranteeing only .cvh-flt-body scrolls and the
           header/active-filters/footer stay pinned. */
        function cvhEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_cvtracker_filter_drawer');
            var cardEl = document.getElementById('cvh_flt_card');
            var bodyEl = cardEl ? cardEl.querySelector('.cvh-flt-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.cvh-flt-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.cvh-flt-footer') : null;
            if (drawerEl) {
                drawerEl.style.setProperty('overflow', 'hidden', 'important');
                drawerEl.style.setProperty('position', 'fixed', 'important');
                drawerEl.style.setProperty('top', '0', 'important');
                drawerEl.style.setProperty('bottom', '0', 'important');
                drawerEl.style.setProperty('height', '100vh', 'important');
            }
            if (cardEl) {
                cardEl.style.setProperty('display', 'block', 'important');
                cardEl.style.setProperty('position', 'relative', 'important');
                cardEl.style.setProperty('overflow', 'hidden', 'important');
                cardEl.style.setProperty('height', '100vh', 'important');
                cardEl.style.setProperty('max-height', '100vh', 'important');
            }
            if (bodyEl) {
                var headerH = headerEl ? headerEl.offsetHeight : 0;
                var footerH = footerEl ? footerEl.offsetHeight : 0;
                var available = Math.max(window.innerHeight - headerH - footerH, 100);
                bodyEl.style.setProperty('position', 'absolute', 'important');
                bodyEl.style.setProperty('left', '0', 'important');
                bodyEl.style.setProperty('right', '0', 'important');
                bodyEl.style.setProperty('top', headerH + 'px', 'important');
                bodyEl.style.setProperty('height', available + 'px', 'important');
                bodyEl.style.setProperty('max-height', available + 'px', 'important');
                bodyEl.style.setProperty('overflow-y', 'auto', 'important');
                bodyEl.style.setProperty('overflow-x', 'hidden', 'important');
            }
            if (footerEl) {
                footerEl.style.setProperty('position', 'absolute', 'important');
                footerEl.style.setProperty('left', '0', 'important');
                footerEl.style.setProperty('right', '0', 'important');
                footerEl.style.setProperty('bottom', '0', 'important');
            }
        }
        $(document).ready(cvhEnforceDrawerLayout);
        $(window).on('resize', cvhEnforceDrawerLayout);
        $(document).on('click', '#kt_cvtracker_filter_toggle, #kt_cvtracker_filter_close', cvhEnforceDrawerLayout);

        /* ------------------------------------------------------------------
           Filter drawer glue: the Active Filters chip bar (inside the drawer,
           previewing DRAFT edits) and the Applied Filters summary card (on the
           main page, reflecting only the last-APPLIED state).
        ------------------------------------------------------------------ */

        // The min_exp/max_exp/min_sal/max_sal fields have no job-derived default on
        // this page (CV Tracker is company-wide, not scoped to a single job) — they
        // start genuinely blank. This baseline is still captured, matching the same
        // pattern used on the job-scoped filter drawers elsewhere in the app, so an
        // unchanged value never renders as a false "active filter" chip.
        var cvhDefaultRange = {
            minExp: $('#min_exp').val(), maxExp: $('#max_exp').val(),
            minSal: $('#min_sal').val(), maxSal: $('#max_sal').val()
        };

        function updateCvActiveFiltersUI() {
            var chips = [];

            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.value.forEach(function(tag) {
                    chips.push({ label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }
            cvhEducationSelected.slice().forEach(function(val) {
                chips.push({
                    label: val.toUpperCase(),
                    clear: function() {
                        $('.cvh-flt-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                    }
                });
            });

            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            var expChanged = (minExp !== '' && minExp !== cvhDefaultRange.minExp) || (maxExp !== '' && maxExp !== cvhDefaultRange.maxExp);
            if (expChanged) {
                chips.push({
                    label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(cvhDefaultRange.minExp); $('#max_exp').val(cvhDefaultRange.maxExp); }
                });
            }

            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var salChanged = (minSal !== '' && minSal !== cvhDefaultRange.minSal) || (maxSal !== '' && maxSal !== cvhDefaultRange.maxSal);
            if (salChanged) {
                chips.push({
                    label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(cvhDefaultRange.minSal); $('#max_sal').val(cvhDefaultRange.maxSal); }
                });
            }

            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                chips.push({
                    label: noticeEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                chips.push({
                    label: statusEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                chips.push({
                    label: activityEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }

            var $chips = $('#cvh_active_chips').empty();
            chips.forEach(function(c) {
                var $chip = $('<span class="cvh-flt-chip"></span>').toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').text('★ ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip.append($('<span class="cvh-flt-chip-remove">&times;</span>').on('click', function() {
                    c.clear();
                    updateCvActiveFiltersUI();
                }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#cvh_active_bar').toggleClass('d-none', n === 0);
            $('#cvh_active_count').text('(' + n + ')');
            $('#cvh_header_count').text(n);
            $('#cvh_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#cvh_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        $(document).on('change', '[name="app_int_notice"], [name="app_int_status"], [name="app_int_activity"]', function() {
            updateCvActiveFiltersUI();
            if (typeof cvhEnforceDrawerLayout === 'function') cvhEnforceDrawerLayout();
        });
        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', updateCvActiveFiltersUI);

        // Salary-in-words hints (min_sal/max_sal) — same helper set as
        // In-house Bench's / Bench Talent Requests' drawers.
        var cvhOnes = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var cvhTeens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        var cvhTens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        function cvhCapitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function cvhConvertToWords(num) {
            if (isNaN(num)) return '';
            if (num < 10) {
                return cvhOnes[num];
            } else if (num < 20) {
                return cvhTeens[num - 10];
            } else if (num < 100) {
                return cvhTens[Math.floor(num / 10)] + ' ' + cvhOnes[num % 10];
            } else if (num < 1000) {
                return cvhOnes[Math.floor(num / 100)] + ' hundred ' + cvhConvertToWords(num % 100);
            } else if (num < 100000) {
                return cvhConvertToWords(Math.floor(num / 1000)) + ' thousand ' + cvhConvertToWords(num % 1000);
            } else if (num < 10000000) {
                return cvhConvertToWords(Math.floor(num / 100000)) + ' lakh ' + cvhConvertToWords(num % 100000);
            } else if (num < 1000000000) {
                return cvhConvertToWords(Math.floor(num / 10000000)) + ' crore ' + cvhConvertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        $(document).on('keyup', '#min_sal', function() {
            var number = parseInt($('#min_sal').val());
            $('.minimum_salary_word').text(isNaN(number) ? '' : cvhCapitalizeFirstLetter(cvhConvertToWords(number)));
        });
        $(document).on('keyup', '#max_sal', function() {
            var number = parseInt($('#max_sal').val());
            $('.maximum_salary_word').text(isNaN(number) ? '' : cvhCapitalizeFirstLetter(cvhConvertToWords(number)));
        });

        /* Applied Filters summary card (main page, above the results table). Reads the
           exact same underlying sources as updateCvActiveFiltersUI() so the two never
           disagree, just grouped/colored by category. Rendered on Apply/Reset/initial
           load — not on every keystroke inside the drawer. */
        var CVH_APPLIED_VISIBLE_CAP = 8;
        var cvhAppliedExpanded = false;

        function cvhCategoryIcon(cat) {
            var paths = {
                skill: '<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>',
                location: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                education: '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/>',
                experience: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                notice: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
                status: '<circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/>',
                activity: '<path d="M3 3v18h18M7 14l4-4 3 3 5-6"/>'
            };
            if (cat === 'salary') {
                return '<span style="font-size:11px;font-weight:700;line-height:1;">₹</span>';
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' + (paths[cat] || '') + '</svg>';
        }

        function cvhBuildAppliedItems() {
            var items = [];
            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    items.push({
                        cat: 'skill', label: tag.value, mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.value.forEach(function(tag) {
                    items.push({ cat: 'location', label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }
            cvhEducationSelected.slice().forEach(function(val) {
                items.push({
                    cat: 'education', label: val.toUpperCase(),
                    clear: function() {
                        $('.cvh-flt-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                    }
                });
            });
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            var expChanged = (minExp !== '' && minExp !== cvhDefaultRange.minExp) || (maxExp !== '' && maxExp !== cvhDefaultRange.maxExp);
            if (expChanged) {
                items.push({
                    cat: 'experience', label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(cvhDefaultRange.minExp); $('#max_exp').val(cvhDefaultRange.maxExp); }
                });
            }
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var salChanged = (minSal !== '' && minSal !== cvhDefaultRange.minSal) || (maxSal !== '' && maxSal !== cvhDefaultRange.maxSal);
            if (salChanged) {
                items.push({
                    cat: 'salary', label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(cvhDefaultRange.minSal); $('#max_sal').val(cvhDefaultRange.maxSal); }
                });
            }
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                items.push({
                    cat: 'notice', label: noticeEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                items.push({
                    cat: 'status', label: statusEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                items.push({
                    cat: 'activity', label: activityEl.next('.cvh-flt-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }
            return items;
        }

        // Removing a chip from this card acts on already-APPLIED filters, so (unlike
        // drawer edits) it re-fetches immediately instead of waiting for Apply, and
        // refreshes the drawer's own snapshot so reopening it shows the same picture.
        function cvhAppliedChipRemoved() {
            fetchCandidates();
            if (typeof updateCvActiveFiltersUI === 'function') updateCvActiveFiltersUI();
            if (typeof cvhCaptureSnapshot === 'function') cvhCaptureSnapshot();
            cvhRenderAppliedFiltersCard();
        }

        function cvhRenderAppliedFiltersCard() {
            var items = cvhBuildAppliedItems();
            var $chips = $('#cvh_applied_chips').empty();
            var visible = cvhAppliedExpanded ? items : items.slice(0, CVH_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="cvh-af-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + cvhCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').text('★'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="cvh-af-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        cvhAppliedChipRemoved();
                    })
                );
                $chips.append($chip);
            });

            if (!cvhAppliedExpanded && items.length > CVH_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - CVH_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="cvh-af-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        cvhAppliedExpanded = true;
                        cvhRenderAppliedFiltersCard();
                    })
                );
            }

            $('#cvh_applied_card').toggleClass('d-none', items.length === 0);
            $('#cvh_applied_count').text(items.length);
        }

        $(document).on('click', '#cvh_applied_clear_all', function() {
            $('#cvh_clear_all').trigger('click');
            fetchCandidates();
            if (typeof cvhCaptureSnapshot === 'function') cvhCaptureSnapshot();
            cvhAppliedExpanded = false;
            cvhRenderAppliedFiltersCard();
            cvhToast.fire({ icon: 'success', title: 'Filters cleared' });
        });

        $(document).on('click', '#cvh_clear_all', function() {
            if (typeof tagify !== 'undefined') tagify.removeAllTags();
            if (typeof tagifylocation !== 'undefined') tagifylocation.removeAllTags();
            cvhEducationSelected = [];
            $('.cvh-flt-edu-check').prop('checked', false);
            $('.cvh-flt-edu-check[value=""]').prop('checked', true);
            cvhSyncEducationInput();
            $('#min_exp, #max_exp, #min_sal, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#app_int_notice1, #app_int_status0, #app_int_activity1').prop('checked', true);
            updateCvActiveFiltersUI();
        });

        $(document).on('click', '#cvh_reset_btn', function() {
            $('#cvh_clear_all').trigger('click');
            fetchCandidates();
            // Reset is an explicit apply of the cleared state, so make the cleared
            // filters the new baseline — otherwise closing the drawer afterwards would
            // "restore" the pre-reset values.
            cvhCaptureSnapshot();
            cvhAppliedExpanded = false;
            cvhRenderAppliedFiltersCard();
        });

        /* ------------------------------------------------------------------
           Draft-until-Apply behaviour. Nothing the user changes inside the drawer
           takes effect until they click "Apply Filters". If they close the drawer by
           any other means (Close button, header X, overlay, ESC) their unsaved edits
           are discarded and every field reverts to the last-applied state.
        ------------------------------------------------------------------ */
        var cvhSnapshot = null;
        var cvhApplying = false;

        function cvhCaptureSnapshot() {
            cvhSnapshot = {
                skills: (typeof tagify !== 'undefined') ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                locations: (typeof tagifylocation !== 'undefined') ? tagifylocation.value.map(function(t) { return t.value; }) : [],
                education: cvhEducationSelected.slice(),
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val(),
                notice: $('[name="app_int_notice"]:checked').val(),
                status: $('[name="app_int_status"]:checked').val(),
                activity: $('[name="app_int_activity"]:checked').val()
            };
        }

        function cvhRestoreSnapshot() {
            if (!cvhSnapshot) return;
            if (typeof tagify !== 'undefined') {
                tagify.removeAllTags();
                if (cvhSnapshot.skills.length) tagify.addTags(cvhSnapshot.skills);
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.removeAllTags();
                if (cvhSnapshot.locations.length) tagifylocation.addTags(cvhSnapshot.locations);
            }
            cvhEducationSelected = cvhSnapshot.education.slice();
            $('.cvh-flt-edu-check').prop('checked', false);
            if (cvhEducationSelected.length === 0) {
                $('.cvh-flt-edu-check[value=""]').prop('checked', true);
            } else {
                cvhEducationSelected.forEach(function(v) {
                    $('.cvh-flt-edu-check[value="' + String(v).replace(/"/g, '\\"') + '"]').prop('checked', true);
                });
            }
            cvhSyncEducationInput();
            $('#min_exp').val(cvhSnapshot.min_exp);
            $('#max_exp').val(cvhSnapshot.max_exp);
            $('#min_sal').val(cvhSnapshot.min_sal);
            $('#max_sal').val(cvhSnapshot.max_sal);
            $('#min_salary_currency').val(cvhSnapshot.min_salary_currency);
            $('#sal_mode').val(cvhSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            if (cvhSnapshot.notice) $('[name="app_int_notice"][value="' + cvhSnapshot.notice + '"]').prop('checked', true);
            if (cvhSnapshot.status) $('[name="app_int_status"][value="' + cvhSnapshot.status + '"]').prop('checked', true);
            if (cvhSnapshot.activity) $('[name="app_int_activity"][value="' + cvhSnapshot.activity + '"]').prop('checked', true);
            updateCvActiveFiltersUI();
        }

        function cvhBindDrawerLifecycle() {
            var el = document.getElementById('kt_cvtracker_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(cvhBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst) { return setTimeout(cvhBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                cvhApplying = false;
                cvhCaptureSnapshot();
                cvhEnforceDrawerLayout();
                setTimeout(cvhEnforceDrawerLayout, 50);
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!cvhApplying) cvhRestoreSnapshot();
                cvhApplying = false;
            });
        }
        $(document).ready(cvhBindDrawerLifecycle);

        // Close discards unsaved edits (handled by the restore-on-hide hook above);
        // Apply commits them: run the fetch, mark this close as an intentional apply
        // so the hook does NOT revert, then close the drawer.
        $(document).on('click', '#cvh_close_btn', function() {
            $('#kt_cvtracker_filter_close').trigger('click');
        });
        $(document).on('click', '#cvh_apply_btn', function() {
            cvhApplying = true;
            fetchCandidates();
            cvhAppliedExpanded = false;
            cvhRenderAppliedFiltersCard();
            cvhToast.fire({ icon: 'success', title: 'Filters applied' });
            $('#kt_cvtracker_filter_close').trigger('click');
        });

        $(document).ready(function() {
            setTimeout(updateCvActiveFiltersUI, 600);
            // Seed a baseline snapshot from the server-provided defaults so a very
            // first close-without-apply reverts correctly even if the shown event
            // hasn't fired yet.
            setTimeout(cvhCaptureSnapshot, 800);
            setTimeout(cvhRenderAppliedFiltersCard, 700);
        });
    </script>
@endpush
