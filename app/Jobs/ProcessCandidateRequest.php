<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Job;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;
use Reply;
use App\Services\MailService;

class ProcessCandidateRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobId;
    protected $conId;
    protected $companyId;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId, $conId, $companyId, $userId)
    {
        $this->jobId = $jobId;
        $this->conId = $conId;
        $this->companyId = $companyId;
        $this->userId = $userId;
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
        $job = Job::findOrFail($this->jobId);
        $source_from_company_id = $this->companyId;
        $source_from_user_id = $this->userId;
        $source_from_type = 1;
        $source_company_id = $job->company_id;
        $source_application_id = (int) $this->conId;
        $source_jobid = (int) $this->jobId;
        $values = [
            'source_from_company_id' => $source_from_company_id,
            'source_from_user_id' => $source_from_user_id,
            'source_from_type' => $source_from_type,
            'source_company_id' => $source_company_id,
            'source_application_id' => $source_application_id,
            'source_from_id' => $source_application_id,
            'source_jobid' => $source_jobid
        ];
        $row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
        $jobcountry = GeneralMethod::JobCountries($source_jobid);
        $country = @$row_data->country;

       
      
        // dd("d");
        $query = false;
        if (@$source_jobid > 0) {
            $event_name = in_array($country, $jobcountry) ? 'BenchSalesIndia' : 'BenchSalesInternational';
            // $query = DB::table('rec_candidate_assign')->insert($values);
        }

        if ($query) {
            $packageStatus = GeneralMethod::clientPackageRedeem($event_name, 'jobs', $source_jobid, $source_from_company_id);
           
        }
    

    }
}
