<div class="modal-header pb-0 border-0 justify-content-between">
    <div class="d-flex align-items-center">
        <div class="symbol symbol-40px me-3">
            <span class="symbol-label bg-light-warning text-warning">
                <i class="fa-solid fa-pen-to-square fs-2 text-warning"></i>
            </span>
        </div>
        <div>
            <h3 class="modal-title fw-bolder fs-4 text-gray-900 mb-0">Edit Language: {{ $language->language_name }}</h3>
            <span class="text-muted fs-7">Modify language locale name and status</span>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
        <i class="fa-solid fa-xmark fs-3"></i>
    </button>
</div>

<div class="modal-body py-5 px-lg-7">
    <form id="editLangModalForm" class="form">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $language->id }}">

        @if($language->language_code === 'en')
            <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-4 mb-5">
                <i class="fa-solid fa-shield-halved fs-1 text-primary me-3 mb-2 mb-sm-0"></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h5 class="fw-bolder text-primary mb-1">Protected Base Language (English)</h5>
                    <span class="fs-7 text-gray-700">English is the primary fallback dictionary for all tenants. The code (<code class="fs-8">en</code>) and enabled status are locked to protect ATS platform stability.</span>
                </div>
            </div>
        @endif

        {{-- Quick Master ISO Preset Selector for non-English --}}
        @if($language->language_code !== 'en')
            <div class="mb-5 p-3 rounded bg-light-primary border border-primary border-dashed">
                <label class="form-label fs-7 fw-bolder text-primary mb-1">
                    <i class="fa-solid fa-filter fs-7 me-1 text-primary"></i>Pick from Master ISO Catalog (135 Languages)
                </label>
                <select id="modal_edit_master_preset" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#application-md-modal">
                    <option value="">-- Or choose from ISO Catalog to update name/code --</option>
                    @foreach($masterLanguages as $mlang)
                        <option value="{{ strtolower($mlang->code) }}" data-name="{{ $mlang->name }}" {{ strtolower($language->language_code) === strtolower($mlang->code) ? 'selected' : '' }}>
                            {{ $mlang->name }} ({{ strtoupper($mlang->code) }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="fv-row mb-5">
            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Name</label>
            <input type="text" name="language_name" id="modal_edit_language_name" class="form-control form-control-solid" value="{{ $language->language_name }}" required>
        </div>

        <div class="fv-row mb-5">
            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Code (ISO-639-1)</label>
            <div class="input-group">
                <input type="text" name="language_code" id="modal_edit_language_code" class="form-control form-control-solid text-lowercase" value="{{ $language->language_code }}" maxlength="20" required {{ $language->language_code === 'en' ? 'readonly' : '' }}>
                <span class="input-group-text bg-light-info text-info fw-bolder text-uppercase" id="modal_edit_code_preview">{{ strtoupper($language->language_code) }}</span>
            </div>
            @if($language->language_code !== 'en')
                <div class="text-muted fs-8 mt-1">Renaming the code will safely synchronize existing database translation entries without data loss.</div>
            @endif
        </div>

        @if($language->language_code !== 'en' && $language->language_code !== $global->locale)
            <div class="fv-row mb-5">
                <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Status</label>
                <select name="status" id="modal_edit_language_status" class="form-select form-select-solid">
                    <option value="enabled" {{ $language->status === 'enabled' ? 'selected' : '' }}>Enabled (Available to all users and applicants)</option>
                    <option value="disabled" {{ $language->status === 'disabled' ? 'selected' : '' }}>Disabled (Hidden from public dropdowns)</option>
                </select>
            </div>
        @else
            <input type="hidden" name="status" value="enabled">
            <div class="fv-row mb-5">
                <label class="form-label fs-6 fw-bold text-gray-800 mb-1">Status</label>
                <div>
                    <span class="badge badge-light-success fs-7 fw-bold px-3 py-2">
                        <i class="fa-solid fa-circle-check fs-6 me-1 text-success"></i>Enabled (Active System Default)
                    </span>
                </div>
            </div>
        @endif
    </form>
</div>

<div class="modal-footer d-flex justify-content-end py-3">
    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
    <button type="button" id="submit-edit-modal-btn" class="btn btn-primary">
        <span class="indicator-label">
            <i class="fa-solid fa-check fs-5 me-1"></i>Update Language
        </span>
        <span class="indicator-progress" style="display: none;">
            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>
</div>

<script>
    // Auto-update preview pill
    $('#modal_edit_language_code').on('input', function() {
        var val = $(this).val().trim().toUpperCase();
        $('#modal_edit_code_preview').text(val ? val : '--');
    });

    // Preset selector handler
    $('#modal_edit_master_preset').on('change', function() {
        var code = $(this).val();
        var name = $(this).find('option:selected').data('name');
        if (code && name) {
            $('#modal_edit_language_name').val(name);
            $('#modal_edit_language_code').val(code).trigger('input');
        }
    });

    // Submit handler
    var isModalSubmitting = false;
    $('#submit-edit-modal-btn').on('click', function(e) {
        e.preventDefault();
        if (isModalSubmitting) return;
        var $btn = $(this);
        var form = $('#editLangModalForm')[0];

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        formData.append('_method', 'PUT');

        isModalSubmitting = true;
        $btn.attr('disabled', true);
        $btn.find('.indicator-label').hide();
        $btn.find('.indicator-progress').show();

        var updateUrl = "{{ route('superadmin.language-settings.update', $language->id) }}";
        var idempKey = (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID() : ('idemp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9));

        $.ajax({
            url: updateUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Idempotency-Key': idempKey
            },
            success: function(response) {
                isModalSubmitting = false;
                $btn.attr('disabled', false);
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();

                if (response.status === 'success') {
                    try {
                        if ('BroadcastChannel' in window) {
                            var ch = new BroadcastChannel('jero_languages_sync');
                            ch.postMessage({ action: 'saved', id: "{{ $language->id }}" });
                        }
                    } catch (e) {}
                    var modalEl = document.getElementById('application-md-modal');
                    if (modalEl) {
                        var bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();
                    }
                    $('#application-md-modal').modal('hide');
                    Swal.fire({
                        title: "Updated!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(function() {
                        if (typeof langTable !== 'undefined' && langTable.draw) {
                            langTable.draw();
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Failed to update language.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            },
            error: function(xhr) {
                isModalSubmitting = false;
                $btn.attr('disabled', false);
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();

                var errorMsg = "An error occurred while updating the language.";
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: "Validation Error",
                    html: errorMsg,
                    icon: "error",
                    confirmButtonText: "OK",
                    customClass: { confirmButton: 'btn btn-danger' }
                });
            }
        });
    });
</script>
