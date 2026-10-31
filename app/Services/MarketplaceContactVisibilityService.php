<?php

namespace App\Services;

use App\Models\CompanyMarketplaceContactAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MarketplaceContactVisibilityService
 *
 * Central domain service governing Marketplace Contact Details Visibility,
 * PII Authorization Firewall, and Tripartite Representative Resolution across JERO Marketplace.
 *
 * Architectural Invariants:
 * 1. Zero Direct PII in Elasticsearch: Search cluster stores only non-PII metadata.
 * 2. PII Authorization Firewall: Direct mobile & email are delivered ONLY when authorized
 *    (Internal team members, or external viewers when visibility = 'public').
 * 3. Invariant M-1 (Mandatory Channels): An organization must maintain >= 1 active contact
 *    for mandatory channels ('client', 'agency', 'bench').
 * 4. Single Active Primary: Guaranteed via DB virtual generated column + atomic transaction demotion.
 * 5. Multi-Tenant Partitioning: Strictly isolated by `company_id`.
 */
class MarketplaceContactVisibilityService
{
    const CACHE_TTL_SECONDS = 2592000; // 30 days

    public const MANDATORY_CHANNELS = ['client', 'agency', 'bench'];

    public const CHANNEL_LABELS = [
        'client' => 'Sales & Client Enquiries',
        'agency' => 'Agency Queries',
        'bench' => 'Bench Talent Enquiries',
    ];

    /**
     * Resolves representative information filtered by the viewer's authorization context.
     *
     * @param  int  $targetCompanyId  Company whose representative is being viewed
     * @param  string  $channel  Marketplace channel ('client', 'agency', 'bench')
     * @param  User|null  $viewer  Currently authenticated viewer (null if guest/public)
     * @return array Safe representative DTO
     */
    public function resolveRepresentativeForViewer(
        int $targetCompanyId,
        string $channel,
        ?User $viewer = null
    ): array {
        $channel = strtolower(trim($channel));
        if ($targetCompanyId <= 0) {
            return $this->getEmptyResponse($channel);
        }

        // 1. Resolve authoritative assignment (Primary -> Secondary -> Backup -> Root fallback)
        $rep = $this->getAuthoritativeAssignment($targetCompanyId, $channel);
        if (! $rep) {
            $rep = $this->resolveRootFallback($targetCompanyId, $channel);
        }

        if (! $rep || empty($rep['user_id'])) {
            return $this->getEmptyResponse($channel);
        }

        // 2. Base DTO with Safe Non-PII Fields (Always safe to expose)
        $dto = [
            'user_id' => (int) $rep['user_id'],
            'company_id' => $targetCompanyId,
            'name' => $rep['name'],
            'designation' => $rep['designation'],
            'avatar' => $rep['avatar'],
            'channel' => $channel,
            'channel_label' => self::CHANNEL_LABELS[$channel] ?? ucfirst($channel),
            'priority' => $rep['priority'] ?? 'primary',
            'visibility' => $rep['visibility'] ?? 'public',
            'is_masked' => true,
            'email' => null,
            'mobile' => null,
            'contact_action' => 'in_app_message',
            'source' => $rep['source'] ?? 'assignment',
        ];

        // 3. PII Authorization Firewall Check
        $isInternalMember = $viewer && ((int) $viewer->company_id === $targetCompanyId);
        $isPublicChannel = (($rep['visibility'] ?? 'public') === 'public');

        if ($isInternalMember || $isPublicChannel) {
            $authoritativePII = $this->fetchAuthoritativeContactInfo((int) $rep['user_id']);
            $dto['email'] = $authoritativePII['email'];
            $dto['mobile'] = $authoritativePII['mobile'];
            $dto['is_masked'] = false;
            $dto['contact_action'] = 'direct';
        }

        return $dto;
    }

