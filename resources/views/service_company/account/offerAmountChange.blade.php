@extends('layouts.service_company_layout')
@php
    use App\Helper\GeneralMethod;
@endphp

<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">

<div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">
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

        /* Basic styling for the step container */
        .tab-container {
            max-width: 600px;
            margin: auto;
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
                var editorElement = CKEDITOR.document.getById('editor1');

                if (isBBCodeBuiltIn) {
                    editorElement.setHtml(
                        'Hello world!\n\n' +
                        'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                    );
                }
                if (wysiwygareaAvailable) {
                    CKEDITOR.replace('editor1');
                } else {
                    editorElement.setAttribute('contenteditable', 'true');
                    CKEDITOR.inline('editor1');
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

@section('content')
    {{-- Font Awesome 6 (the local webfonts may be missing → icons render as tofu without this) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
      // Close / Go Back: when this page is embedded in the Salary Structure drawer,
      // close the parent drawer instead of navigating the iframe to another page.
      function sstClose(){
        try {
          if (window.parent && window.parent !== window && typeof window.parent.closeSalaryEditDrawer === 'function') {
            window.parent.closeSalaryEditDrawer();
            return;
          }
        } catch (e) {}
        window.history.back();
      }
    </script>
    @if (request('embed'))
        {{-- Embedded mode: when opened inside the Salary Structure drawer, hide the app chrome. --}}
        <style>
            #kt_aside, #kt_header, #kt_toolbar, .footer, #kt_footer { display: none !important; }
            .wrapper, #kt_wrapper { padding-left: 0 !important; padding-top: 0 !important; }
            body { background: #fff !important; }
        </style>
    @endif
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
        $salary_ctc = '';
        $salary_mode = '';
        $designation = '';
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

    @endphp
    <div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Add Requisition-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1">Salary Structure</a></h1>
      </div>
      <!--end::Add Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
        <span class="text-dark-800 fw-bold"  id="kt_subheader_total">
            <a href="{{ url()->previous() }}" onclick="event.preventDefault(); sstClose();"> Go Back </a>
        </span>
    </div>
    <!--end::Page title-->
  </div>
  <!--end::Container-->
</div>

    {{-- ── Salary-template redesign chrome (visual layer over the existing editor) ── --}}
    <style>
      .sst-edit, .sst-preview, .sst-drawer { --b:#e4e6ef; --bg:#f5f8fa; --t:#181c32; --tm:#7e8299; --tl:#a1a5b7; --p:#5b4dff; --g:#50cd89; }
      .sst-edit-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; background:#fff; border:1px solid var(--b); border-radius:12px; padding:18px 22px; margin:0 8px 16px; }
      .sst-edit-title { font-size:20px; font-weight:700; color:var(--t); display:flex; align-items:center; gap:10px; }
      .sst-edit-badge { display:inline-flex; align-items:center; gap:5px; background:#e8fff3; color:#47be7d; font-size:11px; font-weight:600; padding:3px 10px; border-radius:50px; }
      .sst-edit-sub { font-size:12.5px; color:var(--tm); margin-top:3px; }
      .sst-edit-acts { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
      .sst-cards { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin:0 8px 16px; }
      @media (max-width:1200px){ .sst-cards { grid-template-columns:repeat(2,1fr); } }
      .sst-card { background:#fff; border:1px solid var(--b); border-radius:12px; padding:18px; }
      .sst-card-l { font-size:12px; color:var(--tm); font-weight:600; margin-bottom:8px; }
      .sst-card-v { font-size:26px; font-weight:700; color:var(--t); line-height:1; margin-bottom:8px; }
      .sst-card-v.p { color:var(--p); }
      .sst-card-note { display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; }
      .sst-card-note.ok { color:var(--g); } .sst-card-note.add { color:var(--p); } .sst-card-note.neutral { color:var(--tm); }
      .sst-card.valid { background:#f8fff9; border-color:#cdf3df; }
      .sst-valid-row { display:flex; align-items:center; gap:10px; }
      .sst-valid-ic { width:34px; height:34px; border-radius:8px; background:#e8fff3; color:var(--g); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
      /* polish the existing editor card + tables */
      .sst-edit #pointList, .sst-edit #bonusList, .sst-edit #insurenceList { width:100% !important; }
      .sst-edit #pointList thead th, .sst-edit #bonusList thead th, .sst-edit #insurenceList thead th { font-size:11px; text-transform:uppercase; letter-spacing:.3px; color:var(--tm); }
      .sst-help { display:grid; grid-template-columns:1fr 1.4fr; gap:20px; background:var(--bg); border:1px solid var(--b); border-radius:12px; padding:18px 22px; margin:8px 8px 20px; }
      @media (max-width:992px){ .sst-help { grid-template-columns:1fr; } }
      .sst-help h4 { font-size:13.5px; font-weight:700; color:var(--t); margin:0 0 6px; }
      .sst-help p { font-size:12.5px; color:var(--tm); line-height:1.6; margin:0; }
      .sst-ex { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
      .sst-ex .chip { background:#fff; border:1px solid var(--b); border-radius:8px; padding:8px 12px; font-size:12px; font-weight:600; color:var(--t); }
      .sst-ex .chip.add { background:#fff8f0; border-color:#ffe0c2; color:#c2671a; }
      .sst-ex .op { color:var(--tm); font-weight:600; }
      /* top form row polish — flatten the two field rows into one clean 6-field row */
      .sst-edit #step1 .form-label { font-size:12px; font-weight:600; color:var(--t); margin-bottom:6px; white-space:nowrap; }
      .sst-edit #step1 .form-select-solid, .sst-edit #step1 .form-control-solid { background:#fff !important; border:1px solid var(--b) !important; border-radius:8px; font-size:13px; }
      .sst-edit #step1 > .tab-content { background:#fff; border:1px solid var(--b); border-radius:12px; padding:18px 20px; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type { display:flex; flex-wrap:wrap; gap:14px; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type > .row { display:contents; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type .col-4 { flex:1 1 15%; min-width:140px; max-width:none; width:auto; padding:0 !important; margin:0 !important; }
      /* Compensation fields mirror the read-only annexure view; all existing form names remain unchanged. */
      .sst-edit #createForm .sst-mf-desc { display:none !important; }
      .sst-edit #createForm .sst-mf-name { order:1; } .sst-edit #createForm .col-4:has(#current_ctc_cur) { order:2; }
      .sst-edit #createForm .col-4:has(#ctc) { order:3; } .sst-edit #createForm .col-4:has(#current_ctc_mode) { order:4; }
      .sst-edit #createForm .col-4:has(#current_designation) { order:5; } .sst-edit #createForm .col-4:has(#dob) { order:6; }
      .sst-edit #createForm .col-4:has(#employment_type) { order:7; }
      /* breadcrumb bar */
      .sst-bc { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; background:#fff; border:1px solid var(--b); border-radius:12px; padding:12px 18px; margin:0 8px 12px; }
      .sst-bc-l { display:flex; align-items:center; gap:9px; font-size:13px; }
      .sst-bc-l i { font-size:9px; color:var(--tl); }
      .sst-bc-mut { color:var(--tm); } .sst-bc-cur { color:var(--t); font-weight:700; }
      .sst-bc-back { color:#5b4dff; font-weight:600; text-decoration:none; }
      .sst-bc-r { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
      .sst-bc-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 13px; border:1px solid var(--b); background:#fff; border-radius:8px; font-size:12.5px; font-weight:500; color:var(--t); cursor:pointer; }
      .sst-bc-btn:hover { background:var(--bg); }
      .sst-bc-btn i { font-size:11px; color:var(--tm); }
      .sst-bc-btn.icon { padding:7px 10px; }
      /* component table polish */
      .sst-edit #pointList, .sst-edit #bonusList, .sst-edit #insurenceList { border:1px solid var(--b); border-radius:10px; overflow:hidden; }
      .sst-edit #pointList thead tr, .sst-edit #bonusList thead tr, .sst-edit #insurenceList thead tr { background:#f8f9fa; }
      .sst-edit #pointList .form-control-solid, .sst-edit #pointList .form-select-solid,
      .sst-edit #bonusList .form-control-solid, .sst-edit #bonusList .form-select-solid,
      .sst-edit #insurenceList .form-control-solid, .sst-edit #insurenceList .form-select-solid { background:#fff !important; border:1px solid var(--b) !important; border-radius:7px; font-size:13px; }
      /* table header look */
      .sst-edit #pointList thead th, .sst-edit #bonusList thead th, .sst-edit #insurenceList thead th { padding:12px 10px !important; border:none !important; }
      /* row: drag handle + auto number in the first cell */
      .sst-edit #pointList tbody, .sst-edit #bonusList tbody, .sst-edit #insurenceList tbody { counter-reset: sstrow; }
      .sst-edit #pointList tbody tr.other_section_row, .sst-edit #bonusList tbody tr.other_section_row, .sst-edit #insurenceList tbody tr.other_section_row { counter-increment: sstrow; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child,
      .sst-edit #bonusList tbody tr.other_section_row > td:first-child,
      .sst-edit #insurenceList tbody tr.other_section_row > td:first-child { position:relative; padding-left:50px !important; white-space:nowrap; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child::before,
      .sst-edit #bonusList tbody tr.other_section_row > td:first-child::before,
      .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::before {
        content:"\22EE\22EE  " counter(sstrow); position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#a1a5b7; font-size:12px; font-weight:600; letter-spacing:-2px; }
      /* name input → looks like text, with a Main/Additional badge after it */
      .sst-edit #pointList .offer_points, .sst-edit #bonusList .offer_points, .sst-edit #insurenceList .offer_points,
      .sst-edit #bonusList .bonus_offer_points, .sst-edit #insurenceList .insurence_offer_points {
        border:none !important; background:transparent !important; box-shadow:none !important; font-weight:600; color:#181c32; width:auto !important; max-width:150px; display:inline-block; vertical-align:middle; padding:4px 0 !important; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child::after,
      .sst-edit #bonusList tbody tr.other_section_row > td:first-child::after,
      .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::after {
        content:"Main"; display:inline-block; vertical-align:middle; margin-left:8px; background:#eef2ff; color:#5b4dff; font-size:10px; font-weight:600; padding:2px 8px; border-radius:6px; }
      .sst-edit #bonusList tbody tr.other_section_row > td:first-child::after,
      .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::after { content:"Additional"; background:#f8f5ff; color:var(--p); }
      /* dropdowns / percentage */
      .sst-edit #pointList .basic_view, .sst-edit #pointList .option_salarymode,
      .sst-edit #bonusList .basic_view, .sst-edit #bonusList .option_salarymode,
      .sst-edit #insurenceList .basic_view, .sst-edit #insurenceList .option_salarymode { width:96px !important; }
      /* action cell: pencil + red delete */
      .sst-edit #pointList tbody tr.other_section_row > td:last-child,
      .sst-edit #bonusList tbody tr.other_section_row > td:last-child,
      .sst-edit #insurenceList tbody tr.other_section_row > td:last-child { white-space:nowrap; }
      .sst-edit #pointList tbody tr.other_section_row > td:last-child::before,
      .sst-edit #bonusList tbody tr.other_section_row > td:last-child::before,
      .sst-edit #insurenceList tbody tr.other_section_row > td:last-child::before {
        content:"\270E"; color:#a1a5b7; font-size:15px; margin-right:12px; cursor:pointer; vertical-align:middle; }
      .sst-edit .remove_points { background:transparent !important; border:none !important; color:#f1416c !important; padding:0 !important; }
      /* "Add component" buttons */
      .sst-edit .addRowBtn, .sst-edit .addRowBtnbonus { background:#fff !important; border:1px solid var(--b) !important; color:#5b4dff !important; border-radius:8px !important; font-weight:600 !important; font-size:12.5px !important; box-shadow:none !important; }
      /* preview sidebar */
      .sst-preview { background:#fff; border:1px solid var(--b); border-radius:12px; padding:18px; }
      .sst-preview::-webkit-scrollbar { width:5px; } .sst-preview::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:3px; }
      .sst-pv-title { font-size:14px; font-weight:700; color:var(--p); display:flex; align-items:center; gap:7px; margin-bottom:3px; }
      .sst-pv-sub { font-size:11.5px; color:var(--tm); margin-bottom:14px; }
      .sst-pv-lbl { font-size:11.5px; font-weight:600; color:var(--t); margin:10px 0 5px; }
      .sst-pv-amt { display:flex; gap:8px; }
      .sst-pv-amt input { flex:1; border:1px solid var(--b); border-radius:8px; padding:8px 10px; font-size:13px; outline:none; }
      .sst-pv-calc { background:var(--p); color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:12.5px; font-weight:600; cursor:pointer; }
      .sst-pv-toggle { display:flex; gap:6px; margin-top:4px; }
      .sst-pv-toggle button { flex:1; border:1px solid var(--b); background:#fff; color:var(--tm); border-radius:7px; padding:6px; font-size:11.5px; font-weight:600; cursor:pointer; }
      .sst-pv-toggle button.on { background:#5b4dff; color:#fff; border-color:#5b4dff; }
      .sst-pv-box { background:#fafbfc; border:1px solid var(--b); border-radius:10px; padding:12px; margin-top:12px; }
      .sst-pv-row { display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--tm); padding:5px 0; }
      .sst-pv-row .v { font-weight:600; color:var(--t); }
      .sst-pv-row.total { border-top:1px solid var(--b); margin-top:6px; padding-top:9px; font-weight:700; color:var(--t); }
      .sst-pv-row.total .v { font-size:14px; }
      .sst-donut-wrap { display:flex; align-items:center; gap:14px; margin-top:14px; }
      .sst-leg { font-size:11.5px; color:var(--tm); }
      .sst-leg .li { display:flex; align-items:center; gap:6px; margin-bottom:6px; }
      .sst-leg .dot { width:9px; height:9px; border-radius:50%; }
      .sst-vchecks { margin-top:16px; }
      .sst-vchecks h5 { font-size:12.5px; font-weight:700; color:var(--t); margin:0 0 8px; }
      .sst-vchecks .ck { display:flex; align-items:flex-start; gap:7px; font-size:11.5px; color:var(--tm); margin-bottom:7px; }
      .sst-vchecks .ck i { font-size:12px; margin-top:1px; }
      .sst-vchecks .ck.ok i { color:var(--g); } .sst-vchecks .ck.warn i { color:#f6a723; }
      /* cards relocated into the form row → full width, sit above the tables */
      .sst-cards.sst-cards-inrow { flex:0 0 100%; max-width:100%; width:100%; margin:4px 0 18px !important; }
      /* two-column area below the cards: tables (left) + preview (right) */
      .sst-2col { display:flex; gap:18px; align-items:flex-start; flex:0 0 100%; width:100%; }
      .sst-2col > .sst-2col-main { flex:1 1 auto; min-width:0; }
      .sst-2col > .sst-preview { width:310px; flex:0 0 310px; position:sticky; top:80px; align-self:flex-start; }
      @media (max-width:1200px){ .sst-2col { flex-wrap:wrap; } .sst-2col > .sst-preview { width:100%; flex-basis:100%; position:static; } }
      /* Embedded drawer: reproduce the compact Compensation Details layout without changing form names or save behavior. */
      @if (request('embed'))
      html, body, #kt_wrapper, #kt_content, #kt_content_container { min-height:100% !important; background:#f5f8fa !important; }
      .sst-edit #kt_activities_scroll { background:#f5f8fa !important; padding:12px !important; overflow-x:hidden !important; }
      .sst-edit #kt_activities_scroll { height:auto !important; max-height:none !important; min-height:0 !important; overflow-y:visible !important; }
      #kt_offercreate_body, #kt_offercreate_body > div, #kt_activities_scroll > .card, #kt_activities_scroll .card-custom { width:100% !important; max-width:none !important; }
      #kt_activities_scroll > .card, #kt_activities_scroll .card-custom { margin:0 !important; border:0 !important; box-shadow:none !important; background:transparent !important; }
      #kt_offercreate_body .card-body { padding:0 !important; }
      .sst-edit #step1 { padding-top:0 !important; }
      .sst-edit #step1 > .tab-content { padding:14px !important; border-radius:9px !important; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px 14px; margin:0 0 14px !important; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type > .row { display:contents; }
      .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type .col-4 { width:auto !important; min-width:0 !important; max-width:none !important; padding:0 !important; margin:0 !important; }
      .sst-edit #step1 .form-label { display:flex; align-items:center; gap:3px; margin:0 0 4px !important; font-size:10px !important; line-height:1.2; }
      .sst-edit #step1 .form-select-solid, .sst-edit #step1 .form-control-solid { min-height:31px !important; height:31px !important; padding:5px 8px !important; border-color:#dce3ef !important; border-radius:5px !important; font-size:11px !important; }
      .sst-edit #pointList, .sst-edit #bonusList, .sst-edit #insurenceList { width:100% !important; min-width:0 !important; table-layout:fixed; margin:0 !important; }
      .sst-edit #pointList th, .sst-edit #pointList td, .sst-edit #bonusList th, .sst-edit #bonusList td, .sst-edit #insurenceList th, .sst-edit #insurenceList td { padding:5px 5px !important; font-size:10px !important; vertical-align:middle !important; }
      .sst-edit #pointList td, .sst-edit #bonusList td, .sst-edit #insurenceList td { color:#26334f; line-height:1.2; text-align:left; }
      .sst-edit #pointList th, .sst-edit #bonusList th, .sst-edit #insurenceList th { background:#f7f9fc; color:#64748b; font-weight:600; white-space:normal !important; line-height:1.15; }
      .sst-edit #pointList th:nth-child(4), .sst-edit #pointList td:nth-child(4), .sst-edit #bonusList th:nth-child(4), .sst-edit #bonusList td:nth-child(4), .sst-edit #insurenceList th:nth-child(4), .sst-edit #insurenceList td:nth-child(4) { display:none; }
      .sst-edit #pointList th:nth-child(1), .sst-edit #bonusList th:nth-child(1), .sst-edit #insurenceList th:nth-child(1) { width:25% !important; }
      .sst-edit #pointList th:nth-child(2), .sst-edit #bonusList th:nth-child(2), .sst-edit #insurenceList th:nth-child(2) { width:15% !important; }
      .sst-edit #pointList th:nth-child(3), .sst-edit #bonusList th:nth-child(3), .sst-edit #insurenceList th:nth-child(3) { width:11% !important; }
      .sst-edit #pointList th:nth-child(5), .sst-edit #bonusList th:nth-child(5), .sst-edit #insurenceList th:nth-child(5) { width:14% !important; }
      .sst-edit #pointList th:nth-child(6), .sst-edit #bonusList th:nth-child(6), .sst-edit #insurenceList th:nth-child(6) { width:14% !important; }
      .sst-edit #pointList th:nth-child(7), .sst-edit #bonusList th:nth-child(7), .sst-edit #insurenceList th:nth-child(7) { width:14% !important; }
      .sst-edit #pointList th:nth-child(8), .sst-edit #bonusList th:nth-child(8), .sst-edit #insurenceList th:nth-child(8) { width:7% !important; }
      .sst-edit #pointList .form-control, .sst-edit #pointList .form-select, .sst-edit #bonusList .form-control, .sst-edit #bonusList .form-select, .sst-edit #insurenceList .form-control, .sst-edit #insurenceList .form-select { width:100% !important; min-width:0 !important; min-height:28px !important; height:28px !important; padding:3px 5px !important; font-size:10px !important; border-radius:4px !important; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child, .sst-edit #bonusList tbody tr.other_section_row > td:first-child, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child { padding-left:26px !important; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child .offer_points, .sst-edit #bonusList tbody tr.other_section_row > td:first-child .offer_points, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child .offer_points { line-height:1.2 !important; vertical-align:middle !important; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child::before, .sst-edit #bonusList tbody tr.other_section_row > td:first-child::before, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::before { left:5px !important; font-size:9px !important; }
      /* Keep serial numbers and Main/Additional badges inside the name cell on every component row. */
      .sst-edit #pointList tbody tr.other_section_row > td:first-child, .sst-edit #bonusList tbody tr.other_section_row > td:first-child, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child { position:relative !important; padding:5px 64px 5px 27px !important; white-space:normal !important; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child::before, .sst-edit #bonusList tbody tr.other_section_row > td:first-child::before, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::before { content:counter(sstrow) !important; left:8px !important; width:13px; color:#71809d; font-size:10px !important; font-weight:600; letter-spacing:0; text-align:center; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child::after, .sst-edit #bonusList tbody tr.other_section_row > td:first-child::after, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child::after { position:absolute !important; right:7px; left:auto !important; top:50%; margin:0 !important; transform:translateY(-50%); white-space:nowrap; }
      .sst-edit #pointList tbody tr.other_section_row > td:first-child .offer_points, .sst-edit #bonusList tbody tr.other_section_row > td:first-child .offer_points, .sst-edit #bonusList tbody tr.other_section_row > td:first-child .bonus_offer_points, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child .offer_points, .sst-edit #insurenceList tbody tr.other_section_row > td:first-child .insurence_offer_points { box-sizing:border-box; width:100% !important; max-width:none !important; }
      .salary-editor-card { margin:0 0 12px; border:1px solid #dce3ef; border-radius:8px; background:#fff; overflow:hidden; }
      .salary-editor-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:12px 14px 7px; }
      .salary-editor-card-head h3 { margin:0; color:#18223d; font-size:12px; font-weight:600; }
      .salary-editor-card-head h3 span { color:#7d8aa6; font-size:10px; font-weight:500; }
      .salary-editor-card-head p { margin:2px 0 0; color:#7d8aa6; font-size:9px; font-weight:500; }
      .salary-editor-add { display:inline-flex; align-items:center; gap:5px; min-height:27px; padding:5px 9px; border:1px solid #d7e0ff; border-radius:5px; background:#f6f8ff; color:#315df6; font-size:10px; font-weight:600; white-space:nowrap; }
      .salary-editor-card .row { margin:0 !important; overflow:visible !important; }
      .salary-editor-card .addRowBtn, .salary-editor-card .addRowBtnbonus { display:none !important; }
      .salary-editor-card #bonusList thead tr:first-child { display:none; }
      .salary-editor-card #final_pay { border:0 !important; background:#e9fff2; }
      .salary-editor-card #final_pay th { padding:8px 7px !important; background:#e9fff2 !important; }
      .salary-editor-card-summary { margin:0 14px 10px; padding:7px 9px; border-radius:5px; background:#dcfbe9; color:#118651; font-size:10px; font-weight:600; }
      .salary-editor-card.additional .salary-editor-card-summary { background:#f4efff; color:#6942c6; }
      .salary-editor-card.final .salary-editor-card-head { padding-bottom:2px; }
      .sst-edit #pointList tbody tr.other_section_row > td:last-child::before, .sst-edit #bonusList tbody tr.other_section_row > td:last-child::before, .sst-edit #insurenceList tbody tr.other_section_row > td:last-child::before { content:none !important; display:none !important; }
      .sst-edit .remove_points { display:inline-flex !important; align-items:center; justify-content:center; width:26px; height:26px; padding:0 !important; border:1px solid #ffd8e2 !important; border-radius:5px !important; background:#fff5f8 !important; color:#f1416c !important; }
      .sst-edit .remove_points svg { width:13px !important; height:13px !important; }
      .sst-edit .remove_points svg rect { fill:#f1416c !important; }
      .sst-edit .remove_points:disabled { opacity:.35; cursor:not-allowed; }
      @media (max-width:900px){ .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type { grid-template-columns:repeat(2,minmax(0,1fr)); } }
      @media (max-width:600px){ .sst-edit #createForm > .form-group.row > .col-lg-12:first-of-type { grid-template-columns:1fr; } .sst-edit #pointList, .sst-edit #bonusList, .sst-edit #insurenceList { min-width:720px !important; } .sst-edit #kt_offercreate_body .row { overflow-x:auto; } }
      @endif
    </style>
    <div class="sst-edit">
      {{-- The drawer keeps only editable controls; standalone editing retains its navigation. --}}
      @unless (request('embed'))
      <div class="sst-bc">
        <div class="sst-bc-l">
          <span class="sst-bc-mut">Salary Structure</span>
          <i class="fas fa-chevron-right"></i>
          <span class="sst-bc-cur">Edit Template</span>
          <i class="fas fa-chevron-right"></i>
          <a href="{{ url()->previous() }}" onclick="event.preventDefault(); sstClose();" class="sst-bc-back">Go Back</a>
        </div>
        <div class="sst-bc-r">
          <button type="button" class="sst-bc-btn"><i class="far fa-clone"></i>Clone Template</button>
          <button type="button" class="sst-bc-btn"><i class="fas fa-history"></i>Version History</button>
          <button type="button" class="sst-bc-btn"><i class="fas fa-play"></i>Test Template</button>
          <button type="button" class="sst-bc-btn icon"><i class="fas fa-ellipsis-v"></i></button>
        </div>
      </div>
      @endunless

      @unless (request('embed'))
      <div class="sst-edit-head">
        @unless (request('embed'))
        <div>
          <div class="sst-edit-title">Edit Salary Template <span class="sst-edit-badge"><i class="fas fa-check" style="font-size:8px;"></i>Active</span></div>
          <div class="sst-edit-sub">Create and manage reusable salary components for the candidate offer letter.</div>
        </div>
        @endunless
        <div class="sst-edit-acts">
          @unless (request('embed'))
          <a href="{{ url()->previous() }}" onclick="event.preventDefault(); sstClose();" class="btn btn-light">Close</a>
          @endunless
          <button type="button" class="btn" style="background:#1b84ff;color:#fff;" onclick="document.getElementById('save-form') && document.getElementById('save-form').click();">Save Template</button>
          <button type="button" class="btn" style="background:#5b21d6;color:#fff;" onclick="document.getElementById('save-form') && document.getElementById('save-form').click();">Save &amp; Set as Default</button>
        </div>
      </div>
      @endunless

      @unless (request('embed'))
      <div class="sst-cards">
        <div class="sst-card">
          <div class="sst-card-l">Main Components (Must Total 100%)</div>
          <div class="sst-card-v"><span id="sstEditMain">0.00</span>%</div>
          <span class="sst-card-note neutral" id="sstEditMainNote"><i class="fas fa-info-circle"></i>Must be exactly 100%</span>
        </div>
        <div class="sst-card">
          <div class="sst-card-l">Additional Components (Above 100%)</div>
          <div class="sst-card-v p"><span id="sstEditAdd">0.00</span>%</div>
          <span class="sst-card-note add"><i class="fas fa-plus"></i>Allowed above 100%</span>
        </div>
        <div class="sst-card">
          <div class="sst-card-l">Overall Structure</div>
          <div class="sst-card-v p"><span id="sstEditOverall">0.00</span>%</div>
          <span class="sst-card-note neutral"><i class="fas fa-chart-bar"></i>Main + Additional</span>
        </div>
        <div class="sst-card valid">
          <div class="sst-valid-row">
            <div class="sst-valid-ic"><i class="fas fa-shield-alt"></i></div>
            <div>
              <div class="sst-card-l" style="color:#47be7d;margin-bottom:2px;">Validation Status</div>
              <div class="sst-edit-sub" id="sstEditValid">Structure is valid and ready to save.</div>
            </div>
          </div>
        </div>
      </div>
      @endunless
    </div>

    @unless (request('embed'))
    {{-- Preview is intentionally available only in standalone editing, not in the account drawer. --}}
    <div class="sst-preview" id="sstPreview">
      <div class="sst-pv-title"><i class="fas fa-eye"></i>Preview Salary Structure</div>
      <div class="sst-pv-sub">See how this structure calculates for a given amount.</div>
      <div class="sst-pv-lbl">Enter Amount</div>
      <div class="sst-pv-amt">
        <input type="text" id="sstPvAmount" placeholder="Enter amount">
        <button type="button" class="sst-pv-calc" id="sstPvCalc">Calculate</button>
      </div>
      <div class="sst-pv-lbl">View As</div>
      <div class="sst-pv-toggle">
        <button type="button" class="on" data-mode="monthly">Monthly View</button>
        <button type="button" data-mode="annual">Annual View</button>
      </div>
      <div class="sst-pv-box">
        <div style="font-size:12.5px;font-weight:700;color:var(--p);margin-bottom:8px;" id="sstPvHead">Breakup Preview (Monthly)</div>
        <div class="sst-pv-row"><span>Main Components (<span id="sstPvMainPct">0</span>%)</span><span class="v" id="sstPvMainAmt">—</span></div>
        <div class="sst-pv-row"><span>Additional Components (<span id="sstPvAddPct">0</span>%)</span><span class="v" id="sstPvAddAmt">—</span></div>
        <div class="sst-pv-row total"><span>Total Cost to Company</span><span class="v" id="sstPvTotal">—</span></div>
        <div class="sst-donut-wrap">
          <svg width="86" height="86" viewBox="0 0 42 42">
            <circle cx="21" cy="21" r="15.9155" fill="none" stroke="#eef2f5" stroke-width="6"></circle>
            <circle id="sstDonutMain" cx="21" cy="21" r="15.9155" fill="none" stroke="#16a34a" stroke-width="6" stroke-dasharray="80 20" stroke-dashoffset="25" transform="rotate(-90 21 21)"></circle>
            <circle id="sstDonutAdd" cx="21" cy="21" r="15.9155" fill="none" stroke="#5b4dff" stroke-width="6" stroke-dasharray="20 80" stroke-dashoffset="25" transform="rotate(-90 21 21)"></circle>
          </svg>
          <div class="sst-leg">
            <div class="li"><span class="dot" style="background:#16a34a"></span>Main (<span id="sstLegMain">0</span>%) <span style="margin-left:auto;font-weight:600;color:var(--t)" id="sstLegMainAmt">—</span></div>
            <div class="li"><span class="dot" style="background:#5b4dff"></span>Additional (<span id="sstLegAdd">0</span>%) <span style="margin-left:auto;font-weight:600;color:var(--t)" id="sstLegAddAmt">—</span></div>
          </div>
        </div>
      </div>
      <div class="sst-vchecks">
        <h5>Validation Checks</h5>
        <div class="ck" id="sstCk1"><i class="fas fa-check-circle"></i><span>Main components total is exactly 100%</span></div>
        <div class="ck ok"><i class="fas fa-check-circle"></i><span>All formulas are valid</span></div>
        <div class="ck ok"><i class="fas fa-check-circle"></i><span>All required components have conditions</span></div>
        <div class="ck warn" id="sstCk2"><i class="fas fa-exclamation-triangle"></i><span>Additional components exceed 100% (Allowed)</span></div>
      </div>
    </div>
    @endunless

    <div class="card-body position-relative px-0 py-0 sst-edit" id="kt_offercreate_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="card mb-5 mb-xl-5 mx-2">
                <div class="card-body pt-5 pb-0 px-5">

                    <div clas <div class="card card-custom mx-2">

                        <div class="card-body py-0">


                            <!-- Step 1 -->
                            <div class="tab-content-section pt-5 active" id="step1">
                                <div class="tab-content pt-5">
                                    <form class="ajax-form" method="POST" id="createForm" name="createForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <input type="hidden" name="offer_id" value="{{ $offer_id }}">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-4 sst-mf-name">
                                                        <label class="form-label required">Template Name <span style="color:#f1416c;">*</span></label>
                                                        <div style="position:relative;">
                                                            <input type="text" class="form-control form-control-solid" id="sstTemplateName" name="template_name" value="Standard Payroll Structure" placeholder="Template name" style="padding-right:30px;">
                                                        </div>
                                                    </div>
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required">Currency</label>
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
                                                    <div class="col-4 px-2">
                                                        <label class="form-label required"><i class="fas fa-wallet me-1 text-primary"></i>Enter Amount (CTC)</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            name="ctc" id="ctc"
                                                            placeholder="Enter current CTC"
                                                            value="{{ @$salary_ctc }}">
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                        </div>
                                                    </div>
                                                    <div class="col-4 pl-0">
                                                        <label class="form-label required">Salary Format</label>
                                                        <select class="form-select form-select-solid"
                                                            arial-label="Select Annual Salary" id="current_ctc_mode"
                                                            name="current_ctc_mode">
                                                            <option value="2" {{ $salary_mode == 2 ? 'selected' : '' }}>Hourly</option>
                                                            <option value="1" {{ $salary_mode == 1 ? 'selected' : '' }}>Monthly</option>
                                                            <option value="3" {{ $salary_mode == 3 ? 'selected' : '' }}>Annual</option>
                                                            <option value="0" {{ empty($salary_mode) || $salary_mode == 0 ? 'selected' : '' }}>Monthly/Annual</option>
                                                            <option value="4" {{ $salary_mode == 4 ? 'selected' : '' }}>Hourly/Annual</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required"><i class="fas fa-briefcase me-1 text-primary"></i>Offered Designation</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            name="current_designation" id="current_designation"
                                                            placeholder="Enter designation"
                                                            value="{{ @$designation }}">
                                                    </div>
                                                    <div class="col-4 px-2">
                                                        <label class="form-label"><i class="far fa-calendar-alt me-1 text-primary"></i>Date of Joining (DOJ)</label>
                                                        <input type="datetime-local"
                                                            class="form-control form-control-solid" name="dob"
                                                            id="dob" placeholder="Enter dob"
                                                            value="{{ @$joined_date }}" onclick="this.showPicker()">
                                                    </div>
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required">Job Type</label>
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
                                            <div class="col-lg-12">

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
                                                        role="grid" aria-describedby="myTable_info"
                                                        style="width: 1270px;">
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
                                                                    style="width: 10% !important;">Conditions</th>
                                                                <th class="sorting_disabled change_text_first"
                                                                    style="width: 17% !important;">Monthly (Preview)</th>
                                                                <th class="sorting_disabled"
                                                                    style="width:15% !important">Annually (Preview)</th>
                                                                <th class="sorting_disabled">Action</th>
                                                            </tr>
                                                            <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}"
                                                                role="row">
                                                                <th class="sorting_disabled"
                                                                    style="width: 47% !important;">Components</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 12% !important;">Percentage</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 3% !important;">&nbsp;</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 10% !important;">Conditions</th>
                                                                <th class="sorting_disabled change_text"
                                                                    style="width:15% !important">Hourly</th>
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
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_points"
                                                                                    name="offer_point[]"
                                                                                    id="offer_point_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_poins }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="">
                                                                                <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $basic->id }}" data-id="{{ $basic->id }}"
                                                                                data-offerid="{{ $basic->id }}"
                                                                                style="width: 100px;display: initial; ">
                                                                                <option value="0" {{ $basic->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                <option value="1" {{ $basic->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                <option value="2" {{ $basic->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                </select>
                                                                            </td>

                                                                            <td class="">

                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage"
                                                                                    name="option_percentage[]"
                                                                                    id="option_percentage_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_percentage }}"
                                                                                    placeholder="%">


                                                                            </td>
                                                                            <td class="" style="position: relative;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                                                            <td class="">
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
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_monthly"
                                                                                    name="offer_monthly[]"
                                                                                    id="offer_monthly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_monthly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_yearly {{ $key > 0 ? 'other_salary_get' : 'basic_salary_get' }}"
                                                                                    name="offer_yearly[]"
                                                                                    id="offer_yearly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_yearly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td style="float:right">
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon-2x">
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
                                                                                    </span> Delete </button>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="hourly_section hourly_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                                                            role="row">
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_point_hourly"
                                                                                    name="offer_point_hourly[]"
                                                                                    id="offer_point_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_poins }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage_hourly"
                                                                                    name="option_percentage_hourly[]"
                                                                                    id="option_percentage_hourly{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_percentage }}"
                                                                                    placeholder="%">

                                                                                {{-- <select &nbsp=""
                                                                                    class="form-select form-select-solid option_percentage_hourly"
                                                                                    arial-label="Select Percentage"
                                                                                    name="option_percentage_hourly[]" id="option_percentage_hourly{{ $basic->id }}"
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
                                                                            <td class="">
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
                                                                            <td class="hourly_section">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_hourly {{ $key > 0 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                                                                    name="offer_hourly[]"
                                                                                    id="offer_hourly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_yearly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td style="float:right">
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon-2x">
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
                                                                                    </span> Delete </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @else
                                                                @if ($offer_points)
                                                                    @foreach ($offer_points as $key => $value)
                                                                        <tr class="other_section other_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_other }}"
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
                                                                            <td class="" style="width:10% !important">
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
                                                                            <td class="" style="width:17% !important">
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
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0"
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

                                                                                <button type="button" id="addRowBtn" class="btn btn-primary addRowBtn me-0" style="">
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
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0"
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
                                                                                    class="btn btn-primary addRowBtn me-0" style="">
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
                                                                    <input type="text" class="form-control form-control-solid" value="Total Gross Amount" readonly style="cursor: not-allowed;background: #ffeb0066;">
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
                                                                    <input type="text" class="form-control form-control-solid" value="Total Gross Amount" readonly style="cursor: not-allowed;background: #ffeb0066;">
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

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList"
                                                        role="grid" aria-describedby="bonusList_info"
                                                        style="width: 1270px;">
                                                        <thead>
                                                            <tr class="other_section other_section_row row-template {{ $section_other }}">
                                                                <th colspan="7" class="sorting_disabled"
                                                                    style="width: 102px;">Other Component</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 102px;position: relative;">
                                                                    <button type="button" id="addRowBtnbonus"
                                                                    class="btn btn-primary addRowBtnbonus me-0" style="position: absolute; top: -25px; right: 16px;">
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
                                                                    style="width: 102px;">Other Component</th>

                                                                    <th class="sorting_disabled"
                                                                    style="width: 102px;position: relative;">
                                                                    <button type="button" id=""
                                                                        class="btn btn-primary me-0 addRowBtnbonus" style="position: absolute; top: -25px; right: 16px;">
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
                                                                            <td class="" style="width: 10% !important;">
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
                                                                            <td class="" style="width: 17% !important;">
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
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0">
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
                                                                                    class="btn btn-primary addRowBtnbonus me-0" style="">
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
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0">
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
                                                                                    class="btn btn-primary me-0 addRowBtnbonus" style="">
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

                                                        </tbody>
                                                        <tfoot class="other_section {{ $section_other }}">
                                                           
                                                            <tr>
                                                                <th colspan="2" class="sorting_disabled">
                                                                   
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalpercentagebonus"
                                                                        name="totalpercentagebonus"
                                                                        value=""
                                                                        id="totalpercentagebonus">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled">&nbsp;</th>
                                                                <th class="sorting_disabled" style="width: 78px;">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalMonthlybonus"
                                                                        name="totalMonthlybonus"
                                                                        id="totalMonthlybonus"
                                                                        value="{{ @$monthly_bounus_gross }}">
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalYearlybonus"
                                                                        name="totalYearlybonus"
                                                                        value="{{ @$yearly_bonus_gross }}"
                                                                        id="totalYearlybonus">
                                                                </th>
                                                                <th class="sorting_disabled">&nbsp;</th>
                                                            </tr>
                                                        </tfoot>

                                                        <tfoot class="hourly_section {{ $section_hourly }}">
                                                            <tr>
                                                                <th colspan="6" class="sorting_disabled"
                                                                    style="width: 102px;">&nbsp; </th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="4" class="sorting_disabled"
                                                                    style="width: 86% !important;">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalhourlypercentagebonus"
                                                                        name="totalhourlypercentagebonus"
                                                                        value=""
                                                                        id="totalhourlypercentagebonus">
                                                                </th>
                                                                <th class="sorting_disabled" colspan="1">
                                                                    <input &nbsp="" type="hidden"
                                                                        class="form-control form-control-solid totalhourlyybonus"
                                                                        name="totalhourlyybonus"
                                                                        value="{{ @$yearly_bonus_gross }}"
                                                                        id="totalhourlyybonus">
                                                                </th>
                                                            </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer"
                                                        id="final_pay" role="grid"
                                                        aria-describedby="final_pay_info" style="width: 1270px;">


                                                        <tfoot class="other_section_final other_section {{ $section_other }}">

                                                            <tr>
                                                                <th colspan="2" class="sorting_disabled" style="width: 35%;">
                                                                    <input type="text" class="form-control form-control-solid" value="Final Salary (Cost To Company)" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled" style="width: 7% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalpercentagefinal"
                                                                        name="totalpercentagefinal"
                                                                        value=""
                                                                        id="totalpercentagefinal" style="background: #7de96f40;">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled" style="width: 13% !important;">
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
                                                                        value="{{ @$yearly_final_gross }}" style="background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled"  style="width: 15% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid finalpaytotalYearly"
                                                                        name="finalpaytotalYearly"
                                                                        id="finalpaytotalYearly"
                                                                        value="{{ @$monthly_final_gross }}" style="background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled">&nbsp;</th>
                                                            </tr>


                                                        </tfoot>
                                                        <tfoot class="hourly_section_final hourly_section {{ $section_hourly }}">

                                                            <tr>
                                                                <th class="sorting_disabled" style="width: 47%;">
                                                                    <input type="text" class="form-control form-control-solid" value="Final Salary (Cost To Company)" readonly style="cursor: not-allowed;background: #7de96f40;">
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

                                                <div id="text_for_hourly_annually" class="d-none"><br>
                                                    <p><b>Note:</b>*The per-hour salary is calculated based on 8 hours per day, 5 days per week, and 2,080 hours per year. The annual salary is subject to adjustments based on the actual number of hours worked</p> </div>
                                                {{-- Footer Save / Set-to-default hidden: the header (Save Template / Save & Set as Default) drives these now. #save-form is kept so the header button can submit. --}}
                                                <hr class="d-none">
                                                <div class="row justify-content-center" style="display:none !important;">
                                                    <div class="col-lg-3  d-flex justify-content-center">
                                                        <input type="submit" name="save"
                                                            value="Save" id="save-form"
                                                            class="btn btn-primary"
                                                            style="width: 100%;height: 40px;margin-top: 10px;">
                                                    </div>
                                                    <div class="col-lg-3  d-flex justify-content-center section_change_value">
                                                        <button class="next-btn btn btn-warning" style="width: 100%; height: 41px; margin-top: 9px;"><a href="{{ route('service_company.account.offerAmountChange',[encrypt(1)]) }}" class="align-self-center" style="color: #fff;">Set to default</a></button>
                                                     </div>


                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        {{-- How it works (matches the salary-template design) --}}
        <div class="sst-edit">
          <div class="sst-help">
            <div>
              <h4><i class="fas fa-lightbulb" style="color:#f6a723;"></i> How it works?</h4>
              <p>Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</p>
            </div>
            <div>
              <h4>Examples</h4>
              <div class="sst-ex">
                <span class="chip">CTC Amount</span><span class="op">=</span>
                <span class="chip">Main 100%</span><span class="op">+</span>
                <span class="chip add">Additional</span><span class="op">=</span>
                <span class="chip">Total Cost to Company</span>
              </div>
            </div>
          </div>
        </div>

        <script>
          // Live-compute the salary-template summary cards from the editor inputs.
          (function () {
            function num(v){ v = parseFloat(String(v).replace(/[^0-9.\-]/g,'')); return isNaN(v) ? 0 : v; }
            function sum(sel){ var t = 0; document.querySelectorAll(sel).forEach(function(el){ if (el.offsetParent !== null) t += num(el.value); }); return t; }
            function refreshSstCards(){
              var main = sum('#pointList .option_percentage');
              var add  = sum('.bonus_option_percentage') + sum('.insurence_option_percentage');
              var overall = main + add;
              var setT = function(id, val){ var e = document.getElementById(id); if (e) e.textContent = val.toFixed(2); };
              setT('sstEditMain', main); setT('sstEditAdd', add); setT('sstEditOverall', overall);
              var note = document.getElementById('sstEditMainNote');
              var valid = document.getElementById('sstEditValid');
              var ok = Math.abs(main - 100) < 0.01;
              if (note){ note.className = 'sst-card-note ' + (ok ? 'ok' : 'neutral');
                note.innerHTML = ok ? '<i class="fas fa-check-circle"></i>Total is exactly 100%' : '<i class="fas fa-info-circle"></i>Must be exactly 100%'; }
              if (valid){ valid.textContent = ok ? 'Structure is valid and ready to save.' : 'Main components must total 100%.'; }
              var ck1 = document.getElementById('sstCk1');
              if (ck1){ ck1.className = 'ck ' + (ok ? 'ok' : 'warn');
                ck1.querySelector('i').className = ok ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'; }
              refreshPreview();
            }

            var pvMode = 'monthly';
            function inr(n){ try { return '₹ ' + Math.round(n).toLocaleString('en-IN'); } catch(e){ return '₹ ' + Math.round(n); } }
            function setHtml(id, v){ var e = document.getElementById(id); if (e) e.textContent = v; }
            function refreshPreview(){
              var amtEl = document.getElementById('sstPvAmount'); if (!amtEl) return;
              var amount = num(amtEl.value);
              var main = sum('#pointList .option_percentage');
              var add  = sum('.bonus_option_percentage') + sum('.insurence_option_percentage');
              var base = (pvMode === 'monthly') ? amount / 12 : amount;
              var mainAmt = base * main / 100, addAmt = base * add / 100, total = mainAmt + addAmt;
              setHtml('sstPvHead', 'Breakup Preview (' + (pvMode === 'monthly' ? 'Monthly' : 'Annual') + ')');
              setHtml('sstPvMainPct', main.toFixed(0)); setHtml('sstPvAddPct', add.toFixed(2));
              setHtml('sstLegMain', main.toFixed(0)); setHtml('sstLegAdd', add.toFixed(2));
              if (amount > 0){
                setHtml('sstPvMainAmt', inr(mainAmt)); setHtml('sstPvAddAmt', inr(addAmt)); setHtml('sstPvTotal', inr(total));
                setHtml('sstLegMainAmt', inr(mainAmt)); setHtml('sstLegAddAmt', inr(addAmt));
              } else {
                ['sstPvMainAmt','sstPvAddAmt','sstPvTotal','sstLegMainAmt','sstLegAddAmt'].forEach(function(id){ setHtml(id,'—'); });
              }
              var tot = main + add, mainFrac = tot > 0 ? (main / tot) * 100 : 100, addFrac = 100 - mainFrac;
              var dm = document.getElementById('sstDonutMain'), da = document.getElementById('sstDonutAdd');
              if (dm){ dm.setAttribute('stroke-dasharray', mainFrac.toFixed(2) + ' ' + (100 - mainFrac).toFixed(2)); dm.setAttribute('stroke-dashoffset', '25'); }
              if (da){ da.setAttribute('stroke-dasharray', addFrac.toFixed(2) + ' ' + (100 - addFrac).toFixed(2)); da.setAttribute('stroke-dashoffset', (25 - mainFrac).toFixed(2)); }
            }

            document.addEventListener('input', function(e){
              if (!e.target) return;
              if (e.target.classList.contains('option_percentage') || e.target.classList.contains('bonus_option_percentage') || e.target.classList.contains('insurence_option_percentage')) refreshSstCards();
              if (e.target.id === 'sstPvAmount') refreshPreview();
              if (e.target.id === 'sstDescription'){ var cc = document.getElementById('sstCC'); if (cc) cc.textContent = e.target.value.length; }
            });
            document.addEventListener('click', function(e){
              var t = e.target.closest ? e.target.closest('[data-mode]') : null;
              if (t){ pvMode = t.getAttribute('data-mode'); document.querySelectorAll('.sst-pv-toggle button').forEach(function(b){ b.classList.toggle('on', b === t); }); refreshPreview(); }
              if (e.target.id === 'sstPvCalc') refreshPreview();
            });
            function relocateCards(){
              // The summary cards are injected above the editor body; move them to sit between
              // the form fields and the component tables (matches the screenshot order).
              var fields = document.querySelector('#createForm > .form-group.row > .col-lg-12');
              var cards = document.querySelector('.sst-cards');
              if (fields && cards && cards.parentNode !== fields.parentNode) {
                cards.classList.add('sst-cards-inrow');
                fields.insertAdjacentElement('afterend', cards);
              }
            }
            function build2col(){
              // Lay the component tables (left) and the Preview panel (right) side by side,
              // below the full-width summary cards — matching the screenshot.
              var form = document.querySelector('#createForm > .form-group.row');
              var preview = document.getElementById('sstPreview');
              if (!form || !preview) return;
              if (preview.closest('.sst-2col')) return; // already built
              var cols = Array.prototype.filter.call(form.children, function(c){ return c.classList && c.classList.contains('col-lg-12'); });
              var tablesCol = cols[cols.length - 1]; // last col-lg-12 = the tables
              if (!tablesCol) return;
              var wrap = document.createElement('div');
              wrap.className = 'sst-2col';
              tablesCol.parentNode.insertBefore(wrap, tablesCol);
              tablesCol.classList.add('sst-2col-main');
              wrap.appendChild(tablesCol);
              wrap.appendChild(preview);
            }
            @if (request('embed'))
            // Create visual card boundaries around the existing tables; original add handlers stay intact.
            function buildSalaryEditorCards(){
              var makeCard = function(tableId, title, subtitle, kind, buttonSelector, buttonLabel){
                var table = document.getElementById(tableId);
                var row = table && table.closest('.row');
                if (!table || !row || row.closest('.salary-editor-card')) return;
                var card = document.createElement('section');
                card.className = 'salary-editor-card ' + kind;
                var header = document.createElement('div');
                header.className = 'salary-editor-card-head';
                header.innerHTML = '<div><h3>' + title + '</h3><p>' + subtitle + '</p></div>';
                if (buttonSelector){
                  var add = document.createElement('button');
                  add.type = 'button'; add.className = 'salary-editor-add';
                  add.innerHTML = '<i class="fas fa-plus"></i>' + buttonLabel;
                  add.addEventListener('click', function(){ var source = document.querySelector(buttonSelector); if (source) source.click(); });
                  header.appendChild(add);
                }
                row.parentNode.insertBefore(card, row); card.appendChild(header); card.appendChild(row);
                if (kind !== 'final'){
                  var summary = document.createElement('div'); summary.className = 'salary-editor-card-summary';
                  summary.setAttribute('data-summary-for', tableId); card.insertBefore(summary, row);
                }
              };
              makeCard('pointList', 'Main Components <span>(Must Total 100%)</span>', 'These are core components. Total percentage must be exactly 100%.', 'main', '.addRowBtn', 'Add Main Component');
              makeCard('bonusList', 'Additional Components <span>(Above 100% – Optional)</span>', 'These components are added on top of the main 100%.', 'additional', '.addRowBtnbonus', 'Add Additional Component');
              makeCard('final_pay', 'Final CTC <span>(Cost to Company)</span>', 'Summary of total cost including main and additional components.', 'final');
            }
            function refreshEditorCardSummaries(){
              var value = function(selector){ return Array.prototype.reduce.call(document.querySelectorAll(selector), function(total, el){ return total + num(el.value); }, 0); };
              var main = value('#pointList .option_percentage');
              var additional = value('#bonusList .bonus_option_percentage') + value('#insurenceList .insurence_option_percentage');
              var mainSummary = document.querySelector('[data-summary-for="pointList"]');
              var addSummary = document.querySelector('[data-summary-for="bonusList"]');
              if (mainSummary) mainSummary.textContent = 'Total main components: ' + main.toFixed(2) + '% ' + (Math.abs(main - 100) < 0.01 ? '— exactly 100%' : '— must total 100%');
              if (addSummary) addSummary.textContent = 'Total additional components: ' + additional.toFixed(2) + '% (allowed above 100%)';
            }
            @endif
            window.addEventListener('load', function(){
              relocateCards(); build2col();
              @if (request('embed'))
              buildSalaryEditorCards(); refreshEditorCardSummaries();
              document.addEventListener('input', refreshEditorCardSummaries);
              @endif
              setTimeout(function(){
                relocateCards(); build2col();
                var ctc = document.getElementById('ctc'), pv = document.getElementById('sstPvAmount');
                if (ctc && pv && num(ctc.value) > 0) pv.value = ctc.value;
                refreshSstCards();
                @if (request('embed'))
                buildSalaryEditorCards(); refreshEditorCardSummaries();
                @endif
              }, 400);
            });
          })();
        </script>

        @endsection
        @push('footer-script')
            <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
            <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
            <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
            <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
            <script src="{{ asset('demo/js/salarybreak.js') }}"></script>
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
                                } else if (response.status == "error") {
                                    Swal.fire("Warning", "Offer letter not ready", "warning");
                                }
                            }
                        });

                    } else {
                        document.getElementById("step" + currentStep).classList.remove("active");
                        document.getElementById("step" + (currentStep + 1)).classList.add("active");
                    }


                }

                // Function to move to the previous step
                function prevStep(event, currentStep) {
                    event.preventDefault();
                    document.getElementById("step" + currentStep).classList.remove("active");
                    document.getElementById("step" + (currentStep - 1)).classList.add("active");
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
                                    var editor1 = CKEDITOR.instances.editor1.setData(data.terms);
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
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
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
                    $(document).on('keyup', '#ctc', function() {
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
                            var change_text = '';
                            if(current_ctc_mode==1){
                                 change_text = 'Monthly';
                            }else if(current_ctc_mode==4){
                                 change_text = 'Hourly';
                            }else if(current_ctc_mode==3){
                                 change_text = 'Annual';
                            }
                            $('.change_text_first').text(change_text);
                            $('.other_section').removeClass('d-none');
                            $('.hourly_section').addClass('d-none');
                        } else {
                            var change_text = '';
                            if(current_ctc_mode==1){
                                 change_text = 'Monthly';
                            }else if(current_ctc_mode==2){
                                 change_text = 'Hourly';
                            }else if(current_ctc_mode==3){
                                 change_text = 'Annual';
                            }
                            $('.change_text').text(change_text);
                            $('.hourly_section').removeClass('d-none');
                            $('.other_section').addClass('d-none');
                        }
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
                    


                    $('#createForm').on('submit', function(e) {
                        e.preventDefault();
                        //  var formData = new FormData($('#createForm')[0]);	
                        let formData = new FormData(this);
                        $.ajax({
                            url: '{{ route('service_company.account.companysavecreateoffer') }}' +
                                '?_token=' + '{{ csrf_token() }}',
                            type: 'POST',
                            data: formData,
                            processData: false, // tell jQuery not to process the data
                            contentType: false, // tell jQuery not to set contentType
                            redirect: true,
                            success: function(response) {

                                if (response.status == 'success') {
                                    Swal.fire("Success", response.message , "success");	
                                    return false;
                                } else if (response.status == 'errors') {
                                    Swal.fire("Warning", response.message, "warning");
                                } else {
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
        @endpush
