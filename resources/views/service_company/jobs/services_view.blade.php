<style>
    .text-justify {
        text-align: justify !important;
    }

    .symbol1>img,
    .symbol1 .symbol-label1 {
        width: 32px !important;
        height: 32px !important;
    }

    .table> :not(caption)>*>* {
        padding: 0rem 0.4rem;
    }

    #kt_activitiesJob_close svg {
            color: red;
    }

    /*.card .card-header{min-height: 80px;}*/
</style>

<style>
/* Commercial editor visual layer: existing pricing/country fields and save contract are intentionally preserved. */
#kt_activitiesJob{width:min(1260px,calc(100vw - 32px))!important;font-family:'Poppins',sans-serif!important;background:#f8fafc!important}#kt_activitiesJob *{font-family:'Poppins',sans-serif!important}#kt_activitiesJob .card{border:0!important;box-shadow:none!important;background:transparent!important}#kt_activities_header{min-height:74px!important;padding:15px 22px!important;background:#fff!important;border-bottom:1px solid #e7edf5!important}#kt_activities_header h3{margin:0!important;color:#111b3d!important;font-size:16px!important;font-weight:600!important;line-height:1.3}#kt_activities_header h3 span{display:inline-flex;margin-left:8px;padding:3px 8px;color:#08a851!important;background:#e8f8ed;border-radius:5px;font-size:10px!important;font-weight:600!important;vertical-align:2px}#kt_activitiesJob_close{width:32px!important;height:32px!important;color:#52638b!important;background:#fff!important;border:1px solid #e2e8f3!important;border-radius:7px!important}#kt_activities_scroll{padding:18px!important;background:#f8fafc!important}.commercial-editor-note{margin:0 2px 14px;padding:10px 13px;color:#40547e;background:#f4f8ff;border:1px solid #d8e4ff;border-radius:8px;font-size:11px;font-weight:500;line-height:1.45}.commercial-editor-note b{color:#173af5;font-weight:600}#kt_activities_scroll>.card{margin:0 0 14px!important;border:1px solid #e2e8f3!important;border-radius:11px!important;background:#fff!important;box-shadow:0 1px 3px rgba(15,23,66,.025)!important}#kt_activities_scroll>.card .card-body{padding:18px!important}#kt_activities_scroll h4{color:#111b3d!important;font-size:13px!important;font-weight:600!important;line-height:1.35}#kt_activities_scroll .form-label{margin-bottom:4px;color:#52638b!important;font-size:10.5px!important;font-weight:500!important;line-height:1.35}#kt_activities_scroll .form-control,#kt_activities_scroll .form-select{height:34px!important;min-height:34px!important;padding:6px 10px!important;color:#172554!important;background:#fff!important;border:1px solid #dce4f0!important;border-radius:6px!important;font-size:11px!important;font-weight:500!important;box-shadow:none!important}#kt_activities_scroll .form-control:focus,#kt_activities_scroll .form-select:focus{border-color:#2563eb!important;box-shadow:0 0 0 3px rgba(37,99,235,.10)!important}#kt_activities_scroll .rate-perecentage .d-flex.mb-5,#kt_activities_scroll .rate-section .d-flex.mb-5{margin-bottom:6px!important;padding:8px 0;border-bottom:1px solid #f0f3f8}#kt_activities_scroll .rate-perecentage .d-flex.mb-5:last-child,#kt_activities_scroll .rate-section .d-flex.mb-5:last-child{border-bottom:0}#kt_activities_scroll .rate-perecentage label,#kt_activities_scroll .rate-section label{font-size:10.5px!important;font-weight:600!important;color:#283a61!important}#kt_activities_scroll .countries{width:16px!important;height:16px!important;margin:0 7px 0 0!important;accent-color:#2563eb}#kt_activities_scroll .countries+label{display:inline-block;min-width:90px;margin:0 0 9px!important;color:#40547e!important;font-size:10.5px!important;font-weight:500!important;vertical-align:2px}#kt_activities_scroll .countries:checked+label{color:#173af5!important;font-weight:600!important}.copy_current_selected_applicant{min-width:150px!important;padding:10px 16px!important;color:#fff!important;background:#2563eb!important;border-color:#2563eb!important;border-radius:7px!important;font-size:11px!important;font-weight:600!important;box-shadow:0 4px 12px rgba(37,99,235,.2)!important}.copy_current_selected_applicant:hover{background:#1d4ed8!important;border-color:#1d4ed8!important}@media(min-width:992px){#kt_activities_scroll>.card:first-of-type{width:46%;float:left;margin-right:2%!important}#kt_activities_scroll>.card:last-of-type{width:52%;float:left}#kt_activities_scroll::after{content:'';display:block;clear:both}}@media(max-width:991px){#kt_activitiesJob{width:100%!important}#kt_activities_scroll{padding:12px!important}}
</style>

