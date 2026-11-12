@extends('layouts.superadmin_layout')

@section('content')
<style>
    /* ─── Global & Top-Gap Normalization (15.6" Screen Standard) ─── */
    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }
    @media (max-width: 991.98px) {
        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(55px + 6px) !important;
        }
    }
    #kt_toolbar {
        display: none !important;
        height: 0 !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #kt_content,
    .content,
    #kt_post,
    .post {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .lang-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 20px 16px !important;
        margin-top: 0 !important;
    }

    .lang-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 20px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .lang-brand-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #eff6ff;
        color: #009ef7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .lang-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .lang-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .lang-form-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        overflow: hidden;
    }
</style>

<div class="lang-container">
    {{-- Top Header Toolbar Card --}}
    <div class="lang-header-card">
        <div class="d-flex align-items-center gap-3">
            <div class="lang-brand-icon">
                <i class="fa-solid fa-circle-plus fs-2 text-primary"></i>
            </div>
            <div>
                <h1 class="lang-title">Add New Language</h1>
                <p class="lang-subtitle">Configure a new language locale, code, and base dictionary for ATS portals</p>
            </div>
        </div>
        <div>
            <a href="{{ route('superadmin.language-settings.index') }}" class="btn btn-sm btn-light fw-bold">
                <i class="fa-solid fa-arrow-left fs-6 me-1"></i>Back to Languages
            </a>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8 col-xxl-6">
            <div class="lang-form-card">
                <div class="card-body p-6 p-lg-8">
                    <form id="createLangStandaloneForm">
                        @csrf

                        {{-- Quick Master ISO Preset Selector --}}
                        <div class="mb-6 p-4 rounded bg-light-primary border border-primary border-dashed">
                            <label class="form-label fs-7 fw-bolder text-primary mb-1">
                                <i class="fa-solid fa-filter fs-7 me-1 text-primary"></i>Quick Preset from Master ISO Catalog (135 Languages)
                            </label>
                            <select id="sa_master_language_preset" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="">-- Choose from ISO Catalog to auto-fill name & code --</option>
                                @foreach($masterLanguages as $mlang)
                                    <option value="{{ strtolower($mlang->code) }}" data-name="{{ $mlang->name }}">
                                        {{ $mlang->name }} ({{ strtoupper($mlang->code) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1">Selecting a language will automatically pre-fill the name and ISO code below.</div>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Name</label>
                            <input type="text" name="language_name" id="sa_language_name" class="form-control form-control-solid" placeholder="e.g. French, Japanese, Hindi" required>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Code (ISO-639-1)</label>
                            <div class="input-group">
                                <input type="text" name="language_code" id="sa_language_code" class="form-control form-control-solid text-lowercase" placeholder="e.g. fr, ja, hi" maxlength="20" required>
                                <span class="input-group-text bg-light-info text-info fw-bolder text-uppercase" id="sa_code_preview">--</span>
                            </div>
                            <div class="text-muted fs-8 mt-1">Standard 2-letter or BCP-47 locale code used for translation directories.</div>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Portal Availability Status</label>
                            <select name="status" id="sa_language_status" class="form-select form-select-solid">
                                <option value="enabled" selected>Enabled (Available to all users and candidates)</option>
                                <option value="disabled">Disabled (Hidden from public dropdowns)</option>
                            </select>
                        </div>

                        <div class="form-check form-check-custom form-check-solid mb-8">
                            <input class="form-check-input" type="checkbox" name="scaffold_dictionary" value="1" id="sa_scaffold_dictionary" checked>
                            <label class="form-check-label fs-7 text-gray-700" for="sa_scaffold_dictionary">
                                Automatically scaffold base translation dictionary files from English (<code class="fs-8">resources/lang/en</code>)
                            </label>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top border-gray-200">
                            <a href="{{ route('superadmin.language-settings.index') }}" class="btn btn-light fw-bold">Cancel</a>
                            <button type="button" id="btn-save-standalone-lang" class="btn btn-primary fw-bold">
                                <span class="indicator-label">
                                    <i class="fa-solid fa-check fs-5 me-1"></i>Save Language
                                </span>
                                <span class="indicator-progress" style="display: none;">
                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {
        // Auto-update preview pill
        $('#sa_language_code').on('input', function() {
            var val = $(this).val().trim().toUpperCase();
            $('#sa_code_preview').text(val ? val : '--');
        });

        // Preset selector handler
        $('#sa_master_language_preset').on('change', function() {
            var code = $(this).val();
            var name = $(this).find('option:selected').data('name');
            if (code && name) {
                $('#sa_language_name').val(name);
                $('#sa_language_code').val(code).trigger('input');
            }
        });

        // Submit form handler
        var isSubmitting = false;
        $('#btn-save-standalone-lang').on('click', function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            var $btn = $(this);
            var form = $('#createLangStandaloneForm')[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var formData = new FormData(form);
            isSubmitting = true;
            $btn.attr('disabled', true);
            $btn.find('.indicator-label').hide();
            $btn.find('.indicator-progress').show();

            var idempKey = (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID() : ('idemp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9));

            $.ajax({
                url: "{{ route('superadmin.language-settings.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Idempotency-Key': idempKey
                },
                success: function(response) {
                    isSubmitting = false;
                    $btn.attr('disabled', false);
                    $btn.find('.indicator-label').show();
                    $btn.find('.indicator-progress').hide();

                    if (response.status === 'success') {
                        try {
                            if ('BroadcastChannel' in window) {
                                var ch = new BroadcastChannel('jero_languages_sync');
                                ch.postMessage({ action: 'saved' });
                            }
                        } catch (e) {}
                        Swal.fire({
                            title: "Created!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "View Languages",
                            customClass: { confirmButton: 'btn btn-primary' }
                        }).then(function() {
                            window.location.href = "{{ route('superadmin.language-settings.index') }}";
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
                    isSubmitting = false;
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
    });
</script>
@endpush