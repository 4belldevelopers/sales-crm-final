@php
    // Intent: present the existing tenant commercial payload in the requested editor without inventing persistence fields.
    $commercial = @$commericials[0] ?? [];
    $isFulltime = (int) $sectionCheck === 2;
    $rateType = (int) ($commercial['rateType'] ?? $sectionCheck);
    $commercialDetails = collect($commercial['commericial_details'] ?? [])->keyBy('exp');
    $selectedCountryIds = array_map('intval', $commercial['commericial_countries'] ?? []);
    $fallbackDefaultRate = $isFulltime ? (@$default_rates->rates_perecentage ?? 8.33) : (@$default_rates->rates_fixed ?? 0);
    // Intent: preserve a usable dynamic range payload when an older AJAX response did not include the controller value.
    $experienceBandRows = collect($commercialExperienceBands ?? []);
    if ($experienceBandRows->isEmpty()) {
        $experienceBandRows = \Illuminate\Support\Facades\DB::table('commercial_experience_bands')
            ->select(['id', 'code', 'label', 'min_experience', 'max_experience', 'sort_order'])
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
    }
    $firstExperienceIndex = (int) data_get($experienceBandRows->sortBy('sort_order')->first(), 'min_experience', 0);
    $defaultRate = data_get($commercialDetails->get($firstExperienceIndex), 'rate', $fallbackDefaultRate);
    $isFreeModel = $commercialDetails->isNotEmpty() && $commercialDetails->every(function ($detail) {
        return (float) data_get($detail, 'rate', 0) === 0.0;
    });
    $selectedCommercialModel = $isFreeModel ? 0 : $rateType;
    // Intent: make range labels and boundaries data-driven while retaining the existing numeric exp keys used by commercial details.
    $experienceRanges = $experienceBandRows
        ->map(function ($band) {
            $minimum = (int) $band->min_experience;
            $maximum = (int) $band->max_experience;

            return [
                'label' => (string) $band->label,
                'indexes' => range($minimum, $maximum),
            ];
        })
        ->values();
    $selectedSalaryMode = isset($commercial['sal_mode']) ? (int) $commercial['sal_mode'] : 0;
    $selectedCurrency = isset($commercial['salary_currency']) ? (int) $commercial['salary_currency'] : 4;
    $availableCurrencies = collect($currency ?? [])->filter(function ($currencyCode) {
        return trim((string) $currencyCode) !== '';
    });
    if ($availableCurrencies->isEmpty()) {
        $availableCurrencies = collect(\App\Helper\GeneralMethod::currency())->filter(function ($currencyCode) {
            return trim((string) $currencyCode) !== '';
        });
    }
    if (! $availableCurrencies->has($selectedCurrency) && $availableCurrencies->isNotEmpty()) {
        $selectedCurrency = (int) $availableCurrencies->keys()->first();
    }
    $selectedReplacementPeriod = (int) ($commercial['replacement_period_days'] ?? 90);
    $selectedReplacementType = (string) ($commercial['replacement_type'] ?? 'free_replacement');
    $selectedReplacementEligibility = (string) ($commercial['replacement_eligibility'] ?? 'early_exit');
    $selectedBillingFrequency = (string) ($commercial['billing_frequency'] ?? 'one_time');
    $selectedBillingTrigger = (string) ($commercial['billing_trigger'] ?? 'candidate_joined');
    $selectedPaymentTerms = (int) ($commercial['payment_terms_days'] ?? 30);
    $countryRegions = collect($countries ?? [])->groupBy(function ($country) {
        return trim((string) ($country->region ?? '')) ?: 'Other';
    });
@endphp

