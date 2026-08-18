<?php

namespace App\Http\Resources;

use App\Helper\GeneralMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MarketJobV2Resource
 *
 * Intent: DTO transformer for Find Jobs V2 API endpoint. Transforms denormalized Elasticsearch
 * job hit documents into a compact, structured JSON payload (~6 KB per page of 10 items), replacing
 * heavy server-rendered HTML payloads with client-side DOM rendering.
 *
 * Added (2026-08-18): 6 new display fields for the 5-column card redesign:
 *   - req_id: formatted job reference ID (REQ-JERO-{id})
 *   - status_label: OPEN / HOLD / FILLING FAST badge text
 *   - updated_at_ago: human-readable time since last update
 *   - submission_deadline_formatted: end_date as human date
 *   - employment_type_badge: Full-Time / Contract
 *   - commercial_terms: summarised fee/terms object (pre-fetched via Redis, zero DB queries)
 *
 * Tenancy & Security: Company name kept in JSON for sort/internal use but not rendered in card UI.
 * Pre-calculated marketplace action buttons based on tenant company ID and status.
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

        // Location display (dynamically resolved from nested client_city_jobs in Elasticsearch)
        $clientCityJobs = $row->client_city_jobs ?? [];
        $locationDisplay = $workType;
        if (($row->work_type ?? '') !== 'yes' && is_array($clientCityJobs) && count($clientCityJobs) > 0) {
            $firstCity = $clientCityJobs[0] ?? [];
            $cityName = is_array($firstCity) ? ($firstCity['city_name'] ?? null) : ($firstCity->city_name ?? null);
            $countryName = is_array($firstCity) ? ($firstCity['country_name'] ?? null) : ($firstCity->country_name ?? null);
            if (! empty($cityName)) {
                $locationDisplay = trim(ucwords(mb_substr($cityName, 0, 16)).($countryName ? ', '.mb_substr($countryName, 0, 12) : ''), ', ');
            }
        }

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

        // Department / Category (dynamic from job_categories)
        $department = $row->v2_category_name ?? ($row->category_name ?? '');
        if (empty($department) && ! empty($row->category_id)) {
            $catId = (int) $row->category_id;
            $department = cache()->remember("ats:category_name:{$catId}", 86400, function () use ($catId) {
                return \Illuminate\Support\Facades\DB::table('job_categories')->where('id', $catId)->value('name') ?? '';
            });
        }

        // Company details — company name intentionally excluded from JSON response per privacy
        // requirement (Q4 decision: completely hidden from agencies viewing the marketplace).
        $companySizeFrom = $row->company_size_from ?? 0;
        $companySize = $row->company_size ?? 0;
        $industryName = $row->industry_name ?? '';

        // Bench candidate match count (Pre-fetched via V2 Service 0 DB queries, with fallback for isolated calls)
        $benchMatchCount = 0;
        if (auth()->check()) {
            if (isset($row->v2_bench_match_count)) {
                $benchMatchCount = (int) $row->v2_bench_match_count;
            } else {
                try {
                    $benchMatchCount = (int) GeneralMethod::benchCandidateCountSkillBase($row->id);
                } catch (\Throwable $e) {
                    $benchMatchCount = 0;
                }
            }
        }

        // ── NEW FIELDS for 5-column card design (2026-08-18) ──────────────────────────

        // REQ-ID: formatted job reference badge string
        $reqId = 'REQ-JERO-'.$row->id;

        // Status label for the card badge — OPEN / HOLD only (FILLING FAST not required per design decision)
        $rawStatus = strtolower($row->status ?? 'active');
        $statusLabel = match ($rawStatus) {
            'active' => 'OPEN',
            'hold' => 'HOLD',
            default => strtoupper($rawStatus),
        };

        // Employment type badge label (0: Full-Time, 1: Contract, 2: Freelance, 3: Part-Time)
        $empTypeInt = (int) ($row->employment_type ?? 0);
        $employmentTypeBadge = match ($empTypeInt) {
            1 => 'Contract',
            2 => 'Freelance',
            3 => 'Part-Time',
            default => 'Full-Time',
        };

        // Last Updated time-ago (ES has updated_at; fall back to created_at if absent)
        $updatedAt = $row->updated_at ?? $createdAt;
        $updatedAtAgo = GeneralMethod::timeAgo($updatedAt);

        // Submission Deadline — maps to end_date in ES (the job's closing date)
        $submissionDeadlineRaw = $row->end_date ?? null;
        $submissionDeadlineFormatted = $submissionDeadlineRaw
            ? date('d M Y', strtotime($submissionDeadlineRaw))
            : null;

        // Commercial Terms — pre-fetched via MarketJobV2Service::getCommercialTermsBulk() and
        // attached as v2_commercial_terms. Fallback to null if not available (drawer still works).
        $commercialTerms = $row->v2_commercial_terms ?? null;

        // ── END NEW FIELDS ──────────────────────────────────────────────────────────────

        return [
            'id' => (int) $row->id,
            'title' => $row->job_title ?? $row->title ?? '',
            'title_short' => ucwords(mb_substr($row->job_title ?? $row->title ?? '', 0, 25)),
            'department' => $department,
            'company_id' => (int) ($row->comId ?? 0),
            // client_name intentionally omitted — hidden from all agency-facing views (Q4)
            'company_size_from' => (int) $companySizeFrom,
            'company_size' => (int) $companySize,
            'industry_name' => $industryName,
            'min_exp' => (int) ($row->min_exp ?? 0),
            'max_exp' => (int) ($row->max_exp ?? 0),
            'total_positions' => (int) ($row->total_positions ?? 1),
            'work_type' => $workType,
            'location_display' => $locationDisplay,
            'employment_type' => $employmentTypeBadge,
            'public_market_status' => $publicMarketStatus,
            'created_at_formatted' => "{$formattedDate} ({$daysAgo})",
            'created_at_date' => $formattedDate,
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
            // ── New 5-column card fields ──
            'req_id' => $reqId,
            'status_label' => $statusLabel,
            'employment_type_badge' => $employmentTypeBadge,
            'updated_at_ago' => $updatedAtAgo,
            'submission_deadline_formatted' => $submissionDeadlineFormatted,
            'commercial_terms' => $commercialTerms,
        ];
    }
}
