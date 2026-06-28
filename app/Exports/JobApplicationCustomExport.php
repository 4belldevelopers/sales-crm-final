<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class JobApplicationCustomExport implements FromCollection , WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = collect($data);
    }

    public function collection()
    {
        // dd($this->data);
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Job ID',
            'Job Title',
            'Full Name',
            'Passition',
            'Email',
            'Phone',
            'Total Exp',
            'City',
            'State',
            'Country',
            'Current company',
            'Client company',
            'Expected Salary',
            'Current Salary',
            'Comment',
            'Notic',
            'Interview Schedule date',
            'Upload date',
            'Qualification',
            'Stage',
            'Sub stage',
            'Source by',
            'Source name',
            'custom_details'

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

}
