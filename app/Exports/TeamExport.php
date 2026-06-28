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

class TeamExport  implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        
				//echo '<pre>'; print_r($this->filters); exit; 
		
		 		$chkAdminRole 	= DB::table('roles')
									->select('roles.id')
									->where('id', $this->loginUserId)
									->where('name', 'admin')
									->count();
								 	
				$categories 	=	DB::table('users')
									->select(
													'users.id', 
													'users.name',
													'users.email',
													'roles.display_name'				
											);				
				
				$categories 	=	$categories->leftJoin('role_user', 'role_user.user_id', '=', 'users.id');
				$categories 	=	$categories->leftJoin('roles', 'roles.id', '=', 'role_user.role_id');
				
				$categories 	=	$categories->where('users.company_id', $this->filters['company_id'] );	
				$categories 	=	$categories->where('users.type', 0 );		
				$categories 	=	$categories->orderBy('users.id', 'DESC');
						
				//$categories 	=	$categories->skip($this->filters['start'])->take($this->filters['length'])->get();
				
				$categories 	=	$categories->offset( $this->filters['start'] )->limit( $this->filters['length'] )->get();
				
		
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
        return ['SNo', 'Name','Email', 'Role Name'];
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