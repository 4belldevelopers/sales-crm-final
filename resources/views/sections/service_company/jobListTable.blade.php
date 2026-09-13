{{-- Shared jobs table shell keeps Total, Direct Client, and Internal Jobs aligned without changing their data loaders. --}}
<div class="table-card">
  <!-- Header -->
  <div class="table-header">
    <div class="th">Job Details</div>
    <div class="th">
      Experience
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
        <rect
          x="2"
          y="7"
          width="20"
          height="14"
          rx="2"
          stroke="currentColor"
          stroke-width="1.8"
        />
        <path
          d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"
          stroke="currentColor"
          stroke-width="1.8"
        />
      </svg>
    </div>
    <div class="th">Location</div>
    <div class="th">Hiring Manager</div>
    <div class="th">Team</div>
    <div class="th">Agency</div>
    <div class="th">
      Hiring Pipeline
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
        <circle
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="1.8"
        />
        <path
          d="M12 8v4M12 16h.01"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
        />
      </svg>
    </div>
  </div>

  <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
  <div class="" id="{{ $listId }}"></div>

  <!-- PAGINATION -->
  <div id="pagination"></div>

  <!-- LEGEND -->
  @include('sections.service_company.pipeline')
</div>
<!-- /table-card -->
