<?php

namespace App\Jobs;

use App\Models\ResumeAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Intent: Manages Class 2 Removed Asset Cleanup (candidate-resumes/*).
 * Purges files from R2 after the retention grace period has elapsed.
 * Guarantees that physical storage errors do NOT roll back or corrupt database rows (R-I08).
 */
class PurgeRemovedResumeAssetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly ?int $resumeAssetId = null,
        private readonly bool $forceNow = false,
    ) {
        $this->onQueue('candidate-index');
    }

    public function handle(): void
    {
        if ($this->resumeAssetId !== null) {
            $this->purgeSingleAsset($this->resumeAssetId);
            return;
        }

        // Batch sweep: purge all REMOVAL_PENDING assets whose retention period expired (7 days)
        $threshold = now()->subDays(7);
        $assets = ResumeAsset::withoutGlobalScopes()
            ->where('status', 'REMOVAL_PENDING')
            ->where('removed_at', '<=', $threshold)
            ->limit(100)
            ->get();

        foreach ($assets as $asset) {
            $this->purgeSingleAsset($asset->id);
        }
    }

    private function purgeSingleAsset(int $assetId): void
    {
        $asset = ResumeAsset::withoutGlobalScopes()->find($assetId);
        if (! $asset) {
            return;
        }

        // Only purge if marked REMOVAL_PENDING or CLEANUP_PENDING
        if (! in_array($asset->status, ['REMOVAL_PENDING', 'CLEANUP_PENDING', 'ORPHANED'], true)) {
            return;
        }

        // Check grace period unless forceNow is specified
        if (! $this->forceNow && $asset->removed_at && $asset->removed_at->gt(now()->subDays(7))) {
            return;
        }

        $r2Deleted = false;
        try {
            if (! empty($asset->object_key)) {
                // If it's a full URL, extract path
                $path = $asset->object_key;
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    $parsed = parse_url($path, PHP_URL_PATH);
                    $path = ltrim((string) $parsed, '/');
                }
                $r2Deleted = Storage::disk('r2')->delete($path);
            }
        } catch (\Throwable $e) {
            Log::warning('PurgeRemovedResumeAssetsJob: R2 physical deletion failed safely', [
                'asset_id' => $assetId,
                'object_key' => $asset->object_key,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            DB::table('resume_assets')
                ->where('id', $assetId)
                ->update([
                    'status' => 'REMOVED',
                ]);
        } catch (\Throwable $e) {
            Log::error('PurgeRemovedResumeAssetsJob: Failed updating asset status to REMOVED', [
                'asset_id' => $assetId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