<div id="kt_activitiesJob" class="bg-body drawer drawer-end drawer-on" data-kt-drawer="true"
    data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle"
    data-kt-drawer-close="#kt_activitiesJob_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header px-5 py-0" id="kt_activities_header">
            <div class="d-flex align-items-center pt-6" style="width: 95%;">
                <div class="row mb-5" style="width: 100%;">
                    <div class="col-md-11">
                        <div class="d-flex flex-column text-dark-75">
                            <!--<span class="fw-bold fs-4">Contact Hiring</span>-->
                            <h3 class="fw-bolder mb-3 mt-4">{{ ($sectionCheck==1) ? 'Contract' : 'Fulltime' }} Hiring Services    <span class="fw-bolder mb-8" style="font-size: 14px; color: gray;">(Commericial For Your Client)</span></h3>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input main_status" type="hidden" value="{{ (@$commericials[0]['status']) ? @$commericials[0]['status'] : 0 }}"  />
                                </label>
                            </a>
                            @if(@$commericials[0]['status'])
                            {{-- <span class="text-muted fw-bold d-block fs-7">
                                <span class="active-bg status_stage text-hover-success ml-1 mr-2">Active</span>
                            </span> --}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                    id="kt_activitiesJob_close"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
        <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body">
            <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
                <input type="hidden" name="hid_job_id" id="hid_job_id" value="2">
                <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
                    data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
                    data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
                    data-kt-scroll-offset="5px">
                    <div class="commercial-editor-note"><b>Commercial setup:</b> Configure the placement fee and select the countries where this service is available. Existing rates and selections are retained until you save.</div>
                    <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row fv-plugins-icon-container mb-4">
                               
                                <div class="d-flex mb-5" style="align-items: center;">
                                    <div class="col-lg-3">
                                        <h4 class="fw-bolder mb-6 mt-2">Experience</h4>
                                    </div>
                                    <div class="col-lg-9">
                                      <div class="row">
                                        <div class="col-lg-2 px-1 d-flex" style="width: 4.666667%;">
                                           <h4 class="fw-bolder mb-6 mt-2">Rates</h4>
                                        </div>
                                        <div class="col-lg-10 px-1 d-flex" style="align-items: center;margin-top: -9px;">
                                            <div class="radio-inline px-10 {{ ($sectionCheck==2) ? '' : 'd-none' }}">
                                                 <label
                                                    class="radio radio-success radio-success text-dark fw-bolder text-hover-primary fs-6">
                                                    <input type="hidden" class="rateTypeClass" value="{{ $sectionCheck }}" name="rateType"  {{ ($sectionCheck==2) ? 'checked' : '' }}/>
                                                    <span></span>
                                                    <span class="fw-bold text-muted d-block fs-7"> (One time service fee from the CTC of the candidate)
                                                        {{-- Perecentage --}}
                                                        {{-- <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                            </g>
                                                        </svg> --}}
                                                    </span>
                                                </label> 
                                            </div>
                                            <div class="radio-inline px-10 {{ ($sectionCheck==1) ? '' : 'd-none' }}">
                                                 <label
                                                    class="radio radio-success radio-success text-dark fw-bolder text-hover-primary fs-6">
                                                    <input type="hidden" class="rateTypeClass" value="{{ $sectionCheck }}" name="rateType" {{ ($sectionCheck==1) ? 'checked' : '' }}/>
                                                    <span></span>
                                                    {{-- Fixed Price --}}
                                                </label> 
                                            </div>
                                        </div>
                                      </div>

                                    </div>
                                </div>
                                <div class="row rate-section {{ ($sectionCheck!=1) ? 'd-none' : '' }}"> 
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label">0 Year(Fresher)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][0]['rate']  : @$default_rates->rates_fixed}}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label">1 Year</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            {{-- {{ dd(@$commericials[0]) }} --}}
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][1]['rate'] : @$default_rates->rates_fixed}}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                        
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 2 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][2]['rate']  : @$default_rates->rates_fixed}}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 3 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][3]['rate'] : @$default_rates->rates_fixed}}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 4 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][4]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 5 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][5]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 6 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][6]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 7 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][7]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 8 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][8]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 9 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][9]['rate'] : @$default_rates->rates_fixed }}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 10+ Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="salary_currency"
                                                                id="salary_currency">
                                                                <option value="0" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 0 ? 'selected' : '') : '' }}>INR</option>
                                                                <option value="1" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 1 ? 'selected' : '') : '' }}>EUR</option>
                                                                <option value="2" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 2 ? 'selected' : '') : '' }}>JPY</option>
                                                                <option value="3" {{ isset($commericials[0]['salary_currency']) ? ($commericials[0]['salary_currency'] == 3 ? 'selected' : '') : '' }}>GBP</option>
                                                                <option value="4" {{ !isset($commericials[0]['salary_currency']) || $commericials[0]['salary_currency'] === null ? 'selected' : ($commericials[0]['salary_currency'] == 4 ? 'selected' : '') }}>USD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-4 px-0">
                                                            <input type="number"
                                                                class="form-control form-control-lg rate_val form-control-solid"
                                                                name="rate[]" placeholder="Rates..."
                                                                value="{{ (@$commericials[0]['rateType']==1) ? @$commericials[0]['commericial_details'][10]['rate']  : @$default_rates->rates_fixed}}">
                                                        </div>
                                                        <div class="col-lg-2 px-1">
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Annual Salary" name="sal_mode"
                                                                id="sal_mode">
                                                                <option value="0" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 0 ? 'selected' : '') : '' }}>Annual</option>
                                                                <option value="1" {{ isset($commericials[0]['sal_mode']) ? ($commericials[0]['sal_mode'] == 1 ? 'selected' : '') : '' }}>Monthly</option>
                                                                <option value="2" {{ !isset($commericials[0]['sal_mode']) || $commericials[0]['sal_mode'] === null ? 'selected' : ($commericials[0]['sal_mode'] == 2 ? 'selected' : '') }}>Hourly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row rate-perecentage {{ ($sectionCheck!=2) ? 'd-none' : '' }}"> 
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label">0 Year(Fresher)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][0]['rate'] : @$default_rates->rates_perecentage}}">

                                                                
                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label">1 Year</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][1]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 2 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][2]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 3 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][3]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 4 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][4]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 5 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][5]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 6 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][6]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 7 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][7]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 8 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][8]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 9 Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][9]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-5" style="align-items: center;">
                                        <div class="col-lg-3">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="form-label"> 10+ Years</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-12 px-1" style="align-items: center;">
                                                    <div class="row">
                                                        <div class="col-lg-8 px-0" style="position: relative;">
                                                            <input type="number"
                                                                class="form-control perecentage_val form-control-lg form-control-solid"
                                                                name="perecentage[]" placeholder="Perecentage"
                                                                value="{{ (@$commericials[0]['rateType']==2) ? @$commericials[0]['commericial_details'][10]['rate'] : @$default_rates->rates_perecentage }}">

                                                                <span style="position: absolute; right: 0; top: 10px;"> 
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="d-flex mb-3 fv-row">
                                        <h4 class="fw-bolder mb-3 col-lg-12">Industries</h4>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row">    
                                            @if(@$industries)  
                                                @foreach($industries as $ind_val)                                      
                                                    <div class="col-lg-3">
                                                        <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                            name="" value="{{ $ind_val->ind_id }}">
                                                        <label class="form-label">{{ $ind_val->ind_name }}</label>
                                                    </div>
                                                 @endforeach   
                                            @endif
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                     <!--start hiring type section-->
                    {{-- <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="d-flex mb-3 fv-row">
                                        <h4 class="fw-bolder mb-3 col-lg-12">Hiring Type</h4>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row">
                                            <div class="col-lg-2">                                                
                                                <input class="form-check-input h-20px w-20px mr-2" type="radio"
                                                    name="hiring_type" value="1">
                                                <label class="form-label">Technical</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="radio"
                                                    name="hiring_type" value="2">
                                                <label class="form-label">Non-Technical</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-check-input h-20px w-20px mr-2" type="radio"
                                                    name="hiring_type" value="3">
                                                <label class="form-label">Both</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                     <!--end hiring type section-->
                     <!--start skill section-->
                    {{-- <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="d-flex mb-3 fv-row">
                                        <h4 class="fw-bolder mb-3 col-lg-12">Technical</h4>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Software</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">IT</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Bootstrap</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">HTML</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">CSS</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Java</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Software</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">IT</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Bootstrap</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">HTML</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">CSS</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Java</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="d-flex mb-3 fv-row">
                                        <h4 class="fw-bolder mb-3 col-lg-12">Non-Technical</h4>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Sales</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Reception</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Marketing</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Designing</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Printing</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Press Media</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Hardware</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Installing</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Office Guard</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Field</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Staff</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="" value="">
                                                <label class="form-label">Hospital</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!--end skill section-->
                    <!--start countries section-->
                    <div class="card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="d-flex mb-3 fv-row">
                                        <h4 class="fw-bolder mb-3 col-lg-12">Select Countries for Your Services</h4>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row">
                                        @if(@$countries)
                                            @foreach($countries as $countri_val)
                                            <div class="col-lg-2">
                                                <input class="form-check-input h-20px w-20px mr-2 countries" type="checkbox"
                                                    name="countries" value="{{ $countri_val->id }}" {{ (@$commericials) ? in_array($countri_val->id, @$commericials[0]['commericial_countries'] ?? []) ? 'checked' : '' : '' }}>
                                                <label class="form-label">{{ substr($countri_val->country_name,0,18) }}</label>
                                            </div>
                                            @endforeach
                                        @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!--end countries section-->
                </div>
            </div>
            <div class="">
                <div class="text-center pt-15" style="text-align: center !important;">
                    <input class="form-input updateId" type="hidden" value=""/>
                    <input class="form-input commType" type="hidden" value="{{ $sectionCheck }}"/>
                  <button type="button" id="{{ ($sectionCheck==2) ? 'form-submit' : 'form-submit'}}" class="btn btn-primary copy_current_selected_applicant"> 
                      <span class="indicator-label">Update</span> 
                      <span class="indicator-progress">
                          Please wait...
                      </span> 
                  </button>
                            
                </div>
              </div> <br>   
        </div>
    </div>
</div>



<script src="{{ asset('demo/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
<script src="{{ asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js') }}"></script>
<script>
    function opensidebar() {
        $('#kt_activitiesClient_toggle').trigger('click');
    }
</script>
<!--Tinymce Editor End-->
<!--begin::Page Scripts-->
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/add-requisition.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>

<script>
    var inputloc = document.querySelector("#kt_tagify_work_location");
    var cities = ['d', 't'];

    // Initialize Tagify script on the above inputs
    var tagifylocation = new Tagify(inputloc, {
        whitelist: cities,
        maxTags: 10,
        dropdown: {
            maxItems: 20, // <- mixumum allowed rendered suggestions
            classname: "tagify__inline__suggestions", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0, // <- show suggestions on focus
            closeOnSelect: false // <- do not hide the suggestions dropdown once an item has been selected
        }
    });

    // Prevent creating tags on blur or enter key
    tagifylocation.on('input', function(e) {
        tagify.dropdown.show(); // show the dropdown immediately
    });

    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
        tagify.addTags([e.detail.data.value]);
    });
</script>
