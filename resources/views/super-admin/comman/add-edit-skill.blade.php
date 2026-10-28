@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
    /* ─── Tagify Custom Enterprise Styling ─── */
    .tagify {
        border-radius: 8px !important;
        border-color: #e2e8f0 !important;
        background-color: #f8fafc !important;
        padding: 4px 6px !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .tagify:hover {
        border-color: #cbd5e1 !important;
    }
    .tagify.tagify--focus {
        border-color: #009ef7 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .tagify__tag {
        background-color: #eff6ff !important;
        border-radius: 6px !important;
        color: #2563eb !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        margin: 3px !important;
    }
    .tagify__tag > div {
        color: #2563eb !important;
    }
    .tagify__tag__removeBtn {
        color: #2563eb !important;
        margin-left: 4px !important;
    }
    .tagify__tag__removeBtn:hover {
        background: #dc2626 !important;
        color: #ffffff !important;
    }

    /* ─── Global & Top-Gap Normalization (15.6" Screen Standard) ─── */
    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }
    #kt_toolbar {
        display: none !important;
    }
    #kt_content {
        padding-top: 0 !important;
    }
    .cd-skills-form-wrapper {
        width: 100% !important;
        max-width: 900px !important;
        margin: 0 auto !important;
    }

    /* ─── Tier 1: Top Header Toolbar Card ─── */
    .cd-skills-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 18px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
    }
    .cd-skills-header-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }
    .cd-skills-header-icon {
        width: 38px !important;
        height: 38px !important;
        border-radius: 8px !important;
        background: #eff6ff !important;
        border: 1px solid #dbeafe !important;
        color: #2563eb !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 16px !important;
    }
    .cd-skills-header-title {
        font-size: 15.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
    }
    .cd-skills-header-sub {
        font-size: 11.5px !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }

    /* ─── Form Card & Sub-Cards ─── */
    .cd-form-subcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 20px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .cd-subcard-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 16px !important;
        padding-bottom: 10px !important;
        border-bottom: 1px dashed #e2e8f0 !important;
    }
    .cd-subcard-title {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    @media (max-width: 576px) {
        .cd-skills-header-card {
            padding: 10px 12px !important;
        }
        .cd-skills-header-right {
            width: 100% !important;
        }
        .cd-skills-header-right button {
            width: 100% !important;
        }
    }
</style>
@endpush

@php
    $isEdit = !empty($skill_id) && $skill_id !== '0' && $skill_name !== 'new';
    $pageHeading = $isEdit ? 'Edit Master Skill' : 'Add New Master Skills';
    $pageSub = $isEdit 
        ? 'Update normalized skill name in the Elasticsearch taxonomy.' 
        : 'Index one or multiple skills into the platform global taxonomy.';
@endphp

@section('content')
<div class="cd-skills-form-wrapper">

    {{-- Tier 1: Header Toolbar Card --}}
    <div class="cd-skills-header-card">
        <div class="cd-skills-header-left">
            <a href="{{ route('superadmin.company.skillskElastic') }}" class="btn btn-sm btn-light fw-bolder me-1" title="Back to directory">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
            <div class="cd-skills-header-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <div class="cd-skills-header-title">
                    {{ $pageHeading }}
                    @if($isEdit)
                        <span class="badge badge-light-primary fw-bold fs-9 font-monospace ms-2">#{{ $skill_id }}</span>
                    @else
                        <span class="badge badge-light-success fw-bold fs-9 ms-2">Bulk Add Mode</span>
                    @endif
                </div>
                <div class="cd-skills-header-sub">{{ $pageSub }}</div>
            </div>
        </div>
        <div class="cd-skills-header-right">
            <button type="button" class="btn btn-sm btn-primary fw-bolder" id="btnTopSave">
                <i class="fa fa-save me-1"></i> Save to Taxonomy
            </button>
        </div>
    </div>

    {{-- Form Sub-Card 1: Skill Definition --}}
    <div class="cd-form-subcard">
        <div class="cd-subcard-header">
            <div class="cd-subcard-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Skill Definition & Tags
            </div>
            <span class="badge badge-light-info fw-bold fs-9">Elasticsearch Read/Write Model</span>
        </div>

        <form id="editSettings" class="ajax-form">
            @csrf
            <input type="hidden" name="tokenId" id="tokenId" value="{{ $isEdit ? $skill_id : 0 }}">

            <div class="mb-5">
                <label class="form-label fs-7 fw-bolder text-dark mb-1 required">
                    {{ $isEdit ? 'Skill Name' : 'Skill Name(s)' }}
                </label>
                <div class="text-muted fs-8 mb-2">
                    @if($isEdit)
                        Edit the normalized skill identifier. White spaces and special characters are cleaned automatically.
                    @else
                        Type skill names and press enter or comma. You can paste comma-separated skills (up to 50 at once).
                    @endif
                </div>
                <input class="form-control form-control-solid" name="skill_name" id="skill_name" placeholder="e.g. PHP, Laravel, React Native" value="{{ $isEdit ? $skill_name : '' }}">
            </div>

            <div class="alert alert-light-primary d-flex align-items-center p-3 mb-5">
                <i class="fa-solid fa-circle-info fs-4 text-primary me-3"></i>
                <div class="fs-8 text-gray-700">
                    Skills are automatically normalized to <strong>lowercase</strong> and trimmed. Special characters allowed: <code>+ # / . -</code>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-4 border-top">
                <a href="{{ route('superadmin.company.skillskElastic') }}" class="btn btn-sm btn-light">Cancel</a>
                <button type="button" id="save-form" class="btn btn-sm btn-primary fw-bolder px-5">
                    <span class="indicator-label"><i class="fa fa-save me-1"></i> Save to Taxonomy</span>
                    <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Form Sub-Card 2: Taxonomy Metadata --}}
    <div class="cd-form-subcard">
        <div class="cd-subcard-header">
            <div class="cd-subcard-title">
                <i class="fa-solid fa-server text-muted"></i> Index Metadata & Tenancy Scope
            </div>
        </div>
        <div class="row g-3 fs-8">
            <div class="col-md-4">
                <div class="text-muted">Target Index:</div>
                <div class="fw-bold text-gray-800 font-monospace">local_skills (ELASTIC_SKILLS)</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Tenancy Scope:</div>
                <div class="fw-bold text-gray-800">Global Master Directory</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Refresh Strategy:</div>
                <div class="fw-bold text-success"><i class="fa fa-check-circle text-success me-1"></i> Synchronous Invalidation</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('footer-script')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
