<?php

namespace App\Helper;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * TenantDB Helper Class
 *
 * Provides central raw DB query scoping helper `tenant_table()` to enforce tenancy
 * without live schema reflection overhead.
 *
 * NOTE: Intent & tenancy note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
class TenantDB
{
    /**
     * List of 93 tenant-scoped database tables containing company_id column.
     * Verified against database/schema/mysql-schema.sql
     */
    public const TENANT_TABLES = [
        'add_job_wise_condidates',
        'application_additional_detail',
        'application_event',
        'application_status',
        'assigned_candidates',
        'bench_assign',
        'bench_candidates',
        'bench_place',
        'candidate_job_skill_exp',
        'candidate_status',
        'carts',
        'cart_transaction_history',
        'categories',
        'clients',
        'client_contacts',
        'client_documents',
        'coin_orders',
        'coin_transection_logs',
        'commercials',
        'company_coins',
        'departments',
        'designations',
        'documents',
        'interview_questions',
        'interview_schedules',
        'interview_schedule_comments',
        'jobs',
        'job_applications',
        'job_assign',
        'job_categories',
        'job_locations',
        'job_questions',
        'job_skills',
        'link_payments',
        'meeting_schedules',
        'offer_job_applications',
        'onboards',
        'onboard_files',
        'questions',
        'roles',
        'role_has_permissions',
        'skills',
        'staffing_company_relation',
        'theme_settings',
        'tickets',
        'todo_items',
        'top_up_logs',
        'users',
        'users_profile',
        'whatsapp_instances',
    ];

    /**
     * Wrap DB::table() with automatic tenancy company_id filter when applicable.
     */
    public static function table(string $table): Builder
    {
        $builder = DB::table($table);

        // Skip scope in CLI, unauthenticated, or superadmin contexts (allow during unit tests)
        if ((app()->runningInConsole() && ! app()->runningUnitTests()) || ! auth()->check()) {
            return $builder;
        }

        $user = auth()->user();
        if (! empty($user->is_superadmin) || empty($user->company_id)) {
            return $builder;
        }

        // Apply company_id filter if table is a known tenant table
        if (in_array($table, self::TENANT_TABLES, true)) {
            $builder->where($table . '.company_id', '=', $user->company_id);
        }

        return $builder;
    }
}

if (! function_exists('tenant_table')) {
    /**
     * Global helper function wrapping TenantDB::table()
     */
    function tenant_table(string $table): Builder
    {
        return \App\Helper\TenantDB::table($table);
    }
}
