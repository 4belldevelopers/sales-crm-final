<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Auth;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Exception;

class GoogleSheetServiceCandidate
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $companyId;

    public function __construct()
    {
          
    }

    public function getSpreadsheetValues($range,$id,$googlesheetid)
    {
        try {
        //  dd($id);
        // $this->companyId = $this->getCompanyId();        
        $lock_unlock = GeneralMethod::googleSheetId($id);
        // dd($lock_unlock);
        $this->client = new Google_Client();
        $this->client->setApplicationName('Laravel Google Sheets API');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('get-goolge-sheet-3ca5023fcfce.json'));
        $this->client->setAccessType('offline');
        
        $this->service = new Google_Service_Sheets($this->client);
        $this->spreadsheetId = $googlesheetid; // Replace with your actual spreadsheet ID $lock_unlock->cand_sheet_id

        $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
        $sheets = $spreadsheet->getSheets();

        $allValues = [];
        foreach ($sheets as $sheet) {
            $sheetTitle = $sheet->getProperties()->getTitle();
            $range = $sheetTitle; // Specify the sheet name as the range to get all data from that sheet
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $allValues[$sheetTitle] = $response->getValues();
        }

        // dd($allValues);

        return $allValues;
    } catch (Google_Service_Exception $e) {
        $allValues = [];
        return $allValues; // The spreadsheet ID is invalid or not accessible
    }

        // dd(auth()->user()->company_id);
        // $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        // return $response->getValues();
    }

    
    public function checkSpreadsheetAccess($spreadsheetId)
    {
        try {
            $this->client = new Google_Client();
            $this->service = new Google_Service_Sheets($this->client);
            $this->service->spreadsheets->get($spreadsheetId);
            return true; // The spreadsheet ID is valid and accessible
        } catch (Google_Service_Exception $e) {
            return false; // The spreadsheet ID is invalid or not accessible
        }
    }
    protected function getCompanyId()
    {
        if (auth()->user()->company_id) {
            return  auth()->user()->company_id;
        }

        return null; // You may handle this case differently based on your application logic
    }
    

}