<style>
/* Intent: scope the reference-inspired commercial editor to the AJAX drawer and preserve all legacy JavaScript selectors. */
#kt_activitiesJob:has(#commercial-editor-v3)>.card>#kt_activities_header{display:none!important}#kt_activitiesJob:has(#commercial-editor-v3)>.card>#kt_activitiesJob_body{padding:0!important}
.commercial-editor-v3,.commercial-editor-v3 *{box-sizing:border-box;font-family:'Poppins',sans-serif!important}.commercial-editor-v3{min-height:100%;color:#111b3d;background:#f6f8fc;font-size:11px;font-weight:500}.cev3-header{position:sticky;top:0;z-index:8;display:flex;align-items:center;justify-content:space-between;gap:14px;min-height:72px;padding:12px 18px;background:#fff;border-bottom:1px solid #e3e9f2}.cev3-heading{display:flex;align-items:center;min-width:0;gap:11px}.cev3-heading-icon,.cev3-section-icon{display:flex;align-items:center;justify-content:center;flex:0 0 auto;color:#4f46e5;background:#eef2ff;border-radius:8px}.cev3-heading-icon{width:38px;height:38px}.cev3-section-icon{width:30px;height:30px}.cev3-heading-icon svg{width:20px;height:20px}.cev3-section-icon svg{width:16px;height:16px}.cev3-heading-icon svg,.cev3-section-icon svg,.cev3-btn svg,.cev3-search svg,.cev3-input-icon svg{fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.cev3-title-row{display:flex;align-items:center;flex-wrap:wrap;gap:8px}.cev3-title{margin:0!important;color:#0f1838!important;font-size:15px!important;font-weight:600!important;line-height:1.3}.cev3-active{display:inline-flex;align-items:center;min-height:21px;padding:2px 8px;color:#047857;background:#dff7ec;border-radius:6px;font-size:9.5px;font-weight:600}.cev3-subtitle{margin:3px 0 0;color:#657395;font-size:10.5px;font-weight:500}.cev3-header-actions{display:flex;align-items:center;gap:8px}.cev3-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:34px;padding:7px 11px;color:#23345d;background:#fff;border:1px solid #d6dfeb;border-radius:6px;font-size:10.5px;font-weight:600;line-height:1;white-space:nowrap;transition:border-color .15s,background .15s,color .15s}.cev3-btn:hover{color:#3730a3;background:#f8f9ff;border-color:#b9c3eb}.cev3-btn:focus-visible{outline:0;box-shadow:0 0 0 3px rgba(79,70,229,.15)}.cev3-btn svg{width:15px;height:15px}.cev3-close{width:34px;padding:0;color:#64748b}.cev3-close:hover{color:#dc2626;background:#fef2f2;border-color:#fecaca}.cev3-workspace{display:grid;grid-template-columns:minmax(340px,.76fr) minmax(560px,1.72fr);gap:14px;padding:14px}.cev3-panel{min-width:0;padding:14px;background:#fff;border:1px solid #dbe3ef;border-radius:10px;box-shadow:0 1px 3px rgba(15,23,42,.045)}.cev3-section+.cev3-section{margin-top:14px;padding-top:14px;border-top:1px solid #e8edf4}.cev3-section-head{display:flex;align-items:flex-start;gap:9px;margin-bottom:10px}.cev3-section-number{display:flex;align-items:center;justify-content:center;flex:0 0 23px;width:23px;height:23px;color:#4338ca;background:#eef2ff;border-radius:6px;font-size:10px;font-weight:600}.cev3-section-title{margin:0;color:#111b3d;font-size:12px;font-weight:600;line-height:1.35}.cev3-section-copy{margin:2px 0 0;color:#697797;font-size:9.5px;font-weight:500;line-height:1.4}.cev3-label{display:block;margin:0 0 5px;color:#26365c;font-size:10px;font-weight:600}.cev3-field{position:relative}.cev3-control{width:100%;height:33px;padding:6px 10px;color:#172554;background:#fff;border:1px solid #ced8e7;border-radius:6px;outline:0;font-size:10.5px;font-weight:500;line-height:1.2}.cev3-control:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}.cev3-control:disabled{color:#697797;background:#f8fafc;cursor:not-allowed}.cev3-control-icon{padding-left:38px}.cev3-input-icon{position:absolute;left:7px;top:50%;display:flex;width:25px;height:25px;align-items:center;justify-content:center;color:#059669;background:#e8f8ef;border-radius:5px;transform:translateY(-50%)}.cev3-input-icon svg{width:14px;height:14px}.cev3-help{margin:5px 0 0;color:#697797;font-size:9.5px;font-weight:500;line-height:1.4}.cev3-info{display:flex;align-items:flex-start;gap:8px;margin-top:8px;padding:9px 10px;color:#386252;background:#f0fbf6;border:1px solid #cceedd;border-radius:7px;font-size:9.5px;line-height:1.45}.cev3-info.blue{color:#405781;background:#f4f8ff;border-color:#d6e3ff}.cev3-info svg{flex:0 0 15px;width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8}.cev3-price-grid{display:grid;grid-template-columns:88px minmax(0,1fr);gap:8px}.cev3-price-grid .wide{grid-column:1/-1}.cev3-rate-wrap{position:relative}.cev3-rate-wrap .cev3-control{padding-right:29px;font-variant-numeric:tabular-nums}.cev3-rate-suffix{position:absolute;right:10px;top:50%;color:#657395;font-size:10px;font-weight:600;transform:translateY(-50%)}.cev3-rate-table{overflow:hidden;border:1px solid #e1e7f0;border-radius:7px}.cev3-rate-head,.cev3-rate-row{display:grid;grid-template-columns:minmax(125px,1fr) 100px 24px;align-items:center;gap:8px}.cev3-rate-head{min-height:27px;padding:5px 8px;color:#697797;background:#f8fafc;border-bottom:1px solid #e6ebf2;font-size:8.5px;font-weight:600}.cev3-rate-row{min-height:36px;padding:4px 8px;border-bottom:1px solid #edf1f6}.cev3-rate-row:last-child{border-bottom:0}.cev3-rate-name{overflow:hidden;color:#24345a;font-size:9.5px;font-weight:600;text-overflow:ellipsis;white-space:nowrap}.cev3-rate-row .cev3-control{height:27px;padding:4px 25px 4px 7px;font-size:9.5px}.cev3-trash{display:flex;width:24px;height:24px;align-items:center;justify-content:center;color:#94a3b8;background:transparent;border:0;border-radius:5px;cursor:not-allowed}.cev3-trash svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:1.7}.cev3-dashed{width:100%;min-height:31px;margin-top:8px;color:#4f46e5;background:#fff;border:1px dashed #b9c3e7;border-radius:6px;font-size:9.5px;font-weight:600}.cev3-dashed:disabled{color:#94a3b8;background:#fafbfc;cursor:not-allowed}.cev3-country-toolbar{display:grid;grid-template-columns:minmax(180px,1fr) auto;gap:10px;margin-bottom:9px}.cev3-search{position:relative}.cev3-search svg{position:absolute;left:10px;top:50%;width:14px;height:14px;color:#71809e;transform:translateY(-50%)}.cev3-search .cev3-control{padding-left:31px}.cev3-country-actions{display:flex;gap:6px}.cev3-country-actions .cev3-btn{min-height:31px;padding:6px 9px;font-size:9px}.cev3-selected{display:flex;min-height:33px;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:9px}.cev3-chip{display:inline-flex;align-items:center;gap:6px;min-height:28px;padding:5px 8px;color:#25365d;background:#f9fbff;border:1px solid #dbe3ef;border-radius:6px;font-size:9.5px;font-weight:600}.cev3-chip-flag{font-size:14px;line-height:1}.cev3-chip-remove{display:flex;width:16px;height:16px;align-items:center;justify-content:center;padding:0;color:#70809e;background:transparent;border:0;font-size:14px}.cev3-country-box{padding:10px;border:1px solid #e0e7f0;border-radius:8px}.cev3-country-box-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:9px}.cev3-country-box-title{display:flex;align-items:center;gap:7px;color:#25365d;font-size:9.5px;font-weight:600}.cev3-country-box-title span{display:flex;width:18px;height:18px;align-items:center;justify-content:center;color:#4f46e5;background:#eef2ff;border-radius:5px}.cev3-region-tabs{display:flex;gap:6px;margin-bottom:9px;overflow-x:auto;scrollbar-width:none}.cev3-region-tabs::-webkit-scrollbar{display:none}.cev3-region-tab{min-height:27px;padding:5px 9px;color:#516184;background:#f5f7fb;border:1px solid transparent;border-radius:5px;font-size:8.8px;font-weight:600;white-space:nowrap}.cev3-region-tab.active{color:#4f46e5;background:#fff;border-color:#aeb8ff}.cev3-country-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:3px 14px;max-height:183px;overflow:hidden}.cev3-country-grid.expanded{max-height:310px;overflow:auto}.cev3-country-option{display:flex;align-items:center;min-width:0;min-height:25px;gap:6px;padding:3px 5px;border-radius:5px}.cev3-country-option:hover{background:#f7f9fc}.cev3-country-option[hidden]{display:none!important}.cev3-country-option input{flex:0 0 14px;width:14px;height:14px;margin:0;accent-color:#4f46e5}.cev3-country-option label{display:flex;align-items:center;min-width:0;gap:6px;margin:0;color:#526184;font-size:9px;font-weight:500;cursor:pointer}.cev3-country-option label b{overflow:hidden;font-weight:500;text-overflow:ellipsis;white-space:nowrap}.cev3-show-more{display:flex;align-items:center;justify-content:center;gap:5px;margin:8px auto 0;padding:3px 8px;color:#4f46e5;background:transparent;border:0;font-size:9px;font-weight:600}.cev3-overrides{margin-top:13px}.cev3-overrides-table{overflow:hidden;border:1px solid #e1e7f0;border-radius:7px}.cev3-overrides-head{display:grid;grid-template-columns:1.2fr .85fr .85fr .7fr 45px;gap:8px;padding:7px 9px;color:#697797;background:#f8fafc;border-bottom:1px solid #e5eaf1;font-size:8.5px;font-weight:600}.cev3-empty{padding:15px;color:#7a879f;text-align:center;font-size:9.5px;font-weight:500}.cev3-unavailable{display:inline-flex;margin-left:6px;padding:2px 6px;color:#b45309;background:#fff7e6;border-radius:5px;font-size:8px;font-weight:600;vertical-align:1px}.cev3-footer{position:sticky;bottom:0;z-index:8;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 14px;background:rgba(255,255,255,.97);border-top:1px solid #dce4ef;backdrop-filter:blur(8px)}.cev3-footer-group{display:flex;align-items:center;gap:8px}.cev3-primary{min-width:140px;color:#fff!important;background:#4f24e8!important;border-color:#4f24e8!important;box-shadow:0 4px 10px rgba(79,36,232,.18)}.cev3-primary:hover{background:#421cca!important;border-color:#421cca!important}.cev3-soft{color:#4f46e5;background:#fff;border-color:#c8c8ff}.cev3-btn:disabled{color:#94a3b8;background:#f8fafc;border-color:#e2e8f0;cursor:not-allowed}.cev3-menu-wrap{position:relative}.cev3-menu{position:absolute;right:0;top:40px;z-index:15;display:none;width:175px;padding:5px;background:#fff;border:1px solid #dbe3ef;border-radius:7px;box-shadow:0 12px 28px rgba(15,23,42,.14)}.cev3-menu.open{display:block}.cev3-menu button{display:flex;width:100%;align-items:center;padding:8px 9px;color:#334466;background:#fff;border:0;border-radius:5px;font-size:9.5px;font-weight:500;text-align:left}.cev3-menu button:hover{background:#f4f6fb}.cev3-hidden{display:none!important}
.commercial-editor-v3{font-size:12px}.cev3-title{font-size:16.5px!important}.cev3-active{font-size:10.5px}.cev3-active.cev3-status-inactive{color:#b42318;background:#fee4e2}.cev3-subtitle,.cev3-btn,.cev3-control{font-size:11.5px}.cev3-section-title{font-size:13px}.cev3-section-copy,.cev3-help,.cev3-info,.cev3-chip,.cev3-country-box-title,.cev3-rate-name{font-size:10.5px}.cev3-label,.cev3-section-number,.cev3-rate-suffix{font-size:11px}.cev3-rate-head,.cev3-region-tab,.cev3-country-actions .cev3-btn{font-size:9.8px}.cev3-rate-row .cev3-control{font-size:10.5px}.cev3-country-panel,.cev3-country-section,.cev3-country-box{display:flex;min-height:0;flex-direction:column}.cev3-country-section,.cev3-country-box{flex:1}.cev3-country-grid{grid-template-columns:repeat(3,minmax(0,1fr));align-content:start;flex:1;gap:4px 16px;min-height:360px;max-height:none;padding-right:5px;overflow-y:auto;overflow-x:hidden;scrollbar-color:#cbd5e1 transparent;scrollbar-width:thin}.cev3-country-option{display:grid;grid-template-columns:16px minmax(0,1fr);min-height:29px;gap:7px;padding:4px 6px}.cev3-country-option label{display:grid;grid-template-columns:28px minmax(0,1fr);width:100%;gap:8px;font-size:10px}.cev3-country-code{display:inline-flex;width:28px;align-items:center;justify-content:flex-start;color:#344668;font-size:9px;font-weight:600;letter-spacing:.02em}.cev3-country-option label b{display:block}.cev3-menu-wrap{position:relative}.cev3-more-button{min-width:118px;justify-content:space-between;padding-inline:13px}.cev3-more-chevron{font-size:12px;transition:transform .16s ease}.cev3-more-button[aria-expanded="true"] .cev3-more-chevron{transform:rotate(180deg)}.cev3-menu{top:calc(100% + 8px);width:224px;padding:7px;border-color:#cfd8e6;border-radius:9px;box-shadow:0 18px 42px rgba(15,23,42,.17)}.cev3-menu button{align-items:center;gap:10px;min-height:38px;padding:9px 10px;border-radius:6px;font-size:10.5px;font-weight:500}.cev3-menu button+button{border-top:1px solid #eef2f6;border-radius:0}.cev3-menu-icon{display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;flex:0 0 24px;color:#4f46e5;background:#eef2ff;border-radius:6px;font-size:12px}.cev3-primary .indicator-progress{display:none!important;align-items:center;gap:8px}.cev3-primary.is-saving{opacity:1!important;cursor:wait}.cev3-primary.is-saving .indicator-label{display:none!important}.cev3-primary.is-saving .indicator-progress{display:inline-flex!important}.cev3-spinner{width:15px;height:15px;border:2px solid rgba(255,255,255,.45);border-top-color:#fff;border-radius:50%;animation:cev3-spin .7s linear infinite}@keyframes cev3-spin{to{transform:rotate(360deg)}}.cev3-hidden{display:none!important}
.cev3-workspace{grid-template-columns:minmax(0,1fr);gap:12px}.cev3-panel{padding:16px;border-color:#d7e0ec;box-shadow:0 2px 6px rgba(15,23,42,.045)}.cev3-section+.cev3-section{margin-top:0;padding-top:0;border-top:0}.cev3-default-pricing-card{display:grid;align-items:start;gap:14px;padding:14px;background:linear-gradient(135deg,#fbfcff,#f8f7ff);border:1px solid #e3e6f2;border-radius:8px}.cev3-model-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.cev3-model-option{position:relative;display:grid;grid-template-columns:38px minmax(0,1fr);align-items:start;gap:11px;min-height:112px;padding:16px;color:#27365a;background:#fff;border:1px solid #dce3ee;border-radius:9px;text-align:left;transition:border-color .16s,box-shadow .16s,background .16s}.cev3-model-option:hover{border-color:#aaa5ff;background:#fcfbff}.cev3-model-option.active{border-color:#6657ff;background:#fcfbff;box-shadow:0 0 0 1px rgba(102,87,255,.12)}.cev3-model-option b{display:block;margin:2px 0 7px;color:#111b3d;font-size:11.5px;font-weight:600}.cev3-model-option small{display:block;color:#617091;font-size:10px;font-weight:500;line-height:1.55}.cev3-model-option i{position:absolute;left:18px;bottom:13px;width:16px;height:16px;border:1.5px solid #cdd5e3;border-radius:50%}.cev3-model-option.active i{border:5px solid #5b45f5}.cev3-model-icon{display:flex;width:34px;height:34px;align-items:center;justify-content:center;color:#4f46e5;background:#eff0ff;border-radius:8px;font-size:15px;font-weight:600}.cev3-default-pricing-card{grid-template-columns:repeat(3,minmax(0,1fr))}.cev3-range-table{overflow:hidden;border:1px solid #dce4ef;border-radius:8px;background:#fff}.cev3-range-head,.cev3-range-row{display:grid;grid-template-columns:minmax(150px,1.05fr) minmax(120px,.72fr) minmax(120px,.72fr) minmax(160px,1fr) 34px;align-items:center;gap:10px}.cev3-range-head{min-height:34px;padding:7px 10px;color:#657395;background:#f7f9fc;border-bottom:1px solid #dfe6ef;font-size:9.8px;font-weight:600}.cev3-range-row{min-height:48px;padding:7px 10px;border-bottom:1px solid #e8edf4}.cev3-range-row:last-child{border-bottom:0}.cev3-range-row .cev3-control{height:32px;font-size:10.5px}.cev3-range-reset{display:flex;width:30px;height:30px;align-items:center;justify-content:center;color:#4f46e5;background:#f1f3ff;border:1px solid #d9dcff;border-radius:6px;font-size:14px;font-weight:600}.cev3-range-reset:hover{color:#fff;background:#4f46e5}.cev3-policy-card{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));align-items:start;gap:14px;padding:14px;background:linear-gradient(135deg,#fbfcff,#f8f7ff);border:1px solid #e3e6f2;border-radius:8px}.cev3-payment-card{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:14px;background:linear-gradient(135deg,#fbfcff,#f8f7ff);border:1px solid #e3e6f2;border-radius:8px}.cev3-policy-note{display:flex;grid-column:1/-1;min-height:54px;align-items:center;gap:9px;padding:11px;color:#435783;background:#f6f5ff;border:1px solid #d8d5ff;border-radius:7px;font-size:10px;line-height:1.5}.cev3-policy-note span:first-child{display:flex;width:22px;height:22px;align-items:center;justify-content:center;flex:0 0 22px;color:#4f46e5;border:1px solid #8d86ff;border-radius:50%;font-weight:600}.cev3-country-grid{grid-template-columns:repeat(4,minmax(0,1fr));min-height:390px}.cev3-country-panel,.cev3-country-section,.cev3-country-box{flex:initial}.cev3-country-box{display:flex;flex-direction:column}.cev3-country-grid{flex:initial}
@media(max-width:1199px){.cev3-default-pricing-card{grid-template-columns:repeat(3,minmax(0,1fr))}.cev3-range-head,.cev3-range-row{grid-template-columns:minmax(135px,1fr) minmax(100px,.65fr) minmax(105px,.7fr) minmax(140px,.9fr) 32px}.cev3-policy-card{grid-template-columns:repeat(3,minmax(0,1fr))}.cev3-policy-note{grid-column:1/-1}.cev3-payment-card{grid-template-columns:repeat(3,minmax(0,1fr))}.cev3-country-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:767px){.cev3-header{align-items:flex-start;padding:10px 12px}.cev3-heading-icon{display:none}.cev3-title{font-size:14px!important}.cev3-subtitle{font-size:10px}.cev3-workspace{padding:9px}.cev3-panel{padding:11px}.cev3-model-grid,.cev3-default-pricing-card,.cev3-policy-card,.cev3-payment-card{grid-template-columns:1fr}.cev3-policy-note{grid-column:auto}.cev3-model-option{min-height:102px}.cev3-range-table{overflow-x:auto}.cev3-range-head,.cev3-range-row{grid-template-columns:145px 105px 110px 155px 32px;min-width:590px}.cev3-country-toolbar{grid-template-columns:1fr}.cev3-country-actions{overflow-x:auto}.cev3-country-grid{grid-template-columns:repeat(2,minmax(0,1fr));min-height:360px}.cev3-footer{flex-wrap:wrap}.cev3-footer-group{flex:1}.cev3-footer-group:last-child{justify-content:flex-end}.cev3-primary{min-width:125px}}
.cev3-fixed-only{display:none}.commercial-editor-v3.is-fixed-fee .cev3-percentage-only{display:none}.commercial-editor-v3.is-fixed-fee .cev3-fixed-only{display:block}.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:repeat(4,minmax(0,1fr))}.commercial-editor-v3.is-fixed-fee .cev3-default-rate{order:1}.commercial-editor-v3.is-fixed-fee .cev3-fixed-only:nth-last-child(2){order:2}.commercial-editor-v3.is-fixed-fee .cev3-fixed-only:last-child{order:3}.commercial-editor-v3.is-fixed-fee .cev3-default-currency{order:4}.commercial-editor-v3.is-fixed-fee .cev3-range-head,.commercial-editor-v3.is-fixed-fee .cev3-range-row{grid-template-columns:minmax(145px,1.05fr) minmax(100px,.7fr) minmax(120px,.82fr) minmax(135px,.9fr) minmax(115px,.78fr) 34px}.commercial-editor-v3.is-fixed-fee .cev3-range-head .cev3-fixed-only{display:block}.commercial-editor-v3.is-fixed-fee .cev3-range-row .cev3-fixed-only{display:block}@media(max-width:1199px){.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:repeat(2,minmax(0,1fr))}.commercial-editor-v3.is-fixed-fee .cev3-range-head,.commercial-editor-v3.is-fixed-fee .cev3-range-row{grid-template-columns:minmax(140px,1fr) minmax(95px,.65fr) minmax(110px,.75fr) minmax(125px,.82fr) minmax(105px,.72fr) 32px}}@media(max-width:767px){.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:1fr}.commercial-editor-v3.is-fixed-fee .cev3-range-head,.commercial-editor-v3.is-fixed-fee .cev3-range-row{grid-template-columns:145px 105px 120px 150px 125px 32px;min-width:677px}}
.cev3-free-only{display:none}.cev3-no-fee-summary{grid-column:1/-1;grid-template-columns:42px minmax(170px,1fr) minmax(360px,2fr);align-items:center;gap:13px;padding:14px;color:#27365a;background:linear-gradient(135deg,#faf8ff,#f6f4ff);border:1px solid #ddd6ff;border-radius:8px}.cev3-no-fee-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;color:#5b45f5;background:#eae6ff;border-radius:8px;font-size:22px}.cev3-no-fee-summary strong{display:block;color:#172554;font-size:11.5px;font-weight:600}.cev3-no-fee-summary p{margin:4px 0 0;color:#657395;font-size:9.8px;font-weight:500;line-height:1.45}.cev3-no-fee-summary dl{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:0}.cev3-no-fee-summary dt{margin:0 0 4px;color:#7a879f;font-size:8.8px;font-weight:500}.cev3-no-fee-summary dd{margin:0;color:#23345d;font-size:9.8px;font-weight:600}.cev3-no-fee-note{min-height:28px;padding:6px 9px;color:#667695;background:#f8f9fc;border:1px solid #e2e7ef;border-radius:6px;font-size:10px;font-weight:500}.cev3-no-fee-payment{align-items:center;gap:10px;padding:12px 14px;color:#435783;background:#f6f5ff;border:1px solid #d8d5ff;border-radius:8px}.cev3-no-fee-payment>span{display:flex;width:22px;height:22px;align-items:center;justify-content:center;flex:0 0 22px;color:#4f46e5;border:1px solid #8d86ff;border-radius:50%;font-weight:600}.cev3-no-fee-payment strong{display:block;color:#28375d;font-size:10.5px;font-weight:600}.cev3-no-fee-payment p{margin:3px 0 0;font-size:9.8px;font-weight:500}.commercial-editor-v3.is-free-fee .cev3-free-only{display:grid}.commercial-editor-v3.is-free-fee .cev3-default-pricing-card{display:block}.commercial-editor-v3.is-free-fee .cev3-default-pricing-card>.cev3-default-currency,.commercial-editor-v3.is-free-fee .cev3-default-pricing-card>.cev3-default-rate,.commercial-editor-v3.is-free-fee .cev3-default-pricing-card>.cev3-default-applied,.commercial-editor-v3.is-free-fee .cev3-default-pricing-card>.cev3-fixed-only,.commercial-editor-v3.is-free-fee .cev3-standard-pricing-field,.commercial-editor-v3.is-free-fee .cev3-percentage-only,.commercial-editor-v3.is-free-fee .cev3-fixed-only,.commercial-editor-v3.is-free-fee [data-range-reset],.commercial-editor-v3.is-free-fee .cev3-payment-card{display:none}.commercial-editor-v3.is-free-fee .cev3-range-head,.commercial-editor-v3.is-free-fee .cev3-range-row{grid-template-columns:minmax(150px,1fr) minmax(260px,2.2fr) 34px}.commercial-editor-v3.is-free-fee .cev3-range-head .cev3-free-only,.commercial-editor-v3.is-free-fee .cev3-range-row .cev3-free-only{display:block}.commercial-editor-v3.is-free-fee .cev3-no-fee-payment{display:flex}@media(max-width:767px){.cev3-no-fee-summary{grid-template-columns:38px minmax(0,1fr)}.cev3-no-fee-summary dl{grid-column:1/-1;grid-template-columns:repeat(2,minmax(0,1fr))}.commercial-editor-v3.is-free-fee .cev3-range-head,.commercial-editor-v3.is-free-fee .cev3-range-row{grid-template-columns:145px 240px 32px;min-width:420px}}
.commercial-editor-v3.is-fixed-fee .cev3-fixed-trigger-field{order:2}.commercial-editor-v3.is-fixed-fee .cev3-fixed-frequency-field{order:3}
.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:repeat(5,minmax(0,1fr))}.commercial-editor-v3.is-fixed-fee .cev3-default-currency{order:1}.commercial-editor-v3.is-fixed-fee .cev3-default-rate{order:2}.commercial-editor-v3.is-fixed-fee .cev3-default-applied{order:3}.commercial-editor-v3.is-fixed-fee .cev3-fixed-trigger-field{order:4}.commercial-editor-v3.is-fixed-fee .cev3-fixed-frequency-field{order:5}@media(max-width:1199px){.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:767px){.commercial-editor-v3.is-fixed-fee .cev3-default-pricing-card{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){.cev3-btn{transition:none}}
</style>

<div id="commercial-editor-v3" class="commercial-editor-v3">
    <header class="cev3-header">
        <div class="cev3-heading">
            <span class="cev3-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4h8v2M3 11h18M10 11v2h4v-2"/></svg></span>
            <div><div class="cev3-title-row"><h3 class="cev3-title">Edit {{ $isFulltime ? 'Fulltime' : 'Contract' }} Hiring Services</h3><span class="cev3-active {{ !empty($commercial['status']) ? 'cev3-status-active' : 'cev3-status-inactive' }}">{{ !empty($commercial['status']) ? 'Active' : 'Inactive' }}</span></div><p class="cev3-subtitle">Configure your placement fee structure and the countries where you offer hiring services.</p></div>
        </div>
        <div class="cev3-header-actions">
            <button type="button" class="cev3-btn cev3-close" aria-label="Close commercial settings" title="Close" onclick="$('#kt_activitiesJob > .card > #kt_activities_header #kt_activitiesJob_close').trigger('click');">×</button>
        </div>
    </header>

    <input class="main_status" type="hidden" value="{{ !empty($commercial['status']) ? 1 : 0 }}"><input class="rateTypeClass" type="hidden" value="{{ $rateType }}"><input class="updateId" type="hidden" value="{{ $commercial['id'] ?? '' }}"><input class="commType" type="hidden" value="{{ $sectionCheck }}">

    <main class="cev3-workspace">
        <section class="cev3-panel">
            <div class="cev3-section">
                <div class="cev3-section-head"><span class="cev3-section-number">1</span><div><h4 class="cev3-section-title">Commercial Model</h4></div></div>
                <input type="hidden" data-commercial-type value="{{ $selectedCommercialModel }}">
                <div class="cev3-model-grid" role="radiogroup" aria-label="Commercial model">
                    <button type="button" class="cev3-model-option {{ $selectedCommercialModel === 2 ? 'active' : '' }}" data-commercial-model="2" role="radio" aria-checked="{{ $selectedCommercialModel === 2 ? 'true' : 'false' }}"><span class="cev3-model-icon" aria-hidden="true">%</span><span><b>Percentage Based</b><small>Charge a percentage of the candidate's compensation or bill rate.</small></span><i aria-hidden="true"></i></button>
                    <button type="button" class="cev3-model-option {{ $selectedCommercialModel === 1 ? 'active' : '' }}" data-commercial-model="1" role="radio" aria-checked="{{ $selectedCommercialModel === 1 ? 'true' : 'false' }}"><span class="cev3-model-icon" aria-hidden="true">₹</span><span><b>Fixed Fee</b><small>Charge a fixed amount for each successful hire or configured period.</small></span><i aria-hidden="true"></i></button>
                    <button type="button" class="cev3-model-option {{ $selectedCommercialModel === 0 ? 'active' : '' }}" data-commercial-model="0" role="radio" aria-checked="{{ $selectedCommercialModel === 0 ? 'true' : 'false' }}"><span class="cev3-model-icon" aria-hidden="true">◇</span><span><b>Free / No Fee</b><small>No commercial charges apply to this hiring service.</small></span><i aria-hidden="true"></i></button>
                </div>
                <div class="cev3-info"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><span data-commercial-model-copy>{{ $selectedCommercialModel === 0 ? 'No commercial fee will be charged for the selected countries.' : ($selectedCommercialModel === 2 ? "Placement fees are calculated as a percentage of the candidate's Cost to Company (CTC)." : 'Billing is calculated from the configured fixed rate for each experience range.') }}</span></div>
            </div>
        </section>

        <section class="cev3-panel">
            <div class="cev3-section">
                <div class="cev3-section-head"><span class="cev3-section-number">2</span><div><h4 class="cev3-section-title">Default Pricing</h4><p class="cev3-section-copy" data-default-pricing-copy>{{ $selectedCommercialModel === 1 ? 'Set the default fixed fee and how it will be charged.' : 'Set the default pricing for all experience ranges.' }}</p></div></div>
                <div class="cev3-default-pricing-card">
                    <div class="cev3-default-currency"><label class="cev3-label" for="commercial_salary_currency">Currency</label><select id="commercial_salary_currency" name="salary_currency" class="cev3-control" data-pricing-currency>@foreach($availableCurrencies as $currencyKey => $currencyCode)<option value="{{ $currencyKey }}" {{ $selectedCurrency === (int) $currencyKey ? 'selected' : '' }}>{{ $currencyCode }}</option>@endforeach</select><p class="cev3-help">All backend currencies are available.</p></div>
                    <div class="cev3-default-rate"><label class="cev3-label" for="commercial_default_rate" data-rate-label>{{ $selectedCommercialModel === 2 ? 'Rate / Percentage' : 'Fixed Fee Amount' }}</label><div class="cev3-rate-wrap"><input type="number" id="commercial_default_rate" class="cev3-control" data-fallback-rate="{{ $fallbackDefaultRate }}" step="0.01" min="0" value="{{ $defaultRate }}"><span class="cev3-rate-suffix" data-rate-suffix>{{ $selectedCommercialModel === 2 ? '%' : '' }}</span></div><p class="cev3-help" data-rate-help>{{ $selectedCommercialModel === 2 ? 'Changing this rate updates every experience range.' : 'The default amount used for each successful placement.' }}</p></div>
                    <div class="cev3-default-applied"><label class="cev3-label" for="commercial_salary_mode">Applied On</label><select id="commercial_salary_mode" name="sal_mode" class="cev3-control" data-applied-on>@foreach(($salaryMode ?? []) as $modeKey => $modeLabel)<option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>{{ $modeLabel }} {{ $isFulltime ? 'CTC' : 'Bill Rate' }}</option>@endforeach</select><p class="cev3-help">Choose the billing period.</p></div>
                    <div class="cev3-fixed-only cev3-fixed-trigger-field"><label class="cev3-label">Fee Charged On</label><select class="cev3-control" data-fixed-fee-trigger>@foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)<option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>{{ $triggerValue === 'candidate_joined' ? 'Per Successful Hire' : $triggerLabel }}</option>@endforeach</select><p class="cev3-help">When the fixed fee becomes chargeable.</p></div>
                    <div class="cev3-fixed-only cev3-fixed-frequency-field"><label class="cev3-label">Billing Frequency</label><select class="cev3-control" data-fixed-fee-frequency>@foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)<option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>{{ $frequencyLabel }}</option>@endforeach</select><p class="cev3-help">How often this fee is billed.</p></div>
                    <div class="cev3-free-only cev3-no-fee-summary"><span class="cev3-no-fee-icon" aria-hidden="true">◇</span><div><strong>No Fee Applicable</strong><p>This agreement is configured as Free / No Fee. No pricing will be applied.</p></div><dl><div><dt>Pricing Model</dt><dd>Free / No Fee</dd></div><div><dt>Fee Amount</dt><dd>{{ $availableCurrencies->get($selectedCurrency, 'Currency') }} 0</dd></div><div><dt>Currency</dt><dd>Not Applicable</dd></div><div><dt>Billing Frequency</dt><dd>Not Applicable</dd></div></dl></div>
                </div>
            </div>
        </section>

        <section class="cev3-panel">
            <div class="cev3-section">
                <div class="cev3-section-head"><span class="cev3-section-number">3</span><div><h4 class="cev3-section-title">Experience-Based Pricing</h4><p class="cev3-section-copy" data-experience-pricing-copy>{{ $selectedCommercialModel === 1 ? 'Define different fixed fees for different experience bands.' : 'Define pricing using clear experience ranges.' }}</p></div></div>
                <div class="cev3-range-table"><div class="cev3-range-head"><span>Experience Band</span><span class="cev3-free-only">Notes (Optional)</span><span class="cev3-standard-pricing-field">Currency</span><span class="cev3-standard-pricing-field" data-range-rate-label>{{ $selectedCommercialModel === 2 ? 'Rate / Percentage' : 'Fixed Fee Amount' }}</span><span class="cev3-percentage-only">Applied On</span><span class="cev3-fixed-only">Fee Charged On</span><span class="cev3-fixed-only">Billing Frequency</span><span></span></div>
                    @foreach($experienceRanges as $range)
                        @php
                            $firstExperienceIndex = $range['indexes'][0];
                            $rangeRate = data_get($commercialDetails->get($firstExperienceIndex), 'rate', $defaultRate);
                        @endphp
                        <div class="cev3-range-row" data-experience-range>
                            <span class="cev3-rate-name">{{ $range['label'] }}</span>
                            <span class="cev3-free-only cev3-no-fee-note">No fee applicable</span>
                            <select class="cev3-control cev3-standard-pricing-field" data-range-currency>@foreach($availableCurrencies as $currencyKey => $currencyCode)<option value="{{ $currencyKey }}" {{ $selectedCurrency === (int) $currencyKey ? 'selected' : '' }}>{{ $currencyCode }}</option>@endforeach</select>
                            <div class="cev3-rate-wrap cev3-standard-pricing-field"><input type="number" class="cev3-control" data-range-rate step="0.01" min="0" value="{{ $rangeRate }}"><span class="cev3-rate-suffix" data-rate-suffix>{{ $selectedCommercialModel === 2 ? '%' : '' }}</span></div>
                            <select class="cev3-control cev3-percentage-only" data-range-applied-on>@foreach(($salaryMode ?? []) as $modeKey => $modeLabel)<option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>{{ $modeLabel }} {{ $isFulltime ? 'CTC' : 'Bill Rate' }}</option>@endforeach</select>
                            <select class="cev3-control cev3-fixed-only" data-range-fixed-trigger>@foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)<option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>{{ $triggerValue === 'candidate_joined' ? 'Per Successful Hire' : $triggerLabel }}</option>@endforeach</select>
                            <select class="cev3-control cev3-fixed-only" data-range-fixed-frequency>@foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)<option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>{{ $frequencyLabel }}</option>@endforeach</select>
                            <button type="button" class="cev3-range-reset" data-range-reset title="Reset this range to the default rate" aria-label="Reset {{ $range['label'] }}">↻</button>
                            @foreach($range['indexes'] as $experienceIndex)
                                @php $experienceRate = data_get($commercialDetails->get($experienceIndex), 'rate', $defaultRate); @endphp
                                <input type="hidden" class="{{ $isFulltime ? 'perecentage_val' : 'rate_val' }}" name="{{ $isFulltime ? 'perecentage[]' : 'rate[]' }}" data-experience-index="{{ $experienceIndex }}" value="{{ $experienceRate }}">
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="cev3-info blue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><span data-experience-info>Each range is saved through the existing experience-rate structure. Applied On and Currency remain consistent across the pricing setup.</span></div>
            </div>
        </section>

        <section class="cev3-panel">
            <div class="cev3-section">
                <div class="cev3-section-head"><span class="cev3-section-number">4</span><div><h4 class="cev3-section-title">Replacement Policy</h4><p class="cev3-section-copy">Define the replacement terms for this commercial agreement.</p></div></div>
                <div class="cev3-policy-card">
                    <div><label class="cev3-label" for="commercial_replacement_period">Guarantee Period</label><select id="commercial_replacement_period" class="cev3-control" data-replacement-period>@foreach(($replacementPeriods ?? []) as $periodValue => $periodLabel)<option value="{{ $periodValue }}" {{ $selectedReplacementPeriod === (int) $periodValue ? 'selected' : '' }}>{{ $periodLabel }}</option>@endforeach</select><p class="cev3-help">Period during which replacement applies.</p></div>
                    <div><label class="cev3-label" for="commercial_replacement_type">Replacement Type</label><select id="commercial_replacement_type" class="cev3-control" data-replacement-type>@foreach(($replacementTypes ?? []) as $typeValue => $typeLabel)<option value="{{ $typeValue }}" {{ $selectedReplacementType === (string) $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>@endforeach</select><p class="cev3-help">The remedy offered to the client.</p></div>
                    <div><label class="cev3-label" for="commercial_replacement_eligibility">Replacement Eligibility</label><select id="commercial_replacement_eligibility" class="cev3-control" data-replacement-eligibility>@foreach(($replacementEligibility ?? []) as $eligibilityValue => $eligibilityLabel)<option value="{{ $eligibilityValue }}" {{ $selectedReplacementEligibility === (string) $eligibilityValue ? 'selected' : '' }}>{{ $eligibilityLabel }}</option>@endforeach</select><p class="cev3-help">The event that makes a replacement eligible.</p></div>
                    <div class="cev3-policy-note"><span aria-hidden="true">i</span><div>A <b data-replacement-summary>{{ strtolower($replacementTypes[$selectedReplacementType] ?? 'replacement') }}</b> applies when an eligible exit occurs within <b data-replacement-days>{{ $selectedReplacementPeriod }}</b> days.</div></div>
                </div>
            </div>
        </section>

        <section class="cev3-panel">
            <div class="cev3-section">
                <div class="cev3-section-head"><span class="cev3-section-number">5</span><div><h4 class="cev3-section-title">Payment Timeline</h4><p class="cev3-section-copy">Define invoicing frequency, trigger and payment period.</p></div></div>
                <div class="cev3-payment-card">
                    <div><label class="cev3-label" for="commercial_billing_frequency">Billing Frequency</label><select id="commercial_billing_frequency" class="cev3-control" data-billing-frequency>@foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)<option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>{{ $frequencyLabel }}</option>@endforeach</select><p class="cev3-help">How often invoices are raised.</p></div>
                    <div><label class="cev3-label" for="commercial_billing_trigger">Billing Trigger</label><select id="commercial_billing_trigger" class="cev3-control" data-billing-trigger>@foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)<option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>{{ $triggerLabel }}</option>@endforeach</select><p class="cev3-help">The event that starts invoicing.</p></div>
                    <div><label class="cev3-label" for="commercial_payment_terms">Payment Period</label><select id="commercial_payment_terms" class="cev3-control" data-payment-terms>@foreach(($paymentTerms ?? []) as $termsValue => $termsLabel)<option value="{{ $termsValue }}" {{ $selectedPaymentTerms === (int) $termsValue ? 'selected' : '' }}>{{ $termsLabel }}</option>@endforeach</select><p class="cev3-help">Time allowed for payment.</p></div>
                </div>
                <div class="cev3-free-only cev3-no-fee-payment"><span aria-hidden="true">i</span><div><strong>Payment timeline not applicable</strong><p>This agreement is Free / No Fee, so invoicing and payment terms are not applicable.</p></div></div>
            </div>
        </section>

        <section class="cev3-panel cev3-country-panel">
            <div class="cev3-section cev3-country-section">
                <div class="cev3-section-head"><span class="cev3-section-number">6</span><div><h4 class="cev3-section-title">Country Coverage</h4><p class="cev3-section-copy">Select the countries where you offer {{ $isFulltime ? 'fulltime' : 'contract' }} hiring services.</p></div></div>
                <div class="cev3-country-toolbar"><div class="cev3-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" class="cev3-control" placeholder="Search country..." data-country-search></div><div class="cev3-country-actions"><button type="button" class="cev3-btn" data-country-select-all>✓ Select All</button><button type="button" class="cev3-btn" data-country-top>⌂ Top Hiring Countries</button><button type="button" class="cev3-btn" data-country-clear>⊗ Deselect All</button></div></div>
                <div class="cev3-selected" data-selected-countries></div>
                <div class="cev3-country-box">
                    <div class="cev3-country-box-head"><div class="cev3-country-box-title"><span>⊙</span>Browse Countries by Region</div><span aria-hidden="true">⌃</span></div>
                    <div class="cev3-region-tabs"><button type="button" class="cev3-region-tab active" data-region="all">All</button>@foreach($countryRegions as $regionName => $regionCountries)<button type="button" class="cev3-region-tab" data-region="{{ strtolower($regionName) }}">{{ $regionName }}</button>@endforeach</div>
                    <div class="cev3-country-grid" data-country-grid>
                        @foreach(($countries ?? []) as $country)
                            @php
                                $countryId = (int) $country->id;
                                $countryRegion = strtolower(trim((string) ($country->region ?? '')) ?: 'other');
                                $countryCode = strtoupper(trim((string) ($country->iso2 ?? $country->country_code ?? '')));
                            @endphp
                            <div class="cev3-country-option" data-country-item data-name="{{ strtolower(trim($countryCode.' '.$country->country_name)) }}" data-region="{{ $countryRegion }}"><input class="countries" type="checkbox" id="commercial_country_{{ $sectionCheck }}_{{ $countryId }}" value="{{ $countryId }}" data-country-name="{{ $country->country_name }}" data-country-code="{{ $countryCode }}" data-initial="{{ in_array($countryId, $selectedCountryIds, true) ? '1' : '0' }}" {{ in_array($countryId, $selectedCountryIds, true) ? 'checked' : '' }}><label for="commercial_country_{{ $sectionCheck }}_{{ $countryId }}"><span class="cev3-country-code">{{ $countryCode ?: '—' }}</span><b>{{ $country->country_name }}</b></label></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="cev3-footer"><div class="cev3-footer-group"><button type="button" class="cev3-btn" data-commercial-cancel>Cancel</button><button type="button" class="cev3-btn cev3-soft" data-commercial-reset>Reset to Saved Values</button></div><div class="cev3-footer-group"><button type="button" id="form-submit" class="btn copy_current_selected_applicant cev3-btn cev3-primary" aria-live="polite"><span class="indicator-label">✓ Save Commercials</span><span class="indicator-progress"><span class="cev3-spinner" aria-hidden="true"></span>Saving...</span></button></div></footer>
