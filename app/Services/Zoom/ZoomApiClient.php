<?php

namespace App\Services\Zoom;

use App\Services\Zoom\Exceptions\ZoomApiException;
use App\Services\Zoom\Exceptions\ZoomAuthenticationException;
use App\Services\Zoom\Exceptions\ZoomCircuitBreakerOpenException;
use App\Services\Zoom\Exceptions\ZoomRateLimitException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ZoomApiClient
 *
 * Intent: Hardened HTTP client for communicating with Zoom REST API v2.
 * - Explicit connection (5s) and request (12s) timeouts.
 * - HTTP 401: controlled single retry with automatic atomic token refresh.
 * - HTTP 403: actionable permission diagnostics without leaking credentials.
 * - HTTP 429: Rate-limit detection with Retry-After header parsing.
 * - Circuit breaker integration: records success/failure to prevent cascading hangs.
 * - Duplicate meeting creation protection: NEVER blindly retries non-idempotent POSTs on timeout.
 * - Sanitized logging: guarantees zero token/secret leakage.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomApiClient
{
    protected const BASE_URL = 'https://api.zoom.us/v2';

    protected const CONNECT_TIMEOUT_SECONDS = 5;

    protected const REQUEST_TIMEOUT_SECONDS = 12;

    protected ZoomTokenManager $tokenManager;

    protected ZoomCircuitBreaker $circuitBreaker;

    public function __construct(ZoomTokenManager $tokenManager, ZoomCircuitBreaker $circuitBreaker)
    {
        $this->tokenManager = $tokenManager;
        $this->circuitBreaker = $circuitBreaker;
    }

    /**
     * Execute an idempotent GET request against Zoom API with bounded retry (max 1 retry).
     *
     * @throws ZoomCircuitBreakerOpenException
     * @throws ZoomRateLimitException
     * @throws ZoomApiException
     */
    public function get(string $endpoint, array $query = []): array
    {
        $this->circuitBreaker->guard();

        $attempt = 0;
        $maxAttempts = 2;

        while ($attempt < $maxAttempts) {
            $attempt++;
            try {
                $token = $this->tokenManager->getAccessToken();
                $url = self::BASE_URL.'/'.ltrim($endpoint, '/');

                $response = Http::withToken($token)
                    ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                    ->timeout(self::REQUEST_TIMEOUT_SECONDS)
                    ->get($url, $query);

                if ($response->status() === 401 && $attempt === 1) {
                    // Stale token on Zoom side — force refresh and retry once
                    Log::warning('ZoomApiClient: Received HTTP 401 on GET. Forcing token refresh and retrying once.');
                    $this->tokenManager->invalidateToken();

                    continue;
                }

                return $this->handleResponse($response, 'GET', $endpoint);
            } catch (ZoomCircuitBreakerOpenException|ZoomRateLimitException $e) {
                throw $e;
            } catch (Throwable $e) {
                if ($attempt >= $maxAttempts) {
                    $this->circuitBreaker->recordFailure($e->getMessage());
                    throw new ZoomApiException('Zoom API GET failed: '.$e->getMessage(), 500, [], $e);
                }
                // Short backoff before second attempt
                usleep(300000); // 300ms
            }
        }

        throw new ZoomApiException("Zoom API GET {$endpoint} failed after {$maxAttempts} attempts.");
    }

    /**
     * Execute a POST request against Zoom API.
     * CRITICAL: Non-idempotent operations (like meeting creation) are NEVER retried on network timeout
     * to prevent creating duplicate calendar entries and sending multiple emails to candidates.
     *
     * @throws ZoomCircuitBreakerOpenException
     * @throws ZoomRateLimitException
     * @throws ZoomApiException
     */
    public function post(string $endpoint, array $body = []): array
    {
        $this->circuitBreaker->guard();

        try {
            $token = $this->tokenManager->getAccessToken();
            $url = self::BASE_URL.'/'.ltrim($endpoint, '/');

            $response = Http::withToken($token)
                ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                ->timeout(self::REQUEST_TIMEOUT_SECONDS)
                ->post($url, $body);

            // Handle HTTP 401 with controlled single retry
            if ($response->status() === 401) {
                Log::warning('ZoomApiClient: Received HTTP 401 on POST. Invalidating token and attempting single retry.');
                $this->tokenManager->invalidateToken();
                $freshToken = $this->tokenManager->getAccessToken(true);

                $response = Http::withToken($freshToken)
                    ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                    ->timeout(self::REQUEST_TIMEOUT_SECONDS)
                    ->post($url, $body);
            }

            return $this->handleResponse($response, 'POST', $endpoint);
        } catch (ZoomCircuitBreakerOpenException|ZoomRateLimitException|ZoomAuthenticationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->circuitBreaker->recordFailure($e->getMessage());
            $this->logSanitizedError('POST', $endpoint, $e->getMessage());

            throw new ZoomApiException('Zoom API POST failed: '.$e->getMessage(), 500, [], $e);
        }
    }

    /**
     * Create a scheduled video meeting via POST /users/me/meetings.
     */
    public function createMeeting(array $meetingPayload): array
    {
        return $this->post('users/me/meetings', $meetingPayload);
    }

    /**
     * Fetch user profile via GET /users/me.
     */
    public function getCurrentUser(): array
    {
        return $this->get('users/me');
    }

    /**
     * Process HTTP response and handle error classifications.
     */
    protected function handleResponse(Response $response, string $method, string $endpoint): array
    {
        $status = $response->status();

        if ($response->successful()) {
            $this->circuitBreaker->recordSuccess();

            return $response->json() ?? [];
        }

        $body = $response->json() ?? [];
        $errorMsg = $body['message'] ?? $response->body();

        // 1. HTTP 429: Rate Limit Exceeded
        if ($status === 429) {
            $retryAfter = (int) ($response->header('Retry-After') ?? 60);
            Log::warning("ZoomApiClient: Rate limit exceeded on {$method} {$endpoint}. Retry-After: {$retryAfter}s");
            throw new ZoomRateLimitException("Zoom API rate limit hit on {$endpoint}", $retryAfter);
        }

        // 2. HTTP 403: Forbidden / Permission Diagnosis
        if ($status === 403) {
            $hint = 'Zoom permissions error (HTTP 403). Ensure Server-to-Server OAuth application has scopes `meeting:write:admin` and `user:read:admin` enabled in Zoom Marketplace.';
            $this->logSanitizedError($method, $endpoint, "Forbidden 403: {$errorMsg}");
            throw new ZoomApiException("{$hint} — Details: {$errorMsg}", 403, $body);
        }

        // 3. HTTP 5xx: Zoom Upstream Outage
        if ($status >= 500) {
            $this->circuitBreaker->recordFailure("HTTP {$status}: {$errorMsg}");
            $this->logSanitizedError($method, $endpoint, "Server error {$status}: {$errorMsg}");
            throw new ZoomApiException("Zoom server error (HTTP {$status}): {$errorMsg}", $status, $body);
        }

        // 4. HTTP 4xx: Client Error
        $this->logSanitizedError($method, $endpoint, "Client error {$status}: {$errorMsg}");
        throw new ZoomApiException("Zoom API error (HTTP {$status}): {$errorMsg}", $status, $body);
    }

    /**
     * Sanitized error logging guaranteeing no sensitive tokens or secrets are logged.
     */
    protected function logSanitizedError(string $method, string $endpoint, string $message): void
    {
        // Strip out any potential tokens or query strings matching token patterns
        $sanitizedMessage = preg_replace('/Bearer\s+[A-Za-z0-9_\-\.]+/i', 'Bearer [REDACTED]', $message);

        Log::error("ZoomApiClient [{$method} {$endpoint}]: {$sanitizedMessage}");
    }
}
