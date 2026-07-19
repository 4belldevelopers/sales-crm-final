<?php

/***########################################################

Controller    	: 	AdminJobApplicationController 
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	

########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Models\ApplicationSetting;
use App\Models\ApplicationStatus;
use App\Models\Company;
use App\Exports\JobApplicationExport;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\Models\InterviewSchedule;
use App\Models\Job;
use Illuminate\Support\Facades\Gate;
use App\Jobs\SendEmailJob;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;
use App\Jobs\ElasticCompanyIndex;
use App\Jobs\ElasticCandidateIndex;
use App\Jobs\JobApplicationsNewUpload;
use App\Jobs\QueueStackCandidates;
use App\Jobs\UploadResumeToS3;
use App\Jobs\UploadResumeToTestS3;
use App\Jobs\UploadResumeToTestRTwo;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\AddJobWiseCondidate;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\CandidateStatusChange;
use App\Notifications\ScheduleInterview;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OfferJobApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AddCandidateMail;
use App\Mail\OfferMail;
use App\Mail\JoinedUsTodayMail;
use App\Mail\RejectedMail;
use App\Mail\ChangeStatusMail;
use App\Mail\InterviewTrackerMail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\Helper\GeneralMethod;
use App\Models\Country;
use App\Exports\JobApplicationSampleExport;
use App\Exports\JobApplicationCustomExport;
use App\Imports\JobApplicationImport;
use App\Helper\Zoom;
use App\Helper\Google;
use Barryvdh\DomPDF\Facade as PDF;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Mail\Message;
use Google_Client as GoogleClient;
use Google_Service_Calendar as Calendar;
use Google_Service_Calendar_Event;
use Validator;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use App\Services\GoogleSheetServiceCandidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use App\Services\ElasticService;
use App\Models\ElasticCandidate;



class ServiceCompanyJobApplicationController extends ServiceCompanyBaseController
{
protected $mailService;
protected $massMailService;
protected $notificationMailService;
protected $elasticService;

	public function __construct(MailService $mailService, GoogleSheetServiceCandidate $GoogleSheetServiceCandidate, MassMailService $massMailService, NotificationMailService $notificationMailService,ElasticService $elasticService)
	{
		
		parent::__construct();
		$this->pageTitle = 'menu.jobApplications';
		$this->pageIcon = 'icon-user';
		$this->mailService = $mailService;
		$this->GoogleSheetServiceCandidate = $GoogleSheetServiceCandidate;
		$this->massMailService = $massMailService;
		$this->notificationMailService = $notificationMailService;
		$this->elasticService = $elasticService;
		
	}
    //**  start ajax  */
	public function uploadresume(Request $request){

        $request->validate([
            'file' => 'required|file|max:6048', // Max size 1MB
        ]);
       
		$file = $request->file('file');

		$filename = uniqid('JERO_').'.'.$file->getClientOriginalExtension();

		$tempPath = $file->storeAs('temp', $filename,'r2'); // STORAGE/app/temp
		// ⭐ GET PUBLIC URL FROM R2
		$tempUrl = Storage::disk('r2')->url($tempPath);
		
		$extension = pathinfo($tempPath, PATHINFO_EXTENSION);
		$full_name = uniqid('JERO_');
		$uniqueName = GeneralMethod::getUniqueCvName($full_name,$extension);
		// ✅ STORE IN SESSION
		// dd($uniqueName);
		session([
			'resume_temp_path' => $tempPath,
			'resume_original_name' => $filename,
		]);

        // Store the file
        if ($request->hasFile('file')) {
            
            // $filePath = $file->store('uploads', 'public'); // Save to 'storage/app/public/uploads'

            // Return a response
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $tempPath,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
        // Handle failure
        return response()->json([
            'success' => false,
            'message' => 'File upload failed',
        ], 400);
    
	}
	public function unsetFile(Request $request)
    {
        $filePath = $request->input('file_path');

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            // Delete the file
            Storage::disk('public')->delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found or already deleted.',
        ], 404);
    }
	public function skillExperienceUpdate(Request $request){

		// dd($request->all());
		$appId = $request->appId;
		$job_id = $request->job_id;

		if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
			$YearskillId = $request->input('YearskillId');
			$MonthskillId = $request->input('MonthskillId');
			$ratingId = $request->input('rating');
			// dd($YearskillId,$MonthskillId);
			DB::table('candidate_job_skill_exp')->where('candidate_id',$appId)->where('job_id', $job_id)->delete();
			$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['candidate_id' => $appId]],
								['term' => ['job_id' => $job_id]],
							],
						],
					],
				],
			]);
			foreach ($YearskillId as $skillId => $value) {
				// if (!empty($value)) { 
				$ss = "Year";
				if($value >=1){
					$ss = "Years";
				}
                if(!empty($MonthskillId[$skillId])){
					$valMonth = $MonthskillId[$skillId] ;
				}else{
					$valMonth = $MonthskillId[$skillId] ;
				}
				 $skill_value = $value.'.'.$valMonth.' '.$ss;
				  $rating = $ratingId[$skillId] ;

					$values = array(
						'job_skill_id' => $skillId,
						'skill_value' => $skill_value,
						'job_id' => $job_id,
						'rating' => (int)$rating,
						'candidate_id' =>$appId
					);
					DB::table('candidate_job_skill_exp')->insert($values);
					$expId = DB::getPdo()->lastInsertId();
					
					$skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillId)->first();
					$this->elasticService->getClient()->index([
						'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'skill_name' => $skills->skill_name ?? '',
							'job_skill_id' => $skillId,
							'skill_value' => $skill_value,
							'job_id' => $job_id,
							'rating' => (int)$rating,
							'candidate_id' =>$appId,
							'created_at' =>  now()->toIso8601String(),
							'updated_at' =>  now()->toIso8601String()
						]
					]);
				// }
			}
            
			   $html = '';
			    $jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) use($appId,$job_id){
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id','=',$appId);
				})
				->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value','candidate_job_skill_exp.rating')
				->where('job_metronics_skills.job_id', '=',$job_id)
				->get();
				
                 $skillShow = true;
				 if(!empty($jobSkill))
                    {
                    $html		=	'<div class="row">';
					$html		.=	'<div class="skills-thead">
										<div>Skill</div>
										<div>Experience</div>
										<div>Self Rating</div>
									</div>';		
                      foreach($jobSkill as $skill_val)
                      {  
                        $exp = 'EXP.';  
                        $yrs = '';      
                          if(!empty($skill_val->skill_value)){
                          
                            $yrs = ''; 
                          }
                          $skillShowView = '';
                          if($skillShow){
                            $skillShowView = $skill_val->skill_value;
                            if (is_int($skill_val->skill_value)) {
                              $skillShowView = $skill_val->skill_value.' '.$yrs;
                            }
                          
                          }
                        
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper($skill_val->skill_name).'
                          </div>
                          <div class="skill-exp-cell">'.$skillShowView.'</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$skill_val->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                                aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$skill_val->rating ?? 0).' / 5</span>
                          </div>
                        </div>';	

                      }
                      $html		.='</div>';			
                     
					}
					

		}
		
		
		$cachekeys = [
			'candidateDetails_version_user_'.$this->user->id
		];
		foreach ($cachekeys as $key) {
			GeneralMethod::cacheClean($key);
		}
		return response()->json(['status'=>true,'html'=>$html]);
		

	}
	public function JobQuestionUpdate(Request $request){

		// dd($request->all());
		$appId = $request->appId;
		$job_id = $request->job_id;
        $html = '';
		DB::table('application_additional_detail')->where('application_id', $appId)->where('job_id', $job_id)->delete();
		$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['application_id' => $appId]],
								['term' => ['job_id' => $job_id]],
							],
						],
					],
				],
			]);

		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$job_id,
					'question_id' => @$jd->id,
					'application_id' => @$appId,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				
				DB::table('application_additional_detail')->insert($values);
				$expId = DB::getPdo()->lastInsertId();
				$this->elasticService->getClient()->update([
						'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
                            'doc' => [
								'job_id' => @$job_id,
								'question_id' =>  @$jd->id,
								'application_id' => @$appId,
								'job_detail_value' => @$jd->detail_value,
								'application_detail_value' =>@$request->additional_detail[$inc],
								'created_at' =>  now()->toIso8601String(),
								'updated_at' =>  now()->toIso8601String()
							],
							'doc_as_upsert' => true // key to avoiding conflicts
						]
					]);
				$inc++;

			}
         
		$job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			// ->where('job_additional_detail.company_id', '=', $company_id)
			->where('job_additional_detail.job_id', '=', @$job_id)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();

		$application_additional_detail = DB::table('application_additional_detail')
			->select('*')
			->where('application_additional_detail.application_id', '=', @$appId)
			->where('application_additional_detail.job_id', '=', @$job_id)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();



			 $customTitleId	=	1;
                      $html			=	'';
                      //$html			=	'<h3 class="py-5">Custom Details</h3>';
                      $inc_first		=	0;
                      $inc_last		=	1;
					  $html			.=	'<div class="skills-thead">
											<div>Job Questions</div>
											<div>Answered</div>
										</div>';
                      foreach($job_additional_detail as $aDetailArr)
                      {
                        
                        $ans_value = $job_detail_value = '';
                        if(!empty($application_additional_detail)){
                          foreach($application_additional_detail as $aval){
                              if($aDetailArr->id == $aval->question_id){
								$job_detail_value = @$aval->job_detail_value;
                                $ans_value	=	@$aval->application_detail_value;	
                              }
                          }
                        }
                
                
                                
                          $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper(@$job_detail_value).'
                          </div>
                          <div class="skill-exp-cell">'.@$ans_value.'</div>
                        </div>';
                        
                        $inc_first++;
                        $inc_last++;
                        $customTitleId++;				
                      }





		}

		$cachekeys = [
			'candidateDetails_version_user_'.$this->user->id
		];
		foreach ($cachekeys as $key) {
			GeneralMethod::cacheClean($key);
		}
		return response()->json(['status'=>true,'html'=>$html]);
		

	}
	public function SlotsUpdate(Request $request){

		
		$addJobWiseCondidate_id = $request->appId;
         

		$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES,
				'id' => $addJobWiseCondidate_id
			]);
			if ($exists) {

			// 	$params = [
			// 	'index' => ELASTIC_CANDIDATES,
			// 	'id'    => $addJobWiseCondidate_id,
			// ];
			
			// $response = $this->elasticService->getClient()->get($params);
			// $data = (object) $response['_source'];
			// dd($data);

			
				$updateTime = now()->toIso8601String();
				
					$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
					
					$interviewDateOne   = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
					$interviewDateTwo   = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
					$interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);

					// interview_mode_one, interview_mode_two, interview_mode_three, interview_mode_four, interview_mode_five
					
				$updateParams = [
					'index' => ELASTIC_CANDIDATES,
					'id' => $addJobWiseCondidate_id,
					'body' => [
						'doc' => [
							'interviewDateOne' => ($interviewDateOne) ? Carbon::parse($interviewDateOne)->toIso8601String() : null,
							'interview_mode_one' => $request->interview_mode_one,
							'interviewDateTwo' => ($interviewDateTwo) ? Carbon::parse($interviewDateTwo)->toIso8601String() : null,
							'interview_mode_two' => $request->interview_mode_two,
							'interviewDateThree' => ($interviewDateThree) ? Carbon::parse($interviewDateThree)->toIso8601String() : null,
							'interview_mode_three' => $request->interview_mode_three,
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
	
				$dsrs = $this->elasticService->getClient()->update($updateParams);

					$params = [
						'index' => ELASTIC_CANDIDATES,
						'id'    => $addJobWiseCondidate_id,
					];
					
					$response = $this->elasticService->getClient()->get($params);
					$data = (object) $response['_source'];

					
					$interview_mode_one = @$data->interview_mode_one;	
					$interviewDateOne = @$data->interviewDateOne;	
					$interviewDateTwo = @$data->interviewDateTwo;		
					$interview_mode_two = @$data->interview_mode_two;			
					$interviewDateThree = @$data->interviewDateThree;	
					$interview_mode_three = @$data->interview_mode_three;
					$wiseId = $addJobWiseCondidate_id;

					 $scheduleDateArr = DB::table('interview_schedules')->where('interview_job_wise_con_id',$addJobWiseCondidate_id)->pluck('schedule_date')->toArray();
                           $book1 = 'Book Now';
                           $book2 = 'Book Now';
                           $book3 = 'Book Now';
                           $bookbtn1 = 'book-btn';
                           $bookbtn2 = 'book-btn';
                           $bookbtn3 = 'book-btn';
                           $modal1 = 'data-toggle=modal data-target=#interview_schedule';
                           $modal2 = 'data-toggle=modal data-target=#interview_schedule';
                           $modal3 = 'data-toggle=modal data-target=#interview_schedule';
						   $date1 =  Carbon::parse($interviewDateOne)->format('Y-m-d H:i:s');
						    $date2 =  Carbon::parse($interviewDateTwo)->format('Y-m-d H:i:s');
							$date3 =  Carbon::parse($interviewDateThree)->format('Y-m-d H:i:s');
                           if(count($scheduleDateArr)>0){
							    
                                if(in_array($date1,$scheduleDateArr)){
                                 $book1 = 'Booked';
                                 $bookbtn1 = 'book-btnoff-booked';
								 $modal1 ='';
                                }
                                if(in_array($date2,$scheduleDateArr)){
                                 $book2 = 'Booked';
                                 $bookbtn2 = 'book-btnoff-booked';
								 $modal2 ='';
                                }
                                if(in_array($date3,$scheduleDateArr)){
                                 $book3 = 'Booked';
                                 $bookbtn3 = 'book-btnoff-booked';
								 $modal3 ='';
                                }
                                
                           }




                $html = '';

				 $html = '<div class="slot-day">
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('d M') : 'N/A' ).'</div>
							</div>
							<div class="slot-time">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('h:i A') : 'N/A' ).'(IST)</div>
							<div class="slot-time">'.(@$interview_mode_one ?? 'N/A' ).'</div>

							<button '.(($interviewDateOne && $date1 >= date('Y-m-d')) ? $modal1 : '' ).' data-mode="'. $interview_mode_one .'"
                                data-date="'. ($interviewDateOne ? Carbon::parse($interviewDateOne)->format('d-m-Y') : '') .'"
                                data-time="'. ($interviewDateOne ? Carbon::parse($interviewDateOne)->format('h:i A') : '') .'"
                                class="'. (($interviewDateOne && $date1 >= date('Y-m-d')) ? $bookbtn1 : 'book-btnoff') .'"
                                rel="'. @$wiseId .'">'.$book1.'</button>
                        </div>

						<div class="slot-day">
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('d M') : 'N/A' ).'</div>
							</div>
							<div class="slot-time">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('h:i A') : 'N/A').'(IST)</div>
							<div class="slot-time">'.(@$interview_mode_two ?? 'N/A') .'</div>
								<button '.(($interviewDateTwo && $date2 >= date('Y-m-d')) ? $modal2:'').' data-mode="'. $interview_mode_two .'"
										data-date="'. ($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('d-m-Y') : '') .'"
										data-time="'. ($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('h:i A') : '') .'"
										class="'. (($interviewDateTwo && $date2 >= date('Y-m-d')) ? $bookbtn2 : 'book-btnoff') .'"
										rel="'. @$wiseId .'">'.$book2.'</button>
						</div>

						<div class="slot-day">						
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('d M') : 'N/A' ).'</div>
							</div>
							<div class="slot-time">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('h:i A') : 'N/A').'(IST)</div>
							<div class="slot-time">'.(@$interview_mode_three ?? 'N/A' ).'</div>
								<button '.(($interviewDateThree && $date3 >= date('Y-m-d')) ? $modal3:'').' data-mode="'. $interview_mode_three .'"
										data-date="'. ($interviewDateThree ? Carbon::parse($interviewDateThree)->format('d-m-Y') : '') .'"
										data-time="'. ($interviewDateThree ? Carbon::parse($interviewDateThree)->format('h:i A') : '') .'"
										class="'. (($interviewDateThree && $date3 >= date('Y-m-d')) ? $bookbtn3 : 'book-btnoff') .'"
										rel="'. @$wiseId .'">'.$book3.'</button>
						</div>';

                

				$cachekeys = [
					'candidateDetails_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}

				return response()->json(['status'=>true,'html'=>$html]);

			}
		

	}
	public function clientCandidateList(Request $request)
	{
		// $candidates = JobApplication::query();

		$company_id = (int) auth()->user()->company_id;
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['client_id' => $request->clientid]],
							])
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function clientCandidateJoinList(Request $request)
	{
		// $candidates = JobApplication::query();
		$company_id = (int) auth()->user()->company_id;
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['client_id' => $request->clientid]],
								['term' => ['job_master_status_id' => 6]],
							])
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function clientCandidateInterviewTracker(Request $request)
	{
		// $candidates = JobApplication::query();
		$company_id = (int) auth()->user()->company_id;

		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
        // dd($request->clientid);
		
		$sort[] = ['schedule_date' => ['order' => 'desc']];
		$sort[] = ['id' => ['order' => 'desc']];
		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['client_id' => $request->clientid]],
							])
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates_interview_tracker', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function directclientCandidateList(Request $request)
	{
		
		$from_company_id  = $request->clientid;
		$to_company_id  = (int) auth()->user()->company_id;
		$company_id = (int) auth()->user()->company_id;

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$boolQuery = [
			'bool' => [
				'must' => [
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['from_company_id' => $from_company_id]],
								['term' => ['ClientComID' => $to_company_id]],
								['term' => ['final_status' => 1]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							])
						]
					],
				],
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

		// dd($pagination);
	}
	public function directclientCandidateJoinList(Request $request)
	{
		// $candidates = JobApplication::query();
		$from_company_id  = $request->clientid;
		$to_company_id  = (int) auth()->user()->company_id;
		$company_id = (int) auth()->user()->company_id;

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					// [
					// 	'bool' => [
					// 		'must' => array_merge([
					// 			['term' => ['from_company_id' => $from_company_id]],
					// 			['term' => ['to_company_id' => $to_company_id]],
					// 			['term' => ['ClientComID' => $to_company_id]],
					// 			['term' => ['job_master_status_id' => 6]],
					// 		])
					// 	]
					// ],
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['from_company_id' => $from_company_id]],
								['term' => ['ClientComID' => $to_company_id]],
								['term' => ['job_master_status_id' => 6]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							])
						]
					],
					// Third condition
					// [
					// 	'bool' => [
					// 		'must' => array_merge([
					// 			['term' => ['from_company_id' => $from_company_id]],
					// 			['term' => ['to_company_id' => $to_company_id]],
					// 			['term' => ['ClientComID' => $to_company_id]],
					// 			['term' => ['job_master_status_id' => 6]],
					// 			['range' => ['job_id' => ['gt' => 0]]],
					// 		])
					// 	]
					// ],
				],
				'minimum_should_match' => 1,
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates_join', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);


	}
	public function directclientCandidateInterviewTracker(Request $request)
	{
		// $candidates = JobApplication::query();
		$from_company_id  = $request->clientid;
		$to_company_id  = (int) auth()->user()->company_id;
		$company_id = (int) auth()->user()->company_id;

		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$must[] = [
			'nested' => [
				'path' => 'recommendation_relation',
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['recommendation_relation.from_company_id' => $from_company_id]],
							['term' => ['recommendation_relation.to_company_id' => $to_company_id]],
						]
					]
				]
			]
		];
		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => [
					'bool' => [
						'must' => $must,
						'filter' => [],
					],
				],
			],
		];

		$response = $this->elasticService->getClient()->search($params);
		if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		return response()->json([
			'html' => view('service_company.ajax_pages.custom_client_candidates_interview_tracker', compact('data','company_id'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function staffingclientCandidateList(Request $request)
	{
		
	        // $params = [
			// 	'index' => ELASTIC_CANDIDATES,
			// 	'id'    => 63770, // 63786
			// ];
			
			// $response = $this->elasticService->getClient()->get($params);
			// $this->application = (object) $response['_source'];
			// dd($this->application);

		$to_company_id  = $request->clientid;
		$from_company_id  = (int) auth()->user()->company_id;
		$company_id  = (int) auth()->user()->company_id;


		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$boolQuery = [
			'bool' => [
				'must' => [
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $to_company_id]],
								['term' => ['from_company_id' =>$from_company_id]],
								['term' => ['final_status' => 1]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							])
						]
					],
				],
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			try {
				$response = $this->elasticService->getClient()->search($params);
				$total = $response['hits']['total']['value'] ?? 0;
				$data = array_map(function ($hit) {
					return $hit['_source'];
				}, $response['hits']['hits']);
			} catch (\Exception $e) {
				    $total = 0;
				    $data = [];
			}
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		
		return response()->json([
			'html' => view('service_company.ajax_pages.agency_client_candidates', compact('data','company_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function staffingclientCandidateJoinList(Request $request)
	{
		// $candidates = JobApplication::query();
		$to_company_id  = $request->clientid;
		$from_company_id  = (int) auth()->user()->company_id;
		$company_id = (int) auth()->user()->company_id;

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $to_company_id]],
								['term' => ['from_company_id' => $from_company_id]],
								['term' => ['job_master_status_id' => 6]],
							])
						]
					],
				],
				'filter' => [] // We'll add all filters here
			]
		];


		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			try {
				$response = $this->elasticService->getClient()->search($params);
				$total = $response['hits']['total']['value'] ?? 0;
				$data = array_map(function ($hit) {
					return $hit['_source'];
				}, $response['hits']['hits']);
			} catch (\Exception $e) {
				    $total = 0;
				    $data = [];
			}
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		return response()->json([
			'html' => view('service_company.ajax_pages.agency_client_candidates_join', compact('data','company_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function staffingclientCandidateInterviewTracker(Request $request)
	{
		// $candidates = JobApplication::query();
		$to_company_id  = $request->clientid;
		$from_company_id  = (int) auth()->user()->company_id;
		$company_id = (int) auth()->user()->company_id;

		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

			// if (empty($from_company_id) || empty($to_company_id)) {
			// 	// fallback query (avoid ES crash)
			// 	$must[] = ['match_all' => (object)[]];
			// } else {
			// 	$must[] = [
			// 		'nested' => [
			// 			'path' => 'recommendation_relation',
			// 			'query' => [
			// 				'bool' => [
			// 					'must' => [
			// 						['term' => ['recommendation_relation.from_company_id' => (int)$from_company_id]],
			// 						['term' => ['recommendation_relation.to_company_id' => (int)$to_company_id]],
			// 						['term' => ['recommendation_relation.final_status' => 1]],
			// 					]
			// 				]
			// 			]
			// 		]
			// 	];
			// }
			// if (empty($must)) {
			// 	$must[] = ['match_all' => (object)[]];
			// }

			$must[] = [
					'bool' => [
						'must' => [
								[
									'term' => ['from_company_id' => $from_company_id]
								],
								[
									'term' => ['to_company_id' => $to_company_id]
								]
						],
					]
				];

			// $must[] = [
			// 	'term' => [
			// 		'real_company_id' => $to_company_id
			// 	]
			// ];

		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => [],
				'query' => [
					'bool' => [
						'must' => $must,
						'filter' => [],
					],
				],
			],
		];
		
		if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
			$total = 0;
			$data = [];
		}else{
			try {
				$response = $this->elasticService->getClient()->search($params);
				$total = $response['hits']['total']['value'] ?? 0;
				$data = array_map(function ($hit) {
					return $hit['_source'];
				}, $response['hits']['hits']);

			} catch (\Exception $e) {
					$total = 0;
					$data = [];
			}
		}
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		return response()->json([
			'html' => view('service_company.ajax_pages.agency_client_candidates_interview_tracker', compact('data','company_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	/** Fetch résumé bytes from r2 / full URL / local public path. Returns string|null. */
	private function fetchResumeBytes($resume){
		$bytes = null;
		try {
			if (strpos($resume, 'file.jero.works') !== false) {
				$key = str_replace('https://file.jero.works/', '', $resume);
				if (Storage::disk('r2')->exists($key)) { $bytes = Storage::disk('r2')->get($key); }
			}
			if ($bytes === null && filter_var($resume, FILTER_VALIDATE_URL)) {
				$resp = \Illuminate\Support\Facades\Http::timeout(15)->withOptions(['verify' => false])->get($resume);
				if ($resp->successful()) { $bytes = $resp->body(); }
			}
			if ($bytes === null) {
				$local = public_path($resume);
				if (file_exists($local)) { $bytes = file_get_contents($local); }
			}
		} catch (\Throwable $e) { $bytes = null; }
		return $bytes;
	}

	/**
	 * Generalized résumé->PDF endpoint keyed by an encrypted resume path (works for any
	 * candidate type). PDFs stream through as-is; .doc/.docx are converted via LibreOffice
	 * (cached on disk). Token is produced server-side, so there is no arbitrary-URL/SSRF surface.
	 */
	public function resumeConvertPdf($token){
		$resume = GeneralMethod::value_decrypt($token);
		if (empty($resume)) { abort(404); }
		$ext = strtolower(pathinfo(parse_url($resume, PHP_URL_PATH) ?: $resume, PATHINFO_EXTENSION));
		if (!in_array($ext, ['pdf', 'doc', 'docx'])) { abort(404); }

		$bytes = $this->fetchResumeBytes($resume);
		if ($bytes === null) { abort(404); }

		if ($ext === 'pdf') {
			return response($bytes, 200)->header('Content-Type', 'application/pdf')->header('Cache-Control', 'private, max-age=86400');
		}

		$cacheDir = storage_path('app/resume-pdf-cache');
		if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
		$hash = md5($resume);
		$pdfCache = $cacheDir . DIRECTORY_SEPARATOR . $hash . '.pdf';

		if (!file_exists($pdfCache) || filesize($pdfCache) < 100) {
			$srcFile = $cacheDir . DIRECTORY_SEPARATOR . $hash . '.' . $ext;
			file_put_contents($srcFile, $bytes);
			$soffice = $this->sofficeBinary();
			if (!$soffice) { @unlink($srcFile); abort(503, 'Document converter unavailable'); }
			$profile = 'file:///' . str_replace('\\', '/', $cacheDir . DIRECTORY_SEPARATOR . 'lo_' . $hash);
			$cmd = '"' . $soffice . '" --headless --norestore --nofirststartwizard'
				. ' -env:UserInstallation=' . $profile
				. ' --convert-to pdf --outdir "' . $cacheDir . '" "' . $srcFile . '"';
			@exec($cmd . ' 2>&1', $out, $code);
			@unlink($srcFile);
			if (!file_exists($pdfCache) || filesize($pdfCache) < 100) { abort(500, 'Conversion failed'); }
		}
		return response()->file($pdfCache, ['Content-Type' => 'application/pdf', 'Cache-Control' => 'private, max-age=86400']);
	}

	/**
	 * Same-origin proxy for the logged-in company's own logo so it can be embedded
	 * into the résumé-banner canvas without CORS taint. No URL param -> no SSRF.
	 */
	public function companyLogoProxy(Request $request){
		$cid  = auth()->user()->company_id ?? 0;
		$row  = DB::table('companies')->select('logo')->where('id', $cid)->first();
		$logo = $row->logo ?? '';
		if (empty($logo)) { abort(404); }
		try {
			$resp = \Illuminate\Support\Facades\Http::timeout(8)->withOptions(['verify' => false])->get($logo);
			if (!$resp->successful()) { abort(404); }
			$ct = $resp->header('Content-Type');
			if (!$ct || strpos($ct, 'image/') !== 0) {
				$ext = strtolower(pathinfo(parse_url($logo, PHP_URL_PATH) ?: $logo, PATHINFO_EXTENSION));
				$map = ['png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif','svg'=>'image/svg+xml','webp'=>'image/webp'];
				$ct  = $map[$ext] ?? 'image/png';
			}
			return response($resp->body(), 200)
				->header('Content-Type', $ct)
				->header('Cache-Control', 'private, max-age=3600');
		} catch (\Throwable $e) {
			abort(404);
		}
	}
	private function sofficeBinary(){
		$candidates = [
			env('SOFFICE_PATH'),
			'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
			'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
			'/usr/bin/soffice', '/usr/bin/libreoffice', '/opt/libreoffice/program/soffice',
		];
		foreach ($candidates as $c) { if ($c && @file_exists($c)) { return $c; } }
		return null;
	}

	/**
	 * Convert a candidate's .doc/.docx résumé to PDF (via LibreOffice headless) and stream it
	 * inline, same-origin. Result is cached on disk so each résumé converts only once.
	 */
	public function resumeAsPdf($id){
		$app = DB::table('job_applications')->where('id', $id)->first();
		if (!$app || empty($app->resume)) { abort(404); }
		$resume = $app->resume;
		$ext = strtolower(pathinfo(parse_url($resume, PHP_URL_PATH) ?: $resume, PATHINFO_EXTENSION));
		if (!in_array($ext, ['doc', 'docx'])) { abort(404); }

		$cacheDir = storage_path('app/resume-pdf-cache');
		if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
		$hash = md5($id . '|' . $resume);
		$pdfCache = $cacheDir . DIRECTORY_SEPARATOR . $hash . '.pdf';

		if (!file_exists($pdfCache) || filesize($pdfCache) < 100) {
			$bytes = null;
			try {
				if (strpos($resume, 'file.jero.works') !== false) {
					$key = str_replace('https://file.jero.works/', '', $resume);
					if (Storage::disk('r2')->exists($key)) { $bytes = Storage::disk('r2')->get($key); }
				}
				if ($bytes === null && filter_var($resume, FILTER_VALIDATE_URL)) {
					$resp = \Illuminate\Support\Facades\Http::timeout(15)->withOptions(['verify' => false])->get($resume);
					if ($resp->successful()) { $bytes = $resp->body(); }
				}
				if ($bytes === null) {
					$local = public_path($resume);
					if (file_exists($local)) { $bytes = file_get_contents($local); }
				}
			} catch (\Throwable $e) { $bytes = null; }
			if ($bytes === null) { abort(404); }

			$srcFile = $cacheDir . DIRECTORY_SEPARATOR . $hash . '.' . $ext;
			file_put_contents($srcFile, $bytes);

			$soffice = $this->sofficeBinary();
			if (!$soffice) { @unlink($srcFile); abort(503, 'Document converter unavailable'); }

			$profile = 'file:///' . str_replace('\\', '/', $cacheDir . DIRECTORY_SEPARATOR . 'lo_' . $hash);
			$cmd = '"' . $soffice . '" --headless --norestore --nofirststartwizard'
				. ' -env:UserInstallation=' . $profile
				. ' --convert-to pdf --outdir "' . $cacheDir . '" "' . $srcFile . '"';
			@exec($cmd . ' 2>&1', $out, $code);
			@unlink($srcFile);

			if (!file_exists($pdfCache) || filesize($pdfCache) < 100) { abort(500, 'Conversion failed'); }
		}

		return response()->file($pdfCache, [
			'Content-Type'  => 'application/pdf',
			'Cache-Control' => 'private, max-age=86400',
		]);
	}
     //**  end ajax  */
	public function create($jobId = null, $appId = null)
	{
       
		$jobId = GeneralMethod::value_decrypt($jobId);
		// dd($jobId);
		$this->pageTitle = __('Add Candidate');
		abort_if(Gate::denies('add_job_applications'), 403);
		$eventId = 4;
		$coinsRequ = 1;
		$companyId = (int) auth()->user()->company_id;
		$packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
		if($packageStatus->status){
			session()->flash('packageStatus', $packageStatus->message); // or false depending on your condition
			$url = url('service_company/apptracker/' . @$jobId . '/all');
			return redirect($url);
		}
		

		$company_id = '';
		$company_id = auth()->user()->company_id;

		abort_if(Gate::denies('add_job_applications'), 403);
		$candidateCount = JobApplication::count();
		// if ($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
		// 	$this->message = __('modules.subscription.candidatePurchase');
		// 	return view('service_company.subscription.ask_purchase', $this->data);
		// }

		$this->jobs = Job::activeJobs();
		$this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
		$chkAdminRole = $this->user->hasRole('admin');
		if ($chkAdminRole > 0) {
			$this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
		} else {
			$this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
		}

		$this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id',[57,58,59])->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->jobId = $jobId;
		$this->appId = $appId;


		$this->jobDetail = Job::select('jobs.id','jobs.company_id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status','jobs.total_positions', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city','jobs.min_sal_mode','jobs.max_sal_mode','jobs.min_salary_currency','jobs.max_salary_currency')
			//->join('job_assign', 'job_assign.job_id', 'jobs.id')
			->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where('jobs.id', '=', $jobId)->where('jobs.company_id', '=', $this->user->company_id)
			->first();


		$this->page_event = 'ADD';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		$this->statelist = GeneralMethod::statelist(101);
		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			->where('job_additional_detail.company_id', '=', $company_id)
			->where('job_additional_detail.job_id', '=', $this->jobId)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();

		$this->jobLocation = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
			->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
			->where('client_location_jobs.job_id', $this->jobId)
			->orderBy('client_location_jobs.id', 'DESC')
			->first();

			$this->cities =GeneralMethod::allCities();

			$this->default_loc = GeneralMethod::allCitiesSelected(array());

			$this->jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id'); // Add the candidate condition
				})
				->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
				->where('job_metronics_skills.job_id', '=', $this->jobId)
				 ->groupBy('job_metronics_skills.id')
				->get();

				// dd($this->jobSkill);
				$this->skillShow = false;

			


		// $this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = '';
		$this->cityListArr = '';
		$this->currentPage = 1;
		$this->resumeAwsPath = false;
        $this->currencyConverter = GeneralMethod::currencyConverter();
		return view('service_company.job-applications.create', $this->data);
	}
	public function statelist($cid)
	{
		// $statelist = GeneralMethod::statelist($cid);

		// if ($cid == 101) {

			$Rawstatelist = DB::table('state_list')
				->select('state_list.id', 'state_list.state as name','state_list.country_id')
				->where('state_list.country_id',$cid)
				->orderBy('state_list.state')
				->get()->toArray();
			$statelist = json_decode(json_encode($Rawstatelist), true);
		// }

		$custom = '<option>Select State</option>';
		foreach ($statelist as $sval) {
			$custom .= '<option value="' . $sval['id'] . '">' . $sval['name'] . '</option>';
		}






		return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
		//echo '<pre>'; print_r($statelist); 	
	}
	public function store(Request $request)
	{

		abort_if(Gate::denies('add_job_applications'), 403);

		// $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		// if ($packageStatus != 1) {
		// 	$url = url('service_company/apptracker/' . GeneralMethod::value_encrypt(@$request->job_id) . '/all');
		// 	return redirect($url);
		// }

		$validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email:rfc,dns',
				'disposable_email'
			]
		], [
			'email.required' => 'Email is required',
			'email.email' => 'Please enter a valid email',
			'email.disposable_email' => 'Temporary emails are not allowed'
		]);

		if ($validator->fails()) {
			return Reply::dataOnly(['status' => 'mailcheck', 'msgType'=> 'warning', 'message' => $validator->errors()->first()]);	
		}

       

		$company_id = '';
		$company_id = auth()->user()->company_id;		

		$eventId = 4;
		$coinsDeductions =  GeneralMethod::coinsDeductions((int) $company_id,$eventId,auth()->user()->id,$company_id,'','');
		if($coinsDeductions->status){
			return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
		}


		$applicationStatus = ApplicationStatus::first();
		$jobApplication = new JobApplication();
		$jobApplication->full_name = ucwords(strtolower($request->full_name));
		$jobApplication->job_id = $request->job_id;
		// $jobApplication->status_id 			=	$applicationStatus->id;
		$jobApplication->status_id = 8;
		$jobApplication->email = $request->email;
		$jobApplication->phone = $request->phone;
		//$jobApplication->cover_letter 		=	$request->cover_letter;
		$jobApplication->column_priority = 0;

		$jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
		$jobApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

		// $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

		$total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '')  ? $request->total_exp_year . '.' . $request->total_exp_month : null;

		$rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '')  ? $request->rel_exp_year . '.' . $request->rel_exp_month : null;

		// $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

		$jobApplication->total_exp = $total_exp; //$request->total_exp;
		//$jobApplication->total_exp 			=	$request->total_exp;
		$jobApplication->rel_exp = $rel_exp; //$request->rel_exp;
		//$jobApplication->passport				=	$request->passport;
		$jobApplication->ctc = $request->ctc;
		$jobApplication->current_ctc_cur = $request->current_ctc_cur;
		$jobApplication->current_ctc_mode = $request->current_ctc_mode;

		$jobApplication->ectc = $request->ectc;
		$jobApplication->current_ectc_cur = $request->current_ectc_cur;
		$jobApplication->current_ectc_mode = $request->current_ectc_mode;
		$jobApplication->gender = $request->gender;
		$jobApplication->notice_comments = @$request->notice_comments;

		$currency = GeneralMethod::currency();  // Currency code
		
		$ecurrency  = $currency[$request->current_ectc_cur];
		$econvertedAmount = $request->bill_annual;
		if($request->current_ectc_cur > 0){
			$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
		}

		$ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
		$jobApplication->ectc_yearly = @$ectcdivided->yearly;
		$jobApplication->ectc_monthly = @$ectcdivided->monthly;
		$jobApplication->ectc_hourly = @$ectcdivided->hourly;

		$currency  = $currency[$request->current_ctc_cur];
		$convertedAmount = $request->pay_annual;
		if($request->current_ctc_cur > 0){
			$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
		}

		$ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);
		
		$jobApplication->ctc_yearly = @$ctcdivided->yearly;
		$jobApplication->ctc_monthly = @$ctcdivided->monthly;
		$jobApplication->ctc_hourly = @$ctcdivided->hourly;


        $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
		$jobApplication->percentage_hike = $request->percentage_hike;
		$jobApplication->comment = $request->comment;
		$jobApplication->pincode = $experienceGet;

		//$jobApplication->current_location 	=	$request->current_location;
		$jobApplication->current_designation = $request->current_designation;
		$jobApplication->current_company = $request->current_company;
		$jobApplication->careersummary = $request->careersummary;
		$jobApplication->nationality = $request->nationality;
		$jobApplication->wm = $request->wm;
		$jobApplication->employmentType = $request->employmentType;
		$jobApplication->oh = $request->oh;
		$jobApplication->visastatu = $request->visastatu;
		$jobApplication->referencename = $request->referencename;
		$jobApplication->reason_for_job_change = $request->reason_for_job_change;


		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		$jobApplication->skills = $skillStr;

		//$jobApplication->master_status_id 	=	$applicationStatus->id;
		$jobApplication->master_status_id = 8;
		$jobApplication->secondrary_email = $request->secondrary_email;
		$jobApplication->secondary_mobile = $request->secondary_mobile;
		$submitted_by = '';
		$submitted_by = auth()->user()->id;
		if (!empty($submitted_by)) {
			$jobApplication->submitted_by = $submitted_by;
		}

		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			$jobApplication->np_id = $request->np_id;
		} else if ($serving_notice_period == 2) {
			$jobApplication->np_id = 'Immediate';
			$jobApplication->last_working_date = $request->np_id;
		} else {
			$jobApplication->np_id = $request->ddl_np_id;
		}


		if (!empty($request->qualification)) {
			$jobApplication->qualification = $request->qualification;
		}
		$jobApplication->company_id = $this->user->company_id;
		$jobApplication->serving_notice_period_mode = $request->serving_notice_period;
		if ($request->has('gender')) {
			$jobApplication->gender = $request->gender;
		}
		if ($request->has('dob')) {
			$jobApplication->dob = $request->dob;
		}


		$stateId = '';
		$cityID = '';
		$countryID = '';
		$location_find = '';
		$work_city = request('kt_tagify_work_location');
    	$work_city_all = json_decode($work_city, true);
		if (\request('kt_tagify_work_location') != "") {
			foreach ($work_city_all as $cityVal) {
				$parts = explode('|', $cityVal['value']);
				// Trim to remove any leading or trailing whitespace
			 	 $cityName = trim($parts[0]);
				 $stateName = trim($parts[1]);
				 $countrName = trim($parts[2]);
				 $location_find = $cityName.'_'.$stateName.'_'.$countrName;
				  // dd($cityName);
				 // Debugging line
				 $city_row = DB::table('city_list')
				 ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
				 ->join('countries', 'countries.id', '=', 'state_list.country_id')
				 ->select('city_list.city_id', 'countries.id as country_id')
				 ->where('city_list.city','!=','')
				 ->where('city_list.city', '=', $cityName)
				 ->where('countries.country_name', '=', $countrName)
				 ->first();

				if($city_row){
					$cityID = (@$city_row) ? $city_row->city_id : '';
					$countryID = (@$city_row) ? $city_row->country_id : '';
					$stateId = GeneralMethod::stateId($cityID);
				}
				
			}
		}

		
		$jobApplication->country = $countryID;
		$jobApplication->state = $stateId;

		if ($jobApplication->country == 101) {
			$jobApplication->city_id = $cityID;
		} else {
			$jobApplication->city_id = $cityID;
		}


		if ($request->hasFile('photo')) {
		    $file = $request->file('photo');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Images/' . $filename;

				Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				 $fileUrl = Storage::disk('s3')->url($filePath);
				 $jobApplication->photo = $fileUrl;
           // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}

		$returnCount = 0;
		$message = 'messages.createdSuccessfully';
		$company_id = $this->user->company_id;
		$app_duplicate = $this->user->company->app_duplicate;
		$app_duplicate_days = $this->user->company->app_duplicate_days;
		$applicationEmail = $request->email;
		$applicationPhone =$request->phone;
		
		if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
			$returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days,$applicationPhone);
			
			if ($returnCount > 0 && $app_duplicate == 1) {
				$message = 'Duplicate :: application already in this job';
				$jobApplication->duplicate = 1;
			} else if ($returnCount > 0 && $app_duplicate == 2) {
				$message = 'Duplicate :: application already in system';
				$jobApplication->duplicate = 1;
			}
			// dd($jobApplication->duplicate,$returnCount,$app_duplicate,$message);
		}

		$chkApplication = DB::table('job_applications')
		->select('job_applications.id')
		->where('company_id', $company_id)
		->where(function($query) use ($applicationEmail, $applicationPhone) {
			$query->where('email', $applicationEmail)
			->orWhere('phone', $applicationPhone);
		})
		->count();
		if ($chkApplication > 0 && $returnCount > 0) {
			$jobApplication->duplicate = 1;
		}
		// dd("k");
        $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
		$interviewDateOne   = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
		$interviewDateTwo   = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
		$interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);
		$interviewDateFour  = $this->formatInterviewDateTime($request->interviewDateFour, $request->interviewTimeFour, $defaultTimeFormat);
		$interviewDateFive  = $this->formatInterviewDateTime($request->interviewDateFive, $request->interviewTimeFive, $defaultTimeFormat);
		$interviewDateSix   = $this->formatInterviewDateTime($request->interviewDateSix, $request->interviewTimeSix, $defaultTimeFormat);

		$jobApplication->interviewDateOne = $interviewDateOne;
		$jobApplication->interviewDateTwo = $interviewDateTwo;
		$jobApplication->interviewDateThree = $interviewDateThree;
		$jobApplication->interviewDateFour = $interviewDateFour;
		$jobApplication->interviewDateFive = $interviewDateFive;
		$jobApplication->interviewDateSix = $interviewDateSix;
		$jobApplication->linkedin = $request->linkedin;
		$jobApplication->github = $request->github;
		$jobApplication->behance = $request->behance;
		$jobApplication->otherLink = $request->otherLink;
		$jobApplication->interview_mode_one = $request->interview_mode_one;
		$jobApplication->interview_mode_two = $request->interview_mode_two;
		$jobApplication->interview_mode_three = $request->interview_mode_three;
		$jobApplication->interview_mode_four = $request->interview_mode_four;
		$jobApplication->interview_mode_five = $request->interview_mode_five;
		$jobApplication->interview_mode_six = $request->interview_mode_six;

		$jobApplication->save();

		
		if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
			$YearskillId = $request->input('YearskillId');
			$MonthskillId = $request->input('MonthskillId');
			$ratingId = $request->input('rating');
			// dd($YearskillId,$MonthskillId);
			DB::table('candidate_job_skill_exp')->where('candidate_id', $jobApplication->id)->where('job_id', $request->job_id)->delete();
			$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
				'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['candidate_id' => $jobApplication->id]],
								['term' => ['job_id' => $request->job_id]],
							],
						],
					],
				],
			]);
			foreach ($YearskillId as $skillId => $value) {
				// if (!empty($value)) { 
				$ss = "Year";
				if($value >=1){
					$ss = "Years";
				}
				if(!empty($MonthskillId[$skillId])){
					$valMonth = $MonthskillId[$skillId] ;
				}else{
					$valMonth = $MonthskillId[$skillId] ;
				}
				 $skill_value = $value.'.'.$valMonth.' '.$ss;
				 $rating = $ratingId[$skillId] ;

					$values = array(
						'job_skill_id' => $skillId,
						'skill_value' => $skill_value,
						'job_id' => (int)$request->job_id,
						'rating' => (int)$rating,
						'candidate_id' => $jobApplication->id
					);
					DB::table('candidate_job_skill_exp')->insert($values);
					$expId = DB::getPdo()->lastInsertId();
					$skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillId)->first();
					$this->elasticService->getClient()->index([
						'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'skill_name' => $skills->skill_name ?? '',
							'job_skill_id' => $skillId,
							'skill_value' => $skill_value,
							'job_id' =>(int)$request->job_id,
							'candidate_id' =>$jobApplication->id,
							'rating' =>(int)$rating,
							'created_at' =>  now()->toIso8601String(),
							'updated_at' =>  now()->toIso8601String()
						]
					]);
				// }
			}
		}

		// dd("j");
      if (!empty($skillArr)) {
		$skillsAddElastic = array_column($skillArr, 'value');
		if(count( $skillsAddElastic)>0){
          GeneralMethod::skillsAddElastic($skillsAddElastic);
		}
	}

		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				$values = array(
					'job_application_id' => $jobApplication->id,
					'skills' => $sval['value']
				);
				if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'.$sval['value'].'%')->exists()){
					DB::table('job_application_skills')->insert($values);
				}
			}
		}
		if (!empty($request->current_designation)) {
		if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'. @$request->current_designation.'%')->exists()){
			DB::table('job_application_skills')->insert(['job_application_id' => $jobApplication->id,'skills' =>  @$request->current_designation]);
			}
		}


		$skillArr = explode(',', $jobApplication->skills);
		$percentage =  GeneralMethod::skill_match_percentage($request->job_id,$skillArr);

        $now = now();
		$insertArr = array('job_id' => $request->job_id, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);
		$add_job_wise_condidates = DB::table('add_job_wise_condidates')->insert($insertArr);
		$lastInsertedId = DB::getPdo()->lastInsertId();
		
		$cachekeys = [
					'allcvtracker_version_user_'.$this->user->id,
					'alljobslist_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}
		$teamRankElastic = [
						'company_id'=>$company_id,
						'user_id'=>$submitted_by,
						'candidate_id'=>$lastInsertedId,
						'profileUpload'=>1,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>0,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>0,
						'respondToBenchRequests'=>0
					];
					GeneralMethod::teamRankElastic($teamRankElastic);

		

		$jobId = $request->job_id;
		$wise_c = DB::table('job_applications')
			->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.client_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
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
			if ($np !='Immediate') {
				$np = "SNP LWD ".date("d-M-Y", strtotime($jobApplication->np_id));
			}
		}
		
		$strAdded = DB::table('clients')->select('companies.company_name','clients.name','clients.self_company')
		                     ->join('companies','companies.id','=','clients.company_id')
		                     ->where('clients.id', @$wise_c->client_id)->first();
		                     
						$contact_person_name = '';
                        if($strAdded->self_company==1){
                             $contact_person_name = $strAdded->company_name;
                        }else{
                             $contact_person_name = $strAdded->name;
                        }
                        

		$data = [
			'VendorTeam' => @$wise_c->company_name,
			'ReqID' => "R" . @$wise_c->job_id,
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
			'SubmittedOn' => date("d-M-Y H:i:s", strtotime(@$wise_c->created_at))
		];
		
      // config(['mail.from.name' => auth()->user()->name]);
		
                        
                $notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = array();//$notification; //array_merge($partnersData, $recommendedDataList);
				
              // $cc = array_merge($partnersData, $recommendedDataList);
		      $subject = "New Candidate Added | $wise_c->title | $contact_person_name | R-$wise_c->job_id";
		
		     $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
			 $checkMailPermision = GeneralMethod::mailPermission(1);
			 if($checkMailPermision){  
				$mailTypeSubject = GeneralMethod::mailTypeSubject(1,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';


				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.add-candidate',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');


            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.add-candidate', (object) $data, $attachments, $fromAddress, $senderName,$cc);

			 }

	   // 	Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data,$subject));


		// Mail::to(auth()->user()->email)->send(new AddCandidateMail((object)$data));

		// Check job Assign Or Not
		$chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
		if ($chkJobAssign < 1) {
			$values = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id);
			DB::table('job_assign')->insert($values);
		}

		if ($request->hasFile('resume')) {

			$companyNameR2 = DB::table('companies')
			->where('id',auth()->user()->company_id)
			->value('company_name');   // change column if needed
			$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
			$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
			$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
			$year  = date('Y');
            $month = date('m');

			$tempPath = session('resume_temp_path');
			$full_name = session('resume_original_name');
			UploadResumeToTestRTwo::dispatch($tempPath, $full_name,$jobApplication->id,$companyNameRTwo,$year,$month)->onConnection('emails');
			
			$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
			
			DB::table('job_applications')
							->where('id', $jobApplication->id)
							->update(['resume' =>$fileUrl]);
							session()->forget([
								'resume_temp_path',
								'resume_original_name'
							]);
							

		}

		if(empty($request->resumeCheck)){
			DB::table('job_applications')
				->where('id', $jobApplication->id)
				->update(['resume' =>'']);
		}

		if (isset($request->jobskill) && !empty($request->jobskill)) {
			DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $jobApplication->id)->delete();
			foreach ($request->jobskill as $key => $value) {
				if (!empty(@$value)) {
					$values = array('job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value, );
					DB::table('candidate_skills_value')->insert($values);
				}
			}
		}
		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)
			//->orderBy('priority', 'ASC')
			->count();
		if ($status_cnt > 0) {
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
			$status_name = $status_row->name;
		}

		$insert_type = 0;

		$timelineArr = array(
			'event_id' => 1,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			'master_process_name' => 'InReview',
			//'child_process_name'	=>	

			'application_id' => $jobApplication->id,
			'application_email' => $jobApplication->email,
			'job_id' => @$request->job_id,
			'note' => 'Successfully Added'
		);
		GeneralMethod::UpdateTimeline($timelineArr);

		$tb_ranks = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id,'status' => 0,'application_id' => $jobApplication->id);
		
		DB::table('tb_ranks')->insert($tb_ranks);


		// Job Application Answer save
		if (isset($request->answer) && !empty($request->answer)) {
			foreach ($request->answer as $key => $value) {
				$answer = new JobApplicationAnswer();
				$answer->job_application_id = $jobApplication->id;
				$answer->job_id = $request->job_id;
				$answer->question_id = $key;
				$answer->answer = $value;
				$answer->save();
			}
		}

		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$request->job_id,
					'question_id' => @$jd->id,
					'application_id' => @$jobApplication->id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$expId = DB::getPdo()->lastInsertId();
				 $this->elasticService->getClient()->update([
						'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
                            'doc' => [
								'job_id' => @$request->job_id,
								'question_id' =>  @$jd->id,
								'application_id' =>@$jobApplication->id,
								'job_detail_value' => @$jd->detail_value,
								'application_detail_value' =>@$request->additional_detail[$inc],
								'created_at' =>  now()->toIso8601String(),
								'updated_at' =>  now()->toIso8601String()
							],
							'doc_as_upsert' => true // 🔥 key to avoiding conflicts
				      ]
					]);
				$inc++;

			}
		}

		$company_id = auth()->user()->company_id;

        $skillStr = '';
		$skillArr = json_decode($request->skills, true);
		$requiredSkills =  [];
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				array_push($requiredSkills, $sval['value']);
			}
		}

		if (@$jobApplication->id > 0) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$jobApplication->id;
			$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
		}
		GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId);
	
		$cachekeys = [
			'alljobslist_version_user_'.$this->user->id,
			'allcvtracker_version_user_'.$this->user->id,
			'allapptracker_version_user_'.$this->user->id,
		];
		foreach ($cachekeys as $key) {
			 GeneralMethod::cacheClean($key);
		}
		$this->currencyConverter = GeneralMethod::currencyConverter();

		// ElasticCandidateIndex::dispatch($lastInsertedId)->onConnection('emails');
		if (!empty($request->job_id)) {
			return Reply::redirect(url('/service_company/apptracker/' .  GeneralMethod::value_encrypt($request->job_id) . '/all'), __('menu.jobApplications') . ' ' . __($message));
		} else {
			return Reply::redirect(route('service_company.job-applications.table'), __('menu.jobApplications') . ' ' . __($message));
		}
		
	}
	public function googleSheetCandidateData(Request $request)
	{

		// dd($request->all());

		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

		$range = 'Sheet1!A1:W1'; // Specify the range of data you want to fetch
		$values = $this->GoogleSheetServiceCandidate->getSpreadsheetValues($range,3);
		unset($values["Sheet1"][0]);

		//  dd($values);

		if ($packageStatus != 1) {
			$url = url('service_company/apptracker/' . @$request->job_id . '/all');
			return redirect($url);
		}


		$company_id = '';
		$company_id = auth()->user()->company_id;

		$applicationStatus = ApplicationStatus::first();
		$jobApplication = new JobApplication();
		$jobApplication->full_name = $request->full_name;
		$jobApplication->job_id = $request->job_id;
		// $jobApplication->status_id 			=	$applicationStatus->id;
		$jobApplication->status_id = 8;
		$jobApplication->email = $request->email;
		$jobApplication->phone = $request->phone;
		//$jobApplication->cover_letter 		=	$request->cover_letter;
		$jobApplication->column_priority = 0;
		$jobApplication->total_exp = $request->total_exp;
		//$jobApplication->total_exp 			=	$request->total_exp;
		$jobApplication->rel_exp = $request->rel_exp;
		//$jobApplication->passport				=	$request->passport;
		$jobApplication->ctc = $request->ctc;
		$jobApplication->current_ctc_cur = $request->current_ctc_cur;
		$jobApplication->current_ctc_mode = $request->current_ctc_mode;

		$jobApplication->ectc = $request->ectc;
		$jobApplication->current_ectc_cur = $request->current_ectc_cur;
		$jobApplication->current_ectc_mode = $request->current_ectc_mode;

		$jobApplication->percentage_hike = $request->percentage_hike;
		$jobApplication->comment = $request->comment;
		$jobApplication->pincode = $request->pincode;

		//$jobApplication->current_location 	=	$request->current_location;
		$jobApplication->current_designation = $request->current_designation;
		$jobApplication->current_company = $request->current_company;


		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		$jobApplication->skills = $skillStr;

		//$jobApplication->master_status_id 	=	$applicationStatus->id;
		$jobApplication->master_status_id = 8;
		$jobApplication->secondrary_email = $request->secondrary_email;
		$jobApplication->secondary_mobile = $request->secondary_mobile;
		$submitted_by = '';
		$submitted_by = auth()->user()->id;
		if (!empty($submitted_by)) {
			$jobApplication->submitted_by = $submitted_by;
		}

		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			$jobApplication->np_id = $request->np_id;
		} else if ($serving_notice_period == 2) {
			$jobApplication->np_id = 'Immediate';
		} else {
			$jobApplication->np_id = $request->ddl_np_id;
		}


		if (!empty($request->qualification)) {
			$jobApplication->qualification = $request->qualification;
		}
		$jobApplication->company_id = $this->user->company_id;
		$jobApplication->serving_notice_period_mode = $request->serving_notice_period;
		if ($request->has('gender')) {
			$jobApplication->gender = $request->gender;
		}
		if ($request->has('dob')) {
			$jobApplication->dob = $request->dob;
		}

		$jobApplication->country = $request->country;
		$jobApplication->state = $request->state;

		if ($jobApplication->country == 101) {
			$jobApplication->city_id = $request->city_id;
		} else {
			$jobApplication->city = $request->city;
		}


		if ($request->hasFile('photo')) {
		    $file = $request->file('photo');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Images/' . $filename;

				Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				 $fileUrl = Storage::disk('s3')->url($filePath);
				 $jobApplication->photo = $fileUrl;
           // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}

		$returnCount = 0;
		$message = 'messages.createdSuccessfully';
		$company_id = $this->user->company_id;
		$app_duplicate = $this->user->company->app_duplicate;
		$app_duplicate_days = $this->user->company->app_duplicate_days;
		$applicationEmail = $request->email;
		$applicationPhone =$request->phone;
		if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
			$returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days,$applicationPhone);
			if ($returnCount > 0 && $app_duplicate == 1) {
				$message = 'Duplicate :: application already in this job';
			} else if ($returnCount > 0 && $app_duplicate == 2) {
				$message = 'Duplicate :: application already in system';
			}
		}

		$chkApplication = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('email', $applicationEmail)->orWhere('phone', $request->phone)->count();
		if ($chkApplication > 0  && $returnCount > 0) {
			$jobApplication->duplicate = 1;
		}

		$jobApplication->save();

		$skillArr = explode(',', $jobApplication->skills);
		$percentage =  GeneralMethod::skill_match_percentage($request->job_id,$skillArr);
		$now = now();
		$insertArr = array('job_id' => $request->job_id, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);
		$add_job_wise_condidates = DB::table('add_job_wise_condidates')->insert($insertArr);
		$lastInsertedId = DB::getPdo()->lastInsertId();
		
		
		$cachekeys = [
					'allcvtracker_version_user_'.$this->user->id,
					'alljobslist_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}

		$teamRankElastic = [
						'company_id'=>$company_id,
						'user_id'=>$submitted_by,
						'candidate_id'=>$lastInsertedId,
						'profileUpload'=>1,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>0,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>0,
						'respondToBenchRequests'=>0
					];
					GeneralMethod::teamRankElastic($teamRankElastic);

		$jobId = $request->job_id;
		$wise_c = DB::table('job_applications')
			->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.client_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
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
		
		$strAdded = DB::table('clients')->select('companies.company_name','clients.name','clients.self_company')
		                     ->join('companies','companies.id','=','clients.company_id')
		                     ->where('clients.id', @$wise_c->client_id)->first();
		                     
						$contact_person_name = '';
                        if($strAdded->self_company==1){
                             $contact_person_name = $strAdded->company_name;
                        }else{
                             $contact_person_name = $strAdded->name;
                        }
                        

		$data = [
			'VendorTeam' => @$wise_c->company_name,
			'ReqID' => "J" . @$wise_c->job_id,
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
			'SubmittedOn' => date("d-M-Y H:i:s", strtotime(@$wise_c->created_at))
		];
		
      // config(['mail.from.name' => auth()->user()->name]);
		
                        
                $notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = array(); //$notification; //array_merge($partnersData, $recommendedDataList);
				
              // $cc = array_merge($partnersData, $recommendedDataList);
		      $subject = "New Candidate Added | $wise_c->title | $contact_person_name | R-$wise_c->job_id";
		
		     $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
			 $checkMailPermision = GeneralMethod::mailPermission(1);
			 if($checkMailPermision){
				$mailTypeSubject = GeneralMethod::mailTypeSubject(1,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.add-candidate',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');

            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.add-candidate', (object) $data, $attachments, $fromAddress, $senderName,$cc);

			 }

		
		
	   // 	Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data,$subject));


		// Mail::to(auth()->user()->email)->send(new AddCandidateMail((object)$data));



		// Check job Assign Or Not
		$chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
		if ($chkJobAssign < 1) {
			$values = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id);
			DB::table('job_assign')->insert($values);
		}
		if ($request->hasFile('resume')) {
		         $file = $request->file('resume');
		      //   $filename = $file->getClientOriginalName();
				$filename = "Jero_".substr($request->full_name,0,10).'.' . $file->getClientOriginalExtension();
				$filePath = 'Resumes/' . $filename;
				Storage::disk('s3')->put($filePath, file_get_contents($file));
				$fileUrl = Storage::disk('s3')->url($filePath);
        // 			$hashname = Files::upload($request->resume, 'documents/' . $jobApplication->id, null, null, false);
			$jobApplication->documents()->create(['name' => 'Resume', 'hashname' =>$fileUrl]);
		}

		if (isset($request->jobskill) && !empty($request->jobskill)) {
			DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $jobApplication->id)->delete();
			foreach ($request->jobskill as $key => $value) {
				if (!empty(@$value)) {
					$values = array('job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value, );
					DB::table('candidate_skills_value')->insert($values);
				}
			}
		}
		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)
			//->orderBy('priority', 'ASC')
			->count();
		if ($status_cnt > 0) {
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
			$status_name = $status_row->name;
		}

		$insert_type = 0;

		$timelineArr = array(
			'event_id' => 1,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			'master_process_name' => 'InReview',
			//'child_process_name'	=>	

			'application_id' => $jobApplication->id,
			'application_email' => $jobApplication->email,
			'job_id' => @$request->job_id,
			'note' => 'Successfully Added'
		);
		GeneralMethod::UpdateTimeline($timelineArr);
		$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$request->job_id,'status' => 0,'application_id' => $jobApplication->id);
		DB::table('tb_ranks')->insert($tb_ranks);
		// Job Application Answer save
		if (isset($request->answer) && !empty($request->answer)) {
			foreach ($request->answer as $key => $value) {
				$answer = new JobApplicationAnswer();
				$answer->job_application_id = $jobApplication->id;
				$answer->job_id = $request->job_id;
				$answer->question_id = $key;
				$answer->answer = $value;
				$answer->save();
			}
		}

		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$request->job_id,
					'question_id' => @$jd->id,
					'application_id' => @$jobApplication->id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$expId = DB::getPdo()->lastInsertId();
				$this->elasticService->getClient()->update([
						'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'doc' => [
								'job_id' => @$request->job_id,
								'question_id' =>  @$jd->id,
								'application_id' => @$jobApplication->id,
								'job_detail_value' => @$jd->detail_value,
								'application_detail_value' =>@$request->additional_detail[$inc],
								'created_at' =>  now()->toIso8601String(),
								'updated_at' =>  now()->toIso8601String()
							],
							'doc_as_upsert' => true
						]
					]);
				$inc++;

			}
		}


		if (@$jobApplication->id > 0) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$jobApplication->id;
			$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
		}

		if (!empty($request->job_id)) {
			return Reply::redirect(url('/service_company/apptracker/' . $request->job_id . '/all'), __('menu.jobApplications') . ' ' . __($message));
		} else {
			return Reply::redirect(route('service_company.job-applications.table'), __('menu.jobApplications') . ' ' . __($message));
		}
	}
	public function show($id)
	{
		// $currentSlug = request()->segment(4);
		

		$company_id = '';
		$company_id = auth()->user()->company_id;
		$userId = $this->user->id;
		$this->editComman = true;

		$currentSlug = '';
		if(isset($_GET['slug'])){
			$currentSlug = $_GET['slug'];
		}		
		// $path = parse_url(url()->current(), PHP_URL_PATH);
		
		$this->notVisiableBench = true;
		

		// dd(auth()->user());
		$this->CaptainCompanyid = auth()->user()->company_id;
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$AddJobWiseCondidate = AddJobWiseCondidate::find($id);
		$jobId = @$AddJobWiseCondidate->job_id;
		// dd($id,$jobId,$AddJobWiseCondidate);
		$this->intModeArr = GeneralMethod::interview_mode();

		if($AddJobWiseCondidate){
			$params = [
				'index' => ELASTIC_CANDIDATES,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$this->application = (object) $response['_source'];

			// dd($this->application);

			$this->application->id = $this->application->candidate_id;
			$this->application->master_status_name = $this->application->name;
			$this->application->offer_currency = $this->application->of_offer_currency;
			$this->application->offer_mode = $this->application->of_offer_mode;
			$this->application->offer_amount = $this->application->of_offer_amount;
			$this->application->joined_date = $this->application->of_joined_date;
			$this->application->hashname = ($this->application->hashname) ? $this->application->hashname : $this->application->resume;
			$this->application->child_status_id = $this->application->childStatus;
			$this->application->master_status_id = $this->application->job_master_status_id;

			// dd($this->application->master_status_id,$this->application->from_company_id,auth()->user()->company_id);
			
		}else{

			$params = [
				'index' => ELASTIC_CANDIDATES_BENCH,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$this->application = (object) $response['_source'];

		}

		$params = [
			'index' => ELASTIC_JOBS,
			'id'    => $jobId,
		];
		
		$response = $this->elasticService->getClient()->get($params);
		$this->job = (object) $response['_source'];
		

		 $skills = DB::table('job_metronics_skills')
		 ->select('id', 'skill_name')
		 ->where('job_id', $jobId) 
		 ->where('company_id', $this->job->comId)
		 ->get();
		//  dd($skills);

		$existingSkillIds = DB::table('candidate_job_skill_exp')
		->where('job_id', $jobId)
		->where('candidate_id', $this->application->id)
		->pluck('job_skill_id')
		->toArray();
		if ($skills) {
				foreach ($skills as $skill) {
					if (!in_array($skill->id, $existingSkillIds)) {
						$values = array(
							'job_skill_id' =>  $skill->id,
							'skill_value' => 0,
							'job_id' => (int)$jobId,
						    'rating' => (int) @$skill->rating,
							'candidate_id' => $this->application->id
						);
						DB::table('candidate_job_skill_exp')->insert($values);
						$expId = DB::getPdo()->lastInsertId();

						$this->elasticService->getClient()->deleteByQuery([
							'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
							'conflicts' => 'proceed',   // important for production queues
							'body'  => [
								'query' => [
									'bool' => [
										'must' => [
											['term' => ['candidate_id' => (int)$this->application->id]],
											['term' => ['job_id'       => (int)$jobId]],
											['term' => ['job_skill_id' => (int)$skill->id]],
										]
									]
								]
							]
						]);



						$this->elasticService->getClient()->index([
							'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
							'id'    => $expId,
							'body'  => [
								'candidate_id' => (int) $this->application->id,
								'job_id'       => (int) $jobId,
								'job_skill_id' => (int) $skill->id,
								'skill_name'  => $skill->skill_name,
						    	'rating' => null,
								'skill_value' => null,   // default exp
								'created_at'  => now()->toIso8601String(),
								'updated_at'  => now()->toIso8601String()
							]
						]);
					}
				}
		}
		
		
		$params = [
			'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['application_id' => $this->application->id]],
							['term' => ['job_id' => (int) $jobId]]
						]
					]
				],
				'size' => 1   // we only need to know if one exists
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		$exists = !empty($response['hits']['hits']);

        if (!$exists) {
			$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->count();
			$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->get();
			if ($job_additional_detail_count > 0) {
				$inc = 0;
				foreach ($job_additional_detail as $jd) {

                   if(!empty(@$jd->detail_value)){
						$values = array(
							'job_id' => $jobId,
							'question_id' => @$jd->id,
							'application_id' => $this->application->id,
							'job_detail_value' => @$jd->detail_value,
							'application_detail_value' => 0
						);
						DB::table('application_additional_detail')->insert($values);
						$expId = DB::getPdo()->lastInsertId();
						$this->elasticService->getClient()->update([
								'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
								'id'    =>$expId,   // store as ES _id
								'body'  => [
									'doc' => [
										'job_id' => $jobId,
										'question_id' =>  @$jd->id,
										'application_id' => $this->application->id,
										'job_detail_value' => @$jd->detail_value,
										'application_detail_value' =>null,
										'created_at' =>  now()->toIso8601String(),
										'updated_at' =>  now()->toIso8601String()
									],
									'doc_as_upsert' => true // 🔥 key to avoiding conflicts
							]
						]);
				   }

					$inc++;

				}
			}
	    }



		
		$cachePayload = [
			'candidate_show' => $id,
			'currentSlug' => $currentSlug,
			'user_id' => $this->user->id
		];

		$versionKey = 'candidateDetails_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:candidateDetails:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
        if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

		
		$this->client_contuser_arr  = ($this->job->client_contact_info_jobs);
		$this->clientjobAssignListCaptain = '';
		if($this->job->comId != $company_id){
			$job_assign = $this->job->job_assign;
			$jobAssign = collect($job_assign);
			$this->jobAssignListCaptain = $jobAssign
			->where('spok_user_id', '>', 0)
			->where('company_id', $this->job->comId)
			->filter()
			->values()
			->all();
			// Check array exists
			if (!empty($this->jobAssignListCaptain)) {

				$this->clientjobAssignListCaptain = ucfirst(
					$this->jobAssignListCaptain[0]['user_name'] ?? ''
				);
			}

			// $this->jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $this->job->comId)
			// ->filter()
			// ->map(fn ($c) => ($c))
			// ->values()
			// ->all();
			// $this->clientjobAssignListCaptain = ucfirst($this->jobAssignListCaptain[0]['user_name']);
		   
		}
		

		$this->job_additional_detail = "";
		$params = [
			'index' => ELASTIC_APPLICANT_NOTES,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['add_job_wise_condidates_id'   => (int)$id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		$this->notesArr = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

		// dd($this->notesArr);
	

		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'body'  => [
				'_source' => ['schedule_date'],
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['interview_job_wise_con_id'   => (int) $id]],
							['term' => ['job_application_id'   => (int) $this->application->id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'asc']]
				],
				'size' => 10000 // optional: get more records
			]
		];
		
		$response = $this->elasticService->getClient()->search($params);
		
		$this->scheduleDateArr = collect($response['hits']['hits'] ?? [])->pluck('_source.schedule_date')->filter()->map(function ($date) {
				return Carbon::parse($date)->format('Y-m-d H:i:s');
		})
		->toArray();

		// dd($this->scheduleDateArr);

		// dd($this->schedule);

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $jobId;
		$this->job_id = $job_id;

		$job_details = Job::find($this->job_id);

		$params = [
			'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['candidate_id'   => $this->application->id]],
							['term' => ['job_id' => $this->job_id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		];
		
		$response = $this->elasticService->getClient()->search($params);
		// return users
		$this->jobSkill = empty($response['hits']['hits']) ? null : array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
		// dd($this->jobSkill);


		$this->jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
				})
				->select('job_metronics_skills.id as job_skill_id', 'job_metronics_skills.skill_name', 'candidate_job_skill_exp.skill_value','candidate_job_skill_exp.rating')
				->where('job_metronics_skills.job_id', '=', $this->job_id)
				->get();
				// dd($this->jobSkill);




		$this->skillShow = true;
		if($this->jobSkill){
			if(count($this->jobSkill) < 0){
				$this->skillShow = false;
			}
		}else{
			$this->skillShow = false; 
		}

		$params = [
			'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['application_id' => $this->application->id]],
							['term' => ['job_id' => $this->job_id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		]; 

		$response = $this->elasticService->getClient()->search($params);
		// return users
		$this->job_additional_detail = empty($response['hits']['hits']) ? null : array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);

		// dd($this->job_id,$this->application->id,$this->job_additional_detail);

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

		$params = [
			'index' => ELASTIC_CANDIDATE_TIMELINE,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['application_id'   => $application_id]],
							['term' => ['job_id' => @$jobId]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		// return users
		$this->updateTimeLine = empty($response['hits']['hits']) ? null : array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
		
		$sel_app_id = $id;
		$sel_job_id = $job_id;
		$this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
		if ($this->benchStatusCnt > 0) {
			$benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();
			$source_from_company_id = @$benchAssignRow->source_from_company_id;
			$source_from_user_id = @$benchAssignRow->source_from_user_id;
			$source_from_id = @$benchAssignRow->source_from_id;
			$source_from_type = @$benchAssignRow->source_from_type;
			$source_company_id = @$benchAssignRow->source_company_id;

			$source_user_id = @$benchAssignRow->source_user_id;
			$source_application_id = @$benchAssignRow->source_application_id;
			$source_jobid = @$benchAssignRow->source_jobid;

			// dd($this->userPermissions);
			
					if($company_id !=$this->application->other_company && $source_from_type == 2){
						$this->application->phone = '2xxx-xxx-xxxx';
						$this->application->secondary_mobile = '2xxx-xxx-xxxx';
						$this->application->email = '2xxx-xxx-xxxx';
						$this->application->secondrary_email = '2xxx-xxx-xxxx';
					}
			
			
			// if ($source_from_type == 2) {
				
			// }

			$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name','users.calling_code','users.email', 'users.mobile','users_profile.designation','users_profile.designation')
				->join('users', 'users.id', '=', 'bench_assign.user_id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
            // dd("djdjdjd");
			$this->bench_additional_detail = DB::table('bench_additional_detail')
				->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
				->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();
				
		}

		if(!in_array('salary_view', $this->userPermissions)){
				$this->application->ctc = 'N/A';
				$this->application->ectc = 'N/A';
				$this->application->pay_rate_amount = 'N/A';
				$this->application->bill_rate_amount = 'N/A';
				$this->application->percentage_hike = 'N/A';
		}

		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;

		// $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

		$params = [
			'index' => ELASTIC_BENCH_ASSIGN,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['bench_applications_id'   => (int) $this->application->id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		// return users
		$this->bench_assign_count = $response['hits']['total']['value'] ?? 0;
		$this->bench_assign_row = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);


		$params = [
			'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['bench_id'   => (int) $this->application->id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'asc']]
				],
				'size' => 10000 // optional: get more records
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		
		$this->bench_additional_detail = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

		$this->skillExperiencesCount = 0;
		$this->skillExperiences = [];

		if(@$this->application->condidate_type=='bench_condidate'){

		 $this->skillExperiences = DB::table('bench_candidate_skill_experience')
        ->where('candidate_id', $this->application->candidate_id)
        ->orderBy('skill_key')
        ->get();
		$this->skillExperiencesCount = DB::table('bench_candidate_skill_experience')
        ->where('candidate_id', $this->application->candidate_id)
        ->orderBy('skill_key')
        ->count();
		}


		// $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile','users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
		// ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

		$this->jobWiseCanId = $id;
		$this->editprofile = false;
		$this->skillAndQuestions = true;
		$this->currentSlug = '';
		if(isset($_GET['slug'])){
			$currentSlug = $_GET['slug'];
			
			  // dd($currentSlug);
			if($currentSlug==='apptracker'){
				$this->skillAndQuestions = true;
			}
			if($currentSlug==='apptracker' || $currentSlug==='cvtracker' || $currentSlug==='interview-schedule'){
				$this->editprofile = true;
				$this->currentSlug = $_GET['slug'];
			}
		}

		$this->currentPage = 1;
		if(isset($_GET['currentPage'])){
			$currentPage = $_GET['currentPage'];
			$this->currentPage = $currentPage;
		}
		$path = url()->previous();
		// $path = parse_url(url()->current(), PHP_URL_PATH);
		$segments = explode('/', trim($path, '/'));
		$exists = in_array('bench-candidates', array_map('strtolower', $segments), true);
		$this->notVisiableBench = true;
		if($exists){
           $this->notVisiableBench = false;
		}

		$this->resumeAwsPath = false;
		if(!empty($this->application->resume)){
			$info = pathinfo($this->application->resume);
			$path = str_replace('https://file.jero.works/', '', $this->application->resume);
			// dd($info);
			$s3Path = 'Resumes/'. $info['basename'];
				

			if (Storage::disk('r2')->exists($path)) {
				$this->resumeAwsPath = true;
			}
		}

		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		// $view = view('service_company.job-applications.show', $this->data)->render();
		// dd("jd");
		$view = view('service_company.job-applications.showdetail', $this->data)->render();
		$responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(CANDIDATE_DETAILS_TIME));

		return $responseData;

	}
	public function websiteShow($id)
	{
		// $currentSlug = request()->segment(4);
		
		$company_id = '';
		$company_id = auth()->user()->company_id;
		// dd(auth()->user());
		$this->CaptainCompanyid = auth()->user()->company_id;
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$AddJobWiseCondidate = JobApplication::find($id);
		$jobId = @$AddJobWiseCondidate->job_id;
		$this->editComman = false;
		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = JobApplication::select(
			'job_applications.id',
			'add_job_wise_condidates.submitted_by',
			'job_applications.submitted_by as editAllow',
			'job_applications.company_id',
			'job_applications.status_type',
			'job_applications.np_id',
			'job_applications.serving_notice_period_mode',
			'job_applications.full_name',
			'job_applications.phone_code',
			'job_applications.second_phone_code',
			'job_applications.email',
			'job_applications.phone',
			'job_applications.careersummary',
			'job_applications.nationality',
			'job_applications.wm',
			'job_applications.employmentType',
			'job_applications.oh',
			'job_applications.visastatu',
			'job_applications.referencename',
			'job_applications.secondary_mobile',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.rating',
			'job_applications.skype_id',
			'job_applications.cover_letter',
			'add_job_wise_condidates.job_id',
			'add_job_wise_condidates.thirdparty_company_id as ClientComID',
			'job_applications.status_id',
			'job_applications.is_viewed',
			'job_applications.column_priority',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.deleted_at',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.passport',
			'job_applications.current_location',
			'job_applications.current_designation',
			'job_applications.current_company',
			'job_applications.process_id',
			'job_applications.qualification',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'job_applications.secondrary_email',
			'job_applications.secondary_mobile',
			'job_applications.skills',
			'job_applications.city',
			'job_applications.country',
			'job_applications.pincode',
			'job_applications.state',
			'job_applications.interviewDateOne',
			'job_applications.interviewDateTwo',
			'job_applications.interviewDateThree',
			'job_applications.interviewDateFour',
			'job_applications.interviewDateFive',
			'job_applications.interviewDateSix',
			'job_applications.linkedin',
			'job_applications.github',
			'job_applications.behance',
			'job_applications.otherLink',
			'job_applications.interview_mode_one',
			'job_applications.interview_mode_two',
			'job_applications.interview_mode_three',
			'job_applications.interview_mode_four',
			'job_applications.interview_mode_five',
			'job_applications.interview_mode_six',
			'job_applications.current_ctc_cur',
			'job_applications.ctc',
			'job_applications.current_ctc_mode',
			'job_applications.current_ectc_cur',
			'job_applications.bill_rate_currency',
			'job_applications.bill_rate_amount',
			'job_applications.bill_rate_mode',
			'job_applications.pay_rate_currency',
			'job_applications.pay_rate_amount',
			'job_applications.pay_rate_mode',
			'job_applications.ectc',
			'job_applications.current_ectc_mode',
			'job_applications.percentage_hike',
			'job_applications.comment',
			'job_applications.dob',
			'job_applications.created_at',
			'job_applications.company_id as other_company',
			'job_applications.condidate_type',
			'clients.name as client_name',
			'jobs.title',
			'users.name as posted_by',
			'users.image as posted_by_image',
			//'notice_periods.np_title',
			'master_status.name as master_status_name',
			'offer_job_applications.proposed_ctc',
			'offer_job_applications.any_offer',
			'offer_job_applications.document',
			'offer_job_applications.sub_for_approval',
			'offer_job_applications.offer_approved',
			'offer_job_applications.offer_release',
			'offer_job_applications.offer_acceptance',
			'offer_job_applications.joined',
			'offer_job_applications.joined_date',
			'offer_job_applications.offer_currency',
			'offer_job_applications.offer_mode',
			'offer_job_applications.offer_amount',
			'offer_job_applications.confidential',
			'qualification_master.name as qualification_name',
			'companies.company_name',
			'owncom.company_name as own_company_name',
			'state_list.state as state_name',
			'city_list.city as city_name',
			'recommendation_relation.job_id as rjobid',
			'recommendation_relation.from_company_id',
			'add_job_wise_condidates.id as wiseId',
			'documents.hashname'
		)
			->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->leftJoin('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftJoin('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('recommendation_relation', function ($join) {
				$join->on('recommendation_relation.job_id', 'jobs.id');
			})
			// ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id') 
			->leftJoin('companies', function ($join) {
				$join->on('companies.id', 'job_applications.company_id');
			})
			->leftJoin('companies as owncom', function ($join) {
				$join->on('owncom.id', 'job_applications.company_id');
			})
			->leftJoin('offer_job_applications', function ($join) use ($id, $jobId) {
				$join->on('offer_job_applications.application_id', 'job_applications.id');
				$join->where('offer_job_applications.job_id', $jobId);
				// $join->where('offer_job_applications.company_id', $company_id);
			})
			// ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			//->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
			
			$this->application = $this->application->where('job_applications.id', $id);
				
		

			// $this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
			$this->application = $this->application->orderByDesc('documents.id')->first();

			//  dd($this->application);


		$this->client_contuser_arr = DB::table('client_contact_info_jobs')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->join('clients', 'clients.id', '=', 'client_contact_info.client_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')->where('client_contact_info_jobs.job_id', @$jobId)->get();

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			// ->where('job_additional_detail.company_id', '=', $company_id)
			->where('job_additional_detail.job_id', '=', @$jobId)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();

		$this->application_additional_detail = DB::table('application_additional_detail')
			->select('*')
			->where('application_additional_detail.application_id', '=', @$this->application->id)
			->where('application_additional_detail.job_id', '=', @$jobId)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();

		$this->bench_additional_detail = DB::table('bench_additional_detail')
			->select('*')
			->where('bench_additional_detail.bench_id', '=', $this->application->id)
			->orderBy('bench_additional_detail.id', 'ASC')
			->get();



		//echo '<pre>'; print_r($this->application); exit;
		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}
		$this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $jobId)->where('job_application_id', $this->application->id)->get();

		$this->notesArr = DB::table('applicant_notes')
			->select(
				'applicant_notes.id',
				'applicant_notes.note_text',
				'applicant_notes.created_at',
				'applicant_notes.updated_at',
				'users.name',
				'users.image as posted_by_image'
			)
			->join('job_applications', 'job_applications.id', 'applicant_notes.job_application_id')
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			//->where('company_id', user()->company_id) 
			->join('users', 'users.id', '=', 'applicant_notes.user_id')
			->where('add_job_wise_condidates.id', $id)
			->where('applicant_notes.job_application_id', $this->application->id)
			->where('applicant_notes.user_id', auth()->user()->id)
			->orderBy('id', 'ASC')
			->get();


		$this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at', 'interview_schedules.available_status', 'interview_schedules.reschedule', 'interview_schedules.feedback')
			->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			// ->where('interview_schedules.company_id', user()->company_id)
			// ->join('users', 'users.id', '=', 'applicant_notes.user_id')
			->where('add_job_wise_condidates.id', $id)
			->where('interview_schedules.job_application_id', $this->application->id)
			->orderBy('id', 'DESC')
			->get();

		// dd($this->schedule);

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $jobId;
		$this->job_id = $job_id;
        $this->notVisiableBench = false;
		
		// $this->jobSkill = DB::table('job_metronics_skills')
		// ->leftJoin('candidate_job_skill_exp','candidate_job_skill_exp.job_skill_id','=','job_metronics_skills.id')
		// ->select('job_metronics_skills.*','candidate_job_skill_exp.skill_value')
		// ->where('candidate_job_skill_exp.job_id','=', $this->job_id)
		// ->where('candidate_job_skill_exp.candidate_id','=', $this->application->id)
		// ->get();
		   $job_details = Job::find($this->job_id);

		   $this->jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
				})
				->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
				->where('job_metronics_skills.job_id', '=', $this->job_id)
				->where('job_metronics_skills.company_id', '=', $job_details->company_id)
				->get();
				// dd($job_details->company_id);

				$this->skillShow = true;
				if(!DB::table('candidate_job_skill_exp')->where('candidate_job_skill_exp.job_id','=', $this->job_id)->where('candidate_job_skill_exp.candidate_id','=', $this->application->id)->exists()){
					$this->skillShow = false;
				}


				$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'body'  => [
				'_source' => ['schedule_date'],
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['interview_job_wise_con_id'   => (int) $id]],
							['term' => ['job_application_id'   => (int) $this->application->id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'asc']]
				],
				'size' => 10000 // optional: get more records
			]
		];
		
		$response = $this->elasticService->getClient()->search($params);
		
		$this->scheduleDateArr = collect($response['hits']['hits'] ?? [])->pluck('_source.schedule_date')->filter()->map(function ($date) {
				return Carbon::parse($date)->format('Y-m-d H:i:s');
		})
		->toArray();
		




		$this->timeline = DB::table('timeline');
		$this->timeline = $this->timeline->select(
			'timeline.id',
			'timeline.owner_id',
			'timeline.application_id',
			'timeline.application_email',
			'timeline.job_id',
			'timeline.process_id',
			'timeline.status_name',
			'timeline.process_child_name',
			'timeline.note',
			'timeline.visible',
			'timeline.insert_type',
			'timeline.create_at',
			'timeline.interview_schedule_date',
			'timeline.interview_from_time',
			'timeline.interview_to_time',
			'timeline.interview_mode',
			'timeline.interview_mention_link',
			'timeline.interviewers',
			'timeline.interview_additional_email',
			'timeline.interview_message',
			'jobs.id as job_id',
			'jobs.title',
			'master_status.name as master_status_name',
			'clients.name as client_name',
			'timeline.timeline_type',
			'timeline.prev_process_name',
			'timeline.prev_process_child_name',
			'timeline.interview_mode_child_level',
			'timeline.average_star_rating',
			'timeline.interview_schedules_id',
			'master_status.css_style',
			'users.name',
			'users.image'
		);
		$this->timeline = $this->timeline->join('job_applications', 'job_applications.id', '=', 'timeline.application_id');
		$this->timeline = $this->timeline->leftJoin('users', 'users.id', '=', 'timeline.owner_id');
		$this->timeline = $this->timeline->leftJoin('jobs', 'jobs.id', '=', 'timeline.job_id');
		$this->timeline = $this->timeline->leftJoin('clients', 'clients.id', '=', 'jobs.client_id');
		$this->timeline = $this->timeline->leftJoin('master_status', 'master_status.id', '=', 'timeline.process_id');
		$this->timeline = $this->timeline->where('timeline.application_id', '=', $application_id);
		$this->timeline = $this->timeline->where('timeline.job_id', '=', $job_id);
		$this->timeline = $this->timeline->orderBy('timeline.id', 'DESC');
		$this->timeline = $this->timeline->get();

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);


		// UPDATE TIMELINE
		$this->updateTimeLine = DB::table('updatetimeline');
		$this->updateTimeLine = $this->updateTimeLine->select(
			'updatetimeline.id',
			'updatetimeline.event_id',
			'updatetimeline.event_by_company_id',
			'updatetimeline.event_by_user_id',
			'updatetimeline.master_process_name',
			'updatetimeline.child_process_name',
			'updatetimeline.application_id',
			'updatetimeline.application_email',
			'updatetimeline.job_id',
			'updatetimeline.note',
			'updatetimeline.create_at',
			'updatetimeline.update_at',
			'updatetimeline.prev_process_name',
			'updatetimeline.comment',
			'updatetimeline.prev_process_child_name',
			'updatetimeline.interview_schedule_date',
			'updatetimeline.interview_mode',
			'updatetimeline.interview_mention_link',
			'updatetimeline.interview_mode_child_level',
			'updatetimeline.interviewers',
			'updatetimeline.average_star_rating',
			'updatetimeline.interview_message',
			'updatetimeline.interview_schedules_id',
			'updatetimeline.offer_currency',
			'updatetimeline.offer_mode',
			'updatetimeline.offer_amount',
			'updatetimeline.joined_date',
			'updatetimeline.candidate_status',
			'updatetimeline.management_status',
			'updatetimeline.accept_date_time',
			'updatetimeline.approved_date_time',
			'updatetimeline.send_approved_date_time',
			'updatetimeline.send_accept_date_time',
			'updatetimeline.email_id_hr',
			'updatetimeline.name_hr',
			'updatetimeline.yearly_gross',
			'updatetimeline.yearly_final_gross',
			'updatetimeline.mtypeSection',
			'updatetimeline.designation_offer',
			'updatetimeline.timezone_abbreviation',
			'updatetimeline.confidential',
			'updatetimeline.interview_to_time',
			'application_event.event_name',
			'jobs.id as job_id',
			'jobs.title',
			'jobs.employment_type',
			'users.name',
			'users.image',
			'clients.name as client_name',
			'interview_schedules.available_status',
			'interview_schedules.reschedule',
			'interview_schedules.feedback',
			'interview_schedules.stage',
			'master_status.name as status_name',
			'companies.company_name as cur_companyname',
			'master_status.css_style'
		);
		$this->updateTimeLine = $this->updateTimeLine->leftjoin('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
		$this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
		$this->updateTimeLine = $this->updateTimeLine->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id');
		$this->updateTimeLine = $this->updateTimeLine->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage');
		$this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
		$this->updateTimeLine = $this->updateTimeLine->leftjoin('companies', 'companies.id', 'users.company_id');
		$this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
		$this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');

		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $application_id);
		// $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.event_by_company_id', '=', auth()->user()->company_id);
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$jobId);
		// $this->updateTimeLine = $this->updateTimeLine->orWhere(function ($query) use ($company_id, $id) {
		// 	return
		// 		$query->where('job_applications.thirdparty_company_id', '=', $company_id)
		// 			->where('job_applications.id', '=', $id);
		// 	//->where('staffing_company_relation.final_status', '=', 1); 
		// });
		$this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
		$this->updateTimeLine = $this->updateTimeLine->get();

        // dd(	$this->updateTimeLine);

		$sel_app_id = $id;
		$sel_job_id = $job_id;
		$this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
		if ($this->benchStatusCnt > 0) {
			$benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

			$source_from_company_id = @$benchAssignRow->source_from_company_id;
			$source_from_user_id = @$benchAssignRow->source_from_user_id;
			$source_from_id = @$benchAssignRow->source_from_id;
			$source_from_type = @$benchAssignRow->source_from_type;
			$source_company_id = @$benchAssignRow->source_company_id;

			$source_user_id = @$benchAssignRow->source_user_id;
			$source_application_id = @$benchAssignRow->source_application_id;
			$source_jobid = @$benchAssignRow->source_jobid;

			// dd($this->userPermissions);
			
				
					if($company_id !=$this->application->other_company && $source_from_type == 2){
						$this->application->phone = '2xxx-xxx-xxxx';
						$this->application->secondary_mobile = '2xxx-xxx-xxxx';
						$this->application->email = '2xxx-xxx-xxxx';
						$this->application->secondrary_email = '2xxx-xxx-xxxx';
					}
				


			
			// if ($source_from_type == 2) {
				
			// }

			$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name','users.calling_code','users.email', 'users.mobile','users_profile.designation')
				->join('users', 'users.id', '=', 'bench_assign.user_id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
			$this->bench_additional_detail = DB::table('bench_additional_detail')
				->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
				->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

		}

		if(!in_array('salary_view', $this->userPermissions)){
				$this->application->ctc = 'N/A';
				$this->application->ectc = 'N/A';
				$this->application->pay_rate_amount = 'N/A';
				$this->application->bill_rate_amount = 'N/A';
				$this->application->percentage_hike = 'N/A';
		}


		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;
		$this->additional_skills_arr = DB::table('skills')->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
			//->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
			->select('skills.name', 'candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', $jobId)->where('candidate_skills_value.candidate_id', $this->application->id)->where('skills.company_id', $this->user->company_id)->get();
		$this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->count();
		$this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->get();
		$this->offer_job_applications_count = DB::table('offer_job_applications')->select('offer_job_applications.id')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
			//->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
			->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->count();
		$this->offer_job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
			//->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
			->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
			//->where('job_id', $this->application->job_id)
			->orderBy('offer_job_applications.id', 'DESC')->get();
		//echo '<pre>'; print_r($this->offer_job_applications); exit;

		$resume = '';
		$resumeData = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', '=', 'Resume')->first();
		$this->resume = @$resumeData->hashname;


		$this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

		$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile','users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
		->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

		$this->jobWiseCanId = $id;
		$this->editprofile = false;
		$this->skillAndQuestions = true;
		$this->currentSlug = '';
		if(isset($_GET['slug'])){
			$currentSlug = $_GET['slug'];
			
			  // dd($currentSlug);
			  if($currentSlug==='apptracker'){
                  $this->skillAndQuestions = true;
			  }
			if($currentSlug==='apptracker' || $currentSlug==='cvtracker' || $currentSlug==='interview-schedule'){
				$this->editprofile = true;
				$this->currentSlug = $_GET['slug'];
			}
		}

		$this->currentPage = 1;
		if(isset($_GET['currentPage'])){
			$currentPage = $_GET['currentPage'];
			$this->currentPage = $currentPage;
		}


		if($AddJobWiseCondidate){
			$params = [
				'index' => ELASTIC_CAREER_APPLY,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$data = (object) $response['_source'];

			// dd($data);
			$this->application->photo = $data->photo;
			$this->application->country = $data->country;
			$this->application->state = $data->state;
			$this->application->state_name = $data->state_name;
			$this->application->city_name = $data->city_name;
			$this->application->city = $data->city;
			$this->application->condidate_type = $data->condidate_type;
			$this->application->current_ctc_cur = $data->current_ctc_cur;
			$this->application->current_ectc_cur = $data->current_ectc_cur;
			$this->application->bill_rate_currency = $data->bill_rate_currency;
			$this->application->bill_rate_currency = $data->bill_rate_currency;
			$this->application->master_status_id = $data->master_status_id;
			$this->application->rjobid = $data->rjobid;
			$this->application->from_company_id = $data->from_company_id;
			$this->application->wiseId = $data->id;
			$this->application->company_id = $data->company_id;
			$this->application->submitted_by = $data->submitted_by;
			$this->application->other_company = $data->other_company;
			$this->application->phone = $data->phone;
			$this->application->full_name = $data->full_name;
			$this->application->current_designation = $data->current_designation;
			$this->application->total_exp = $data->total_exp;
			$this->application->current_company = $data->current_company;
			$this->application->skills = $data->skills;
			$this->application->ClientComID = $data->ClientComID;
			$this->application->job_id = $data->jobId;
			$this->application->posted_by = $data->posted_by;
			$this->application->posted_by_image = $data->posted_by_image;
			$this->application->np_id = $data->np_id;
			$this->application->serving_notice_period_mode = $data->serving_notice_period_mode;
			$this->application->rel_exp = $data->rel_exp;
			$this->application->qualification_name = $data->qualification_name;
			$this->application->dob = $data->dob;
			$this->application->ctc = $data->ctc;
			$this->application->current_ctc_mode = $data->current_ctc_mode;
			$this->application->pay_rate_amount = $data->pay_rate_amount;
			$this->application->pay_rate_mode = $data->pay_rate_mode;
			$this->application->ectc = $data->ectc;
			$this->application->bill_rate_amount = $data->bill_rate_amount;
			$this->application->bill_rate_mode = $data->bill_rate_mode;
			$this->application->percentage_hike = $data->percentage_hike;
			$this->application->comment = $data->comment;
			$this->application->secondary_mobile = $data->secondary_mobile;
			$this->application->email = $data->email;
			$this->application->secondrary_email = $data->secondrary_email;
			$this->application->master_status_name = $data->name;
			$this->application->offer_currency = $data->of_offer_currency;
			$this->application->offer_mode = $data->of_offer_mode;
			$this->application->offer_amount = $data->of_offer_amount;
			$this->application->joined_date = $data->of_joined_date;
			$this->application->hashname = ($data->hashname) ? $data->hashname : $data->resume;
			$this->application->phone_code = $data->phone_code;
			$this->application->second_phone_code = $data->second_phone_code;
			$this->application->id = $data->candidate_id;
			$this->application->updated_at = $data->updated_at;	
			$this->application->created_at = $data->created_at;	
			$this->application->interview_mode_one = @$data->interview_mode_one;	
			$this->application->interviewDateOne = @$data->interviewDateOne;	
			$this->application->interviewDateTwo = @$data->interviewDateTwo;		
			$this->application->interview_mode_two = @$data->interview_mode_two;			
			$this->application->interviewDateThree = @$data->interviewDateThree;	
			$this->application->interview_mode_three = @$data->interview_mode_three;	
			$this->application->interviewDateFour = @$data->interviewDateFour;
			$this->application->interview_mode_four = @$data->interview_mode_four;
			$this->application->interviewDateFive = @$data->interviewDateFive;	
			$this->application->interview_mode_five = @$data->interview_mode_five;
			$this->application->interviewDateSix = @$data->interviewDateSix;	
			$this->application->interview_mode_six = @$data->interview_mode_six;	
			$this->application->github = @$data->github;
			$this->application->otherLink = @$data->otherLink;	
			$this->application->behance = @$data->behance;	
			$this->application->linkedin = @$data->linkedin;		
			

		}

		$this->resumeAwsPath = false;
		if(!empty($this->application->resume)){
		    $info = pathinfo($this->application->resume);
			$path = str_replace('https://file.jero.works/', '', $this->application->resume);
			$s3Path = 'Resumes/'. $info['basename'];
				
			if (Storage::disk('r2')->exists($path)) {
				$this->resumeAwsPath = true;
			}
		}

		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();

		// Populate job skills for skill-highlight panel — mirrors show() but uses local $jobId
		$this->jobSkill = collect();
		$_wsJobId = $jobId ?? @$this->application->job_id;
		if (!empty($_wsJobId)) {
			$this->jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function ($join) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id);
				})
				->select('job_metronics_skills.id as job_skill_id', 'job_metronics_skills.skill_name', 'candidate_job_skill_exp.skill_value', 'candidate_job_skill_exp.rating')
				->where('job_metronics_skills.job_id', '=', $_wsJobId)
				->get();
		}

		// $view = view('service_company.job-applications.show', $this->data)->render();
		// dd("jd");
		$view = view('service_company.job-applications.showdetail', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);


	}
	public function edit($id)
	{
		// dd("kdf");
		$this->pageTitle = __('Edit Candidate');
		abort_if(Gate::denies('edit_job_applications'), 403);
		$id = GeneralMethod::value_decrypt($id);
		$company_id = '';
		$company_id = auth()->user()->company_id;

		// Authorization: only the assigned Captain (job_assign.spok_user_id) or the submitting user
		// (add_job_wise_condidates.submitted_by), within the same company, may open this edit form.
		// Here $id is the add_job_wise_condidates.id.
		$authUserId = auth()->id();
		$canEditCandidate = DB::table('add_job_wise_condidates as ajwc')
			->leftJoin('job_assign as ja', function ($join) {
				$join->on('ja.job_id', '=', 'ajwc.job_id')
					->on('ja.company_id', '=', 'ajwc.company_id');
			})
			->where('ajwc.id', $id)
			->where('ajwc.company_id', $company_id)
			->where(function ($query) use ($authUserId) {
				$query->where('ja.spok_user_id', $authUserId)
					->orWhere('ajwc.submitted_by', $authUserId);
			})
			->exists();

		if (!$canEditCandidate) {
			return redirect()->back()->with('candidate_edit_denied', "You are not authorized to edit this candidate's profile. Only the assigned Captain or the submitting user can edit it");
		}

		$AddJobWiseCondidate = AddJobWiseCondidate::find($id);
		$this->AddJobWiseCondidateRankRow = $id;
		// dd($AddJobWiseCondidate->job_id);
		$this->statuses = ApplicationStatus::all();
		$this->application = JobApplication::find($AddJobWiseCondidate->job_application_id);
		$this->jobQuestion = $this->application->job->questions;
		$this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->get();
		$this->noticeperiod = NoticePeriod::all();
		$this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id',[57,58,59])->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$this->childArr = DB::table('candidate_status')
			->select('*')->where('parent', $this->application->master_status_id)
			->where('company_id', $this->user->company_id)
			->orderBy('priority', 'ASC')
			->get();

		$this->company_id = $this->user->company_id;
		$this->application_add_detail_cnt = DB::table('application_additional_detail')
			->select('*')
			->where('application_id', $this->application->id)
			->orderBy('application_additional_detail.id', 'ASC')
			->count();

		$this->application_add_detail = DB::table('application_additional_detail')
			->select('*')
			->where('application_id', $this->application->id)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();


		//$jobId 							=	$this->application->job_id; 	
		$this->jobId = $AddJobWiseCondidate->job_id; //$this->application->job_id;
		$this->appId = $this->application->id;

		$this->jobDetail = Job::select('jobs.id','jobs.company_id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city','jobs.min_sal_mode','jobs.max_sal_mode','jobs.min_salary_currency','jobs.max_salary_currency')
			//->join('job_assign', 'job_assign.job_id', 'jobs.id')
			->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where('jobs.id', '=', $this->jobId)
			->first();


			// $this->jobSkill = DB::table('job_metronics_skills')
			// 	->leftJoin('candidate_job_skill_exp', function($join) {
			// 		$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
			// 			->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
			// 	})
			// 	->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
			// 	->where('job_metronics_skills.job_id', '=', $this->jobId)
			// 	->get();

			$this->jobSkill = DB::table('job_metronics_skills')
			->leftJoin('candidate_job_skill_exp', function($join) {
				$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
					->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id);
			})
			->select(
				DB::raw('MIN(job_metronics_skills.id) as id'), // 👈 pick one id
				'job_metronics_skills.skill_name',
				DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
				DB::raw('MAX(candidate_job_skill_exp.rating)  as rating')
			)
			->where('job_metronics_skills.job_id', $this->jobId)
			->groupBy('job_metronics_skills.skill_name') // 👈 KEY FIX
			->get();

				$this->skillShow = true;

			


		$this->page_event = 'EDIT';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->countries = GeneralMethod::getAllCountryList();
		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		$this->statelist = GeneralMethod::statelist($this->application->country);
		$this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->orderByDesc('id')->first();

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			// ->where('job_additional_detail.company_id', '=', $company_id)
			->where('job_additional_detail.job_id', '=', $this->jobId)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();
        // dd($this->job_additional_detail,$this->application->id,$this->jobId);
		$this->application_additional_detail = DB::table('application_additional_detail')
			->select('*')
			->where('application_additional_detail.application_id', '=', $this->application->id)
			->where('application_additional_detail.job_id', '=', $this->jobId)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();
			// dd($this->application_additional_detail);

		$this->cityListArr = '';
		if ($this->application->country == 101) {
			$statelist = DB::table('state_list')
				->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
				->orderBy('state_list.state')
				->get()->toArray();
			$this->statelist = json_decode(json_encode($statelist), true);

			$citylist = DB::table('city_list')
			    ->join('state_list','state_list.id','=','city_list.state_id')
				->join('countries','countries.id','=','state_list.country_id')
				->select('city_list.city_id as id', 'city_list.city as name','state_list.state','countries.country_name','countries.country_code')
				->where('state_list.country_id', $this->application->country)
				->orderBy('city_list.city')
				->get()->toArray();

				// dd($citylist);

			   $this->cityListArr = json_decode(json_encode($citylist), true);

		}
		$this->cities = GeneralMethod::allCities();

		if($AddJobWiseCondidate){
			$params = [
				'index' => ELASTIC_CANDIDATES,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$data = (object) $response['_source'];

			// dd($data);
			$this->application->photo = $data->photo;
			$this->application->country = $data->country;
			$this->application->state = $data->state;
			$this->application->state_name = $data->state_name;
			$this->application->city_name = $data->city_name;
			$this->application->city = $data->city;
			$this->application->pincode = $data->pincode;
			$this->application->condidate_type = $data->condidate_type;
			$this->application->current_ctc_cur = $data->current_ctc_cur;
			$this->application->current_ectc_cur = $data->current_ectc_cur;
			$this->application->bill_rate_currency = $data->bill_rate_currency;
			$this->application->bill_rate_currency = $data->bill_rate_currency;
			$this->application->master_status_id = $data->master_status_id;
			$this->application->rjobid = $data->rjobid;
			$this->application->from_company_id = $data->from_company_id;
			$this->application->wiseId = $data->id;
			$this->application->company_id = $data->company_id;
			$this->application->submitted_by = $data->submitted_by;
			$this->application->other_company = $data->other_company;
			$this->application->phone = $data->phone;
			$this->application->full_name = $data->full_name;
			$this->application->current_designation = $data->current_designation;
			$this->application->total_exp = $data->total_exp;
			$this->application->current_company = $data->current_company;
			$this->application->skills = $data->skills;
			$this->application->ClientComID = $data->ClientComID;
			$this->application->job_id = $data->jobId;
			$this->application->posted_by = $data->posted_by;
			$this->application->posted_by_image = $data->posted_by_image;
			$this->application->np_id = $data->np_id;
			$this->application->serving_notice_period_mode = $data->serving_notice_period_mode;
			$this->application->rel_exp = $data->rel_exp;
			$this->application->qualification_name = $data->qualification_name;
			$this->application->dob = $data->dob;
			$this->application->ctc = $data->ctc;
			$this->application->current_ctc_mode = $data->current_ctc_mode;
			$this->application->pay_rate_amount = $data->pay_rate_amount;
			$this->application->pay_rate_mode = $data->pay_rate_mode;
			$this->application->ectc = $data->ectc;
			$this->application->bill_rate_amount = $data->bill_rate_amount;
			$this->application->bill_rate_mode = $data->bill_rate_mode;
			$this->application->percentage_hike = $data->percentage_hike;
			$this->application->comment = $data->comment;
			$this->application->secondary_mobile = $data->secondary_mobile;
			$this->application->email = $data->email;
			$this->application->secondrary_email = $data->secondrary_email;
			$this->application->master_status_name = $data->name;
			$this->application->offer_currency = $data->of_offer_currency;
			$this->application->offer_mode = $data->of_offer_mode;
			$this->application->offer_amount = $data->of_offer_amount;
			$this->application->joined_date = $data->of_joined_date;
			$this->application->hashname = $data->hashname;
			$this->application->phone_code = $data->phone_code;
			$this->application->second_phone_code = $data->second_phone_code;
			$this->application->id = $data->candidate_id;	
			$this->application->city_id = $data->city_id;
			$this->application->qualification = $data->qualification;	
			$this->application->interview_mode_one = @$data->interview_mode_one;	
			$this->application->interviewDateOne = @$data->interviewDateOne;	
			$this->application->interviewDateTwo = @$data->interviewDateTwo;		
			$this->application->interview_mode_two = @$data->interview_mode_two;			
			$this->application->interviewDateThree = @$data->interviewDateThree;	
			$this->application->interview_mode_three = @$data->interview_mode_three;	
			$this->application->interviewDateFour = @$data->interviewDateFour;
			$this->application->interview_mode_four = @$data->interview_mode_four;
			$this->application->interviewDateFive = @$data->interviewDateFive;	
			$this->application->interview_mode_five = @$data->interview_mode_five;
			$this->application->interviewDateSix = @$data->interviewDateSix;	
			$this->application->interview_mode_six = @$data->interview_mode_six;	
			$this->application->github = @$data->github;
			$this->application->otherLink = @$data->otherLink;	
			$this->application->behance = @$data->behance;	
			$this->application->linkedin = @$data->linkedin;	
		}
		$this->resumeAwsPath = false;
		if(!empty($this->application->resume)){
		    $info = pathinfo($this->application->resume);
			$path = str_replace('https://file.jero.works/', '', $this->application->resume);
			$s3Path = 'Resumes/'. $info['basename'];
				
			if (Storage::disk('r2')->exists($path)) {
				$this->resumeAwsPath = true;
			}
		}
			   
        $this->currentPage = 1;
		if(isset($_GET['currentPage'])){
			$currentPage = GeneralMethod::value_decrypt($_GET['currentPage']); ;
			$this->currentPage = $currentPage;
		}
		 $this->currentSlug = 1;
		if(isset($_GET['currentSlug'])){
			$currentSlug = GeneralMethod::value_decrypt($_GET['currentSlug']); ;
			$this->currentSlug = $currentSlug;
		}
	    $this->default_loc = GeneralMethod::allCitiesSelected(array(@$this->application->city_id));
		$this->currencyConverter = GeneralMethod::currencyConverter();
		// dd($id);
		return view('service_company.job-applications.create', $this->data);
	}
	public function createOffer($id){


		// dd($id);
		$company_id = '';
		$company_id = auth()->user()->company_id;
		// dd(auth()->user());
		$OfferJobApplication = OfferJobApplication::find($id);
		// dd($OfferJobApplication);
		$this->offer_id = ($id) ? $id : '';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$AddJobWiseCondidate = AddJobWiseCondidate::find($OfferJobApplication->joined);
		$id =  $OfferJobApplication->joined;
		$jobId = @$AddJobWiseCondidate->job_id;
		// dd($id,$jobId,$AddJobWiseCondidate);
		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = JobApplication::select(
			'job_applications.id',
			'add_job_wise_condidates.submitted_by',
			'job_applications.submitted_by as editAllow',
			'add_job_wise_condidates.company_id',
			'job_applications.np_id',
			'job_applications.serving_notice_period_mode',
			'job_applications.full_name',
			'job_applications.email',
			'job_applications.phone',
			'job_applications.secondary_mobile',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.rating',
			'job_applications.skype_id',
			'job_applications.cover_letter',
			'add_job_wise_condidates.job_id',
			'job_applications.status_id',
			'job_applications.is_viewed',
			'job_applications.column_priority',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.deleted_at',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.passport',
			'job_applications.current_location',
			'job_applications.current_designation',
			'job_applications.current_company',
			'job_applications.process_id',
			'job_applications.qualification',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'job_applications.secondrary_email',
			'job_applications.secondary_mobile',
			'job_applications.skills',
			'job_applications.city',
			'job_applications.country',
			'job_applications.pincode',
			'job_applications.state',
			'job_applications.current_ctc_cur',
			'job_applications.ctc',
			'job_applications.current_ctc_mode',
			'job_applications.current_ectc_cur',
			'job_applications.bill_rate_currency',
			'job_applications.bill_rate_amount',
			'job_applications.bill_rate_mode',
			'job_applications.pay_rate_currency',
			'job_applications.pay_rate_amount',
			'job_applications.pay_rate_mode',
			'job_applications.ectc',
			'job_applications.current_ectc_mode',
			'job_applications.percentage_hike',
			'job_applications.comment',
			'job_applications.dob',
			'job_applications.created_at',
			'job_applications.company_id as other_company',
			'job_applications.condidate_type',
			'clients.name as client_name',
			'jobs.title',
			'users.name as posted_by',
			'users.image as posted_by_image',
			//'notice_periods.np_title',
			'master_status.name as master_status_name',
			'offer_job_applications.proposed_ctc',
			'offer_job_applications.any_offer',
			'offer_job_applications.document',
			'offer_job_applications.sub_for_approval',
			'offer_job_applications.offer_approved',
			'offer_job_applications.offer_release',
			'offer_job_applications.offer_acceptance',
			'offer_job_applications.joined',
			'offer_job_applications.joined_date',
			'offer_job_applications.offer_amount',
			'qualification_master.name as qualification_name',
			'companies.company_name',
			'owncom.company_name as own_company_name',
			'state_list.state as state_name',
			'city_list.city as city_name',
			'recommendation_relation.job_id as rjobid',
			'recommendation_relation.from_company_id',
			'add_job_wise_condidates.id as wiseId',
			'documents.hashname'
		)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftJoin('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('recommendation_relation', function ($join) {
				$join->on('recommendation_relation.job_id', 'jobs.id');
			})
			// ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id') 
			->leftJoin('companies', function ($join) {
				$join->on('companies.id', 'add_job_wise_condidates.company_id');
			})
			->leftJoin('companies as owncom', function ($join) {
				$join->on('owncom.id', 'job_applications.company_id');
			})
			->leftJoin('offer_job_applications', function ($join) use ($id, $jobId) {
				$join->on('offer_job_applications.application_id', 'job_applications.id');
				$join->where('offer_job_applications.job_id', $jobId);
				// $join->where('offer_job_applications.company_id', $company_id);
			})
			// ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			//->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
			if($AddJobWiseCondidate){
				$this->application = $this->application->where('add_job_wise_condidates.id', $id);
			}else{
				$this->application = $this->application->where('job_applications.id', $id);
			}

			$this->section_change_value = OfferJobApplication::join('offer_create_main_tds','offer_create_main_tds.offer_id','=','offer_job_applications.id')->select('offer_create_main_tds.*','offer_job_applications.id as offer_id_main')->where('offer_job_applications.id', $this->offer_id)->where('offer_create_main_tds.status','=',1)->first();
            $this->section_other = "";
			$this->section_hourly = "";
			$this->first_text = "Annually";
			$this->secont_text = "Monthly";
			if($this->section_change_value){
				if($this->section_change_value->salary_mode==2 || $this->section_change_value->salary_mode==1 || $this->section_change_value->salary_mode==3){
					$this->section_hourly = "";
					$this->section_other = "d-none";
					if($this->section_change_value->salary_mode==1){
                         $this->secont_text = "Monthly";
					}elseif($this->section_change_value->salary_mode==2){
                         $this->secont_text = "Hourly";
					}elseif($this->section_change_value->salary_mode==3){
                         $this->secont_text = "Annually";
					}
				}else{
					$this->section_other = "";
					$this->section_hourly = "d-none";

					if($this->section_change_value->salary_mode==4){
                         $this->first_text = "Annually";
					}elseif($this->section_change_value->salary_mode==0){
                         $this->first_text = "Annually";
					}
				}
				$offerPermision = 65;
			}else{
				$this->section_other = "";
			    $this->section_hourly = "d-none";
				$offerPermision = 66;
			}

			// dd($this->text_change_value);

			$offerDetails = DB::table('offer_create_main_tds')
			->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
			->where('offer_job_applications.id', $this->offer_id)
			->where('offer_create_main_tds.status','=',1)
			->select(
					'offer_create_main_tds.offer_id',
					'offer_create_main_tds.employment_type',
					'offer_create_main_tds.id as of_main_td',
					'offer_job_applications.offer_currency as currency',
					'offer_job_applications.offer_amount as salary_ctc',
					'offer_job_applications.offer_mode as salary_mode',
					'offer_create_main_tds.designation',
					// The Compensation step's Joining Date field saves to offer_create_main_tds.joined_date
					// (not offer_job_applications.joined_date, which nothing keeps in sync) — reading from
					// the wrong table meant a joining date set on Compensation could show blank everywhere
					// this feeds (hero facts, approval info card, email body).
					'offer_create_main_tds.joined_date',
					'offer_job_applications.confidential',
					'offer_create_main_tds.monthly_gross',
					'offer_create_main_tds.yearly_gross',
					'offer_create_main_tds.monthly_bounus_gross',
					'offer_create_main_tds.yearly_bonus_gross',
					'offer_create_main_tds.monthly_insurence_gross',
					'offer_create_main_tds.yearly_insurence_gross',
					'offer_create_main_tds.yearly_final_gross',
					'offer_create_main_tds.monthly_final_gross'
    			)->get()
				->map(function($offer) {
					  // Fetch Basic offer points
        			$basicPoints = DB::table('basic_offer_points')
					->where('offer_create_main_td_id', $offer->of_main_td)
					->select(
					'id',
					'view_type',
					'poins as basic_poins',
					'Percentage as basic_percentage',
					'condition_s as basic_condition_s',
					'monthly as basic_monthly',
					'yearly as basic_yearly'
				)->get()->toArray();

                // Fetch Bonus offer points
				$bonusPoints = DB::table('bonus_offer_points')
				->where('offer_create_main_td_id', $offer->of_main_td)
				->select(
					'id',
					'view_type',
					'poins as bonus_poins',
					'Percentage as bonus_percentage',
					'condition_s as bonus_condition_s',
					'monthly as bonus_monthly',
					'yearly as bonus_yearly'
				)->get()->toArray();

				// Fetch Insurance offer points
			  $insurancePoints = DB::table('insurence_offer_points')
				->where('offer_create_main_td_id', $offer->of_main_td)
				->select(
					'id',
					'view_type',
					'poins as insurence_poins',
					'Percentage as insurence_percentage',
					'condition_s as insurence_condition_s',
					'monthly as insurence_monthly',
					'yearly as insurence_yearly'
				)->get()->toArray();
               // Merge all into the main offer array
				return [
					'offer_id' => $offer->offer_id,
					'employment_type' => $offer->employment_type,
					'of_main_td' => $offer->of_main_td,
					'currency' => $offer->currency,
					'confidential' => $offer->confidential,
					'salary_ctc' => $offer->salary_ctc,
					'salary_mode' => $offer->salary_mode,
					'designation' => $offer->designation,
					'joined_date' => $offer->joined_date,
					'monthly_gross' => $offer->monthly_gross,
					'yearly_gross' => $offer->yearly_gross,
					'monthly_bounus_gross' => $offer->monthly_bounus_gross,
					'yearly_bonus_gross' => $offer->yearly_bonus_gross,
					'monthly_insurence_gross' => $offer->monthly_insurence_gross,
					'yearly_insurence_gross' => $offer->yearly_insurence_gross,
					'yearly_final_gross' => $offer->yearly_final_gross,
					'monthly_final_gross' => $offer->monthly_final_gross,
					'Basic_offer_points' => $basicPoints,
					'Bonus_offer_points' => $bonusPoints,
					'Insurance_offer_points' => $insurancePoints
				];
			    });

            //  dd($offerDetails);
			 $this->OfferJobApplication = $offerDetails;

			 $this->offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id','=',$this->offer_id)->orderByDesc('id')->first();
			// dd($this->OfferJobApplication);
		// $this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
		 $this->application = $this->application->first();

		//  dd($this->application);



		//echo '<pre>'; print_r($this->application); exit;
		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}
		$this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $jobId)->where('job_application_id', $this->application->id)->get();

		

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $jobId;
		$this->job_id = $job_id;


		
		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);


        // dd(	$this->updateTimeLine);

		$this->child_status_name = 'N/A';
		
		$this->jobWiseCanId = $id;
		
        $offer_points = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',0)
			->where('percentage','>',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',0)
			->where('percentage','>',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_points->isEmpty()) {
				$offer_points = DB::table('offer_points')->select('offer_points.*')->where('percentage','>',0)->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other->isEmpty()) {
				$offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('percentage','>',0)->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',1)->orderByDesc('sorting')->get();
			}

			$offer_point_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',1)
			->where('percentage','>',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',1)
			->where('percentage','>',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_point_hourly->isEmpty()) {
				$offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',0)->where('percentage','>',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other_hourly->isEmpty()) {
				$offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',1)->where('percentage','>',0)->orderByDesc('sorting')->get();
			}



			$this->offer_points = $offer_points;
			$this->offer_point_other = $offer_point_other;

			$this->offer_point_hourly = $offer_point_hourly;
			$this->offer_point_other_hourly = $offer_point_other_hourly;

			$rowDataOffer = DB::table('offer_job_applications')
			->join('job_applications','job_applications.id','=','offer_job_applications.application_id')
			->select('offer_job_applications.*','job_applications.full_name')
			->where('offer_job_applications.id', $this->offer_id)->first();





		$this->account 				=	DB::table('companies')->select('*')->where('id', $rowDataOffer->company_id)->first();
		$this->global_settings 				=	DB::table('global_settings')->select('*')->first();
		$offer_terms = '';
		if($this->offer_terms_conditions){
			$offer_terms =  (@$this->offer_terms_conditions->terms_condition) ? $this->offer_terms_conditions->terms_condition : '';
		}else{
			$offer_terms =  (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms; 
			if($this->application->condidate_type=='bench_condidate'){
				$offer_terms =  (@$this->account->agency_offer_terms) ? $this->account->agency_offer_terms : @$this->global_settings->agency_offer_terms;
			}
		}
		$rowDataOfferDetails = GeneralMethod::offerLetterdetails($this->offer_id);
	
		
		$address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
		$comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
		$logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
		$job_title = (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->job_title : '';
		$candidate_name =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->full_name : '';
		$candidate_email =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->email : '';
		$candidate_mobile =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->mobile : '';
		$of_joined_date =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->of_joined_date : '';

		if(!empty($of_joined_date)){
                 $of_joined_date_update = $of_joined_date;
		}else{
				 $of_joined_date_update = "[JOINING_DATE]";
		}

		$replaceFrom = ["[CANDIDATE_NAME]","[CANDIDATE_EMAIL]","[CANDIDATE_MOBILE]","[COMPANY_NAME]","[JOB_TITLE]","[JOINING_DATE]","[{JOB_TITLE}]","[TODAY_DATE]"];
		$replaceTo   = [$candidate_name, $candidate_email, $candidate_mobile, $comapny_name, $job_title, $of_joined_date_update, $job_title, date("d M Y")];

		$this->offer_terms = str_replace($replaceFrom, $replaceTo, $offer_terms);

		// Resolve both templates with variables replaced so the view can switch between them
		$raw_company_terms = (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms;
		$raw_agency_terms  = (@$this->account->agency_offer_terms) ? $this->account->agency_offer_terms : @$this->global_settings->agency_offer_terms;
		$this->company_offer_terms = str_replace($replaceFrom, $replaceTo, (string)$raw_company_terms);
		$this->agency_offer_terms  = str_replace($replaceFrom, $replaceTo, (string)$raw_agency_terms);
		$this->is_bench_candidate  = ($this->application->condidate_type == 'bench_condidate');

		// Same values used in the merge above, exposed to the view so the Template step's
		// "Insert Variable" chips can insert this offer's actual resolved values.
		$this->co_candidate_name   = $candidate_name;
		$this->co_candidate_email  = $candidate_email;
		$this->co_candidate_mobile = $candidate_mobile;
		$this->co_company_name     = $comapny_name;
		$this->co_job_title        = $job_title;
		$this->co_joining_date     = $of_joined_date_update;
		$this->co_today_date       = date("d M Y");
		$this->co_company_logo     = $logo;
		$this->co_company_address  = $address;

		// Approval step (Step 4): $section_change_value already carries offer_create_main_tds.*
		// (management_status/send_approved_date_time/approved_date_time). The approver's own
		// name/email only lives on updatetimeline (written by fwd_offer_send()/saveSignature()),
		// so pull the latest approval-type row for this application, plus a short activity feed.
		// Fetch ALL approval emails sent for this offer (one row per approver/resend).
		// Left-join users_profile by email to get the approver's designation when available.
		$this->co_appr_timeline = DB::table('updatetimeline')
			->leftJoin('users', 'users.email', '=', 'updatetimeline.email_id_hr')
			->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
			->select('updatetimeline.*', 'users_profile.designation as approver_designation')
			->where('updatetimeline.application_id', $this->application->id)
			->where('updatetimeline.job_id', $this->application->job_id)
			->whereIn('updatetimeline.mtypeSection', [2, 8]) // 2 = legacy approval, 8 = new approval flow (mtypeId=8)
			->orderByDesc('updatetimeline.id')
			->get();

		$this->co_activity_log = DB::table('updatetimeline')
			->where('application_id', $this->application->id)
			->where('job_id', $this->application->job_id)
			->whereNotNull('mtypeSection')
			->orderByDesc('id')
			->limit(8)
			->get();

		// Step 5 hero: "Sent By" — mtypeSection 1 = candidate-facing offer email (fwd_offer_send,
		// mtypeId==1). name_hr/email_id_hr aren't populated for this event type, so the sender
		// comes from event_by_user_id instead. "Sent/Not Sent" itself reads
		// offer_create_main_tds.send_accept_date_time (already in $section_change_value).
		$this->co_send_timeline = DB::table('updatetimeline')
			->leftJoin('users', 'users.id', '=', 'updatetimeline.event_by_user_id')
			->where('updatetimeline.application_id', $this->application->id)
			->where('updatetimeline.job_id', $this->application->job_id)
			->where('updatetimeline.mtypeSection', 1)
			->orderByDesc('updatetimeline.id')
			->select('updatetimeline.*', 'users.name as sent_by_name')
			->first();

		// Step 5 "Template" dropdown — same ES-backed list offerSendMail() uses for mtype==1
		// (candidate offer email): temptype 7. Selecting one calls the existing
		// service_company.cvtracker.email_template endpoint (unchanged) to fill Subject/Body.
		$co_send_tpl_params = [
			'index' => ELASTIC_EMAIL_TEMPLATE,
			'body'  => [
				'size' => 10000,
				'query' => [
					'bool' => [
						'must' => [
							['term'  => ['status' => 1]],
							['match' => ['temptype' => 7]],
						],
					],
				],
			],
		];
		$co_send_tpl_response = $this->elasticService->getClient()->search($co_send_tpl_params);
		$this->co_send_templates = collect($co_send_tpl_response['hits']['hits'] ?? [])
			->map(function ($hit) { return (object) $hit['_source']; });

		// Step 5 "Add Email" picker: internal team members (active users in this company) and,
		// when the job belongs to an external client, that client's contacts. Same directories
		// the approval composer uses, exposed here so the native Send Offer Letter page can
		// offer a pick-from-team drawer without an AJAX round-trip.
		$this->co_send_internal = DB::table('users')
			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->select('users.id', 'users.name', 'users.email', 'users_profile.designation as display_name', 'companies.company_name')
			->where('users.company_id', '=', auth()->user()->company_id)
			->where('users.status', '=', 'active')
			->whereNotNull('users.email')
			->groupBy('users.id')->get()->toArray();

		$this->co_send_client = [];
		$co_job_client = DB::table('jobs')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->select('clients.id as client_id', 'clients.self_company')
			->where('jobs.id', $this->application->job_id)->first();
		if ($co_job_client && (int) @$co_job_client->self_company === 0 && @$co_job_client->client_id) {
			$this->co_send_client = DB::table('client_contact_info')
				->leftjoin('clients', 'clients.id', 'client_contact_info.client_id')
				->select('client_contact_info.id', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
				->where('client_contact_info.client_id', $co_job_client->client_id)
				->whereNotNull('client_contact_info.contact_person_email')->get()->toArray();
		}

		// dd($this->offer_points);
		$view = view('service_company.job-applications.create-offer', $this->data)->render();
		$permisiionCheck  = GeneralMethod::permissioncheck($offerPermision,auth()->user()->id);
		if($permisiionCheck){
			
			$offerPermisiontext = 'You are not authorized to access these features. Please contact your administrator for assistance.';
			// dd($permisiionCheck);
			return Reply::dataOnly(['status' => 'permisionInfo', 'view' => $offerPermisiontext]);
		}

		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

            
	}
	public function saveCreateOffer(Request $request){
	
		$offer_id = $request->offer_id;
		$yearly_gross = 0;
		$yearly_final_gross = 0;
		$this->section_change_value = OfferJobApplication::join('offer_create_main_tds','offer_create_main_tds.offer_id','=','offer_job_applications.id')->select('offer_create_main_tds.*','offer_job_applications.id as offer_id_main')->where('offer_job_applications.id', $offer_id)->where('offer_create_main_tds.status','=',1)->first();
		if($this->section_change_value){
			$offerPermision = 64;
		}else{
			$offerPermision = 66;
		}
		$employment_type = GeneralMethod::employmentType();
		$of_employment_type = $employment_type[$request->employment_type];
		//dd($of_employment_type,$request->all());
		
		$permisiionCheck  = GeneralMethod::permissioncheck($offerPermision,auth()->user()->id);
		if($permisiionCheck){
			// dd($permisiionCheck);
			$offerPermisiontext = 'You are not authorized to access these features. Please contact your administrator for assistance.';
			return response()->json(['status'=>'permisionInfo','view' => $offerPermisiontext]);
		}
	
	
		if($request->current_ctc_mode==2 || $request->current_ctc_mode==1 || $request->current_ctc_mode==3){
			$validator = Validator::make($request->all(), [
				'offer_point_hourly.*' => 'sometimes|required',
				'option_percentage_hourly.*' => 'sometimes|required',
				'option_salarymode_hourly.*' => 'sometimes|required',
				'offer_hourly.*' => 'sometimes|required',
				'bonus_point_hourly.*' => 'sometimes|required',
				'bonus_option_percentage_hourly.*' => 'sometimes|required',
				'bonus_option_salarymode_hourly.*' => 'sometimes|required',
				'bonus_offer_hourly.*' => 'sometimes|required',
				'insurence_point_hourly.*' => 'sometimes|required',
				'insurence_option_percentage_hourly.*' => 'sometimes|required',
				'insurence_option_salarymode_hourly.*' => 'sometimes|required',
				'insurence_offer_hourly.*' => 'sometimes|required',
				'finalpaytotalhourly' => 'required',
				'ctc' => 'required',
				'dob' => 'required',
				'current_designation' => 'required'
			],[
				'offer_point_hourly.*.required' => 'Offer point is required.',
				'option_percentage_hourly.*.required' => 'Option percentage is required.',
				'option_salarymode_hourly.*.required' => 'Option salary mode is required.',
				'offer_hourly.*.required' => 'Offer hourly is required.',
				
				'bonus_point_hourly.*.required' => 'Bonus point is required.',
				'bonus_option_percentage_hourly.*.required' => 'Bonus option percentage is required.',
				'bonus_option_salarymode_hourly.*.required' => 'Bonus option salary mode is required.',
				'bonus_offer_hourly.*.required' => 'Bonus offer hourly is required.',
			
				'insurence_point_hourly.*.required' => 'Insurance point is required.',
				'insurence_option_percentage_hourly.*.required' => 'Insurance option percentage is required.',
				'insurence_option_salarymode_hourly.*.required' => 'Insurance option salary mode is required.',
				'insurence_offer_hourly.*.required' => 'Insurance offer hourly is required.',
			
				'finalpaytotalhourly.required' => 'The final total hourly payment is required.',
				'ctc.required' => 'The CTC is required.',
				'dob.required' => 'The join date is required.',
				'current_designation.required' => 'The designation is required.'
			]);
		}else{

		
		$validator = Validator::make($request->all(), [
			'offer_point.*' => 'sometimes|required',
			'option_percentage.*' => 'sometimes|required',
			'option_salarymode.*' => 'sometimes|required',
			'offer_monthly.*' => 'sometimes|required',
			'offer_yearly.*' => 'sometimes|required',
			'bonus_points.*' => 'sometimes|required',
			'bonus_option_percentage.*' => 'sometimes|required',
			'bonus_option_salarymode.*' => 'sometimes|required',
			'bonus_offer_monthly.*' => 'sometimes|required',
			'bonus_offer_yearly.*' => 'sometimes|required',
			'insurence_points.*' => 'sometimes|required',
			'insurence_option_percentage.*' => 'sometimes|required',
			'insurence_option_salarymode.*' => 'sometimes|required',
			'insurence_offer_monthly.*' => 'sometimes|required',
			'insurence_offer_yearly.*' => 'sometimes|required',
			'finalpaytotalMonthly' => 'required',
			'finalpaytotalYearly' => 'required',
			'ctc' => 'required',
			'dob' => 'required',
			'current_designation' => 'required'
		],[
			'offer_point.*.required' => 'Offer point is required.',
			'option_percentage.*.required' => 'Option percentage is required.',
			'option_salarymode.*.required' => 'Option salary mode is required.',
			'offer_monthly.*.required' => 'Offer monthly is required.',
			'offer_yearly.*.required' => 'Offer yearly is required.',
			
			'bonus_points.*.required' => 'Bonus point is required.',
			'bonus_option_percentage.*.required' => 'Bonus option percentage is required.',
			'bonus_option_salarymode.*.required' => 'Bonus option salary mode is required.',
			'bonus_offer_monthly.*.required' => 'Bonus offer monthly is required.',
			'bonus_offer_yearly.*.required' => 'Bonus offer yearly is required.',
		
			'insurence_points.*.required' => 'Insurance point is required.',
			'insurence_option_percentage.*.required' => 'Insurance option percentage is required.',
			'insurence_option_salarymode.*.required' => 'Insurance option salary mode is required.',
			'insurence_offer_monthly.*.required' => 'Insurance offer monthly is required.',
			'insurence_offer_yearly.*.required' => 'Insurance offer yearly is required.',
		
			'finalpaytotalMonthly.required' => 'The final total monthly payment is required.',
			'finalpaytotalYearly.required' => 'The final total yearly payment is required.',
			'ctc.required' => 'The CTC is required.',
			'dob.required' => 'The join date is required.',
			'current_designation.required' => 'The designation is required.'
		]);
	  }
	    
	     
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
		 	], 422);
		}
		
		if($request->current_ctc_mode==2){
		      $totalSum = array_sum($request->option_percentage_hourly);
		}else{
			$totalSum = array_sum($request->option_percentage);
		}
		// dd($totalSum);
		if($totalSum != 100){
			  return response()->json(['status'=>'errors','message' => 'Please check Percentage and complete 100%']);
		}

		try {
			// Start the transaction
			DB::beginTransaction();

			$exits_offer = DB::table('offer_create_main_tds')->where('offer_id','=',$offer_id)->where('status','=',1)->orderByDesc('id')->first();

			if(!DB::table('offer_create_main_tds')->where('offer_id','=',$offer_id)->exists()){
					$offer_job_applications = DB::table('offer_job_applications')
					->select('joined as wiseId')
					->where('id', $offer_id)
					->first();
				$AddJobWiseCondidate = AddJobWiseCondidate::find($offer_job_applications->wiseId);

						$spok_user_id_rows = DB::table('job_assign')
						->where('job_id', $AddJobWiseCondidate->job_id)
						->get(['spok_user_id', 'company_id'])->toArray();

						// Step 2: Build result as [company_id => spok_user_id]
						$teamCaptainId = [];

						foreach ($spok_user_id_rows as $row) {
							if (
								!is_null($row->spok_user_id) &&
								$row->spok_user_id != $AddJobWiseCondidate->submitted_by
							) {
								$teamCaptainId[$row->company_id][] = $row->spok_user_id;
							}
						}

						// Step 3: Remove duplicates and reindex inner arrays
						foreach ($teamCaptainId as $companyId => $spokIds) {
							$teamCaptainId[$companyId] = array_values(array_unique($spokIds));
						}

						
						$join_update_date_check = date("Y-m-d H:i:s", strtotime($request->dob));
						$join_update_date_check = Carbon::parse($join_update_date_check);
						$today = Carbon::today();
						$diffInDays = abs($today->diffInDays($join_update_date_check));

						// Every offer release counts (no 30-day window), so offers always reconcile with joins
						// (which are ungated). Product decision 2026-06-30 — fix applies going forward.
						$offerReleasedUser = 300;
						$offerReleasedTeam = 20;

						$teamCaptainId = array_map(function ($arr) {
							return array_values(array_filter($arr, function ($value) {
								return $value != 0;
							}));
						}, $teamCaptainId);
						
						if(count($teamCaptainId) > 0){

							foreach($teamCaptainId as $comId => $captainId){
								$teamRankElastic = [
									'company_id'=>$comId,
									'user_id'=>$captainId[0],
									'candidate_id'=>$AddJobWiseCondidate->id,
									'profileUpload'=>0,
									'interviewScheduled'=>0,
									'interviewNoShow'=>0,
									'offerReleased'=>0,
									'candidateJoined'=>0,
									'jobsPosted'=>0,
									'feedbackGivenOnCVs'=>0,
									'postJobToVendorMarket'=>0,
									'outsourceJobsToVendors'=>0,
									'listBenchCandidates'=>0,
									'respondToBenchRequests'=>0,
									'interestedCandidates'=>0,
									'notInterestedCandidates'=>0,
									'teamCaptainInterviewScheduled'=>0,
									'teamCaptainInterviewNoShow'=>0,
									'teamCaptainOfferReleased'=>$offerReleasedTeam,
									'teamCaptainCandidateJoined'=>0
								];
								GeneralMethod::teamRankElastic($teamRankElastic);
							}

						}



						$teamRankElastic = [
						'company_id'=>$AddJobWiseCondidate->company_id,
						'user_id'=>$AddJobWiseCondidate->submitted_by,
						'candidate_id'=>$AddJobWiseCondidate->id,
						'profileUpload'=>0,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>$offerReleasedUser,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>0,
						'respondToBenchRequests'=>0
					];
					GeneralMethod::teamRankElastic($teamRankElastic);
			}

			if($exits_offer){
				DB::table('offer_create_main_tds')->where('offer_id', $offer_id)->where('status','=',1)->update(['status'=>2]);
			}

			
			    $eventId = 17;
				$coinsRequ = 1;
				$companyId = (int) auth()->user()->company_id;

				$packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
				if($packageStatus->status){
					return response()->json(['status'=>'errors','message' =>  $packageStatus->message]);
				}
				
				$coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');  
				if($coinsDeductions->status){
					return response()->json(['status'=>'errors','message' =>  $coinsDeductions->message]);
				}

	
			if($request->current_ctc_mode==2 || $request->current_ctc_mode==1 || $request->current_ctc_mode==3){

				// Prepare the main offer data
				
				$otherPoint = 0;
				if (isset($request->bonus_point_hourly) && is_array($request->bonus_point_hourly) && count($request->bonus_point_hourly) > 0) {
					$otherPoint = count($request->bonus_point_hourly);
				}
				
			$offer_create_main_tds = array(
				'offer_id' => $offer_id,
				'currency' => $request->current_ctc_cur,
				'employment_type' => $request->employment_type,
				'salary_ctc' => $request->ctc,
				'salary_mode' => $request->current_ctc_mode,
				'designation' => $request->current_designation,
				'joined_date' => date("Y-m-d H:i:s", strtotime($request->dob)),
				'joined' => '',
				'yearly_gross' => $request->totalhourlyhourly,
				'yearly_bonus_gross' => $request->totalhourlyybonus,
				'yearly_insurence_gross' => $request->totalYearlyinsurencehourly,
				'yearly_final_gross' =>  ($otherPoint) ? $request->finalpaytotalhourly : $request->totalhourlyhourly
			);
			// $exits_offer_sign = DB::table('offer_create_main_tds')->where('offer_id','=',$offer_id)->where('signature_candidate','=',1)->orderByDesc('id')->first();
			// if($exits_offer_sign){
			// 	$offer_create_main_tds['signature_candidate'] = ($exits_offer->signature_candidate) ? $exits_offer->signature_candidate : '';
			// 	$offer_create_main_tds['accept_date_time'] = ($exits_offer->accept_date_time) ? $exits_offer->accept_date_time : '';
			// 	$offer_create_main_tds['approved_date_time'] = ($exits_offer->approved_date_time) ? $exits_offer->approved_date_time : '';
			// 	$offer_create_main_tds['management_status'] = ($exits_offer->management_status) ? $exits_offer->management_status : '';
			// }
			$yearly_gross = $request->totalhourlyhourly;
			$yearly_final_gross = ($otherPoint) ? $request->finalpaytotalhourly :  $request->totalhourlyhourly;
			// Insert the main offer data and get the ID
			$offer_create_main_td_id = DB::table('offer_create_main_tds')->insertGetId($offer_create_main_tds);
		
			// Check if offer points exist and insert them
			if (!empty($request->offer_point_hourly)) {
				foreach ($request->offer_point_hourly as $bp => $point) {
					$basic_offer_points = array(
						'poins' => $point,
						'Percentage' => $request->option_percentage_hourly[$bp],
						'condition_s' => $request->option_salarymode_hourly[$bp],
						'yearly' => $request->offer_hourly[$bp],
						'offer_create_main_td_id' => $offer_create_main_td_id,
						'offer_id' => $offer_id
					);
					DB::table('basic_offer_points')->insert($basic_offer_points);
				}
			}
		
			// Check if insurance points exist and insert them
			if (!empty($request->insurence_point_hourly)) {
				foreach ($request->insurence_point_hourly as $bp => $point) {
					$insurence_offer_points = array(
						'poins' => $point,
						'Percentage' => $request->insurence_option_percentage_hourly[$bp],
						'condition_s' => $request->insurence_option_salarymode_hourly[$bp],
						'yearly' => $request->insurence_offer_hourly[$bp],
						'offer_create_main_td_id' => $offer_create_main_td_id,
						'offer_id' => $offer_id
					);
					DB::table('insurence_offer_points')->insert($insurence_offer_points);
				}
			}
		
			// Check if bonus points exist and insert them
			if (!empty($request->bonus_point_hourly)) {
				foreach ($request->bonus_point_hourly as $bp => $point) {
					$bonus_offer_points = array(
						'poins' => $point,
						'Percentage' => $request->bonus_option_percentage_hourly[$bp],
						'condition_s' => $request->bonus_option_salarymode_hourly[$bp],
						'yearly' => $request->bonus_offer_hourly[$bp],
						'offer_create_main_td_id' => $offer_create_main_td_id,
						'offer_id' => $offer_id
					);
					DB::table('bonus_offer_points')->insert($bonus_offer_points);
				}
			}

			}else{
				$otherPoint = 0;
				if (!empty($request->bonus_points)) {
				   $otherPoint = count($request->bonus_points); 
				}

				// Prepare the main offer data
				$offer_create_main_tds = array(
					'offer_id' => $offer_id,
					'currency' => $request->current_ctc_cur,
					'employment_type' => $request->employment_type,
					'salary_ctc' => $request->ctc,
					'salary_mode' => $request->current_ctc_mode,
					'designation' => $request->current_designation,
					'joined_date' => date("Y-m-d H:i:s", strtotime($request->dob)),
					'joined' => '',
					'monthly_gross' => $request->totalMonthly,
					'yearly_gross' => $request->totalYearly,
					'monthly_bounus_gross' => $request->totalMonthlybonus,
					'yearly_bonus_gross' => $request->totalYearlybonus,
					'monthly_insurence_gross' => $request->totalMonthlyinsurence,
					'yearly_insurence_gross' => $request->totalYearlyinsurence,
					'monthly_final_gross' => $request->finalpaytotalMonthly,
					'yearly_final_gross' => ($otherPoint) ? $request->finalpaytotalYearly : $request->totalYearly
				);
				$yearly_gross = $request->totalYearly;
				$yearly_final_gross = ($otherPoint) ? $request->finalpaytotalYearly : $request->totalYearly;
				// Insert the main offer data and get the ID
				// dd($offer_create_main_tds);
				$offer_create_main_td_id = DB::table('offer_create_main_tds')->insertGetId($offer_create_main_tds);
				// dd($offer_create_main_tds);
			
				// Check if offer points exist and insert them
				if (!empty($request->offer_point)) {
					foreach ($request->offer_point as $bp => $point) {
						$basic_offer_points = array(
							'poins' => $point,
							'Percentage' => $request->option_percentage[$bp],
							'condition_s' => $request->option_salarymode[$bp],
							'monthly' => $request->offer_monthly[$bp],
							'yearly' => $request->offer_yearly[$bp],
							'view_type' => $request->basic_view[$bp],
							'offer_create_main_td_id' => $offer_create_main_td_id,
							'offer_id' => $offer_id
						);
						DB::table('basic_offer_points')->insert($basic_offer_points);
					}
				}
			
				// Check if insurance points exist and insert them
				if (!empty($request->insurence_points)) {
					foreach ($request->insurence_points as $bp => $point) {
						$insurence_offer_points = array(
							'poins' => $point,
							'Percentage' => $request->insurence_option_percentage[$bp],
							'condition_s' => $request->insurence_option_salarymode[$bp],
							'monthly' => $request->insurence_offer_monthly[$bp],
							'yearly' => $request->insurence_offer_yearly[$bp],
							'view_type' => $request->insurence_view[$bp],
							'offer_create_main_td_id' => $offer_create_main_td_id,
							'offer_id' => $offer_id
						);
						DB::table('insurence_offer_points')->insert($insurence_offer_points);
					}
				}
			
				// Check if bonus points exist and insert them
				if (!empty($request->bonus_points)) {
					foreach ($request->bonus_points as $bp => $point) {
						$bonus_offer_points = array(
							'poins' => $point,
							'Percentage' => $request->bonus_option_percentage[$bp],
							'condition_s' => $request->bonus_option_salarymode[$bp],
							'monthly' => $request->bonus_offer_monthly[$bp],
							'yearly' => $request->bonus_offer_yearly[$bp],
							'view_type' => $request->bonus_view[$bp],
							'offer_create_main_td_id' => $offer_create_main_td_id,
							'offer_id' => $offer_id
						);
						DB::table('bonus_offer_points')->insert($bonus_offer_points);
					}
				}

		    }


			$Offertimelime = OfferJobApplication::find($offer_id);

			$option_currency_val = @$request->current_ctc_cur;
			$joined_date = @$request->dob ?  date("Y-m-d H:i:s", strtotime(@$request->dob)) : 'NULL';
			$ctc = @$request->ctc;
			$current_ctc_mode = @$request->current_ctc_mode;
            $confidential = @$request->confidential;
			$Offertimelime->confidential = @$confidential;
			$Offertimelime->offer_currency=  @$option_currency_val ? @$option_currency_val : 'NULL'; 
			$Offertimelime->offer_mode=  @$current_ctc_mode ? @$current_ctc_mode : 'NULL';
			$Offertimelime->offer_amount=  @$yearly_final_gross ? @$yearly_final_gross : 'NULL' ;
			$Offertimelime->joined_date=  @$joined_date ?  $joined_date : 'NULL'; 
			$Offertimelime->save();


			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES,
				'id' => $Offertimelime->joined
			]);	
			
			// dd($Offertimelime->joined,$joined_date,$option_currency_val,$current_ctc_mode,$yearly_final_gross);
			if ($exists) {

			
				$updateTime = now()->toIso8601String();
				$updateParams_e = [
					'index' => ELASTIC_CANDIDATES,
					'id' => $Offertimelime->joined,
					'body' => [
						'doc' => [
							'of_joined_date' => (!empty( @$joined_date) &&  @$joined_date != '0000-00-00 00:00:00') ? Carbon::parse( @$joined_date)->toIso8601String() : null,
							'of_offer_currency' =>(int)@$option_currency_val,
							'of_offer_mode' => (int)@$current_ctc_mode,
							'of_offer_amount' => @$yearly_final_gross ? @$yearly_final_gross : 'NULL',
							'of_offer_acceptance' => 0,
							'of_employment_type' => $of_employment_type,
							'of_created_date' => $updateTime,
							'confidential' => $confidential,
							'updated_at' => $updateTime
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
				// dd($updateParams);
				
				$redudds = $this->elasticService->getClient()->update($updateParams_e);
				
				if(@$Offertimelime->application_id){
					GeneralMethod::elasticBenchCandidateAddEditIndex($Offertimelime->application_id);
				}
				
				// dd($redudds);

			}
			

			$date = date('Y-m-d H:i:s');

			$values = array(
				'event_id' => 4,
				'event_by_company_id' => auth()->user()->company_id,
				'event_by_user_id' => auth()->user()->id,

				'application_id' => @$Offertimelime->application_id ? @$Offertimelime->application_id : NULL,
				'application_email' => @$Offertimelime->email ? @$Offertimelime->email : NULL,
				'job_id' => @$Offertimelime->job_id ? @$Offertimelime->job_id : NULL,
				'note' => '',
				'create_at' => @$date ? @$date : NULL,
				'update_at' => @$date ? @$date : NULL,
			);
			$jobApplication = AddJobWiseCondidate::find($Offertimelime->joined);
			if (@$jobApplication->master_status_id > 0) {
				$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
				$values['master_process_name'] = @$masterStatus->name;
			}
			if (@$jobApplication->child_status_id > 0) {
				$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
				$values['child_process_name'] = @$masterStatus->name;
			}


			$values['master_process_name'] = @$masterStatus->name;
			$values['child_process_name'] = @$masterStatus->name;
			$values['offer_currency'] = @$option_currency_val;
			$values['offer_mode'] = @$current_ctc_mode;
			$values['offer_amount'] = @$ctc;
			$values['joined_date'] = @$joined_date;
			$values['confidential'] = @$confidential;


			$values['offer_currency'] = @$option_currency_val ? @$option_currency_val : NULL;
			$values['offer_mode'] = @$current_ctc_mode ? @$current_ctc_mode : NULL;
			$values['offer_amount'] = @$ctc ? @$ctc : NULL ;
			$values['joined_date'] = @$request->dob ?  $joined_date : NULL;

			$values['candidate_status'] = 0;
			$values['management_status'] = 0;
			$values['accept_date_time'] = '';
			$values['signature_candidate'] ='';
			$values['email_id_hr'] = '';
			$values['name_hr'] = '';
			$values['yearly_gross'] = @$yearly_gross ?  $yearly_gross : '';
			$values['yearly_final_gross'] = @$yearly_final_gross ? $yearly_final_gross : '';
			$values['mtypeSection'] = 3;
			$values['designation_offer'] = @$request->current_designation;

			GeneralMethod::UpdateTimeline($values);    
			
			
			 $this->offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id','=',$offer_id)->orderByDesc('id')->first();

			 $rowDataOffer = DB::table('offer_job_applications')
			->join('job_applications','job_applications.id','=','offer_job_applications.application_id')
			->select('offer_job_applications.*','job_applications.full_name')
			->where('offer_job_applications.id', $offer_id)->first();
			// dd($rowDataOffer);

			$this->account 	=	DB::table('companies')->select('*')->where('id', auth()->user()->company_id)->first();
			$this->global_settings 				=	DB::table('global_settings')->select('*')->first();
			$offer_terms = '';
			
				$offer_terms =  (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms; 

				$rowDataOfferDetails = GeneralMethod::offerLetterdetails($offer_id);
			
				$address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
				$comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
				$logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
				$job_title = (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->job_title : '';
				$candidate_name =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->full_name : '';
				$candidate_email =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->email : '';
				$candidate_mobile =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->mobile : '';
				$of_joined_date =  (@$rowDataOfferDetails) ?  @$rowDataOfferDetails->of_joined_date : '';
				if(!empty($of_joined_date)){
                 $of_joined_date_update = $of_joined_date;
				}else{
						$of_joined_date_update = "[JOINING_DATE]";
				}

		
			
			$this->offer_terms = str_replace(["[CANDIDATE_NAME]","[CANDIDATE_EMAIL]","[CANDIDATE_MOBILE]","[COMPANY_NAME]","[JOB_TITLE]","[JOINING_DATE]","[{JOB_TITLE}]","[TODAY_DATE]"],[$candidate_name, $candidate_email,$candidate_mobile,$comapny_name,$job_title,$of_joined_date_update,$job_title,date("d M Y")], $offer_terms);

			
			// Commit the transaction if all operations are successful
			DB::commit();
			
			// Return a success response
		
			$cachekeys = [
					'allapptracker_version_user_'.$this->user->id,
					'allcvtracker_version_user_'.$this->user->id,
					'candidateDetails_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}


			return response()->json(['status'=>'success','terms' => $this->offer_terms,'message' => 'Offer Created successfully.']);
		
		} catch (\Exception $e) {
			// Rollback the transaction if any operation fails
			DB::rollBack();
		
			// Log the exception message (optional)
			\Log::error('Transaction failed: ' . $e->getMessage());
			// dd($e->getMessage());
		
			// Return an error response
			return response()->json(['status'=>'failed','message' => 'Failed to save data. Please try again.'], 500);
		}

	}
	public function termsSaveCreateOffer(Request $request){

		// dd($request->all());
		$validator = Validator::make($request->all(), [
			'editor1' => 'sometimes|required'
		],[
			'editor1.*.required' => 'Offer point is required.'
		]);
		if ($validator->fails()) {
			// dd($validator->errors(),'hi');
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
		 	], 422);
		}

		$offer_create_main_tds = array(
			'offer_id' => $request->offerId,
			'terms_condition' => $request->editor1
		);
		// Insert the main offer data and get the ID
		$offer_terms_conditions = DB::table('offer_terms_conditions')->insertGetId($offer_create_main_tds);
        if($offer_terms_conditions>0){
			return response()->json(['status'=>'success','message' => 'Offer Terms Created successfully.']);
		}else{
			return response()->json(['status'=>'failed','message' => 'Failed to save data. Please try again']);
		}
		


	}
	// Function to convert currency to INR
	public function update(Request $request, $id)
	{
		// echo '<pre>'; print_r($_FILES); exit;
		// die;

		// dd(auth()->user()->company_id);

		// dd($request->all());

		// Authorization: a candidate profile may only be edited by the assigned Captain
		// (job_assign.spok_user_id) or the submitting user (add_job_wise_condidates.submitted_by),
		// and only within the same company (tenancy).
		$authUserId = auth()->id();
		$authCompanyId = auth()->user()->company_id;
		$canEditCandidate = DB::table('add_job_wise_condidates as ajwc')
			->leftJoin('job_assign as ja', function ($join) {
				$join->on('ja.job_id', '=', 'ajwc.job_id')
					->on('ja.company_id', '=', 'ajwc.company_id');
			})
			->where('ajwc.job_application_id', $id)
			->where('ajwc.company_id', $authCompanyId)
			->where(function ($query) use ($authUserId) {
				$query->where('ja.spok_user_id', $authUserId)
					->orWhere('ajwc.submitted_by', $authUserId);
			})
			->exists();

		if (!$canEditCandidate) {
			return Reply::dataOnly([
				'status'  => 'notauthorized',
				'msgType' => 'warning',
				'message' => "You are not authorized to edit this candidate's profile. Only the assigned Captain or the submitting user can edit it"
			]);
		}

		 $validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email:rfc,dns',
				'disposable_email'
			]
		], [
			'email.required' => 'Email is required',
			'email.email' => 'Please enter a valid email',
			'email.disposable_email' => 'Temporary emails are not allowed'
		]);

		if ($validator->fails()) {
			return Reply::dataOnly(['status' => 'mailcheck', 'msgType'=> 'warning', 'message' => $validator->errors()->first()]);	
		}
		

		$company_id = auth()->user()->company_id;

		// $education = request('kt_tagify_education');
		// $custom_education = json_decode($education, true);

		// dd($custom_education);
		// $education = array();
		// if (\request('kt_tagify_education') != "") {
		// 	foreach ($custom_education as $sval) {
		// 		$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
		// 		if ($education_data) {
		// 			$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
		// 			$education = array_merge($education, $educationfiletr);
		// 		}
		// 	}
		// }

		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		$requiredSkills =  [];
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				array_push($requiredSkills, $sval['value']);
			}
		}

    



		abort_if(Gate::denies('edit_job_applications'), 403);


		

		$jobApplication = JobApplication::with(['documents'])->findOrFail($id);
		// dd($jobApplication,$id);
		$jobApplication->full_name = ucwords(strtolower($request->full_name));
		if (!empty($request->current_designation)) {
			$jobApplication->current_designation = @$request->current_designation;
		}
		if (!empty($request->current_company)) {
			$jobApplication->current_company = @$request->current_company;
		}
		$jobApplication->phone = $request->phone;
		$jobApplication->secondary_mobile = $request->secondary_mobile;
		if ($request->has('dob')) {
			$jobApplication->dob = $request->dob;
		}
		$jobApplication->email = $request->email;
		$jobApplication->secondrary_email = $request->secondrary_email;

		if (!empty($request->qualification)) {
			$jobApplication->qualification = @$request->qualification;
		}

		$jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
		$jobApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

		// $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;


		$total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '')  ? $request->total_exp_year . '.' . $request->total_exp_month : null;

		$rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '')  ? $request->rel_exp_year . '.' . $request->rel_exp_month : null;

        // dd($total_exp,$rel_exp,$id);

		// dd( "hu");



		// $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

		// dd($total_exp,$rel_exp,$request->total_exp_year);


		// if (!empty($request->total_exp)) {
			$jobApplication->total_exp = $total_exp;
		// }
		// if (!empty($request->rel_exp)) {
			$jobApplication->rel_exp = $rel_exp;
		// }

		$jobApplication->gender = $request->gender;


		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			// if (!empty($request->calender)) {
			//    $jobApplication->np_id 				= 'SNP-LWD-' . $request->calender;
			//} else {
			$np_id_date = $request->np_id;
            if(!empty($request->np_id_display)){
				$np_id_date = ($request->np_id) ? $request->np_id : (Carbon::createFromFormat('d/M/Y', $request->np_id_display, 'UTC')->format('Y-m-d') ?? null);
			}
			

			$jobApplication->np_id = $np_id_date;
		
			//}
		} else if ($serving_notice_period == 2) {
			$jobApplication->np_id = 'Immediate';
			$np_id_date = $request->np_id;
			if(!empty($request->np_id_display)){
				$np_id_date = ($request->np_id) ? $request->np_id : (Carbon::createFromFormat('d/M/Y', $request->np_id_display, 'UTC')->format('Y-m-d') ?? null);
			}
			$jobApplication->last_working_date = $np_id_date;

		} else {
			$jobApplication->np_id = $request->ddl_np_id;
		}
		$jobApplication->serving_notice_period_mode = $request->serving_notice_period;
		$jobApplication->current_ctc_cur = $request->current_ctc_cur;
		if (!empty($request->ctc)) {
			$jobApplication->ctc = @$request->ctc;
		}
		$jobApplication->current_ctc_mode = $request->current_ctc_mode;
		$jobApplication->current_ectc_cur = $request->current_ectc_cur;
		if (!empty($request->ectc)) {
			$jobApplication->ectc = @$request->ectc;
		}
		$jobApplication->current_ectc_mode = @$request->current_ectc_mode;


		$currency = GeneralMethod::currency();  // Currency code

		$ecurrency  = $currency[$request->current_ectc_cur];
		$econvertedAmount = $request->bill_annual;
		if($request->current_ectc_cur > 0){
			$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
		}

		$ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
		$jobApplication->ectc_yearly = @$ectcdivided->yearly;
		$jobApplication->ectc_monthly = @$ectcdivided->monthly;
		$jobApplication->ectc_hourly = @$ectcdivided->hourly;

		$currency  = $currency[$request->current_ctc_cur];
		// dd($currency);
		$convertedAmount = $request->pay_annual;
		if($request->current_ctc_cur > 0){
			$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
		}

		$ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);
		$jobApplication->ctc_yearly = @$ctcdivided->yearly;
		$jobApplication->ctc_monthly = @$ctcdivided->monthly;
		$jobApplication->ctc_hourly = @$ctcdivided->hourly;


		$jobApplication->percentage_hike = @$request->percentage_hike;
		$jobApplication->comment = @$request->comment;
		$jobApplication->notice_comments = @$request->notice_comments;

		
		$jobApplication->careersummary = $request->careersummary;
		$jobApplication->nationality = $request->nationality;
		$jobApplication->wm = $request->wm;
		$jobApplication->employmentType = $request->employmentType;
		$jobApplication->oh = $request->oh;
		$jobApplication->visastatu = $request->visastatu;
		$jobApplication->referencename = $request->referencename;
		$jobApplication->reason_for_job_change = $request->reason_for_job_change;


		$stateId = '';
		$cityID = '';
		$countryID = '';
		$location_find = '';
		$work_city = request('kt_tagify_work_location');
    	$work_city_all = json_decode($work_city, true);
		if (\request('kt_tagify_work_location') != "") {
			foreach ($work_city_all as $cityVal) {
				$parts = explode('|', $cityVal['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$stateName = trim($parts[1]);
				$countrName = trim($parts[2]);
				$location_find = $cityName.'_'.$stateName.'_'.$countrName;

				// dd($cityName,$location_find);
				 // Debugging line
				 $city_row = DB::table('city_list')
				 ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
				 ->join('countries', 'countries.id', '=', 'state_list.country_id')
				 ->select('city_list.city_id', 'countries.id as country_id')
				 ->where('city_list.city','!=','')
				 ->where('city_list.city', '=', $cityName)
				 ->where('countries.country_name', '=', $countrName)
				 ->first();

				if($city_row){
					$cityID = (@$city_row) ? $city_row->city_id : '';
					$countryID = (@$city_row) ? $city_row->country_id : '';
					$stateId = GeneralMethod::stateId($cityID);
					// dd($stateId,$countryID);
				}
				
			}
		}

		
		$jobApplication->country = $countryID;
		$jobApplication->state = $stateId;

		if ($jobApplication->country == 101) {
			$jobApplication->city_id = $cityID;
		} else {
			$jobApplication->city_id = $cityID;
		}
        $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
		$jobApplication->pincode = $experienceGet;//(@$request->pincode) ? @$request->pincode : '';

		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
       if (!empty($skillArr)) {
			$requiredSkills = array_column($skillArr, 'value');
			GeneralMethod::skillsAddElastic($requiredSkills);
	   }

		if (!empty($skillArr)) {
			if(DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->exists()){
			     DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->delete();
			}
			foreach ($skillArr as $sval) {

				$values = array(
					'job_application_id' => $jobApplication->id,
					'skills' => $sval['value']
				);
				if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'.$sval['value'].'%')->exists()){
					DB::table('job_application_skills')->insert($values);
				}
				



				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		


		if (!empty($request->current_designation)) {
			if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'. @$request->current_designation.'%')->exists()){
				DB::table('job_application_skills')->insert(['job_application_id' => $jobApplication->id,'skills' =>  @$request->current_designation]);
				}
			}
		$jobApplication->skills = $skillStr;



		if ($request->hasFile('photo')) {
		    
		        $file = $request->file('photo');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Images/' . $filename;

			    Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				 $fileUrl = Storage::disk('s3')->url($filePath);
				 $jobApplication->photo = $fileUrl;
				 
             //$jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}
         //  dd("dkd");
		$jobApplication->save();


		if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
			$YearskillId = $request->input('YearskillId');
			$MonthskillId = $request->input('MonthskillId');
			$ratingId = $request->input('rating');
			// dd($YearskillId,$MonthskillId);
			DB::table('candidate_job_skill_exp')->where('candidate_id', $jobApplication->id)->where('job_id', $request->job_id)->delete();
			$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
				'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['candidate_id' => $jobApplication->id]],
								['term' => ['job_id' => $request->job_id]],
							],
						],
					],
				],
			]);
			foreach ($YearskillId as $skillId => $value) {
				// if (!empty($value)) { 
				$ss = "Year";
				if($value >=1){
					$ss = "Years";
				}
                if(!empty($MonthskillId[$skillId])){
					$valMonth = $MonthskillId[$skillId] ;
				}else{
					$valMonth = $MonthskillId[$skillId] ;
				}
				 $skill_value = $value.'.'.$valMonth.' '.$ss;
				 $rating = $ratingId[$skillId] ;
		
					$values = array(
						'job_skill_id' => $skillId,
						'skill_value' => $skill_value,
						'job_id' => $request->job_id,
						'rating' => (int)$rating,
						'candidate_id' => $jobApplication->id
					);
					DB::table('candidate_job_skill_exp')->insert($values);
					$expId = DB::getPdo()->lastInsertId();
					$skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillId)->first();
					$this->elasticService->getClient()->index([
						'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'skill_name' => $skills->skill_name ?? '',
							'job_skill_id' => $skillId,
							'skill_value' => $skill_value,
							'job_id' => $request->job_id,
							'rating' => (int)$rating,
							'candidate_id' =>$jobApplication->id,
							'created_at' =>  now()->toIso8601String(),
							'updated_at' =>  now()->toIso8601String()
						]
					]);
				// }
			}
		}

		if ($request->has('AddJobWiseCondidateRankRow')){
			if($request->AddJobWiseCondidateRankRow > 0){
				$skillArr = explode(',', @$jobApplication->skills);
		        $percentage =  GeneralMethod::skill_match_percentage(@$request->job_id,$skillArr);
				DB::table('add_job_wise_condidates')->where('id', '=', $request->AddJobWiseCondidateRankRow)->update(['skill_match' => $percentage]);
			}
			
	    }

		// $mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;
		$mailSetting = optional(ApplicationSetting::select('id', 'mail_setting')->first())->mail_setting;


		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)
			//->orderBy('priority', 'ASC')
			->count();
		if ($status_cnt > 0) {
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)->where('id', @$request->childStatus)->first();
			//$status_name			=	$status_row->name;

		}
		//GeneralMethod :: manageTimeline(auth()->user()->id, $jobApplication->id, $jobApplication->job_id, $request->status_id, $status_name, 'Successfully Application  Update ');
		if ($request->hasFile('resume')) {

             $companyNameR2 = DB::table('companies')
			->where('id',auth()->user()->company_id)
			->value('company_name');   // change column if needed
			$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
			$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
			$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
			$year  = date('Y');
            $month = date('m');

			$tempPath = session('resume_temp_path');
			$full_name = session('resume_original_name');
			
			UploadResumeToTestRTwo::dispatch($tempPath, $full_name,$jobApplication->id,$companyNameRTwo,$year,$month)->onConnection('emails');
			$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
			DB::table('job_applications')
							->where('id', $jobApplication->id)
							->update(['resume' =>$fileUrl]);
							session()->forget([
								'resume_temp_path',
								'resume_original_name'
							]);				
				
		}

		if(empty($request->resumeCheck)){
			DB::table('job_applications')
				->where('id', $jobApplication->id)
				->update(['resume' =>'']);
		}

		
		// Job Application Answer save
		if (isset($request->answer) && count($request->answer) > 0) {
			foreach ($request->answer as $key => $value) {
				JobApplicationAnswer::updateOrCreate(['job_application_id' => $jobApplication->id, 'job_id' => $jobApplication->job_id, 'question_id' => $key], ['answer' => $value]);
			}
		}

		DB::table('application_additional_detail')->where('application_id', $jobApplication->id)->where('job_id', $jobApplication->job_id)->delete();

		$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
				'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['application_id' => (int) $jobApplication->id]],
								['term' => ['job_id' =>(int) $jobApplication->job_id]],
							],
						],
					],
				],
		]);


		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$jobApplication->job_id,
					'question_id' => @$jd->id,
					'application_id' => @$jobApplication->id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$expId = DB::getPdo()->lastInsertId();
				$this->elasticService->getClient()->update([
						'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'doc' => [
								'job_id' => @$jobApplication->job_id,
								'question_id' =>  @$jd->id,
								'application_id' =>@$jobApplication->id,
								'job_detail_value' => @$jd->detail_value,
								'application_detail_value' =>@$request->additional_detail[$inc],
								'created_at' =>  now()->toIso8601String(),
								'updated_at' =>  now()->toIso8601String()
							],
							'doc_as_upsert' => true
						]
					]);
				$inc++;

			}
		}

		if ($request->has('AddJobWiseCondidateRankRow')){
			$addJobWiseCondidate_id = $request->AddJobWiseCondidateRankRow;
			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES,
				'id' => $addJobWiseCondidate_id
			]);
			if ($exists) {
				// 2. Prepare the update timestamp
				$updateTime = now()->toIso8601String();
				$applications_up = JobApplication::select(
				'job_applications.*',
				'state_list.state as state_name',
			    'city_list.city as city_name',
                'countries.country_name',
				'qualification_master.name as qualification_name')
				->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
				->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
				->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
				->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
				->where('add_job_wise_condidates.id', $addJobWiseCondidate_id)
				->first();

				 $jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) use($jobApplication) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id', '=', $jobApplication->id); // Add the candidate condition
				})
				->select('job_metronics_skills.*','candidate_job_skill_exp.skill_value')
				->where('job_metronics_skills.job_id', '=', $request->job_id)
				->get();

                // dd($jobSkill);

                if (count($jobSkill)>0) {
                    foreach ($jobSkill as $sval) {

                         $skillValue = str_replace(['Years','Year',' '],'',$sval->skill_value);
                                    // dd($skillValue);
                         if(!is_null($skillValue) && !empty($skillValue) && $skillValue !='0.'){
                            $values = array(
                                'job_application_id' => $jobApplication->id,
                                'skills' => $sval->skill_name
                            );
                            if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'.$sval->skill_name.'%')->exists()){
                                DB::table('job_application_skills')->insert($values);
                            }
                        }
                    
                    }
                }


				$job_application_skills = DB::table('job_application_skills')
                    ->where('job_application_id', $jobApplication->id)
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'job_application_id' => $s->id,
                        'skills' => strtolower($s->skills)
                    ])
                    ->toArray();

					$application_skills = DB::table('job_application_skills')
					->where('job_application_id', $jobApplication->id)
					->pluck('skills') // only take skills column
					->map(fn($skill) => strtolower($skill))
					->implode(',');

					$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
					
					$interviewDateOne   = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
					$interviewDateTwo   = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
					$interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);
					$interviewDateFour  = $this->formatInterviewDateTime($request->interviewDateFour, $request->interviewTimeFour, $defaultTimeFormat);
					$interviewDateFive  = $this->formatInterviewDateTime($request->interviewDateFive, $request->interviewTimeFive, $defaultTimeFormat);
					$interviewDateSix   = $this->formatInterviewDateTime($request->interviewDateSix, $request->interviewTimeSix, $defaultTimeFormat);

					// interview_mode_one, interview_mode_two, interview_mode_three, interview_mode_four, interview_mode_five
					
				$updateParams = [
					'index' => ELASTIC_CANDIDATES,
					'id' => $addJobWiseCondidate_id,
					'retry_on_conflict' => 5,
					'body' => [
						'doc' => [
							'thirdparty_company_id' => $applications_up->thirdparty_company_id,
							'full_name' => $applications_up->full_name,
							'email' => $applications_up->email,
							'phone' => $applications_up->phone,
							'np_id' => $applications_up->np_id,
							'serving_notice_period_mode' => $applications_up->serving_notice_period_mode,
							'country' => $applications_up->country,
							'state' => $applications_up->state,
							'gender' => $applications_up->gender,
							'city' => $applications_up->city,
							'city_id' => $applications_up->city_id,
							'skills' => $application_skills,
							'photo' => $applications_up->photo,
							'pincode' => $applications_up->pincode,
							'total_exp' => $applications_up->total_exp,
							'rel_exp' => $applications_up->rel_exp,
							'careersummary' => $applications_up->careersummary,
							'nationality' => $applications_up->nationality,
							'wm' => $applications_up->wm,
							'employmentType' => $applications_up->employmentType,
							'oh' => $applications_up->oh,
							'visastatu' => $applications_up->visastatu,
							'referencename' => $applications_up->referencename,
							'reason_for_job_change' => $applications_up->reason_for_job_change,
							'ctc' => $applications_up->ctc,
							'job_application_skills' => $job_application_skills,
							'current_ctc_cur' => $applications_up->current_ctc_cur,
							'current_ctc_mode' => $applications_up->current_ctc_mode,
							'current_designation' => $applications_up->current_designation,
							'ectc' => $applications_up->ectc,
							'current_ectc_cur' => $applications_up->current_ectc_cur,
							'current_ectc_mode' => $applications_up->current_ectc_mode,
							'current_company' => $applications_up->current_company,
							'ctc_hourly' => $applications_up->ctc_hourly,
							'ctc_monthly' => $applications_up->ctc_monthly,
							'ctc_yearly' => $applications_up->ctc_yearly,
							'ectc_hourly' => $applications_up->ectc_hourly,
							'ectc_monthly' => $applications_up->ectc_monthly,
							'ectc_yearly' => $applications_up->ectc_yearly,
							'status' => $applications_up->status,
							'duplicate' => $applications_up->duplicate,
							'bill_rate_currency' => $applications_up->bill_rate_currency,
							'bill_rate_amount' => $applications_up->bill_rate_amount,
							'bill_rate_mode' => $applications_up->bill_rate_mode,
							'condidate_type' => $applications_up->condidate_type,
							'master_status_id' => $applications_up->master_status_id,
							'is_viewed' => $applications_up->is_viewed,
							'column_priority' => $applications_up->column_priority,
							'phone_code' => $applications_up->phone_code,
							'second_phone_code' => $applications_up->second_phone_code,
							'secondary_mobile' => $applications_up->secondary_mobile,
							'resume' => $applications_up->resume,
							'qualification_name' => $applications_up->qualification_name,
							'qualification' => $applications_up->qualification,
							'country_name' => $applications_up->country_name,
							'state_name' => $applications_up->state_name,
							'city_name' => $applications_up->city_name,
							'dob' => $applications_up->dob,
							'percentage_hike' => $applications_up->percentage_hike,
							'linkedin' => $request->linkedin,
							'github' => $request->github,
							'behance' => $request->behance,
							'otherLink' => $request->otherLink,
							'interviewDateOne' => ($interviewDateOne) ? Carbon::parse($interviewDateOne)->toIso8601String() : null,
							'interview_mode_one' => $request->interview_mode_one,
							'interviewDateTwo' => ($interviewDateTwo) ? Carbon::parse($interviewDateTwo)->toIso8601String() : null,
							'interview_mode_two' => $request->interview_mode_two,
							'interviewDateThree' => ($interviewDateThree) ? Carbon::parse($interviewDateThree)->toIso8601String() : null,
							'interview_mode_three' => $request->interview_mode_three,
							'interviewDateFour' => ($interviewDateFour) ? Carbon::parse($interviewDateFour)->toIso8601String() : null,
							'interview_mode_four' => $request->interview_mode_four,
							'interviewDateFive' => ($interviewDateFive) ? Carbon::parse($interviewDateFive)->toIso8601String() : null,
							'interview_mode_five' => $request->interview_mode_five,
							'interviewDateSix' => ($interviewDateSix) ? Carbon::parse( $interviewDateSix)->toIso8601String() : null,
							'interview_mode_six' => $request->interview_mode_six,
							'comment' => $applications_up->comment
						],
						'doc_as_upsert' => true,
						'detect_noop' => false
					],
					'refresh' => true // Force immediate refresh
				];
	            // GeneralMethod::JobApplicationsAddEditUpload($addJobWiseCondidate_id);
				$this->elasticService->getClient()->update($updateParams);
				GeneralMethod::elasticBenchCandidateAddEditIndex($jobApplication->id);
				GeneralMethod::addCompanyCandidate($company_id,$addJobWiseCondidate_id);
				GeneralMethod::addCompanyBenchCandidate($company_id,$jobApplication->id);
			}
			
	    }


			$cachekeys = [
					'allcvtracker_version_user_'.$this->user->id,
					'allapptracker_version_user_'.$this->user->id,
					'allclientapp_version_user_'.$this->user->id,
					'candidateDetails_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}


		

         $currentPage =  @$request->currentPage;
		 $currentSlug  =  @$request->currentSlug;

		 $relationget =  GeneralMethod::getrRcommendationRelationData(auth()->user()->company_id,$request->job_id);
         $recommendation_id = $relationget->to_company_id ?? null;
		 $to_company_id = $relationget->to_company_id ?? null;

		 if($currentSlug=='cvtracker'){	
				return Reply::redirect(url('/service_company/cvtracker/'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
		 }else{

			if ($recommendation_id > 0 && @$to_company_id == auth()->user()->company_id) {
				return Reply::redirect(url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($request->job_id) . '/all'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
			} else {
				return Reply::redirect(url('/service_company/apptracker/' . GeneralMethod::value_encrypt($request->job_id) . '/all'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
			}
	  }

	}
	function formatInterviewDateTime($date, $time, $defaultTimeFormat) {
		if (empty($date) || empty($time)) {
			return null; // if either is missing, return null
		}

		$eventStartTime = $date . ' ' . $time;

		if ($defaultTimeFormat == 1) {
			return date('Y-m-d H:i:s', strtotime($eventStartTime)); // 12-hour format
		} else {
			return date('Y-m-d H:i:s', strtotime($eventStartTime));   // 24-hour format
		}
	}
	public function createSchedule(Request $request, $id)
	{
		// dd("kf");
	   
		abort_if(Gate::denies('add_schedule'), 403);

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

        // 		$this->candidates = JobApplication::all(); // 02/05/2024 comment by rajveer 
		// $this->users 				=	User::with('company')->company()->get();
		$this->users = User::with('company')->company()->where('users.type', '<>', 1)->get();

		$this->datadate = $request->input('datadate');  // null if not set
        $this->datatime = $request->input('datatime');  // null if not set
		$this->datatmode = $request->input('datatmode', 2); // null if not set;  

		// dd($this->datadate,$this->datatime,$this->datatmode);
		
		$this->scheduleDate = $request->date;
		// $this->currentApplicant = JobApplication::findOrFail($id);
		$this->interview_node = GeneralMethod::interview_mode();
		$this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
		$this->application_id = $id;
		// dd($this->application_id);
		$this->interview_detail = array();
		$this->application = JobApplication::select(
			'job_applications.*',
			'clients.name as client_name',
			'clients.self_company',
			'jobs.title as job_title',
			'client_location.city',
			'jobs.company_id',
			'master_status.name as master_status_name',
			'master_status.css_style as master_status_style',
			'users.email as submitted_by_email',
			'users.name as submitted_by_name',
			'users.company_id as submitted_by_company_id',
			'qualification_master.name as qualification_name',
			'state_list.state as state_name',
			'users.name as posted_by',
			'users.image as posted_by_image',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'add_job_wise_condidates.id as wiseId',
			'add_job_wise_condidates.job_id as jobId',
			'add_job_wise_condidates.thirdparty_company_id as ClientComID',
			'jobs.client_id'
		)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			// ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->join('clients', 'clients.id', '=', 'jobs.client_id')
			// ->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->join('users', 'users.id', '=', 'add_job_wise_condidates.submitted_by')
			->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')
			->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->where('add_job_wise_condidates.id', $this->application_id)
			->groupBy('job_applications.id')->first();

	
					// dd($aval);
					$i = 0;
					if($this->application->condidate_type=='bench_condidate'){

					$bench_assign = DB::table(table: 'bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')
					->select('users.id','users.email','users.name')
					->where('bench_assign.bench_applications_id', $this->application->id)
					->get()->toArray();
					 $commonCcArr = array();
						foreach ($bench_assign as $assign) {
							$commonCcArr[$i] = $assign->email;
							$i++;
						}
						$this->application->email = implode(',',$commonCcArr);
					// dd($aval->candidate_id,$commonCcArr);
					}

					
				


		$country = '';
		$this->row_country_name = '';

		if (empty($this->application->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->application->country)) {
				if (is_numeric($this->application->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->application->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}

		$this->row_state_name = '';
		if (empty($this->application->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->application->country == 101) {
				$this->row_state_name = $this->application->state_name;
			} else {
				$state = $this->application->state;
				if (is_numeric($this->application->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}
			}
		}

		$this->row_city_name = '';
		if ($this->application->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = "";
		$current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur];
		$current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur];
		if ($this->application->condidate_type == 'bench_condidate') {
			$current_ectc_cur = $currencySymbole[$this->application->bill_rate_currency];
		}
		$ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc);
		$ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc);
		if ($this->application->condidate_type == 'bench_condidate') {
			$ectc = GeneralMethod::thousandsCurrencyFormat($this->application->bill_rate_amount);
		}
		$current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode ?? 0], 0, 1);
		$current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode ?? 0], 0, 1);
		if ($this->application->condidate_type == 'bench_condidate') {
			$current_ectc_mode = substr($salaryMode[$this->application->bill_rate_mode], 0, 1);
		}
		$this->current_ctc = @$current_ctc_cur . @$ctc . '/' . @$current_ctc_mode;
		$this->expected_ctc = @$current_ectc_cur . @$ectc . '/' . @$current_ectc_mode;

		$this->child_status_name = '';
		$submitted_by_company_id = $this->application->submitted_by_company_id;
		$submitted_by_row = DB::table('companies')->select('*')->where('companies.id', $submitted_by_company_id)->first();
		$this->submitted_by_company = $submitted_by_row->company_name;

		if ($this->application->child_status_id) {
			$child_status_by_row = DB::table('candidate_status')->select('*')->where('candidate_status.id', $this->application->child_status_id)->first();
			$this->child_status_name = $child_status_by_row->name;
		}

		$this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$this->client_contact_info = DB::table('jobs')->join('client_contact_info_jobs', 'client_contact_info_jobs.job_id', '=', 'jobs.id')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', '=', $this->application->jobId)->orderBy('client_contact_info.contact_person_name', 'ASC')->first();

		$this->job_id = $this->application->jobId;
		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		// interview_mode_lavel
		$this->email_template = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 3)->orderBy('th_email_template.e_temp_id', 'ASC')->get();
		$this->from_email = $this->user->email;
		$this->cc_email = $this->user->email;
		if ($this->application->submitted_by_email != $this->user->email) {
			$this->cc_email = $this->cc_email . ' , ' . $this->application->submitted_by_email;
		}

		$client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $this->application->jobId)->get();
		if (!empty($client_contact_arr)) {
			foreach ($client_contact_arr as $ccData) {
				if (!empty($ccData->contact_person_email)) {
					$this->cc_email = $this->cc_email . ' , ' . $ccData->contact_person_email;
				}
			}
		}
		
		$assignDataone = DB::table('users')
				->leftjoin('role_user', 'role_user.user_id', 'users.id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->leftjoin('roles', 'roles.id', 'role_user.role_id')
				->leftjoin('companies', 'companies.id', 'users.company_id')
				->where('users.company_id','=',auth()->user()->company_id)
				->where('users.status', 'active')
				->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')->groupBy('users.id')->get()->toArray();
			
							
			$assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->leftjoin('companies', 'companies.id', 'job_assign.company_id')
			->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')->where('job_assign.job_id', $this->application->jobId)->get()->toArray();

			$mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
				return $a->id <=> $b->id; // Compare by 'id' field
			});

			// dd($mergedData);
			$clientId = Job::where('jobs.id', $this->application->jobId)
			->join('clients', 'clients.id', '=', 'jobs.client_id')->pluck('jobs.client_id')->toArray();

			$assignDataclient = DB::table('clients')
				->leftjoin('companies', 'companies.id', 'clients.company_id')
				->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
				->select('client_contact_info.id','client_contact_info.contact_person_email as email','client_contact_info.contact_person_name as name','client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
				->whereIn('clients.id', $clientId)->get()->toArray();

				// dd($assignDataclient,$clientId,$this->application->jobId);



			// $assignData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
			// ->leftjoin('role_user', 'role_user.user_id', 'users.id')
			// ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			// ->leftjoin('roles', 'roles.id', 'role_user.role_id')
			// ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
			// ->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')->where('job_assign.job_id',$this->application->jobId )->get()->toArray();
            // dd($this->application->jobId,$assignData);
     
            $companyOrClientName = GeneralMethod::companyOrClientName($this->application->client_id);
            $this->selfCompany = ($companyOrClientName->selfCompany) ? $companyOrClientName->selfCompany : '';
            $this->companyTemp = ($companyOrClientName->company) ? $companyOrClientName->company : '';
            $this->clientTemp = ($companyOrClientName->client) ? $companyOrClientName->client : '';
            
			// $assignDataFinal = array_udiff($mergedData, $assignDataclient, function ($a, $b) {
			// 	return $a->id <=> $b->id; // Compare by 'id' field
			// });
			// dd($assignDataFinal);

			$assignDataFinal = array_merge($assignDataclient,$mergedData);

			// dd($assignDataFinal);
     
	    $this->cc_email_assignData = $assignDataFinal;
	    $this->update = false;
	    $this->savesend = true;
	    if($request->relint){
	        	$interviewSchedule = InterviewSchedule::join('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id')->select('interview_schedules.*','updatetimeline.interview_mention_link')->where('interview_schedules.id',$request->relint)->first();
	           //dd($interviewSchedule);
	            $this->scheduledate = $interviewSchedule->schedule_date;
	             $this->emailtemplateset = $interviewSchedule->email_template;
	            $this->interviewmode = $interviewSchedule->interview_mode;
	            $this->interviewmodelavel = $interviewSchedule->interview_mode_lavel;
	            $this->interviewers = $interviewSchedule->interviewers;
	            $this->interviewmentionlink = $interviewSchedule->interview_mention_link;
	           $this->update = true;
	           $this->savesend = false;
	           
	    }
		$this->timezone 					=	DB::table('master_timezone')->select('*')->orderByDesc('master_timezone.priority')->get();
	    
		$this->reschedule = @$_REQUEST['reschedule'];
		$view = view('service_company.job-applications.interview-create', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);


	}
	public function contactPersionWithTeam($id)
	{

		
		$client_id = $_REQUEST['client_id'];
		$job_id = $_REQUEST['job_id'];
		$this->panel = $_REQUEST['panel'];
		$this->self_company = $_REQUEST['self_company'];
		$this->client_name = $_REQUEST['client_name'];
		$this->cc_email = array_map('trim', explode(",", $_REQUEST['cc_email']));
		

		$assignDataone = DB::table('users')
				->leftjoin('role_user', 'role_user.user_id', 'users.id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->leftjoin('roles', 'roles.id', 'role_user.role_id')
				->leftjoin('companies', 'companies.id', 'users.company_id')
				->where('users.company_id','=',auth()->user()->company_id)
				->where('users.status', 'active')
				->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')->groupBy('users.id')->get()->toArray();
			
							
			$assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->leftjoin('companies', 'companies.id', 'job_assign.company_id')
			->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')
			->where('users.status', 'active')
			->where('job_assign.job_id', $job_id)->get()->toArray();

			// $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
			// 	return $a->id <=> $b->id; // Compare by 'id' field
			// });
			$mergedData = collect(array_merge($assignDataone, $assignDatatwo))->unique('id')->values()->toArray();

			$assignDataoneEmail = DB::table('users')
				->leftjoin('role_user', 'role_user.user_id', 'users.id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->leftjoin('roles', 'roles.id', 'role_user.role_id')
				->leftjoin('companies', 'companies.id', 'users.company_id')
				->where('users.company_id','=',auth()->user()->company_id)
				->where('users.status', 'inactive')
				->pluck('users.email')->toArray();

				DB::table('client_contact_info')->whereIn('contact_person_email',$assignDataoneEmail)->where('client_id','=',$client_id)->delete();

				$assignDataoneActive = DB::table('users')
				->leftjoin('role_user', 'role_user.user_id', 'users.id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->leftjoin('roles', 'roles.id', 'role_user.role_id')
				->leftjoin('companies', 'companies.id', 'users.company_id')
				->where('users.company_id','=',auth()->user()->company_id)
				->where('users.status', 'active')
				->pluck('users.email')->toArray();


		$assignDataclient = DB::table('clients')
				->leftjoin('companies', 'companies.id', 'clients.company_id')
				->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
				->select('client_contact_info.id','client_contact_info.contact_person_email as email','client_contact_info.contact_person_name as name','client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
				->whereNotIn('contact_person_email',$assignDataoneActive)
				->where('clients.id',$client_id)
				->get()->toArray();

	
        $assignDataFinal = collect(array_merge($assignDataclient, $mergedData))->unique('id')->values()->toArray();
		// $assignDataFinal = array_merge($assignDataclient,$mergedData);
		$this->cc_email_assignData = $assignDataFinal;
		

		$view = view('service_company.job-applications.contact-persion-with-team', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function getinterviewchild(Request $request)
	{
		$id = $job_id = '';
		$id = @$request->id;
		$job_id = @$request->job_id;
		// dd($job_id);
		$Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();

		$companyData = $this->user->company;

		// dd($Data);

		if (!empty($Data)) {
			if ($id == 1) {
				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ');">
													<option value="">Select Mode</option>';
				foreach ($Data as $cval) {

					$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';

					$flag = 1;
				}
				$html .= '</select>';
			} else if ($id == 2) {
				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ',' . $job_id . ');">
													<option value="">Select Mode</option>';
				foreach ($Data as $cval) {

					if ($cval->id == 2) {  // Google Meeting
						// if ($companyData->google_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						// }
					} else if ($cval->id == 3) {  // Zoom Meeting
						// if ($companyData->zoom_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						// }
					} else if ($cval->id == 5) {  // Microsoft Meeting
						// if ($companyData->microsoft_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						// }
					} else {
						$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
					}


					$flag = 1;
				}
				$html .= '</select>';
			} else if ($id == 3) {

				$Data = DB::table('client_location')
					->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'city_list.city as cityname', 'state_list.state', 'countries.country_name')
					->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')
					->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
					->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
					->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
					->where('client_location_jobs.job_id', $job_id)
					->orderBy('client_location.id', 'ASC')
					->get();

				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ');">
													<option value="">Select Address</option>';
				foreach ($Data as $cval) {
					$cityName = @$cval->city;
					if (empty(@$cval->city)) {
						$cityName = @$cval->cityname;
					}
					$country_name = '';
					if (!empty(@$cval->country_name)) {
						$country_name = "- " . @$cval->country_name;
					}
					$stateName = '';
					if (!empty(@$cval->state)) {
						$stateName = ", " . @$cval->state;
					}
					$html .= '<option value= "' . @$cval->id . '">' . ucfirst(@$cval->address) . '  ' . ucfirst(@$cityName) . '  - ' . @$cval->postcode . ' ' . @$stateName . ' ' . @$country_name . '</option>';
					$flag = 1;
				}
				$html .= '<<option value="0">Other address</option>';
				$html .= '</select>';
			}
		} else {
			$html = '&nbsp;-&nbsp;';
		}
		return Reply::dataOnly(['status' => 'success', 'data' => $html]);
	}
	public function recruiter($id)
	{

		$panellist = '';
		$panellist = $_REQUEST['panellist'];
		$this->panellistArr = explode(',', $panellist);

		//echo '<pre>'; print_r($panellistArr); 


		$company_id = auth()->user()->company_id;
		$this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();


		$company_id = auth()->user()->company_id;
		$this->team = DB::table('users')
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->select('users.id', 'users.name', 'users.email', 'users_profile.designation', 'roles.display_name', 'users.working_status')
			->where('type', 0)
			->where('users.company_id', $company_id)
			->where('status', 'active')
			->get();

		// dd($this->team);

		$view = view('service_company.job-applications.recruiter', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function accessEmailTemplate(Request $request)
	{

		$template_id = $application_id = $template_subject = $job_title = $client_name = $job_id = '';
		$email = $template_description = $full_name = $cc_email = $submitted_by_email = $to_email = '';
		$template_id = @$request->template_id;
		$application_id = @$request->application_id;
		$interviewTime = @$request->interviewTime;
		$timezone = @$request->timezone;
		$email = $this->user->email;
		
		//echo '<pre>'; print_r($this->user->email); exit;
        // 		$applicationRow = DB::table('job_applications')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('users', 'users.id', '=', 'job_applications.submitted_by')->select('jobs.id as job_id', 'jobs.title', 'clients.name as client_name', 'job_applications.full_name', 'users.email as submitted_by_email', 'job_applications.email as to_email')->where('job_applications.id', $application_id)->first();
        		
        		
        		
        // 		 dd(auth()->user()->id);
		

		    
		$params = [
            'index' => ELASTIC_CANDIDATES,
            'size'  => 1,
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['jobId' => (int) @$request->jobId]],
                            ['term' => ['candidate_id' => $application_id]],
                        ]
                    ]
                ]
            ]
        ];
        
        $response = $this->elasticService->getClient()->search($params);

        $job_applications = null;
        
        if (!empty($response['hits']['hits'])) {
            $job_applications = (object) $response['hits']['hits'][0]['_source'];
        }
        
        $params = [
				'index' => ELASTIC_JOBS,
				'id'    =>(int) @$request->jobId,
		];
		
		$response = $this->elasticService->getClient()->get($params);
		$this->job = (object) $response['_source'];
		
		$client_city_jobs = $this->job->client_city_jobs;
		
		$firstCity = collect($client_city_jobs)->first();
		
	   $client_location = $this->job->client_location;
		
		$firstLocation = collect($client_location)->first();
		
        $addressName = $firstLocation['address'] ?? null;                  
        $cityName = $firstCity['city_name'] ?? null;
        $city = mb_substr(@$cityName, 0, 21);
        $countryName = $firstCity['country_name'] ?? null;
        $country_name = mb_substr(@$countryName, 0, 6);
        
        $location = ucwords(@$addressName).' | '. ucwords(@$city).' | '.ucwords(@$country_name);
        
         $params = [
				'index' => ELASTIC_USERS_LIST,
				'id'    =>(int) @$job_applications->submitted_by,
		];
		
		$response = $this->elasticService->getClient()->get($params);
		$submitted_by = (object) $response['_source'];
		

		
		
		
		$job_title = "(" . $job_applications->title . ")";
		$client_name = "(" . $job_applications->client_name . ")";
		$full_name = $job_applications->full_name;
		$job_id = @$job_applications->jobId;


		$dataRow = DB::table('th_email_template')->select('*')->where('th_email_template.e_temp_id', $template_id)->first();

		$interview_questions = DB::table('interview_questions')->select('*')->where('job_id', $job_id)->get();
		

		
		$job = Job::find($request->jobId);
		
		$companyOrClientName = GeneralMethod::companyOrClientName($job_applications->client_id);
	    
		$template_subject = str_replace(["[{COMPANY_NAME}]","[{JOB_TITLE}]","[{CLIENT_NAME}]"], [$companyOrClientName->company,$job_title, $companyOrClientName->client], $dataRow->template_subject);
		
		;
		$template_subject = str_replace("[{JOB_TITLE}]", $job_title, $template_subject);
		$template_subject = str_replace("[{COMPANY_NAME}]", $client_name, $template_subject);
		         $InterviewMode = '';
    	    	if (@$request->interview_mode == 1) {
    				$InterviewMode = 'Telephonic';
    			}
    			if (@$request->interview_mode == 2) {
    				$videoUrl = @$request->videoUrl;
    				$InterviewMode = 'Video';
    			}
    			if (@$request->interview_mode == 3) {
    				$videoUrl = @$request->videoUrl;
    				$InterviewMode = 'F2F';
    			}
			$dateTime = date('d-M-Y', strtotime($request->interviewDate)). ' '.$interviewTime.' ('.$timezone.')';
		
		    // if($dataRow->tempForInterview==1){
            //      $template_subject =	"Interview Confirmation || $job->title || $companyOrClientName->company | $dateTime | $InterviewMode Interview";
            // }elseif($dataRow->tempForInterview==2){
            //      $template_subject =	"Interview Confirmation || $job->title || $companyOrClientName->client | $dateTime | $InterviewMode Interview";
            // }elseif($dataRow->tempForInterview==3){
            //      $template_subject =	"Interview Confirmation ||  $job->title || $companyOrClientName->client | Payroll $companyOrClientName->company";
            // }
            
		$template_description = $dataRow->template_description;
        // $template_description = str_replace("[{JOB_TITLE}]", $job_title, $template_description);
        // $template_description = str_replace("[{COMPANY_NAME}]", $client_name, $template_description);
        // $template_description = str_replace("[{CANDIDATE_NAME}]", $full_name, $template_description);
        
             $jobid =  @$job_applications->jobId;
				


		$videoUrl = '';
		$videoUrlInter = '';
		$text_set = "";
		$interview_level='';
		if (!empty(@$request->interview_mode)) {
			if (@$request->interview_mode == 1) {
				$videoUrl = '**Telephonic** : ...........';
				$otherDetails = "";
				$videoUrlInter = '';
				$text_set = "";
				$interview_level='Interview Telephonic';
			}
			if (@$request->interview_mode == 2) {
				$videoUrl = @$request->videoUrl;
				$videoUrlInter = '<a href="'.$videoUrl.'" style="cursor: pointer;"> '.@$request->videoUrl.'</a>';
				$videoUrl = '<a href="'.$videoUrl.'" style="cursor: pointer;"> Click Here to Join the Interview </a><br>';
				$otherDetails = "<p>**Interview Instructions for Video Interview:**</p>
				<p>&nbsp;</p>
				<p>1. **Join the interview 5 minutes before the scheduled time.**</p>
				<p>2. **Ensure you have a stable internet connection.**</p>
				<p>3. **Use a laptop for the interview; mobile devices are not recommended.**</p>
				<p>4. **Avoid using proxies during the interview, as this will result in immediate disqualification.**</p>
				<p>&nbsp;</p>";
				$text_set = "Or copy and paste the link below into your browser";
				$interview_level='Interview Link';
			}
			if (@$request->interview_mode == 3) {
				$videoUrl = @$request->videoUrl;
				$videoUrlInter = '';
				$otherDetails = "<p>**Interview Instructions for Face-to-Face Interview:**</p>
				<p>&nbsp;</p>
				<p>1. **Arrive at the interview location 30 minutes before the scheduled time.**</p>
				<p>2. **Turn off your mobile phone during the interview.**</p>
				<p>3. **Review the job description and prepare for key skills relevant to the position.**</p><p>&nbsp;</p>";
				$text_set = "";
				$interview_level='Interview Address';
			}
		}

	$interview_ques='';
	if(count($interview_questions) >0){
		 $interview_ques .= '<h3><strong>Job Interview Questions</strong></h3>';
         $interview_ques .= '<ol>';
		foreach($interview_questions as $row){
							$interview_ques .= '<li>'.$row->questions.'</i>';
		}
		 $interview_ques .= '</ol><hr />';

	}
	// dd($interview_ques);


	$template_description = str_replace("[{VIDEOURL}]", $videoUrl, $template_description);
	if($dataRow->tempForInterview==1){
		$template_description = str_replace(["[{CANDIDATE_NAME}]","[{COMPANY_NAME}]","[{JOB_TITLE}]","[{JOB_ID}]","[{INTERVIEW_MODE}]","[{INTERVIEW_DATE_TIME}]","[{WEBSITE}]","[{JOB_DESCRIPTION}]","[{COMPANY_DESCRIPTION}]","[{INTERVIEW_LINK}]","[{OTHER_DESCRIPTION}]","[{WORK_LOCATION}]","[{HR_NAME}]","[{INTERVIEW_LINK_URL}]","[{INTERVIEW_LEVEL}]","[{TEXT_SET}]","[{INTERVIEW_QUESTIONS}]"], [$full_name,$companyOrClientName->company,$job_title, $job_id,$InterviewMode,$dateTime,$companyOrClientName->company_website,$job->job_description,$companyOrClientName->company_description,$videoUrl,$otherDetails,$location,	auth()->user()->name,$videoUrlInter,$interview_level,$text_set,$interview_ques], $template_description);
	}elseif($dataRow->tempForInterview==2){
	    $template_description = str_replace(["[{CANDIDATE_NAME}]","[{CLIENT_NAME}]","[{JOB_TITLE}]","[{JOB_ID}]","[{INTERVIEW_MODE}]","[{INTERVIEW_DATE_TIME}]","[{WEBSITE}]","[{JOB_DESCRIPTION}]","[{COMPANY_DESCRIPTION}]","[{CLIENT_DESCRIPTION}]","[{INTERVIEW_LINK}]","[{OTHER_DESCRIPTION}]","[{WORK_LOCATION}]","[{HR_NAME}]","[{COMPANY_NAME}]","[{INTERVIEW_LINK_URL}]","[{INTERVIEW_LEVEL}]","[{TEXT_SET}]","[{INTERVIEW_QUESTIONS}]"], [$full_name,$companyOrClientName->client,$job_title, $job_id,$InterviewMode,$dateTime,$companyOrClientName->client_website,$job->job_description,$companyOrClientName->company_description,$companyOrClientName->client_description,$videoUrl,$otherDetails,$location,auth()->user()->name,$companyOrClientName->company,$videoUrlInter,$interview_level,$text_set,$interview_ques], $template_description);
	}elseif($dataRow->tempForInterview==3){
	     $template_description = str_replace(["[{CANDIDATE_NAME}]","[{CLIENT_NAME}]","[{JOB_TITLE}]","[{JOB_ID}]","[{INTERVIEW_MODE}]","[{INTERVIEW_DATE_TIME}]","[{WEBSITE}]","[{JOB_DESCRIPTION}]","[{CLIENT_DESCRIPTION}]","[{INTERVIEW_LINK}]","[{OTHER_DESCRIPTION}]","[{WORK_LOCATION}]","[{HR_NAME}]","[{INTERVIEW_LINK_URL}]","[{INTERVIEW_LEVEL}]","[{TEXT_SET}]","[{INTERVIEW_QUESTIONS}]"], [$full_name,$companyOrClientName->client,$job_title, $job_id,$InterviewMode,$dateTime,$companyOrClientName->client_website,$job->job_description,$companyOrClientName->client_description,$videoUrl,$otherDetails,$location,auth()->user()->name,$videoUrlInter,$interview_level,$text_set,$interview_ques], $template_description);
	     
	     $template_description = str_replace(["[{CANDIDATE_NAME}]","[{COMPANY_NAME}]","[{JOB_TITLE}]","[{JOB_ID}]","[{INTERVIEW_MODE}]","[{INTERVIEW_DATE_TIME}]","[{WEBSITE}]","[{JOB_DESCRIPTION}]","[{COMPANY_DESCRIPTION}]","[{INTERVIEW_LINK}]","[{OTHER_DESCRIPTION}]","[{INTERVIEW_LINK_URL}]","[{INTERVIEW_LEVEL}]","[{TEXT_SET}]","[{INTERVIEW_QUESTIONS}]"], [$full_name,$companyOrClientName->company,$job_title, $job_id,$InterviewMode,$dateTime,$companyOrClientName->company_website,$job->job_description,$companyOrClientName->company_description,$videoUrl,$otherDetails,$videoUrlInter,$interview_level,$text_set,$interview_ques], $template_description);
	     
	}


		$to_email = $job_applications->email;
		$cc_email = $this->user->email;
		if ($submitted_by->email != $this->user->email) {
			$cc_email = $cc_email . ' , ' . $submitted_by->email;
		}
		$client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $job_id)->get();
		if (!empty($client_contact_arr)) {
			foreach ($client_contact_arr as $ccData) {
				if (!empty($ccData->contact_person_email)) {
					$cc_email = $cc_email . ' , ' . $ccData->contact_person_email;
				}
			}
		}

		return Reply::dataOnly(['status' => 'success', 'to_email' => $to_email, 'cc_email' => $cc_email, 'from_email' => $email, 'template_subject' => $template_subject, 'template_description' => $template_description]);

	}
	public function refreshZoomAccessToken($refreshToken)
   {
    try {
		$zoomsettings = DB::table('zoomsettings')->select('zoomsettings.*')->first();
        $clientId = $zoomsettings->client_id;
        $clientSecret = $zoomsettings->client_secret;

        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
        ])->post('https://zoom.us/oauth/token', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->successful()) {
            $newAccessToken = $response->json()['access_token'];
            $newRefreshToken = $response->json()['refresh_token'];
            $newExpiresIn = $response->json()['expires_in'];

            // Store the new tokens securely
            session([
                'zoom_access_token' => $newAccessToken,
                'zoom_refresh_token' => $newRefreshToken,
                'zoom_token_expires_at' => now()->addSeconds($newExpiresIn),
            ]);

            return $newAccessToken;
        } else {
            // Handle error
            return response()->json(['error' => 'Failed to refresh access token'], 400);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while refreshing the access token'], 500);
    }
    }
    private function getZoomAccessToken()
    {
		$refreshToken = DB::table('zoomsettings')->where('id','=',1)->first();
		
        $clientId = $refreshToken->client_id;
        $clientSecret = $refreshToken->client_secret;
        $accountId = $refreshToken->redirect_uri;

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Could not retrieve Zoom access token: ' . $response->body());
    }
	public function getGoogleVideoUrl(Request $request){

			
		    // $client = new Google_Client();
			// $client->setAuthConfig(storage_path('jeroworksproject-f09c7549cf35.json')); // Use Service Account JSON
			// $client->addScope(Google_Service_Calendar::CALENDAR);
			// $client->setSubject('jeroworksproject@jeroworksproject.iam.gserviceaccount.com'); // Use your Google Calendar email
			
			// $service = new Google_Service_Calendar($client);
			
			// $startDate = date('c', strtotime($request->interviewDate . ' ' . $request->interviewTime));
			// $endDate = date('c', strtotime($request->interviewDate . ' ' . $request->interviewTime . ' +1 hour'));
			
			// $attendees = [
			// 	['email' => 'vibhu2103m@gmail.com'],
			// 	['email' => 'rajveer@4bell.com'],
			// ];
			
			// $event = new Google_Service_Calendar_Event([
			// 	'summary' => 'Interview Schedule',
			// 	'start' => ['dateTime' => $startDate, 'timeZone' => 'Asia/Kolkata'],
			// 	'end' => ['dateTime' => $endDate, 'timeZone' => 'Asia/Kolkata'],
			// 	'conferenceData' => [
			// 		'createRequest' => [
			// 			'requestId' => uniqid(),
			// 			'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
			// 		],
			// 	],
			// 	'attendees' => $attendees,
			// ]);
			
			
			// $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
			
			// return response()->json(['status' => 'success', 'meet_link' => $event->getHangoutLink()]);



			
        
		if((int)$request->currentId==3){

            try {
				$interviewDate = @$request->interviewDate;
				$interviewTime = @$request->interviewTime;
				$defaultTimezone = date_default_timezone_get();

				$eventStartTime = $interviewDate . ' ' . $interviewTime;
				 
    		    $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
    			 if($defaultTimeFormat==1){
    			     $formattedStartTime = Carbon::createFromFormat('d-m-Y h:i A', $eventStartTime)->format('Y-m-d\TH:i');
    			   
    			     $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $formattedStartTime);
    			     
    			 }else{
    			     
    				$formattedStartTime = Carbon::createFromFormat('d-m-Y H:i', $eventStartTime)->format('Y-m-d\TH:i');
    				$startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $eventStartTime);
    				
    			 }

				$company_id  = auth()->user()->company_id;

				$refreshToken = DB::table('zoomsettings')->select('zoom_access_token')->first()->zoom_access_token;

				// $newAccessToken = $this->refreshZoomAccessToken($refreshToken);
				 $accessToken = $this->getZoomAccessToken();
                // dd($newAccessToken);

			 $response = Http::withToken($accessToken)
                ->post('https://api.zoom.us/v2/users/me/meetings', [
                    'topic' => 'Interview Schedule',
                    'type' => 2,
                    'start_time' => $startDateTime,
                    'duration' => 60,
                    'timezone' => 'Asia/Kolkata',
                    'agenda' => 'Test Zoom meeting using Server-to-Server OAuth',
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => true,
                        'mute_upon_entry' => true,
                        'approval_type' => 1,
                    ]
                ]);
	
				// $response = Http::withToken($newAccessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
				// 	'topic'      => "Interview Schedule",
				// 	'type'       => 2, // Scheduled meeting
				// 	'start_time' => $startDateTime, // Static start time
				// 	'duration'   => 60, // Static duration
				// 	'timezone'   => 'UTC',
				// 	'settings'   => [
				// 		'join_before_host' => true, // Allow participants to join before the host
				// 	],
				// ]);
			
			
			
			if ($response->successful()) {
				$meetingData = $response->json();
				$joinUrl = $meetingData['join_url']; // Get the meeting join URL
				// dd("1",$joinUrl, $response->status(),$response->body());
				return Reply::dataOnly(['status' => 'success', 'getHangoutLink' =>$joinUrl,'mode'=>'zoom']);
			} else {
				// Handle errors with more detail
				$errorData = $response->json();
				// dd("2",$errorData,$response->status());
				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Zoom Meet','eventId'=>'']);
			}

			} catch (\Exception $e) {
				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Zoom Meet','eventId'=>'']);
			}
			
		}elseif((int)$request->currentId==2){

			try {
				//  $interviewTime = @$request->interviewTime;
				//  $eventStartTime = $request->interviewDate.' '.$interviewTime;
				$eventStartTime = $request->interviewDate.' '.$request->interviewTime;

				
				$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
				if($defaultTimeFormat==1){
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}else{
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}
				$interviewDateCarbon = \Carbon\Carbon::parse($interviewDate);

				$twoHoursBefore = $interviewDateCarbon->copy()->subHours(2)->format('Y-m-d H:i:s');
				$twoHoursAfter = $interviewDateCarbon->copy()->addHours(2)->format('Y-m-d H:i:s');
				$interviewDate = $interviewDateCarbon->toDateString();

				$conflictingMeetingIds = DB::table('google_meeting_schedule')->whereDate('interview_date', $interviewDate)->pluck('meeting_link');

				$google_meeting_lists = DB::table('google_meeting_lists')->where('status','=',1)->whereNotIn('meeting_link', $conflictingMeetingIds)->first();
		

				//  $google_meeting_lists = DB::table('google_meeting_lists')
				// 	->where(function ($query) use ($threeHoursBefore, $fourHoursAfter) {
				// 		$query->whereNull('interview_date')
				// 			->orWhereNotBetween('interview_date', [$threeHoursBefore, $fourHoursAfter]);
				// 	})
				// 	->first();


				// $google_meeting_lists = DB::table('google_meeting_lists')->first();

				if($google_meeting_lists){
					return response()->json(['status' => 'success', 'getHangoutLink' => @$google_meeting_lists->meeting_link]);
				}else{
					return response()->json(['status' => 'expired', 'getHangoutLink' => 'Google Meet','eventId'=>'']);
				}

				


				$eventStartTime = $request->interviewDate.' '.$request->interviewTime; // Replace with actual start time
				$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
				if($defaultTimeFormat==1){
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}else{
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}
				$defaultTimezone = date_default_timezone_get();
				
				$client = new Google_Client();

				$client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Ensure this file exists

				//ats-jero-works
				// $client->setAuthConfig(storage_path('client_secret-ats-jero-works.apps.googleusercontent.com.json')); // Ensure this file exists

				$client->addScope(Google_Service_Calendar::CALENDAR);
				$client->setAccessType('offline');
				$client->setPrompt('consent'); // Ensures refresh token is generated
				
				// Retrieve stored access and refresh token
				$accessToken = session('google_access_token');
				$refreshToken = session('google_refresh_token'); 
				
				if ($accessToken) {
					$client->setAccessToken($accessToken);
				
					// Refresh token if access token is expired
					if ($client->isAccessTokenExpired()) {
						if ($refreshToken) {
							$newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
							session(['google_access_token' => $newAccessToken]);
				
							if (isset($newAccessToken['refresh_token'])) {
								session(['google_refresh_token' => $newAccessToken['refresh_token']]); // Store refresh token
							}
						} else {
							return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Google Meet','eventId'=>'']);
							// return response()->json(['status' => 'expired', 'error' => 'Session expired, please login again.'], 401);
						}
					}
				} else {
					return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Google Meet','eventId'=>'']);
					// return redirect('/google/auth')->with('error', 'Please authenticate with Google.');
				}
				
				$service = new Google_Service_Calendar($client);
				
				
				$startDate = date('c', strtotime($interviewDate));
				$endDate = date('c', strtotime($interviewDate . ' +1 hour'));

				$attendees = [
					['email' => 'vibhu2103m@gmail.com'],
					['email' => 'rajveer@4bell.com'],
				];
				
				$event = new Google_Service_Calendar_Event([
					'summary' => 'Interview Schedule',
					'start' => ['dateTime' => $startDate, 'timeZone' => $defaultTimezone],
					'end' => ['dateTime' => $endDate, 'timeZone' => $defaultTimezone],
					'conferenceData' => [
						'createRequest' => [
							'requestId' => uniqid(),
							'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
						],
					],
					'attendees' => $attendees, // Add attendees here
				]);
				
				$event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
				
				return response()->json(['status' => 'success', 'getHangoutLink' => $event->getHangoutLink()]);

			} catch (\InvalidArgumentException $e) {

				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Google Meet','eventId'=>'']);
			} catch (\Exception $e) {
				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Google Meet','eventId'=>'']);
			}

			
		}elseif((int)$request->currentId==5){
			try {
				//  $interviewTime = @$request->interviewTime;
				//  $eventStartTime = $request->interviewDate.' '.$interviewTime;
				$eventStartTime = $request->interviewDate.' '.$request->interviewTime;

				
				$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
				if($defaultTimeFormat==1){
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}else{
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}
				$interviewDateCarbon = \Carbon\Carbon::parse($interviewDate);

				$twoHoursBefore = $interviewDateCarbon->copy()->subHours(2)->format('Y-m-d H:i:s');
				$twoHoursAfter = $interviewDateCarbon->copy()->addHours(2)->format('Y-m-d H:i:s');

				
				$interviewDate = $interviewDateCarbon->toDateString();

				$conflictingMeetingIds = DB::table('team_meeting_schedule')->whereDate('interview_date', $interviewDate)->pluck('meeting_link');

				// dd($twoHoursBefore,$twoHoursAfter,$conflictingMeetingIds);

				$google_meeting_lists = DB::table('team_meeting_lists')->where('status','=',1)->whereNotIn('meeting_link', $conflictingMeetingIds)->first();
		

				//  $google_meeting_lists = DB::table('google_meeting_lists')
				// 	->where(function ($query) use ($threeHoursBefore, $fourHoursAfter) {
				// 		$query->whereNull('interview_date')
				// 			->orWhereNotBetween('interview_date', [$threeHoursBefore, $fourHoursAfter]);
				// 	})
				// 	->first();


				// $google_meeting_lists = DB::table('google_meeting_lists')->first();

				if($google_meeting_lists){
					return response()->json(['status' => 'success', 'getHangoutLink' => @$google_meeting_lists->meeting_link]);
				}else{
					return response()->json(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting','eventId'=>'']);
				}

				


				$eventStartTime = $request->interviewDate.' '.$request->interviewTime; // Replace with actual start time
				$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
				if($defaultTimeFormat==1){
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}else{
					$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
				}
				$defaultTimezone = date_default_timezone_get();
				
				$client = new Google_Client();

				$client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Ensure this file exists

				//ats-jero-works
				// $client->setAuthConfig(storage_path('client_secret-ats-jero-works.apps.googleusercontent.com.json')); // Ensure this file exists

				$client->addScope(Google_Service_Calendar::CALENDAR);
				$client->setAccessType('offline');
				$client->setPrompt('consent'); // Ensures refresh token is generated
				
				// Retrieve stored access and refresh token
				$accessToken = session('google_access_token');
				$refreshToken = session('google_refresh_token'); 
				
				if ($accessToken) {
					$client->setAccessToken($accessToken);
				
					// Refresh token if access token is expired
					if ($client->isAccessTokenExpired()) {
						if ($refreshToken) {
							$newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
							session(['google_access_token' => $newAccessToken]);
				
							if (isset($newAccessToken['refresh_token'])) {
								session(['google_refresh_token' => $newAccessToken['refresh_token']]); // Store refresh token
							}
						} else {
							return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Teams Meeting','eventId'=>'']);
							// return response()->json(['status' => 'expired', 'error' => 'Session expired, please login again.'], 401);
						}
					}
				} else {
					return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Teams Meeting','eventId'=>'']);
					// return redirect('/google/auth')->with('error', 'Please authenticate with Google.');
				}
				
				$service = new Google_Service_Calendar($client);
				
				
				$startDate = date('c', strtotime($interviewDate));
				$endDate = date('c', strtotime($interviewDate . ' +1 hour'));

				$attendees = [
					['email' => 'vibhu2103m@gmail.com'],
					['email' => 'rajveer@4bell.com'],
				];
				
				$event = new Google_Service_Calendar_Event([
					'summary' => 'Interview Schedule',
					'start' => ['dateTime' => $startDate, 'timeZone' => $defaultTimezone],
					'end' => ['dateTime' => $endDate, 'timeZone' => $defaultTimezone],
					'conferenceData' => [
						'createRequest' => [
							'requestId' => uniqid(),
							'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
						],
					],
					'attendees' => $attendees, // Add attendees here
				]);
				
				$event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
				
				return response()->json(['status' => 'success', 'getHangoutLink' => $event->getHangoutLink()]);

			} catch (\InvalidArgumentException $e) {

				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Teams Meeting','eventId'=>'']);
			} catch (\Exception $e) {
				return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' =>  'Teams Meeting','eventId'=>'']);
			}
	 }

		// dd( $event->getHangoutLink());
	}
	public function changeEventOrganizer(Request $request, $eventId)
	{
		$newOrganizerEmail = '';
		$defaultTimezone = date_default_timezone_get();

		// Initialize the Google Client
		$client = new GoogleClient();
		// $client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Adjust the path as needed
		$client->addScope(Calendar::CALENDAR);
		// $client->setAccessType('offline');

		// Set the access token
		$accessToken = session('google_access_token');
		$client->setAccessToken($accessToken);

		// Refresh the access token if expired
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			session(['google_access_token' => $client->getAccessToken()]);
		}

		// Initialize the Calendar Service
		$calendarService = new Calendar($client);

		try {
			// Retrieve the original event
			$originalEvent = $calendarService->events->get('primary', $eventId);

			// dd($originalEvent);

			// Prepare the new event details
			$newEvent = new Google_Service_Calendar_Event([
				'summary' => $originalEvent->getSummary(),
				'description' => $originalEvent->getDescription(),
				'location' => $originalEvent->getLocation(),
				'start' => $originalEvent->getStart(),
				'end' => $originalEvent->getEnd(),
				'attendees' => $originalEvent->getAttendees(),
				'conferenceData' => $originalEvent->getConferenceData(),
				'creator' => [
					'email' => $newOrganizerEmail,
				],
				'organizer' => [
					'email' => $newOrganizerEmail,
				],
			]);

			// Insert the new event into the calendar
			$newEvent = $calendarService->events->insert('primary', $newEvent, [
				'conferenceDataVersion' => 1,
				'sendUpdates' => 'all'
			]);

			// Delete the original event
			$calendarService->events->delete('primary', $eventId);

			// Return the link of the newly created event
			$newHangoutLink = $newEvent->getHangoutLink();
			$newHtmlLink = $newEvent->htmlLink;

			return response()->json([
				'newLink' => $newHangoutLink,
				'newHtmlLink' => $newHtmlLink,
			]);

		} catch (\Exception $e) {
			// Handle exceptions
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
	public function storeSchedule(Request $request)
	{

		// echo '<pre>'; print_r($_REQUEST); exit; 
		// dd(json_encode(@$request->cc_email));
		$AddJobWiseCondidate = AddJobWiseCondidate::find($request->wiseId);
		// dd($AddJobWiseCondidate->master_status_id);
		// $client = new GoogleClient();
        // $client->setClientId(config('google.client_id'));
        // $client->setClientSecret(config('google.client_secret'));
        // $client->setRedirectUri(config('google.redirect_uri'));
        // $client->addScope(Calendar::CALENDAR);

        // // Generate the Google OAuth URL
        // $authUrl = $client->createAuthUrl();
        // //    dd( $authUrl);
        // // Redirect to Google's OAuth 2.0 server
        // return redirect($authUrl);
        //  dd($request->all());

		$application_id = $interview_type = $interviewDate = $master_status_id = $child_status_id = $interview_mode = $interview_mode_lavel = $interviewers = $panellist = '';
		$application_id = @$request->application_id;
		$interview_type = @$request->interview_type;
		$interviewDate = @$request->interviewDate;
		$interviewTime = @$request->interviewTime;
		$interview_mode = @$request->interview_mode;
		$interview_mode_lavel = @$request->interview_mode_lavel;
		$master_status_id = @$request->master_status_id;
		$child_status_id = @$request->child_status_id;
		$interviewers = @$request->interviewers;
		$panellist = @$request->panellist;
		$mailstatus = @$request->mailstatus;
		$email_template = @$request->email_template_set;
		$timezone = @$request->timezone;

		// CC emails come in as a comma-separated string e.g. "Pooja@yopmail.com , Shobha@4bell.com , ...".
		// Explode, trim, lowercase, keep only valid emails, and drop duplicates before storing.
		$ccEmailArr   = [];
		$ccEmailClean = '';
		if (!empty($request->cc_email)) {
			foreach (explode(',', $request->cc_email) as $rawEmail) {
				$oneEmail = strtolower(trim($rawEmail));
				if ($oneEmail !== '' && filter_var($oneEmail, FILTER_VALIDATE_EMAIL)) {
					$ccEmailArr[] = $oneEmail;
				}
			}
			$ccEmailArr   = array_values(array_unique($ccEmailArr));
			$ccEmailClean = implode(',', $ccEmailArr);
		}

		// dd($interviewDate,$interviewTime);
		    $eventStartTime = $request->interviewDate.' '.$interviewTime; // Replace with actual start time
		    $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
			 if($defaultTimeFormat==1){
				$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
			 }else{
				$interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
			 }



		$eventId = 12;
		if($interview_mode==2){
			$eventId = 37;
		}
		$coinsRequ = 1;
		$companyId = (int) auth()->user()->company_id;
		$packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
		if($packageStatus->status){
			return Reply::dataOnly(['status' => 'coinCheck', 'message' => $packageStatus->message,'company_coins'=> $packageStatus->company_coins]);
		}
		
		$coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');  
		if($coinsDeductions->status){
			return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
		}
		
		

		// INTERVIEW SCHEDULE 
		$interviewSchedule = new InterviewSchedule();
		$interviewSchedule->job_application_id = $application_id;
		if (!empty($request->interview_type)) {
			$interviewSchedule->reschedule = $interview_type;
		}
		if (!empty($interview_mode)) {
			$interviewSchedule->interview_mode = $interview_mode;
		}
		if (!empty($interview_mode_lavel)) {
			$interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
		}
		if (!empty($master_status_id)) {
			$interviewSchedule->stage = ($AddJobWiseCondidate->master_status_id) ? $AddJobWiseCondidate->master_status_id : $master_status_id;
		}
		if (!empty($child_status_id)) {
			$interviewSchedule->substage = $child_status_id;
		}
		if (!empty($panellist)) {
			$interviewSchedule->interviewers = $panellist;   //implode(',', $panellist);
		}
		if (!empty($interview_created_by)) {
			$interviewSchedule->interview_created_by = $this->user->id;
		}

		$interviewSchedule->schedule_date = $interviewDate;
		$interviewSchedule->schedule_to_time = $interviewTime;
		$interviewSchedule->interview_job_id = $AddJobWiseCondidate->job_id;
		$interviewSchedule->interview_job_wise_con_id = $AddJobWiseCondidate->id;
		$interviewSchedule->mailstatus = $mailstatus;
		$interviewSchedule->email_template = $email_template;
		$interviewSchedule->company_id = $companyId;
		$interviewSchedule->url_link = $request->url;
		$interviewSchedule->timezone_abbreviation = $timezone;
		$interviewSchedule->other_email = $ccEmailClean; // cleaned, de-duplicated CC email list
		$interviewSchedule->save();


		$updateParams = [
					'index' => ELASTIC_CANDIDATES,
					'id' => $request->wiseId,
					'retry_on_conflict' => 5,
					'body' => [
						'doc' => [
							'schedule_to_time' => $interviewSchedule->schedule_to_time,
							'timezone_abbreviation' => $interviewSchedule->timezone_abbreviation,
							'schedule_date' => ($interviewSchedule->schedule_date) ? Carbon::parse($interviewSchedule->schedule_date)->toIso8601String() : null
						],
						'doc_as_upsert' => true,
						'detect_noop' => false
					],
					'refresh' => true // Force immediate refresh
				];
	
		$this->elasticService->getClient()->update($updateParams);
		

		$interviewSchedule_id = '';
		$interviewSchedule_id = $interviewSchedule->id;
      if($interview_mode==2 && $interview_mode_lavel==2){
		DB::table('google_meeting_schedule')->insert(['meeting_link' =>$request->url,'user_id' =>auth()->user()->id,'company_id' =>auth()->user()->company_id,'interview_job_wise_con_id' =>$AddJobWiseCondidate->id,'interview_date' =>$interviewDate]);
	  }
	  if($interview_mode==2 && $interview_mode_lavel==5){
		DB::table('team_meeting_schedule')->insert(['meeting_link' =>$request->url,'user_id' =>auth()->user()->id,'company_id' =>auth()->user()->company_id,'interview_job_wise_con_id' =>$AddJobWiseCondidate->id,'interview_date' =>$interviewDate]);
	  }

		// mail to candidate for inform interview schedule
		if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
			//Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));	
		}



		
		


		$jobApplication = JobApplication::find($request->application_id);

		$AddJobWiseCondidate = AddJobWiseCondidate::find($request->wiseId);

		$date = date('Y-m-d H:i:s');
		$insert_type = $request->interview_type;

		// dd($request->master_status_id);

		$statusRow = ApplicationStatus::find($request->master_status_id);   



		$interview_mode_lavel = '';
		$interview_mode_lavel = @$request->interview_mode;
		// dd($interview_mode_lavel,$request->all());
		if ($interview_mode_lavel > 0) {
			$intModeLbl = DB::table('interview_mode_lavel')
				->select('interview_mode_lavel.name')
				->where('interview_mode_lavel.id', $interview_mode_lavel)
				->first();
			$interview_mode_lavel = $intModeLbl->name;
		}




		/*$values 				=	array(
												  'owner_id' 						=> auth()->user()->id, 
												  
												  'application_id' 				=> @$request->application_id, 
												  'application_email' 			=> @$jobApplication->email, 
												  'job_id' 						=> @$jobApplication->job_id, 
												  'process_id' 					=> @$request->master_status_id, 
												  'status_name' 					=> @$statusRow->name,

												  'note'			 				=> 'Interview Schedule', 
												  'insert_type' 					=> @$insert_type, 
												  'create_at' 					=> @$date, 
												  'update_at' 					=> @$date, 
												  'interview_schedule_date' 		=> @$interviewDate,
												  'interview_mode' 				=> $interview_mode,
												  'timeline_type' 				=> 1,
												  'interview_mode_child_level' 	=> $interview_mode_lavel

											  );		
			  if(!empty($request->url) && $request->url != ''){
				  $values['interview_mention_link'] = @$request->url;	
			  }
			  if(!empty($request->panellist) && $request->panellist != ''){
				  $values['interviewers'] 	=	@$request->panellist;	
			  }						
			  if ($request->child_status_id > 0) 
			  {
				  $childStatus = '';
				  $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
				  $values['process_child_name'] = @$childStatus->name;
			  }
			  if (!empty($panellist)) {           
				  $values['interviewers'] 		= $panellist; 
			  }
			  $values['interview_schedules_id'] 	= $interviewSchedule_id; 
			  DB::table('timeline')->insert($values);*/




		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 3,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			//'master_process_name' 		=>	@$jobApplication->job_id, 
			//'child_process_name' 			=>	@$master_status, 

			'application_id' => @$request->application_id ? @$request->application_id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$AddJobWiseCondidate->job_id ? @$AddJobWiseCondidate->job_id : $request->jobId,
			'note' => 'Interview Schedule',
			'create_at' => @$date ? @$date : NULL,

			'update_at' => @$date ? @$date : NULL,

			'interview_schedule_date' =>  $interviewDate,
			'interview_mode' => $interview_mode,
			//'timeline_type' 				=>	1,
			'interview_mode_child_level' => ($request->interview_mode != 3) ? $interview_mode_lavel : '',
			//'interview_from_time'			=>	
			//'interview_to_time'			=>							
		);

		if ($jobApplication->master_status_id > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if ($jobApplication->child_status_id > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
		if (!empty(@$comment)) {
			$values['note'] = @$comment;
		}

		if (!empty($request->url) && $request->url != '') {
			$values['interview_mention_link'] = @$request->url;
		}
		if (!empty($request->panellist) && $request->panellist != '') {
			$values['interviewers'] = @$request->panellist;
		}
		$values['interview_schedules_id'] = $interviewSchedule_id;

		$values['timezone_abbreviation'] = $timezone;
		$values['interview_to_time'] = $interviewTime;

		// DB::table('timeline')->insert($values);
		GeneralMethod::UpdateTimeline($values);
        $jobIdGet = @$AddJobWiseCondidate->job_id ? @$AddJobWiseCondidate->job_id : $request->jobId;

		// $tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet,'status' => 1,'application_id' => @$request->application_id);
		// DB::table('tb_ranks')->insert($tb_ranks);


		


		$InterviewSchedule = InterviewSchedule::join('add_job_wise_condidates','add_job_wise_condidates.id','=','interview_schedules.interview_job_wise_con_id')
		->join('companies','companies.id','=','interview_schedules.company_id')
		->join('clients','clients.company_id','=','companies.id')
		->join('client_location','client_location.client_id','=','clients.id')
		->join('jobs','jobs.id','=','interview_schedules.interview_job_id')
		->join('job_applications','job_applications.id','=','interview_schedules.job_application_id')
		->join('updatetimeline','updatetimeline.interview_schedules_id','=','interview_schedules.id')
		->select('interview_schedules.*','updatetimeline.interview_mention_link','updatetimeline.interview_mode_child_level as mode_child','companies.company_name','companies.description','companies.website','client_location.address as c_address','job_applications.full_name','job_applications.email as cond_email','job_applications.current_designation','jobs.employment_type','jobs.title','jobs.job_description','jobs.id as jobID','jobs.total_positions')
		->where('interview_schedules.id','=',$interviewSchedule_id)->first();
		// dd($InterviewSchedule,$interviewSchedule_id);

		$company_or_client = GeneralMethod::companyDetailsByClient($InterviewSchedule->interview_job_id);
		$interview_mode	= "";
		if($InterviewSchedule->interview_mode > 0){
			$interview_mode_Arr		= 	GeneralMethod::interview_mode();
			$interview_mode			= 	$interview_mode_Arr[@$InterviewSchedule->interview_mode];				
		}
		$interview_mode_child_level	=	$InterviewSchedule->interview_mode_child_level;	
			if($InterviewSchedule->interview_mode==3){
			$interview_mode_child_level	=	@$InterviewSchedule->interview_mention_link;	
		}


		    $jobId = $InterviewSchedule->interview_job_id;
			$lastInsertedId = $InterviewSchedule->interview_job_wise_con_id;
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

				$partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
				->join('package_active', function ($join) {
					$join->on('users.company_id', '=', 'package_active.company_id')
						->where('package_active.emails_balance', '>', 0)
						->where('package_active.startdate', '<=', now()) 
						->where('package_active.enddate', '>=', now()) 
						->where('package_active.packages_status', 1); 
				})
				->where('job_assign.job_id', '=', $jobId)
				->pluck('users.email')
				->toArray();
		
				$recommendedDataList = DB::table('recommendation_relation');
				$recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
				$recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
				$recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
					$join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
					$join->on('job_applications.job_id', '=', 'jobs.id');
				});
				$recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
					$join->on('companies.id', '=', 'package_active.company_id')
						->where('package_active.emails_balance', '>', 0)
						->where('package_active.startdate', '<=', now()) 
						->where('package_active.enddate', '>=', now()) 
						->where('package_active.packages_status', 1);
				});
				$recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
				$recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
				$recommendedDataList = $recommendedDataList->groupBy('companies.id');
				$recommendedDataList = $recommendedDataList->pluck('companies.company_email');
				$recommendedDataList = $recommendedDataList->toArray();





		$data = [
			'InterviewDate' => $interviewDate,
			'InterviewMode' => $interview_mode.'('.$InterviewSchedule->mode_child.')',
			'InterviewAddress' => $InterviewSchedule->interview_mention_link,
			'CompanyName' => $InterviewSchedule->company_name,
			'CompanyLocation' => $InterviewSchedule->c_address,
			'Position' => $InterviewSchedule->current_designation,
			'EmploymentType' => ($InterviewSchedule->employment_type==1) ? 'Contract' : 'Full-time',			
			'ReqID' => "R-" . @$InterviewSchedule->jobID,
			'Reqname' => @$InterviewSchedule->title,
			'NoofEmployees' => $InterviewSchedule->total_positions,
			'OfficeTiming' => "...",
			'NoofWorkingDays' => "...",
			'WebsiteURL' => $InterviewSchedule->website,
			'Jobdescription' => $InterviewSchedule->job_description,
			'AboutOrganization' => $InterviewSchedule->description,
			'VendorTeam' => @$InterviewSchedule->company_name,
			'CompanyNameClientname' => $company_or_client,			
			"CandidateName" => $InterviewSchedule->full_name,
			"username" => auth()->user()->name,
			"mobile" => auth()->user()->mobile
		];
		// dd($data);
		config(['mail.from.name' => auth()->user()->name]);
		$cc_cond = array($InterviewSchedule->cond_email);
		$cc = array_merge($partnersData, $recommendedDataList,$cc_cond);
		

		if(count($cc) > 0){
			foreach($cc as $email){
				$client_company_id = 0;
				$useremail = DB::table('users')->select('*')->where('email',$email)->first();
				if (!empty($useremail)) {
					$client_company_id = @$useremail->company_id;
				}
				// dd($client_company_id);
				if (@$client_company_id > 0) {
					$event_name = 'emails';
					$event_table_name = 'job applications stage change';
					$event_table_id = @$client_company_id;
					GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
				}
			}
		}


		
		GeneralMethod::interviewTrackerAddEditUpload($interviewSchedule_id);

		$cachekeys = [
					'allapptracker_version_user_'.$this->user->id,
					'candidateDetails_version_user_'.$this->user->id,
					'allinterviewschedule_version_user_'.$this->user->id,
					'allinterviewbenchcandidates_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}

		



		return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);




	}
	public function emailSchedule(Request $request)
	{
	   // dd($request->all());

		$job = Job::find($request->jobId);
		$companyOrClientName = GeneralMethod::companyOrClientName($job->client_id);
	    $subject =	@$request->subject;
	    
	    $cc =	(@$request->cc_email) ? @$request->cc_email : '';
	     $cc_email = [];
	    if (!empty ($cc)) {
		    $emails = explode(',', $request->cc_email); 
             // 			dd($emails);
            
		    foreach ($emails as $keymail=> $email) {
                 array_push($cc_email,str_replace(' ','',$email));
            }
		}
		
	    
	     // Define validation rules
            $rules = [
                'attachment' => 'file|max:2048', // 2048 KB = 2 MB
            ];
        
            // Define custom error messages (optional)
            $messages = [
                'attachment.max' => 'The file must not be larger than 2 MB.',
            ];
        
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules, $messages);
        
            // Check if validation fails
            if ($validator->fails()) {
                return Reply::dataOnly(['status' => 'faild', 'message' => 'The file must not be larger than 2 MB !']);
                // return redirect()->back()->withErrors($validator)->withInput();
            }

			// dd($request->all());
			 // Interview Details
			 $interviewLink = $request->url; // Interview link
			 $eventTitle = $request->subject;
			 $eventDescription = $request->editor1;
			 $timezone = $request->timezone;
			 $interviewTime = @$request->interviewTime;

			 $eventStartTime = $request->interviewDate.' '.$interviewTime; // Replace with actual start time

			 $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
			 if($defaultTimeFormat==1){
				$eventEndTime = date('d-m-Y h:i A', strtotime($eventStartTime . ' +1 hour'));
			 }else{
				$eventEndTime = date('d-m-Y H:i', strtotime($eventStartTime . ' +1 hour'));
			 }

			

			//  dd($eventStartTime,$eventEndTime);
    
	   
			$data = [
				'details' =>  @$request->editor1,
				'interviewLink' =>  @$interviewLink,
				'eventTitle' =>  @$eventTitle,
				'eventDescription' =>  @$eventDescription,
				'eventStartTime' =>  @$eventStartTime.' ('.$timezone.')',
				'eventEndTime' =>  @$eventEndTime.' ('.$timezone.')'
				
			];
			// dd($data);
	  
	
        $attachments = array();
        if ($request->hasFile('attachment')) {

                $file = $request->file('attachment');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Resumes/' . $filename;
				Storage::disk('s3')->put($filePath, file_get_contents($file));
				$fileUrl = Storage::disk('s3')->url($filePath);
				
				$attachments[] = [
							'path' => $fileUrl,
							'options' => []
						];
		}
		
		// 		$mail = Mail::to(auth()->user()->email);
				
		//         if (!empty($cc)) {
		//             $mail->cc($cc_email);
		//         }
        
          $senderName =  auth()->user()->name;
          $fromAddress = auth()->user()->email;
		  $company_id = auth()->user()->company_id;
          $to_email = @$request->to_email;
              if(!empty($to_email)){
				$checkMailPermision = GeneralMethod::mailPermission(2);
				if($checkMailPermision){
					// QueueMailService
					QueueNotificationMailService::dispatch(
						$to_email,
						$subject,
						'email.candidate.interview-tracker',
						$data,
						$attachments,
						$fromAddress,
						$senderName,
						$cc_email,
						$company_id
					)->onConnection('emails');
           
					// $result = $this->notificationMailService->sendEmail($to_email, $subject,'email.candidate.interview-tracker', $data, $attachments, $fromAddress, $senderName,$cc_email);
				}
              }               
          
        
        // $mail->send(new InterviewTrackerMail((object) $data, $subject, $attachments));

		return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);
		
    	// below code old 

		$application_id = $master_status_id = $child_status_id = $interview_type = $interviewDate = $interview_mode = $interview_mode_lavel = $panellist = $interviewers = '';
		$email_template = $from_email = $to_email = $cc_email = $subject = $editor = '';
		$application_id = @$request->application_id;
		$master_status_id = @$request->master_status_id;
		$child_status_id = @$request->child_status_id;
		$interview_type = @$request->interview_type;
		$interviewDate = @$request->interviewDate;
		$interview_mode = @$request->interview_mode;
		$interview_mode_lavel = @$request->interview_mode_lavel;
		$panellist = @$request->panellist;

		$email_template = @$request->email_template;
		$from_email = @$request->from_email;
		$to_email = @$request->to_email;
		$cc_email = @$request->cc_email;
		$subject = @$request->subject;
		$editor = @$request->editor;

		// INTERVIEW SCHEDULE
		$interviewSchedule = new InterviewSchedule();
		$interviewSchedule->job_application_id = $application_id;
		if (!empty($master_status_id)) {
			$interviewSchedule->stage = $master_status_id;
		}
		if (!empty($child_status_id)) {
			$interviewSchedule->substage = $child_status_id;
		}
		if (!empty($request->interview_type)) {
			$interviewSchedule->reschedule = $interview_type;
		}
		$interviewSchedule->schedule_date = $interviewDate;
		if (!empty($interview_mode)) {
			$interviewSchedule->interview_mode = $interview_mode;
		}
		if (!empty($interview_mode_lavel)) {
			$interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
		}
		if (!empty($panellist)) {
			$interviewSchedule->interviewers = $panellist;   //implode(',', $panellist);
		}
		if (!empty($interview_created_by)) {
			$interviewSchedule->interview_created_by = $this->user->id;
		}
		$interviewSchedule->save();

		$interviewSchedule_id = '';
		$interviewSchedule_id = $interviewSchedule->id;


		// UPDATE JOB APPLICATION
		/*$intScheduleId 		= '';
			  $intScheduleId 		= $interviewSchedule->id;
			  $jobApplication 	= JobApplication::find($request->application_id);
			  if ($request->masterStatus > 0) {
				  $jobApplication->status_id = $request->masterStatus;
				  $jobApplication->master_status_id = $request->masterStatus;
			  }
			  if ($request->child_status > 0) {
				  $jobApplication->child_status_id = $request->child_status;
			  }
			  $jobApplication->save();*/


		// mail to candidate for inform interview schedule
		if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
			//Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

		}
		$jobApplication = JobApplication::find($request->application_id);
		$date = date('Y-m-d H:i:s');
		$insert_type = $request->interview_type;
		$statusRow = ApplicationStatus::findOrFail($request->master_status_id);
		$values = array(
			'owner_id' => auth()->user()->id,
			'application_id' => @$request->application_id,
			'application_email' => @$jobApplication->email,
			'job_id' => @$jobApplication->job_id,
			'process_id' => @$request->master_status_id,
			'status_name' => @$statusRow->name,

			'note' => 'Interview Schedule',
			'insert_type' => @$insert_type,
			'create_at' => @$date,
			'update_at' => @$date,
			'interview_schedule_date' => @$interviewDate,
			'interview_mode' => $interview_mode,
			'timeline_type' => 1,

		);

		/*if ($request->child_status_id > 0) 
					{
						$childStatus = '';
						$childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
						$values['process_child_name'] = @$childStatus->name;
					}		
					if (!empty($panellist)) {           
						$values['interviewers'] = $panellist; 
					}*/

		if (!empty($request->url) && $request->url != '') {
			$values['interview_mention_link'] = @$request->url;
		}
		if (!empty($request->panellist) && $request->panellist != '') {
			$values['interviewers'] = @$request->panellist;
		}
		if ($request->child_status_id > 0) {
			$childStatus = '';
			$childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
			$values['process_child_name'] = @$childStatus->name;
		}

		/*if (!empty($panellist)) {           
								 $values['interviewers'] 		= $panellist; 
							 }*/
		$values['interview_schedules_id'] = $interviewSchedule_id;




		DB::table('timeline')->insert($values);






		// START SEND EMAIL 
		//$file 			=	$path.$filename;
		//$content 			=	file_get_contents( $file);
		//$content 			=	chunk_split(base64_encode($content));

				//$file_name 		=	basename($file);


		$from_name = "Vibhanshu";
		$from_mail = "vibhanshu@4bel.com";
		$replyto = "support@4bell.com";
		$uid = md5(uniqid(time()));
		$message = $editor;
		$mailto = "abu@4bell.com";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: $from_mail' . "\r\n";
		$headers .= 'Cc: $replyto' . "\r\n";

		mail($mailto, $subject, $message, $headers);


		// END SEND EMAIL		


		return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);




	}
	public function stage_body_content($id)
	{
		$current_company_id = auth()->user()->company_id;
		$this->qualification = '';
		$this->application = '';

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = JobApplication::select(
			'job_applications.id',
			'job_applications.submitted_by',
			'job_applications.company_id',
			'job_applications.np_id',
			'job_applications.serving_notice_period_mode',
			'job_applications.full_name',
			'job_applications.email',
			'job_applications.phone',
			'job_applications.secondary_mobile',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.rating',
			'job_applications.skype_id',
			'job_applications.cover_letter',
			'add_job_wise_condidates.job_id',
			'job_applications.status_id',
			'job_applications.is_viewed',
			'job_applications.column_priority',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.deleted_at',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.passport',
			'job_applications.current_ctc_cur',
			'job_applications.current_ectc_cur',
			'job_applications.current_ctc_mode',
			'job_applications.current_ectc_mode',
			'job_applications.ctc',
			'job_applications.ectc',

			'job_applications.current_location',
			'job_applications.current_designation',
			'job_applications.current_company',
			'job_applications.process_id',
			'job_applications.qualification',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'job_applications.secondrary_email',
			'job_applications.secondary_mobile',
			'job_applications.skills',
			'clients.name as client_name',
			'jobs.title',
			'jobs.max_sal_mode as jobMode',
			'jobs.max_salary_currency as jobCurrency',
			'users.name as posted_by',
			'master_status.name as master_status_name',
			'job_applications.proposed_ctc',
			'job_applications.any_offer',
			'job_applications.document',
			'job_applications.sub_for_approval',
			'job_applications.offer_approved',
			'job_applications.offer_release',
			'job_applications.offer_acceptance',
			'job_applications.joined',
			'job_applications.joined_date',
			'job_applications.ctc_yearly',
			'job_applications.ectc_yearly',
			'job_applications.city',
			'job_applications.state',
			'job_applications.country',
			'job_applications.city_id',
			'state_list.state as state_name',
			'job_applications.condidate_type',
			'job_applications.bill_rate_amount',
			'job_applications.bill_rate_mode',
			'job_applications.bill_rate_currency',
			'users.name as submitted_by_name',
			'users.company_id as submitted_by_company_id',
			'users.image as posted_by_image',
			'add_job_wise_condidates.job_id as jobId',
			'add_job_wise_condidates.thirdparty_company_id',
			'add_job_wise_condidates.id as job_wise_condidates_id'
		)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})->leftJoin('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			// ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
		// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');

         
		$this->application = $this->application->where('add_job_wise_condidates.id', $id);
		$this->application = $this->application->first();

		// dd($this->application);

		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}

		$submitted_by_company_id = $this->application->submitted_by_company_id;
		$this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

		//$this->master_status 				=	DB::table('master_status')->select('*')->orderBy('master_status.priority', 'ASC')->get();

		$thirdparty_company_id = $this->application->thirdparty_company_id;
		if ($thirdparty_company_id == $current_company_id) {
			$this->master_status = DB::table('master_status')->select('*')->where('master_status.direct_client', '=', 1)->orderBy('master_status.direct_client_priority', 'ASC')->get();
		} else {
			$this->master_status = DB::table('master_status')->select('*')->where('master_status.custom_client', '=', 1)->orderBy('master_status.priority', 'ASC')->get();
		}


		$master_status_id = $this->application->master_status_id;
		$this->candidate_status_Arr = DB::table('candidate_status')
			->select('*')
			->where('parent', $master_status_id)
			->where('company_id', $current_company_id)
			->orderBy('candidate_status.priority', 'ASC')
			->get();

		// dd($master_status_id);


		$this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
			->where('job_application_id', $this->application->id)
			->orderBy('id', 'DESC')
			->get();

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $this->application->job_id;
		$this->job_id = $job_id;

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;


         
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];
		 $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$this->application->jobId)->first();
        $jobMode = $jobData->jobMode ?? @$this->application->current_ctc_mode;
		$jobCurrency = $jobData->jobCurrency ?? @$this->application->current_ctc_cur;
		$symbols = $symbolsValue[$jobCurrency] ?? "INR";
		
		$current_ctc_cur = '';
		$current_ctc_cur = $currencySymbole[@$jobCurrency];

		$current_ectc_cur = '';
		$current_ectc_cur = $currencySymbole[@$jobCurrency];
		
        $current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
        $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);

		$payFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ctc_yearly,$symbols,$jobMode);
		$billFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ectc_yearly,$symbols,$jobMode);
		$ctc = '';
		$ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
		$ectc = '';
		$ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

		$this->current_ctc = @$current_ctc_cur . @$ctc . '/' . @$current_ctc_mode;

		$this->expected_ctc = @$current_ectc_cur . @$ectc . '/' . @$current_ectc_mode;



		$country = '';
		$this->row_country_name = '';

		if (empty($this->application->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->application->country)) {
				if (is_numeric($this->application->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->application->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}

		$this->row_state_name = '';
		if (empty($this->application->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->application->country == 101) {
				$this->row_state_name = $this->application->state_name;
			} else {
				$state = $this->application->state;
				if (is_numeric($this->application->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}
			}
		}

		$this->row_city_name = '';
		if ($this->application->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$view = view('service_company.job-applications.stage_body_content', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);


	}
	public function child_stage($id)
	{
		$company_id = "";
		$company_id = auth()->user()->company_id;

		$master_status_id = $id;
		// dd($master_status_id,$company_id);
		$candidate_status_Arr = DB::table('candidate_status')
			->select('*')
			->where('parent', $master_status_id)
			->where('company_id', $company_id)
			->orderBy('candidate_status.priority', 'ASC')
			->get();

		$html = '';
		// dd($candidate_status_Arr);
		if (!empty($candidate_status_Arr)) {
			foreach ($candidate_status_Arr as $cstatus) {
				$csdMeta = GeneralMethod::csdSubStatusMeta(@$cstatus->name);
				$csdName = e(@$cstatus->name);
				$csdDesc = $csdMeta['desc'] !== '' ? '<div class="ds">' . e($csdMeta['desc']) . '</div>' : '';

				// Same .csd-sub-tile markup/classes the two body-content
				// Blade partials render initially — this AJAX response (used
				// by both the plain and client-gated drawers) must match so
				// switching stages mid-drawer looks identical to the first
				// paint. onclick calls the shared, flow-aware csdSelectSub().
				// csdMeta['icon'] is inline SVG inner-markup (not a Font
				// Awesome class — this app only has FA 4.7.0 loaded, which
				// lacks the icon names/prefix a newer cheat-sheet suggests).
				$html .= '<div class="csd-sub-tile" onclick="csdSelectSub(this)">
									<input type="radio" class="d-none" name="sub_stages" value="' . @$cstatus->id . '" id="sub_stage_' . @$cstatus->id . '">
									<span class="ic" style="background:' . $csdMeta['bg'] . ';color:' . $csdMeta['color'] . ';"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $csdMeta['icon'] . '</svg></span>
									<div class="txt">
										<div class="nm">' . $csdName . '</div>
										' . $csdDesc . '
									</div>
								</div>';
			}
		}
		// dd($html);
		return Reply::dataOnly(['status' => 'success', 'html' => $html]);

	}

	/**
	 * Builds the "one stage at a time" validation message shared by
	 * stage_submit() and client_stage_submit() whenever a candidate is
	 * moved more than one step ahead, or more than one step backward, in
	 * the stage sequence. Rather than just saying the attempted move is
	 * blocked, it tells the user exactly which stages ARE reachable from
	 * where the candidate currently sits (previous / next / Rejected),
	 * since that's the actionable information they need next.
	 *
	 * @param array  $stages       Same order map ($id => ['display' => ..., 'order' => ...]) already built in the caller.
	 * @param int    $currentOrder Order value of the candidate's current stage.
	 * @param string $currentName  Display name of the candidate's current stage.
	 * @param string $targetName   Display name of the stage the user attempted to jump to.
	 */
	private function stageSequenceGuardMessage(array $stages, int $currentOrder, string $currentName, string $targetName): string
	{
		$prevName = null;
		$nextName = null;
		foreach ($stages as $sid => $s) {
			if ($s['order'] === $currentOrder - 1) {
				$prevName = $s['display'];
			}
			// Rejected (7 in both flows, 9 in the client flow's agency-only
			// bucket) is always offered separately below — excluded here so
			// it's never mentioned twice.
			if ($s['order'] === $currentOrder + 1 && !in_array((int) $sid, [7, 9], true)) {
				$nextName = $s['display'];
			}
		}

		$options = [];
		if ($prevName) {
			$options[] = "<b style='color:#F59E0B;'>{$prevName}</b> (previous stage)";
		}
		if ($nextName) {
			$options[] = "<b style='color:#3B82F6;'>{$nextName}</b> (next stage)";
		}
		$options[] = "<b style='color:#EF4444;'>Rejected</b>";

		$last = array_pop($options);
		$optionsText = count($options) ? implode(', ', $options) . ' or ' . $last : $last;

		return "Candidates move through hiring stages one step at a time.<br>"
			. "From <b style='color:#181c32;'>{$currentName}</b>, you can move to {$optionsText}.<br>"
			. "Jumping directly to <b style='color:#181c32;'>{$targetName}</b> isn't supported — please select an adjacent stage to continue.";
	}

	public function stage_submit(Request $request)
	{


		$company_id = "";
		$company_id = auth()->user()->company_id;

		$application_id = $master_status = $sub_stages = $comment = $addJobWiseCondidate_id = "";
		$prev_process_name = $prev_process_child_name = '';
		$application_id = @$request->application_id;
		$addJobWiseCondidate_id = @$request->job_wise_condidates_id;
		$master_status = @$request->master_status;
		$sub_stages = @$request->sub_stages;
		$comment = @$request->comment;
		// dd($addJobWiseCondidate_id);
		$currency = GeneralMethod::currency();
		$salaryModeOffer = GeneralMethod::salaryModeOffer();


		$userId = auth()->id(); 
		$isValid = DB::table('add_job_wise_condidates as ajwc')
			->leftJoin('job_assign as ja', function ($join) {
				$join->on('ja.job_id', '=', 'ajwc.job_id')
					->on('ja.company_id', '=', 'ajwc.company_id');
			})
			->where('ajwc.id', $addJobWiseCondidate_id) // Candidate row id
			->where(function ($query) use ($userId) {
				$query->where('ja.spok_user_id', $userId)
					->orWhere('ajwc.submitted_by', $userId);
			})
			->exists();

		if (!$isValid) {

			$validations = (object)[
				'message' => "You are not authorized to update this candidate's status. Only the assigned Captain or the submitting user can update this candidate's status."
			];

			return Reply::dataOnly([
				'status'  => 'candidatevalidation',
				'message' => $validations->message
			]);
		}


        		                    // $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        							// if($chkAdminRole){
        							    
        
        		                    //       $candidateJoineddTeam = -30;
        							// 	   $candidateJoineddUser = -1500;
        		                    //        $teamRankElastic = [
        							// 		'company_id'=>2,
        							// 		'user_id'=>5,
        							// 		'candidate_id'=>$addJobWiseCondidate_id,
        							// 		'profileUpload'=>0,
        							// 		'interviewScheduled'=>0,
        							// 		'interviewNoShow'=>0,
        							// 		'offerReleased'=>0,
        							// 		'candidateJoined'=>0,
        							// 		'jobsPosted'=>0,
        							// 		'feedbackGivenOnCVs'=>0,
        							// 		'postJobToVendorMarket'=>0,
        							// 		'outsourceJobsToVendors'=>0,
        							// 		'listBenchCandidates'=>0,
        							// 		'respondToBenchRequests'=>0,
        							// 		'interestedCandidates'=>0,
        							// 		'notInterestedCandidates'=>0,
        							// 		'teamCaptainInterviewScheduled'=>0,
        							// 		'teamCaptainInterviewNoShow'=>0,
        							// 		'teamCaptainOfferReleased'=>0,
        							// 		'teamCaptainCandidateJoined'=>$candidateJoineddTeam
        							// 	];
            						// 		GeneralMethod::teamRankElastic($teamRankElastic);
            
            						// 		$teamRankElastic = [
            						// 							'company_id'=>2,
            						// 							'user_id'=>13,
            						// 							'candidate_id'=>$addJobWiseCondidate_id,
            						// 							'profileUpload'=>0,
            						// 							'interviewScheduled'=>0,
            						// 							'interviewNoShow'=>0,
            						// 							'offerReleased'=>0,
            						// 							'candidateJoined'=>$candidateJoineddUser,
            						// 							'jobsPosted'=>0,
            						// 							'feedbackGivenOnCVs'=>0,
            						// 							'postJobToVendorMarket'=>0,
            						// 							'outsourceJobsToVendors'=>0,
            						// 							'listBenchCandidates'=>0,
            						// 							'respondToBenchRequests'=>0
            						// 						];
            						// 		GeneralMethod::teamRankElastic($teamRankElastic);
            
            						// 		dd('Okay');
        						    // }

       $addJobWiseCondidate = AddJobWiseCondidate::find($addJobWiseCondidate_id);
 		$offer_id = null;
		$exists = $this->elasticService->getClient()->exists([
			'index' => ELASTIC_CANDIDATES,
			'id' => $addJobWiseCondidate_id
		]);	
		if ($exists) {
				$params = [
						'index' => ELASTIC_CANDIDATES,
						'id'    =>$addJobWiseCondidate_id,
					];
				
				$response = $this->elasticService->getClient()->get($params);
				$data = (object) $response['_source'];

				

				if(($master_status != 6) && ($data->job_master_status_id==6)){
                       
					$status_row = DB::table('candidate_status')->select('*')->where('id','=',$data->childStatus)->first();
					if($status_row){
						if(@$status_row->name=='Onboarded'){

							$AddJobWiseCondidate = AddJobWiseCondidate::find($addJobWiseCondidate_id);

							$spok_user_id_rows = DB::table('job_assign')
							->where('job_id', $AddJobWiseCondidate->job_id)
							->get(['spok_user_id', 'company_id'])->toArray();
							//  dd($spok_user_id_rows);
							// Step 2: Build result as [company_id => spok_user_id]
							$teamCaptainId = [];

							foreach ($spok_user_id_rows as $row) {
								if (!is_null($row->spok_user_id) && $row->spok_user_id != $AddJobWiseCondidate->submitted_by) {
									$teamCaptainId[$row->company_id][] = $row->spok_user_id;
								}
							}
							// dd($teamCaptainId);

							// Step 3: Remove duplicates and reindex inner arrays
							foreach ($teamCaptainId as $companyId => $spokIds) {
								$teamCaptainId[$companyId] = array_values(array_unique($spokIds));
							}

							$candidateJoineddUser = 0;
							$candidateJoineddTeam = 0;
							$candidateJoineddUserbackout = -1500;
							$candidateJoineddTeambackout = -30;

							// Route the join/backout points into full-time keys or contract keys based on the job type.
							$sstEmpType = DB::table('jobs')->where('id', $AddJobWiseCondidate->job_id)->value('employment_type');
							$sstIsContract = ((int) $sstEmpType !== 0); // 0 = Full-time; Contract/Freelance/Part-time all use the higher weight
							// Full-time keeps the base weights (join 1500 / team 30); contract uses higher weights (join 5000 / team 500).
							$cjUserFT = $sstIsContract ? 0 : $candidateJoineddUser;
							$boUserFT = $sstIsContract ? 0 : $candidateJoineddUserbackout;
							$cjTeamFT = $sstIsContract ? 0 : $candidateJoineddTeam;
							$boTeamFT = $sstIsContract ? 0 : $candidateJoineddTeambackout;
							$cjUserCT = ($sstIsContract && $candidateJoineddUser != 0)        ? 5000  : 0;
							$boUserCT = ($sstIsContract && $candidateJoineddUserbackout != 0) ? -5000 : 0;
							$cjTeamCT = ($sstIsContract && $candidateJoineddTeam != 0)        ? 500   : 0;
							$boTeamCT = ($sstIsContract && $candidateJoineddTeambackout != 0) ? -500  : 0;

							$teamCaptainId = array_map(function ($arr) {
								return array_values(array_filter($arr, function ($value) {
									return $value != 0;
								}));
							}, $teamCaptainId);
							// dd($teamCaptainId);						
							if(count($teamCaptainId) > 0){

								foreach($teamCaptainId as $comId => $captainId){
									$teamRankElastic = [
										'company_id'=>$comId,
										'user_id'=>$captainId[0],
										'candidate_id'=>$AddJobWiseCondidate->id,
										'profileUpload'=>0,
										'interviewScheduled'=>0,
										'interviewNoShow'=>0,
										'offerReleased'=>0,
										'candidateJoined'=>0,
										'jobsPosted'=>0,
										'feedbackGivenOnCVs'=>0,
										'postJobToVendorMarket'=>0,
										'outsourceJobsToVendors'=>0,
										'listBenchCandidates'=>0,
										'respondToBenchRequests'=>0,
										'interestedCandidates'=>0,
										'notInterestedCandidates'=>0,
										'teamCaptainInterviewScheduled'=>0,
										'teamCaptainInterviewNoShow'=>0,
										'teamCaptainOfferReleased'=>0,
										'teamCaptainCandidateJoined'=>$cjTeamFT,
										'backoutTeam'=>$boTeamFT,
										'teamCaptainCandidateJoinedContract'=>$cjTeamCT,
										'backoutTeamContract'=>$boTeamCT,
									];
									GeneralMethod::teamRankElastic($teamRankElastic);
								}

							}


							$teamRankElastic = [
								'company_id'=>$AddJobWiseCondidate->company_id,
								'user_id'=>$AddJobWiseCondidate->submitted_by,
								'candidate_id'=>$AddJobWiseCondidate->id,
								'profileUpload'=>0,
								'interviewScheduled'=>0,
								'interviewNoShow'=>0,
								'offerReleased'=>0,
								'candidateJoined'=>$cjUserFT,
								'backout'=>$boUserFT,
								'candidateJoinedContract'=>$cjUserCT,
								'backoutContract'=>$boUserCT,
								'jobsPosted'=>0,
								'feedbackGivenOnCVs'=>0,
								'postJobToVendorMarket'=>0,
								'outsourceJobsToVendors'=>0,
								'listBenchCandidates'=>0,
								'respondToBenchRequests'=>0
							];


							GeneralMethod::teamRankElastic($teamRankElastic);
							// if($master_status == 6 ){
							// 	return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);
							// }
						}
					}
				}


				if($master_status == 6 ){

					$of_offer_amount = $data->of_offer_amount;
					$of_joined_date = $data->of_joined_date;

					if(empty($sub_stages) || $sub_stages==''){
						return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => "Please select joined sub stages Onboarded OR Backed Out"]);
						
					}

				}



			// InReview 8 , Sourcing 1, Screening 2, Evaluation 3, Negotiation 4, Offered 5
			if($master_status != 7){
				$stages = [
					8 => ['display' => 'InReview',   'order' => 1],
					1 => ['display' => 'Sourcing',   'order' => 2],
					2 => ['display' => 'Screening',  'order' => 3],
					3 => ['display' => 'Evaluation', 'order' => 4],
					4 => ['display' => 'Negotiation','order' => 5],
					5 => ['display' => 'Offered',    'order' => 6],
					6 => ['display' => 'Joined',    'order' => 7],
					7 => ['display' => 'Rejected',    'order' => 8],
				];

				$currentStageId = $data->job_master_status_id;
				$newStageId     = (int)$master_status;

				// ✅ Ensure both exist in mapping
				if($data->job_master_status_id==7){
					// dd($data->job_master_status_id);
					$stage_point_tb = DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->first();
					// dd($stage_point_tb );
					if($stage_point_tb){
					  if($stage_point_tb->stage_id !=5 && $stage_point_tb->stage_id !=6){
						if($stage_point_tb->stage_id!=$master_status){
							$currentName  = $stages[$master_status]['display'];
				            $newName      = $stages[$stage_point_tb->stage_id]['display'];
							return Reply::dataOnly([
								'status' => 'candidatevalidation',
								'message' => "You cannot move backward from 
											<b style='color:#EF4444;'>Rejected</b> 
											to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
										Please complete the required step in 
										<b style='color:#F59E0B;'>{$newName}</b> first."
							]);

						}
					  }elseif($stage_point_tb->stage_id==5 || $stage_point_tb->stage_id==6){
						if($master_status !=1){
							$currentName  = $stages[$master_status]['display'];
				            $newName      = $stages[$stage_point_tb->stage_id]['display'];
                            return Reply::dataOnly([
								'status' => 'candidatevalidation',
								'message' => "You cannot move backward from 
											<b style='color:#EF4444;'>Rejected</b> 
											to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
										Please complete the required step in 
										<b style='color:#F59E0B;'>{$newName}</b> first."
							]);
						}

					  }	
						
                       
					}
					DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->delete();
				    //  dd($master_status);
					$values = array(
					'job_id' => $data->jobId,
					'candidate_id' => $data->candidate_id,
					'stage_id' => $master_status,
					'user_id' => $data->submitted_by,
					'created_at' => date('Y-m-d'),
					'updated_at' => date('Y-m-d')

				);
				DB::table('stage_point_tb')->insert($values);

				}else{

					if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {
						return Reply::dataOnly([
							'status' => 'candidatevalidation',
							'message' => 'Invalid stage transition.'
						]);
					}

				
					$currentOrder = $stages[$currentStageId]['order'];
					$newOrder     = $stages[$newStageId]['order'];

					$currentName  = $stages[$currentStageId]['display'];
					$newName      = $stages[$newStageId]['display'];

					// 🔹 Check if jumping ahead
					if ($newOrder > $currentOrder + 1) {
						return Reply::dataOnly([
							'status' => 'candidatevalidation',
							'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName)
						]);
					}

					// dd($newOrder,$currentOrder);

					// 🔹 Check if going backward
					if ($newOrder < $currentOrder) {

					if($currentOrder != $newOrder+1){
						return Reply::dataOnly([
								'status' => 'candidatevalidation',
								'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName)
							]);
					}

					}

					$sourcing=0; $screening=0;  $evaluation=0;  $negotiation=0; 
					// if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {
                    //  dd($newStageId,$currentStageId);
					if ($newStageId > $currentStageId || $currentStageId==8 ) {
						if($newStageId==1){
						    $sourcing=1; 
						}elseif($newStageId==2){
							$screening=1;  
						}elseif($newStageId==3){
							$evaluation=1;  
						}elseif($newStageId==4){
							$negotiation=1; 
						}

					}

					if ($newStageId < $currentStageId) {
						if($currentStageId==1){
								$sourcing=-1;
						}elseif($currentStageId==2){
								$screening=-1;  
						}elseif($currentStageId==3){
								$evaluation=-1; 
						}elseif($currentStageId==4){
								$negotiation=-1; 
						}
					}elseif($newStageId==8 && $currentStageId==1){
							$sourcing=-1;
					}

					$teamRankElastic = [
							'company_id'=>$data->company_id,
							'user_id'=>$data->submitted_by,
							'candidate_id'=>$data->candidate_id,
							'sourcing'=>$sourcing,
							'screening'=>$screening,
							'evaluation'=>$evaluation,
							'negotiation'=>$negotiation
					];
					GeneralMethod::teamRankElastic($teamRankElastic);

					DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->delete();
					
					$values = array(
						'job_id' => $data->jobId,
						'candidate_id' => $data->candidate_id,
						'stage_id' => $master_status,
						'user_id' => $data->submitted_by,
						'created_at' => date('Y-m-d'),
						'updated_at' => date('Y-m-d')

					);
					DB::table('stage_point_tb')->insert($values);

			    }
			
				if($data->job_master_status_id !=6){
			
					if($master_status == 6){

							$of_offer_amount = $data->of_offer_amount;
							$of_joined_date = $data->of_joined_date;

							if($data->job_master_status_id==5){
								if (($of_offer_amount == 0 || $of_offer_amount == '') && ($of_joined_date == '0000-00-00 00:00:00' || empty($of_joined_date))) {
								return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => "Please create the offer under the <b style='color:#F59E0B;'> Offer Tracker </b> tab, complete the required details <b style='color:#10B981;'> (Offer Amount and Date of Joining) </b>and release the offer. <br>After updating the details, please update the status to <b style='color:#10B981;'>Joining</b>."]);
								}
							}else{
								return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => "Kindly update the status to <b style='color:#F59E0B;'>'Offer Stage'</b> and complete the <b>'Create Offer'</b> section by entering the <b style='color:#10b981;'>offered salary</b> and <b style='color:#3b82f6;'>expected date of joining</b>. Once all details are filled in, please proceed to update the status to <b style='color:#10B981;'>'Joining Stage'</b>."]);
							}

						}
				}
			}
		}

		$jobApplication = JobApplication::find($application_id);
		
			
		if($master_status != 7){
    
			$validations = GeneralMethod::candidateValidation($addJobWiseCondidate_id);
			if($validations->status){
				return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => $validations->message]);
			}


			if(JobApplication::where('job_applications.resume','=','')->where('job_applications.id','=',$application_id)->exists()){
				return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']); 
			}


	    }

	


		if (($addJobWiseCondidate->master_status_id == $master_status) && ($addJobWiseCondidate->child_status_id == $sub_stages)) {
			return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => 'Match current record with previous.!']);
			exit;
		}

		if (!empty(@$master_status)) {
			if ($jobApplication->master_status_id >= 0 || $addJobWiseCondidate->master_status_id >= 0) {
				$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $addJobWiseCondidate->master_status_id)->first();
				$prev_process_name = @$masterStatus->name;
			}
			$jobApplication->master_status_id = @$master_status; //1
			$addJobWiseCondidate->master_status_id = @$master_status; //1
		}

		if ($jobApplication->child_status_id > 0 || $addJobWiseCondidate->child_status_id > 0) {
			$subProcess = DB::table('candidate_status')
				->select('candidate_status.name')
				->where('candidate_status.company_id', $addJobWiseCondidate->company_id)
				->where('candidate_status.id', $addJobWiseCondidate->child_status_id)
				->first();
			$prev_process_child_name = @$subProcess->name;
		}

		if (!empty(@$sub_stages)) {
			$jobApplication->child_status_id = @$sub_stages; //2
			$addJobWiseCondidate->child_status_id = @$sub_stages; //2
		} else {
			$jobApplication->child_status_id = '';
			$addJobWiseCondidate->child_status_id = '';
		}


		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 2,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			//'master_process_name' 		=>	@$jobApplication->job_id, 
			//'child_process_name' 			=>	@$master_status, 

			'application_id' => @$jobApplication->id ? @$jobApplication->id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL,
			'note' => '',
			'create_at' => @$date ? @$date : NULL,

			'update_at' => @$date ? @$date : NULL,
			'prev_process_name' => $prev_process_name ? $prev_process_name : NULL,
			'prev_process_child_name' => $prev_process_child_name ? $prev_process_child_name : NULL
		);


		if (@$master_status > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if (@$sub_stages > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
		if (!empty(@$comment)) {
			$values['note'] = @$comment;
		}

		$joined_date = '';
		if ((@$master_status > 0) && @$master_status == 6) {
			if (!empty($jobApplication->joined_date)) {
				$joined_date = date("d-M-Y", strtotime($jobApplication->joined_date));
				$values['note'] = 'Applicant joining date :: &nbsp;' . $joined_date;
			}
		}

		//DB::table('timeline')->insert($values);    
		GeneralMethod::UpdateTimeline($values);

		

		// $jobApplication->save();
		DB::table('job_applications')->where('id', $application_id)->update([
			'master_status_id' => (@$master_status) ? @$master_status : @$master_status,
			'child_status_id' => (@$sub_stages) ? @$sub_stages : ''
		]);
		

		$addJobWiseCondidate->save();
		// dd($jobApplication);
		

		$jobId = $addJobWiseCondidate->job_id;
			$lastInsertedId = $addJobWiseCondidate->id;
		    $wise_c = DB::table('job_applications')
			->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus','jobs.client_id','jobs.employment_type', 'add_job_wise_condidates.master_status_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
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

			$clients_data = DB::table('clients')->select('company_id','self_company')->where('id','=', @$wise_c->client_id)->first();

			$self_company = 1;

			if($clients_data){

				if($clients_data->company_id != $company_id){
                       $self_company = 1;
				}else{
					if($clients_data->self_company == 0){
                         $self_company = $clients_data->self_company;
					}else{
						$self_company = $clients_data->self_company;
					}
                   
				}
				
			}
		

		$partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
        ->join('package_active', function ($join) {
            $join->on('users.company_id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
				->where('package_active.startdate', '<=', now()) 
                ->where('package_active.enddate', '>=', now()) 
                ->where('package_active.packages_status', 1); 
        })
		->where('job_assign.job_id', '=', $jobId)
        ->pluck('users.email')
        ->toArray();



		
		$recommendedDataList = DB::table('recommendation_relation');
		$recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
		$recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
		$recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
			$join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
			$join->on('job_applications.job_id', '=', 'jobs.id');
		});
		$recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
            $join->on('companies.id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
				->where('package_active.startdate', '<=', now()) 
                ->where('package_active.enddate', '>=', now()) 
                ->where('package_active.packages_status', 1);
        });
		$recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
		$recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
		$recommendedDataList = $recommendedDataList->groupBy('companies.id');
		$recommendedDataList = $recommendedDataList->pluck('companies.company_email');
		$recommendedDataList = $recommendedDataList->toArray();

        // dd($recommendedDataList);
        
        $companyOrClientName = GeneralMethod::companyOrClientName($wise_c->client_id);

		// dd($companyOrClientName);
        
        
        $subject = "Status Changed |$wise_c->name | $jobApplication->full_name | $wise_c->title | $companyOrClientName->companyOrclientName | R-$wise_c->job_id";

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
				$np = "SNP LWD ".GeneralMethod::ymdTodmyNotTime($jobApplication->np_id) ;
			}
		}

        if($master_status != 5 && $master_status != 6){
			$data = [
				'VendorTeam' => @$wise_c->company_name,
				'ReqID' => "R" . @$wise_c->job_id,
				'Reqname' => @$wise_c->title,
				'CompanyNameClientname' => $company_or_client,
				'CandidateStatus' => $jobApplication->duplicate,
				'prev_process_name' => $prev_process_name,
				'CurrentStage' => @$wise_c->name,
				'SubStage' => $name,
				"CandidateName" => $jobApplication->full_name,
				"Experience" => $jobApplication->total_exp,
				'Position' => $jobApplication->current_designation,
				'NoticePeriod' => $np,
				'comments' => @$comment,
				'RefferedBy' => @$wise_c->posted_by,
				'companyOrclientName' => $companyOrClientName->companyOrclientName,
				'selfCompany' => $self_company,
				'SubmittedOn' => date("d-M-Y H:i:s", strtotime(@$wise_c->created_at))
			];
          // config(['mail.from.name' => auth()->user()->name]);
			if($master_status == 7){
			    
    			$notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
				
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = array(); //$notification; //array_merge($partnersData, $recommendedDataList);
				
			 $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
			 $checkMailPermision = GeneralMethod::mailPermission(3);
			 if($checkMailPermision){ 
				$mailTypeSubject = GeneralMethod::mailTypeSubject(3,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                // dd($to_Mail_Id);
				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.rejected',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');

            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.rejected', (object) $data, $attachments, $fromAddress, $senderName,$cc);
			 }
				// Mail::to(auth()->user()->email)->cc($cc)->send(new RejectedMail((object) $data,$subject));
			}else{

				// $mailConfig = [
				// 	'driver' => 'smtp',
				// 	'host' => 'smtp.gmail.com',
				// 	'port' => 587,
				// 	'from' => ['address' => '4belldevelopers@gmail.com', 'name' => 'Your Name'],
				// 	'encryption' => 'tls',
				// 	'username' => '4belldevelopers@gmail.com',
				// 	'password' => 'ghhcefkjvqaqkmkg',
				// ];

				// Mail::send([], [], function (Message $message) use ($mailConfig) {
				// 	$message->to(auth()->user()->email)
				// 			->subject('Subject of the Email')
				// 			->setBody('Body of the Email', 'text/html');

				// 	// Set the mail config dynamically
				// 	$message->getSwiftMessage()->getHeaders()->addTextHeader('X-Mailer', 'Laravel');
				// 	$transport = new \Swift_SmtpTransport($mailConfig['host'], $mailConfig['port']);
				// 	$transport->setUsername($mailConfig['username']);
				// 	$transport->setPassword($mailConfig['password']);
				// 	$transport->setEncryption($mailConfig['encryption']);
				// 	$mailer = new \Swift_Mailer($transport);
				// 	Mail::setSwiftMailer($mailer);
				// });
				
				
				$notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = array();//$notification; //array_merge($partnersData, $recommendedDataList);
				
			 $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
                    //  dd(auth()->user()->email, $subject,'email.candidate.change-status', (object) $data, $attachments, $fromAddress, $senderName,$cc);
					
			$checkMailPermision = GeneralMethod::mailPermission(6);
			if($checkMailPermision){
				$mailTypeSubject = GeneralMethod::mailTypeSubject(6,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.change-status',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');


            //    $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.change-status', (object) $data, $attachments, $fromAddress, $senderName,$cc);
			}
            //  dd("d");

				// Mail::to(auth()->user()->email)->cc($cc)->send(new ChangeStatusMail((object) $data,$subject));
				
			}
			
		}


		if ($master_status == 5) {

			$rowData = DB::table('job_applications')->select('*')->where('id', $application_id)->first();
			$rowDataOffer = DB::table('offer_job_applications')->select('*')->where('application_id', $application_id)->where('job_id', $addJobWiseCondidate->job_id)->first();

			$values = array(
				'application_id' => $rowData->id,
				'submitted_by' => $rowData->submitted_by,
				'company_id' => $rowData->company_id,
				'np_id' => $rowData->np_id,
				'full_name' => $rowData->full_name,

				'email' => $rowData->email,
				'phone' => $rowData->phone,
				'gender' => $rowData->gender,
				'dob' => $rowData->dob,
				'country' => $rowData->country,

				'state' => $rowData->state,
				'city' => $rowData->city,
				'secondrary_email' => $rowData->secondrary_email,
				'secondary_mobile' => $rowData->secondary_mobile,
				'skills' => $rowData->skills,

				'photo' => $rowData->photo,
				'resume' => $rowData->resume,
				'rating' => @$rowData->rating,
				'skype_id' => $rowData->skype_id,
				'cover_letter' => $rowData->cover_letter,

				'job_id' => $addJobWiseCondidate->job_id,
				'status_id' => $rowData->status_id,
				'is_viewed' => $rowData->is_viewed,
				'column_priority' => $rowData->column_priority,
				'total_exp' => $rowData->total_exp,

				'rel_exp' => $rowData->rel_exp,
				'passport' => $rowData->passport,
				'ctc' => $rowData->ctc,
				'ectc' => $rowData->ectc,
				'current_location' => $rowData->current_location,

				'current_designation' => $rowData->current_designation,
				'current_company' => $rowData->current_company,
				'process_id' => $rowData->process_id,
				'qualification' => $rowData->qualification,
				'master_status_id' => $rowData->master_status_id,

				'child_status_id' => $rowData->child_status_id,
				'serving_notice_period_mode' => $rowData->serving_notice_period_mode,
				'proposed_ctc' => $rowData->proposed_ctc,
				'any_offer' => $rowData->any_offer,
				'document' => $rowData->document,

				'sub_for_approval' => $rowData->sub_for_approval,
				'offer_approved' => $rowData->offer_approved,
				'offer_release' => $rowData->offer_release,
				'offer_acceptance' => $rowData->offer_acceptance,
				'joined' => $addJobWiseCondidate->id,

				'created_at' => date('Y-m-d'),
				'updated_at' => date('Y-m-d')

			);
			if (empty($rowDataOffer)) {
				DB::table('offer_job_applications')->insert($values);
				$offer_id = DB::getPdo()->lastInsertId();
			} else {
				$offer_id = $rowDataOffer->id;
				DB::table('offer_job_applications')->where('id', $rowDataOffer->id)->update([
					'joined_date' => null,
					'offer_mode' => null,
					'offer_amount' => null,
					'offer_currency' => null
				]);
			}
           
			if ($exists) {
		
				// 2. Prepare the update timestamp
				$updateTime = now()->toIso8601String();

			   // 3. Execute update with debugging
				$updateParams_e = [
					'index' => ELASTIC_CANDIDATES,
					'id' => $addJobWiseCondidate_id,
					'body' => [
						'doc' => [
							'of_joined_date' => null,
							'of_offer_currency' => null,
							'of_offer_mode' => null,
							'of_offer_amount' =>null,
							'of_offer_acceptance' =>null,
							'of_offer_approved' =>null,
							'of_created_date' =>null,
							'of_expired_date' =>null,
							'send_accept_date_time' =>null,
							'send_approved_date_time' =>null,
							'approved_date_time' =>null,
							'accept_date_time' =>null,
							'offer_id' =>$offer_id,
							'updated_at' => $updateTime
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
				// dd($updateParams);
				$redudds = $this->elasticService->getClient()->update($updateParams_e);
				GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);

				// dd($redudds,$addJobWiseCondidate_id);
			}

			$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $application_id)->where('interview_job_id', $wise_c->job_id)->orderBy('interview_schedules.id', 'DESC')->first();
			$schedule_date = '';
			if ($intRow) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y H:i", strtotime($schedule_date)). '(IST)';
				}



			$data = [
				'VendorTeam' => @$wise_c->company_name,
				'ReqID' => "R" . @$wise_c->job_id,
				'Reqname' => @$wise_c->title,
				'CompanyNameClientname' => $companyOrClientName->companyOrclientName,
				'CandidateStatus' => $jobApplication->duplicate,
				'prev_process_name' => $prev_process_name,
				'CurrentStage' => @$wise_c->name,
				'SubStage' => $name,
				"CandidateName" => $jobApplication->full_name,
				"Experience" => $jobApplication->total_exp,
				'Position' => $jobApplication->current_designation,
				'NoticePeriod' => $np,
				'comments' => @$comment,
				'interviewDateTime' => @$schedule_date,
				'linkedinId' => '',
				'RefferedBy' => @$wise_c->posted_by,
				'SubmittedOn' => date("d-M-Y H:i:s", strtotime(@$wise_c->created_at)),
				'companyOrclientName' => $companyOrClientName->companyOrclientName,
				'selfCompany' => $self_company,
				'OfferAmount' => "",
				'Joiningdate' => ""
			];
			// dd($data);
			// 		config(['mail.from.name' => auth()->user()->name]);

	           $notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc =  array();//$notification; //array_merge($partnersData, $recommendedDataList);
                // $cc = array_merge($partnersData, $recommendedDataList);
		
		 $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
			 $checkMailPermision = GeneralMethod::mailPermission(4);
			 if($checkMailPermision){
				$mailTypeSubject = GeneralMethod::mailTypeSubject(4,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

				// dd($to_Mail_Id);

				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.offer',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');

            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.offer', (object) $data, $attachments, $fromAddress, $senderName,$cc);
			 }
             

			 $jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL;
		$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => $jobApplication->submitted_by, 'job_id' => @$jobIdGet,'status' => 1,'application_id' => @$jobApplication->id);
		DB::table('tb_ranks')->insert($tb_ranks);
        // 		Mail::to(auth()->user()->email)->cc($cc)->send(new OfferMail((object) $data,$subject));
		

		}

		if ($master_status == 6) {

			$oderVal = DB::table('offer_job_applications')->where('application_id', @$application_id)->where('job_id', $addJobWiseCondidate->job_id)->first();
            $offer_id = @$oderVal->id;

			
				$offer_job_applications = DB::table('offer_job_applications')
				->select('joined as wiseId')
				->where('id', @$offer_id)
				->first();

				$master_status_check = DB::table('offer_job_applications')
				->select('master_status_id')
				->where('id', @$offer_id)
				->first();
				
			   $AddJobWiseCondidate = AddJobWiseCondidate::find($offer_job_applications->wiseId);

			   $spok_user_id_rows = DB::table('job_assign')
						->where('job_id', $AddJobWiseCondidate->job_id)
						->get(['spok_user_id', 'company_id'])->toArray();

						// Step 2: Build result as [company_id => spok_user_id]
						$teamCaptainId = [];

						foreach ($spok_user_id_rows as $row) {
							if (
								!is_null($row->spok_user_id) &&
								$row->spok_user_id != $AddJobWiseCondidate->submitted_by
							) {
								$teamCaptainId[$row->company_id][] = $row->spok_user_id;
							}
						}

						// Step 3: Remove duplicates and reindex inner arrays
						foreach ($teamCaptainId as $companyId => $spokIds) {
							$teamCaptainId[$companyId] = array_values(array_unique($spokIds));
						}

						$join_update_date_check = date("Y-m-d H:i:s", strtotime($oderVal->joined_date));
						$join_update_date_check = Carbon::parse($join_update_date_check);
						$today = Carbon::today();
						$diffInDays = abs($today->diffInDays($join_update_date_check));

						$candidateJoineddUser = 0;
						$candidateJoineddTeam = 0;
						$candidateJoineddUserbackout = 0;
						$candidateJoineddTeambackout = 0; 

						if($master_status_check->master_status_id==6){ //$diffInDays<=30 && 
							if(!empty($sub_stages) || $sub_stages !=''){
								$status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('id','=',$sub_stages)->first();
								if(@$status_row->name=='Onboarded'){
									$candidateJoineddUser = 1500;
									$candidateJoineddTeam = 30;
								}else{
									$candidateJoineddUserbackout = -1500;
									$candidateJoineddTeambackout = -30;
								}
							}

						}elseif($master_status_check->master_status_id==5){  //$diffInDays<=30 &&
							$candidateJoineddUser = 1500;
						    $candidateJoineddTeam = 30;
						}

						// Route the join/backout points into full-time keys or contract keys based on the job type.
						$sstIsContract = ((int) @$wise_c->employment_type !== 0); // 0 = Full-time; Contract/Freelance/Part-time all use the higher weight
						$cjUserFT = $sstIsContract ? 0 : $candidateJoineddUser;
						$cjUserCT = ($sstIsContract && $candidateJoineddUser != 0)        ? 5000  : 0;
						$boUserFT = $sstIsContract ? 0 : $candidateJoineddUserbackout;
						$boUserCT = ($sstIsContract && $candidateJoineddUserbackout != 0) ? -5000 : 0;
						$cjTeamFT = $sstIsContract ? 0 : $candidateJoineddTeam;
						$cjTeamCT = ($sstIsContract && $candidateJoineddTeam != 0)        ? 500   : 0;
						$boTeamFT = $sstIsContract ? 0 : $candidateJoineddTeambackout;
						$boTeamCT = ($sstIsContract && $candidateJoineddTeambackout != 0) ? -500  : 0;

						$teamCaptainId = array_map(function ($arr) {
							return array_values(array_filter($arr, function ($value) {
								return $value != 0;
							}));
						}, $teamCaptainId);
						
						if(count($teamCaptainId) > 0){

							foreach($teamCaptainId as $comId => $captainId){
								$teamRankElastic = [
									'company_id'=>$comId,
									'user_id'=>$captainId[0],
									'candidate_id'=>$AddJobWiseCondidate->id,
									'profileUpload'=>0,
									'interviewScheduled'=>0,
									'interviewNoShow'=>0,
									'offerReleased'=>0,
									'candidateJoined'=>0,
									'jobsPosted'=>0,
									'feedbackGivenOnCVs'=>0,
									'postJobToVendorMarket'=>0,
									'outsourceJobsToVendors'=>0,
									'listBenchCandidates'=>0,
									'respondToBenchRequests'=>0,
									'interestedCandidates'=>0,
									'notInterestedCandidates'=>0,
									'teamCaptainInterviewScheduled'=>0,
									'teamCaptainInterviewNoShow'=>0,
									'teamCaptainOfferReleased'=>0,
									'teamCaptainCandidateJoined'=>$cjTeamFT,
									'backoutTeam'=>$boTeamFT,
									'teamCaptainCandidateJoinedContract'=>$cjTeamCT,
									'backoutTeamContract'=>$boTeamCT,
								];
								GeneralMethod::teamRankElastic($teamRankElastic);
							}

						}


					$teamRankElastic = [
					'company_id'=>$AddJobWiseCondidate->company_id,
					'user_id'=>$AddJobWiseCondidate->submitted_by,
					'candidate_id'=>$AddJobWiseCondidate->id,
					'profileUpload'=>0,
					'interviewScheduled'=>0,
					'interviewNoShow'=>0,
					'offerReleased'=>0,
					'candidateJoined'=>$cjUserFT,
					'backout'=>$boUserFT,
					'candidateJoinedContract'=>$cjUserCT,
					'backoutContract'=>$boUserCT,
					'jobsPosted'=>0,
					'feedbackGivenOnCVs'=>0,
					'postJobToVendorMarket'=>0,
					'outsourceJobsToVendors'=>0,
					'listBenchCandidates'=>0,
					'respondToBenchRequests'=>0
				];

				 DB::table('offer_job_applications')
				->where('id', @$offer_id)
				->update(['master_status_id'=>6]);

				GeneralMethod::teamRankElastic($teamRankElastic);
			

			
            $of_currency = $currency[(@$oderVal->offer_currency) ? $oderVal->offer_currency : 0];
			$of_mode = $salaryModeOffer[(@$oderVal->offer_mode) ? $oderVal->offer_mode : 0];
			

			$data = [
				'VendorTeam' => @$wise_c->company_name,
				'ReqID' => "R" . @$wise_c->job_id,
				'Reqname' => @$wise_c->title,
				'CompanyNameClientname' => $companyOrClientName->companyOrclientName,
				'CandidateStatus' => $jobApplication->duplicate,
				'prev_process_name' => $prev_process_name,
				'CurrentStage' => @$wise_c->name,
				'SubStage' => $name,
				"CandidateName" => $jobApplication->full_name,
				"Experience" => $jobApplication->total_exp,
				'Position' => $jobApplication->current_designation,
				'NoticePeriod' => $np,
				'employment_type' =>( @$wise_c->employment_type==1) ? 'Contract' : 'Full-time',
				'comments' => @$comment,
				'RefferedBy' => @$wise_c->posted_by,
				'SubmittedOn' => date("d-M-Y H:i:s", strtotime(@$wise_c->created_at)),
				'companyOrclientName' => $companyOrClientName->companyOrclientName,
				'selfCompany' => $self_company,
				'OfferAmount' => @$of_currency.' '.@$oderVal->offer_amount.' /'.@$of_mode,
				'Joiningdate' =>  date("d-M-Y H:i:s", strtotime(@$oderVal->joined_date))
			];
			// dd($data);
			// 			config(['mail.from.name' => auth()->user()->name]);

	            $notification = DB::table('clients')->where('id','=', @$wise_c->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = $notification; //array_merge($partnersData, $recommendedDataList);
				
             //$cc = array_merge($partnersData, $recommendedDataList);
			 $basecompany = GeneralMethod::baseCompany();
             $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
             $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             $attachments = array();
             $checkMailPermision = GeneralMethod::mailPermission(5);
			if($checkMailPermision){  
				$mailTypeSubject = GeneralMethod::mailTypeSubject(5,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                // dd($to_Mail_Id);
				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.joined-us-today',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');

            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.joined-us-today', (object) $data, $attachments, $fromAddress, $senderName,$cc);

			}
			// 			Mail::to(auth()->user()->email)->cc($cc)->send(new JoinedUsTodayMail((object) $data, $subject));

			$valuesJoin = array(
				'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL,
				'master_status_id' => @$addJobWiseCondidate->master_status_id ? @$addJobWiseCondidate->master_status_id : 6,
				'child_status_id' => @$addJobWiseCondidate->child_status_id ? @$addJobWiseCondidate->child_status_id : null,
				'jobApplication' => @$application_id,
				'addJobWiseCondidate' => @$addJobWiseCondidate_id,
				'offer_amount' => (@$oderVal->offer_amount) ? @$oderVal->offer_amount : 0,
				'joined_date' => @$oderVal->joined_date,
				'offer_currency' => (@$oderVal->offer_currency) ? @$oderVal->offer_currency : 0,
				'offer_mode' => (@$oderVal->offer_mode) ? @$oderVal->offer_mode : 0,
			);
			DB::table('join_applications')->insert($valuesJoin);
			$jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL;
			$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => $jobApplication->submitted_by, 'job_id' => @$jobIdGet,'status' => 6,'application_id' => @$jobApplication->id);
			DB::table('tb_ranks')->insert($tb_ranks);

		}

		if(count($cc) > 0){
			foreach($cc as $email){
				$client_company_id = 0;
				$useremail = DB::table('users')->select('*')->where('email',$email)->first();
				if (!empty($useremail)) {
					$client_company_id = @$useremail->company_id;
				}
				// dd($client_company_id);
				if (@$client_company_id > 0) {
					$event_name = 'emails';
					$event_table_name = 'job applications stage change';
					$event_table_id = @$client_company_id;
					GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
				}
			}
		}

		// 1. First verify the document exists
		

		// dd($addJobWiseCondidate_id);
	
		if ($exists) {
			
			// 2. Prepare the update timestamp
			$updateTime = now()->toIso8601String();
			$master_status_name = DB::table('master_status')
			->select('*')
			->where('id', (int)$master_status)
			->first('name');

		   // 3. Execute update with debugging
			$updateParams = [
				'index' => ELASTIC_CANDIDATES,
				'id' => $addJobWiseCondidate_id,
				'body' => [
					'doc' => [
						'job_master_status_id' => (int)$master_status,
						'childStatus' => (int)$sub_stages,
						'name' => $master_status_name->name,
						'css_style' => $master_status_name->css_style,
						'updated_at' => $updateTime
					],
					'doc_as_upsert' => false // Changed to false since we verified existence
				],
				'refresh' => true // Force immediate refresh
			];
            // dd($updateParams);
			$redud = $this->elasticService->getClient()->update($updateParams);
			GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);

			// dd($redud);
		}
		

		$cachekeys = [
					'alljobslist_version_user_'.$this->user->id,
					'allcvtracker_version_user_'.$this->user->id,
					'allapptracker_version_user_'.$this->user->id,
					'allclientapp_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}


		return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);


	}
	public function clientcreate($jobId = null, $appId = null)
	{

		$jobId = GeneralMethod::value_decrypt($jobId);
		
		$this->pageTitle = __('Add Candidate');
		abort_if(Gate::denies('add_job_applications'), 403);

		$eventId = 4;
		$coinsRequ = 1;
		$companyId = (int) auth()->user()->company_id;
		$packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
		if($packageStatus->status){
			session()->flash('packageStatus', $packageStatus->message); // or false depending on your condition
			$url = url('service_company/apptracker/clientapp/' . @$jobId . '/all');
			return redirect($url);
		}

		// $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		// if ($packageStatus != 1) {
		// 	$url = url('service_company/apptracker/clientapp/' . @$jobId . '/all');
		// 	return redirect($url);
		// }

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$this->job = DB::table('recommendation_relation')
			->select('jobs.id', 'jobs.title', 'jobs.status', 'recommendation_relation.id as recommendation_relation_id', 'recommendation_relation.final_status')
			->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
			->where('jobs.id', '=', $jobId)
			->where('jobs.status', '=', 'active')
			->where('recommendation_relation.final_status', '=', 1)
			->where('recommendation_relation.to_company_id', '=', $company_id)
			->count();
		if ($this->job == 0) {
			return abort(404);
		}


		$this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
		$chkAdminRole = $this->user->hasRole('admin');

		$this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id',[57,58,59])->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->jobId = $jobId;
		$this->appId = $appId;

		$this->jobDetail = DB::table('jobs')
			->select('jobs.id', 'jobs.company_id', 'jobs.total_positions', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city','jobs.min_sal_mode','jobs.max_sal_mode','jobs.min_salary_currency','jobs.max_salary_currency')
			//->join('job_assign', 'job_assign.job_id', 'jobs.id')
			->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where('jobs.id', '=', $jobId)
			//->where('jobs.company_id', '=', $this->user->company_id)
			->first();

		$client_company_id = $this->jobDetail->company_id;

		$this->page_event = 'ADD';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		// $this->countries = Country::all();

		$this->statelist = GeneralMethod::statelist(101);
		//echo '<pre>'; print_r($this->statelist); exit; 

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			->where('job_additional_detail.company_id', '=', $client_company_id)
			->where('job_additional_detail.job_id', '=', $this->jobId)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();

		$this->jobLocation = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
			->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
			->where('client_location_jobs.job_id', $this->jobId)
			->orderBy('client_location_jobs.id', 'DESC')
			->first();

			// $this->jobSkill = DB::table('job_metronics_skills')
			// 	->leftJoin('candidate_job_skill_exp', function($join) {
			// 		$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id'); // Add the candidate condition
			// 	})
			// 	->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
			// 	->where('job_metronics_skills.job_id', '=', $this->jobId)
			// 	->groupBy('job_metronics_skills.id')
			// 	->get();

			$this->jobSkill = DB::table('job_metronics_skills')
			->leftJoin('candidate_job_skill_exp', function($join) {
				$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id');
			})
			->select(
				DB::raw('MIN(job_metronics_skills.id) as id'), // 👈 pick one id
				'job_metronics_skills.skill_name',
				DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value')
			)
			->where('job_metronics_skills.job_id', $this->jobId)
			->groupBy('job_metronics_skills.skill_name') // 👈 KEY FIX
			->get();

				$this->skillShow = false;
				
			
				
		

		$this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = '';
		$this->cityListArr = '';
		$this->cities = GeneralMethod::allCities();
		$this->default_loc = GeneralMethod::allCitiesSelected(array());
		 $this->currentPage = 1;

		return view('service_company.job-applications.clientcreate', $this->data);

	}
	public function clientappstore(Request $request)
	{

		abort_if(Gate::denies('add_job_applications'), 403);

		// $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		// if ($packageStatus != 1) {
		// 	$url = url('service_company/apptracker/' . GeneralMethod::value_encrypt(@$request->job_id) . '/all');
		// 	return redirect($url);
		// }

		$validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email:rfc,dns',
				'disposable_email'
			]
		], [
			'email.required' => 'Email is required',
			'email.email' => 'Please enter a valid email',
			'email.disposable_email' => 'Temporary emails are not allowed'
		]);

		if ($validator->fails()) {
			return Reply::dataOnly(['status' => 'mailcheck', 'msgType'=> 'warning', 'message' => $validator->errors()->first()]);	
		}


		$company_id = '';
		$company_id = auth()->user()->company_id;
        //   dd($this->user->company_id);

		    $eventId = 4;
			$coinsDeductions =  GeneralMethod::coinsDeductions((int) $company_id,$eventId,auth()->user()->id,$this->user->company_id,'','');
			// dd($coinsDeductions);
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}


		$applicationStatus = ApplicationStatus::first();
		$jobRow = DB::table('jobs')->select('jobs.id', 'jobs.company_id')->where('jobs.id', '=', $request->job_id)->first();
		$job_application_arr = array();

		if (!empty($request->full_name)) {
			$job_application_arr['full_name'] = ucwords(strtolower($request->full_name));
		}
		if (!empty($request->job_id)) {
			$job_application_arr['job_id'] = $request->job_id;
		}
		$job_application_arr['status_id'] = 8;
		if (!empty($request->email)) {
			$job_application_arr['email'] = $request->email;
		}
		if (!empty($request->phone)) {
			$job_application_arr['phone'] = $request->phone;
		}
		
		$job_application_arr['column_priority'] = 0;

		$job_application_arr['phone_code'] = ($request->primary_country_code) ? @$request->primary_country_code : null;
		$job_application_arr['second_phone_code'] = ($request->secondary_country_code) ? @$request->secondary_country_code : null;


		// $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

		$total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '')  ? $request->total_exp_year . '.' . $request->total_exp_month : null;

		$rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '')  ? $request->rel_exp_year . '.' . $request->rel_exp_month : null;

		// $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

		
		// if (!empty($request->total_exp)) {
			$job_application_arr['total_exp'] = $total_exp;//$request->total_exp;
		// }
		// if (!empty($request->rel_exp)) {
			$job_application_arr['rel_exp'] = $rel_exp;//$request->rel_exp;
		// }

			$currency = GeneralMethod::currency();  // Currency code
					
			$ecurrency  = $currency[$request->current_ectc_cur];
			$econvertedAmount = $request->bill_annual;
			if($request->current_ectc_cur > 0){
				$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
			}

			$ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);

			$job_application_arr['ectc_yearly'] = @$ectcdivided->yearly;
			$job_application_arr['ectc_monthly'] = @$ectcdivided->monthly;
			$job_application_arr['ectc_hourly'] = @$ectcdivided->hourly;

			$currency  = $currency[$request->current_ctc_cur];
			$convertedAmount = $request->pay_annual;
			if($request->current_ctc_cur > 0){
				$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
			}

			$ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);

			$job_application_arr['ctc_yearly'] = @$ctcdivided->yearly;
			$job_application_arr['ctc_monthly'] = @$ctcdivided->monthly;
			$job_application_arr['ctc_hourly'] = @$ctcdivided->hourly;

		if (!empty($request->ctc)) {
			$job_application_arr['ctc'] = $request->ctc;
		}
		if (!empty($request->current_ctc_cur)) {
			$job_application_arr['current_ctc_cur'] = $request->current_ctc_cur;
		}
		if (!empty($request->current_ctc_mode)) {
			$job_application_arr['current_ctc_mode'] = $request->current_ctc_mode;
		}
		if (!empty($request->ectc)) {
			$job_application_arr['ectc'] = $request->ectc;
		}
		if (!empty($request->current_ectc_cur)) {
			$job_application_arr['current_ectc_cur'] = $request->current_ectc_cur;
		}
		if (!empty($request->current_ectc_cur)) {
			$job_application_arr['current_ectc_cur'] = $request->current_ectc_cur;
		}
		if (!empty($request->percentage_hike)) {
			$job_application_arr['percentage_hike'] = $request->percentage_hike;
		}
		if (!empty($request->comment)) {
			$job_application_arr['comment'] = $request->comment;
		}
		if (!empty($request->pincode)) {
			$experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
			$job_application_arr['pincode'] = $experienceGet; //$request->pincode;
		}
		if (!empty($request->current_designation)) {
			$job_application_arr['current_designation'] = $request->current_designation;
		}
		if (!empty($request->current_company)) {
			$job_application_arr['current_company'] = $request->current_company;
		}
		if (!empty($request->notice_comments)) {
		    $job_application_arr['notice_comments'] = $request->notice_comments;
		}
		if (!empty($request->careersummary)) {
		$job_application_arr['careersummary'] = $request->careersummary;
		}
		if (!empty($request->nationality)) {
		$job_application_arr['nationality'] = $request->nationality;
		}
		if (!empty($request->wm)) {
		$job_application_arr['wm'] = $request->wm;
		}
		if (!empty($request->employmentType)) {
		$job_application_arr['employmentType'] = $request->employmentType;
		}
		if (!empty($request->oh)) {
		$job_application_arr['oh'] = $request->oh;
		}
		if (!empty($request->visastatu)) {
		$job_application_arr['visastatu'] = $request->visastatu;
		}
		if (!empty($request->referencename)) {
		$job_application_arr['referencename'] = $request->referencename;
		}
		if (!empty($request->reason_for_job_change)) {
		$job_application_arr['reason_for_job_change'] = $request->reason_for_job_change;
		}
		


		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);

		if (!empty($skillArr)) {
			$skillsAddElastic = array_column($skillArr, 'value');
			if(count( $skillsAddElastic)>0){
			GeneralMethod::skillsAddElastic($skillsAddElastic);
			}
		}
		
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}

		$job_application_arr['skills'] = $skillStr;
		$job_application_arr['master_status_id'] = 8;
		if (!empty($request->secondrary_email)) {
			$job_application_arr['secondrary_email'] = $request->secondrary_email;
		}
		if (!empty($request->secondary_mobile)) {
			$job_application_arr['secondary_mobile'] = $request->secondary_mobile;
		}
		$submitted_by = '';
		$submitted_by = auth()->user()->id;
		if (!empty($submitted_by)) {
			$job_application_arr['submitted_by'] = $submitted_by;
		}

		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			$job_application_arr['np_id'] = $request->np_id;
		} else if ($serving_notice_period == 2) {
			$job_application_arr['np_id'] = 'Immediate';
			$job_application_arr['last_working_date'] = $request->np_id;
		} else {
			$job_application_arr['np_id'] = $request->ddl_np_id;
		}

		if (!empty($request->qualification)) {
			$job_application_arr['qualification'] = $request->qualification;
		}

		$job_application_arr['thirdparty_company_id'] = $this->user->company_id;
		$job_application_arr['company_id'] = $jobRow->company_id;
		$job_application_arr['serving_notice_period_mode'] = $request->serving_notice_period;
		if ($request->has('gender')) {
			$job_application_arr['gender'] = $request->gender;
		}
		if ($request->has('dob')) {
			$job_application_arr['dob'] = $request->dob;
		}

		$stateId = '';
		$cityID = '';
		$countryID = '';
		$work_city = request('kt_tagify_work_location');
    	$work_city_all = json_decode($work_city, true);
		if (\request('kt_tagify_work_location') != "") {
			foreach ($work_city_all as $cityVal) {
				$parts = explode('|', $cityVal['value']);
				// Trim to remove any leading or trailing whitespace
			 	$cityName = trim($parts[0]);
				 $countrName = trim($parts[2]);

				  // dd($cityName);
				 // Debugging line
				 $city_row = DB::table('city_list')
				 ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
				 ->join('countries', 'countries.id', '=', 'state_list.country_id')
				 ->select('city_list.city_id', 'countries.id as country_id')
				 ->where('city_list.city','!=','')
				 ->where('city_list.city', '=', $cityName)
				 ->where('countries.country_name', '=', $countrName)
				 ->first();

				if($city_row){
					$cityID = (@$city_row) ? $city_row->city_id : '';
					$countryID = (@$city_row) ? $city_row->country_id : '';
					$stateId = GeneralMethod::stateId($cityID);
				}
				
			}
		}
		$job_application_arr['country'] = $countryID;
		$job_application_arr['state'] = $stateId;

		if ($job_application_arr['country'] == 101) {
			$job_application_arr['city_id'] = $cityID;
		} else {
			$job_application_arr['city_id'] = $cityID;
		}

		// if ($request->hasFile('photo')) {
		// 	// $jobApplication->photo 		= Files::upload($request->photo, 'candidate-photos');
		// 	$job_application_arr['photo'] = Files::upload($request->photo, 'candidate-photos');
		// }

		if ($request->hasFile('photo')) {
		    $file = $request->file('photo');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Images/' . $filename;

				Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				 $fileUrl = Storage::disk('s3')->url($filePath);
				 $job_application_arr['photo'] = $fileUrl;
           // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}


		$returnCount = 0;
		$message = 'messages.createdSuccessfully';
		$company_id = $this->user->company_id;
		$app_duplicate = $this->user->company->app_duplicate;
		$app_duplicate_days = $this->user->company->app_duplicate_days;
		$applicationEmail = $request->email;
		$applicationPhone =$request->phone;
		if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
			$returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days,$applicationPhone);
			if ($returnCount > 0 && $app_duplicate == 1) {
				$message = 'Duplicate :: application already in this job';
				$job_application_arr['duplicate'] = 1;
			} else if ($returnCount > 0 && $app_duplicate == 2) {
				$message = 'Duplicate :: application already in system';
				$job_application_arr['duplicate'] = 1;
			}
		}

		$chkApplication = DB::table('job_applications')
			->select('job_applications.id')
			->where('thirdparty_company_id', $company_id)
			->where(function($query) use ($applicationEmail, $applicationPhone) {
				$query->where('email', $applicationEmail)
				->orWhere('phone', $applicationPhone);
			})
			->count();
		if ($chkApplication > 0 && $returnCount > 0) {
			//$jobApplication->duplicate = 1;
			$job_application_arr['duplicate'] = 1;
		}
		//$jobApplication->save();
		


		$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
		$interviewDateOne   = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
		$interviewDateTwo   = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
		$interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);
		$interviewDateFour  = $this->formatInterviewDateTime($request->interviewDateFour, $request->interviewTimeFour, $defaultTimeFormat);
		$interviewDateFive  = $this->formatInterviewDateTime($request->interviewDateFive, $request->interviewTimeFive, $defaultTimeFormat);
		$interviewDateSix   = $this->formatInterviewDateTime($request->interviewDateSix, $request->interviewTimeSix, $defaultTimeFormat);

		$job_application_arr['interviewDateOne'] = $interviewDateOne;
		$job_application_arr['interviewDateTwo'] = $interviewDateTwo;
		$job_application_arr['interviewDateThree'] = $interviewDateThree;
		$job_application_arr['interviewDateFour'] = $interviewDateFour;
		$job_application_arr['interviewDateFive'] = $interviewDateFive;
		$job_application_arr['interviewDateSix'] = $interviewDateSix;
		$job_application_arr['linkedin'] = $request->linkedin;
		$job_application_arr['github'] = $request->github;
		$job_application_arr['behance'] = $request->behance;
		$job_application_arr['otherLink'] = $request->otherLink;
		$job_application_arr['interview_mode_one'] = $request->interview_mode_one;
		$job_application_arr['interview_mode_two'] = $request->interview_mode_two;
		$job_application_arr['interview_mode_three'] = $request->interview_mode_three;
		$job_application_arr['interview_mode_four'] = $request->interview_mode_four;
		$job_application_arr['interview_mode_five'] = $request->interview_mode_five;
		$job_application_arr['interview_mode_six'] = $request->interview_mode_six;

		
		

		$job_application_arr['created_at'] = date('Y-m-d');


		DB::table('job_applications')->insert($job_application_arr);
		$application_id = DB::getPdo()->lastInsertId();

		

		//$jobApplication->save();
		$jobApplicationGet = JobApplication::find($application_id);

		$skillArr = explode(',', $jobApplicationGet->skills);
		$percentage =  GeneralMethod::skill_match_percentage($request->job_id,$skillArr);

		$now = now();
		$insertArr = array('job_id' => $request->job_id, 'job_application_id' => $application_id, 'status_id' => 8, 'master_status_id' => 8, 'company_id' => $jobRow->company_id, 'submitted_by' => $submitted_by, 'thirdparty_company_id' => $this->user->company_id,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);

		// dd($insertArr);

		$query_update = DB::table('add_job_wise_condidates')->insert($insertArr);
		$lastInsertedId = DB::getPdo()->lastInsertId();
		
		
	

		$cachekeys = [
					'allcvtracker_version_user_'.$this->user->id,
					'alljobslist_version_user_'.$this->user->id,
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}



		$teamRankElastic = [
						'company_id'=>auth()->user()->company_id,
						'user_id'=>auth()->user()->id,
						'candidate_id'=>$lastInsertedId,
						'profileUpload'=>1,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>0,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>0,
						'respondToBenchRequests'=>0
		];
		GeneralMethod::teamRankElastic($teamRankElastic);


		if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
			$YearskillId = $request->input('YearskillId');
			$MonthskillId = $request->input('MonthskillId');
			$ratingId = $request->input('rating');
			// dd($YearskillId,$MonthskillId);
			DB::table('candidate_job_skill_exp')->where('candidate_id',$application_id)->where('job_id', $request->job_id)->delete();
			$this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
				'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['candidate_id' => $application_id]],
								['term' => ['job_id' => $request->job_id]],
							],
						],
					],
				],
			]);
			foreach ($YearskillId as $skillId => $value) {
				// if (!empty($value)) { 
				$ss = "Year";
				if($value >=1){
					$ss = "Years";
				}
				if(!empty($MonthskillId[$skillId])){
					$valMonth = $MonthskillId[$skillId] ;
				}else{
					$valMonth = $MonthskillId[$skillId] ;
				}
				 $skill_value = $value.'.'.$valMonth.' '.$ss;
				 $rating = $ratingId[$skillId] ;

					$values = array(
						'job_skill_id' => $skillId,
						'skill_value' => $skill_value,
						'job_id' => $request->job_id,
						'rating' => (int)$rating,
						'candidate_id' => $application_id
					);
					DB::table('candidate_job_skill_exp')->insert($values);
					$expId = DB::getPdo()->lastInsertId();
					$skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillId)->first();
					$this->elasticService->getClient()->index([
						'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'skill_name' => $skills->skill_name ?? '',
							'job_skill_id' => $skillId,
							'skill_value' => $skill_value,
							'job_id' => $request->job_id,
							'rating' => (int)$rating,
							'candidate_id' => $application_id,
							'created_at' =>  now()->toIso8601String(),
							'updated_at' =>  now()->toIso8601String()
						]
					]);
				// }
			}
		}
        //  dd($skillArr);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				$values = array(
					'job_application_id' =>$application_id,
					'skills' => $sval
				);
				if(!DB::table('job_application_skills')->where('job_application_id','=',$application_id)->where('skills','LIKE', '%'.$sval.'%')->exists()){
					DB::table('job_application_skills')->insert($values);
				}
			}
		}
		

		// Check job Assign Or Not
		$chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $jobRow->company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
		if ($chkJobAssign < 1) {
			$values = array('company_id' => $jobRow->company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id);
			DB::table('job_assign')->insert($values);
		}

		if ($request->hasFile('resume')) {

		   $companyNameR2 = DB::table('companies')
			->where('id',auth()->user()->company_id)
			->value('company_name');   // change column if needed
			$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
			$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
			$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
			$year  = date('Y');
            $month = date('m');
			
			$tempPath = session('resume_temp_path');
			$full_name = session('resume_original_name');
			
			UploadResumeToTestRTwo::dispatch($tempPath, $full_name,$jobApplicationGet->id,$companyNameRTwo,$year,$month)->onConnection('emails');
			$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
			DB::table('job_applications')
							->where('id', $jobApplicationGet->id)
							->update(['resume' =>$fileUrl]);
							session()->forget([
								'resume_temp_path',
								'resume_original_name'
							]);	

		}

		if(empty($request->resumeCheck)){
					DB::table('job_applications')
				->where('id', $jobApplicationGet->id)
				->update(['resume' =>'']);
		}


		// if ($request->hasFile('resume')) {
		// 	$hashname = Files::upload($request->resume, 'documents/' . $application_id, null, null, false);
		// 	//$jobApplication->documents()->create(['name' => 'Resume', 'hashname' => $hashname]);

		// 	$documentArr = array(
		// 		'company_id' => $jobRow->company_id,
		// 		'documentable_id' => @$application_id,
		// 		'documentable_type' => 'App\JobApplication',
		// 		'name' => 'Resume',
		// 		'hashname' => $hashname
		// 	);
		// 	DB::table('documents')->insert($documentArr);
		// }
		

		// if ($request->hasFile('resume')) {
		// 	$hashname = Files::upload($request->resume, 'documents/' . $application_id, null, null, false);
		// 	//$jobApplication->documents()->create(['name' => 'Resume', 'hashname' => $hashname]);

		// 	$documentArr = array(
		// 		'company_id' => $jobRow->company_id,
		// 		'documentable_id' => @$application_id,
		// 		'documentable_type' => 'App\JobApplication',
		// 		'name' => 'Resume',
		// 		'hashname' => $hashname
		// 	);
		// 	DB::table('documents')->insert($documentArr);
		// }


		if (isset($request->jobskill) && !empty($request->jobskill)) {
			DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $application_id)->delete();
			foreach ($request->jobskill as $key => $value) {
				if (!empty(@$value)) {
					$values = array('job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $application_id, 'skill_value' => @$value, );
					DB::table('candidate_skills_value')->insert($values);
				}
			}
		}
		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)
			//->orderBy('priority', 'ASC')
			->count();
		if ($status_cnt > 0) {
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
			$status_name = $status_row->name;
		}

		$timelineArr = array(
			'event_id' => 1,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			'master_process_name' => 'InReview',
			//'child_process_name'	=>	

			'application_id' => $application_id,
			'application_email' => $request->email,
			'job_id' => @$request->job_id,
			'note' => 'Successfully Added'
		);
		GeneralMethod::UpdateTimeline($timelineArr);

		$jobIdGet = @$request->job_id;
		$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => $jobApplicationGet->submitted_by, 'job_id' => @$jobIdGet,'status' => 0,'application_id' => @$application_id);
		DB::table('tb_ranks')->insert($tb_ranks);


		// Job Application Answer save
		if (isset($request->answer) && !empty($request->answer)) {
			foreach ($request->answer as $key => $value) {
				$answer = new JobApplicationAnswer();
				$answer->job_application_id = $application_id;
				$answer->job_id = $request->job_id;
				$answer->question_id = $key;
				$answer->answer = $value;
				$answer->save();
			}
		}


		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$request->job_id,
					'question_id' => @$jd->id,
					'application_id' => $application_id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$expId = DB::getPdo()->lastInsertId();
				$this->elasticService->getClient()->update([
						'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
						'id'    =>$expId,   // store as ES _id
						'body'  => [
							'doc' => [
								'job_id' => @$request->job_id,
								'question_id' =>  @$jd->id,
								'application_id' => $application_id,
								'job_detail_value' => @$jd->detail_value,
								'application_detail_value' =>@$request->additional_detail[$inc],
								'created_at' =>  now()->toIso8601String(),
								'updated_at' =>  now()->toIso8601String()
							],
							'doc_as_upsert' => true
						]
					]);
				$inc++;

			}
		}

		$company_id = auth()->user()->company_id;


        $skillStr = '';
		$skillArr = json_decode($request->skills, true);
		$requiredSkills =  [];
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				array_push($requiredSkills, $sval['value']);
			}
		}
		


		if (@$application_id > 0) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$application_id;
			$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
		}
		// ElasticCandidateIndex::dispatch($lastInsertedId)->onConnection('emails');
		GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId);

		$cachekeys = [
					'alljobslist_version_user_'.$this->user->id,
					'allcvtracker_version_user_'.$this->user->id,
					'allclientapp_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}

		$this->currencyConverter = GeneralMethod::currencyConverter();


		if (!empty($request->job_id)) {
			return Reply::redirect(url('/service_company/apptracker/clientapp/' .  GeneralMethod::value_encrypt($request->job_id) . '/all'), __('menu.jobApplications') . ' ' . __($message));
		} else {
			return Reply::redirect(route('service_company.job-applications.table'), __('menu.jobApplications') . ' ' . __($message));
		}
	}
	public function clientshow($id)
	{

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$AddJobWiseCondidate = AddJobWiseCondidate::find($id);
		$jobId = $AddJobWiseCondidate->job_id;
		// dd($id,$jobId);
		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = DB::table('job_applications')
			->select(
				'job_applications.id',
				'add_job_wise_condidates.submitted_by',
				'add_job_wise_condidates.company_id',
				'job_applications.np_id',
				'job_applications.serving_notice_period_mode',
				'job_applications.full_name',
				'job_applications.email',
				'job_applications.phone',
				'job_applications.phone_code',
				'job_applications.second_phone_code',
				'job_applications.secondary_mobile',
				'job_applications.photo',
				'job_applications.resume',
				'job_applications.rating',
				'job_applications.skype_id',
				'job_applications.cover_letter',
				'add_job_wise_condidates.job_id',
				'job_applications.status_id',
				'job_applications.is_viewed',
				'job_applications.column_priority',
				'job_applications.created_at',
				'job_applications.updated_at',
				'job_applications.deleted_at',
				'job_applications.total_exp',
				'job_applications.rel_exp',
				'job_applications.passport',
				'job_applications.current_location',
				'job_applications.current_designation',
				'job_applications.current_company',
				'job_applications.process_id',
				'job_applications.qualification',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'job_applications.secondrary_email',
				'job_applications.secondary_mobile',
				'job_applications.skills',
				'job_applications.city',
				'job_applications.country',
				'job_applications.pincode',
				'job_applications.state',
				'job_applications.company_id as other_company',
				'job_applications.condidate_type',
				'job_applications.current_ctc_cur',
				'job_applications.ctc',
				'job_applications.current_ctc_mode',
				'job_applications.current_ectc_cur',
				'job_applications.ectc',
				'job_applications.current_ectc_mode',
				'job_applications.percentage_hike',
				'job_applications.comment',
				'job_applications.dob',
                'job_applications.interviewDateOne',
                'job_applications.interviewDateTwo',
                'job_applications.interviewDateThree',
                'job_applications.interviewDateFour',
                'job_applications.interviewDateFive',
                'job_applications.interviewDateSix',
                'job_applications.linkedin',
                'job_applications.github',
                'job_applications.behance',
                'job_applications.otherLink',
                'job_applications.interview_mode_one',
                'job_applications.interview_mode_two',
                'job_applications.interview_mode_three',
                'job_applications.interview_mode_four',
                'job_applications.interview_mode_five',
                'job_applications.interview_mode_six',
				'job_applications.created_at',
				'clients.name as client_name',
				'jobs.title',
				'users.name as posted_by',
				'users.image as posted_by_image',
				//'notice_periods.np_title',
				'master_status.name as master_status_name',
				'offer_job_applications.proposed_ctc',
				'offer_job_applications.any_offer',
				'offer_job_applications.document',
				'offer_job_applications.sub_for_approval',
				'offer_job_applications.offer_approved',
				'offer_job_applications.offer_release',
				'offer_job_applications.offer_acceptance',
				'offer_job_applications.joined',
				'offer_job_applications.joined_date',
				'offer_job_applications.offer_amount',
				'offer_job_applications.confidential',
				'qualification_master.name as qualification_name',
				'state_list.state as state_name',
				'city_list.city as city_name',
				'add_job_wise_condidates.id as wiseId'
			)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})->leftJoin('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})->leftJoin('offer_job_applications', function ($join) use ($id, $jobId) {
				$join->on('offer_job_applications.application_id', 'job_applications.id');
				$join->where('offer_job_applications.job_id', $jobId);
				// $join->where('offer_job_applications.company_id', $company_id);
			})
			// ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id', )
			//->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
		$this->application = $this->application->where('add_job_wise_condidates.id', $id);
		$this->application = $this->application->first();


		$this->client_contuser_arr = DB::table('client_contact_info_jobs')
			->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
			->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
			->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')
			->where('client_contact_info_jobs.job_id', $jobId)
			->get();



		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')
			->select('*')
			//->where('job_additional_detail.company_id', '=', $company_id)
			->where('job_additional_detail.job_id', '=', @$jobId)
			->orderBy('job_additional_detail.id', 'ASC')
			->get();

		$this->application_additional_detail = DB::table('application_additional_detail')
			->select('*')
			->where('application_additional_detail.application_id', '=', $this->application->id)
			->where('application_additional_detail.job_id', '=', @$jobId)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();

		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')
				->select('name')
				->where('id', $this->application->qualification)
				->first();
		}

		$this->notesArr = DB::table('applicant_notes')
			->select(
				'applicant_notes.id',
				'applicant_notes.note_text',
				'applicant_notes.created_at',
				'applicant_notes.updated_at',
				'users.name',
				'users.image as posted_by_image'
			)
			//->where('company_id', user()->company_id)
			->join('users', 'users.id', '=', 'applicant_notes.user_id')
			->where('job_application_id', $this->application->id)
			->orderBy('id', 'ASC')
			->get();


		$this->schedule = DB::table('interview_schedules')
			->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
			//->where('company_id', user()->company_id)
			// ->join('users', 'users.id', '=', 'applicant_notes.user_id')
			->where('job_application_id', $this->application->id)
			->orderBy('id', 'DESC')
			->get();

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $jobId;
		$this->job_id = $job_id;

		$this->timeline = DB::table('timeline');
		$this->timeline = $this->timeline->select(
			'timeline.id',
			'timeline.owner_id',
			'timeline.application_id',
			'timeline.application_email',
			'timeline.job_id',
			'timeline.process_id',
			'timeline.status_name',
			'timeline.process_child_name',
			'timeline.note',
			'timeline.visible',
			'timeline.insert_type',
			'timeline.create_at',
			'timeline.interview_schedule_date',
			'timeline.interview_from_time',
			'timeline.interview_to_time',
			'timeline.interview_mode',
			'timeline.interview_mention_link',
			'timeline.interviewers',
			'timeline.interview_additional_email',
			'timeline.interview_message',
			'jobs.id as job_id',
			'jobs.title',
			'master_status.name as master_status_name',
			'clients.name as client_name',

			'timeline.timeline_type',
			'timeline.prev_process_name',
			'timeline.prev_process_child_name',
			'timeline.interview_mode_child_level',
			'timeline.average_star_rating',
			'timeline.interview_schedules_id',
			'master_status.css_style',
			'users.name',
			'users.image'

		);
		$this->timeline = $this->timeline->join('job_applications', 'job_applications.id', '=', 'timeline.application_id');
		$this->timeline = $this->timeline->leftJoin('users', 'users.id', '=', 'timeline.owner_id');
		$this->timeline = $this->timeline->leftJoin('jobs', 'jobs.id', '=', 'timeline.job_id');
		$this->timeline = $this->timeline->leftJoin('clients', 'clients.id', '=', 'jobs.client_id');
		$this->timeline = $this->timeline->leftJoin('master_status', 'master_status.id', '=', 'timeline.process_id');
		$this->timeline = $this->timeline->where('timeline.application_id', '=', $application_id);
		$this->timeline = $this->timeline->where('timeline.job_id', '=', $job_id);
		$this->timeline = $this->timeline->orderBy('timeline.id', 'DESC');
		$this->timeline = $this->timeline->get();

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

		// UPDATE TIMELINE
		$this->updateTimeLine = DB::table('updatetimeline');
		$this->updateTimeLine = $this->updateTimeLine->select(
			'updatetimeline.id',
			'updatetimeline.event_id',
			'updatetimeline.event_by_company_id',
			'updatetimeline.event_by_user_id',
			'updatetimeline.master_process_name',
			'updatetimeline.child_process_name',
			'updatetimeline.application_id',
			'updatetimeline.application_email',
			'updatetimeline.job_id',
			'updatetimeline.note',
			'updatetimeline.create_at',
			'updatetimeline.update_at',
			'updatetimeline.prev_process_name',
			'updatetimeline.note',
			'updatetimeline.confidential',
			'updatetimeline.prev_process_child_name',
			'application_event.event_name',
			'updatetimeline.timezone_abbreviation',
			'updatetimeline.interview_to_time',
			'jobs.id as job_id',
			'jobs.title',
			'users.name',
			'users.image',
			'clients.name as client_name'
		);
		$this->updateTimeLine = $this->updateTimeLine->join('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
		$this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
		$this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
		$this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
		$this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $application_id);
		// $this->updateTimeLine = $this->updateTimeLine->where('job_applications.id', '=', $application_id);
		// $this->updateTimeLine = $this->updateTimeLine->where('job_applications.company_id', '=', auth()->user()->company_id);
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$jobId);
		// $this->updateTimeLine = $this->updateTimeLine->orWhere(function ($query) use ($company_id, $application_id) {
		// 	return
		// 		$query->where('job_applications.thirdparty_company_id', '=', $company_id)
		// 			->where('job_applications.id', '=', $application_id);
		// 	//->where('staffing_company_relation.final_status', '=', 1); 
		// });
		$this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
		$this->updateTimeLine = $this->updateTimeLine->get();

		// dd($application_id,$jobId,$this->updateTimeLine);


		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')
				->select('*')
				->where('id', $this->application->child_status_id)
				->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;
		$this->additional_skills_arr = DB::table('skills')
			->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
			->select('skills.name', 'candidate_skills_value.skill_value')
			->where('candidate_skills_value.job_id', $jobId)
			->where('candidate_skills_value.candidate_id', $this->application->id)
			->where('skills.company_id', $this->user->company_id)
			->get();

		$this->application_add_detail_cnt = DB::table('application_additional_detail')
			->select('*')->where('application_id', $this->application->id)
			->where('job_id', $jobId)
			->orderBy('application_additional_detail.id', 'ASC')
			->count();

		$this->application_add_detail = DB::table('application_additional_detail')
			->select('*')->where('application_id', $this->application->id)
			->where('job_id', $jobId)
			->orderBy('application_additional_detail.id', 'ASC')
			->get();

		$this->offer_job_applications_count = DB::table('offer_job_applications')
			->select('offer_job_applications.id')
			->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')
			->join('clients', 'clients.id', '=', 'jobs.client_id')
			->where('offer_job_applications.email', '=', $this->application->email)
			->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
			->orderBy('offer_job_applications.id', 'DESC')
			->count();

		$this->offer_job_applications = DB::table('offer_job_applications')
			->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
			->where('offer_job_applications.email', '=', $this->application->email)
			->where('offer_job_applications.company_id', '=', auth()->user()
				->company_id)
			->orderBy('offer_job_applications.id', 'DESC')->get();

		$resume = '';
		$resumeData = DB::table('documents')
			->select('*')->where('documentable_id', $this->application->id)
			->where('name', '=', 'Resume')
			->first();
		$this->resume = @$resumeData->hashname;


		$sel_app_id = $id;
		$sel_job_id = $job_id;
		$this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
		if ($this->benchStatusCnt > 0) {
			$benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

			$source_from_company_id = @$benchAssignRow->source_from_company_id;
			$source_from_user_id = @$benchAssignRow->source_from_user_id;
			$source_from_id = @$benchAssignRow->source_from_id;
			$source_from_type = @$benchAssignRow->source_from_type;
			$source_company_id = @$benchAssignRow->source_company_id;

			$source_user_id = @$benchAssignRow->source_user_id;
			$source_application_id = @$benchAssignRow->source_application_id;
			$source_jobid = @$benchAssignRow->source_jobid;

			if ($source_from_type == 2) {
				$this->application->phone = $this->application->secondary_mobile = 'xxx-xxx-xxxx';
				$this->application->email = $this->application->secondrary_email = 'anonymous@email.com';
			}

			$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name','users.calling_code','users.email', 'users.mobile','users_profile.designation')
				->join('users', 'users.id', '=', 'bench_assign.user_id')
				->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
				->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
			$this->bench_additional_detail = DB::table('bench_additional_detail')
				->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
				->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

		}

		$this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

		$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile','users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
		->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

		$this->jobSkill = DB::table('job_metronics_skills')
				->leftJoin('candidate_job_skill_exp', function($join) {
					$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
						->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
				})
				->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
				->where('job_metronics_skills.job_id', '=', $this->job_id)
				->get();

				$this->skillShow = true;
				if(!DB::table('candidate_job_skill_exp')->where('candidate_job_skill_exp.job_id','=', $this->job_id)->where('candidate_job_skill_exp.candidate_id','=', $this->application->id)->exists()){
					$this->skillShow = false;
				}


		$view = view('service_company.job-applications.clientshow', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function download($id)
	{
		$resume = '';
		$resumeData = DB::table('documents')->select('*')->where('documentable_id', $id)->where('name', '=', 'Resume')->orderBy('created_at', 'desc')->first();
		$resume = $resumeData->hashname;
		$company = user()->company_id;
		$jobApplication = JobApplication::findOrFail($id);
		$path = public_path() . '/user-uploads/documents/' . $jobApplication->id . '/' . $resume;
		// dd($path);
		// if (!file_exists($path)) {
		// 	abort(404);
		// }
		return response()->download($path);
	}
	public function client_stage_body_content($id)
	{
		$current_company_id = auth()->user()->company_id;
		$this->qualification = '';
		$this->application = '';
		$this->requisition_owner = 1;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = DB::table('job_applications')
			->select(
				'job_applications.id',
				'job_applications.submitted_by',
				'job_applications.company_id',
				'job_applications.np_id',
				'job_applications.serving_notice_period_mode',
				'job_applications.full_name',
				'job_applications.email',
				'job_applications.phone',
				'job_applications.secondary_mobile',
				'job_applications.photo',
				'job_applications.resume',
				'job_applications.rating',
				'job_applications.skype_id',
				'job_applications.cover_letter',
				'add_job_wise_condidates.job_id',
				'add_job_wise_condidates.status_id',
				'job_applications.is_viewed',
				'job_applications.column_priority',
				'job_applications.created_at',
				'job_applications.updated_at',
				'job_applications.deleted_at',
				'job_applications.total_exp',
				'job_applications.rel_exp',
				'job_applications.passport',
				'job_applications.current_ctc_cur',
				'job_applications.current_ectc_cur',
				'job_applications.current_ctc_mode',
				'job_applications.current_ectc_mode',
				'job_applications.ctc',
				'job_applications.ectc',
				'job_applications.ctc_yearly',
				'job_applications.ectc_yearly',
				'job_applications.current_location',
				'job_applications.current_designation',
				'job_applications.current_company',
				'job_applications.process_id',
				'job_applications.qualification',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'job_applications.secondrary_email',
				'job_applications.secondary_mobile',
				'job_applications.skills',
				'clients.name as client_name',
				'jobs.title',
				'users.name as posted_by',
				'master_status.name as master_status_name',
				'job_applications.proposed_ctc',
				'job_applications.any_offer',
				'job_applications.document',
				'job_applications.sub_for_approval',
				'job_applications.offer_approved',
				'job_applications.offer_release',
				'job_applications.offer_acceptance',
				'job_applications.joined',
				'job_applications.joined_date',
				'job_applications.pay_rate_amount',
				'job_applications.bill_rate_amount',
				'job_applications.bill_rate_mode',
				'job_applications.bill_rate_currency',
				'job_applications.condidate_type',
				'job_applications.city',
				'job_applications.state',
				'job_applications.country',
				'job_applications.city_id',
				'state_list.state as state_name',

				'users.name as submitted_by_name',
				'users.company_id as submitted_by_company_id',
				'users.image as posted_by_image',
				'add_job_wise_condidates.job_id as jobId',
				'add_job_wise_condidates.thirdparty_company_id',
				'add_job_wise_condidates.id as job_wise_condidates_id'
			)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftJoin('jobs', 'jobs.id', '=', 'add_job_wise_condidates.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
		// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');

		$this->application = $this->application->where('add_job_wise_condidates.id', $id);
		$this->application = $this->application->first();

		// dd($this->application);


		if (@$current_company_id != @$this->application->company_id) {
			$this->requisition_owner = 0;
		}

		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}

		// dd($this->application);
		$submitted_by_company_id = @$this->application->submitted_by_company_id;
		$this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

		$thirdparty_company_id = $this->application->thirdparty_company_id;
		if ($thirdparty_company_id == $current_company_id) {
			$this->master_status = DB::table('master_status')->select('*')->where('master_status.direct_client', '=', 1)->orderBy('master_status.direct_client_priority', 'ASC')->get();
		} else {
			$this->master_status = DB::table('master_status')->select('*')->where('master_status.custom_client', '=', 1)->orderBy('master_status.priority', 'ASC')->get();
		}
		$master_status_id = $this->application->master_status_id;

		$this->candidate_status_Arr = DB::table('candidate_status')
			->select('*')
			->where('parent', $master_status_id)
			->where('company_id', $this->application->company_id)
			->orderBy('candidate_status.priority', 'ASC')
			->get();

		$this->schedule = DB::table('interview_schedules')
			->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
			->where('job_application_id', $this->application->id)
			->orderBy('id', 'DESC')
			->get();

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $this->application->job_id;
		$this->job_id = $job_id;

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);


		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;

		$current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = $country = $this->row_country_name = '';
		
         
			$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
			$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
				0	=>	"INR",
				1	=>	"EUR",
				2	=>	"JPY",
				3	=>	"GBP",
				4	=>	"USD",
				5	=>	"AED"
			];
			$jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$this->application->jobId)->first();
			$jobMode = $jobData->jobMode ?? @$this->application->current_ctc_mode;
			$jobCurrency = $jobData->jobCurrency ?? @$this->application->current_ctc_cur;
			$symbols = $symbolsValue[$jobCurrency] ?? "INR";
			
			$current_ctc_cur = '';
			$current_ctc_cur = $currencySymbole[@$jobCurrency];

			$current_ectc_cur = '';
			$current_ectc_cur = $currencySymbole[@$jobCurrency];
			
			$current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
			$current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);

			$payFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ctc_yearly,$symbols,$jobMode);
			$billFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ectc_yearly,$symbols,$jobMode);
			$ctc = '';
			$ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
			$ectc = '';
			$ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

			$this->current_ctc = @$current_ctc_cur . @$ctc . '/' . @$current_ctc_mode;
			$this->expected_ctc = @$current_ectc_cur . @$ectc . '/' . @$current_ectc_mode;
		

		$this->condidate_type= $this->application->condidate_type;

		if (empty($this->application->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->application->country)) {
				if (is_numeric($this->application->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->application->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}
		$this->row_state_name = '';
		if (empty($this->application->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->application->country == 101) {
				$this->row_state_name = $this->application->state_name;
			} else {
				$state = $this->application->state;
				if (is_numeric($this->application->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}

			}

		}

		$this->row_city_name = '';
		if ($this->application->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$view = view('service_company.job-applications.client_stage_body_content', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);




	}
	public function client_stage_submit(Request $request)
	{

		$client_job_applications = array();
		$company_id = "";
		$company_id = auth()->user()->company_id;

		$application_id = $master_status = $sub_stages = $comment = $addJobWiseCondidate_id = "";
		$prev_process_name = $prev_process_child_name = '';
		$addJobWiseCondidate_id = @$request->job_wise_condidates_id;
		$application_id = @$request->application_id;
		$master_status = @$request->master_status;
		$sub_stages = @$request->sub_stages;
		$comment = @$request->comment;

		$userId = auth()->id(); 
		$isValid = DB::table('add_job_wise_condidates as ajwc')
			->leftJoin('job_assign as ja', function ($join) {
				$join->on('ja.job_id', '=', 'ajwc.job_id')
					->on('ja.company_id', '=', 'ajwc.company_id');
			})
			->where('ajwc.id', $addJobWiseCondidate_id) // Candidate row id
			->where(function ($query) use ($userId) {
				$query->where('ja.spok_user_id', $userId)
					->orWhere('ajwc.submitted_by', $userId);
			})
			->exists();

		if (!$isValid) {
			
			$validations = (object)[
				'message' => "You are not authorized to update this candidate's status. Only the assigned Captain or the submitting user can update this candidate's status."
			];

			return Reply::dataOnly([
				'status'  => 'candidatevalidation',
				'message' => $validations->message
			]);
		}


		$addJobWiseCondidate = AddJobWiseCondidate::find($addJobWiseCondidate_id);


		if($master_status==1){
			// dd($addJobWiseCondidate->thirdparty_company_id);
			$eventId = 4;
			$coinsValidations =  GeneralMethod::coinsValidations((int)$addJobWiseCondidate->thirdparty_company_id,$eventId,auth()->user()->id,$addJobWiseCondidate->thirdparty_company_id,'','');
			// dd($coinsValidations);
			if($coinsValidations->status){
				$Company = Company::find($addJobWiseCondidate->thirdparty_company_id);
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => '"'.$Company->company_name.'" does not have sufficient coins to receive your profile at this time','company_coins'=> $coinsValidations->company_coins]);
			}
			$coinsDeductions = GeneralMethod::coinsValidations((int) $addJobWiseCondidate->company_id,$eventId,auth()->user()->id,$addJobWiseCondidate->thirdparty_company_id,'','');
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}

			GeneralMethod::coinsDeductions((int) $addJobWiseCondidate->company_id,$eventId,auth()->user()->id,$addJobWiseCondidate->thirdparty_company_id,'','');
		}




		$offer_id = null;
		$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES,
				'id' => $addJobWiseCondidate_id
			]);	
		if ($exists) {
				$params = [
						'index' => ELASTIC_CANDIDATES,
						'id'    =>$addJobWiseCondidate_id,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$data = (object) $response['_source'];
				
				// dd($data);
				
			if($master_status != 9){
                $stages = [
                    8 => ['display' => 'InReview',   'order' => 1],
                    1 => ['display' => 'Sourcing',   'order' => 2],
                    2 => ['display' => 'Screening',  'order' => 3],
                    3 => ['display' => 'Evaluation', 'order' => 4],
                    4 => ['display' => 'Negotiation','order' => 5],
                    5 => ['display' => 'Offered',    'order' => 6],
                    6 => ['display' => 'Joined',    'order' => 7],
                    7 => ['display' => 'Rejected',    'order' => 8],
                    9 => ['display' => 'Rejected',    'order' => 9],
                ];
 
                $currentStageId = $data->job_master_status_id;
                $newStageId     = $master_status;
 
                // ✅ Ensure both exist in mapping
                if($data->job_master_status_id==7){
                    // dd($data->job_master_status_id);
                    $stage_point_tb = DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->first();

                    // Same backward-recovery guard stage_submit() applies when
                    // un-rejecting: resume at exactly the stage left off at,
                    // or (if it had already reached Offered/Joined) restart at
                    // Sourcing — no skipping to an arbitrary stage in between.
                    if($stage_point_tb){
                        if($stage_point_tb->stage_id !=5 && $stage_point_tb->stage_id !=6){
                            if($stage_point_tb->stage_id!=$master_status){
                                $currentName  = $stages[$master_status]['display'];
                                $newName      = $stages[$stage_point_tb->stage_id]['display'];
                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from
                                                <b style='color:#EF4444;'>Rejected</b>
                                                to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
                                            Please complete the required step in
                                            <b style='color:#F59E0B;'>{$newName}</b> first."
                                ]);
                            }
                        }elseif($stage_point_tb->stage_id==5 || $stage_point_tb->stage_id==6){
                            if($master_status !=1){
                                $currentName  = $stages[$master_status]['display'];
                                $newName      = $stages[$stage_point_tb->stage_id]['display'];
                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from
                                                <b style='color:#EF4444;'>Rejected</b>
                                                to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
                                            Please complete the required step in
                                            <b style='color:#F59E0B;'>{$newName}</b> first."
                                ]);
                            }
                        }
                    }

                    DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->delete();
                    //  dd($master_status);
                    $values = array(
                    'job_id' => $data->jobId,
                    'candidate_id' => $data->candidate_id,
                    'stage_id' => $master_status,
                    'user_id' => $data->submitted_by,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d')

                );
                DB::table('stage_point_tb')->insert($values);

                }else{
 
                    if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'Invalid stage transition.'
                        ]);
                    }
 
                   
 
                    $currentOrder = $stages[$currentStageId]['order'];
                    $newOrder     = $stages[$newStageId]['order'];

                    $currentName  = $stages[$currentStageId]['display'];
                    $newName      = $stages[$newStageId]['display'];

                    // InReview(8) and agency-Rejected(9) are the submitting
                    // agency's own territory — once a candidate is sourced
                    // (currentStageId 1-7), control belongs to the client
                    // company, not the agency, and the agency can no longer
                    // change its stage at all (not even to reject via 9).
                    $isAgencySubmission = ((int) $addJobWiseCondidate->thirdparty_company_id === (int) $company_id)
                        && (int) $addJobWiseCondidate->thirdparty_company_id > 0;

                    // The only valid moves touching InReview/agency-Rejected:
                    // 8->1 (agency sources it), 8->9 (agency rejects pre-
                    // sourcing), 9->8 (agency un-rejects, back to review).
                    // 9->1 directly is explicitly NOT allowed — it must go
                    // back through InReview first.
                    $isSpecialAgencyEdge = ($currentStageId == 8 && in_array($newStageId, [1, 9], true))
                        || ($currentStageId == 9 && $newStageId == 8);

                    if ($currentStageId == 9 && $newStageId == 1) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'Move this candidate back to InReview before sourcing it again.'
                        ]);
                    }

                    if (!$isSpecialAgencyEdge && $isAgencySubmission && !in_array($currentStageId, [8, 9], true)) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'This candidate has already been sourced — only the client company can change its stage now.'
                        ]);
                    }

                    // The company can never send a candidate back to
                    // InReview once it's been sourced — that stage belongs
                    // to the agency, and reverting it isn't the company's
                    // call to make.
                    if (!$isSpecialAgencyEdge && $newStageId == 8) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'A candidate cannot be moved back to InReview once sourced.'
                        ]);
                    }

                    if (!$isSpecialAgencyEdge) {
                        // 🔹 Check if jumping ahead — Rejected(7) is reachable
                        // directly from any active company stage, so it's
                        // exempt from the "no skipping ahead" rule.
                        if ($newStageId != 7 && $newOrder > $currentOrder + 1) {
                            return Reply::dataOnly([
                                'status' => 'candidatevalidation',
                                'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName)
                            ]);
                        }

                        // 🔹 Check if going backward
                        if ($newOrder < $currentOrder) {
                            if($currentOrder != $newOrder+1){
                                return Reply::dataOnly([
                                        'status' => 'candidatevalidation',
                                        'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName)
                                    ]);
                            }
                        }
                    }

                    $sourcing=0; $screening=0;  $evaluation=0;  $negotiation=0;
                    // if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {
 
                    if ($newStageId > $currentStageId) {
                        if($newStageId==1){
                        $sourcing=1;
                        }elseif($newStageId==2){
                            $screening=1;  
                        }elseif($newStageId==3){
                            $evaluation=1;  
                        }elseif($newStageId==4){
                            $negotiation=1;
                        }
 
                    }
 
                    if ($newStageId < $currentStageId) {
                        if($currentStageId==-1){
                                $sourcing=-1;
                        }elseif($currentStageId==2){
                                $screening=-1;  
                        }elseif($currentStageId==3){
                                $evaluation=-1;
                        }elseif($currentStageId==4){
                                $negotiation=-1;
                        }
                    }elseif($newStageId==8 && $currentStageId==1){
                            $sourcing=-1;
                    }
 
                    $teamRankElastic = [
                            'company_id'=>$data->company_id,
                            'user_id'=>$data->submitted_by,
                            'candidate_id'=>$data->candidate_id,
                            'sourcing'=>$sourcing,
                            'screening'=>$screening,
                            'evaluation'=>$evaluation,
                            'negotiation'=>$negotiation
                    ];
                    GeneralMethod::teamRankElastic($teamRankElastic);

                    DB::table('stage_point_tb')->where('job_id','=',$data->jobId)->where('candidate_id','=',$data->candidate_id)->delete();

                    // When this move IS a rejection (newStageId 7), the resume
                    // point must be the stage being rejected FROM, not 7 itself —
                    // recording 7 here permanently blocked ever moving back to
                    // InReview/wherever the candidate was, since un-rejecting
                    // later requires the target to match this recorded stage_id.
                    $resumeStageId = ($newStageId == 7) ? $currentStageId : $master_status;
                    $values = array(
                        'job_id' => $data->jobId,
                        'candidate_id' => $data->candidate_id,
                        'stage_id' => $resumeStageId,
                        'user_id' => $data->submitted_by,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d')

                    );
                    DB::table('stage_point_tb')->insert($values);

                }
            }

		}

		if($master_status != 9){

			// dd("this is error section ");

			$validations = GeneralMethod::candidateValidation($addJobWiseCondidate_id);
			if($validations->status){
				return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => $validations->message]);
			}

			if(JobApplication::where('job_applications.resume','=','')->where('job_applications.id','=',$application_id)->exists()){
				return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']); 
			}


	    }
        // dd("dk");

		$jobApplication = DB::table('job_applications')
			->select('*')
			->where('job_applications.id', '=', $application_id)
			->first();

			

		$addJobWiseCondidate = DB::table('add_job_wise_condidates')
			->select('*')
			->where('add_job_wise_condidates.id', '=', $addJobWiseCondidate_id)
			->first();

		$appRow = DB::table('job_applications')->select('job_applications.company_id')->where('job_applications.id', '=', $application_id)->first();
		$client_company_id = $appRow->company_id;

		

         //    dd("l");
		// if (($master_status == 1) && ($client_company_id > 0)) {

		// 	$packageStatus = GeneralMethod::validateClientPackageRet('CVACCESS', $client_company_id);
		// 	if ($packageStatus != 1) {
		// 		return Reply::dataOnly(['status' => 'info', "message" => "CV Access / Received Opps something worng with client package , please contact to Administrator."]);
		// 	}

		// }

		if (!empty(@$addJobWiseCondidate)) {

			$masterStatus = DB::table('master_status')->select('master_status.name')->where('master_status.id', $addJobWiseCondidate->master_status_id)->first();
			$prev_process_name = @$masterStatus->name;
			$client_job_applications['master_status_id'] = @$master_status;
		}

		if ($addJobWiseCondidate->child_status_id > 0 || $addJobWiseCondidate->child_status_id) {
			$subProcess = DB::table('candidate_status')
				->select('candidate_status.name')
				->where('candidate_status.company_id', $company_id)
				->where('candidate_status.id', $addJobWiseCondidate->child_status_id)
				->first();
			$prev_process_child_name = @$subProcess->name;
		}


		if (!empty(@$sub_stages)) {
			$client_job_applications['child_status_id'] = @$sub_stages;
		} else {
			$client_job_applications['child_status_id'] = '';
		}






		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 2,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			//'master_process_name' 		=>	@$jobApplication->job_id, 
			//'child_process_name' 			=>	@$master_status, 
			'application_id' => @$application_id ? @$application_id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL,
			'note' => '',
			'create_at' => @$date ? @$date : NULL,
			'update_at' => @$date ? @$date : NULL,
			'prev_process_name' => $prev_process_name ? $prev_process_name : NULL,
			'prev_process_child_name' => $prev_process_child_name ? $prev_process_child_name : NULL
		);

		if (@$master_status > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if (@$sub_stages > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
		// dd($values);
		if (!empty(@$comment)) {
			$values['note'] = @$comment;
		}
		//DB::table('timeline')->insert($values);
		GeneralMethod::UpdateTimeline($values);

		$jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL;
		$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet,'status' => 0,'application_id' => @$application_id);
		DB::table('tb_ranks')->insert($tb_ranks);



		DB::table('job_applications')->where('job_applications.id', '=', $application_id)->limit(1)->update($client_job_applications);

		DB::table('add_job_wise_condidates')->where('add_job_wise_condidates.id', '=', $addJobWiseCondidate_id)->limit(1)->update($client_job_applications);
       
		

		if (($master_status == 1) && (@$application_id > 0)) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$application_id;
			// $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
		}

		// 1. First verify the document exists
		$exists = $this->elasticService->getClient()->exists([
			'index' => ELASTIC_CANDIDATES,
			'id' => $addJobWiseCondidate_id
		]);
	
		if ($exists) {
			
			// 2. Prepare the update timestamp
			$updateTime = now()->toIso8601String();
			$master_status_name = DB::table('master_status')
			->select('*')
			->where('id', (int)$master_status)
			->first('name');

		   // 3. Execute update with debugging
			$updateParams = [
				'index' => ELASTIC_CANDIDATES,
				'id' => $addJobWiseCondidate_id,
				'body' => [
					'doc' => [
						'job_master_status_id' => (int)$master_status,
						'childStatus' => (int)$sub_stages,
						'name' => $master_status_name->name,
						'css_style' => $master_status_name->css_style,
						'updated_at' => $updateTime
					],
					'doc_as_upsert' => false // Changed to false since we verified existence
				],
				'refresh' => true // Force immediate refresh
			];
			

			$this->elasticService->getClient()->update($updateParams);
			if($application_id){
					$exists = $this->elasticService->getClient()->exists([
							'index' => ELASTIC_CANDIDATES_BENCH,
							'id' => $application_id
						]);	
					if ($exists) {
				           GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);
					}
			}
			
		}

		$cachekeys = [
			'alljobslist_version_user_'.$this->user->id,
			'allcvtracker_version_user_'.$this->user->id,
			'allapptracker_version_user_'.$this->user->id,
			'allclientapp_version_user_'.$this->user->id
		];
		foreach ($cachekeys as $key) {
			GeneralMethod::cacheClean($key);
		}


		return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);




	}
	public function sampleDownload(Request $request){
       
	
		$file =  Excel::download(new JobApplicationSampleExport($request->job_id), 'users.csv');
		return response()->json(['file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()),'file_name'=>$file->getFile()->getFilename()]);


	}
	public function candidatejobDownload(Request $request){
       
	
	$candidateID = $request->arr;
	$jobId = $request->jobIdArr;
	$company_id = auth()->user()->company_id;
	$currency = GeneralMethod::currency();
	$salaryMode = GeneralMethod::salaryMode();
	$currencySymbole = GeneralMethod::currencySymboleforexcell();

	$job_status_arr = GeneralMethod::job_status();


	           
				$eventId = 30;
				$coinsRequ = count($candidateID);
				// dd($coinsRequ);
				$companyId = $company_id;
				$coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);

				// dd($coinsDeductions,$coinsDeductions->status,$coinsDeductions->company_coins,count($candidateID));
				if($coinsDeductions->status || $coinsDeductions->company_coins < count($candidateID)){
					 return response()->json(['status' => 'info', 'message' => $coinsDeductions->message,'file_url' => '','file_name'=>'']);
				}else{
					if(count($candidateID) > 0){
						for($i=0; $i< count($candidateID); $i++){
							GeneralMethod::coinsDeductions((int) $company_id, $eventId,auth()->user()->id,$company_id,'','');
						}
						
					}
				}

			$boolQuery = [
				'bool' => [
					'should' => [
						// First condition
						[
							'bool' => [
								'must' => array_merge([
									['terms' => ['id' => $candidateID]],
								])
							]
						],
					],
					'minimum_should_match' => 1,
					'filter' => [] // We'll add all filters here
				]
			];
			    $sort = [];
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
				$params = [
					'index' => ELASTIC_CANDIDATES,
					'request_cache' => true,
					'body' => [
						'sort' => $sort,
						'track_total_hits' => true,
						'query' => $boolQuery
					]
				];
				if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
					$total = 0;
					$data = [];
				}else{
					$response = $this->elasticService->getClient()->search($params);
					$total = $response['hits']['total']['value'] ?? 0;
					$data = array_map(function ($hit) {
						return $hit['_source'];
					}, $response['hits']['hits']);
					$lastSort = end($response['hits']['hits'])['sort'] ?? null;
				}
				

				// dd($data);
                $temArr = [];
				$custArr = [];
				if (count($data) > 0){
					// dd($data);
					foreach ($data as $key=> $rowArr){
						$row = (object) $rowArr;
						$CustomDetails = DB::table('application_additional_detail')->where('application_id','=',$row->candidate_id)->where('job_id','=',$row->jobId)->pluck('application_detail_value','job_detail_value')->toArray();
						$custArr = array();
						if(count($CustomDetails) > 0){
							foreach($CustomDetails as $dkey=> $de_value){
								$custArr[$dkey] = $dkey."-".$de_value;
							}
						}
						$childStatus_name = '';
						if ($row->job_master_status_id == 8) {
							$childStatus_name = 'Private';
						}
						if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
							$childStatus = '';
							$childStatus = DB::table('candidate_status')
								->select('*')
								->where('id', $row->childStatus)
								->first();
							if (!empty($childStatus)) {
								$childStatus_name = @$childStatus->name;
							}
						}
						$schedule_date = $className = '';
						$intRowCount = DB::table('interview_schedules')
							->select('*')
							->where('interview_job_wise_con_id', $row->wiseId)
							->where('interview_job_id', $row->jobId)
							->orderBy('interview_schedules.id', 'DESC')
							->count();
						$intRow = DB::table('interview_schedules')
							->select('*')
							->where('interview_job_wise_con_id', $row->wiseId)
							->where('interview_job_id', $row->jobId)
							->orderBy('interview_schedules.id', 'DESC')
							->first();
						$html = '';
						$schedule_time = '';
						if ($intRowCount > 0) {
							$schedule_date = @$intRow->schedule_date;
							$schedule_date = date('d M, Y', strtotime($schedule_date));
							$timezone_abbreviation = ($intRow->timezone_abbreviation) ? ' ('.$intRow->timezone_abbreviation.')' : '';
							$schedule_time = @$intRow->schedule_to_time. ' '.$timezone_abbreviation;
						}
						 $np = $row->np_id;
						if (empty($np)) {
							$np = 'N/A';
						}
						if ($row->serving_notice_period_mode == 0) {
							$np = $row->np_id . ' Days';
							if ($row->np_id == 1) {
								$np = $row->np_id . '  Day';
							}
						} elseif ($row->serving_notice_period_mode == 1) {
							$np = $row->np_id;
							if ($np != 'Immediate') {
								$np = date('d M y', strtotime($row->np_id));
							}
						}
						
						$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
						$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
							0	=>	"INR",
							1	=>	"EUR",
							2	=>	"JPY",
							3	=>	"GBP",
							4	=>	"USD",
							5	=>	"AED"
						];

						$jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$row->jobId)->first();
						$jobMode =  $jobData->jobMode;
						$jobCurrency = $jobData->jobCurrency;
						$symbols = $symbolsValue[$jobCurrency] ?? "INR";


						$current_ctc_cur = "";
						$current_ctc_cur = $currencySymbole[@$jobCurrency];

						$current_ectc_cur = "";
						$current_ectc_cur = $currencySymbole[@$jobCurrency];

						if ($row->condidate_type == 'bench_condidate') {
							$current_ectc_cur = $currencySymbole[@$jobCurrency];
						}

					$payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);
                      
						$ctc = "";
						$ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
						$ectc = "";
						$ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
						if ($row->condidate_type == 'bench_condidate') {
							$ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
						}
						

						$current_ctc_mode = "";
						$current_ctc_mode = substr($salaryMode[@$jobMode], 0, 1);
						$current_ctc_mode = substr($salaryMode[0], 0, 1);

						$current_ectc_mode = "";
						$current_ectc_mode = substr($salaryMode[@$jobMode], 0, 1);
						$current_ectc_mode = substr($salaryMode[0], 0, 1);
						if ($row->condidate_type == 'bench_condidate') {
							$current_ectc_mode = substr($salaryMode[@$jobMode], 0, 1);
						}
						$dnone = '';
						if ($row->condidate_type == 'bench_condidate') {
							$dnone = '--------';
						}
				
						$bench_assign = DB::table('bench_assign')
						->leftjoin('users', 'users.id', 'bench_assign.user_id')
						->select('users.name','users.email')
						->where('bench_assign.bench_applications_id', $row->candidate_id)
						->first();
						$percentage_hike = '-----';
						$current_ctc_cur_set = '------';
						if($bench_assign){
							$email = @$bench_assign->email;
							$phone = @$bench_assign->mobile;
							$row->current_company = @$row->apl_company_name;
						}else{
							$email = @$row->email;
							$phone = @$row->phone;
							$percentage_hike = @$row->percentage_hike.'% ' . @$row->comment;
							$current_ctc_cur_set  = @$current_ctc_cur .' '  . @$ctc . '/' . $current_ctc_mode;
						}

						$client_name = '';
						$client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
						if (!empty($client_name_com)) {
							$client_title_name = $client_name_com;
							$client_name = ucwords($client_title_name);
						} else {
							$client_title_name = $row->client_name;
							$client_name = ucwords($client_title_name);
						}

						$temArr[$key] = [
							'jobId'=>"R-".@$row->jobId,
							'jobTitle'=> ucwords(@$row->title),
							'fullName'=> ucwords(@$row->full_name),
							'currentDesignation'=>@$row->current_designation,
							'email'=>$row->email,
							'phone'=>$row->phone,
							'totalExp'=> @$row->total_exp." Yr",
							'row_city_name'=>@$row->city_name,
							'row_state_name'=>@$row->state_name,
							'row_country_name'=>@$row->country_name,
							'current_company'=>(@$row->current_company) ? @$row->current_company : '-------',
							'client_company'=>(@$client_name) ? @$client_name : '-------',
							'current_ectc_cur'=> @$current_ectc_cur.' ' . @$ectc . '/' . @$current_ectc_mode,
							'current_ctc_cur'=> @$current_ctc_cur_set,
							'Comment'=> @$row->Comments,
							'notic'=>@$np,
							'schedule_date'=>@$schedule_date."\n".$schedule_time,
							'upload_date'=>date("d-m-Y H:i:s",strtotime(@$row->created_at)),
							'qualification_name'=> ucwords(@$row->qualification_name),
							'stage'=> ucwords($row->name),
							'sub_stage'=> ucwords(@$childStatus_name),
							'sourceby'=> ucwords(@$row->company_name),
							'sourcename'=> ucwords(@$row->posted_by),
							'custom_details'=> implode(",",@$custArr)
						];
					}
				}	

		   $filename = 'candidate_export_' . now()->format('Ymd_His') . '.xlsx';

		   // Store in a public path or storage/app/public/...
		   $path = 'temp/exports/' . $filename;
			// Excel::store(new JobApplicationCustomExport($temArr), 'exports/' . $filename, 'public');
			Excel::store(new JobApplicationCustomExport($temArr), $path, 'r2');

			// dd(asset('storage/app/public/exports/' . $filename));
            $fileUrl = "https://file.jero.works/{$path}";
			// Return response with full URL
			return response()->json([
				'status' => 'success',
				'file_url' => $fileUrl,
				'file_name' => $filename,
			]);		
					





	$jobApplications = DB::table('job_applications')
			->select(
				'job_applications.*',
				'jobs.title',
				'job_locations.location',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'qualification_master.name as qualification_name',
				'state_list.state as state_name',
				'recommendation_relation.final_status',
				'recommendation_relation.id as recommendation_id',
				'recommendation_relation.from_company_id',
				'recommendation_relation.to_company_id',
				'companies.company_name',
				'app_com.company_name as apl_company_name',
				'current_com.company_name as client_company',
				'jobs.status as job_status',
				'add_job_wise_condidates.child_status_id as childStatus',
				'add_job_wise_condidates.id as wiseId',
				'offer_job_applications.any_offer as of_any_offer',
				'offer_job_applications.offer_approved as of_offer_approved',
				'offer_job_applications.offer_release as of_offer_release',
				'offer_job_applications.offer_acceptance as of_offer_acceptance',
				'offer_job_applications.joined as of_joined',
				'offer_job_applications.joined_date as of_joined_date',
				'offer_job_applications.offer_currency as of_offer_currency',
				'offer_job_applications.offer_mode as of_offer_mode',
				'offer_job_applications.offer_amount as of_offer_amount',
				'offer_job_applications.id as offer_id',
				'add_job_wise_condidates.job_id as add_job_id',
				'add_job_wise_condidates.job_id as jobId',
				'add_job_wise_condidates.created_at as upload_date'
			)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) use ($company_id) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftjoin('offer_job_applications', function ($join) use ($company_id) {
				$join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
			})
			->join('jobs', function ($join) use ($company_id) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftjoin('companies as app_com', function ($join) use ($company_id) {
				$join->on('app_com.id', 'job_applications.company_id');
			})
			->leftjoin('companies as current_com', function ($join) use ($company_id) {
				$join->on('current_com.id', 'add_job_wise_condidates.company_id');
			})
			// ->join('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
		//    dd(request('status_cus'));
		

		$jobApplications = $jobApplications->whereIn('add_job_wise_condidates.job_application_id', $candidateID);

		$jobApplications = $jobApplications->whereIn('add_job_wise_condidates.job_id',$jobId);

		
		$jobApplications = $jobApplications->groupBy('add_job_wise_condidates.id');
		$jobApplications = $jobApplications->orderBy('add_job_wise_condidates.id', 'DESC')->get()->toArray();
         $temArr = [];
         foreach($jobApplications as $key=> $row){
			
				if (empty ($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty (@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				$state = '';
				if (empty ($row->state)) {
					$row_state_name = '----';
				} else {
					if (@$row->country == 101) {
						$row_state_name = @$row->state_name;
					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}

					}

				}

				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}


				$current_ctc_cur = "";
				$current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];

				$current_ectc_cur = "";
				$current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];

				if ($row->condidate_type == 'bench_condidate') {
					$current_ectc_cur = $currencySymbole[@$row->bill_rate_currency];
				}


				$ctc = "";
				$ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
				$ectc = "";
				$ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);
				if ($row->condidate_type == 'bench_condidate') {
					$ectc = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
				}

				$current_ctc_mode = "";
				$current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
				$current_ctc_mode = substr($salaryMode[0], 0, 1);

				$current_ectc_mode = "";
				$current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);
				$current_ectc_mode = substr($salaryMode[0], 0, 1);
				if ($row->condidate_type == 'bench_condidate') {
					$current_ectc_mode = substr($salaryMode[@$row->bill_rate_mode], 0, 1);
				}
				$dnone = '';
				if ($row->condidate_type == 'bench_condidate') {
					$dnone = '--------';
				}

				$np = $row->np_id;
				if (empty ($np)) {
					$np = 'N/A';
				}
				if ($row->serving_notice_period_mode == 0) {
					if (!empty ($row->np_id)) {
						$np = $row->np_id . '  Days';
					}
				} else {
					$np = $row->np_id;
				}

				if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id)) {
					$stage_default_css = 'client_stage_sub_stage';
				}
				if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) && $row->master_status_id != 8) {
					$stage_default_css = '';
				}
				$name = '--------';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty (@$row->childStatus) && @$row->childStatus > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->childStatus)->first();
					if (!empty ($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				$schedule_date = '------';
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->where('interview_job_id', $row->add_job_id)->orderBy('interview_schedules.id', 'DESC')->first();
				if ($intRow) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				}
				$CustomDetails = DB::table('application_additional_detail')->where('job_id','=',$row->jobId)->pluck('application_detail_value','job_detail_value')->toArray();
				$custArr = array();
				if(count($CustomDetails) > 0){
					foreach($CustomDetails as $dkey=> $de_value){
						$custArr[$dkey] = $dkey."-".$de_value;
					}
				}
				
				$this->team = DB::table('users')
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->select('users.id', 'users.name','users.email', 'roles.display_name', 'users.working_status', 'users_profile.designation')
			->where('users.company_id', '=', $company_id)
			->where('status', '=', 'active')
			->orderBy('users.name', 'ASC')
			->get();

			$bench_assign = DB::table('bench_assign')
			->leftjoin('users', 'users.id', 'bench_assign.user_id')
			->select('users.name','users.email')
			->where('bench_assign.bench_applications_id', $row->id)
			->first();
			$percentage_hike = '-----';
			$current_ctc_cur_set = '------';
			if($bench_assign){
				$email = @$bench_assign->email;
				$phone = @$bench_assign->mobile;
				$row->current_company = @$row->apl_company_name;
			}else{
				$email = @$row->email;
				$phone = @$row->phone;
				$percentage_hike = @$row->percentage_hike.'% ' . @$row->comment;
				$current_ctc_cur_set  = @$current_ctc_cur .' '  . @$ctc . '/' . $current_ctc_mode;
			}


				// dd($custArr);

			$temArr[$key] = [
				'jobId'=>"R-".@$row->jobId,
				'jobTitle'=> ucwords(@$row->title),
				'fullName'=> ucwords(@$row->full_name),
				'currentDesignation'=>@$row->current_designation,
				'email'=>@$email,
				'phone'=>@$phone,
				'totalExp'=> @$row->total_exp." Yr",
				'row_city_name'=>@$row_city_name,
				'row_state_name'=>@$row_state_name,
				'row_country_name'=>@$row_country_name,
				'current_company'=>(@$row->current_company) ? @$row->current_company : '-------',
				'client_company'=>(@$row->client_company) ? @$row->client_company : '-------',
				'current_ectc_cur'=> @$current_ectc_cur.' ' . @$ectc . '/' . $current_ectc_mode,
				'current_ctc_cur'=> $current_ctc_cur_set,
				'Comment'=> @$percentage_hike,
				'notic'=>@$np,
				'schedule_date'=>$schedule_date,
				'upload_date'=>date("d-m-Y H:i:s",strtotime(@$row->upload_date)),
				'qualification_name'=> ucwords(@$row->qualification_name),
				'stage'=> ucwords($row->name),
				'sub_stage'=> ucwords(@$name),
				'sourceby'=> ucwords(@$row->company_name),
				'sourcename'=> ucwords(@$row->posted_by),
				'custom_details'=> implode(",",$custArr)
			];

			// $temArr = array_merge($temArr[$key],$custArr);

		 }
		//  dd($temArr);

		// Create a unique or custom file name
		  $filename = 'candidate_export_' . now()->format('Ymd_His') . '.xlsx';

		// Store in a public path or storage/app/public/...
			Excel::store(new JobApplicationCustomExport($temArr), 'exports/' . $filename, 'public');

			// dd(asset('storage/app/public/exports/' . $filename));

			// Return response with full URL
			return response()->json([
				'status' => 'success',
				'file_url' => asset('storage/app/public/exports/' . $filename),
				'file_name' => $filename,
			]);

		   
		//  $file =   Excel::download(new JobApplicationCustomExport($temArr), 'custom-export.xlsx');

		//  dd( $file,$file->getFile()->getFilename());

		//  return response()->json(['status' => 'success','file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()),'file_name'=>$file->getFile()->getFilename()]);


	    //   dd($temArr,$candidateID,$jobId,$jobApplications);

		// $file =  Excel::download(new JobApplicationSampleExport($request->job_id), 'users.xlsx');
		// return response()->json(['file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()),'file_name'=>$file->getFile()->getFilename()]);


	}
	public function unlinkDownload(Request $request){
		// $filename = asset('storage/framework/laravel-excel/'.$request->file_name);
		// $filename = 'framework/laravel-excel/' . $request->file_name;
		// $filename = public_path('storage/framework/laravel-excel/'.$request->file_name);
		$filePath = asset('storage/app/public/exports/' . $request->file_name); //storage_path('framework/laravel-excel/' . );
		// Check if the file exists before attempting to delete it
		if (file_exists($filePath)) {
			// Attempt to delete the file
			if (unlink($filePath)) {
				// File deleted successfully
				return "File deleted successfully.";
			} else {
				// Unable to delete the file
				return "Unable to delete the file.";
			}
		} else {
			// File doesn't exist
			return "File not found.";
		}
	}
	public function importJobCondidate(Request $request){


		// $filePath = $request->file('file')->getRealPath();
		$file = $request->file('file');
		$filePath = $file->store('temp');
		$extension = $file->extension();
		// dd($extension);

		// Check if the extension is what you expect
		if ($extension === 'txt') {
			$rows = 	Excel::import(new JobApplicationImport($request->job_id), $request->file('file'));
		} else {
			
		
			// dd($rows);
			// $rows = Excel::toCollection(new JobApplicationImport($file), $file);
			// Get the real path of the uploaded file
			$realPath = Storage::path($filePath);

			// $csvData = array_map('str_getcsv', file($file));

			$rows = Excel::toCollection([], $filePath)->first();
			// Define chunk size
			$chunkSize = 50;
			// Split data into chunks
			$chunks = $rows->chunk($chunkSize);
			foreach ($chunks as $chunk) {
				$this->processChunk($request->job_id,$chunk);
			}
		}

		// Excel::import(new JobApplicationImport($request->job_id), $request->file('file'));
		// dd("djd");
		return response()->json(['status' => true,'message' => 'File(s) imported successfully.']);
		// dd($request->all());
	}
	public function processChunk($jonId,$chunk)
    {
		$collection = $chunk->toArray();
		if ($collection) {          
                
			foreach($collection as $key=> $row) {
				
				if($row[0] !='Full Name') {
					// dd($row[0]);
					$qualification_master = DB::table('qualification_master')->select('id')->where('name', 'LIKE', '%' . $row[8] . '%')->first();

					$countr_master = DB::table('countries')->select('id')->where('country_name', '=', $row[15])->first();
					$state_master = DB::table('state_list')->select('id')->where('state', '=', $row[16])->first();
					$city_master = DB::table('city_list')->select('city_id')->where('city', '=', $row[17])->first();

					$company_id = '';
					$company_id = auth()->user()->company_id;

					$applicationStatus = ApplicationStatus::first();
					$jobApplication = new JobApplication();


					$jobApplication->full_name = $row[0];
					$jobApplication->job_id = $jonId;
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
					//   dd($row[5],$jobApplication->company_id );
					if ($row[5]) {
						$date = Carbon::createFromTimestamp((int) ($row[5] - 25569) * 86400);
						$formattedDate = $date->format('Y-m-d');
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

					$skillArr = explode(',', $jobApplication->skills);
					$percentage =  GeneralMethod::skill_match_percentage($jonId,$skillArr);
					$now = now();
					$insertArr = array('job_id' => $jonId, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);
					DB::table('add_job_wise_condidates')->insert($insertArr);
					$lastInsertedId = DB::getPdo()->lastInsertId();
					
					$cachekeys = [
						'alljobslist_version_user_'.$this->user->id,
						'allcvtracker_version_user_'.$this->user->id
					];
					foreach ($cachekeys as $key) {
						GeneralMethod::cacheClean($key);
					}

					$jobId = $jonId;
					$teamRankElastic = [
						'company_id'=>$company_id,
						'user_id'=>$submitted_by,
						'candidate_id'=>$lastInsertedId,
						'profileUpload'=>1,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>0,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>0,
						'respondToBenchRequests'=>0
					];
					GeneralMethod::teamRankElastic($teamRankElastic);


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
						'SubmittedOn' => date("d-m-Y H:i:s", strtotime(@$wise_c->created_at))
					];
					config(['mail.from.name' => auth()->user()->name]);
					$cc = array_merge($partnersData, $recommendedDataList);
					// Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data));


					$chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $jobId)->where('user_id', $submitted_by)->count();
					if ($chkJobAssign < 1) {
						$values = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => $jobId);
						DB::table('job_assign')->insert($values);
					}
					
					if (!empty($row[19])) {
						 DB::table('candidate_skills_value')->where('job_id', $jobId)->where('candidate_id', $jobApplication->id)->delete();
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

					$jobIdGet = $jobId;
					$tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet,'status' => 0,'application_id' => @$jobApplication->id);
					DB::table('tb_ranks')->insert($tb_ranks);

					// Job Application Answer save



					// $rowData = $row->toArray();
					$job_additional_detail_arr = array_slice($row,20);
					// dd($job_additional_detail[0]);

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
							// dd($valueswww);
						   DB::table('application_additional_detail')->insert($values);
						   $expId = DB::getPdo()->lastInsertId();
						  $this->elasticService->getClient()->update([
								'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
								'id'    =>$expId,   // store as ES _id
								'body'  => [
									'doc' => [
										'job_id' => @$jobId,
										'question_id' =>  @$jd->id,
										'application_id' =>  @$jobApplication->id,
										'job_detail_value' => @$jd->detail_value,
										'application_detail_value' =>$job_additional_detail_arr[$inc],
										'created_at' =>  now()->toIso8601String(),
										'updated_at' =>  now()->toIso8601String()
									],
									'doc_as_upsert' => true
								]
							]);
							$inc++;

						}
					}

				}

			}
		}
		// Excel::import(new JobApplicationImport($jonId), $chunk);
		// Excel::import(new JobApplicationImport($jonId), $chunk);
    }
	public function resumExtract(Request $request){

		// dd($request->all());
		if (is_array($request->resumeText)) {
			$textData = implode(",",$request->resumeText);
		} else {
			$textData = $request->resumeText;
		}

		// dd($textData);

		
		$textData = str_replace("Resume"," ",$textData);
		$textData = str_replace("Curriculum Vitae"," ",$textData);
		$textData = str_replace("CURRICULUM - VITAE"," ",$textData);
		$textData = str_replace("CURRICULUM VITAE"," ",$textData);
		$textData = str_replace("Curriculum-vitae"," ",$textData);
		$textData = str_replace("CV"," ",$textData);
		$textData = str_replace("CURRICULUM"," ",$textData);
		$textData = str_replace("Vitae"," ",$textData);
		$textData = str_replace("Curriculum"," ",$textData);
		$textData = str_replace("VITAE"," ",$textData);
		$textData = str_replace("NAME"," ",$textData);
		$textData = str_replace("Name"," ",$textData);
		$textData = str_replace("Greater"," ",$textData);
		$textData = str_replace("Address"," ",$textData);
		$textData = str_replace("PERSONAL INFORMATION"," ",$textData);
		$textData = str_replace("Contact Mobile"," ",$textData);
		$textData = str_replace("Email"," ",$textData);
		$textData = str_replace("email"," ",$textData);
		$textData = str_replace("Contact"," ",$textData);
		$textData = str_replace("contact"," ",$textData);
		$textData = str_replace("Mobile"," ",$textData);
		$textData = str_replace("mobile"," ",$textData);
		$textData = str_replace("Key Skills"," ",$textData);
		$textData = str_replace("Skills"," ",$textData);
		$textData = str_replace("Objective"," ",$textData);
		$textData = str_replace("Highly"," ",$textData);
		$textData = str_replace("Highlights"," ",$textData);
		$textData = str_replace("vitae"," ",$textData);
		$textData = str_replace("in meeting its"," ",$textData);
		$textData = str_replace("Committed"," ",$textData);
		$textData = str_replace("I would"," ",$textData);
		$textData = str_replace("Organization Post From"," ",$textData);
		$textData = str_replace("BIO DATA"," ",$textData);
		$textData = str_replace("Qualified"," ",$textData);
		$textData = str_replace("I have"," ",$textData);
		$textData = str_replace("SPECIAL"," ",$textData);
		
		// dd($textData,$textData1);
		// dd($textData);
		// Exclude common header words
		$textData = $this->excludeHeaderWords($textData);



		//////////////////////////

				// $apiKey = env('OPENAI_API_KEY'); // Ensure the key is set correctly
				// $openAiUrl = 'https://api.openai.com/v1/chat/completions'; // Change to chat completions

				// // Prepare the data structure for the chat model
				// $data = [
				// 	'model' => 'gpt-3.5-turbo', // Specify the model
				// 	'messages' => [
				// 		[
				// 			'role' => 'user',
				// 			'content' => "Extract the relevant information from this resume:\n\n" . $textData,
				// 		],
				// 	],
				// 	'max_tokens' => 1000,
				// 	'temperature' => 0.5,
				// ];

				// $curl = curl_init();

				// curl_setopt_array($curl, [
				// 	CURLOPT_URL => $openAiUrl,
				// 	CURLOPT_RETURNTRANSFER => true,
				// 	CURLOPT_ENCODING => '',
				// 	CURLOPT_MAXREDIRS => 10,
				// 	CURLOPT_TIMEOUT => 30,
				// 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				// 	CURLOPT_CUSTOMREQUEST => 'POST',
				// 	CURLOPT_POSTFIELDS => json_encode($data), // Ensure this is correctly set
				// 	CURLOPT_HTTPHEADER => [
				// 		"Content-Type: application/json",
				// 		"Authorization: Bearer $apiKey", // Ensure this line is correctly set
				// 	],
				// 	CURLOPT_VERBOSE => true, // Enable verbose output for debugging
				// ]);

				// Log::info("Sending JSON: " . json_encode($data)); // Log the JSON data

				//     $response = curl_exec($curl);
				//     $err = curl_error($curl);

				//     curl_close($curl);

				//     if ($err) {
				//         return response()->json(['error' => "cURL Error: $err"], 500);
				//     } else {
				//         $parsedResume = json_decode($response, true);
				//         Log::info("OpenAI API Response: ", $parsedResume); // Log the full response
				//         if (isset($parsedResume['error'])) {
				//             return response()->json(['error' => $parsedResume['error']['message']], 500);
				//         }
				//         if (isset($parsedResume['choices'][0]['message']['content'])) {
				//             $resumeContent = $parsedResume['choices'][0]['message']['content'];
							
				//             // Example of parsing logic (you can refine it as per your needs)
				//             preg_match('/Name:\s*(.*)/', $resumeContent, $matches);
				//             $parsedData['name'] = $matches[1] ?? '';

				//             preg_match('/Total Experience:\s*(.*)/', $resumeContent, $matches);
				//             $parsedData['experience']['years'] = $matches[1] ?? '';

				//             preg_match('/Skills:\s*(.*)/', $resumeContent, $matches);
				//             $parsedData['skills'] = explode(', ', $matches[1] ?? '');

				//             preg_match('/Current Location:\s*(.*)/', $resumeContent, $matches);
				//             $parsedData['contact']['location'] = $matches[1] ?? '';

				// 			preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $resumeContent, $matches);
				//             $parsedData['contact']['email'] = $matches[0] ?? '';

				//             // Parsing phone number (flexible pattern for different phone formats)
				//             preg_match('/\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/', $resumeContent, $matches);
				//             $parsedData['contact']['phone'] = $matches[0] ?? '';


				//             preg_match('/LinkedIn:\s*(.*)/', $resumeContent, $matches);
				//             // Extract the LinkedIn URL if needed, e.g., storing it in contact
				//             // $parsedData['contact']['linkedin'] = $matches[1] ?? '';

				//             // Continue parsing for other fields as necessary...

				// 			return response()->json([
				// 				'name' => $parsedData['name'],
				// 				'number' => $parsedData['contact']['phone'],
				// 				'email' => $parsedData['contact']['email'],
				// 				'city' => '',
				// 				'state' =>'',
				// 				'country' =>'',
				// 				'dob' => '',
				// 				'skills' => array_unique($parsedData['skills']),
				// 				'currentjob' => '',
				// 			]);

							
				//             // dd($parsedData);
				//             return $parsedResume['choices'][0]['message']['content'];
				//             // return view('resume.result', ['resume' => $parsedResume['choices'][0]['message']['content']]);
				//         } else {
				//             return response()->json(['error' => 'Failed to parse resume.'], 500);
				//         }
				//     }

				
			

		///////////////////////


		
		// dd($textData);
		

		// Check for repeated text patterns within the initial first and last 50 words
		$name = $this->checkAdjacentNouns($textData);
		
		// If name is not found, check for both adjacent text nouns condition within the initial 100 words
		if (!$name) {
			$name = $this->checkRepeatedText($textData);
		}

		// If none of the above conditions identify a name, resort to regular expression matching of common name patterns
		if (!$name) {
			$name = $this->matchWithRegexes($textData);
		}
		$numberRegexes = [
			'/\b\d{10}\b/',                        // 10-digit number
			'/\b\d{3}-\d{3}-\d{4}\b/',            // XXX-XXX-XXXX format
			'/\b\(\d{3}\)\s?\d{3}-\d{4}\b/',      // (XXX) XXX-XXXX format
		];


		
		$emailRegex = ['/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/'];
		$number = $this->matchWithRegexesCommon($textData, $numberRegexes);
		$email = $this->matchWithRegexesCommon($textData, $emailRegex);


		$locationSet = $this->locationCountryStateCity($textData);

		$parseDateOfBirth = $this->parseDateOfBirth($textData);
		// dd($parseDateOfBirth->getData()->dob);
		$dob = $parseDateOfBirth->getData()->dob;
		// dd($dob,$parseDateOfBirth);
	    // dd($textData);
		$parseCurrentEmployer = $this->parseCurrentEmployer($textData);
		$currentjob = str_replace(":","",$parseCurrentEmployer->getData()->employer_name);
		// dd($parseCurrentEmployer);

		
        $careerSummary  = $this->extractCareerSummary($textData);


		$jobTitles = [
			'sap sd/wm architect',
			'react.js + typescript',
			'plsql developer',
			'fullstack developer',
			'salesforce functional/business analyst',
			'salesforce techno functional',
			'salesforce technical architect',
			'linux admin',
			'server admin l3',
			'platform administrator – dynatrace, splunk',
			'php developer',
			'dotnet lead',
			'finance audit with ca',
			'technical architect (java+cloud)',
			'senior plsql postgres developer',
			'python & node developer',
			'angular developer',
			'mobile automation tester',
			'dotnet with azure',
			'sql lead',
			'php',
			'laravel',
			'ssis , ssrs & power bi',
			'php developer with laravel',
			'academic content writer',
			'business analyst (bfsi/insurance)',
			'scrum master (bfsi domain/insurance)',
			'fullstack developer',
			'python developer',
			'reactjs developer',
			'java backend developer',
			'technical support engineer',
			'data engineer',
			'data analyst',
			'project manager',
			'business analyst',
			'node.js developer',
			'java fullstack developer',
			'qa engineer',
			'principal engineer',
			'network segmentation engineer',
			'email security engineer',
			'sailpoint developer',
			'sailpoint l3 support & developer',
			'oracle dba with aws',
			'web designer',
			'full stack developer',
			'ui/ux designer',
			'devops engineer',
			'python developer',
			'ibm api connect developer',
			'ibm api connect/azure apim architect',
			'python-django developer',
			'data engineer (aws, python, sql, databricks, snowflake)',
			'business development executive',
			'sap ui5/abap developer',
			'sap abap rap developer',
			'devops engineer with aws and kubernetes',
			'dotnet tcp/ip developer',
			'database engineer',
			'angular developer',
			'data engineer lead with azure',
			'senior data engineer with azure',
			'xamarin developer',
			'node.js developer with sap capm',
			'finance controller',
			'sap abap architect',
			'health connection integration engineer',
			'senior .net developer with azure',
			'mocc consultant (sap basis administrator)',
			'mq administrator',
			'fullstack developer',
			'mern stack developer',
			'automation tester with selenium',
			'react.js + typescript developer',
			'network engineer l2',
			'network engineer l3',
			'qa automation lead',
			'sdet (selenium + python)',
			'sales manager',
			'regional business leader',
			'java developer (tier 1 / tier 2)',
			'automation tester',
			'python developer',
			'jr. salesforce developer',
			'sr. salesforce developer',
			'automation testing engineer',
			'qa lead (automation testing)',
			'senior principal engineer (java)',
			'senior data engineer (snaplogic + azure data bricks or snaplogic + synapse analytics)',
			'php developer with laravel and aws',
			'sap mm consultant',
			'executive assistant (female)',
			'dotnet developer',
			'java developer (tier 1 / tier 2)',
			'aws data engineer',
			'react.js with next.js developer',
			'devops engineer',
			'php developer with codeigniter',
			'java developer',
			'lead sdet (selenium + java/python)',
			'web automation tester',
			'manual tester',
			'automation tester',
			'qa manager (tier 1 / tier 2) (ecommerce or internet domain)',
			'java architect (java + react + aws)',
			'python architect',
			'l3 application security engineer',
			'llm',
			'rpa expert (c#/java, sql / t-sql, api, abby, python, r, huggingface)',
			'rpa developer (automation anywhere, sql, ssis, tensorflow, abby, python/r, java)',
			'program manager',
			'realestate-project sales manager (male candidate)',
			'product manager',
			'technical architect (java/python)',
			'devops lead',
			'database administrator (sql and no sql)',
			'sr. platform manager',
			'lims developer',
			'inside sales executive',
			'field sales executive (tier 1 and tier 2)',
			'data scientist/ai-ml engineer',
			'python & node developer',
			'data science',
			'business analyst (tier 1 and tier 2)',
			'frontend developer (react with typescript) (tier 1 college)',
			'plsql developer',
			'automation tester',
			'backend developer (java/python/node/golang)',
			'backend developer (node/golang)',
			'business analyst',
			'database developer (sql, postgres, no sql)',
			'database developer',
			'flutter developer',
			'frontend developer (react/vue.js)',
			'frontend developer',
			'frontend architect (mobile and web)',
			'senior devops engineer',
			'senior executive- performance marketing',
			'senior executive growth team',
			'seo specialist',
			'social media marketing executive',
			'drupal developer',
			'program manager',
			'project manager - technical',
			'java developer',
			'functional tester',
			'java lead developer',
			'hcl cdp developer',
			'hcl unica api developer',
			'hcl unica campaign developer',
			'hcl unica technical architect',
			'automation tester',
			'academic content writer (for management subject)',
			'senior embedded linux software engineer',
			'hardware engineer',
			'scrum master (it domain)',
			'salesforce developer',
			'fullstack developer',
			'technical lead (php, laravel+vue.js)',
			'senior .net developer',
			'aws data engineer (aws, python, pyspark, sql, snowflake)',
			'adobe campaign developer (adobe campaign standard, javascript, adobe administration)',
			'backend developer (node.js, typescript)',
			'drupal developer (acquia, studio)',
			'assistant operation manager - ar follow up/billing',
			'backend developer (python with django)',
			'frontend developer (angular)',
			'senior tech lead - python lead',
			'senior security analyst-cyber security',
			'data science lead',
			'data science ml',
			'data science cv',
			'.net developer',
			'manager ops',
			'angular developer',
			'ssis, ssrs & power bi',
			'ror developer',
			'lead sdet (selenium + python)',
			'lead ops',
			'executive ops',
			'mern developer',
			'mobile automation testing-appium',
			'python+react developer',
			'saas infrastructure engineer',
			'cisco sme',
			'content writer',
			'senior cloud network engineer',
			'.net technical architect (c#, angular)',
			'.netcore full stack developer',
			'java developer',
			'ad sales senior executive/assistant manager',
			'government sales manager',
			'marketing executive',
			'input desk manager',
			'saas sales',
			'frontend developer (javascript, typescript, react)',
			'.net + angular developer',
			'lead frontend developer (javascript, typescript, react)',
			'.netcore+angular.js/blazor developer',
			'gcp data engineer (apache airflow, big data, teradata, google kubernetes engine)',
			'java developer',
			'java fullstack developer',
			'angular developer',
			'angular developer',
			'java developer',
			'python developer+aws',
			'research engineer-path planning',
			'research engineer-perception',
			'slam developer',
			'senior android developer',
			'automation engineer',
			'compliance and grc analyst',
			'network administrator',
			'windows administrator',
			'database administrator-ms sql',
			'.net developer (asp .net, asp .net core, c#, ado .net)angular',
			'disaster recovery specialist',
			'senior/lead uft automation engineer',
			'.netcore+angular+mvc, microservices developer',
			'dotnet developer',
			'.netcore+angular8 developer',
			'c/c++ developer',
			'dba',
			'frontend developer-react',
			'backend developer-java',
			'qa tester',
			'data science engineer',
			'program manager',
			'ui/ux designer',
			'embedded engineering manager',
			'golang developer',
			'angular fullstack developer',
			'embedded developer',
			'.net developer-webapi, dot net core, entity framework',
			'.netcore+azure developer',
			'senior/lead qa automation engineer (selenium+java)',
			'azure data engineer',
			'.net fullstack developer',
			'product manager',
			'devops lead',
			'operations support lead',
			'devops engineer with oci',
			'net core developer-(financial accountancy & inventory module)',
			'senior software engineer (.netcore+angular/react)',
			'java developer with microservices',
		];

		$job_metronics_skills = DB::table('job_metronics_skills')->where('job_id', $request->jobId)->pluck('skill_name')->toArray();

		// dd($job_metronics_skills);

		// Initialize an empty array to store matched job titles
		$matchedJobTitles = [];

		if(count($job_metronics_skills) > 0){
			// Loop through the job titles array to find matches in the text
			foreach ($job_metronics_skills as $jobTitle) {
				if (stripos($textData, $jobTitle) !== false) {
					// If a match is found, add it to the matched job titles array
					$matchedJobTitles[] = ucwords($jobTitle);
				}
			}
		}else{
			foreach ($jobTitles as $jobTitle) {
				if (stripos($textData, $jobTitle) !== false) {
					// If a match is found, add it to the matched job titles array
					$matchedJobTitles[] = ucwords($jobTitle);
				}
			}
		}
				$matchedqualification = '';
				$qualification_row= DB::table('qualification_master')
				->select('qualification_master.id','qualification_master.name')
				->pluck('name','id')->toArray();
				// dd($qualification_row);
				if($qualification_row){
					foreach ($qualification_row as $key=> $qualifiactionTitle) {
						if (stripos($textData, $qualifiactionTitle) !== false) {
							// If a match is found, add it to the matched job titles array
							$matchedqualification = ucwords($key);
						}	
					}
					
				}
				// dd($matchedqualification);

				$location = '';
				// dd($locationSet['city'] ,$locationSet['state'] , $locationSet['country']);
				if($locationSet['city'] && $locationSet['state'] && $locationSet['country']){
					$location  =  $locationSet['city'] . ' | ' . $locationSet['state'] . ' | ' . $locationSet['country'];

					// $city = $locationSet['city']; 
					// $city_row_city= DB::table('city_list')
					// 			   ->join('state_list','state_list.id','=','city_list.state_id')
					// 			   ->join('countries','countries.id','=','state_list.country_id')
					// 			   ->select('city_list.city','countries.country_name','state_list.state')
					// 			   ->where('city_list.city', 'LIKE', "%{$city}%")
					// 			   ->first();
					// 			   if($city_row_city){
					// 				   $location  =  $city_row_city->city. ' | ' . $city_row_city->state . ' | ' . $city_row_city->country_name;
					// 			   }
				}

				
					
				// LinkedIn
				preg_match('/(?:https?:\/\/)?(?:www\.)?linkedin\.[a-z.]+\/[^\s]+/i', $textData, $linkedinMatch);
				$linkedin = $linkedinMatch[0] ?? null;

				// GitHub
				preg_match('/(?:https?:\/\/)?(?:www\.)?github\.[a-z.]+\/[^\s]+/i', $textData, $githubMatch);
				$github = $githubMatch[0] ?? null;

				// Behance
				preg_match('/(?:https?:\/\/)?(?:www\.)?behance\.[a-z.]+\/[^\s]+/i', $textData, $behanceMatch);
				$behance = $behanceMatch[0] ?? null;


				


		
		// dd($locationSet['city']);
		// dd($matchedJobTitles);
		// If validation passes, return the parsed data
		return response()->json([
			'name' => str_replace("of","",$name),
			'number' => $number,
			'email' => $email,
			'city' => ($locationSet['city']) ? $locationSet['city'] : '',
			'state' =>($locationSet['state']) ? $locationSet['state'] : '',
			'country' => ($location) ? $location : '',
			'dob' => ($dob) ? $dob : '',
			'skills' => array_unique($matchedJobTitles),
			'qualification' => ($matchedqualification),
			'currentjob' => ($currentjob) ? $currentjob : '',
			'linkedin' => $linkedin,
			'github'   => $github,
			'behance'  => $behance,
			'careerSummary'  =>  $careerSummary
		]);


	}
	private function extractCareerSummary($text)
    {
        
		$headings = [
			'Career Objective', 'Career Summary', 'Professional Summary', 'Objective',
			'Profile Summary', 'Executive Summary', 'Summary', 'About Me', 'Profile',
			'Career Highlights', 'Overview', 'Personal Statement', 'Introduction'
		];

		$pattern = '/(?:' . implode('|', array_map('preg_quote', $headings)) . ')\s*[:|\-]?\s*(.*?)(?=\n\s*\n|\n\s*[A-Z][A-Za-z\s]+[:|\-]|\z)/is';

		if (preg_match($pattern, $text, $match)) {
			$summary = trim(preg_replace('/\s+/', ' ', $match[1]));

			if (str_word_count($summary) > 100) {
				$words = preg_split('/\s+/', $summary);
				$summary = implode(' ', array_slice($words, 0, 100));
			}

			return $summary;
		}

		return null;

    }
	private function matchWithRegexesCommon($text, $regexes)
	{
		foreach ($regexes as $regex) {
			preg_match($regex, $text, $matches);
			if (!empty($matches)) {
				return $matches[0];
			}
		}
		return null;
	}
	private function excludeHeaderWords($text)
	{
		// Common header words to exclude
		$headerWords = ['Resume', 'CV', 'Curriculum Vitae','CURRICULUM - VITAE', 'CURRICULUM VITAE','Curriculum-vitae'];

		// Remove common header words from the text
		foreach ($headerWords as $word) {
			$text = preg_replace("/\b$word\b/i", '', $text); 
		}

		return $text;
	}
	private function checkRepeatedText($text)
	{
		// Get the first and last 50 words
		$words = str_word_count($text, 1);
		$first50Words = implode(' ', array_slice($words, 0, 50));
		$last50Words = implode(' ', array_slice($words, -50));

		// Filter out numbers and email addresses from the first and last 50 words
		$filteredFirst50Words = preg_replace('/\b\d+\b/', '', $first50Words);
		$filteredFirst50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredFirst50Words);

		$filteredLast50Words = preg_replace('/\b\d+\b/', '', $last50Words);
		$filteredLast50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredLast50Words);

		// Check for repeated text in the filtered first and last 50 words
		preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredFirst50Words, $firstMatch);
		preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredLast50Words, $lastMatch);

		// If repeated text is found in both ranges, consider them as a potential name
		if (!empty($firstMatch) && !empty($lastMatch)) {
			return $firstMatch[1];
		}

		return null;
	}
	private function checkAdjacentNouns($text)
	{

		// Get the first 100 words
		$first100Words = implode(' ', array_slice(str_word_count($text, 1), 0, 100));


		// Identify nouns within the first 100 words
		preg_match_all('/\b[A-Za-z]+\b/i', $first100Words, $nounMatches);

		// If two adjacent nouns are found within the first 100 words, consider them as a potential name
		if (!empty($nounMatches[0])) {
			$nouns = $nounMatches[0];

			for ($i = 0; $i < count($nouns) - 1; $i++) {
				if ($i + 1 < count($nouns) && $nouns[$i + 1] != '') {
					$potentialName =  $nouns[$i] . ' ' . $nouns[$i + 1];
					if (!$this->isCommonNoun($potentialName)) {
						if (!$this->isCommonNoun($potentialName)) {
							return $nouns[$i] . ' ' . $nouns[$i + 1];
						}
					}
					
				}
			}
		}

		return null;
	}
	private function matchWithRegexes($text)
	{
		// Define regular expressions for different data types
		$regexes = [
			'/\b[A-Z][a-z]+ [A-Z][a-z]+\b/',       // First and last name with capital initials
			'/\b[A-Z][a-z]+\b/',                   // Single capitalized name
			'/\b[a-zA-Z]+ [a-zA-Z]+\b/',           // First and last name with any case
			'/\b[A-Z][a-z]+ [A-Z]\. [A-Z][a-z]+\b/', // First name, middle initial, last name
			'/\b[A-Z]\. [A-Z]\. [A-Z][a-z]+\b/',   // Initials format (e.g., J. K. Rowling)
			'/\b[A-Z][a-z]+ [A-Z][a-z]+ [JS]r\.?\b/', // Name with Jr./Sr. suffix
			'/\b[A-Z][a-z]+ [A-Z][a-z]+ III\b/',   // Name with Roman numeral suffix
			'/\b[a-zA-Z]+-[a-zA-Z]+\b/',           // Hyphenated last name (e.g., Maria Rodriguez-Garcia)
			'/\b[A-Z][a-z]+ [A-Z][a-z]+ [A-Z][a-z]+\b/', // Compound last names (e.g., Jean-Luc Picard)
			'/\b(?:Dr\.|Mr\.|Ms\.|Mrs\.) [A-Z][a-z]+\b/', // Names with prefixes (e.g., Dr. John Smith)
		];

		// Match data using regular expressions
		foreach ($regexes as $regex) {
			preg_match($regex, $text, $matches);
			if (!empty($matches)) {
				return $matches[0];
			}
		}

		return null;
	}
	private function isCommonNoun($word)
	{
		// List of common nouns to exclude
		$commonNouns = ['mobile', 'email', 'address', 'phone', 'fax'];

		// Check if the word is a common noun
		return in_array(strtolower($word), $commonNouns);
	}
	private function locationCountryStateCity($text)
	{
			// Get the text data from the request

			// Define the list of Indian cities with their respective states and countries
			$indianCities = [
				'Mumbai' => ['Maharashtra', 'India'],
				'Delhi' => ['Delhi', 'India'],
				'Bangalore' => ['Karnataka', 'India'],
				'Hyderabad' => ['Telangana', 'India'],
				'Ahmedabad' => ['Gujarat', 'India'],
				'Chennai' => ['Tamil Nadu', 'India'],
				'Kolkata' => ['West Bengal', 'India'],
				'Surat' => ['Gujarat', 'India'],
				'Pune' => ['Maharashtra', 'India'],
				'Jaipur' => ['Rajasthan', 'India'],
				'Lucknow' => ['Uttar Pradesh', 'India'],
				'Kanpur' => ['Uttar Pradesh', 'India'],
				'Nagpur' => ['Maharashtra', 'India'],
				'Patna' => ['Bihar', 'India'],
				'Indore' => ['Madhya Pradesh', 'India'],
				'Thane' => ['Maharashtra', 'India'],
				'Bhopal' => ['Madhya Pradesh', 'India'],
				'Visakhapatnam' => ['Andhra Pradesh', 'India'],
				'Vadodara' => ['Gujarat', 'India'],
				'Firozabad' => ['Uttar Pradesh', 'India'],
				'Ludhiana' => ['Punjab', 'India'],
				'Rajkot' => ['Gujarat', 'India'],
				'Agra' => ['Uttar Pradesh', 'India'],
				'Siliguri' => ['West Bengal', 'India'],
				'Nashik' => ['Maharashtra', 'India'],
				'Faridabad' => ['Haryana', 'India'],
				'Patiala' => ['Punjab', 'India'],
				'Meerut' => ['Uttar Pradesh', 'India'],
				'Kalyan-Dombivli' => ['Maharashtra', 'India'],
				'Vasai-Virar' => ['Maharashtra', 'India'],
				'Varanasi' => ['Uttar Pradesh', 'India'],
				'Srinagar' => ['Jammu and Kashmir', 'India'],
				'Dhanbad' => ['Jharkhand', 'India'],
				'Jodhpur' => ['Rajasthan', 'India'],
				'Amritsar' => ['Punjab', 'India'],
				'Raipur' => ['Chhattisgarh', 'India'],
				'Allahabad' => ['Uttar Pradesh', 'India'],
				'Coimbatore' => ['Tamil Nadu', 'India'],
				'Jabalpur' => ['Madhya Pradesh', 'India'],
				'Gwalior' => ['Madhya Pradesh', 'India'],
				'Vijayawada' => ['Andhra Pradesh', 'India'],
				'Madurai' => ['Tamil Nadu', 'India'],
				'Guwahati' => ['Assam', 'India'],
				'Chandigarh' => ['Chandigarh', 'India'],
				'Hubli-Dharwad' => ['Karnataka', 'India'],
				'Amroha' => ['Uttar Pradesh', 'India'],
				'Moradabad' => ['Uttar Pradesh', 'India'],
				'Gurgaon' => ['Haryana', 'India'],
				'Aligarh' => ['Uttar Pradesh', 'India'],
				'Solapur' => ['Maharashtra', 'India'],
				'Ranchi' => ['Jharkhand', 'India'],
				'Jalandhar' => ['Punjab', 'India'],
				'Tiruchirappalli' => ['Tamil Nadu', 'India'],
				'Bhubaneswar' => ['Odisha', 'India'],
				'Salem' => ['Tamil Nadu', 'India'],
				'Warangal' => ['Telangana', 'India'],
				'Mira-Bhayandar' => ['Maharashtra', 'India'],
				'Thiruvananthapuram' => ['Kerala', 'India'],
				'Bhiwandi' => ['Maharashtra', 'India'],
				'Saharanpur' => ['Uttar Pradesh', 'India'],
				'Guntur' => ['Andhra Pradesh', 'India'],
				'Amravati' => ['Maharashtra', 'India'],
				'Bikaner' => ['Rajasthan', 'India'],
				'Noida' => ['Uttar Pradesh', 'India'],
				'Jamshedpur' => ['Jharkhand', 'India'],
				'Bhilai Nagar' => ['Chhattisgarh', 'India'],
				'Cuttack' => ['Odisha', 'India'],
				'Kochi' => ['Kerala', 'India'],
				'Udaipur' => ['Rajasthan', 'India'],
				'Bhavnagar' => ['Gujarat', 'India'],
				'Dehradun' => ['Uttarakhand', 'India'],
				'Asansol' => ['West Bengal', 'India'],
				'Nanded' => ['Maharashtra', 'India'],
				'Ajmer' => ['Rajasthan', 'India'],
				'Jamnagar' => ['Gujarat', 'India'],
				'Ujjain' => ['Madhya Pradesh', 'India'],
				'Sangli-Miraj & Kupwad' => ['Maharashtra', 'India'],
				'Loni' => ['Uttar Pradesh', 'India'],
				'Jhansi' => ['Uttar Pradesh', 'India'],
				'Pondicherry' => ['Puducherry', 'India'],
				'Nellore' => ['Andhra Pradesh', 'India'],
				'Jammu' => ['Jammu and Kashmir', 'India'],
				'Belagavi' => ['Karnataka', 'India'],
				'Raurkela' => ['Odisha', 'India'],
				'Mangaluru' => ['Karnataka', 'India'],
				'Tirunelveli' => ['Tamil Nadu', 'India'],
				'Malegaon' => ['Maharashtra', 'India'],
				'Gaya' => ['Bihar', 'India'],
				'Tiruppur' => ['Tamil Nadu', 'India'],
				'Davanagere' => ['Karnataka', 'India'],
				'Kozhikode' => ['Kerala', 'India'],
				'Akola' => ['Maharashtra', 'India'],
				'Kurnool' => ['Andhra Pradesh', 'India'],
				'Bokaro Steel City' => ['Jharkhand', 'India'],
				'Rajahmundry' => ['Andhra Pradesh', 'India'],
				'Ballari' => ['Karnataka', 'India'],
				'Agartala' => ['Tripura', 'India'],
				'Bhagalpur' => ['Bihar', 'India'],
				'Latur' => ['Maharashtra', 'India'],
				'Dhule' => ['Maharashtra', 'India'],
				'Korba' => ['Chhattisgarh', 'India'],
				'Bhilwara' => ['Rajasthan', 'India'],
				'Berhampur' => ['Odisha', 'India'],
				'Muzaffarpur' => ['Bihar', 'India'],
				'Ahmednagar' => ['Maharashtra', 'India'],
				'Mathura' => ['Uttar Pradesh', 'India'],
				'Kollam' => ['Kerala', 'India'],
				'Avadi' => ['Tamil Nadu', 'India'],
				'Kadapa' => ['Andhra Pradesh', 'India'],
				'Anantapur' => ['Andhra Pradesh', 'India'],
				'Kamarhati' => ['West Bengal', 'India'],
				'Bilaspur' => ['Chhattisgarh', 'India'],
				'Shahjahanpur' => ['Uttar Pradesh', 'India'],
				'Satara' => ['Maharashtra', 'India'],
				'Bijapur' => ['Karnataka', 'India'],
				'Rampur' => ['Uttar Pradesh', 'India'],
				'Shivamogga' => ['Karnataka', 'India'],
				'Chandrapur' => ['Maharashtra', 'India'],
				'Junagadh' => ['Gujarat', 'India'],
				'Thrissur' => ['Kerala', 'India'],
				'Alwar' => ['Rajasthan', 'India'],
				'Bardhaman' => ['West Bengal', 'India'],
				'Kulti' => ['West Bengal', 'India'],
				'Nizamabad' => ['Telangana', 'India'],
				'Parbhani' => ['Maharashtra', 'India'],
				'Tumkur' => ['Karnataka', 'India'],
				'Khammam' => ['Telangana', 'India'],
				'Ozhukarai' => ['Puducherry', 'India'],
				'Bihar Sharif' => ['Bihar', 'India'],
				'Panipat' => ['Haryana', 'India'],
				'Darbhanga' => ['Bihar', 'India'],
				'Bally' => ['West Bengal', 'India'],
				'Aizawl' => ['Mizoram', 'India'],
				'Dewas' => ['Madhya Pradesh', 'India'],
				'Karnal' => ['Haryana', 'India'],
				'Bathinda' => ['Punjab', 'India'],
				'Jalna' => ['Maharashtra', 'India'],
				'Eluru' => ['Andhra Pradesh', 'India'],
				'Barasat' => ['West Bengal', 'India'],
				'Kirari Suleman Nagar' => ['Delhi', 'India'],
				'Purnia' => ['Bihar', 'India'],
				'Satna' => ['Madhya Pradesh', 'India'],
				'Mau' => ['Uttar Pradesh', 'India'],
				'Sonipat' => ['Haryana', 'India'],
				'Farrukhabad' => ['Uttar Pradesh', 'India'],
				'Sagar' => ['Madhya Pradesh', 'India'],
				'Rourkela' => ['Odisha', 'India'],
				'Durg' => ['Chhattisgarh', 'India'],
				'Imphal' => ['Manipur', 'India'],
				'Ratlam' => ['Madhya Pradesh', 'India'],
				'Hapur' => ['Uttar Pradesh', 'India'],
				'Arrah' => ['Bihar', 'India'],
				'Karimnagar' => ['Telangana', 'India'],
				'Etawah' => ['Uttar Pradesh', 'India'],
				'Ambernath' => ['Maharashtra', 'India'],
				'North Dumdum' => ['West Bengal', 'India'],
				'Bharatpur' => ['Rajasthan', 'India'],
				'Begusarai' => ['Bihar', 'India'],
				'New Delhi' => ['Delhi', 'India'],
				'Gandhidham' => ['Gujarat', 'India'],
				'Baranagar' => ['West Bengal', 'India'],
				'Tiruvottiyur' => ['Tamil Nadu', 'India'],
				'Pondicherry' => ['Puducherry', 'India'],
				'Sikar' => ['Rajasthan', 'India'],
				'Thoothukudi' => ['Tamil Nadu', 'India'],
				'Rewa' => ['Madhya Pradesh', 'India'],
				'Mirzapur' => ['Uttar Pradesh', 'India'],
				'Raichur' => ['Karnataka', 'India'],
				'Pali' => ['Rajasthan', 'India'],
				'Ramagundam' => ['Telangana', 'India'],
				'Silchar' => ['Assam', 'India'],
				'Haridwar' => ['Uttarakhand', 'India'],
				'Vijayanagaram' => ['Andhra Pradesh', 'India'],
				'Tenali' => ['Andhra Pradesh', 'India'],
				'Nagercoil' => ['Tamil Nadu', 'India'],
				'Sri Ganganagar' => ['Rajasthan', 'India'],
				'Karawal Nagar' => ['Delhi', 'India'],
				'Mango' => ['Jharkhand', 'India'],
				'Thanjavur' => ['Tamil Nadu', 'India'],
				'Bulandshahr' => ['Uttar Pradesh', 'India'],
				'Uluberia' => ['West Bengal', 'India'],
				'Katni' => ['Madhya Pradesh', 'India'],
				'Sambalpur' => ['Odisha', 'India'],
				'Singrauli' => ['Madhya Pradesh', 'India'],
				'Nadiad' => ['Gujarat', 'India'],
				'Secunderabad' => ['Telangana', 'India'],
				'Naihati' => ['West Bengal', 'India'],
				'Yamunanagar' => ['Haryana', 'India'],
				'Bidar' => ['Karnataka', 'India'],
				'Pallavaram' => ['Tamil Nadu', 'India'],
				'Bidhan Nagar' => ['West Bengal', 'India'],
				'Munger' => ['Bihar', 'India'],
				'Panchkula' => ['Haryana', 'India'],
				'Burhanpur' => ['Madhya Pradesh', 'India'],
				'Raurkela Industrial Township' => ['Odisha', 'India'],
				'Kharagpur' => ['West Bengal', 'India'],
				'Dindigul' => ['Tamil Nadu', 'India'],
				'Gandhinagar' => ['Gujarat', 'India'],
				'Hospet' => ['Karnataka', 'India'],
				'Nangloi Jat' => ['Delhi', 'India'],
				'Malda' => ['West Bengal', 'India'],
				'Ongole' => ['Andhra Pradesh', 'India'],
				'Deoghar' => ['Jharkhand', 'India'],
				'Chapra' => ['Bihar', 'India'],
				'Haldia' => ['West Bengal', 'India'],
				'Khandwa' => ['Madhya Pradesh', 'India'],
				'Nandyal' => ['Andhra Pradesh', 'India'],
				'Morena' => ['Madhya Pradesh', 'India'],
				'Bhind' => ['Madhya Pradesh', 'India'],
				'Bhalswa Jahangir Pur' => ['Delhi', 'India'],
				'Madhyamgram' => ['West Bengal', 'India'],
				'Bhiwani' => ['Haryana', 'India'],
				'Berhampore' => ['West Bengal', 'India'],
				'Ambala' => ['Haryana', 'India'],
				'Morbi' => ['Gujarat', 'India'],
				'Fatehpur' => ['Uttar Pradesh', 'India'],
				'Bijnor' => ['Uttar Pradesh', 'India'],
				'Raebareli' => ['Uttar Pradesh', 'India'],
				'Khora' => ['Uttar Pradesh', 'India'],
				'Bhusawal' => ['Maharashtra', 'India'],
				'Orai' => ['Uttar Pradesh', 'India'],
				'Bahraich' => ['Uttar Pradesh', 'India'],
				'Phusro' => ['Jharkhand', 'India'],
				'Vellore' => ['Tamil Nadu', 'India'],
				'Mahesana' => ['Gujarat', 'India'],
				'Sambhal' => ['Uttar Pradesh', 'India'],
				'Raiganj' => ['West Bengal', 'India'],
				'Sirsa' => ['Haryana', 'India'],
				'Danapur' => ['Bihar', 'India'],
				'Serampore' => ['West Bengal', 'India'],
				'Sultan Pur Majra' => ['Delhi', 'India'],
				'Guna' => ['Madhya Pradesh', 'India'],
				'Jaunpur' => ['Uttar Pradesh', 'India'],
				'Panvel' => ['Maharashtra', 'India'],
				'Shivpuri' => ['Madhya Pradesh', 'India'],
				'Surendranagar Dudhrej' => ['Gujarat', 'India'],
				'Unnao' => ['Uttar Pradesh', 'India'],
				'Chinsurah' => ['West Bengal', 'India'],
				'Alappuzha' => ['Kerala', 'India'],
				'Kottayam' => ['Kerala', 'India'],
				'Machilipatnam' => ['Andhra Pradesh', 'India'],
				'Shimla' => ['Himachal Pradesh', 'India'],
				'Adoni' => ['Andhra Pradesh', 'India'],
				'Udupi' => ['Karnataka', 'India'],
				'Katihar' => ['Bihar', 'India'],
				'Proddatur' => ['Andhra Pradesh', 'India'],
				'Mahbubnagar' => ['Telangana', 'India'],
				'Saharsa' => ['Bihar', 'India'],
				'Dibrugarh' => ['Assam', 'India'],
				'Jorhat' => ['Assam', 'India'],
				'Hazaribagh' => ['Jharkhand', 'India'],
				'Hindupur' => ['Andhra Pradesh', 'India'],
				'Nagaon' => ['Assam', 'India'],
				'Sasaram' => ['Bihar', 'India'],
				'Hajipur' => ['Bihar', 'India'],
				'Bongaigaon' => ['Assam', 'India'],
				'North Barrackpur' => ['West Bengal', 'India'],
				'Karaikudi' => ['Tamil Nadu', 'India'],
				'Hooghly' => ['West Bengal', 'India'],
				'Bhuj' => ['Gujarat', 'India'],
				'Tiruvannamalai' => ['Tamil Nadu', 'India'],
				'Kumbakonam' => ['Tamil Nadu', 'India'],
				'Modinagar' => ['Uttar Pradesh', 'India'],
				'Unnao' => ['Uttar Pradesh', 'India'],
				'Chinsurah' => ['West Bengal', 'India'],
				'Alappuzha' => ['Kerala', 'India'],
				'Kottayam' => ['Kerala', 'India'],
				'Machilipatnam' => ['Andhra Pradesh', 'India'],
				'Shimla' => ['Himachal Pradesh', 'India'],
				'Adoni' => ['Andhra Pradesh', 'India'],
				'Udupi' => ['Karnataka', 'India'],
				'Katihar' => ['Bihar', 'India'],
				'Proddatur' => ['Andhra Pradesh', 'India'],
				'Mahbubnagar' => ['Telangana', 'India'],
				'Saharsa' => ['Bihar', 'India'],
				'Dibrugarh' => ['Assam', 'India'],
				'Jorhat' => ['Assam', 'India'],
				'Hazaribagh' => ['Jharkhand', 'India'],
				'Hindupur' => ['Andhra Pradesh', 'India'],
				'Nagaon' => ['Assam', 'India'],
				'Sasaram' => ['Bihar', 'India'],
				'Hajipur' => ['Bihar', 'India'],
				'Bongaigaon' => ['Assam', 'India'],
				'North Barrackpur' => ['West Bengal', 'India'],
				'Karaikudi' => ['Tamil Nadu', 'India'],
				'Hooghly' => ['West Bengal', 'India'],
				'Bhuj' => ['Gujarat', 'India'],
				'Tiruvannamalai' => ['Tamil Nadu', 'India'],
				'Kumbakonam' => ['Tamil Nadu', 'India'],
				'Modinagar' => ['Uttar Pradesh', 'India'],
				'Mahbubnagar' => ['Telangana', 'India'],
				'Godhra' => ['Gujarat', 'India'],
				'Porbandar' => ['Gujarat', 'India'],
				'Palakkad' => ['Kerala', 'India'],
				'Pudukkottai' => ['Tamil Nadu', 'India'],
				'Hosur' => ['Tamil Nadu', 'India'],
				'Faridkot' => ['Punjab', 'India'],
				'Alappuzha' => ['Kerala', 'India'],
				'Kottayam' => ['Kerala', 'India'],
				'Machilipatnam' => ['Andhra Pradesh', 'India'],
				'Shimla' => ['Himachal Pradesh', 'India'],
				'Adoni' => ['Andhra Pradesh', 'India'],
				'Udupi' => ['Karnataka', 'India'],
				'Katihar' => ['Bihar', 'India'],
				'Proddatur' => ['Andhra Pradesh', 'India'],
				'Mahbubnagar' => ['Telangana', 'India'],
				'Saharsa' => ['Bihar', 'India'],
				'Dibrugarh' => ['Assam', 'India'],
				'Jorhat' => ['Assam', 'India'],
				'Hazaribagh' => ['Jharkhand', 'India'],
				'Hindupur' => ['Andhra Pradesh', 'India'],
				'Nagaon' => ['Assam', 'India'],
				'Sasaram' => ['Bihar', 'India'],
				'Hajipur' => ['Bihar', 'India'],
				'Bongaigaon' => ['Assam', 'India'],
				'North Barrackpur' => ['West Bengal', 'India'],
				'Karaikudi' => ['Tamil Nadu', 'India'],
				'Hooghly' => ['West Bengal', 'India'],
				'Bhuj' => ['Gujarat', 'India'],
				'Tiruvannamalai' => ['Tamil Nadu', 'India'],
				'Kumbakonam' => ['Tamil Nadu', 'India'],
				'Modinagar' => ['Uttar Pradesh', 'India'],
				'Mahbubnagar' => ['Telangana', 'India'],
				'Godhra' => ['Gujarat', 'India'],
				'Porbandar' => ['Gujarat', 'India'],
				'Palakkad' => ['Kerala', 'India'],
				'Anand' => ['Gujarat', 'India'],
				'Pudukkottai' => ['Tamil Nadu', 'India'],
				'Hosur' => ['Tamil Nadu', 'India'],
				'Faridkot' => ['Punjab', 'India']
			];

			// Initialize an empty array to store matching cities
			$matchingCities = ['city' =>'','state' => '','country' =>''];

			// Loop through the Indian cities list and check for matches in the text
			foreach ($indianCities as $city => $details) {
				// Check if the city name appears in the text
				if (stripos($text, $city) !== false) {
					// If found, add the city along with its state and country to the result array
					$matchingCities = [
						'city' => $city,
						'state' => $details[0],
						'country' => $details[1]
					];
				}
			}

			// Return the matching cities
			return  $matchingCities;
	}
	private function parseDateOfBirth($text){
		$textData = $text;

		// Define common date formats
		$indianDateFormats = [
			'd/m/Y', 'd-m-Y', 'd.m.Y', 'd-m-y', 'd.m.y', 'd/m/y'
		];
		$westernDateFormats = [
			'm/d/Y', 'm-d-Y', 'm.d.Y', 'm-d-y', 'm.d.y', 'm/d/y'
		];
		
		// Formats with text-based months
		$textMonthFormats = [
			'F j, Y',  // August 31, 1990
			'M j, Y',  // Aug 31, 1990
			'F j Y',   // August 31 1990 (without commas)
			'M j Y'    // Aug 31 1990 (without commas)
		];
		//    dd("dskjdkghkfdhgk");
		$dateFormats = array_merge($indianDateFormats, $westernDateFormats, $textMonthFormats);

		// Reverse document lines
		$lines = array_reverse(preg_split("/\r\n|\n|\r/", $textData));

		foreach ($lines as $line) {
			// Clean the line: remove null bytes & control characters
			$line = preg_replace('/[\x00-\x1F\x7F]/u', '', $line);
			$line = trim($line);

			if (empty($line)) {
				continue;
			}

			// Remove unwanted text and markers
			$line = str_replace(
				["DOB:", "th", "rd", "st", "nd", ":", " : ", "Date of birth", "Date Of Birth", "date of birth", "DOB", " "],
				"",
				$line
			);

			// Skip if empty after cleanup
			if (empty($line)) {
				continue;
			}

			foreach ($dateFormats as $format) {
				// Attempt to parse the date
				$date = @date_create_from_format($format, $line);

				// Skip invalid parses
				if ($date === false) {
					continue;
				}

				// Create the range dates
				$tenYearsAgo = date_sub(date_create(), date_interval_create_from_date_string('10 years'));
				$hundredFiftyYearsAgo = date_sub(date_create(), date_interval_create_from_date_string('150 years'));

				// Validate date is realistic and within range
				if ($date <= $tenYearsAgo &&
					$date >= $hundredFiftyYearsAgo &&
					checkdate($date->format('m'), $date->format('d'), $date->format('Y'))) {

					return response()->json([
						'dob' => $date->format('Y-m-d')
					]);
				}
			}
		}


		// Return null if no valid date is found
		return response()->json([
			'dob' => null
		]);
	}  
	public function parseCurrentEmployer($text)
	{
			// Resume text extracted from the request
			$resumeText = $text;

			// List of formats indicating current employment status
			$currentEmploymentFormats = [
				// Existing formats
				"Actively Employed",
				"Currently Employed",
				"Currently Engaged As",
				"Currently Engaged With",
				"Currently Occupied As",
				"Currently Occupied With",
				"Currently Holding Position Of",
				"Currently Holding Role Of",
				"Currently in Active Employment",
				"Currently in Present Job",
				"Currently in Present Role",
				"Currently in Present Employment",
				"Currently in Current Position",
				"Currently in Present Position",
				"Currently on the Job",
				"Currently on the Position",
				"Currently on the Role",
				"Currently on Employment",
				"Currently in Service",
				"Currently in Work",
				"Currently Serving As",
				"Currently Serving With",
				"Currently Working As",
				"Currently Working With",
				"Employed As",
				"Employed At",
				"Employed In",
				"Employed With",
				"Engaged in Employment",
				"Engaged in Job",
				"Engaged in Position",
				"Engaged in Role",
				"Holding Position Of",
				"Holding Role Of",
				"In Active Employment",
				"In Current Position",
				"In Current Role",
				"In Current Employment",
				"In Current Job",
				"In Current Position",
				"In Present Employment",
				"In Present Job",
				"In Present Position",
				"In Present Role",
				"Job Status: Employed",
				"Occupation: Employed",
				"Presently Employed At",
				"Presently Employed With",
				"Presently Engaged As",
				"Presently Engaged With",
				"Presently Occupied As",
				"Presently Occupied With",
				"Presently Holding Position Of",
				"Presently Holding Role Of",
				"Presently in Active Employment",
				"Presently in Current Position",
				"Presently in Current Role",
				"Presently in Current Employment",
				"Presently in Current Job",
				"Presently in Present Position",
				"Presently in Present Role",
				"Presently on the Job",
				"Presently on the Position",
				"Presently on the Role",
				"Presently on Employment",
				"Presently in Service",
				"Presently in Work",
				"Presently Serving As",
				"Presently Serving With",
				"Presently Working As",
				"Presently Working With",
				"Still Holding Position Of",
				"Still Holding Role Of",
				"Still in Active Employment",
				"Still in Current Position",
				"Still in Current Role",
				"Still in Current Employment",
				"Still in Current Job",
				"Still in Present Employment",
				"Still in Present Job",
				"Still in Present Position",
				"Still in Present Role",
				"Still on the Job",
				"Still on the Position",
				"Still on the Role",
				"Still on Employment",
				"Still in Service",
				"Still in Work",
				"Still Serving As",
				"Still Serving With",
				"Still Working As",
				"Still Working With",
				"Actively Working at",
				"Current Employer"
			];

			// List of additional formats indicating experience
			$experienceKeywords = [
				// Existing keywords
				"Worked at", "Previous Employer", "Company", "Employment History",
				"Career", "Professional Background", "Previous Work Experience", "Work History",
				"Employment Record", "Job History", "Work Experience", "Experience with",
				"Employer", "Prior Work", "Former Employer", "Company Name", "Previous Job",
				"Worked As", "Employment Details",
				"Work Experience", "Professional Experience", "Previous Experience",
				"Experience with", "Professional Experience Summary", "Job Experience", "Previous Positions",
				"Job Roles", "Roles and Responsibilities", "Current Role", "Current Job",
				"Current Employment", "Present Role", "Present Job", "Current Position",
				"Present Employment", "Job Profile", "Current Company", "Present Company", "Experience", "Experience in",
			];

			// Common designation keywords to exclude from employer name
			$designationKeywords = [
				// Existing keywords
				"CEO", "CTO", "CFO", "COO", "President", "Vice President", "Director", "Manager", "Supervisor", "Lead",
				"Administrator", "Coordinator", "Consultant", "Analyst", "Engineer", "Developer", "Designer", "Specialist",
				"Officer", "Executive", "Assistant", "Representative", "Associate", "Advisor", "Agent", "Technician",
				"Operator", "Accountant", "Auditor", "Counselor", "Educator", "Trainer", "Coach", "Therapist", "Physician",
				"Nurse", "Scientist", "Researcher", "Writer", "Editor", "Designer", "Artist", "Photographer", "Architect",
				"Lawyer", "Attorney", "Advocate", "Paralegal", "Secretary", "Receptionist", "Clerk", "Assistant", "Aide",
				"Coordinator", "Assistant", "Intern", "Trainee", "Volunteer", "Freelancer", "Contractor"
			];

			// Additional conditions and patterns
			$additionalConditions = [
				// Additional conditions for better results
				// For example:
				"Actively Working at", "Currently Holding Position as", "Currently Engaged with Company",
				"Currently Employed as"
			];

			// Combine all conditions and keywords
			$allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

			// dd($allConditions);

			// Check if any of the formats indicate current employment status
			foreach ($allConditions as $condition) {
				// Check for the condition in the resume text
				if (stripos($resumeText, $condition) !== false) {
					// If a match is found, extract the employer name
					// dd($resumeText, $condition, $designationKeywords);
					$employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);
					// Return the condition and employer name
					return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
				}
			}

			// If no match is found, return null
			return response()->json(['current_employer_format' => null, 'employer_name' => null]);
	}
    // Helper function to extract the employer name
    private function extractEmployerName($resumeText, $keyword, $designationKeywords)
    {
        // Find the position of the keyword in the text
        $keywordIndex = stripos($resumeText, $keyword);
        // Extract text after the keyword
        $textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
        // Split text into individual words
		// trim(preg_replace('/\s+/', ' ', $string));
        $words = explode(" ", trim(preg_replace('/\s+/', ' ', $textAfterKeyword)));
        // Limit words to 20
        $limitedWords = array_slice($words, 0, 20);
        $nounCount = 0;
        $employerName = '';
        // Iterate through words to find the first noun
        foreach ($limitedWords as $word) {
            if ($this->isNoun($word) && !$this->isDesignation($word, $designationKeywords)) {
                // Concatenate the noun to employerName
                $employerName .= ($employerName ? ' ' : '') . $word;
                $nounCount++;
                // Break the loop if maximum adjacent nouns reached
                if ($nounCount >= 3) {
                    break;
                }
            } elseif ($nounCount > 0) {
               	 // Break the loop if a non-noun is encountered after finding at least one noun
                break;
            }
        }
        return $employerName ?: null; // If no noun is found
    }
    // Subfunction to determine if a word is a noun
    private function isNoun($word)
    {
        // Check if word is not empty
        return !empty($word);
    }
    // Subfunction to determine if a word is a common designation keyword
    private function isDesignation($word, $designationKeywords)
    {
        // Check if word exists in the list of designation keywords
        return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
    }
   // Main function to parse the current employer from resume text
	private function parseCurrentEmployerw($text)
	{
		// Resume text extracted from the request
		$resumeText = $text;

		// List of formats indicating current employment status
		$currentEmploymentFormats = [
			// Existing formats
			"Actively Employed",
			"Currently Employed",
			"Currently Engaged As",
			"Currently Engaged With",
			"Currently Occupied As",
			"Currently Occupied With",
			"Currently Holding Position Of",
			"Currently Holding Role Of",
			"Currently in Active Employment",
			"Currently in Present Job",
			"Currently in Present Role",
			"Currently in Present Employment",
			"Currently in Current Position",
			"Currently in Present Position",
			"Currently on the Job",
			"Currently on the Position",
			"Currently on the Role",
			"Currently on Employment",
			"Currently in Service",
			"Currently in Work",
			"Currently Serving As",
			"Currently Serving With",
			"Currently Working As",
			"Currently Working With",
			"Employed As",
			"Employed At",
			"Employed In",
			"Employed With",
			"Engaged in Employment",
			"Engaged in Job",
			"Engaged in Position",
			"Engaged in Role",
			"Holding Position Of",
			"Holding Role Of",
			"In Active Employment",
			"In Current Position",
			"In Current Role",
			"In Current Employment",
			"In Current Job",
			"In Current Position",
			"In Present Employment",
			"In Present Job",
			"In Present Position",
			"In Present Role",
			"Job Status: Employed",
			"Occupation: Employed",
			"Presently Employed At",
			"Presently Employed With",
			"Presently Engaged As",
			"Presently Engaged With",
			"Presently Occupied As",
			"Presently Occupied With",
			"Presently Holding Position Of",
			"Presently Holding Role Of",
			"Presently in Active Employment",
			"Presently in Current Position",
			"Presently in Current Role",
			"Presently in Current Employment",
			"Presently in Current Job",
			"Presently in Present Position",
			"Presently in Present Role",
			"Presently on the Job",
			"Presently on the Position",
			"Presently on the Role",
			"Presently on Employment",
			"Presently in Service",
			"Presently in Work",
			"Presently Serving As",
			"Presently Serving With",
			"Presently Working As",
			"Presently Working With",
			"Still Holding Position Of",
			"Still Holding Role Of",
			"Still in Active Employment",
			"Still in Current Position",
			"Still in Current Role",
			"Still in Current Employment",
			"Still in Current Job",
			"Still in Present Employment",
			"Still in Present Job",
			"Still in Present Position",
			"Still in Present Role",
			"Still on the Job",
			"Still on the Position",
			"Still on the Role",
			"Still on Employment",
			"Still in Service",
			"Still in Work",
			"Still Serving As",
			"Still Serving With",
			"Still Working As",
			"Still Working With"
		];

		// List of additional formats indicating experience
		$experienceKeywords = [
			// Existing keywords
			"Experience:", "Worked at", "Previous Employer:", "Company:", "Employment History:",
			"Career:", "Professional Background:", "Previous Work Experience:", "Work History:",
			"Employment Record:", "Job History:", "Work Experience:", "Experience with",
			"Employer:", "Prior Work:", "Former Employer:", "Company Name:", "Previous Job:",
			"Worked As:", "Employment Details:",
			"Work Experience:", "Professional Experience:", "Previous Experience:", "Experience in",
			"Experience with", "Professional Experience Summary:", "Job Experience:", "Previous Positions:",
			"Job Roles:", "Roles and Responsibilities:", "Current Role:", "Current Job:",
			"Current Employment:", "Present Role:", "Present Job:", "Current Position:",
			"Present Employment:", "Job Profile:", "Current Company:", "Present Company:"
		];

		// Common designation keywords to exclude from employer name
		$designationKeywords = [
			// Existing keywords
			"CEO", "CTO", "CFO", "COO", "President", "Vice President", "Director", "Manager", "Supervisor", "Lead",
			"Administrator", "Coordinator", "Consultant", "Analyst", "Engineer", "Developer", "Designer", "Specialist",
			"Officer", "Executive", "Assistant", "Representative", "Associate", "Advisor", "Agent", "Technician",
			"Operator", "Accountant", "Auditor", "Counselor", "Educator", "Trainer", "Coach", "Therapist", "Physician",
			"Nurse", "Scientist", "Researcher", "Writer", "Editor", "Designer", "Artist", "Photographer", "Architect",
			"Lawyer", "Attorney", "Advocate", "Paralegal", "Secretary", "Receptionist", "Clerk", "Assistant", "Aide",
			"Coordinator", "Assistant", "Intern", "Trainee", "Volunteer", "Freelancer", "Contractor"
		];

		// Additional conditions and patterns
		$additionalConditions = [
			// Additional conditions for better results
			// For example:
			"Actively Working at", "Currently Holding Position as", "Currently Engaged with Company",
			"Currently Employed as"
		];

		// Combine all conditions and keywords
		$allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

		// Check if any of the formats indicate current employment status
		foreach ($allConditions as $condition) {
			// Check for the condition in the resume text
			if (stripos($resumeText, $condition) !== false) {
				// If a match is found, extract the employer name
				$employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);
				// Return the condition and employer name
				return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
			}
		}

		// If no match is found, return null
		return response()->json(['current_employer_format' => null, 'employer_name' => null]);
	}
   // Helper function to extract the employer name
	private function extractEmployerNamew($resumeText, $keyword, $designationKeywords)
	{
		// Find the position of the keyword in the text
		$keywordIndex = stripos($resumeText, $keyword);
		// Extract text after the keyword
		$textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
		// Split text into individual words
		$words = explode(" ", $textAfterKeyword);
		// Limit words to 50
		$limitedWords = array_slice($words, 0, 50);
		// Iterate through words to find the first noun
		foreach ($limitedWords as $word) {
			if ($this->isNoun($word) && !$this->isDesignation($word, $designationKeywords)) {
				return $word;
			}
		}
		return null; // If no noun is found
	}
    // Subfunction to determine if a word is a noun
	private function isNounw($word)
	{
		// Check if word is not empty
		return !empty($word);
	}
   // Subfunction to determine if a word is a common designation keyword
	private function isDesignationw($word, $designationKeywords)
	{
		// Check if word exists in the list of designation keywords
		return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
	}


}
