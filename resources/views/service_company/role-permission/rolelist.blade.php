{{-- AJAX partial injected into #roleFormBody for "Add New Role" — thin wrapper around the shared drawer form --}}
@include('service_company.role-permission._role_form', [
    'isEdit' => false,
    'permissions' => $permissions ?? collect(),
    'permission_role' => [],
    'role_row' => null,
    'role_users' => collect(),
    'id' => 0,
])
