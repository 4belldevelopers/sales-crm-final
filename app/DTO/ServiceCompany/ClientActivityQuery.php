<?php

namespace App\DTO\ServiceCompany;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * ClientActivityQuery
 *
 * Dedicated CQRS Read Query Contract for Client Drawer Activity Tab.
 * Strictly constructed from an already-authorized ClientAccessContext,
 * guaranteeing untrusted external parameters cannot reach Elasticsearch.
 */
class ClientActivityQuery
{
    public const DEFAULT_LIMIT = 15;

    public const MAX_LIMIT = 50;

    public const CURSOR_TTL_MINUTES = 30;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $serviceCompanyId,
        public readonly int $clientId,
        public readonly string $clientType,         // 'internal' | 'direct'
        public readonly ?int $partnerCompanyId,     // Marketplace partner ID
        public readonly string $category,           // 'all' | 'pipeline' | 'interviews' | 'offers' | 'jobs' | 'crm'
        public readonly string $timeframe,          // 'all' | 'today' | '7d' | '30d'
        public readonly ?string $search,
        public readonly ?int $cursorSequence,
        public readonly ?string $cursorEventId,
        public readonly int $limit = self::DEFAULT_LIMIT,
        public readonly ?int $fromSequence = null,
        public readonly ?int $toSequence = null,
        public readonly array $eventIds = []
    ) {}

    /**
     * Strictly construct from a policy-approved context.
     * Raw external parameters CANNOT instantiate this query without authorization.
     */
    public static function fromAuthorizedContext(ClientAccessContext $context, Request $request): self
    {
        if (! $context->isAuthorized) {
            abort(403, 'Unauthorized access: Client is outside authenticated tenant boundary.');
        }

        $rawCategory = strtolower(trim((string) $request->input('category', 'all')));
        $allowedCategories = ['all', 'pipeline', 'interviews', 'offers', 'jobs', 'crm'];
        $category = in_array($rawCategory, $allowedCategories, true) ? $rawCategory : 'all';

        $rawTimeframe = strtolower(trim((string) $request->input('timeframe', 'all')));
        $allowedTimeframes = ['all', 'today', '7d', '30d'];
        $timeframe = in_array($rawTimeframe, $allowedTimeframes, true) ? $rawTimeframe : 'all';

        $rawSearch = $request->input('search') ?: $request->input('q');
        $search = ! empty($rawSearch) ? strtolower(trim((string) $rawSearch)) : null;

        $limit = min(self::MAX_LIMIT, max(1, (int) $request->input('limit', self::DEFAULT_LIMIT)));

        // Range parameters for gap reconciliation
        $fromSequence = $request->filled('from_sequence') ? (int) $request->input('from_sequence') : null;
        $toSequence = $request->filled('to_sequence') ? (int) $request->input('to_sequence') : null;
        $eventIds = (array) $request->input('event_ids', []);

        // Decrypt cursor if provided
        $cursorSequence = null;
        $cursorEventId = null;
        $rawCursor = $request->input('cursor');

        if (! empty($rawCursor)) {
            $decrypted = self::decryptCursor($rawCursor, $context, $category, $search, $timeframe);
            if ($decrypted !== null) {
                $cursorSequence = $decrypted['client_activity_sequence'] ?? null;
                $cursorEventId = $decrypted['event_id'] ?? null;
            }
        }

        return new self(
            tenantId: $context->tenantId,
            serviceCompanyId: $context->serviceCompanyId,
            clientId: $context->clientId,
            clientType: $context->clientType,
            partnerCompanyId: $context->partnerCompanyId,
            category: $category,
            timeframe: $timeframe,
            search: $search,
            cursorSequence: $cursorSequence,
            cursorEventId: $cursorEventId,
            limit: $limit,
            fromSequence: $fromSequence,
            toSequence: $toSequence,
            eventIds: array_filter(array_map('trim', $eventIds))
        );
    }

    /**
     * Decrypt and verify HMAC integrity and TTL expiration of incoming cursor.
     */
    public static function decryptCursor(
        string $encryptedCursor,
        ClientAccessContext $context,
        string $category,
        ?string $search,
        string $timeframe
    ): ?array {
        try {
            $json = Crypt::decryptString($encryptedCursor);
            $payload = json_decode($json, true);

            if (! is_array($payload)) {
                return null;
            }

            // Expiration check (30 minutes TTL)
            $expiresAt = $payload['expires_at'] ?? 0;
            if (now()->timestamp > $expiresAt) {
                return null;
            }

            // Boundary & parameter integrity check
            $canonicalQuery = json_encode([
                'tenant_id' => $context->tenantId,
                'client_id' => $context->clientId,
                'category' => $category,
                'search' => $search,
                'timeframe' => $timeframe,
            ]);
            $expectedHash = hash_hmac('sha256', $canonicalQuery, config('app.key'));

            if (! hash_equals($expectedHash, (string) ($payload['filter_hash'] ?? ''))) {
                return null;
            }

            return $payload;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Create an HMAC-bound encrypted cursor for pagination.
     */
    public function buildCursor(int $sequence, string $eventId): string
    {
        $canonicalQuery = json_encode([
            'tenant_id' => $this->tenantId,
            'client_id' => $this->clientId,
            'category' => $this->category,
            'search' => $this->search,
            'timeframe' => $this->timeframe,
        ]);
        $filterHash = hash_hmac('sha256', $canonicalQuery, config('app.key'));

        $payload = [
            'client_activity_sequence' => $sequence,
            'event_id' => $eventId,
            'filter_hash' => $filterHash,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(self::CURSOR_TTL_MINUTES)->timestamp,
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Normalized filter hash representing this exact query slice.
     */
    public function getFilterHash(): string
    {
        return md5("{$this->category}:{$this->timeframe}:{$this->search}");
    }

    /**
     * Page cache key incorporating current version epoch and cursor.
     */
    public function toPageCacheKey(int $version): string
    {
        $cursorPart = $this->cursorSequence ? "{$this->cursorSequence}_{$this->cursorEventId}" : 'initial';

        return "ats:client_act:page:{$this->tenantId}:{$this->clientId}:v{$version}:{$this->getFilterHash()}:{$cursorPart}:{$this->limit}";
    }

    /**
     * Decoupled KPI cache key (strictly independent of pagination cursor).
     */
    public function toKpiCacheKey(int $version): string
    {
        return "ats:client_act:kpi:{$this->tenantId}:{$this->clientId}:v{$version}:{$this->getFilterHash()}";
    }
}
