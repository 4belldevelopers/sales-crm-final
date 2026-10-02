@extends('layouts.superadmin_layout')

@push('head-script')
<style>
    .rm-badge { display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
    .rm-badge.active  { background:#e8f5e9;color:#2e7d32; }
    .rm-badge.inactive{ background:#fce4ec;color:#c62828; }
    .rm-actions .btn { padding:4px 10px;font-size:12px;margin:0 2px; }
    #rmModal .modal-dialog { max-width:720px; }
    .ck-content-preview { max-height:60px;overflow:hidden;font-size:12px;color:#6c757d;line-height:1.4; }
</style>
@endpush

@section('content')
<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Relationship Manager Note Templates</h1>
            <span class="text-muted fs-7 ms-2">Global default templates available for Bench Candidate / Talent comments</span>
        </div>
        <div>
            <button class="btn btn-sm btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus me-1"></i> Add RM Template
            </button>
        </div>
    </div>
</div>

<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">
            <div class="card">
                <div class="card-body pt-3 px-5">
                    <table class="table table-bordered table-hover align-middle" id="rmNoteTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="260">Template Title</th>
                                <th>Content Preview</th>
                                <th width="90" class="text-center">Status</th>
                                <th width="120" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $i => $tpl)
                            <tr id="row-{{ $tpl->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $tpl->title }}</td>
                                <td><div class="ck-content-preview">{!! strip_tags($tpl->content) !!}</div></td>
                                <td class="text-center">
                                    <span class="rm-badge {{ $tpl->status ? 'active' : 'inactive' }}">
                                        {{ $tpl->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center rm-actions">
                                    <button class="btn btn-sm btn-light-primary" onclick="openEditModal({{ $tpl->id }})" title="Edit Template">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-danger" onclick="deleteTemplate({{ $tpl->id }})" title="Delete Template">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No Relationship Manager templates found. Click "Add RM Template" to create one.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="rmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rmModalTitle">Add Relationship Manager Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rmForm">
                    @csrf
                    <input type="hidden" id="rm_id" name="id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rm_title" name="title" placeholder="e.g. Standard Bench Evaluation" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template Content <span class="text-danger">*</span></label>
                        <textarea id="rm_content" name="content" rows="6"></textarea>
                    </div>

                    <div class="mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="rm_status" name="status" checked>
                            <label class="form-check-label fw-semibold" for="rm_status">Active (Available across all Service Companies)</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="rmSaveBtn" onclick="saveTemplate()">Save Template</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
var _rmEditor = null;

function _initEditor() {
    if (_rmEditor) { _rmEditor.destroy(); _rmEditor = null; }
    _rmEditor = CKEDITOR.replace('rm_content', { height: 250 });
}

function openAddModal() {
    $('#rmModalTitle').text('Add Relationship Manager Template');
    $('#rm_id').val('');
    $('#rm_title').val('');
    $('#rm_status').prop('checked', true);
    $('#rmModal').modal('show');
    setTimeout(_initEditor, 300);
}

function openEditModal(id) {
    $.get('{{ route("superadmin.rm-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(res) {
        if (res.status !== 'success') return toastr.error('Failed to load template.');
        var d = res.data;
        $('#rmModalTitle').text('Edit Relationship Manager Template');
        $('#rm_id').val(d.id);
        $('#rm_title').val(d.title);
        $('#rm_status').prop('checked', d.status == 1);
        $('#rmModal').modal('show');
        setTimeout(function() {
            _initEditor();
            _rmEditor.setData(d.content || '');
        }, 300);
    });
}

function saveTemplate() {
    if (_rmEditor) _rmEditor.updateElement();

    var id      = $('#rm_id').val();
    var title   = $.trim($('#rm_title').val());
    var content = $.trim($('#rm_content').val());
    var status  = $('#rm_status').is(':checked') ? 1 : 0;

    if (!title)   { toastr.warning('Template title is required.'); return; }
    if (!content) { toastr.warning('Template content is required.'); return; }

    var url    = id
        ? '{{ route("superadmin.rm-note.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
        : '{{ route("superadmin.rm-note.store") }}';
    var method = id ? 'PUT' : 'POST';

    $('#rmSaveBtn').prop('disabled', true).text('Saving…');

    $.ajax({
        url: url,
        type: method,
        data: {
            _token: '{{ csrf_token() }}',
            title: title,
            content: content,
            status: status
        },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Saved successfully.');
                $('#rmModal').modal('hide');
                setTimeout(function(){ location.reload(); }, 800);
            } else {
                toastr.error(res.message || 'Error saving template.');
            }
        },
        error: function(xhr) {
            var msg = 'Save failed.';
            try { var r = JSON.parse(xhr.responseText); if(r.message) msg = r.message; } catch(e){}
            toastr.error(msg);
        },
        complete: function() { $('#rmSaveBtn').prop('disabled', false).text('Save Template'); }
    });
}

function deleteTemplate(id) {
    if (!confirm('Are you sure you want to delete this Relationship Manager template?')) return;
    $.ajax({
        url: '{{ route("superadmin.rm-note.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success('Template deleted.');
                $('#row-' + id).fadeOut(300, function(){ $(this).remove(); });
            } else {
                toastr.error(res.message || 'Delete failed.');
            }
        },
        error: function() { toastr.error('Delete failed.'); }
    });
}

$('#rmModal').on('hidden.bs.modal', function() {
    if (_rmEditor) { _rmEditor.destroy(); _rmEditor = null; }
});
</script>
@endpush
