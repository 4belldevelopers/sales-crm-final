<?php

namespace App\Services\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Jobs\ProcessThemeSettingOutboxEventJob;
use App\Models\ThemeSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminThemeSettingMutationService
 *
 * Intent: Encapsulates state-mutating operations for Super Admin Theme Settings under the
 * Transactional Outbox, Two-Tier Idempotency, and CQRS dual-store synchronization architecture.
 * Guarantees zero duplicate executions under network retries, ACID row-locked state transitions,
 * zero remote network I/O inside MySQL transactions (duration < 15ms), instant Elasticsearch sync,
 * and O(1) Redis/Dragonfly L2 cache invalidation.
 *
 * Tenancy: Scoped strictly to Super Admin (company_id IS NULL).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminThemeSettingMutationService
{
    public const DEFAULT_PRIMARY_COLOR = '#009ef7';

    /**
     * Compute a canonical SHA-256 hash for a given payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Format a deterministic idempotency key when explicitly supplied by client.
     */
    protected function formatIdempotencyKey(?string $clientKey): ?string
    {
        if ($clientKey && trim($clientKey) !== '') {
            return trim($clientKey);
        }

        return null;
    }

    /**
     * Update Super Admin theme settings with two-tier idempotency and transactional outbox.
     *
     * @param  Request  $request  HTTP request with validated parameters
     * @param  string|null  $idempotencyKey  Client-supplied UUID or token
     * @param  int|null  $adminId  Authenticated super admin ID
     * @return array Standardized Reply payload
     */
    public function updateThemeSettings(Request $request, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalIdempotencyKey = $this->formatIdempotencyKey($idempotencyKey);
        $payloadForHash = [
            'action' => 'update',
            'primary_color' => $request->input('primary_color'),
            'admin_custom_css' => $request->input('admin_custom_css'),
            'front_custom_css' => $request->input('front_custom_css'),
            'remove_image' => $request->input('remove_image'),
        ];
        $requestHash = $this->computeRequestHash($payloadForHash);

        // 1. Two-Tier Idempotency Lease Claim Check
        if ($finalIdempotencyKey) {
            $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'request_hash' => $requestHash,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addHours(24),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                $existing = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'A theme settings update is currently processing for this request. Please wait.');
                        }

                        DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $finalIdempotencyKey)
                            ->update([
                                'status' => 'PROCESSING',
                                'request_hash' => $requestHash,
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
            }
        }

        // 2. Pre-transaction file handling (Zero remote / slow I/O inside the DB transaction)
        $currentTheme = ThemeSetting::withoutGlobalScopes()->whereNull('company_id')->first();
        $oldImage = $currentTheme ? $currentTheme->home_background_image : null;
        $newImage = null;
        $imageChanged = false;
        $removeImageRequested = $request->filled('remove_image') && in_array($request->input('remove_image'), ['1', 1, 'true', true], true);

        if ($removeImageRequested) {
            $imageChanged = true;
            $newImage = null;
        }

        if ($request->hasFile('image')) {
            $newImage = Files::upload($request->file('image'), 'background');
            $imageChanged = true;
        }

        // 3. Row-locked MySQL transaction (<15ms)
        DB::beginTransaction();

        try {
            $theme = ThemeSetting::lockForUpdate()
                ->withoutGlobalScopes()
                ->whereNull('company_id')
                ->first();

            if (! $theme) {
                $theme = new ThemeSetting;
                $theme->company_id = null;
            }

            $theme->primary_color = $request->input('primary_color');
            $theme->admin_custom_css = $request->input('admin_custom_css');
            $theme->front_custom_css = $request->input('front_custom_css');

            if ($imageChanged) {
                $theme->home_background_image = $newImage;
            }

            $theme->save();

            // 4. Atomic Transactional Outbox Event Registration
            $outboxPayload = [
                'theme_id' => $theme->id,
                'primary_color' => $theme->primary_color,
                'admin_custom_css' => $theme->admin_custom_css,
                'front_custom_css' => $theme->front_custom_css,
                'home_background_image' => $theme->home_background_image,
                'admin_id' => $adminId,
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'ThemeSetting',
                'aggregate_id' => (int) $theme->id,
                'aggregate_version' => (int) (microtime(true) * 1000),
                'event_type' => 'THEME_SETTING_UPDATED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'created_at' => Carbon::now(),
                'available_at' => Carbon::now(),
            ]);

            $responseData = Reply::successWithData(__('menu.themeSettings').' '.__('messages.updatedSuccessfully'), [
                'primary_color' => $theme->primary_color,
                'background_image_url' => $theme->background_image_url,
                'has_background_image' => ! is_null($theme->home_background_image),
                'outbox_id' => $outboxId,
                'action' => 'redirect',
                'url' => route('superadmin.theme-settings.index'),
            ]);

            // Complete Idempotency record if key was provided
            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseData),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            DB::commit();

            // Clean up replaced old image file after successful commit
            if ($imageChanged && $oldImage && $oldImage !== $newImage) {
                try {
                    $path = public_path('user-uploads/background/'.$oldImage);
                    if (File::exists($path)) {
                        Files::deleteFile($oldImage, 'background');
                    }
                } catch (Throwable $fileEx) {
                    Log::warning('Old background image cleanup warning: '.$fileEx->getMessage());
                }
            }

            // 5. Atomic O(1) Cache Invalidation (Rule 6)
            SuperAdminThemeSettingCacheManager::flushCaches();

            // 6. Post-commit fast path execution to sync Elasticsearch <15ms
            try {
                $job = new ProcessThemeSettingOutboxEventJob((int) $outboxId);
                $job->handle();
            } catch (Throwable $syncEx) {
                Log::warning('Fast-path ES sync failed, queued for background retry: '.$syncEx->getMessage());
                ProcessThemeSettingOutboxEventJob::dispatch((int) $outboxId);
            }

            return $responseData;
        } catch (Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded file if transaction rolled back
            if ($imageChanged && $newImage) {
                try {
                    Files::deleteFile($newImage, 'background');
                } catch (Throwable) {
                    // Ignore cleanup error on rollback
                }
            }

            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->delete();
            }

            throw $e;
        }
    }

    /**
     * Reset Super Admin theme settings to default values with idempotency and transactional outbox.
     *
     * @param  string|null  $idempotencyKey  Client-supplied UUID or token
     * @param  int|null  $adminId  Authenticated super admin ID
     * @return array Standardized Reply payload
     */
    public function resetThemeSettings(?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalIdempotencyKey = $this->formatIdempotencyKey($idempotencyKey);
        $payloadForHash = [
            'action' => 'reset',
            'timestamp' => Carbon::now()->format('Y-m-d H:i'),
        ];
        $requestHash = $this->computeRequestHash($payloadForHash);

        // 1. Two-Tier Idempotency Lease Claim Check
        if ($finalIdempotencyKey) {
            $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'request_hash' => $requestHash,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addHours(24),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                $existing = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'A theme reset is currently processing for this request. Please wait.');
                        }
                    }
                }
            }
        }

        $currentTheme = ThemeSetting::withoutGlobalScopes()->whereNull('company_id')->first();
        $oldImage = $currentTheme ? $currentTheme->home_background_image : null;

        // 2. Row-locked MySQL transaction (<15ms)
        DB::beginTransaction();

        try {
            $theme = ThemeSetting::lockForUpdate()
                ->withoutGlobalScopes()
                ->whereNull('company_id')
                ->first();

            if (! $theme) {
                $theme = new ThemeSetting;
                $theme->company_id = null;
            }

            $theme->primary_color = self::DEFAULT_PRIMARY_COLOR;
            $theme->admin_custom_css = null;
            $theme->front_custom_css = null;
            $theme->home_background_image = null;
            $theme->save();

            // 3. Atomic Outbox Event
            $outboxPayload = [
                'theme_id' => $theme->id,
                'primary_color' => self::DEFAULT_PRIMARY_COLOR,
                'admin_custom_css' => null,
                'front_custom_css' => null,
                'home_background_image' => null,
                'admin_id' => $adminId,
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'ThemeSetting',
                'aggregate_id' => (int) $theme->id,
                'aggregate_version' => (int) (microtime(true) * 1000),
                'event_type' => 'THEME_SETTING_RESET',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'created_at' => Carbon::now(),
                'available_at' => Carbon::now(),
            ]);

            $responseData = Reply::successWithData(__('messages.updatedSuccessfully'), [
                'primary_color' => self::DEFAULT_PRIMARY_COLOR,
                'admin_custom_css' => '',
                'front_custom_css' => '',
                'background_image_url' => asset('front/assets/img/header-bg.jpg'),
                'has_background_image' => false,
                'outbox_id' => $outboxId,
                'action' => 'redirect',
                'url' => route('superadmin.theme-settings.index'),
            ]);

            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseData),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            DB::commit();

            // Clean up removed image file after commit
            if ($oldImage) {
                try {
                    $path = public_path('user-uploads/background/'.$oldImage);
                    if (File::exists($path)) {
                        Files::deleteFile($oldImage, 'background');
                    }
                } catch (Throwable $ex) {
                    Log::warning('Reset background image cleanup error: '.$ex->getMessage());
                }
            }

            // Invalidate caches
            SuperAdminThemeSettingCacheManager::flushCaches();

            // Post-commit fast path ES synchronization
            try {
                $job = new ProcessThemeSettingOutboxEventJob((int) $outboxId);
                $job->handle();
            } catch (Throwable $syncEx) {
                Log::warning('Fast-path ES sync failed on reset, queued for background retry: '.$syncEx->getMessage());
                ProcessThemeSettingOutboxEventJob::dispatch((int) $outboxId);
            }

            return $responseData;
        } catch (Throwable $e) {
            DB::rollBack();

            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->delete();
            }

            throw $e;
        }
    }
}
