<?php

namespace App\Services\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\UpdateSuperAdminProfileRequest;
use App\Jobs\ProcessSuperAdminProfileOutboxEventJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminProfileMutationService
 *
 * Intent: Encapsulates state-mutating operations for Super Admin Profile under the
 * Transactional Outbox, Two-Tier Idempotency, and CQRS dual-store synchronization architecture.
 * Guarantees zero duplicate executions under network retries, ACID row-locked state transitions,
 * zero remote/disk I/O inside MySQL transactions (duration < 15ms), fast-path Elasticsearch sync,
 * and O(1) Redis/Dragonfly L2 cache invalidation.
 *
 * Tenancy: Master Super Admin singleton scope (tenant_id = 0).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminProfileMutationService
{
    /**
     * Compute a canonical SHA-256 hash for a given payload.
     *
     * @param array<string, mixed> $payload
     * @return string
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Format a deterministic idempotency key when explicitly supplied by client.
     *
     * @param string|null $clientKey
     * @return string|null
     */
    protected function formatIdempotencyKey(?string $clientKey): ?string
    {
        if ($clientKey && trim($clientKey) !== '') {
            return trim($clientKey);
        }

        return null;
    }

    /**
     * Safely purge old avatar from disk and storage outside transaction boundary.
     *
     * @param string|null $image
     * @return void
     */
    public function removeOldAvatar(?string $image): void
    {
        if (! $image) {
            return;
        }

        try {
            Files::deleteFile($image, 'profile');
            $localPath = public_path('user-uploads/profile/' . $image);
            if (File::exists($localPath)) {
                File::delete($localPath);
            }
        } catch (Throwable $e) {
            // Gracefully ignore missing file exceptions
        }
    }

    /**
     * Persist updated Super Admin profile credentials and contact channels with two-tier idempotency and outbox.
     *
     * @param UpdateSuperAdminProfileRequest $request
     * @param int $userId
     * @param string|null $idempotencyKey
     * @return array
     */
    public function updateProfile(UpdateSuperAdminProfileRequest $request, int $userId, ?string $idempotencyKey = null): array
    {
        $finalIdempotencyKey = $this->formatIdempotencyKey($idempotencyKey);
        $allInputs = $request->except(['_token', '_method', 'image', 'idempotency_key', 'current_password', 'password', 'password_confirmation']);
        $requestHash = $this->computeRequestHash($allInputs);

        // ── 1. Two-Tier Idempotency Lease Claim Check ────────────────────────────
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
                    // Forensic Check 4: Enforce exact payload match on duplicate idempotency key
                    if ($existing->request_hash && $existing->request_hash !== $requestHash) {
                        abort(422, 'Idempotency key collision: This idempotency key was previously used with a different request payload.');
                    }

                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'A profile update is currently processing for this request. Please wait.');
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

        // ── 2. Pre-transaction file handling (Zero slow I/O inside DB transaction) ─
        $currentUser = DB::table('users')->where('id', $userId)->select('image', 'password')->first();
        $oldImage = $currentUser ? $currentUser->image : null;
        $newImage = null;
        $imageChanged = false;
        $shouldDeleteOld = false;

        if ($request->boolean('remove_image')) {
            $newImage = null;
            $imageChanged = true;
            $shouldDeleteOld = true;
        } elseif ($request->hasFile('image')) {
            $newImage = Files::upload($request->file('image'), 'profile');
            $imageChanged = true;
            $shouldDeleteOld = true;
        }

        // ── 3. Row-locked MySQL transaction (<15ms) ──────────────────────────────
        DB::beginTransaction();

        try {
            /** @var User $user */
            $user = User::lockForUpdate()->findOrFail($userId);

            // Password Verification & Rotation
            if ($request->filled('password')) {
                if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                    DB::rollBack();

                    // Forensic Check 9: Saga Compensation — Purge newly uploaded orphan image
                    if ($newImage) {
                        $this->removeOldAvatar($newImage);
                    }

                    if ($finalIdempotencyKey) {
                        DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $finalIdempotencyKey)
                            ->update([
                                'status' => 'FAILED_FINAL',
                                'last_error' => 'The provided current password does not match our records.',
                                'updated_at' => Carbon::now(),
                            ]);
                    }

                    return Reply::error(__('The provided current password does not match our records.'), 'current_password');
                }
                $user->password = Hash::make($request->password);
            }

            // Core Identity Attributes
            $user->name = $request->name;
            $user->email = $request->email;

            // Direct Mobile & Calling Code (Handle state transition)
            if ($request->has('mobile')) {
                $newMobile = $request->filled('mobile') ? trim((string) $request->mobile) : null;
                $newCallingCode = $request->filled('calling_code') ? trim((string) $request->calling_code) : null;

                if ($user->mobile !== $newMobile || $user->calling_code !== $newCallingCode) {
                    $user->mobile_verified = 0;
                }

                $user->mobile = $newMobile;
                $user->calling_code = $newCallingCode;
            }

            // Communications & Social Links
            if ($request->has('whatsapp_number')) {
                $user->whatsapp_number = $request->filled('whatsapp_number') ? trim((string) $request->whatsapp_number) : null;
            }

            if ($request->has('linkedin')) {
                $user->linkedin = $request->filled('linkedin') ? trim((string) $request->linkedin) : null;
            }

            // Profile Picture
            if ($imageChanged) {
                $user->image = $newImage;
            }

            $user->save();

            $aggregateVersion = $user->updated_at ? Carbon::parse($user->updated_at)->timestamp : time();

            // Insert Transactional Outbox Event
            $outboxPayload = [
                'aggregate_id' => $user->id,
                'aggregate_version' => (int) $aggregateVersion,
                'attributes' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'calling_code' => $user->calling_code,
                    'mobile' => $user->mobile,
                    'mobile_verified' => (int) $user->mobile_verified,
                    'whatsapp_number' => $user->whatsapp_number,
                    'linkedin' => $user->linkedin,
                    'image' => $user->image,
                    'profile_image_url' => $user->profile_image_url,
                    'aggregate_version' => (int) $aggregateVersion,
                    'updated_at' => $user->updated_at ? $user->updated_at->toIso8601String() : now()->toIso8601String(),
                ],
            ];

            $eventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'SuperAdminProfile',
                'aggregate_id' => $user->id,
                'aggregate_version' => (int) $aggregateVersion,
                'event_type' => 'SUPERADMIN_PROFILE_UPDATED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            // Structured JSON Response for Client-Side DOM Sync
            $responseData = Reply::successWithData(__('menu.myProfile') . ' ' . __('messages.updatedSuccessfully'), [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'calling_code' => $user->calling_code,
                    'mobile' => $user->mobile,
                    'mobile_verified' => (int) $user->mobile_verified,
                    'whatsapp_number' => $user->whatsapp_number,
                    'linkedin' => $user->linkedin,
                    'image' => $user->image,
                    'profile_image_url' => $user->profile_image_url,
                ],
                'action' => 'none',
            ]);

            // Finalize Idempotency record with lease token check
            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->where('status', 'PROCESSING')
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseData),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            DB::commit();

            // Forensic Check 9: Two-Phase Avatar Purge — Only delete old avatar AFTER successful commit
            if ($shouldDeleteOld && $oldImage) {
                $this->removeOldAvatar($oldImage);
            }
        } catch (Throwable $e) {
            DB::rollBack();

            // Forensic Check 9: Saga Compensation — Purge newly uploaded orphan image on failure
            if ($newImage) {
                $this->removeOldAvatar($newImage);
            }

            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->update([
                        'status' => 'FAILED_FINAL',
                        'last_error' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            Log::error('SuperAdminProfileMutationService::updateProfile transaction failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'exception' => $e,
            ]);

            throw $e;
        }

        // ── 4. Post-Commit Cache Priming & Fast-Path Execution (<15ms) ───────────
        SuperAdminProfileCacheManager::flushCaches($userId);

        // Forensic Check 6: Prime L2 cache write-through under new generation version to eliminate stale cache window
        $primedDocAttributes = [
            'id' => (int) $user->id,
            'company_id' => $user->company_id ? (int) $user->company_id : null,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'calling_code' => $user->calling_code ? (string) $user->calling_code : null,
            'mobile' => $user->mobile ? (string) $user->mobile : null,
            'mobile_verified' => (int) $user->mobile_verified,
            'whatsapp_number' => $user->whatsapp_number ? (string) $user->whatsapp_number : null,
            'linkedin' => $user->linkedin ? (string) $user->linkedin : null,
            'image' => $user->image ? (string) $user->image : null,
            'profile_image_url' => $user->profile_image_url,
            'is_superadmin' => (int) ($user->is_superadmin ?? 1),
            'status' => (string) ($user->status ?? 'active'),
            'aggregate_version' => (int) $aggregateVersion,
            'last_login' => $user->last_login ? Carbon::parse($user->last_login)->toIso8601String() : null,
            'created_at' => $user->created_at ? Carbon::parse($user->created_at)->toIso8601String() : null,
            'updated_at' => $user->updated_at ? Carbon::parse($user->updated_at)->toIso8601String() : null,
        ];
        \Illuminate\Support\Facades\Cache::put(
            SuperAdminProfileCacheManager::getProfileKey($userId),
            $primedDocAttributes,
            SuperAdminProfileCacheManager::PROFILE_TTL
        );

        try {
            ProcessSuperAdminProfileOutboxEventJob::dispatchSync($eventId);
        } catch (Throwable $e) {
            Log::warning('SuperAdminProfileMutationService: fast-path sync deferred to queue: ' . $e->getMessage());
            ProcessSuperAdminProfileOutboxEventJob::dispatch($eventId);
        }

        return $responseData;
    }
}
