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

class ExportCVTracker implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        
		
		
		
		
		 return $categories;				
    }
	
	//$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
	

    public function headings(): array
    {
        return [
					'ID', 'Applicant Name', 'Company',  'Requirement', 
					'Total Position', 'Location', 'Category', 'Client', 'Start Date', 
					'End Date', 'Status', 'Employment Type', 'Priority', 'Min Exp',
					'Max Exp', 'Min Salary', 'Max Salary', 'Qualification', 'Detail Description',
					'Created'
					
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