<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Illuminate\Support\Facades\Storage;
use App\Helper\GeneralMethod;
use App\Models\Job;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\Client;
use App\Jobs\QueueNotificationMailService;
use Carbon\Carbon;
use App\Services\MailService;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Mail;

class AutoCloseJobsService
{
    protected $mailService;
    protected $elasticService;

    public function __construct(MailService $mailService,ElasticService $elasticService)
    {
      
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        
    }

    /**
     * Close expired active jobs for a company
     */
    public function closeExpiredJobs(int $companyId): int
    {

        $end_date = date('Y-m-d H:i:s');
        // $jobsCount = DB::table('jobs')->where('status','=','active')->where('end_date', '<',$end_date)->pluck('jobs.id')->toArray();

        $end_date = date('Y-m-d'); // or your existing $end_date

        // dd($companyId);
        
        $params = [
            'index' => ELASTIC_JOBS,
            'size'  => 10000, // ES max result window
            '_source' => ['id'], // fetch only job ID
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'status' => 'active'
                                ]
                            ],
                            [
                                'term' => [
                                    'comId' =>  $companyId
                                ]
                            ],
                            [
                                'range' => [
                                    'end_date' => [
                                        'lt' => $end_date
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->elasticService->getClient()->search($params);
    
        $jobsCount = [];

        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                if (isset($hit['_source']['id'])) {
                    $jobsCount[] = $hit['_source']['id'];
                }
            }
        }

        // dd($jobsCount);


        // dd($jobsCount,$end_date);

        if(count(@$jobsCount)>0){
                    $valid_day = 0;
                    $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));

                    // dd($valid_date);

                    $candidateRequCount = DB::table('rec_candidate_assign')->where('created_at', '<', $valid_date)->where('ac_status','=',0)->get();

                    DB::table('jobs')->whereIn('id',$jobsCount)->where('status','=','active')->update(['status'=>'inactive','closeType'=>1,'public_market_status'=>0]);
                    DB::table('recommendation_relation')->whereIn('job_id',$jobsCount)->where('final_status','=',1)->update(['final_status'=>2]);

