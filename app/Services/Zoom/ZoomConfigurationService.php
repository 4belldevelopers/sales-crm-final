<?php

namespace App\Services\Zoom;

use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Class ZoomConfigurationService
 *
 * Intent: Centralized enterprise configuration management for the Zoom Server-to-Server OAuth integration.
 * - Enforces CQRS: reads via L2 Redis / Elasticsearch ReadService (0% direct MySQL reads).
 * - Enforces singleton pattern (id = 1).
 * - Handles transparent decryption of client_secret at runtime (with legacy plaintext fallback).
 * - Computes configuration-version hashes to scope OAuth token caches and prevent race conditions.
 * - Masks sensitive credentials in diagnostic reports and responses.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomConfigurationService
{
    protected SuperAdminZoomSettingReadService $readService;

    public function __construct(SuperAdminZoomSettingReadService $readService)
    {
        $this->readService = $readService;
    }

    /**
     * Retrieve active, decrypted Zoom configuration.
     *
     * @return array{
     *     id: int,
     *     title: string,
     *     account_id: string,
     *     client_id: string,
     *     client_secret: string,
     *     status: int,
     *     is_active: bool,
     *     is_configured: bool,
     *     version: string
     * }
     */
    public function getConfiguration(): array
    {
        $setting = $this->readService->getZoomSetting();

        $accountId = trim((string) ($setting->redirect_uri ?? ''));
        $clientId = trim((string) ($setting->client_id ?? ''));
        $rawSecret = trim((string) ($setting->client_secret ?? ''));
        $status = (int) ($setting->status ?? 0);
        $title = (string) ($setting->title ?? 'JERO Official Zoom Integration');

        $decryptedSecret = $this->decryptSecret($rawSecret);

        $isConfigured = ! empty($accountId) && ! empty($clientId) && ! empty($decryptedSecret);
        $isActive = $isConfigured && ($status === 1);

        $version = $this->computeVersionHash($accountId, $clientId, $decryptedSecret, $status);

        return [
            'id' => 1,
            'title' => $title,
            'account_id' => $accountId,
            'client_id' => $clientId,
            'client_secret' => $decryptedSecret,
            'status' => $status,
            'is_active' => $isActive,
            'is_configured' => $isConfigured,
            'version' => $version,
        ];
    }

    /**
     * Retrieve sanitized configuration suitable for logs, UI, and diagnostic outputs.
     * Decrypted client secret is masked with asterisks.
     */
    public function getSanitizedConfiguration(): array
    {
        $config = $this->getConfiguration();
        $secretLength = strlen($config['client_secret']);

        $config['client_secret'] = $secretLength > 0 ? str_repeat('•', min(16, $secretLength)) : '';

        return $config;
    }

    /**
     * Check if Zoom integration is properly configured and enabled.
     */
    public function isAvailable(): bool
    {
        $config = $this->getConfiguration();

        return $config['is_active'];
    }

    /**
     * Compute a deterministic version hash representing the current credential snapshot.
     */
    public function getVersion(): string
    {
        $config = $this->getConfiguration();

        return $config['version'];
    }

    /**
     * Decrypt client secret stored at rest. Handles legacy plaintext transparently.
     */
    public function decryptSecret(string $rawSecret): string
    {
        if (empty($rawSecret)) {
            return '';
        }

        try {
            return Crypt::decryptString($rawSecret);
        } catch (DecryptException $e) {
            // Legacy plaintext fallback — log notice for background migration
            Log::debug('ZoomConfigurationService: Client secret read as legacy plaintext.');

            return $rawSecret;
        }
    }

    /**
     * Compute MD5 version hash combined with cache version counter.
     */
    protected function computeVersionHash(string $accountId, string $clientId, string $clientSecret, int $status): string
    {
        $cacheVersion = (string) Cache::get('superadmin:zoom_setting:version', '1');
        $signature = implode('|', [$accountId, $clientId, substr(hash('sha256', $clientSecret), 0, 16), $status, $cacheVersion]);

        return substr(md5($signature), 0, 12);
    }
}
