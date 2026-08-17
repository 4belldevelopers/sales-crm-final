<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Service: AgencyCommercialTermsService
 *
 * Intent: Provide a lightweight, cache-first source for agency-facing commercial terms.
 * Tenancy: Every read/write is scoped by the service company's company_id.
 */
class AgencyCommercialTermsService
{
    private const CACHE_TTL = 3600;

    public function getForCompany(int $companyId): object
    {
        return Cache::remember($this->cacheKey($companyId), self::CACHE_TTL, function () use ($companyId) {
            $defaults = $this->defaults($companyId);

            if (! Schema::hasTable('agency_commercial_terms')) {
                return (object) $defaults;
            }

            $terms = DB::table('agency_commercial_terms')
                ->where('company_id', $companyId)
                ->first();

            $termsArray = (array) $terms;
            $settings = [];
            if (! empty($termsArray['settings_json'])) {
                $settings = json_decode((string) $termsArray['settings_json'], true) ?: [];
            }

            return (object) array_merge($defaults, $termsArray, $settings);
        });
    }

    public function upsertForCompany(int $companyId, array $payload): void
    {
        if (! Schema::hasTable('agency_commercial_terms')) {
            return;
        }
        if (! Schema::hasColumn('agency_commercial_terms', 'settings_json')) {
            unset($payload['settings_json']);
        }

        $payload['updated_at'] = now();
        $exists = DB::table('agency_commercial_terms')
            ->where('company_id', $companyId)
            ->exists();

        DB::table('agency_commercial_terms')->updateOrInsert(
            ['company_id' => $companyId],
            $exists ? $payload : array_merge($payload, ['created_at' => now()])
        );

        $this->flush($companyId);
    }

    public function flush(int $companyId): void
    {
        Cache::forget($this->cacheKey($companyId));
    }

    private function cacheKey(int $companyId): string
    {
        return "agency_commercial_terms:company:{$companyId}";
    }

    private function defaults(int $companyId): array
    {
        return [
            'company_id' => $companyId,
            'status' => 'active',
            'version' => '1.0',
            'fulltime_fee_model' => 'percentage',
            'fulltime_success_fee_percentage' => 8.33,
            'fulltime_fixed_fee_currency' => 4,
            'fulltime_fixed_fee_amount' => null,
            'fulltime_fixed_fee_unit' => 'Per Hire',
            'fulltime_experience_pricing' => [
                ['label' => '0 - 2 Years', 'rate' => 8.33],
                ['label' => '3 - 5 Years', 'rate' => 8.33],
                ['label' => '6 - 8 Years', 'rate' => 8.33],
                ['label' => '9+ Years', 'rate' => 8.33],
            ],
            'fulltime_payment_terms_days' => 30,
            'fulltime_replacement_period_days' => 90,
            'contract_billing_model' => 'monthly',
            'contract_rate_amount' => 80000,
            'contract_rate_currency' => 4,
            'contract_rate_display' => 'Depend upon job requirements',
            'contract_experience_pricing' => [
                ['label' => '0 - 2 Years', 'rate' => 'Depend upon job requirements'],
                ['label' => '3 - 5 Years', 'rate' => 'Depend upon job requirements'],
                ['label' => '6 - 8 Years', 'rate' => 'Depend upon job requirements'],
                ['label' => '9+ Years', 'rate' => 'Depend upon job requirements'],
            ],
            'contract_payment_terms_days' => 30,
            'contract_min_duration_months' => 3,
            'contract_replacement_policy' => 'As per Contract Terms',
            'fulltime_fee_cap_currency' => 4,
            'fulltime_fee_cap_amount' => null,
            'fulltime_payment_timeline_from' => 'From invoice date',
            'fulltime_replacement_from' => 'From candidate joining date',
            'fulltime_notice_period' => 'As per client notice period',
            'fulltime_basis' => 'Annual Fixed CTC',
            'fulltime_invoice_raising' => 'After candidate joins',
            'contract_agency_rate_type' => 'Fixed Rate',
            'contract_billing_cycle' => 'Monthly in Advance',
            'contract_overtime_policy' => 'As per client terms',
            'contract_early_termination' => 'As per contract terms',
            'contract_invoice_raising' => 'Monthly in Advance',
            'contract_escalation_policy' => 'As per contract terms',
            'payment_modes_allowed' => ['bank_transfer', 'neft', 'rtgs'],
            'general_currency' => 4,
            'tax_handling' => 'GST Extra',
            'tds_deduction' => 'As per applicable law',
            'round_off' => 'To Nearest Rupee',
            'applies_to' => 'All Agencies',
            'acceptance_required' => 1,
            'acceptance_trigger' => 'Before Candidate Submission',
            'submission_before_acceptance' => 0,
            'effective_from' => 'Agency Acceptance Date',
            'agreement_duration_months' => 12,
            'auto_renewal' => 1,
            'agency_can_reject' => 1,
            'updated_by_name' => null,
            'updated_at' => null,
        ];
    }
}
