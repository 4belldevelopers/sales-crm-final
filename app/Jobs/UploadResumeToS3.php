<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\JobApplication;

class UploadResumeToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $full_name;
    protected $jobApplicationId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tempPath, $full_name, $jobApplicationId)
    {
        $this->tempPath = $tempPath;
        $this->full_name = $full_name;
        $this->jobApplicationId = $jobApplicationId;

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
            $localFilePath = storage_path('app/' . $this->tempPath);
            if (!file_exists($localFilePath)) {
                \Log::error("Temp resume file not found: " . $localFilePath);
                return;
            }
                // Prepare file name
            $extension = pathinfo($localFilePath, PATHINFO_EXTENSION);
            $filename = "Jero_" . str_replace(' ', '_', substr($this->full_name, 0, 10)) . '.' . $extension;
            $s3Path = 'Resumes/' . $filename;

            // Upload to S3
            // Storage::disk('s3')->put($s3Path, file_get_contents($localFilePath));
            Storage::disk('s3')->put($s3Path,file_get_contents($localFilePath), ['visibility' => 'public']);
            $fileUrl = Storage::disk('s3')->url($s3Path);

            // Save to DB
            $jobApplication = JobApplication::withoutGlobalScopes()->find($this->jobApplicationId);
            $jobApp = JobApplication::withoutGlobalScopes()->find($this->jobApplicationId);
            if ($jobApplication) {
                $jobApp->resume = $fileUrl;
                $jobApp->save();
                $jobApplication->documents()->updateOrCreate(
                    [
                        'documentable_type' => JobApplication::class,
                        'documentable_id' => $this->jobApplicationId,
                        'name' => 'Resume',
                    ],
                    [
                        'hashname' => $fileUrl
                    ]
                );
            }
            
            // Clean up temp file
            unlink($localFilePath);

    }
}