    /**
     * Resolves the authoritative contact assignment for a company and channel.
     * Evaluates Primary -> Secondary -> Backup hierarchy.
     */
    public function getAuthoritativeAssignment(int $companyId, string $channel): ?array
    {
        $channel = strtolower(trim($channel));
        $cacheKey = "ats:marketplace_rep:authoritative:{$companyId}:{$channel}";

        try {
            $cached = Cache::get($cacheKey);
            if (! empty($cached) && is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {
            Log::warning('MarketplaceContactVisibilityService: cache read error: '.$e->getMessage());
        }

        // Query assignments ordered by priority tier
        $assignment = CompanyMarketplaceContactAssignment::forCompany($companyId)
            ->forChannel($channel)
            ->active()
            ->currentlyEffective()
            ->join('users', 'users.id', '=', 'company_marketplace_contact_assignments.user_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('users.status', 'active')
            ->select([
                'company_marketplace_contact_assignments.id as assignment_id',
                'company_marketplace_contact_assignments.user_id',
                'company_marketplace_contact_assignments.channel',
                'company_marketplace_contact_assignments.visibility',
                'company_marketplace_contact_assignments.priority',
                'users.name',
                'users.image',
                'users_profile.designation',
                'roles.display_name as role_name',
            ])
            ->orderByRaw("FIELD(company_marketplace_contact_assignments.priority, 'primary', 'secondary', 'backup') ASC")
            ->orderBy('company_marketplace_contact_assignments.id', 'ASC')
            ->first();

        if ($assignment) {
            $roleTitle = ! empty($assignment->role_name) ? ucwords(strtolower($assignment->role_name)) : 'Member';
            $desig = ! empty($assignment->designation) ? ucwords(strtolower($assignment->designation)) : $roleTitle;

            $result = [
                'assignment_id' => (int) $assignment->assignment_id,
                'user_id' => (int) $assignment->user_id,
                'channel' => $channel,
                'visibility' => $assignment->visibility,
                'priority' => $assignment->priority,
                'name' => ucwords(strtolower($assignment->name)),
                'designation' => $desig,
                'avatar' => $assignment->image ?: null,
                'source' => 'assignment',
            ];

            try {
                Cache::put($cacheKey, $result, now()->addSeconds(self::CACHE_TTL_SECONDS));
            } catch (\Throwable $e) {
            }

            return $result;
        }

        return null;
    }

    /**
     * Fallback resolution to Root User or First Active Administrator if no assignments exist.
     */
    public function resolveRootFallback(int $companyId, string $channel): ?array
    {
        $rootUser = DB::table('users')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('users.company_id', $companyId)
            ->where('users.status', 'active')
            ->orderByDesc('users.root_user')
            ->orderBy('users.id', 'asc')
            ->select([
                'users.id',
                'users.name',
                'users.image',
                'users_profile.designation',
                'roles.display_name as role_name',
            ])
            ->first();

        if (! $rootUser) {
            return null;
        }

        $roleTitle = ! empty($rootUser->role_name) ? ucwords(strtolower($rootUser->role_name)) : 'Administrator';
        $desig = ! empty($rootUser->designation) ? ucwords(strtolower($rootUser->designation)) : $roleTitle;

        return [
            'assignment_id' => 0,
            'user_id' => (int) $rootUser->id,
            'channel' => $channel,
            'visibility' => 'public',
            'priority' => 'backup',
            'name' => ucwords(strtolower($rootUser->name)),
            'designation' => $desig,
            'avatar' => $rootUser->image ?: null,
            'source' => 'root_fallback',
        ];
    }

    /**
     * Fetches authoritative contact information (Email & Phone) from secure MySQL storage.
     * Executed strictly upon authorized request.
     */
    protected function fetchAuthoritativeContactInfo(int $userId): array
    {
        if ($userId <= 0) {
            return ['email' => null, 'mobile' => null];
        }

        $user = DB::table('users')
            ->where('id', $userId)
            ->select(['email', 'mobile', 'calling_code'])
            ->first();

        if (! $user) {
            return ['email' => null, 'mobile' => null];
        }

        $phone = '';
        if (! empty($user->mobile)) {
            $phone = (! empty($user->calling_code) ? '+'.ltrim($user->calling_code, '+').' ' : '').$user->mobile;
        }

        return [
            'email' => strtolower($user->email),
            'mobile' => $phone,
        ];
    }

    /**
     * Validates whether a channel can be deactivated or removed (Invariant M-1 Guard).
     * Returns ['allowed' => true] or ['allowed' => false, 'message' => '...'].
     */
    public function validateMandatoryChannelInvariant(
        int $companyId,
        string $channel,
        ?int $excludingUserId = null
    ): array {
        $channel = strtolower(trim($channel));
        if (! in_array($channel, self::MANDATORY_CHANNELS, true)) {
            return ['allowed' => true];
        }

        $query = CompanyMarketplaceContactAssignment::forCompany($companyId)
            ->forChannel($channel)
            ->active()
            ->currentlyEffective();

        if ($excludingUserId && $excludingUserId > 0) {
            $query->where('user_id', '!=', $excludingUserId);
        }

        $activeRemainingCount = $query->count();

        if ($activeRemainingCount === 0) {
            $label = self::CHANNEL_LABELS[$channel] ?? ucfirst($channel);

            return [
                'allowed' => false,
                'message' => "Mandatory Contact Rule: At least 1 active contact is required for {$label}. Please assign an alternate representative before removing this contact.",
                'channel' => $channel,
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Assigns or updates a communication channel for a user atomically.
     * Enforces single-primary demotion transactionally and maintains dual-write to company_tags.
     */
    public function assignChannel(
        int $companyId,
        int $userId,
        string $channel,
        string $visibility = 'public',
        string $priority = 'secondary',
        bool $isActive = true,
        ?Carbon $effectiveFrom = null,
        ?Carbon $effectiveUntil = null
    ): array {
        if ($companyId <= 0 || $userId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid company or user ID.'];
        }

        $channel = strtolower(trim($channel));
        if (! in_array($channel, ['client', 'agency', 'bench'], true)) {
            return ['status' => 'error', 'message' => "Invalid channel '{$channel}'."];
        }

        $visibility = in_array(strtolower($visibility), ['public', 'private'], true) ? strtolower($visibility) : 'public';
        $priority = in_array(strtolower($priority), ['primary', 'secondary', 'backup'], true) ? strtolower($priority) : 'secondary';

        // Validate user belongs to company and is active
        $user = DB::table('users')
            ->where('id', $userId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->first(['id', 'name', 'email']);

        if (! $user) {
            return ['status' => 'error', 'message' => 'Selected user was not found or is not active.'];
        }

        DB::transaction(function () use (
            $companyId,
            $userId,
            $channel,
            $visibility,
            $priority,
            $isActive,
            $effectiveFrom,
            $effectiveUntil
        ) {
            // Atomic Single-Primary Demotion:
            // If setting priority = 'primary' and is_active = 1, demote any existing active primary for this channel to 'secondary'
            if ($priority === 'primary' && $isActive) {
                CompanyMarketplaceContactAssignment::forCompany($companyId)
                    ->forChannel($channel)
                    ->primary()
                    ->where('user_id', '!=', $userId)
                    ->update([
                        'priority' => 'secondary',
                        'updated_at' => now(),
                    ]);
            }

            // Upsert assignment record
            CompanyMarketplaceContactAssignment::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'user_id' => $userId,
                    'channel' => $channel,
                ],
                [
                    'visibility' => $visibility,
                    'priority' => $priority,
                    'is_active' => $isActive,
                    'effective_from' => $effectiveFrom,
                    'effective_until' => $effectiveUntil,
                    'updated_at' => now(),
                ]
            );

            // Phase 1 Dual-Write: Mirror to legacy company_tags
            $this->dualWriteLegacyTags($companyId, $userId, $channel, $priority, $isActive);
        });

        // Invalidate Tenant Caches
        $this->invalidateTenantCaches($companyId, $channel);

        return [
            'status' => 'success',
            'message' => "Successfully updated {$channel} contact assignment for {$user->name}.",
            'channel' => $channel,
            'user_id' => $userId,
            'priority' => $priority,
            'visibility' => $visibility,
        ];
    }

    /**
     * Removes / unassigns a channel from a user, checking Invariant M-1 first.
     */
    public function removeChannel(int $companyId, int $userId, string $channel): array
    {
        $channel = strtolower(trim($channel));

        // Check Invariant M-1
        $validation = $this->validateMandatoryChannelInvariant($companyId, $channel, $userId);
        if (! $validation['allowed']) {
            return [
                'status' => 'error',
                'message' => $validation['message'],
                'code' => 422,
            ];
        }

        DB::transaction(function () use ($companyId, $userId, $channel) {
            CompanyMarketplaceContactAssignment::forCompany($companyId)
                ->forChannel($channel)
                ->where('user_id', $userId)
                ->delete();

            // Dual-write legacy cleanup
            $legacyChannel = ($channel === 'agency') ? 'agency' : $channel;
            DB::table('company_tags')
                ->where('company_id', $companyId)
                ->where(function ($q) use ($userId, $legacyChannel) {
                    $q->where('tag_name', "rep:{$legacyChannel}:{$userId}")
                        ->orWhere('tag_name', 'like', "rep_prio:{$legacyChannel}:{$userId}:%");
                })
                ->delete();
        });

        $this->invalidateTenantCaches($companyId, $channel);

        return [
            'status' => 'success',
            'message' => "Successfully removed contact from {$channel}.",
        ];
    }

    /**
     * Phase 1 Dual-Write: Mirrors assignments to legacy company_tags during transition.
     */
    protected function dualWriteLegacyTags(
        int $companyId,
        int $userId,
        string $channel,
        string $priority,
        bool $isActive
    ): void {
        $legacyChannel = ($channel === 'agency') ? 'agency' : $channel;
        $tagName = "rep:{$legacyChannel}:{$userId}";
        $prioTagName = "rep_prio:{$legacyChannel}:{$userId}:".ucfirst($priority);

        // Remove old tags
        DB::table('company_tags')
            ->where('company_id', $companyId)
            ->where(function ($q) use ($userId, $legacyChannel) {
                $q->where('tag_name', "rep:{$legacyChannel}:{$userId}")
                    ->orWhere('tag_name', 'like', "rep_prio:{$legacyChannel}:{$userId}:%");
            })
            ->delete();

        if ($isActive) {
            $now = now();
            DB::table('company_tags')->insert([
                [
                    'company_id' => $companyId,
                    'tag_name' => $tagName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'company_id' => $companyId,
                    'tag_name' => $prioTagName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    /**
     * Invalidates tenant caches across Redis / Dragonfly.
     */
    public function invalidateTenantCaches(int $companyId, ?string $channel = null): void
    {
        if ($companyId <= 0) {
            return;
        }

        try {
            Cache::forget("ats:company_rep:defaults:{$companyId}");
            Cache::forget("ats:company_rep:all_roles:{$companyId}");
            Cache::forget("ats:company_rep:all_prios:{$companyId}");
            Cache::forget("ats:teamcounts:{$companyId}");

            if ($channel) {
                Cache::forget("ats:marketplace_rep:authoritative:{$companyId}:{$channel}");
            } else {
                foreach (self::MANDATORY_CHANNELS as $ch) {
                    Cache::forget("ats:marketplace_rep:authoritative:{$companyId}:{$ch}");
                }
            }

            // Invalidate team listing version key
            $versionKey = 'ats:teamversion:company_'.$companyId;
            if (Cache::has($versionKey)) {
                Cache::increment($versionKey);
            } else {
                Cache::put($versionKey, 2, now()->addDays(30));
            }
        } catch (\Throwable $e) {
            Log::warning('MarketplaceContactVisibilityService: cache invalidation error: '.$e->getMessage());
        }
    }

    /**
     * Returns an empty DTO structure.
     */
    protected function getEmptyResponse(string $channel): array
    {
        return [
            'user_id' => 0,
            'company_id' => 0,
            'name' => 'Marketplace Representative',
            'designation' => 'Representative',
            'avatar' => null,
            'channel' => $channel,
            'channel_label' => self::CHANNEL_LABELS[$channel] ?? ucfirst($channel),
            'priority' => 'none',
            'visibility' => 'private',
            'is_masked' => true,
            'email' => null,
            'mobile' => null,
            'contact_action' => 'in_app_message',
            'source' => 'empty',
        ];
    }
}
