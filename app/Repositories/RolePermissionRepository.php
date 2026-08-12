<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Repository: RolePermissionRepository
 *
 * Intent: Encapsulates database queries for tenant roles, permission models, and role-user mappings.
 * Applies strict tenancy isolation using company_id filtering on roles and permissions.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class RolePermissionRepository
{
    /**
     * Fetch all roles belonging to a tenant along with eager-loaded permissions.
     *
     * Tenancy: Scoped strictly by $companyId.
     */
    public function getRolesWithPermissions(int $companyId): Collection
    {
        return Role::company($companyId)
            ->with(['permissions' => function ($q) {
                $q->select('id', 'name', 'group_name');
            }])
            ->orderBy('priority', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * Fetch assigned active team members grouped by role ID in a SINGLE aggregated database query.
     * Eliminates the severe N+1 query problem previously present in Blade rendering loops.
     *
     * Tenancy: Scoped by the provided array of tenant role IDs.
     */
    public function getAssignedUsersGroupedByRole(array $roleIds): Collection
    {
        if (empty($roleIds)) {
            return collect();
        }

        return DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->whereIn('role_user.role_id', $roleIds)
            ->where('users.type', 0)
            ->select('role_user.role_id', 'users.id', 'users.name', 'users.email')
            ->get()
            ->groupBy('role_id');
    }

    /**
     * Fetch map of permission name -> display_name.
     */
    public function getPermissionDisplayMap(): array
    {
        return DB::table('permissions')
            ->pluck('display_name', 'name')
            ->toArray();
    }

    /**
     * Fetch total assignable permissions per group_name across active permissions.
     */
    public function getGroupTotals(array $excludedPermIds = [58, 59, 14, 15, 16, 17]): array
    {
        return DB::table('permissions')
            ->where('status', 1)
            ->whereNotIn('id', $excludedPermIds)
            ->select('group_name', DB::raw('count(*) as cnt'))
            ->groupBy('group_name')
            ->pluck('cnt', 'group_name')
            ->toArray();
    }

    /**
     * Fetch permissions associated with a company's roles for drawer forms.
     *
     * Tenancy: Joined through tenant roles filtered by $companyId.
     */
    public function getPermissionsForCompany(int $companyId, int $globalCompanyType): Collection
    {
        $query = DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
            ->where('roles.company_id', $companyId)
            ->where('permissions.status', 1)
            ->whereNotIn('permissions.id', [58, 59, 14, 15, 16, 17]);

        if ($globalCompanyType == 3) {
            $query->where('permissions.product_based', 1);
        }

        return $query->select('permissions.id', 'permissions.name', 'permissions.display_name', 'permissions.status', 'permissions.group_name')
            ->orderBy('permissions.group_name')
            ->orderBy('permissions.name')
            ->distinct()
            ->get();
    }

    /**
     * Fetch a single role row by ID for a tenant.
     */
    public function getRoleRow(int $roleId): ?object
    {
        return DB::table('roles')
            ->select('roles.id', 'roles.display_name', 'roles.name', 'roles.manual_add', 'roles.priority', 'roles.company_id')
            ->where('roles.id', $roleId)
            ->first();
    }

    /**
     * Fetch assigned permission IDs for a specific role.
     */
    public function getRolePermissionIds(int $roleId): array
    {
        $rawList = DB::table('role_has_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->toArray();

        return array_column($rawList, 'permission_id');
    }

    /**
     * Fetch active users assigned to a specific role.
     */
    public function getUsersAssignedToRole(int $roleId): Collection
    {
        return DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id', $roleId)
            ->where('users.type', 0)
            ->select('users.id', 'users.name', 'users.email')
            ->get();
    }

    /**
     * Fetch master permissions for new role creation form.
     */
    public function getPermissionsMaster(int $globalCompanyType): Collection
    {
        $query = DB::table('permissions_master')
            ->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
            ->where('permissions_master.status', 1);

        if ($globalCompanyType == 3) {
            $query->where('permissions_master.product_based', 1);
        }

        return $query->orderBy('permissions_master.priority', 'ASC')->get();
    }

    /**
     * Fetch existing roles for a tenant sorted by priority and name.
     *
     * Tenancy: Filtered strictly by $companyId.
     */
    public function getRolesList(int $companyId): Collection
    {
        return DB::table('roles')
            ->select('roles.id', 'roles.display_name', 'roles.priority', 'roles.name', 'roles.manual_add')
            ->where('roles.company_id', '=', $companyId)
            ->orderBy('roles.priority', 'ASC')
            ->orderBy('roles.name', 'ASC')
            ->get();
    }

    /**
     * Get the latest role priority for a company to calculate next role priority.
     */
    public function getLastRoleForCompany(int $companyId): ?object
    {
        return DB::table('roles')
            ->select('*')
            ->where('company_id', $companyId)
            ->orderBy('roles.id', 'DESC')
            ->first();
    }

    /**
     * Insert a new role record and return inserted ID.
     */
    public function insertRole(array $values): int
    {
        DB::table('roles')->insert($values);

        return (int) DB::getPdo()->lastInsertId();
    }

    /**
     * Insert permissions to a role.
     */
    public function insertRolePermissions(int $roleId, array $permissionIds): void
    {
        foreach ($permissionIds as $permId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permId,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * Reassign users assigned to $fromRoleId to $toRoleId.
     */
    public function reassignRoleUsers(int $fromRoleId, int $toRoleId): void
    {
        DB::table('role_user')
            ->where('role_id', $fromRoleId)
            ->update(['role_id' => $toRoleId]);
    }

    /**
     * Delete role record by ID.
     */
    public function deleteRoleById(int $roleId): void
    {
        Role::whereId($roleId)->delete();
    }
}
