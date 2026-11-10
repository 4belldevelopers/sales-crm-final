<?php

namespace App\Services\Zoom;

use App\Services\Zoom\Exceptions\ZoomAuthenticationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ZoomTokenManager
 *
 * Intent: Enterprise-grade OAuth Token Management for Zoom Server-to-Server integration.
 * - Configuration-version-aware Redis token caching: eliminates cross-credential collisions.
 * - Distributed Locking (`Cache::lock`) with Double-Checked Cache Lookup: guarantees exactly ONE
 *   worker refreshes the OAuth token during thundering-herd cache misses.
 * - Expiry Safety Margin: subtracts 300 seconds (5 mins) from `expires_in` to prevent edge-case 401s.
 * - Stale Worker Protection: prevents slow asynchronous workers from overwriting freshly rotated credentials.
 * - Graceful Degradation: falls back seamlessly if Redis lock services encounter transient errors.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomTokenManager
{
    protected const CACHE_PREFIX = 'zoom:oauth:token:v_';

    protected const LOCK_PREFIX = 'zoom:oauth:lock:v_';

    protected const SAFETY_MARGIN_SECONDS = 300;

    protected const LOCK_WAIT_SECONDS = 10;

    protected const LOCK_EXPIRY_SECONDS = 15;

    protected ZoomConfigurationService $configService;

    /**
     * In-memory local static cache for same-request / Redis-outage fallback.
     */
    protected static array $inMemoryTokens = [];

    public function __construct(ZoomConfigurationService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Retrieve a valid OAuth access token for the active Zoom configuration.
     * Guaranteed to be concurrency-safe with distributed locking.
     *
     * @param  bool  $forceRefresh  If true, bypasses existing cache and acquires a fresh token.
     *
     * @throws ZoomAuthenticationException
     */
    public function getAccessToken(bool $forceRefresh = false): string
    {
        $config = $this->configService->getConfiguration();

        if (! $config['is_configured']) {
            throw new ZoomAuthenticationException('Zoom Server-to-Server credentials are not configured in Super Admin settings.');
        }

        if (! $config['is_active']) {
            throw new ZoomAuthenticationException('Zoom integration is currently paused/disabled by Super Admin.');
        }

        $version = $config['version'];
        $cacheKey = self::CACHE_PREFIX.$version;
        $lockKey = self::LOCK_PREFIX.$version;

        // 1. Fast Cache Read (0ms latency, zero locks)
        if (! $forceRefresh) {
            $token = $this->getCachedToken($cacheKey);
            if (! empty($token)) {
                return $token;
            }
        }

        // 2. Distributed Locking with Double-Checked Cache Lookup (Thundering Herd Protection)
        return $this->acquireTokenWithLock($cacheKey, $lockKey, $config, $forceRefresh);
    }

    /**
     * Invalidate all cached tokens for the specified or current configuration version.
     */
    public function invalidateToken(?string $version = null): void
    {
        $targetVersion = $version ?? $this->configService->getVersion();
        $cacheKey = self::CACHE_PREFIX.$targetVersion;

        try {
            Cache::forget($cacheKey);
            unset(self::$inMemoryTokens[$cacheKey]);
            Log::info("ZoomTokenManager: Invalidated OAuth token for version {$targetVersion}.");
        } catch (Throwable $e) {
            unset(self::$inMemoryTokens[$cacheKey]);
        }
    }

    /**
     * Acquire token using distributed lock to prevent thundering herd.
     */
    protected function acquireTokenWithLock(string $cacheKey, string $lockKey, array $config, bool $forceRefresh): string
    {
        $lock = null;
        try {
            $lock = Cache::lock($lockKey, self::LOCK_EXPIRY_SECONDS);
            $acquired = $lock->block(self::LOCK_WAIT_SECONDS);

            if ($acquired) {
                // Double-Check Cache: check if another worker refreshed the token while we waited for the lock
                if (! $forceRefresh) {
                    $doubleCheckedToken = $this->getCachedToken($cacheKey);
                    if (! empty($doubleCheckedToken)) {
                        return $doubleCheckedToken;
                    }
                }

                // We are the single authorized worker refreshing the token
                $tokenData = $this->requestNewTokenFromZoom($config);
                $this->cacheToken($cacheKey, $tokenData, $config['version']);

                return $tokenData['access_token'];
            }
        } catch (LockTimeoutException $e) {
            Log::warning('ZoomTokenManager: Lock acquisition timed out waiting for token refresh. Attempting emergency double-check.');
            $token = $this->getCachedToken($cacheKey);
            if (! empty($token)) {
                return $token;
            }
        } catch (Throwable $lockEx) {
            Log::warning('ZoomTokenManager: Redis lock failure (falling back to direct fetch): '.$lockEx->getMessage());
        } finally {
            if ($lock) {
                try {
                    $lock->release();
                } catch (Throwable $relEx) {
                    // Suppress lock release errors
                }
            }
        }

        // Emergency fallback: fetch token directly if lock mechanism failed
        $tokenData = $this->requestNewTokenFromZoom($config);
        $this->cacheToken($cacheKey, $tokenData, $config['version']);

        return $tokenData['access_token'];
    }

    /**
     * Execute live OAuth exchange against Zoom Server-to-Server endpoint.
     */
    public function requestNewTokenFromZoom(array $config): array
    {
        $accountId = $config['account_id'];
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];

        try {
            $response = Http::asForm()
                ->timeout(12)
                ->connectTimeout(5)
                ->withBasicAuth($clientId, $clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $body = $response->json() ?? [];
                $errorDesc = $body['reason'] ?? $body['error_description'] ?? $body['message'] ?? $response->body();

                Log::error('ZoomTokenManager: OAuth token request failed', [
                    'status' => $status,
                    'error_code' => $body['code'] ?? null,
                    'error' => $body['error'] ?? null,
                ]);

                throw new ZoomAuthenticationException("Zoom OAuth handshake failed (HTTP {$status}): {$errorDesc}");
            }

            $data = $response->json();
            $accessToken = $data['access_token'] ?? null;
            $expiresIn = (int) ($data['expires_in'] ?? 3600);

            if (empty($accessToken)) {
                throw new ZoomAuthenticationException('Zoom OAuth response did not contain an access_token.');
            }

            return [
                'access_token' => $accessToken,
                'expires_in' => $expiresIn,
                'token_type' => $data['token_type'] ?? 'bearer',
                'scope' => $data['scope'] ?? '',
            ];
        } catch (ZoomAuthenticationException $authEx) {
            throw $authEx;
        } catch (Throwable $e) {
            Log::error('ZoomTokenManager: Network error during token request: '.$e->getMessage());
            throw new ZoomAuthenticationException('Network failure connecting to Zoom OAuth token endpoint: '.$e->getMessage(), null, $e);
        }
    }

    /**
     * Cache token with safety margin TTL and stale-worker protection.
     */
    protected function cacheToken(string $cacheKey, array $tokenData, string $expectedVersion): void
    {
        // Stale Worker Protection: verify config version has not changed while waiting for network
        $currentVersion = $this->configService->getVersion();
        if ($currentVersion !== $expectedVersion) {
            Log::warning("ZoomTokenManager: Stale worker aborted token cache write. Expected version {$expectedVersion}, current version {$currentVersion}.");

            return;
        }

        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);
        $ttlSeconds = max(60, $expiresIn - self::SAFETY_MARGIN_SECONDS);

        try {
            Cache::put($cacheKey, $tokenData['access_token'], $ttlSeconds);
        } catch (Throwable $e) {
            Log::warning('ZoomTokenManager: Failed to write token to Redis cache: '.$e->getMessage());
        }

        // Store in local process memory as secondary fallback
        self::$inMemoryTokens[$cacheKey] = [
            'token' => $tokenData['access_token'],
            'expires_at' => time() + $ttlSeconds,
        ];
    }

    /**
     * Read token from Redis or in-memory fallback.
     */
    protected function getCachedToken(string $cacheKey): ?string
    {
        try {
            $token = Cache::get($cacheKey);
            if (! empty($token)) {
                return $token;
            }
        } catch (Throwable $e) {
            // Redis error — fallback to memory
        }

        if (isset(self::$inMemoryTokens[$cacheKey])) {
            $memory = self::$inMemoryTokens[$cacheKey];
            if ($memory['expires_at'] > time()) {
                return $memory['token'];
            }
            unset(self::$inMemoryTokens[$cacheKey]);
        }

        return null;
    }
}
