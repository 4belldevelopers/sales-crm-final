{{-- 
  JERO ATS — Service Company Department List Partial (Rule 20 Modern Sub-Card Grid)
  Intent: Renders organization departments as modern, discrete Sub-Cards with delete actions.
  Tenancy: Strictly tenant-isolated by company_id.
--}}
<div class="row g-3" id="dept-cards-grid">
  @forelse($departmentArr ?? [] as $dval)
    <div class="col-xl-4 col-md-6 col-12 dept-item-col" data-dept-id="{{ $dval->id }}" data-dept-name="{{ strtolower($dval->name) }}">
      <div class="dept-subcard d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3 overflow-hidden">
          <div class="dept-icon-box d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #009ef7; font-size: 14px; border: 1px solid #dbeafe; flex-shrink: 0;">
            <i class="fa-solid fa-briefcase"></i>
          </div>
          <div class="overflow-hidden">
            <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $dval->name }}">{{ $dval->name }}</div>
            <div class="text-muted fs-8">Active Department</div>
          </div>
        </div>
        <div class="d-flex align-items-center ms-2 flex-shrink-0">
          <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light-danger dept_delete" rel="{{ $dval->id }}" title="Delete Department" style="width: 32px; height: 32px; border-radius: 6px;">
            <i class="fa-solid fa-trash-can fs-7 text-danger"></i>
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-8" id="dept-empty-state">
      <div class="d-flex flex-column align-items-center justify-content-center py-6">
        <div class="w-50px h-50px rounded-circle bg-light-primary d-flex align-items-center justify-content-center mb-3">
          <i class="fa-solid fa-building text-primary fs-3"></i>
        </div>
        <div class="fw-bolder text-gray-800 fs-6">No Departments Configured</div>
        <div class="text-muted fs-7 mt-1">Click "+ Add Department" to establish your organizational hierarchy.</div>
      </div>
    </div>
  @endforelse
</div>
