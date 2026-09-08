<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Repositories\RolePermissionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Service: RolePermissionService
 *
 * Intent: Business logic, Redis caching, and data transformation layer for Role & Permission management (V2).
 * Formats tenant role stats, coverage metrics, and handles role creation/deletion with Redis cache invalidation.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class RolePermissionService
{
    private const CACHE_TTL = 86400; // 24 Hours Redis Cache TTL

    protected RolePermissionRepository $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Clear Redis cache key for a tenant's role permission listing data.
     */
    public function clearTenantRoleCache(int $companyId): void
    {
        if ($companyId > 0) {
            Cache::forget("tenant:{$companyId}:roles_permissions_v2");
        }
    }

    /**
     * Build combined page data for the primary Role Permission Listing view.
     * Cached in Redis per tenant with key tenant:{companyId}:roles_permissions_v2.
     *
     * Tenancy: Scoped strictly by $companyId.
     */
    public function getRoleListingData(int $companyId): array
    {
        $cacheKey = "tenant:{$companyId}:roles_permissions_v2";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($companyId) {
            // 1. Fetch roles with eager-loaded permissions
            $roles = $this->repository->getRolesWithPermissions($companyId);

            // 2. Fetch permission display name map & group totals
            $permDisplayMap = $this->repository->getPermissionDisplayMap();
            $groupTotals = $this->repository->getGroupTotals();

            // 3. Batch-fetch assigned users for ALL tenant roles in ONE query (N+1 removal)
            $roleIds = $roles->pluck('id')->toArray();
            $assignedUsersMap = $this->repository->getAssignedUsersGroupedByRole($roleIds);

            // 4. Coverage buckets & palette definitions
            $coverageBuckets = [
                'Recruitment' => ['Jobs', 'Job Applications', 'Offer', 'Joining', 'Schedule', 'Requisition', 'Team On Requisition'],
                'Talent Marketplace' => ['Bench', 'Staffing Partners'],
                'Sales & CRM' => ['Clients', 'Team', 'Reports'],
                'Admin & Security' => ['Roles & Permissions', 'Manage Settings', 'Settings', 'Website', 'Subscription', 'Salary', 'Salary Break'],
            ];
            $bucketColors = [
                'Recruitment' => '#27ae60',
                'Talent Marketplace' => '#1a73e8',
                'Sales & CRM' => '#5e72e4',
                'Admin & Security' => '#c0392b',
            ];
            $cardPalette = [
                ['bg' => '#eef0fb', 'stroke' => '#5e72e4', 'icon' => 'shield-check'],
                ['bg' => '#e3f1ff', 'stroke' => '#1a73e8', 'icon' => 'users'],
                ['bg' => '#e8f8f0', 'stroke' => '#27ae60', 'icon' => 'user'],
                ['bg' => '#fff0f5', 'stroke' => '#e91e8c', 'icon' => 'message'],
                ['bg' => '#fff8e6', 'stroke' => '#f39c12', 'icon' => 'currency'],
                ['bg' => '#fde8e8', 'stroke' => '#c0392b', 'icon' => 'shield'],
                ['bg' => '#f1f3f8', 'stroke' => '#8b93b8', 'icon' => 'settings'],
            ];
            $avColors = ['#5e72e4', '#1a73e8', '#27ae60', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#e91e8c'];

            // 5. Pre-compute statistics & coverage per role
            $totalRolesCount = $roles->count();
            $customRolesCount = 0;
            $systemRolesCount = 0;
            $totalAssignedUsers = 0;
            $totalPermGroups = 0;
            $allRoleStats = [];
            $loopIndex = 0;

            foreach ($roles as $rlist) {
                $roleId = $rlist->id;

                // Map permissions grouped by group_name
                $rlist->grouped_permissions = $rlist->permissions
                    ->groupBy('group_name')
                    ->map(fn ($g) => $g->pluck('name'));

                // Get users assigned to this role from single aggregated query
                $roleUsers = $assignedUsersMap->get($roleId, collect());
                $uCount = $roleUsers->count();

                $totalAssignedUsers += $uCount;
                $grpAllForStats = $rlist->grouped_permissions ?? collect();
                $grpCount = $grpAllForStats->count();
                $totalPermGroups += $grpCount;

                $totalAssignedPerms = 0;
                foreach ($grpAllForStats as $permNames) {
                    $totalAssignedPerms += count($permNames);
                }

                // Calculate coverage percentages
                $coverage = [];
                foreach ($coverageBuckets as $bucketName => $groupNames) {
                    $assigned = 0;
                    $total = 0;
                    foreach ($groupNames as $gn) {
                        $assigned += isset($grpAllForStats[$gn]) ? count($grpAllForStats[$gn]) : 0;
                        $total += $groupTotals[$gn] ?? 0;
                    }
                    $coverage[$bucketName] = [
                        'assigned' => $assigned,
                        'total' => $total,
                        'pct' => $total > 0 ? round(($assigned / $total) * 100) : 0,
                        'color' => $bucketColors[$bucketName],
                    ];
                }

                if ($rlist->manual_add) {
                    $customRolesCount++;
                } else {
                    $systemRolesCount++;
                }

                $isCustom = (bool) $rlist->manual_add;
                $roleCode = ($isCustom ? 'CUS-' : 'SYS-').str_pad($roleId, 2, '0', STR_PAD_LEFT);
                $palette = $cardPalette[$loopIndex % count($cardPalette)];

                $permissionsFlat = [];
                foreach ($grpAllForStats as $gn => $permNames) {
                    $items = [];
                    foreach ($permNames as $permName) {
                        $items[] = $permDisplayMap[$permName] ?? ucwords(str_replace(['_', '-'], ' ', $permName));
                    }
                    $permissionsFlat[] = ['group' => $gn, 'items' => $items];
                }

                $access = [
                    'label' => (str_contains(strtolower($rlist->name ?? ''), 'admin') || $grpCount >= 5) ? 'High Access' : (($grpCount >= 3) ? 'Medium Access' : 'Low Access'),
                    'class' => (str_contains(strtolower($rlist->name ?? ''), 'admin') || $grpCount >= 5) ? 'badge-high' : (($grpCount >= 3) ? 'badge-medium' : 'badge-low'),
                ];

                $detailJson = json_encode([
                    'id' => $roleId,
                    'name' => ucwords($rlist->display_name),
                    'roleCode' => $roleCode,
                    'isCustom' => $isCustom,
                    'grpCount' => $grpCount,
                    'totalPerms' => $totalAssignedPerms,
                    'userCount' => $uCount,
                    'access' => $access,
                    'coverage' => $coverage,
                    'permissions' => $permissionsFlat,
                    'users' => $roleUsers->map(fn ($u) => ['name' => $u->name, 'email' => $u->email])->values(),
                    'createdOn' => $rlist->created_at ? \Carbon\Carbon::parse($rlist->created_at)->format('d M, Y') : null,
                    'updatedOn' => $rlist->updated_at ? \Carbon\Carbon::parse($rlist->updated_at)->format('d M, Y') : null,
                    'palette' => $palette,
                    'canEdit' => ($rlist->priority != 0) && ($rlist->name != 'admin'),
                    'canDelete' => $isCustom,
                ]);

                $allRoleStats[$roleId] = [
                    'userCount' => $uCount,
                    'users' => $roleUsers,
                    'grpCount' => $grpCount,
                    'totalAssignedPerms' => $totalAssignedPerms,
                    'coverage' => $coverage,
                    'detailJson' => $detailJson,
                ];
                $loopIndex++;
            }

            $roleslist = $this->repository->getRolesList($companyId);
            $totalPermissions = Permission::count();
            $modules = Module::all();

            return [
                'roles' => $roles,
                'roleslist' => $roleslist,
                'totalPermissions' => $totalPermissions,
                'modules' => $modules,
                'company_id' => $companyId,
                'totalRolesCount' => $totalRolesCount,
                'customRolesCount' => $customRolesCount,
                'systemRolesCount' => $systemRolesCount,
                'totalAssignedUsers' => $totalAssignedUsers,
                'totalPermGroups' => $totalPermGroups,
                'allRoleStats' => $allRoleStats,
                'permDisplayMap' => $permDisplayMap,
                'coverageBuckets' => $coverageBuckets,
                'bucketColors' => $bucketColors,
                'groupTotals' => $groupTotals,
                'cardPalette' => $cardPalette,
                'avColors' => $avColors,
            ];
        });
    }

    /**
     * Build data for permissions drawer form (loadRole / loadEditRole).
     */
    public function getGroupedPermissionsForCompany(int $companyId, int $globalCompanyType, ?int $roleId = null): array
    {
        $permissionsList = $this->repository->getPermissionsForCompany($companyId, $globalCompanyType);

        $permissions = $permissionsList->groupBy('group_name')
            ->map(function ($group) {
                return $group->map(function ($perm) {
                    return [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'display_name' => $perm->display_name,
                        'status' => $perm->status,
                    ];
                });
            });

        $data = ['permissions' => $permissions, 'id' => $roleId];

        if ($roleId) {
            $data['role_row'] = $this->repository->getRoleRow($roleId);
            $data['permission_role'] = $this->repository->getRolePermissionIds($roleId);
            $data['role_users'] = $this->repository->getUsersAssignedToRole($roleId);
        }

        return $data;
    }

    /**
     * Fetch master permissions for role creation.
     */
    public function getCreatePermissionData(int $globalCompanyType): Collection
    {
        return $this->repository->getPermissionsMaster($globalCompanyType);
    }

    /**
     * Create a new tenant role with assigned permissions.
     * Preserves logic for special requisition permission IDs (58 & 59).
     * Invalidates tenant Redis cache.
     */
    public function saveRole(int $companyId, int $userId, array $requestData): array
    {
        $roleName = $requestData['role_name'] ?? '';
        $roleIds = $requestData['role'] ?? [];

        if (empty($roleName)) {
            return ['status' => 'success'];
        }

        $lastRole = $this->repository->getLastRoleForCompany($companyId);
        $rolePriority = 1;
        if ($lastRole && $lastRole->priority >= 0) {
            $rolePriority = $lastRole->priority + 1;
        }

        if ($lastRole && $lastRole->display_name == $roleName) {
            return [
                'status' => 'warning',
                'message' => 'Role name already registered. Please choose another name.',
            ];
        }

        $values = [
            'company_id' => $companyId,
            'name' => strtolower($roleName),
            'display_name' => $roleName,
            'description' => $roleName,
            'priority' => $rolePriority,
            'manual_add' => 1,
        ];

        $lastInsertId = $this->repository->insertRole($values);

        if ($lastInsertId > 0 && ! empty($roleIds)) {
            $this->repository->insertRolePermissions($lastInsertId, $roleIds);
        }

        // Special Requisition permissions
        if (! empty($requestData['all_requisition']) && $requestData['all_requisition'] == 1) {
            $this->repository->insertRolePermissions($lastInsertId, [58]);
        }
        if (! empty($requestData['add_team_on_requisition']) && $requestData['add_team_on_requisition'] == 1) {
            $this->repository->insertRolePermissions($lastInsertId, [59]);
        }

        // Flush tenant Redis cache
        $this->clearTenantRoleCache($companyId);

        return ['status' => 'success'];
    }

    /**
     * Delete role safely by reassigning affected users to default 'Recruiter' role first.
     * Invalidates tenant Redis cache.
     */
    public function manualDeleteRole(int $roleId, int $companyId = 0): array
    {
        // Reassign assigned users to 'Recruiter' system role before deleting
        $recruiterRole = Role::where('display_name', '=', 'Recruiter')->first();
        if ($recruiterRole) {
            $this->repository->reassignRoleUsers($roleId, (int) $recruiterRole->id);
        }

        $this->repository->deleteRoleById($roleId);

        // Flush tenant Redis cache
        if ($companyId > 0) {
            $this->clearTenantRoleCache($companyId);
        }

        return ['status' => 'success', 'view' => ''];
    }
}
