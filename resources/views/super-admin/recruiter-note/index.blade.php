@extends('layouts.superadmin_layout')

@push('head-script')
<style>
    .rnote-badge { display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
    .rnote-badge.active  { background:#e8f5e9;color:#2e7d32; }
    .rnote-badge.inactive{ background:#fce4ec;color:#c62828; }
    .rnote-actions .btn { padding:4px 10px;font-size:12px;margin:0 2px; }
    #rnoteModal .modal-dialog { max-width:700px; }
    .ck-content-preview { max-height:60px;overflow:hidden;font-size:12px;color:#6c757d; }
</style>
@endpush

@section('content')
<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Recruiter Note Templates</h1>
        </div>
        <div>
            <button class="btn btn-sm btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus me-1"></i> Add Template
            </button>
        </div>
    </div>
</div>

<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">
            <div class="card">
                <div class="card-body pt-3 px-5">
                    <table class="table table-bordered table-hover align-middle" id="rnoteTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Title</th>
                                <th>Preview</th>
                                <th width="90" class="text-center">Status</th>
                                <th width="120" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $i => $tpl)
                            <tr id="row-{{ $tpl->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $tpl->title }}</td>
                                <td><div class="ck-content-preview">{!! strip_tags($tpl->content) !!}</div></td>
                                <td class="text-center">
                                    <span class="rnote-badge {{ $tpl->status ? 'active' : 'inactive' }}">
                                        {{ $tpl->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center rnote-actions">
                                    <button class="btn btn-sm btn-light-primary" onclick="openEditModal({{ $tpl->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-danger" onclick="deleteTemplate({{ $tpl->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No templates found. Click "Add Template" to create one.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="rnoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rnoteModalTitle">Add Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rnoteForm">
                    @csrf
                    <input type="hidden" id="rnote_id" name="id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rnote_title" name="title" placeholder="e.g. Strong Communication Skills" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea id="rnote_content" name="content" rows="6"></textarea>
                    </div>

                    <div class="mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="rnote_status" name="status" checked>
                            <label class="form-check-label" for="rnote_status">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="rnoteSaveBtn" onclick="saveTemplate()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
var _rnoteEditor = null;

function _initEditor() {
    if (_rnoteEditor) { _rnoteEditor.destroy(); _rnoteEditor = null; }
    _rnoteEditor = CKEDITOR.replace('rnote_content', { height: 250 });
}

function openAddModal() {
    $('#rnoteModalTitle').text('Add Template');
    $('#rnote_id').val('');
    $('#rnote_title').val('');
    $('#rnote_status').prop('checked', true);
    $('#rnoteModal').modal('show');
    setTimeout(_initEditor, 300);
}

function openEditModal(id) {
    $.get('{{ route("superadmin.recruiter-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(res) {
        if (res.status !== 'success') return toastr.error('Failed to load template.');
        var d = res.data;
        $('#rnoteModalTitle').text('Edit Template');
        $('#rnote_id').val(d.id);
        $('#rnote_title').val(d.title);
        $('#rnote_status').prop('checked', d.status == 1);
        $('#rnoteModal').modal('show');
        setTimeout(function() {
            _initEditor();
            _rnoteEditor.setData(d.content || '');
        }, 300);
    });
}

function saveTemplate() {
    if (_rnoteEditor) _rnoteEditor.updateElement();

    var id      = $('#rnote_id').val();
    var title   = $.trim($('#rnote_title').val());
    var content = $.trim($('#rnote_content').val());
    var status  = $('#rnote_status').is(':checked') ? 1 : 0;

    if (!title)   { toastr.warning('Title is required.'); return; }
    if (!content) { toastr.warning('Content is required.'); return; }

    var url    = id
        ? '{{ route("superadmin.recruiter-note.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
        : '{{ route("superadmin.recruiter-note.store") }}';
    var method = id ? 'PUT' : 'POST';

    $('#rnoteSaveBtn').prop('disabled', true).text('Saving…');

    $.ajax({
        url: url, type: method,
        data: { _token: '{{ csrf_token() }}', title: title, content: content, status: status },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Saved.');
                $('#rnoteModal').modal('hide');
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
        complete: function() { $('#rnoteSaveBtn').prop('disabled', false).text('Save'); }
    });
}

function deleteTemplate(id) {
    if (!confirm('Delete this template?')) return;
    $.ajax({
        url: '{{ route("superadmin.recruiter-note.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success('Deleted.');
                $('#row-' + id).fadeOut(300, function(){ $(this).remove(); });
            } else {
                toastr.error(res.message || 'Delete failed.');
            }
        },
        error: function() { toastr.error('Delete failed.'); }
    });
}

$('#rnoteModal').on('hidden.bs.modal', function() {
    if (_rnoteEditor) { _rnoteEditor.destroy(); _rnoteEditor = null; }
});
</script>
@endpush
