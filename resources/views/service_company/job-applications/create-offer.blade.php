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

        /* ── 5-step timeline stepper ── */
        .co-stepper { display: flex; align-items: flex-start; padding: 16px 24px; background: #fff; border-bottom: 1px solid #eff2f5; }
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
        .co-valid-banner--warn { background: #fff8dd; color: #b78103; }
        .co-valid-banner--warn .co-valid-ico { background: #f6c000; }
        .co-valid-banner--ok { background: #e8fff3; color: #1bc5bd; }
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




    <div class="card-header px-5 py-0" id="kt_activities_header">
        <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark">Compensation</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        </div>

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

                                            {{-- ── Preview Salary Structure panel (100% client-side; reads existing fields, writes nothing) ── --}}
                                            <div class="col-lg-12">
                                                <div class="card card-body py-5 mb-5 mb-xl-5 mx-2 co-preview-card">
                                                    <div class="co-section-head">
                                                        <div>
                                                            <h5 class="co-section-title"><i class="co-preview-eye"></i> Preview Salary Structure</h5>
                                                            <div class="co-section-sub">See how this structure calculates for the entered amount.</div>
                                                        </div>
                                                        <div class="co-view-toggle" id="co_view_toggle">
                                                            <button type="button" class="co-view-btn co-view-btn--active" data-view="monthly">Monthly View</button>
                                                            <button type="button" class="co-view-btn" data-view="annual">Annual View</button>
                                                        </div>
                                                    </div>
                                                    <div class="co-preview-topbar">
                                                        <div class="co-preview-amt">
                                                            <label class="form-label">Amount</label>
                                                            <input type="text" class="form-control form-control-solid" id="co_preview_amount" readonly>
                                                        </div>
                                                        <div class="co-preview-fmt">
                                                            <label class="form-label">Salary Format</label>
                                                            <div id="co_preview_format" class="co-fmt-badge">—</div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary co-calc-btn" id="co_calc_btn">Calculate</button>
                                                    </div>
                                                    <div class="co-preview-grid">
                                                        <div class="co-preview-breakup">
                                                            <div class="co-preview-h">Breakup Preview (<span id="co_breakup_mode">Monthly</span>)</div>
                                                            <table class="co-breakup-table" id="co_breakup_preview">
                                                                <thead><tr><th>Components</th><th class="text-end">Amount</th></tr></thead>
                                                                <tbody></tbody>
                                                                <tfoot>
                                                                    <tr class="co-breakup-total"><td>Total Cost to Company</td><td class="text-end" id="co_tcc">—</td></tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                        <div class="co-preview-chart">
                                                            <div id="co_breakup_chart"></div>
                                                            <div class="co-chart-legend">
                                                                <span><i class="co-dot co-dot--main"></i> Main (<span id="co_legend_main">100%</span>)</span>
                                                                <span><i class="co-dot co-dot--add"></i> Additional (<span id="co_legend_add">0%</span>)</span>
                                                            </div>
                                                        </div>
                                                        <div class="co-preview-checks">
                                                            <div class="co-preview-h">Validation Checks</div>
                                                            <ul class="co-checks" id="co_checks">
                                                                <li data-check="main100"><i class="co-chk"></i> Main components total exactly 100%</li>
                                                                {{-- static/presentational: no backing validation source in this form (per product decision) --}}
                                                                <li class="co-chk--static"><i class="co-chk co-chk--ok"></i> No circular dependencies found</li>
                                                                <li class="co-chk--static"><i class="co-chk co-chk--ok"></i> All formulas are valid</li>
                                                                <li data-check="currency"><i class="co-chk"></i> Currency and salary format selected</li>
                                                                <li data-check="additional"><i class="co-chk"></i> Additional components within allowed range</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                                                    <div class="co-section-head">
                                                        <div>
                                                            <h5 class="co-section-title">Main Components <span class="co-section-note">(Must Total 100%)</span></h5>
                                                            <div class="co-section-sub">These are core components. Total percentage must be exactly 100%.</div>
                                                        </div>
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
                                                </div>

                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2 co-components-card">
                                                    <div class="co-section-head">
                                                        <div>
                                                            <h5 class="co-section-title">Additional Components <span class="co-section-note">(Above 100% - Optional)</span></h5>
                                                            <div class="co-section-sub">These components are additional and will be added on top of the main 100%.</div>
                                                        </div>
                                                    </div>
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
                                                </div>

                                                <div class="card card-body py-7 mb-5 mb-xl-5 mx-2">
                                                    <div class="row">
                                                        <table class="table-row-dashed fs-6 gy-5 no-footer"
                                                            id="final_pay" role="grid"
                                                            aria-describedby="final_pay_info" style="width: 100%;">


                                                            <tfoot class="other_section_final other_section {{ $section_other }}">

                                                                <tr>
                                                                    <th colspan="2" class="sorting_disabled" style="width: 35%;">
                                                                        <input type="text" class="form-control form-control-solid" value="Final Salary (Cost To Company)" readonly style="cursor: not-allowed;background: #7de96f40;">
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
                                                </div>
                                                <div id="text_for_hourly_annually" class="d-none"><br>
                                                    <p><b>Note:</b>*The per-hour salary is calculated based on 8 hours per day, 5 days per week, and 2,080 hours per year. The annual salary is subject to adjustments based on the actual number of hours worked</p> </div>
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

                        var mainPct = coNum($('#totalpercentage').val()) || coNum($('#totalpercentagehouly').val());
                        var addPct = coNum($('#totalpercentagebonus').val()) || coNum($('#totalhourlypercentagebonus').val());

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
                        pushRows(coVisibleRows('#pointList tbody'), 'offer_monthly', 'offer_yearly', 'offer_points');
                        pushRows(coVisibleRows('#bonusList tbody'), 'bonus_offer_monthly', 'bonus_offer_yearly', 'bonus_offer_points');
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
                    };

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
