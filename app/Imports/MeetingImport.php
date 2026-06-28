<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class MeetingImport implements OnEachRow, WithChunkReading, WithBatchInserts, ShouldQueue
{
    private $data = [];
    private $table;

    // 👇 constructor
    public function __construct($table)
    {
        $this->table = $table;
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Skip empty row
        if (!isset($row[0])) return;

        // Validate email
        if (!filter_var($row[0], FILTER_VALIDATE_EMAIL)) return;

        $this->data[] = [
            'account_name' => $row[0],
            'meeting_link' => $row[1] ?? null,
            'status' => 1
        ];

        // Insert when batch full
        if (count($this->data) >= 1000) {
            DB::table($this->table)->insertOrIgnore($this->data);
            $this->data = [];
        }
    }

    public function __destruct()
    {
        // Insert remaining rows
        if (!empty($this->data)) {
            DB::table($this->table)->insertOrIgnore($this->data);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}