</div>

<script>
// Intent: provide reference-design interactions locally while the existing delegated Save Commercials handler remains authoritative.
(function () {
    var root = document.getElementById('commercial-editor-v3');
    if (!root) return;
    var countries = Array.prototype.slice.call(root.querySelectorAll('.countries'));
    var countryItems = Array.prototype.slice.call(root.querySelectorAll('[data-country-item]'));
    var selectedHost = root.querySelector('[data-selected-countries]');
    var search = root.querySelector('[data-country-search]');
    var appliedOn = root.querySelector('[data-applied-on]');
    var pricingCurrency = root.querySelector('[data-pricing-currency]');
    var commercialType = root.querySelector('[data-commercial-type]');
    var defaultRateInput = root.querySelector('#commercial_default_rate');
    var replacementPeriod = root.querySelector('[data-replacement-period]');
    var replacementType = root.querySelector('[data-replacement-type]');
    var replacementEligibility = root.querySelector('[data-replacement-eligibility]');
    var billingFrequency = root.querySelector('[data-billing-frequency]');
    var billingTrigger = root.querySelector('[data-billing-trigger]');
    var fixedFeeTrigger = root.querySelector('[data-fixed-fee-trigger]');
    var fixedFeeFrequency = root.querySelector('[data-fixed-fee-frequency]');
    var paymentTerms = root.querySelector('[data-payment-terms]');
    var initialState;
    var activeRegion = 'all';
    function updateCommercialType() {
        var modelValue = commercialType.value;
        var isPercentage = modelValue === '2';
        var isFixedFee = modelValue === '1';
        var isFree = modelValue === '0';
        var wasFree = defaultRateInput.disabled;
        var supportedServiceType = root.querySelector('.commType').value === '2' ? '2' : '1';
        root.querySelector('.rateTypeClass').value = isFree ? supportedServiceType : modelValue;
        root.querySelectorAll('[data-commercial-model]').forEach(function (option) { var active = option.dataset.commercialModel === modelValue; option.classList.toggle('active', active); option.setAttribute('aria-checked', active ? 'true' : 'false'); });
        root.classList.toggle('is-fixed-fee', isFixedFee);
        root.classList.toggle('is-free-fee', isFree);
        if (isFree) {
            defaultRateInput.value = '0';
            defaultRateInput.dispatchEvent(new Event('input'));
        } else if (wasFree && Number(defaultRateInput.value) === 0) {
            defaultRateInput.value = defaultRateInput.dataset.fallbackRate;
            defaultRateInput.dispatchEvent(new Event('input'));
        }
        defaultRateInput.disabled = isFree;
        appliedOn.disabled = isFree;
        pricingCurrency.disabled = isFree;
        root.querySelectorAll('[data-range-rate],[data-range-applied-on],[data-range-currency],[data-range-fixed-trigger],[data-range-fixed-frequency],[data-range-reset]').forEach(function (control) { control.disabled = isFree; });
        root.querySelectorAll('[data-fixed-fee-trigger],[data-fixed-fee-frequency]').forEach(function (control) { control.disabled = isFree; });
        root.querySelectorAll('[data-rate-suffix]').forEach(function (suffix) { suffix.textContent = isPercentage ? '%' : ''; });
        root.querySelector('[data-rate-label]').textContent = isFree ? 'No Fee' : (isPercentage ? 'Rate / Percentage' : 'Fixed Fee Amount');
        root.querySelector('[data-rate-help]').textContent = isFree ? 'No commercial fee is charged.' : (isPercentage ? 'Changing this rate updates every experience range.' : 'The default amount used for each successful placement.');
        root.querySelector('[data-range-rate-label]').textContent = isFree ? 'No Fee' : (isPercentage ? 'Rate / Percentage' : 'Fixed Fee Amount');
        root.querySelector('[data-default-pricing-copy]').textContent = isFree ? 'No charge will be applied for this service.' : (isPercentage ? 'Set the default pricing for all experience ranges.' : 'Set the default fixed fee and how it will be charged.');
        root.querySelector('[data-experience-pricing-copy]').textContent = isFree ? 'No per-range fee is required for this service.' : (isPercentage ? 'Define pricing using clear experience ranges.' : 'Define different fixed fees for different experience bands.');
        root.querySelector('[data-experience-info]').textContent = isFree ? 'Experience bands are retained for informational reference only. No fee will be charged for any band.' : 'Each range is saved through the existing experience-rate structure. Applied On and Currency remain consistent across the pricing setup.';
        root.querySelector('[data-commercial-model-copy]').textContent = isFree ? 'No commercial fee will be charged for the selected countries.' : (isPercentage ? "Placement fees are calculated as a percentage of the candidate's Cost to Company (CTC)." : 'Billing is calculated from the configured fixed rate for each experience range.');
    }
    function captureInitialState() {
        initialState = {
            commercialType: commercialType.value,
            defaultRate: defaultRateInput.value,
            appliedOn: appliedOn.value,
            currency: pricingCurrency.value,
            replacementPeriod: replacementPeriod.value,
            replacementType: replacementType.value,
            replacementEligibility: replacementEligibility.value,
            billingFrequency: billingFrequency.value,
            billingTrigger: billingTrigger.value,
            paymentTerms: paymentTerms.value,
            storedRates: Array.prototype.map.call(root.querySelectorAll('.rate_val,.perecentage_val'), function (rate) { return rate.value; }),
            countries: countries.map(function (country) { return country.checked; })
        };
    }
    function restoreInitialState() {
        commercialType.value = initialState.commercialType;
        defaultRateInput.value = initialState.defaultRate;
        appliedOn.value = initialState.appliedOn;
        pricingCurrency.value = initialState.currency;
        replacementPeriod.value = initialState.replacementPeriod;
        replacementType.value = initialState.replacementType;
        replacementEligibility.value = initialState.replacementEligibility;
        billingFrequency.value = initialState.billingFrequency;
        billingTrigger.value = initialState.billingTrigger;
        paymentTerms.value = initialState.paymentTerms;
        root.querySelectorAll('.rate_val,.perecentage_val').forEach(function (rate, index) { rate.value = initialState.storedRates[index]; });
        root.querySelectorAll('[data-experience-range]').forEach(function (rangeRow) { rangeRow.querySelector('[data-range-rate]').value = rangeRow.querySelector('.rate_val,.perecentage_val').value; });
        countries.forEach(function (country, index) { country.checked = initialState.countries[index]; });
        syncRangeSelects('[data-range-applied-on]', appliedOn.value);
        syncRangeSelects('[data-range-currency]', pricingCurrency.value);
        syncRangeSelects('[data-fixed-fee-trigger]', billingTrigger.value);
        syncRangeSelects('[data-fixed-fee-frequency]', billingFrequency.value);
        updateCommercialType();
        updateReplacementSummary();
        renderSelectedCountries();
    }
    function updateReplacementSummary() {
        root.querySelector('[data-replacement-days]').textContent = replacementPeriod.value;
        root.querySelector('[data-replacement-summary]').textContent = replacementType.options[replacementType.selectedIndex].text.toLowerCase();
    }
    function syncRangeSelects(selector, value) { root.querySelectorAll(selector).forEach(function (select) { select.value = value; }); }
    root.querySelectorAll('[data-experience-range]').forEach(function (rangeRow) {
        var rangeRate = rangeRow.querySelector('[data-range-rate]');
        var storedRates = rangeRow.querySelectorAll('.rate_val,.perecentage_val');
        rangeRate.addEventListener('input', function () { storedRates.forEach(function (rate) { rate.value = rangeRate.value; }); });
        rangeRow.querySelector('[data-range-reset]').addEventListener('click', function () { rangeRate.value = root.querySelector('#commercial_default_rate').value; rangeRate.dispatchEvent(new Event('input')); });
    });
    appliedOn.addEventListener('change', function () { syncRangeSelects('[data-range-applied-on]', appliedOn.value); });
    pricingCurrency.addEventListener('change', function () { syncRangeSelects('[data-range-currency]', pricingCurrency.value); });
    billingTrigger.addEventListener('change', function () { syncRangeSelects('[data-fixed-fee-trigger]', billingTrigger.value); });
    billingFrequency.addEventListener('change', function () { syncRangeSelects('[data-fixed-fee-frequency]', billingFrequency.value); });
    replacementPeriod.addEventListener('change', updateReplacementSummary);
    replacementType.addEventListener('change', updateReplacementSummary);
    root.querySelectorAll('[data-commercial-model]').forEach(function (option) { option.addEventListener('click', function () { commercialType.value = option.dataset.commercialModel; updateCommercialType(); }); });
    defaultRateInput.addEventListener('input', function () { root.querySelectorAll('[data-range-rate]').forEach(function (rangeRate) { rangeRate.value = defaultRateInput.value; rangeRate.dispatchEvent(new Event('input')); }); });
    root.querySelectorAll('[data-range-applied-on]').forEach(function (select) { select.addEventListener('change', function () { appliedOn.value = select.value; syncRangeSelects('[data-range-applied-on]', select.value); }); });
    root.querySelectorAll('[data-range-currency]').forEach(function (select) { select.addEventListener('change', function () { pricingCurrency.value = select.value; syncRangeSelects('[data-range-currency]', select.value); }); });
    root.querySelectorAll('[data-fixed-fee-trigger]').forEach(function (select) { select.addEventListener('change', function () { billingTrigger.value = select.value; syncRangeSelects('[data-fixed-fee-trigger]', select.value); }); });
    root.querySelectorAll('[data-fixed-fee-frequency]').forEach(function (select) { select.addEventListener('change', function () { billingFrequency.value = select.value; syncRangeSelects('[data-fixed-fee-frequency]', select.value); }); });
    function renderSelectedCountries() {
        var selected = countries.filter(function (country) { return country.checked; });
        selectedHost.innerHTML = '';
        selected.slice(0, 6).forEach(function (country) {
            var chip = document.createElement('span'); chip.className = 'cev3-chip';
            chip.innerHTML = '<span class="cev3-country-code"></span><span></span><button type="button" class="cev3-chip-remove" aria-label="Remove country">×</button>';
            chip.children[0].textContent = country.dataset.countryCode || '—'; chip.children[1].textContent = country.dataset.countryName;
            chip.querySelector('button').addEventListener('click', function () { country.checked = false; renderSelectedCountries(); }); selectedHost.appendChild(chip);
        });
        if (selected.length > 6) { var more = document.createElement('span'); more.className = 'cev3-chip'; more.textContent = '+ ' + (selected.length - 6) + ' more'; selectedHost.appendChild(more); }
        if (!selected.length) { var empty = document.createElement('span'); empty.className = 'cev3-help'; empty.textContent = 'No countries selected'; selectedHost.appendChild(empty); }
    }
    function filterCountries() { var term = (search.value || '').trim().toLowerCase(); countryItems.forEach(function (item) { item.hidden = !((activeRegion === 'all' || item.dataset.region === activeRegion) && (!term || item.dataset.name.indexOf(term) !== -1)); }); }
    countries.forEach(function (country) { country.addEventListener('change', renderSelectedCountries); }); search.addEventListener('input', filterCountries);
    root.querySelectorAll('.cev3-region-tab[data-region]').forEach(function (button) { button.addEventListener('click', function () { activeRegion = button.dataset.region; root.querySelectorAll('.cev3-region-tab[data-region]').forEach(function (tab) { tab.classList.toggle('active', tab === button); }); filterCountries(); }); });
    root.querySelectorAll('[data-country-select-all]').forEach(function (button) { button.addEventListener('click', function () { countryItems.filter(function (item) { return !item.hidden; }).forEach(function (item) { item.querySelector('.countries').checked = true; }); renderSelectedCountries(); }); });
    root.querySelectorAll('[data-country-clear]').forEach(function (button) { button.addEventListener('click', function () { countries.forEach(function (country) { country.checked = false; }); renderSelectedCountries(); }); });
    root.querySelector('[data-country-top]').addEventListener('click', function () { var topCountries = ['india','united states','australia','united kingdom','united arab emirates','singapore']; countries.forEach(function (country) { country.checked = topCountries.indexOf(country.dataset.countryName.toLowerCase()) !== -1; }); renderSelectedCountries(); });
    root.querySelectorAll('[data-commercial-reset]').forEach(function (button) { button.addEventListener('click', restoreInitialState); });
    root.querySelector('[data-commercial-cancel]').addEventListener('click', function () { root.querySelector('.cev3-close').click(); });
    root.addEventListener('commercial:saved', captureInitialState);
    updateCommercialType();
    updateReplacementSummary();
    captureInitialState();
    renderSelectedCountries();
})();
</script>
