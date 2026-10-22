<?php

namespace App\Services\Analytics;

/**
 * ClientPerformanceCacheManager
 *
 * Manages multi-dimensional caching in DragonflyDB / Redis with atomic,
 * version-driven tenant cache invalidation.
 *
 * Key Schema:
 * ats:client_perf:v2:{tenant_id}:{client_type}:{client_id}:{version}:{range}:{compare}:{timezone}
 */
class ClientPerformanceCacheManager
{
    /**
     * Generate a multi-dimensional cache key.
     */
    public function buildKey(int $tenantId, string $clientType, int $clientId, string $range, string $compare, string $timezone): string
    {
        $version = $this->getVersion($tenantId, $clientId);
        $cleanTz = str_replace(['/', ':'], '_', $timezone);

        return sprintf(
            'ats:client_perf:%s:%d:%s:%d:%d:%s:%s:%s',
            ClientPerformanceConstants::SCHEMA_VERSION,
            $tenantId,
            $clientType,
            $clientId,
            $version,
            $range,
            $compare,
            $cleanTz
        );
    }

    /**
     * Retrieve cached payload if present.
     */
    public function get(string $key): ?array
    {
        try {
            return cache()->get($key);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Store payload in cache.
     */
    public function put(string $key, array $data, int $ttlSeconds = ClientPerformanceConstants::CACHE_TTL_SECONDS): void
    {
        try {
            cache()->put($key, $data, $ttlSeconds);
        } catch (\Throwable $e) {
            // Ignore cache storage errors
        }
    }

    /**
     * Atomically invalidate all cached ranges and comparisons for this client.
     */
    public function invalidate(int $tenantId, int $clientId): void
    {
        try {
            $versionKey = "client_perf_version_{$tenantId}_{$clientId}";
            if (cache()->has($versionKey)) {
                cache()->increment($versionKey);
            } else {
                cache()->put($versionKey, 2, 86400 * 30);
            }
        } catch (\Throwable $e) {
            // Fail safe
        }
    }

    public function invalidateClient(int $tenantId, int $clientId): void
    {
        $this->invalidate($tenantId, $clientId);
    }

    /**
     * Get the current cache version for this client.
     */
    public function getVersion(int $tenantId, int $clientId): int
    {
        try {
            $versionKey = "client_perf_version_{$tenantId}_{$clientId}";

            return (int) cache()->get($versionKey, 1);
        } catch (\Throwable $e) {
            return 1;
        }
    }

    public function getClientVersion(int $tenantId, int $clientId): int
    {
        return $this->getVersion($tenantId, $clientId);
    }
}
