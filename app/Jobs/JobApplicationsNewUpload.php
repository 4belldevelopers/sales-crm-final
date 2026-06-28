<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use App\Services\JobApplicationsNewUploadService;

class JobApplicationsNewUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $requestAll;
    public $company_id;
    public $submitted_by;
    public $jobApplication_company_id;
    public $company_id_roles;
    public $app_duplicate;
    public $app_duplicate_days;
    protected $jobApplicationsNewUploadService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestAll,$company_id,$submitted_by,$jobApplication_company_id,$company_id_roles,$app_duplicate,$app_duplicate_days)
    {
        $this->requestAll = $requestAll;
        $this->company_id = $company_id;
        $this->submitted_by = $submitted_by;
        $this->jobApplication_company_id = $jobApplication_company_id;
        $this->company_id_roles = $company_id_roles;
        $this->app_duplicate_days = $app_duplicate_days;
        $this->app_duplicate = $app_duplicate;
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(JobApplicationsNewUploadService $jobApplicationsNewUploadService)
    {
        try {
          
        $jobApplicationsNewUploadService->candidateIndexElastic($this->requestAll,$this->company_id,$this->submitted_by,$this->jobApplication_company_id,$this->company_id_roles,$this->app_duplicate,$this->app_duplicate_days);
            } catch (\Throwable $e) {
                \Log::error('JobApplicationsNewUpload Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $this->requestAll, // Log input data too
                ]);
                throw $e; // re-throw so Laravel knows the job failed
            }
    }
}
