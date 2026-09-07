<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: adds all JERO-specific columns to users that exist in
 * production but are absent from the base Laravel 0001_01_01_000000 migration.
 * Every column is guarded with hasColumn() so running on production is safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedInteger('company_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'root_user')) {
                $table->integer('root_user')->default(0)->after('company_id');
            }
            if (!Schema::hasColumn('users', 'calling_code')) {
                $table->string('calling_code', 191)->nullable();
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 191)->nullable();
            }
            if (!Schema::hasColumn('users', 'mobile_verified')) {
                $table->tinyInteger('mobile_verified')->default(0);
            }
            if (!Schema::hasColumn('users', 'image')) {
                $table->string('image', 191)->nullable();
            }
            if (!Schema::hasColumn('users', 'is_superadmin')) {
                $table->tinyInteger('is_superadmin')->default(0);
            }
            if (!Schema::hasColumn('users', 'email_verification_code')) {
                $table->string('email_verification_code', 40)->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
            if (!Schema::hasColumn('users', 'working_status')) {
                $table->integer('working_status')->default(1);
            }
            if (!Schema::hasColumn('users', 'type')) {
                $table->integer('type')->default(0)->comment('0: For Employer user, 1: For Consultancy');
            }
            if (!Schema::hasColumn('users', 'active_session')) {
                $table->string('active_session', 55);
            }
            if (!Schema::hasColumn('users', 'session_time')) {
                $table->integer('session_time');
            }
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->dateTime('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
            }
            if (!Schema::hasColumn('users', 'auto_date_team')) {
                $table->date('auto_date_team')->nullable();
            }
            if (!Schema::hasColumn('users', 'session_id')) {
                $table->string('session_id', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'super_password')) {
                $table->text('super_password')->nullable();
            }
            if (!Schema::hasColumn('users', 'super_admin_login_code')) {
                $table->string('super_admin_login_code', 244)->default('1111');
            }
            if (!Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->text('linkedin')->nullable();
            }
        });

        // Add index if missing
        if (!collect(\DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_company_id_foreign'"))->count()) {
            \DB::statement('ALTER TABLE `users` ADD KEY `users_company_id_foreign` (`company_id`)');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'company_id', 'root_user', 'calling_code', 'mobile', 'mobile_verified',
                'image', 'is_superadmin', 'email_verification_code', 'status',
                'working_status', 'type', 'active_session', 'session_time', 'last_login',
                'auto_date_team', 'session_id', 'super_password', 'super_admin_login_code',
                'whatsapp_number', 'linkedin',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
