{{-- DEPRECATED: Forwards directly to Master Shared Activity Cards Stream --}}
@include('service_company.ajax_pages.client_activity_cards_ajax', [
    'viewModel' => $viewModel ?? null,
    'items' => $items ?? [],
    'companyTimezone' => $companyTimezone ?? 'Asia/Kolkata'
])
