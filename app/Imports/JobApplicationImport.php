<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use App\ApplicationStatus;
use App\JobApplication;
use App\User;
use Carbon\Carbon;
use App\Helper\GeneralMethod;

class JobApplicationImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection(Collection $collection)
    {
    //    dd($collection);
        // $filePath = $collection->store('temp');

		// Get the real path of the uploaded file
		// $realPath = Storage::path($filePath);
        $chunkSize = 50;
		// $rows = Excel::toCollection([], $filePath)->first();
        $chunks = $collection->chunk($chunkSize);
        // dd($chunks ,$collection);

        foreach ($chunks as $chunk) {
            // return $chunk;
            $collection = $chunk->toArray();
			if ($collection) {          
                
                foreach($collection as $key=> $row) {
                    
                    if($row[0] !='Full Name') {
                        // dd($row[0]);
                       
                        $qualification_master = DB::table('qualification_master')->select('id')->where('name', 'LIKE', '%' . $row[8] . '%')->first();

                        $countr_master = DB::table('countries')->select('id')->where('country_name', '=', $row[15])->first();
                        $state_master = DB::table('state_list')->select('id')->where('state', '=', $row[16])->first();
                        $city_master = DB::table('city_list')->select('city_id')->where('city', '=', $row[17])->first();

                        // dd($qualification_master,$countr_master,$state_master,$city_master);

                        $company_id = '';
                        $company_id = auth()->user()->company_id;

                        $applicationStatus = ApplicationStatus::first();
                        $jobApplication = new JobApplication();


                        $jobApplication->full_name = $row[0];
                        $jobApplication->job_id = $this->id;
                        $jobApplication->status_id = 8;
                        $jobApplication->email = $row[6];
                        $jobApplication->phone = $row[3];
                        $jobApplication->column_priority = 0;
                        $jobApplication->total_exp = $row[9];
                        $jobApplication->rel_exp = $row[10];
                        $jobApplication->ctc = $row[12];
                        $jobApplication->current_ctc_cur = 1;
                        $jobApplication->current_ctc_mode = 1;

                        $jobApplication->ectc = $row[13];
                        $jobApplication->current_ectc_cur = 1;
                        $jobApplication->current_ectc_mode = 1;

                        $jobApplication->percentage_hike = $row[14];
                        $jobApplication->comment = '';
                        $jobApplication->pincode = $row[18];

                        //$jobApplication->current_location 	=	 $row[0]->current_location;
                        $jobApplication->current_designation = $row[1];
                        $jobApplication->current_company = $row[2];



                        $jobApplication->skills = $row[19];

                        //$jobApplication->master_status_id 	=	$applicationStatus->id;
                        $jobApplication->master_status_id = 8;
                        $jobApplication->secondrary_email = $row[7];
                        $jobApplication->secondary_mobile = $row[4];
                        $submitted_by = '';
                        $submitted_by = auth()->user()->id;
                        if (!empty($submitted_by)) {
                            $jobApplication->submitted_by = $submitted_by;
                        }


                        $jobApplication->np_id = $row[11];

                        if (!empty($row[8])) {
                            $jobApplication->qualification = ($qualification_master) ? $qualification_master->id : 1;
                        }
                        $jobApplication->company_id = $company_id;
                        //   dd(date('Y-m-d', strtotime($row[5])));
                        if ($row[5]) {
                            $formattedDate =date('Y-m-d', strtotime($row[5]));
                            $jobApplication->dob = $formattedDate;
                        }
                        // dd($jobApplication->dob);

                        $jobApplication->country = ($countr_master) ? $countr_master->id : 101;
                        $jobApplication->state = ($state_master) ? $state_master->id : '';


                        $jobApplication->city_id = ($city_master) ? $city_master->city_id : '';

                        $jobApplication->city = ($city_master) ? '' : $row[17];



                        $applicationEmail = $row[6];
                        $chkApplication = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('email', $applicationEmail)->orWhere('phone', $row[3])->count();
                        if ($chkApplication > 0) {
                            $jobApplication->duplicate = 1;
                        }
                        //    dd($jobApplication);
                        $jobApplication->save();


                        $insertArr = array('job_id' => $this->id, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by);
                        DB::table('add_job_wise_condidates')->insert($insertArr);
                        $lastInsertedId = DB::getPdo()->lastInsertId();
                        $jobId = $this->id;


                        $wise_c = DB::table('job_applications')
                            ->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
                            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                            ->leftjoin('companies', 'companies.id', 'users.company_id')
                            ->join('master_status', function ($join) {
                                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                            })->join('jobs', function ($join) {
                                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                            })
                            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                            ->where('add_job_wise_condidates.id', '=', $lastInsertedId)->where('add_job_wise_condidates.job_id', '=', $jobId)->first();

                        $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->where('job_assign.job_id', '=', $jobId)->pluck('users.email')->toArray();

                     

                        $recommendedDataList = DB::table('recommendation_relation');
                        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
                        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
                        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
                            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
                            $join->on('job_applications.job_id', '=', 'jobs.id');
                        });
                        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
                        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
                        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
                        $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
                        $recommendedDataList = $recommendedDataList->toArray();


                        $company_or_client = GeneralMethod::companyDetailsByClient($jobId);

                        $name = '--------';
                        if (@$wise_c->master_status_id == 8) {
                            $name = '';
                        }
                        if (!empty(@$wise_c->childStatus) && @$wise_c->childStatus > 0) {
                            $childStatus = '';
                            $childStatus = DB::table('candidate_status')->select('*')->where('id', $wise_c->childStatus)->first();
                            if (!empty($childStatus)) {
                                $name = @$childStatus->name;
                            }
                        }

                        $np = $jobApplication->np_id;
                        if (empty($np)) {
                            $np = 'N/A';
                        }
                        if ($jobApplication->serving_notice_period_mode == 0) {
                            $np = $jobApplication->np_id . " Days";
                            if ($jobApplication->np_id == 1) {
                                $np = $jobApplication->np_id . '  Day';
                            }
                        } elseif ($jobApplication->serving_notice_period_mode == 1) {
                            $np = $jobApplication->np_id;
                            if ($np != 'Immediate') {
                                $np = date("d-m-Y", strtotime($jobApplication->np_id));
                            }
                        }

                        $data = [
                            'VendorTeam' => @$wise_c->company_name,
                            'ReqID' => "R-" . @$wise_c->job_id,
                            'Reqname' => @$wise_c->title,
                            'CompanyNameClientname' => $company_or_client,
                            'CandidateStatus' => $jobApplication->duplicate,
                            'CurrentStage' => @$wise_c->name,
                            'SubStage' => $name,
                            "CandidateName" => $jobApplication->full_name,
                            "Experience" => $jobApplication->total_exp,
                            'Position' => $jobApplication->current_designation,
                            'NoticePeriod' => $np,
                            'RefferedBy' => @$wise_c->posted_by,
                            'SubmittedOn' => date("d-m-Y h:i:s", strtotime(@$wise_c->created_at))
                        ];
                        config(['mail.from.name' => auth()->user()->name]);
                        $cc = array_merge($partnersData, $recommendedDataList);
                        // Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data));


                        $chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $jobId)->where('user_id', $submitted_by)->count();
                        if ($chkJobAssign < 1) {
                            $values = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => $jobId);
                            DB::table('job_assign')->insert($values);
                        }
                        // dd("kf",$row[19]);
                        if (!empty($row[19])) {
                            //  DB::table('candidate_skills_value')->where('job_id', $jobId)->where('candidate_id', $jobApplication->id)->delete();
                            // dd("hi");
                            $skill_value  = explode(",", $row[19]);
                            foreach ($skill_value as $key => $value) {
                                if (!empty(@$value)) {
                                    // dd($value,$jobApplication);
                                    $values = array('job_id' => @$jobId, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value, );
                                    DB::table('candidate_skills_value')->insert($values);
                                }
                            }
                        }

                        $timelineArr = array(
                            'event_id' => 1,
                            'event_by_company_id' => auth()->user()->company_id,
                            'event_by_user_id' => auth()->user()->id,
                            'master_process_name' => 'InReview',
                            //'child_process_name'	=>	

                            'application_id' => $jobApplication->id,
                            'application_email' => $jobApplication->email,
                            'job_id' => $jobId,
                            'note' => 'Successfully Added'
                        );
                        GeneralMethod::UpdateTimeline($timelineArr);
                        // Job Application Answer save
                    
                        $job_additional_detail_arr = array_slice($row,20);
                        // dd($job_additional_detail_arr);

                        $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->count();
                        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->get();
                        // dd($job_additional_detail);
                        if ($job_additional_detail_count > 0) {
                            $inc = 0;
                            foreach ($job_additional_detail as $jd) {
                                    // dd($job_additional_detail_arr[$inc]);
                                $values = array(
                                    'job_id' => @$jobId,
                                    'question_id' => @$jd->id,
                                    'application_id' => @$jobApplication->id,
                                    'job_detail_value' => @$jd->detail_value,
                                    'application_detail_value' => $job_additional_detail_arr[$inc]
                                );
                                // dd($values);
                               DB::table('application_additional_detail')->insert($values);
                                $inc++;
                          
                            }
                        }

                    }

                }
            }
        }
            
        }

    
}
