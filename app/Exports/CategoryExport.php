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

class CategoryExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
		 	
				$categories 	=	DB::table('job_categories')
									->select(
													'job_categories.id', 
													'job_categories.name'				
											);
				//->join('consultancy', 'consultancy.consultancy_id', 'users.id');
		
				//$categories		=	$categories->where('users.type', 1);
				$categories 	=	$categories->where('job_categories.company_id', $this->filters['company_id'] );		
				$categories 	=	$categories->orderBy('job_categories.id', 'DESC');
				
				
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
						 
				$cnt	=	$cnt+1; 		
				
		}
		
		
		//echo '<pre>'; print_r($client); exit; 
		
		
		 return $categories;				
    }
	
	//$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
	

    public function headings(): array
    {
        return ['SNo', 'Category Name'];
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