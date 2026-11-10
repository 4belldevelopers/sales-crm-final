<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class MeetingImport implements OnEachRow, ShouldQueue, WithBatchInserts, WithChunkReading
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
        if (! isset($row[0]) || ! isset($row[1])) {
            return;
        }

        $email = trim((string) $row[0]);
        $link = trim((string) $row[1]);

        // Validate email
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        if (empty($link)) {
            return;
        }

        if (! preg_match('/^https?:\/\//i', $link)) {
            $link = 'https://'.$link;
        }

        $this->data[] = [
            'account_name' => $email,
            'meeting_link' => $link,
            'status' => 1,
        ];

        // Insert when batch full
        if (count($this->data) >= 1000) {
            DB::table($this->table)->insertOrIgnore($this->data);
            $this->data = [];
            if ($this->table === 'google_meeting_lists') {
                \Illuminate\Support\Facades\Cache::forget('jero:google_meeting_links:active_pool');
                \App\Services\SuperAdmin\SuperAdminGoogleMeetingCacheManager::flushCaches();
            } elseif ($this->table === 'team_meeting_lists') {
                \Illuminate\Support\Facades\Cache::forget('jero:team_meeting_links:active_pool');
            }
        }
    }

    public function __destruct()
    {
        // Insert remaining rows
        if (! empty($this->data)) {
            DB::table($this->table)->insertOrIgnore($this->data);
            if ($this->table === 'google_meeting_lists') {
                \Illuminate\Support\Facades\Cache::forget('jero:google_meeting_links:active_pool');
                \App\Services\SuperAdmin\SuperAdminGoogleMeetingCacheManager::flushCaches();
            } elseif ($this->table === 'team_meeting_lists') {
                \Illuminate\Support\Facades\Cache::forget('jero:team_meeting_links:active_pool');
            }
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
