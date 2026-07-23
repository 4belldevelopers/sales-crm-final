@extends('layouts.service_company_layout')
@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
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
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        .selectednotice {
            background-color: #007bff !important;
            /* Example color */
            color: white !important;
        }

        /* Skill / Location tag chips: square-ish rounded rect (not a full pill),
           bordered for definition, consistent padding so star+text+remove all
           sit on one visual baseline regardless of which section renders them. */
        /* Tagify chip styles — visual properties are set as inline styles in the
           JS template so they can't be overridden by plugins.bundle.css (loaded
           mid-body after this <style> block). Only interactive states are here. */
        .tagify__tag .star {
            cursor: pointer;
        }
        .tagify__tag .star.checked {
            color: #eab308 !important;
        }
        /* The vendor Tagify stylesheet renders this button via a CSS
           mask-image (an SVG X clipped out of background-color) rather than
           real text — combined with our own inline background of 6% black
           opacity, the icon was nearly invisible against the light chip
           background. We render a literal "×" character instead (see the
           tag template below) and explicitly cancel the mask so it can't
           clip our text away or double up with a mask-rendered glyph. */
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

        /* The original mass_mail toolbar button stays in the DOM (so its modal-trigger
           class keeps working unchanged) but is permanently hidden now — its restyled
           equivalent lives in #aptk_selection_bar below. */
        .aptk-legacy-hide {
            display: none !important;
        }

        /* Row selection action bar — same design/position as CV Tracker's
           #cvh_selection_bar: hidden until candidates are checked, then sits directly
           above the results table. Only Send Email is offered here (this page has no
           Forward Resume / Copy / Export functionality). */
        .aptk-selection-bar {
            display: none;
            align-items: center;
            gap: 14px;
            background: #fff;
            border: 1px solid #e5e9f2;
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 14px;
            font-family: 'Poppins', sans-serif;
            flex-wrap: wrap;
        }
        .aptk-selection-bar.show {
            display: flex;
        }
        .aptk-selection-count {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }
        .aptk-selection-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
        }
        .aptk-selection-actions .btn {
            font-size: 12px;
            font-weight: 500;
            background: #fff !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            box-shadow: none !important;
        }
        .aptk-selection-actions .btn:hover {
            background: #eff6ff !important;
            border-color: #93c5fd !important;
        }
        .aptk-selection-close {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .aptk-selection-close:hover {
            background: #f1f5f9;
            color: #334155;
        }

        /* Similar Candidates filter drawer (replaces the old #apptrackerfilter modal) */
        /* Force the drawer itself to a fixed, viewport-bound box so only
           .simf-body scrolls internally — without this, the drawer's height
           was growing with its content and the whole browser page ended up
           scrolling instead of just the filter list. */
        #kt_similar_filter_drawer.simf-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: auto !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            z-index: 1200;
            transform: translateX(110%);
            transition: transform .3s ease;
        }
        /* Metronic adds .drawer-on when the filter is open — override the
           initial off-screen transform so the drawer slides fully into view. */
        #kt_similar_filter_drawer.simf-drawer.drawer-on {
            transform: translateX(0) !important;
        }
        #simf_card.simf-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .simf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid #eef0f4;
            flex-shrink: 0;
        }
        .simf-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .simf-title {
            font-size: 17px;
            font-weight: 700;
            color: #181c32;
            margin: 0;
        }
        .simf-count-pill {
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .simf-subtitle {
            font-size: 12px;
            color: #7e8299;
            margin: 2px 0 0;
        }
        .simf-close {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #7e8299;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .simf-close:hover {
            background: #f1f5f9;
            color: #334155;
        }
        .simf-activebar {
            /* Lives inside the scroll container — no pinning needed */
            padding: 12px 0;
            border-bottom: 1px solid #eef0f4;
        }
        .simf-activebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .simf-activebar-label {
            font-size: 12px;
            font-weight: 600;
            color: #181c32;
        }
        .simf-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .simf-clearall:hover {
            color: #b91c1c;
        }
        .simf-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .simf-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #3730a3;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            padding: 5px 7px 5px 10px;
            font-size: 11.5px;
            font-weight: 500;
        }
        .simf-chip.mandatory {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }
        .simf-chip-remove {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,.06);
            cursor: pointer;
            flex-shrink: 0;
        }
        .simf-chip-remove:hover {
            background: rgba(0,0,0,.14);
        }
        #simf_card .simf-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 4px 20px 16px;
        }
        #simf_card .simf-header,
        #simf_card .simf-footer {
            flex-shrink: 0 !important;
        }
        .simf-section {
            padding: 16px 0;
            border-bottom: 1px solid #f1f2f6;
        }
        .simf-section-last {
            border-bottom: none;
        }
        .simf-section-head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }
        .simf-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .simf-icon-blue { background: #eef2ff; color: #4f46e5; }
        .simf-icon-green { background: #ecfdf5; color: #059669; }
        .simf-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .simf-icon-teal { background: #ecfeff; color: #0e7490; }
        .simf-icon-amber { background: #fffbeb; color: #b45309; }
        .simf-icon-rose { background: #fff1f2; color: #be123c; }
        .simf-icon-pink { background: #fdf2f8; color: #be185d; }
        .simf-icon-sky { background: #f0f9ff; color: #0369a1; }
        .simf-section-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #181c32;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .simf-section-desc {
            font-size: 11.5px;
            color: #99a1b7;
            margin-top: 1px;
        }
        .simf-badge-primary {
            font-size: 9.5px;
            font-weight: 600;
            color: #2563eb;
            background: #dbeafe;
            border-radius: 4px;
            padding: 1px 6px;
        }
        .simf-mini-label {
            font-size: 11px;
            font-weight: 500;
            color: #7e8299;
            margin-bottom: 4px;
            display: block;
        }
        .simf-salary-row {
            flex-wrap: nowrap;
        }
        .simf-salary-row > [class^="col-"] {
            padding-left: 4px;
            padding-right: 4px;
            min-width: 0;
        }
        .simf-salary-row .form-select,
        .simf-salary-row .form-control {
            font-size: 11px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .simf-salary-row .form-select {
            padding-right: 20px;
        }

        /* Education multiselect dropdown */
        .simf-select-dd {
            position: relative;
        }
        .simf-select-dd-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 12.5px;
            color: #3f4254;
            text-align: left;
        }
        .simf-select-dd-btn span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 8px;
            /* Education/qualification data is always displayed uppercase,
               regardless of how it was entered/stored — see the standing
               convention note in docs/UI_UX_STANDARDS.md. Applied via CSS
               (display-only) rather than mutating the value, so the exact
               casing used for backend matching (qualification_master.name)
               is untouched. */
            text-transform: uppercase;
        }
        .simf-edu-name {
            text-transform: uppercase;
        }
        .simf-select-dd-btn svg {
            flex-shrink: 0;
            color: #99a1b7;
        }
        .simf-select-dd-panel {
            margin-top: 6px;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }
        .simf-select-dd-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            font-size: 12.5px;
            color: #3f4254;
            cursor: pointer;
            margin: 0;
            border-bottom: 1px solid #f5f6f8;
        }
        .simf-select-dd-item:last-child {
            border-bottom: none;
        }
        .simf-select-dd-item:hover {
            background: #f8f9fb;
        }
        .simf-select-dd-item input {
            accent-color: #2563eb;
        }
        .simf-loc-inputwrap {
            position: relative;
        }
        .simf-loc-chevron {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            color: #99a1b7;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: transform .15s;
        }
        .simf-loc-chevron.open {
            transform: translateY(-50%) rotate(180deg);
        }
        .simf-loc-suggestions {
            margin-top: 10px;
            border: 1px solid #eef0f4;
            border-radius: 8px;
            padding: 10px;
            background: #fafbfc;
        }
        .simf-loc-label {
            font-size: 11px;
            font-weight: 600;
            color: #99a1b7;
            margin-bottom: 6px;
        }
        .simf-loc-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 4px;
        }
        .simf-loc-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 11.5px;
            font-weight: 500;
            color: #3f4254;
        }
        .simf-loc-chip:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .simf-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .simf-pill-row .btn-check {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .simf-pill {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #3f4254;
            cursor: pointer;
            transition: border-color .15s, background-color .15s, color .15s;
        }
        .simf-pill-row .btn-check:checked + .simf-pill {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .simf-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid #eef0f4;
            background: #f8f9fb;
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .simf-footer-count {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .simf-footer-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .simf-footer-number {
            font-size: 15px;
            font-weight: 700;
            color: #181c32;
            line-height: 1.2;
        }
        .simf-footer-label {
            font-size: 10.5px;
            color: #7e8299;
        }
        .simf-footer-hint {
            cursor: help;
        }
        .simf-footer-actions {
            display: flex;
            gap: 6px;
        }
        .simf-footer-actions .btn {
            font-size: 11.5px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 576px) {
            .simf-footer { flex-direction: column; align-items: stretch; }
            .simf-footer-actions { justify-content: stretch; }
            .simf-footer-actions .btn { flex: 1 1 auto; justify-content: center; }
        }

        /* ==================================================================
           Enterprise toolbar redesign: search / sort / filter.
           #kt_tagify_2 is a plain free-text field with one job (candidate
           name / company / recruiter search) — it no longer opens the
           drawer or mirrors the drawer's Skills tagify; the Filter button
           is the drawer's only entry point now, and the Active Filters card
           below is the single place structured filters are summarized.
        ================================================================== */
        .simf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        /* All three controls (search / sort / filter) share the same
           height, background, border and radius so they read as one
           cohesive row — only the Filter button breaks from it deliberately,
           as the single primary/colored action in the row. */
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
        .simf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .simf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .simf-search-icon,
        .simf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .simf-search-icon svg,
        .simf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .simf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        /* Custom Sort control — the native <select> can't be styled
           cross-browser once its dropdown is open (confirmed: Select2 never
           actually initializes on this element, only data-hide-search was
           present, which does nothing without data-control="select2"), so
           the visible control is this button + an owned dropdown panel; the
           real <select id="sortingfilter"> stays hidden underneath purely
           so fetchCandidates()/filtersdataget() keep reading it unchanged. */
        .simf-sort-btn {
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
        .simf-sort-btn:focus { outline: none; }
        .simf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .simf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .simf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .simf-sort-panel {
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
        .simf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .simf-sort-item:hover {
            background: #f8f9fb;
        }
        .simf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .simf-filter-btn {
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
        .simf-filter-btn:hover {
            background: #1d4ed8 !important;
        }
        @media (max-width: 992px) {
            .simf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .simf-search-wrap { flex-basis: 100%; }
            .simf-sort-wrap { width: 140px; }
        }

        /* ==================================================================
           Applied Filters summary card — lives on the main page above the
           results table (NOT inside the drawer), hidden until at least one
           filter has actually been applied via "Apply Filters".
        ================================================================== */
        .simf-applied-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            background: #fff;
            padding: 16px 18px;
            margin-bottom: 16px;
            font-family: 'Poppins', sans-serif;
        }
        .simf-applied-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .simf-applied-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #181c32;
        }
        .simf-applied-count {
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .simf-applied-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .simf-applied-clearall:hover { color: #b91c1c; }
        .simf-applied-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .simf-applied-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .simf-applied-chip .ic { display: inline-flex; align-items: center; flex-shrink: 0; }
        .simf-applied-chip .star { font-size: 12px; line-height: 1; }
        .simf-applied-chip-remove {
            cursor: pointer;
            opacity: .55;
            display: inline-flex;
            align-items: center;
            margin-left: 2px;
        }
        .simf-applied-chip-remove:hover { opacity: 1; }
        .simf-applied-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .simf-applied-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .simf-applied-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .simf-applied-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .simf-applied-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .simf-applied-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .simf-applied-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
        .simf-applied-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .simf-applied-more {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }
        .simf-applied-more:hover { background: #e5e7eb; }
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
               @php
                    use App\Helper\GeneralMethod;

                    $req_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt(@$job->id) . '/all');
                    $public_bench_path = url(
                        '/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $internal_bench_path = url(
                        '/service_company/apptracker/internal_bench/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $existing_path = url(
                        '/service_company/apptracker/similar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
                    );

                    $career_url = url(
                        '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $candidates_request_path = route('service_company.apptracker.candidates_request');
                 
                    $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'tabactive',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'agencyBench_active'=>'',
                    'agencyBench_url'=>$public_bench_path,
                    'agencyBench_id'=>'agencyBench_counter',
                    'benchTalentReq_active'=>'',
                    'benchTalentReq_url'=>$candidates_request_path,
                    'benchTalentReq_id'=>'benchTalentReq_counter',
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    'public_bench_counter' =>@$public_bench_counter,
                    'recomnded_counter' => @$recomnded_counter,
                    ];
                   

                @endphp

             <!--begin::Page title-->
             @include('sections.service_company.toolbar',['id' => $job->id])
            
            <!--end::Page title-->
            <form action="" id="filter-form" style="">
                @csrf
                <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
                <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                    @if (in_array('view_jobs', $userPermissions))
                        {{-- Opens the same right-side job-details drawer (#kt_activitiesJob_show,
                             defined once in the shared layout) that the job title/logo already
                             uses elsewhere — .show-detail-open's click handler in
                             service_company_layout.blade.php ajax-loads jobs.viewmore into it. --}}
                        <a href="javascript:void(0);" data-id="{{ $job->id }}"
                            class="btn btn-sm btn-flex btn-outline btn-outline-primary fw-bolder me-2 show-detail-open">
                            View Job
                        </a>
                    @endif
                    <!--begin::Forward CV-->
                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-1 custombtn aptk-legacy-hide d-none" data-toggle="modal"
                        data-target="#mass_mail"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
                        </span> Mass Email 
                    </a>

                    <!--begin::Search-->
                    <div class="d-none d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                    transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1 me-3">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                                <!--end::Svg Icon-->
                                <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                                    placeholder="Search..." />
                            </div>
                            <!--end::Search-->

                            <!--begin::Filter-->
                            <!--<button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">-->
                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                            <!--<span class="svg-icon svg-icon-2">-->
                            <!--<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">-->
                            <!--  <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />-->
                            <!--</svg>-->
                            <!--</span>-->
                            <!--end::Svg Icon-->
                            <!--Filter </button>-->



                            <!--end::Filter-->
                            <!--begin::Export-->
                            <button type="button"
                                class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2"
                                            rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                        <path
                                            d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                            fill="black" />
                                        <path
                                            d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                            fill="#C4C4C4" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                Export
                            </button>
                            <!--end::Export-->

                        </div>
                        <!--end::Toolbar-->
                    </div>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    @php
        //echo '<pre>'; print_r($candStatusClass);
        $sourcing_bg = $screening_bg = $interview_bg = $negotation_bg = $offer_bg = $joining_bg = $rejected_bg = $All_candidate_bg =
            'alert bg-gray text-center circle_boxes';
        $sourcing_text = $screening_text = $interview_text = $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text =
            'text-center color-all';

        if (!empty(@$candStatusClass['Index'])) {
            if (@$candStatusClass['Index'] == 'Sourcing') {
                $sourcing_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $sourcing_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Screening') {
                $screening_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $screening_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Interview') {
                $interview_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $interview_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Negotiation') {
                $negotation_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $negotation_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Offered') {
                $offer_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $offer_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Joined') {
                $joining_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $joining_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Rejected') {
                $rejected_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $rejected_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } else {
                $All_candidate_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $All_candidate_text = 'text-center  ' . @$candStatusClass['nav_text'];
            }
        }

        $tr_class = 'bg-gray';
        if (!empty($mstatus_id)) {
            if ($mstatus_id == 'all') {
                $tr_class = 'bg-total-active';
            } else {
                $tr_class = 'bg-gray';
            }
        }

    @endphp

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">

            <div id="" class="container-fluid d-flex flex-stack second_header">
               @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])

            </div>

                <div class="card mb-4 simf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="simf-search-wrap" style="position: relative;">
                                <span class="simf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control simf-search-input" name="inputJobSearch"
                                    placeholder="Search by candidate name, company or recruiter..." id="kt_tagify_2" autocomplete="off" />
                            </div>
                            <div class="simf-sort-wrap" id="simf_sort_wrap">
                                <span class="simf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="simf-sort-btn" id="simf_sort_btn">
                                    <span id="simf_sort_label">Best Match</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="10" selected="">Best Match</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice Period (Serving)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="simf-sort-panel d-none" id="simf_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_similar_filter_toggle"
                                class="btn simf-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="simf_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary simf-count-badge d-none" id="simf_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!--begin::Applied Filters summary card — hidden until Apply Filters is clicked-->
                <div class="simf-applied-card d-none" id="simf_applied_card">
                    <div class="simf-applied-head">
                        <span class="simf-applied-title">
                            Active Filters
                            <span class="simf-applied-count" id="simf_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="simf-applied-clearall" id="simf_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="simf-applied-chips" id="simf_applied_chips"></div>
                </div>
                <!--end::Applied Filters summary card-->


                <!--Start Filter Drawer-->
                <div id="kt_similar_filter_drawer" class="bg-body simf-drawer" data-kt-drawer="true" data-kt-drawer-name="similar-filter"
                    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_similar_filter_toggle" data-kt-drawer-close="#kt_similar_filter_close">
                    <div id="simf_card" class="card shadow-none rounded-0 w-100 h-100 simf-card">
                        <!--begin::Header-->
                        <div class="simf-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="simf-header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="simf-title">Filter Candidates</h3>
                                        <span class="simf-count-pill" id="simf_header_count">0</span>
                                    </div>
                                    <p class="simf-subtitle">Refine your search and find the best talent</p>
                                </div>
                            </div>
                            <button type="button" class="simf-close" id="kt_similar_filter_close" title="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="simf-body">
                            <form id="createSchedule" class="ajax-form" method="post">

                                <!--begin::Active filters — inside scroll area so it flows with the sections -->
                                <div class="simf-activebar d-none" id="simf_active_bar">
                                    <div class="simf-activebar-head">
                                        <span class="simf-activebar-label">Active Filters <span id="simf_active_count">(0)</span></span>
                                        <a href="javascript:void(0);" class="simf-clearall" id="simf_clear_all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                            Clear All
                                        </a>
                                    </div>
                                    <div class="simf-chips" id="simf_active_chips"></div>
                                </div>
                                <!--end::Active filters-->

                                <!--begin::Skills-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Skills <span class="simf-badge-primary">Primary</span></div>
                                            <div class="simf-section-desc">Add skills to find relevant candidates — click ★ to make a skill mandatory</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
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
                                                        const mandatoryClass = tagData.checked ? 'simf-mandatory-tag' : '';
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
                                            // OR the keyword text next to it — previously only the tiny
                                            // star hit-target worked, so clicking the (much bigger, more
                                            // obvious) keyword label appeared to do nothing.
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

                                                    // Update glyph, star colour, chip background/border inline
                                                    // (template uses inline styles so CSS classes alone won't work)
                                                    starEl.textContent = nowChecked ? '★' : '☆';
                                                    starEl.style.color = nowChecked ? '#eab308' : '#94a3b8';
                                                    tagElm.style.background = nowChecked ? '#dbeafe' : '#eef2ff';
                                                    tagElm.style.borderColor = nowChecked ? '#bfdbfe' : '#e0e7ff';
                                                    textEl.style.color = nowChecked ? '#1d4ed8' : '#3730a3';
                                                    tagElm.classList.toggle('simf-mandatory-tag', nowChecked);

                                                    // Update the tag data
                                                    const tagData = tagify.value.find(tag => tag.value === value);
                                                    if (tagData) {
                                                        setTimeout(() => {
                                                            tagData.checked = nowChecked;
                                                            tagify.updateValueByDOMTags();
                                                            updateActiveFiltersUI();
                                                        }, 500);
                                                    }
                                                }
                                            });

                                            // #kt_tagify_2 (the top toolbar's search box) is a plain
                                            // free-text field, deliberately NOT Tagify-wrapped and NOT
                                            // synced with the drawer's Skills tagify — it previously
                                            // mirrored the drawer's skill chips two-way, which duplicated
                                            // what the Active Filters card and drawer already show. Its
                                            // one job now is free-text search (name/company/recruiter/an
                                            // unfiltered skill), kept separate from structured filtering.

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
                                                            tagify.settings.whitelist = data; // Update whitelist
                                                            tagify.dropdown.show.call(tagify);
                                                        } else {
                                                            console.error("Invalid API response format: Expected array.");
                                                        }
                                                    },
                                                    error: function(err) {
                                                        console.error("Error fetching Skill:", err);
                                                    }
                                                });
                                            }, 200);

                                            // Listen for user input to trigger API call
                                            tagify.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 1) return;
                                                fetchSuggestionsSkills(query);
                                            });
                                            tagify.on('add', updateActiveFiltersUI);
                                            tagify.on('remove', updateActiveFiltersUI);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Skills-->

                                <!--begin::Location-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Preferred Location</div>
                                            <div class="simf-section-desc">Preferred cities or work locations</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="simf-loc-inputwrap" id="simf_loc_inputwrap">
                                            <input class="form-control form-control-solid"
                                                value="{{ $default_loc }}" id="kt_tagify_location"
                                                placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
                                            <button type="button" class="simf-loc-chevron" id="simf_loc_chevron" title="Show suggestions">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        </div>
                                        <script>
                                            // The DOM elements you wish to replace with Tagify
                                            var inputloc = document.querySelector("#kt_tagify_location");
                                            var cities = @json($cities);
                                            // Initialize Tagify script on the above inputs
                                            var tagifylocation = new Tagify(inputloc, {
                                                whitelist: [],
                                                maxTags: 10,
                                                // Without a custom template, Location tags fell back to
                                                // Tagify's own default (grey) styling — the Skills tagify
                                                // above only looks themed because ITS colour comes from
                                                // this same kind of custom template, not from a shared
                                                // CSS class. Same blue/indigo chip + blue remove "×" as
                                                // Skills, just without the star (Location has no
                                                // mandatory/optional concept).
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
                                                       clipped by .simf-body's overflow:hidden/auto.
                                                       The body is position:absolute (set by
                                                       simfEnforceDrawerLayout), making it the containing
                                                       block for any position:absolute child — including
                                                       Tagify's dropdown — so the dropdown was silently
                                                       clipped. Appending to document.body escapes that
                                                       context entirely; Tagify repositions the dropdown
                                                       via getBoundingClientRect so it still aligns with
                                                       the input field. */
                                                    appendTarget: document.body
                                                }
                                            });
                                            // Create tags only on select
                                            tagifylocation.on('dropdown:select', function(e) {
                                                tagifylocation.addTags([e.detail.data.value]);
                                                simfCloseLocPanel();
                                            });
                                            tagifylocation.on('add', function(e) {
                                                updateActiveFiltersUI();
                                                simfCloseLocPanel();
                                            });
                                            tagifylocation.on('remove', function(e) {
                                                updateActiveFiltersUI();
                                            });

                                            function debounce(func, delay) {
                                                let timer;
                                                return function (...args) {
                                                    clearTimeout(timer);
                                                    timer = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            // Debounced function to fetch suggestions.
                                            // The whitelist is refreshed FIRST, then the dropdown is
                                            // shown — showing it before the whitelist arrives (as an
                                            // earlier input handler did) rendered an empty, stuck
                                            // dropdown that never repopulated, which is why the city
                                            // autosuggest appeared broken.
                                            const fetchSuggestions = debounce(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            // Tagify's dropdown filters against
                                                            // settings.whitelist specifically — a
                                                            // top-level tagifylocation.whitelist isn't
                                                            // read anywhere, which is why suggestions
                                                            // stopped appearing after this was changed.
                                                            tagifylocation.settings.whitelist = data;
                                                            // Native typeahead and the custom "Popular
                                                            // Locations" panel would otherwise overlap;
                                                            // close the panel so the real suggestions show.
                                                            simfCloseLocPanel();
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

                                            // Listen for user input to trigger API call
                                            tagifylocation.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 2) return;
                                                simfCloseLocPanel();
                                                fetchSuggestions(query);
                                            });

                                            // $default_locations is EVERY city configured on this job
                                            // (not just the first — that was the bug: JobcitiesTags()
                                            // used by the old single-string $default_loc only ever
                                            // returns one city via ->take(1), so a job posted across
                                            // multiple locations only had the first auto-applied as a
                                            // filter here, silently dropping the rest).
                                            setTimeout(() => {
                                                var defaultLocations = {!! json_encode(@$default_locations ?? []) !!};
                                                var firstCity = defaultLocations.length ? defaultLocations[0].split(" | ")[0] : '';
                                                if (firstCity !== '') {
                                                    fetchSuggestions(firstCity);
                                                }
                                            }, 100);
                                            setTimeout(() => {
                                                var defaultLocations = {!! json_encode(@$default_locations ?? []) !!};

                                                if (defaultLocations.length) {
                                                    // One addTags() call with the full array creates one
                                                    // tag per element — not one tag containing everything.
                                                    tagifylocation.addTags(defaultLocations);
                                                    tagifylocation.dropdown.hide();
                                                }
                                                updateActiveFiltersUI();
                                            }, 5000);
                                        </script>

                                        <div class="simf-loc-suggestions d-none" id="simf_loc_panel">
                                            <div class="simf-loc-label">Popular Locations</div>
                                            <div class="simf-loc-chip-row">
                                                <button type="button" class="simf-loc-chip" data-city="Bangalore">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    Bangalore
                                                </button>
                                                <button type="button" class="simf-loc-chip" data-city="Hyderabad">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                                                    Hyderabad
                                                </button>
                                                <button type="button" class="simf-loc-chip" data-city="Pune">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 21V10l6-4 6 4v11M9 21v-5h6v5"/></svg>
                                                    Pune
                                                </button>
                                                <button type="button" class="simf-loc-chip" data-city="Mumbai">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 21v-4h6v4"/></svg>
                                                    Mumbai
                                                </button>
                                                <button type="button" class="simf-loc-chip" data-city="Chennai">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V9l8-6 8 6v12M10 21v-6h4v6"/></svg>
                                                    Chennai
                                                </button>
                                                <button type="button" class="simf-loc-chip" data-city="Remote">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>
                                                    Remote
                                                </button>
                                            </div>
                                            <div class="simf-loc-label mt-2">Other Suggestions</div>
                                            <div class="simf-loc-chip-row">
                                                <button type="button" class="simf-loc-chip" data-city="Noida">Noida</button>
                                                <button type="button" class="simf-loc-chip" data-city="Gurgaon">Gurgaon</button>
                                                <button type="button" class="simf-loc-chip" data-city="Kolkata">Kolkata</button>
                                                <button type="button" class="simf-loc-chip" data-city="Ahmedabad">Ahmedabad</button>
                                                <button type="button" class="simf-loc-chip" data-city="Chandigarh">Chandigarh</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--end::Location-->

                                <!--begin::Education-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Education</div>
                                            <div class="simf-section-desc">Highest qualification</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <input type="hidden" id="kt_tagify_education" value="{{ $qualification }}" />
                                        <div class="simf-select-dd" id="simf_edu_dd">
                                            <button type="button" class="simf-select-dd-btn" id="simf_edu_dd_btn">
                                                <span id="simf_edu_dd_label">Any Education</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div class="simf-select-dd-panel d-none" id="simf_edu_dd_panel">
                                                <label class="simf-select-dd-item">
                                                    <input type="checkbox" class="simf-edu-check" value="" checked>
                                                    <span>Any</span>
                                                </label>
                                                @foreach ($qualificationTag as $qval)
                                                    <label class="simf-select-dd-item">
                                                        <input type="checkbox" class="simf-edu-check" value="{{ $qval }}">
                                                        <span class="simf-edu-name">{{ $qval }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <script>
                                            // Drives the same #kt_tagify_education hidden field the backend
                                            // already reads (JSON array of {value:"..."} objects, matched
                                            // against qualification_master.name) — only the picker UI changed
                                            // from free-typed Tagify chips to a checklist dropdown.
                                            var simfEducationSelected = (function() {
                                                var initial = @json($qualification ? [$qualification] : []);
                                                return initial.filter(Boolean);
                                            })();

                                            function simfSyncEducationInput() {
                                                var payload = simfEducationSelected.map(function(v) { return { value: v }; });
                                                $('#kt_tagify_education').val(JSON.stringify(payload));
                                                $('#simf_edu_dd_label').text(simfEducationSelected.length ? simfEducationSelected.join(', ') : 'Any Education');
                                                updateActiveFiltersUI();
                                            }

                                            $(document).on('change', '.simf-edu-check', function() {
                                                var val = $(this).val();
                                                if (val === '') {
                                                    // "Any" clears every specific selection
                                                    if ($(this).is(':checked')) {
                                                        $('.simf-edu-check').not(this).prop('checked', false);
                                                        simfEducationSelected = [];
                                                    }
                                                } else {
                                                    $('.simf-edu-check[value=""]').prop('checked', false);
                                                    if ($(this).is(':checked')) {
                                                        if (simfEducationSelected.indexOf(val) === -1) simfEducationSelected.push(val);
                                                    } else {
                                                        simfEducationSelected = simfEducationSelected.filter(function(v) { return v !== val; });
                                                        if (simfEducationSelected.length === 0) $('.simf-edu-check[value=""]').prop('checked', true);
                                                    }
                                                }
                                                simfSyncEducationInput();
                                            });

                                            $(document).on('click', '#simf_edu_dd_btn', function() {
                                                $('#simf_edu_dd_panel').toggleClass('d-none');
                                            });
                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#simf_edu_dd').length) {
                                                    $('#simf_edu_dd_panel').addClass('d-none');
                                                }
                                            });

                                            $(document).ready(simfSyncEducationInput);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Education-->

                                <!--begin::Experience-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Experience (Years)</div>
                                            <div class="simf-section-desc">Total work experience</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="simf-mini-label">Minimum Experience</label>
                                                <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                                    <option value="" selected="">Min Experience</option>
                                                    <option value="0" {{ 0 == $min_exp ? 'selected' : '' }}>Freshers</option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}" {{ $i == $min_exp ? 'selected' : '' }}>{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                            <div class="col-6">
                                                <label class="simf-mini-label">Maximum Experience</label>
                                                <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                                    <option value="" selected="">Max Experience</option>
                                                    <option value="0" {{ 0 == $max_exp ? 'selected' : '' }}>Freshers</option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}" {{ $i == $max_exp ? 'selected' : '' }}>{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container" id="experience_error" style="color:red !important;text-align:center;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Experience-->

                                <!--begin::Salary-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-amber">
                                            <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Salary (CTC)</div>
                                            <div class="simf-section-desc">Salary range with currency &amp; mode</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="row g-2 simf-salary-row">
                                            <div class="col-3">
                                                <label class="simf-mini-label">Currency</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency">
                                                    <option value="">Select currency</option>
                                                    <option value="0" {{ 0 == $min_salary_currency ? 'selected' : '' }}>INR (₹)</option>
                                                    <option value="1" {{ 1 == $min_salary_currency ? 'selected' : '' }}>EUR</option>
                                                    <option value="2" {{ 2 == $min_salary_currency ? 'selected' : '' }}>JPY</option>
                                                    <option value="3" {{ 3 == $min_salary_currency ? 'selected' : '' }}>GBP</option>
                                                    <option value="4" {{ 4 == $min_salary_currency ? 'selected' : '' }}>USD</option>
                                                    <option value="5" {{ 5 == $min_salary_currency ? 'selected' : '' }}>AED</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="simf-mini-label">Mode</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                    <option value="">Select Mode</option>
                                                    <option value="0" {{ 0 == $min_sal_mode ? 'selected' : '' }}>Annual</option>
                                                    <option value="1" {{ 1 == $min_sal_mode ? 'selected' : '' }}>Monthly</option>
                                                    <option value="2" {{ 2 == $min_sal_mode ? 'selected' : '' }}>Hourly</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="simf-mini-label">Min Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="0">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted minimum_salary_word"></span>
                                            </div>
                                            <div class="col-3">
                                                <label class="simf-mini-label">Max Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Any" value="{{ $max_salary }}">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted maximum_salary_word"></span>
                                            </div>
                                            <span class="form-text text-muted" id="salary_error" style="color:red !important;text-align:center;"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Salary-->

                                <!--begin::Notice Period-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-rose">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Notice Period / Availability</div>
                                            <div class="simf-section-desc">Time to join</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="simf-pill-row" data-radio-group="app_int_notice">
                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="1" id="app_int_notice1" checked>
                                            <label class="simf-pill" for="app_int_notice1">Any</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="8" id="app_int_notice8">
                                            <label class="simf-pill" for="app_int_notice8">Immediate</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="2" id="app_int_notice2">
                                            <label class="simf-pill" for="app_int_notice2">0-15 Days</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="3" id="app_int_notice3">
                                            <label class="simf-pill" for="app_int_notice3">1 Month</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="4" id="app_int_notice4">
                                            <label class="simf-pill" for="app_int_notice4">2 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="5" id="app_int_notice5">
                                            <label class="simf-pill" for="app_int_notice5">3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="6" id="app_int_notice6">
                                            <label class="simf-pill" for="app_int_notice6">More than 3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="9" id="app_int_notice9">
                                            <label class="simf-pill" for="app_int_notice9">Served Notice Period</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="7" id="app_int_notice7">
                                            <label class="simf-pill" for="app_int_notice7">Currently Serving Notice</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Notice Period-->

                                <!--begin::Stages-->
                                <div class="simf-section">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-pink">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Stages</div>
                                            <div class="simf-section-desc">Current stage in your pipeline</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="simf-pill-row" data-radio-group="app_int_status">
                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="0" id="app_int_status0" checked>
                                            <label class="simf-pill" for="app_int_status0">Any</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="8" id="app_int_status8">
                                            <label class="simf-pill" for="app_int_status8">Inreview</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="1" id="app_int_status1">
                                            <label class="simf-pill" for="app_int_status1">Sourcing</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="2" id="app_int_status2">
                                            <label class="simf-pill" for="app_int_status2">Screening</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="3" id="app_int_status3">
                                            <label class="simf-pill" for="app_int_status3">Evaluation</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="4" id="app_int_status4">
                                            <label class="simf-pill" for="app_int_status4">Negotiations</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="5" id="app_int_status5">
                                            <label class="simf-pill" for="app_int_status5">Offer</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="6" id="app_int_status6">
                                            <label class="simf-pill" for="app_int_status6">Joined</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="7" id="app_int_status7">
                                            <label class="simf-pill" for="app_int_status7">Rejected</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Stages-->

                                <!--begin::Activity-->
                                <div class="simf-section simf-section-last">
                                    <div class="simf-section-head">
                                        <span class="simf-icon simf-icon-sky">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                                        </span>
                                        <div>
                                            <div class="simf-section-title">Activity</div>
                                            <div class="simf-section-desc">Last active on JeroATS</div>
                                        </div>
                                    </div>
                                    <div class="simf-section-body">
                                        <div class="simf-pill-row" data-radio-group="app_int_activity">
                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                                            <label class="simf-pill" for="app_int_activity1">Any</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                            <label class="simf-pill" for="app_int_activity2">30 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                            <label class="simf-pill" for="app_int_activity3">60 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                            <label class="simf-pill" for="app_int_activity4">90 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                            <label class="simf-pill" for="app_int_activity5">180 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                            <label class="simf-pill" for="app_int_activity6">1 Year</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Activity-->

                            </form>
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="simf-footer">
                            <div class="simf-footer-count">
                                <span class="simf-footer-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </span>
                                <div>
                                    <div class="simf-footer-number" id="simf_footer_number">{{ @$existing_counter ?? 0 }}</div>
                                    <div class="simf-footer-label">Candidates Match <span class="simf-footer-hint" title="Updates after Preview or Apply">ⓘ</span></div>
                                </div>
                            </div>
                            <div class="simf-footer-actions">
                                <button type="button" class="btn btn-sm btn-light" id="simf_close_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    Close
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="simf_reset_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                    Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="simf_apply_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
                <!--End Filter Drawer-->

                {{-- Row selection action bar — hidden until candidates are checked.
                     Reuses the same mass_mail trigger class as the legacy toolbar button,
                     so the existing modal/ajax handler works unchanged; only the
                     presentation and position (directly above the results table) are
                     new. Only Send Email is offered here (no Forward Resume / Copy /
                     Export on this page). --}}
                <div class="aptk-selection-bar" id="aptk_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="aptk_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="aptk-selection-count"><span id="aptk_selected_count">0</span> candidates selected</span>
                    <div class="aptk-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" data-toggle="modal" data-target="#mass_mail">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                    </div>
                    <button type="button" class="aptk-selection-close" id="aptk_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Loader outside card so it shows even before card is revealed -->
                <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                    <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                </div>

                <!--begin::Card-->
                <div class="card" id="sc_table_card" style="display:none;">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5" style="position: relative;">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="myTable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                    <th class="w-10px pe-0">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input chk_list" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th class="">Candidate Name</th>
                                    {{-- <th class="">Contacts</th> --}}
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Notice</th>
                                    <th class="">Location</th>
                                    <th class="">Jobs</th>
                                    <th class="">Status</th>
                                    <th class="">Ownership</th>
                                    {{-- <th class="">Date</th> --}}
                                    <th class="">Stage</th>
                                    <th class="">Action</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <tbody class="text-gray-600 fw-bold" id="candidatetList">
                            </tbody>
                        </table>
                        <div id="paginationcandidate"></div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->

    <div id="kt_activitiesBA_toggle"></div>
    <div id="kt_activitiesBA" class="bg-body" style="display:none;" data-kt-drawer="true" data-kt-drawer-name="activities"
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
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect fill="#000000" opacity="0.5"
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
    
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        $(document).ready(function() {

            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_exp", function() {
                experience_check()
            });
            $(document).on("change", "#max_exp", function() {
                experience_check()
            });

            $(document).on('keyup', '#min_sal', function() {
                var number = parseInt($('#min_sal').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));
            });
            $(document).on('keyup', '#max_sal', function() {
                var number = parseInt($('#max_sal').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));
            });

        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
            } else {
                $('.chk_list_item').prop('checked', false);	
            }
        });
        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
            updateAptkSelectionBar();
        });
        $(document).on('click', '.chk_list_item', function() {
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
            updateAptkSelectionBar();
        });

        /* Selection bar: live count + clear-selection controls (checkbox + close X) —
           same behavior as the Pool tab / CV Tracker's #cvh_selection_bar. */
        function updateAptkSelectionBar() {
            var n = $('.chk_list_item:checked').length;
            $('#aptk_selected_count').text(n);
            $('#aptk_selection_bar').toggleClass('show', n > 0);
            $('#aptk_selection_master_chk').prop('checked', n > 0);
        }
        function clearAptkSelection() {
            $('.chk_list, .chk_list_item').prop('checked', false);
            $('.fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
            $('.custom_hide').removeClass('d-none');
            updateAptkSelectionBar();
        }
        $(document).on('click', '#aptk_selection_close', clearAptkSelection);
        $(document).on('change', '#aptk_selection_master_chk', function() {
            if (!$(this).is(':checked')) clearAptkSelection();
        });

        $(document).on('click', '.mass_mail', function() {

            $('#application_mass_mail_body_content').html('');
            $('#application_fwd_resume_body_content').html('');
            var id = {{ $job->id }};
            var arr = [];
            var jobIdArr = [id];
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

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function convertToWords(num) {
            if (num < 10) {
                return ones[num];
            } else if (num < 20) {
                return teens[num - 10];
            } else if (num < 100) {
                return tens[Math.floor(num / 10)] + ' ' + ones[num % 10];
            } else if (num < 1000) {
                return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100);
            } else if (num < 100000) {
                return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000);
            } else if (num < 10000000) {
                return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000);
            } else if (num < 1000000000) {
                return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        function salary_check() {
            var minimum_salary = parseInt($("#min_sal").val());
            var maximum_salary = parseInt($("#max_sal").val());
            if (minimum_salary != '') {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Enter the maximum salary greater than the minimum salary',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_sal").val('');
                }
            }
        }

        function experience_check() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (min_exp != '') {
                if (min_exp > max_exp) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Select the maximum experience greater than the minimum experience',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_exp").val('');
                }
            }
        }





        // A second, independent Tagify instance used to be created here on
        // #kt_tagify_2 (in addition to the one removed alongside the sync
        // code near its declaration) with whitelist: [""] — a single blank
        // entry — and dropdown.enabled: 0, which showed a dropdown with one
        // blank-looking suggestion the instant the field was focused. That
        // was the actual cause of the "blank dropdown opens on search bar
        // click" bug; #kt_tagify_2 is a plain input now, not Tagify-wrapped
        // by anything.

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('[data-kt-modal-bidding="option"]');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove the "selected" class from all buttons
                    buttons.forEach(btn => btn.classList.remove('selectednotice'));

                    // Add the "selected" class to the clicked button
                    this.classList.add('selectednotice');
                });
            });
        });
    </script>
    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');	
        }, 500);

        function filtersdataget() {
            // Used by the sort dropdown's onchange — the filter drawer itself
            // now has its own Preview/Apply buttons that call fetchCandidates()
            // directly, this just covers the sort-only auto-apply case.
            fetchCandidates();
        }

        function tableLoad(type) {

            var jobid = '{{ @$job->id }}';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();

            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.similar_existing_data') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&app_int_notice=' + app_int_notice + '&sal_mode=' +
                    sal_mode,
                "fnDrawCallback": function(oSettings) {},
                "initComplete": function(settings, json) {
                    $('.clientStatusLoader').html('');
                    $('.jobLoader').html('');
                    $('.sourceLoader').html('');
                    $('.search_np_idLoader').html('');
                    $('.search_passportLoader').html('');
                    $('.statusLoader').html('');
                },

                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        orderable: false,
                        searchable: false,
                        width: '12%'
                    },
                    // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '10%'},						
                    {
                        data: 'Exp',
                        name: 'Exp',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'NP',
                        name: 'NP',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Qualification',
                        name: 'Qualification',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        orderable: false
                    },
                    {
                        data: 'Date',
                        name: 'Date',
                        orderable: false
                    },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        orderable: false
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        orderable: false
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        orderable: false
                    },
                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false
                    }
                ]
            });
            new $.fn.dataTable.FixedHeader(table);

            countexist();

        }


        function countexist() {
            setTimeout(() => {
                // var rowCount = $('#myTable tbody tr:not(.dataTables_empty)').length;
                var allRowCount = $('#myTable tbody tr').length;

                // Count rows that have a cell with the class dataTables_empty
                var emptyRowCount = $('#myTable tbody tr:has(td.dataTables_empty)').length;
                var existingDataCount = allRowCount - emptyRowCount;
                $('.existing_data').text(existingDataCount);


            }, 2500);
        }
    </script>
    <script>
        // Small top-right toast, auto-dismissing, pause-on-hover — SweetAlert2's
        // own documented toast mode, so no new notification library is added.
        // Replaces the old success/fail/cancel POPUP modals (which blocked the
        // screen and needed a click to dismiss) per design feedback; the "Are
        // you sure?" CONFIRMATION dialog below is intentionally left as a real
        // modal — it's the one safety check before an action the app itself
        // describes as irreversible, and removing it would be a functionality
        // change, not a presentation one.
        const simfToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2600,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // The candidate stays visible in THIS list after a successful add —
        // swapping the button to the same "Added" label the row partial
        // already renders server-side for candidates already in the job's
        // pipeline (see similar_existing_ajax.blade.php's $existingCandidatesQuery
        // branch) — rather than fading/removing the row. The row only
        // actually disappears once the list is genuinely re-fetched
        // (pagination, filter, sort, or a real page reload), which is when
        // the server-side exclusion naturally takes over. This avoids the
        // "did that actually work, where did the row go?" confusion of an
        // instant removal.
        function simfMarkCandidateRowAdded($btn) {
            $btn.closest('div.d-flex.flex-column').html(
                '<a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a>'
            );
        }

        $(document).on('click', '.add_to_inreview', function(e) {
            e.preventDefault(); // 🚫 stop anchor default behavior

            var $btn = $(this);

            // 🔒 Prevent double click
            if ($btn.hasClass('processing')) {
                return false;
            }

            var id = $btn.data('row-id');
            var source_from_type = $btn.data('row-source_from_type');
            var source_jobid = $btn.data('row-jobid');
            var source_tab = $btn.data('row-from');
            var wiseId = $btn.data('row-wiseid');

            var url = '{{ route('service_company.apptracker.rec_candidate_assign') }}';
            var token = "{{ csrf_token() }}";

            // No confirmation dialog — a single click starts the add
            // immediately; the spinner below is the only "processing"
            // feedback needed, and the result comes back as a toast.
            var originalHtml = $btn.html();
            $btn.addClass('processing')
                .prop('disabled', true)
                .css('pointer-events', 'none')
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');

            function restoreButton() {
                $btn.removeClass('processing')
                    .prop('disabled', false)
                    .css('pointer-events', '')
                    .html(originalHtml);
            }

            var inner_url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
            var inner_token = "{{ csrf_token() }}";
            var eventId = 9;

            $.ajax({
                type: 'POST',
                url: inner_url,
                beforeSend: function() {},
                data: {
                    '_token': inner_token,
                    'event': 'internaldatabaseaccess',
                    'app_id': id,
                    'source_jobid': source_jobid,
                    'eventId': eventId
                },
                success: function(response) {
                    if (response.status == "success") {
                        $.ajax({
                            type: 'GET',
                            url: url,
                            data: {
                                'id': id,
                                'source_from_type': source_from_type,
                                'source_jobid': source_jobid,
                                'source_tab': source_tab,
                                'wiseId': wiseId
                            },
                            success: function(response) {
                                if (response.status == 'coinCheck') {
                                    restoreButton();
                                    simfToast.fire({ icon: 'warning', title: response.message });
                                    return false;
                                }

                                if (response.status == 'success') {
                                    simfMarkCandidateRowAdded($btn);
                                    simfToast.fire({ icon: 'success', title: 'Candidate added to Job — In-Review stage' });
                                } else {
                                    restoreButton();
                                }
                            },
                            error: function() {
                                restoreButton();
                                simfToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
                            }
                        });
                    } else if (response.status == "info") {
                        restoreButton();
                        simfToast.fire({ icon: 'error', title: response.message });
                    } else {
                        restoreButton();
                    }
                },
                error: function() {
                    restoreButton();
                    simfToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
                }
            });
        });


        $(document).ready(function() {
            // $.noConflict();
            
            setTimeout(() => {
                fetchCandidates();
            }, 200);
            
            
            $(document).on('click', '#paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('pagi-disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow').removeClass('pagi-disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow').removeClass('pagi-disabled');
                });
            });


        });

        function fetchCandidates(page = 1, search = '') {
            var jobid = '{{ @$job->id }}';
            // #inputSearch never existed on this page — the real search box
            // is #kt_tagify_2. This was previously always reading undefined,
            // silently sending an empty "search" param on every request.
            // similar_existing_data_ajax now reads it and applies it as an
            // additional AND-ed Elasticsearch filter clause (see the backend
            // for field list/details), so it narrows whatever filters are
            // already active rather than searching the full candidate pool.
            var inputSearch = $('#kt_tagify_2').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();

            $('.loadingMessage').css({ display: 'none' });
            $('.page-loader').css('display', 'flex');
            // Return the jqXHR so callers that chain .then()/.catch() (e.g. the
            // pagination handler) don't crash on an undefined return value.
            return $.ajax({
                url: `{!! route('service_company.apptracker.similar_existing_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                jobid:jobid,
                search:inputSearch,
                sortingfilter:sortingfilter,
                country:country,
                cus_search:(cus_search),
                min_exp:min_exp,
                max_exp:max_exp,
                kt_tagify_location:kt_tagify_location,
                min_sal:min_sal,
                max_sal:max_sal,
                min_salary_currency:min_salary_currency,
                kt_tagify_education:kt_tagify_education,
                app_int_status:app_int_status,
                app_int_activity:app_int_activity,
                app_int_notice:app_int_notice,
                sal_mode:sal_mode
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    // Guard every field: if the backend ever returns an error
                    // shape or a non-JSON page for a particular filter combo,
                    // keep the existing list instead of blanking it with
                    // `undefined`, and log the raw payload so the real cause is
                    // visible in the console rather than a silent blank screen.
                    if (!response || typeof response.html === 'undefined') {
                        console.error('similar_existing_data_ajax: unexpected response', response);
                        return;
                    }
                    $('#candidatetList').html(response.html);
                    $('#similar_counter').html(response.total);
                    $('#simf_footer_number').text(response.total);
                    $('#paginationcandidate').html(response.pagination);
                    // The header card's hint (see apptrackerHeader.blade.php)
                    // says "job defaults" by default — correct everywhere
                    // EXCEPT here, since this specific number IS the live,
                    // current-filter result once fetched. Swap the tooltip
                    // so it doesn't contradict what the page just showed.
                    $('#apth_hint_similar_counter').attr('title',
                        'This is your current filter selection on this page — the same card on other tabs shows this job\'s default count instead.');
                },
                error: function(xhr) {
                    // Never leave the loading overlay stuck or the list blank on
                    // a failed request — surface what went wrong instead.
                    $('.loadingMessage').css({ display: 'none' });
                     $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    console.error('similar_existing_data_ajax failed:', xhr.status, xhr.responseText);
                }
            });
        }

        // Free-text search box: Enter re-runs the same fetchCandidates() call
        // the sort dropdown and pagination already use — no separate endpoint.
        $(document).on('keydown', '#kt_tagify_2', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                fetchCandidates();
            }
        });

        // Custom Sort dropdown. Reads options straight from the real
        // #sortingfilter <select> (single source of truth — nothing is
        // duplicated/hardcoded here), keeps that select's value in sync so
        // filtersdataget()/fetchCandidates() are completely unaffected, and
        // only replaces how the option list is PRESENTED, since a native
        // <select>'s open dropdown can't be styled cross-browser.
        function simfRenderSortPanel() {
            var $panel = $('#simf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="simf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#simf_sort_label').text(label);
                        simfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function simfOpenSortPanel() {
            simfRenderSortPanel();
            $('#simf_sort_panel').removeClass('d-none');
            $('#simf_sort_btn').addClass('open');
        }
        function simfCloseSortPanel() {
            $('#simf_sort_panel').addClass('d-none');
            $('#simf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#simf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#simf_sort_panel').hasClass('d-none')) simfOpenSortPanel();
            else simfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#simf_sort_wrap').length) simfCloseSortPanel();
        });
    </script>
    <script>
        /* Drawer width = 80% of the actual content area (i.e. "20% less than
           the full page width, excluding the vertical sidebar"). A CSS
           percentage on the drawer itself would resolve against the full
           viewport (position:fixed drawers use the viewport as their
           containing block), which would include the space the sidebar
           sits on — so instead we measure #kt_wrapper, Metronic's main
           content flex box that already excludes #kt_aside, and size the
           drawer to 80% of that measured width in pixels. Recalculated on
           resize and right before the drawer opens, so it stays correct if
           the sidebar collapses/expands or the window is resized. */
        function simfSetDrawerWidth() {
            var wrapper = document.getElementById('kt_wrapper');
            var contentWidth = wrapper ? wrapper.getBoundingClientRect().width : window.innerWidth;
            if (!contentWidth) contentWidth = window.innerWidth;
            var drawerWidth = Math.round(contentWidth * 0.8);
            var drawerEl = document.getElementById('kt_similar_filter_drawer');
            if (drawerEl) {
                drawerEl.style.width = drawerWidth + 'px';
            }
        }
        $(document).ready(simfSetDrawerWidth);
        $(window).on('resize', simfSetDrawerWidth);
        $(document).on('click', '#kt_similar_filter_toggle', simfSetDrawerWidth);

        /* Metronic's base .drawer class ships its own (non-!important)
           "overflow: auto" rule, loaded via a <link> that sits after our
           inline <style> block in source order — on ties, later-in-source
           wins, so stylesheet-only overrides have proven unreliable here.
           Setting these as genuine inline !important styles via JS beats
           any stylesheet rule unconditionally, guaranteeing only .simf-body
           scrolls and the header/active-filters/footer stay pinned. */
        function simfEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_similar_filter_drawer');
            var cardEl = document.getElementById('simf_card');
            var bodyEl = cardEl ? cardEl.querySelector('.simf-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.simf-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.simf-footer') : null;
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
            /* Body: absolute-positioned between the header and footer.
               The activebar is now inside the scroll container, so it is
               NOT subtracted from the available height. */
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
        $(document).ready(simfEnforceDrawerLayout);
        $(window).on('resize', simfEnforceDrawerLayout);
        $(document).on('click', '#kt_similar_filter_toggle, #kt_similar_filter_close', simfEnforceDrawerLayout);

        /* ------------------------------------------------------------------
           Filter drawer glue: popular-location quick-add chips, the Active
           Filters chip bar, and the Preview/Reset/Apply footer buttons. None
           of this touches the request params fetchCandidates() sends — it
           only reads/writes the same field IDs the backend already expects.
        ------------------------------------------------------------------ */

        // Popular/other location suggestion chips just add a tag to the
        // existing location Tagify instance — no extra request.
        $(document).on('click', '.simf-loc-chip', function() {
            var city = $(this).data('city');
            var $btn = $(this);
            if (typeof tagifylocation === 'undefined' || !city) return;
            $btn.prop('disabled', true);
            // Resolve through the exact same /city-job-suggestions lookup the
            // real search box uses, so the tag added is the full "City | State |
            // Country" record the backend expects — not just a bare city name
            // that never matches a real city_list entry.
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(city),
                method: 'GET',
                success: function(data) {
                    $btn.prop('disabled', false);
                    if (Array.isArray(data) && data.length > 0) {
                        var match = data.find(function(item) {
                            return String(item).toLowerCase().indexOf(city.toLowerCase()) === 0;
                        }) || data[0];
                        tagifylocation.addTags([match]);
                        simfCloseLocPanel();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        // Location suggestions panel: opens on focus/chevron click, closes once
        // a location is picked (from either the popular chips or the real
        // Tagify autocomplete dropdown) or when clicking outside it.
        function simfOpenLocPanel() {
            $('#simf_loc_panel').removeClass('d-none');
            $('#simf_loc_chevron').addClass('open');
        }
        function simfCloseLocPanel() {
            $('#simf_loc_panel').addClass('d-none');
            $('#simf_loc_chevron').removeClass('open');
        }
        // Tagify hides the original <input> and renders its own contenteditable
        // wrapper, so we hook Tagify's own focus event rather than the hidden
        // input's DOM events.
        $(document).ready(function() {
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.on('focus', simfOpenLocPanel);
            }
        });
        $(document).on('click', '#simf_loc_chevron', function() {
            if ($('#simf_loc_panel').hasClass('d-none')) {
                simfOpenLocPanel();
            } else {
                simfCloseLocPanel();
            }
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#simf_loc_inputwrap, #simf_loc_panel').length) {
                simfCloseLocPanel();
            }
        });

        function simfFieldLabel(name) {
            var labels = {
                min_exp: 'Min Exp', max_exp: 'Max Exp',
                min_sal: 'Min Salary', max_sal: 'Max Salary'
            };
            return labels[name] || name;
        }

        function updateActiveFiltersUI() {
            var chips = [];

            // Skills (starred = mandatory)
            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }

            // Location
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.value.forEach(function(tag) {
                    chips.push({ label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }

            // Education — label is displayed uppercase (education data
            // convention, see docs/UI_UX_STANDARDS.md); val itself stays
            // exact-case since it's what the clear() lookup and the
            // backend payload match against.
            if (typeof simfEducationSelected !== 'undefined') {
                simfEducationSelected.slice().forEach(function(val) {
                    chips.push({
                        label: val.toUpperCase(),
                        clear: function() {
                            $('.simf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }

            // Experience range
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp || maxExp) {
                chips.push({
                    label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }

            // Salary range
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            if (minSal || maxSal) {
                chips.push({
                    label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(''); $('#max_sal').val(''); }
                });
            }

            // Notice / Stage / Activity — only show when not the default "Any"/"All" pill
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                chips.push({
                    label: noticeEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                chips.push({
                    label: statusEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                chips.push({
                    label: activityEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }

            var $chips = $('#simf_active_chips').empty();
            chips.forEach(function(c, idx) {
                var $chip = $('<span class="simf-chip"></span>')
                    .toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').text('★ ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip
                    .append($('<span class="simf-chip-remove">&times;</span>').on('click', function() {
                        c.clear();
                        updateActiveFiltersUI();
                    }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#simf_active_bar').toggleClass('d-none', n === 0);
            $('#simf_active_count').text('(' + n + ')');
            $('#simf_header_count').text(n);
            $('#simf_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#simf_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        /* ------------------------------------------------------------------
           Applied Filters summary card (main page, above the results table).
           Unlike the drawer's own #simf_active_bar (which previews DRAFT
           changes live, before Apply), this card reflects only the
           last-APPLIED state — it's rendered on Apply/Reset and on initial
           load, not on every keystroke inside the drawer. Reads the exact
           same underlying sources as updateActiveFiltersUI() so the two
           never disagree, just grouped/colored by category to match the
           enterprise design instead of one flat list.
        ------------------------------------------------------------------ */
        var SIMF_APPLIED_VISIBLE_CAP = 8;
        var simfAppliedExpanded = false;

        function simfCategoryIcon(cat) {
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

        function simfBuildAppliedItems() {
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
            if (typeof simfEducationSelected !== 'undefined') {
                simfEducationSelected.slice().forEach(function(val) {
                    items.push({
                        cat: 'education', label: val,
                        clear: function() {
                            $('.simf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp || maxExp) {
                items.push({
                    cat: 'experience', label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            if (minSal || maxSal) {
                items.push({
                    cat: 'salary', label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(''); $('#max_sal').val(''); }
                });
            }
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                items.push({
                    cat: 'notice', label: noticeEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                items.push({
                    cat: 'status', label: statusEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                items.push({
                    cat: 'activity', label: activityEl.next('.simf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }
            return items;
        }

        // Removing a chip from this card acts on already-APPLIED filters, so
        // (unlike drawer edits) it re-fetches immediately instead of waiting
        // for an Apply click, and refreshes the drawer's own state/snapshot
        // so reopening the drawer shows the same, now-current picture.
        function simfAppliedChipRemoved() {
            fetchCandidates();
            if (typeof updateActiveFiltersUI === 'function') updateActiveFiltersUI();
            if (typeof simfCaptureSnapshot === 'function') simfCaptureSnapshot();
            renderAppliedFiltersCard();
        }

        function renderAppliedFiltersCard() {
            var items = simfBuildAppliedItems();
            var $chips = $('#simf_applied_chips').empty();
            var visible = simfAppliedExpanded ? items : items.slice(0, SIMF_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="simf-applied-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + simfCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').text('★'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="simf-applied-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        simfAppliedChipRemoved();
                    })
                );
                $chips.append($chip);
            });

            if (!simfAppliedExpanded && items.length > SIMF_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - SIMF_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="simf-applied-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        simfAppliedExpanded = true;
                        renderAppliedFiltersCard();
                    })
                );
            }

            $('#simf_applied_card').toggleClass('d-none', items.length === 0);
            $('#simf_applied_count').text(items.length);
        }

        $(document).on('click', '#simf_applied_clear_all', function() {
            $('#simf_clear_all').trigger('click');
            fetchCandidates();
            if (typeof simfCaptureSnapshot === 'function') simfCaptureSnapshot();
            simfAppliedExpanded = false;
            renderAppliedFiltersCard();
        });

        $(document).on('change', '[name="app_int_notice"], [name="app_int_status"], [name="app_int_activity"]', function() {
            updateActiveFiltersUI();
            // Selecting a Notice/Stage/Activity pill can add a chip to the Active
            // Filters bar, changing its height — re-assert the drawer's layout so
            // the flex column recalculates and the footer/body stay correctly
            // sized instead of drifting after that reflow.
            if (typeof simfEnforceDrawerLayout === 'function') simfEnforceDrawerLayout();
        });
        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', updateActiveFiltersUI);

        $(document).on('click', '#simf_clear_all', function() {
            if (typeof tagify !== 'undefined') tagify.removeAllTags();
            if (typeof tagifylocation !== 'undefined') tagifylocation.removeAllTags();
            $('.simf-edu-check').prop('checked', false);
            $('.simf-edu-check[value=""]').prop('checked', true);
            simfEducationSelected = [];
            simfSyncEducationInput();
            $('#min_exp, #max_exp, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#min_sal').val(0);
            $('#app_int_notice1, #app_int_status0, #app_int_activity1').prop('checked', true);
            updateActiveFiltersUI();
        });

        $(document).on('click', '#simf_reset_btn', function() {
            $('#simf_clear_all').trigger('click');
            fetchCandidates();
            // Reset is an explicit apply of the cleared state, so make the
            // cleared filters the new baseline — otherwise closing the drawer
            // afterwards would "restore" the pre-reset values.
            simfCaptureSnapshot();
            simfAppliedExpanded = false;
            renderAppliedFiltersCard();
        });

        /* ------------------------------------------------------------------
           Draft-until-Apply behaviour.
           Nothing the user changes inside the drawer takes effect until they
           click "Apply Filters". If they close the drawer by any other means
           (Close button, the header X, the overlay, or ESC) their unsaved
           edits are discarded and every field reverts to the last-applied
           state. We snapshot on open (kt.drawer.shown) and restore on close
           (kt.drawer.after.hidden) using Metronic's own drawer lifecycle
           events, so overlay/ESC closes are covered too — not just the
           buttons we control.
        ------------------------------------------------------------------ */
        var simfSnapshot = null;
        var simfApplying = false;

        function simfCaptureSnapshot() {
            simfSnapshot = {
                skills: (typeof tagify !== 'undefined')
                    ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                locations: (typeof tagifylocation !== 'undefined')
                    ? tagifylocation.value.map(function(t) { return t.value; }) : [],
                education: (typeof simfEducationSelected !== 'undefined') ? simfEducationSelected.slice() : [],
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val(),
                notice: $('[name="app_int_notice"]:checked').val(),
                status: $('[name="app_int_status"]:checked').val(),
                activity: $('[name="app_int_activity"]:checked').val()
            };
        }

        function simfRestoreSnapshot() {
            if (!simfSnapshot) return;
            if (typeof tagify !== 'undefined') {
                tagify.removeAllTags();
                if (simfSnapshot.skills.length) tagify.addTags(simfSnapshot.skills);
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.removeAllTags();
                if (simfSnapshot.locations.length) tagifylocation.addTags(simfSnapshot.locations);
            }
            simfEducationSelected = simfSnapshot.education.slice();
            $('.simf-edu-check').prop('checked', false);
            if (simfEducationSelected.length === 0) {
                $('.simf-edu-check[value=""]').prop('checked', true);
            } else {
                simfEducationSelected.forEach(function(v) {
                    $('.simf-edu-check[value="' + String(v).replace(/"/g, '\\"') + '"]').prop('checked', true);
                });
            }
            simfSyncEducationInput();
            $('#min_exp').val(simfSnapshot.min_exp);
            $('#max_exp').val(simfSnapshot.max_exp);
            $('#min_sal').val(simfSnapshot.min_sal);
            $('#max_sal').val(simfSnapshot.max_sal);
            $('#min_salary_currency').val(simfSnapshot.min_salary_currency);
            $('#sal_mode').val(simfSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            if (simfSnapshot.notice) $('[name="app_int_notice"][value="' + simfSnapshot.notice + '"]').prop('checked', true);
            if (simfSnapshot.status) $('[name="app_int_status"][value="' + simfSnapshot.status + '"]').prop('checked', true);
            if (simfSnapshot.activity) $('[name="app_int_activity"][value="' + simfSnapshot.activity + '"]').prop('checked', true);
            updateActiveFiltersUI();
        }

        function simfBindDrawerLifecycle() {
            var el = document.getElementById('kt_similar_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(simfBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst) { return setTimeout(simfBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                simfApplying = false;
                simfCaptureSnapshot();
                // Re-assert the height/scroll layout now that the drawer is
                // actually visible and measurable — this is the reliable moment,
                // more so than the toggle-click (which fires before the open
                // animation lays the element out).
                simfEnforceDrawerLayout();
                setTimeout(simfEnforceDrawerLayout, 50);
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!simfApplying) simfRestoreSnapshot();
                simfApplying = false;
            });
        }
        $(document).ready(simfBindDrawerLifecycle);

        // Close discards unsaved edits (handled by the restore-on-hide hook above);
        // Apply commits them: run the fetch, mark this close as an intentional
        // apply so the hook does NOT revert, then close the drawer.
        $(document).on('click', '#simf_close_btn', function() {
            $('#kt_similar_filter_close').trigger('click');
        });
        $(document).on('click', '#simf_apply_btn', function() {
            simfApplying = true;
            fetchCandidates();
            simfAppliedExpanded = false;
            renderAppliedFiltersCard();
            $('#kt_similar_filter_close').trigger('click');
        });

        $(document).ready(function() {
            setTimeout(updateActiveFiltersUI, 600);
            // Seed a baseline snapshot from the server-provided defaults so a
            // very first close-without-apply reverts correctly even if the
            // shown event hasn't fired yet.
            setTimeout(simfCaptureSnapshot, 800);
            // Reflect any filters that arrived pre-applied (e.g. carried over
            // from the URL/session on first load) in the summary card too.
            setTimeout(renderAppliedFiltersCard, 700);
        });
    </script>
@endpush