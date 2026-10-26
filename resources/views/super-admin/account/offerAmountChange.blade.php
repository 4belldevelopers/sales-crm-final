@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
    $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];
    $currency = 0;
    $salary_ctc = '1000000';
    $salary_mode = 0;
    $monthly_gross = '';
    $yearly_gross = '';
    $monthly_bounus_gross = '';
    $yearly_bonus_gross = '';
    $monthly_insurence_gross = '';
    $yearly_insurence_gross = '';
    $yearly_final_gross = '';
    $monthly_final_gross = '';
    $first_text = 'Annually';
    $secont_text = 'Hourly';
@endphp

@push('head-script')
    <style>
        body {
            overflow-x: hidden !important;
        }

        /* Manage top gap: collapse Metronic's toolbar-fixed padding and content gap */
        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(65px + 6px) !important;
        }
        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .header-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper,
            #kt_wrapper {
                padding-top: calc(55px + 6px) !important;
            }
        }
        #kt_content,
        .content {
            padding-top: 0 !important;
        }
        #salary_breakup_container {
            padding-top: 4px !important;
        }

        /* Tier 1 Header Toolbar Card */
        .sb-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 14px !important;
        }

        .sb-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .sb-header-icon {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            font-size: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .sb-header-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
        }

        .sb-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
        }

        .sb-header-right {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .sb-search-subcard {
            height: 36px !important;
            width: 280px !important;
            max-width: 100% !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 10px !important;
            gap: 8px !important;
            transition: all 0.2s ease !important;
        }

        .sb-search-subcard:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .sb-search-subcard input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-size: 12px !important;
            font-family: inherit !important;
            color: #1e293b !important;
            width: 100% !important;
        }

        .sb-search-clear {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            color: #94a3b8 !important;
            cursor: pointer !important;
            font-size: 11px !important;
            line-height: 1 !important;
        }

        .sb-search-clear:hover {
            color: #475569 !important;
        }

        .sb-save-btn {
            height: 36px !important;
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 0 16px !important;
            display: inline-flex !important;
            align-items: center !important;
            color: #ffffff !important;
            transition: all 0.2s ease !important;
        }

        .sb-save-btn:hover {
            background: #008cd8 !important;
            border-color: #008cd8 !important;
            color: #ffffff !important;
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
        .co-details-card { border: 1px solid #f1f1f4; border-radius: 12px; background: #fff; }
        .co-details-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .co-conf-wrap { min-width: 240px; }
        .co-conf-hint { display: block; font-size: 11px; color: #3699ff; margin-top: 3px; text-align: right; text-decoration: none; }
        .co-conf-hint:hover { text-decoration: underline; }
        .co-field-grid { margin: 0; }
        .co-field { margin-bottom: 14px; }
        .co-flabel { font-size: 12px !important; font-weight: 600; color: #3f4254; display: flex; align-items: center; margin-bottom: 5px; }
        .co-flabel.required::after { content: "*"; color: #f1416c; margin-left: 4px; font-size: 13px; font-weight: 700; }
        .co-field-hint { font-size: 10px; color: #a1a5b7; margin-top: 3px; }
        .co-details-card .form-control,
        .co-details-card .form-select {
            background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 8px !important;
            min-height: 42px; height: 42px; font-size: 13px !important; color: #3f4254 !important; box-shadow: none !important;
        }
        .co-details-card .form-control:focus,
        .co-details-card .form-select:focus { border-color: #3699ff !important; }
        @media (max-width: 700px) { .co-field { flex: 0 0 100%; max-width: 100%; } }

        /* ── Components card section headings + validation banner ── */
        .co-components-card { border: 1px solid #f1f1f4; border-radius: 12px; background: #fff; }
        .co-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
        .co-section-head--flex { display: flex; align-items: center; justify-content: space-between; }
        .co-section-title { font-size: 14px !important; font-weight: 700; color: #181c32; margin: 0; }
        .co-section-note { font-size: 12px; font-weight: 500; color: #a1a5b7; }
        .co-section-sub { font-size: 11px; color: #a1a5b7; margin-top: 2px; }
        .co-valid-banner {
            display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600; margin: 8px 0 12px;
        }
        .co-valid-banner .co-valid-ico { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
        /* Below 100% -> red (per spec); exactly 100% -> green */
        .co-valid-banner--warn { background: #fff5f8; color: #f1416c; }
        .co-valid-banner--warn .co-valid-ico { background: #f1416c; }
        .co-valid-banner--ok { background: #e8fff3; color: #17a673; }
        .co-valid-banner--ok .co-valid-ico { background: #50cd89; }

        /* Row numbering via CSS counter */
        #pointList tbody, #bonusList tbody { counter-reset: co-rownum; }
        #pointList tbody tr.row-template:not(.d-none), #bonusList tbody tr.row-template:not(.d-none) { counter-increment: co-rownum; }

        /* ── Component tables ── */
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

        /* Name column generous room so # + drag + name + badge sit on ONE line */
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

        /* delete -> red trash button */
        #pointList .remove_points, #bonusList .remove_points {
            background: #fff5f8 !important; color: #f1416c !important; border: none !important;
            border-radius: 7px !important; width: 34px; height: 34px; padding: 0 !important; margin: 0 !important;
            display: inline-flex; align-items: center; justify-content: center;
        }
        #pointList .remove_points:hover, #bonusList .remove_points:hover { background: #f1416c !important; color: #fff !important; }
        #pointList .remove_points svg, #bonusList .remove_points svg { display: none; }
        #pointList .remove_points .svg-icon svg, #bonusList .remove_points .svg-icon svg { display: none; }
        #pointList .remove_points .svg-icon, #bonusList .remove_points .svg-icon {
            width: 16px; height: 16px; display: inline-block; background-color: currentColor;
            -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat;
            -webkit-mask-size: contain; mask-size: contain;
            -webkit-mask-position: center; mask-position: center;
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2024%2024'%3E%3Crect%20x='4'%20y='6'%20width='16'%20height='2'%20rx='1'/%3E%3Crect%20x='9'%20y='3'%20width='6'%20height='2'%20rx='1'/%3E%3Cpath%20d='M6%209h12l-1.2%2011.2A2%202%200%200114.8%2022H9.2a2%202%200%2001-2-1.8L6%209z'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2024%2024'%3E%3Crect%20x='4'%20y='6'%20width='16'%20height='2'%20rx='1'/%3E%3Crect%20x='9'%20y='3'%20width='6'%20height='2'%20rx='1'/%3E%3Cpath%20d='M6%209h12l-1.2%2011.2A2%202%200%200114.8%2022H9.2a2%202%200%2001-2-1.8L6%209z'/%3E%3C/svg%3E");
        }
        #pointList .remove_points:disabled, #bonusList .remove_points:disabled { background: #f4f4f6 !important; color: #c1c5cc !important; cursor: not-allowed; }

        /* ── Client-side Validation Highlights ── */
        .is-invalid, input.is-invalid, select.is-invalid {
            border-color: #f1416c !important;
            background-color: #fff8f8 !important;
            box-shadow: 0 0 0 2px rgba(241, 65, 108, 0.15) !important;
        }
        .invalid-feedback {
            color: #f1416c !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            margin-top: 4px !important;
            display: none;
        }
        .is-invalid ~ .invalid-feedback {
            display: block !important;
        }

        /* Total Main (indigo tint) */
        #pointList tfoot tr.co-total-row > th { background: #eef3ff !important; }
        #pointList tfoot tr.co-total-row input { background: transparent !important; color: #4f46e5 !important; font-weight: 700 !important; border-color: transparent !important; }
        #pointList tfoot tr.co-total-row input.co-pct-invalid { color: #f1416c !important; }
        #pointList tfoot tr.co-total-row th:has(> input.co-pct-invalid)::after { color: #f1416c !important; }

        /* Total Additional (violet tint) */
        #bonusList tfoot tr.co-total-row > th { background: #f5f0ff !important; }
        #bonusList tfoot tr.co-total-row input { background: transparent !important; color: #7c3aed !important; font-weight: 700 !important; border-color: transparent !important; }

        /* Final CTC */
        .co-finalctc-card { border: 1px solid #b7ebc6 !important; border-radius: 12px; background: #fff; }
        #final_pay { background: transparent; border: none; margin-top: 4px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
        #final_pay thead th { background: transparent !important; }
        #final_pay .form-control, #final_pay .form-select {
            background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 7px !important;
            box-shadow: none !important; min-height: 36px; height: 36px; font-size: 13px !important;
            color: #3f4254 !important; padding: 4px 10px !important;
        }
        #final_pay tfoot th { vertical-align: middle; background: #e3f7e9 !important; }
        #final_pay tfoot .form-control, #final_pay tfoot .form-select { background: transparent !important; color: #0f7a3d !important; font-weight: 700 !important; border-color: transparent !important; box-shadow: none !important; }
        #final_pay tfoot input[value*="Total CTC"] { color: #0b5c2e !important; }

        /* Applicable column: widen so "Annually & Monthly" shows fully */
        #pointList .basic_view, #bonusList .bonus_view { min-width: 150px !important; width: auto !important; }
        #pointList thead th:nth-child(2), #bonusList thead th:nth-child(2) { min-width: 150px; }

        /* "%" INSIDE the Allocation input box (data rows); "x" multiplier sits in the spacer cell */
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

        /* "%" appended inside the Total (Main/Additional) + Final CTC percentage cells */
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

        /* Header "+ Add" buttons */
        .co-add-btn { background: #eef3ff; color: #4f46e5; border: 1px solid #e0e7ff; border-radius: 8px; padding: 8px 14px !important; font-size: 12px !important; font-weight: 600; cursor: pointer; margin: 0 !important; white-space: nowrap; }
        .co-add-btn:hover { background: #4f46e5; color: #fff; }
        #pointList .addRowBtn, #bonusList .addRowBtnbonus { display: none !important; }

        /* ── Row #, drag-handle, Main/Additional badge ── */
        .co-row-lead { display: inline-flex; align-items: center; gap: 6px; margin-right: 6px; vertical-align: middle; }
        .co-drag-handle { color: #c1c5cc; font-size: 12px; line-height: 1; cursor: grab; letter-spacing: -1px; user-select: none; }
        .co-row-num { color: #a1a5b7; font-size: 12px; font-weight: 600; min-width: 14px; display: inline-block; }
        .co-comp-badge { display: inline-block; font-size: 10px !important; font-weight: 700; padding: 2px 8px; border-radius: 99px; margin: 0; vertical-align: middle; white-space: nowrap; }
        .co-comp-badge--main { background: #eef3ff !important; color: #4f46e5 !important; }
        .co-comp-badge--add  { background: #f5f0ff !important; color: #7c3aed !important; }

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

        /* ── Footer actions ── */
        .co-footer-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 15px; margin-bottom: 25px; }
        .co-btn-close { background: #fff !important; border: 1px solid #d7dae3 !important; color: #5e6278 !important; font-weight: 600 !important; min-width: 110px; height: 40px; border-radius: 8px; }
        .co-btn-close:hover { background: #f5f8fa !important; border-color: #c4c9d6 !important; }
        .co-btn-save { background: #1b84ff !important; border: 1px solid #1b84ff !important; color: #fff !important; font-weight: 600 !important; min-width: 160px; height: 40px; border-radius: 8px; transition: background .15s, opacity .15s; }
        .co-btn-save:hover:not(:disabled) { background: #0b6cdb !important; border-color: #0b6cdb !important; }
        .co-btn-save:disabled { opacity: .72 !important; cursor: not-allowed !important; }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-5 pt-1 pb-4" id="salary_breakup_container">

        <!-- Tier 1: Top Header Toolbar Card with Dynamic Search -->
        <div class="sb-header-card">
            <div class="sb-header-left">
                <div class="sb-header-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div class="sb-header-title">Salary Breakup Master</div>
                    <div class="sb-header-sub">Configure default platform salary breakdown formulas & preview live CTC calculations.</div>
                </div>
            </div>
            <div class="sb-header-right">
                <div class="sb-search-subcard">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" id="inputComponentSearch" placeholder="Search components..." autocomplete="off">
                    <button type="button" class="sb-search-clear" id="btnComponentSearchClear" style="display: none;" title="Clear search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary fw-bold sb-save-btn" id="btnTopSaveBreakup">
                    <i class="fa fa-save me-1"></i> Save Salary Breakup
                </button>
            </div>
        </div>

        <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="offer_id" value="{{ $offer_id }}">

            <div class="row">
                {{-- CARD 1: Compensation Details --}}
                <div class="col-lg-12">
                    <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-details-card">
                        <div class="co-details-head">
                            <div>
                                <h5 class="co-section-title">Compensation Details</h5>
                                <div class="co-section-sub">Enter the offer details to generate the compensation structure</div>
                            </div>
                            <div class="co-conf-wrap">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--indigo">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/>
                                        </svg>
                                    </span>
                                    Confidentiality Status
                                </label>
                                <select class="form-select form-select-solid" arial-label="Select Confidential" id="confidential" name="confidential">
                                    <option value="0" {{ ($confidential ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ ($confidential ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                <a href="javascript:void(0);" class="co-conf-hint">Who can view the Salary</a>
                            </div>
                        </div>

                        <div class="row co-field-grid">
                            <div class="col-lg-4 co-field">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M14.5 9a2.5 2.5 0 0 0-2.5-1.5c-1.4 0-2.5.9-2.5 2s1.1 2 2.5 2 2.5.9 2.5 2-1.1 2-2.5 2A2.5 2.5 0 0 1 9.5 15M12 6v1.5M12 16.5V18"/>
                                        </svg>
                                    </span>
                                    Currency
                                </label>
                                <select class="form-select form-select-solid" arial-label="Select Currency" id="current_ctc_cur" name="current_ctc_cur">
                                    <option value="0" {{ ($currency ?? 0) == 0 ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    <option value="1" {{ ($currency ?? 0) == 1 ? 'selected' : '' }}>EUR</option>
                                    <option value="2" {{ ($currency ?? 0) == 2 ? 'selected' : '' }}>JPY</option>
                                    <option value="3" {{ ($currency ?? 0) == 3 ? 'selected' : '' }}>GBP</option>
                                    <option value="4" {{ ($currency ?? 0) == 4 ? 'selected' : '' }}>USD</option>
                                    <option value="5" {{ ($currency ?? 0) == 5 ? 'selected' : '' }}>AED</option>
                                </select>
                            </div>
                            <div class="col-lg-4 co-field">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--green">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="6" width="18" height="12" rx="2"/>
                                            <circle cx="12" cy="12" r="2.2"/>
                                        </svg>
                                    </span>
                                    Enter Amount (CTC)
                                </label>
                                <input type="text" class="form-control form-control-solid" name="ctc" id="ctc" placeholder="Enter current CTC" value="{{ @$salary_ctc ?: '500000' }}">
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                            <div class="col-lg-4 co-field">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--indigo">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
                                        </svg>
                                    </span>
                                    Salary Breakdown Format
                                </label>
                                <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                                    <option value="2" {{ ($salary_mode ?? 0) == 2 ? 'selected' : '' }}>Hourly</option>
                                    <option value="1" {{ ($salary_mode ?? 0) == 1 ? 'selected' : '' }}>Monthly</option>
                                    <option value="3" {{ ($salary_mode ?? 0) == 3 ? 'selected' : '' }}>Annually</option>
                                    <option value="0" {{ empty($salary_mode) || $salary_mode == 0 ? 'selected' : '' }}>Monthly/Annually</option>
                                    <option value="4" {{ ($salary_mode ?? 0) == 4 ? 'selected' : '' }}>Hourly/Annually</option>
                                </select>
                                <div class="co-field-hint">Annually / Monthly</div>
                            </div>
                        </div>

                        <div class="row co-field-grid">
                            <div class="col-lg-4 co-field">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--indigo">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="7" width="18" height="13" rx="2"/>
                                            <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </span>
                                    Offered Designation
                                </label>
                                <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{ @$designation ?: 'Junior Java Developer' }}">
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                            <div class="col-lg-4 co-field">
                                <label class="form-label co-flabel">
                                    <span class="co-ic co-ic--amber">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M3 9h18M8 3v4M16 3v4"/>
                                        </svg>
                                    </span>
                                    Date of Joining (DOJ)
                                </label>
                                <input type="datetime-local" class="form-control form-control-solid" name="dob" id="dob" placeholder="Enter dob" value="{{ @$joined_date ?: '2026-10-29T16:46' }}" onclick="this.showPicker()">
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                            <div class="col-lg-4 co-field">
                                <label class="form-label required co-flabel">
                                    <span class="co-ic co-ic--blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="8" r="3.2"/>
                                            <path d="M3 20a6 6 0 0 1 12 0M16 11a3 3 0 0 0 0-6M21 20a5 5 0 0 0-3.5-4.8"/>
                                        </svg>
                                    </span>
                                    Employment Type
                                </label>
                                <select class="form-select form-select-solid" arial-label="Select Job Type" id="employment_type" name="employment_type">
                                    <option value="0" {{ ($employment_type ?? 0) == 0 ? 'selected' : '' }}>Full Time</option>
                                    <option value="1" {{ ($employment_type ?? 0) == 1 ? 'selected' : '' }}>Contract</option>
                                    <option value="3" {{ ($employment_type ?? 0) == 3 ? 'selected' : '' }}>Part Time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Main Components (Must Total 100%) --}}
                <div class="col-lg-12">
                    <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                        <div class="co-section-head co-section-head--flex">
                            <div>
                                <h5 class="co-section-title">Main Components <span class="co-section-note">(Must Total 100%)</span></h5>
                                <div class="co-section-sub">These are core components. Total percentage must be exactly 100%.</div>
                            </div>
                            <button type="button" class="co-add-btn" onclick="$('#pointList').find('.addRowBtn').first().trigger('click')">+ Add Main Component</button>
                        </div>

                        <div id="co_validation_banner" class="co-valid-banner co-valid-banner--ok">
                            <i class="co-valid-ico"></i>
                            <span id="co_validation_text">Total of main components: 100.00% — exactly 100%</span>
                        </div>

                        <div class="row co-components-rows">
                            <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList" role="grid" aria-describedby="myTable_info" style="width: 100%;">
                                <thead>
                                    <tr class="fw-bolder text-muted other_section {{ $section_other }}" role="row">
                                        <th class="sorting_disabled" style="width: 25%!important">Component Name</th>
                                        <th class="sorting_disabled" style="width:10% !important">Applicable</th>
                                        <th class="sorting_disabled" style="width: 7% !important;">Allocation</th>
                                        <th class="sorting_disabled" style="width: 3% !important;">&nbsp;</th>
                                        <th class="sorting_disabled" style="width: 12% !important;">Condition</th>
                                        <th class="sorting_disabled change_text_first" style="width: 15% !important;">Monthly</th>
                                        <th class="sorting_disabled change_text_second" style="width:15% !important">{{ $first_text }}</th>
                                        <th class="sorting_disabled">Action</th>
                                    </tr>
                                    <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}" role="row">
                                        <th class="sorting_disabled" style="width: 47% !important;">Component Name</th>
                                        <th class="sorting_disabled" style="width: 12% !important;">Allocation</th>
                                        <th class="sorting_disabled" style="width: 3% !important;">&nbsp;</th>
                                        <th class="sorting_disabled" style="width: 10% !important;">Condition</th>
                                        <th class="sorting_disabled change_text" style="width:15% !important">{{ $secont_text }}</th>
                                        <th class="sorting_disabled" style="width: 78px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($offer_points) && count($offer_points) > 0)
                                        @foreach ($offer_points as $key => $value)
                                            <tr class="other_section other_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_other }}" role="row">
                                                <td class="" style="width: 25%!important">
                                                    <input type="text" class="form-control form-control-solid offer_points" name="offer_point[]" id="offer_point_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="{{ $value->points }}" placeholder="Enter Point">
                                                </td>
                                                <td class="" style="width:10% !important">
                                                    <select class="form-select form-select-solid basic_view" arial-label="Select View" name="basic_view[]" id="basic_view_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" style="width: 100%;display: initial;">
                                                        <option value="0" {{ $value->view_type == 0 ? 'selected' : '' }}>Annually</option>
                                                        <option value="1" {{ $value->view_type == 1 ? 'selected' : '' }}>Monthly</option>
                                                        <option value="2" {{ $value->view_type == 2 ? 'selected' : '' }}>Annually &amp; Monthly</option>
                                                    </select>
                                                </td>
                                                <td class="" style="width:7% !important">
                                                    <input type="text" class="form-control form-select-solid option_percentage" name="option_percentage[]" id="option_percentage_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="{{ $value->percentage }}" placeholder="0">
                                                </td>
                                                <td class="" style="position: relative; width: 3% !important;">
                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span>
                                                </td>
                                                <td class="" style="width:12% !important">
                                                    <select class="form-select form-select-solid option_salarymode" arial-label="Select Annual Salary" name="option_salarymode[]" id="option_salarymode_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" style="width: 100%;display: initial;">
                                                        <option value="0" {{ $value->condition_check == 0 ? 'selected' : '' }}>Amount</option>
                                                        @if($key > 0)
                                                        <option value="1" {{ $value->condition_check == 1 ? 'selected' : '' }}>Basic</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td class="" style="width:15% !important">
                                                    <input type="text" class="form-control form-control-solid offer_monthly" name="offer_monthly[]" id="offer_monthly_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td class="" style="width:15% !important">
                                                    <input type="text" class="form-control form-control-solid offer_yearly {{ $key > 0 ? 'other_salary_get' : 'basic_salary_get' }}" name="offer_yearly[]" id="offer_yearly_{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td style="width: 13% !important ;display: inline-flex;">
                                                    <button type="button" class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0" {{ $key > 0 ? '' : 'disabled' }}>
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect></g></svg>
                                                        </span>
                                                    </button>
                                                    <button type="button" id="addRowBtn" class="btn btn-sm btn-primary py-4 addRowBtn me-0" style="">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                    <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if (isset($offer_point_hourly) && count($offer_point_hourly) > 0)
                                        @foreach ($offer_point_hourly as $key => $value)
                                            <tr class="hourly_section hourly_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_hourly }}" role="row">
                                                <td class="" style="width: 47% !important;">
                                                    <input type="text" class="form-control form-control-solid offer_point_hourly" name="offer_point_hourly[]" id="offer_point_hourly{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="{{ $value->points }}" placeholder="Enter Point">
                                                </td>
                                                <td class="" style="width: 12% !important;">
                                                    <input type="text" class="form-control form-select-solid option_percentage_hourly" name="option_percentage_hourly[]" id="option_percentage_hourly{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="{{ $value->percentage }}" placeholder="0">
                                                </td>
                                                <td class="" style="position: relative; width: 3% !important;">
                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span>
                                                </td>
                                                <td class="" style="width: 10% !important;">
                                                    <select class="form-select form-select-solid option_salarymode_hourly" arial-label="Select Annual Salary" name="option_salarymode_hourly[]" id="option_salarymode_hourly{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" style="width: 100%;display: initial;">
                                                        <option value="0" {{ $value->condition_check == 0 ? 'selected' : '' }}>Amount</option>
                                                        @if($key > 0)
                                                        <option value="1" {{ $value->condition_check == 1 ? 'selected' : '' }}>Basic</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td class="" style="width: 15% !important;">
                                                    <input type="text" class="form-control form-control-solid offer_hourly {{ $key > 0 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}" name="offer_hourly[]" id="offer_hourly{{ $value->id }}" data-id="{{ $value->id }}" data-offerid="{{ $value->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td style="width: 13% !important ;display: inline-flex;">
                                                    <button type="button" class="btn btn-sm py-4 btn-light-{{ $key > 0 ? 'danger' : 'info' }} font-weight-bolder remove_points me-0" {{ $key > 0 ? '' : 'disabled' }}>
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect></g></svg>
                                                        </span>
                                                    </button>
                                                    <button type="button" id="" class="btn btn-sm py-4 btn-primary addRowBtn me-0" style="">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                    <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>

                                <tfoot class="other_section {{ $section_other }}">
                                    <tr>
                                        <th colspan="8" class="sorting_disabled" style="width: 102px;">&nbsp;</th>
                                    </tr>
                                    <tr class="co-total-row">
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid" value="Total Main Components" readonly style="cursor: not-allowed;background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid totalpercentage" name="totalpercentage" value="" id="totalpercentage" style="background: #eef3ff;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid totalMonthly" name="totalMonthly" value="{{ @$monthly_gross }}" id="totalMonthly" style="background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid totalYearly" name="totalYearly" value="{{ @$yearly_gross }}" id="totalYearly" style="background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled">&nbsp;</th>
                                    </tr>
                                </tfoot>

                                <tfoot class="hourly_section {{ $section_hourly }}">
                                    <tr>
                                        <th colspan="6" class="sorting_disabled" style="width: 102px;">&nbsp;</th>
                                    </tr>
                                    <tr class="co-total-row">
                                        <th class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid" value="Total Main Components" readonly style="cursor: not-allowed;background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid totalpercentagehouly" name="totalpercentagehouly" value="" id="totalpercentagehouly" style="background: #eef3ff;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #eef3ff;">
                                        </th>
                                        <th class="sorting_disabled" colspan="2">
                                            <input type="text" class="form-control form-control-solid totalhourlyhourly" name="totalhourlyhourly" value="{{ @$yearly_gross }}" id="totalhourlyhourly" style="width: 88%;background: #eef3ff;">
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: Additional Components (Above 100% - Optional) --}}
                <div class="col-lg-12">
                    <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                        <div class="co-section-head co-section-head--flex">
                            <div>
                                <h5 class="co-section-title">Additional Components <span class="co-section-note">(Above 100% - Optional)</span></h5>
                                <div class="co-section-sub">These components are additional and will be added on top of the main 100%.</div>
                            </div>
                            <button type="button" class="co-add-btn" onclick="$('#bonusList').find('.addRowBtnbonus').first().trigger('click')">+ Add Additional Component</button>
                        </div>

                        <div id="co_additional_total" class="co-info-line">Total additional components: 0.00% (Allowed above 100%)</div>

                        {{-- Empty state --}}
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
                            <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList" role="grid" aria-describedby="bonusList_info" style="width: 100%;">
                                <thead>
                                    <tr class="fw-bolder text-muted other_section {{ $section_other }}">
                                        <th class="sorting_disabled" style="width: 25%!important">Component Name</th>
                                        <th class="sorting_disabled" style="width:10% !important">Applicable</th>
                                        <th class="sorting_disabled" style="width: 7% !important;">Allocation</th>
                                        <th class="sorting_disabled" style="width: 3% !important;">&nbsp;</th>
                                        <th class="sorting_disabled" style="width: 12% !important;">Condition</th>
                                        <th class="sorting_disabled" style="width: 15% !important;">Monthly</th>
                                        <th class="sorting_disabled" style="width:15% !important">{{ $first_text }}</th>
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
                                    {{-- Hidden template rows used for cloning in addRowBtnbonus --}}
                                    <tr class="other_section other_section_row row-template {{ $section_other }}" style="display:none;">
                                        <th colspan="7" class="sorting_disabled" style="width: 102px;">Additional Component</th>
                                        <th class="sorting_disabled" style="width: 102px;position: relative;">
                                            <button type="button" id="addRowBtnbonus" class="btn btn-primary btn-sm py-4 addRowBtnbonus me-0" style="position: absolute; top: -25px;">
                                                <span class="svg-icon svg-icon svg-icon me-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                            <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </button>
                                        </th>
                                    </tr>
                                    <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}" style="display:none;">
                                        <th colspan="6" class="sorting_disabled other_text" style="width: 102px;">Additional Component</th>
                                        <th class="sorting_disabled" style="width: 102px;position: relative;">
                                            <button type="button" id="" class="btn btn-primary btn-sm py-4 me-0 addRowBtnbonus" style="position: absolute; top: -25px;">
                                                <span class="svg-icon svg-icon svg-icon me-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                            <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($offer_point_other) && count($offer_point_other) > 0)
                                        @foreach ($offer_point_other as $offer)
                                            <tr class="other_section other_section_row row-template remove_row {{ $section_other }}" role="row">
                                                <td class="" style="width: 25% !important;">
                                                    <input type="text" class="form-control form-control-solid bonus_points" name="bonus_points[]" id="bonus_points_{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="{{ $offer->points }}" placeholder="Enter Point">
                                                </td>
                                                <td class="" style="width: 10% !important;">
                                                    <select class="form-select form-select-solid bonus_view" arial-label="Select View" name="bonus_view[]" id="bonus_view_{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" style="width: 100%;display: initial;">
                                                        <option value="0" {{ @$offer->view_type == 0 ? 'selected' : '' }}>Annually</option>
                                                        <option value="1" {{ @$offer->view_type == 1 ? 'selected' : '' }}>Monthly</option>
                                                        <option value="2" {{ @$offer->view_type == 2 ? 'selected' : '' }}>Annually &amp; Monthly</option>
                                                    </select>
                                                </td>
                                                <td class="" style="width: 7% !important;">
                                                    <input type="text" class="form-control form-select-solid bonus_option_percentage" name="bonus_option_percentage[]" id="bonus_option_percentage{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="{{ $offer->percentage }}" placeholder="0">
                                                </td>
                                                <td class="" style="position: relative; width: 3% !important;">
                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span>
                                                </td>
                                                <td class="" style="width: 12% !important;">
                                                    <select class="form-select form-select-solid bonus_option_salarymode" arial-label="Select Annual Salary" name="bonus_option_salarymode[]" id="bonus_option_salarymode_{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" style="width: 100%;display: initial;">
                                                        <option value="0" {{ $offer->condition_check == 0 ? 'selected' : '' }}>Amount</option>
                                                        <option value="1" {{ $offer->condition_check == 1 ? 'selected' : '' }}>Basic</option>
                                                    </select>
                                                </td>
                                                <td class="" style="width: 15% !important;">
                                                    <input type="text" class="form-control form-control-solid bonus_offer_monthly" name="bonus_offer_monthly[]" id="bonus_offer_monthly_{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td class="" style="width: 15% !important;">
                                                    <input type="text" class="form-control form-control-solid bonus_offer_yearly" name="bonus_offer_yearly[]" id="bonus_offer_yearly{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td style="width: 13% !important">
                                                    <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder remove_points py-4 me-0">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect></g></svg>
                                                        </span>
                                                    </button>
                                                    <button type="button" id="addRowBtnbonus" class="btn btn-sm btn-primary addRowBtnbonus py-4 me-0" style="">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                    <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if (isset($offer_point_other_hourly) && count($offer_point_other_hourly) > 0)
                                        @foreach ($offer_point_other_hourly as $offer)
                                            <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}" role="row">
                                                <td class="" style="width: 47% !important;">
                                                    <input type="text" class="form-control form-control-solid bonus_point_hourly" name="bonus_point_hourly[]" id="bonus_points_hourly{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="{{ $offer->points }}" placeholder="Enter Point">
                                                </td>
                                                <td class="" style="width: 12% !important;">
                                                    <input type="text" class="form-control form-select-solid bonus_option_percentage_hourly" name="bonus_option_percentage_hourly[]" id="bonus_option_percentage_hourly{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="{{ $offer->percentage }}" placeholder="0">
                                                </td>
                                                <td class="" style="position: relative; width: 3% !important;">
                                                    <span class="co-mult-sign" style="position: absolute; top: 10px; left: 2px; color:#a1a5b7; font-size:14px; font-weight:600;">&times;</span>
                                                </td>
                                                <td class="" style="width: 10% !important;">
                                                    <select class="form-select form-select-solid bonus_option_salarymode_hourly" arial-label="Select Annual Salary" name="bonus_option_salarymode_hourly[]" id="bonus_option_salarymode_hourly{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" style="width: 100px;display: initial;">
                                                        <option value="0" {{ $offer->condition_check == 0 ? 'selected' : '' }}>CTC</option>
                                                        <option value="1" {{ $offer->condition_check == 1 ? 'selected' : '' }}>Basic</option>
                                                    </select>
                                                </td>
                                                <td class="" style="width: 15% !important;">
                                                    <input type="text" class="form-control form-control-solid bonus_offer_hourly" name="bonus_offer_hourly[]" id="bonus_offer_hourly{{ $offer->id }}" data-id="{{ $offer->id }}" data-offerid="{{ $offer->id }}" value="" placeholder="0.00">
                                                </td>
                                                <td style="width: 13% !important">
                                                    <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder remove_points py-4 me-0">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect></g></svg>
                                                        </span>
                                                    </button>
                                                    <button type="button" id="" class="btn btn-sm btn-primary me-0 py-4 addRowBtnbonus" style="">
                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                    <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>

                                <tfoot class="other_section {{ $section_other }}">
                                    <tr>
                                        <th colspan="8" class="sorting_disabled" style="width: 102px;">&nbsp;</th>
                                    </tr>
                                    <tr class="co-total-row">
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid" value="Total Additional Components" readonly style="cursor: not-allowed;background: #f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" readonly class="form-control form-control-solid totalpercentagebonus" name="totalpercentagebonus" value="" id="totalpercentagebonus" style="background:#f5f0ff;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled" style="width: 78px;">
                                            <input type="text" readonly class="form-control form-control-solid totalMonthlybonus" name="totalMonthlybonus" id="totalMonthlybonus" value="{{ @$monthly_bounus_gross }}" style="background:#f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" readonly class="form-control form-control-solid totalYearlybonus" name="totalYearlybonus" id="totalYearlybonus" value="{{ @$yearly_bonus_gross }}" style="background:#f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled">&nbsp;</th>
                                    </tr>
                                </tfoot>

                                <tfoot class="hourly_section {{ $section_hourly }}">
                                    <tr>
                                        <th colspan="6" class="sorting_disabled" style="width: 102px;">&nbsp;</th>
                                    </tr>
                                    <tr class="co-total-row">
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid" value="Total Additional Components" readonly style="cursor: not-allowed;background: #f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled">
                                            <input type="text" readonly class="form-control form-control-solid totalhourlypercentagebonus" name="totalhourlypercentagebonus" value="" id="totalhourlypercentagebonus" style="background:#f5f0ff;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #f5f0ff;">
                                        </th>
                                        <th class="sorting_disabled" colspan="1">
                                            <input type="text" readonly class="form-control form-control-solid totalhourlyybonus" name="totalhourlyybonus" value="{{ @$yearly_bonus_gross }}" id="totalhourlyybonus" style="background:#f5f0ff;">
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: Final CTC (Cost to Company) --}}
                <div class="col-lg-12">
                    <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-finalctc-card">
                        <div class="co-section-head">
                            <div>
                                <h5 class="co-section-title">Final CTC <span class="co-section-note">(Cost to Company)</span></h5>
                                <div class="co-section-sub">Summary of the total cost including main and additional components.</div>
                            </div>
                        </div>

                        <div class="row">
                            <table class="table-row-dashed fs-6 gy-5 no-footer" id="final_pay" role="grid" aria-describedby="final_pay_info" style="width: 100%;">
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
                                            <input type="text" class="form-control form-control-solid totalpercentagefinal" name="totalpercentagefinal" value="" id="totalpercentagefinal" style="background: #7de96f40;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled" style="width: 15% !important;">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                        </th>
                                        <th class="sorting_disabled" style="width: 15% !important;">
                                            <input type="text" class="form-control form-control-solid finalpaytotalMonthly" name="finalpaytotalMonthly" id="finalpaytotalMonthly" value="{{ @$monthly_final_gross }}" style="background: #7de96f40;">
                                        </th>
                                        <th class="sorting_disabled" style="width: 15% !important;">
                                            <input type="text" class="form-control form-control-solid finalpaytotalYearly" name="finalpaytotalYearly" id="finalpaytotalYearly" value="{{ @$yearly_final_gross }}" style="background: #7de96f40;">
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
                                            <input type="text" class="form-control form-control-solid totalpercentagefinalhourly" name="totalpercentagefinalhourly" value="" id="totalpercentagefinalhourly" style="background: #7de96f40;">
                                        </th>
                                        <th colspan="2" class="sorting_disabled" style="width: 13% !important;">
                                            <input type="text" class="form-control form-control-solid show_currency" name="show_currency" value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                        </th>
                                        <th class="sorting_disabled" colspan="2">
                                            <input type="text" class="form-control form-control-solid finalpaytotalhourly" name="finalpaytotalhourly" id="finalpaytotalhourly" value="{{ @$yearly_final_gross }}" style="width:88%;background: #7de96f40;">
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Hourly/Annually disclaimer --}}
                <div id="text_for_hourly_annually" class="d-none mx-2 mb-3">
                    <p class="fs-8 text-muted"><b>Note:</b> *The per-hour salary is calculated based on 8 hours per day, 5 days per week, and 2,080 hours per year. The annual salary is subject to adjustments based on the actual number of hours worked</p>
                </div>

                {{-- Informational Footer Boxes --}}
                <div class="col-lg-12">
                    <div class="co-howit-box mx-2">
                        <div class="co-howit-col">
                            <div class="co-howit-title"><i class="co-howit-ico"></i> How it works?</div>
                            <div class="co-howit-text">Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</div>
                        </div>
                        <div class="co-howit-col">
                            <div class="co-howit-title"><i class="co-howit-ico co-howit-ico--amber"></i> Before you proceed</div>
                            <div class="co-howit-text">Double-check the currency, condition (Amount/Basic) and allocation of every component — these values drive the offer letter and cannot be edited once the offer is generated.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <hr class="mx-2">
                </div>

                {{-- Action Buttons --}}
                <div class="col-lg-12">
                    <div class="co-footer-actions mx-2">
                        <button type="button" class="btn co-btn-close" onclick="window.history.back()">Close</button>
                        <input type="submit" name="save" value="Save & Generate Structure" id="save-form" class="btn co-btn-save">
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        $(document).ready(function() {

            // Helper functions
            function coNum(v) { v = (v == null ? '' : String(v)).replace(/[^0-9.\-]/g, ''); var n = parseFloat(v); return isNaN(n) ? 0 : n; }
            function coMoney(n) { try { return Number(Math.round(n)).toLocaleString('en-IN'); } catch (e) { return Math.round(n); } }
            function coVisibleRows(sel) {
                return $(sel + ' tr.row-template').filter(function () { return $(this).css('display') !== 'none' && !$(this).hasClass('d-none'); });
            }

            // Injects row #, drag-handle and Main/Additional badge
            function coEnhanceRows() {
                var enhance = function (tbodySel, nameClasses, badgeClass, badgeText) {
                    var n = 0;
                    coVisibleRows(tbodySel).each(function () {
                        n++;
                        var $name = $(this).find(nameClasses).first();
                        if (!$name.length) return;
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

            // Toggles empty state for additional components
            function coToggleAdditionalEmpty() {
                var $empty = $('#co_additional_empty');
                if (!$empty.length) return;
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

            // Top save button triggers main save button
            $('#btnTopSaveBreakup').on('click', function(e) {
                e.preventDefault();
                $('#save-form').trigger('click');
            });

            // Dynamic Component Search Filter (200ms debounce)
            var searchDebounceTimer = null;
            $('#inputComponentSearch').on('input keyup', function() {
                clearTimeout(searchDebounceTimer);
                var query = $(this).val().trim().toLowerCase();
                if (query.length > 0) {
                    $('#btnComponentSearchClear').show();
                } else {
                    $('#btnComponentSearchClear').hide();
                }

                searchDebounceTimer = setTimeout(function() {
                    filterComponents(query);
                }, 200);
            });

            $('#btnComponentSearchClear').on('click', function() {
                $('#inputComponentSearch').val('').trigger('keyup');
                $(this).hide();
            });

            function filterComponents(query) {
                var mode = $('#current_ctc_mode').val();
                var isHourly = (mode == 1 || mode == 2 || mode == 3);
                var activeRowClass = isHourly ? '.hourly_section_row' : '.other_section_row';
                var nameInputClass = isHourly ? '.offer_point_hourly, .bonus_point_hourly' : '.offer_points, .bonus_points';

                $('#pointList tbody tr' + activeRowClass + ', #bonusList tbody tr' + activeRowClass).each(function() {
                    var $row = $(this);
                    if ($row.hasClass('d-none')) return;
                    var name = $row.find(nameInputClass).val() || '';
                    if (!query || name.toLowerCase().indexOf(query) !== -1) {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            }

            function updateAllocationMeter() {
                var mode = $('#current_ctc_mode').val();
                var isHourly = (mode == 1 || mode == 2 || mode == 3);
                var mainPct = 0;
                var addPct = 0;

                if (isHourly) {
                    $('#pointList tbody .hourly_section_row:not(.d-none)').each(function() {
                        mainPct += parseFloat($(this).find('.option_percentage_hourly').val()) || 0;
                    });
                    $('#bonusList tbody .hourly_section_row:not(.d-none)').each(function() {
                        addPct += parseFloat($(this).find('.bonus_option_percentage_hourly').val()) || 0;
                    });
                } else {
                    $('#pointList tbody .other_section_row:not(.d-none)').each(function() {
                        mainPct += parseFloat($(this).find('.option_percentage').val()) || 0;
                    });
                    $('#bonusList tbody .other_section_row:not(.d-none)').each(function() {
                        addPct += parseFloat($(this).find('.bonus_option_percentage').val()) || 0;
                    });
                }

                var main100 = Math.abs(mainPct - 100) < 0.01;
                $('#co_validation_text').text('Total of main components: ' + mainPct.toFixed(2) + '%' + (main100 ? ' — exactly 100%' : ''));
                $('#co_validation_banner').removeClass('co-valid-banner--warn co-valid-banner--ok').addClass(main100 ? 'co-valid-banner--ok' : 'co-valid-banner--warn');

                if (!main100) {
                    $('#pointList tfoot tr.co-total-row input.totalpercentage, #pointList tfoot tr.co-total-row input.totalpercentagehouly').addClass('co-pct-invalid');
                } else {
                    $('#pointList tfoot tr.co-total-row input.totalpercentage, #pointList tfoot tr.co-total-row input.totalpercentagehouly').removeClass('co-pct-invalid');
                }

                var addAmtTotal = coNum($('#totalYearlybonus').val()) || coNum($('#totalMonthlybonus').val()) || (coNum($('#ctc').val()) * addPct / 100);
                $('#co_additional_total').html('Total additional components: <b>' + addPct.toFixed(2) + '%</b> (Allowed above 100%)'
                    + (addAmtTotal ? ' &nbsp;•&nbsp; Amount: <b>' + coMoney(addAmtTotal) + '</b>' : ''));

                coEnhanceRows();
            }

            function calculateRowDetails(row) {
                const option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                var currentSalary = 0;
                if (option_salarymode == 1) {
                    currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                } else {
                    currentSalary = parseFloat($('#ctc').val()) || 0;
                }

                const percentage = parseFloat(row.find('.option_percentage').val()) || 0;
                const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                const addSubtract = $('#current_ctc_mode').val();

                const calculatedAmount = (currentSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;

                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                } else if (addSubtract === "1") {
                    monthly = calculatedAmount;
                    yearly = monthly * 12;
                } else if (addSubtract === "4") {
                    yearly = calculatedAmount;
                    hourly = yearly / (8 * 5 * 52);
                    monthly = hourly;
                }

                if (basic_view == 2) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.offer_monthly').val((0.00).toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                }
            }

            function calculateRowDetailshourly(row) {
                const option_salarymode_hourly = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;
                var currentSalary = 0;
                if (option_salarymode_hourly == 1) {
                    currentSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                } else {
                    currentSalary = parseFloat($('#ctc').val()) || 0;
                }

                const percentage = parseFloat(row.find('.option_percentage_hourly').val()) || 0;
                const addSubtract = $('#current_ctc_mode').val();

                const calculatedAmount = (currentSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;

                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                    hourly = yearly / (8 * 5 * 52);
                } else if (addSubtract === "2") {
                    hourly = calculatedAmount;
                    monthly = hourly * 8 * 5 * 4;
                    yearly = hourly * 8 * 5 * 52;
                } else if (addSubtract === "1") {
                    yearly = calculatedAmount;
                    hourly = yearly / 12;
                } else if (addSubtract === "3") {
                    monthly = calculatedAmount;
                    hourly = monthly * 12;
                }
                row.find('.offer_hourly').val(hourly.toFixed(2));
            }

            function updateTotalAmountshourly() {
                let totalhourly = 0;
                $('#pointList tbody .hourly_section_row').each(function() {
                    const hourly = parseFloat($(this).find('.offer_hourly').val()) || 0;
                    totalhourly += hourly;
                });
                $('#totalhourlyhourly').val(totalhourly.toFixed(2));
            }

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
                let totalpercentage = 0;
                $('#pointList tbody .other_section_row').each(function() {
                    const percentage = parseFloat($(this).find('.option_percentage').val()) || 0;
                    totalpercentage += percentage;
                });
                $('#totalpercentage').val(totalpercentage.toFixed(2));
            }

            function updateTotalpercentagehourly() {
                let totalpercentage = 0;
                $('#pointList tbody .hourly_section_row').each(function() {
                    const percentage = parseFloat($(this).find('.option_percentage_hourly').val()) || 0;
                    totalpercentage += percentage;
                });
                $('#totalpercentagehouly').val(totalpercentage.toFixed(2));
            }

            function updateTotalpercentagebonus() {
                let totalpercentage = 0;
                $('#bonusList tbody .other_section_row').each(function() {
                    const percentage = parseFloat($(this).find('.bonus_option_percentage').val()) || 0;
                    totalpercentage += percentage;
                });
                $('#totalpercentagebonus').val(totalpercentage.toFixed(2));
            }

            function updateTotalpercentagebonushourly() {
                let totalpercentage = 0;
                $('#bonusList tbody .hourly_section_row').each(function() {
                    const percentage = parseFloat($(this).find('.bonus_option_percentage_hourly').val()) || 0;
                    totalpercentage += percentage;
                });
                $('#totalhourlypercentagebonus').val(totalpercentage.toFixed(2));
            }

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
                const addSubtract = $('#current_ctc_mode').val();

                const calculatedAmount = (currentSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;

                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                } else if (addSubtract === "1") {
                    monthly = calculatedAmount;
                    yearly = monthly * 12;
                } else if (addSubtract === "4") {
                    yearly = calculatedAmount;
                    hourly = yearly / (8 * 5 * 52);
                    monthly = hourly;
                }

                if (basic_view == 2) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.bonus_offer_monthly').val((0.00).toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                }
            }

            function calculateRowDetailsbonushourly(row) {
                const bonus_option_salarymode_hourly = parseFloat(row.find('.bonus_option_salarymode_hourly').val()) || 0;
                var currentSalary = 0;
                if (bonus_option_salarymode_hourly == 1) {
                    currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                } else {
                    currentSalary = parseFloat($('#ctc').val()) || 0;
                }

                const percentage = parseFloat(row.find('.bonus_option_percentage_hourly').val()) || 0;
                const addSubtract = $('#current_ctc_mode').val();

                const calculatedAmount = (currentSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;

                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                    hourly = yearly / (8 * 5 * 52);
                } else if (addSubtract === "2") {
                    hourly = calculatedAmount;
                    monthly = hourly * 8 * 5 * 4;
                    yearly = hourly * 8 * 5 * 52;
                } else if (addSubtract === "1") {
                    yearly = calculatedAmount;
                    hourly = yearly / 12;
                } else if (addSubtract === "3") {
                    monthly = calculatedAmount;
                    hourly = monthly * 12;
                }

                row.find('.bonus_offer_hourly').val(hourly.toFixed(2));
            }

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

            function finalPayupdateTotalAmounts() {
                let totalMonthly = 0;
                let totalYearly = 0;
                const totalMonthlyBasic = parseFloat($('#totalMonthly').val()) || 0;
                const totalYearlyBasic = parseFloat($('#totalYearly').val()) || 0;
                const totalMonthlybonus = parseFloat($('#totalMonthlybonus').val()) || 0;
                const totalYearlybonus = parseFloat($('#totalYearlybonus').val()) || 0;

                totalMonthly = totalMonthlyBasic + totalMonthlybonus;
                totalYearly = totalYearlyBasic + totalYearlybonus;

                $('#finalpaytotalMonthly').val(totalMonthly.toFixed(2));
                $('#finalpaytotalYearly').val(totalYearly.toFixed(2));
            }

            function finalPayupdateTotalAmountshourly() {
                let totalhourly = 0;
                const totalhourlyhourly = parseFloat($('.totalhourlyhourly').val()) || 0;
                const totalhourlyybonus = parseFloat($('.totalhourlyybonus').val()) || 0;

                totalhourly = totalhourlyhourly + totalhourlyybonus;
                $('#finalpaytotalhourly').val(totalhourly.toFixed(2));
            }

            function totalpercentagefinal() {
                let totalpercentagefinal = 0;
                const totalpercentage = parseFloat($('#totalpercentage').val()) || 0;
                const totalpercentagebonus = parseFloat($('#totalpercentagebonus').val()) || 0;

                totalpercentagefinal = totalpercentage + totalpercentagebonus;
                $('#totalpercentagefinal').val(totalpercentagefinal.toFixed(2));
            }

            function totalpercentagefinalhourly() {
                let totalpercentagefinal = 0;
                const totalpercentage = parseFloat($('#totalpercentagehouly').val()) || 0;
                const totalpercentagebonus = parseFloat($('#totalhourlypercentagebonus').val()) || 0;

                totalpercentagefinal = totalpercentage + totalpercentagebonus;
                $('#totalpercentagefinalhourly').val(totalpercentagefinal.toFixed(2));
            }

            function finalRow() {
                const rowCountOther = $('#bonusList tbody .other_section_row').length;
                const rowCountHourly = $('#bonusList tbody .hourly_section_row').length;
                const addSubtract = $('#current_ctc_mode').val();
                if ((rowCountOther == 0) && (addSubtract == 4 || addSubtract == 0)) {
                    $('.other_section_final').addClass('d-none');
                } else if ((rowCountOther > 0) && (addSubtract == 4 || addSubtract == 0)) {
                    $('.other_section_final').removeClass('d-none');
                }

                if ((rowCountHourly == 0) && (addSubtract == 1 || addSubtract == 2 || addSubtract == 3)) {
                    $('.hourly_section_final').addClass('d-none');
                } else if ((rowCountHourly > 0) && (addSubtract == 1 || addSubtract == 2 || addSubtract == 3)) {
                    $('.hourly_section_final').removeClass('d-none');
                }
            }

            // Reverse calculate percentage when yearly/monthly amount is changed
            $(document).on('change', '.offer_yearly', function() {
                const row = $(this).closest('.other_section_row');
                const changeValue = parseFloat($(this).val()) || 0;
                var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                const addSubtract = $('#current_ctc_mode').val();
                var baseSalary = 0;
                if (option_salarymode == 1) {
                    baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                } else {
                    baseSalary = parseFloat($('#ctc').val()) || 0;
                }
                let percentage = baseSalary > 0 ? (changeValue / baseSalary) * 100 : 0;
                const calculatedAmount = (baseSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;
                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                } else if (addSubtract === "1") {
                    monthly = calculatedAmount;
                    yearly = monthly * 12;
                } else if (addSubtract === "4") {
                    yearly = calculatedAmount;
                    hourly = yearly / (8 * 5 * 52);
                    monthly = hourly;
                }
                if (basic_view == 2) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.offer_monthly').val((0.00).toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                }
                row.find('.option_percentage').val(percentage.toFixed(2));
                updateTotalAmounts();
                updateTotalpercentage();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.offer_monthly', function() {
                const row = $(this).closest('.other_section_row');
                const monthlyValue = parseFloat($(this).val()) || 0;
                var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                var baseSalary = 0;
                if (option_salarymode == 1) {
                    baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                } else {
                    baseSalary = parseFloat($('#ctc').val()) || 0;
                }
                let yearly = monthlyValue * 12;
                let monthly = yearly / 12;
                let percentage = baseSalary > 0 ? (yearly / baseSalary) * 100 : 0;
                if (basic_view == 2) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.offer_monthly').val(monthly.toFixed(2));
                    row.find('.offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.offer_monthly').val((0.00).toFixed(2));
                    row.find('.offer_yearly').val(yearly.toFixed(2));
                }
                row.find('.option_percentage').val(percentage.toFixed(2));
                updateTotalAmounts();
                updateTotalpercentage();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.offer_hourly', function() {
                const row = $(this).closest('.hourly_section_row');
                const changeValue = parseFloat($(this).val()) || 0;
                var option_salarymode = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;
                var baseSalary = 0;
                if (option_salarymode == 1) {
                    baseSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                } else {
                    baseSalary = parseFloat($('#ctc').val()) || 0;
                }
                let percentage = baseSalary > 0 ? (changeValue / baseSalary) * 100 : 0;
                row.find('.option_percentage_hourly').val(percentage.toFixed(2));
                updateTotalAmountshourly();
                updateTotalpercentagehourly();
                updateTotalpercentagebonushourly();
                finalPayupdateTotalAmountshourly();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.bonus_offer_yearly', function() {
                const row = $(this).closest('.other_section_row');
                const changeValue = parseFloat($(this).val()) || 0;
                var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                const addSubtract = $('#current_ctc_mode').val();
                var baseSalary = 0;
                if (option_salarymode == 1) {
                    baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                } else {
                    baseSalary = parseFloat($('#ctc').val()) || 0;
                }
                let percentage = baseSalary > 0 ? (changeValue / baseSalary) * 100 : 0;
                const calculatedAmount = (baseSalary * (percentage / 100));
                let monthly = 0;
                let yearly = 0;
                let hourly = 0;
                if (addSubtract === "0") {
                    yearly = calculatedAmount;
                    monthly = yearly / 12;
                } else if (addSubtract === "1") {
                    monthly = calculatedAmount;
                    yearly = monthly * 12;
                } else if (addSubtract === "4") {
                    yearly = calculatedAmount;
                    hourly = yearly / (8 * 5 * 52);
                    monthly = hourly;
                }
                if (basic_view == 2) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.bonus_offer_monthly').val((0.00).toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                }
                row.find('.bonus_option_percentage').val(percentage.toFixed(2));
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.bonus_offer_monthly', function() {
                const row = $(this).closest('.other_section_row');
                const monthlyValue = parseFloat($(this).val()) || 0;
                var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                var baseSalary = 0;
                if (option_salarymode == 1) {
                    baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                } else {
                    baseSalary = parseFloat($('#ctc').val()) || 0;
                }
                let yearly = monthlyValue * 12;
                let monthly = yearly / 12;
                let percentage = baseSalary > 0 ? (yearly / baseSalary) * 100 : 0;
                if (basic_view == 2) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                } else if (basic_view == 1) {
                    row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                    row.find('.bonus_offer_yearly').val((0.00).toFixed(2));
                } else if (basic_view == 0) {
                    row.find('.bonus_offer_monthly').val((0.00).toFixed(2));
                    row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                }
                row.find('.bonus_option_percentage').val(percentage.toFixed(2));
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            // CTC trigger
            $(document).on('keyup input change', '#ctc', function() {
                var current_ctc_mode = $('#current_ctc_mode').val();
                if (current_ctc_mode == 2 || current_ctc_mode == 1 || current_ctc_mode == 3) {
                    $('#pointList tbody .hourly_section_row').each(function() {
                        calculateRowDetailshourly($(this));
                    });
                    $('#bonusList tbody .hourly_section_row').each(function() {
                        calculateRowDetailsbonushourly($(this));
                    });
                    updateTotalAmountshourly();
                    updateTotalpercentagehourly();
                    updateTotalpercentagebonushourly();
                    updateTotalAmountsbonushourly();
                    finalPayupdateTotalAmountshourly();
                    totalpercentagefinalhourly();
                } else {
                    $('#pointList tbody .other_section_row').each(function() {
                        calculateRowDetails($(this));
                    });
                    $('#bonusList tbody .other_section_row').each(function() {
                        calculateRowDetailsbonus($(this));
                    });
                    updateTotalAmounts();
                    updateTotalpercentage();
                    updateTotalAmountsbonus();
                    updateTotalpercentagebonus();
                    finalPayupdateTotalAmounts();
                    totalpercentagefinal();
                }
                updateAllocationMeter();
            });

            // Row field change triggers
            $(document).on('change keyup input', '.option_percentage', function() {
                const row = $(this).closest('tr');
                calculateRowDetails(row);
                updateTotalAmounts();
                updateTotalpercentage();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.basic_view', function() {
                const row = $(this).closest('tr');
                const basic_view = $(this).val();
                if (basic_view == 0) {
                    row.find('.offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#f9fafd'});
                    row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                } else if (basic_view == 1) {
                    row.find('.offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#f9fafd'});
                    row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                } else if (basic_view == 2) {
                    row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                    row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                }
                calculateRowDetails(row);
                updateTotalAmounts();
                updateTotalpercentage();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.option_salarymode', function() {
                const row = $(this).closest('tr');
                calculateRowDetails(row);
                updateTotalAmounts();
                updateTotalpercentage();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change keyup input', '.option_percentage_hourly', function() {
                const row = $(this).closest('tr');
                calculateRowDetailshourly(row);
                updateTotalAmountshourly();
                updateTotalpercentagehourly();
                finalPayupdateTotalAmountshourly();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.option_salarymode_hourly', function() {
                const row = $(this).closest('tr');
                calculateRowDetailshourly(row);
                updateTotalAmountshourly();
                updateTotalpercentagehourly();
                finalPayupdateTotalAmountshourly();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change keyup input', '.bonus_option_percentage', function() {
                const row = $(this).closest('.other_section_row');
                calculateRowDetailsbonus(row);
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.bonus_view', function() {
                const row = $(this).closest('.other_section_row');
                const basic_view = $(this).val();
                if (basic_view == 0) {
                    row.find('.bonus_offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#f9fafd'});
                    row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                } else if (basic_view == 1) {
                    row.find('.bonus_offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#f9fafd'});
                    row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                } else if (basic_view == 2) {
                    row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                    row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': '#f9fafd'});
                }
                calculateRowDetailsbonus(row);
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.bonus_option_salarymode', function() {
                const row = $(this).closest('.other_section_row');
                calculateRowDetailsbonus(row);
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                finalPayupdateTotalAmounts();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                updateAllocationMeter();
            });

            $(document).on('change keyup input', '.bonus_option_percentage_hourly', function() {
                const row = $(this).closest('.hourly_section_row');
                calculateRowDetailsbonushourly(row);
                updateTotalAmountsbonushourly();
                finalPayupdateTotalAmountshourly();
                updateTotalpercentagehourly();
                updateTotalpercentagebonushourly();
                updateAllocationMeter();
            });

            $(document).on('change', '.bonus_option_salarymode_hourly', function() {
                const row = $(this).closest('.hourly_section_row');
                calculateRowDetailsbonushourly(row);
                updateTotalAmountsbonushourly();
                finalPayupdateTotalAmountshourly();
                updateTotalpercentagehourly();
                updateTotalpercentagebonushourly();
                updateAllocationMeter();
            });

            // Format switch
            $(document).on('change', '#current_ctc_mode', function() {
                var current_ctc_mode = $(this).val();
                finalRow();

                $('.basic_view').each(function() {
                    const select = $(this);
                    const monthlyOption = select.find('option[value="1"]');
                    if (current_ctc_mode == 4) {
                        monthlyOption.text("Hourly");
                        $('#text_for_hourly_annually').removeClass('d-none');
                    } else {
                        monthlyOption.text("Monthly");
                        $('#text_for_hourly_annually').addClass('d-none');
                    }
                });

                $('.bonus_view').each(function() {
                    const select = $(this);
                    const monthlyOption = select.find('option[value="1"]');
                    if (current_ctc_mode == 4) {
                        monthlyOption.text("Hourly");
                    } else {
                        monthlyOption.text("Monthly");
                    }
                });

                if (current_ctc_mode == 0 || current_ctc_mode == 4) {
                    var change_text = '';
                    if (current_ctc_mode == 1) {
                        change_text = 'Monthly';
                    } else if (current_ctc_mode == 4) {
                        change_text = 'Hourly';
                    } else if (current_ctc_mode == 3) {
                        change_text = 'Annual';
                    }
                    $('.change_text_first').text(change_text || 'Monthly');
                    $('.other_section').removeClass('d-none');
                    $('.hourly_section').addClass('d-none');
                } else {
                    var change_text = '';
                    if (current_ctc_mode == 1) {
                        change_text = 'Monthly';
                    } else if (current_ctc_mode == 2) {
                        change_text = 'Hourly';
                    } else if (current_ctc_mode == 3) {
                        change_text = 'Annual';
                    }
                    $('.change_text').text(change_text || 'Hourly');
                    $('.hourly_section').removeClass('d-none');
                    $('.other_section').addClass('d-none');
                }
                updateAllocationMeter();
            });

            // Currency change
            $(document).on('change', '#current_ctc_cur', function() {
                changeCurrency();
            });

            function changeCurrency() {
                const val_currency = $('#current_ctc_cur').val();
                let code = 'INR';
                if (val_currency == 0) {
                    code = 'INR';
                } else if (val_currency == 1) {
                    code = 'EUR';
                } else if (val_currency == 2) {
                    code = 'JPY';
                } else if (val_currency == 3) {
                    code = 'GBP';
                } else if (val_currency == 4) {
                    code = 'USD';
                } else if (val_currency == 5) {
                    code = 'AED';
                }
                $('.show_currency').val(code);
            }

            // Add Main Component
            $(document).off('click', '.addRowBtn').on('click', '.addRowBtn', function() {
                var current_ctc_mode = $('#current_ctc_mode').val();
                var isHourly = (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3);
                var templateSel = isHourly ? '.hourly_section_row' : '.other_section_row';
                var newRow = $(templateSel).first().clone();

                newRow.find('.co-row-lead, .co-comp-badge').remove();
                newRow.find('input').val('');
                newRow.addClass('remove_row');
                newRow.find('.remove_points').removeAttr('disabled').attr('title', 'Delete Component');
                if (newRow.find('.option_salarymode option[value="1"]').length === 0) {
                    newRow.find('.option_salarymode').append('<option value="1">Basic</option>');
                }
                if (newRow.find('.option_salarymode_hourly option[value="1"]').length === 0) {
                    newRow.find('.option_salarymode_hourly').append('<option value="1">Basic</option>');
                }
                $('#pointList tbody').append(newRow);

                newRow.find('.option_percentage').on('change keyup input', function() {
                    calculateRowDetails(newRow);
                    updateTotalAmounts();
                    updateTotalpercentage();
                    updateAllocationMeter();
                });
                newRow.find('.option_percentage_hourly').on('change keyup input', function() {
                    calculateRowDetailshourly(newRow);
                    updateTotalAmountshourly();
                    updateTotalpercentagehourly();
                    updateAllocationMeter();
                });
                coEnhanceRows();
                updateAllocationMeter();
            });

            // Add Additional Component
            $(document).off('click', '.addRowBtnbonus').on('click', '.addRowBtnbonus', function() {
                var current_ctc_mode = $('#current_ctc_mode').val();
                var isHourly = (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3);

                if (isHourly) {
                    var newRow = $('.hourly_section_row').first().clone();
                    newRow.find('.co-row-lead, .co-comp-badge').remove();
                    newRow.find('input').val('');
                    newRow.find('select').val('0');
                    newRow.find('.offer_point_hourly').addClass('bonus_point_hourly').removeClass('offer_point_hourly');
                    newRow.find('.bonus_point_hourly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_point_hourly[]').attr('id', 'bonus_point_hourly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.option_percentage_hourly').addClass('bonus_option_percentage_hourly').removeClass('option_percentage_hourly');
                    newRow.find('.bonus_option_percentage_hourly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_option_percentage_hourly[]').attr('id', 'bonus_option_percentage_hourly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.option_salarymode_hourly').addClass('bonus_option_salarymode_hourly').removeClass('option_salarymode_hourly');
                    newRow.find('.bonus_option_salarymode_hourly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_option_salarymode_hourly[]').attr('id', 'bonus_option_salarymode_hourly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.offer_hourly').addClass('bonus_offer_hourly').removeClass('offer_hourly');
                    newRow.find('.bonus_offer_hourly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_offer_hourly[]');
                        $(this).attr('id', 'bonus_offer_hourly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');
                    newRow.find('.bonus_offer_hourly').removeClass('basic_salary_get_hourly');
                } else {
                    var newRow = $('.other_section_row').first().clone();
                    newRow.find('.co-row-lead, .co-comp-badge').remove();
                    newRow.find('input').val('');
                    newRow.find('select').val('0');
                    newRow.find('.offer_points').addClass('bonus_points').removeClass('offer_points');
                    newRow.find('.bonus_points').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_points[]').attr('id', 'bonus_points_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.offer_monthly').addClass('bonus_offer_monthly').removeClass('offer_monthly');
                    newRow.find('.bonus_offer_monthly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_offer_monthly[]').attr('id', 'bonus_offer_monthly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.basic_view').addClass('bonus_view').removeClass('basic_view');
                    newRow.find('.bonus_view').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_view[]').attr('id', 'bonus_view_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.option_percentage').addClass('bonus_option_percentage').removeClass('option_percentage');
                    newRow.find('.bonus_option_percentage').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_option_percentage[]').attr('id', 'bonus_option_percentage_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.option_salarymode').addClass('bonus_option_salarymode').removeClass('option_salarymode');
                    newRow.find('.bonus_option_salarymode').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_option_salarymode[]').attr('id', 'bonus_option_salarymode_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.offer_yearly').addClass('bonus_offer_yearly').removeClass('offer_yearly');
                    newRow.find('.bonus_offer_yearly').each(function(index) {
                        var idx = $('#bonusList tbody tr').length + 1;
                        $(this).attr('name', 'bonus_offer_yearly[]').attr('id', 'bonus_offer_yearly_' + idx).attr('data-id', idx).attr('data-offerid', idx);
                    });
                    newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');
                    newRow.find('.bonus_offer_yearly').removeClass('basic_salary_get');
                }

                newRow.addClass('remove_row');
                newRow.find('.remove_points').removeAttr('disabled').attr('title', 'Delete Component');
                if (newRow.find('.bonus_option_salarymode option[value="1"]').length === 0) {
                    newRow.find('.bonus_option_salarymode').append('<option value="1">Basic</option>');
                }
                if (newRow.find('.bonus_option_salarymode_hourly option[value="1"]').length === 0) {
                    newRow.find('.bonus_option_salarymode_hourly').append('<option value="1">Basic</option>');
                }
                $('#bonusList tbody').append(newRow);
                finalRow();
                coEnhanceRows();
                updateAllocationMeter();
            });

            // Remove Component
            $(document).off('click', '.remove_points').on('click', '.remove_points', function() {
                const removeRow = $(this).closest('.remove_row');
                removeRow.remove();
                finalRow();
                updateTotalAmounts();
                updateTotalpercentage();
                updateTotalAmountshourly();
                updateTotalpercentagehourly();
                updateTotalpercentagebonushourly();
                updateTotalAmountsbonushourly();
                updateTotalAmountsbonus();
                updateTotalpercentagebonus();
                totalpercentagefinal();
                totalpercentagefinalhourly();
                finalPayupdateTotalAmountshourly();
                finalPayupdateTotalAmounts();
                coEnhanceRows();
                updateAllocationMeter();
            });

            // ── Client-Side Validation Helpers & Real-Time Feedback ──
            function showFieldError($input, msg) {
                $input.addClass('is-invalid');
                var $feedback = $input.siblings('.invalid-feedback');
                if ($feedback.length) {
                    $feedback.text(msg).show();
                }
            }

            function clearFieldError($input) {
                $input.removeClass('is-invalid');
                var $feedback = $input.siblings('.invalid-feedback');
                if ($feedback.length) {
                    $feedback.text('').hide();
                }
            }

            // Real-time listener for CTC
            $('#ctc').on('input keyup blur', function() {
                var raw = $(this).val().replace(/,/g, '').trim();
                var num = parseFloat(raw);
                if (!raw || isNaN(num) || num <= 0) {
                    showFieldError($(this), 'Please enter a valid CTC amount greater than 0.');
                } else {
                    clearFieldError($(this));
                }
            });

            // Real-time listener for Offered Designation
            $('#current_designation').on('input blur', function() {
                var val = $(this).val() ? $(this).val().trim() : '';
                if (!val) {
                    showFieldError($(this), 'Offered Designation is required.');
                } else {
                    clearFieldError($(this));
                }
            });

            // Real-time listener to clear invalid component name states on typing
            $(document).on('input', '.offer_points, .offer_point_hourly, .bonus_points, .bonus_point_hourly', function() {
                if ($(this).val().trim().length > 0) {
                    $(this).removeClass('is-invalid');
                }
            });

            // Real-time listener to clear invalid percentage states on typing
            $(document).on('input', '.option_percentage, .option_percentage_hourly, .bonus_option_percentage, .bonus_option_percentage_hourly', function() {
                var val = parseFloat($(this).val());
                if (!isNaN(val) && val >= 0 && val <= 100) {
                    $(this).removeClass('is-invalid');
                }
            });

            // Client-Side Pre-Submission Validation Function
            function validateMasterSalaryForm() {
                // 1. Validate CTC Amount
                var $ctc = $('#ctc');
                var ctcVal = $ctc.val().replace(/,/g, '').trim();
                var ctcNum = parseFloat(ctcVal);
                if (!ctcVal || isNaN(ctcNum) || ctcNum <= 0) {
                    showFieldError($ctc, 'Please enter a valid CTC amount greater than 0.');
                    $ctc.focus();
                    Swal.fire({
                        title: "Invalid CTC Amount",
                        text: "Please enter a valid positive CTC amount (e.g., 500000) before saving.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                } else {
                    clearFieldError($ctc);
                }

                // 2. Validate Offered Designation
                var $desig = $('#current_designation');
                var desigVal = $desig.val() ? $desig.val().trim() : '';
                if (!desigVal) {
                    showFieldError($desig, 'Offered Designation is required.');
                    $desig.focus();
                    Swal.fire({
                        title: "Missing Designation",
                        text: "Please enter an Offered Designation before saving.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                } else {
                    clearFieldError($desig);
                }

                var mode = $('#current_ctc_mode').val();
                var isHourly = (mode == 1 || mode == 2 || mode == 3);

                // 3. Validate Primary Salary Components
                var $primaryRows = isHourly
                    ? $('#pointList tbody .hourly_section_row:not(.d-none)')
                    : $('#pointList tbody .other_section_row:not(.d-none)');

                if ($primaryRows.length === 0) {
                    Swal.fire({
                        title: "No Components Configured",
                        text: "Please add at least one primary salary component before saving.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                var primaryNames = [];
                var firstEmptyNameInput = null;
                var duplicatePrimaryName = '';
                var firstDuplicateNameInput = null;
                var firstInvalidPctInput = null;
                var invalidPctMsg = '';
                var totalSum = 0;

                $primaryRows.each(function() {
                    var $nameInput = isHourly ? $(this).find('.offer_point_hourly') : $(this).find('.offer_points');
                    var $pctInput = isHourly ? $(this).find('.option_percentage_hourly') : $(this).find('.option_percentage');

                    var name = $nameInput.val() ? $nameInput.val().trim() : '';
                    var pctRaw = $pctInput.val() ? $pctInput.val().trim() : '';
                    var pct = parseFloat(pctRaw);

                    // Name validation
                    if (!name) {
                        $nameInput.addClass('is-invalid');
                        if (!firstEmptyNameInput) firstEmptyNameInput = $nameInput;
                    } else {
                        var lowerName = name.toLowerCase();
                        if (primaryNames.indexOf(lowerName) !== -1) {
                            $nameInput.addClass('is-invalid');
                            if (!firstDuplicateNameInput) {
                                firstDuplicateNameInput = $nameInput;
                                duplicatePrimaryName = name;
                            }
                        } else {
                            $nameInput.removeClass('is-invalid');
                            primaryNames.push(lowerName);
                        }
                    }

                    // Percentage validation
                    if (pctRaw === '' || isNaN(pct) || pct <= 0 || pct > 100) {
                        $pctInput.addClass('is-invalid');
                        if (!firstInvalidPctInput) {
                            firstInvalidPctInput = $pctInput;
                            invalidPctMsg = (isNaN(pct) || pct <= 0)
                                ? `Allocation for "${name || 'Component'}" must be greater than 0%.`
                                : `Allocation for "${name || 'Component'}" cannot exceed 100%.`;
                        }
                    } else {
                        $pctInput.removeClass('is-invalid');
                        totalSum += pct;
                    }
                });

                if (firstEmptyNameInput) {
                    firstEmptyNameInput.focus();
                    Swal.fire({
                        title: "Incomplete Component Name",
                        text: "Please enter a valid Component Name for all primary salary components before saving.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                if (firstDuplicateNameInput) {
                    firstDuplicateNameInput.focus();
                    Swal.fire({
                        title: "Duplicate Component Name",
                        text: `The component name "${duplicatePrimaryName}" is repeated in primary components. Each component must have a unique name.`,
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                if (firstInvalidPctInput) {
                    firstInvalidPctInput.focus();
                    Swal.fire({
                        title: "Invalid Allocation Percentage",
                        text: invalidPctMsg || "Each primary component allocation must be greater than 0% and at most 100%.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                // Balance check: Must equal 100.00%
                var roundedSum = Math.round(totalSum * 100) / 100;
                if (Math.abs(roundedSum - 100) > 0.01) {
                    var diff = Math.round((100 - roundedSum) * 100) / 100;
                    var detailText = (diff > 0)
                        ? `<span class="text-warning fw-bold">${diff.toFixed(2)}% remaining</span> to reach 100.00%`
                        : `<span class="text-danger fw-bold">+${Math.abs(diff).toFixed(2)}% overallocated</span> above 100.00%`;

                    Swal.fire({
                        title: "Primary Allocation Must Equal 100.00%",
                        html: `Primary component allocation must sum to exactly <b>100.00%</b>.<br>Current Sum: <b>${roundedSum.toFixed(2)}%</b> (${detailText}).<br>Please balance your components before saving.`,
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                // 4. Validate Additional / Bonus Components
                var $bonusRows = isHourly
                    ? $('#bonusList tbody .hourly_section_row:not(.d-none)')
                    : $('#bonusList tbody .other_section_row:not(.d-none)');

                var firstEmptyBonusInput = null;
                var duplicateBonusName = '';
                var firstDuplicateBonusInput = null;
                var firstInvalidBonusPctInput = null;
                var bonusNames = [];

                $bonusRows.each(function() {
                    var $nameInput = isHourly ? $(this).find('.bonus_point_hourly') : $(this).find('.bonus_points');
                    var $pctInput = isHourly ? $(this).find('.bonus_option_percentage_hourly') : $(this).find('.bonus_option_percentage');

                    var name = $nameInput.val() ? $nameInput.val().trim() : '';
                    var pctRaw = $pctInput.val() ? $pctInput.val().trim() : '';
                    var pct = parseFloat(pctRaw);

                    if (!name) {
                        $nameInput.addClass('is-invalid');
                        if (!firstEmptyBonusInput) firstEmptyBonusInput = $nameInput;
                    } else {
                        var lowerName = name.toLowerCase();
                        if (bonusNames.indexOf(lowerName) !== -1 || primaryNames.indexOf(lowerName) !== -1) {
                            $nameInput.addClass('is-invalid');
                            if (!firstDuplicateBonusInput) {
                                firstDuplicateBonusInput = $nameInput;
                                duplicateBonusName = name;
                            }
                        } else {
                            $nameInput.removeClass('is-invalid');
                            bonusNames.push(lowerName);
                        }
                    }

                    if (pctRaw !== '' && (isNaN(pct) || pct < 0 || pct > 100)) {
                        $pctInput.addClass('is-invalid');
                        if (!firstInvalidBonusPctInput) firstInvalidBonusPctInput = $pctInput;
                    } else {
                        $pctInput.removeClass('is-invalid');
                    }
                });

                if (firstEmptyBonusInput) {
                    firstEmptyBonusInput.focus();
                    Swal.fire({
                        title: "Incomplete Additional Component",
                        text: "Please enter a valid Component Name for all additional components or remove empty rows before saving.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                if (firstDuplicateBonusInput) {
                    firstDuplicateBonusInput.focus();
                    Swal.fire({
                        title: "Duplicate Component Name",
                        text: `The additional component name "${duplicateBonusName}" is already in use. Please enter a unique name.`,
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                if (firstInvalidBonusPctInput) {
                    firstInvalidBonusPctInput.focus();
                    Swal.fire({
                        title: "Invalid Additional Allocation",
                        text: "Additional component allocation must be between 0% and 100%.",
                        icon: "warning",
                        confirmButtonColor: "#1b84ff"
                    });
                    return false;
                }

                return true;
            }

            // Form Submit via AJAX
            $('#createForm').on('submit', function(e) {
                e.preventDefault();

                // Run thorough Client-Side Validation
                if (!validateMasterSalaryForm()) {
                    return false;
                }

                var $saveBtn = $('#save-form');
                var origVal = $saveBtn.val();
                $saveBtn.prop('disabled', true).val('Saving...');

                let formData = new FormData(this);
                $.ajax({
                    url: '{{ route('superadmin.company.companysavecreateoffer') }}?_token={{ csrf_token() }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status == 'success' || response.success === true) {
                            Swal.fire({
                                title: "Saved Successfully",
                                text: response.message || "Master salary breakup configuration updated successfully.",
                                icon: "success",
                                confirmButtonColor: "#1b84ff"
                            });
                        } else if (response.status == 'errors') {
                            Swal.fire({
                                title: "Configuration Notice",
                                text: response.message,
                                icon: "warning",
                                confirmButtonColor: "#1b84ff"
                            });
                        } else {
                            Swal.fire({
                                title: "Notice",
                                text: response.message || "Configuration updated.",
                                icon: "info",
                                confirmButtonColor: "#1b84ff"
                            });
                        }
                    },
                    error: function(response) {
                        let errors = response.responseJSON ? response.responseJSON.errors : null;
                        let errorMessages = '';
                        if (errors) {
                            let counter = 1;
                            $.each(errors, function(fieldName, errorMessage) {
                                errorMessages += `<div class="text-danger fs-8 mb-1"><b>${counter}.</b> ${errorMessage[0]}</div>`;
                                counter++;
                            });
                        } else {
                            errorMessages = '<div class="text-danger fs-8">An unexpected error occurred while saving. Please try again.</div>';
                        }

                        Swal.fire({
                            title: "Validation Errors",
                            html: errorMessages,
                            icon: "warning",
                            confirmButtonColor: "#1b84ff",
                            customClass: {
                                popup: 'text-start'
                            }
                        });
                    },
                    complete: function() {
                        $saveBtn.prop('disabled', false).val(origVal);
                    }
                });
            });

            // Initial triggers on page load
            changeCurrency();
            $('#ctc').trigger('keyup');
            finalPayupdateTotalAmounts();
            totalpercentagefinal();
            finalPayupdateTotalAmountshourly();
            totalpercentagefinalhourly();
            finalRow();
            coEnhanceRows();
            updateAllocationMeter();

        });
    </script>
@endpush
