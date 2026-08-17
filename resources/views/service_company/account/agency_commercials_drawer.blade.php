{{--
  Agency Commercials drawer body (Edit Fulltime / Edit Contract).
  Extracted from service_company/account/index.blade.php so the agency
  commercials configuration lives in its own file alongside the partials
  for client commercials (services_view.blade.php etc.). Saved through
  AgencyCommercialTermsService into the dedicated agency_commercial_terms
  table (one row per company_id) — fully separate from client commercials.
--}}

<form class="aat-drawer" id="agency_commercial_terms_form" aria-hidden="true">
  @csrf
  <input type="hidden" name="pageterms" value="agency_commercial_terms">
  <input type="hidden" name="version" value="{{ $aatVersion }}">
  <input type="hidden" name="status" id="agencyCommercialStatus" value="{{ $aatActive ? 'active' : 'inactive' }}">
  @foreach((array) (@$aatTerms->payment_modes_allowed ?? ['bank_transfer','neft','rtgs']) as $modeKey)
    <input type="hidden" name="payment_modes_allowed[]" value="{{ $modeKey }}">
  @endforeach
  <input type="hidden" name="round_off" value="{{ @$aatTerms->round_off ?: 'To Nearest Rupee' }}">
  <div class="aat-drawer-head">
    <div class="aat-drawer-title">
      <span class="aat-hero-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 12h6M9 16h4"/></svg></span>
      <div><h3 data-agency-commercial-title>Edit Commercial Terms</h3><p data-agency-commercial-subtitle>Configure the commercial structure for agencies. These terms reflect in agreements and job assignments.</p></div>
    </div>
    <div class="aat-drawer-meta">
      <button type="button" class="aat-drawer-close" id="closeAgencyCommercialDrawer" aria-label="Close">&times;</button>
    </div>
  </div>
  <div class="aat-drawer-body">
    <div class="aat-edit-alert"><span data-agency-alert-text>These commercial terms define how agencies will be paid for successful placements. They populate the agreement and can be overridden at job level if required.</span><button type="button" class="aat-drawer-close" style="width:28px;height:28px;font-size:18px;">&times;</button></div>

    @php
      $aatFulltimeDefaultRate = (float) (@$aatTerms->fulltime_success_fee_percentage ?? 8.33);
      $aatFixedRate = (float) (@$aatTerms->fulltime_fixed_fee_amount ?? 0);
      $aatExperienceBands = \Illuminate\Support\Facades\Cache::get('commercial_experience_bands:v1');
      if (!collect($aatExperienceBands)->isEmpty()) {
          $aatExperienceBands = collect($aatExperienceBands)->map(function ($band) {
              return [
                  'label' => (string) $band->label,
                  'indexes' => range((int) $band->min_experience, (int) $band->max_experience),
              ];
          })->values()->all();
      } else {
          $aatExperienceBands = [
              ['label' => '0 - 2 Years', 'indexes' => range(0, 2)],
              ['label' => '3 - 5 Years', 'indexes' => range(3, 5)],
              ['label' => '6 - 8 Years', 'indexes' => range(6, 8)],
              ['label' => '9+ Years', 'indexes' => range(9, 30)],
          ];
      }
      $aatFixedFeeCurrency = (int) (@$aatTerms->fulltime_fixed_fee_currency ?? 4);
      $aatGeneralCurrency = (int) (@$aatTerms->general_currency ?? 4);
      $aatPaymentTerms = (int) (@$aatTerms->fulltime_payment_terms_days ?? 30);
      $aatReplacementPeriodDays = (int) (@$aatTerms->fulltime_replacement_period_days ?? 90);
      $aatFulltimePaymentTermsDays = (int) (@$aatTerms->fulltime_payment_terms_days ?? 30);
      $aatContractPaymentTermsDays = (int) (@$aatTerms->contract_payment_terms_days ?? 30);
      $aatContractMinDuration = (int) (@$aatTerms->contract_min_duration_months ?? 3);
    @endphp

    <input type="hidden" name="fulltime_fee_model" id="aatFulltimeFeeModelHidden" value="{{ $aatFulltimeFeeModel }}">

    <section class="aat-edit-section" data-agency-section="fulltime aat-contract-only">
      <div class="aat-edit-section-head">
        <div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 12h6M9 16h4"/></svg></span><div><h4>Commercial Model</h4><p data-aat-commercial-model-copy>Pick how the agency is paid for every successful placement.</p></div></div>
      </div>
      <div class="aat-edit-grid">
        <div class="aat-model-grid">
          <button type="button" class="aat-model-option {{ $aatFulltimeFeeModel === 'percentage' ? 'active' : '' }}" data-aat-commercial-model="percentage" role="radio" aria-checked="{{ $aatFulltimeFeeModel === 'percentage' ? 'true' : 'false' }}"><span class="aat-model-icon">%</span><span><b>Percentage Based</b><small>Charge a percentage of the candidate's cost to company.</small></span><i></i></button>
          <button type="button" class="aat-model-option {{ $aatFulltimeFeeModel === 'fixed' ? 'active' : '' }}" data-aat-commercial-model="fixed" role="radio" aria-checked="{{ $aatFulltimeFeeModel === 'fixed' ? 'true' : 'false' }}"><span class="aat-model-icon">&#8377;</span><span><b>Fixed Fee</b><small>Charge a fixed amount for each successful placement.</small></span><i></i></button>
        </div>
        <div class="aat-info blue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><span id="aatCommercialModelHelp">{{ $aatFulltimeFeeModel === 'fixed' ? 'Billing is calculated from the configured fixed rate for each experience range.' : 'Placement fees are calculated as a percentage of the candidate\'s Cost to Company (CTC).' }}</span></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="fulltime">
      <div class="aat-edit-section-head">
        <div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4h8v2M3 11h18"/></svg></span><div><h4>Default Pricing</h4><p data-aat-default-pricing-copy>{{ $aatFulltimeFeeModel === 'fixed' ? 'Set the default fixed fee and how it will be charged.' : 'Set the default pricing for all experience ranges.' }}</p></div></div>
      </div>
      <div class="aat-edit-grid aat-default-pricing-card">
        <div class="aat-default-currency"><label class="aat-label">Currency <span class="req">*</span></label><select name="fulltime_fixed_fee_currency" class="aat-control" data-aat-default-currency><option value="0" {{ $aatFixedFeeCurrency == 0 ? 'selected' : '' }}>INR</option><option value="1" {{ $aatFixedFeeCurrency == 1 ? 'selected' : '' }}>EUR</option><option value="2" {{ $aatFixedFeeCurrency == 2 ? 'selected' : '' }}>JPY</option><option value="3" {{ $aatFixedFeeCurrency == 3 ? 'selected' : '' }}>GBP</option><option value="4" {{ $aatFixedFeeCurrency == 4 ? 'selected' : '' }}>USD</option><option value="5" {{ $aatFixedFeeCurrency == 5 ? 'selected' : '' }}>AED</option></select><p class="aat-help">All backend currencies are available.</p></div>
        <div class="aat-default-rate"><label class="aat-label" data-aat-rate-label>{{ $aatFulltimeFeeModel === 'fixed' ? 'Fixed Fee Amount' : 'Rate / Percentage' }}</label><div class="aat-rate-wrap"><input name="{{ $aatFulltimeFeeModel === 'fixed' ? 'fulltime_fixed_fee_amount' : 'fulltime_success_fee_percentage' }}" type="number" step="0.01" min="0" value="{{ $aatFulltimeFeeModel === 'fixed' ? @$aatTerms->fulltime_fixed_fee_amount : @$aatTerms->fulltime_success_fee_percentage }}" class="aat-control" data-aat-default-rate-input data-aat-fallback-rate="{{ $aatFulltimeFeeModel === 'fixed' ? (@$aatTerms->fulltime_fixed_fee_amount ?: 0) : (@$aatTerms->fulltime_success_fee_percentage ?: 8.33) }}"><span class="aat-rate-suffix" data-aat-rate-suffix>{{ $aatFulltimeFeeModel === 'fixed' ? '' : '%' }}</span></div><p class="aat-help" data-aat-rate-help>{{ $aatFulltimeFeeModel === 'fixed' ? 'The default amount used for each successful placement.' : 'Changing this rate updates every experience range.' }}</p></div>
        <div class="aat-default-applied"><label class="aat-label">Applied On</label><select name="fulltime_basis" class="aat-control" data-aat-applied-on><option value="Annual Fixed CTC" {{ (@$aatTerms->fulltime_basis === 'Annual Fixed CTC' || @$aatTerms->fulltime_basis === 'free_replacement') ? 'selected' : '' }}>Annual CTC</option><option value="Monthly CTC" {{ @$aatTerms->fulltime_basis === 'Monthly CTC' ? 'selected' : '' }}>Monthly</option><option value="Hourly Rate" {{ @$aatTerms->fulltime_basis === 'Hourly Rate' ? 'selected' : '' }}>Hourly</option></select><p class="aat-help">Choose the billing basis.</p></div>
        <div class="aat-fixed-only aat-fixed-trigger-field"><label class="aat-label">Fee Charged On</label><select name="fulltime_payment_timeline_from" class="aat-control" data-aat-fixed-trigger><option value="Per Hire" {{ (@$aatTerms->fulltime_payment_timeline_from === 'Per Hire' || $aatFulltimeFixedUnit === 'Per Hire') ? 'selected' : '' }}>Per Successful Hire</option><option value="Per Joining" {{ (@$aatTerms->fulltime_payment_timeline_from === 'Per Joining' || $aatFulltimeFixedUnit === 'Per Joining') ? 'selected' : '' }}>Per Joining</option></select><p class="aat-help">When the fixed fee becomes chargeable.</p></div>
        <div class="aat-fixed-only aat-fixed-frequency-field"><label class="aat-label">Billing Frequency</label><select name="fulltime_fixed_fee_unit" class="aat-control" data-aat-fixed-frequency><option value="One Time" {{ (@$aatTerms->fulltime_fixed_fee_unit === 'One Time' || @$aatTerms->fulltime_fixed_fee_unit === 'Per Hire') ? 'selected' : '' }}>One Time</option><option value="Monthly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Monthly' ? 'selected' : '' }}>Monthly</option><option value="Weekly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Weekly' ? 'selected' : '' }}>Weekly</option><option value="Quarterly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Quarterly' ? 'selected' : '' }}>Quarterly</option></select><p class="aat-help">How often this fee is billed.</p></div>
      </div>
      <div class="aat-calc" data-aat-percentage-only><h5>Example Calculation</h5><div class="aat-calc-row"><div class="aat-calc-cell">Annual CTC<b>&#8377; 12,00,000</b></div><div>&times;</div><div class="aat-calc-cell">Success Fee<b>{{ number_format($aatFulltimeDefaultRate, 2) }}%</b></div><div>=</div><div class="aat-calc-cell">Agency Fee<b>&#8377; {{ number_format(1200000 * $aatFulltimeDefaultRate / 100) }}</b></div></div></div>
      <div class="aat-calc" data-aat-fixed-only><h5>Example Calculation</h5><div class="aat-calc-row simple"><div class="aat-calc-cell">Fixed Fee<b>{{ $aatFulltimeFixedLabel }}</b></div><div>=</div><div class="aat-calc-cell">Agency Fee<b>{{ $aatFulltimeFixedLabel }}</b></div></div></div>
    </section>

    <section class="aat-edit-section" data-agency-section="fulltime">
      <div class="aat-edit-section-head">
        <div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m8 15 3-4 3 2 4-6"/></svg></span><div><h4>Experience-Based Pricing</h4><p data-aat-experience-pricing-copy>{{ $aatFulltimeFeeModel === 'fixed' ? 'Define different fixed fees for different experience bands.' : 'Define pricing using clear experience ranges.' }}</p></div></div>
      </div>
      <div class="aat-edit-grid">
        <div class="aat-range-table">
          <div class="aat-range-head"><span>Experience Band</span><span class="aat-standard-pricing-field">Currency</span><span class="aat-standard-pricing-field" data-aat-range-rate-label>{{ $aatFulltimeFeeModel === 'fixed' ? 'Fixed Fee Amount' : 'Rate / Percentage' }}</span><span class="aat-percentage-only">Applied On</span><span class="aat-fixed-only">Fee Charged On</span><span class="aat-fixed-only">Billing Frequency</span><span></span></div>
          @foreach($aatExperienceBands as $rangeIndex => $range)
            @php
              $experienceRow = $aatFulltimeExperiencePricing->get($rangeIndex, ['label' => '', 'rate' => '']);
              $rangeRate = data_get($experienceRow, 'rate', '');
              $rangeLabel = data_get($experienceRow, 'label', $range['label']);
            @endphp
            <div class="aat-range-row">
              <span class="aat-range-label">{{ $rangeLabel }}</span>
              <select class="aat-control aat-standard-pricing-field" name="fulltime_range_currency[{{ $rangeIndex }}]" data-aat-range-currency data-aat-row-index="{{ $rangeIndex }}">
                <option value="0" {{ $aatFixedFeeCurrency == 0 ? 'selected' : '' }}>INR</option>
                <option value="1" {{ $aatFixedFeeCurrency == 1 ? 'selected' : '' }}>EUR</option>
                <option value="2" {{ $aatFixedFeeCurrency == 2 ? 'selected' : '' }}>JPY</option>
                <option value="3" {{ $aatFixedFeeCurrency == 3 ? 'selected' : '' }}>GBP</option>
                <option value="4" {{ $aatFixedFeeCurrency == 4 ? 'selected' : '' }}>USD</option>
                <option value="5" {{ $aatFixedFeeCurrency == 5 ? 'selected' : '' }}>AED</option>
              </select>
              <div class="aat-rate-wrap aat-standard-pricing-field">
                <input type="number" step="0.01" min="0" class="aat-control" name="fulltime_range_rate[{{ $rangeIndex }}]" value="{{ $rangeRate }}" placeholder="{{ $aatFulltimeFeeModel === 'fixed' ? 'Fixed fee amount' : 'Percentage' }}" data-aat-row-rate-input data-aat-row-index="{{ $rangeIndex }}">
                <span class="aat-rate-suffix" data-aat-rate-suffix>{{ $aatFulltimeFeeModel === 'fixed' ? '' : '%' }}</span>
              </div>
              <select class="aat-control aat-percentage-only" name="fulltime_range_applied_on[{{ $rangeIndex }}]" data-aat-range-applied-on data-aat-row-index="{{ $rangeIndex }}">
                <option value="Annual Fixed CTC" {{ (@$aatTerms->fulltime_basis === 'Annual Fixed CTC' || @$aatTerms->fulltime_basis === 'free_replacement') ? 'selected' : '' }}>Annual CTC</option>
                <option value="Monthly CTC" {{ @$aatTerms->fulltime_basis === 'Monthly CTC' ? 'selected' : '' }}>Monthly</option>
                <option value="Hourly Rate" {{ @$aatTerms->fulltime_basis === 'Hourly Rate' ? 'selected' : '' }}>Hourly</option>
              </select>
              <select class="aat-control aat-fixed-only" name="fulltime_range_fixed_trigger[{{ $rangeIndex }}]" data-aat-range-fixed-trigger data-aat-row-index="{{ $rangeIndex }}">
                <option value="Per Hire" {{ (@$aatTerms->fulltime_payment_timeline_from === 'Per Hire' || $aatFulltimeFixedUnit === 'Per Hire') ? 'selected' : '' }}>Per Successful Hire</option>
                <option value="Per Joining" {{ (@$aatTerms->fulltime_payment_timeline_from === 'Per Joining' || $aatFulltimeFixedUnit === 'Per Joining') ? 'selected' : '' }}>Per Joining</option>
              </select>
              <select class="aat-control aat-fixed-only" name="fulltime_range_fixed_frequency[{{ $rangeIndex }}]" data-aat-range-fixed-frequency data-aat-row-index="{{ $rangeIndex }}">
                <option value="One Time" {{ (@$aatTerms->fulltime_fixed_fee_unit === 'One Time' || @$aatTerms->fulltime_fixed_fee_unit === 'Per Hire') ? 'selected' : '' }}>One Time</option>
                <option value="Monthly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="Weekly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="Quarterly" {{ @$aatTerms->fulltime_fixed_fee_unit === 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
              </select>
              <button type="button" class="aat-range-reset" data-aat-range-reset data-aat-row-index="{{ $rangeIndex }}" title="Reset to default" aria-label="Reset {{ $range['label'] }}">&#8634;</button>
              <input type="hidden" name="fulltime_experience_label[]" value="{{ $rangeLabel }}" data-aat-row-label-input data-aat-row-index="{{ $rangeIndex }}">
              <input type="hidden" name="fulltime_experience_rate[]" value="{{ $rangeRate }}" data-aat-row-rate-hidden-input data-aat-row-index="{{ $rangeIndex }}">
            </div>
          @endforeach
        </div>
        <div class="aat-info blue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><span data-aat-experience-info>Each range is saved through the existing experience-rate structure. Applied On and Currency remain consistent across the pricing setup.</span></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="fulltime">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/></svg></span><div><h4>Replacement Policy</h4><p>Define the replacement terms applicable for placements made under this agreement.</p></div></div></div>
      <div class="aat-edit-grid aat-policy-card">
        <div><label class="aat-label" data-aat-policy-period-label>Guarantee Period <span class="req">*</span></label><div class="aat-input-suffix"><input name="fulltime_replacement_period_days" type="number" value="{{ $aatReplacementPeriodDays }}" data-aat-replacement-period><span data-aat-policy-period-suffix>Days</span></div><select name="fulltime_replacement_from" style="margin-top:8px;"><option value="From candidate joining date" {{ (@$aatTerms->fulltime_replacement_from ?: 'From candidate joining date') === 'From candidate joining date' ? 'selected' : '' }}>From candidate joining date</option><option value="From invoice date" {{ @$aatTerms->fulltime_replacement_from === 'From invoice date' ? 'selected' : '' }}>From invoice date</option></select><p class="aat-help">Period during which replacement applies.</p></div>
        <div><label class="aat-label">Replacement Type</label><select name="fulltime_basis" class="aat-control" data-aat-replacement-type><option value="free_replacement" {{ (@$aatTerms->fulltime_basis === 'free_replacement' || @$aatTerms->fulltime_basis === 'Annual Fixed CTC') ? 'selected' : '' }}>Free Replacement</option><option value="credit_note" {{ @$aatTerms->fulltime_basis === 'credit_note' ? 'selected' : '' }}>Credit Note</option><option value="none" {{ @$aatTerms->fulltime_basis === 'none' ? 'selected' : '' }}>No Replacement</option></select><p class="aat-help">The remedy offered to the client.</p></div>
        <div><label class="aat-label">Replacement Eligibility</label><select name="fulltime_invoice_raising" class="aat-control" data-aat-replacement-eligibility><option value="After candidate joins" {{ (@$aatTerms->fulltime_invoice_raising === 'After candidate joins' || !@$aatTerms->fulltime_invoice_raising) ? 'selected' : '' }}>Candidate Resignation / Early Exit</option><option value="After offer acceptance" {{ @$aatTerms->fulltime_invoice_raising === 'After offer acceptance' ? 'selected' : '' }}>Employer Termination</option></select><p class="aat-help">The event that makes a replacement eligible.</p></div>
        <div class="aat-policy-note"><span aria-hidden="true">i</span><div>A <b id="aatReplacementSummary">free replacement</b> applies when an eligible exit occurs within <b id="aatReplacementDays">{{ $aatReplacementPeriodDays }}</b> <span data-aat-policy-period-suffix>days</span>.</div></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="fulltime">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><h4>Payment Timeline</h4><p>Define the invoice cadence and the period agencies have to settle payment.</p></div></div></div>
      <div class="aat-edit-grid aat-payment-card">
        <div><label class="aat-label">Billing Frequency</label><select name="fulltime_payment_timeline_from" class="aat-control" data-aat-billing-frequency><option value="Monthly" {{ @$aatTerms->fulltime_payment_timeline_from === 'Monthly' ? 'selected' : '' }}>Monthly</option><option value="One Time" {{ (@$aatTerms->fulltime_payment_timeline_from ?: 'From invoice date') === 'One Time' ? 'selected' : '' }}>One Time</option><option value="Weekly" {{ @$aatTerms->fulltime_payment_timeline_from === 'Weekly' ? 'selected' : '' }}>Weekly</option><option value="Quarterly" {{ @$aatTerms->fulltime_payment_timeline_from === 'Quarterly' ? 'selected' : '' }}>Quarterly</option></select><p class="aat-help">How often invoices are raised.</p></div>
        <div><label class="aat-label">Billing Trigger</label><select name="fulltime_notice_period" class="aat-control" data-aat-billing-trigger><option value="candidate_joined" {{ (@$aatTerms->fulltime_notice_period === 'candidate_joined' || @$aatTerms->fulltime_notice_period === 'As per client notice period') ? 'selected' : '' }}>Candidate Joined</option><option value="offer_accepted" {{ @$aatTerms->fulltime_notice_period === 'offer_accepted' ? 'selected' : '' }}>Offer Accepted</option><option value="invoice_raised" {{ @$aatTerms->fulltime_notice_period === 'invoice_raised' ? 'selected' : '' }}>Invoice Raised</option></select><p class="aat-help">The event that starts invoicing.</p></div>
        <div><label class="aat-label">Payment Period <span class="req">*</span></label><select name="fulltime_payment_terms_days" class="aat-control" data-aat-payment-terms><option value="0" {{ $aatPaymentTerms === 0 ? 'selected' : '' }}>Due Immediately</option><option value="15" {{ $aatPaymentTerms === 15 ? 'selected' : '' }}>15 Days</option><option value="30" {{ $aatPaymentTerms === 30 ? 'selected' : '' }}>30 Days</option><option value="45" {{ $aatPaymentTerms === 45 ? 'selected' : '' }}>45 Days</option><option value="60" {{ $aatPaymentTerms === 60 ? 'selected' : '' }}>60 Days</option><option value="90" {{ $aatPaymentTerms === 90 ? 'selected' : '' }}>90 Days</option><option value="120" {{ $aatPaymentTerms === 120 ? 'selected' : '' }}>120 Days</option></select><p class="aat-help">Time allowed for payment.</p></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="fulltime">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M9 11h6M9 15h4"/></svg></span><div><h4>Optional Commercial Settings</h4><p>Additional fields visible only in Full-Time mode.</p></div></div></div>
      <div class="aat-edit-grid">
        <div class="aat-field"><label>Notice Period <span class="aat-help-inline">(If any)</span></label><select name="fulltime_basis_replacement" disabled><option>As per client notice period</option></select><p class="aat-help">Reserved for the next release.</p></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="contract">
      <div class="aat-edit-section-head">
        <div class="aat-edit-section-title"><span class="aat-avatar orange"><svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span><div><h4>Contract Hiring Commercial Terms</h4><p>Configure terms applicable for contract / temporary staffing.</p></div></div>
      </div>
      <div class="aat-edit-grid">
        <div class="aat-field"><label>Pricing Model</label><select name="contract_billing_model"><option value="monthly" {{ (@$aatTerms->contract_billing_model ?: 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly Rate</option><option value="hourly" {{ @$aatTerms->contract_billing_model === 'hourly' ? 'selected' : '' }}>Hourly Rate</option></select></div>
        <div class="aat-field"><label>Agency Rate Type <span class="req">*</span></label><select name="contract_agency_rate_type"><option value="Fixed Rate" {{ (@$aatTerms->contract_agency_rate_type ?: 'Fixed Rate') === 'Fixed Rate' ? 'selected' : '' }}>Fixed Rate</option><option value="Markup" {{ @$aatTerms->contract_agency_rate_type === 'Markup' ? 'selected' : '' }}>Markup</option></select></div>
        <div class="aat-field"><label>Default Agency Rate <span class="req">*</span></label><input name="contract_rate_display" type="text" value="{{ $aatContractRateDisplay }}" placeholder="Depend upon job requirements"><input name="contract_rate_currency" type="hidden" value="{{ @$aatTerms->contract_rate_currency ?? 4 }}"><input name="contract_rate_amount" type="hidden" value="{{ @$aatTerms->contract_rate_amount ?? 80000 }}"></div>
        <div class="aat-field"><label>Billing Cycle <span class="req">*</span></label><select name="contract_billing_cycle"><option value="Monthly in Advance" {{ (@$aatTerms->contract_billing_cycle ?: 'Monthly in Advance') === 'Monthly in Advance' ? 'selected' : '' }}>Monthly in Advance</option><option value="Monthly in Arrears" {{ @$aatTerms->contract_billing_cycle === 'Monthly in Arrears' ? 'selected' : '' }}>Monthly in Arrears</option></select></div>
        <div class="aat-field"><label>Payment Period <span class="req">*</span></label><select name="contract_payment_terms_days"><option value="0" {{ $aatContractPaymentTermsDays === 0 ? 'selected' : '' }}>Due Immediately</option><option value="15" {{ $aatContractPaymentTermsDays === 15 ? 'selected' : '' }}>15 Days</option><option value="30" {{ $aatContractPaymentTermsDays === 30 ? 'selected' : '' }}>30 Days</option><option value="45" {{ $aatContractPaymentTermsDays === 45 ? 'selected' : '' }}>45 Days</option><option value="60" {{ $aatContractPaymentTermsDays === 60 ? 'selected' : '' }}>60 Days</option><option value="90" {{ $aatContractPaymentTermsDays === 90 ? 'selected' : '' }}>90 Days</option></select></div>
        <div class="aat-field"><label>Minimum Contract Duration <span class="req">*</span></label><div class="aat-input-suffix"><input name="contract_min_duration_months" type="number" value="{{ $aatContractMinDuration }}"><span>Months</span></div></div>
        <div class="aat-field"><label>Overtime / Additional Hours</label><select name="contract_overtime_policy"><option value="As per client terms" {{ (@$aatTerms->contract_overtime_policy ?: 'As per client terms') === 'As per client terms' ? 'selected' : '' }}>As per client terms</option><option value="Not applicable" {{ @$aatTerms->contract_overtime_policy === 'Not applicable' ? 'selected' : '' }}>Not applicable</option></select></div>
        <div class="aat-field"><label>Replacement Policy <span class="req">*</span></label><select name="contract_replacement_policy"><option value="As per contract terms" {{ (@$aatTerms->contract_replacement_policy ?: 'As per contract terms') === 'As per contract terms' ? 'selected' : '' }}>As per contract terms</option><option value="No replacement" {{ @$aatTerms->contract_replacement_policy === 'No replacement' ? 'selected' : '' }}>No replacement</option></select></div>
        <div class="aat-field"><label>Early Termination</label><select name="contract_early_termination"><option value="As per contract terms" {{ (@$aatTerms->contract_early_termination ?: 'As per contract terms') === 'As per contract terms' ? 'selected' : '' }}>As per contract terms</option><option value="30 days notice" {{ @$aatTerms->contract_early_termination === '30 days notice' ? 'selected' : '' }}>30 days notice</option></select></div>
        <div class="aat-field"><label>Invoice Raising</label><select name="contract_invoice_raising_contract"><option value="Monthly in Advance" {{ (@$aatTerms->contract_invoice_raising ?: 'Monthly in Advance') === 'Monthly in Advance' ? 'selected' : '' }}>Monthly in Advance</option><option value="Monthly in Arrears" {{ @$aatTerms->contract_invoice_raising === 'Monthly in Arrears' ? 'selected' : '' }}>Monthly in Arrears</option></select></div>
        <div class="aat-field"><label>Escalation / Price Revision</label><select name="contract_escalation_policy"><option value="As per contract terms" {{ (@$aatTerms->contract_escalation_policy ?: 'As per contract terms') === 'As per contract terms' ? 'selected' : '' }}>As per contract terms</option><option value="Annual revision" {{ @$aatTerms->contract_escalation_policy === 'Annual revision' ? 'selected' : '' }}>Annual revision</option></select></div>
        <div class="aat-calc orange"><h5>Example Calculation</h5><div class="aat-calc-row simple"><div class="aat-calc-cell">Default Agency Rate<b>{{ $aatContractRateDisplay }}</b></div><div>=</div><div class="aat-calc-cell">Final Agency Rate<b>Defined per job requirement</b></div></div></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="contract">
      <div class="aat-edit-section-head">
        <div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m8 15 3-4 3 2 4-6"/></svg></span><div><h4>Experience-Based Pricing</h4><p>Define contract agency commercial notes or rates by experience band. Country coverage is intentionally not included.</p></div></div>
      </div>
      <div class="aat-edit-grid">
        <div class="aat-range-table aat-contract-range-table">
          <div class="aat-range-head"><span>Experience Band</span><span class="aat-standard-pricing-field">Currency</span><span class="aat-standard-pricing-field">Agency Rate</span><span></span></div>
          @foreach($aatExperienceBands as $rangeIndex => $range)
            @php
              $experienceRow = $aatContractExperiencePricing->get($rangeIndex, ['label' => '', 'rate' => '']);
              $rangeRate = data_get($experienceRow, 'rate', '');
              $rangeLabel = data_get($experienceRow, 'label', $range['label']);
            @endphp
            <div class="aat-range-row">
              <span class="aat-range-label">{{ $rangeLabel }}</span>
              <select class="aat-control aat-standard-pricing-field" name="contract_range_currency[{{ $rangeIndex }}]" data-aat-row-index="{{ $rangeIndex }}">
                <option value="0" {{ $aatFixedFeeCurrency == 0 ? 'selected' : '' }}>INR</option>
                <option value="1" {{ $aatFixedFeeCurrency == 1 ? 'selected' : '' }}>EUR</option>
                <option value="2" {{ $aatFixedFeeCurrency == 2 ? 'selected' : '' }}>JPY</option>
                <option value="3" {{ $aatFixedFeeCurrency == 3 ? 'selected' : '' }}>GBP</option>
                <option value="4" {{ $aatFixedFeeCurrency == 4 ? 'selected' : '' }}>USD</option>
                <option value="5" {{ $aatFixedFeeCurrency == 5 ? 'selected' : '' }}>AED</option>
              </select>
              <div class="aat-rate-wrap aat-standard-pricing-field">
                <input type="text" class="aat-control" name="contract_range_rate[{{ $rangeIndex }}]" value="{{ $rangeRate }}" placeholder="Depend upon job requirements" data-aat-row-index="{{ $rangeIndex }}">
              </div>
              <button type="button" class="aat-range-reset" data-aat-contract-range-reset data-aat-row-index="{{ $rangeIndex }}" title="Reset to default" aria-label="Reset {{ $range['label'] }}">&#8634;</button>
              <input type="hidden" name="contract_experience_label[]" value="{{ $rangeLabel }}" data-aat-contract-row-label-input data-aat-row-index="{{ $rangeIndex }}">
              <input type="hidden" name="contract_experience_rate[]" value="{{ $rangeRate }}" data-aat-contract-row-rate-hidden-input data-aat-row-index="{{ $rangeIndex }}">
            </div>
          @endforeach
        </div>
        <div class="aat-info blue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><span>Each contract band uses the existing experience-rate structure. Currency stays editable per row.</span></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="contract">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/></svg></span><div><h4>Replacement Policy</h4><p>Replacement eligibility specific to contract staffing.</p></div></div></div>
      <div class="aat-edit-grid aat-policy-card">
        <div><label class="aat-label">Guarantee Period <span class="req">*</span></label><div class="aat-input-suffix"><input name="contract_min_duration_months" type="number" value="{{ $aatContractMinDuration }}" data-aat-contract-replacement-period><span>Months</span></div><p class="aat-help">Period during which replacement applies.</p></div>
        <div><label class="aat-label">Replacement Type</label><select name="contract_replacement_policy" class="aat-control" data-aat-contract-replacement-type><option value="As per contract terms" {{ (@$aatTerms->contract_replacement_policy ?: 'As per contract terms') === 'As per contract terms' ? 'selected' : '' }}>As per contract terms</option><option value="No replacement" {{ @$aatTerms->contract_replacement_policy === 'No replacement' ? 'selected' : '' }}>No replacement</option></select><p class="aat-help">The remedy offered to the client.</p></div>
        <div><label class="aat-label">Replacement Eligibility</label><select name="contract_overtime_policy" class="aat-control" data-aat-contract-replacement-eligibility><option value="As per client terms" {{ (@$aatTerms->contract_overtime_policy ?: 'As per client terms') === 'As per client terms' ? 'selected' : '' }}>Candidate Resignation / Early Exit</option><option value="Not applicable" {{ @$aatTerms->contract_overtime_policy === 'Not applicable' ? 'selected' : '' }}>Not applicable</option></select><p class="aat-help">The event that makes a replacement eligible.</p></div>
        <div class="aat-policy-note"><span aria-hidden="true">i</span><div>A <b id="aatContractReplacementSummary">replacement</b> applies when an eligible exit occurs within <b id="aatContractReplacementDays">{{ $aatContractMinDuration }}</b> months.</div></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="contract">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><h4>Payment Timeline</h4><p>Define how invoices are raised and the period agencies have to settle.</p></div></div></div>
      <div class="aat-edit-grid aat-payment-card">
        <div><label class="aat-label">Billing Frequency</label><select name="contract_billing_cycle" class="aat-control" data-aat-contract-billing-frequency><option value="Monthly in Advance" {{ (@$aatTerms->contract_billing_cycle ?: 'Monthly in Advance') === 'Monthly in Advance' ? 'selected' : '' }}>Monthly in Advance</option><option value="Monthly in Arrears" {{ @$aatTerms->contract_billing_cycle === 'Monthly in Arrears' ? 'selected' : '' }}>Monthly in Arrears</option><option value="One Time" {{ @$aatTerms->contract_billing_cycle === 'One Time' ? 'selected' : '' }}>One Time</option></select><p class="aat-help">How often invoices are raised.</p></div>
        <div><label class="aat-label">Billing Trigger</label><select name="contract_invoice_raising" class="aat-control" data-aat-contract-billing-trigger><option value="Monthly in Advance" {{ (@$aatTerms->contract_invoice_raising ?: 'Monthly in Advance') === 'Monthly in Advance' ? 'selected' : '' }}>Candidate Joined</option><option value="Monthly in Arrears" {{ @$aatTerms->contract_invoice_raising === 'Monthly in Arrears' ? 'selected' : '' }}>Offer Accepted</option></select><p class="aat-help">The event that starts invoicing.</p></div>
        <div><label class="aat-label">Payment Period <span class="req">*</span></label><select name="contract_payment_terms_days" class="aat-control" data-aat-contract-payment-terms><option value="0" {{ $aatContractPaymentTermsDays === 0 ? 'selected' : '' }}>Due Immediately</option><option value="15" {{ $aatContractPaymentTermsDays === 15 ? 'selected' : '' }}>15 Days</option><option value="30" {{ $aatContractPaymentTermsDays === 30 ? 'selected' : '' }}>30 Days</option><option value="45" {{ $aatContractPaymentTermsDays === 45 ? 'selected' : '' }}>45 Days</option><option value="60" {{ $aatContractPaymentTermsDays === 60 ? 'selected' : '' }}>60 Days</option><option value="90" {{ $aatContractPaymentTermsDays === 90 ? 'selected' : '' }}>90 Days</option></select><p class="aat-help">Time allowed for payment.</p></div>
      </div>
    </section>

    <section class="aat-edit-section" data-agency-section="general">
      <div class="aat-edit-section-head"><div class="aat-edit-section-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3"/></svg></span><div><h4>General Commercial Settings</h4><p>Settings applicable to both full-time and contract hiring.</p></div></div></div>
      <div class="aat-edit-grid">
        <div class="aat-field"><label>Currency <span class="req">*</span></label><select name="general_currency"><option value="0" {{ $aatGeneralCurrency == 0 ? 'selected' : '' }}>INR (&#8377;)</option><option value="1" {{ $aatGeneralCurrency == 1 ? 'selected' : '' }}>EUR (&#8364;)</option><option value="3" {{ $aatGeneralCurrency == 3 ? 'selected' : '' }}>GBP (&#163;)</option><option value="4" {{ $aatGeneralCurrency == 4 ? 'selected' : '' }}>USD ($)</option></select></div>
        <div class="aat-field"><label>Tax Handling <span class="req">*</span></label><select name="tax_handling"><option value="GST Extra" {{ (@$aatTerms->tax_handling ?: 'GST Extra') === 'GST Extra' ? 'selected' : '' }}>GST Extra</option><option value="Inclusive of Tax" {{ @$aatTerms->tax_handling === 'Inclusive of Tax' ? 'selected' : '' }}>Inclusive of Tax</option></select></div>
        <div class="aat-field"><label>TDS Deduction</label><select name="tds_deduction"><option value="As per applicable law" {{ (@$aatTerms->tds_deduction ?: 'As per applicable law') === 'As per applicable law' ? 'selected' : '' }}>As per applicable law</option><option value="Not applicable" {{ @$aatTerms->tds_deduction === 'Not applicable' ? 'selected' : '' }}>Not applicable</option></select></div>
      </div>
    </section>
  </div>
  <div class="aat-drawer-footer">
    <button type="button" class="aat-btn ghost" id="cancelAgencyCommercialTerms">Cancel</button>
    <div class="aat-footer-actions"><button type="button" class="aat-btn primary save" id="saveAgencyCommercialTerms">Save &amp; Update</button></div>
  </div>
</form>