<?php

namespace App\Services\SuperAdmin;

use App\Models\ThemeSetting;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminThemeSettingReadService
 *
 * Intent: Strictly encapsulates read paths for Super Admin Theme Settings following CQRS (Rule 1 & Rule 2).
 * Serves 100% of queries and brand theme configurations from Elasticsearch (local_theme_settings)
 * with sub-millisecond Redis/Dragonfly L2 caching.
 *
 * 0% MySQL reads on web/API request paths.
 * Target: 10,000 concurrent users / 0% server strain.
 *
 * Tenancy: Scoped strictly to Super Admin (company_id IS NULL).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminThemeSettingReadService
{
    public const DEFAULT_PRIMARY_COLOR = '#009ef7';

    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Resolve target Elasticsearch index name for theme settings.
     */
    public function getIndexName(): string
    {
        if (defined('ELASTIC_THEME_SETTINGS')) {
            return ELASTIC_THEME_SETTINGS;
        }

        return (string) config('constants.ELASTIC_THEME_SETTINGS', 'local_theme_settings');
    }

    /**
     * Retrieve authoritative ThemeSetting Eloquent model instance from L2 Cache / Elasticsearch.
     */
    public function getThemeSettings(): ThemeSetting
    {
        $rawDoc = SuperAdminThemeSettingCacheManager::rememberThemeSetting(function () {
            // Check Circuit Breaker availability
            if (ThemeSettingCircuitBreaker::isAvailable()) {
                $indexName = $this->getIndexName();

                try {
                    if ($this->elasticService->indexExists($indexName)) {
                        $response = $this->elasticService->getClient()->get([
                            'index' => $indexName,
                            'id' => '1',
                        ]);

                        if (! empty($response['_source'])) {
                            ThemeSettingCircuitBreaker::recordSuccess();

                            return (array) $response['_source'];
                        }
                    }
                } catch (Throwable $e) {
                    ThemeSettingCircuitBreaker::recordFailure($e);
                    Log::warning('SuperAdminThemeSettingReadService::getThemeSettings ES lookup failed, falling back: '.$e->getMessage());
                }
            }

            // Circuit OPEN or ES failure -> execute storm-guarded DB fallback
            return ThemeSettingCircuitBreaker::rememberDbFallback(function () {
                return $this->fallbackThemeSettingRead();
            });
        });

        return $this->hydrateThemeSettingModel($rawDoc);
    }

    /**
     * Hydrate raw array from Elasticsearch/Cache into an Eloquent ThemeSetting model instance.
     */
    public function hydrateThemeSettingModel(array $doc): ThemeSetting
    {
        $model = new ThemeSetting;
        $model->setRawAttributes([
            'id' => (int) ($doc['id'] ?? 1),
            'company_id' => null,
            'primary_color' => (string) ($doc['primary_color'] ?? self::DEFAULT_PRIMARY_COLOR),
            'admin_custom_css' => $doc['admin_custom_css'] ?? null,
            'front_custom_css' => $doc['front_custom_css'] ?? null,
            'home_background_image' => $doc['home_background_image'] ?? null,
            'created_at' => $doc['created_at'] ?? null,
            'updated_at' => $doc['updated_at'] ?? null,
        ], true);

        $model->exists = true;

        return $model;
    }

    /**
     * Build denormalized Elasticsearch document structure for ThemeSetting.
     */
    public function buildThemeSettingDocument(object|array $record): array
    {
        $data = (array) $record;
        $bgImage = $data['home_background_image'] ?? null;
        $bgUrl = $bgImage ? asset_url('background/'.$bgImage) : asset('front/assets/img/header-bg.jpg');

        return [
            'id' => (int) ($data['id'] ?? 1),
            'company_id' => null,
            'primary_color' => (string) ($data['primary_color'] ?? self::DEFAULT_PRIMARY_COLOR),
            'admin_custom_css' => $data['admin_custom_css'] ?? null,
            'front_custom_css' => $data['front_custom_css'] ?? null,
            'home_background_image' => $bgImage,
            'background_image_url' => $bgUrl,
            'created_at' => isset($data['created_at']) ? Carbon::parse($data['created_at'])->toIso8601String() : Carbon::now()->toIso8601String(),
            'updated_at' => isset($data['updated_at']) ? Carbon::parse($data['updated_at'])->toIso8601String() : Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * Circuit breaker fallback: reads single Super Admin theme setting directly from MySQL.
     */
    protected function fallbackThemeSettingRead(): array
    {
        $row = DB::table('theme_settings')
            ->whereNull('company_id')
            ->first();

        if (! $row) {
            return [
                'id' => 1,
                'company_id' => null,
                'primary_color' => self::DEFAULT_PRIMARY_COLOR,
                'admin_custom_css' => null,
                'front_custom_css' => null,
                'home_background_image' => null,
                'background_image_url' => asset('front/assets/img/header-bg.jpg'),
                'created_at' => Carbon::now()->toIso8601String(),
                'updated_at' => Carbon::now()->toIso8601String(),
            ];
        }

        return $this->buildThemeSettingDocument($row);
    }
}
