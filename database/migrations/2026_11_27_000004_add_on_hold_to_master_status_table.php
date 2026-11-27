<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('master_status')->updateOrInsert(
            ['id' => 10],
            [
                'name' => 'On Hold',
                'css_style' => 'hold-bg',
                'text_name' => 'onhold',
                'divname' => 'hold-div',
                'priority' => 8,
                'direct_client' => 1,
                'direct_client_priority' => 4,
                'custom_client' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('master_status')->where('id', 10)->where('text_name', 'onhold')->delete();
    }
};
