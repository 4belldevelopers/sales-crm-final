<div class="modal-header pb-0 border-0 justify-content-between">
    <div class="d-flex align-items-center">
        <div class="symbol symbol-40px me-3">
            <span class="symbol-label bg-light-primary text-primary">
                <i class="fa-solid fa-circle-plus fs-2 text-primary"></i>
            </span>
        </div>
        <div>
            <h3 class="modal-title fw-bolder fs-4 text-gray-900 mb-0">Add New Language</h3>
            <span class="text-muted fs-7">Configure a new language locale for the platform</span>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
        <i class="fa-solid fa-xmark fs-3"></i>
    </button>
</div>

<div class="modal-body py-5 px-lg-7">
    <form id="createLangModalForm" class="form">
        @csrf

        {{-- Quick Master ISO Preset Selector --}}
        <div class="mb-5 p-3 rounded bg-light-primary border border-primary border-dashed">
            <label class="form-label fs-7 fw-bolder text-primary mb-1">
                <i class="fa-solid fa-filter fs-7 me-1 text-primary"></i>Quick Preset from Master ISO Catalog (135 Languages)
            </label>
            <select id="modal_master_language_preset" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#application-md-modal">
                <option value="">-- Or choose from ISO Catalog to auto-fill --</option>
                @foreach($masterLanguages as $mlang)
                    <option value="{{ strtolower($mlang->code) }}" data-name="{{ $mlang->name }}">
                        {{ $mlang->name }} ({{ strtoupper($mlang->code) }})
                    </option>
                @endforeach
            </select>
            <div class="text-muted fs-8 mt-1">Selecting a language will automatically pre-fill the name and ISO code below.</div>
        </div>

        <div class="fv-row mb-5">
            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Name</label>
            <input type="text" name="language_name" id="modal_language_name" class="form-control form-control-solid" placeholder="e.g. French, Japanese, Hindi" required>
        </div>

        <div class="fv-row mb-5">
            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Code (ISO-639-1)</label>
            <div class="input-group">
                <input type="text" name="language_code" id="modal_language_code" class="form-control form-control-solid text-lowercase" placeholder="e.g. fr, ja, hi" maxlength="20" required>
                <span class="input-group-text bg-light-info text-info fw-bolder text-uppercase" id="modal_code_preview">--</span>
            </div>
            <div class="text-muted fs-8 mt-1">Standard 2-letter or BCP-47 locale code used for translation directories.</div>
        </div>

        <div class="fv-row mb-5">
            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Status</label>
            <select name="status" id="modal_language_status" class="form-select form-select-solid">
                <option value="enabled" selected>Enabled (Available to all users and applicants)</option>
                <option value="disabled">Disabled (Hidden from public dropdowns)</option>
            </select>
        </div>

        <div class="form-check form-check-custom form-check-solid mb-2">
            <input class="form-check-input" type="checkbox" name="scaffold_dictionary" value="1" id="modal_scaffold_dictionary" checked>
            <label class="form-check-label fs-7 text-gray-700" for="modal_scaffold_dictionary">
                Automatically scaffold base translation files from English (<code class="fs-8">resources/lang/en</code>)
            </label>
        </div>
    </form>
</div>

<div class="modal-footer d-flex justify-content-end py-3">
    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
    <button type="button" id="submit-create-modal-btn" class="btn btn-primary">
        <span class="indicator-label">
            <i class="fa-solid fa-check fs-5 me-1"></i>Save Language
        </span>
        <span class="indicator-progress" style="display: none;">
            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>
</div>

<script>
    // Auto-update preview pill
    $('#modal_language_code').on('input', function() {
        var val = $(this).val().trim().toUpperCase();
        $('#modal_code_preview').text(val ? val : '--');
    });

    // Preset selector handler
    $('#modal_master_language_preset').on('change', function() {
        var code = $(this).val();
        var name = $(this).find('option:selected').data('name');
        if (code && name) {
            $('#modal_language_name').val(name);
            $('#modal_language_code').val(code).trigger('input');
        }
    });

    // Submit handler
    $('#submit-create-modal-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var form = $('#createLangModalForm')[0];

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        $btn.attr('disabled', true);
        $btn.find('.indicator-label').hide();
        $btn.find('.indicator-progress').show();

        $.ajax({
            url: "{{ route('superadmin.language-settings.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $btn.attr('disabled', false);
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();

                if (response.status === 'success') {
                    var modalEl = document.getElementById('application-md-modal');
                    if (modalEl) {
                        var bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();
                    }
                    $('#application-md-modal').modal('hide');
                    Swal.fire({
                        title: "Created!",
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
                        text: response.message || "Failed to create language.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            },
            error: function(xhr) {
                $btn.attr('disabled', false);
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();

                var errorMsg = "An error occurred while saving the language.";
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
