<?php

namespace App\Http\Resources;

use App\Helper\GeneralMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MarketJobV2Resource
 *
 * Intent: DTO transformer for Find Jobs V2 API endpoint. Transforms denormalized Elasticsearch
 * job hit documents into a compact, structured JSON payload (~5 KB per page of 20 items), replacing
 * heavy server-rendered HTML payloads with client-side DOM rendering.
 *
 * Tenancy & Security: Pre-calculated marketplace action buttons based on tenant company ID and status.
 */
class MarketJobV2Resource extends JsonResource
{
    /**
     * Transform the job resource into an array.
     */
    public function toArray(Request $request): array
    {
        $row = (object) $this->resource;
        $companyId = auth()->user()->company_id ?? 0;

        $targetJobId = $row->id;
        $relation = $row->v2_relation ?? (object) [];

        $fromCompanyId = $row->v2_from_company_id ?? ($relation->from_company_id ?? null);
        $toCompanyId = $row->v2_to_company_id ?? ($relation->to_company_id ?? null);
        $recommendationRelationId = $row->v2_recommendation_id ?? ($relation->id ?? null);
        $finalStatus = $row->v2_final_status ?? ($relation->final_status ?? null);

        // Salary formatting
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbols = GeneralMethod::currencySymbole();
        $currencies = GeneralMethod::currency();

        $mod = $request->input('sal_mode') ?? ($row->max_sal_mode ?? 0);
        $cur = $request->input('min_salary_currency') ?? ($row->max_salary_currency ?? 0);

        $currencyCode = $currencies[$cur] ?? 'INR';
        $currencySymbol = $currencySymbols[$cur] ?? '₹';

        $convertedEctc = GeneralMethod::conversionRates($currencyCode, $row->max_yearly ?? 0, $mod);
        $convertedCtc = GeneralMethod::conversionRates($currencyCode, $row->min_yearly ?? 0, $mod);

        $minSalFormatted = GeneralMethod::formatK($convertedCtc[$mod] ?? 0);
        $maxSalFormatted = GeneralMethod::formatK($convertedEctc[$mod] ?? 0);
        $salModeShort = mb_substr($salaryMode[$mod] ?? 'Annual', 0, 1);

        // Work type text
        $workType = match ($row->work_type ?? '') {
            'yes' => 'Remote',
            'other' => 'Hybrid',
            default => 'Work From Office',
        };

        // Skill tags extraction
        $rawSkills = $row->job_metronics_skills ?? [];
        $skillNames = [];
        if (is_array($rawSkills)) {
            foreach ($rawSkills as $s) {
                $name = is_array($s) ? ($s['skill_name'] ?? '') : ($s->skill_name ?? '');
                if (! empty($name)) {
                    $skillNames[] = ucfirst($name);
                }
            }
        }

        $displaySkills = array_slice($skillNames, 0, 5);
        $remainingSkillsCount = count($skillNames) > 5 ? count($skillNames) - 5 : 0;

        // Action type calculation
        $vendorCount = (int) ($row->vendor_added_count ?? 0);
        $exceededLimit = 20;
        $publicMarketStatus = (int) ($row->public_market_status ?? 0);

        $actionType = 'send_bench_request';
        if ($publicMarketStatus === 2) {
            if (($vendorCount >= $exceededLimit || $publicMarketStatus === 6) && ($toCompanyId != $companyId)) {
                $actionType = 'vendor_fulfilled';
            } elseif (empty($recommendationRelationId) || $finalStatus == 2 || $finalStatus == 0) {
                $actionType = 'apply_agency';
            } elseif ($publicMarketStatus === 3) {
                $actionType = 'reject';
            } elseif ($publicMarketStatus === 1) {
                $actionType = 'under_review';
            } elseif ($publicMarketStatus === 4) {
                $actionType = 'vendor_limit';
            } elseif ($toCompanyId == $companyId) {
                $actionType = 'view_job';
            }
        }

        // Days ago string
        $createdAt = $row->created_at ?? date('Y-m-d H:i:s');
        $formattedDate = date('d M, Y', strtotime($createdAt));
        $daysAgo = GeneralMethod::timeAgo($createdAt);

        // Department / Category
        $department = $row->category_name ?? '';

        // Company Details
        $companyName = $row->client_name ?? '';
        $companySizeFrom = $row->company_size_from ?? 0;
        $companySize = $row->company_size ?? 0;
        $industryName = $row->industry_name ?? '';

        // Bench candidate match count
        $benchMatchCount = 0;
        if (auth()->check()) {
            try {
                $benchMatchCount = (int) GeneralMethod::benchCandidateCountSkillBase($row->id);
            } catch (\Throwable $e) {
                $benchMatchCount = 0;
            }
        }

        return [
            'id' => (int) $row->id,
            'title' => $row->title ?? '',
            'title_short' => ucwords(mb_substr($row->title ?? '', 0, 25)),
            'department' => $department,
            'company_id' => (int) ($row->comId ?? 0),
            'client_name' => $companyName,
            'company_size_from' => (int) $companySizeFrom,
            'company_size' => (int) $companySize,
            'industry_name' => $industryName,
            'min_exp' => (int) ($row->min_exp ?? 0),
            'max_exp' => (int) ($row->max_exp ?? 0),
            'total_positions' => (int) ($row->total_positions ?? 1),
            'work_type' => $workType,
            'employment_type' => ((int) ($row->employment_type ?? 0)) === 1 ? 'Contract' : 'Full-time',
            'public_market_status' => $publicMarketStatus,
            'created_at_formatted' => "{$formattedDate} ({$daysAgo})",
            'salary_currency' => $currencySymbol,
            'min_salary' => $minSalFormatted,
            'max_salary' => $maxSalFormatted,
            'salary_mode_short' => $salModeShort,
            'skills' => $displaySkills,
            'remaining_skills_count' => $remainingSkillsCount,
            'bench_match_count' => (int) $benchMatchCount,
            'vendor_added_count' => $vendorCount,
            'recommendation_relation_id' => $recommendationRelationId,
            'final_status' => $finalStatus,
            'to_company_id' => $toCompanyId,
            'action_type' => $actionType,
            'view_job_url' => $toCompanyId == $companyId ? url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/all') : null,
            'mod' => $mod,
            'cur' => $cur,
        ];
    }
}
