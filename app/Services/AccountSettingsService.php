<?php

namespace App\Services;

use App\Repositories\AccountRepository;

/**
 * Service: AccountSettingsService
 *
 * Intent: Service layer for Account Settings V2. Orchestrates company tenant data
 * from AccountRepository and static master dropdowns from ReferenceDataCache.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class AccountSettingsService
{
    protected AccountRepository $accountRepository;

    protected ElasticService $elasticService;

    public function __construct(AccountRepository $accountRepository, ElasticService $elasticService)
    {
        $this->accountRepository = $accountRepository;
        $this->elasticService = $elasticService;
    }

    /**
     * Build combined page data for Account Settings view.
     *
     * Tenancy: Scoped strictly by $companyId.
     */
    public function getAccountPageData(int $companyId, array $options = []): array
    {
        $includeNotifications = $options['include_notifications'] ?? true;

        // 1. Get tenant-scoped company account data (cached in Redis)
        $tenantData = $this->accountRepository->getCompanyAccountSettings($companyId);

        // 2. Get static master reference data (cached in Redis 24h)
        $masterData = [
            'master_status' => ReferenceDataCache::masterStatuses(),
            'languagesArr' => ReferenceDataCache::masterLanguages(),
            'timezone' => ReferenceDataCache::masterTimezones(),
            'currencyArr' => ReferenceDataCache::masterCurrencies(),
            'time_formatArr' => ReferenceDataCache::masterTimeFormats(),
            'date_formatArr' => ReferenceDataCache::masterDateFormats(),
            'email_preferencesArr' => ReferenceDataCache::masterEmailPreferences(),
            'countries' => ReferenceDataCache::masterCountries(),
        ];

        // Intent: Commercials and Ads Settings are standalone tabs, so they can skip notification-only Elastic payloads.
        $masterData['master_notificationsArr'] = collect([]);
        $notificationData = collect([]);
        if ($includeNotifications) {
            $masterData['master_notificationsArr'] = ReferenceDataCache::masterNotificationsElastic($this->elasticService);

            // 3. Elastic company notifications (cached in Redis per company)
            $notificationData = \Illuminate\Support\Facades\Cache::remember(
                "account_notifications:company:{$companyId}",
                3600,
                function () use ($companyId) {
                    try {
                        $params = [
                            'index' => ELASTIC_MASTER_NOTIFICATIONS,
                            'body' => [
                                'size' => 10000,
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['company_id' => (int) $companyId]],
                                        ],
                                    ],
                                ],
                            ],
                        ];
                        $response = $this->elasticService->getClient()->search($params);

                        return collect($response['hits']['hits'] ?? [])
                            ->map(function ($hit) {
                                return (object) $hit['_source'];
                            })->keyBy('notifications_name');
                    } catch (\Exception $e) {
                        return collect([]);
                    }
                }
            );
        }

        return array_merge($tenantData, $masterData, ['notification' => $notificationData]);
    }

    /**
     * Invalidate tenant account cache when settings are updated.
     */
    public function flushAccountCache(int $companyId): void
    {
        $this->accountRepository->flushCompanyCache($companyId);
        \Illuminate\Support\Facades\Cache::forget("account_notifications:company:{$companyId}");
    }
}
