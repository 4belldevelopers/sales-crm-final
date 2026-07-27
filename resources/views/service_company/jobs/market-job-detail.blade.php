@extends('layouts.service_company_layout')

@push('head-script')
<style>
.mjd-back-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px 0;
}
.mjd-back-bar a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #5e6278;
    text-decoration: none;
    font-weight: 500;
}
.mjd-back-bar a:hover { color: #009ef7; }
/* Remove the close button inherited from the drawer partial */
.page-close { display: none !important; }
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -60px;">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-0">

      <!-- Back link -->
      <div class="mjd-back-bar">
        <a href="javascript:history.back()">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
          Back to Market Jobs
        </a>
      </div>

      @include('service_company.jobs.show', [
          'hideMarketSections' => true,
          'benchshowtab'       => false,
          'viewmore'           => false,
          'agencyView'         => false,
          'jobfindhide'        => false,
          'tabshow'            => false,
      ])

    </div>
  </div>
</div>
@endsection
