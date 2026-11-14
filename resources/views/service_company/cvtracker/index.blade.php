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
           CV Tracker — Page-Isolated Compact Header Spacing
           Removes excessive ghost toolbar whitespace above All Candidates Tracker
        ============================================================ */
        .kt_content_cvtracker,
        #kt_content.kt_content_cvtracker {
            margin-top: -65px !important;
            padding-top: 10px !important;
        }
        @media (max-width: 991.98px) {
            .kt_content_cvtracker,
            #kt_content.kt_content_cvtracker {
                margin-top: -55px !important;
                padding-top: 8px !important;
            }
        }

        /* ============================================================
           CV Tracker — Enterprise Header Card (.cl-header-card)
           Client Tracker Design System
        ============================================================ */
        .cl-header, .cl-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 18px;
            margin-top: 0 !important;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            font-family: 'Poppins', sans-serif;
        }
        .cl-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .cl-header-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06);
        }
        .cl-header-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cl-header-title {
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.015em;
            margin: 0;
            line-height: 1.2;
        }
        .cl-verified-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #16a34a;
        }
        .cl-header-subtitle {
            font-size: 12.5px;
            font-weight: 400;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.35;
        }
        .cl-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* ============================================================
           CV Tracker — 9 Interactive Status KPI Cards
           Client Tracker KPI Design System: All, In Review, Sourcing,
           Screening, Evaluation, Negotiation, Offer, Joined, Rejected
        ============================================================ */
        .cv-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(9, minmax(0, 1fr)) !important;
            gap: 6px !important;
            margin-bottom: 14px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            font-family: 'Poppins', sans-serif;
        }
        .cl-card.cv-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            user-select: none;
        }
        .cl-card.cv-kpi-card:hover {
            transform: translateY(-1px);
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            text-decoration: none;
        }
        .cl-card-top {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
            min-width: 0;
        }
        .cl-card-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .cl-card-icon svg {
            width: 14px !important;
            height: 14px !important;
        }
        .cl-card-icon.blue   { background: #eff6ff; color: #2563eb; }
        .cl-card-icon.yellow { background: #fefce8; color: #ca8a04; }
        .cl-card-icon.gray   { background: #f1f5f9; color: #64748b; }
        .cl-card-icon.purple { background: #faf5ff; color: #9333ea; }
        .cl-card-icon.sky    { background: #f0f9ff; color: #0284c7; }
        .cl-card-icon.pink   { background: #fff1f2; color: #e11d48; }
        .cl-card-icon.orange { background: #fff7ed; color: #ea580c; }
        .cl-card-icon.green  { background: #f0fdf4; color: #16a34a; }
        .cl-card-icon.red    { background: #fef2f2; color: #dc2626; }

        .cl-card-meta {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }
        .cl-card-num {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-variant-numeric: tabular-nums;
        }
        .cl-card-lbl {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .cl-card-sub {
            display: flex;
            align-items: center;
            gap: 3px;
            font-size: 9px;
            font-weight: 500;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .cl-card-sub svg { flex-shrink: 0; width: 10px !important; height: 10px !important; }
        .cl-card-sub span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cl-card-sub.blue   { color: #2563eb; }
        .cl-card-sub.yellow { color: #a16207; }
        .cl-card-sub.gray   { color: #64748b; }
        .cl-card-sub.purple { color: #9333ea; }
        .cl-card-sub.sky    { color: #0284c7; }
        .cl-card-sub.pink   { color: #e11d48; }
        .cl-card-sub.orange { color: #ea580c; }
        .cl-card-sub.green  { color: #16a34a; }
        .cl-card-sub.red    { color: #dc2626; }

        /* KPI Active Selected States */
        .cv-kpi-card.active[data-tab="all"] {
            background: #eff6ff !important;
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 1.5px #2563eb, 0 4px 12px rgba(37, 99, 235, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="all"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="8"] {
            background: #fefce8 !important;
            border-color: #facc15 !important;
            box-shadow: 0 0 0 1.5px #ca8a04, 0 4px 12px rgba(202, 138, 4, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="8"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="1"] {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 1.5px #64748b, 0 4px 12px rgba(100, 116, 139, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="1"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="2"] {
            background: #faf5ff !important;
            border-color: #c084fc !important;
            box-shadow: 0 0 0 1.5px #9333ea, 0 4px 12px rgba(147, 51, 234, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="2"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="3"] {
            background: #f0f9ff !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 1.5px #0284c7, 0 4px 12px rgba(2, 132, 199, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="3"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="4"] {
            background: #fff1f2 !important;
            border-color: #fb7185 !important;
            box-shadow: 0 0 0 1.5px #e11d48, 0 4px 12px rgba(225, 29, 72, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="4"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="5"] {
            background: #fff7ed !important;
            border-color: #fb923c !important;
            box-shadow: 0 0 0 1.5px #ea580c, 0 4px 12px rgba(234, 88, 12, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="5"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="6"] {
            background: #f0fdf4 !important;
            border-color: #4ade80 !important;
            box-shadow: 0 0 0 1.5px #16a34a, 0 4px 12px rgba(22, 163, 74, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="6"] .cl-card-icon { background: #ffffff !important; }

        .cv-kpi-card.active[data-tab="7,9"] {
            background: #fef2f2 !important;
            border-color: #f87171 !important;
            box-shadow: 0 0 0 1.5px #dc2626, 0 4px 12px rgba(220, 38, 38, 0.12) !important;
        }
        .cv-kpi-card.active[data-tab="7,9"] .cl-card-icon { background: #ffffff !important; }

        /* Ensure all 9 KPIs strictly stay in ONE single row across all viewports */
        @media (max-width: 1200px) {
            .cv-kpi-grid {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                padding-bottom: 6px !important;
                gap: 6px !important;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }
            .cv-kpi-grid::-webkit-scrollbar {
                height: 4px;
            }
            .cv-kpi-grid::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            .cl-card.cv-kpi-card {
                min-width: 110px !important;
                flex: 0 0 auto !important;
            }
        }

        /* Rule 25 Canonical Organization Filter Dropdown (Dropdown List Only) */
        .cvh-tb-org-wrap {
            background: #ffffff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 12px;
            height: 44px;
            flex-shrink: 0;
            width: 215px;
            position: relative;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .cvh-tb-org-wrap:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .cvh-tb-org-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            text-align: left;
            cursor: pointer;
        }
        .cvh-tb-org-btn:focus { outline: none; }
        .cvh-tb-org-icon {
            width: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .cvh-tb-org-label {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .cvh-tb-org-btn .chevron {
            flex-shrink: 0;
            color: #94a3b8;
            transition: transform .15s;
        }
        .cvh-tb-org-btn.open .chevron { transform: rotate(180deg); }
        .cvh-tb-org-panel {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 220px;
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(16,24,40,.12), 0 4px 6px -2px rgba(16,24,40,.06);
            padding: 6px;
            z-index: 60;
        }
        .cvh-tb-org-item {
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease;
        }
        .cvh-tb-org-item:hover { background: #f8f9fb; }
        .cvh-tb-org-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .cvh-tb-org-item .cvh-tb-org-check { margin-left: auto; font-size: 11px; display: none; }
        .cvh-tb-org-item.active .cvh-tb-org-check { display: inline-block; color: #1d4ed8; }

        /* Organization badge tokens (matching Rule 25) */
        .type-my-org { background: #ecfeff !important; color: #0891b2 !important; border-color: #a5f3fc !important; }
        .type-internal { background: #eff6ff !important; color: #2563eb !important; border-color: #bfdbfe !important; }
        .type-direct { background: #ecfdf5 !important; color: #059669 !important; border-color: #a7f3d0 !important; }
        .type-request { background: #f5f3ff !important; color: #7c3aed !important; border-color: #ddd6fe !important; }

        /* ============================================================
           Candidate Stream & Master Card + Sub-Card Architecture
           App Tracker / Similar Candidates Format (Rule 20 Modern Card + Sub-Card)
        ============================================================ */
        .cv-cards-stream, .simf-cand-stream {
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }
        .simf-cand-card, .aptk-cand-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            transition: all 0.2s ease;
            width: 100%;
        }
        .simf-cand-card:hover, .aptk-cand-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        }
        .simf-cand-card.is-selected, .aptk-cand-card.is-selected {
            background-color: #f8fbff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 2px 10px rgba(0, 158, 247, 0.12) !important;
        }
        .simf-cand-card.is-duplicate, .aptk-cand-card.is-duplicate {
            border-left-color: #ef4444;
        }

        /* 6 Upper Discrete Sub-Cards Grid (15.6" screen optimized) */
        .simf-cand-grid, .aptk-cand-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.1fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.15fr);
            gap: 8px;
            padding: 10px 12px;
            width: 100%;
            max-width: 100%;
            align-items: stretch;
        }
        .simf-cand-subcard, .aptk-cand-subcard {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            padding: 7px 9px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            overflow: hidden;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }
        .simf-cand-card:hover .simf-cand-subcard,
        .aptk-cand-card:hover .aptk-cand-subcard {
            border-color: #e2e8f0;
        }

        /* Sub-Card 4: Rule 25 Canonical Badges (Applied strictly to badge pill/text only, subcard remains neutral) */
        .type-my-org { background: #ecfeff !important; color: #0891b2 !important; border: 1px solid #a5f3fc !important; }
        .type-internal { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
        .type-direct { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
        .simf-target-type-badge {
            font-size: 9.5px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            white-space: nowrap !important;
            flex-shrink: 0;
        }

        /* Subcard Header & Label */
        .simf-subcard-head, .aptk-subcard-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            margin-bottom: 5px;
            line-height: 1;
            min-height: 18px;
        }
        .simf-subcard-label, .aptk-subcard-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.1;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .aptk-chk-wrap,
        .simf-subcard-head .form-check {
            display: inline-flex;
            align-items: center;
            margin: 0;
            padding: 0;
            min-height: auto;
        }
        .aptk-chk-wrap .form-check-input,
        .simf-subcard-head .form-check-input {
            width: 15px;
            height: 15px;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
        }
        .aptk-hdr-badges {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }
        .aptk-pill-bench {
            font-size: 8.5px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 4px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            text-transform: uppercase;
        }
        .simf-dup-pill, .aptk-pill-dup {
            font-size: 8.5px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 4px;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .aptk-subcard-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 3px;
            flex: 1 1 auto;
            min-width: 0;
            overflow: hidden;
        }

        /* Sub-Card 1: Identity & Candidate Info */
        .simf-cand-subcard--info {
            cursor: pointer;
        }
        .simf-cand-subcard--info:hover {
            background: #f0f9ff;
            border-color: #bae6fd !important;
        }
        .simf-cand-info-row, .aptk-identity-row {
            display: flex !important;
            align-items: flex-start !important;
            gap: 9px !important;
            min-width: 0 !important;
        }
        .simf-avatar, .aptk-avatar-box {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
            text-decoration: none !important;
            overflow: hidden !important;
            position: relative !important;
            flex-shrink: 0 !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            text-transform: uppercase !important;
        }
        .simf-avatar img, .aptk-avatar-box img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        .simf-cand-info-meta, .aptk-identity-meta {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5px !important;
        }
        .simf-cand-name, .aptk-cand-name {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-decoration: none;
        }
        .simf-cand-name:hover, .aptk-cand-name:hover,
        .simf-cand-subcard--info:hover .simf-cand-name {
            color: #009ef7;
        }
        .simf-cand-role, .aptk-cand-desig {
            font-size: 11px;
            font-weight: 500;
            color: #475569;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }
        .simf-cand-company, .aptk-cand-company {
            font-size: 10.5px;
            color: #64748b;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .simf-cand-company i, .aptk-cand-company i { font-size: 9.5px; color: #94a3b8; }

        /* Sub-Card 2: Compensation & Exp */
        .simf-val-salary, .aptk-val-salary {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
            font-variant-numeric: tabular-nums;
        }
        .simf-sal-main, .aptk-sal-main {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
        }
        .simf-sal-mode, .aptk-sal-mode {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
        }
        .simf-sal-sub, .aptk-sal-sub {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .simf-val-exp, .aptk-val-exp,
        .simf-val-notice, .aptk-val-notice {
            font-size: 10.5px;
            font-weight: 500;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-variant-numeric: tabular-nums;
            line-height: 1.3;
        }
        .simf-val-exp i, .aptk-val-exp i,
        .simf-val-notice i, .aptk-val-notice i { font-size: 10px; color: #94a3b8; flex-shrink: 0; }
        .aptk-dot { color: #cbd5e1; font-size: 8px; }
        .simf-notice-txt, .aptk-notice-txt { font-weight: 600; }
        .simf-notice-immediate, .aptk-notice-immediate { color: #16a34a; }
        .simf-notice-days, .aptk-notice-days { color: #d97706; }
        .simf-notice-snp, .aptk-notice-snp { color: #7c3aed; }
        .simf-notice-default, .aptk-notice-default { color: #64748b; }

        /* Sub-Card 3: Location & Degree */
        .simf-loc-city, .aptk-loc-city {
            font-size: 11.5px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .simf-loc-city i, .aptk-loc-city i { font-size: 10px; color: #94a3b8; }
        .simf-loc-mode, .aptk-loc-mode {
            font-size: 10.5px;
            font-weight: 500;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
            margin-top: 2px;
        }
        .simf-loc-mode i, .aptk-loc-mode i { font-size: 9.5px; color: #94a3b8; }
        .simf-loc-edu, .aptk-loc-edu {
            font-size: 10.5px;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
            margin-top: 2px;
        }
        .simf-loc-edu i, .aptk-loc-edu i { font-size: 10px; color: #94a3b8; }
        .simf-edu-uppercase, .aptk-edu-uppercase { text-transform: uppercase; }

        /* Sub-Card 4: Job Details */
        .simf-req-title, .aptk-job-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
            text-decoration: none;
            display: block;
        }
        .simf-req-title:hover, .aptk-job-title:hover { color: #009ef7; }
        .simf-req-meta-line {
            font-size: 11px;
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .simf-req-sub-line, .aptk-job-client {
            font-size: 11px;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
            margin-top: 2px;
        }
        .aptk-job-client i { font-size: 9.5px; color: #94a3b8; }

        /* Sub-Card 5: Ownership */
        .simf-owner-name {
            font-size: 11.5px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .simf-owner-company {
            font-size: 11px;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }
        .simf-owner-submitted {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }

        /* Sub-Card 6: Stage & Action (Restored Update Stage Cue) */
        .simf-stage-action-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        .simf-stage-trigger, .aptk-stage-trigger {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
            text-decoration: none !important;
            min-width: 0 !important;
            cursor: pointer !important;
        }
        .simf-stage-pill, .aptk-stage-pill {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            max-width: 100%;
            padding: 2px 7px;
            border-radius: 5px;
            border: 1px solid transparent;
            font-size: 10.5px;
            font-weight: 600;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .simf-substage-txt, .aptk-substage-txt {
            font-size: 10px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .aptk-substage-txt strong, .simf-substage-txt strong { color: #334155; font-weight: 600; }
        .simf-stage-cue, .aptk-stage-cue {
            font-size: 10px !important;
            font-weight: 600 !important;
            color: #009ef7 !important;
            line-height: 1.2 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            margin-top: 1px !important;
            text-decoration: none !important;
        }
        .simf-stage-cue:hover, .aptk-stage-cue:hover,
        .simf-stage-trigger:hover .simf-stage-cue {
            text-decoration: underline !important;
        }

        /* "Add to Other Job" Button */
        .cv-add-to-other-job {
            transition: all 0.15s ease;
        }
        .cv-add-to-other-job:hover {
            box-shadow: 0 2px 8px rgba(0, 158, 247, 0.35);
        }

        /* Sub-Card 7: Bottom Intelligence & Skills Strip */
        .simf-cand-bottom, .aptk-cand-bottom {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            padding: 6px 12px !important;
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            width: 100% !important;
            min-height: 38px !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .simf-bottom-left, .aptk-bottom-left {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }
        .simf-match-pill, .aptk-match-pill {
            display: inline-flex !important;
            align-items: center !important;
            padding: 2.5px 8px !important;
            border-radius: 5px !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-variant-numeric: tabular-nums !important;
        }
        .simf-skills-scroll, .aptk-skills-scroll {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            white-space: nowrap !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        .simf-skills-scroll::-webkit-scrollbar, .aptk-skills-scroll::-webkit-scrollbar { display: none !important; }
        .simf-skill-pill, .aptk-skill-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            background: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 10px !important;
            font-weight: 500 !important;
            line-height: 1.35 !important;
            flex-shrink: 0 !important;
            white-space: nowrap !important;
        }
        .simf-skill-pill.is-matched, .aptk-skill-pill.is-matched {
            background: #dcfce7 !important;
            color: #15803d !important;
            border-color: #bbf7d0 !important;
            font-weight: 600 !important;
        }
        .simf-skill-pill.is-mandatory, .aptk-skill-pill.is-mandatory {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border-color: #93c5fd !important;
            font-weight: 600 !important;
        }
        .simf-skills-empty { font-size: 10.5px; color: #94a3b8; }
        .simf-bottom-right, .aptk-bottom-right {
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .simf-resume-pill, .aptk-resume-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            background: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .simf-resume-pill.cursor-pointer, .aptk-resume-pill.cursor-pointer, .jw-open-resume-tab {
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
        }
        .simf-resume-pill.cursor-pointer:hover, .aptk-resume-pill.cursor-pointer:hover, .jw-open-resume-tab:hover {
            background: #dcfce7 !important;
            border-color: #86efac !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 1px 2px rgba(22, 163, 74, 0.15) !important;
        }
        .simf-nocv-pill, .aptk-nocv-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            background: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .simf-linkedin-pill, .aptk-linkedin-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            background: #ecfdf5 !important;
            color: #15803d !important;
            border: 1px solid #86efac !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            transition: all 0.15s ease !important;
        }
        .simf-linkedin-pill:hover, .aptk-linkedin-pill:hover { background: #dcfce7 !important; color: #166534 !important; border-color: #4ade80 !important; }
        .simf-visa-pill, .aptk-visa-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            background: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .simf-empty-card, .aptk-empty-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 42px 20px;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        /* 15.6" screen responsive grid adjustments (1366x768 & 1920x1080) */
        @media (max-width: 1440px) {
            .simf-cand-grid, .aptk-cand-grid {
                grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.1fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.15fr);
                gap: 6px;
                padding: 8px 10px;
            }
            .simf-cand-subcard, .aptk-cand-subcard { padding: 6px 8px; }
        }
        @media (max-width: 1180px) {
            .simf-cand-grid, .aptk-cand-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        }
        @media (max-width: 640px) {
            .simf-cand-grid, .aptk-cand-grid { grid-template-columns: 1fr; gap: 8px; }
            .simf-cand-bottom, .aptk-cand-bottom { flex-direction: column; align-items: flex-start; gap: 6px; }
        }

        /* Selected-rows action bar */
        .cvh-selection-bar { display: none; align-items: center; gap: 14px; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; padding: 10px 16px; margin-bottom: 14px; font-family: 'Poppins', sans-serif; flex-wrap: wrap; }
        .cvh-selection-bar.show { display: flex; }
        .cvh-selection-count { font-size: 13px; font-weight: 600; color: #334155; white-space: nowrap; }
        .cvh-selection-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .cvh-selection-actions .btn { font-size: 12px; font-weight: 500; background: #fff !important; border: 1px solid #bfdbfe !important; border-radius: 8px !important; padding: 6px 14px !important; box-shadow: none !important; }
        .cvh-selection-actions .btn:hover { background: #eff6ff !important; border-color: #93c5fd !important; }
        .cvh-selection-close { width: 28px; height: 28px; border-radius: 50%; border: none; background: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cvh-selection-close:hover { background: #f1f5f9; color: #334155; }

        /* Legacy hidden hooks */
        .cvh-legacy-hide { display: none !important; }

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
    {{-- Hidden DOM hooks for backward compatibility --}}
    <input type="hidden" id="kt_daterangepicker" />
    <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" value="" />
    <span class="d-none" id="total_record"></span>

    <div class="content d-flex flex-column flex-column-fluid pt-0 kt_content_cvtracker" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <!-- ── HEADER CARD (Client Tracker Design Standard) ── -->
                <div class="cl-header cl-header-card">
                    <div class="cl-header-left">
                        <div class="cl-header-icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <div class="cl-header-title-row">
                                <h1 class="cl-header-title">All Candidates Tracker</h1>
                                <span class="cl-verified-badge" title="Verified ATS Talent Pipeline">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="cl-header-subtitle">Track and manage all talent pipeline candidates across jobs and organizations</div>
                        </div>
                    </div>
                    <div class="cl-header-actions">
                        <button type="button" class="btn btn-sm btn-light-primary fw-bolder exportRecord" title="Export candidates list">
                            <i class="fa fa-print me-1"></i> Export List
                        </button>
                    </div>
                </div>

                <!-- ── 9 INTERACTIVE STATUS KPI CARDS (Client Tracker KPI Design System) ── -->
                <div class="cv-kpi-grid mb-4">
                    {{-- 1. All Candidates --}}
                    <div class="cl-card cv-kpi-card active" data-tab="all" onclick="setCvKpiTab('all', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon blue">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_all">{{ number_format($candidateStats['tabs']['all'] ?? $candidateStats['total'] ?? $applicationCounter ?? 0) }}</div>
                                <div class="cl-card-lbl">All Candidates</div>
                            </div>
                        </div>
                        <div class="cl-card-sub blue">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <span>Total talent pool</span>
                        </div>
                    </div>

                    {{-- 2. In Review --}}
                    <div class="cl-card cv-kpi-card" data-tab="8" onclick="setCvKpiTab('8', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon yellow">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_inreview">{{ number_format($candidateStats['tabs']['inreview'] ?? 0) }}</div>
                                <div class="cl-card-lbl">In Review</div>
                            </div>
                        </div>
                        <div class="cl-card-sub yellow">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20M12 2v20"/></svg>
                            <span>Under review</span>
                        </div>
                    </div>

                    {{-- 3. Sourcing --}}
                    <div class="cl-card cv-kpi-card" data-tab="1" onclick="setCvKpiTab('1', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon gray">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_sourcing">{{ number_format($candidateStats['tabs']['sourcing'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Sourcing</div>
                            </div>
                        </div>
                        <div class="cl-card-sub gray">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Active sourcing</span>
                        </div>
                    </div>

                    {{-- 4. Screening --}}
                    <div class="cl-card cv-kpi-card" data-tab="2" onclick="setCvKpiTab('2', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon purple">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_screening">{{ number_format($candidateStats['tabs']['screening'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Screening</div>
                            </div>
                        </div>
                        <div class="cl-card-sub purple">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Technical screen</span>
                        </div>
                    </div>

                    {{-- 5. Evaluation --}}
                    <div class="cl-card cv-kpi-card" data-tab="3" onclick="setCvKpiTab('3', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon sky">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_evaluation">{{ number_format($candidateStats['tabs']['evaluation'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Evaluation</div>
                            </div>
                        </div>
                        <div class="cl-card-sub sky">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            <span>Evaluations</span>
                        </div>
                    </div>

                    {{-- 6. Negotiation --}}
                    <div class="cl-card cv-kpi-card" data-tab="4" onclick="setCvKpiTab('4', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon pink">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_negotiation">{{ number_format($candidateStats['tabs']['negotiation'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Negotiation</div>
                            </div>
                        </div>
                        <div class="cl-card-sub pink">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                            <span>Comp & terms</span>
                        </div>
                    </div>

                    {{-- 7. Offer --}}
                    <div class="cl-card cv-kpi-card" data-tab="5" onclick="setCvKpiTab('5', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon orange">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_offered">{{ number_format($candidateStats['tabs']['offered'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Offer</div>
                            </div>
                        </div>
                        <div class="cl-card-sub orange">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                            <span>Offer stage</span>
                        </div>
                    </div>

                    {{-- 8. Joined --}}
                    <div class="cl-card cv-kpi-card" data-tab="6" onclick="setCvKpiTab('6', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon green">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_joined">{{ number_format($candidateStats['tabs']['joined'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Joined</div>
                            </div>
                        </div>
                        <div class="cl-card-sub green">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                            <span>Onboarded</span>
                        </div>
                    </div>

                    {{-- 9. Rejected --}}
                    <div class="cl-card cv-kpi-card" data-tab="7,9" onclick="setCvKpiTab('7,9', this)">
                        <div class="cl-card-top">
                            <div class="cl-card-icon red">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="cl-card-meta">
                                <div class="cl-card-num" id="kpi_rejected">{{ number_format($candidateStats['tabs']['rejected'] ?? 0) }}</div>
                                <div class="cl-card-lbl">Rejected</div>
                            </div>
                        </div>
                        <div class="cl-card-sub red">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                            <span>Archived</span>
                        </div>
                    </div>
                </div>

                <!-- ── TOOLBAR CARD: SEARCH + SORT + ORGANIZATION + FILTER ── -->
                <div class="card cvh-tb-card mb-4">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center gap-3 flex-wrap flex-xl-nowrap">

                            {{-- 1. Search Bar --}}
                            <div class="cvh-tb-search-wrap position-relative flex-grow-1" style="min-width: 240px;">
                                <span class="cvh-tb-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control cvh-tb-search-input pe-8" name="inputJobSearch"
                                    value="" placeholder="Search by name, skills, role, experience, location..." id="kt_tagify_2" autocomplete="off" />
                                <button type="button" id="cv_search_clear" class="btn btn-icon btn-sm text-muted position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); display: none; width: 22px; height: 22px; background: transparent; border: none;" title="Clear search">
                                    <i class="fa fa-times fs-8"></i>
                                </button>
                                <div style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%);">
                                    <div id="loader_search" class="loader_search hidden_search"></div>
                                </div>
                            </div>

                            {{-- 2. Sort Dropdown --}}
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
                                    <option value="7">High to Low (Experience)</option>
                                    <option value="8">Low to High (Experience)</option>
                                    <option value="30">Best Match</option>
                                </select>
                                <div class="cvh-tb-sort-panel d-none" id="cvh_sort_panel"></div>
                            </div>

                            {{-- 3. Organization Dropdown (Rule 25 Canonical Dropdown List) --}}
                            <div class="cvh-tb-org-wrap" id="cvh_org_wrap">
                                <button type="button" class="cvh-tb-org-btn" id="cvh_org_btn">
                                    <span class="cvh-tb-org-icon" id="cvh_org_btn_icon">
                                        <i class="fa fa-layer-group" style="color: #2563eb;"></i>
                                    </span>
                                    <span class="cvh-tb-org-label" id="cvh_org_label">All Organizations</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="cv_company_type_select" style="display:none;">
                                    <option value="all" selected>All Organizations</option>
                                    <option value="my_organization">My Organization</option>
                                    <option value="internal_client">Internal Client</option>
                                    <option value="direct_client">Direct Client</option>
                                </select>
                                <div class="cvh-tb-org-panel d-none" id="cvh_org_panel">
                                    <div class="cvh-tb-org-item active" data-value="all" data-icon="fa fa-layer-group" data-icon-color="#2563eb">
                                        <span class="cvh-tb-org-item-icon" style="color: #2563eb; width: 16px; text-align: center;"><i class="fa fa-layer-group"></i></span>
                                        <span class="cvh-tb-org-item-text">All Organizations</span>
                                        <span class="cvh-tb-org-check"><i class="fa fa-check"></i></span>
                                    </div>
                                    <div class="cvh-tb-org-item" data-value="my_organization" data-icon="fa fa-id-badge" data-icon-color="#0891b2">
                                        <span class="cvh-tb-org-item-icon" style="color: #0891b2; width: 16px; text-align: center;"><i class="fa fa-id-badge"></i></span>
                                        <span class="cvh-tb-org-item-text">My Organization</span>
                                        <span class="cvh-tb-org-check"><i class="fa fa-check"></i></span>
                                    </div>
                                    <div class="cvh-tb-org-item" data-value="internal_client" data-icon="fa fa-building" data-icon-color="#2563eb">
                                        <span class="cvh-tb-org-item-icon" style="color: #2563eb; width: 16px; text-align: center;"><i class="fa fa-building"></i></span>
                                        <span class="cvh-tb-org-item-text">Internal Client</span>
                                        <span class="cvh-tb-org-check"><i class="fa fa-check"></i></span>
                                    </div>
                                    <div class="cvh-tb-org-item" data-value="direct_client" data-icon="fa fa-shield-alt" data-icon-color="#059669">
                                        <span class="cvh-tb-org-item-icon" style="color: #059669; width: 16px; text-align: center;"><i class="fa fa-shield-alt"></i></span>
                                        <span class="cvh-tb-org-item-text">Direct Client</span>
                                        <span class="cvh-tb-org-check"><i class="fa fa-check"></i></span>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Filters Button --}}
                            <button type="button" id="kt_cvtracker_filter_toggle"
                                class="btn cvh-tb-filter-btn position-relative flex-shrink-0">
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

                {{-- ── Row selection action bar — hidden until candidates are checked.
                     Reuses the exact same trigger classes/data-target attributes as the
                     legacy toolbar buttons (mass_mail / fwd_resume / application_copy /
                     exportRecord), so every existing modal + ajax handler works unchanged;
                     only the presentation is new. ── --}}
                <div class="cvh-selection-bar" id="cvh_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input datatable_chk" type="checkbox" id="cvh_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="cvh-selection-count"><span id="cvh_selected_count">0</span> candidates selected</span>
                    <div class="cvh-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" id="kt_mass_mail_drawer_toggle">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success test_fwd_resume" id="kt_test_fwd_resume_drawer_toggle">
                            <span class="svg-icon svg-icon-success svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
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

                <div class="card border-0 shadow-none bg-transparent" id="cvh_table_card">
                    <div class="card-body p-0" style="position: relative">
                        @if (in_array('view_job_applications', $userPermissions))
                           <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                               <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                           </div>
                            <div class="cv-cards-stream d-flex flex-column gap-3" id="candidatetList">
                            </div>
                            <div id="paginationcandidate" class="mt-4"></div>
                            {{-- Print target — populated client-side from checked cards --}}
                            <div id="cvh_print_area" class="d-none"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
      /* Must render above #kt_activitiesApplication when opened from showdetail */
      #csd_drawer { z-index: 1000000 !important; }
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
            <div class="card-toolbar px-4 py-2 d-flex justify-content-end" id="kt_activities_header">
                <button type="button" id="submit-frm-btn" class="btn btn-primary btn-sm copy_current_selected_applicant d-none me-2">
                    <span class="indicator-label">Save to Jobs</span>
                    <span class="indicator-progress">Please wait...</span>
                </button>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>



    <!-- Note: Candidate Forward Resume Drawer is centrally defined at layout root in service_company_layout.blade.php -->

    <!--begin::Mass Mail - Drawer-->
    <div id="kt_mass_mail_drawer_toggle_hidden" style="display:none;"></div>
    <div id="kt_mass_mail_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="mass-mail"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
        data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_mass_mail_drawer_toggle" data-kt-drawer-close="#kt_mass_mail_drawer_close">
        <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
            <div class="card-header px-5 py-0 flex-shrink-0" id="kt_mass_mail_drawer_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark mb-0">Send Email</h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_mass_mail_drawer_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1"/>
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"/>
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_mass_mail_body_content"></div>
        </div>
    </div>
    <!--end::Mass Mail - Drawer-->



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

    <script src="{{ asset('js/opendoc/CandidateSnapshotResolver.js') }}"></script>
    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>

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
                var $thead = $('<thead><tr>' +
                    '<th>Candidate</th>' +
                    '<th>Job & Client</th>' +
                    '<th>Exp & Notice</th>' +
                    '<th>Compensation</th>' +
                    '<th>Location</th>' +
                    '<th>Stage</th>' +
                    '<th>Match</th>' +
                    '</tr></thead>');
                $table.append($thead);

                var $tbody = $('<tbody></tbody>');
                $checked.each(function() {
                    var $card = $(this).closest('.aptk-cand-card');
                    if ($card.length) {
                        var $tr = $('<tr></tr>');
                        $tr.append($('<td></td>').html('<strong>' + $('<div>').text($card.attr('data-print-name') || '').html() + '</strong><br><small>' + $('<div>').text($card.attr('data-print-desig') || '').html() + ' (' + $('<div>').text($card.attr('data-print-company') || '').html() + ')</small>'));
                        $tr.append($('<td></td>').html('<strong>' + $('<div>').text($card.attr('data-print-job') || '').html() + '</strong><br><small>' + $('<div>').text($card.attr('data-print-client') || '').html() + '</small>'));
                        $tr.append($('<td></td>').html($('<div>').text($card.attr('data-print-exp') || '—').html() + '<br><small>' + $('<div>').text($card.attr('data-print-notice') || '').html() + '</small>'));
                        $tr.append($('<td></td>').text($card.attr('data-print-salary') || '—'));
                        $tr.append($('<td></td>').html($('<div>').text($card.attr('data-print-location') || '').html() + '<br><small>' + $('<div>').text($card.attr('data-print-edu') || '').html() + '</small>'));
                        $tr.append($('<td></td>').text($card.attr('data-print-stage') || '—'));
                        $tr.append($('<td></td>').text($card.attr('data-print-match') || '0%'));
                        $tbody.append($tr);
                    } else {
                        var $row = $(this).closest('tr').clone();
                        $row.find('td').first().remove();
                        $tbody.append($row);
                    }
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

            $(document).on('click', '.test_fwd_resume', function(e) {
                e.preventDefault();

                var arr = [];
                var jobIdArr = [];
                var matchMap = {};
                $('.chk_list_item:checked').each(function() {
                    var jobId = $(this).attr('data-jobId');
                    var candId = $(this).val();
                    var matchVal = $(this).attr('data-match');
                    arr.push(candId);
                    jobIdArr.push(jobId);
                    if (matchVal !== undefined && matchVal !== '') {
                        matchMap[candId] = parseInt(matchVal, 10);
                    }
                });

                if (arr.length === 0) {
                    Swal.fire({
                        title: 'No Candidate Selected',
                        text: 'Please select at least one candidate before testing forward resumes.',
                        icon: 'warning',
                        confirmButtonColor: '#0D9488'
                    });
                    return;
                }

                if (arr.length > 5) {
                    Swal.fire({
                        title: 'Maximum 5 Candidates Allowed',
                        html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                              '<p style="margin-bottom:8px;">You’ve selected <strong>' + arr.length + ' candidates</strong>. The forward resume engine supports up to 5 candidates.</p>' +
                              '<p style="color:#6B7280;margin:0;">Please select 5 candidates or fewer to continue.</p>' +
                              '</div>',
                        icon: 'warning',
                        confirmButtonText: 'OK, I Understand',
                        confirmButtonColor: '#0D9488'
                    });
                    return;
                }

                if (window.ForwardResumeManager) {
                    window.ForwardResumeManager.open(arr, jobIdArr, matchMap);
                } else if (typeof window.openCandidateForwardResume === 'function') {
                    window.openCandidateForwardResume(arr, jobIdArr);
                }
            });

            $(document).on('click', '#kt_test_fwd_resume_drawer_close', function(e) {
                e.preventDefault();
                if (window.ForwardResumeManager && typeof window.ForwardResumeManager.close === 'function') {
                    window.ForwardResumeManager.close();
                } else if (typeof window.closeCandidateForwardResume === 'function') {
                    window.closeCandidateForwardResume();
                }
            });


            $(document).on('click', '.mass_mail', function(e) {

                e.preventDefault();

                // Open the Send Email drawer
                var drawerEl = document.getElementById('kt_mass_mail_drawer');
                if (drawerEl && typeof KTDrawer !== 'undefined') {
                    var drawer = KTDrawer.getInstance(drawerEl);
                    if (drawer) { drawer.show(); }
                }

                // Show loading state inside drawer
                $('#application_mass_mail_body_content').html(
                    '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
                    '<span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading...</div>'
                );
                $('#application_fwd_resume_body_content').html('');

                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {
                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                var id = jobIdArr[0];
                var url = "{{ route('service_company.cvtracker.mass_mail', ':id') }}";
                url = url.replace(':id', id);
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
        
        // Free-text search box: 300ms debounce + Enter key immediate trigger + Clear button
        var cvSearchDebounce = null;
        $(document).on('input keyup', '#kt_tagify_2', function(e) {
            var val = $(this).val();
            $('#cv_search_clear').toggle(val.length > 0);
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                clearTimeout(cvSearchDebounce);
                page_no = 1;
                localStorage.setItem('currentPage', 1);
                fetchCandidates(1);
                return;
            }
            clearTimeout(cvSearchDebounce);
            cvSearchDebounce = setTimeout(function() {
                page_no = 1;
                localStorage.setItem('currentPage', 1);
                fetchCandidates(1);
            }, 300);
        });

        $(document).on('click', '#cv_search_clear', function() {
            $('#kt_tagify_2').val('');
            $(this).hide();
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
        });

        function filtersdataget() {
            // Used by the sort dropdown's onchange — the filter drawer itself has
            // its own Apply/Reset buttons that call fetchCandidates() directly,
            // this just covers the sort-only auto-apply case.
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
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
            window._currentCandidateName = '';
            var candSkills = [];
            var firstCandName = '';
            var checkedCount = $('.chk_list_item:checked').length;

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
                wiseId.push($(this).data('wiseid'));
                var sk = $(this).attr('data-skills') || $(this).data('skills');
                if (sk) {
                    var sArr = String(sk).split(',');
                    sArr.forEach(function(s) {
                        var cleaned = $.trim(s).toLowerCase();
                        if (cleaned && candSkills.indexOf(cleaned) === -1) {
                            candSkills.push(cleaned);
                        }
                    });
                }
                var cn = $(this).attr('data-cname') || $(this).data('cname');
                if (cn && !firstCandName) {
                    firstCandName = cn;
                }
            });

            window._candidateSkills = candSkills;
            if (checkedCount === 1 && firstCandName) {
                window._currentCandidateName = firstCandName;
            } else if (checkedCount > 1) {
                window._currentCandidateName = checkedCount + ' candidates';
            }

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

        // "Add to Other Job" Button Handler (CV Tracker Candidate Card Action -> Copy Candidate Drawer)
        $(document).on('click', '.cv-add-to-other-job', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var candId = $(this).attr('data-candidate-id') || $(this).data('candidate-id');
            var wiseId = $(this).attr('data-wise-id') || $(this).data('wise-id');
            var cname = $(this).attr('data-cname') || $(this).data('cname') || '';
            var skills = $(this).attr('data-skills') || $(this).data('skills') || '';

            // Uncheck other cards, select this candidate's checkbox
            $('.chk_list_item').prop('checked', false);
            var $card = $(this).closest('.simf-cand-card, .aptk-cand-card');
            $card.find('.chk_list_item').prop('checked', true);

            $('.simf-cand-card, .aptk-cand-card').removeClass('is-selected');
            $card.addClass('is-selected');

            // Trigger copy drawer workflow for this specific candidate
            window._currentCandidateName = cname;
            var candSkills = [];
            if (skills) {
                var sArr = String(skills).split(',');
                sArr.forEach(function(s) {
                    var cleaned = $.trim(s).toLowerCase();
                    if (cleaned && candSkills.indexOf(cleaned) === -1) {
                        candSkills.push(cleaned);
                    }
                });
            }
            window._candidateSkills = candSkills;

            $('#application_copy_body_content').html('');
            $('#kt_activitiesCopy_toggle').trigger('click');

            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': [candId],
                    'wiseId': [wiseId]
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copied', "warning").then(function() {
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
                            var rowId = response.wise_id || $('#status_frm input[name="job_wise_condidates_id"]').val() || $('#status_frm input[name="row_id"]').val();
                            if (window.patchCandidateRowStage) {
                                window.patchCandidateRowStage(
                                    rowId,
                                    response.master_status_id,
                                    response.master_status_name,
                                    response.css_style,
                                    response.sub_stage_name,
                                    response.old_master_status_id,
                                    response.candidate_id
                                );
                            } else {
                                if (typeof invalidateCvTrackerCaches === 'function') invalidateCvTrackerCaches();
                                var page = $('#paginationcandidate .active_disabled').data('page') || page_no || 1;
                                fetchCandidates(page);
                            }

                        } else if (response.status == 'conflict') {
                            Swal.fire("Notice", response.message || 'Candidate stage was modified by another user. Refreshing...', "warning").then(function() {
                                $('#csd_drawer_close').trigger('click');
                            });
                            if (typeof invalidateCvTrackerCaches === 'function') invalidateCvTrackerCaches();
                            fetchCandidates(page_no);
                        } else if (response.status == "info") {
                            Swal.fire('Fail!', response.message, 'error')
                        } else if (response.status == 'cvpending') {
                            Swal.fire("Warning", 'Resume pending this job application.', "warning").then(function() {
                                $('#csd_drawer_close').trigger('click');
                            });
                        } else if (response.status == 'candidatevalidation') {
                            if (typeof window.showStageRequiredFieldsModal === 'function') {
                                window.showStageRequiredFieldsModal(response);
                            } else {
                                Swal.fire("Warning", response.message, "warning").then(function() {
                                    $('#csd_drawer_close').trigger('click');
                                });
                            }
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

        $(document).on('click', '#hdr_move_stage_btn', async function(e) {
            e.preventDefault();
            const connected = await isInternetAvailable();
            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: { confirmButton: "btn btn-warning" }
                });
                return;
            }
            var id = $(this).attr('rel');
            $('#client_stage_body_content').html('').hide();
            $('#stage_body_content').html(
                '<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>'
            ).show();
            $('#custom_stage_submit').show();
            $('#int_stage_submit').hide();
            $('#csd_drawer_toggle').trigger('click');
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
                                    $("#stage_sub_stage_close").click();
                                });
                            var rowId = response.wise_id || $('#status_frm input[name="job_wise_condidates_id"]').val() || $('#status_frm input[name="row_id"]').val();
                            if (window.patchCandidateRowStage) {
                                window.patchCandidateRowStage(
                                    rowId,
                                    response.master_status_id,
                                    response.master_status_name,
                                    response.css_style,
                                    response.sub_stage_name,
                                    response.old_master_status_id,
                                    response.candidate_id
                                );
                            } else {
                                if (typeof invalidateCvTrackerCaches === 'function') invalidateCvTrackerCaches();
                                var page = $('#paginationcandidate .active_disabled').data('page') || page_no || 1;
                                fetchCandidates(page);
                            }
                            load_interviewSchedule();
                        } else if (response.status == 'conflict') {
                            Swal.fire("Notice", response.message || 'Candidate stage was modified by another user. Refreshing...', "warning").then(function() {
                                $("#stage_sub_stage_close").click();
                            });
                            if (typeof invalidateCvTrackerCaches === 'function') invalidateCvTrackerCaches();
                            fetchCandidates(page_no);
                        } else if (response.status == 'cvpending') {
                            Swal.fire("Warning", 'Resume pending this job application.', "warning")
                                .then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                        } else if (response.status == 'candidatevalidation') {
                            if (typeof window.showStageRequiredFieldsModal === 'function') {
                                window.showStageRequiredFieldsModal(response);
                            } else {
                                Swal.fire("Warning", response.message, "warning").then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            }
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

                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.test_fwd_resume').removeClass("d-none");
                    $('.custom_hide').addClass("d-none");
                    $('.mass_mail').removeClass("d-none");
                    $('.send_email').removeClass("d-none");
                    $('.downloadexcell').removeClass("d-none");
                    $('.app_copy').removeClass("d-none");
                    $('.application_copy').removeClass("d-none");

                } else {
                    $('.test_fwd_resume').addClass("d-none");
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
                  
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.test_fwd_resume').removeClass("d-none");
                        $('.mass_mail').removeClass("d-none");
                        $('.send_email').removeClass("d-none");
                        $('.custom_hide').addClass("d-none");
                        $('.downloadexcell').removeClass("d-none");
                        $('.app_copy').removeClass("d-none");
                        $('.application_copy').removeClass("d-none");
                    } else {
                        $('.test_fwd_resume').addClass("d-none");
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
                $('.chk_list_item').each(function() {
                    $(this).closest('.aptk-cand-card').toggleClass('is-selected', $(this).is(':checked'));
                });
                if (n === 0) {
                    $('.chk_list, .datatable_chk, #cvh_selection_master_chk').prop('checked', false);
                    $('.aptk-cand-card').removeClass('is-selected');
                    $('.test_fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
                    $('.custom_hide').removeClass('d-none');
                }
            }
            function clearCvSelection() {
                $('.chk_list, .chk_list_item, .datatable_chk, #cvh_selection_master_chk, #chk_all_list').prop('checked', false);
                $('.aptk-cand-card').removeClass('is-selected');
                $('#myTable tbody tr').removeClass('table-active selected');
                $('.test_fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
                $('.custom_hide').removeClass('d-none');
                updateCvSelectionBar();
            }
            window.clearCvSelection = clearCvSelection;
            window.resetCvTrackerSelection = clearCvSelection;
            $(document).on('click', '#cvh_selection_close', clearCvSelection);
            $(document).on('change', '#cvh_selection_master_chk', function() {
                if (!$(this).is(':checked')) clearCvSelection();
            });

            /* Candidate Card click handler: open detail drawer unless clicking interactive elements */
            $(document).on('click', '.aptk-cand-card', function(e) {
                if ($(e.target).closest('.chk_list_item, .aptk-chk-wrap, .aptk-subcard-stage, .stage_sub_stage, .client_stage_sub_stage, .aptk-linkedin-pill, .show-application-detail, a[href^="http"], a[data-toggle="modal"], button, input, select, textarea').length) {
                    return;
                }
                if (window.getSelection && window.getSelection().toString().trim() !== '') {
                    return;
                }
                var $detailTrigger = $(this).find('.show-application-detail').first();
                if ($detailTrigger.length) {
                    $detailTrigger.trigger('click');
                }
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
                fetchCandidates(page, $('#inputSearch').val() || '').then(function() {
                    cvhPaginationLoading = false;
                }).catch(function() {
                    cvhPaginationLoading = false;
                });
            });

            $(document).on('change', '#paginationcandidate .pagi-perpage', function() {
                var selectedPerPage = $(this).val();
                localStorage.setItem('candidatePerPage', selectedPerPage);
                localStorage.setItem('currentPage', 1);
                page_no = 1;
                fetchCandidates(1);
            });

        });
        var page_no = localStorage.getItem('currentPage') || 1;
        var activeStageTab = 'all';
        var activeCompanyType = 'all';

        function setCvKpiTab(stage, el) {
            $('.cv-kpi-card').removeClass('active');
            $(el).closest('.cv-kpi-card').addClass('active');
            activeStageTab = stage;
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
        }
        window.setCvKpiTab = setCvKpiTab;

        function setCvCompanyType(orgType, el) {
            $('.cvh-tb-org-item').removeClass('active');
            var $target = $('.cvh-tb-org-item[data-value="' + orgType + '"]');
            if ($target.length) {
                $target.addClass('active');
                $('#cvh_org_label').text($target.find('.cvh-tb-org-item-text').text());
                $('#cvh_org_btn_icon').html('<i class="' + $target.data('icon') + '" style="color:' + $target.data('icon-color') + ';"></i>');
            }
            $('#cv_company_type_select').val(orgType);
            activeCompanyType = orgType;
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
        }
        window.setCvCompanyType = setCvCompanyType;

        function cvhOpenOrgPanel() {
            if (typeof cvhCloseSortPanel === 'function') cvhCloseSortPanel();
            $('#cvh_org_panel').removeClass('d-none');
            $('#cvh_org_btn').addClass('open');
        }
        function cvhCloseOrgPanel() {
            $('#cvh_org_panel').addClass('d-none');
            $('#cvh_org_btn').removeClass('open');
        }
        $(document).on('click', '#cvh_org_btn', function(e) {
            e.stopPropagation();
            if ($('#cvh_org_panel').hasClass('d-none')) {
                cvhOpenOrgPanel();
            } else {
                cvhCloseOrgPanel();
            }
        });
        $(document).on('click', '.cvh-tb-org-item', function(e) {
            e.stopPropagation();
            var val = $(this).data('value');
            setCvCompanyType(val, this);
            cvhCloseOrgPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#cvh_org_wrap').length) cvhCloseOrgPanel();
        });

        /* ── Phase 7: GOD-Level Client Caching (L1 RAM + L2 IndexedDB + SWR + Request Abort + Multi-Tab Sync) ──
         * Reference: service_company/job-onboard
         * Intent: 0% avoidable server hit when switching pages or refreshing.
         * L1 RAM: Map() cache for instant 0ms sub-millisecond pagination transitions.
         * L2 IndexedDB: Persistent snapshot cache for instant paint on browser refresh.
         * SWR: Stale-While-Revalidate background refresh only when data exceeds FRESH_MS.
         * Abort: In-flight XHR cancellation to eliminate race conditions on fast paging.
         * Invalidation: Cross-tab BroadcastChannel invalidation on candidate mutations.
         */
        var TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        var activeCvXhr = null;

        /* ── L1 RAM Snapshot Cache ── */
        var CvTrackerCache = {
            MAX_ENTRIES: 30,
            TTL_MS: 180000, // 3 minutes TTL in user RAM
            store: new Map(),

            makeKey: function(s) {
                return [
                    s.tenant,
                    s.page,
                    s.per_page,
                    (s.search || '').trim().toLowerCase(),
                    s.sortingfilter || '',
                    s.country || '',
                    (s.cus_search || '').trim().toLowerCase(),
                    (s.cus_search_skill || '').trim().toLowerCase(),
                    s.min_exp || '',
                    s.max_exp || '',
                    (s.kt_tagify_location || '').trim().toLowerCase(),
                    s.min_sal || '',
                    s.max_sal || '',
                    s.min_salary_currency || '',
                    s.sal_mode || '',
                    (s.kt_tagify_education || '').trim().toLowerCase(),
                    s.app_int_status || '',
                    s.app_int_activity || '',
                    s.app_int_notice || '',
                    s.stage_tab || '',
                    s.company_type || ''
                ].join('|');
            },

            get: function(s) {
                var key = this.makeKey(s);
                var entry = this.store.get(key);
                if (!entry) return null;
                if (Date.now() - entry.time > this.TTL_MS) {
                    this.store.delete(key);
                    return null;
                }
                return entry.data;
            },

            set: function(s, data) {
                var key = this.makeKey(s);
                if (this.store.size >= this.MAX_ENTRIES) {
                    var oldestKey = this.store.keys().next().value;
                    this.store.delete(oldestKey);
                }
                this.store.set(key, { data: data, time: Date.now() });
            },

            clear: function() {
                this.store.clear();
            }
        };
        window.CvTrackerCache = CvTrackerCache;

        /* ── L2 IndexedDB Persistence Layer ── */
        var CvTrackerIDB = {
            DB_NAME: 'JeroCvTrackerDB',
            DB_VERSION: 1,
            STORE_NAME: 'snapshots',
            TTL_MS: 300000,   // 5 minutes max storage TTL
            FRESH_MS: 60000,  // 1 minute fresh TTL (no server hit on refresh if under 60s)
            MAX_ENTRIES: 30,
            db: null,

            open: function() {
                var self = this;
                if (self.db) return Promise.resolve(self.db);
                if (!window.indexedDB) return Promise.resolve(null);

                return new Promise(function(resolve) {
                    try {
                        var req = window.indexedDB.open(self.DB_NAME, self.DB_VERSION);
                        req.onupgradeneeded = function(e) {
                            var db = e.target.result;
                            if (!db.objectStoreNames.contains(self.STORE_NAME)) {
                                var store = db.createObjectStore(self.STORE_NAME, { keyPath: 'key' });
                                store.createIndex('time', 'time', { unique: false });
                            }
                        };
                        req.onsuccess = function(e) {
                            self.db = e.target.result;
                            resolve(self.db);
                        };
                        req.onerror = function() {
                            resolve(null);
                        };
                    } catch (err) {
                        resolve(null);
                    }
                });
            },

            get: function(key) {
                var self = this;
                return self.open().then(function(db) {
                    if (!db) return null;
                    return new Promise(function(resolve) {
                        try {
                            var tx = db.transaction(self.STORE_NAME, 'readonly');
                            var store = tx.objectStore(self.STORE_NAME);
                            var req = store.get(key);
                            req.onsuccess = function(e) {
                                var record = e.target.result;
                                if (!record) return resolve(null);
                                var age = Date.now() - record.time;
                                if (age > self.TTL_MS) {
                                    self.delete(key);
                                    return resolve(null);
                                }
                                resolve({ data: record.data, age: age });
                            };
                            req.onerror = function() {
                                resolve(null);
                            };
                        } catch (err) {
                            resolve(null);
                        }
                    });
                });
            },

            set: function(key, data) {
                var self = this;
                return self.open().then(function(db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(self.STORE_NAME, 'readwrite');
                        var store = tx.objectStore(self.STORE_NAME);
                        store.put({ key: key, data: data, time: Date.now() });
                        self.trim(store);
                    } catch (err) {}
                });
            },

            delete: function(key) {
                var self = this;
                return self.open().then(function(db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(self.STORE_NAME, 'readwrite');
                        tx.objectStore(self.STORE_NAME).delete(key);
                    } catch (err) {}
                });
            },

            clear: function() {
                var self = this;
                return self.open().then(function(db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(self.STORE_NAME, 'readwrite');
                        tx.objectStore(self.STORE_NAME).clear();
                    } catch (err) {}
                });
            },

            trim: function(store) {
                try {
                    var countReq = store.count();
                    var self = this;
                    countReq.onsuccess = function() {
                        if (countReq.result > self.MAX_ENTRIES) {
                            var index = store.index('time');
                            var cursorReq = index.openCursor();
                            cursorReq.onsuccess = function(e) {
                                var cursor = e.target.result;
                                if (cursor) {
                                    cursor.delete();
                                }
                            };
                        }
                    };
                } catch (e) {}
            }
        };
        window.CvTrackerIDB = CvTrackerIDB;

        /* ── Multi-Tab Coordination (BroadcastChannel) ── */
        var cvBroadcastChannel = null;
        if ('BroadcastChannel' in window) {
            try {
                cvBroadcastChannel = new BroadcastChannel('jero_cv_tracker_' + TENANT_ID);
                cvBroadcastChannel.onmessage = function(ev) {
                    if (!ev || !ev.data) return;
                    var msg = ev.data;
                    if (msg.type === 'INVALIDATE_CACHE' || msg.type === 'STAGE_UPDATED') {
                        CvTrackerCache.clear();
                        CvTrackerIDB.clear();
                        if (msg.payload && typeof window.patchCandidateRowStage === 'function') {
                            window.patchCandidateRowStage(
                                msg.payload.wise_id,
                                msg.payload.master_status_id,
                                msg.payload.master_status_name,
                                msg.payload.css_style,
                                msg.payload.sub_stage_name,
                                msg.payload.old_master_status_id,
                                msg.payload.candidate_id,
                                false
                            );
                        }
                    }
                };
            } catch (e) {}
        }

        /* ── Cache Invalidation Helper ── */
        function invalidateCvTrackerCaches(broadcastPayload) {
            CvTrackerCache.clear();
            CvTrackerIDB.clear();
            if (cvBroadcastChannel) {
                try {
                    cvBroadcastChannel.postMessage({
                        type: broadcastPayload ? 'STAGE_UPDATED' : 'INVALIDATE_CACHE',
                        payload: broadcastPayload || null
                    });
                } catch (e) {}
            }
        }
        window.invalidateCvTrackerCaches = invalidateCvTrackerCaches;

        /* ── Render Candidates Response (Memory or Network) ── */
        function renderCandidatesResponse(response, targetPage) {
            if (!response) return;
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');
            $('#cvh_table_card').show();
            if (response.total !== undefined) {
                $('#total_record').html(response.total);
            }
            if (response.html !== undefined) {
                $('#candidatetList').html(response.html);
            }
            if (targetPage) {
                localStorage.setItem('currentPage', targetPage);
            }
            if (response.pagination !== undefined) {
                $('#paginationcandidate').html(response.pagination);
            }
            if (response.candidateStats) {
                updateCvHeroStats(response.candidateStats);
            }
        }

        /* ── Network Fetch with In-Flight Request Abort & Dual-Tier Storage ── */
        function executeCandidateFetch(filterState, targetPage, isBackgroundRevalidate) {
            if (activeCvXhr && activeCvXhr.readyState !== 4) {
                activeCvXhr.abort();
                activeCvXhr = null;
            }

            var urlParams = new URLSearchParams(window.location.search);
            var isV2Active = urlParams.get('v2') === '1' || {{ config('app.cvtracker_v2_enabled', false) ? 'true' : 'false' }};
            var candidateEndpoint = isV2Active
                ? `{!! route('service_company.cvtracker.v2.allcandidates') !!}?page=${targetPage}`
                : `{!! route('service_company.cvtracker.allcandidates') !!}?page=${targetPage}`;

            var requestData = {
                search: filterState.search,
                sortingfilter: filterState.sortingfilter,
                country: filterState.country,
                cus_search: filterState.cus_search,
                cus_search_skill: filterState.cus_search_skill,
                min_exp: filterState.min_exp,
                max_exp: filterState.max_exp,
                kt_tagify_location: filterState.kt_tagify_location,
                min_sal: filterState.min_sal,
                max_sal: filterState.max_sal,
                min_salary_currency: filterState.min_salary_currency,
                sal_mode: filterState.sal_mode,
                kt_tagify_education: filterState.kt_tagify_education,
                app_int_status: filterState.app_int_status,
                app_int_activity: filterState.app_int_activity,
                app_int_notice: filterState.app_int_notice,
                stage_tab: filterState.stage_tab,
                company_type: filterState.company_type,
                size: filterState.per_page,
                per_page: filterState.per_page
            };

            return new Promise(function(resolve, reject) {
                activeCvXhr = $.ajax({
                    url: candidateEndpoint,
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        activeCvXhr = null;
                        // 1. Store in L1 RAM snapshot cache
                        CvTrackerCache.set(filterState, response);
                        // 2. Store in L2 IndexedDB persistence layer
                        var key = CvTrackerCache.makeKey(filterState);
                        CvTrackerIDB.set(key, response);
                        // 3. Render DOM
                        renderCandidatesResponse(response, targetPage);
                        resolve(response);
                    },
                    error: function(xhr, status) {
                        activeCvXhr = null;
                        if (status === 'abort') {
                            return resolve(null);
                        }
                        if (!isBackgroundRevalidate) {
                            $('.loadingMessage').css({ display: 'none' });
                            $('.loader_search').css({ display: 'none' });
                            $('.page-loader').css('display', 'none');
                            $('#cvh_table_card').show();
                        }
                        console.error('allcandidates failed:', xhr.status, xhr.responseText);
                        reject(xhr);
                    }
                });
            });
        }

        /* ── Fetch Candidates with L1 RAM Cache, L2 IndexedDB SWR & Stale Request Abort ── */
        function fetchCandidates(page, search = '') {
            var targetPage = page || page_no || 1;
            page_no = targetPage;
            localStorage.setItem('currentPage', targetPage);

            var perPage = $('#paginationcandidate .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;

            var filterState = {
                tenant: TENANT_ID,
                page: targetPage,
                per_page: perPage,
                search: $('#inputSearch').val() || search || '',
                sortingfilter: $('#sortingfilter').val() || '',
                country: $('#country').val() || '',
                cus_search: $('#kt_tagify_2').val() || '',
                cus_search_skill: $('#kt_tagify_users').val() || '',
                min_exp: $('#min_exp').val() || '',
                max_exp: $('#max_exp').val() || '',
                kt_tagify_location: $('#kt_tagify_location').val() || '',
                min_sal: $('#min_sal').val() || '',
                max_sal: $('#max_sal').val() || '',
                min_salary_currency: $('#min_salary_currency').val() || '',
                sal_mode: $('#sal_mode').val() || '',
                kt_tagify_education: $('#kt_tagify_education').val() || '',
                app_int_status: $('[name="app_int_status"]:checked').val() || '',
                app_int_activity: $('[name="app_int_activity"]:checked').val() || '',
                app_int_notice: $('[name="app_int_notice"]:checked').val() || '',
                stage_tab: activeStageTab === 'all' ? '' : activeStageTab,
                company_type: activeCompanyType === 'all' ? '' : activeCompanyType
            };

            // 1. Check L1 RAM Snapshot Cache first (instant 0ms render, 0 network hit)
            var ramCached = CvTrackerCache.get(filterState);
            if (ramCached) {
                renderCandidatesResponse(ramCached, targetPage);
                return Promise.resolve(ramCached);
            }

            var key = CvTrackerCache.makeKey(filterState);

            // 2. Check L2 IndexedDB Cache (instant paint on refresh/rehydrate)
            return CvTrackerIDB.get(key).then(function(idbRecord) {
                if (idbRecord && idbRecord.data) {
                    // Populate L1 RAM cache
                    CvTrackerCache.set(filterState, idbRecord.data);
                    // Render immediately from IndexedDB (0ms perceived latency, no spinner)
                    renderCandidatesResponse(idbRecord.data, targetPage);

                    // Stale-While-Revalidate: only revalidate silently if older than FRESH_MS
                    if (idbRecord.age > CvTrackerIDB.FRESH_MS) {
                        return executeCandidateFetch(filterState, targetPage, true);
                    }
                    return Promise.resolve(idbRecord.data);
                } else {
                    // Cache miss: show spinner and execute network fetch
                    $('.loadingMessage').css({ display: 'flex' });
                    if ($('#kt_tagify_2').val() != '') {
                        $('.loader_search').css({ display: 'block' });
                    } else {
                        $('.page-loader').css('display', 'flex');
                    }
                    return executeCandidateFetch(filterState, targetPage, false);
                }
            }).catch(function() {
                // Fallback on IDB failure
                $('.loadingMessage').css({ display: 'flex' });
                if ($('#kt_tagify_2').val() != '') {
                    $('.loader_search').css({ display: 'block' });
                } else {
                    $('.page-loader').css('display', 'flex');
                }
                return executeCandidateFetch(filterState, targetPage, false);
            });
        }
        window.fetchCandidates = fetchCandidates;

        /* Sync hero KPI cards + stage tab badges from the same ES aggregation the table fetch already returns */
        function updateCvHeroStats(stats) {
            if (!stats) return;

            // 1. Synchronize 9 interactive status KPI cards
            if (stats.tabs) {
                if (stats.tabs.all != null) $('#kpi_all').text(Number(stats.tabs.all).toLocaleString());
                if (stats.tabs.inreview != null) $('#kpi_inreview').text(Number(stats.tabs.inreview).toLocaleString());
                if (stats.tabs.sourcing != null) $('#kpi_sourcing').text(Number(stats.tabs.sourcing).toLocaleString());
                if (stats.tabs.screening != null) $('#kpi_screening').text(Number(stats.tabs.screening).toLocaleString());
                if (stats.tabs.evaluation != null) $('#kpi_evaluation').text(Number(stats.tabs.evaluation).toLocaleString());
                if (stats.tabs.negotiation != null) $('#kpi_negotiation').text(Number(stats.tabs.negotiation).toLocaleString());
                if (stats.tabs.offered != null) $('#kpi_offered').text(Number(stats.tabs.offered).toLocaleString());
                if (stats.tabs.joined != null) $('#kpi_joined').text(Number(stats.tabs.joined).toLocaleString());
                if (stats.tabs.rejected != null) $('#kpi_rejected').text(Number(stats.tabs.rejected).toLocaleString());
            } else if (stats.total != null) {
                $('#kpi_all').text(Number(stats.total).toLocaleString());
            }

            // 2. Legacy data hooks
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
        window.updateCvHeroStats = updateCvHeroStats;

        window.patchCandidateRowStage = function(wiseId, masterStatusId, masterStatusName, cssStyle, subStageName, oldMasterStatusId, candidateId, broadcastToTabs = true) {
            if (!wiseId && !candidateId && !masterStatusId) return;
            var wid = wiseId ? String(wiseId) : null;
            var cid = candidateId ? String(candidateId) : null;

            // Invalidate local client caches to guarantee 0% stale data
            if (broadcastToTabs && typeof invalidateCvTrackerCaches === 'function') {
                invalidateCvTrackerCaches({
                    wise_id: wid,
                    candidate_id: cid,
                    master_status_id: masterStatusId,
                    master_status_name: masterStatusName,
                    css_style: cssStyle,
                    sub_stage_name: subStageName,
                    old_master_status_id: oldMasterStatusId
                });
            } else if (!broadcastToTabs) {
                if (typeof CvTrackerCache !== 'undefined') CvTrackerCache.clear();
                if (typeof CvTrackerIDB !== 'undefined') CvTrackerIDB.clear();
            }

            var $row = $();
            if (wid) $row = $row.add($('tr[data-wise-id="' + wid + '"], .aptk-cand-card[data-wise-id="' + wid + '"]'));
            if (cid && !$row.length) $row = $row.add($('tr[data-candidate-id="' + cid + '"], .aptk-cand-card[data-candidate-id="' + cid + '"]'));

            var STAGE_ID_TO_TAB = {
                8: 'inreview',
                1: 'sourcing',
                2: 'screening',
                3: 'evaluation',
                4: 'negotiation',
                5: 'offered',
                6: 'joined',
                7: 'rejected',
                9: 'rejected'
            };

            var currentStageTab = $('#stage_tab_filter_val').val() || activeStageTab || '';
            if (currentStageTab && currentStageTab !== 'all' && oldMasterStatusId && masterStatusId && String(oldMasterStatusId) !== String(masterStatusId)) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                    if ($('#candidatetList tr').length === 0 && $('#candidatetList .aptk-cand-card').length === 0) {
                        var page = $('#paginationcandidate .active_disabled').data('page') || page_no || 1;
                        fetchCandidates(page);
                    }
                });
            } else if ($row.length) {
                var $stageLink = $row.find('.stage_sub_stage, .client_stage_sub_stage, .aptk-stage-trigger');
                if ($stageLink.length) {
                    if (cssStyle && masterStatusName) {
                        var $badge = $row.find('.aptk-stage-pill, .stage_sub_stage .badge, .client_stage_sub_stage .badge');
                        $badge.attr('class', 'aptk-stage-pill fw-bolder fs-7 ' + cssStyle + ' px-2 py-1');
                        $badge.text(masterStatusName.substring(0, 12));
                    }
                    if (subStageName !== undefined && subStageName !== null) {
                        $row.find('.aptk-substage-txt strong, .stage_sub_stage .text-muted').text(subStageName || '');
                    }
                    $row.css('transition', 'background-color 0.4s ease');
                    var origBg = $row.css('background-color');
                    $row.css('background-color', '#f0fdf4');
                    setTimeout(function() {
                        $row.css('background-color', origBg || '');
                    }, 1200);
                }
            }

            if (oldMasterStatusId && STAGE_ID_TO_TAB[oldMasterStatusId]) {
                var oldKey = STAGE_ID_TO_TAB[oldMasterStatusId];
                var $oldEl = $('#kpi_' + oldKey + ', [data-cvh-tab="' + oldKey + '"]');
                if ($oldEl.length) {
                    var cnt = Math.max(0, parseInt($oldEl.first().text().replace(/,/g, ''), 10) - 1);
                    $oldEl.text(cnt.toLocaleString());
                }
            }
            if (masterStatusId && STAGE_ID_TO_TAB[masterStatusId]) {
                var newKey = STAGE_ID_TO_TAB[masterStatusId];
                var $newEl = $('#kpi_' + newKey + ', [data-cvh-tab="' + newKey + '"]');
                if ($newEl.length) {
                    var cnt = (parseInt($newEl.first().text().replace(/,/g, ''), 10) || 0) + 1;
                    $newEl.text(cnt.toLocaleString());
                }
            }
        };

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

        // Once a client-owned candidate has been sourced, the Job Stage badge
        // is no longer a stage-change trigger (that authority now belongs to
        // the client) — it's this notice instead, so the recruiter understands
        // why the row no longer opens the drawer, rather than it silently
        // doing nothing.
        $(document).on('click', '.client_stage_locked_notice', function (e) {
            e.preventDefault();
            Swal.fire({
                width: 460,
                padding: '1.75em',
                showCloseButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#2563EB',
                html: `
                    <div style="font-family:'Poppins',sans-serif;text-align:left;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                            <span style="width:44px;height:44px;border-radius:12px;background:#EFF6FF;color:#2563EB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
                            </span>
                            <div style="font-size:15px;font-weight:700;color:#181c32;line-height:1.3;">This Stage Is Managed by the Client</div>
                        </div>
                        <div style="font-size:13px;line-height:1.65;color:#4b5563;">
                            This candidate has been sourced and is now part of the client's active hiring pipeline. From this point forward, every stage decision — review, evaluation, offer, and rejection — is owned and managed directly by the client's hiring team.
                        </div>
                        <div style="font-size:13px;line-height:1.65;color:#4b5563;margin-top:10px;">
                            To discuss this candidate's progress or request a stage change, please coordinate with the client's Hiring Manager.
                        </div>
                    </div>
                `
            });
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
            if (typeof cvhCloseOrgPanel === 'function') cvhCloseOrgPanel();
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
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
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
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
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
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
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
            page_no = 1;
            localStorage.setItem('currentPage', 1);
            fetchCandidates(1);
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
