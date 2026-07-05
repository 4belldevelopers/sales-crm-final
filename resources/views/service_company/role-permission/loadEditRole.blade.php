{{-- AJAX partial injected into #roleFormBody for "Edit Role" — thin wrapper around the shared drawer form --}}
@include('service_company.role-permission._role_form', [
    'isEdit' => true,
    'permissions' => $permissions ?? collect(),
    'permission_role' => $permission_role ?? [],
    'role_row' => $role_row ?? null,
    'role_users' => $role_users ?? collect(),
    'id' => $id ?? 0,
])
