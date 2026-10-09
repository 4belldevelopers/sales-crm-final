<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadResumeToTestRTwo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;

    protected $full_name;

    protected $jobApplicationId;

    protected $company_name;

    protected $year;

    protected $month;

    protected $targetR2Path;

    /**
     * Create a new job instance.
     * Intent: Support pre-computed unique R2 path to avoid cross-candidate CV mismatches in async queue environments.
     *
     * @return void
     */
    public function __construct($tempPath, $full_name, $jobApplicationId, $company_name, $year, $month, $targetR2Path = null)
    {
        $this->tempPath = $tempPath;
        $this->full_name = $full_name;
        $this->jobApplicationId = $jobApplicationId;
        $this->company_name = $company_name;
        $this->year = $year;
        $this->month = $month;
        $this->targetR2Path = $targetR2Path;

        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tempPath = $this->tempPath;    // example: temp/JERO_123.pdf

        if (empty($tempPath)) {
            \Log::error('UploadResumeToTestRTwo: tempPath is null — job dispatched without an uploaded file', [
                'jobApplicationId' => $this->jobApplicationId,
                'full_name' => $this->full_name,
            ]);

            return;
        }

        // ✅ CHECK INSIDE R2 (NOT LOCAL)
        if (! Storage::disk('r2')->exists($tempPath)) {

            \Log::error('Temp missing in R2: '.$tempPath);

            return;
        }

        $extension = pathinfo($tempPath, PATHINFO_EXTENSION);

        // ⭐ YEAR / MONTH
        $year = $this->year;
        $month = $this->month;

        $company = $this->company_name;

        // Intent: Prevent R2 object key overwrites when different candidates upload files with identical filenames.
        if (! empty($this->targetR2Path)) {
            $r2Path = ltrim(str_replace('https://file.jero.works/', '', $this->targetR2Path), '/');
        } else {
            $baseName = pathinfo($this->full_name, PATHINFO_FILENAME);
            $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
            $uuidSuffix = ! empty($this->uuid) ? $this->uuid : Str::random(8);
            $uniqueFileName = "{$this->jobApplicationId}_{$safeBaseName}_{$uuidSuffix}.{$extension}";

            // ⭐ FINAL R2 PATH (Collision-proof)
            $r2Path = "Companies/{$company}/Resumes/{$year}/{$month}/{$uniqueFileName}";
        }
        $dbSave = 'https://file.jero.works/'.$r2Path;

        // ✅ MOVE FILE INSIDE R2
        try {
            Storage::disk('r2')->move($tempPath, $r2Path);
        } catch (\Throwable $me) {
            \Log::warning('UploadResumeToTestRTwo: R2 move fallback: '.$me->getMessage());
        }

        DB::table('job_applications')
            ->where('id', $this->jobApplicationId)
            ->update(['resume' => $dbSave]);

        $appRow = DB::table('job_applications')->where('id', $this->jobApplicationId)->first(['company_id', 'current_resume_id']);
        $companyId = $appRow?->company_id ?? 0;

        try {
            DB::table('documents')->updateOrInsert(
                [
                    'documentable_type' => 'App\JobApplication',
                    'documentable_id' => $this->jobApplicationId,
                    'name' => 'Resume',
                ],
                [
                    'hashname' => $dbSave,
                    'company_id' => $companyId,
                    'updated_at' => now(),
                ]
            );
        } catch (\Throwable $de) {
            \Log::warning('UploadResumeToTestRTwo: documents update error: '.$de->getMessage());
        }

        try {
            DB::table('resume_assets')
                ->where('candidate_id', $this->jobApplicationId)
                ->where('status', 'ACTIVE')
                ->update([
                    'object_key' => $dbSave,
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $re) {
            \Log::warning('UploadResumeToTestRTwo: resume_assets update error: '.$re->getMessage());
        }

        try {
            \App\Jobs\SyncCandidateToElasticJob::dispatch($this->jobApplicationId);
        } catch (\Throwable $ee) {
            \Log::warning('UploadResumeToTestRTwo: SyncCandidateToElasticJob dispatch error: '.$ee->getMessage());
        }

        \Log::info('R2 temp moved to Resumes with unique key', ['r2Path' => $r2Path]);
    }
}
