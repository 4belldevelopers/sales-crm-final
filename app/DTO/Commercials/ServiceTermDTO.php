<?php

namespace App\DTO\Commercials;

/**
 * ServiceTermDTO — Strongly-typed DTO representing a specific service's commercial terms.
 * Compliant with CQRS Read Model & Client-First Architecture Standard.
 *
 * Tenancy: Always strictly scoped to company_id.
 */
class ServiceTermDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $companyId,
        public readonly int $commtype,
        public readonly string $serviceType, // 'fulltime' | 'contract'
        public readonly int $status,
        public readonly int $rateType,
        public readonly string $rateTypeLabel,
        public readonly int $salMode,
        public readonly string $salModeLabel,
        public readonly int $salaryCurrency,
        public readonly string $currencyCode,
        public readonly int $replacementPeriodDays,
        public readonly string $replacementType,
        public readonly string $replacementTypeLabel,
        public readonly string $replacementEligibility,
        public readonly string $replacementEligibilityLabel,
        public readonly string $billingFrequency,
        public readonly string $billingFrequencyLabel,
        public readonly string $billingTrigger,
        public readonly string $billingTriggerLabel,
        public readonly int $paymentTermsDays,
        public readonly array $rates,
        public readonly array $countryIds,
        public readonly array $countryNames,
        public readonly int $countryCount
    ) {}

    public static function fromArray(array $data): self
    {
        $commtype = (int) ($data['commtype'] ?? 2);
        $serviceType = ($commtype === 2) ? 'fulltime' : 'contract';
        $rateType = (int) ($data['rateType'] ?? ($commtype === 2 ? 2 : 1));
        $salMode = (int) ($data['sal_mode'] ?? 0);
        $salaryCurrency = (int) ($data['salary_currency'] ?? 0);
        $countryIds = (array) ($data['commericial_countries'] ?? $data['country_ids'] ?? $data['countries'] ?? []);
        $countryNames = (array) ($data['country_names'] ?? []);
        $rates = (array) ($data['commericial_details'] ?? $data['rates'] ?? []);

        return new self(
            id: (int) ($data['id'] ?? 0),
            companyId: (int) ($data['company_id'] ?? 0),
            commtype: $commtype,
            serviceType: $serviceType,
            status: (int) ($data['status'] ?? 0),
            rateType: $rateType,
            rateTypeLabel: ($commtype === 2)
                ? ($rateType === 2 ? 'Percentage of CTC' : ($rateType === 1 ? 'Fixed Commercial Rate' : 'Free / No Fee'))
                : ($rateType === 1 ? 'Markup on Bill Rate' : 'Contract Commercial Rate'),
            salMode: $salMode,
            salModeLabel: ($salMode === 0) ? 'Annual Staffing' : (($salMode === 1) ? 'Monthly Staffing' : 'Hourly Staffing'),
            salaryCurrency: $salaryCurrency,
            currencyCode: (string) ($data['currency_code'] ?? 'INR'),
            replacementPeriodDays: (int) ($data['replacement_period_days'] ?? ($commtype === 2 ? 90 : 30)),
            replacementType: (string) ($data['replacement_type'] ?? 'free_replacement'),
            replacementTypeLabel: ucwords(str_replace('_', ' ', (string) ($data['replacement_type'] ?? 'free_replacement'))),
            replacementEligibility: (string) ($data['replacement_eligibility'] ?? 'early_exit'),
            replacementEligibilityLabel: ucwords(str_replace('_', ' ', (string) ($data['replacement_eligibility'] ?? 'early_exit'))),
            billingFrequency: (string) ($data['billing_frequency'] ?? ($commtype === 2 ? 'one_time' : 'monthly')),
            billingFrequencyLabel: ucwords(str_replace('_', ' ', (string) ($data['billing_frequency'] ?? ($commtype === 2 ? 'one_time' : 'monthly')))),
            billingTrigger: (string) ($data['billing_trigger'] ?? ($commtype === 2 ? 'candidate_joined' : 'approved_timesheet')),
            billingTriggerLabel: ucwords(str_replace('_', ' ', (string) ($data['billing_trigger'] ?? ($commtype === 2 ? 'candidate_joined' : 'approved_timesheet')))),
            paymentTermsDays: (int) ($data['payment_terms_days'] ?? 30),
            rates: $rates,
            countryIds: $countryIds,
            countryNames: $countryNames,
            countryCount: count($countryIds)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'commtype' => $this->commtype,
            'service_type' => $this->serviceType,
            'status' => $this->status,
            'rateType' => $this->rateType,
            'rate_type_label' => $this->rateTypeLabel,
            'sal_mode' => $this->salMode,
            'sal_mode_label' => $this->salModeLabel,
            'salary_currency' => $this->salaryCurrency,
            'currency_code' => $this->currencyCode,
            'replacement_period_days' => $this->replacementPeriodDays,
            'replacement_type' => $this->replacementType,
            'replacement_type_label' => $this->replacementTypeLabel,
            'replacement_eligibility' => $this->replacementEligibility,
            'replacement_eligibility_label' => $this->replacementEligibilityLabel,
            'billing_frequency' => $this->billingFrequency,
            'billing_frequency_label' => $this->billingFrequencyLabel,
            'billing_trigger' => $this->billingTrigger,
            'billing_trigger_label' => $this->billingTriggerLabel,
            'payment_terms_days' => $this->paymentTermsDays,
            'rates' => $this->rates,
            'country_ids' => $this->countryIds,
            'country_names' => $this->countryNames,
            'country_count' => $this->countryCount,
        ];
    }
}
