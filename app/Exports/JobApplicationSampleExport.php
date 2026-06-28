<?php

namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class JobApplicationSampleExport implements FromCollection, WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    

    public function collection()
    {
        return User::select('users.*')->where('id','=',0)->get();
    }

    public function headings(): array
    {

        $CustomDetails = DB::table('job_additional_detail')->where('job_id','=',$this->data)->pluck('detail_value')->toArray();
        // dd( $CustomDetails);

        $headings =  [
            'Full Name',
            'Designation',
            'Current Company',
            'Primary Number',
            'Secondary Number',
            'Date of Birth',
            'Primary Email',
            'Secondary Email',
            'Qualification',
            'Total Experience',
            'Relevant Experience',
            'Notice period',
            'Current CTC',
            'Expected CTC',
            'Percentage',
            'Country',
            'State',
            'City',
            'Pincode',
            'Skills',
        ];
        $headings = array_merge($headings, $CustomDetails);

        return $headings;

    }

    public function styles(Worksheet $sheet)
    {
        // Set bold style for the headings row
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold' => true,
            ], 
            'alignment' => [
                'wrapText' => true,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

}
