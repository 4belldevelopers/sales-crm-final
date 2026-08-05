<?php

namespace App\Services\Apptracker;

use App\Exports\AppTrackerExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * BenchExportService — Phase D of the Step-16 Apptracker controller slice.
 *
 * Wraps the Maatwebsite Excel export pipeline for Apptracker listings.
 * Extracted from ServiceCompanyAppTrackerController::export().
 */
class BenchExportService
{
    /**
     * Stream an Excel download of Apptracker candidates.
     *
     * Required $filters keys: company_id, jobs, master_status_id, start, length,
     *   loginUserId, loginUserRole. Optional: company_name (string, used as header).
     */
    public function export(array $filters): BinaryFileResponse
    {
        $exportData = ['company' => $filters['company_name'] ?? ''];

        return Excel::download(
            new AppTrackerExport($filters, $exportData),
            'AppTrackerExport.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
