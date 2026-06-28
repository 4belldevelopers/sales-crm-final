<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsServiceGet;

class GoogleSheetsService extends Controller
{
    protected $GoogleSheetsServiceGet;

    public function __construct(GoogleSheetsServiceGet $GoogleSheetsServiceGet)
    {
        // dd("d");
        $this->GoogleSheetsServiceGet = $GoogleSheetsServiceGet;
    }

    public function index()
    {
        // dd("dkj");
        $range = 'Sheet1!A1:P1'; // Specify the range of data you want to fetch
        $values = $this->GoogleSheetsServiceGet->getSpreadsheetValues($range);
        dd($values);

        return view('sheets.index', compact('values'));
    }
}
