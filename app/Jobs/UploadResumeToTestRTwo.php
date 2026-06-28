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

class UploadResumeToTestRTwo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $full_name;
    protected $jobApplicationId;
    protected $company_name;
    protected $year;
    protected $month;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tempPath, $full_name, $jobApplicationId,$company_name,$year,$month)
    {
        $this->tempPath = $tempPath;
        $this->full_name = $full_name;
        $this->jobApplicationId = $jobApplicationId;
        $this->company_name = $company_name;
        $this->year = $year;
        $this->month = $month;

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

    // ✅ CHECK INSIDE R2 (NOT LOCAL)
    if (!Storage::disk('r2')->exists($tempPath)) {

        \Log::error("Temp missing in R2: ".$tempPath);
        return;
    }

    $extension = pathinfo($tempPath, PATHINFO_EXTENSION);

        // ⭐ YEAR / MONTH
    $year  = $this->year;
    $month = $this->month;

    $finalName = $this->full_name;
    $company = $this->company_name;

        // ⭐ FINAL R2 PATH
    $r2Path = "Companies/{$company}/Resumes/{$year}/{$month}/{$finalName}";
    // $r2Path = 'Resumes/'.$finalName;
    // $dbSave = Storage::disk('r2')->url($r2Path);
    $dbSave = "https://file.jero.works/".$r2Path;

    // ✅ MOVE FILE INSIDE R2
    Storage::disk('r2')->move($tempPath, $r2Path);

    DB::table('job_applications')
        ->where('id',$this->jobApplicationId)
        ->update(['resume'=>$dbSave]);

    \Log::info("R2 temp moved to Resumes");
}
}
