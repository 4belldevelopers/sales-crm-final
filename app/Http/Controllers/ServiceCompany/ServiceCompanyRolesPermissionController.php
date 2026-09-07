<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller: ServiceCompanyRolesPermissionController
 *
 * Intent: HTTP Controller for Service Company Roles & Permissions (V2).
 * Delegated to RolePermissionService to enforce clean separation of concerns
 * and zero N+1 database queries.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class ServiceCompanyRolesPermissionController extends ServiceCompanyBaseController
{
    protected RolePermissionService $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        parent::__construct();
        $this->rolePermissionService = $rolePermissionService;
        $this->pageTitle = 'menu.rolesPermission';
        $this->pageIcon = 'ti-lock';
    }

    /**
     * Display primary Role Permission Listing dashboard.
     *
     * Tenancy: Scoped by authenticated user's company_id.
     */
    public function index()
    {
        abort_if(Gate::denies('view_roles_permission'), 403);

        $companyId = auth()->user()->company_id;
        $pageData = $this->rolePermissionService->getRoleListingData($companyId);

        foreach ($pageData as $key => $val) {
            $this->{$key} = $val;
        }

        return view('service_company.role-permission.index', $this->data);
    }

    /**
     * Render create role form modal.
     */
    public function create()
    {
        $globalCompanyType = $this->global_company_type ?? 0;
        $permissionsMaster = $this->rolePermissionService->getCreatePermissionData($globalCompanyType);

        $this->permissions_master = $permissionsMaster;
        $this->id = null;

        $view = view('service_company.role-permission.create', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Load role drawer with permissions tree.
     */
    public function loadRole($id = null)
    {
        abort_if(! $this->user->can('add_roles_permission'), 403);

        $companyId = auth()->user()->company_id;
        $globalCompanyType = $this->global_company_type ?? 0;
        $roleData = $this->rolePermissionService->getGroupedPermissionsForCompany($companyId, $globalCompanyType, $id);

        foreach ($roleData as $key => $val) {
            $this->{$key} = $val;
        }

        $view = view('service_company.role-permission.rolelist', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Store new tenant role.
     *
     * Tenancy: Scoped by authenticated user's company_id.
     */
    public function saveRole(Request $request)
    {
        abort_if(! $this->user->can('add_roles_permission'), 403);

        $companyId = auth()->user()->company_id;
        $userId = auth()->user()->id;

        $response = $this->rolePermissionService->saveRole($companyId, $userId, $request->all());

        return Reply::dataOnly($response);
    }

    /**
     * Load role edit drawer with current permissions & assigned user summary.
     */
    public function loadEditRole($id)
    {
        $companyId = auth()->user()->company_id;
        $globalCompanyType = $this->global_company_type ?? 0;
        $roleData = $this->rolePermissionService->getGroupedPermissionsForCompany($companyId, $globalCompanyType, $id);

        foreach ($roleData as $key => $val) {
            $this->{$key} = $val;
        }

        $view = view('service_company.role-permission.loadEditRole', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Delete custom role safely (reassigning members to default 'Recruiter' role first).
     */
    public function manualDeleteRole($id)
    {
        $companyId = auth()->user()->company_id;
        $response = $this->rolePermissionService->manualDeleteRole((int) $id, (int) $companyId);

        return Reply::dataOnly($response);
    }
}
