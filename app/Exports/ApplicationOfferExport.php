<?php

namespace App\Exports;

use App\Job;
use App\JobCategory;
use App\JobLocation;
use App\Client;
use App\JobQuestion;
use App\JobSkill;
use App\Question;
use App\Skill;
use App\Company;
use App\User;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport; 

class ApplicationOfferExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $filters;
    protected $data;
	protected $loginUserId;
	

    public function __construct(array $filters, array $data)
    {
        $this->filters 		= $filters;
        $this->data 		= $data;
		$this->loginUserId 	= $this->filters['loginUserId'];
    }

    public function collection()
    {
        
		
		
		 $chkAdminRole = DB::table('roles')
						->select('roles.id')
						->where('id', $this->loginUserId)
						->where('name', 'admin')
						->count();
						
		 if($chkAdminRole > 0)
		 {
		 	
				$categories 	=	DB::table('offer_job_applications')
									->select(
													'offer_job_applications.id', 
													'offer_job_applications.full_name',
													
													'jobs.title',
													'clients.name',
													'jobs.id as jobid',
													DB::raw('1 as REP'),
													'offer_job_applications.proposed_ctc',
													
													'offer_job_applications.any_offer',
													'offer_job_applications.document',
													'offer_job_applications.sub_for_approval',
													'offer_job_applications.offer_approved',
													
													'offer_job_applications.offer_release',
													'offer_job_applications.offer_acceptance',
													'offer_job_applications.joined',
													'offer_job_applications.joined_date' 				
											)
				->join('jobs', 'jobs.id', 'offer_job_applications.job_id')
				->join('clients', 'clients.id', 'jobs.client_id')
				->join('master_status', 'master_status.id', 'offer_job_applications.master_status_id');
				//$categories		=	$categories->where('users.type', 1);
				$categories 	=	$categories->where('offer_job_applications.company_id', $this->filters['company_id'] );	
				$categories 	=	$categories->where('offer_job_applications.master_status_id', 5);	
				$categories 	=	$categories->orderBy('offer_job_applications.id', 'DESC');
				
				
				//$categories 	= $categories->where('job_categories.company_id', $this->user->company_id);
			//->take(10);
			
		 	
		 
		 }
		 else
		 {
		 	
							$categories = Job::select(
							
								'jobs.id', 
								'jobs.created_at', 
								'jobs.status', 
								'jobs.title', 
								'clients.name as clients_name', 										
															
								DB::raw('CONCAT(jobs.min_exp,"-",jobs.max_exp) as exp'),
								'jobs.total_positions',
								DB::raw('1 as location'),
								DB::raw('1 as REP'),
								DB::raw('1 as HR'),			
								
								DB::raw("count(job_applications.id) as allCandidate"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
								
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
								DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
								
								'jobs.start_date', 'jobs.end_date'
								
							)
							
						->join('job_assign', 'job_assign.job_id', 'jobs.id')
						->join('clients', 'clients.id', 'jobs.client_id')
						->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
						->where('jobs.id', '>', '0')
						->where('job_assign.user_id', '=', $this->loginUserId)
						->take(10);
						$categories->where('job_assign.user_id', $this->loginUserId); 
		
			
		
		}				
		
		$categories 			= $categories->skip($this->filters['start'])->take($this->filters['length'])->get();
				
		
		if($this->filters['start'] == 0){
			$cnt	=	$this->filters['start']+1;	
		}else{
			$cnt	=	$this->filters['start'];		
		}
					
		foreach($categories as $jval)
		{
				
				
				$jval->id				=	$cnt;
				
				if($jval->jobid > 0)
				{
						$contactHtml		= '';
						$jobid 				= $jval->jobid;
						
						$client_loc_count 	= DB::table('client_location_jobs')
												->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
												->join('countries', 'countries.id', '=', 'client_location.country_id')
												->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
												//->where('jobs.status', 'active')
												->where('client_location_jobs.job_id', $jobid)
												->count();
						$client_loc_arr	 	= DB::table('client_location_jobs')
												->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
												->join('countries', 'countries.id', '=', 'client_location.country_id')
												->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
												//->where('jobs.status', 'active')
												->where('client_location_jobs.job_id', $jobid)
												->get();
												
					if($client_loc_count > 0)
					{
						$contactHtml 		.= '';
						foreach($client_loc_arr as $locArr)
						{
							
							$contactHtml 		.= 'Address :: '.$locArr->address.' City :: '.$locArr->city.'Postcode :: '.$locArr->postcode.' Country :: '.$locArr->country_name;
						}
						
	
					}
					else
					{
						$contactHtml = 'Any';
					}	
				
					$jval->jobid		= $contactHtml ;							
										
				}
				
				if($jval->REP > 0)
				{
						$contactHtml 			=	'';
						$jobid 					=	$jval->jobid;
						$client_contact_arr 	=	DB::table('client_contact_info')
														->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
														->select('client_contact_info.contact_person_name')
														->where('client_contact_info_jobs.job_id', $jobid)
														->get();						
						foreach($client_contact_arr as $contArr)
						{									
								$contactHtml 	.= ' Name 	: '.@$contArr->contact_person_name;															
						}
						$jval->REP				=	$contactHtml;				
				  
				}
				
				
				if($jval->any_offer > 0){
					$jval->any_offer = 'Yes';
				}else{
					$jval->any_offer = 'No';	 
				}
				
				if($jval->document > 0){
					$jval->document = 'Yes';
				}else{
					$jval->document = 'No';	 
				}
								
				if($jval->sub_for_approval > 0){
					$jval->sub_for_approval = 'Yes';
				}else{
					$jval->sub_for_approval = 'No';	 
				}
								
				if($jval->offer_approved > 0){
					$jval->offer_approved = 'Yes';
				}else{
					$jval->offer_approved = 'No';	 
				}
				
				if($jval->offer_release > 0){
					$jval->offer_release = 'Yes';
				}else{
					$jval->offer_release = 'No';	 
				}
				
				if($jval->offer_acceptance > 0){
					$jval->offer_acceptance = 'Yes';
				}else{
					$jval->offer_acceptance = 'No';	 
				}
						
				if($jval->joined > 0){
					$jval->joined = 'Yes';
				}else{
					$jval->joined = 'No';	 
				}
				 
				$cnt	=	$cnt+1; 		
				
		}
		
		
		//echo '<pre>'; print_r($client); exit; 
		
		
		 return $categories;				
    }
	
	//$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
	

    public function headings(): array
    {
        return ['SNo', 'Applicants', 'Requisition', 'Client', 'Loc', 'REP', 'P.CTC', 'AOF', 'DOC', 'SFA', 'OAP', 'ORE', 'OAC', 'JOI', 'J.Date'];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event) {
                $event->writer->getProperties()->setTitle('Job Applications')->setDescription('job-applications file')->setCreator('Recruit')->setCompany($this->data['company']);
            },
            AfterSheet::class => function(AfterSheet $event) {
                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray($styleArray);
            },
        ];
    }
}