                    foreach($jobsCount as $jobid){

                        $m_n_id = 47;

                        $checkMailPermision = GeneralMethod::mailPermission($m_n_id);
    
                        if($checkMailPermision && $m_n_id >0){ 
                                $job = Job::find($jobid);
                                $main_user = DB::table('users')->where('users.root_user', 1)->where('company_id', $job->company_id)->first();  

                                $senderName = ($main_user) ? $main_user->name : '';
                                $fromAddress = ($main_user) ? $main_user->email : '';

                                $company_id = $job->company_id;
                                $mailTypeSubject = GeneralMethod::mailTypeSubject($m_n_id,1);
                                $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
                                $currentJobId = $jobid;
                                $currentUserId = $main_user->id;
                                $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
                                
                                $employmentType = GeneralMethod::employmentType();   
                                $employment_type = (int) $job->employment_type;
                                $employment_type = $employmentType[$employment_type];
                                $currency = GeneralMethod::currencySymbole();
                                $currencyGet = GeneralMethod::currencySymboleforexcell();
                                $salaryMode = GeneralMethod::salaryMode();

                                

                            $recruiter = DB::table('users')
                            ->join('job_assign','job_assign.user_id','=','users.id')
                            ->where('job_assign.job_id', $currentJobId)->pluck('users.name')->toArray();

                            $recruiterdata = (implode(",",$recruiter));

                            $hiring_namager = DB::table('client_contact_info')
                            ->join('client_contact_info_jobs','client_contact_info_jobs.client_contact_info_id','=','client_contact_info.id')
                            ->where('client_contact_info_jobs.job_id', $currentJobId)->pluck('client_contact_info.contact_person_name')->toArray();
                            $hiring_namagerdata = (implode(",",$hiring_namager));
                            

                            $strAdded = DB::table('clients')->select('companies.company_name','clients.name','clients.self_company')->join('companies','companies.id','=','clients.company_id')->where('clients.id', $job->client_id)->first();
                          
                            $contact_person_name = '';
                            if($strAdded){
                                if(@$strAdded->self_company==1){
                                        $contact_person_name = @$strAdded->company_name;
                                }else{
                                    $contact_person_name = @$strAdded->company_name;
                                    if($company_id==$job->company_id){
                                        $contact_person_name = @$strAdded->name;
                                    }
                                        
                                }
                            }
                            
                            // dd($strAdded);
                            

                            $jobAdditional = Job::Select('clients.name as client_name','clients.description as client_description','clients.logo_logical_path','job_categories.name as cat_name','qualification_master.name as qualification_name')
                                    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                                    ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
                                    ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
                                    ->find($jobid);

                                    // dd($jobAdditional,$job->id);

                            $assignTeamemail = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->where('users.company_id', $company_id)
                            ->where('job_assign.job_id', $job->id)
                            ->pluck('users.email')->toArray();

                                    $cc = $assignTeamemail;
                                    $attachments = array();


                                $oprnlink =  url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($job->id) . '/all');

                                $subject = str_replace(["[{JOB_TITLE}]","[{ClientCompany}]","[{Location}]","[{JobID}]"],[@$job->title, @$contact_person_name,"R-".@$job->id], $mailTypeSubject->subject);

                                
                                $maillink = "You may access and manage the job posting using the link below:<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>Click here to open the job</a> <br><br> If the above link does not open directly, you may copy and paste the below link it into your browser.<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink."</a>";

                                if($job->status=='active'){
                                    $status_job = "<strong><span style='color:#27ae60'>Active</span></strong>";
                                }elseif($job->status=='inactive'){
                                    $status_job = "<strong><span style='color:#ff0202'>Closed</span></strong>";
                                }else{
                                    $status_job = "<strong><span style='color:#ffab00'>Hold</span></strong>";
                                }
                                
                                

                                $template_description = str_replace(["[{status}]","[{clientCompany}]","[{job_id}]","[{job_title}]","[{experience}]","[{salaryrange}]","[{NoofOpenings}]","[{location}]","[{remote}]","[{JobType}]","[{Department}]","[{Qualification}]","[{hiringManager}]","[{recruiter}]","[{addedby}]","[{JobDescriptions}]","[{maillink}]"],[@$status_job, @$contact_person_name,"R-".@$job->id, @$job->title, @$job->min_exp.'-'.@$job->max_exp .' Years', $currencyGet[@$job->min_salary_currency].' '. @$job->min_salary.' to '.@$job->max_salary.' '.$max_sal_mode,  @$job->total_positions, @$client_loc->city_name, @$work_type, @$employment_type, $jobAdditional->cat_name,$jobAdditional->qualification_name,@$hiring_namagerdata,$recruiterdata,@$contact_person_name,@$job->job_description,$maillink], $mailTypeSubject->body_description);


                                $data = [
                                    'message' => $template_description
                                ];

                                $defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
                                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? ''; 
                                $teamsEmail = DB::table('users')->where('users.id', $currentUserId)->where('company_id', $company_id)->first();  

                                QueueNotificationMailService::dispatch(
                                    $teamsEmail->email,
                                    $subject,
                                    'email.common.index',
                                    (object) $data,
                                    $attachments,
                                    $fromAddress,
                                    $senderName,
                                    $cc,
                                    $currentUserId
                                )->onConnection('emails');     
                        }

                        $exists_jobs = $this->elasticService->getClient()->exists([
                                'index' => ELASTIC_JOBS,
                                'id' => $jobid
                        ]);
                            if ($exists_jobs) {
                                $updateParams = [
                                    'index' => ELASTIC_JOBS,
                                    'id' => $jobid,
                                    'body' => [
                                        'doc' => [
                                            'status' => 'inactive',
                                        ],
                                        'doc_as_upsert' => false // Changed to false since we verified existence
                                    ],
                                    'refresh' => true // Force immediate refresh
                                ];
                            $this->elasticService->getClient()->update($updateParams);
                            
                           
                        }
                    }	
                    return 1;
        }
         return 2;
       

    }
}