$(document).ready(function() {
    "use strict";

    let isEditMode = {{ $isEdit ? 'true' : 'false' }};
    let maxItems = isEditMode ? 1 : 50;

    let input = document.querySelector('input[name=skill_name]');
    let tagify = new Tagify(input, {
        whitelist: [],
        dropdown: {
            maxItems: 20,
            enabled: 0,
            closeOnSelect: true
        },
        maxTags: maxItems,
        transformTag: function(tagData) {
            tagData.value = tagData.value.replace(/[^a-zA-Z0-9 _+#\/.-]/g, '').toLowerCase().trim();
            return tagData;
        },
        validate: function(tagData) {
            return tagData.value.trim().length > 0;
        }
    });

    function submitSkillForm() {
        // Auto-commit any typed text remaining in the Tagify input buffer before submitting
        if (tagify && tagify.DOM && tagify.DOM.input && tagify.DOM.input.textContent.trim()) {
            tagify.addTags([tagify.DOM.input.textContent.trim()]);
        }

        let $btn = $('#save-form');
        let form = $('#editSettings')[0];
        let formData = new FormData(form);

        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
        $('#btnTopSave').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: "{{ route('superadmin.company.addEditSkillUpdate') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#btnTopSave').prop('disabled', false);

                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Skill saved successfully.',
                        icon: 'success',
                        confirmButtonText: 'Back to Directory',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        window.location.href = "{{ route('superadmin.company.skillskElastic') }}";
                    });
                } else {
                    Swal.fire('Error', response.message || 'Please verify entered skill names.', 'error');
                }
            },
            error: function(xhr) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#btnTopSave').prop('disabled', false);

                let errorMsg = 'Failed to save skills.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }

    $('#save-form, #btnTopSave').on('click', function(e) {
        e.preventDefault();
        submitSkillForm();
    });

    // Keyboard shortcut: Ctrl + Enter or Cmd + Enter to save
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            submitSkillForm();
        }
    });
});
</script>
@endpush
