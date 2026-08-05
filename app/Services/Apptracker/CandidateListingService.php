<?php

namespace App\Services\Apptracker;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\ReferenceDataCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * CandidateListingService — Phase A of the Step-16 Apptracker controller slice.
 *
 * Owns search query building, row map resolution, and Tagify filter parsing
 * for all 8 candidate listing tabs. The full ~7,000 LoC migration from
 * ServiceCompanyAppTrackerController is a subsequent sprint; resolveFilters()
 * is implemented here now to unblock dependent services.
 *
 * Migration status: resolveFilters() IMPLEMENTED; search methods are stubs
 * pending full test coverage (Step 18) before extraction.
 */
class CandidateListingService
{
    public function __construct(private readonly ElasticService $elasticService) {}

    /**
     * Build and execute the ES listing search for the "Similar Existing" tab.
     */
    public function similarExisting(Request $request, int $jobId, int $companyId): array
    {
        // TODO: extract from ServiceCompanyAppTrackerController::similar_existing_data_ajax()
        // Pending full Step 18 chaos-test coverage before safe extraction.
        throw new \LogicException('CandidateListingService::similarExisting() not yet migrated');
    }

    /**
     * Build and execute the ES listing search for the "Internal Bench" tab.
     */
    public function internalBench(Request $request, int $jobId, int $companyId): array
    {
        // TODO: extract from ServiceCompanyAppTrackerController::internal_bench_data_ajax()
        throw new \LogicException('CandidateListingService::internalBench() not yet migrated');
    }

    /**
     * Build and execute the ES listing search for the "Public Bench" (Agency Bench) tab.
     */
    public function publicBench(Request $request, int $jobId, int $companyId): array
    {
        // TODO: extract from ServiceCompanyAppTrackerController::public_bench_data_ajax()
        throw new \LogicException('CandidateListingService::publicBench() not yet migrated');
    }

    /**
     * Batch-resolve Tagify location and qualification filter arrays into IDs.
     *
     * @param  array  $locations      Already-decoded Tagify location chips (value format: "City|State")
     * @param  array  $qualifications Already-decoded Tagify qualification chips (value: qualification name)
     * @return array{city_ids: int[], qualification_ids: int[]}
     */
    public function resolveFilters(array $locations, array $qualifications): array
    {
        // Batch city→id resolution in one query; 5-min Redis cache per unique filter set.
        $cityIds = [];
        if (!empty($locations)) {
            $names = array_map(fn($s) => trim(explode('|', $s['value'])[0]), $locations);
            $cacheKey  = 'tagify:cities:' . md5(implode(',', $names));
            $cityIdMap = Cache::remember($cacheKey, 300, fn() =>
                DB::table('city_list')->whereIn('city', $names)->pluck('city_id', 'city')
            );
            foreach ($names as $name) {
                if (isset($cityIdMap[$name])) {
                    $cityIds[] = (int) $cityIdMap[$name];
                }
            }
        }

        // Qualification ID expansion via GeneralMethod (handles parent→child grouping)
        $qualIds = [];
        foreach ($qualifications as $qual) {
            $qRow = ReferenceDataCache::qualifications()->firstWhere('name', $qual['value'] ?? '');
            if ($qRow) {
                $qualIds = array_merge($qualIds, GeneralMethod::qualificationFilterCondition($qRow->id));
            }
        }

        return ['city_ids' => $cityIds, 'qualification_ids' => $qualIds];
    }
}
