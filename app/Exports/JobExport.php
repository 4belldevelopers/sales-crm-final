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

class JobExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
		 	
			$categories = DB::table('jobs')
							->select(
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
											
											DB::raw('1 as allCandidate'),
											DB::raw('1 as Sourcing'),
											DB::raw('1 as Screening'),
											
											DB::raw('1 as Interview'),
											DB::raw('1 as Negotiation'),
											DB::raw('1 as Offered'),
											DB::raw('1 as Joined'),
											DB::raw('1 as Rejected')
			
				
									)
		->join('clients', 'clients.id', 'jobs.client_id')
		->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
		->where('jobs.id', '>', '0')
		->where('jobs.company_id', '=', $this->filters['company_id'])
		
		;
		
		$categories->groupBy('jobs.id');
		$categories->orderByRaw( "FIELD(jobs.status, 'active', 'hold', 'inactive')" );
		$categories->orderBy('jobs.id', 'DESC');
			//->take(10);
			
		 	/*$categories = Job::select(
										'jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.title', 'companies.company_name', 
										'jobs.job_requirement',
										'jobs.total_positions', DB::raw('1 as location'), 'job_categories.name as category_name',
										'clients.name as clients_name', 'jobs.start_date', 'jobs.end_date', 'jobs.employment_type',
										'jobs.priority', 'jobs.min_exp',  'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'qualification_master.name as qualification_name',
										 'jobs.job_description'										  
				 					)
				 	->leftJoin('companies', 'companies.id', 'jobs.company_id')
					->leftJoin('job_categories', 'job_categories.id', 'jobs.category_id')
					->leftJoin('clients', 'clients.id', 'jobs.client_id')
					->leftJoin('qualification_master', 'qualification_master.id', 'jobs.qualification')
					
					->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
					->where('jobs.id', '>', '0')
					->take(1);*/
		 
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
		
			/*$categories = Job::select(
										'jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.title', 'companies.company_name', 
										'jobs.job_requirement',
										'jobs.total_positions', DB::raw('1 as location'), 'job_categories.name as category_name',
										'clients.name as clients_name', 'jobs.start_date', 'jobs.end_date',  'jobs.employment_type',
										'jobs.priority', 'jobs.min_exp',  'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'qualification_master.name as qualification_name',
										 'jobs.job_description'										  
				 					)
					->leftJoin('companies', 'companies.id', 'jobs.company_id')
					->leftJoin('job_categories', 'job_categories.id', 'jobs.category_id')
					->leftJoin('clients', 'clients.id', 'jobs.client_id')
					->leftJoin('qualification_master', 'qualification_master.id', 'jobs.qualification')
					->leftJoin('job_assign', 'job_assign.job_id', 'jobs.id')
					->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
					->where('jobs.id', '>', '0')
					->where('job_assign.user_id', '=', $this->loginUserId)
					->take(1);
			$categories->where('job_assign.user_id', $this->loginUserId); 	*/
		
		}				
		
		$categories 	= $categories->skip($this->filters['start'])->take($this->filters['length'])->get();
		
		if($this->filters['start'] == 0){
			$cnt	=	$this->filters['start']+1;	
		}else{
			$cnt	=	$this->filters['start'];		
		}
					
		foreach($categories as $jval)
		{
				$job_id					=	$jval->id;
								

				$ja 					= DB::table('job_applications')
											->select(
														
														DB::raw("count(job_applications.id) as allCandidate"),
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
														
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),			
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
														DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected")
											)
											->where('job_applications.job_id', $job_id)
											->first();
				
				$jval->id				=	$cnt;
				
				
				$originalDate 			= $jval->created_at;
				$jval->created_at 		= date("d-M-Y", strtotime($originalDate));
				
				
				
				
				
				$contactHtml = '';
				$client_loc_count = DB::table('client_location_jobs')
									->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
									->join('countries', 'countries.id', '=', 'client_location.country_id')
									->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
									//->where('jobs.status', 'active')
									->where('client_location_jobs.job_id', $job_id)
									->count();
									
				$client_loc_arr = DB::table('client_location_jobs')
								->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
								->join('countries', 'countries.id', '=', 'client_location.country_id')
								->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
								//->where('jobs.status', 'active')
								->where('client_location_jobs.job_id', $job_id)
								->get();
								
				if($client_loc_count > 0)
				{
					foreach($client_loc_arr as $locArr)
					{						
						$contactHtml 		.= 'Address :'.$locArr->address.'   City 	: '.$locArr->city.'   Postcode : '.$locArr->postcode.'  Country : '.$locArr->country_name;																		
					}
				}
				else
				{
					$contactHtml = 'None';
				}				
			    $jval->location		= $contactHtml;	
				
				

				 $REP_html	=	'';	
				 $client_contuser_arr = DB::table('client_contact_info_jobs')
								->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
								->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
								->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email',  'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')
								->where('client_contact_info_jobs.job_id', $job_id)
								->get();
					foreach($client_contuser_arr as $cliArr)
					{

						$REP_html 		.= 'Client 	: '.$cliArr->name.'   
															Name 	: '.$cliArr->contact_person_name.'  
															Email 	: '.$cliArr->contact_person_email.'   
															Phone 	: '.$cliArr->contact_person_number.'   
															Designation : '.$cliArr->contact_person_designation;
														

					}
				$jval->REP		= $REP_html;	
				
				
				
				//ASSIGN 
			 $assignArr 			=  DB::table('job_assign')
										->join('users', 'users.id', '=', 'job_assign.user_id')
										->select('users.id', 'users.name', 'users.type')
										->where('job_assign.job_id', '=', $job_id)
										->orderBy('users.name', 'ASC')
										->get();
			$html			= '';
			$cnt    		= 0;
			$consultancy 	= '';
			$team 			= '';
			$hr_face_value	=	'';

			if (!is_null($assignArr)) {
				foreach ($assignArr as $teamArr) {
					$cnt = $cnt + 1;	
					if($teamArr->type==1){
						if(empty($consultancy)){
							$consultancy 	.= $teamArr->name;	
						}
						else{
							$consultancy 	.= ' , '.$teamArr->name;
						}
					}
					else{
						if(empty($team)){
							$team 			.=	$teamArr->name;
							$fname			=	$teamArr->name;
							$fnameArr		=	explode(" ", $fname);
							foreach($fnameArr as $fval){
								$hr_face_value	.=	substr($fval, 0, 1);	
							}
						}
						else{
							$team 	.= ' , '.$teamArr->name;	
						}
					}
				}
			}
			$jval->HR		= $consultancy.' '.$team;
			
			
			
				$jval->allCandidate		= $ja->allCandidate;
				if($ja->allCandidate < 1){
					$jval->allCandidate		= '0';
				}					
				$jval->Sourcing			= $ja->Sourcing;
				if($ja->Sourcing < 1){
					$jval->Sourcing		= '0';
				}	
				$jval->Screening		= $ja->Screening;	
				if($ja->Screening < 1){
					$jval->Screening		= '0';
				}	
				$jval->Interview		= $ja->Interview;
				if($ja->Interview < 1){
					$jval->Interview		= '0';
				}	
				$jval->Negotiation		= $ja->Negotiation;
				if($ja->Negotiation < 1){
					$jval->Negotiation		= '0';
				}
				$jval->Offered			= $ja->Offered;
				if($ja->Offered < 1){
					$jval->Offered		= '0';
				}
				$jval->Joined			= $ja->Joined;
				if($ja->Joined < 1){
					$jval->Joined		= '0';
				}
				$jval->Rejected			= $ja->Rejected;
				if($ja->Rejected < 1){
					$jval->Rejected		= '0';
				}
							 
				$cnt	=	$cnt+1; 		
				
		}
		
		
		//echo '<pre>'; print_r($client); exit; 
		
		
		 return $categories;				
    }
	
	//$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
	

    public function headings(): array
    {
        return [
					'SNo', 'Date', 'Status', 'Job Title',  'Hiring', 
					'Exp', 'No', 'Loc', 'REP', 'HR', 
					'TOT', 'SOU', 'SCR', 'INT', 'NEO',
					'OFF', 'JOI', 'REJ'
					
			   ];
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