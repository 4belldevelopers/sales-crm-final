<?php

namespace App\DTO\ServiceCompany;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * AgencyActivityQuery
 *
 * Dedicated CQRS Read Query Contract for Agency Drawer Activity Tab.
 * Enforces validated category taxonomy, timeframe boundaries, and encrypted cursor pagination.
 */
class AgencyActivityQuery
{
    public const DEFAULT_LIMIT = 25;

    public const MAX_LIMIT = 100;

    public const ALLOWED_CATEGORIES = [
        'all',
        'account',
        'team',
        'services',
        'jobs',
        'candidates',
        'interviews',
        'offers',
        'joining',
        'communication',
        'client',
        'marketplace',
        'performance',
        'compliance',
        'relationship',
        'system',
    ];

    public function __construct(
        public readonly int $tenantId,
        public readonly int $serviceCompanyId,
        public readonly int $agencyCompanyId,
        public readonly string $category,
        public readonly string $timeframe,
        public readonly ?string $search,
        public readonly ?int $cursorSequence,
        public readonly ?string $cursorEventId,
        public readonly int $limit = self::DEFAULT_LIMIT,
        public readonly bool $forceRefresh = false
    ) {}

    public static function fromAuthorizedContext(AgencyAccessContext $context, Request $request): self
    {
        if (! $context->isAuthorized) {
            abort(403, 'Unauthorized access to agency activity feed.');
        }

        $rawCategory = strtolower(trim((string) $request->input('category', 'all')));
        $category = in_array($rawCategory, self::ALLOWED_CATEGORIES, true) ? $rawCategory : 'all';

        $rawTimeframe = strtolower(trim((string) $request->input('timeframe', 'all')));
        $allowedTimeframes = ['all', 'today', '7d', '30d', '90d'];
        $timeframe = in_array($rawTimeframe, $allowedTimeframes, true) ? $rawTimeframe : 'all';

        $rawSearch = $request->input('search') ?: $request->input('q');
        $search = ! empty($rawSearch) ? strtolower(trim((string) $rawSearch)) : null;

        $limit = min(self::MAX_LIMIT, max(1, (int) $request->input('limit', self::DEFAULT_LIMIT)));
        $forceRefresh = $request->boolean('refresh') || $request->input('force') == '1';

        $cursorSequence = null;
        $cursorEventId = null;
        $rawCursor = $request->input('cursor');

        if (! empty($rawCursor)) {
            $decrypted = self::decryptCursor($rawCursor, $context, $category, $search, $timeframe);
            if ($decrypted !== null) {
                $cursorSequence = $decrypted['sequence'] ?? null;
                $cursorEventId = $decrypted['event_id'] ?? null;
            }
        }

        return new self(
            tenantId: $context->tenantId,
            serviceCompanyId: $context->serviceCompanyId,
            agencyCompanyId: $context->agencyCompanyId,
            category: $category,
            timeframe: $timeframe,
            search: $search,
            cursorSequence: $cursorSequence,
            cursorEventId: $cursorEventId,
            limit: $limit,
            forceRefresh: $forceRefresh
        );
    }

    public function toPageCacheKey(int $version): string
    {
        $hash = md5(implode(':', [
            $this->category,
            $this->timeframe,
            $this->search ?? '',
            $this->cursorSequence ?? 0,
            $this->cursorEventId ?? '',
            $this->limit,
        ]));

        return "ats:agency_act_page:{$this->serviceCompanyId}:{$this->agencyCompanyId}:v{$version}:{$hash}";
    }

    public function toKpiCacheKey(int $version): string
    {
        $hash = md5(implode(':', [
            $this->timeframe,
            $this->search ?? '',
        ]));

        return "ats:agency_act_kpi:{$this->serviceCompanyId}:{$this->agencyCompanyId}:v{$version}:{$hash}";
    }

    public static function encryptCursor(int $sequence, string $eventId, AgencyAccessContext $context, string $category, ?string $search, string $timeframe): string
    {
        $payload = [
            'sequence' => $sequence,
            'event_id' => $eventId,
            'agency_id' => $context->agencyCompanyId,
            'tenant_id' => $context->serviceCompanyId,
            'category' => $category,
            'search' => $search,
            'timeframe' => $timeframe,
            'iat' => time(),
        ];

        return base64_encode(Crypt::encryptString(json_encode($payload)));
    }

    public static function decryptCursor(string $encoded, AgencyAccessContext $context, string $category, ?string $search, string $timeframe): ?array
    {
        try {
            $raw = Crypt::decryptString(base64_decode($encoded));
            $payload = json_decode($raw, true);

            if (! is_array($payload)) {
                return null;
            }
            if (($payload['agency_id'] ?? 0) !== $context->agencyCompanyId) {
                return null;
            }
            if (($payload['category'] ?? '') !== $category) {
                return null;
            }

            return $payload;
        } catch (\Throwable) {
            return null;
        }
    }
}
