<?php

namespace Tests\Fixtures;

use App\Models\Company;
use App\Models\User;

/**
 * TenantFixture
 *
 * Provides isolated tenant contexts (Company A and Company B) and users
 * for multi-tenancy isolation testing.
 *
 * NOTE: Intent-commented per docs/CODE_DOCUMENTATION_STANDARD.md
 */
class TenantFixture
{
    public static function companyA(): array
    {
        return [
            'id' => 100001,
            'company_name' => 'Company Alpha Test',
        ];
    }

    public static function companyB(): array
    {
        return [
            'id' => 100002,
            'company_name' => 'Company Beta Test',
        ];
    }

    public static function userA(): array
    {
        return [
            'id' => 900001,
            'name' => 'Alpha Tenant User',
            'email' => 'user_alpha@test.jero.works',
            'company_id' => 100001,
            'is_superadmin' => 0,
        ];
    }

    public static function userB(): array
    {
        return [
            'id' => 900002,
            'name' => 'Beta Tenant User',
            'email' => 'user_beta@test.jero.works',
            'company_id' => 100002,
            'is_superadmin' => 0,
        ];
    }
}
