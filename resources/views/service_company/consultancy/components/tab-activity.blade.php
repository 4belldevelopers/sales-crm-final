{{-- DEPRECATED: Forwards directly to Master Shared Activity Component --}}
@include('service_company.client.components.tab-activity', [
    'clientId' => $agencyId ?? ($clientId ?? 0),
    'clientType' => 'agency',
    'viewerType' => 'agency',
    'agencyCompanyId' => $agencyId ?? ($clientId ?? 0)
])
