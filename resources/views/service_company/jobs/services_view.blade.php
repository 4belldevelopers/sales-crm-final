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
/* Intent: make the asynchronously loaded commercial drawer use the account page's enterprise card/subcard system without changing field names or save hooks. */
.commercial-editor-v2{color:#172554;background:#eef2f7!important}
.commercial-editor-v2-header{position:sticky;top:0;z-index:5;min-height:66px!important;padding:11px 18px!important;box-shadow:0 1px 0 rgba(15,23,42,.03)}
.commercial-editor-heading{display:flex;align-items:center;min-width:0;gap:11px}
.commercial-editor-heading-icon,.commercial-section-icon{display:flex;flex:0 0 auto;align-items:center;justify-content:center;color:#2563eb;background:#eff6ff;border-radius:9px}
.commercial-editor-heading-icon{width:38px;height:38px}.commercial-section-icon{width:32px;height:32px;border-radius:8px}
.commercial-editor-heading-icon svg{width:20px;height:20px}.commercial-section-icon svg{width:17px;height:17px}
.commercial-editor-heading-icon svg,.commercial-section-icon svg{fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.commercial-editor-v2-header h3{display:flex;align-items:center;flex-wrap:wrap;gap:7px;margin:0!important;font-size:15px!important}
.commercial-editor-v2-header h3 span{margin:0!important;padding:3px 8px!important}
.commercial-editor-heading p{margin:3px 0 0;color:#64748b;font-size:10.5px;font-weight:500;line-height:1.35}
.commercial-editor-v2-close{display:flex!important;flex:0 0 32px;align-items:center;justify-content:center;padding:0!important;transition:border-color .15s,background-color .15s,color .15s}
.commercial-editor-v2-close:hover{color:#dc2626!important;background:#fef2f2!important;border-color:#fecaca!important}
.commercial-editor-v2-close:focus-visible{outline:0;box-shadow:0 0 0 3px rgba(37,99,235,.18)!important}
.commercial-editor-v2-scroll{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(300px,.85fr);align-items:start;gap:14px;padding:14px!important}
.commercial-editor-note{grid-column:1/-1;margin:0!important;padding:10px 13px 10px 39px;position:relative;background:#fff!important;border-color:#cbdcff!important;box-shadow:0 1px 3px rgba(15,23,42,.035)}
.commercial-editor-note:before{position:absolute;left:13px;top:10px;display:flex;width:18px;height:18px;align-items:center;justify-content:center;content:'i';color:#fff;background:#2563eb;border-radius:50%;font-size:10px;font-weight:600}
.commercial-editor-v2 .commercial-editor-v2-scroll>.commercial-rates-card,.commercial-editor-v2 .commercial-editor-v2-scroll>.commercial-countries-card{width:auto!important;float:none!important;margin:0!important;border:1px solid #d9e2ef!important;border-radius:12px!important;background:#fff!important;box-shadow:0 1px 3px rgba(15,23,42,.05),0 5px 14px rgba(15,23,42,.025)!important}
.commercial-editor-v2-scroll>.commercial-rates-card .card-body,.commercial-editor-v2-scroll>.commercial-countries-card .card-body{padding:13px!important}
.commercial-section-heading{display:flex;align-items:center;gap:10px;margin:0 0 11px;padding:0 0 11px;border-bottom:1px solid #e7edf5}
.commercial-section-heading h4{margin:0!important;color:#111b3d!important;font-size:13px!important;font-weight:600!important}
.commercial-section-heading p{margin:2px 0 0;color:#64748b;font-size:10px;font-weight:500;line-height:1.35}
.commercial-rates-card .form-group,.commercial-countries-card .form-group{margin:0!important}
.commercial-rates-card .rate-section:not(.d-none),.commercial-rates-card .rate-perecentage:not(.d-none){display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin:0!important}
.commercial-rates-card .rate-section>.d-flex.mb-5,.commercial-rates-card .rate-perecentage>.d-flex.mb-5{display:grid!important;grid-template-columns:104px minmax(0,1fr);align-items:center!important;min-width:0;gap:9px;margin:0!important;padding:9px!important;background:#fbfdff;border:1px solid #e3eaf3!important;border-radius:8px}
.commercial-rates-card .rate-section>.d-flex>.col-lg-3,.commercial-rates-card .rate-section>.d-flex>.col-lg-9,.commercial-rates-card .rate-perecentage>.d-flex>.col-lg-3,.commercial-rates-card .rate-perecentage>.d-flex>.col-lg-9{width:auto!important;max-width:none!important;padding:0!important}
.commercial-rates-card .row{margin-right:0!important;margin-left:0!important}.commercial-rates-card .row>*{min-width:0}
.commercial-rates-card .rate-section .col-lg-12,.commercial-rates-card .rate-perecentage .col-lg-12{padding:0!important}
.commercial-rates-card .rate-perecentage .col-lg-8{width:100%!important;max-width:100%!important;padding:0!important}
.commercial-rates-card .rate-section>.d-flex label,.commercial-rates-card .rate-perecentage>.d-flex label{margin:0!important;color:#26385e!important;font-size:10.5px!important;font-weight:600!important}
.commercial-rates-card .form-control,.commercial-rates-card .form-select{height:32px!important;min-height:32px!important;border-color:#cfd9e8!important;background:#fff!important;font-variant-numeric:tabular-nums}
.commercial-rates-card .rate-perecentage .col-lg-8>span{right:8px!important;top:50%!important;display:flex;transform:translateY(-50%)}
.commercial-rates-card .rate-perecentage .col-lg-8>span svg{width:17px;height:17px}
.commercial-country-grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;margin:0!important}
.commercial-countries-card .commercial-country-grid{max-height:calc(100vh - 255px);padding-right:4px;overflow:auto;scrollbar-width:thin;scrollbar-color:#cbd5e1 transparent}
.commercial-country-option{display:flex;align-items:center;min-width:0;min-height:36px;padding:7px 8px!important;background:#fbfdff;border:1px solid #e3eaf3;border-radius:7px}
.commercial-editor-v2-scroll .commercial-country-option .countries{flex:0 0 16px;margin:0 7px 0 0!important}
.commercial-editor-v2-scroll .commercial-country-option label{min-width:0;margin:0!important;overflow:hidden;color:#34476e!important;text-overflow:ellipsis;white-space:nowrap}
.commercial-country-option:has(.countries:checked){background:#eff6ff;border-color:#b9d2ff}
.commercial-editor-actions{position:sticky;bottom:0;z-index:4;display:flex;align-items:center;justify-content:flex-end;padding:11px 16px!important;background:rgba(255,255,255,.96);border-top:1px solid #dbe3ef;backdrop-filter:blur(8px)}
.commercial-editor-actions .text-center{padding:0!important}.commercial-editor-v2 .copy_current_selected_applicant{min-width:142px!important;padding:9px 16px!important}
@media(max-width:1199px){.commercial-editor-v2-scroll{grid-template-columns:1fr}.commercial-editor-note{grid-column:auto}.commercial-country-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:767px){.commercial-editor-v2-header{padding:10px 12px!important}.commercial-editor-heading-icon{display:none}.commercial-editor-v2-header h3{font-size:13px!important}.commercial-editor-heading p{font-size:9.5px}.commercial-editor-v2-scroll{padding:10px!important}.commercial-rates-card .rate-section:not(.d-none),.commercial-rates-card .rate-perecentage:not(.d-none){grid-template-columns:1fr}.commercial-country-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.commercial-editor-actions{padding:9px 10px!important}.commercial-editor-v2 .copy_current_selected_applicant{width:100%;min-width:0!important}}
@media(prefers-reduced-motion:reduce){.commercial-editor-v2-close{transition:none}}
</style>

<style>
/* Commercial editor visual layer: existing pricing/country fields and save contract are intentionally preserved. */
#kt_activitiesJob:has(#commercial-editor-v2)> .card > #kt_activities_header{display:none!important}#kt_activitiesJob:has(#commercial-editor-v2)> .card > #kt_activitiesJob_body{padding:0!important}.commercial-editor-v2{min-height:100%;font-family:'Poppins',sans-serif!important;background:#f8fafc!important}.commercial-editor-v2 *{font-family:'Poppins',sans-serif!important}.commercial-editor-v2 .card{border:0!important;box-shadow:none!important;background:transparent!important}.commercial-editor-v2-header{display:flex;align-items:center;justify-content:space-between;min-height:74px!important;padding:15px 22px!important;background:#fff!important;border-bottom:1px solid #e7edf5!important}.commercial-editor-v2-header h3{margin:0!important;color:#111b3d!important;font-size:16px!important;font-weight:600!important;line-height:1.3}.commercial-editor-v2-header h3 span{display:inline-flex;margin-left:8px;padding:3px 8px;color:#08a851!important;background:#e8f8ed;border-radius:5px;font-size:10px!important;font-weight:600!important;vertical-align:2px}.commercial-editor-v2-close{width:32px!important;height:32px!important;color:#52638b!important;background:#fff!important;border:1px solid #e2e8f3!important;border-radius:7px!important}.commercial-editor-v2-scroll{padding:18px!important;background:#f8fafc!important}.commercial-editor-note{margin:0 2px 14px;padding:10px 13px;color:#40547e;background:#f4f8ff;border:1px solid #d8e4ff;border-radius:8px;font-size:11px;font-weight:500;line-height:1.45}.commercial-editor-note b{color:#173af5;font-weight:600}.commercial-editor-v2-scroll>.card{margin:0 0 14px!important;border:1px solid #e2e8f3!important;border-radius:11px!important;background:#fff!important;box-shadow:0 1px 3px rgba(15,23,66,.025)!important}.commercial-editor-v2-scroll>.card .card-body{padding:18px!important}.commercial-editor-v2-scroll h4{color:#111b3d!important;font-size:13px!important;font-weight:600!important;line-height:1.35}.commercial-editor-v2-scroll .form-label{margin-bottom:4px;color:#52638b!important;font-size:10.5px!important;font-weight:500!important;line-height:1.35}.commercial-editor-v2-scroll .form-control,.commercial-editor-v2-scroll .form-select{height:34px!important;min-height:34px!important;padding:6px 10px!important;color:#172554!important;background:#fff!important;border:1px solid #dce4f0!important;border-radius:6px!important;font-size:11px!important;font-weight:500!important;box-shadow:none!important}.commercial-editor-v2-scroll .form-control:focus,.commercial-editor-v2-scroll .form-select:focus{border-color:#2563eb!important;box-shadow:0 0 0 3px rgba(37,99,235,.10)!important}.commercial-editor-v2-scroll .rate-perecentage .d-flex.mb-5,.commercial-editor-v2-scroll .rate-section .d-flex.mb-5{margin-bottom:6px!important;padding:8px 0;border-bottom:1px solid #f0f3f8}.commercial-editor-v2-scroll .rate-perecentage .d-flex.mb-5:last-child,.commercial-editor-v2-scroll .rate-section .d-flex.mb-5:last-child{border-bottom:0}.commercial-editor-v2-scroll .rate-perecentage label,.commercial-editor-v2-scroll .rate-section label{font-size:10.5px!important;font-weight:600!important;color:#283a61!important}.commercial-editor-v2-scroll .countries{width:16px!important;height:16px!important;margin:0 7px 0 0!important;accent-color:#2563eb}.commercial-editor-v2-scroll .countries+label{display:inline-block;min-width:90px;margin:0 0 9px!important;color:#40547e!important;font-size:10.5px!important;font-weight:500!important;vertical-align:2px}.commercial-editor-v2-scroll .countries:checked+label{color:#173af5!important;font-weight:600!important}.commercial-editor-v2 .copy_current_selected_applicant{min-width:150px!important;padding:10px 16px!important;color:#fff!important;background:#2563eb!important;border-color:#2563eb!important;border-radius:7px!important;font-size:11px!important;font-weight:600!important;box-shadow:0 4px 12px rgba(37,99,235,.2)!important}.commercial-editor-v2 .copy_current_selected_applicant:hover{background:#1d4ed8!important;border-color:#1d4ed8!important}@media(min-width:992px){.commercial-editor-v2-scroll>.card:first-of-type{width:46%;float:left;margin-right:2%!important}.commercial-editor-v2-scroll>.card:last-of-type{width:52%;float:left}.commercial-editor-v2-scroll::after{content:'';display:block;clear:both}}@media(max-width:991px){.commercial-editor-v2-scroll{padding:12px!important}}
</style>

<div id="commercial-editor-v2" class="commercial-editor-v2">
    <div class="card shadow-none rounded-0 w-100">
        <div class="commercial-editor-v2-header">
            <div class="commercial-editor-heading">
                <span class="commercial-editor-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4h8v2M3 11h18M10 11v2h4v-2"/></svg></span>
                <div>
                    <h3>{{ ($sectionCheck==1) ? 'Contract' : 'Fulltime' }} Hiring Services <span>Commercial for your client</span></h3>
                    <p>Configure experience-based rates and service availability.</p>
                </div>
            </div>
            <input class="main_status" type="hidden" value="{{ (@$commericials[0]['status']) ? @$commericials[0]['status'] : 0 }}">
            <div class="card-toolbar">
                <button type="button" class="btn commercial-editor-v2-close" aria-label="Close commercial settings" title="Close"
                    onclick="$('#kt_activitiesJob > .card > #kt_activities_header #kt_activitiesJob_close').trigger('click');"> <span class="svg-icon svg-icon-1">
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
        <div class="card-body position-relative px-0 py-0">
            <div class="card-body position-relative px-0 py-0">
                <input type="hidden" name="hid_job_id" id="hid_job_id" value="2">
                <div class="commercial-editor-v2-scroll">
                    <div class="commercial-editor-note"><b>Commercial setup:</b> Configure the placement fee and select the countries where this service is available. Existing rates and selections are retained until you save.</div>
                    <div class="card commercial-rates-card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row fv-plugins-icon-container mb-4">
                                <div class="commercial-section-heading">
                                    <span class="commercial-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v11"/><path d="M8 6V4h8v2M8 11h8M8 15h5"/></svg></span>
                                    <div>
                                        <h4>Experience-based rates</h4>
                                        <p>{{ ($sectionCheck==2) ? 'One-time service fee calculated from the candidate CTC.' : 'Set the billing rate by candidate experience.' }}</p>
                                    </div>
                                </div>
                                <input type="hidden" class="rateTypeClass" value="{{ $sectionCheck }}" name="rateType">
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
                    <div class="card commercial-countries-card mb-5 mb-xl-5 mx-2">
                        <div class="card-body pt-5 pb-0 px-5">
                            <div class="form-group row fv-row mb-0 fv-plugins-icon-container mb-4">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <div class="commercial-section-heading commercial-country-heading">
                                        <span class="commercial-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg></span>
                                        <div><h4>Service countries</h4><p>Select every country where this commercial applies.</p></div>
                                    </div>
                                    <div class="d-flex flex-column mb-6 fv-row">
                                        <div class="row commercial-country-grid">
                                        @if(@$countries)
                                            @foreach($countries as $countri_val)
                                            <div class="col-lg-2 commercial-country-option" title="{{ $countri_val->country_name }}">
                                                <input class="form-check-input h-20px w-20px mr-2 countries" type="checkbox" id="commercial_country_{{ $sectionCheck }}_{{ $countri_val->id }}"
                                                    name="countries" value="{{ $countri_val->id }}" {{ (@$commericials) ? in_array($countri_val->id, @$commericials[0]['commericial_countries'] ?? []) ? 'checked' : '' : '' }}>
                                                <label class="form-label" for="commercial_country_{{ $sectionCheck }}_{{ $countri_val->id }}">{{ $countri_val->country_name }}</label>
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
            <div class="commercial-editor-actions">
                <div class="text-center pt-15" style="text-align: center !important;">
                    <input class="form-input updateId" type="hidden" value=""/>
                    <input class="form-input commType" type="hidden" value="{{ $sectionCheck }}"/>
                  <button type="button" id="{{ ($sectionCheck==2) ? 'form-submit' : 'form-submit'}}" class="btn btn-primary copy_current_selected_applicant"> 
                      <span class="indicator-label">Save Changes</span>
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
