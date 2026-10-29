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
                <i class="fa-solid fa-pen-to-square fs-2 text-primary"></i>
            </div>
            <div>
                <h1 class="lang-title">Edit Language: {{ $language->language_name }}</h1>
                <p class="lang-subtitle">Modify language configuration, dictionary code, and system availability</p>
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
                    <form id="editLangStandaloneForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $language->id }}">

                        @if($language->language_code === 'en')
                            <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-4 mb-6">
                                <i class="fa-solid fa-shield-halved fs-1 text-primary me-3 mb-2 mb-sm-0"></i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="fw-bolder text-primary mb-1">Protected Base Language (English)</h5>
                                    <span class="fs-7 text-gray-700">English is the primary fallback dictionary for all tenants. The ISO code (<code class="fs-8">en</code>) and active status are locked to safeguard platform integrity.</span>
                                </div>
                            </div>
                        @else
                            {{-- Quick Master ISO Preset Selector for non-English --}}
                            <div class="mb-6 p-4 rounded bg-light-primary border border-primary border-dashed">
                                <label class="form-label fs-7 fw-bolder text-primary mb-1">
                                    <i class="fa-solid fa-filter fs-7 me-1 text-primary"></i>Pick from Master ISO Catalog (135 Languages)
                                </label>
                                <select id="sa_edit_master_preset" class="form-select form-select-sm form-select-solid" data-control="select2">
                                    <option value="">-- Choose from ISO Catalog to update name & code --</option>
                                    @foreach($masterLanguages as $mlang)
                                        <option value="{{ strtolower($mlang->code) }}" data-name="{{ $mlang->name }}" {{ strtolower($language->language_code) === strtolower($mlang->code) ? 'selected' : '' }}>
                                            {{ $mlang->name }} ({{ strtoupper($mlang->code) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="fv-row mb-6">
                            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Name</label>
                            <input type="text" name="language_name" id="sa_edit_language_name" class="form-control form-control-solid" value="{{ $language->language_name }}" required>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Code (ISO-639-1)</label>
                            <div class="input-group">
                                <input type="text" name="language_code" id="sa_edit_language_code" class="form-control form-control-solid text-lowercase" value="{{ $language->language_code }}" maxlength="20" required {{ $language->language_code === 'en' ? 'readonly' : '' }}>
                                <span class="input-group-text bg-light-info text-info fw-bolder text-uppercase" id="sa_edit_code_preview">{{ strtoupper($language->language_code) }}</span>
                            </div>
                            @if($language->language_code !== 'en')
                                <div class="text-muted fs-8 mt-1">Renaming the code will safely synchronize existing database translation entries without data loss.</div>
                            @endif
                        </div>

                        @if($language->language_code !== 'en' && $language->language_code !== $global->locale)
                            <div class="fv-row mb-6">
                                <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Portal Availability Status</label>
                                <select name="status" id="sa_edit_language_status" class="form-select form-select-solid">
                                    <option value="enabled" {{ $language->status === 'enabled' ? 'selected' : '' }}>Enabled (Available to all users and candidates)</option>
                                    <option value="disabled" {{ $language->status === 'disabled' ? 'selected' : '' }}>Disabled (Hidden from public dropdowns)</option>
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="status" value="enabled">
                            <div class="fv-row mb-6">
                                <label class="form-label fs-6 fw-bold text-gray-800 mb-1">Portal Availability Status</label>
                                <div>
                                    <span class="badge badge-light-success fs-7 fw-bold px-3 py-2">
                                        <i class="fa-solid fa-circle-check fs-6 me-1 text-success"></i>Enabled (Active System Default Locale)
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top border-gray-200">
                            <a href="{{ route('superadmin.language-settings.index') }}" class="btn btn-light fw-bold">Cancel</a>
                            <button type="button" id="btn-update-standalone-lang" class="btn btn-primary fw-bold">
                                <span class="indicator-label">
                                    <i class="fa-solid fa-check fs-5 me-1"></i>Update Language
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
        $('#sa_edit_language_code').on('input', function() {
            var val = $(this).val().trim().toUpperCase();
            $('#sa_edit_code_preview').text(val ? val : '--');
        });

        // Preset selector handler
        $('#sa_edit_master_preset').on('change', function() {
            var code = $(this).val();
            var name = $(this).find('option:selected').data('name');
            if (code && name) {
                $('#sa_edit_language_name').val(name);
                $('#sa_edit_language_code').val(code).trigger('input');
            }
        });

        // Submit form handler
        $('#btn-update-standalone-lang').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var form = $('#editLangStandaloneForm')[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var formData = new FormData(form);
            formData.append('_method', 'PUT');

            $btn.attr('disabled', true);
            $btn.find('.indicator-label').hide();
            $btn.find('.indicator-progress').show();

            var updateUrl = "{{ route('superadmin.language-settings.update', $language->id) }}";

            $.ajax({
                url: updateUrl,
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
                        Swal.fire({
                            title: "Updated!",
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
                            text: response.message || "Failed to update language.",
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
    });
</script>
@endpush
