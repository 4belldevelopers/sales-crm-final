<?php

namespace App\Services;

use App\Models\ForwardAttachment;
use Aws\S3\S3Client;
use Illuminate\Support\Str;

/**
 * Class ForwardResumeUploadService
 *
 * Intent: Orchestrates direct-to-R2 ephemeral upload intent generation,
 * presigned URL creation, and in-memory byte streaming for Forward Resume (Engine 2).
 *
 * Invariants:
 * - Zero local server disk writes (INV-1).
 * - Zero PDF bytes traversing PHP during upload (INV-2).
 * - Multi-tenant isolation enforced via company_id (INV-6).
 */
class ForwardResumeUploadService
{
    private ?S3Client $client = null;

    private string $bucket;

    public function __construct()
    {
        $disk = config('filesystems.disks.r2') ?: [];
        $s3Disk = config('filesystems.disks.s3') ?: [];

        $this->bucket = $disk['bucket'] ?? (config('resumes.r2.temp_bucket') ?: ($s3Disk['bucket'] ?? 'jero-resume-uploads-temp'));

        $key = $disk['key'] ?? ($s3Disk['key'] ?? null);
        $secret = $disk['secret'] ?? ($s3Disk['secret'] ?? null);
        $endpoint = $disk['endpoint'] ?? (config('resumes.r2.endpoint') ?: ($s3Disk['endpoint'] ?? null));
        $region = $disk['region'] ?? (config('resumes.r2.region') ?: 'auto');

        if (! empty($key) && ! empty($secret) && ! empty($endpoint)) {
            try {
                $this->client = new S3Client([
                    'version' => 'latest',
                    'region' => $region,
                    'endpoint' => $endpoint,
                    'use_path_style_endpoint' => true,
                    'credentials' => [
                        'key' => $key,
                        'secret' => $secret,
                    ],
                    'signature_version' => 'v4',
                ]);
            } catch (\Throwable $e) {
                \Log::warning('[ForwardResumeUploadService] S3Client initialization skipped: '.$e->getMessage());
                $this->client = null;
            }
        }
    }

    /**
     * Check whether Cloudflare R2 / S3 storage is properly configured for presigned direct uploads.
     */
    public function isR2Configured(): bool
    {
        return $this->client !== null;
    }

    /**
     * Generate batch presigned PUT URLs for direct browser-to-R2 upload.
     * Eliminates sequential waterfall round-trips.
     *
     * @param  int  $companyId  Tenant company ID
     * @param  int  $userId  Recruiter user ID
     * @param  array  $files  List of file descriptors: [['candidate_id' => ..., 'filename' => ..., 'size' => ...]]
     * @param  int  $expirySeconds  TTL of presigned URL (default 15 minutes)
     * @return array<int, array{attachment_id: string, upload_url: string}>
     */
    public function initiateBatchUploads(int $companyId, int $userId, array $files, int $expirySeconds = 900): array
    {
        if (! $this->isR2Configured()) {
            return [];
        }

        $results = [];

        foreach ($files as $file) {
            $candidateId = (int) ($file['candidate_id'] ?? 0);
            $rawFilename = (string) ($file['filename'] ?? 'Candidate_Resume.pdf');
            $fileSize = ! empty($file['size']) ? (int) $file['size'] : null;
            $mimeType = ! empty($file['mime']) ? (string) $file['mime'] : 'application/pdf';

            $attUuid = (string) Str::uuid();
            $objectKey = sprintf('temp-forward/%d/%s.pdf', $companyId, $attUuid);

            $cmd = $this->client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
                'ContentType' => $mimeType,
            ]);

            $presigned = $this->client->createPresignedRequest($cmd, '+'.$expirySeconds.' seconds');
            $uploadUrl = (string) $presigned->getUri();

            // Safely record tracking record if table exists (graceful degradation)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('forward_attachments')) {
                    ForwardAttachment::create([
                        'uuid' => $attUuid,
                        'company_id' => $companyId,
                        'candidate_id' => $candidateId ?: null,
                        'r2_object_key' => $objectKey,
                        'filename' => $rawFilename,
                        'file_size_bytes' => $fileSize,
                        'mime_type' => $mimeType,
                        'status' => 'PENDING',
                        'expires_at' => now()->addHours(4),
                    ]);
                }
            } catch (\Throwable $dbErr) {
                \Log::warning('[ForwardResume] Tracking record creation skipped: '.$dbErr->getMessage());
            }

            $results[$candidateId] = [
                'attachment_id' => $attUuid,
                'upload_url' => $uploadUrl,
            ];
        }

        return $results;
    }

    /**
     * Stream binary bytes directly from private Cloudflare R2 into worker RAM.
     * Server hard drive is never touched.
     *
     * @param  string  $objectKey  R2 key
     * @return string|null Raw PDF bytes or null on failure
     */
    public function getR2ObjectBytes(string $objectKey): ?string
    {
        if (! $this->client) {
            return null;
        }

        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
            ]);

            return (string) $result['Body'];
        } catch (\Throwable $e) {
            \Log::error('Failed to stream attachment from Cloudflare R2: '.$e->getMessage(), [
                'bucket' => $this->bucket,
                'key' => $objectKey,
            ]);

            return null;
        }
    }

    /**
     * Idempotently delete temporary attachment object from Cloudflare R2.
     *
     * @param  string  $objectKey  R2 key
     * @return bool True if deleted or did not exist
     */
    public function deleteR2Attachment(string $objectKey): bool
    {
        if (! $this->client) {
            return true;
        }

        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
            ]);

            return true;
        } catch (\Throwable $e) {
            \Log::warning('Non-fatal warning deleting R2 temporary attachment: '.$e->getMessage(), [
                'bucket' => $this->bucket,
                'key' => $objectKey,
            ]);

            return false;
        }
    }
}
