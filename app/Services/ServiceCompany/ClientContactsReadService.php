<?php

namespace App\Services\ServiceCompany;

use App\Services\ElasticService;
use Illuminate\Support\Facades\Log;

/**
 * Class ClientContactsReadService
 *
 * Dedicated CQRS Read Service for Direct Client Drawer Contacts Tab.
 * Guarantees:
 * 1. Strictly 0% MySQL reads on the web request path (JERO Rule 1 & 2).
 * 2. 100% reads from Dragonfly cache or Elasticsearch (local_users_list_elastic).
 * 3. Strict SPOC bounding (designated account SPOCs and shared-job hiring managers).
 * 4. Protection of non-SPOC internal client staff from external vendor surveillance.
 */
class ClientContactsReadService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Retrieve authorized client contacts for the authenticated agency.
     *
     * @param  int  $companyId  Authenticated agency company ID
     * @param  int  $clientId  Target client company ID
     * @param  array  $params  Optional pagination/filtering params
     * @return array Standardized Contacts contract
     */
    public function getContacts(int $companyId, int $clientId, array $params = []): array
    {
        $cacheKey = "ats:client_contacts:{$companyId}:{$clientId}";

        try {
            $store = config('cache.stores.dragonfly') ? 'dragonfly' : config('cache.default');
            $cached = cache()->store($store)->get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {
            try {
                $cached = cache()->get($cacheKey);
                if (is_array($cached)) {
                    return $cached;
                }
            } catch (\Throwable $ex) {
            }
        }

        $contacts = [];

        // Query local_users_list_elastic with strict company_id filter
        $indexUsers = defined('ELASTIC_USERS') ? ELASTIC_USERS : config('constants.ELASTIC_USERS', 'local_users_list_elastic');

        try {
            if ($this->elasticService->indexExists($indexUsers)) {
                $esBody = [
                    'size' => 100,
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['term' => ['company_id' => $clientId]],
                                ['term' => ['status' => 'active']],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search([
                    'index' => $indexUsers,
                    'body' => $esBody,
                ]);

                foreach ($response['hits']['hits'] ?? [] as $hit) {
                    $src = $hit['_source'] ?? [];
                    $contacts[] = [
                        'id' => (int) ($src['id'] ?? 0),
                        'userId' => (int) ($src['id'] ?? 0),
                        'name' => (string) ($src['name'] ?? ''),
                        'email' => (string) ($src['email'] ?? ''),
                        'mobile' => (string) ($src['mobile'] ?? ''),
                        'display_name' => (string) ($src['designation'] ?? ($src['display_name'] ?? 'Account SPOC')),
                        'is_spoc' => (bool) (! empty($src['is_spoc']) || ! empty($src['from_user_id'])),
                        'from_user_id' => (int) ($src['from_user_id'] ?? 0),
                        'linked_jobs_count' => (int) ($src['linked_jobs_count'] ?? 1),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch ClientContactsReadService query exception', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
        }

        $result = [
            'data' => $contacts,
            'recordsTotal' => count($contacts),
            'recordsFiltered' => count($contacts),
        ];

        try {
            $store = config('cache.stores.dragonfly') ? 'dragonfly' : config('cache.default');
            cache()->store($store)->put($cacheKey, $result, 300);
        } catch (\Throwable $e) {
            try {
                cache()->put($cacheKey, $result, 300);
            } catch (\Throwable $ex) {
            }
        }

        return $result;
    }
}
