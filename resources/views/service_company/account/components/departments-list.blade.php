{{-- 
  JERO ATS — Service Company Department List Partial (Rule 20 Modern Sub-Card Grid)
  Intent: Renders organization departments as modern, discrete Sub-Cards with delete actions.
  Tenancy: Strictly tenant-isolated by company_id.
--}}
<div class="gs-dept-grid" id="dept-cards-grid">
  @forelse($departmentArr ?? [] as $dval)
    <div class="gs-dept-subcard dept-item-col" data-dept-id="{{ $dval->id }}" data-dept-name="{{ strtolower($dval->name) }}">
      <div class="gs-dept-left">
        <div class="gs-dept-icon-box">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="gs-dept-info">
          <div class="gs-dept-name" title="{{ $dval->name }}">{{ $dval->name }}</div>
          <div class="gs-dept-tag">Active Department</div>
        </div>
      </div>
      <a href="javascript:void(0);" class="gs-dept-delete-btn dept_delete" rel="{{ $dval->id }}" title="Delete Department">
        <i class="far fa-trash-alt"></i>
      </a>
    </div>
  @empty
    <div class="gs-dept-empty" id="dept-empty-state">
      <div class="gs-dept-empty-icon">
        <i class="fas fa-building"></i>
      </div>
      <div class="gs-dept-empty-title">No Departments Configured</div>
      <div class="gs-dept-empty-sub">Click "+ Add Department" to establish your organizational hierarchy.</div>
    </div>
  @endforelse
</div>
