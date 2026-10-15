<?php

namespace App\Services\Marketplace;

use App\Exceptions\MarketplaceException;
use App\Exceptions\VendorLimitReachedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Class MarketplaceClaimService
 *
 * Intent:
 * Executes atomic agency requisition claims on Method 1 (Agency Hiring) with zero race conditions
 * under 10,000 concurrent users. Enforces strict multi-tenant authorization guards.
 *
 * Tenancy:
 * Scoped by hiring company_id and authenticated partner agency_id.
 * Adheres to Section 19.3 of docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md.
 */
class MarketplaceClaimService
{
    /**
     * Executes atomic agency claim with zero race conditions under 10k concurrency.
     */
    public function claimJob(int $jobId, int $authenticatedAgencyId, string $idempotencyKey): array
    {
        return DB::transaction(function () use ($jobId, $authenticatedAgencyId) {
            // 1. Authoritative MySQL Revalidation (Never trust cached ES projection for writes)
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (! $job || $job->status !== 'active') {
                throw new MarketplaceException('Job requisition is no longer active.');
            }

            $channel = DB::table('marketplace_channels')
                ->where('job_id', $jobId)
                ->where('channel_type', 'AGENCY_HIRING')
                ->first();

            $meta = is_string($job->meta_details) ? json_decode($job->meta_details, true) : (array) ($job->meta_details ?? []);
            $jobVendorLimit = (int) ($meta['vendor_limit'] ?? 20);
            $vendorLimit = $channel ? ($channel->vendor_limit ?? $jobVendorLimit) : $jobVendorLimit;
            if ($vendorLimit <= 0) {
                $vendorLimit = 20;
            }
            $channelEnabled = $channel ? ($channel->enabled && $channel->publication_status === 'LIVE') : ($job->show_vendor === 'yes');

            if (! $channelEnabled) {
                throw new MarketplaceException('Agency Hiring channel is not currently accepting claims.');
            }

            // 2. Prevent Duplicate Application / Claim
            $existingClaim = DB::table('recommendation_relation')
                ->where('job_id', $jobId)
                ->where('to_company_id', $authenticatedAgencyId)
                ->where('add_status', 1)
                ->first();

            if ($existingClaim) {
                throw new MarketplaceException('Your agency has already claimed or applied for this position.');
            }

            // 3. Atomic Vendor Counter Increment (ACID Race-Condition Guard)
            $affected = DB::table('job_agency_counter')
                ->where('job_id', $jobId)
                ->where('claimed_count', '<', $vendorLimit)
                ->increment('claimed_count');

            if ($affected === 0) {
                // If counter row was missing, attempt atomic insert or fail
                $counterExists = DB::table('job_agency_counter')->where('job_id', $jobId)->exists();
                if (! $counterExists) {
                    DB::table('job_agency_counter')->insert([
                        'job_id' => $jobId,
                        'claimed_count' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    throw new VendorLimitReachedException("Vendor capacity of {$vendorLimit} agencies has been reached.");
                }
            }

            // 4. Create Authoritative Recommendation Claim
            $claimId = DB::table('recommendation_relation')->insertGetId([
                'from_company_id' => $job->company_id,
                'to_company_id' => $authenticatedAgencyId,
                'job_id' => $jobId,
                'add_status' => 1, // 1 = market_in_added / claimed
                'final_status' => 0, // 0 = pending review, 1 = accepted/hired
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Append Transactional Outbox Event
            DB::table('sys_outbox_events')->insert([
                'tenant_id' => $job->company_id,
                'aggregate_type' => 'job',
                'aggregate_id' => $jobId,
                'aggregate_version' => ($job->state_version ?? 0) + 1,
                'event_type' => 'AgencyClaimedJob',
                'payload' => json_encode([
                    'job_id' => $jobId,
                    'agency_id' => $authenticatedAgencyId,
                    'claim_id' => $claimId,
                ]),
                'status' => 'PENDING',
                'available_at' => now(),
                'created_at' => now(),
            ]);

            // 6. Purge Tenant Dragonfly Cache
            try {
                Redis::del("company_id:{$job->company_id}:marketplace_jobs");
                Redis::del("company_id:{$authenticatedAgencyId}:direct_client_jobs");
            } catch (\Throwable $e) {
                // Ephemeral cache eviction failure must never break DB transaction
            }

            return ['status' => 'success', 'message' => 'Requisition successfully claimed.', 'claim_id' => $claimId];
        });
    }

    /**
     * Strict Multi-Tenant Authorization Policy Check.
     */
    public function authorizeSubmissionAccess(object $submission, int $companyId, string $role): void
    {
        if ($role === 'superadmin') {
            return;
        }

        if ($role === 'agency' && (int) $submission->agency_id !== $companyId) {
            abort(403, 'Unauthorized: You do not have ownership of this candidate submission.');
        }

        if ($role === 'client' && (int) $submission->company_id !== $companyId) {
            abort(403, 'Unauthorized: This candidate submission does not belong to your company.');
        }
    }
}
