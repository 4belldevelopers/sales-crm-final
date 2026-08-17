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

            return (object) array_merge($defaults, (array) $terms);
        });
    }

    public function upsertForCompany(int $companyId, array $payload): void
    {
        if (! Schema::hasTable('agency_commercial_terms')) {
            return;
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
            'fulltime_payment_terms_days' => 30,
            'fulltime_replacement_period_days' => 90,
            'contract_billing_model' => 'monthly',
            'contract_rate_amount' => 80000,
            'contract_rate_currency' => 4,
            'contract_payment_terms_days' => 30,
            'contract_min_duration_months' => 3,
            'contract_replacement_policy' => 'As per Contract Terms',
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
