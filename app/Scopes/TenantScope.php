<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * TenantScope
 *
 * Canonical Eloquent global scope enforcing multi-tenancy isolation.
 * Automatically adds `WHERE company_id = ?` based on the authenticated user's company_id.
 *
 * Bypasses scope when running in CLI/console environment or for superadmin users.
 *
 * NOTE: Intent & tenancy note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip in CLI/Console except during unit test executions
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        // Skip if user is unauthenticated or has superadmin privilege
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();
        if (! empty($user->is_superadmin)) {
            return;
        }

        // Apply company_id filter when available
        if (! empty($user->company_id)) {
            $table = $model->getTable();
            $builder->where($table . '.company_id', '=', $user->company_id);
        }
    }
}
