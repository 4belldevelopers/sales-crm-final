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
            #kt_activitiesBenchApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesBenchApplication {
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

        /* Website Candidates filter drawer (replaces the old #apptrackerfilter modal).
           Naming convention: wcf-* (mirrors In-house Bench Talents' ibf-*/Agency Bench's
           abf-*/Similar Candidates' simf-* 1:1, renamed so these pages' drawer
           styles/scripts never collide if ever loaded together). */
        #kt_wcf_filter_drawer.wcf-drawer {
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
        #wcf_card.wcf-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .wcf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid #eef0f4;
            flex-shrink: 0;
        }
        .wcf-header-icon {
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
        .wcf-title {
            font-size: 17px;
            font-weight: 700;
            color: #181c32;
            margin: 0;
        }
        .wcf-count-pill {
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
        .wcf-subtitle {
            font-size: 12px;
            color: #7e8299;
            margin: 2px 0 0;
        }
        .wcf-close {
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
        .wcf-close:hover {
            background: #f1f5f9;
            color: #334155;
        }
        .wcf-activebar {
            padding: 12px 0;
            border-bottom: 1px solid #eef0f4;
        }
        .wcf-activebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .wcf-activebar-label {
            font-size: 12px;
            font-weight: 600;
            color: #181c32;
        }
        .wcf-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .wcf-clearall:hover {
            color: #b91c1c;
        }
        .wcf-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .wcf-chip {
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
        .wcf-chip.mandatory {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }
        .wcf-chip-remove {
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
        .wcf-chip-remove:hover {
            background: rgba(0,0,0,.14);
        }
        #wcf_card .wcf-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 4px 20px 16px;
        }
        #wcf_card .wcf-header,
        #wcf_card .wcf-footer {
            flex-shrink: 0 !important;
        }
        .wcf-section {
            padding: 16px 0;
            border-bottom: 1px solid #f1f2f6;
        }
        .wcf-section-last {
            border-bottom: none;
        }
        .wcf-section-head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }
        .wcf-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .wcf-icon-blue { background: #eef2ff; color: #4f46e5; }
        .wcf-icon-green { background: #ecfdf5; color: #059669; }
        .wcf-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .wcf-icon-teal { background: #ecfeff; color: #0e7490; }
        .wcf-icon-amber { background: #fffbeb; color: #b45309; }
        .wcf-icon-rose { background: #fff1f2; color: #be123c; }
        .wcf-icon-pink { background: #fdf2f8; color: #be185d; }
        .wcf-icon-sky { background: #f0f9ff; color: #0369a1; }
        .wcf-icon-slate { background: #f1f5f9; color: #475569; }
        .wcf-section-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #181c32;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .wcf-section-desc {
            font-size: 11.5px;
            color: #99a1b7;
            margin-top: 1px;
        }
        .wcf-badge-primary {
            font-size: 9.5px;
            font-weight: 600;
            color: #2563eb;
            background: #dbeafe;
            border-radius: 4px;
            padding: 1px 6px;
        }
        .wcf-mini-label {
            font-size: 11px;
            font-weight: 500;
            color: #7e8299;
            margin-bottom: 4px;
            display: block;
        }
        .wcf-salary-row {
            flex-wrap: nowrap;
        }
        .wcf-salary-row > [class^="col-"] {
            padding-left: 4px;
            padding-right: 4px;
            min-width: 0;
        }
        .wcf-salary-row .form-select,
        .wcf-salary-row .form-control {
            font-size: 11px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .wcf-salary-row .form-select {
            padding-right: 20px;
        }

        /* Education multiselect dropdown */
        .wcf-select-dd {
            position: relative;
        }
        .wcf-select-dd-btn {
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
        .wcf-select-dd-btn span {
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
        .wcf-edu-name {
            text-transform: uppercase;
        }
        .wcf-select-dd-btn svg {
            flex-shrink: 0;
            color: #99a1b7;
        }
        .wcf-select-dd-panel {
            margin-top: 6px;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }
        .wcf-select-dd-item {
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
        .wcf-select-dd-item:last-child {
            border-bottom: none;
        }
        .wcf-select-dd-item:hover {
            background: #f8f9fb;
        }
        .wcf-select-dd-item input {
            accent-color: #2563eb;
        }
        .wcf-loc-inputwrap {
            position: relative;
        }
        .wcf-loc-chevron {
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
        .wcf-loc-chevron.open {
            transform: translateY(-50%) rotate(180deg);
        }
        .wcf-loc-suggestions {
            margin-top: 10px;
            border: 1px solid #eef0f4;
            border-radius: 8px;
            padding: 10px;
            background: #fafbfc;
        }
        .wcf-loc-label {
            font-size: 11px;
            font-weight: 600;
            color: #99a1b7;
            margin-bottom: 6px;
        }
        .wcf-loc-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 4px;
        }
        .wcf-loc-chip {
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
        .wcf-loc-chip:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .wcf-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .wcf-pill-row .btn-check {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .wcf-pill {
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
        .wcf-pill-row .btn-check:checked + .wcf-pill {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .wcf-footer {
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
        .wcf-footer-count {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .wcf-footer-icon {
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
        .wcf-footer-number {
            font-size: 15px;
            font-weight: 700;
            color: #181c32;
            line-height: 1.2;
        }
        .wcf-footer-label {
            font-size: 10.5px;
            color: #7e8299;
        }
        .wcf-footer-hint {
            cursor: help;
        }
        .wcf-footer-actions {
            display: flex;
            gap: 6px;
        }
        .wcf-footer-actions .btn {
            font-size: 11.5px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 576px) {
            .wcf-footer { flex-direction: column; align-items: stretch; }
            .wcf-footer-actions { justify-content: stretch; }
            .wcf-footer-actions .btn { flex: 1 1 auto; justify-content: center; }
        }

        /* ==================================================================
           Toolbar redesign: search / sort / filter.
           #kt_tagify_2 is a plain free-text field (candidate name search) —
           it no longer opens a modal or gets Tagify-wrapped; the Filter
           button is the drawer's only entry point now.
        ================================================================== */
        .wcf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .wcf-search-wrap,
        .wcf-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .wcf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .wcf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .wcf-search-icon,
        .wcf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wcf-search-icon svg,
        .wcf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .wcf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        /* Custom Sort control — the real <select id="sortingfilter"> stays
           hidden underneath purely so filtersdataget()/fetchCandidates() keep
           reading it unchanged; this button + panel only replaces how the
           option list is PRESENTED. */
        .wcf-sort-btn {
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
        .wcf-sort-btn:focus { outline: none; }
        .wcf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .wcf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .wcf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .wcf-sort-panel {
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
        .wcf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .wcf-sort-item:hover {
            background: #f8f9fb;
        }
        .wcf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .wcf-filter-btn {
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
        .wcf-filter-btn:hover {
            background: #1d4ed8 !important;
        }
        @media (max-width: 992px) {
            .wcf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .wcf-search-wrap { flex-basis: 100%; }
            .wcf-sort-wrap { width: 140px; }
        }

        /* ==================================================================
           Applied Filters summary card — lives on the main page above the
           results table (NOT inside the drawer), hidden until at least one
           filter has actually been applied via "Apply Filters".
        ================================================================== */
        .wcf-applied-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            background: #fff;
            padding: 16px 18px;
            margin-bottom: 16px;
            font-family: 'Poppins', sans-serif;
        }
        .wcf-applied-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .wcf-applied-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #181c32;
        }
        .wcf-applied-count {
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
        .wcf-applied-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .wcf-applied-clearall:hover { color: #b91c1c; }
        .wcf-applied-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .wcf-applied-chip {
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
        .wcf-applied-chip .ic { display: inline-flex; align-items: center; flex-shrink: 0; }
        .wcf-applied-chip .star { font-size: 12px; line-height: 1; }
        .wcf-applied-chip-remove {
            cursor: pointer;
            opacity: .55;
            display: inline-flex;
            align-items: center;
            margin-left: 2px;
        }
        .wcf-applied-chip-remove:hover { opacity: 1; }
        .wcf-applied-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .wcf-applied-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .wcf-applied-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .wcf-applied-chip.cat-company    { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .wcf-applied-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .wcf-applied-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .wcf-applied-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .wcf-applied-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
        .wcf-applied-chip.cat-hiring     { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
        .wcf-applied-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .wcf-applied-more {
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
        .wcf-applied-more:hover { background: #e5e7eb; }
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
         @php
          use App\Helper\GeneralMethod;
            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );

            // This route is shared by both flows (an agency's own jobs AND jobs a
            // direct client shared with this company), but the two flows have
            // completely different sibling tabs — a direct-client job only has
            // Total/Similar/In-house Bench/Website Candidates (the "d"-prefixed
            // routes + clientappHeader), it has no Agency Bench or Bench Talent
            // Request tabs at all. Reaching this page from a client job used to
            // always render the full 6-tab apptrackerHeader with own-job URLs,
            // which 404s the moment "Agency Bench Talents"/"Bench Talent Requests"
            // is clicked for a job this company doesn't own. Same ownership check
            // toolbar.blade.php already uses for the Internal/Client tag.
            $isClientJob = @$job->company_id != auth()->user()->company_id;

            if ($isClientJob) {
                $req_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt(@$job->id) . '/all');
                $internal_bench_path = url(
                    '/service_company/apptracker/dinternal_bench/' . GeneralMethod::value_encrypt(@$job->id),
                );
                $existing_path = url(
                    '/service_company/apptracker/dsimilar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
                );

                $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'career_active'=>'tabactive',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    ];
            } else {
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
                $candidates_request_path = route('service_company.apptracker.candidates_request');

                $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'',
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
                    'career_active'=>'tabactive',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    'public_bench_counter' =>@$public_bench_counter,
                    'recomnded_counter' => @$recomnded_counter,
                    ];
            }

        @endphp
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
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
                    @if ($isClientJob)
                        @include('sections.service_company.clientappHeader',['tabactive'=>$tabactive])
                    @else
                        @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])
                    @endif
                </div>

                <div class="card mb-4 wcf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="wcf-search-wrap" style="position: relative;">
                                <span class="wcf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control wcf-search-input" name="inputJobSearch"
                                    placeholder="Search by candidate name, skills, education or location..." id="kt_tagify_2" autocomplete="off" />
                            </div>
                            <div class="wcf-sort-wrap" id="wcf_sort_wrap">
                                <span class="wcf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="wcf-sort-btn" id="wcf_sort_btn">
                                    <span id="wcf_sort_label">Best Match</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="Show All">Sort</option>
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
                                <div class="wcf-sort-panel d-none" id="wcf_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_wcf_filter_toggle"
                                class="btn wcf-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="wcf_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary wcf-count-badge d-none" id="wcf_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!--begin::Applied Filters summary card — hidden until Apply Filters is clicked-->
                <div class="wcf-applied-card d-none" id="wcf_applied_card">
                    <div class="wcf-applied-head">
                        <span class="wcf-applied-title">
                            Active Filters
                            <span class="wcf-applied-count" id="wcf_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="wcf-applied-clearall" id="wcf_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="wcf-applied-chips" id="wcf_applied_chips"></div>
                </div>
                <!--end::Applied Filters summary card-->

                <!--Start Filter Drawer-->
                <div id="kt_wcf_filter_drawer" class="bg-body wcf-drawer" data-kt-drawer="true" data-kt-drawer-name="career-apply-filter"
                    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_wcf_filter_toggle" data-kt-drawer-close="#kt_wcf_filter_close">
                    <div id="wcf_card" class="card shadow-none rounded-0 w-100 h-100 wcf-card">
                        <!--begin::Header-->
                        <div class="wcf-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="wcf-header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="wcf-title">Filter Candidates</h3>
                                        <span class="wcf-count-pill" id="wcf_header_count">0</span>
                                    </div>
                                    <p class="wcf-subtitle">Refine your search across website candidates for this job</p>
                                </div>
                            </div>
                            <button type="button" class="wcf-close" id="kt_wcf_filter_close" title="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="wcf-body">
                            <form id="createSchedule" class="ajax-form" method="post">

                                <!--begin::Active filters — inside scroll area so it flows with the sections -->
                                <div class="wcf-activebar d-none" id="wcf_active_bar">
                                    <div class="wcf-activebar-head">
                                        <span class="wcf-activebar-label">Active Filters <span id="wcf_active_count">(0)</span></span>
                                        <a href="javascript:void(0);" class="wcf-clearall" id="wcf_clear_all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                            Clear All
                                        </a>
                                    </div>
                                    <div class="wcf-chips" id="wcf_active_chips"></div>
                                </div>
                                <!--end::Active filters-->

                                <!--begin::Skills-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Skills <span class="wcf-badge-primary">Primary</span></div>
                                            <div class="wcf-section-desc">Add skills to find relevant candidates — click ★ to make a skill mandatory</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
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
                                                        const mandatoryClass = tagData.checked ? 'wcf-mandatory-tag' : '';
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
                                                    tagElm.classList.toggle('wcf-mandatory-tag', nowChecked);

                                                    const tagData = tagify.value.find(tag => tag.value === value);
                                                    if (tagData) {
                                                        setTimeout(() => {
                                                            tagData.checked = nowChecked;
                                                            tagify.updateValueByDOMTags();
                                                            wcfUpdateActiveFiltersUI();
                                                        }, 500);
                                                    }
                                                }
                                            });

                                            // #kt_tagify_2 (the top toolbar's search box) is a plain
                                            // free-text field, deliberately NOT Tagify-wrapped and NOT
                                            // synced with the drawer's Skills tagify.

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
                                            tagify.on('add', wcfUpdateActiveFiltersUI);
                                            tagify.on('remove', wcfUpdateActiveFiltersUI);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Skills-->

                                <!--begin::Location-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Current Location</div>
                                            <div class="wcf-section-desc">Current location of candidate</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="wcf-loc-inputwrap" id="wcf_loc_inputwrap">
                                            <input class="form-control form-control-solid"
                                                value="{{ $default_loc }}" id="kt_tagify_location"
                                                placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
                                            <button type="button" class="wcf-loc-chevron" id="wcf_loc_chevron" title="Show suggestions">
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
                                                    /* appendTarget prevents the dropdown from being clipped
                                                       by .wcf-body's overflow:hidden/auto — see the matching
                                                       comment in similar_candidates.blade.php. */
                                                    appendTarget: document.body
                                                }
                                            });
                                            // Create tags only on select
                                            tagifylocation.on('dropdown:select', function(e) {
                                                tagifylocation.addTags([e.detail.data.value]);
                                                wcfCloseLocPanel();
                                            });
                                            tagifylocation.on('add', function(e) {
                                                wcfUpdateActiveFiltersUI();
                                                wcfCloseLocPanel();
                                            });
                                            tagifylocation.on('remove', function(e) {
                                                wcfUpdateActiveFiltersUI();
                                            });

                                            function debounce(func, delay) {
                                                let timer;
                                                return function (...args) {
                                                    clearTimeout(timer);
                                                    timer = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            // Debounced function to fetch suggestions.
                                            const fetchSuggestions = debounce(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            tagifylocation.settings.whitelist = data;
                                                            wcfCloseLocPanel();
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
                                                wcfCloseLocPanel();
                                                fetchSuggestions(query);
                                            });

                                            setTimeout(() => {
                                                var city = "{{ @$default_loc }}";
                                                if (city !== '') {
                                                    var cityName = city.split(" | ")[0];
                                                    fetchSuggestions(cityName);
                                                }
                                            }, 100);
                                            setTimeout(() => {
                                                var defaultLocation = {!! json_encode(@$default_loc) !!};

                                                if (defaultLocation) {
                                                    tagifylocation.addTags([defaultLocation]);
                                                    tagifylocation.dropdown.hide();
                                                }
                                                wcfUpdateActiveFiltersUI();
                                            }, 5000);
                                        </script>

                                        <div class="d-flex mt-3">
                                            <div class="fw-semibold me-5 d-flex">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="communication[]" value="email" checked="checked">
                                                <div class="fs-7 text-muted">Include candidates who prefer to relocate</div>
                                            </div>
                                        </div>

                                        <div class="wcf-loc-suggestions d-none" id="wcf_loc_panel">
                                            <div class="wcf-loc-label">Popular Locations</div>
                                            <div class="wcf-loc-chip-row">
                                                <button type="button" class="wcf-loc-chip" data-city="Bangalore">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    Bangalore
                                                </button>
                                                <button type="button" class="wcf-loc-chip" data-city="Hyderabad">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                                                    Hyderabad
                                                </button>
                                                <button type="button" class="wcf-loc-chip" data-city="Pune">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 21V10l6-4 6 4v11M9 21v-5h6v5"/></svg>
                                                    Pune
                                                </button>
                                                <button type="button" class="wcf-loc-chip" data-city="Mumbai">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 21v-4h6v4"/></svg>
                                                    Mumbai
                                                </button>
                                                <button type="button" class="wcf-loc-chip" data-city="Chennai">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V9l8-6 8 6v12M10 21v-6h4v6"/></svg>
                                                    Chennai
                                                </button>
                                                <button type="button" class="wcf-loc-chip" data-city="Remote">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>
                                                    Remote
                                                </button>
                                            </div>
                                            <div class="wcf-loc-label mt-2">Other Suggestions</div>
                                            <div class="wcf-loc-chip-row">
                                                <button type="button" class="wcf-loc-chip" data-city="Noida">Noida</button>
                                                <button type="button" class="wcf-loc-chip" data-city="Gurgaon">Gurgaon</button>
                                                <button type="button" class="wcf-loc-chip" data-city="Kolkata">Kolkata</button>
                                                <button type="button" class="wcf-loc-chip" data-city="Ahmedabad">Ahmedabad</button>
                                                <button type="button" class="wcf-loc-chip" data-city="Chandigarh">Chandigarh</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--end::Location-->

                                <!--begin::Education-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Education</div>
                                            <div class="wcf-section-desc">Highest qualification</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <input type="hidden" id="kt_tagify_education" value="{{ $qualification }}" />
                                        <div class="wcf-select-dd" id="wcf_edu_dd">
                                            <button type="button" class="wcf-select-dd-btn" id="wcf_edu_dd_btn">
                                                <span id="wcf_edu_dd_label">Any Education</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div class="wcf-select-dd-panel d-none" id="wcf_edu_dd_panel">
                                                <label class="wcf-select-dd-item">
                                                    <input type="checkbox" class="wcf-edu-check" value="" checked>
                                                    <span>Any</span>
                                                </label>
                                                @foreach ($qualificationTag as $qval)
                                                    <label class="wcf-select-dd-item">
                                                        <input type="checkbox" class="wcf-edu-check" value="{{ $qval }}">
                                                        <span class="wcf-edu-name">{{ $qval }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <script>
                                            // Drives the same #kt_tagify_education hidden field the backend
                                            // already reads (JSON array of {value:"..."} objects, matched
                                            // against qualification_master.name).
                                            var wcfEducationSelected = (function() {
                                                var initial = @json($qualification ? [$qualification] : []);
                                                return initial.filter(Boolean);
                                            })();

                                            function wcfSyncEducationInput() {
                                                var payload = wcfEducationSelected.map(function(v) { return { value: v }; });
                                                $('#kt_tagify_education').val(JSON.stringify(payload));
                                                $('#wcf_edu_dd_label').text(wcfEducationSelected.length ? wcfEducationSelected.join(', ') : 'Any Education');
                                                wcfUpdateActiveFiltersUI();
                                            }

                                            $(document).on('change', '.wcf-edu-check', function() {
                                                var val = $(this).val();
                                                if (val === '') {
                                                    if ($(this).is(':checked')) {
                                                        $('.wcf-edu-check').not(this).prop('checked', false);
                                                        wcfEducationSelected = [];
                                                    }
                                                } else {
                                                    $('.wcf-edu-check[value=""]').prop('checked', false);
                                                    if ($(this).is(':checked')) {
                                                        if (wcfEducationSelected.indexOf(val) === -1) wcfEducationSelected.push(val);
                                                    } else {
                                                        wcfEducationSelected = wcfEducationSelected.filter(function(v) { return v !== val; });
                                                        if (wcfEducationSelected.length === 0) $('.wcf-edu-check[value=""]').prop('checked', true);
                                                    }
                                                }
                                                wcfSyncEducationInput();
                                            });

                                            $(document).on('click', '#wcf_edu_dd_btn', function() {
                                                $('#wcf_edu_dd_panel').toggleClass('d-none');
                                            });
                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#wcf_edu_dd').length) {
                                                    $('#wcf_edu_dd_panel').addClass('d-none');
                                                }
                                            });

                                            $(document).ready(wcfSyncEducationInput);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Education-->

                                <!--begin::Company Name-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-teal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Company Name</div>
                                            <div class="wcf-section-desc">Current or past employer</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <input type="text" id="kt_tagify_company"
                                            class="form-control form-control-solid"
                                            placeholder="Enter company name..." name="company_name"
                                            value="">
                                        <script>
                                            var inputdcompany = document.querySelector("#kt_tagify_company");
                                            var tagifyCompany = new Tagify(inputdcompany, {
                                                whitelist: [""],
                                                maxTags: 10,
                                                dropdown: {
                                                    maxItems: 20,
                                                    classname: "",
                                                    enabled: 0,
                                                    closeOnSelect: false
                                                }
                                            });
                                            tagifyCompany.on('add', wcfUpdateActiveFiltersUI);
                                            tagifyCompany.on('remove', wcfUpdateActiveFiltersUI);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Company Name-->

                                <!--begin::Experience-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Experience (Years)</div>
                                            <div class="wcf-section-desc">Total work experience</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="wcf-mini-label">Minimum Experience</label>
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
                                                <label class="wcf-mini-label">Maximum Experience</label>
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
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-amber">
                                            <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Salary (CTC)</div>
                                            <div class="wcf-section-desc">Salary range with currency &amp; mode</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="row g-2 wcf-salary-row">
                                            <div class="col-3">
                                                <label class="wcf-mini-label">Currency</label>
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
                                                <label class="wcf-mini-label">Mode</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                    <option value="">Select Mode</option>
                                                    <option value="0" {{ 0 == $min_sal_mode ? 'selected' : '' }}>Annual</option>
                                                    <option value="1" {{ 1 == $min_sal_mode ? 'selected' : '' }}>Monthly</option>
                                                    <option value="2" {{ 2 == $min_sal_mode ? 'selected' : '' }}>Hourly</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="wcf-mini-label">Min Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="0">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted minimum_salary_word"></span>
                                            </div>
                                            <div class="col-3">
                                                <label class="wcf-mini-label">Max Salary</label>
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
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-rose">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Notice Period / Availability</div>
                                            <div class="wcf-section-desc">Time to join</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="wcf-pill-row" data-radio-group="app_int_notice">
                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="1" id="app_int_notice1" checked>
                                            <label class="wcf-pill" for="app_int_notice1">Any</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="8" id="app_int_notice8">
                                            <label class="wcf-pill" for="app_int_notice8">Immediate</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="2" id="app_int_notice2">
                                            <label class="wcf-pill" for="app_int_notice2">0-15 Days</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="3" id="app_int_notice3">
                                            <label class="wcf-pill" for="app_int_notice3">1 Month</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="4" id="app_int_notice4">
                                            <label class="wcf-pill" for="app_int_notice4">2 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="5" id="app_int_notice5">
                                            <label class="wcf-pill" for="app_int_notice5">3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="6" id="app_int_notice6">
                                            <label class="wcf-pill" for="app_int_notice6">More than 3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="9" id="app_int_notice9">
                                            <label class="wcf-pill" for="app_int_notice9">Served Notice Period</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="7" id="app_int_notice7">
                                            <label class="wcf-pill" for="app_int_notice7">Currently Serving Notice</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Notice Period-->

                                <!--begin::Stages-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-pink">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Stages</div>
                                            <div class="wcf-section-desc">Current stage in your pipeline</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="wcf-pill-row" data-radio-group="app_int_status">
                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="0" id="app_int_status0" checked>
                                            <label class="wcf-pill" for="app_int_status0">All</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="8" id="app_int_status8">
                                            <label class="wcf-pill" for="app_int_status8">Inreview</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="1" id="app_int_status1">
                                            <label class="wcf-pill" for="app_int_status1">Sourcing</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="2" id="app_int_status2">
                                            <label class="wcf-pill" for="app_int_status2">Screening</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="3" id="app_int_status3">
                                            <label class="wcf-pill" for="app_int_status3">Evaluation</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="5" id="app_int_status5">
                                            <label class="wcf-pill" for="app_int_status5">Offer</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="4" id="app_int_status4">
                                            <label class="wcf-pill" for="app_int_status4">Negotiation</label>

                                            <input type="radio" class="btn-check app_int_status" name="app_int_status" value="7" id="app_int_status7">
                                            <label class="wcf-pill" for="app_int_status7">Rejected</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Stages-->

                                <!--begin::Hiring Type-->
                                <div class="wcf-section">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-slate">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Hiring Type</div>
                                            <div class="wcf-section-desc">Fulltime or contract engagement</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="wcf-pill-row" data-radio-group="app_int_outsource">
                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource1" checked>
                                            <label class="wcf-pill" for="app_int_outsource1">All</label>

                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource2">
                                            <label class="wcf-pill" for="app_int_outsource2">Fulltime</label>

                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="2" id="app_int_outsource3">
                                            <label class="wcf-pill" for="app_int_outsource3">Contract</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Hiring Type-->

                                <!--begin::Activity-->
                                <div class="wcf-section wcf-section-last">
                                    <div class="wcf-section-head">
                                        <span class="wcf-icon wcf-icon-sky">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                                        </span>
                                        <div>
                                            <div class="wcf-section-title">Activity</div>
                                            <div class="wcf-section-desc">Last active on JeroATS</div>
                                        </div>
                                    </div>
                                    <div class="wcf-section-body">
                                        <div class="wcf-pill-row" data-radio-group="app_int_activity">
                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                                            <label class="wcf-pill" for="app_int_activity1">Any</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                            <label class="wcf-pill" for="app_int_activity2">30 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                            <label class="wcf-pill" for="app_int_activity3">60 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                            <label class="wcf-pill" for="app_int_activity4">90 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                            <label class="wcf-pill" for="app_int_activity5">180 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                            <label class="wcf-pill" for="app_int_activity6">1 Year</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Activity-->

                            </form>
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="wcf-footer">
                            <div class="wcf-footer-count">
                                <span class="wcf-footer-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </span>
                                <div>
                                    <div class="wcf-footer-number" id="wcf_footer_number">{{ @$career_counter ?? 0 }}</div>
                                    <div class="wcf-footer-label">Candidates Match <span class="wcf-footer-hint" title="Updates after Apply">ⓘ</span></div>
                                </div>
                            </div>
                            <div class="wcf-footer-actions">
                                <button type="button" class="btn btn-sm btn-light" id="wcf_close_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    Close
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="wcf_reset_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                    Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="wcf_apply_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
                <!--End Filter Drawer-->

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5" style="position: relative;">
                      <!-- Circle loader only (Bootstrap/Metronic's own spinner-border) —
                           position:fixed + viewport-centered so it stays centered on
                           screen regardless of scroll position or row count, same
                           pattern as Similar Candidates'/In-house Bench Talents'/Agency
                           Bench Talents' filter drawers. Reuses the existing
                           .loadingMessage class that fetchCandidates() already toggles —
                           just upgrades the markup, no duplicate loader added. -->
                      <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                          <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                      </div>
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
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Availability</th>
                                    <th class="">Location</th>
                                    {{-- <th class="">Type</th> --}}
                                    <!--<th class="">Offer by</th>-->
                                    <th class="">Last Active</th>
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
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
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
                // alert("dkd");
                var number = parseInt($('#min_sal').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));

            });
            $(document).on('keyup', '#max_sal', function() {
                var number = parseInt($('#max_sal').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));

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
    </script>
    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');
        }, 500);

        function filtersdataget() {
            fetchCandidates();
        }

        function tableLoad(type) {
            var jobid = '{{ $job->id }}';
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
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();

            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.career_apply_data_ajax') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&kt_tagify_company=' + kt_tagify_company +
                    '&app_int_notice=' + app_int_notice + '&sal_mode=' + sal_mode + '&app_int_outsource=' +
                    app_int_outsource,
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
                        width: '17%'
                    },
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
                        width: '5%'
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false,
                        width: '5%'
                    },
                    // {data: 'Type', name: 'Type', orderable : false},
                    // 		{data: 'OfferBy', name: 'OfferBy', orderable: false},
                    {
                        data: 'LastActive',
                        name: 'LastActive',
                        orderable: false,
                        width: '10%'
                    },

                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false,
                        width: '15%'
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
                $('.internal_bench_data').text(existingDataCount);


            }, 2500);
        }
    </script>
    <script>
        // Small top-right toast, auto-dismissing, pause-on-hover — same
        // SweetAlert2 toast mixin used on Similar Candidates/In-house Bench/
        // Agency Bench. This page had no toast mixin at all before; every
        // add_to_inreview notice used a blocking Swal.fire() modal instead.
        const careerToast = Swal.mixin({
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
        // pipeline (see career_apply_ajax.blade.php's $existingCandidatesQuery
        // branch) — rather than re-fetching the list (which used to make it
        // vanish immediately via fetchCandidates(page)). The row only
        // actually disappears once the list is genuinely re-fetched
        // (pagination, filter, sort, or a real page reload). This page's
        // button isn't wrapped in its own div (unlike the other bench
        // pages), so the anchor itself is replaced in place.
        function careerMarkCandidateRowAdded($btn) {
            $btn.replaceWith('<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a>');
        }

        $(document).on('click', '.add_to_inreview', function(e) {
            e.preventDefault();

            var $btn = $(this);

            // 🔒 Prevent double click
            if ($btn.hasClass('processing')) {
                return false;
            }

            var id = $btn.data('row-id');
            var source_from_type = $btn.data('row-source_from_type');
            var source_jobid = $btn.data('row-jobid');

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

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'id': id,
                    'source_from_type': source_from_type,
                    'source_jobid': source_jobid
                },
                success: function(response) {
                    if (response.status == 'coinCheck') {
                        restoreButton();
                        careerToast.fire({ icon: 'warning', title: response.message });
                        return false;
                    }
                    if (response.status == 'success') {
                        careerMarkCandidateRowAdded($btn);
                        careerToast.fire({ icon: 'success', title: 'Selected request has been In-Review' });
                    } else {
                        restoreButton();
                    }
                },
                error: function() {
                    restoreButton();
                    careerToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
                }
            });
        });
        $(document).ready(function() {
            // $.noConflict();

            setTimeout(() => {
                fetchCandidates();
            }, 200);


            $(document).on('click', '#paginationcandidate .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcandidate .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcandidate .page-link').removeClass('disabled');
                });
            });


        });

        function fetchCandidates(page = 1, search = '') {
            var jobid = '{{ $job->id }}';
            // #inputSearch never existed as a visible field on this page — the
            // real search box is #kt_tagify_2, same fix as Similar Candidates
            // / In-house Bench Talents / Agency Bench Talents.
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
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();


            $('.loadingMessage').css({ display: 'flex' });
            return $.ajax({
                url: `{!! route('service_company.apptracker.career_apply_data_ajax') !!}?page=${page}`,
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
                kt_tagify_company:kt_tagify_company,
                app_int_notice:app_int_notice,
                sal_mode:sal_mode,
                app_int_outsource:app_int_outsource
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    if (!response || typeof response.html === 'undefined') {
                        console.error('career_apply_data_ajax: unexpected response', response);
                        return;
                    }
                    $('#candidatetList').html(response.html);
                    // Was '#inhouseBench_counter' (copy-paste from In-house Bench) —
                    // this page's own tab counter element is '#career_counter'
                    // (see sections/service_company/apptrackerHeader.blade.php's
                    // 'career_id'=>'career_counter' mapping).
                    $('#career_counter').html(response.total);
                    $('#wcf_footer_number').text(response.total);
                    $('#paginationcandidate').html(response.pagination);
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    console.error('career_apply_data_ajax failed:', xhr.status, xhr.responseText);
                }
            });
        }

        // Free-text search box: Enter re-runs the same fetchCandidates() call
        // the sort dropdown and pagination already use.
        $(document).on('keydown', '#kt_tagify_2', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                fetchCandidates();
            }
        });

        // Custom Sort dropdown — reads options straight from the real
        // #sortingfilter <select> (single source of truth), keeps that
        // select's value in sync so filtersdataget()/fetchCandidates() are
        // unaffected, and only replaces how the option list is PRESENTED.
        function wcfRenderSortPanel() {
            var $panel = $('#wcf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="wcf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#wcf_sort_label').text(label);
                        wcfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function wcfOpenSortPanel() {
            wcfRenderSortPanel();
            $('#wcf_sort_panel').removeClass('d-none');
            $('#wcf_sort_btn').addClass('open');
        }
        function wcfCloseSortPanel() {
            $('#wcf_sort_panel').addClass('d-none');
            $('#wcf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#wcf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#wcf_sort_panel').hasClass('d-none')) wcfOpenSortPanel();
            else wcfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#wcf_sort_wrap').length) wcfCloseSortPanel();
        });
    </script>
    <script>
        /* Drawer width = 80% of the actual content area, same measurement
           approach as Similar Candidates'/In-house Bench Talents'/Agency
           Bench Talents' filter drawers. */
        function wcfSetDrawerWidth() {
            var wrapper = document.getElementById('kt_wrapper');
            var contentWidth = wrapper ? wrapper.getBoundingClientRect().width : window.innerWidth;
            if (!contentWidth) contentWidth = window.innerWidth;
            var drawerWidth = Math.round(contentWidth * 0.8);
            var drawerEl = document.getElementById('kt_wcf_filter_drawer');
            if (drawerEl) {
                drawerEl.style.width = drawerWidth + 'px';
            }
        }
        $(document).ready(wcfSetDrawerWidth);
        $(window).on('resize', wcfSetDrawerWidth);
        $(document).on('click', '#kt_wcf_filter_toggle', wcfSetDrawerWidth);

        /* Forces the drawer's header/body/footer into a fixed viewport-bound
           box via genuine inline !important styles — see the matching
           comment in similar_candidates.blade.php for why stylesheet-only
           overrides aren't reliable here (Metronic's base .drawer class ships
           its own non-!important "overflow: auto" loaded after this page's
           inline <style> block). */
        function wcfEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_wcf_filter_drawer');
            var cardEl = document.getElementById('wcf_card');
            var bodyEl = cardEl ? cardEl.querySelector('.wcf-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.wcf-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.wcf-footer') : null;
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
        $(document).ready(wcfEnforceDrawerLayout);
        $(window).on('resize', wcfEnforceDrawerLayout);
        $(document).on('click', '#kt_wcf_filter_toggle, #kt_wcf_filter_close', wcfEnforceDrawerLayout);

        /* ------------------------------------------------------------------
           Filter drawer glue: popular-location quick-add chips, the Active
           Filters chip bar, and the Close/Reset/Apply footer buttons.
        ------------------------------------------------------------------ */
        $(document).on('click', '.wcf-loc-chip', function() {
            var city = $(this).data('city');
            var $btn = $(this);
            if (typeof tagifylocation === 'undefined' || !city) return;
            $btn.prop('disabled', true);
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
                        wcfCloseLocPanel();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        function wcfOpenLocPanel() {
            $('#wcf_loc_panel').removeClass('d-none');
            $('#wcf_loc_chevron').addClass('open');
        }
        function wcfCloseLocPanel() {
            $('#wcf_loc_panel').addClass('d-none');
            $('#wcf_loc_chevron').removeClass('open');
        }
        $(document).ready(function() {
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.on('focus', wcfOpenLocPanel);
            }
        });
        $(document).on('click', '#wcf_loc_chevron', function() {
            if ($('#wcf_loc_panel').hasClass('d-none')) {
                wcfOpenLocPanel();
            } else {
                wcfCloseLocPanel();
            }
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#wcf_loc_inputwrap, #wcf_loc_panel').length) {
                wcfCloseLocPanel();
            }
        });

        function wcfUpdateActiveFiltersUI() {
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

            if (typeof wcfEducationSelected !== 'undefined') {
                wcfEducationSelected.slice().forEach(function(val) {
                    chips.push({
                        label: val.toUpperCase(),
                        clear: function() {
                            $('.wcf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }

            if (typeof tagifyCompany !== 'undefined') {
                tagifyCompany.value.forEach(function(tag) {
                    chips.push({ label: tag.value, clear: function() { tagifyCompany.removeTags(tag.value); } });
                });
            }

            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            var minExpChanged = minExp !== '' && minExp !== wcfDefaultRange.minExp;
            var maxExpChanged = maxExp !== '' && maxExp !== wcfDefaultRange.maxExp;
            if (minExpChanged || maxExpChanged) {
                chips.push({
                    label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(wcfDefaultRange.minExp); $('#max_exp').val(wcfDefaultRange.maxExp); }
                });
            }

            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var minSalChanged = minSal !== '' && minSal !== wcfDefaultRange.minSal;
            var maxSalChanged = maxSal !== '' && maxSal !== wcfDefaultRange.maxSal;
            if (minSalChanged || maxSalChanged) {
                chips.push({
                    label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(wcfDefaultRange.minSal); $('#max_sal').val(wcfDefaultRange.maxSal); }
                });
            }

            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                chips.push({
                    label: noticeEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                chips.push({
                    label: statusEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var outsourceEl = $('[name="app_int_outsource"]:checked');
            if (outsourceEl.val() && outsourceEl.val() !== '0') {
                chips.push({
                    label: outsourceEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_outsource1').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                chips.push({
                    label: activityEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }

            var $chips = $('#wcf_active_chips').empty();
            chips.forEach(function(c, idx) {
                var $chip = $('<span class="wcf-chip"></span>')
                    .toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').text('★ ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip
                    .append($('<span class="wcf-chip-remove">&times;</span>').on('click', function() {
                        c.clear();
                        wcfUpdateActiveFiltersUI();
                    }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#wcf_active_bar').toggleClass('d-none', n === 0);
            $('#wcf_active_count').text('(' + n + ')');
            $('#wcf_header_count').text(n);
            $('#wcf_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#wcf_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        /* ------------------------------------------------------------------
           Applied Filters summary card (main page, above the results table).
           Reflects only the last-APPLIED state, rendered on Apply/Reset and
           on initial load — reads the same underlying sources as
           wcfUpdateActiveFiltersUI() so the two never disagree.
        ------------------------------------------------------------------ */
        var WCF_APPLIED_VISIBLE_CAP = 8;
        var wcfAppliedExpanded = false;

        function wcfCategoryIcon(cat) {
            var paths = {
                skill: '<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>',
                location: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                education: '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/>',
                company: '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/>',
                experience: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                notice: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
                status: '<circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/>',
                hiring: '<line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/>',
                activity: '<path d="M3 3v18h18M7 14l4-4 3 3 5-6"/>'
            };
            if (cat === 'salary') {
                return '<span style="font-size:11px;font-weight:700;line-height:1;">₹</span>';
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' + (paths[cat] || '') + '</svg>';
        }

        function wcfBuildAppliedItems() {
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
            if (typeof wcfEducationSelected !== 'undefined') {
                wcfEducationSelected.slice().forEach(function(val) {
                    items.push({
                        cat: 'education', label: val,
                        clear: function() {
                            $('.wcf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }
            if (typeof tagifyCompany !== 'undefined') {
                tagifyCompany.value.forEach(function(tag) {
                    items.push({ cat: 'company', label: tag.value, clear: function() { tagifyCompany.removeTags(tag.value); } });
                });
            }
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            var minExpChanged = minExp !== '' && minExp !== wcfDefaultRange.minExp;
            var maxExpChanged = maxExp !== '' && maxExp !== wcfDefaultRange.maxExp;
            if (minExpChanged || maxExpChanged) {
                items.push({
                    cat: 'experience', label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(wcfDefaultRange.minExp); $('#max_exp').val(wcfDefaultRange.maxExp); }
                });
            }
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var minSalChanged = minSal !== '' && minSal !== wcfDefaultRange.minSal;
            var maxSalChanged = maxSal !== '' && maxSal !== wcfDefaultRange.maxSal;
            if (minSalChanged || maxSalChanged) {
                items.push({
                    cat: 'salary', label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(wcfDefaultRange.minSal); $('#max_sal').val(wcfDefaultRange.maxSal); }
                });
            }
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                items.push({
                    cat: 'notice', label: noticeEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                items.push({
                    cat: 'status', label: statusEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var outsourceEl = $('[name="app_int_outsource"]:checked');
            if (outsourceEl.val() && outsourceEl.val() !== '0') {
                items.push({
                    cat: 'hiring', label: outsourceEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_outsource1').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                items.push({
                    cat: 'activity', label: activityEl.next('.wcf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }
            return items;
        }

        function wcfAppliedChipRemoved() {
            fetchCandidates();
            if (typeof wcfUpdateActiveFiltersUI === 'function') wcfUpdateActiveFiltersUI();
            if (typeof wcfCaptureSnapshot === 'function') wcfCaptureSnapshot();
            wcfRenderAppliedFiltersCard();
        }

        function wcfRenderAppliedFiltersCard() {
            var items = wcfBuildAppliedItems();
            var $chips = $('#wcf_applied_chips').empty();
            var visible = wcfAppliedExpanded ? items : items.slice(0, WCF_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="wcf-applied-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + wcfCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').text('★'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="wcf-applied-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        wcfAppliedChipRemoved();
                    })
                );
                $chips.append($chip);
            });

            if (!wcfAppliedExpanded && items.length > WCF_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - WCF_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="wcf-applied-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        wcfAppliedExpanded = true;
                        wcfRenderAppliedFiltersCard();
                    })
                );
            }

            $('#wcf_applied_card').toggleClass('d-none', items.length === 0);
            $('#wcf_applied_count').text(items.length);
        }

        $(document).on('click', '#wcf_applied_clear_all', function() {
            $('#wcf_clear_all').trigger('click');
            fetchCandidates();
            if (typeof wcfCaptureSnapshot === 'function') wcfCaptureSnapshot();
            wcfAppliedExpanded = false;
            wcfRenderAppliedFiltersCard();
        });

        $(document).on('change', '[name="app_int_notice"], [name="app_int_status"], [name="app_int_outsource"], [name="app_int_activity"]', function() {
            wcfUpdateActiveFiltersUI();
            if (typeof wcfEnforceDrawerLayout === 'function') wcfEnforceDrawerLayout();
        });
        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', wcfUpdateActiveFiltersUI);

        $(document).on('click', '#wcf_clear_all', function() {
            if (typeof tagify !== 'undefined') tagify.removeAllTags();
            if (typeof tagifylocation !== 'undefined') tagifylocation.removeAllTags();
            if (typeof tagifyCompany !== 'undefined') tagifyCompany.removeAllTags();
            $('.wcf-edu-check').prop('checked', false);
            $('.wcf-edu-check[value=""]').prop('checked', true);
            wcfEducationSelected = [];
            wcfSyncEducationInput();
            $('#min_exp, #max_exp, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#min_sal').val(0);
            $('#app_int_notice1, #app_int_status0, #app_int_outsource1, #app_int_activity1').prop('checked', true);
            wcfUpdateActiveFiltersUI();
        });

        $(document).on('click', '#wcf_reset_btn', function() {
            $('#wcf_clear_all').trigger('click');
            fetchCandidates();
            wcfCaptureSnapshot();
            wcfAppliedExpanded = false;
            wcfRenderAppliedFiltersCard();
        });

        /* ------------------------------------------------------------------
           Draft-until-Apply behaviour. Nothing the user changes inside the
           drawer takes effect until "Apply Filters" is clicked. Closing the
           drawer any other way (Close button, header X, overlay, ESC)
           discards unsaved edits and reverts every field to the
           last-applied state, via Metronic's own drawer lifecycle events.
        ------------------------------------------------------------------ */
        var wcfSnapshot = null;
        var wcfApplying = false;

        function wcfCaptureSnapshot() {
            wcfSnapshot = {
                skills: (typeof tagify !== 'undefined')
                    ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                locations: (typeof tagifylocation !== 'undefined')
                    ? tagifylocation.value.map(function(t) { return t.value; }) : [],
                education: (typeof wcfEducationSelected !== 'undefined') ? wcfEducationSelected.slice() : [],
                company: (typeof tagifyCompany !== 'undefined')
                    ? tagifyCompany.value.map(function(t) { return t.value; }) : [],
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val(),
                notice: $('[name="app_int_notice"]:checked').val(),
                status: $('[name="app_int_status"]:checked').val(),
                outsource: $('[name="app_int_outsource"]:checked').val(),
                activity: $('[name="app_int_activity"]:checked').val()
            };
        }

        function wcfRestoreSnapshot() {
            if (!wcfSnapshot) return;
            if (typeof tagify !== 'undefined') {
                tagify.removeAllTags();
                if (wcfSnapshot.skills.length) tagify.addTags(wcfSnapshot.skills);
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.removeAllTags();
                if (wcfSnapshot.locations.length) tagifylocation.addTags(wcfSnapshot.locations);
            }
            if (typeof tagifyCompany !== 'undefined') {
                tagifyCompany.removeAllTags();
                if (wcfSnapshot.company.length) tagifyCompany.addTags(wcfSnapshot.company);
            }
            wcfEducationSelected = wcfSnapshot.education.slice();
            $('.wcf-edu-check').prop('checked', false);
            if (wcfEducationSelected.length === 0) {
                $('.wcf-edu-check[value=""]').prop('checked', true);
            } else {
                wcfEducationSelected.forEach(function(v) {
                    $('.wcf-edu-check[value="' + String(v).replace(/"/g, '\\"') + '"]').prop('checked', true);
                });
            }
            wcfSyncEducationInput();
            $('#min_exp').val(wcfSnapshot.min_exp);
            $('#max_exp').val(wcfSnapshot.max_exp);
            $('#min_sal').val(wcfSnapshot.min_sal);
            $('#max_sal').val(wcfSnapshot.max_sal);
            $('#min_salary_currency').val(wcfSnapshot.min_salary_currency);
            $('#sal_mode').val(wcfSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            if (wcfSnapshot.notice) $('[name="app_int_notice"][value="' + wcfSnapshot.notice + '"]').prop('checked', true);
            if (wcfSnapshot.status) $('[name="app_int_status"][value="' + wcfSnapshot.status + '"]').prop('checked', true);
            if (wcfSnapshot.outsource) $('[name="app_int_outsource"][value="' + wcfSnapshot.outsource + '"]').prop('checked', true);
            if (wcfSnapshot.activity) $('[name="app_int_activity"][value="' + wcfSnapshot.activity + '"]').prop('checked', true);
            wcfUpdateActiveFiltersUI();
        }

        function wcfBindDrawerLifecycle() {
            var el = document.getElementById('kt_wcf_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(wcfBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst) { return setTimeout(wcfBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                wcfApplying = false;
                wcfCaptureSnapshot();
                wcfEnforceDrawerLayout();
                setTimeout(wcfEnforceDrawerLayout, 50);
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!wcfApplying) wcfRestoreSnapshot();
                wcfApplying = false;
            });
        }
        $(document).ready(wcfBindDrawerLifecycle);

        $(document).on('click', '#wcf_close_btn', function() {
            $('#kt_wcf_filter_close').trigger('click');
        });
        $(document).on('click', '#wcf_apply_btn', function() {
            wcfApplying = true;
            fetchCandidates();
            wcfAppliedExpanded = false;
            wcfRenderAppliedFiltersCard();
            $('#kt_wcf_filter_close').trigger('click');
        });

        // The job's own computed defaults (min_exp/max_exp/min_sal/max_sal
        // pre-selected on load, e.g. min_exp="0" for Freshers) are real,
        // meaningful values — but they aren't something the user actually
        // applied, so they shouldn't show up as an Active Filters chip
        // ("0-Any Yrs") until the user changes them away from this baseline.
        var wcfDefaultRange = {
            minExp: $('#min_exp').val(), maxExp: $('#max_exp').val(),
            minSal: $('#min_sal').val(), maxSal: $('#max_sal').val()
        };

        $(document).ready(function() {
            setTimeout(wcfUpdateActiveFiltersUI, 600);
            setTimeout(wcfCaptureSnapshot, 800);
            setTimeout(wcfRenderAppliedFiltersCard, 700);
        });
    </script>
@endpush
