@extends('layouts.service_company_layout')
@push('head-script')
    <link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* ===== Design tokens (from new design) ===== */
    :root {
    --primary: #2563eb; --primary-hover: #1d4ed8; --primary-light: #eff6ff; --primary-soft: #dbeafe;
    --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --border-focus: #2563eb;
    --text-primary: #0f172a; --text-secondary: #475569; --text-muted: #94a3b8;
    --danger: #ef4444; --danger-light: #fef2f2; --success: #10b981; --success-light: #d1fae5; --warning: #f59e0b;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05); --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --radius: 10px; --radius-sm: 6px; --radius-lg: 12px; --radius-xl: 16px;
    --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* ===== Scope reset under #jrwrap so Metronic theme stays intact ===== */
    #jrwrap *, #jrwrap *::before, #jrwrap *::after { box-sizing: border-box; }
    #jrwrap { font-family: 'Poppins', sans-serif; color: var(--text-primary); line-height: 1.5; font-size: 14px; }
    #jrwrap p, #jrwrap h1, #jrwrap h2, #jrwrap h3, #jrwrap h4, #jrwrap h5, #jrwrap h6, #jrwrap ul, #jrwrap ol { margin: 0; }

    /* Content */
    #jrwrap .content { padding: 4px 20px 32px; }
    #jrwrap .page-header { margin-bottom: 20px; }
    #jrwrap .page-title { font-size: 15px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 12px; }
    #jrwrap .page-title span { font-size: 13px; font-weight: 400; color: var(--text-secondary); }

    /* Stepper V2 */
    #jrwrap .stepper-v2 { display: flex; align-items: center; gap: 0; margin-bottom: 24px; background: var(--card-bg); border-radius: var(--radius-lg); padding: 18px 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    #jrwrap .step-v2 { display: flex; align-items: center; gap: 10px; flex: 1; position: relative; cursor: pointer; }
    #jrwrap .step-v2:not(:last-child)::after { content: ''; position: absolute; right: 16px; top: 50%; transform: translateY(-50%); width: 32px; height: 2px; background: var(--border); }
    #jrwrap .step-v2.completed:not(:last-child)::after { background: var(--success); }
    #jrwrap .step-v2-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 600; background: var(--bg); color: var(--text-muted); }
    #jrwrap .step-v2.completed .step-v2-icon { background: var(--success); color: #fff; }
    #jrwrap .step-v2.current .step-v2-icon { background: var(--primary); color: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    #jrwrap .step-v2-info { display: flex; flex-direction: column; gap: 1px; }
    #jrwrap .step-v2-label { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    #jrwrap .step-v2-desc { font-size: 11px; color: var(--text-muted); font-weight: 400; }
    #jrwrap .step-v2.current .step-v2-label { color: var(--primary); }

    /* Cards */
    #jrwrap .card { background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 16px; overflow: visible; }
    #jrwrap .card-body { padding: 20px; }
    #jrwrap .card-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    #jrwrap .card-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-primary); }
    #jrwrap .card-title svg { width: 16px; height: 16px; color: var(--primary); }
    #jrwrap .card-subtitle { padding: 0 24px; font-size: 12px; color: var(--text-muted); margin-top: -8px; margin-bottom: 16px; }
    #jrwrap .card-edit { font-size: 12px; color: var(--primary); font-weight: 500; cursor: pointer; background: none; border: none; font-family: inherit; }
    #jrwrap .card-edit:hover { text-decoration: underline; }
    #jrwrap .section-title { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
    #jrwrap .section-title svg { width: 18px; height: 18px; color: var(--primary); }

    /* Forms */
    #jrwrap .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; }
    #jrwrap .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    #jrwrap .form-group-cus { display: flex; flex-direction: column; gap: 6px; }
    #jrwrap .form-group-cus.full-width { grid-column: 1 / -1; }
    #jrwrap .form-label { font-size: 12px; font-weight: 500; color: var(--text-primary); display: flex; align-items: center; gap: 4px; margin-bottom: 0; }
    #jrwrap .required::after { content: '*'; color: var(--danger); margin-left: 2px; }
    #jrwrap .form-hint { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    #jrwrap .form-input, #jrwrap .form-select, #jrwrap .form-textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius); font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--text-primary); background: var(--card-bg); transition: all 0.2s; outline: none; }
    #jrwrap .form-textarea { min-height: 90px; resize: vertical; }
    #jrwrap .form-input::placeholder, #jrwrap .form-textarea::placeholder { color: var(--text-muted); font-size: 12px; }
    #jrwrap .form-input:focus, #jrwrap .form-select:focus, #jrwrap .form-textarea:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    #jrwrap .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; cursor: pointer; }
    #jrwrap .input-with-icon { position: relative; }
    #jrwrap .input-with-icon .form-input { padding-left: 38px; }
    #jrwrap .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 16px; height: 16px; }
    #jrwrap .salary-input-wrapper { display: flex; gap: 8px; }
    #jrwrap .currency-select { width: 140px; min-width: 130px; flex-shrink: 0; }
    #jrwrap .salary-input-wrapper .select2-container { width: 140px !important; min-width: 130px !important; flex-shrink: 0; }
    #jrwrap .salary-input-wrapper .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid var(--border);
        border-radius: 6px;
        display: flex;
        align-items: center;
        padding-left: 10px;
        padding-right: 26px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        box-sizing: border-box;
        background-color: var(--card-bg, #ffffff);
        color: var(--text-primary, #1e293b);
    }
    #jrwrap .salary-input-wrapper .select2-container--default.select2-container--open .select2-selection--single,
    #jrwrap .salary-input-wrapper .select2-container--default .select2-selection--single:focus {
        border-color: var(--border-focus, #3b82f6) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        outline: none;
    }
    #jrwrap .salary-input-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        top: 1px;
        right: 6px;
    }
    .br-currency-select2-dropdown {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
        z-index: 1060;
    }
    .br-currency-select2-dropdown .select2-search--dropdown {
        padding: 8px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .br-currency-select2-dropdown .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        outline: none;
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff;
        width: 100%;
    }
    .br-currency-select2-dropdown .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    .br-currency-select2-dropdown .select2-results__options {
        max-height: 260px;
    }
    .br-currency-select2-dropdown .select2-results__option {
        padding: 7px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        line-height: 1.4;
    }
    .br-currency-select2-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
        background-color: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
    }
    #jrwrap .range-group { display: flex; align-items: flex-start; gap: 12px; }
    #jrwrap .range-field { flex: 1; }
    #jrwrap .range-field-label { font-size: 11px; color: var(--text-muted); margin-top: 6px; display: block; text-align: center; }
    #jrwrap .range-separator { color: var(--text-muted); font-size: 13px; padding-top: 11px; }

    /* Chips */
    #jrwrap .chips-container { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    #jrwrap .chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 20px; font-size: 12px; font-weight: 500; color: var(--text-primary); transition: all 0.2s; cursor: pointer; }
    #jrwrap .chip:hover { border-color: var(--primary); color: var(--primary); }
    #jrwrap .chip.selected { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }
    #jrwrap .chip .remove { width: 14px; height: 14px; border-radius: 50%; background: var(--text-muted); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; margin-left: 2px; line-height: 1; }
    #jrwrap .chip .remove:hover { background: var(--danger); }
    #jrwrap .chip-add { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: transparent; border: 1px dashed var(--border); border-radius: 20px; font-size: 12px; font-weight: 500; color: var(--primary); cursor: pointer; transition: all 0.2s; }
    #jrwrap .chip-add:hover { border-color: var(--primary); background: var(--primary-light); }
    #jrwrap .selected-header { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; margin-bottom: 8px; }
    #jrwrap .selected-label { font-size: 12px; font-weight: 500; color: var(--text-secondary); }
    #jrwrap .clear-all { font-size: 12px; color: var(--primary); cursor: pointer; font-weight: 500; background: none; border: none; font-family: inherit; }
    #jrwrap .clear-all:hover { text-decoration: underline; }

    /* Team */
    #jrwrap .team-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    #jrwrap .team-section { display: flex; flex-direction: column; gap: 12px; }
    #jrwrap .team-list { display: flex; flex-direction: column; gap: 8px; }
    #jrwrap .team-member { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: var(--bg); border-radius: var(--radius); border: 1px solid var(--border); }
    #jrwrap .member-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; }
    #jrwrap .member-info { flex: 1; min-width: 0; }
    #jrwrap .member-name { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #jrwrap .member-role { font-size: 11px; color: var(--text-muted); }
    #jrwrap .member-remove { width: 18px; height: 18px; border-radius: 50%; border: none; background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; }
    #jrwrap .member-remove:hover { background: var(--danger); color: #fff; }

    /* Create Options */
    #jrwrap .create-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 4px; }
    #jrwrap .option-card { padding: 20px; border: 2px solid var(--border); border-radius: var(--radius-lg); cursor: pointer; transition: all 0.2s; position: relative; background: var(--card-bg); }
    #jrwrap .option-card:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
    #jrwrap .option-card.selected { border-color: var(--primary); background: var(--primary-light); }
    #jrwrap .option-card.selected::after { content: ''; position: absolute; top: 12px; right: 12px; width: 20px; height: 20px; background: var(--primary); border-radius: 50%; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: center; }
    #jrwrap .option-icon { width: 40px; height: 40px; border-radius: var(--radius); background: var(--bg); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--primary); }
    #jrwrap .option-card.selected .option-icon { background: var(--primary); color: #fff; }
    #jrwrap .option-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    #jrwrap .option-desc { font-size: 11px; color: var(--text-muted); line-height: 1.4; }

    /* Editor */
    #jrwrap .editor-toolbar { display: flex; gap: 4px; padding: 8px; background: var(--bg); border: 1px solid var(--border); border-bottom: none; border-radius: var(--radius) var(--radius) 0 0; flex-wrap: wrap; }
    #jrwrap .toolbar-btn { width: 32px; height: 32px; border: none; background: transparent; border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    #jrwrap .toolbar-btn:hover, #jrwrap .toolbar-btn.active { background: var(--card-bg); color: var(--primary); }
    #jrwrap .toolbar-btn svg { width: 16px; height: 16px; }
    #jrwrap .toolbar-divider { width: 1px; height: 24px; background: var(--border); margin: 4px 4px; }
    #jrwrap .rich-editor { min-height: 300px; padding: 16px; border: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius); font-size: 13px; line-height: 1.6; outline: none; overflow-y: auto; background:#fff; }
    #jrwrap .rich-editor:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    #jrwrap .rich-editor:empty::before { content: attr(data-placeholder); color: var(--text-muted); }

    /* Tags Input */
    #jrwrap .tags-input-wrapper { display: flex; flex-wrap: wrap; gap: 8px; padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--card-bg); min-height: 44px; align-items: center; transition: all 0.2s; }
    #jrwrap .tags-input-wrapper:focus-within { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    #jrwrap .tag { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--primary-light); border: 1px solid var(--primary); border-radius: 16px; font-size: 12px; font-weight: 500; color: var(--primary); }
    #jrwrap .tag-remove { width: 14px; height: 14px; border-radius: 50%; background: var(--primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; border: none; line-height: 1; }
    #jrwrap .tag-input { border: none; outline: none; font-family: 'Poppins', sans-serif; font-size: 13px; flex: 1; min-width: 100px; background: transparent; }
    #jrwrap .tag-input::placeholder { color: var(--text-muted); }

    /* Questions (Step 3) */
    #jrwrap .content-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
    #jrwrap .right-sidebar { display: flex; flex-direction: column; gap: 20px; }
    #jrwrap .btn-ai { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); color: white; border: none; border-radius: var(--radius-sm); font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif; cursor: pointer; transition: var(--transition); box-shadow: 0 2px 8px rgba(139, 92, 246, 0.25); }
    #jrwrap .btn-ai:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35); }
    #jrwrap .btn-ai:active { transform: translateY(0); }
    #jrwrap .btn-ai.loading { pointer-events: none; opacity: 0.8; }
    #jrwrap .btn-ai.loading i { animation: jrspin 1s linear infinite; }
    @keyframes jrspin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    #jrwrap .questions-list { display: flex; flex-direction: column; gap: 12px; }
    #jrwrap .question-item { border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 20px; display: flex; align-items: center; gap: 14px; transition: var(--transition); cursor: grab; position: relative; background: var(--card-bg); }
    #jrwrap .question-item:hover { border-color: #cbd5e1; box-shadow: var(--shadow-md); transform: translateY(-1px); }
    #jrwrap .question-item.dragging { opacity: 0.5; cursor: grabbing; box-shadow: var(--shadow-lg); }
    #jrwrap .drag-handle { display: grid; grid-template-columns: repeat(2, 1fr); gap: 3px; cursor: grab; padding: 4px; opacity: 0.4; transition: var(--transition); }
    #jrwrap .drag-handle:hover { opacity: 0.7; }
    #jrwrap .drag-dot { width: 4px; height: 4px; background: var(--text-muted); border-radius: 50%; }
    #jrwrap .question-content { flex: 1; display: flex; flex-direction: column; gap: 8px; }
    #jrwrap .question-text { font-size: 13px; font-weight: 500; color: var(--text-primary); line-height: 1.4; }
    #jrwrap .question-meta { display: flex; align-items: center; gap: 8px; }
    #jrwrap .question-meta .badge { border: none; padding: 4px 10px; font-size: 11px; }
    #jrwrap .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; transition: var(--transition); }
    #jrwrap .badge-type-short { background: #eff6ff; color: #1d4ed8; }
    #jrwrap .badge-type-yesno { background: #f0fdf4; color: #15803d; }
    #jrwrap .badge-type-multiple { background: #fef3c7; color: #b45309; }
    #jrwrap .badge-type-number { background: #f5f3ff; color: #6d28d9; }
    #jrwrap .badge-required { background: var(--danger-light); color: var(--danger); }
    #jrwrap .badge-required::before { content: ''; width: 6px; height: 6px; background: var(--danger); border-radius: 50%; display: inline-block; }
    #jrwrap .badge-optional { background: #f8fafc; color: var(--text-muted); }
    #jrwrap .badge-optional::before { content: ''; width: 6px; height: 6px; background: var(--text-muted); border-radius: 50%; display: inline-block; }
    #jrwrap .question-actions { display: flex; align-items: center; gap: 8px; position: relative; }
    #jrwrap .btn-icon { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif; cursor: pointer; transition: var(--transition); border: 1px solid var(--border); background: white; color: var(--text-secondary); }
    #jrwrap .btn-icon:hover { background: var(--bg); border-color: #cbd5e1; }
    #jrwrap .btn-icon i { font-size: 12px; }
    #jrwrap .btn-delete { border-color: #fecaca; color: var(--danger); background: var(--danger-light); }
    #jrwrap .btn-delete:hover { background: var(--danger); color: white; border-color: var(--danger); }
    #jrwrap .btn-dropdown { width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 1px solid var(--border); background: white; color: var(--text-muted); cursor: pointer; transition: var(--transition); }
    #jrwrap .btn-dropdown:hover { background: var(--bg); color: var(--text-primary); }
    #jrwrap .add-question-area { margin-top: 16px; border: 2px dashed #cbd5e1; border-radius: var(--radius); padding: 32px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: var(--transition); background: #fafbfc; }
    #jrwrap .add-question-area:hover { border-color: var(--primary); background: var(--primary-light); }
    #jrwrap .add-question-icon { width: 44px; height: 44px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; transition: var(--transition); }
    #jrwrap .add-question-area:hover .add-question-icon { transform: scale(1.1); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    #jrwrap .add-question-title { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    #jrwrap .add-question-desc { font-size: 12px; color: var(--text-muted); }
    #jrwrap .help-card { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: var(--radius-lg); padding: 20px; position: relative; overflow: hidden; }
    #jrwrap .help-card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-size: 13px; font-weight: 600; color: var(--text-primary); }
    #jrwrap .help-card-header i { color: #f59e0b; font-size: 14px; }
    #jrwrap .help-card-text { font-size: 12px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 8px; max-width: 85%; }
    #jrwrap .help-card-recommendation { font-size: 11px; color: var(--text-muted); font-weight: 500; }
    #jrwrap .help-card-recommendation span { color: var(--success); font-weight: 600; }
    #jrwrap .help-illustration { position: absolute; right: 12px; bottom: 12px; width: 60px; height: 60px; opacity: 0.8; }
    #jrwrap .templates-card { background: white; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    #jrwrap .templates-header { padding: 16px 20px; font-size: 13px; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border); }
    #jrwrap .template-list { padding: 8px; }
    #jrwrap .template-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: var(--radius-sm); transition: var(--transition); cursor: pointer; }
    #jrwrap .template-item:hover { background: var(--bg); }
    #jrwrap .template-icon { width: 28px; height: 28px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
    #jrwrap .template-icon.blue { background: #eff6ff; color: #3b82f6; }
    #jrwrap .template-icon.green { background: #f0fdf4; color: #22c55e; }
    #jrwrap .template-icon.purple { background: #f5f3ff; color: #8b5cf6; }
    #jrwrap .template-icon.orange { background: #fff7ed; color: #f97316; }
    #jrwrap .template-icon.pink { background: #fdf2f8; color: #ec4899; }
    #jrwrap .template-icon.teal { background: #f0fdfa; color: #14b8a6; }
    #jrwrap .template-icon.indigo { background: #eef2ff; color: #6366f1; }
    #jrwrap .template-icon.red { background: #fef2f2; color: #ef4444; }
    #jrwrap .template-icon.cyan { background: #ecfeff; color: #06b6d4; }
    #jrwrap .template-icon.amber { background: #fffbeb; color: #d97706; }
    #jrwrap .template-icon.lime { background: #f7fee7; color: #65a30d; }
    #jrwrap .template-name { flex: 1; font-size: 12px; font-weight: 500; color: var(--text-primary); }
    #jrwrap .btn-add-template { width: 24px; height: 24px; border-radius: 50%; border: 1px solid var(--border); background: white; color: var(--text-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); font-size: 10px; padding: 0; }
    #jrwrap .btn-add-template:hover { background: var(--primary); color: white; border-color: var(--primary); transform: scale(1.1); }
    #jrwrap .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    #jrwrap .toggle-group { display: flex; align-items: center; gap: 10px; padding: 10px 0; }
    #jrwrap .toggle-label { font-size: 12px; font-weight: 500; color: var(--text-secondary); }
    #jrwrap .toggle-switch { position: relative; width: 40px; height: 22px; background: var(--border); border-radius: 11px; cursor: pointer; transition: var(--transition); flex-shrink: 0; }
    #jrwrap .toggle-switch.active { background: var(--primary); }
    #jrwrap .toggle-switch::after { content: ''; position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; background: white; border-radius: 50%; transition: var(--transition); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    #jrwrap .toggle-switch.active::after { left: 20px; }
    #jrwrap .phone-input-group { display: flex; gap: 8px; }
    #jrwrap .country-select { width: 100px; flex-shrink: 0; }
    #jrwrap .dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 6px; background: white; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); min-width: 160px; z-index: 50; opacity: 0; visibility: hidden; transform: translateY(-4px); transition: var(--transition); overflow: hidden; display:block; padding:0; }
    #jrwrap .dropdown-menu.show { opacity: 1; visibility: visible; transform: translateY(0); }
    #jrwrap .dropdown-item { display: flex; align-items: center; gap: 8px; padding: 10px 14px; font-size: 12px; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition); border: none; background: none; width: 100%; text-align: left; font-family: 'Poppins', sans-serif; }
    #jrwrap .dropdown-item:hover { background: var(--bg); color: var(--text-primary); }
    #jrwrap .dropdown-item i { font-size: 12px; width: 16px; text-align: center; }
    #jrwrap .dropdown-item.text-danger { color: var(--danger); }
    #jrwrap .dropdown-item.text-danger:hover { background: var(--danger-light); }
    #jrwrap .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }
    @keyframes jrfadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    #jrwrap .animate-in { animation: jrfadeIn 0.3s ease forwards; }

    /* Modal */
    #jrwrap .modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1100; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s; }
    #jrwrap .modal-overlay.active { opacity: 1; visibility: visible; }
    #jrwrap .modal { background: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); width: 100%; max-width: 560px; max-height: 90vh; overflow: hidden; transform: scale(0.95) translateY(10px); transition: all 0.3s; display: flex; flex-direction: column; }
    #jrwrap .modal-overlay.active .modal { transform: scale(1) translateY(0); }
    #jrwrap .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    #jrwrap .modal-title { font-size: 15px; font-weight: 600; }
    #jrwrap .modal-close { width: 32px; height: 32px; border: none; background: var(--bg); border-radius: var(--radius); color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    #jrwrap .modal-close:hover { background: var(--danger); color: #fff; }
    #jrwrap .modal-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
    #jrwrap .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; }

    /* Review (Step 4) */
    #jrwrap .job-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
    #jrwrap .job-badges .badge { padding: 5px 12px; border: 1px solid var(--border); background: var(--card-bg); color: var(--text-secondary); }
    #jrwrap .job-badges .badge svg { width: 13px; height: 13px; color: var(--text-muted); }
    #jrwrap .job-badges .badge.primary { border-color: var(--primary-soft); background: var(--primary-light); color: var(--primary); }
    #jrwrap .job-badges .badge.primary svg { color: var(--primary); }
    #jrwrap .review-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    #jrwrap .review-row:last-child { margin-bottom: 0; }
    #jrwrap .review-col { display: flex; flex-direction: column; gap: 12px; }
    #jrwrap .review-item { display: flex; flex-direction: column; gap: 3px; }
    #jrwrap .review-label { font-size: 11px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px; }
    #jrwrap .review-value { font-size: 13px; color: var(--text-primary); font-weight: 500; }
    #jrwrap .review-compensation { font-size: 18px; font-weight: 700; color: var(--success); margin-bottom: 4px; }
    #jrwrap .review-compensation-note { font-size: 12px; color: var(--text-muted); }
    #jrwrap .review-tags { display: flex; flex-wrap: wrap; gap: 8px; }
    #jrwrap .review-tag { padding: 5px 12px; background: var(--primary-light); border: 1px solid var(--primary-soft); border-radius: 20px; font-size: 12px; color: var(--primary); font-weight: 500; }
    #jrwrap .review-text { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }
    #jrwrap .review-text-link { font-size: 12px; color: var(--primary); font-weight: 500; cursor: pointer; margin-top: 8px; display: inline-flex; align-items: center; gap: 4px; }
    #jrwrap .review-text-link:hover { text-decoration: underline; }
    #jrwrap .review-text-link svg { width: 12px; height: 12px; }
    #jrwrap .review-questions-list { display: flex; flex-direction: column; gap: 10px; }
    #jrwrap .review-question-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--text-primary); }
    #jrwrap .review-question-num { width: 22px; height: 22px; border-radius: 50%; background: var(--primary-light); border: 1px solid var(--primary-soft); color: var(--primary); font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    #jrwrap .review-question-text { line-height: 1.5; padding-top: 2px; }

    /* Publishing */
    #jrwrap .publish-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px; }
    #jrwrap .publish-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; }
    #jrwrap .publish-card-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; }
    #jrwrap .publish-card-title svg { width: 16px; height: 16px; color: var(--primary); }
    #jrwrap .checkbox-item { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 10px; cursor: pointer; }
    #jrwrap .checkbox-item input { width: 16px; height: 16px; accent-color: var(--primary); margin-top: 2px; cursor: pointer; flex-shrink: 0; }
    #jrwrap .checkbox-label { font-size: 12px; color: var(--text-primary); font-weight: 500; line-height: 1.4; }
    #jrwrap .checkbox-sublabel { font-size: 11px; color: var(--text-muted); font-weight: 400; display: block; margin-top: 2px; }
    #jrwrap .checkbox-badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; background: var(--success-light); color: var(--success); margin-left: 4px; }
    #jrwrap .radio-item { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer; }
    #jrwrap .radio-item input { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; flex-shrink: 0; }
    #jrwrap .radio-label { font-size: 12px; color: var(--text-primary); font-weight: 500; }
    #jrwrap .coin-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 12px; }
    #jrwrap .coin-row-label { color: var(--text-secondary); }
    #jrwrap .coin-row-value { color: var(--text-primary); font-weight: 600; }
    #jrwrap .coin-row-value.green { color: var(--success); }
    #jrwrap .coin-divider { height: 1px; background: var(--border); margin: 10px 0; }
    #jrwrap .coin-link { font-size: 11px; color: var(--primary); font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
    #jrwrap .coin-link:hover { text-decoration: underline; }
    #jrwrap .coin-link svg { width: 11px; height: 11px; }
    #jrwrap .coin-alert { display: flex; align-items: center; gap: 6px; padding: 8px 10px; background: var(--success-light); border-radius: var(--radius-sm); margin-top: 10px; font-size: 12px; color: var(--success); font-weight: 500; }
    #jrwrap .coin-alert svg { width: 14px; height: 14px; flex-shrink: 0; }
    #jrwrap .terms-list { display: flex; flex-direction: column; gap: 8px; }
    #jrwrap .terms-item { display: flex; align-items: flex-start; gap: 6px; font-size: 11px; color: var(--text-secondary); line-height: 1.5; }
    #jrwrap .terms-item svg { width: 14px; height: 14px; color: var(--success); flex-shrink: 0; margin-top: 1px; }
    #jrwrap .info-box { background: var(--bg); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: 10px; font-size: 11px; color: var(--text-secondary); line-height: 1.5; }
    #jrwrap .info-box a { color: var(--primary); font-weight: 500; cursor: pointer; }
    #jrwrap .info-box a:hover { text-decoration: underline; }

    /* Summary */
    #jrwrap .summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 16px; }
    #jrwrap .summary-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; }
    #jrwrap .summary-card-title { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; }
    #jrwrap .summary-card-title svg { width: 16px; height: 16px; color: var(--primary); }
    #jrwrap .summary-list { display: flex; flex-direction: column; gap: 8px; }
    #jrwrap .summary-list-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-secondary); }
    #jrwrap .summary-list-item svg { width: 14px; height: 14px; color: var(--success); flex-shrink: 0; }
    #jrwrap .summary-metric { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 12px; }
    #jrwrap .summary-metric-label { color: var(--text-muted); }
    #jrwrap .summary-metric-value { color: var(--text-primary); font-weight: 600; }
    #jrwrap .summary-note { font-size: 10px; color: var(--text-muted); margin-top: 8px; line-height: 1.4; padding: 6px; background: var(--bg); border-radius: var(--radius-sm); }
    #jrwrap .summary-check { display: flex; align-items: flex-start; gap: 6px; margin-bottom: 8px; font-size: 11px; color: var(--text-secondary); }
    #jrwrap .summary-check input { width: 14px; height: 14px; accent-color: var(--primary); margin-top: 2px; flex-shrink: 0; }
    #jrwrap .summary-check label { cursor: pointer; line-height: 1.4; }
    #jrwrap .summary-check a { color: var(--primary); font-weight: 500; cursor: pointer; }
    #jrwrap .summary-check a:hover { text-decoration: underline; }
    #jrwrap .compact-summary { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 20px; margin-bottom: 16px; }
    #jrwrap .compact-summary-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    #jrwrap .compact-summary-title svg { width: 16px; height: 16px; color: var(--primary); }
    #jrwrap .compact-summary-subtitle { font-size: 11px; color: var(--text-muted); margin-bottom: 14px; }
    #jrwrap .compact-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 20px; }
    #jrwrap .compact-item { display: flex; align-items: center; gap: 8px; }
    #jrwrap .compact-item svg { width: 14px; height: 14px; color: var(--text-muted); flex-shrink: 0; }
    #jrwrap .compact-item-text { font-size: 12px; color: var(--text-secondary); }
    #jrwrap .compact-item-text strong { color: var(--text-primary); font-weight: 600; }

    /* Submit */
    #jrwrap .submit-card { border: 1px solid var(--primary-soft); border-radius: var(--radius-lg); background: linear-gradient(180deg, #fff 0%, var(--primary-light) 100%); padding: 24px; text-align: center; }
    #jrwrap .submit-card-title { font-size: 15px; font-weight: 600; color: var(--primary); margin-bottom: 6px; }
    #jrwrap .submit-card-desc { font-size: 12px; color: var(--text-secondary); margin-bottom: 16px; }
    #jrwrap .submit-card-footer { font-size: 11px; color: var(--text-muted); margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 6px; }
    #jrwrap .submit-card-footer svg { width: 12px; height: 12px; }

    /* Buttons */
    #jrwrap .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: var(--radius-sm); font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; border: none; outline: none; }
    #jrwrap .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    #jrwrap .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
    #jrwrap.internal-job-create .btn-primary { background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important; background-color: #ffd428 !important; color: #10143b !important; border: 1px solid #d4a017 !important; box-shadow: 0 1px 2px rgba(16, 20, 59, 0.08), 0 1px 3px rgba(245, 196, 0, 0.25) !important; }
    #jrwrap.internal-job-create .btn-primary:hover { background: linear-gradient(180deg, #fbc02d 0%, #f59e0b 100%) !important; background-color: #f59e0b !important; color: #0f172a !important; border-color: #b45309 !important; box-shadow: 0 4px 14px rgba(217, 119, 6, 0.32) !important; transform: translateY(-1px) !important; outline: none !important; }
    #jrwrap.internal-job-create .btn-primary:focus { outline: none !important; }
    #jrwrap .btn-secondary { background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border); }
    #jrwrap .btn-secondary:hover { background: var(--bg); border-color: var(--text-muted); }
    #jrwrap .btn svg { width: 16px; height: 16px; }

    /* Step Content */
    #jrwrap .step-content { display: none; animation: jrfadeIn 0.3s ease; }
    #jrwrap .step-content.active { display: block; }

    /* Search Dropdown */
    #jrwrap .search-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: var(--card-bg); border: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius); box-shadow: var(--shadow-md); max-height: 240px; overflow-y: auto; z-index: 1000; display: none; margin-top: 2px; }
    #jrwrap .search-dropdown.active { display: block; }
    #jrwrap .search-option { padding: 10px 14px; cursor: pointer; font-size: 13px; transition: background 0.15s; display: flex; align-items: center; gap: 10px; }
    #jrwrap .search-option:hover { background: var(--bg); }
    #jrwrap .search-option-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; background:#6366f1; }
    #jrwrap .search-option-info { flex: 1; }
    #jrwrap .search-option-name { font-weight: 500; color: var(--text-primary); }
    #jrwrap .search-option-role { font-size: 11px; color: var(--text-muted); }

    #jrwrap .divider { height: 1px; background: var(--border); margin: 24px 0; }
    #jrwrap .form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; gap: 12px; }

    /* =========================================================================
       Job Title Intelligence Engine Styles (Metronic Enterprise / 15.6" Screen Fitted)
       ========================================================================= */
    #jrwrap .jero-title-form-group { position: relative; }
    #jrwrap .jero-title-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 500;
        color: #10B981;
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        padding: 2px 8px;
        border-radius: 12px;
        line-height: 1.2;
    }
    #jrwrap .jero-title-select-wrap {
        position: relative;
        width: 100%;
    }
    #jrwrap .jero-title-input-box {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    #jrwrap .jero-title-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #A1A5B7;
        pointer-events: none;
        transition: color 0.2s;
        z-index: 2;
    }
    #jrwrap.internal-job-create input#title.jero-title-input,
    #jrwrap.internal-job-create .jero-title-input,
    #jrwrap input#title.jero-title-input,
    #jrwrap .jero-title-input {
        width: 100%;
        padding-left: 40px !important;
        padding-right: 56px !important;
        height: 40px;
        font-size: 13px;
        color: #181C32;
        background: #FFFFFF;
        border: 1px solid #E4E6EF;
        border-radius: 6px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    #jrwrap .jero-title-input:focus {
        border-color: #3E97FF;
        box-shadow: 0 0 0 3px rgba(62, 151, 255, 0.12);
        outline: none;
    }
    #jrwrap .jero-title-input-box:focus-within .jero-title-search-icon {
        color: #3E97FF;
    }
    #jrwrap .jero-title-clear-btn {
        position: absolute;
        right: 28px;
        background: none;
        border: none;
        padding: 4px;
        cursor: pointer;
        color: #A1A5B7;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.15s;
    }
    #jrwrap .jero-title-clear-btn:hover {
        color: #181C32;
        background: #F1F1F4;
    }
    #jrwrap .jero-title-chevron {
        position: absolute;
        right: 12px;
        color: #A1A5B7;
        pointer-events: auto;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease, background 0.15s ease;
    }
    #jrwrap .jero-title-chevron:hover {
        color: #181C32;
        background: #F1F1F4;
    }
    #jrwrap .jero-title-select-wrap.is-open .jero-title-chevron {
        transform: rotate(180deg);
        color: #3E97FF;
    }
    #jrwrap .jero-title-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        z-index: 1050;
        background: #FFFFFF;
        border: 1px solid #E1E3EA;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: min(360px, calc(100vh - 120px));
        animation: jeroDropdownFadeIn 0.15s ease-out;
    }
    #jrwrap .jero-title-dropdown.drop-up {
        top: auto;
        bottom: calc(100% + 4px);
        box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08);
    }
    @keyframes jeroDropdownFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #jrwrap .jero-title-tabs {
        display: flex;
        flex-shrink: 0;
        gap: 4px;
        padding: 6px 10px;
        background: #FFFFFF;
        border-bottom: 1px solid #F1F1F4;
    }
    #jrwrap .jero-title-tab-btn {
        font-size: 11px;
        font-weight: 500;
        color: #7E8299;
        background: none;
        border: none;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
        font-family: inherit;
    }
    #jrwrap .jero-title-tab-btn:hover {
        color: #181C32;
        background: #F1F1F4;
    }
    #jrwrap .jero-title-tab-btn.active {
        background: #EEF6FF;
        color: #3E97FF;
        font-weight: 600;
    }
    #jrwrap .jero-title-tab-btn .tab-count {
        font-size: 10px;
        opacity: 0.85;
        padding: 1px 5px;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.05);
    }
    #jrwrap .jero-title-tab-btn.active .tab-count {
        background: rgba(62, 151, 255, 0.15);
    }
    #jrwrap .jero-title-list-container {
        flex: 1 1 auto;
        min-height: 60px;
        max-height: min(280px, 42vh);
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 4px 0;
        scrollbar-width: thin;
        scrollbar-color: #D1D5DB transparent;
    }
    #jrwrap .jero-title-list-container::-webkit-scrollbar {
        width: 5px;
    }
    #jrwrap .jero-title-list-container::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }
    #jrwrap .jero-title-section-header {
        padding: 6px 12px 2px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #A1A5B7;
    }
    #jrwrap .jero-title-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        cursor: pointer;
        border-left: 3px solid transparent;
        transition: background 0.1s, border-color 0.1s;
    }
    #jrwrap .jero-title-item:hover,
    #jrwrap .jero-title-item.focused {
        background: #F8F9FA;
        border-left-color: #3E97FF;
    }
    #jrwrap .jero-title-item.selected {
        background: #F1F7FF;
        border-left-color: #3E97FF;
    }
    #jrwrap .jero-title-item-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    #jrwrap .jero-title-item-name {
        font-size: 13px;
        font-weight: 500;
        color: #181C32;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-title-item-name mark {
        background: #FFF3CD;
        color: #181C32;
        padding: 0 1px;
        border-radius: 2px;
    }
    #jrwrap .jero-title-item-sub {
        font-size: 11px;
        color: #7E8299;
    }
    #jrwrap .jero-title-item-side {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    #jrwrap .jero-title-badge {
        font-size: 10px;
        font-weight: 500;
        padding: 2px 6px;
        border-radius: 4px;
        white-space: nowrap;
    }
    #jrwrap .badge-autofill {
        background: #ECFDF5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }
    #jrwrap .badge-exp {
        background: #F1F5F9;
        color: #475569;
    }
    #jrwrap .badge-source-org {
        background: #EFF6FF;
        color: #2563EB;
    }
    #jrwrap .badge-source-std {
        background: #F8FAFC;
        color: #64748B;
    }
    #jrwrap .jero-title-item-check {
        color: #3E97FF;
        display: flex;
        align-items: center;
    }
    #jrwrap .jero-title-custom-action {
        flex-shrink: 0;
        padding: 6px 10px;
        border-top: 1px solid #F1F1F4;
        background: #FAFAFB;
    }
    #jrwrap .jero-title-custom-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 6px;
        cursor: pointer;
        background: #FFFFFF;
        border: 1px dashed #CBD5E1;
        transition: all 0.15s;
    }
    #jrwrap .jero-title-custom-pill:hover,
    #jrwrap .jero-title-custom-pill.focused {
        border-color: #3E97FF;
        background: #F0F7FF;
    }
    #jrwrap .jero-title-custom-pill .custom-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background: #E0F2FE;
        color: #0284C7;
        border-radius: 50%;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
    }
    #jrwrap .jero-title-custom-pill .custom-text-wrap {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    #jrwrap .jero-title-custom-pill .custom-title-label {
        font-size: 12px;
        font-weight: 600;
        color: #1E293B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-title-custom-pill .custom-title-sub {
        font-size: 10px;
        color: #64748B;
    }
    #jrwrap .jero-title-empty-state {
        padding: 18px 12px;
        text-align: center;
        color: #64748B;
        font-size: 12px;
    }
    #jrwrap .jero-title-footer {
        flex-shrink: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 12px;
        background: #FAFAFB;
        border-top: 1px solid #F1F1F4;
        font-size: 10px;
        color: #94A3B8;
    }
    #jrwrap .jero-title-sync-status {
        display: flex;
        align-items: center;
        gap: 4px;
        color: #0284C7;
        font-weight: 500;
    }
    #jrwrap .jero-title-footer-hint {
        color: #94A3B8;
    }
    /* =========================================================================
       God-Level Enterprise Job Department Combobox (Client-First / 10K Concurrency)
       Metronic 8, Poppins Font, 15.6" Screen Fit (1366x768 & 1920x1080)
       ========================================================================= */
    #jrwrap .jero-dept-form-group { position: relative; }
    #jrwrap .jero-dept-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 12px;
        margin-left: 8px;
        transition: all 0.2s ease;
    }
    #jrwrap .jero-dept-status-chip.chip-std {
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    #jrwrap .jero-dept-status-chip.chip-org {
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }
    #jrwrap .jero-dept-select-wrap {
        position: relative;
        width: 100%;
        font-family: 'Poppins', sans-serif;
    }
    #jrwrap .jero-dept-input-box {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    #jrwrap .jero-dept-search-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        pointer-events: none;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
    }
    #jrwrap .jero-dept-input {
        width: 100%;
        padding-left: 36px !important;
        padding-right: 68px !important;
        height: 40px;
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background 0.15s ease;
        font-family: inherit;
    }
    #jrwrap .jero-dept-input:hover {
        border-color: #94a3b8;
    }
    #jrwrap .jero-dept-input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
        outline: none;
        background-color: #ffffff;
    }
    #jrwrap .jero-dept-input.is-invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
        animation: jeroShake 0.3s ease;
    }
    @keyframes jeroShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px); }
        40%, 80% { transform: translateX(4px); }
    }
    #jrwrap .jero-dept-input-box:focus-within .jero-dept-search-icon {
        color: #2563eb;
    }
    #jrwrap .jero-dept-kbd {
        position: absolute;
        right: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 20px;
        min-width: 20px;
        padding: 0 4px;
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        pointer-events: none;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        font-family: inherit;
    }
    #jrwrap .jero-dept-input-box:focus-within .jero-dept-kbd {
        display: none;
    }
    #jrwrap .jero-dept-clear-btn {
        position: absolute;
        right: 30px;
        background: none;
        border: none;
        padding: 4px;
        color: #94a3b8;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.15s ease;
    }
    #jrwrap .jero-dept-clear-btn:hover {
        color: #ef4444;
        background: #fef2f2;
    }
    #jrwrap .jero-dept-chevron {
        position: absolute;
        right: 12px;
        color: #94a3b8;
        pointer-events: auto;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease, background 0.15s ease;
    }
    #jrwrap .jero-dept-chevron:hover {
        color: #1e293b;
        background: #f1f5f9;
    }
    #jrwrap .jero-dept-select-wrap.is-open .jero-dept-chevron {
        transform: rotate(180deg);
        color: #2563eb;
    }
    #jrwrap .jero-dept-dropdown {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        z-index: 1060;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: min(340px, calc(100vh - 140px));
        animation: jeroDropdownFadeIn 0.15s ease-out;
    }
    #jrwrap .jero-dept-dropdown.drop-up {
        top: auto;
        bottom: calc(100% + 5px);
        box-shadow: 0 -12px 32px -4px rgba(15, 23, 42, 0.12), 0 -4px 12px -2px rgba(15, 23, 42, 0.08);
    }
    @keyframes jeroDropdownFadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #jrwrap .jero-dept-tabs {
        display: flex;
        gap: 4px;
        padding: 6px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    #jrwrap .jero-dept-tab-btn {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        background: none;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
        font-family: inherit;
    }
    #jrwrap .jero-dept-tab-btn:hover {
        color: #0f172a;
        background: #f1f5f9;
    }
    #jrwrap .jero-dept-tab-btn.active {
        background: #ffffff;
        color: #1e293b;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    #jrwrap .jero-dept-tab-btn .tab-count {
        font-size: 10px;
        font-weight: 600;
        padding: 1px 6px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
    }
    #jrwrap .jero-dept-tab-btn.active .tab-count {
        background: #eff6ff;
        color: #2563eb;
    }
    #jrwrap .jero-dept-recent-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        overflow-x: auto;
        scrollbar-width: none;
    }
    #jrwrap .jero-dept-recent-bar::-webkit-scrollbar { display: none; }
    #jrwrap .jero-dept-recent-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        flex-shrink: 0;
    }
    #jrwrap .jero-dept-recent-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 500;
        color: #1e293b;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 2px 8px;
        border-radius: 12px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.12s ease;
    }
    #jrwrap .jero-dept-recent-chip:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    #jrwrap .jero-dept-list-container {
        flex: 1 1 auto;
        min-height: 0;
        max-height: 220px;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 4px 0;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    #jrwrap .jero-dept-list-container::-webkit-scrollbar {
        width: 5px;
    }
    #jrwrap .jero-dept-list-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    #jrwrap .jero-dept-section-header {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        background: rgba(248, 250, 252, 0.96);
        backdrop-filter: blur(4px);
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    #jrwrap .jero-dept-div-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 6px;
        flex-shrink: 0;
    }
    #jrwrap .jero-dept-div-icon svg {
        width: 12px;
        height: 12px;
    }
    #jrwrap .jero-dept-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 12px;
        margin: 1px 6px;
        border-radius: 6px;
        cursor: pointer;
        border-left: 3px solid transparent;
        transition: background 0.12s ease, border-color 0.12s ease;
    }
    #jrwrap .jero-dept-item:hover,
    #jrwrap .jero-dept-item.focused {
        background: #f1f5f9;
        border-left-color: #2563eb;
    }
    #jrwrap .jero-dept-item.selected {
        background: #eff6ff;
        border-left-color: #2563eb;
    }
    #jrwrap .jero-dept-item-main {
        display: flex;
        flex-direction: column;
        gap: 1px;
        min-width: 0;
    }
    #jrwrap .jero-dept-item-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.35;
    }
    #jrwrap .jero-dept-mark {
        background: #fef08a !important;
        color: #854d0e !important;
        font-weight: 700 !important;
        padding: 0 2px !important;
        border-radius: 2px !important;
    }
    #jrwrap .jero-dept-item-sub {
        font-size: 11px;
        color: #64748b;
        line-height: 1.25;
    }
    #jrwrap .jero-dept-item-side {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    #jrwrap .jero-dept-badge {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        white-space: nowrap;
        letter-spacing: 0.2px;
    }
    #jrwrap .badge-dept-org {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }
    #jrwrap .badge-dept-std {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    #jrwrap .jero-dept-item-check {
        color: #2563eb;
        display: flex;
        align-items: center;
    }
    #jrwrap .jero-dept-create-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        margin: 6px 8px;
        background: #f8faff;
        border: 1px dashed #93c5fd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    #jrwrap .jero-dept-create-action:hover,
    #jrwrap .jero-dept-create-action.focused {
        background: #eff6ff;
        border-color: #2563eb;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
    }
    #jrwrap .jero-dept-create-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    #jrwrap .jero-dept-create-plus {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #2563eb;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }
    #jrwrap .jero-dept-create-title {
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-dept-create-title strong {
        color: #2563eb;
    }
    #jrwrap .jero-dept-create-desc {
        font-size: 10.5px;
        color: #64748b;
    }
    #jrwrap .jero-dept-create-btn {
        font-size: 11px;
        font-weight: 600;
        color: #2563eb;
        background: #ffffff;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 5px;
        flex-shrink: 0;
        transition: all 0.12s ease;
    }
    #jrwrap .jero-dept-create-action:hover .jero-dept-create-btn {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    #jrwrap .jero-dept-empty-state {
        padding: 20px 14px;
        text-align: center;
        color: #64748b;
        font-size: 12px;
    }
    #jrwrap .jero-dept-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 12px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        font-size: 10px;
        color: #64748b;
    }
    #jrwrap .jero-dept-sync-status {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #059669;
        font-weight: 600;
    }
    #jrwrap .jero-dept-sync-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        animation: jeroPulseDot 2s infinite;
    }
    @keyframes jeroPulseDot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.3); opacity: 0.7; }
    }
    #jrwrap .jero-dept-footer-hint {
        color: #94a3b8;
    }
    /* Safe Auto-Fill Transactional Undo Snackbar */
    #jrwrap .jero-autofill-undo-toast {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 6px;
        padding: 6px 12px;
        background: #F0FDF4;
        border: 1px solid #86EFAC;
        border-radius: 6px;
        font-size: 12px;
        color: #166534;
        animation: jeroToastIn 0.2s ease;
    }
    @keyframes jeroToastIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #jrwrap .jero-autofill-undo-toast .undo-icon {
        display: flex;
        align-items: center;
        color: #16A34A;
        flex-shrink: 0;
    }
    #jrwrap .jero-autofill-undo-toast .undo-msg {
        flex: 1;
        font-weight: 500;
    }
    #jrwrap .jero-autofill-undo-toast .undo-btn {
        background: #DCFCE7;
        border: 1px solid #86EFAC;
        color: #15803D;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.15s;
    }
    #jrwrap .jero-autofill-undo-toast .undo-btn:hover {
        background: #BBF7D0;
    }
    /* Non-blocking Conflict Confirmation Dialog */
    #jrwrap .jero-autofill-confirm-dialog {
        margin-top: 6px;
        padding: 10px 14px;
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 6px;
        animation: jeroToastIn 0.2s ease;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-title {
        font-size: 12px;
        font-weight: 600;
        color: #92400E;
        margin-bottom: 2px;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-sub {
        font-size: 11px;
        color: #B45309;
        margin-bottom: 8px;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-actions {
        display: flex;
        gap: 8px;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-btn {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-keep {
        background: #FFFFFF;
        border-color: #D1D5DB;
        color: #374151;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-keep:hover {
        background: #F3F4F6;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-apply {
        background: #F59E0B;
        color: #FFFFFF;
        border-color: #D97706;
    }
    #jrwrap .jero-autofill-confirm-dialog .confirm-apply:hover {
        background: #D97706;
    }

    /* Toast (global, top-right) */
    .jr-toast-container { position: fixed; top: 20px; right: 20px; z-index: 2000; display: flex; flex-direction: column; gap: 8px; font-family:'Poppins',sans-serif; }
    .jr-toast { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); padding: 12px 18px; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 500; animation: jrslideIn 0.3s ease; min-width: 280px; }
    .jr-toast.success { border-left: 4px solid #10b981; }
    .jr-toast.error { border-left: 4px solid #ef4444; }
    .jr-toast.info { border-left: 4px solid #2563eb; }
    @keyframes jrslideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .jr-toast-icon { width: 18px; height: 18px; flex-shrink: 0; }
    .jr-toast.success .jr-toast-icon { color: #10b981; }
    .jr-toast.error .jr-toast-icon { color: #ef4444; }
    .jr-toast.info .jr-toast-icon { color: #2563eb; }

    @media (max-width: 1280px) {
    #jrwrap .content-grid { grid-template-columns: 1fr; }
    #jrwrap .publish-grid { grid-template-columns: repeat(2, 1fr); }
    #jrwrap .summary-grid { grid-template-columns: repeat(3, 1fr); }
    #jrwrap .compact-grid { grid-template-columns: repeat(2, 1fr); }
    #jrwrap .form-grid, #jrwrap .form-grid-3, #jrwrap .team-grid, #jrwrap .create-options { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
    #jrwrap .publish-grid, #jrwrap .summary-grid, #jrwrap .compact-grid, #jrwrap .review-row { grid-template-columns: 1fr; }
    }
    /* ── Add Contact Person — right-side drawer (overrides Bootstrap modal positioning) ── */
    #add_team_member { padding: 0 !important; }
    #add_team_member .modal-dialog {
        margin: 0 0 0 auto;
        width: min(500px, 100vw);
        max-width: 100%;
        height: 100vh;
        transform: translateX(100%);
        transition: transform .28s cubic-bezier(.4,0,.2,1) !important;
    }
    #add_team_member.show .modal-dialog { transform: translateX(0); }
    #add_team_member .modal-content {
        height: 100%;
        border-radius: 0 !important;
        border: none !important;
        border-left: 1px solid #e2e8f4 !important;
        box-shadow: -12px 0 48px rgba(17,28,68,.14) !important;
        display: flex; flex-direction: column; overflow: hidden;
    }
    @media (max-width: 767px) { #add_team_member .modal-dialog { width: 100vw; } }
    @media (min-width: 768px) and (max-width: 1024px) { #add_team_member .modal-dialog { width: min(420px,100vw); } }

    /* Header */
    #add_team_member .modal-header {
        padding: 20px 24px !important;
        border-bottom: 1px solid #edf0f7 !important;
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
        flex-shrink: 0;
        background: #fff;
    }
    #add_team_member .atm-head-info { flex: 1; min-width: 0; }
    #add_team_member .atm-title {
        margin: 0 0 3px !important;
        font-size: 17px !important; font-weight: 700 !important;
        color: #0f1940 !important; line-height: 1.3 !important;
    }
    #add_team_member .atm-subtitle {
        margin: 0 !important; font-size: 12px !important;
        color: #7889a8 !important; line-height: 1.5 !important;
        font-weight: 400 !important;
    }
    #add_team_member .atm-close-btn {
        flex-shrink: 0; width: 34px; height: 34px;
        border-radius: 8px; border: 1px solid #e2e8f4;
        background: #f5f7fb; display: inline-flex;
        align-items: center; justify-content: center;
        cursor: pointer; color: #64748b; font-size: 15px; padding: 0;
        transition: background .15s, border-color .15s, color .15s;
        margin-top: 1px;
    }
    #add_team_member .atm-close-btn:hover { background: #fff0f2; border-color: #fca5a5; color: #dc2626; }

    /* Body */
    #add_team_member .modal-body {
        padding: 24px !important;
        overflow-y: auto !important;
        flex: 1 !important;
        background: #fff;
    }

    /* Field groups */
    #add_team_member .atm-field { margin-bottom: 18px; }
    #add_team_member .atm-label {
        display: block; margin-bottom: 7px;
        font-size: 13px; font-weight: 600; color: #0f1940; line-height: 1;
    }
    #add_team_member .atm-label .atm-req { color: #e5484d; margin-left: 2px; }

    /* Input wrapper with icon */
    #add_team_member .atm-input-wrap { position: relative; display: flex; align-items: center; }
    #add_team_member .atm-input-icon {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        font-size: 16px; color: #94a3b8; pointer-events: none; line-height: 1; z-index: 2;
    }
    #add_team_member .atm-input-wrap input,
    #add_team_member .atm-input-wrap .atm-iti-wrap input {
        width: 100% !important; height: 44px !important;
        padding: 0 14px 0 40px !important;
        border: 1px solid #d8e2f0 !important; border-radius: 8px !important;
        background: #fff !important; color: #1e2d4f !important;
        font-size: 13px !important; font-family: inherit !important;
        outline: none !important;
        box-shadow: none !important;
        transition: border-color .15s, box-shadow .15s !important;
    }
    #add_team_member .atm-input-wrap input::placeholder { color: #94a3b8 !important; font-size: 12.5px !important; }
    #add_team_member .atm-input-wrap input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,.10) !important;
    }
    #add_team_member .atm-input-wrap input.is-invalid { border-color: #ef4444 !important; }
    #add_team_member .atm-input-wrap input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.10) !important; }
    #add_team_member .atm-error { display: none; margin-top: 5px; font-size: 11.5px; color: #dc2626; line-height: 1.4; }
    #add_team_member .atm-error.visible { display: block; }

    /* intl-tel-input overrides — keep icon padding */
    #add_team_member .iti { width: 100%; }
    #add_team_member .iti .iti__flag-container { top: 50%; transform: translateY(-50%); height: 30px; left: 8px; }
    #add_team_member .iti input[type=tel],
    #add_team_member .iti input[type=number] {
        padding-left: 52px !important;
    }

    /* Footer */
    #add_team_member .atm-footer {
        padding: 16px 24px;
        border-top: 1px solid #edf0f7;
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px; flex-shrink: 0; background: #fff;
    }
    #add_team_member .atm-cancel-btn {
        height: 42px; padding: 0 20px; border-radius: 8px;
        border: 1px solid #d0d8e8; background: #fff;
        color: #0f1940; font-size: 13px; font-weight: 600; cursor: pointer;
        transition: background .15s, border-color .15s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    #add_team_member .atm-cancel-btn:hover { background: #f5f7fb; border-color: #b6c2d6; }
    #add_team_member .atm-save-btn {
        height: 42px; padding: 0 22px; border-radius: 8px;
        background: #315efb; border: none;
        color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: background .15s, opacity .15s;
    }
    #add_team_member .atm-save-btn:hover:not(:disabled) { background: #1e4fd4; }
    #add_team_member .atm-save-btn:disabled { opacity: .7; cursor: not-allowed; }

    /* ==========================================================================
       Gender Diversity Dropdown — God-Level Metronic SaaS Redesign
       Scope: #jrwrap.internal-job-create .ij-gender-group
       ========================================================================== */
    #jrwrap .ij-gender-group { position: relative !important; display: flex !important; flex-direction: column !important; gap: 4px !important; }
    #jrwrap .ij-gender-label { display: inline-flex !important; align-items: center !important; gap: 6px !important; font-size: 12px !important; font-weight: 500 !important; color: #1e293b !important; margin-bottom: 2px !important; user-select: none; }
    #jrwrap .ij-gender-info-icon { display: inline-flex !important; align-items: center !important; justify-content: center !important; color: #94a3b8 !important; cursor: pointer !important; transition: color 0.15s ease, transform 0.15s ease !important; line-height: 1 !important; }
    #jrwrap .ij-gender-info-icon:hover { color: #2563eb !important; transform: scale(1.08) !important; }
    #jrwrap .ij-gender-dropdown-wrapper { position: relative !important; width: 100% !important; }
    #jrwrap .ij-gender-trigger { width: 100% !important; height: 36px !important; min-height: 36px !important; padding: 0 12px !important; display: flex !important; align-items: center !important; justify-content: space-between !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; border-radius: 6px !important; color: #0f172a !important; font-family: 'Poppins', sans-serif !important; font-size: 12.5px !important; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important; outline: none !important; box-sizing: border-box !important; text-align: left !important; }
    #jrwrap .ij-gender-trigger:hover { border-color: #94a3b8 !important; background: #fafbfc !important; }
    #jrwrap .ij-gender-trigger:focus, #jrwrap .ij-gender-trigger.is-active { border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important; background: #ffffff !important; }
    #jrwrap .ij-gender-trigger-left { display: flex !important; align-items: center !important; gap: 9px !important; min-width: 0 !important; flex: 1 1 auto !important; overflow: hidden !important; }
    #jrwrap .ij-gender-trigger-badge { width: 24px !important; height: 24px !important; border-radius: 5px !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; transition: all 0.15s ease !important; }
    #jrwrap .ij-gender-trigger-text { font-size: 12.5px !important; font-weight: 400 !important; color: #94a3b8 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; transition: color 0.15s ease, font-weight 0.15s ease !important; }
    #jrwrap .ij-gender-trigger-text.has-value { color: #0f172a !important; font-weight: 500 !important; }
    #jrwrap .ij-gender-chevron { color: #64748b !important; flex-shrink: 0 !important; margin-left: 8px !important; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease !important; }
    #jrwrap .ij-gender-trigger.is-active .ij-gender-chevron { transform: rotate(180deg) !important; color: #2563eb !important; }
    #jrwrap .ij-gender-menu { position: absolute !important; top: calc(100% + 4px) !important; left: 0 !important; width: 100% !important; min-width: 290px !important; max-width: 380px !important; background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 10px !important; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.14), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important; padding: 6px !important; z-index: 1060 !important; display: flex !important; flex-direction: column !important; gap: 2px !important; max-height: 330px !important; overflow-y: auto !important; overscroll-behavior: contain !important; transform-origin: top center !important; opacity: 0 !important; transform: translateY(-4px) scale(0.98) !important; transition: opacity 0.15s cubic-bezier(0, 0, 0.2, 1), transform 0.15s cubic-bezier(0, 0, 0.2, 1) !important; box-sizing: border-box !important; }
    #jrwrap .ij-gender-menu.is-open { opacity: 1 !important; transform: translateY(0) scale(1) !important; }
    #jrwrap .ij-gender-menu::-webkit-scrollbar { width: 5px !important; }
    #jrwrap .ij-gender-menu::-webkit-scrollbar-track { background: transparent !important; }
    #jrwrap .ij-gender-menu::-webkit-scrollbar-thumb { background: #cbd5e1 !important; border-radius: 4px !important; }
    #jrwrap .ij-gender-option { display: flex !important; align-items: center !important; gap: 10px !important; padding: 8px 10px !important; border-radius: 7px !important; cursor: pointer !important; background: transparent !important; border: 1px solid transparent !important; transition: background 0.15s ease, border-color 0.15s ease !important; user-select: none !important; outline: none !important; box-sizing: border-box !important; }
    #jrwrap .ij-gender-option:hover { background: #f8fafc !important; border-color: #f1f5f9 !important; }
    #jrwrap .ij-gender-option.is-focused { background: #f1f5f9 !important; border-color: #e2e8f0 !important; }
    #jrwrap .ij-gender-option.is-selected { background: #eff6ff !important; border-color: #bfdbfe !important; }
    #jrwrap .ij-gender-option.is-selected .ij-gender-option-title { color: #1d4ed8 !important; font-weight: 600 !important; }
    #jrwrap .ij-gender-option-info { display: flex !important; flex-direction: column !important; gap: 1px !important; flex: 1 1 auto !important; min-width: 0 !important; }
    #jrwrap .ij-gender-option-title { font-size: 12.5px !important; font-weight: 600 !important; color: #1e293b !important; line-height: 1.3 !important; font-family: 'Poppins', sans-serif !important; }
    #jrwrap .ij-gender-option-desc { font-size: 11px !important; color: #64748b !important; line-height: 1.25 !important; font-family: 'Poppins', sans-serif !important; }
    #jrwrap .ij-gender-badge { width: 32px !important; height: 32px !important; border-radius: 7px !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; transition: transform 0.15s ease !important; }
    #jrwrap .ij-gender-option:hover .ij-gender-badge { transform: scale(1.05) !important; }
    #jrwrap .ij-badge-default { background: #f8fafc !important; color: #64748b !important; border: 1px solid #e2e8f0 !important; }
    #jrwrap .ij-badge-neutral { background: #f1f5f9 !important; color: #475569 !important; }
    #jrwrap .ij-badge-male { background: #eff6ff !important; color: #2563eb !important; }
    #jrwrap .ij-badge-female { background: #fff1f2 !important; color: #e11d48 !important; }
    #jrwrap .ij-badge-balanced { background: #ecfdf5 !important; color: #059669 !important; }
    #jrwrap .ij-badge-other { background: #f5f3ff !important; color: #7c3aed !important; }
    #jrwrap .ij-gender-check { display: flex !important; align-items: center !important; justify-content: center !important; color: #2563eb !important; flex-shrink: 0 !important; width: 18px !important; height: 18px !important; opacity: 0 !important; transform: scale(0.6) !important; transition: opacity 0.15s ease, transform 0.15s ease !important; }
    #jrwrap .ij-gender-option.is-selected .ij-gender-check { opacity: 1 !important; transform: scale(1) !important; }
    #jrwrap .ij-gender-hint { font-size: 11px !important; color: #64748b !important; margin-top: 3px !important; line-height: 1.25 !important; font-family: 'Poppins', sans-serif !important; }

    /* ==========================================================================
       God-Level Enterprise Client Select Component (Client-First Execution)
       Scope: Strictly isolated to #jrwrap in resources/views/service_company/jobs/create.blade.php
       Standards: Metronic 8, Poppins font, max 14px, 15.6" screen fit (1366x768 & 1920x1080)
       ========================================================================== */
    #jrwrap .jero-client-select-wrap {
        position: relative;
        width: 100%;
        font-family: 'Poppins', sans-serif;
    }
    #jrwrap .jero-client-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 42px;
        padding: 6px 14px;
        border: 1px solid var(--border, #E4E6EF);
        border-radius: var(--radius, 6px);
        background: var(--card-bg, #FFFFFF);
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
        user-select: none;
        gap: 10px;
    }
    #jrwrap .jero-client-trigger:hover {
        border-color: #B5B5C3;
    }
    #jrwrap .jero-client-trigger:focus,
    #jrwrap .jero-client-trigger.active {
        border-color: #3E97FF !important;
        box-shadow: 0 0 0 3px rgba(62, 151, 255, 0.15) !important;
    }
    #jrwrap .jero-client-trigger-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    #jrwrap .jero-client-avatar {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        text-transform: uppercase;
        background: #F1F1F4;
        color: #7E8299;
        overflow: hidden;
    }
    #jrwrap .jero-client-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #jrwrap .jero-client-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }
    #jrwrap .jero-client-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary, #181C32);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-client-label.placeholder {
        color: var(--text-muted, #7E8299);
        font-weight: 400;
    }
    #jrwrap .jero-client-sub {
        font-size: 11px;
        color: var(--text-muted, #7E8299);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-client-badge {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        flex-shrink: 0;
        letter-spacing: 0.2px;
    }
    #jrwrap .jero-client-badge.badge-internal {
        background: #F8F5FF;
        color: #7239EA;
        border: 1px solid #E9D5FF;
    }
    #jrwrap .jero-client-badge.badge-client {
        background: #E8FFF3;
        color: #50CD89;
        border: 1px solid #BBF7D0;
    }
    #jrwrap .jero-client-badge.badge-deactivated,
    #jrwrap .jero-client-badge.badge-deleted {
        background: #FFF5F8;
        color: #F1416C;
        border: 1px solid #FDC9D7;
    }
    #jrwrap .jero-client-badge.badge-recent {
        background: #FFF8DD;
        color: #F6C000;
        border: 1px solid #FFE680;
    }
    #jrwrap .jero-client-trigger.selection-invalid {
        border-color: #F1416C !important;
        background-color: #FFFDFD;
    }
    #jrwrap .jero-client-trigger-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    #jrwrap .jero-client-clear-btn {
        background: none;
        border: none;
        padding: 4px;
        cursor: pointer;
        color: #A1A5B7;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.15s;
    }
    #jrwrap .jero-client-clear-btn:hover {
        color: #F1416C;
        background: #FFF5F8;
    }
    #jrwrap .jero-client-chevron {
        display: flex;
        align-items: center;
        color: #7E8299;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #jrwrap .jero-client-trigger.active .jero-client-chevron {
        transform: rotate(180deg);
    }

    /* Popover Container (Floating UI, Boundary Clamped for 15.6" Displays) */
    #jrwrap .jero-client-popover {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 5px);
        z-index: 1055;
        background: #FFFFFF;
        border: 1px solid #E4E6EF;
        border-radius: 8px;
        box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.1), 0 2px 6px 0 rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: jeroFadeDown 0.15s ease-out;
    }
    #jrwrap .jero-client-popover.flipped-up {
        top: auto;
        bottom: calc(100% + 5px);
        animation: jeroFadeUp 0.15s ease-out;
    }
    @keyframes jeroFadeDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes jeroFadeUp {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Sticky Search Header */
    #jrwrap .jero-client-search-box {
        position: sticky;
        top: 0;
        padding: 8px 10px;
        background: #F9F9F9;
        border-bottom: 1px solid #F1F1F4;
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 2;
    }
    #jrwrap .jero-client-search-icon {
        color: #7E8299;
        flex-shrink: 0;
    }
    #jrwrap .jero-client-search-input {
        flex: 1;
        border: 1px solid #E4E6EF;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        background: #FFFFFF;
        color: #181C32;
        outline: none;
        transition: border-color 0.15s;
    }
    #jrwrap .jero-client-search-input:focus {
        border-color: #3E97FF;
        box-shadow: 0 0 0 2px rgba(62, 151, 255, 0.1);
    }
    #jrwrap .jero-client-search-clear {
        background: none;
        border: none;
        padding: 2px;
        cursor: pointer;
        color: #A1A5B7;
        display: flex;
        align-items: center;
        border-radius: 4px;
    }
    #jrwrap .jero-client-search-clear:hover {
        color: #F1416C;
    }

    /* Segmented Filter Tabs */
    #jrwrap .jero-client-tabs {
        display: flex;
        gap: 4px;
        padding: 6px 10px;
        background: #FFFFFF;
        border-bottom: 1px solid #F1F1F4;
    }
    #jrwrap .jero-tab-btn {
        font-size: 11px;
        font-weight: 500;
        color: #7E8299;
        background: none;
        border: none;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
        font-family: inherit;
    }
    #jrwrap .jero-tab-btn:hover {
        color: #181C32;
        background: #F1F1F4;
    }
    #jrwrap .jero-tab-btn.active {
        background: #EEF6FF;
        color: #3E97FF;
        font-weight: 600;
    }
    #jrwrap .tab-count {
        font-size: 10px;
        opacity: 0.85;
        padding: 1px 5px;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.05);
    }
    #jrwrap .jero-tab-btn.active .tab-count {
        background: rgba(62, 151, 255, 0.15);
    }

    /* List Container (Optimized for 15.6" screens: 264px viewport with DOM Virtualization) */
    #jrwrap .jero-client-list-container {
        height: 264px;
        max-height: 264px;
        overflow-y: auto;
        position: relative;
        padding: 0;
        scrollbar-width: thin;
        scrollbar-color: #D1D5DB transparent;
    }
    #jrwrap .jero-virtual-phantom {
        width: 100%;
        position: relative;
        pointer-events: none;
    }
    #jrwrap .jero-virtual-window {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        will-change: transform;
        pointer-events: auto;
    }
    #jrwrap .jero-client-list-container::-webkit-scrollbar {
        width: 5px;
    }
    #jrwrap .jero-client-list-container::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 4px;
    }

    /* List Item (Strictly 44px Fixed Height for O(1) Virtual Windowing) */
    #jrwrap .jero-client-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 44px;
        max-height: 44px;
        padding: 0 12px;
        cursor: pointer;
        transition: background 0.12s ease;
        gap: 10px;
        border-left: 3px solid transparent;
        user-select: none;
        box-sizing: border-box;
    }
    #jrwrap .jero-client-item:hover,
    #jrwrap .jero-client-item.focused {
        background: #F8F9FA;
        border-left-color: #3E97FF;
    }
    #jrwrap .jero-client-item.selected {
        background: #F1F7FF;
        border-left-color: #3E97FF;
    }
    #jrwrap .jero-client-item-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    #jrwrap .jero-client-item-avatar {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    #jrwrap .jero-client-item-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #jrwrap .jero-client-item-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }
    #jrwrap .jero-client-item-name {
        font-size: 13px;
        font-weight: 500;
        color: #181C32;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-client-item-name mark {
        background: #FFF3CD;
        color: #181C32;
        padding: 0 1px;
        border-radius: 2px;
    }
    #jrwrap .jero-client-item-sub {
        font-size: 11px;
        color: #7E8299;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jrwrap .jero-client-item-check {
        color: #3E97FF;
        flex-shrink: 0;
    }

    /* Footer */
    #jrwrap .jero-client-footer {
        padding: 8px 12px;
        border-top: 1px solid #F1F1F4;
        background: #FDFDFE;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 2;
    }
    #jrwrap .jero-btn-add-client {
        font-size: 12px;
        font-weight: 500;
        color: #3E97FF;
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 3px 6px;
        border-radius: 4px;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.15s;
    }
    #jrwrap .jero-btn-add-client:hover {
        background: #EEF6FF;
        color: #2563EB;
        text-decoration: none;
    }
    #jrwrap .jero-footer-hint {
        font-size: 10px;
        color: #A1A5B7;
    }

    /* ==========================================================================
       ENTERPRISE REQUIRED FIELDS MODAL & FIELD VALIDATION ERROR STYLES
       ========================================================================== */
    .rf-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999999 !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        padding: 16px !important;
        box-sizing: border-box !important;
        animation: rfFadeIn 0.18s ease-out;
    }

    @keyframes rfFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes rfScaleIn {
        from { opacity: 0; transform: scale(0.96) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .rf-modal-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        width: 100% !important;
        max-width: 480px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        padding: 32px 30px 26px !important;
        position: relative !important;
        font-family: 'Poppins', sans-serif !important;
        animation: rfScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-sizing: border-box !important;
        margin: auto !important;
        border: 1px solid #e2e8f0 !important;
    }

    .rf-modal-close {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        background: #f1f5f9 !important;
        border: none !important;
        color: #64748b !important;
        font-size: 14px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.15s ease !important;
        padding: 0 !important;
    }

    .rf-modal-close:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }

    .rf-modal-close svg {
        stroke: currentColor !important;
        transition: transform 0.15s ease !important;
    }

    .rf-modal-close:hover svg {
        transform: rotate(90deg) !important;
    }

    .rf-warning-icon-wrap {
        width: 64px !important;
        height: 64px !important;
        border-radius: 50% !important;
        background: #fef9c3 !important;
        border: 2px solid #facc15 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 16px !important;
    }

    .rf-warning-icon {
        font-size: 30px !important;
        font-weight: 700 !important;
        color: #ca8a04 !important;
        line-height: 1 !important;
        font-family: 'Poppins', sans-serif !important;
    }

    .rf-modal-title {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        text-align: center !important;
        margin: 0 0 8px 0 !important;
        line-height: 1.25 !important;
    }

    .rf-modal-desc {
        font-size: 13.5px !important;
        color: #64748b !important;
        text-align: center !important;
        line-height: 1.45 !important;
        margin: 0 auto 22px !important;
        max-width: 360px !important;
    }

    .rf-section-header {
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.6px !important;
        color: #64748b !important;
        text-transform: uppercase !important;
        text-align: left !important;
        margin-bottom: 10px !important;
    }

    .rf-fields-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        max-height: 240px !important;
        overflow-y: auto !important;
        padding-right: 4px !important;
        margin-bottom: 12px !important;
    }

    .rf-field-row {
        background: #fef2f2 !important;
        border: 1px solid #fee2e2 !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
        box-sizing: border-box !important;
    }

    .rf-field-row:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        transform: translateY(-1px) !important;
    }

    .rf-field-row-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }

    .rf-field-badge {
        width: 20px !important;
        height: 20px !important;
        border-radius: 50% !important;
        background: #ef4444 !important;
        color: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        flex-shrink: 0 !important;
        line-height: 1 !important;
    }

    .rf-field-name {
        font-size: 13.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .rf-field-chevron {
        color: #64748b !important;
        font-size: 12px !important;
        flex-shrink: 0 !important;
        margin-left: 8px !important;
        stroke: currentColor !important;
        transition: transform 0.15s ease, color 0.15s ease !important;
    }

    .rf-field-row:hover .rf-field-chevron {
        color: #0f172a !important;
        transform: translateX(2px) !important;
    }

    .rf-helper-text {
        font-size: 12px !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin-bottom: 20px !important;
        text-align: left !important;
    }

    .rf-helper-text svg {
        font-size: 13px !important;
        color: #64748b !important;
        stroke: currentColor !important;
        flex-shrink: 0 !important;
    }

    .rf-cta-btn {
        width: 100% !important;
        height: 44px !important;
        background: #1b84ff !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        font-family: 'Poppins', sans-serif !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        cursor: pointer !important;
        transition: background 0.15s ease, transform 0.1s ease !important;
    }

    .rf-cta-btn:hover {
        background: #1570ef !important;
    }

    .rf-cta-btn:active {
        transform: scale(0.99) !important;
    }

    /* Field Attention Pulse Highlight */
    .field-attention-highlight {
        outline: 2px solid #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out !important;
        background-color: rgba(239, 246, 255, 0.5) !important;
        animation: rfPulseHighlight 0.4s ease-in-out 2;
    }

    @keyframes rfPulseHighlight {
        0% { transform: scale(1); }
        50% { transform: scale(1.008); }
        100% { transform: scale(1); }
    }

    /* Inline Field Error Message */
    .field-error-message {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin-top: 6px !important;
        color: #ef4444 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
        animation: fieldErrorFadeIn 0.2s ease-in-out;
    }

    .field-error-message svg {
        color: #ef4444 !important;
        stroke: currentColor !important;
        flex-shrink: 0 !important;
    }

    .field-error-message span {
        color: #dc2626 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
    }

    @keyframes fieldErrorFadeIn {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Field Validation Error State Highlights */
    .has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .jero-client-select-wrap.has-validation-error .jero-client-trigger {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .jero-title-select-wrap.has-validation-error .jero-title-input-box,
    .jero-title-input-box.has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .jero-dept-select-wrap.has-validation-error .jero-dept-input-box,
    .jero-dept-input-box.has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .ij-choices.has-validation-error {
        border: 1.5px solid #ef4444 !important;
        border-radius: 10px !important;
        padding: 3px !important;
        background: #fef2f2 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
    }

    .ij-salary-input-wrapper.has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .ij-location-input-wrap.has-validation-error .form-input,
    .ij-location-input-wrap.has-validation-error .input-with-icon {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .ij-qual-input-wrap.has-validation-error .form-input,
    .ij-qual-input-wrap.has-validation-error .input-with-icon {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    #jobDescription.has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    #skillsWrapper.has-validation-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .team-section .input-wrapper.has-validation-error .form-input {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    }

    .r4-visibility-list.has-validation-error {
        border: 1px solid #ef4444 !important;
        border-radius: 8px !important;
        padding: 8px !important;
        background: #fef2f2 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
    }
    </style>
    {{-- Create-only presentation assets placed AFTER the base styles so all override tokens take precedence. --}}
    @if(request()->routeIs('service_company.jobs.create') || request()->is('service_company/jobs/create*'))
        <style>
            #kt_toolbar { display: none !important; height: 0 !important; min-height: 0 !important; padding: 0 !important; margin: 0 !important; }
            .header-fixed.toolbar-fixed #kt_wrapper, .toolbar-fixed #kt_wrapper { padding-top: 65px !important; }
            #jrwrap.internal-job-create { margin-top: 0 !important; padding-top: 0 !important; }
        </style>
        <link rel="stylesheet" href="{{ asset('css/internal-job-create-design.css') }}?v=20261012d">
    @endif
@endpush
@section('content')
    <?php
    use App\Helper\GeneralMethod;
    $currency_code = GeneralMethod::defaultCurrency();
    $country_code = GeneralMethod::countryCodeFun();

    $pagetitle = $end_date = $jobtags = $recruiterData = $recruiterDataId = $cpersonData = $carriew_website_chk = '';
    $job_id = 0;
    $numberDays = '';
    if ($page_event == 'ADD') {
        $pagetitle = 'Add Job';
        $rec_inc = 0;
        foreach ($recruiterlist as $recData) {
            if ($rec_inc == 0) {
                $recruiterData = $recData->name;
                $recruiterDataId = $recData->id;
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
                $recruiterDataId = $recruiterDataId . ' , ' . $recData->id;
            }
            $rec_inc++;
        }
    } elseif ($page_event == 'EDIT') {
        $pagetitle = 'Edit Job';
        $end_date = @$job->end_date;
        $date = strtotime($end_date);
        $new_date = date('Y-m-d', $date);

        $startTimeStamp = strtotime($job->start_date);
        $endTimeStamp = strtotime($job->end_date);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400;
        $numberDays = intval($numberDays);

        $cnt = 0;
        foreach ($job_metronics_skills as $jobSkills) {
            if ($cnt == 0) {
                $jobtags = $jobSkills->skill_name;
            } else {
                $jobtags = $jobtags . ' , ' . $jobSkills->skill_name;
            }
            $cnt++;
        }
        $job_id = @$job->id;

        $rec_inc = 0;
        foreach ($recruiterlist as $recData) {
            if ($rec_inc == 0) {
                $recruiterData = $recData->name;
                $recruiterDataId = $recData->id;
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
                $recruiterDataId = $recruiterDataId . ' , ' . $recData->id;
            }
            $rec_inc++;
        }

        $cp_inc = 0;
        foreach ($contactPersonList as $cpData) {
            if ($cp_inc == 0) {
                $cpersonData = $cpData->contact_person_name;
            } else {
                $cpersonData = $cpersonData . ' , ' . $cpData->contact_person_name;
            }
            $cp_inc++;
        }

        if (@$job->carrier_website == 1) {
            $carriew_website_chk = 'checked="checked"';
        }
    }
    ?>

    @if(!request()->routeIs('service_company.jobs.create') && !request()->is('service_company/jobs/create*'))
      <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Add Requisition-->
                <div class="d-flex flex-column">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);"
                            class="text-gray-800 mb-1"><?php echo @$pagetitle; ?></a></h1>
                </div>
                <!--end::Add Requisition-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Job details and submit</span>
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    @endif


    <div id="jrwrap" @if(request()->routeIs('service_company.jobs.create') || request()->is('service_company/jobs/create*')) class="internal-job-create" @endif>
        <div class="content">@if(!request()->routeIs('service_company.jobs.create') && !request()->is('service_company/jobs/create*'))<br>@endif
       
            <!-- Stepper -->
            <div class="stepper-v2" id="stepper">
                <div class="step-v2 current" data-step="1" onclick="goToStep(1)">
                    <div class="step-v2-icon">1</div>
                    <div class="step-v2-info">
                        <div class="step-v2-label">Basic Details</div>
                        <div class="step-v2-desc">Current Step</div>
                    </div>
                </div>
                <div class="step-v2 pending" data-step="2" onclick="goToStep(2)">
                    <div class="step-v2-icon">2</div>
                    <div class="step-v2-info">
                        <div class="step-v2-label">Job Description</div>
                        <div class="step-v2-desc">Pending</div>
                    </div>
                </div>
                <div class="step-v2 pending" data-step="3" onclick="goToStep(3)">
                    <div class="step-v2-icon">3</div>
                    <div class="step-v2-info">
                        <div class="step-v2-label">Screening</div>
                        <div class="step-v2-desc">Pending</div>
                    </div>
                </div>
                <div class="step-v2 pending" data-step="4" onclick="goToStep(4)">
                    <div class="step-v2-icon">4</div>
                    <div class="step-v2-info">
                        <div class="step-v2-label">Review &amp; Publish</div>
                        <div class="step-v2-desc">Pending</div>
                    </div>
                </div>
            </div>
            
            <form class="form" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_contact_add_form" name="kt_contact_add_form">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <!-- ================= STEP 1: JOB DETAILS ================= -->
                <div class="step-content active" id="step1">
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>Basic Information
                            </div>
                            <div class="form-grid">
                                <div class="form-group-cus" id="form_group_client_select" style="position:relative;">
                                    <label class="form-label required">Hiring Organization</label>
                                    <?php
                                    $selected_client_id = 0;
                                    if (isset($job->client_id) && $job->client_id > 0) { $selected_client_id = (int) $job->client_id; }
                                    if (isset($currentClient) && $currentClient > 0) { $selected_client_id = (int) $currentClient; }
                                    if ($global_company_type == 3) { ?>
                                        <input type="text" class="form-input" value="<?php echo @$client[0]->name; ?>" readonly>
                                        <input type="hidden" id="client_id" name="client_id" value="<?php echo @$client[0]->id; ?>">
                                    <?php } else { ?>
                                    <select class="form-select jero-legacy-client-select" id="client_id" name="client_id" style="position: absolute; opacity: 0; pointer-events: none; height: 0; width: 0; margin: 0; padding: 0; border: 0; z-index: -1;">
                                    @if (@$direct_add)
                                        <option value="">Select Hiring Organization</option>
                                    @endif
                                        <optgroup label="My Organization">
                                            <?php
                                            foreach ($client as $clientarr) {
                                                if ($clientarr->self_company) {
                                                    $client_id = $sel = '';
                                                    $client_id = @$job->client_id;
                                                    if (@$currentClient > 0) { $client_id = $currentClient; }
                                                    if ($client_id == $clientarr->id) { $sel = 'selected="selected"'; }
                                                    echo '<option value="' . @$clientarr->id . '" ' . $sel . '>' . ucfirst($clientarr->name) . '</option>';
                                                }
                                            }
                                            ?>
                                        </optgroup>
                                        <optgroup label="Clients">
                                            <?php
                                            foreach ($client as $clientarr) {
                                                if (!$clientarr->self_company) {
                                                    $client_id = $sel = '';
                                                    $client_id = @$job->client_id;
                                                    if (@$currentClient > 0) { $client_id = $currentClient; }
                                                    if ($client_id == $clientarr->id) { $sel = 'selected="selected"'; }
                                                    echo '<option value="' . @$clientarr->id . '" ' . $sel . '>' . ucfirst($clientarr->name) . '</option>';
                                                }
                                            }
                                            ?>
                                        </optgroup>
                                    </select>

                                    <!-- God-Level Enterprise Client Select Component (Client-First, User RAM & IndexedDB) -->
                                    <div class="jero-client-select-wrap" id="jero_client_select_wrap">
                                        <div class="jero-client-trigger" id="jero_client_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-label="Select Hiring Organization">
                                            <div class="jero-client-trigger-content">
                                                <div class="jero-client-avatar" id="jero_trigger_avatar">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                                                </div>
                                                <div class="jero-client-info">
                                                    <span class="jero-client-label placeholder" id="jero_trigger_label">Select Hiring Organization</span>
                                                    <span class="jero-client-sub" id="jero_trigger_sub" style="display:none;"></span>
                                                </div>
                                                <span class="jero-client-badge" id="jero_trigger_badge" style="display:none;"></span>
                                            </div>
                                            <div class="jero-client-trigger-actions">
                                                <button type="button" class="jero-client-clear-btn" id="jero_trigger_clear" title="Clear selection" style="display:none;" aria-label="Clear organization selection">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                </button>
                                                <span class="jero-client-chevron" id="jero_trigger_chevron">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-9"/></svg>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Floating Popover Dropdown (Boundary Clamped for 15.6" Displays: 1366x768 & 1920x1080) -->
                                        <div class="jero-client-popover" id="jero_client_popover" style="display:none;" role="listbox">
                                            <!-- Sticky Search Bar -->
                                            <div class="jero-client-search-box">
                                                <svg class="jero-client-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                                <input type="text" class="jero-client-search-input" id="jero_client_search_input" placeholder="Search company, organization, industry, code..." autocomplete="off">
                                                <button type="button" class="jero-client-search-clear" id="jero_search_clear" style="display:none;" aria-label="Clear search">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            <!-- Category Segment Tabs -->
                                            <div class="jero-client-tabs">
                                                <button type="button" class="jero-tab-btn active" data-filter="all">All Companies <span class="tab-count" id="jero_count_all">0</span></button>
                                                <button type="button" class="jero-tab-btn" data-filter="internal">My Organization <span class="tab-count" id="jero_count_internal">0</span></button>
                                                <button type="button" class="jero-tab-btn" data-filter="client">Clients <span class="tab-count" id="jero_count_clients">0</span></button>
                                            </div>

                                            <!-- Scrollable Items Container (264px viewport with DOM Virtualization) -->
                                            <div class="jero-client-list-container" id="jero_client_list_container">
                                                <div class="jero-virtual-phantom" id="jero_virtual_phantom" style="height: 0px;">
                                                    <div class="jero-virtual-window" id="jero_virtual_window" style="transform: translateY(0px);">
                                                        <div class="jero-client-loading" style="padding: 16px; text-align: center; color: #7E8299; font-size: 12px;">
                                                            Loading organizations...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Sticky Footer -->
                                            <div class="jero-client-footer">
                                                <a href="{{ route('service_company.clients.create') }}" target="_blank" class="jero-btn-add-client" id="jero_btn_quick_add_client">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5v14"/></svg>
                                                    Add New Client
                                                </a>
                                                <span class="jero-footer-hint">↑↓ Navigate · ↵ Select · Esc Close</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="form-hint">*Please select the hiring organization with openings</span>
                                    <?php } ?>
                                </div>
                                <div class="form-group-cus" style="position:relative;">
                                    <label class="form-label required">Select the Address</label>
                                    <select class="form-select" id="address_id" name="address_id">
                                        <option value="" selected="selected">Select Address</option>
                                    </select>
                                    <div class="address_show" style="position:absolute;right:8px;top:30px;display:none;">
                                        <a href="javascript:;" class="chip-add add_address">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M5 12h14"/>
                                                <path d="M12 5v14"/>
                                            </svg>Add Address
                                        </a>
                                    </div>
                                    <span class="form-hint">Please select the address of your organization/client</span>
                                </div>
                                <div class="form-group-cus jero-title-form-group" style="position:relative;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                        <label class="form-label required mb-0">Job Title / Designation</label>
                                        <span class="jero-title-status-chip" id="jero_title_status_chip" style="display:none;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span id="jero_title_status_text">Template active</span>
                                        </span>
                                    </div>

                                    <!-- Combobox Container -->
                                    <div class="jero-title-select-wrap" id="jero_title_select_wrap">
                                        <div class="jero-title-input-box" id="jero_title_input_box">
                                            <svg class="jero-title-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                            </svg>
                                            <input type="text" 
                                                   class="form-input jero-title-input" 
                                                   id="title" 
                                                   name="title" 
                                                   value="<?php echo @$job->title; ?>" 
                                                   placeholder="Search or enter job title" 
                                                   style="padding-left: 40px !important; padding-right: 56px !important;"
                                                   autocomplete="off" 
                                                   role="combobox" 
                                                   aria-autocomplete="list" 
                                                   aria-expanded="false" 
                                                   aria-controls="jero_title_dropdown" 
                                                   aria-haspopup="listbox">
                                            <button type="button" class="jero-title-clear-btn" id="jero_title_clear_btn" style="display:none;" aria-label="Clear job title" title="Clear">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            </button>
                                            <div class="jero-title-chevron" id="jero_title_chevron">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                            </div>
                                        </div>

                                        <!-- Decision-Centric Dropdown Menu -->
                                        <div class="jero-title-dropdown" id="jero_title_dropdown" style="display:none;" role="listbox" aria-label="Job Titles">
                                            <!-- Category Segment Tabs (All | My Organization | Standard Library) -->
                                            <div class="jero-title-tabs">
                                                <button type="button" class="jero-title-tab-btn active" data-filter="all">All <span class="tab-count" id="jero_title_count_all">0</span></button>
                                                <button type="button" class="jero-title-tab-btn" data-filter="company">My Organization <span class="tab-count" id="jero_title_count_company">0</span></button>
                                                <button type="button" class="jero-title-tab-btn" data-filter="standard">Standard Library <span class="tab-count" id="jero_title_count_standard">0</span></button>
                                            </div>

                                            <!-- Scrollable Options List Container -->
                                            <div class="jero-title-list-container" id="jero_title_list_container">
                                                <!-- Dynamically populated by JeroTitleIntelligenceEngine -->
                                            </div>

                                            <!-- Custom Title Action Pill -->
                                            <div class="jero-title-custom-action" id="jero_title_custom_action" style="display:none;">
                                                <div class="jero-title-custom-pill" id="jero_title_custom_pill">
                                                    <span class="custom-icon">+</span>
                                                    <div class="custom-text-wrap">
                                                        <div class="custom-title-label">Use "<span id="jero_custom_title_text"></span>" as custom title</div>
                                                        <div class="custom-title-sub">Enter job description and skills manually</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Sticky Footer Bar -->
                                            <div class="jero-title-footer">
                                                <span class="jero-title-sync-status" id="jero_title_sync_status"></span>
                                                <span class="jero-title-footer-hint">↑↓ Navigate · ↵ Select · Esc Close</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="form-hint">*Select from the library for automated JD & skills, or enter a custom designation.</span>

                                    <!-- Safe Auto-Fill Transactional Undo Snackbar -->
                                    <div class="jero-autofill-undo-toast" id="jero_autofill_undo_toast" style="display:none;">
                                        <span class="undo-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
                                        <span class="undo-msg">Template applied: Description • Experience • Skills</span>
                                        <button type="button" class="undo-btn" id="jero_autofill_undo_btn">Undo</button>
                                    </div>

                                    <!-- Non-blocking Conflict Confirmation Dialog -->
                                    <div class="jero-autofill-confirm-dialog" id="jero_autofill_confirm_dialog" style="display:none;">
                                        <div class="confirm-content">
                                            <div class="confirm-title">Job description already contains custom content.</div>
                                            <div class="confirm-sub">Do you want to replace it with the standard template or keep your custom writing?</div>
                                            <div class="confirm-actions">
                                                <button type="button" class="confirm-btn confirm-keep" id="jero_confirm_keep_btn">Keep My Writing</button>
                                                <button type="button" class="confirm-btn confirm-apply" id="jero_confirm_apply_btn">Apply Template</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-cus jero-dept-form-group">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label required mb-0">Job Department</label>
                                        <div id="jero_dept_status_wrap" style="display:none;">
                                            <span class="jero-dept-status-chip" id="jero_dept_status_chip">Standard ✓</span>
                                        </div>
                                    </div>

                                    <!-- Hidden Synchronized Native Select (Preserves 100% Backward Compatibility with Validation & Submit) -->
                                    <select id="category_id" name="category_id" style="display:none;" tabindex="-1" aria-hidden="true">
                                        <option value="">Select Department</option>
                                        <?php
                                        $selectedDeptName = '';
                                        foreach ($categories as $category) {
                                            $sel = '';
                                            if (@$job->category_id == @$category->id) {
                                                $sel = 'selected="selected"';
                                                $selectedDeptName = $category->name;
                                            }
                                            echo '<option value="' . @$category->id . '" ' . $sel . '>' . ucfirst($category->name) . '</option>';
                                        }
                                        ?>
                                    </select>

                                    <!-- God-Level Enterprise Combobox Component (Client-First / 10K Concurrency) -->
                                    <div class="jero-dept-select-wrap" id="jero_dept_select_wrap">
                                        <div class="jero-dept-input-box">
                                            <span class="jero-dept-search-icon">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                            </span>
                                            <input type="text"
                                                   class="jero-dept-input"
                                                   id="category_id_display"
                                                   placeholder="Search 60+ departments or type to create..."
                                                   autocomplete="off"
                                                   spellcheck="false"
                                                   role="combobox"
                                                   aria-expanded="false"
                                                   aria-haspopup="listbox"
                                                   aria-autocomplete="list"
                                                   value="<?php echo htmlspecialchars($selectedDeptName); ?>">
                                            <kbd class="jero-dept-kbd" id="jero_dept_kbd" title="Press / to search">/</kbd>
                                            <button type="button" class="jero-dept-clear-btn" id="jero_dept_clear_btn" title="Clear selection" style="<?php echo $selectedDeptName ? 'display:flex;' : 'display:none;'; ?>">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            </button>
                                            <div class="jero-dept-chevron" id="jero_dept_chevron" title="Toggle department list" role="button" tabindex="-1">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                            </div>
                                        </div>

                                        <!-- Decision-Centric Hierarchical Dropdown Menu -->
                                        <div class="jero-dept-dropdown" id="jero_dept_dropdown" style="display:none;" role="listbox" aria-label="Job Departments">
                                            <!-- Segment Filter Tabs (All | My Organization | Standard Library) -->
                                            <div class="jero-dept-tabs">
                                                <button type="button" class="jero-dept-tab-btn active" data-filter="all">All <span class="tab-count" id="jero_dept_count_all">0</span></button>
                                                <button type="button" class="jero-dept-tab-btn" data-filter="company">My Organization <span class="tab-count" id="jero_dept_count_company">0</span></button>
                                                <button type="button" class="jero-dept-tab-btn" data-filter="standard">Standard Library <span class="tab-count" id="jero_dept_count_standard">0</span></button>
                                            </div>

                                            <!-- Recently Used Bar (1-Click Quick Picks) -->
                                            <div class="jero-dept-recent-bar" id="jero_dept_recent_bar" style="display:none;"></div>

                                            <!-- Scrollable Options List Container -->
                                            <div class="jero-dept-list-container" id="jero_dept_list_container">
                                                <!-- Dynamically populated by JeroDeptIntelligenceEngine -->
                                            </div>

                                            <!-- Sticky Footer Bar -->
                                            <div class="jero-dept-footer">
                                                <div class="jero-dept-sync-status" id="jero_dept_sync_status">
                                                    <span class="jero-dept-sync-dot"></span>
                                                    <span>Live Synced</span>
                                                </div>
                                                <span class="jero-dept-footer-hint">↑↓ Navigate · ↵ Select · Esc Close</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="form-hint">*Please select or search the department for this job.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card ij-employment-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>Employment Details
                            </div>
                            <div class="form-grid form-grid-3 ij-employment-grid">
                                <div class="form-group-cus ij-exp-group">
                                    <label class="form-label required">Experience Range</label>
                                    <div class="range-group ij-range-group">
                                        <div class="range-field">
                                            <select class="form-select ij-exp-select" name="min_exp" id="min_exp">
                                                <option value="" disabled <?php echo (@$job->min_exp === null) ? 'selected' : ''; ?>>Select</option>
                                                <option value="0" <?php if (@$job->min_exp === 0 || @$job->min_exp === '0') echo 'selected'; ?>>Freshers</option>
                                                <?php
                                                    $yearText = 'Year';
                                                    for ($i = 1; $i <= 30; $i++) {
                                                        if ($i > 9) { $yearText = 'Years'; }
                                                        $sel = (@$job->min_exp == $i) ? 'selected="selected"' : '';
                                                        echo '<option value="' . $i . '" ' . $sel . '>' . $i . ' ' . $yearText . '</option>';
                                                    }
                                                ?>
                                            </select>
                                            <span class="form-hint">Min</span>
                                        </div>
                                        <span class="range-separator">-</span>
                                        <div class="range-field">
                                            <select class="form-select ij-exp-select" name="max_exp" id="max_exp">
                                                <option value="" disabled <?php echo (@$job->max_exp === null) ? 'selected' : ''; ?>>Select</option>
                                                <option value="0" <?php if (@$job->max_exp === 0 || @$job->max_exp === '0') echo 'selected'; ?>>Freshers</option>
                                                <?php
                                                    $yearText = 'Year';
                                                    for ($i = 1; $i <= 30; $i++) {
                                                        if ($i > 9) { $yearText = 'Years'; }
                                                        $sel = (@$job->max_exp == $i) ? 'selected="selected"' : '';
                                                        echo '<option value="' . $i . '" ' . $sel . '>' . $i . ' ' . $yearText . '</option>';
                                                    }
                                                ?>
                                            </select>
                                            <span class="form-hint">Max</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-cus ij-positions-group">
                                    <label class="form-label required">No. of Positions</label>
                                    <select class="form-select" id="total_positions" name="total_positions">
                                        <?php
                                        $selPos = (int) (@$job->total_positions ?: 1);
                                        for ($p = 1; $p <= 500; $p++) {
                                            echo '<option value="' . $p . '"' . ($selPos == $p ? ' selected="selected"' : '') . '>' . $p . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="form-hint">Select openings to fill (1–500).</span>
                                </div>
                                {{--
                                    Gender Diversity Custom Selector
                                    Purpose: Provides an enterprise-grade, accessible custom dropdown for candidate gender diversity preferences.
                                    Saves selection to the `required_columns` job attribute (synced with hidden input for seamless form submission).
                                    Options: No Preference, Male Only, Female Only, Balanced / Equal Opportunity, Other / Prefer Not to Say.
                                --}}
                                @php
                                    $rawGender = @$job->required_columns;
                                    if (is_array($rawGender)) {
                                        $rawGender = $rawGender['gender'] ?? ($rawGender[0] ?? '');
                                    }
                                    $rawGender = (string) $rawGender;
                                    $initialGenderVal = '';
                                    if ($rawGender === 'Male' || $rawGender === 'Male Only') {
                                        $initialGenderVal = 'Male Only';
                                    } elseif ($rawGender === 'Female' || $rawGender === 'Female Only') {
                                        $initialGenderVal = 'Female Only';
                                    } elseif ($rawGender === 'Balanced / Equal Opportunity' || $rawGender === 'Balanced') {
                                        $initialGenderVal = 'Balanced / Equal Opportunity';
                                    } elseif ($rawGender === 'Other / Prefer Not to Say') {
                                        $initialGenderVal = 'Other / Prefer Not to Say';
                                    } elseif ($rawGender === 'No Preference') {
                                        $initialGenderVal = 'No Preference';
                                    } elseif ($rawGender === 'Rather Not Say') {
                                        $initialGenderVal = 'Other / Prefer Not to Say';
                                    }
                                @endphp

                                <div class="form-group-cus ij-gender-group">
                                    <label class="form-label ij-gender-label" for="genderDiversityTrigger">
                                        <span>Gender Diversity</span>
                                        <span class="ij-gender-info-icon" 
                                              data-bs-toggle="tooltip" 
                                              data-bs-placement="top" 
                                              title="Specify gender diversity preferences to encourage inclusive hiring.">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M12 16v-4"></path>
                                                <path d="M12 8h.01"></path>
                                            </svg>
                                        </span>
                                    </label>

                                    <div class="ij-gender-dropdown-wrapper" id="genderDropdownWrapper">
                                        <input type="hidden" name="required_columns" id="required_columns" value="{{ $initialGenderVal }}">

                                        <button type="button" 
                                                class="ij-gender-trigger {{ $initialGenderVal ? 'has-selected' : '' }}" 
                                                id="genderDiversityTrigger" 
                                                aria-haspopup="listbox" 
                                                aria-expanded="false" 
                                                aria-controls="genderDiversityMenu"
                                                aria-label="Gender Diversity Preference">
                                            <div class="ij-gender-trigger-left">
                                                <span class="ij-gender-trigger-badge {{ $initialGenderVal ? ('ij-badge-' . ($initialGenderVal === 'No Preference' ? 'neutral' : ($initialGenderVal === 'Male Only' ? 'male' : ($initialGenderVal === 'Female Only' ? 'female' : ($initialGenderVal === 'Balanced / Equal Opportunity' ? 'balanced' : 'other'))))) : 'ij-badge-default' }}" id="genderTriggerBadge">
                                                    @if($initialGenderVal === 'Male Only')
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                                            <circle cx="12" cy="4.5" r="2.5"/>
                                                            <path d="M8.5 8.5C7.7 8.5 7 9.2 7 10v4.2c0 .5.4.8.9.8h1.1v6c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-5h0v5c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-6h1.1c.5 0 .9-.3.9-.8V10c0-.8-.7-1.5-1.5-1.5h-7z"/>
                                                        </svg>
                                                    @elseif($initialGenderVal === 'Female Only')
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                                            <circle cx="12" cy="4.5" r="2.5"/>
                                                            <path d="M10 8.5c-.8 0-1.5.7-1.5 1.5l-1 5.2c-.1.5.3.8.8.8h1.7v5c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-5h1.7c.5 0 .9-.3.8-.8l-1-5.2c0-.8-.7-1.5-1.5-1.5h-4z"/>
                                                        </svg>
                                                    @elseif($initialGenderVal === 'Balanced / Equal Opportunity')
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                            <circle cx="9" cy="7" r="4"/>
                                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                        </svg>
                                                    @elseif($initialGenderVal === 'Other / Prefer Not to Say')
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="10" cy="8" r="4"/>
                                                            <path d="M4 20v-2a5 5 0 0 1 10 0v2"/>
                                                            <path d="M18 11v6"/>
                                                            <path d="M21 14h-6"/>
                                                        </svg>
                                                    @elseif($initialGenderVal === 'No Preference')
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                            <circle cx="9" cy="7" r="4"/>
                                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                        </svg>
                                                    @else
                                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                            <circle cx="9" cy="7" r="4"/>
                                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                                <span class="ij-gender-trigger-text {{ $initialGenderVal ? 'has-value' : '' }}" id="genderTriggerText">
                                                    {{ $initialGenderVal ?: 'Select preference' }}
                                                </span>
                                            </div>
                                            <svg class="ij-gender-chevron" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m6 9 6 6 6-9"/>
                                            </svg>
                                        </button>

                                        <div class="ij-gender-menu" id="genderDiversityMenu" role="listbox" aria-label="Gender Diversity Options" style="display: none;">
                                            {{-- Option 1: No Preference --}}
                                            <div class="ij-gender-option {{ $initialGenderVal === 'No Preference' ? 'is-selected' : '' }}" 
                                                 role="option" 
                                                 tabindex="0"
                                                 data-value="No Preference"
                                                 aria-selected="{{ $initialGenderVal === 'No Preference' ? 'true' : 'false' }}">
                                                <div class="ij-gender-badge ij-badge-neutral">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                        <circle cx="9" cy="7" r="4"/>
                                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                    </svg>
                                                </div>
                                                <div class="ij-gender-option-info">
                                                    <span class="ij-gender-option-title">No Preference</span>
                                                    <span class="ij-gender-option-desc">Open to all candidates</span>
                                                </div>
                                                <span class="ij-gender-check" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </div>

                                            {{-- Option 2: Male Only --}}
                                            <div class="ij-gender-option {{ $initialGenderVal === 'Male Only' ? 'is-selected' : '' }}" 
                                                 role="option" 
                                                 tabindex="0"
                                                 data-value="Male Only"
                                                 aria-selected="{{ $initialGenderVal === 'Male Only' ? 'true' : 'false' }}">
                                                <div class="ij-gender-badge ij-badge-male">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                        <circle cx="12" cy="4.5" r="2.5"/>
                                                        <path d="M8.5 8.5C7.7 8.5 7 9.2 7 10v4.2c0 .5.4.8.9.8h1.1v6c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-5h0v5c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-6h1.1c.5 0 .9-.3.9-.8V10c0-.8-.7-1.5-1.5-1.5h-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="ij-gender-option-info">
                                                    <span class="ij-gender-option-title">Male Only</span>
                                                    <span class="ij-gender-option-desc">Prefer male candidates only</span>
                                                </div>
                                                <span class="ij-gender-check" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </div>

                                            {{-- Option 3: Female Only --}}
                                            <div class="ij-gender-option {{ $initialGenderVal === 'Female Only' ? 'is-selected' : '' }}" 
                                                 role="option" 
                                                 tabindex="0"
                                                 data-value="Female Only"
                                                 aria-selected="{{ $initialGenderVal === 'Female Only' ? 'true' : 'false' }}">
                                                <div class="ij-gender-badge ij-badge-female">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                        <circle cx="12" cy="4.5" r="2.5"/>
                                                        <path d="M10 8.5c-.8 0-1.5.7-1.5 1.5l-1 5.2c-.1.5.3.8.8.8h1.7v5c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-5h1.7c.5 0 .9-.3.8-.8l-1-5.2c0-.8-.7-1.5-1.5-1.5h-4z"/>
                                                    </svg>
                                                </div>
                                                <div class="ij-gender-option-info">
                                                    <span class="ij-gender-option-title">Female Only</span>
                                                    <span class="ij-gender-option-desc">Prefer female candidates only</span>
                                                </div>
                                                <span class="ij-gender-check" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </div>

                                            {{-- Option 4: Balanced / Equal Opportunity --}}
                                            <div class="ij-gender-option {{ $initialGenderVal === 'Balanced / Equal Opportunity' ? 'is-selected' : '' }}" 
                                                 role="option" 
                                                 tabindex="0"
                                                 data-value="Balanced / Equal Opportunity"
                                                 aria-selected="{{ $initialGenderVal === 'Balanced / Equal Opportunity' ? 'true' : 'false' }}">
                                                <div class="ij-gender-badge ij-badge-balanced">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                        <circle cx="9" cy="7" r="4"/>
                                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                    </svg>
                                                </div>
                                                <div class="ij-gender-option-info">
                                                    <span class="ij-gender-option-title">Balanced / Equal Opportunity</span>
                                                    <span class="ij-gender-option-desc">Encourage gender diversity (M/F/Other)</span>
                                                </div>
                                                <span class="ij-gender-check" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </div>

                                            {{-- Option 5: Other / Prefer Not to Say --}}
                                            <div class="ij-gender-option {{ $initialGenderVal === 'Other / Prefer Not to Say' ? 'is-selected' : '' }}" 
                                                 role="option" 
                                                 tabindex="0"
                                                 data-value="Other / Prefer Not to Say"
                                                 aria-selected="{{ $initialGenderVal === 'Other / Prefer Not to Say' ? 'true' : 'false' }}">
                                                <div class="ij-gender-badge ij-badge-other">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="10" cy="8" r="4"/>
                                                        <path d="M4 20v-2a5 5 0 0 1 10 0v2"/>
                                                        <path d="M18 11v6"/>
                                                        <path d="M21 14h-6"/>
                                                    </svg>
                                                </div>
                                                <div class="ij-gender-option-info">
                                                    <span class="ij-gender-option-title">Other / Prefer Not to Say</span>
                                                    <span class="ij-gender-option-desc">Open to all, no specific preference</span>
                                                </div>
                                                <span class="ij-gender-check" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="form-hint ij-gender-hint">Help us promote an inclusive workplace.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card ij-location-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>Job Location or Work Location
                            </div>
                            <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">Select up to 15 cities where this job is based.</p>

                            <!-- L1 Quick-Select Popular Hubs Bar -->
                            <div class="ij-popular-hubs-wrap">
                                <span class="ij-popular-label">Popular Hubs:</span>
                                <div class="ij-popular-pills" id="popularHubsPills"></div>
                            </div>

                            <div class="form-group-cus full-width">
                                <div class="input-wrapper ij-location-input-wrap">
                                    <div class="input-with-icon">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                        <input type="text" class="form-input" id="citySearch" placeholder="Search for cities (e.g. Bangalore, Mumbai, London, New York)..." autocomplete="off" spellcheck="false">
                                    </div>
                                    <span class="ij-search-spinner" id="citySearchSpinner" style="display:none;" title="Searching Elasticsearch..."></span>
                                    <button type="button" class="ij-search-clear" id="citySearchClear" style="display:none;" onclick="clearCitySearchInput()" title="Clear input">&times;</button>
                                    <div class="search-dropdown ij-city-dropdown" id="cityDropdown"></div>
                                </div>
                            </div>
                            <div class="selected-header">
                                <span class="selected-label">Selected Locations (<span id="selectedCount">0</span>/15)</span>
                                <button type="button" class="clear-all" onclick="clearAllCities()">Clear All</button>
                            </div>
                            <div class="chips-container ij-selected-chips" id="selectedCities"></div>
                            <input type="hidden" id="kt_tagify_work_location" name="kt_tagify_work_location" value="">
                        </div>
                    </div>
                    {{-- Position Type card restored as dedicated section immediately after Job Location --}}
                    <div class="card ij-position-type-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>Position Type <span class="text-danger">*</span>
                            </div>
                            <?php
                                $currEmp = isset($job->employment_type) && $job->employment_type !== '' ? (string)$job->employment_type : '0';
                            ?>
                            <div class="ij-choices ij-choices-4" role="group" aria-label="Position Type">
                                <button type="button" class="ij-choice ij-choice-with-icon" data-value="0" aria-pressed="<?php echo ((string)$currEmp === '0') ? 'true' : 'false'; ?>">
                                    <span class="ij-choice-icon-wrap ij-icon-yellow">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                        </svg>
                                    </span>
                                    <span class="ij-choice-text">
                                        <strong>Full-time</strong>
                                        <small>Permanent employment</small>
                                    </span>
                                </button>
                                <button type="button" class="ij-choice ij-choice-with-icon" data-value="1" aria-pressed="<?php echo ((string)$currEmp === '1') ? 'true' : 'false'; ?>">
                                    <span class="ij-choice-icon-wrap ij-icon-blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </span>
                                    <span class="ij-choice-text">
                                        <strong>Contract</strong>
                                        <small>Project-based hiring</small>
                                    </span>
                                </button>
                                <button type="button" class="ij-choice ij-choice-with-icon" data-value="2" aria-pressed="<?php echo ((string)$currEmp === '2') ? 'true' : 'false'; ?>">
                                    <span class="ij-choice-icon-wrap ij-icon-slate">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <span class="ij-choice-text">
                                        <strong>Freelance</strong>
                                        <small>Independent contractor</small>
                                    </span>
                                </button>
                                <button type="button" class="ij-choice ij-choice-with-icon" data-value="3" aria-pressed="<?php echo ((string)$currEmp === '3') ? 'true' : 'false'; ?>">
                                    <span class="ij-choice-icon-wrap ij-icon-purple">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </span>
                                    <span class="ij-choice-text">
                                        <strong>Part Time</strong>
                                        <small>Flexible working hours</small>
                                    </span>
                                </button>
                            </div>
                            <div class="ij-source-control">
                                <select class="form-select" name="employment_type" id="employment_type" tabindex="-1">
                                    <option value="">Select Position Type</option>
                                    <?php
                                    foreach ($employmentType as $key => $data_value) {
                                        $sel = ((string)$currEmp === (string)$key) ? 'selected="selected"' : '';
                                        echo '<option value="' . $key . '" ' . $sel . '>' . $data_value . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <span class="form-hint" style="margin-top:10px;display:block;">Select the type of employment for this opening.</span>
                        </div>
                    </div>
                    <div class="card ij-work-mode-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                                    <path d="M2 12h20"/>
                                </svg>Work Mode
                            </div>
                            <div class="ij-choices" role="group" aria-label="Work Mode">
                                <button type="button" class="ij-choice" data-value="yes" aria-pressed="<?php echo (@$job->work_type == 'yes') ? 'true' : 'false'; ?>">
                                    <strong>Remote</strong>
                                    <small>Work from anywhere</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="other" aria-pressed="<?php echo (@$job->work_type == 'other') ? 'true' : 'false'; ?>">
                                    <strong>Hybrid</strong>
                                    <small>Office + Remote</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="no" aria-pressed="<?php echo (@$job->work_type == 'no' || empty(@$job->work_type)) ? 'true' : 'false'; ?>">
                                    <strong>Work From Office</strong>
                                    <small>Based at office location</small>
                                </button>
                            </div>
                            <div class="ij-source-control">
                                <select name="work_from_home" id="work_from_home" class="form-select" tabindex="-1">
                                    <option value="yes" <?php if (@$job->work_type == 'yes') echo 'selected'; ?>>Remote</option>
                                    <option value="other" <?php if (@$job->work_type == 'other') echo 'selected'; ?>>Hybrid</option>
                                    <option value="no" <?php if (@$job->work_type == 'no') echo 'selected'; ?>>Work From Office</option>
                                </select>
                            </div>
                            <span class="form-hint" style="margin-top:10px;display:block;">Choose whether this position is Remote, Hybrid, or Work From Office.</span>
                        </div>
                    </div>
                    <div class="card ij-work-shift-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="4"/>
                                    <path d="M12 2v2"/><path d="M12 20v2"/>
                                    <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                                    <path d="M2 12h2"/><path d="M20 12h2"/>
                                    <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                                </svg>Work Shift
                            </div>
                            <div class="ij-choices" role="group" aria-label="Work Shift">
                                <button type="button" class="ij-choice" data-value="Day Shift" aria-pressed="<?php echo (@$job->work_shifts == 'Day Shift' || empty(@$job->work_shifts)) ? 'true' : 'false'; ?>">
                                    <strong>Day Shift</strong>
                                    <small>Regular daytime working hours</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="Night Shift" aria-pressed="<?php echo (@$job->work_shifts == 'Night Shift') ? 'true' : 'false'; ?>">
                                    <strong>Night Shift</strong>
                                    <small>Regular night working hours</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="Rotational shift" aria-pressed="<?php echo (@$job->work_shifts == 'Rotational shift') ? 'true' : 'false'; ?>">
                                    <strong>Rotational shift</strong>
                                    <small>Rotating work schedule</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="UK Timezone" aria-pressed="<?php echo (@$job->work_shifts == 'UK Timezone') ? 'true' : 'false'; ?>">
                                    <strong>UK Timezone</strong>
                                    <small>Based on UK working hours</small>
                                </button>
                                <button type="button" class="ij-choice" data-value="US Timezone" aria-pressed="<?php echo (@$job->work_shifts == 'US Timezone') ? 'true' : 'false'; ?>">
                                    <strong>US Timezone</strong>
                                    <small>Based on US working hours</small>
                                </button>
                            </div>
                            <div class="ij-source-control">
                                <select class="form-select" name="work_shifts" id="work_shifts" tabindex="-1">
                                    <option value="Day Shift" @if (@$job->work_shifts == 'Day Shift') selected @endif>Day Shift</option>
                                    <option value="Night Shift" @if (@$job->work_shifts == 'Night Shift') selected @endif>Night Shift</option>
                                    <option value="Rotational shift" @if (@$job->work_shifts == 'Rotational shift') selected @endif>Rotational shift</option>
                                    <option value="UK Timezone" @if (@$job->work_shifts == 'UK Timezone') selected @endif>UK Timezone</option>
                                    <option value="US Timezone" @if (@$job->work_shifts == 'US Timezone') selected @endif>US Timezone</option>
                                </select>
                            </div>
                            <span class="form-hint" style="margin-top:10px;display:block;">Specify the operational working shift or timezone required.</span>
                        </div>
                    </div>
                    <div class="card ij-compensation-card">
                        <div class="card-body">
                            <?php
                            $initialCurrCode = $currency_code;
                            $initialCurrSymbol = '₹';
                            foreach ($currency as $key => $val) {
                                $isSel = false;
                                if (@$job->min_salary_currency === null || @$job->min_salary_currency === '') {
                                    if ($currency_code == $val) { $isSel = true; }
                                } else {
                                    if (@$job->min_salary_currency == $key) { $isSel = true; }
                                }
                                if ($isSel) {
                                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                    $initialCurrCode = $val;
                                    $initialCurrSymbol = $cMeta['symbol'] ?? $val;
                                    break;
                                }
                            }
                            ?>
                            <div class="ij-comp-header">
                                <div class="section-title">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>
                                    </svg>Compensation
                                </div>
                                <div class="ij-comp-currency-wrap">
                                    <label class="ij-comp-currency-label" for="headerCurrTrigger">Currency:</label>
                                    <button type="button" class="ij-header-curr-btn" id="headerCurrTrigger" aria-haspopup="dialog" aria-expanded="false" title="Click to change currency">
                                        <span class="ij-header-curr-symbol" id="headerCurrSymbol"><?php echo e($initialCurrSymbol); ?></span>
                                        <span class="ij-header-curr-code" id="headerCurrCode"><?php echo e($initialCurrCode); ?></span>
                                        <svg class="ij-header-curr-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                    </button>
                                </div>
                            </div>
                            {{-- Preserved backend select elements for full backward compatibility --}}
                            <select name="min_salary_currency" id="min_salary_currency" class="d-none">
                                <?php
                                foreach ($currency as $key => $val) {
                                    $sel = '';
                                    if (@$job->min_salary_currency === null || @$job->min_salary_currency === '') { if ($currency_code == $val) { $sel = 'selected="selected"'; } }
                                    else { if (@$job->min_salary_currency == $key) { $sel = 'selected="selected"'; } }
                                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                    $symbol = $cMeta['symbol'] ?? '';
                                    $shortText = !empty($symbol) ? $cMeta['code'] . ' (' . $symbol . ')' : $cMeta['code'];
                                    echo '<option value="' . $key . '" data-code="' . e($val) . '" data-symbol="' . e($symbol) . '" data-short="' . e($shortText) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';
                                }
                                ?>
                            </select>
                            <select name="max_salary_currency" id="max_salary_currency" class="d-none">
                                <?php
                                foreach ($currency as $key => $val) {
                                    $sel = '';
                                    if (@$job->max_salary_currency === null || @$job->max_salary_currency === '') { if ($currency_code == $val) { $sel = 'selected="selected"'; } }
                                    else { if (@$job->max_salary_currency == $key) { $sel = 'selected="selected"'; } }
                                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                    $symbol = $cMeta['symbol'] ?? '';
                                    $shortText = !empty($symbol) ? $cMeta['code'] . ' (' . $symbol . ')' : $cMeta['code'];
                                    echo '<option value="' . $key . '" data-code="' . e($val) . '" data-symbol="' . e($symbol) . '" data-short="' . e($shortText) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';
                                }
                                ?>
                            </select>

                            <div class="form-grid form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label required">Minimum Salary</label>
                                    <div class="salary-input-wrapper ij-salary-input-wrapper" id="minSalaryWrap">
                                        <button type="button" class="ij-currency-btn" id="minCurrTrigger" data-target="minSalaryWrap" title="Click to change currency" aria-haspopup="dialog" aria-expanded="false">
                                            <span class="ij-curr-pill-symbol"><?php echo e($initialCurrSymbol); ?></span>
                                            <span class="ij-curr-pill-code"><?php echo e($initialCurrCode); ?></span>
                                            <svg class="ij-curr-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </button>
                                        <input type="number" class="form-input" name="minimum_salary" id="minimum_salary" value="<?php echo @$job->min_salary; ?>" placeholder="Enter min salary" min="0">
                                    </div>
                                    <span class="form-hint minimum_salary_word">*Minimum salary offered for this position.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Maximum Salary</label>
                                    <div class="salary-input-wrapper ij-salary-input-wrapper" id="maxSalaryWrap">
                                        <button type="button" class="ij-currency-btn" id="maxCurrTrigger" data-target="maxSalaryWrap" title="Click to change currency" aria-haspopup="dialog" aria-expanded="false">
                                            <span class="ij-curr-pill-symbol"><?php echo e($initialCurrSymbol); ?></span>
                                            <span class="ij-curr-pill-code"><?php echo e($initialCurrCode); ?></span>
                                            <svg class="ij-curr-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </button>
                                        <input type="number" class="form-input" name="maximum_salary" id="maximum_salary" value="<?php echo @$job->max_salary; ?>" placeholder="Enter max salary" min="0">
                                    </div>
                                    <span class="form-hint maximum_salary_word">*Maximum salary offered for this position.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Salary Type</label>
                                    <select class="form-select" name="min_sal_mode" id="min_sal_mode">
                                        <?php
                                        foreach ($salaryMode as $key => $val) {
                                            $sel = (@$job->min_sal_mode == $key) ? 'selected="selected"' : '';
                                            echo '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Modern Enterprise Currency Dropdown Popover -->
                            <div class="ij-curr-popover" id="ijCurrencyPopover" style="display:none;" tabindex="-1">
                                <div class="ij-curr-pop-search-wrap">
                                    <svg class="ij-curr-pop-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                    </svg>
                                    <input type="text" class="ij-curr-pop-search" id="ijCurrPopSearch" placeholder="Search currency (code, name, symbol)..." autocomplete="off" spellcheck="false">
                                </div>
                                <div class="ij-curr-pop-quick">
                                    <span class="ij-curr-pop-quick-label">Popular:</span>
                                    <div class="ij-curr-pop-quick-chips">
                                        <button type="button" class="ij-curr-pop-chip" data-code="INR">INR (₹)</button>
                                        <button type="button" class="ij-curr-pop-chip" data-code="USD">USD ($)</button>
                                        <button type="button" class="ij-curr-pop-chip" data-code="EUR">EUR (€)</button>
                                        <button type="button" class="ij-curr-pop-chip" data-code="GBP">GBP (£)</button>
                                        <button type="button" class="ij-curr-pop-chip" data-code="AED">AED (د.إ)</button>
                                        <button type="button" class="ij-curr-pop-chip" data-code="SGD">SGD (S$)</button>
                                    </div>
                                </div>
                                <div class="ij-curr-pop-list" id="ijCurrPopList">
                                    <?php
                                    foreach ($currency as $key => $val) {
                                        $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                        $symbol = $cMeta['symbol'] ?? '';
                                        $name = $cMeta['name'] ?? $val;
                                        $search = strtolower($cMeta['keywords'] . ' ' . $val . ' ' . $name . ' ' . $symbol);
                                        $isSel = ($val === $initialCurrCode);
                                    ?>
                                        <div class="ij-curr-pop-item <?php echo $isSel ? 'is-selected' : ''; ?>" data-val="<?php echo e($key); ?>" data-code="<?php echo e($val); ?>" data-symbol="<?php echo e($symbol); ?>" data-name="<?php echo e($name); ?>" data-search="<?php echo e($search); ?>">
                                            <div class="ij-curr-item-left">
                                                <span class="ij-curr-badge"><strong class="ij-curr-badge-code"><?php echo e($val); ?></strong> <span class="ij-curr-badge-symbol"><?php echo e($symbol); ?></span></span>
                                                <span class="ij-curr-name"><?php echo e($name); ?></span>
                                            </div>
                                            <svg class="ij-curr-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <span class="form-hint" style="margin-top:10px;display:block;">Enter the expected CTC range for this position.</span>
                        </div>
                    </div>
                    <div class="card ij-qual-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                </svg>Educational Qualification
                            </div>
                            <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">Select up to 5 eligible educational qualifications or degrees for this job posting.</p>

                            <!-- L1 Quick-Select Enterprise Standards Bar -->
                            <div class="ij-popular-hubs-wrap ij-quick-qual-wrap">
                                <span class="ij-popular-label">Quick Select:</span>
                                <div class="ij-popular-pills" id="quickQualPills"></div>
                            </div>

                            <div class="form-group-cus full-width" style="margin-top:10px;">
                                <div class="input-wrapper ij-qual-input-wrap">
                                    <div class="input-with-icon">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                        <input type="text" class="form-input" id="qualSearch" placeholder="Search or select qualifications (e.g. Any Graduation, B.Tech, MBA, MCA)..." autocomplete="off" spellcheck="false">
                                    </div>
                                    <button type="button" class="ij-search-clear" id="qualSearchClear" style="display:none;" onclick="clearQualSearchInput()" title="Clear input">&times;</button>
                                    <div class="search-dropdown ij-qual-dropdown" id="qualDropdown"></div>
                                </div>
                            </div>
                            <div class="selected-header">
                                <span class="selected-label">Selected Qualifications (<span id="qualSelectedCount">0</span>/5)</span>
                                <button type="button" class="clear-all" onclick="clearAllQualifications()">Clear All</button>
                            </div>
                            <div class="chips-container ij-selected-chips ij-qual-selected-chips" id="qualList"></div>
                            <input type="hidden" id="qualification" name="qualification" value="">
                        </div>
                    </div>

                    <div class="card ij-autoclose-card ij-additional-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>Job Auto Close
                            </div>
                            <?php
                                $currDays = isset($numberDays) && $numberDays != '' ? (string)$numberDays : '60';
                                if (!in_array($currDays, ['15', '30', '45', '60'])) {
                                    $currDays = '60';
                                }
                            ?>
                            <div class="form-group-cus">
                                <label class="form-label required mb-2" for="end_date">Job automatically closed in</label>
                                <div class="ij-choices ij-choices-4 ij-autoclose-choices" role="group" aria-label="Job Auto Close">
                                    <button type="button" class="ij-choice ij-choice-with-icon" data-value="15" aria-pressed="<?php echo ($currDays === '15') ? 'true' : 'false'; ?>">
                                        <span class="ij-choice-icon-wrap ij-icon-blue">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <span class="ij-choice-text">
                                            <strong>15 Days</strong>
                                            <small>Closes in 2 weeks</small>
                                        </span>
                                    </button>
                                    <button type="button" class="ij-choice ij-choice-with-icon" data-value="30" aria-pressed="<?php echo ($currDays === '30') ? 'true' : 'false'; ?>">
                                        <span class="ij-choice-icon-wrap ij-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <span class="ij-choice-text">
                                            <strong>30 Days</strong>
                                            <small>Standard 1 month</small>
                                        </span>
                                    </button>
                                    <button type="button" class="ij-choice ij-choice-with-icon" data-value="45" aria-pressed="<?php echo ($currDays === '45') ? 'true' : 'false'; ?>">
                                        <span class="ij-choice-icon-wrap ij-icon-purple">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <span class="ij-choice-text">
                                            <strong>45 Days</strong>
                                            <small>Extended 1.5 months</small>
                                        </span>
                                    </button>
                                    <button type="button" class="ij-choice ij-choice-with-icon" data-value="60" aria-pressed="<?php echo ($currDays === '60') ? 'true' : 'false'; ?>">
                                        <span class="ij-choice-icon-wrap ij-icon-green">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <span class="ij-choice-text">
                                            <strong>60 Days</strong>
                                            <small>Maximum 2 months (Default)</small>
                                        </span>
                                    </button>
                                </div>
                                <select class="form-select d-none" name="end_date" id="end_date">
                                    <option value="15" <?php echo ($currDays === '15') ? 'selected' : ''; ?>>15 Days</option>
                                    <option value="30" <?php echo ($currDays === '30') ? 'selected' : ''; ?>>30 Days</option>
                                    <option value="45" <?php echo ($currDays === '45') ? 'selected' : ''; ?>>45 Days</option>
                                    <option value="60" <?php echo ($currDays === '60') ? 'selected' : ''; ?>>60 Days</option>
                                </select>
                                <span class="form-hint" style="margin-top:8px;">Job will be automatically marked as closed after this duration.</span>
                            </div>
                        </div>
                    </div>
                    <div class="card ij-hiring-team-card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>Hiring Team
                            </div>
                            <div class="team-grid">
                                <div class="team-section">
                                    <label class="form-label required">Select the Hiring Manager / HR for this job position from the {{ ($global_company_type == 3) ? 'company' : 'client' }}.</label>
                                    <div class="input-wrapper" style="position:relative;">
                                        <div class="input-with-icon">
                                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                            </svg>
                                            <input type="text" class="form-input hr_manager_add" id="hmSearchDisplay" placeholder="Search hiring manager / HR..." style="cursor:pointer;" readonly>
                                        </div>
                                        <button type="button" class="chip-add hr_manager_add" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M5 12h14"/><path d="M12 5v14"/>
                                            </svg>Add More
                                        </button>
                                    </div>
                                    {{-- Real field POSTed to backend (comma-separated names); kept hidden, shown as cards below --}}
                                    <input type="hidden" name="hiring_namager" id="hiring_namager" value="<?php echo @$cpersonData; ?>">
                                    <span class="form-hint">This person will be the primary point of contact.</span>
                                    <div class="team-list" id="hmCardList"></div>
                                </div>
                                <div class="team-section">
                                    <label class="form-label required">Select your team who will work on this job position.</label>
                                    <div class="input-wrapper" style="position:relative;">
                                        <div class="input-with-icon">
                                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="11" cy="11" r="8"/>
                                                <path d="m21 21-4.3-4.3"/>
                                            </svg>
                                            <input type="text" class="form-input team_manager_add" id="recSearchDisplay" placeholder="Search team members..." style="cursor:pointer;" readonly>
                                        </div>
                                        <button type="button" class="chip-add team_manager_add" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/>
                                            </svg>Add More
                                        </button>
                                    </div>
                                    {{-- Real fields POSTed to backend; kept hidden, shown as cards below --}}
                                    <input type="hidden" name="recruiter" id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>">
                                    <input type="hidden" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>">
                                    <span class="form-hint">Assign internal team members / recruiters.</span>
                                    <div class="team-list" id="recruiterCardList"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)">Continue
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="step-content" id="step2">
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>
                                </svg>Job Description <span class="required"></span>
                            </div>
                            <div class="editor-toolbar">
                                <button type="button" class="toolbar-btn" onclick="formatText('bold')" title="Bold">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                                        <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                                    </svg>
                                </button>
                                <button type="button" class="toolbar-btn" onclick="formatText('italic')" title="Italic">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="19" x2="10" y1="4" y2="4"/>
                                        <line x1="14" x2="5" y1="20" y2="20"/>
                                        <line x1="15" x2="9" y1="4" y2="20"/>
                                    </svg>
                                </button>
                                <button type="button" class="toolbar-btn" onclick="formatText('underline')" title="Underline">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"/>
                                        <line x1="4" x2="20" y1="21" y2="21"/>
                                    </svg>
                                </button>
                                <div class="toolbar-divider"></div>
                                <button type="button" class="toolbar-btn" onclick="formatText('insertUnorderedList')" title="Bullet List">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" x2="21" y1="6" y2="6"/>
                                        <line x1="8" x2="21" y1="12" y2="12"/>
                                        <line x1="8" x2="21" y1="18" y2="18"/>
                                        <line x1="3" x2="3.01" y1="6" y2="6"/>
                                        <line x1="3" x2="3.01" y1="12" y2="12"/>
                                        <line x1="3" x2="3.01" y1="18" y2="18"/>
                                    </svg>
                                </button>
                                <button type="button" class="toolbar-btn" onclick="formatText('insertOrderedList')" title="Numbered List">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="10" x2="21" y1="6" y2="6"/>
                                        <line x1="10" x2="21" y1="12" y2="12"/>
                                        <line x1="10" x2="21" y1="18" y2="18"/>
                                        <path d="M4 6h1v4"/>
                                        <path d="M4 10h2"/>
                                        <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/>
                                    </svg>
                                </button>
                                <div class="toolbar-divider"></div>
                                <button type="button" class="toolbar-btn" onclick="formatText('justifyLeft')" title="Align Left">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="17" x2="3" y1="10" y2="10"/>
                                        <line x1="21" x2="3" y1="6" y2="6"/>
                                        <line x1="21" x2="3" y1="14" y2="14"/>
                                        <line x1="17" x2="3" y1="18" y2="18"/>
                                    </svg>
                                </button>
                                <button type="button" class="toolbar-btn" onclick="formatText('justifyCenter')" title="Align Center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="21" x2="3" y1="6" y2="6"/>
                                        <line x1="17" x2="7" y1="10" y2="10"/>
                                        <line x1="21" x2="3" y1="14" y2="14"/>
                                        <line x1="17" x2="7" y1="18" y2="18"/>
                                    </svg>
                                </button>
                                <div class="toolbar-divider"></div>
                                <button type="button" class="toolbar-btn" onclick="formatText('removeFormat')" title="Clear Formatting">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 7h18"/>
                                        <path d="M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"/>
                                        <line x1="9" x2="9" y1="7" y2="13"/>
                                        <line x1="15" x2="15" y1="7" y2="13"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="rich-editor" id="jobDescription" contenteditable="true" data-placeholder="Enter detailed job description here... Include responsibilities, requirements, benefits, and company overview.">
                                <?php echo @$job->job_description; ?>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                                <span class="form-hint">Use the toolbar to format your description</span>
                                <span class="form-hint" id="charCount">0 characters</span>
                            </div>
                            {{-- Hidden real field POSTed to backend (synced from #jobDescription on submit) --}}
                            <textarea name="kt_docs_tinymce_hidden" id="kt_docs_tinymce_hidden" style="display:none;"><?php echo @$job->job_description; ?></textarea>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/>
                                </svg>Key Skills
                            </div>
                            <label class="form-label required">Add skills required for this position (press Enter or comma to add)</label>
                            <div class="tags-input-wrapper" id="skillsWrapper" onclick="document.getElementById('skillsInput').focus()">
                                <input type="text" class="tag-input" id="skillsInput" placeholder="e.g. Java, React, Node.js...">
                            </div>
                            <span style="font-size:11px;color:var(--text-muted);margin-right:8px;">Suggested:</span>
                            <div class="chips-container skillsWrapperSkill" style="margin-top:12px;">
                                <span class="chip" onclick="addSuggestedSkill(this,'Java')">Java <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'Spring Boot')">Spring Boot <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'Microservices')">Microservices <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'React')">React <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'AWS')">AWS <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'Docker')">Docker <span class="remove">+</span></span><span class="chip" onclick="addSuggestedSkill(this,'Kubernetes')">Kubernetes <span class="remove">+</span></span>
                                <span class="chip" onclick="addSuggestedSkill(this,'SQL')">SQL <span class="remove">+</span></span>
                            </div>
                            {{-- Hidden real field POSTed to backend (Tagify JSON, synced from skills chips) --}}
                            <input type="hidden" name="tags" id="tags" value="">
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/><path d="m12 19-7-7 7-7"/>
                            </svg>Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(3)">Continue
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="step-content" id="step3">
                    <div class="content-grid">
                        <div class="left-column">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-row">
                                        <div class="card-title">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                                <line x1="12" x2="12.01" y1="17" y2="17"/>
                                            </svg>
                                            Screening Questions
                                        </div>
                                        <button type="button" class="btn-ai" id="aiGenerateBtn" onclick="generateAIQuestions()">
                                            <i class="fas fa-sparkles"></i>
                                            Generate Questions with AI
                                        </button>
                                    </div>
                                    <div class="card-subtitle" style="padding:0;font-size:12px;color:var(--text-muted);margin-top:-8px;margin-bottom:16px;">Add questions to screen candidates before they apply. You can set questions as required or optional.
                                    </div>
                                    <div class="questions-list" id="questionsList"></div>
                                    <div class="add-question-area" onclick="openQuestionModal()">
                                        <div class="add-question-icon">
                                            <i class="fas fa-plus"></i>
                                        </div>
                                        <div class="add-question-title">Add Screening Question</div>
                                        <div class="add-question-desc">Create a custom question to screen candidates</div>
                                    </div>
                                    {{-- Hidden real fields POSTed to backend (synced from questions list) --}}
                                    <div id="customTitleHidden"></div>
                                    <input type="hidden" name="customTitleId" id="customTitleId" value="<?php echo @$customTitleId; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="right-sidebar">
                            <div class="help-card">
                                <div class="help-card-header">
                                    <i class="fas fa-lightbulb"></i>
                                    Help &amp; Tips
                                </div>
                                <div class="help-card-text">Screening questions help you pre-qualify candidates before reviewing applications.
                                </div>
                                <div class="help-card-recommendation">Recommended: <span>3-5 questions</span></div>
                                <div class="help-card-text" style="margin-top:4px;font-size:11px;">Keep questions simple and relevant to the role.
                                </div>
                                <svg class="help-illustration" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="12" y="8" width="40" height="48" rx="4" fill="white" stroke="#3b82f6" stroke-width="2"/>
                                    <path d="M20 20h24M20 28h18M20 36h14" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="46" cy="44" r="8" fill="#dbeafe" stroke="#3b82f6" stroke-width="2"/>
                                    <path d="M43 44l2 2 4-4" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M48 12v-2a2 2 0 00-2-2H18a2 2 0 00-2 2v2" stroke="#3b82f6" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="templates-card">
                                <div class="templates-header">Popular Templates</div>
                                <div class="template-list" id="templateList"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(2)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/><path d="m12 19-7-7 7-7"/>
                            </svg>Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(4)">Continue
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="step-content" id="step4">
                    <div class="r4-grid">
                        {{-- ==================== COLUMN 1 ==================== --}}
                        <div class="r4-col">
                            {{-- Card 1: Basic Information --}}
                            <div class="r4-card r4-card-top">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                                <line x1="16" y1="13" x2="8" y2="13"/>
                                                <line x1="16" y1="17" x2="8" y2="17"/>
                                                <line x1="10" y1="9" x2="8" y2="9"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Basic Information</h3>
                                            <p>Enter the basic details for this job opening.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="goToStep(1)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-kv-list">
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-blue">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>
                                                </span>
                                                <span class="r4-label">Hiring Organization</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvClient">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-purple">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                                </span>
                                                <span class="r4-label">Department</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvDepartment">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-amber">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                </span>
                                                <span class="r4-label">Job Title</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvJobTitle">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-slate">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                                </span>
                                                <span class="r4-label">Job Type</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvJobType">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-amber">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                </span>
                                                <span class="r4-label">Experience</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvExp">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-indigo">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                                                </span>
                                                <span class="r4-label">Compensation</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvCompensation">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: Job Description --}}
                            <div class="r4-card">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                                <line x1="16" y1="13" x2="8" y2="13"/>
                                                <line x1="16" y1="17" x2="8" y2="17"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Job Description</h3>
                                            <p>Provide a detailed and clear job description.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="goToStep(2)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-desc-box">
                                        <div class="review-text" id="rvDescription">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== COLUMN 2 ==================== --}}
                        <div class="r4-col">
                            {{-- Card 3: Location & Employment --}}
                            <div class="r4-card r4-card-top">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Location &amp; Employment</h3>
                                            <p>Specify key employment details for this job opening.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="goToStep(1)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-kv-list">
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-red">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                </span>
                                                <span class="r4-label">Location</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvLocation">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-green">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                                </span>
                                                <span class="r4-label">Work Mode</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvWorkMode2">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-indigo">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                                                </span>
                                                <span class="r4-label">Shift</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvShift">-</div>
                                        </div>
                                        <div class="r4-kv-row">
                                            <div class="r4-kv-key">
                                                <span class="r4-row-icon r4-icon-purple">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                                </span>
                                                <span class="r4-label">Employment Type</span>
                                            </div>
                                            <div class="r4-kv-val" id="rvEmployment">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 4: Key Skills --}}
                            <div class="r4-card">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Key Skills <span id="rvSkillsCount">(0)</span></h3>
                                            <p>Add the key skills required for this position.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="goToStep(2)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-skills-list" id="rvSkills"></div>
                                </div>
                            </div>

                            {{-- Card 5: Screening Questions --}}
                            <div class="r4-card">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M9 11l3 3L22 4"/>
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Screening Questions <span id="rvQuestionsCount">(0)</span></h3>
                                            <p>Add screening questions for applicants.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="goToStep(3)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-questions-list" id="rvQuestions"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== COLUMN 3 ==================== --}}
                        <div class="r4-col">
                            {{-- Card 6: Publishing Visibility --}}
                            <div class="r4-card r4-card-blue r4-card-top">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-blue-box">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 11l18-5v12L3 13v-2z"/>
                                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Publishing Visibility</h3>
                                            <p>Control where this job opening is published and who can view it.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="r4-card-body">
                                    <div class="r4-visibility-list">
                                        <label class="r4-check-item">
                                            <input type="checkbox" class="r4-custom-check" name="visibility_career" checked>
                                            <span class="r4-check-box">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            </span>
                                            <span class="r4-check-content">
                                                <span class="r4-check-text">Publish on Company Career Page</span>
                                            </span>
                                        </label>

                                        <label class="r4-check-item">
                                            <input type="checkbox" class="r4-custom-check" name="visibility_marketplace" checked>
                                            <span class="r4-check-box">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            </span>
                                            <span class="r4-check-content">
                                                <span class="r4-check-title-row">
                                                    <span class="r4-check-text">Publish on Agency Marketplace</span>
                                                    <span class="r4-badge-recommended">Recommended</span>
                                                </span>
                                                <span class="r4-check-sublabel">Visible to 250+ partner agencies</span>
                                            </span>
                                        </label>

                                        <label class="r4-check-item">
                                            <input type="checkbox" class="r4-custom-check" name="visibility_internal">
                                            <span class="r4-check-box">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            </span>
                                            <span class="r4-check-content">
                                                <span class="r4-check-text">Publish Internally Only</span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="r4-info-box">
                                        <span class="r4-info-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="16" x2="12" y2="12"/>
                                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                                            </svg>
                                        </span>
                                        <div class="r4-info-text">
                                            This job will be visible to approved agency partners and their recruiters.
                                            <a href="javascript:void(0)" onclick="if(typeof showCoinRulesModal==='function')showCoinRulesModal();else showToast('Learn more about marketplace','info');">Learn more about marketplace</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 7: Important Notes --}}
                            <div class="r4-card">
                                <div class="r4-card-header">
                                    <div class="r4-header-left">
                                        <div class="r4-header-icon r4-icon-yellow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                        </div>
                                        <div class="r4-header-text">
                                            <h3>Important Notes</h3>
                                            <p>Key points to keep in mind.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="r4-card-body">
                                    <ul class="r4-notes-list">
                                        <li><span class="r4-dot"></span><span>Job will auto close after 60 days</span></li>
                                        <li><span class="r4-dot"></span><span>Coins will be deducted daily</span></li>
                                        <li><span class="r4-dot"></span><span>You can pause the job anytime</span></li>
                                        <li><span class="r4-dot"></span><span>Applications will be saved</span></li>
                                        <li><span class="r4-dot"></span><span>Extend duration anytime using coins</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== HIDDEN BACKWARD COMPATIBILITY BLOCK ===== --}}
                    {{-- Preserves all legacy IDs queried by validateStep/publishJob/populateReview so zero runtime errors occur --}}
                    <div class="d-none" aria-hidden="true">
                        <label class="summary-check"><input type="checkbox" checked></label>
                        <label class="summary-check"><input type="checkbox" checked></label>
                        <label class="summary-check"><input type="checkbox" checked></label>
                        <div class="submit-card">
                            <button type="button" class="btn btn-primary" onclick="publishJob()">
                                <?php echo @$buttontext ? $buttontext : 'Publish Job'; ?>
                            </button>
                        </div>
                        <span id="badgeEmployment"></span>
                        <span id="badgeWorkMode"></span>
                        <span id="badgeExp"></span>
                        <span id="badgeLocation"></span>
                        <span id="badgeSalary"></span>
                        <span id="csTitle"></span>
                        <span id="csLocation"></span>
                        <span id="csExp"></span>
                        <span id="csEmployment"></span>
                        <span id="csWorkMode"></span>
                        <span id="csSalary"></span>
                    </div>
                </div>
                    {{-- ===== HIDDEN / BACKEND-ONLY FIELDS (kept so the original store/update controller keeps working) ===== --}}<input type="hidden" name="show_client_name" id="show_client_name" value="no">
                    {{-- Legacy d-none salary block (kept verbatim so backend receives the same fields) --}}
                    <div class="d-none">
                        <select arial-label="Select Currency" name="salary_currency" id="salary_currency">
                            <?php
                                foreach ($currency as $key => $val) {
                                    $sel = '';
                                    $cur_sc = @$job->min_salary_currency;
                                    if ($cur_sc === null || $cur_sc === '') {
                                        if ($currency_code == $val) {
                                            $sel = 'selected="selected"';
                                        }
                                    } elseif ($cur_sc == @$key) {
                                        $sel = 'selected="selected"';
                                    }
                                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                    echo '<option value="' . $key . '" data-code="' . e($val) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';
                                }
                            ?>
                        </select>
                        <input type="number" name="min_salary" id="min_salary" placeholder="From" value="<?php echo @$job->min_salary; ?>">
                        <input type="number" name="exp_ctc" id="exp_ctc" placeholder="To" value="">
                        <select arial-label="Select Annual Salary" name="salary_time_mode" id="salary_time_mode">
                            <?php
                            foreach ($salaryMode as $key => $val) {
                                echo '<option value="' . $key . '">' . $val . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    {{-- Hidden max salary mode (synced from #min_sal_mode by JS) --}}
                    <select name="max_sal_mode" id="max_sal_mode" class="d-none">
                        <?php
                        foreach ($salaryMode as $key => $val) {
                            echo '<option value="' . $key . '">' . $val . '</option>';
                        }
                        ?>
                    </select>
                    {{-- Country allow (kept hidden) --}}
                    <div class="d-none">
                        <select class="form-control select2" id="kt_select2_4" name="kt_select2_4[]" multiple="multiple">
                            <optgroup label="Select Country for Vendor">
                                <?php
                                foreach ($countries as $val) {
                                    $sel = '';
                                    if (in_array($val->id, $valSelected)) {
                                        $sel = 'selected';
                                    } elseif ($val->id == $country) {
                                        $sel = 'selected';
                                    }
                                    echo '<option value="' . $val->id . '" ' . $sel . '>' . ucfirst($val->country_name) . '</option>';
                                }
                                ?>
                            </optgroup>
                        </select>
                    </div>

                    {{-- Career site checkbox (kept hidden) --}}
                    <div class="d-none">
                        <input class="form-check-input" name="carrier_website" type="checkbox" <?php echo @$carriew_website_chk; ?> value="1">
                    </div>
            </form>
        </div>
        <div class="modal-overlay" id="questionModal">
            <div class="modal" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <div class="modal-title" id="modalTitle">Add Screening Question</div>
                    <button type="button" class="modal-close" onclick="closeQuestionModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group-cus" style="margin-bottom:16px;">
                        <label class="form-label required">Question</label>
                        <textarea class="form-textarea" id="questionInput" placeholder="Enter your screening question..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group-cus" style="margin-bottom:16px;">
                            <label class="form-label required">Answer Type</label>
                            <select class="form-select" id="typeInput" onchange="togglePhoneField()">
                                <option value="short">Short Text</option>
                                <option value="paragraph">Paragraph</option>
                                <option value="yesno">Yes / No</option>
                                <option value="multiple">Multiple Choice</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="phone">Phone Number</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="form-group-cus" style="margin-bottom:16px;">
                            <label class="form-label">Requirement</label>
                            <div class="toggle-group">
                                <div class="toggle-switch active" id="requiredToggle" onclick="toggleRequired()"></div>
                                <span class="toggle-label" id="requiredLabel">Required</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-cus" id="phoneGroup" style="display:none;margin-bottom:16px;">
                        <label class="form-label">Phone Number with Country Code</label>
                        <div class="phone-input-group">
                            <select class="form-select country-select" id="countryCode">
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                                <option value="+91">🇮🇳 +91</option>
                                <option value="+61">🇦🇺 +61</option>
                                <option value="+49">🇩🇪 +49</option>
                                <option value="+33">🇫🇷 +33</option>
                                <option value="+81">🇯🇵 +81</option>
                                <option value="+86">🇨🇳 +86</option>
                                <option value="+7">🇷🇺 +7</option>
                                <option value="+55">🇧🇷 +55</option>
                                <option value="+39">🇮🇹 +39</option>
                                <option value="+34">🇪🇸 +34</option>
                                <option value="+82">🇰🇷 +82</option>
                                <option value="+65">🇸🇬 +65</option>
                                <option value="+971">🇦🇪 +971</option>
                            </select>
                            <input type="tel" class="form-input" placeholder="Enter phone number">
                        </div>
                    </div>
                    <div class="form-group-cus" id="optionsGroup" style="display:none;margin-bottom:16px;"><label class="form-label">Options (comma separated)</label>
                        <input type="text" class="form-input" id="questionOptions" placeholder="Option 1, Option 2, Option 3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeQuestionModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" id="modalActionBtn" onclick="saveQuestion()">Add Question</button>
                </div>
            </div>
        </div>
        <div class="jr-toast-container" id="toastContainer"></div>
    </div>
    <div id="kt_activitiesClient_toggle"></div>
    <div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle"
        data-kt-drawer-close="#kt_activitiesClient_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                            style="float:left"> <a href="javascript:void(0);" class="l">
                                <div class="symbol-label border-radius-5" id="light_client_caption">&nbsp;</div>
                            </a>
                        </div>
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_client_name">&nbsp;</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4">(Contacts Person/Hiring Manager)</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesClient_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
        </div>
    </div>

    <div id="kt_add_address_toggle"></div>
    <div id="kt_add_address" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_add_address_toggle"
        data-kt-drawer-close="#kt_add_address_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                            style="float:left"> <a href="javascript:void(0);" class="l">
                                <div class="symbol-label border-radius-5" id="light_address_caption">&nbsp;</div>
                            </a> </div>
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_address">&nbsp;</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_add_address_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_add_address_body">

                <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
                    <div class="col-xl-12 col-xxl-12">
                        <!--begin::Form Wizard Form-->
                        <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post"
                            id="frmupdateAddress" name="frmupdateAddress" enctype="multipart/form-data">
                            <meta name="csrf-token" content="{{ csrf_token() }}" />
                            <div class="pb-5" data-wizard-type="step-content">
                                <div class="row client-address-container">
                                    <div class="row client-address-container-child" id="clientAddr_0">
                                        <div class="form-group-cus">
                                            <label class="form-label required fw-bolder">Address: 1</label>
                                            <input type="text" class="form-control form-control-lg form-control-solid"
                                                name="address[]" placeholder="Enter address" value=""
                                                id="address_0">
                                            <span class="form-text text-muted">Please enter your Address</span>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group-cus fv-plugins-icon-container">
                                                <label class="form-label required fw-bolder">Select City:</label>
                                                <input class="form-control form-control-solid" value=""
                                                    id="kt_tagify_work_location_one" name="kt_tagify_work_location_one[]"
                                                    placeholder="Enter your Location..." />
                                                <span class="form-text text-muted">Please select your city</span>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="addressLoader"> </div>
                                <input type="hidden" name="clientId" id="clientId" value="0" />
                                <div class="d-flex justify-content-center py-5 my-5">

                                    <button type="button"
                                        class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 frmupdateAddress"
                                        data-wizard-type="action-next">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Address Details-->




            </div>
        </div>
    </div>


    <div id="kt_activitiesRecruiter_toggle"></div>
    <div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle"
        data-kt-drawer-close="#kt_activitiesRecruiter_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        @php
                            $logo_path = @$companylogo;
                            // dd($logo_path );
                        @endphp
                        @if (!empty($companylogo))
                            <div data-row-id="' . $row->id . '"
                                class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                <a href="javascript:void(0);">
                                    <div class="symbol-label border-radius-5">
                                        <img src="{{ @$logo_path }}" alt="Company Logo" class="border-radius-5 w-100">
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                style="float:left"> <a href="javascript:void(0);" class="l">
                                    <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
                                </a>
                            </div>
                        @endif

                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_recruiter_name_n">{{ @$company_name }}</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4">(Team Member)</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
        </div>
    </div>

    <!--begin::Add Contact Person Drawer (Bootstrap modal repurposed as right-side drawer via CSS)-->
    <div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header" id="add_team_member_header">
                    <div class="atm-head-info">
                        <h4 class="atm-title">Add Contact Person</h4>
                        <p class="atm-subtitle">Add a hiring manager or HR contact for this job position.</p>
                    </div>
                    <button type="button" class="atm-close-btn add_team_member_close" data-dismiss="modal" title="Close" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form id="add_team_member_form" name="add_team_member_form" class="form" action="#" method="post">
                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                        @csrf
                        @method('POST')
                        <input type="hidden" value="{{ @$country_code }}" id="phone_country_code" name="phone_country_code">

                        <!-- Contact Name -->
                        <div class="atm-field">
                            <label class="atm-label" for="cp_name_0">Contact Name<span class="atm-req">*</span></label>
                            <div class="atm-input-wrap">
                                <i class="la la-user atm-input-icon" aria-hidden="true"></i>
                                <input type="text" name="cp_name" id="cp_name_0" placeholder="Enter full name" autocomplete="off">
                            </div>
                            <div class="atm-error" id="atm_err_name">Contact name is required.</div>
                        </div>

                        <!-- Designation -->
                        <div class="atm-field">
                            <label class="atm-label" for="cp_desigination_0">Designation<span class="atm-req">*</span></label>
                            <div class="atm-input-wrap">
                                <i class="la la-id-badge atm-input-icon" aria-hidden="true"></i>
                                <input type="text" name="cp_desigination" id="cp_desigination_0" placeholder="e.g. HR Manager, Hiring Lead" autocomplete="off">
                            </div>
                            <div class="atm-error" id="atm_err_desig">Designation is required.</div>
                        </div>

                        <!-- Email Address -->
                        <div class="atm-field">
                            <label class="atm-label" for="cp_email_0">Email Address<span class="atm-req">*</span></label>
                            <div class="atm-input-wrap">
                                <i class="la la-envelope atm-input-icon" aria-hidden="true"></i>
                                <input type="email" name="cp_email" id="cp_email_0" placeholder="name@company.com" autocomplete="off">
                            </div>
                            <div class="atm-error" id="atm_err_email">Please enter a valid email address.</div>
                        </div>

                        <!-- Phone Number -->
                        <div class="atm-field">
                            <label class="atm-label" for="cp_number_0">Phone Number</label>
                            <x-jero-phone-input
                                id="cp_number_0"
                                name="cp_number"
                                countryCodeName="phone_country_code"
                                countryCodeId="phone_country_code"
                                value=""
                                :countryCode="@$country_code"
                                label=""
                                :required="false"
                                placeholder="Enter phone number" />
                            <div class="atm-error" id="atm_err_phone">Please enter a valid phone number.</div>
                        </div>

                    </form>
                </div>

                <!-- Footer (outside form — buttons use onclick/id selectors, not form submit) -->
                <div class="atm-footer">
                    <button type="button" class="atm-cancel-btn add_team_member_close" data-dismiss="modal">
                        <i class="la la-times-circle" aria-hidden="true"></i> Cancel
                    </button>
                    <button type="button" class="atm-save-btn" id="hiring_contact_person_add">
                        <span class="indicator-label" style="display:inline-flex;align-items:center;gap:6px;">
                            <i class="la la-user-plus" aria-hidden="true"></i> Add Contact Person
                        </span>
                        <span class="indicator-progress" style="display:none;align-items:center;gap:6px;">
                            <span class="spinner-border spinner-border-sm" style="width:14px;height:14px;border-width:2px;"></span> Adding…
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!--end::Add Contact Person Drawer-->

    <!-- Enterprise Complete Required Fields Modal -->
    <div id="requiredFieldsModal" class="rf-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="rfModalTitle">
        <div class="rf-modal-box">
            <button type="button" class="rf-modal-close" id="rfModalClose" aria-label="Close modal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>

            <!-- Warning Icon (Yellow circle with exclamation) -->
            <div class="rf-warning-icon-wrap" aria-hidden="true">
                <span class="rf-warning-icon">!</span>
            </div>

            <!-- Title -->
            <h3 class="rf-modal-title" id="rfModalTitle">Complete Required Fields</h3>

            <!-- Description -->
            <p class="rf-modal-desc" id="rfModalDesc">
                <span id="rfMissingCountText">3 required fields</span> need your attention before you can proceed with this job.
            </p>

            <!-- Section Heading -->
            <div class="rf-section-header">REQUIRED FIELDS</div>

            <!-- Interactive Fields List -->
            <div class="rf-fields-list" id="rfFieldsList">
            </div>

            <!-- Helper text -->
            <div class="rf-helper-text">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <span>Click a field above to jump directly to it.</span>
            </div>

            <!-- Primary CTA -->
            <button type="button" class="rf-cta-btn" id="rfPrimaryCta">
                <span>Review Required Fields</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </div>
    </div>
@endsection
@push('footer-script')
    {{-- internal-job-create-design.js is loaded at the end of this @push block (just before @endpush)
         so that it runs AFTER all inline scripts that define currentStep / goToStep / __indexUrl. --}}
    {{-- =========================================================
         ORIGINAL BACKEND AJAX (preserved verbatim, only TinyMCE/wizard
         specific bits adapted to the new design)
         ========================================================= --}}
    <script>
    $('#cp_number_0').on('input', function () {
        this.value = this.value.slice(0, 10);
    });

    $(document).on("change", 'input[name="cp_email"]', function () {
        let input  = $(this);
        let email  = input.val();
        if (email !== '') {
            $('.page-loader').css('display', 'flex');
            $.ajax({
                url: "{{ route('valid-email-chk') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", email: email },
                success: function (response) {
                    $('.page-loader').hide();
                    if (response.status === 'fail') {
                        input.val('');
                        Swal.fire({ text: "Sorry, Email [" + email + "] is already registered, Please use another email.", icon: "error", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-light" } });
                    } else if (response.status === 'emailchecker') {
                        input.val('');
                        Swal.fire({ text: response.message, icon: "error", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-light" } });
                    } else {
                        $('.verification_email').val(email);
                    }
                },
                error: function (xhr) {
                    $('.page-loader').hide();
                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                    } else {
                        Swal.fire({ text: "Server error, please try again", icon: "error" });
                    }
                }
            });
        }
    });

    $(document).ready(function () {
        if (window.JeroPhoneBox) {
            window.JeroPhoneBox.initAll();
        }
    });

    function opensidebar() { $('#kt_activitiesClient_toggle').trigger('click'); }
    </script>

    <!--begin::Page Scripts (Tagify lib lives in v7scripts.bundle.js) -->
    <script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>

    <script>
        // ---- Job title suggestions forwarders (backward compatibility) ----
        function getSuggestions(query) {
            if (window.JeroTitleIntelligenceEngine) {
                window.JeroTitleIntelligenceEngine.search(query);
            }
        }

        function selectSuggestion(suggestion) {
            if (window.JeroTitleIntelligenceEngine) {
                window.JeroTitleIntelligenceEngine.selectByTitle(suggestion);
            } else {
                $('#title').val(suggestion);
                changeautofillfield();
            }
        }

        $(document).ready(function() {
            $('.address_show').hide();
            $.noConflict();

            // ---- Add Hiring Contact Person ----
            function _atmClearErrors() {
                $('#cp_name_0, #cp_email_0, #cp_desigination_0, #cp_number_0').removeClass('is-invalid');
                $('.atm-error').removeClass('visible');
            }
            // Clear inline errors on input
            $(document).on('input', '#cp_name_0',       function() { $(this).removeClass('is-invalid'); $('#atm_err_name').removeClass('visible'); });
            $(document).on('input', '#cp_email_0',      function() { $(this).removeClass('is-invalid'); $('#atm_err_email').removeClass('visible'); });
            $(document).on('input', '#cp_desigination_0', function() { $(this).removeClass('is-invalid'); $('#atm_err_desig').removeClass('visible'); });
            $(document).on('input', '#cp_number_0',     function() { $(this).removeClass('is-invalid'); $('#atm_err_phone').removeClass('visible'); });
            // Clear errors when drawer opens
            $('#add_team_member').on('show.bs.modal', function() { _atmClearErrors(); });

            $("#hiring_contact_person_add").on("click", function(e) {
                e.preventDefault();
                _atmClearErrors();
                var client_id        = $('#client_id').val();
                var cp_name_0        = $('#cp_name_0').val().trim();
                var cp_email_0       = $('#cp_email_0').val().trim();
                var cp_number_0      = $('#cp_number_0').val().trim();
                var cp_desigination_0 = $('#cp_desigination_0').val().trim();
                var phone_country_code = $('#phone_country_code').val();
                var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var valid = true, $firstBad = null;

                if (!cp_name_0) {
                    $('#cp_name_0').addClass('is-invalid'); $('#atm_err_name').addClass('visible');
                    $firstBad = $firstBad || $('#cp_name_0'); valid = false;
                }
                if (!cp_desigination_0) {
                    $('#cp_desigination_0').addClass('is-invalid'); $('#atm_err_desig').addClass('visible');
                    $firstBad = $firstBad || $('#cp_desigination_0'); valid = false;
                }
                if (!cp_email_0 || !emailRe.test(cp_email_0)) {
                    $('#cp_email_0').addClass('is-invalid');
                    $('#atm_err_email').text(!cp_email_0 ? 'Email address is required.' : 'Please enter a valid email address.').addClass('visible');
                    $firstBad = $firstBad || $('#cp_email_0'); valid = false;
                }
                if (!valid) { if ($firstBad) $firstBad.focus(); return; }

                if (!(client_id > 0)) {
                    Swal.fire({ text: "Please select a client before adding a contact person.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-light" } });
                    return;
                }

                // Loading state
                var $btn = $('#hiring_contact_person_add');
                $btn.attr('disabled', true);
                $btn.find('.indicator-label').css('display', 'none');
                $btn.find('.indicator-progress').css('display', 'inline-flex');

                var token = "{{ csrf_token() }}";
                var id    = client_id;
                var url   = "{{ route('service_company.jobs.hiring_contact_person', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    type: 'POST', url: url,
                    data: { '_token': token, 'cp_name_0': cp_name_0, 'cp_email_0': cp_email_0, 'cp_number_0': cp_number_0, 'cp_desigination_0': cp_desigination_0, 'phone_country_code': phone_country_code, 'id': id },
                    success: function(response) {
                        $btn.attr('disabled', false);
                        $btn.find('.indicator-label').css('display', 'inline-flex');
                        $btn.find('.indicator-progress').css('display', 'none');
                        if (response.status == 'warning') {
                            $("input[name=cp_email]").val('');
                            $('#cp_email_0').addClass('is-invalid');
                            $('#atm_err_email').text(response.message).addClass('visible');
                            $('#cp_email_0').focus();
                            return false;
                        }
                        $('#Iteam_Table').prepend(response.view);
                        var hiring_namager = $('#hiring_namager').val();
                        if (hiring_namager == '') { $('#hiring_namager').val(response.contact_person_name); }
                        else { $('#hiring_namager').val(hiring_namager + ',' + response.contact_person_name); }
                        $('#add_team_member_form')[0].reset();
                        _atmClearErrors();
                        $(".add_team_member_close").click();
                        if (typeof phoneDisplay === 'function') { phoneDisplay(); }
                    },
                    error: function() {
                        $btn.attr('disabled', false);
                        $btn.find('.indicator-label').css('display', 'inline-flex');
                        $btn.find('.indicator-progress').css('display', 'none');
                        Swal.fire({ text: "Server error, please try again.", icon: "error", confirmButtonText: "Ok" });
                    }
                });
            });

            $('.hr_manager_add').on('click', function() { $("#hiring_namager").click(); });

            $("#hiring_namager").on("click", function() {
                var client_id = $('#client_id').val();
                if (client_id > 0) {
                    $('#kt_activitiesClient_toggle').trigger('click');
                    var clientName = $("#client_id option:selected");
                    var text_value = clientName.text();
                    $('#light_client_name').html(text_value);
                    var strVal = '', bgTextDecore = '', str = text_value;
                    str = str.substring(0, 1); str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);
                    if (str_bg >= 97 && str_bg <= 101) { bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    else if (str_bg >= 102 && str_bg <= 106) { bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    else if (str_bg >= 107 && str_bg <= 111) { bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    else if (str_bg >= 112 && str_bg <= 116) { bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    else if (str_bg >= 117 && str_bg <= 121) { bgTextDecore = 'fs-3 bg-dark text-light'; }
                    else { bgTextDecore = 'fs-3 bg-light text-dark'; }
                    $('#light_client_caption').html(strVal.toUpperCase());
                    $('#light_client_caption').addClass(bgTextDecore);
                    var hiring_namager = $('#hiring_namager').val();
                    var token = "{{ csrf_token() }}";
                    var id = client_id;
                    var url = "{{ route('service_company.jobs.hiring_manager', ':id') }}";
                    url = url.replace(':id', id);
                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    $.ajax({
                        type: 'POST', url: url, data: { '_token': token, 'hiring_namager': hiring_namager },
                        beforeSend: function() { $('#kt_activitiesClient_body').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
                        success: function(response) {
                            $('#kt_activitiesClient_body').html(response.view);
                            if (response.logo) {
                                $('#light_client_caption').removeClass('d-none');
                                $('#light_client_caption').html('<img src="' + response.logo + '" height="35px" width="100%">');
                            } else { $('#light_client_caption').addClass('d-none'); }
                        }
                    });
                } else {
                    alert("Please select any client after that choose our Hiring manager or Contact person");
                }
            });

            $(".add_address").on("click", function() {
                var clientId = $("#client_id").val();
                if (clientId != '') {
                    $('#kt_add_address_toggle').trigger('click');
                    var clientName = $("#client_id option:selected");
                    var text_value = clientName.text();
                    $('#light_address').html(text_value);
                    $('#clientId').val(clientId);
                    var strVal = '', bgTextDecore = '', str = text_value;
                    str = str.substring(0, 1); str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);
                    if (str_bg >= 97 && str_bg <= 101) { bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    else if (str_bg >= 102 && str_bg <= 106) { bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    else if (str_bg >= 107 && str_bg <= 111) { bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    else if (str_bg >= 112 && str_bg <= 116) { bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    else if (str_bg >= 117 && str_bg <= 121) { bgTextDecore = 'fs-3 bg-dark text-light'; }
                    else { bgTextDecore = 'fs-3 bg-light text-dark'; }
                    $('#light_address_caption').html(strVal.toUpperCase());
                    $('#light_address_caption').addClass(bgTextDecore);
                } else {
                    Swal.fire({ title: 'Warning', text: 'Please select Hiring Organization', icon: 'warning', allowOutsideClick: false });
                }
            });

            $(document).on('click', '.frmupdateAddress', function() {
                var clientId = $("#client_id").val();
                var data = new FormData();
                var form_data = $('#frmupdateAddress').serializeArray();
                $.each(form_data, function(key, input) { data.append(input.name, input.value); });
                $.ajax({
                    url: '{{ route('service_company.account.addressupdate') }}',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    method: "POST", processData: false, contentType: false, data: data, redirect: true,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#kt_add_address_close').click();
                            $("#client_id").val(clientId).change();
                            if (data.tenant_defaults && window.JeroTenantDefaults) {
                                window.JeroTenantDefaults.syncFromSettings(data.tenant_defaults);
                            }
                        }
                    }
                });
            });

            $('.remove_address').click(function() { $('.client-address-container').children().last().remove(); });

            $('.team_manager_add').on('click', function() { $("#recruiter").click(); });
            $("#recruiter").on("click", function() {
                var client_id = $('#client_id').val();
                if (client_id > 0) {
                    $('#kt_activitiesRecruiter_toggle').trigger('click');
                    var clientName = $("#client_id option:selected");
                    var text_value = clientName.text();
                    $('#light_recruiter_name').html(text_value);
                    var strVal = '', bgTextDecore = '', str = text_value;
                    str = str.substring(0, 1); str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);
                    if (str_bg >= 97 && str_bg <= 101) { bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    else if (str_bg >= 102 && str_bg <= 106) { bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    else if (str_bg >= 107 && str_bg <= 111) { bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    else if (str_bg >= 112 && str_bg <= 116) { bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    else if (str_bg >= 117 && str_bg <= 121) { bgTextDecore = 'fs-3 bg-dark text-light'; }
                    else { bgTextDecore = 'fs-3 bg-light text-dark'; }
                    $('#light_recruiter_caption').html(strVal.toUpperCase());
                    $('#light_recruiter_caption').addClass(bgTextDecore);
                    var recruiter = $('#recruiter').val();
                    var recruiter_id = $('#recruiter_id').val();
                    var token = "{{ csrf_token() }}";
                    var id = client_id;
                    var url = "{{ route('service_company.jobs.recruiter', ':id') }}";
                    url = url.replace(':id', id);
                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    $.ajax({
                        type: 'GET', url: url, data: { '_token': token, 'recruiter': recruiter, 'recruiter_id': recruiter_id },
                        beforeSend: function() { $('#kt_activitiesRecruiter_body').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
                        success: function(response) { $('#kt_activitiesRecruiter_body').html(response.view); }
                    });
                } else {
                    alert("Please select any client after that choose our Recruiters team");
                }
            });

            // ---- Salary/Experience validations (original) ----
            $(document).on("change", "#maximum_salary", function() { salary_check(); });
            $(document).on("change", "#minimum_salary", function() { salary_check(); });
            $(document).on("change", "#min_exp", function() { experience_check(); });
            $(document).on("change", "#max_exp", function() { experience_check(); });

            $(document).on('change', '#address_id', function() {
                var locationId = $(this).val();
                var url = "{{ route('service_company.jobs.getlocationCity', ':id') }}?locationId=" + locationId;
                url = url.replace(':id', locationId);
                $.ajax({ url: url, success: function(response) { /* location city loaded */ } });
            });

            // ---- Client change -> contact person + address list (original) ----
            $('#client_id').change(function() {
                var id = $(this).val();
                if (window.__BYPASS_NEXT_CLIENT_AJAX__) {
                    window.__BYPASS_NEXT_CLIENT_AJAX__ = false;
                    return;
                }
                $('#address_id').html('<option value="">Select Address</option>');
                if (!id || id <= 0) {
                    $('.address_show').hide();
                    return;
                }
                var url = "{{ route('service_company.jobs.getContactPerson', ':id') }}?job_id={{ $job_id }}&&jobLocation={{ implode(',', $jobLocation) }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    success: function(response) {
                        if (response.address_show) { $('.address_show').show(); } else { $('.address_show').hide(); }
                        $('#address_id').html(response.locArr);
                        if (typeof window.sanitizeAddressSelectOptions === 'function') {
                            window.sanitizeAddressSelectOptions();
                        }
                    }
                });
            }).change();

            // ---- Salary words (Enterprise Dynamic Multi-Currency) ----
            $(document).on('input keyup change', '#minimum_salary, #maximum_salary', function() {
                if (typeof updateSalaryWords === 'function') {
                    updateSalaryWords();
                }
            });

            // ---- Hiring Team: render selected people as cards (synced to hidden inputs) ----
            renderHiringCards();
            renderRecruiterCards();
            // The drawers set the input values programmatically (no change event); poll keeps cards in sync.
            setInterval(function() {
                if (($('#hiring_namager').val() || '') !== _jrLastHM) { renderHiringCards(); }
                if (($('#recruiter').val() || '') !== _jrLastRec) { renderRecruiterCards(); }
            }, 400);

            // Capture each person's real designation from the drawer rows whenever a drawer loads/changes,
            // then re-render the cards so they show the actual designation (not a generic label).
            ['kt_activitiesClient_body', 'kt_activitiesRecruiter_body'].forEach(function(idv) {
                var el = document.getElementById(idv);
                if (!el) return;
                jrScanDrawerDesignations(el);
                try {
                    var mo = new MutationObserver(function() {
                        jrScanDrawerDesignations(el);
                        renderHiringCards();
                        renderRecruiterCards();
                    });
                    mo.observe(el, { childList: true, subtree: true });
                } catch (e) {}
            });
        });

        // ---- Hiring Team card helpers ----
        var _jrLastHM = null, _jrLastRec = null;
        // name (lowercase) -> current designation, harvested from the open drawer rows
        var __personDesig = {};
        function jrScanDrawerDesignations(container) {
            try {
                $(container).find('.cp_select[data-title]').each(function() {
                    var nm = ($(this).data('title') || '').toString().trim();
                    if (!nm) return;
                    var $row = $(this).closest('tr');
                    var d = $row.find('.member-role').first().text().trim();
                    if (!d) { d = $row.find('.text-muted').first().text().trim(); }
                    if (d) { __personDesig[nm.toLowerCase()] = d; }
                });
            } catch (e) {}
        }
        function jrDesig(name, fallback) {
            return __personDesig[(name || '').trim().toLowerCase()] || fallback;
        }
        function jrAvatarColor(ch) {
            var c = (ch || 'a').toLowerCase().charCodeAt(0);
            if (c >= 97 && c <= 101) return '#ef4444';
            if (c >= 102 && c <= 106) return '#f59e0b';
            if (c >= 107 && c <= 111) return '#2563eb';
            if (c >= 112 && c <= 116) return '#10b981';
            if (c >= 117 && c <= 121) return '#6366f1';
            return '#64748b';
        }
        function jrInitials(name) {
            var p = name.trim().split(/\s+/);
            var s = (p[0] ? p[0].charAt(0) : '') + (p[1] ? p[1].charAt(0) : '');
            return (s || name.charAt(0)).toUpperCase();
        }
        function jrEsc(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
        function jrTitleCase(s) { return (s || '').replace(/\b\w/g, function(c) { return c.toUpperCase(); }); }
        function jrPersonCard(name, role, onRemove) {
            var disp = jrTitleCase(name);
            return '<div class="team-member">'
                + '<div class="member-avatar" style="background:' + jrAvatarColor(name.charAt(0)) + '">' + jrEsc(jrInitials(name)) + '</div>'
                + '<div class="member-info"><div class="member-name">' + jrEsc(disp) + '</div><div class="member-role">' + jrEsc(role) + '</div></div>'
                + '<button type="button" class="member-remove" title="Remove" onclick="' + onRemove + '">'
                + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
                + '</button></div>';
        }
        function jrSplit(v) { return (v || '').split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length; }); }
        function renderHiringCards() {
            var val = $('#hiring_namager').val() || '';
            _jrLastHM = val;
            var names = jrSplit(val), html = '';
            names.forEach(function(n, i) { html += jrPersonCard(n, jrDesig(n, 'Hiring Manager / HR'), 'removeHiringCard(' + i + ')'); });
            $('#hmCardList').html(html);
            if (val.trim() && typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('hiring_namager');
            }
        }
        function removeHiringCard(i) {
            var names = jrSplit($('#hiring_namager').val());
            names.splice(i, 1);
            $('#hiring_namager').val(names.join(','));
            renderHiringCards();
        }
        function renderRecruiterCards() {
            var val = $('#recruiter').val() || '';
            _jrLastRec = val;
            var names = jrSplit(val), html = '';
            names.forEach(function(n, i) { html += jrPersonCard(n, jrDesig(n, 'Recruiter'), 'removeRecruiterCard(' + i + ')'); });
            $('#recruiterCardList').html(html);
            if (val.trim() && typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('recruiter');
            }
        }
        function removeRecruiterCard(i) {
            var names = jrSplit($('#recruiter').val());
            var ids = jrSplit($('#recruiter_id').val());
            names.splice(i, 1);
            if (ids.length > i) ids.splice(i, 1);
            $('#recruiter').val(names.join(','));
            $('#recruiter_id').val(ids.join(','));
            renderRecruiterCards();
        }

        // ---- Salary words helpers (Enterprise Multi-Currency Engine) ----
        var CURRENCY_DENOMINATIONS = {
            'INR': { singular: 'Rupee', plural: 'Rupees', system: 'indian' },
            'USD': { singular: 'Dollar', plural: 'Dollars', system: 'intl' },
            'EUR': { singular: 'Euro', plural: 'Euros', system: 'intl' },
            'GBP': { singular: 'Pound', plural: 'Pounds', system: 'intl' },
            'AED': { singular: 'Dirham', plural: 'Dirhams', system: 'intl' },
            'CAD': { singular: 'Canadian Dollar', plural: 'Canadian Dollars', system: 'intl' },
            'AUD': { singular: 'Australian Dollar', plural: 'Australian Dollars', system: 'intl' },
            'SGD': { singular: 'Singapore Dollar', plural: 'Singapore Dollars', system: 'intl' },
            'CHF': { singular: 'Swiss Franc', plural: 'Swiss Francs', system: 'intl' },
            'JPY': { singular: 'Yen', plural: 'Yen', system: 'intl' },
            'NZD': { singular: 'New Zealand Dollar', plural: 'New Zealand Dollars', system: 'intl' },
            'ZAR': { singular: 'Rand', plural: 'Rand', system: 'intl' },
            'BRL': { singular: 'Real', plural: 'Reais', system: 'intl' },
            'PHP': { singular: 'Philippine Peso', plural: 'Philippine Pesos', system: 'intl' },
            'MYR': { singular: 'Malaysian Ringgit', plural: 'Malaysian Ringgits', system: 'intl' },
            'THB': { singular: 'Thai Baht', plural: 'Thai Baht', system: 'intl' },
            'IDR': { singular: 'Indonesian Rupiah', plural: 'Indonesian Rupiah', system: 'intl' },
            'VND': { singular: 'Dong', plural: 'Dong', system: 'intl' },
            'SEK': { singular: 'Swedish Krona', plural: 'Swedish Kronor', system: 'intl' },
            'NOK': { singular: 'Norwegian Krone', plural: 'Norwegian Kroner', system: 'intl' },
            'DKK': { singular: 'Danish Krone', plural: 'Danish Kroner', system: 'intl' },
            'SAR': { singular: 'Saudi Riyal', plural: 'Saudi Riyals', system: 'intl' },
            'QAR': { singular: 'Qatari Riyal', plural: 'Qatari Riyals', system: 'intl' },
            'KWD': { singular: 'Kuwaiti Dinar', plural: 'Kuwaiti Dinars', system: 'intl' },
            'BHD': { singular: 'Bahraini Dinar', plural: 'Bahraini Dinars', system: 'intl' },
            'OMR': { singular: 'Omani Rial', plural: 'Omani Rials', system: 'intl' },
            'CNY': { singular: 'Yuan', plural: 'Yuan', system: 'intl' },
            'KRW': { singular: 'Won', plural: 'Won', system: 'intl' },
            'RUB': { singular: 'Russian Ruble', plural: 'Russian Rubles', system: 'intl' },
            'TRY': { singular: 'Turkish Lira', plural: 'Turkish Liras', system: 'intl' },
            'PLN': { singular: 'Zloty', plural: 'Zlotys', system: 'intl' }
        };

        var _ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        var _tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        function convertUnderThousand(n) {
            var str = '';
            if (n >= 100) {
                str += _ones[Math.floor(n / 100)] + ' Hundred ';
                n %= 100;
            }
            if (n >= 20) {
                str += _tens[Math.floor(n / 10)] + (n % 10 !== 0 ? ' ' + _ones[n % 10] : '');
            } else if (n > 0) {
                str += _ones[n];
            }
            return str.trim();
        }

        function convertIndianSystem(num) {
            if (num === 0) return 'Zero';
            var words = '';
            var crore = Math.floor(num / 10000000);
            num %= 10000000;
            var lakh = Math.floor(num / 100000);
            num %= 100000;
            var thousand = Math.floor(num / 1000);
            num %= 1000;
            var rest = num;

            if (crore > 0) {
                words += convertIndianSystem(crore) + ' Crore ';
            }
            if (lakh > 0) {
                words += convertIndianSystem(lakh) + ' Lakh ';
            }
            if (thousand > 0) {
                words += convertIndianSystem(thousand) + ' Thousand ';
            }
            if (rest > 0) {
                words += convertUnderThousand(rest);
            }
            return words.trim();
        }

        function convertIntlSystem(num) {
            if (num === 0) return 'Zero';
            var words = '';
            var billion = Math.floor(num / 1000000000);
            num %= 1000000000;
            var million = Math.floor(num / 1000000);
            num %= 1000000;
            var thousand = Math.floor(num / 1000);
            num %= 1000;
            var rest = num;

            if (billion > 0) {
                words += convertUnderThousand(billion) + ' Billion ';
            }
            if (million > 0) {
                words += convertUnderThousand(million) + ' Million ';
            }
            if (thousand > 0) {
                words += convertUnderThousand(thousand) + ' Thousand ';
            }
            if (rest > 0) {
                words += convertUnderThousand(rest);
            }
            return words.trim();
        }

        function formatAmountInWords(amount, currCode) {
            if (isNaN(amount) || amount <= 0) return '';
            var code = (currCode || 'INR').toUpperCase();
            var denom = CURRENCY_DENOMINATIONS[code] || {
                singular: code,
                plural: code,
                system: (code === 'INR' ? 'indian' : 'intl')
            };

            var words = (denom.system === 'indian') ? convertIndianSystem(amount) : convertIntlSystem(amount);
            var unit = (amount === 1) ? denom.singular : denom.plural;
            return (words + ' ' + unit).trim();
        }

        function getActiveCurrencyCode() {
            var $opt = $('#min_salary_currency option:selected');
            if ($opt.length && $opt.data('code')) return String($opt.data('code')).toUpperCase();
            var curText = ($('#minCurrTrigger .ij-curr-pill-code').text() || 'INR').trim().toUpperCase();
            return curText || 'INR';
        }

        function updateSalaryWords() {
            var currCode = getActiveCurrencyCode();
            var minVal = parseInt($('#minimum_salary').val(), 10);
            var maxVal = parseInt($('#maximum_salary').val(), 10);

            if (!isNaN(minVal) && minVal > 0) {
                $('.minimum_salary_word').text(formatAmountInWords(minVal, currCode));
            } else {
                $('.minimum_salary_word').text('*Minimum salary offered for this position.');
            }

            if (!isNaN(maxVal) && maxVal > 0) {
                $('.maximum_salary_word').text(formatAmountInWords(maxVal, currCode));
            } else {
                $('.maximum_salary_word').text('*Maximum salary offered for this position.');
            }
        }

        // Backward compatibility aliases
        function capitalizeFirstLetter(string) { return string ? string.charAt(0).toUpperCase() + string.slice(1) : ''; }
        function convertToWords(num) { return formatAmountInWords(num, getActiveCurrencyCode()); }

        function salary_check() {
            var minimum_salary = parseInt($("#minimum_salary").val());
            var maximum_salary = parseInt($("#maximum_salary").val());
            if (!isNaN(minimum_salary) && !isNaN(maximum_salary) && minimum_salary > 0 && maximum_salary > 0) {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({ title: 'Warning', text: 'Enter the maximum salary greater than the minimum salary', icon: 'warning', allowOutsideClick: false });
                    $("#maximum_salary").val('');
                    updateSalaryWords();
                }
            }
        }
        function experience_check() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (min_exp != '') {
                if (min_exp > max_exp) {
                    Swal.fire({ title: 'Warning', text: 'Select the maximum experience greater than the minimum experience', icon: 'warning', allowOutsideClick: false });
                    $("#max_exp").val('');
                }
            }
        }

        function updateCompensationCurrencyContext() {
            var $sel = $('#min_salary_currency');
            if (!$sel.length) return;
            var opt = $sel.find('option:selected');
            if (!opt.length) {
                opt = $sel.find('option').first();
                if (opt.length) $sel.val(opt.val());
            }
            var val = $sel.val();
            var symbol = opt.attr('data-symbol') || opt.data('symbol') || '';
            var code = (opt.attr('data-code') || opt.data('code') || opt.val() || 'INR').toString().toUpperCase();

            // Update header currency trigger
            $('#headerCurrSymbol').text(symbol || code);
            $('#headerCurrCode').text(code);

            // Update both input trigger pills
            $('#minCurrTrigger .ij-curr-pill-symbol, #maxCurrTrigger .ij-curr-pill-symbol').text(symbol || code);
            $('#minCurrTrigger .ij-curr-pill-code, #maxCurrTrigger .ij-curr-pill-code').text(code);

            // Sync hidden inputs
            if ($('#max_salary_currency').length && String($('#max_salary_currency').val()) !== String(val)) {
                $('#max_salary_currency').val(val);
            }
            if ($('#salary_currency').length && String($('#salary_currency').val()) !== String(val)) {
                $('#salary_currency').val(val);
            }

            // Sync popover selection state
            $('#ijCurrPopList .ij-curr-pop-item').each(function() {
                var itemVal = $(this).attr('data-val');
                var itemCode = ($(this).attr('data-code') || '').toString().toUpperCase();
                if (String(itemVal) === String(val) || itemCode === code) {
                    $(this).addClass('is-selected');
                } else {
                    $(this).removeClass('is-selected');
                }
            });

            // Update salary amount in words immediately
            updateSalaryWords();
        }
        updateCompensationCurrencyContext();

        $('body').on('change', '#min_salary_currency', function() {
            updateCompensationCurrencyContext();
        });
        $('body').on('change', '#max_salary_currency', function() {
            var val = $(this).val();
            if ($('#min_salary_currency').val() !== val) {
                $('#min_salary_currency').val(val).trigger('change');
            }
        });

        // Enterprise Currency Popover Controller
        (function initEnterpriseCurrencyPopover() {
            var $pop = $('#ijCurrencyPopover');
            if (!$pop.length) return;

            // Move popover to document.body to avoid parent container overflow clipping
            if ($pop.parent()[0] !== document.body) {
                $('body').append($pop);
            }

            var activeTrigger = null;

            function positionPopover($trigger) {
                if (!$trigger || !$trigger.length) return;
                var rect = $trigger[0].getBoundingClientRect();
                var popWidth = 320;
                var popHeight = $pop.outerHeight() || 340;
                var left = rect.left;

                // Screen boundary horizontal clamping
                if (left + popWidth > window.innerWidth - 16) {
                    left = window.innerWidth - popWidth - 16;
                }
                if (left < 16) left = 16;

                // Vertical positioning: default below, flip above if not enough room
                var top = rect.bottom + 4;
                if (top + popHeight > window.innerHeight - 10 && rect.top > popHeight + 10) {
                    top = rect.top - popHeight - 4;
                }

                $pop.css({
                    position: 'fixed',
                    top: top + 'px',
                    left: left + 'px',
                    zIndex: 999999
                });
            }

            function openCurrencyPopover($trigger) {
                activeTrigger = $trigger;
                $pop.show();
                positionPopover($trigger);

                $('#headerCurrTrigger, #minCurrTrigger, #maxCurrTrigger').attr('aria-expanded', 'false');
                $trigger.attr('aria-expanded', 'true');

                // Clear search and reset list
                var $search = $('#ijCurrPopSearch');
                $search.val('');
                $('#ijCurrPopList .ij-curr-pop-item').show();
                $('.ij-curr-pop-empty').remove();
                setTimeout(function() { $search.focus().select(); }, 50);

                // Scroll current selected currency into view
                var $selItem = $('#ijCurrPopList .ij-curr-pop-item.is-selected').first();
                if ($selItem.length) {
                    var list = document.getElementById('ijCurrPopList');
                    if (list) {
                        list.scrollTop = $selItem[0].offsetTop - list.offsetTop - 30;
                    }
                }
            }

            function closeCurrencyPopover() {
                $pop.hide();
                $('#headerCurrTrigger, #minCurrTrigger, #maxCurrTrigger').attr('aria-expanded', 'false');
                activeTrigger = null;
            }

            function selectCurrency(val, code) {
                $('#min_salary_currency').val(String(val)).trigger('change');
                $('#max_salary_currency').val(String(val)).trigger('change');
                $('#salary_currency').val(String(val)).trigger('change');
                updateCompensationCurrencyContext();
                closeCurrencyPopover();
                if (activeTrigger && activeTrigger.length) {
                    try { activeTrigger.focus(); } catch (e) {}
                }
            }

            $(document).on('click', '#headerCurrTrigger, #minCurrTrigger, #maxCurrTrigger', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if ($pop.is(':visible') && activeTrigger && activeTrigger[0] === this) {
                    closeCurrencyPopover();
                } else {
                    openCurrencyPopover($(this));
                }
            });

            // Search filter with instant matching and Enter key selection
            $('#ijCurrPopSearch').on('input keyup', function(e) {
                if (e.key === 'Escape') {
                    closeCurrencyPopover();
                    return;
                }
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var $firstVis = $('#ijCurrPopList .ij-curr-pop-item:visible').first();
                    if ($firstVis.length) {
                        var val = $firstVis.attr('data-val');
                        var code = ($firstVis.attr('data-code') || '').toUpperCase();
                        selectCurrency(val, code);
                    }
                    return;
                }
                var term = $(this).val().trim().toLowerCase();
                var matches = 0;
                $('#ijCurrPopList .ij-curr-pop-item').each(function() {
                    var searchData = ($(this).attr('data-search') || $(this).data('search') || '').toString().toLowerCase();
                    var code = ($(this).attr('data-code') || $(this).data('code') || '').toString().toLowerCase();
                    var name = ($(this).attr('data-name') || $(this).data('name') || '').toString().toLowerCase();
                    var sym = ($(this).attr('data-symbol') || $(this).data('symbol') || '').toString().toLowerCase();

                    if (!term || searchData.indexOf(term) > -1 || code.indexOf(term) > -1 || name.indexOf(term) > -1 || sym.indexOf(term) > -1) {
                        $(this).show();
                        matches++;
                    } else {
                        $(this).hide();
                    }
                });

                $('.ij-curr-pop-empty').remove();
                if (matches === 0) {
                    $('#ijCurrPopList').append('<div class="ij-curr-pop-empty" style="padding:16px 12px;text-align:center;font-size:12px;color:#94a3b8;">No currencies match your search</div>');
                }
            });

            // Select currency from list item (using robust closest selector)
            $(document).on('click', '.ij-curr-pop-item', function(e) {
                e.preventDefault();
                var $item = $(this).closest('.ij-curr-pop-item');
                var val = $item.attr('data-val');
                var code = ($item.attr('data-code') || '').toUpperCase();
                selectCurrency(val, code);
            });

            // Select currency from popular chips
            $(document).on('click', '.ij-curr-pop-chip', function(e) {
                e.preventDefault();
                var chipCode = ($(this).attr('data-code') || $(this).data('code') || '').toString().toUpperCase();
                var $targetItem = $('#ijCurrPopList .ij-curr-pop-item').filter(function() {
                    return ($(this).attr('data-code') || '').toString().toUpperCase() === chipCode;
                }).first();

                if ($targetItem.length) {
                    var val = $targetItem.attr('data-val');
                    selectCurrency(val, chipCode);
                } else {
                    closeCurrencyPopover();
                }
            });

            // Close on click outside or escape
            $(document).on('click', function(e) {
                if ($pop.is(':visible') && !$(e.target).closest('#ijCurrencyPopover, #headerCurrTrigger, #minCurrTrigger, #maxCurrTrigger').length) {
                    closeCurrencyPopover();
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $pop.is(':visible')) {
                    closeCurrencyPopover();
                }
            });

            // Reposition or update when window or any scroll container scrolls
            window.addEventListener('scroll', function() {
                if ($pop.is(':visible') && activeTrigger && activeTrigger.length) {
                    positionPopover(activeTrigger);
                }
            }, true);

            window.addEventListener('resize', function() {
                if ($pop.is(':visible') && activeTrigger && activeTrigger.length) {
                    positionPopover(activeTrigger);
                }
            });
        })();
        $('body').on('change', '#min_sal_mode', function() {
            document.getElementById("max_sal_mode").selectedIndex = this.selectedIndex;
        });

        // ---- Auto-fill JD from title (safe client-first execution + AJAX fallback) ----
        function changeautofillfield() {
            if (window._isApplyingTitleTemplate) return;
            var rawTitle = ($('#title').val() || '').trim();
            if (!rawTitle) return;

            if (window.JeroTitleIntelligenceEngine && window.JeroTitleIntelligenceEngine.hasLocalTemplate(rawTitle)) {
                window.JeroTitleIntelligenceEngine.applyLocalTemplate(rawTitle);
                return;
            }

            var data = new FormData();
            data.append("title", rawTitle);
            $.ajax({
                url: '{{ route('service_company.jobs.jdDetails') }}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST", processData: false, contentType: false, data: data, redirect: true,
                success: function(data) {
                    if (data.status == 'success') {
                        var ed = document.getElementById('jobDescription');
                        var currentDesc = ed ? (ed.innerText || ed.textContent || '').trim() : '';
                        if (ed && currentDesc === '') {
                            ed.innerHTML = data.jd || '';
                            syncDescription();
                        }
                        if (data.skills && skills.length === 0) {
                            (data.skills.split(',')).forEach(function(s) { s = s.trim(); if (s) skills.push(s); });
                            renderSkills();
                        }
                    }
                }
            });
        }

        $('#jobDescription').on('input', function() {

            $('#kt_docs_tinymce_hidden').val($(this).html());

            clearTimeout(window.jdTimer);

            window.jdTimer = setTimeout(function() {

                var data = new FormData();
                data.append("gdDetails", $('#kt_docs_tinymce_hidden').val());
                data.append("title", $('#title').val());

                $.ajax({
                    url: '{{ route('service_company.jobs.jdDetails') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function(data) {

                        if (data.status == 'success') {
                            let html = '';
                            if (data.skills) {
                            // Do NOT reset skills here — preserve already-added Key Skills
                            data.skills.forEach(function(item) {
                                html += `<span class="chip" onclick="addSuggestedSkill(this,'${item.skill}')">${item.skill} <span class="remove">+</span></span>`;
                            });
                            $('.skillsWrapperSkill').html(html);
                        }

                            // if (data.skills) {

                            //     skills = [];

                            //     data.skills.split(',').forEach(function(s) {

                            //         s = s.trim();

                            //         if (s) {
                            //             skills.push(s);
                            //         }
                            //     });

                            //     renderSkills();
                            // }
                        }
                    }
                });

            }, 1000);

        });

        // ---- Tagify only for the Address drawer city picker (original) ----
        function debounceone(func, delay) { let timer; return function (...args) { clearTimeout(timer); timer = setTimeout(() => func.apply(this, args), delay); }; }
        var tagifyInstance;
        (function() {
            var inputElement = document.querySelector("#kt_tagify_work_location_one");
            if (!inputElement || typeof Tagify === 'undefined') return;
            tagifyInstance = new Tagify(inputElement, {
                whitelist: [], enforceWhitelist: true, maxTags: 1,
                dropdown: { maxItems: 20, classname: "custom-dropdown-class", enabled: 1, closeOnSelect: true }
            });
            tagifyInstance.on('input', function(e) { tagifyInstance.dropdown.show(); });
            tagifyInstance.on('dropdown:select', function(e) { tagifyInstance.addTags([e.detail.data.value]); });
            const fetchSuggestionsone = debounceone(function(query) {
                $.ajax({
                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query, method: 'GET',
                    success: function(data) { if (Array.isArray(data)) { tagifyInstance.settings.whitelist = data; tagifyInstance.dropdown.show.call(tagifyInstance); } },
                    error: function(err) { console.error("Error fetching cities:", err); }
                });
            }, 200);
            tagifyInstance.on('input', function(e) { var query = e.detail.value.trim(); if (query.length < 2) return; fetchSuggestionsone(query); });
        })();
    </script>

    {{-- =========================================================
         NEW-DESIGN INTERACTIONS (wired to the real hidden inputs)
         ========================================================= --}}
    <script>
        // ---------- State ----------
        var currentStep = 1;
        var selectedCities = [];
        var qualifications = [];
        var skills = [];
        var editingQuestionId = null;
        var isRequired = true;
        var dragStartIndex = null;
        var questions = [];

        var qualWhitelist = @json($qualificationTag);

        // EDIT prefill payloads
        <?php
            $__editQ = [];
            if (($page_event ?? '') == 'EDIT' && !empty($additionalDetail)) {
                foreach ($additionalDetail as $aDetailArr) { $__editQ[] = $aDetailArr->detail_value; }
            }
        ?>
        var __pageEvent  = "<?php echo @$page_event; ?>";
        var __editQuestions = {!! json_encode($__editQ) !!};
        var __editTags   = {!! json_encode(@$jobtags) !!};
        var __editQual   = {!! json_encode(@$default_qualification) !!};
        var __editLoc    = {!! json_encode(@$default_loc) !!};

        <?php if (($page_event ?? '') == 'EDIT') { ?>
            var __submitUrl = "{{ route('service_company.jobs.update', @$job->id) }}";
        <?php } else { ?>
            var __submitUrl = "{{ route('service_company.jobs.store') }}";
        <?php } ?>
        var __indexUrl = "{{ route('service_company.jobs.index') }}";

        var templates = [
            { name: "From which date can you join?", type: "short", icon: "calendar-check", color: "blue" },
            { name: "Are you willing to relocate?", type: "yesno", icon: "map-marker-alt", color: "green" },
            { name: "Do you have any offer in hand?", type: "yesno", icon: "hand-holding-usd", color: "purple" },
            { name: "Are you comfortable working from office?", type: "yesno", icon: "building", color: "orange" },
            { name: "Are you comfortable working in shifts?", type: "yesno", icon: "clock", color: "pink" },
            { name: "Are you legally authorized to work in this country?", type: "yesno", icon: "passport", color: "teal" },
            { name: "Rate your communication skills.", type: "number", icon: "star", color: "indigo" },
            { name: "Have you worked with international clients?", type: "yesno", icon: "globe", color: "red" },
            { name: "Why are you looking for a change?", type: "short", icon: "exchange-alt", color: "cyan" },
            { name: "Have you managed a team?", type: "yesno", icon: "users-cog", color: "amber" },
            { name: "Do you have a passport available?", type: "yesno", icon: "passport", color: "lime" }
        ];
        var typeConfig = {
            short: { label: "Short Text", class: "badge-type-short", icon: "font" },
            yesno: { label: "Yes / No", class: "badge-type-yesno", icon: "check-circle" },
            multiple: { label: "Multiple Choice", class: "badge-type-multiple", icon: "list" },
            number: { label: "Number", class: "badge-type-number", icon: "hashtag" },
            phone: { label: "Phone", class: "badge-type-short", icon: "phone" },
            email: { label: "Email", class: "badge-type-yesno", icon: "envelope" },
            paragraph: { label: "Paragraph", class: "badge-type-short", icon: "align-left" },
            date: { label: "Date", class: "badge-type-yesno", icon: "calendar" }
        };

        // ---------- INIT ----------
        document.addEventListener('DOMContentLoaded', function() {
            initCitySearch(); initQualSearch(); initSkillsInput(); initCharCount(); updateCompensationCurrencyContext();

            // EDIT prefill: locations
            if (__editLoc) {
                String(__editLoc).split(',').forEach(function(entry) {
                    entry = entry.trim(); if (!entry) return;
                    if (selectedCities.indexOf(entry) === -1) selectedCities.push(entry);
                });
                renderSelectedCities();
            }
            // EDIT prefill: skills
            if (__editTags) {
                String(__editTags).split(',').forEach(function(s) { s = s.trim(); if (s && skills.indexOf(s) === -1) skills.push(s); });
                renderSkills();
            }
            // EDIT prefill: qualifications
            if (__editQual) {
                String(__editQual).split(',').forEach(function(q) { q = q.trim(); if (q && qualifications.indexOf(q) === -1) qualifications.push(q); });
                renderQualifications();
            }
            // EDIT prefill: questions
            if (Array.isArray(__editQuestions) && __editQuestions.length) {
                __editQuestions.forEach(function(t, i) { if (t && String(t).trim() !== '') questions.push({ id: i + 1, text: t, type: 'short', required: true }); });
            }
            renderQuestions();
            renderTemplates();
            setupDragAndDrop();
            updateStepper(1);
            syncDescription();
        });

        // ---------- STEP NAVIGATION ----------
        function goToStep(step, skipValidation) {
            if (step < 1 || step > 4) return;
            if (!skipValidation && step > currentStep) {
                for (var s = currentStep; s < step; s++) {
                    if (!validateStep(s)) return;
                }
            }
            currentStep = step;
            if (window.JeroDeptIntelligenceEngine && typeof window.JeroDeptIntelligenceEngine.close === 'function') {
                window.JeroDeptIntelligenceEngine.close();
            }
            if (window.JeroTitleIntelligenceEngine && typeof window.JeroTitleIntelligenceEngine.close === 'function') {
                window.JeroTitleIntelligenceEngine.close();
            }
            document.querySelectorAll('.step-content').forEach(function(el) { el.classList.remove('active'); });
            var stepTarget = document.getElementById('step' + step);
            if (stepTarget) stepTarget.classList.add('active');
            updateStepper(step);
            if (step === 4) { syncAll(); populateReview(); }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function updateStepper(step) {
            var steps = document.querySelectorAll('.step-v2');
            steps.forEach(function(el, idx) {
                var s = idx + 1;
                el.classList.remove('completed', 'current', 'pending');
                var icon = el.querySelector('.step-v2-icon');
                if (s < step) { el.classList.add('completed'); icon.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>'; }
                else if (s === step) { el.classList.add('current'); icon.textContent = s; }
                else { el.classList.add('pending'); icon.textContent = s; }
            });
        }

        // ---------- ENTERPRISE VALIDATION ENGINE ----------
        var jobFieldConfigMap = {
            // Step 1: Basic Details
            client_id: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Hiring Organization',
                selector: '#jero_client_select_wrap',
                highlightSelector: '#jero_client_select_wrap',
                focusSelector: '#jero_client_trigger',
                validate: function() {
                    var val = ($('#client_id').val() || '').trim();
                    return !!val;
                },
                message: 'Please select a Hiring Organization.'
            },
            address_id: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Workplace Address',
                selector: '#address_id',
                highlightSelector: '#address_id',
                focusSelector: '#address_id',
                validate: function() {
                    var val = ($('#address_id').val() || '').trim();
                    return !!val;
                },
                message: 'Please select an Address.'
            },
            title: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Job Title / Designation',
                selector: '#jero_title_select_wrap',
                highlightSelector: '#jero_title_select_wrap',
                focusSelector: '#title',
                validate: function() {
                    var val = ($('#title').val() || '').trim();
                    return !!val;
                },
                message: 'Please enter the Job Title / Designation.'
            },
            category_id: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Job Department',
                selector: '#jero_dept_select_wrap',
                highlightSelector: '#jero_dept_select_wrap',
                focusSelector: '#category_id_display',
                validate: function() {
                    var val = ($('#category_id').val() || '').trim();
                    return !!val;
                },
                message: 'Please select a Job Department.'
            },
            min_exp: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Minimum Experience',
                selector: '.ij-exp-group',
                highlightSelector: '.ij-exp-group',
                focusSelector: '#min_exp',
                validate: function() {
                    var val = $('#min_exp').val();
                    return val !== null && val !== '';
                },
                message: 'Please select Minimum Experience.'
            },
            max_exp: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Maximum Experience',
                selector: '.ij-exp-group',
                highlightSelector: '.ij-exp-group',
                focusSelector: '#max_exp',
                validate: function() {
                    var min = $('#min_exp').val();
                    var max = $('#max_exp').val();
                    if (max === null || max === '') return false;
                    if (min !== null && min !== '' && parseInt(min, 10) > parseInt(max, 10)) return false;
                    return true;
                },
                message: function() {
                    var min = $('#min_exp').val();
                    var max = $('#max_exp').val();
                    if (min !== null && min !== '' && max !== null && max !== '' && parseInt(min, 10) > parseInt(max, 10)) {
                        return 'Maximum experience must be greater than or equal to minimum experience.';
                    }
                    return 'Please select Maximum Experience.';
                }
            },
            total_positions: {
                step: 1,
                stepName: 'Basic Details',
                label: 'No. of Positions',
                selector: '#total_positions',
                highlightSelector: '#total_positions',
                focusSelector: '#total_positions',
                validate: function() {
                    var val = ($('#total_positions').val() || '').trim();
                    return !!val;
                },
                message: 'Please select No. of Positions.'
            },
            employment_type: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Position Type',
                selector: '.ij-employment-card .ij-choices',
                highlightSelector: '.ij-employment-card',
                focusSelector: '.ij-employment-card .ij-choices button:first',
                validate: function() {
                    var val = ($('#employment_type').val() || '').trim();
                    return !!val;
                },
                message: 'Please select Position Type.'
            },
            location: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Job Locations',
                selector: '.ij-location-card .ij-location-input-wrap',
                highlightSelector: '.ij-location-card',
                focusSelector: '#citySearch',
                validate: function() {
                    return typeof selectedCities !== 'undefined' && Array.isArray(selectedCities) && selectedCities.length > 0;
                },
                message: 'Please select at least one job location.'
            },
            work_from_home: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Work Mode',
                selector: '.ij-work-mode-card .ij-choices',
                highlightSelector: '.ij-work-mode-card',
                focusSelector: '.ij-work-mode-card .ij-choices button:first',
                validate: function() {
                    var val = ($('#work_from_home').val() || '').trim();
                    return !!val;
                },
                message: 'Please select Work Mode.'
            },
            work_shifts: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Work Shift',
                selector: '.ij-work-shift-card .ij-choices',
                highlightSelector: '.ij-work-shift-card',
                focusSelector: '.ij-work-shift-card .ij-choices button:first',
                validate: function() {
                    var val = ($('#work_shifts').val() || '').trim();
                    return !!val;
                },
                message: 'Please select Work Shift.'
            },
            minimum_salary: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Minimum Salary',
                selector: '#minSalaryWrap',
                highlightSelector: '#minSalaryWrap',
                focusSelector: '#minimum_salary',
                validate: function() {
                    var val = ($('#minimum_salary').val() || '').trim();
                    return val !== '' && !isNaN(val) && parseFloat(val) >= 0;
                },
                message: 'Please enter Minimum Salary.'
            },
            maximum_salary: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Maximum Salary',
                selector: '#maxSalaryWrap',
                highlightSelector: '#maxSalaryWrap',
                focusSelector: '#maximum_salary',
                validate: function() {
                    var min = ($('#minimum_salary').val() || '').trim();
                    var max = ($('#maximum_salary').val() || '').trim();
                    if (max === '' || isNaN(max) || parseFloat(max) < 0) return false;
                    if (min !== '' && !isNaN(min) && parseFloat(max) < parseFloat(min)) return false;
                    return true;
                },
                message: function() {
                    var min = ($('#minimum_salary').val() || '').trim();
                    var max = ($('#maximum_salary').val() || '').trim();
                    if (min !== '' && !isNaN(min) && max !== '' && !isNaN(max) && parseFloat(max) < parseFloat(min)) {
                        return 'Maximum salary must be greater than or equal to minimum salary.';
                    }
                    return 'Please enter Maximum Salary.';
                }
            },
            min_sal_mode: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Salary Type',
                selector: '#min_sal_mode',
                highlightSelector: '#min_sal_mode',
                focusSelector: '#min_sal_mode',
                validate: function() {
                    var val = ($('#min_sal_mode').val() || '').trim();
                    return !!val;
                },
                message: 'Please select Salary Type.'
            },
            qualification: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Qualifications',
                selector: '.ij-qual-card .ij-qual-input-wrap',
                highlightSelector: '.ij-qual-card',
                focusSelector: '#qualSearch',
                validate: function() {
                    return typeof qualifications !== 'undefined' && Array.isArray(qualifications) && qualifications.length > 0;
                },
                message: 'Please add at least one Qualification.'
            },
            end_date: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Auto Close Duration',
                selector: '.ij-autoclose-card .ij-choices',
                highlightSelector: '.ij-autoclose-card',
                focusSelector: '.ij-autoclose-card .ij-choices button:first',
                validate: function() {
                    var val = ($('#end_date').val() || '').trim();
                    return !!val;
                },
                message: 'Please select the auto-close duration.'
            },
            hiring_namager: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Hiring Manager / HR',
                selector: '.ij-hiring-team-card .team-section:first-child .input-wrapper',
                highlightSelector: '.ij-hiring-team-card .team-section:first-child',
                focusSelector: '#hmSearchDisplay',
                validate: function() {
                    var val = ($('#hiring_namager').val() || '').trim();
                    return !!val;
                },
                message: 'Please select the Hiring Manager / HR for this job.'
            },
            recruiter: {
                step: 1,
                stepName: 'Basic Details',
                label: 'Assigned Recruiter / Team',
                selector: '.ij-hiring-team-card .team-section:last-child .input-wrapper',
                highlightSelector: '.ij-hiring-team-card .team-section:last-child',
                focusSelector: '#recSearchDisplay',
                validate: function() {
                    var val = ($('#recruiter').val() || '').trim();
                    return !!val;
                },
                message: 'Please select your internal team for this job.'
            },

            // Step 2: Job Description
            jobDescription: {
                step: 2,
                stepName: 'Job Description',
                label: 'Job Description',
                selector: '#jobDescription',
                highlightSelector: '#step2 .card:first-child',
                focusSelector: '#jobDescription',
                validate: function() {
                    var descEl = document.getElementById('jobDescription');
                    var descText = descEl ? (descEl.innerText || descEl.textContent || '').trim() : '';
                    return !!descText;
                },
                message: 'Please enter the Job Description.'
            },
            skills: {
                step: 2,
                stepName: 'Job Description',
                label: 'Key Skills',
                selector: '#skillsWrapper',
                highlightSelector: '#step2 .card:last-child',
                focusSelector: '#skillsInput',
                validate: function() {
                    return typeof skills !== 'undefined' && Array.isArray(skills) && skills.length > 0;
                },
                message: 'Please add at least one Key Skill.'
            },

            // Step 3: Screening Questions
            screeningQuestions: {
                step: 3,
                stepName: 'Screening Questions',
                label: 'Screening Questions',
                selector: '#questionsList',
                highlightSelector: '#step3 .card',
                focusSelector: '#step3 .add-question-area',
                validate: function() {
                    if (typeof questions === 'undefined' || !Array.isArray(questions) || questions.length === 0) {
                        return true;
                    }
                    var hasEmpty = questions.some(function(q) {
                        return !q || !q.text || !String(q.text).trim();
                    });
                    return !hasEmpty;
                },
                message: 'Please ensure all added screening questions have text specified.'
            },

            // Step 4: Review & Publish
            visibility: {
                step: 4,
                stepName: 'Review & Publish',
                label: 'Publishing Visibility',
                selector: '.r4-visibility-list',
                highlightSelector: '.r4-card-blue',
                focusSelector: '.r4-visibility-list input[type="checkbox"]:first',
                validate: function() {
                    var c1 = $('input[name="visibility_career"]').is(':checked');
                    var c2 = $('input[name="visibility_marketplace"]').is(':checked');
                    var c3 = $('input[name="visibility_internal"]').is(':checked');
                    return c1 || c2 || c3;
                },
                message: 'Please select at least one publishing visibility option.'
            }
        };

        function detectJobMissingFields(stepFilter) {
            var missing = [];
            for (var key in jobFieldConfigMap) {
                if (!jobFieldConfigMap.hasOwnProperty(key)) continue;
                var cfg = jobFieldConfigMap[key];
                if (stepFilter && stepFilter !== 'all' && cfg.step !== stepFilter) {
                    continue;
                }
                var isValid = false;
                try {
                    isValid = cfg.validate();
                } catch (e) {
                    isValid = false;
                }
                if (!isValid) {
                    var msg = typeof cfg.message === 'function' ? cfg.message() : cfg.message;
                    missing.push({
                        key: key,
                        label: cfg.label,
                        step: cfg.step,
                        stepName: cfg.stepName,
                        selector: cfg.selector,
                        highlightSelector: cfg.highlightSelector,
                        focusSelector: cfg.focusSelector,
                        message: msg
                    });
                }
            }
            return missing;
        }

        function applyFieldValidationErrors(missingFields) {
            if (!Array.isArray(missingFields)) return;

            missingFields.forEach(function(f) {
                var $target = $(f.selector);
                if (!$target.length) {
                    $target = $('#' + f.key);
                }
                if (!$target.length) {
                    $target = $('[name="' + f.key + '"]');
                }

                $target.addClass('has-validation-error');
                if (f.key === 'title') {
                    $('#title').addClass('has-validation-error');
                    $('#jero_title_input_box').addClass('has-validation-error');
                } else if (f.key === 'category_id') {
                    $('#category_id_display').addClass('has-validation-error');
                    $('.jero-dept-input-box').addClass('has-validation-error');
                } else if (f.key === 'client_id') {
                    $('#jero_client_trigger').addClass('has-validation-error');
                } else if (f.key === 'min_exp' || f.key === 'max_exp') {
                    $('#' + f.key).addClass('has-validation-error');
                } else if (f.key === 'minimum_salary' || f.key === 'maximum_salary') {
                    $('#' + f.key).addClass('has-validation-error');
                }

                var errorHtml = '<div class="field-error-message" data-field-error="' + f.key + '">' +
                    '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                    '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>' +
                    '</svg>' +
                    '<span>' + f.message + '</span>' +
                    '</div>';

                if (!$('[data-field-error="' + f.key + '"]').length) {
                    if (f.key === 'min_exp' || f.key === 'max_exp') {
                        if (!$('[data-field-error="experience_range"]').length && !$('[data-field-error="' + f.key + '"]').length) {
                            $('.ij-exp-group').after(errorHtml);
                        }
                    } else if (f.key === 'location') {
                        $('.ij-location-card .ij-location-input-wrap').after(errorHtml);
                    } else if (f.key === 'qualification') {
                        $('.ij-qual-card .ij-qual-input-wrap').after(errorHtml);
                    } else if (f.key === 'skills') {
                        $('#skillsWrapper').after(errorHtml);
                    } else if (f.key === 'jobDescription') {
                        $('#jobDescription').after(errorHtml);
                    } else if (f.key === 'employment_type') {
                        $('.ij-employment-card .ij-choices').after(errorHtml);
                    } else if (f.key === 'work_from_home') {
                        $('.ij-work-mode-card .ij-choices').after(errorHtml);
                    } else if (f.key === 'work_shifts') {
                        $('.ij-work-shift-card .ij-choices').after(errorHtml);
                    } else if (f.key === 'end_date') {
                        $('.ij-autoclose-card .ij-choices').after(errorHtml);
                    } else if (f.key === 'hiring_namager') {
                        $('.ij-hiring-team-card .team-section:first-child .input-wrapper').after(errorHtml);
                    } else if (f.key === 'recruiter') {
                        $('.ij-hiring-team-card .team-section:last-child .input-wrapper').after(errorHtml);
                    } else if (f.key === 'visibility') {
                        $('.r4-visibility-list').after(errorHtml);
                    } else if ($target.length) {
                        $target.after(errorHtml);
                    }
                }
            });
        }

        function clearFieldValidationErrors(targetKey) {
            if (targetKey) {
                $('[data-field-error="' + targetKey + '"]').remove();
                if (targetKey === 'client_id') {
                    $('#jero_client_select_wrap').removeClass('has-validation-error');
                    $('#jero_client_trigger').removeClass('has-validation-error');
                } else if (targetKey === 'title') {
                    $('#title').removeClass('has-validation-error');
                    $('#jero_title_input_box').removeClass('has-validation-error');
                    $('#jero_title_select_wrap').removeClass('has-validation-error');
                } else if (targetKey === 'category_id') {
                    $('#category_id_display').removeClass('has-validation-error');
                    $('.jero-dept-input-box').removeClass('has-validation-error');
                    $('#jero_dept_select_wrap').removeClass('has-validation-error');
                } else if (targetKey === 'location') {
                    $('.ij-location-card .ij-location-input-wrap').removeClass('has-validation-error');
                    $('#citySearch').removeClass('has-validation-error');
                } else if (targetKey === 'qualification') {
                    $('.ij-qual-card .ij-qual-input-wrap').removeClass('has-validation-error');
                    $('#qualSearch').removeClass('has-validation-error');
                } else if (targetKey === 'skills') {
                    $('#skillsWrapper').removeClass('has-validation-error');
                    $('#skillsInput').removeClass('has-validation-error');
                } else if (targetKey === 'jobDescription') {
                    $('#jobDescription').removeClass('has-validation-error');
                } else if (targetKey === 'employment_type') {
                    $('.ij-employment-card .ij-choices').removeClass('has-validation-error');
                    $('#employment_type').removeClass('has-validation-error');
                } else if (targetKey === 'work_from_home') {
                    $('.ij-work-mode-card .ij-choices').removeClass('has-validation-error');
                    $('#work_from_home').removeClass('has-validation-error');
                } else if (targetKey === 'work_shifts') {
                    $('.ij-work-shift-card .ij-choices').removeClass('has-validation-error');
                    $('#work_shifts').removeClass('has-validation-error');
                } else if (targetKey === 'end_date') {
                    $('.ij-autoclose-card .ij-choices').removeClass('has-validation-error');
                    $('#end_date').removeClass('has-validation-error');
                } else if (targetKey === 'minimum_salary' || targetKey === 'maximum_salary' || targetKey === 'salary') {
                    $('#minSalaryWrap').removeClass('has-validation-error');
                    $('#maxSalaryWrap').removeClass('has-validation-error');
                    $('#minimum_salary').removeClass('has-validation-error');
                    $('#maximum_salary').removeClass('has-validation-error');
                    $('[data-field-error="minimum_salary"]').remove();
                    $('[data-field-error="maximum_salary"]').remove();
                } else if (targetKey === 'min_exp' || targetKey === 'max_exp' || targetKey === 'experience') {
                    $('.ij-exp-group').removeClass('has-validation-error');
                    $('#min_exp').removeClass('has-validation-error');
                    $('#max_exp').removeClass('has-validation-error');
                    $('[data-field-error="min_exp"]').remove();
                    $('[data-field-error="max_exp"]').remove();
                } else if (targetKey === 'hiring_namager') {
                    $('.ij-hiring-team-card .team-section:first-child .input-wrapper').removeClass('has-validation-error');
                    $('#hmSearchDisplay').removeClass('has-validation-error');
                } else if (targetKey === 'recruiter') {
                    $('.ij-hiring-team-card .team-section:last-child .input-wrapper').removeClass('has-validation-error');
                    $('#recSearchDisplay').removeClass('has-validation-error');
                } else if (targetKey === 'visibility') {
                    $('.r4-visibility-list').removeClass('has-validation-error');
                } else {
                    $('#' + targetKey).removeClass('has-validation-error');
                    $('[name="' + targetKey + '"]').removeClass('has-validation-error');
                }
            } else {
                $('.has-validation-error').removeClass('has-validation-error');
                $('.field-error-message').remove();
            }
        }

        function showRequiredFieldsModal(missingFields) {
            if (!missingFields || !missingFields.length) return;
            window.__currentJobMissingFields = missingFields;

            var count = missingFields.length;
            var countText = count === 1 ? '1 required field' : (count + ' required fields');
            var descText = countText + ' need' + (count === 1 ? 's' : '') + ' your attention before you can proceed with this job.';

            $('#rfMissingCountText').text(countText);
            $('#rfModalDesc').text(descText);

            var $list = $('#rfFieldsList');
            $list.empty();

            missingFields.forEach(function(f) {
                var rowHtml = '<div class="rf-field-row" role="button" tabindex="0" data-field-key="' + f.key + '" aria-label="Go to ' + f.label + '">' +
                    '<div class="rf-field-row-left">' +
                    '<span class="rf-field-badge" aria-hidden="true">!</span>' +
                    '<div style="display:flex; flex-direction:column; gap:2px; min-width:0; text-align:left;">' +
                    '<span class="rf-field-name">' + $('<div>').text(f.label).html() + '</span>' +
                    '<span style="font-size:11px; color:#64748b; font-weight:500;">Step ' + f.step + ': ' + f.stepName + '</span>' +
                    '</div>' +
                    '</div>' +
                    '<svg class="rf-field-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>' +
                    '</div>';
                $list.append(rowHtml);
            });

            var $modal = $('#requiredFieldsModal');
            $modal.fadeIn(150, function() {
                $('#rfPrimaryCta').focus();
            });
        }

        function closeRequiredFieldsModal() {
            $('#requiredFieldsModal').fadeOut(120);
        }

        function jumpToField(fieldKey) {
            closeRequiredFieldsModal();

            var cfg = jobFieldConfigMap[fieldKey];
            if (!cfg) return;

            var step = cfg.step;
            if (typeof goToStep === 'function' && typeof currentStep !== 'undefined' && currentStep !== step) {
                goToStep(step, true);
            }

            setTimeout(function() {
                var highlightEl = null;
                var focusTarget = null;

                if (cfg.highlightSelector) {
                    highlightEl = document.querySelector(cfg.highlightSelector);
                }
                if (!highlightEl && cfg.selector) {
                    highlightEl = document.querySelector(cfg.selector);
                }
                if (cfg.focusSelector) {
                    focusTarget = document.querySelector(cfg.focusSelector);
                }
                if (!focusTarget && highlightEl) {
                    focusTarget = highlightEl;
                }

                if (highlightEl) {
                    highlightEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    $(highlightEl).addClass('field-attention-highlight');
                    setTimeout(function() {
                        $(highlightEl).removeClass('field-attention-highlight');
                    }, 3000);
                }

                setTimeout(function() {
                    if (fieldKey === 'client_id') {
                        var trigger = document.getElementById('jero_client_trigger');
                        if (trigger) {
                            trigger.focus();
                            trigger.click();
                        }
                    } else if (fieldKey === 'category_id') {
                        var deptInput = document.getElementById('category_id_display');
                        if (deptInput) deptInput.focus();
                    } else if (fieldKey === 'hiring_namager') {
                        var hmToggle = document.querySelector('.hr_manager_add');
                        if (hmToggle) hmToggle.click();
                    } else if (fieldKey === 'recruiter') {
                        var recToggle = document.querySelector('.team_manager_add');
                        if (recToggle) recToggle.click();
                    } else if (focusTarget && typeof focusTarget.focus === 'function') {
                        try { focusTarget.focus(); } catch (e) {}
                    }
                }, 350);
            }, 150);
        }

        function validateStep(step) {
            clearFieldValidationErrors();
            var missing = detectJobMissingFields(step);
            if (missing.length > 0) {
                applyFieldValidationErrors(missing);
                showRequiredFieldsModal(missing);
                return false;
            }
            return true;
        }

        function validateAllSteps() {
            clearFieldValidationErrors();
            var missing = detectJobMissingFields('all');
            if (missing.length > 0) {
                applyFieldValidationErrors(missing);
                showRequiredFieldsModal(missing);
                return false;
            }
            return true;
        }

        // Modal event listeners
        $(document).on('click', '#rfModalClose', function(e) {
            e.preventDefault();
            closeRequiredFieldsModal();
        });

        $(document).on('click', '#requiredFieldsModal', function(e) {
            if (e.target === this) {
                closeRequiredFieldsModal();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#requiredFieldsModal').is(':visible')) {
                closeRequiredFieldsModal();
            }
        });

        $(document).on('click', '.rf-field-row', function(e) {
            e.preventDefault();
            var key = $(this).data('field-key');
            if (key) {
                jumpToField(key);
            }
        });

        $(document).on('keydown', '.rf-field-row', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var key = $(this).data('field-key');
                if (key) {
                    jumpToField(key);
                }
            }
        });

        $(document).on('click', '#rfPrimaryCta', function(e) {
            e.preventDefault();
            if (window.__currentJobMissingFields && window.__currentJobMissingFields.length) {
                jumpToField(window.__currentJobMissingFields[0].key);
            } else {
                closeRequiredFieldsModal();
            }
        });

        // Real-time error clearing listeners
        $(document).on('input change focus', 'input, select, textarea, [contenteditable="true"]', function() {
            var $this = $(this);
            var key = $this.attr('name') || $this.attr('id');
            if ($this.hasClass('has-validation-error')) {
                $this.removeClass('has-validation-error');
            }
            var $wrap = $this.closest('.has-validation-error');
            if ($wrap.length) {
                $wrap.removeClass('has-validation-error');
            }
            if (key) {
                clearFieldValidationErrors(key);
            }
        });

        $(document).on('click', '.ij-choice', function() {
            var $group = $(this).closest('.ij-choices');
            if ($group.length) {
                $group.removeClass('has-validation-error');
            }
            var select = $(this).closest('.card-body').find('select');
            if (select.length && select.attr('id')) {
                clearFieldValidationErrors(select.attr('id'));
            }
        });

        $(document).on('input keyup', '#jobDescription', function() {
            var text = ($('#jobDescription').text() || '').trim();
            if (text) {
                clearFieldValidationErrors('jobDescription');
            }
        });

        $(document).on('change', '.r4-visibility-list input[type="checkbox"]', function() {
            var c1 = $('input[name="visibility_career"]').is(':checked');
            var c2 = $('input[name="visibility_marketplace"]').is(':checked');
            var c3 = $('input[name="visibility_internal"]').is(':checked');
            if (c1 || c2 || c3) {
                clearFieldValidationErrors('visibility');
            }
        });

        // ---------- CREATE OPTIONS (mock) ----------
        function selectOption(el, type) {
            document.querySelectorAll('.option-card').forEach(function(c) { c.classList.remove('selected'); });
            el.classList.add('selected');
            showToast(type === 'new' ? 'Creating new job from scratch' : type === 'template' ? 'Template selection coming soon' : 'Clone job coming soon', 'info');
        }

        // ---------- GOD-LEVEL CANONICAL LOCATION ENGINE (V4 ARCHITECTURE) ----------
        function debounce(func, delay) { let timer; return function (...args) { clearTimeout(timer); timer = setTimeout(() => func.apply(this, args), delay); }; }

        // Layer 1: In-Browser Curated Global Hubs (30 worldwide hubs grouped by Region)
        var curatedLocationGroups = [
            {
                region: 'India Tech Hubs',
                items: [
                    { city: 'Bengaluru', state: 'Karnataka', country: 'India', badge: 'Metro Hub', canonical: 'Bengaluru, Karnataka, India' },
                    { city: 'Mumbai', state: 'Maharashtra', country: 'India', badge: 'Financial Capital', canonical: 'Mumbai, Maharashtra, India' },
                    { city: 'New Delhi', state: 'Delhi', country: 'India', badge: 'National Capital', canonical: 'New Delhi, Delhi, India' },
                    { city: 'Hyderabad', state: 'Telangana', country: 'India', badge: 'Tech Hub', canonical: 'Hyderabad, Telangana, India' },
                    { city: 'Pune', state: 'Maharashtra', country: 'India', badge: 'IT Hub', canonical: 'Pune, Maharashtra, India' },
                    { city: 'Chennai', state: 'Tamil Nadu', country: 'India', badge: 'SaaS Capital', canonical: 'Chennai, Tamil Nadu, India' },
                    { city: 'Gurugram', state: 'Haryana', country: 'India', badge: 'NCR Hub', canonical: 'Gurugram, Haryana, India' },
                    { city: 'Noida', state: 'Uttar Pradesh', country: 'India', badge: 'NCR Hub', canonical: 'Noida, Uttar Pradesh, India' },
                    { city: 'Kolkata', state: 'West Bengal', country: 'India', badge: 'Metro Hub', canonical: 'Kolkata, West Bengal, India' },
                    { city: 'Ahmedabad', state: 'Gujarat', country: 'India', badge: 'Commercial Hub', canonical: 'Ahmedabad, Gujarat, India' }
                ]
            },
            {
                region: 'North America',
                items: [
                    { city: 'San Francisco', state: 'California', country: 'United States', badge: 'Silicon Valley', canonical: 'San Francisco, California, United States' },
                    { city: 'New York City', state: 'New York', country: 'United States', badge: 'Global Hub', canonical: 'New York City, New York, United States' },
                    { city: 'Seattle', state: 'Washington', country: 'United States', badge: 'Cloud Hub', canonical: 'Seattle, Washington, United States' },
                    { city: 'Austin', state: 'Texas', country: 'United States', badge: 'Tech Hub', canonical: 'Austin, Texas, United States' },
                    { city: 'Boston', state: 'Massachusetts', country: 'United States', badge: 'Biotech / AI', canonical: 'Boston, Massachusetts, United States' },
                    { city: 'Toronto', state: 'Ontario', country: 'Canada', badge: 'Canada Tech Hub', canonical: 'Toronto, Ontario, Canada' },
                    { city: 'Vancouver', state: 'British Columbia', country: 'Canada', badge: 'Tech Center', canonical: 'Vancouver, British Columbia, Canada' }
                ]
            },
            {
                region: 'Europe & UK',
                items: [
                    { city: 'London', state: 'England', country: 'United Kingdom', badge: 'FinTech Capital', canonical: 'London, England, United Kingdom' },
                    { city: 'Berlin', state: 'Berlin', country: 'Germany', badge: 'Startup Capital', canonical: 'Berlin, Germany' },
                    { city: 'Amsterdam', state: 'North Holland', country: 'Netherlands', badge: 'Tech Hub', canonical: 'Amsterdam, Netherlands' },
                    { city: 'Dublin', state: 'Leinster', country: 'Ireland', badge: 'EMEA HQ Hub', canonical: 'Dublin, Ireland' },
                    { city: 'Paris', state: 'Île-de-France', country: 'France', badge: 'Tech Hub', canonical: 'Paris, France' }
                ]
            },
            {
                region: 'APAC & Middle East',
                items: [
                    { city: 'Singapore', state: 'Central', country: 'Singapore', badge: 'Asia Gateway', canonical: 'Singapore, Singapore' },
                    { city: 'Dubai', state: 'Dubai', country: 'United Arab Emirates', badge: 'MENA Hub', canonical: 'Dubai, United Arab Emirates' },
                    { city: 'Tokyo', state: 'Tokyo', country: 'Japan', badge: 'Asia Metro', canonical: 'Tokyo, Japan' },
                    { city: 'Sydney', state: 'New South Wales', country: 'Australia', badge: 'ANZ Hub', canonical: 'Sydney, New South Wales, Australia' },
                    { city: 'Melbourne', state: 'Victoria', country: 'Australia', badge: 'Tech Center', canonical: 'Melbourne, Victoria, Australia' }
                ]
            }
        ];

        // Layer 1 Instant Quick-Select Popular Hubs
        var popularPillList = [
            { label: 'Bengaluru', canonical: 'Bengaluru, Karnataka, India' },
            { label: 'Mumbai', canonical: 'Mumbai, Maharashtra, India' },
            { label: 'Delhi NCR', canonical: 'New Delhi, Delhi, India' },
            { label: 'Hyderabad', canonical: 'Hyderabad, Telangana, India' },
            { label: 'Pune', canonical: 'Pune, Maharashtra, India' },
            { label: 'London', canonical: 'London, England, United Kingdom' },
            { label: 'New York', canonical: 'New York City, New York, United States' },
            { label: 'Singapore', canonical: 'Singapore, Singapore' }
        ];

        // Layer 1 In-Memory Synonym Aliases
        var citySynonymAliases = {
            'baroda': 'Vadodara',
            'vizag': 'Visakhapatnam',
            'bombay': 'Mumbai',
            'calcutta': 'Kolkata',
            'madras': 'Chennai',
            'poona': 'Pune',
            'bangalore': 'Bengaluru',
            'bengaluru': 'Bengaluru',
            'gurgaon': 'Gurugram',
            'gurugram': 'Gurugram',
            'trivandrum': 'Thiruvananthapuram',
            'cochin': 'Kochi',
            'sf': 'San Francisco',
            'bay area': 'San Francisco',
            'nyc': 'New York City',
            'ny': 'New York City',
            'la': 'Los Angeles',
            'dc': 'Washington',
            'kl': 'Kuala Lumpur',
            'sg': 'Singapore',
            'uae': 'Dubai'
        };

        // Layer 2 Session LRU In-Memory Cache (Capacity: 100, TTL: 30 minutes)
        var cityLruCache = new Map();
        var CITY_CACHE_MAX = 100;
        var CITY_CACHE_TTL = 1800000;
        var citySearchAbortCtrl = null;
        var cityHighlightedIdx = -1;

        function getCachedCities(key) {
            var entry = cityLruCache.get(key);
            if (!entry) return null;
            if (Date.now() - entry.ts > CITY_CACHE_TTL) {
                cityLruCache.delete(key);
                return null;
            }
            cityLruCache.delete(key);
            cityLruCache.set(key, entry);
            return entry.data;
        }

        function setCachedCities(key, data) {
            if (cityLruCache.size >= CITY_CACHE_MAX) {
                var firstKey = cityLruCache.keys().next().value;
                cityLruCache.delete(firstKey);
            }
            cityLruCache.set(key, { ts: Date.now(), data: data });
        }

        function escapeJsStr(val) {
            return String(val || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }

        function isCitySelected(canonical, label) {
            var cNorm = String(canonical || '').toLowerCase().trim();
            var lNorm = String(label || '').toLowerCase().trim();
            return selectedCities.some(function(item) {
                var it = String(item || '').toLowerCase().trim();
                if (it === cNorm || (lNorm && it === lNorm)) return true;
                var cityPart = it.split(/[,|]/)[0].trim();
                if (lNorm && cityPart === lNorm) return true;
                if (cNorm && it.indexOf(cNorm) === 0) return true;
                return false;
            });
        }

        function renderPopularPills() {
            var container = document.getElementById('popularHubsPills');
            if (!container) return;
            container.innerHTML = popularPillList.map(function(p) {
                var active = isCitySelected(p.canonical, p.label);
                var activeClass = active ? ' is-active' : '';
                var prefix = active ? '✓ ' : '+ ';
                return '<button type="button" class="ij-pop-pill' + activeClass + '" onclick="togglePopularPill(\'' + escapeJsStr(p.canonical) + '\', \'' + escapeJsStr(p.label) + '\')">' +
                    prefix + escapeHtml(p.label) + '</button>';
            }).join('');
        }

        function togglePopularPill(canonical, label) {
            var existingIdx = selectedCities.findIndex(function(item) {
                var it = String(item || '').toLowerCase().trim();
                var cNorm = String(canonical || '').toLowerCase().trim();
                var lNorm = String(label || '').toLowerCase().trim();
                return it === cNorm || (lNorm && it === lNorm) || (lNorm && it.split(/[,|]/)[0].trim() === lNorm);
            });
            if (existingIdx !== -1) {
                selectedCities.splice(existingIdx, 1);
            } else {
                if (selectedCities.length >= 15) {
                    showToast('Maximum 15 cities allowed', 'error');
                    return;
                }
                selectedCities.push(canonical);
            }
            renderSelectedCities();
        }

        function renderCuratedDropdown() {
            var dd = document.getElementById('cityDropdown');
            if (!dd) return;
            cityHighlightedIdx = -1;
            var html = '';
            curatedLocationGroups.forEach(function(group) {
                html += '<div class="ij-cd-group">';
                html += '<div class="ij-cd-group-title">' + escapeHtml(group.region) + '</div>';
                group.items.forEach(function(item) {
                    var selected = isCitySelected(item.canonical, item.city);
                    var itemClass = 'ij-cd-item' + (selected ? ' is-selected' : '');
                    var clickAttr = selected ? '' : 'onclick="selectCity(\'' + escapeJsStr(item.canonical) + '\')"';
                    var statusBadge = selected ? '<span class="ij-cd-status">✓ Selected</span>' : '';
                    var subText = item.state ? (item.state + ', ' + item.country) : item.country;
                    html += '<div class="' + itemClass + '" ' + clickAttr + ' data-canonical="' + escapeHtml(item.canonical) + '">';
                    html += '  <div class="ij-cd-left">';
                    html += '    <span class="ij-cd-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>';
                    html += '    <div class="ij-cd-text">';
                    html += '      <span class="ij-cd-name">' + escapeHtml(item.city) + '</span>';
                    html += '      <span class="ij-cd-meta">' + escapeHtml(subText) + '</span>';
                    html += '    </div>';
                    html += '  </div>';
                    html += '  <div class="ij-cd-right">';
                    if (item.badge) {
                        html += '    <span class="ij-cd-badge">' + escapeHtml(item.badge) + '</span>';
                    }
                    html += statusBadge;
                    html += '  </div>';
                    html += '</div>';
                });
                html += '</div>';
            });
            dd.innerHTML = html;
            dd.classList.add('active');
        }

        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            return escapeHtml(text).replace(regex, '<strong style="color:#2563eb;">$1</strong>');
        }

        function renderCitySearchResults(results, query) {
            var dd = document.getElementById('cityDropdown');
            if (!dd) return;
            cityHighlightedIdx = -1;
            if (!results || results.length === 0) {
                var safeQ = escapeHtml(query);
                var safeJsQ = escapeJsStr(query);
                dd.innerHTML = '<div class="ij-cd-empty">' +
                    '<div>No matching cities found for &ldquo;' + safeQ + '&rdquo;</div>' +
                    '<button type="button" class="ij-btn-custom-add" onclick="addCustomCityDirect(\'' + safeJsQ + '\')">' +
                    '+ Add &ldquo;' + safeQ + '&rdquo; as custom location</button>' +
                    '</div>';
                dd.classList.add('active');
                return;
            }
            var html = '<div class="ij-cd-group">';
            html += '<div class="ij-cd-group-title">Search Results (' + results.length + ')</div>';
            results.forEach(function(item) {
                var canonical = item.canonical || item.label || (item.city + (item.state ? ', ' + item.state : '') + (item.country ? ', ' + item.country : ''));
                var selected = isCitySelected(canonical, item.city);
                var itemClass = 'ij-cd-item' + (selected ? ' is-selected' : '');
                var clickAttr = selected ? '' : 'onclick="selectCity(\'' + escapeJsStr(canonical) + '\')"';
                var statusBadge = selected ? '<span class="ij-cd-status">✓ Selected</span>' : '';
                var subText = item.state ? (item.state + ', ' + item.country) : (item.country || '');
                html += '<div class="' + itemClass + '" ' + clickAttr + ' data-canonical="' + escapeHtml(canonical) + '">';
                html += '  <div class="ij-cd-left">';
                html += '    <span class="ij-cd-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>';
                html += '    <div class="ij-cd-text">';
                html += '      <span class="ij-cd-name">' + highlightMatch(item.city, query) + '</span>';
                html += '      <span class="ij-cd-meta">' + escapeHtml(subText) + '</span>';
                html += '    </div>';
                html += '  </div>';
                html += '  <div class="ij-cd-right">';
                if (item.badge) {
                    html += '    <span class="ij-cd-badge">' + escapeHtml(item.badge) + '</span>';
                }
                html += statusBadge;
                html += '  </div>';
                html += '</div>';
            });
            html += '</div>';
            dd.innerHTML = html;
            dd.classList.add('active');
        }

        function parseRawCityData(data) {
            var rawList = [];
            if (data && Array.isArray(data.results)) {
                rawList = data.results;
            } else if (Array.isArray(data)) {
                rawList = data;
            }
            return rawList.map(function(c) {
                if (typeof c === 'object' && c !== null) {
                    var cityName = c.city || (c.label ? c.label.split(',')[0].trim() : '');
                    var stateName = c.state || (c.label ? (c.label.split(',')[1] || '').trim() : '');
                    var countryName = c.country || (c.label ? (c.label.split(',')[2] || '').trim() : '');
                    var canon = c.canonical || c.label || [cityName, stateName, countryName].filter(Boolean).join(', ');
                    var badge = c.badge || (countryName === 'India' ? (stateName || 'India') : countryName);
                    return {
                        id: c.id,
                        city: cityName || canon,
                        state: stateName,
                        country: countryName,
                        label: c.label || canon,
                        canonical: canon,
                        badge: badge
                    };
                } else {
                    var str = String(c || '').trim();
                    var parts = str.split(/[,|]/).map(function(s) { return s.trim(); });
                    var cityName = parts[0] || str;
                    var stateName = parts[1] || '';
                    var countryName = parts[2] || '';
                    return {
                        city: cityName,
                        state: stateName,
                        country: countryName,
                        label: str,
                        canonical: str,
                        badge: countryName || stateName || ''
                    };
                }
            });
        }

        function performCitySearch(query) {
            var trimmed = query.trim();
            var spinner = document.getElementById('citySearchSpinner');
            var clearBtn = document.getElementById('citySearchClear');
            if (clearBtn) clearBtn.style.display = trimmed ? 'inline-flex' : 'none';

            if (!trimmed) {
                if (spinner) spinner.style.display = 'none';
                renderCuratedDropdown();
                return;
            }

            // In-Memory Alias Normalization
            var lookupQuery = trimmed.toLowerCase();
            var normalizedQ = citySynonymAliases[lookupQuery] || trimmed;

            // Check L2 Session LRU Cache
            var cacheKey = normalizedQ.toLowerCase().trim();
            var cached = getCachedCities(cacheKey);
            if (cached) {
                if (spinner) spinner.style.display = 'none';
                renderCitySearchResults(cached, trimmed);
                return;
            }

            // Abort prior in-flight request
            if (citySearchAbortCtrl) {
                citySearchAbortCtrl.abort();
            }
            citySearchAbortCtrl = new AbortController();

            if (spinner) spinner.style.display = 'inline-block';

            var targetUrl = "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(normalizedQ) + "&format=canonical&limit=25";

            fetch(targetUrl, {
                method: 'GET',
                signal: citySearchAbortCtrl.signal,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Network error: ' + res.status);
                return res.json();
            })
            .then(function(data) {
                if (spinner) spinner.style.display = 'none';
                var parsed = parseRawCityData(data);
                setCachedCities(cacheKey, parsed);
                renderCitySearchResults(parsed, trimmed);
            })
            .catch(function(err) {
                if (err.name === 'AbortError') return;
                if (spinner) spinner.style.display = 'none';
                // Fallback to local filter of curated list if network fails
                var filtered = [];
                curatedLocationGroups.forEach(function(g) {
                    g.items.forEach(function(item) {
                        if (item.city.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1 ||
                            item.canonical.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1) {
                            filtered.push(item);
                        }
                    });
                });
                renderCitySearchResults(filtered, trimmed);
            });
        }

        var debounceCitySearch = debounce(function(val) {
            performCitySearch(val);
        }, 180);

        function clearCitySearchInput() {
            var input = document.getElementById('citySearch');
            var clearBtn = document.getElementById('citySearchClear');
            var spinner = document.getElementById('citySearchSpinner');
            if (input) {
                input.value = '';
                input.focus();
            }
            if (clearBtn) clearBtn.style.display = 'none';
            if (spinner) spinner.style.display = 'none';
            renderCuratedDropdown();
        }

        function initCitySearch() {
            var input = document.getElementById('citySearch');
            var dropdown = document.getElementById('cityDropdown');
            var clearBtn = document.getElementById('citySearchClear');
            renderPopularPills();

            if (!input || !dropdown) return;

            input.addEventListener('focus', function() {
                if (!input.value.trim()) {
                    renderCuratedDropdown();
                } else {
                    performCitySearch(input.value);
                }
            });

            input.addEventListener('input', function(e) {
                var v = e.target.value;
                if (clearBtn) clearBtn.style.display = v.trim() ? 'inline-flex' : 'none';
                debounceCitySearch(v);
            });

            input.addEventListener('keydown', function(e) {
                var items = dropdown.querySelectorAll('.ij-cd-item');
                if (!dropdown.classList.contains('active') || items.length === 0) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addCustomCity();
                    }
                    return;
                }
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    cityHighlightedIdx = (cityHighlightedIdx + 1) % items.length;
                    items.forEach(function(el, i) {
                        if (i === cityHighlightedIdx) {
                            el.classList.add('is-highlighted');
                            el.scrollIntoView({ block: 'nearest' });
                        } else {
                            el.classList.remove('is-highlighted');
                        }
                    });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    cityHighlightedIdx = (cityHighlightedIdx - 1 + items.length) % items.length;
                    items.forEach(function(el, i) {
                        if (i === cityHighlightedIdx) {
                            el.classList.add('is-highlighted');
                            el.scrollIntoView({ block: 'nearest' });
                        } else {
                            el.classList.remove('is-highlighted');
                        }
                    });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (cityHighlightedIdx >= 0 && items[cityHighlightedIdx]) {
                        items[cityHighlightedIdx].click();
                    } else {
                        addCustomCity();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('active');
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target) && !(clearBtn && clearBtn.contains(e.target))) {
                    dropdown.classList.remove('active');
                }
            });
        }

        function selectCity(city) {
            if (!city) return;
            var clean = String(city).trim();
            if (isCitySelected(clean, clean.split(',')[0].trim())) {
                showToast('City already selected', 'info');
                return;
            }
            if (selectedCities.length >= 15) {
                showToast('Maximum 15 cities allowed', 'error');
                return;
            }
            selectedCities.push(clean);
            renderSelectedCities();
            var input = document.getElementById('citySearch');
            if (input) input.value = '';
            var clearBtn = document.getElementById('citySearchClear');
            if (clearBtn) clearBtn.style.display = 'none';
            var dd = document.getElementById('cityDropdown');
            if (dd) dd.classList.remove('active');
        }

        function addCustomCityDirect(val) {
            if (!val) return;
            selectCity(val);
        }

        function addCustomCity() {
            var input = document.getElementById('citySearch');
            if (!input) return;
            var val = input.value.trim();
            if (!val) {
                showToast('Please type a city name', 'info');
                return;
            }
            selectCity(val);
        }

        function clearAllCities() {
            selectedCities = [];
            renderSelectedCities();
        }

        function removeCity(city) {
            var cNorm = String(city || '').toLowerCase().trim();
            var lNorm = cNorm.split(/[,|]/)[0].trim();
            selectedCities = selectedCities.filter(function(c) {
                var it = String(c || '').toLowerCase().trim();
                return !(it === cNorm || (lNorm && it === lNorm) || (lNorm && it.split(/[,|]/)[0].trim() === lNorm));
            });
            renderSelectedCities();
        }

        function renderSelectedCities() {
            var countEl = document.getElementById('selectedCount');
            if (countEl) countEl.textContent = selectedCities.length;
            var container = document.getElementById('selectedCities');
            if (!container) return;

            if (selectedCities.length === 0) {
                container.innerHTML = '<span class="ij-no-locs">No locations selected yet. Choose from popular hubs above or search.</span>';
            } else {
                container.innerHTML = selectedCities.map(function(city) {
                    var str = String(city || '').trim();
                    var parts = str.split(/[,|]/).map(function(s) { return s.trim(); });
                    var primaryName = parts[0] || str;
                    var regionMeta = parts.slice(1).join(', ');
                    var metaHtml = regionMeta ? '<span class="ij-chip-region">(' + escapeHtml(regionMeta) + ')</span>' : '';
                    return '<span class="ij-location-chip">' +
                        '<span class="ij-chip-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>' +
                        '<span class="ij-chip-label">' + escapeHtml(primaryName) + '</span>' +
                        metaHtml +
                        '<button type="button" class="ij-chip-del" onclick="removeCity(\'' + escapeJsStr(str) + '\')" title="Remove ' + escapeHtml(primaryName) + '" aria-label="Remove">&times;</button>' +
                        '</span>';
                }).join('');
            }
            syncCities();
            renderPopularPills();
            if (selectedCities.length > 0 && typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('location');
            }
        }

        function syncCities() {
            var hidden = document.getElementById('kt_tagify_work_location');
            if (hidden) {
                hidden.value = JSON.stringify(selectedCities.map(function(c) { return { value: c }; }));
            }
        }

        // ---------- QUALIFICATIONS (UNESCO ISCED-2011 / Enterprise Standard) ----------
        var enterpriseQualTaxonomy = [
            {
                category: "General Eligibility & Universal Standard",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>',
                badgeClass: "ij-qual-badge-universal",
                items: [
                    { name: "Any Qualification", label: "Any Qualification (Open Eligibility)", badge: "Universal", aliases: "any,all,open,qualification,no bar,any qualification" },
                    { name: "Any Degree", label: "Any Degree (All Disciplines / Streams)", badge: "All Streams", aliases: "any degree,degree,all degrees,any discipline,graduate" },
                    { name: "Any Graduation", label: "Any Graduation (Undergraduate Standard)", badge: "Undergraduate", aliases: "any graduation,graduation,graduate,any graduate,bachelors" },
                    { name: "Any Post Graduation", label: "Any Post Graduation (Postgraduate Standard)", badge: "Postgraduate", aliases: "any post graduation,post graduation,postgraduate,pg,masters,any pg" }
                ]
            },
            {
                category: "Bachelor's / Undergraduate Degrees (ISCED 6)",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
                badgeClass: "ij-qual-badge-bachelor",
                items: [
                    { name: "B.Tech/B.E", label: "B.Tech / B.E (Engineering & Technology)", badge: "Engineering / Tech", aliases: "btech,be,b.tech,b.e,bachelor of technology,bachelor of engineering,beng,computer science,cse,it,software" },
                    { name: "B.Sc", label: "B.Sc / B.S (Science, IT & Computing)", badge: "Science / IT", aliases: "bsc,bs,b.sc,b.s,bachelor of science,computer science,bs cs,bsc it,information technology" },
                    { name: "BCA", label: "BCA (Bachelor of Computer Applications)", badge: "IT / Software", aliases: "bca,b.c.a,computer applications,software development,programming" },
                    { name: "B.B.A/ B.M.S", label: "BBA / BMS (Business Administration & Management)", badge: "Business / Mgmt", aliases: "bba,bms,b.b.a,b.m.s,bachelor of business administration,management" },
                    { name: "B.Com", label: "B.Com (Commerce, Accounting & Finance)", badge: "Commerce / Finance", aliases: "bcom,b.com,bachelor of commerce,accounting,finance,banking" },
                    { name: "B.A", label: "B.A (Arts, Humanities & Social Sciences)", badge: "Arts / Humanities", aliases: "ba,b.a,bachelor of arts,humanities,economics,psychology,english" },
                    { name: "B.Des", label: "B.Des (Design, UX/UI & Product)", badge: "Design / UX", aliases: "bdes,b.des,bachelor of design,ui,ux,product design,graphic design" },
                    { name: "B.Arch", label: "B.Arch (Architecture & Urban Planning)", badge: "Architecture", aliases: "barch,b.arch,bachelor of architecture,architecture" },
                    { name: "B.Pharma", label: "B.Pharma (Pharmacy & Healthcare)", badge: "Pharma / Health", aliases: "bpharma,b.pharma,b pharm,pharmacy,bachelor of pharmacy" },
                    { name: "B.Ed", label: "B.Ed (Education & Pedagogy)", badge: "Education", aliases: "bed,b.ed,bachelor of education,teaching" },
                    { name: "BAMS", label: "BAMS (Ayurvedic Medicine & Surgery)", badge: "AYUSH / Medicine", aliases: "bams,ayurveda,ayush" },
                    { name: "BHMS", label: "BHMS (Homeopathic Medicine & Surgery)", badge: "Homeopathy", aliases: "bhms,homeopathy" },
                    { name: "B.U.M.S", label: "B.U.M.S (Unani Medicine & Surgery)", badge: "Unani", aliases: "bums,b.u.m.s,unani" },
                    { name: "BFA", label: "BFA (Fine Arts & Digital Media)", badge: "Fine Arts", aliases: "bfa,b.f.a,fine arts,visual arts" },
                    { name: "BHM", label: "BHM (Hotel & Hospitality Management)", badge: "Hospitality", aliases: "bhm,hotel management,hospitality" },
                    { name: "BVSC", label: "BVSC / DVM (Veterinary Science)", badge: "Veterinary", aliases: "bvsc,veterinary medicine" },
                    { name: "Other Bachelor's Degree", label: "Other Bachelor's / Undergraduate Degree", badge: "Bachelor's / Other", aliases: "other bachelor,other bachelors,unlisted bachelor" }
                ]
            },
            {
                category: "Master's / Postgraduate Degrees (ISCED 7)",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
                badgeClass: "ij-qual-badge-master",
                items: [
                    { name: "MBA/PGDM", label: "MBA / PGDM (Business Administration & Strategy)", badge: "Management / MBA", aliases: "mba,pgdm,master of business administration,executive mba,strategy,business" },
                    { name: "M.Tech", label: "M.Tech / M.E (Engineering & Technology)", badge: "Engineering / Postgrad", aliases: "mtech,me,m.tech,m.e,master of technology,master of engineering" },
                    { name: "MS/M.Sc(Science)", label: "M.S. / M.Sc (Science, IT & Engineering)", badge: "Science / IT", aliases: "ms,msc,master of science,ms cs,msc it,data science,ai" },
                    { name: "MCA", label: "MCA (Master of Computer Applications)", badge: "IT / Software", aliases: "mca,m.c.a,master of computer applications,software engineering" },
                    { name: "M.Com", label: "M.Com (Commerce, Economics & Finance)", badge: "Commerce / Finance", aliases: "mcom,m.com,master of commerce,accounting,finance" },
                    { name: "M.A", label: "M.A (Arts, Humanities & Social Sciences)", badge: "Arts / Humanities", aliases: "ma,m.a,master of arts,humanities,sociology,economics" },
                    { name: "M.Des.", label: "M.Des (Master of Design / UX)", badge: "Design / Postgrad", aliases: "mdes,m.des,master of design,ux design" },
                    { name: "M.Pharma", label: "M.Pharma (Master of Pharmacy)", badge: "Pharma Postgrad", aliases: "mpharma,m.pharma,master of pharmacy" },
                    { name: "M.Ed", label: "M.Ed (Master of Education)", badge: "Education", aliases: "med,m.ed,master of education" },
                    { name: "M.Arch", label: "M.Arch (Master of Architecture)", badge: "Architecture", aliases: "march,m.arch,master of architecture" },
                    { name: "MCM", label: "MCM (Master of Computer Management)", badge: "Management & IT", aliases: "mcm,computer management" },
                    { name: "Integrated PG", label: "Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS)", badge: "Dual Degree", aliases: "integrated pg,dual degree,5 year integrated" },
                    { name: "Other Master's Degree", label: "Other Master's / Postgraduate Degree", badge: "Master's / Other", aliases: "other master,other masters,unlisted master" }
                ]
            },
            {
                category: "Doctorate & Research Degrees (ISCED 8)",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>',
                badgeClass: "ij-qual-badge-doc",
                items: [
                    { name: "Ph.D/Doctorate", label: "Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy)", badge: "Doctorate / Global", aliases: "phd,ph.d,doctorate,doctor of philosophy,dphil,postdoc,research" },
                    { name: "Other Doctorate / Post-Doctoral", label: "Other Doctorate / Post-Doctoral Degree", badge: "Doctorate / Other", aliases: "other doctorate,other phd,dba,post-doctoral" }
                ]
            },
            {
                category: "Associate Degrees & Diplomas (ISCED 5)",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>',
                badgeClass: "ij-qual-badge-dip",
                items: [
                    { name: "Diploma", label: "Diploma / Polytechnic Diploma (2-3 Years)", badge: "Polytechnic / Diploma", aliases: "diploma,polytechnic,associate degree,technical diploma" },
                    { name: "PG Diploma", label: "PG Diploma (Post Graduate Diploma / Certificate)", badge: "PG Diploma", aliases: "pg diploma,pgd,post graduate diploma,pgdiploma" },
                    { name: "HND/HNC", label: "HND / HNC (Higher National Diploma / Certificate)", badge: "UK / Commonwealth", aliases: "hnd,hnc,higher national diploma" },
                    { name: "Other Associate / Diploma", label: "Other Associate / Diploma / Vocational Degree", badge: "Diploma / Other", aliases: "other diploma,other associate,vocational" }
                ]
            },
            {
                category: "Professional, Legal & Medical Degrees",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>',
                badgeClass: "ij-qual-badge-prof",
                items: [
                    { name: "CA (Chartered Accountant)", label: "CA / CPA / CFA / ACCA (Finance & Accounting)", badge: "Finance / Audit", aliases: "ca,cpa,cfa,acca,chartered accountant,accounting,audit" },
                    { name: "CS (Company Secretary)", label: "CS (Company Secretary / Corporate Governance)", badge: "Corporate Governance", aliases: "cs,company secretary,corporate governance,icsi" },
                    { name: "ICWA (CMA)", label: "ICWA / CMA (Cost & Management Accounting)", badge: "Cost Accounting", aliases: "icwa,cma,cost accountant,icmai" },
                    { name: "LLB", label: "LLB / J.D. (Law Degree / Juris Doctor)", badge: "Legal / Law", aliases: "llb,jd,law,bachelor of laws,attorney,lawyer,advocate" },
                    { name: "LLM", label: "LLM (Master of Laws / Legal Postgrad)", badge: "Legal Postgrad", aliases: "llm,master of laws,corporate law" },
                    { name: "MBBS", label: "MBBS / M.D. (General Medicine & Surgery)", badge: "Medicine / Surgery", aliases: "mbbs,doctor,physician,medicine,surgery" },
                    { name: "Medical-MS/MD", label: "MD / MS (Medical Specialty / Master of Surgery)", badge: "Medical Specialty", aliases: "md,ms general surgery,medical specialty,residency" },
                    { name: "DM (Doctor of Medicine)", label: "DM (Doctor of Medicine - Super Specialty)", badge: "Super Specialty", aliases: "dm,super specialty,cardiology,neurology" },
                    { name: "M.Ch", label: "M.Ch (Surgical Super Specialty)", badge: "Surgical Specialty", aliases: "mch,neurosurgery,cardiac surgery" },
                    { name: "BDS", label: "BDS (Bachelor of Dental Surgery)", badge: "Dental", aliases: "bds,dental surgery,dentist" },
                    { name: "MDS", label: "MDS (Master of Dental Surgery)", badge: "Dental Postgrad", aliases: "mds,master of dental surgery" }
                ]
            },
            {
                category: "Secondary & High School (ISCED 3)",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6H5a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h13l4-3.5L18 6Z"/><path d="M12 13v8"/></svg>',
                badgeClass: "ij-qual-badge-sec",
                items: [
                    { name: "12th", label: "12th Standard / Higher Secondary / High School / GED", badge: "High School", aliases: "12th,12th standard,hsc,intermediate,high school,plus two" },
                    { name: "10th", label: "10th Standard / Matriculation / Secondary School", badge: "Secondary", aliases: "10th,10th standard,ssc,matric,matriculation,secondary" },
                    { name: "A levels/Advanced", label: "A Levels / Advanced Level (UK / International)", badge: "A-Levels", aliases: "a levels,a-levels,alevel,advanced level" },
                    { name: "GCSE/GNVQ/O level", label: "GCSE / O-Levels (UK / International)", badge: "GCSE / O-Level", aliases: "gcse,o levels,igcse,o-levels" }
                ]
            },
            {
                category: "Other Qualifications",
                icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
                badgeClass: "ij-qual-badge-sec",
                items: [
                    { name: "Others", label: "Others / Intensive Bootcamp / Specialized Certification", badge: "Bootcamp / Cert", aliases: "others,other,bootcamp,certification,courses" }
                ]
            }
        ];

        // Dynamically merge any unmapped database qualifications from qualWhitelist
        (function mergeUnmappedQuals() {
            var mappedNames = {};
            enterpriseQualTaxonomy.forEach(function(cat) {
                cat.items.forEach(function(it) { mappedNames[it.name.toLowerCase()] = true; });
            });
            var unmappedList = [];
            if (Array.isArray(qualWhitelist)) {
                qualWhitelist.forEach(function(qw) {
                    if (qw && !mappedNames[String(qw).toLowerCase()]) {
                        unmappedList.push({ name: String(qw), label: String(qw), badge: "Other", aliases: String(qw).toLowerCase() });
                    }
                });
            }
            if (unmappedList.length > 0) {
                enterpriseQualTaxonomy.push({
                    category: "Additional Qualifications",
                    icon: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
                    badgeClass: "ij-qual-badge-sec",
                    items: unmappedList
                });
            }
        })();

        var quickQualOptions = [
            { label: 'Any Qualification', value: 'Any Qualification' },
            { label: 'Any Degree', value: 'Any Degree' },
            { label: 'Any Graduation', value: 'Any Graduation' },
            { label: 'Any Post Graduation', value: 'Any Post Graduation' },
            { label: 'B.Tech / B.E', value: 'B.Tech/B.E' },
            { label: 'MBA / PGDM', value: 'MBA/PGDM' },
            { label: 'MCA', value: 'MCA' },
            { label: 'B.Sc', value: 'B.Sc' },
            { label: 'B.Com', value: 'B.Com' },
            { label: 'Diploma', value: 'Diploma' }
        ];

        function renderQuickQualPills() {
            var wrap = document.getElementById('quickQualPills');
            if (!wrap) return;
            wrap.innerHTML = quickQualOptions.map(function(opt) {
                var isActive = qualifications.indexOf(opt.value) !== -1;
                var cls = 'ij-qual-pill' + (isActive ? ' is-active' : '');
                var icon = isActive
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
                return '<button type="button" class="' + cls + '" onclick="toggleQualPill(\'' + String(opt.value).replace(/'/g, "\\'") + '\')">' +
                    icon + escapeHtml(opt.label) +
                '</button>';
            }).join('');
        }

        function toggleQualPill(val) {
            if (qualifications.indexOf(val) !== -1) {
                removeQualification(val);
            } else {
                addQualification(val);
            }
        }

        var focusedQualIndex = -1;

        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            var idx = text.toLowerCase().indexOf(query.toLowerCase());
            if (idx === -1) return escapeHtml(text);
            var before = text.substring(0, idx);
            var match = text.substring(idx, idx + query.length);
            var after = text.substring(idx + query.length);
            return escapeHtml(before) + '<span class="ij-qual-highlight">' + escapeHtml(match) + '</span>' + escapeHtml(after);
        }

        function renderQualDropdown(query) {
            var dropdown = document.getElementById('qualDropdown');
            if (!dropdown) return;
            var q = (query || '').trim().toLowerCase();
            focusedQualIndex = -1;

            var html = '';
            var totalMatches = 0;

            enterpriseQualTaxonomy.forEach(function(cat) {
                var filtered = cat.items.filter(function(item) {
                    if (!q) return true;
                    return item.name.toLowerCase().indexOf(q) !== -1 ||
                        item.label.toLowerCase().indexOf(q) !== -1 ||
                        (item.aliases && item.aliases.toLowerCase().indexOf(q) !== -1) ||
                        cat.category.toLowerCase().indexOf(q) !== -1 ||
                        item.badge.toLowerCase().indexOf(q) !== -1;
                });

                if (filtered.length > 0) {
                    html += '<div class="ij-qual-group">';
                    html += '<div class="ij-qual-group-title">' + cat.icon + escapeHtml(cat.category) + '</div>';
                    filtered.forEach(function(item) {
                        totalMatches++;
                        var isSel = qualifications.indexOf(item.name) !== -1;
                        var itemCls = 'ij-qual-item' + (isSel ? ' is-selected' : '');
                        var icon = isSel
                            ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'
                            : '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
                        var badgeCls = 'ij-qual-item-badge ' + (cat.badgeClass || '');
                        html += '<div class="' + itemCls + '" data-qual-val="' + escapeHtml(item.name) + '" onclick="addQualification(\'' + String(item.name).replace(/'/g, "\\'") + '\')">' +
                            '<div class="ij-qual-item-left">' +
                                '<span class="ij-qual-item-icon">' + icon + '</span>' +
                                '<span class="ij-qual-item-name">' + highlightMatch(item.label, q) + '</span>' +
                            '</div>' +
                            '<span class="' + badgeCls + '">' + escapeHtml(item.badge) + '</span>' +
                        '</div>';
                    });
                    html += '</div>';
                }
            });

            if (totalMatches === 0 && q) {
                html = '<div class="ij-cd-empty">' +
                    '<span>No matching qualifications found for "<strong>' + escapeHtml(q) + '</strong>".</span>' +
                    '<button type="button" class="ij-btn-custom-add" onclick="addCustomQual()">' +
                        '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>' +
                        'Add "' + escapeHtml(q) + '" as custom qualification' +
                    '</button>' +
                '</div>';
            }

            dropdown.innerHTML = html;
        }

        function clearQualSearchInput() {
            var input = document.getElementById('qualSearch');
            var clearBtn = document.getElementById('qualSearchClear');
            if (input) {
                input.value = '';
                input.focus();
            }
            if (clearBtn) clearBtn.style.display = 'none';
            renderQualDropdown('');
        }

        function initQualSearch() {
            var input = document.getElementById('qualSearch');
            var dropdown = document.getElementById('qualDropdown');
            var clearBtn = document.getElementById('qualSearchClear');
            if (!input || !dropdown) return;

            renderQuickQualPills();

            input.addEventListener('focus', function() {
                renderQualDropdown(input.value);
                dropdown.classList.add('active');
            });

            input.addEventListener('input', function(e) {
                var v = e.target.value.trim();
                if (clearBtn) clearBtn.style.display = v ? 'inline-flex' : 'none';
                renderQualDropdown(v);
                dropdown.classList.add('active');
            });

            input.addEventListener('keydown', function(e) {
                var items = dropdown.querySelectorAll('.ij-qual-item');
                if (!items.length) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addCustomQual();
                    }
                    return;
                }
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    focusedQualIndex = Math.min(focusedQualIndex + 1, items.length - 1);
                    items.forEach(function(el, i) { el.classList.toggle('is-focused', i === focusedQualIndex); });
                    if (items[focusedQualIndex]) items[focusedQualIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    focusedQualIndex = Math.max(focusedQualIndex - 1, 0);
                    items.forEach(function(el, i) { el.classList.toggle('is-focused', i === focusedQualIndex); });
                    if (items[focusedQualIndex]) items[focusedQualIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (focusedQualIndex >= 0 && items[focusedQualIndex]) {
                        var val = items[focusedQualIndex].getAttribute('data-qual-val');
                        if (val) addQualification(val);
                    } else {
                        addCustomQual();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('active');
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target) && (!clearBtn || !clearBtn.contains(e.target))) {
                    dropdown.classList.remove('active');
                }
            });
        }

        function addQualification(qual) {
            qual = String(qual || '').trim();
            if (!qual) return;
            if (qualifications.indexOf(qual) !== -1) {
                var inputEl = document.getElementById('qualSearch');
                if (inputEl) inputEl.value = '';
                var clr = document.getElementById('qualSearchClear');
                if (clr) clr.style.display = 'none';
                document.getElementById('qualDropdown').classList.remove('active');
                return;
            }
            if (qualifications.length >= 5) {
                showToast('Maximum 5 qualifications allowed', 'error');
                return;
            }
            qualifications.push(qual);
            renderQualifications();
            var inputEl = document.getElementById('qualSearch');
            if (inputEl) inputEl.value = '';
            var clr = document.getElementById('qualSearchClear');
            if (clr) clr.style.display = 'none';
            document.getElementById('qualDropdown').classList.remove('active');
        }

        function addCustomQual() {
            var input = document.getElementById('qualSearch');
            if (!input) return;
            var val = input.value.trim();
            if (val) addQualification(val);
        }

        function removeQualification(qual) {
            qualifications = qualifications.filter(function(q) { return q !== qual; });
            renderQualifications();
        }

        function clearAllQualifications() {
            qualifications = [];
            renderQualifications();
        }

        function renderQualifications() {
            var list = document.getElementById('qualList');
            var countEl = document.getElementById('qualSelectedCount');
            if (countEl) countEl.textContent = qualifications.length;

            if (list) {
                if (qualifications.length === 0) {
                    list.innerHTML = '<span class="ij-no-locs">No qualifications selected yet. Pick from above or search to add.</span>';
                } else {
                    list.innerHTML = qualifications.map(function(q) {
                        var isUni = (['Any Qualification', 'Any Degree', 'Any Graduation', 'Any Post Graduation'].indexOf(q) !== -1);
                        var extraClass = isUni ? ' is-universal' : '';
                        return '<span class="ij-location-chip ij-qual-chip' + extraClass + '">' +
                            '<span class="ij-chip-pin"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>' +
                            '<span class="ij-chip-label">' + escapeHtml(q) + '</span>' +
                            '<button type="button" class="ij-chip-del" onclick="removeQualification(\'' + String(q).replace(/'/g, "\\'") + '\')" title="Remove">&times;</button>' +
                        '</span>';
                    }).join('');
                }
            }
            syncQual();
            renderQuickQualPills();
            if (qualifications.length > 0 && typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('qualification');
            }
        }

        function syncQual() {
            var hidden = document.getElementById('qualification');
            if (hidden) {
                hidden.value = JSON.stringify(qualifications.map(function(q) { return { value: q }; }));
            }
        }

        // ---------- SKILLS ----------
        function initSkillsInput() {
            var input = document.getElementById('skillsInput');
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); var val = input.value.trim().replace(',', ''); if (val) addSkill(val); }
                if (e.key === 'Backspace' && input.value === '' && skills.length > 0) removeSkill(skills[skills.length - 1]);
            });
            input.addEventListener('blur', function() { var val = input.value.trim(); if (val) addSkill(val); });
        }
        function addSkill(skill) { skill = skill.replace(/[^a-zA-Z0-9 +#.\/&()_-]/g, '').replace(/\s+/g, ' ').trim(); if (!skill || skills.indexOf(skill) !== -1) return; if (skills.length >= 10) { showToast('Maximum 10 skills allowed', 'error'); return; } skills.push(skill); renderSkills(); }
        function addSuggestedSkill(el, skill) { addSkill(skill); el.classList.add('selected'); }
        function removeSkill(skill) { skills = skills.filter(function(s) { return s !== skill; }); renderSkills(); document.querySelectorAll('#step2 .chip').forEach(function(el) { if (el.textContent.indexOf(skill) !== -1) el.classList.remove('selected'); }); }
        function renderSkills() {
            var wrapper = document.getElementById('skillsWrapper');
            var input = document.getElementById('skillsInput');
            var tags = skills.map(function(s) { return '<span class="tag">' + escapeHtml(s) + ' <button type="button" class="tag-remove" onclick="removeSkill(\'' + String(s).replace(/'/g, "\\'") + '\')">&times;</button></span>'; }).join('');
            wrapper.querySelectorAll('.tag').forEach(function(t) { t.remove(); });
            input.insertAdjacentHTML('beforebegin', tags);
            input.value = '';
            syncSkills();
            if (skills.length > 0 && typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('skills');
            }
        }
        function syncSkills() { document.getElementById('tags').value = JSON.stringify(skills.map(function(s) { return { value: s }; })); }

        // ---------- RICH TEXT EDITOR ----------
        function formatText(command) { document.execCommand(command, false, null); document.getElementById('jobDescription').focus(); syncDescription(); }
        function syncDescription() { var ed = document.getElementById('jobDescription'); var hid = document.getElementById('kt_docs_tinymce_hidden'); if (ed && hid) hid.value = ed.innerHTML; }
        function initCharCount() {
            var editor = document.getElementById('jobDescription');
            editor.addEventListener('input', function() {
                var text = editor.innerText || '';
                document.getElementById('charCount').textContent = text.length + ' characters';
                syncDescription();
                if (text.trim() && typeof clearFieldValidationErrors === 'function') {
                    clearFieldValidationErrors('jobDescription');
                }
            });
        }

        // ---------- QUESTIONS ----------
        function renderQuestions() {
            var list = document.getElementById('questionsList');
            list.innerHTML = questions.map(function(q, index) {
                var cfg = typeConfig[q.type] || typeConfig.short;
                return '' +
                '<div class="question-item animate-in" draggable="true" data-index="' + index + '" data-id="' + q.id + '">' +
                    '<div class="drag-handle">' + Array(6).fill('<div class="drag-dot"></div>').join('') + '</div>' +
                    '<div class="question-content">' +
                        '<div class="question-text">' + escapeHtml(q.text) + '</div>' +
                        '<div class="question-meta">' +
                            '<span class="badge ' + cfg.class + '"><i class="fas fa-' + cfg.icon + '"></i> ' + cfg.label + '</span>' +
                            '<span class="badge ' + (q.required ? 'badge-required' : 'badge-optional') + '">' + (q.required ? 'Required' : 'Optional') + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="question-actions">' +
                        '<button type="button" class="btn-icon" onclick="editQuestion(' + q.id + ')"><i class="fas fa-pencil-alt"></i> Edit</button>' +
                        '<button type="button" class="btn-icon btn-delete" onclick="deleteQuestion(' + q.id + ')"><i class="fas fa-trash-alt"></i></button>' +
                        '<button type="button" class="btn-dropdown" onclick="toggleDropdown(this)"><i class="fas fa-chevron-down"></i></button>' +
                        '<div class="dropdown-menu">' +
                            '<button type="button" class="dropdown-item" onclick="duplicateQuestion(' + q.id + ')"><i class="fas fa-copy"></i> Duplicate</button>' +
                            '<button type="button" class="dropdown-item" onclick="moveQuestion(' + q.id + ', -1)"><i class="fas fa-arrow-up"></i> Move Up</button>' +
                            '<button type="button" class="dropdown-item" onclick="moveQuestion(' + q.id + ', 1)"><i class="fas fa-arrow-down"></i> Move Down</button>' +
                            '<div class="dropdown-divider"></div>' +
                            '<button type="button" class="dropdown-item text-danger" onclick="deleteQuestion(' + q.id + ')"><i class="fas fa-trash-alt"></i> Delete</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }).join('');
            setupDragAndDrop();
            syncQuestions();
        }
        function syncQuestions() {
            var holder = document.getElementById('customTitleHidden');
            holder.innerHTML = questions.map(function(q) { return '<input type="hidden" name="custom_title[]" value="' + escapeHtml(q.text).replace(/"/g, '&quot;') + '">'; }).join('');
            document.getElementById('customTitleId').value = questions.length ? questions.length - 1 : 0;
        }
        function renderTemplates() {
            var list = document.getElementById('templateList');
            list.innerHTML = templates.map(function(t) {
                return '<div class="template-item" onclick="addFromTemplate(\'' + escapeHtml(t.name).replace(/'/g, "\\'") + '\', \'' + t.type + '\')">' +
                    '<div class="template-icon ' + t.color + '"><i class="fas fa-' + t.icon + '"></i></div>' +
                    '<span class="template-name">' + escapeHtml(t.name) + '</span>' +
                    '<button type="button" class="btn-add-template" onclick="event.stopPropagation(); addFromTemplate(\'' + escapeHtml(t.name).replace(/'/g, "\\'") + '\', \'' + t.type + '\')"><i class="fas fa-plus"></i></button>' +
                '</div>';
            }).join('');
        }
        function setupDragAndDrop() {
            var items = document.querySelectorAll('.question-item');
            items.forEach(function(item) {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragend', handleDragEnd);
                item.addEventListener('dragenter', handleDragEnter);
                item.addEventListener('dragleave', handleDragLeave);
            });
        }
        function handleDragStart(e) { dragStartIndex = +this.dataset.index; this.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', dragStartIndex); }
        function handleDragOver(e) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; }
        function handleDragEnter(e) { e.preventDefault(); this.style.borderColor = 'var(--primary)'; }
        function handleDragLeave(e) { this.style.borderColor = ''; }
        function handleDrop(e) {
            e.preventDefault();
            var dragEndIndex = +this.dataset.index;
            if (dragStartIndex !== dragEndIndex) {
                var item = questions.splice(dragStartIndex, 1)[0];
                questions.splice(dragEndIndex, 0, item);
                renderQuestions(); showToast('Question reordered', 'success');
            }
            this.style.borderColor = '';
        }
        function handleDragEnd() { this.classList.remove('dragging'); document.querySelectorAll('.question-item').forEach(function(item) { item.style.borderColor = ''; }); }
        function moveQuestion(id, direction) {
            var index = questions.findIndex(function(q) { return q.id === id; });
            var newIndex = index + direction;
            if (newIndex >= 0 && newIndex < questions.length) {
                var item = questions.splice(index, 1)[0];
                questions.splice(newIndex, 0, item);
                renderQuestions(); showToast('Question reordered', 'success');
            }
        }
        function openQuestionModal() {
            editingQuestionId = null;
            document.getElementById('modalTitle').textContent = 'Add Screening Question';
            document.getElementById('questionInput').value = '';
            document.getElementById('typeInput').value = 'short';
            document.getElementById('modalActionBtn').textContent = 'Add Question';
            document.getElementById('questionOptions').value = '';
            document.getElementById('optionsGroup').style.display = 'none';
            isRequired = true; updateToggleUI(); togglePhoneField();
            document.getElementById('questionModal').classList.add('active');
            document.getElementById('questionInput').focus();
        }
        function closeQuestionModal() { document.getElementById('questionModal').classList.remove('active'); document.querySelectorAll('.dropdown-menu').forEach(function(d) { d.classList.remove('show'); }); }
        function toggleRequired() { isRequired = !isRequired; updateToggleUI(); }
        function updateToggleUI() { var toggle = document.getElementById('requiredToggle'); var label = document.getElementById('requiredLabel'); toggle.classList.toggle('active', isRequired); label.textContent = isRequired ? 'Required' : 'Optional'; }
        function togglePhoneField() {
            var type = document.getElementById('typeInput').value;
            document.getElementById('phoneGroup').style.display = type === 'phone' ? 'block' : 'none';
            document.getElementById('optionsGroup').style.display = type === 'multiple' ? 'block' : 'none';
        }
        function editQuestion(id) {
            var q = questions.find(function(q) { return q.id === id; });
            if (!q) return;
            editingQuestionId = id;
            document.getElementById('modalTitle').textContent = 'Edit Screening Question';
            document.getElementById('questionInput').value = q.text;
            document.getElementById('typeInput').value = q.type;
            document.getElementById('modalActionBtn').textContent = 'Save Changes';
            isRequired = q.required; updateToggleUI(); togglePhoneField();
            document.getElementById('questionModal').classList.add('active');
        }
        function saveQuestion() {
            var text = document.getElementById('questionInput').value.trim();
            var type = document.getElementById('typeInput').value;
            if (!text) { showToast('Please enter a question', 'error'); return; }
            if (editingQuestionId) {
                var idx = questions.findIndex(function(q) { return q.id === editingQuestionId; });
                if (idx > -1) { questions[idx].text = text; questions[idx].type = type; questions[idx].required = isRequired; }
                showToast('Question updated successfully', 'success');
            } else {
                var newId = Math.max.apply(null, questions.map(function(q) { return q.id; }).concat([0])) + 1;
                questions.push({ id: newId, text: text, type: type, required: isRequired });
                showToast('Question added successfully', 'success');
            }
            renderQuestions(); closeQuestionModal();
        }
        function deleteQuestion(id) { if (!confirm('Are you sure you want to delete this question?')) return; questions = questions.filter(function(q) { return q.id !== id; }); renderQuestions(); showToast('Question deleted', 'success'); }
        function duplicateQuestion(id) {
            var q = questions.find(function(q) { return q.id === id; });
            if (!q) return;
            var newId = Math.max.apply(null, questions.map(function(q) { return q.id; }).concat([0])) + 1;
            var index = questions.findIndex(function(qq) { return qq.id === id; });
            questions.splice(index + 1, 0, Object.assign({}, q, { id: newId }));
            renderQuestions(); showToast('Question duplicated', 'success');
        }
        function addFromTemplate(name, type) {
            var newId = Math.max.apply(null, questions.map(function(q) { return q.id; }).concat([0])) + 1;
            questions.push({ id: newId, text: name, type: type, required: true });
            renderQuestions(); showToast(name + ' question added', 'success');
        }
        function generateAIQuestions() {
            var btn = document.getElementById('aiGenerateBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner"></i> Generating...';
            setTimeout(function() {
                var aiQuestions = [
                    { text: "Describe your experience relevant to this role.", type: "short", required: true },
                    { text: "Are you available to join within the notice period?", type: "yesno", required: true },
                    { text: "Rate your proficiency for the key skill (1-10).", type: "number", required: false }
                ];
                var maxId = Math.max.apply(null, questions.map(function(q) { return q.id; }).concat([0]));
                aiQuestions.forEach(function(q) { maxId++; questions.push(Object.assign({}, q, { id: maxId })); });
                renderQuestions();
                btn.classList.remove('loading');
                btn.innerHTML = '<i class="fas fa-sparkles"></i> Generate Questions with AI';
                showToast('3 AI-generated questions added!', 'success');
            }, 1500);
        }
        function toggleDropdown(btn) {
            var menu = btn.nextElementSibling;
            var isOpen = menu.classList.contains('show');
            document.querySelectorAll('.dropdown-menu').forEach(function(d) { d.classList.remove('show'); });
            if (!isOpen) menu.classList.add('show');
        }

        // ---------- REVIEW (Step 4) ----------
        function getSelText(id) {
            var el = document.getElementById(id);
            if (!el) return '';
            if (el.tagName === 'SELECT') {
                var opt = el.options[el.selectedIndex];
                if (!opt) return '';
                return opt.getAttribute('data-code') || opt.text || '';
            }
            return el.value || '';
        }
        function setText(id, val) { var el = document.getElementById(id); if (el) el.textContent = (val && String(val).trim() !== '') ? val : '-'; }
        function clientName() {
            if (window.JeroClientSelectEngine && typeof window.JeroClientSelectEngine.getSelectedName === 'function') {
                var name = window.JeroClientSelectEngine.getSelectedName();
                if (name) return name;
            }
            var el = document.getElementById('client_id');
            if (!el) return '';
            if (el.tagName === 'SELECT') { return el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : ''; }
            // type==3 readonly input sits just before the hidden client_id
            var ro = document.querySelector('input.form-input[readonly]');
            return ro ? ro.value : '';
        }
        function salaryText() {
            var c1 = getSelText('min_salary_currency'); var s1 = document.getElementById('minimum_salary').value;
            var s2 = document.getElementById('maximum_salary').value; var mode = getSelText('min_sal_mode');
            if (!s1 && !s2) return '';
            return (c1 + ' ' + (s1 || '') + ' - ' + c1 + ' ' + (s2 || '') + ' ' + mode).trim();
        }
        function expText() {
            var a = getSelText('min_exp'); var b = getSelText('max_exp');
            if (!a && !b) return ''; return (a || '') + ' - ' + (b || '');
        }
        function populateReview() {
            var cities = selectedCities.map(function(c) { return String(c).split(' | ')[0]; }).join(', ');
            var titleVal = (document.getElementById('title') ? document.getElementById('title').value : '') || '-';
            setText('rvClient', clientName());
            setText('rvDepartment', getSelText('category_id'));
            setText('rvJobTitle', titleVal);
            setText('rvJobType', getSelText('employment_type'));
            setText('rvExp', expText());
            setText('rvCompensation', salaryText());

            setText('rvLocation', cities);
            setText('rvWorkMode2', getSelText('work_from_home'));
            setText('rvShift', getSelText('work_shifts'));
            setText('rvEmployment', getSelText('employment_type'));

            // skills
            var countEl = document.getElementById('rvSkillsCount');
            if (countEl) countEl.textContent = '(' + skills.length + ')';
            var skillsEl = document.getElementById('rvSkills');
            if (skillsEl) {
                skillsEl.innerHTML = skills.map(function(s) {
                    return '<span class="r4-skill-pill">' + escapeHtml(s) + '</span>';
                }).join('') || '<span style="color:var(--text-muted);font-size:12px;">No skills added</span>';
            }

            // description
            var descBox = document.getElementById('jobDescription');
            var descPlain = (descBox ? (descBox.innerText || '') : '').trim();
            setText('rvDescription', descPlain || '-');

            // questions
            var qCountEl = document.getElementById('rvQuestionsCount');
            if (qCountEl) qCountEl.textContent = '(' + questions.length + ')';
            var qEl = document.getElementById('rvQuestions');
            if (qEl) {
                qEl.innerHTML = questions.map(function(q, i) {
                    var isReq = q.required !== false;
                    var badgeClass = isReq ? 'r4-q-badge-req' : 'r4-q-badge-opt';
                    var badgeText = isReq ? 'Required' : 'Optional';
                    return '<div class="r4-question-row">' +
                        '<span class="r4-question-num">' + (i + 1) + '</span>' +
                        '<span class="r4-question-text">' + escapeHtml(q.text) + '</span>' +
                        '<span class="r4-question-badge ' + badgeClass + '">' + badgeText + '</span>' +
                    '</div>';
                }).join('') || '<span style="color:var(--text-muted);font-size:12px;">No screening questions</span>';
            }

            // badges (fallback/hidden)
            setText('badgeEmployment', getSelText('employment_type') || 'Job Type');
            setText('badgeWorkMode', getSelText('work_from_home') || 'Work Mode');
            setText('badgeExp', expText() || 'Experience');
            setText('badgeLocation', cities || 'Location');
            setText('badgeSalary', salaryText() || 'Salary');

            // compact summary (fallback/hidden)
            setText('csTitle', titleVal);
            setText('csLocation', cities);
            setText('csExp', expText());
            setText('csEmployment', getSelText('employment_type'));
            setText('csWorkMode', getSelText('work_from_home'));
            setText('csSalary', salaryText());
        }

        window.showCoinRulesModal = function() {
            Swal.fire({
                title: '<div style="font-size:16px;font-weight:700;color:#0f172a;display:flex;align-items:center;justify-content:center;gap:8px;"><span>🪙</span> Coin Consumption Rules</div>',
                html: '<div style="text-align:left;font-size:12px;color:#334155;line-height:1.6;display:flex;flex-direction:column;gap:10px;padding:4px 8px;">' +
                    '<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:10px 12px;">' +
                        '<div style="font-weight:600;color:#1e40af;margin-bottom:2px;">Marketplace Daily Deduction</div>' +
                        '<div><strong>2 Coins / Day</strong> when published on Agency Marketplace.</div>' +
                    '</div>' +
                    '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;">' +
                        '<div style="font-weight:600;color:#0f172a;margin-bottom:2px;">Per Candidate Contact</div>' +
                        '<div><strong>1 Coin / Candidate Contacted</strong> through the marketplace.</div>' +
                    '</div>' +
                    '<div style="background:#fefce8;border:1px solid #fef08a;border-radius:6px;padding:10px 12px;">' +
                        '<div style="font-weight:600;color:#854d0e;margin-bottom:2px;">Duration &amp; Lifecycle</div>' +
                        '<div>Job automatically closes after <strong>60 days</strong>. You can pause or extend anytime.</div>' +
                    '</div>' +
                '</div>',
                confirmButtonText: 'Got it',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    popup: 'swal2-enterprise-popup'
                }
            });
        };

        // ---------- SYNC ALL HIDDEN INPUTS ----------
        function syncAll() { syncCities(); syncSkills(); syncQual(); syncDescription(); syncQuestions(); }

        // ---------- PUBLISH (real submit) ----------
        function publishJob() {
            if (typeof validateAllSteps === 'function' && !validateAllSteps()) return;

            var checkboxes = document.querySelectorAll('.summary-check input');
            var allChecked = true; checkboxes.forEach(function(cb) { if (!cb.checked) allChecked = false; });
            if (!allChecked) { showToast('Please accept all compliance checks', 'error'); return; }

            syncAll();

            var data = new FormData();
            var form_data = $('#kt_contact_add_form').serializeArray();
            $.each(form_data, function(key, input) { data.append(input.name, input.value); });
            data.append('detail_description', document.getElementById('jobDescription').innerHTML);

            var $btn = $('.submit-card .btn-primary');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...');

            if (!navigator.onLine) {
                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                $btn.prop('disabled', false).html('Publish Job');
                return;
            }

            $.ajax({
                url: __submitUrl,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST", processData: false, contentType: false, data: data, redirect: true,
                success: function(data) {
                    if (data.status == 'success') {
                         $btn.prop('disabled', false).html('Publish Job');
                        showToast('Job saved successfully!', 'success');
                        setTimeout(function() { window.location = __indexUrl; }, 600);
                    } else {
                        $btn.prop('disabled', false).html('Publish Job');
                        if (!navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        }
                        showToast((data && data.message) ? data.message : 'Could not save the job. Please check the details.', 'error');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('Publish Job');
                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                    } else {
                        showToast('Server error while saving the job. Please try again.', 'error');
                    }
                }
            });
        }
        function saveDraft() { syncAll(); showToast('Draft synced. Use Publish to save the job.', 'info'); }

        // ---------- UTILITIES ----------
        function escapeHtml(text) { var div = document.createElement('div'); div.textContent = text == null ? '' : text; return div.innerHTML; }
        function showToast(message, type) {
            type = type || 'info';
            var container = document.getElementById('toastContainer');
            var toast = document.createElement('div'); toast.className = 'jr-toast ' + type;
            var icons = {
                success: '<svg class="jr-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>',
                error: '<svg class="jr-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>',
                info: '<svg class="jr-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>'
            };
            toast.innerHTML = (icons[type] || icons.info) + '<span>' + escapeHtml(message) + '</span>';
            container.appendChild(toast);
            setTimeout(function() { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(function() { toast.remove(); }, 300); }, 3000);
        }

        document.addEventListener('click', function(e) { if (!e.target.closest('.question-actions')) { document.querySelectorAll('.dropdown-menu').forEach(function(d) { d.classList.remove('show'); }); } });
        (function() { var m = document.getElementById('questionModal'); if (m) m.addEventListener('click', function(e) { if (e.target === e.currentTarget) closeQuestionModal(); }); })();

        // ==========================================================================
        // GENDER DIVERSITY CUSTOM DROPDOWN
        // Purpose: Accessible custom dropdown controller for Gender Diversity.
        // Handles: Click toggle, outside click dismiss, keyboard navigation
        //          (ArrowUp/Down, Enter, Space, Escape, Tab, Home, End), screen bounds
        //          overflow protection, and synchronization with hidden #required_columns input.
        // Tenancy: Client UI behavior only, scoped strictly to #genderDropdownWrapper.
        // ==========================================================================
        (function initGenderDiversityDropdown() {
            function setupGenderDropdown() {
                var wrapper = document.getElementById('genderDropdownWrapper');
                if (!wrapper || wrapper.dataset.initialized === 'true') return;
                wrapper.dataset.initialized = 'true';

                var trigger = document.getElementById('genderDiversityTrigger');
                var menu = document.getElementById('genderDiversityMenu');
                var input = document.getElementById('required_columns');
                var triggerBadge = document.getElementById('genderTriggerBadge');
                var triggerText = document.getElementById('genderTriggerText');
                if (!trigger || !menu || !input) return;

                var options = Array.from(menu.querySelectorAll('.ij-gender-option'));

                var optionConfigs = {
                    'No Preference': {
                        title: 'No Preference',
                        desc: 'Open to all candidates',
                        badgeClass: 'ij-badge-neutral',
                        badgeHtml: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
                    },
                    'Male Only': {
                        title: 'Male Only',
                        desc: 'Prefer male candidates only',
                        badgeClass: 'ij-badge-male',
                        badgeHtml: '<svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><circle cx="12" cy="4.5" r="2.5"/><path d="M8.5 8.5C7.7 8.5 7 9.2 7 10v4.2c0 .5.4.8.9.8h1.1v6c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-5h0v5c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-6h1.1c.5 0 .9-.3.9-.8V10c0-.8-.7-1.5-1.5-1.5h-7z"/></svg>'
                    },
                    'Female Only': {
                        title: 'Female Only',
                        desc: 'Prefer female candidates only',
                        badgeClass: 'ij-badge-female',
                        badgeHtml: '<svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><circle cx="12" cy="4.5" r="2.5"/><path d="M10 8.5c-.8 0-1.5.7-1.5 1.5l-1 5.2c-.1.5.3.8.8.8h1.7v5c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-5h1.7c.5 0 .9-.3.8-.8l-1-5.2c0-.8-.7-1.5-1.5-1.5h-4z"/>                </svg>'
                    },
                    'Balanced / Equal Opportunity': {
                        title: 'Balanced / Equal Opportunity',
                        desc: 'Encourage gender diversity (M/F/Other)',
                        badgeClass: 'ij-badge-balanced',
                        badgeHtml: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
                    },
                    'Other / Prefer Not to Say': {
                        title: 'Other / Prefer Not to Say',
                        desc: 'Open to all, no specific preference',
                        badgeClass: 'ij-badge-other',
                        badgeHtml: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="8" r="4"/><path d="M4 20v-2a5 5 0 0 1 10 0v2"/><path d="M18 11v6"/><path d="M21 14h-6"/></svg>'
                    }
                };

                var focusedIndex = -1;

                function positionMenu() {
                    menu.style.left = '0';
                    menu.style.right = 'auto';
                    var rect = menu.getBoundingClientRect();
                    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                    if (rect.right > viewportWidth - 12) {
                        menu.style.left = 'auto';
                        menu.style.right = '0';
                    }
                }

                function openMenu() {
                    menu.style.display = 'flex';
                    void menu.offsetHeight;
                    menu.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                    trigger.classList.add('is-active');
                    positionMenu();

                    var selectedIdx = options.findIndex(function(opt) {
                        return opt.getAttribute('aria-selected') === 'true';
                    });
                    focusedIndex = selectedIdx >= 0 ? selectedIdx : 0;
                    updateFocus();
                }

                function closeMenu(focusTrigger) {
                    menu.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                    trigger.classList.remove('is-active');
                    clearFocus();
                    setTimeout(function() {
                        if (trigger.getAttribute('aria-expanded') !== 'true') {
                            menu.style.display = 'none';
                        }
                    }, 150);
                    if (focusTrigger) {
                        trigger.focus();
                    }
                }

                function updateFocus() {
                    options.forEach(function(opt, idx) {
                        if (idx === focusedIndex) {
                            opt.classList.add('is-focused');
                            if (typeof opt.scrollIntoView === 'function') {
                                opt.scrollIntoView({ block: 'nearest' });
                            }
                        } else {
                            opt.classList.remove('is-focused');
                        }
                    });
                }

                function clearFocus() {
                    options.forEach(function(opt) {
                        opt.classList.remove('is-focused');
                    });
                    focusedIndex = -1;
                }

                function selectOption(val) {
                    var config = optionConfigs[val];
                    if (!config) return;

                    input.value = val;
                    if (triggerText) {
                        triggerText.textContent = config.title;
                        triggerText.classList.add('has-value');
                    }
                    if (triggerBadge) {
                        triggerBadge.className = 'ij-gender-trigger-badge ' + config.badgeClass;
                        triggerBadge.innerHTML = config.badgeHtml;
                    }
                    trigger.classList.add('has-selected');

                    options.forEach(function(opt) {
                        var isMatch = opt.getAttribute('data-value') === val;
                        opt.setAttribute('aria-selected', isMatch ? 'true' : 'false');
                        opt.classList.toggle('is-selected', isMatch);
                    });

                    // Trigger input and change events for any observers or form serializations
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    if (window.jQuery) {
                        window.jQuery(input).trigger('change');
                    }

                    closeMenu(true);
                }

                // Click toggle on trigger
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var expanded = trigger.getAttribute('aria-expanded') === 'true';
                    if (expanded) {
                        closeMenu(false);
                    } else {
                        openMenu();
                    }
                });

                // Click on option items
                options.forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var val = this.getAttribute('data-value');
                        selectOption(val);
                    });
                });

                // Outside click dismissal
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) {
                        if (trigger.getAttribute('aria-expanded') === 'true') {
                            closeMenu(false);
                        }
                    }
                });

                // Window resize reposition
                window.addEventListener('resize', function() {
                    if (trigger.getAttribute('aria-expanded') === 'true') {
                        positionMenu();
                    }
                });

                // Keyboard interactions
                trigger.addEventListener('keydown', function(e) {
                    var expanded = trigger.getAttribute('aria-expanded') === 'true';
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        if (!expanded) {
                            openMenu();
                        }
                    }
                });

                wrapper.addEventListener('keydown', function(e) {
                    var expanded = trigger.getAttribute('aria-expanded') === 'true';
                    if (!expanded) return;

                    if (e.key === 'Escape') {
                        e.preventDefault();
                        closeMenu(true);
                    } else if (e.key === 'Tab') {
                        closeMenu(false);
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        focusedIndex = (focusedIndex + 1) % options.length;
                        updateFocus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        focusedIndex = (focusedIndex - 1 + options.length) % options.length;
                        updateFocus();
                    } else if (e.key === 'Home') {
                        e.preventDefault();
                        focusedIndex = 0;
                        updateFocus();
                    } else if (e.key === 'End') {
                        e.preventDefault();
                        focusedIndex = options.length - 1;
                        updateFocus();
                    } else if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        if (focusedIndex >= 0 && focusedIndex < options.length) {
                            var val = options[focusedIndex].getAttribute('data-value');
                            selectOption(val);
                        }
                    }
                });

                // Tooltip initialization if Bootstrap tooltips are active
                if (window.bootstrap && window.bootstrap.Tooltip) {
                    var tipEl = wrapper.querySelector('[data-bs-toggle="tooltip"]');
                    if (tipEl) {
                        try { new window.bootstrap.Tooltip(tipEl); } catch (err) {}
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupGenderDropdown);
            } else {
                setupGenderDropdown();
            }
        })();
    </script>

    {{-- =========================================================================
         JeroClientSelectEngine: God-Level Enterprise Client-First Dropdown Engine (v2.0)
         Scope: resources/views/service_company/jobs/create.blade.php
         Architecture: L0 Component State -> L1 In-Memory RAM -> L2 IndexedDB (Tenant Partitioned) -> L3 Edge ETag (304) -> L4 Redis Read Cache
         Target: 10K Concurrency with Zero Per-Interaction Server Reads (<2ms search, <5ms open)
         15.6" Screen Fit: Collision detection & dynamic boundary clamping
         Search: 4-Tier Pipeline (Prefix -> Token -> Acronym -> Levenshtein fallback)
         Frecency: Time-decayed ranking for recently selected clients
         Data Model: Compact Manifest Index + Lazy-loaded Details (Addresses & Contacts)
         Security: Tenant + User ID DB partitioning & Contact PII Protection
         ========================================================================= --}}
    <script>
    (function() {
        'use strict';

        // =========================================================================
        // JeroClientSelectEngine: God-Level Enterprise Client-First Dropdown Engine (v5.0)
        // Architecture: L0 Browser State -> L1 RAM -> L2 IndexedDB -> L3 Edge ETag (304) -> L4 Redis -> L5 Elasticsearch
        // Target: 10K Concurrency with 0% Per-Interaction Server Reads (Option A Pre-Distributed Envelope)
        // DOM Virtualization: Strictly 14 DOM nodes (44px fixed height) for 50,000+ records
        // Search: Inverted Token Prefix Index (<1.5ms) + Fuzzy Fallback
        // Real-time: Reverb Delta Broadcast + Sequence Gap Detection + Multi-Tab BroadcastChannel
        // =========================================================================

        // Tenancy & Context Constants
        var CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        var CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
        var INITIAL_SELECTED_ID = {{ (int) $selected_client_id }};
        var BUNDLE_URL = "{{ route('service_company.jobs.clientsBundle') }}";
        var DETAILS_URL_TEMPLATE = "{{ route('service_company.jobs.clientDetails', ['client_id' => ':id']) }}";

        // Geometry Constants for 15.6" Ergonomic Virtual Scrolling
        var ITEM_HEIGHT = 44;     // Fixed item height in pixels
        var VIEWPORT_HEIGHT = 264; // Max visible viewport height (6 items)
        var WINDOW_SIZE = 14;     // 6 visible + 4 overscan top + 4 overscan bottom

        // Component State Machine
        var STATES = {
            EMPTY: 'EMPTY',
            HYDRATING: 'HYDRATING',
            READY: 'READY',
            OPEN: 'OPEN',
            SEARCHING: 'SEARCHING',
            SELECTED: 'SELECTED',
            SYNCING: 'SYNCING',
            STALE: 'STALE',
            ERROR: 'ERROR'
        };
        var currentState = STATES.EMPTY;

        // Selection Validity State Machine (Non-Destructive Ergonomics)
        var VALIDITY_STATES = {
            VALID: 'VALID',
            STALE: 'STALE',
            DEACTIVATED: 'DEACTIVATED',
            DELETED: 'DELETED',
            PERMISSION_REVOKED: 'PERMISSION_REVOKED'
        };
        var currentValidity = VALIDITY_STATES.VALID;

        function setState(state) {
            currentState = state;
            var wrap = document.getElementById('jero_client_select_wrap');
            if (wrap) {
                wrap.setAttribute('data-state', state);
            }
        }

        // L0 Active Component State
        var L0 = {
            selectedClientId: INITIAL_SELECTED_ID || 0,
            selectedClient: null,
            selectedAddressId: null,
            detailsCache: new Map() // client_id -> fallback details
        };

        // L1 In-Memory RAM Store & Inverted Index
        var clientsMap = new Map();
        var clientsList = [];
        var tokenIndex = new Map(); // token -> Set(clientId)
        var currentManifestVersion = 1;
        var currentManifestId = '';
        var currentFilter = 'all';
        var currentSearch = '';
        var focusedIndex = -1;
        var visibleItems = [];
        var lastStartIndex = -1;
        var lastEndIndex = -1;
        var isScrollPending = false;

        // Frecency Model (Count / (1 + lambda * delta_sec))
        var FRECENCY_KEY = 'jero_client_frecency_' + CURRENT_TENANT_ID;
        var FRECENCY_LAMBDA = 0.0001;

        // Multi-Tab Synchronization Channel
        var indexBroadcast = null;
        if ('BroadcastChannel' in window) {
            try {
                indexBroadcast = new BroadcastChannel('jero-client-index');
                indexBroadcast.onmessage = function(ev) {
                    if (!ev || !ev.data) return;
                    var payload = ev.data;
                    if (payload.type === 'delta' && payload.client) {
                        applyClientDelta(payload.action, payload.client, payload.version);
                    } else if (payload.type === 'manifest_reload') {
                        fetchClientsBundle(true);
                    }
                };
            } catch (e) {}
        }

        function broadcastToTabs(type, data) {
            if (indexBroadcast) {
                try {
                    indexBroadcast.postMessage(Object.assign({ type: type }, data));
                } catch (e) {}
            }
        }

        // Seed initial client headers passed from server Blade for 0.0ms initial paint
        window.__INITIAL_CLIENTS__ = [
            <?php if (isset($client) && (is_array($client) || is_object($client))) { ?>
                <?php foreach ($client as $c) { ?>
                {
                    id: <?php echo (int) $c->id; ?>,
                    n: <?php echo json_encode(ucfirst($c->name)); ?>,
                    t: <?php echo (int) $c->self_company; ?>,
                    s: 1,
                    l: <?php echo json_encode($c->logo ?: ''); ?>,
                    i: <?php echo json_encode($c->industry ?: ''); ?>
                },
                <?php } ?>
            <?php } ?>
        ];

        // Metronic Corporate Avatar Palette
        var AVATAR_COLORS = [
            { bg: '#EEF6FF', color: '#3E97FF' },
            { bg: '#F8F5FF', color: '#7239EA' },
            { bg: '#E8FFF3', color: '#50CD89' },
            { bg: '#FFF8DD', color: '#F6C000' },
            { bg: '#FFF5F8', color: '#F1416C' },
            { bg: '#F1FAFF', color: '#009EF7' }
        ];

        function getAvatarColor(name) {
            if (!name) return AVATAR_COLORS[0];
            var hash = 0;
            for (var i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return AVATAR_COLORS[Math.abs(hash) % AVATAR_COLORS.length];
        }

        function getInitials(name) {
            if (!name) return 'CL';
            var parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /**
         * Standard enterprise title-casing for location names, cities, states, countries, and addresses.
         * Formats 1st letter in capital across words while preserving standard acronyms (USA, UK, etc.).
         */
        function formatTitleCase(str) {
            if (!str) return '';
            var acronyms = { 'usa': 'USA', 'uk': 'UK', 'uae': 'UAE', 'ncr': 'NCR', 'ii': 'II', 'iii': 'III', 'iv': 'IV' };
            return String(str).replace(/\b[a-zA-Z]+(?:\b|(?=[,\/\-\(\)]))/g, function(word) {
                var lower = word.toLowerCase();
                if (acronyms[lower]) return acronyms[lower];
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
        }

        /**
         * Enforces standard 1st letter capital across all options in the Address dropdown.
         */
        function sanitizeAddressSelectOptions() {
            var $select = $('#address_id');
            if (!$select.length) return;
            $select.find('option').each(function() {
                var val = $(this).val();
                var text = $(this).text();
                if (!val) {
                    if (text !== 'Select Address' && text !== 'No Address') {
                        $(this).text('Select Address');
                    }
                    return;
                }
                $(this).text(formatTitleCase(text));
            });
        }
        window.sanitizeAddressSelectOptions = sanitizeAddressSelectOptions;

        // Schema Normalizer: decodes Option A envelope keys { id, n, t, s, i, c, l, loc, cc }
        function normalizeClient(c) {
            if (!c) return null;
            var name = c.n || c.name || '';
            var cleanChars = name.replace(/[^A-Za-z0-9]/g, '');
            var code = c.c || c.code || cleanChars.substring(0, 4).toUpperCase();
            var locs = [];
            if (c.loc && Array.isArray(c.loc)) {
                locs = c.loc.map(function(l) {
                    return {
                        id: parseInt(l.id, 10),
                        l: formatTitleCase(l.l || l.label || l.address || '')
                    };
                });
            }
            return {
                id: parseInt(c.id, 10),
                name: name,
                self_company: (c.t !== undefined ? !!c.t : (c.self_company !== undefined ? !!c.self_company : false)),
                status: (c.s !== undefined ? parseInt(c.s, 10) : (c.status !== undefined ? parseInt(c.status, 10) : 1)),
                industry: formatTitleCase(c.i || c.industry || ''),
                code: code,
                logo: c.l || c.logo || '',
                loc: locs,
                cc: c.cc || []
            };
        }

        // =========================================================================
        // L2 IndexedDB Store (Multi-Tenant & Security Hardened Partitioning)
        // =========================================================================
        var DB_NAME = 'jero_service_clients_db_v6';
        var DB_VERSION = 1;
        var dbPromise = null;

        function getDB() {
            if (!window.indexedDB) return Promise.resolve(null);
            if (dbPromise) return dbPromise;
            dbPromise = new Promise(function(resolve) {
                try {
                    var req = indexedDB.open(DB_NAME, DB_VERSION);
                    req.onupgradeneeded = function(e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains('clients_cache')) {
                            db.createObjectStore('clients_cache', { keyPath: 'key' });
                        }
                        if (!db.objectStoreNames.contains('meta')) {
                            db.createObjectStore('meta', { keyPath: 'id' });
                        }
                    };
                    req.onsuccess = function(e) {
                        var db = e.target.result;
                        try {
                            var tx = db.transaction(['meta'], 'readonly');
                            var metaReq = tx.objectStore('meta').get('context');
                            metaReq.onsuccess = function() {
                                var meta = metaReq.result;
                                if (meta && (meta.tenant_id !== CURRENT_TENANT_ID || meta.user_id !== CURRENT_USER_ID)) {
                                    // Security wipe on tenant / user switch
                                    var wipeTx = db.transaction(['clients_cache', 'meta'], 'readwrite');
                                    wipeTx.objectStore('clients_cache').clear();
                                    wipeTx.objectStore('meta').put({ id: 'context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID });
                                } else if (!meta) {
                                    var setTx = db.transaction(['meta'], 'readwrite');
                                    setTx.objectStore('meta').put({ id: 'context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID });
                                }
                            };
                        } catch (err) {}
                        resolve(db);
                    };
                    req.onerror = function() { resolve(null); };
                } catch (err) { resolve(null); }
            });
            return dbPromise;
        }

        async function idbGet(key) {
            var db = await getDB();
            if (!db) return null;
            return new Promise(function(resolve) {
                try {
                    var tx = db.transaction(['clients_cache'], 'readonly');
                    var req = tx.objectStore('clients_cache').get(key);
                    req.onsuccess = function() { resolve(req.result ? req.result.data : null); };
                    req.onerror = function() { resolve(null); };
                } catch (e) { resolve(null); }
            });
        }

        async function idbSet(key, data) {
            var db = await getDB();
            if (!db) return;
            try {
                var tx = db.transaction(['clients_cache'], 'readwrite');
                tx.objectStore('clients_cache').put({ key: key, data: data, timestamp: Date.now() });
            } catch (e) {}
        }

        // Quota-Aware Garbage Collection (requestIdleCallback)
        function scheduleIndexedDBMaintenance() {
            var runner = window.requestIdleCallback || function(cb) { setTimeout(cb, 1200); };
            runner(async function() {
                try {
                    var db = await getDB();
                    if (!db) return;
                    if (navigator.storage && navigator.storage.estimate) {
                        var est = await navigator.storage.estimate();
                        if (est.usage && est.quota && (est.usage / est.quota) > 0.8) {
                            console.warn('[JeroClientSelectEngine] Storage usage high; pruning stale records');
                        }
                    }
                    var tx = db.transaction(['clients_cache'], 'readwrite');
                    var store = tx.objectStore('clients_cache');
                    var req = store.openCursor();
                    var now = Date.now();
                    var maxAge = 7 * 86400 * 1000; // 7 days retention
                    req.onsuccess = function(e) {
                        var cursor = e.target.result;
                        if (cursor) {
                            var record = cursor.value;
                            if (record && record.timestamp && (now - record.timestamp > maxAge)) {
                                cursor.delete();
                            }
                            cursor.continue();
                        }
                    };
                } catch (e) {}
            });
        }

        // =========================================================================
        // Prebuilt Inverted Token Index (< 1.5ms Query Execution)
        // =========================================================================
        function buildInvertedIndex(list) {
            tokenIndex.clear();
            for (var i = 0; i < list.length; i++) {
                var c = list[i];
                var text = ((c.name || '') + ' ' + (c.code || '') + ' ' + (c.industry || '')).toLowerCase();
                var words = text.split(/[^a-z0-9]+/i).filter(Boolean);
                for (var w = 0; w < words.length; w++) {
                    var word = words[w];
                    for (var p = 2; p <= word.length; p++) {
                        var prefix = word.substring(0, p);
                        var set = tokenIndex.get(prefix);
                        if (!set) {
                            set = new Set();
                            tokenIndex.set(prefix, set);
                        }
                        set.add(c.id);
                    }
                }
            }
        }

        function levenshteinDistance(a, b, maxDist) {
            var al = a.length, bl = b.length;
            if (Math.abs(al - bl) > maxDist) return maxDist + 1;
            var v0 = new Array(bl + 1);
            var v1 = new Array(bl + 1);
            for (var i = 0; i <= bl; i++) v0[i] = i;
            for (var i = 0; i < al; i++) {
                v1[0] = i + 1;
                var minRow = v1[0];
                for (var j = 0; j < bl; j++) {
                    var cost = (a[i] === b[j]) ? 0 : 1;
                    v1[j + 1] = Math.min(v1[j] + 1, v0[j + 1] + 1, v0[j] + cost);
                    if (v1[j + 1] < minRow) minRow = v1[j + 1];
                }
                if (minRow > maxDist) return maxDist + 1;
                for (var j = 0; j <= bl; j++) v0[j] = v1[j];
            }
            return v0[bl];
        }

        function isFuzzyMatch(str, query) {
            if (!str || !query) return false;
            var s = str.toLowerCase();
            var q = query.toLowerCase();
            if (Math.abs(s.length - q.length) > 2) {
                var words = s.split(/\s+/);
                for (var i = 0; i < words.length; i++) {
                    if (levenshteinDistance(words[i], q, 2) <= 1) return true;
                }
                return false;
            }
            return levenshteinDistance(s, q, 2) <= 2;
        }

        function searchClientsFast(query, filter) {
            var baseList = clientsList;
            if (filter === 'internal') {
                baseList = clientsList.filter(function(c) { return c.self_company; });
            } else if (filter === 'client') {
                baseList = clientsList.filter(function(c) { return !c.self_company; });
            }

            if (!query) {
                // When query is empty and viewing All, hoist top frecent clients
                if (filter === 'all') {
                    var frecent = getFrecentClients(3);
                    if (frecent.length > 0) {
                        var frecentIds = new Set(frecent.map(function(c) { return c.id; }));
                        var rest = baseList.filter(function(c) { return !frecentIds.has(c.id); });
                        return frecent.concat(rest);
                    }
                }
                return baseList;
            }

            var q = query.trim().toLowerCase();
            var queryTokens = q.split(/[^a-z0-9]+/i).filter(Boolean);
            if (queryTokens.length === 0) return baseList;

            var matchingSets = [];
            for (var i = 0; i < queryTokens.length; i++) {
                var token = queryTokens[i];
                var set = tokenIndex.get(token);
                if (set) {
                    matchingSets.push(set);
                } else {
                    matchingSets.push(null);
                }
            }

            var candidateIds = null;
            for (var i = 0; i < matchingSets.length; i++) {
                var set = matchingSets[i];
                if (set === null) {
                    candidateIds = null;
                    break;
                }
                if (candidateIds === null) {
                    candidateIds = new Set(set);
                } else {
                    var nextSet = new Set();
                    set.forEach(function(id) {
                        if (candidateIds.has(id)) nextSet.add(id);
                    });
                    candidateIds = nextSet;
                }
            }

            var results = [];
            if (candidateIds && candidateIds.size > 0) {
                for (var i = 0; i < baseList.length; i++) {
                    var c = baseList[i];
                    if (candidateIds.has(c.id)) {
                        results.push(c);
                    }
                }
            } else {
                // Linear token match fallback
                for (var i = 0; i < baseList.length; i++) {
                    var c = baseList[i];
                    var name = (c.name || '').toLowerCase();
                    var ind = (c.industry || '').toLowerCase();
                    var code = (c.code || '').toLowerCase();
                    var match = queryTokens.every(function(t) {
                        return name.indexOf(t) !== -1 || ind.indexOf(t) !== -1 || code.indexOf(t) !== -1;
                    });
                    if (match) results.push(c);
                }
            }

            // Fuzzy match fallback if results < 3 and query >= 3
            if (results.length < 3 && q.length >= 3) {
                var seen = new Set(results.map(function(c) { return c.id; }));
                for (var i = 0; i < baseList.length; i++) {
                    var c = baseList[i];
                    if (seen.has(c.id)) continue;
                    if (isFuzzyMatch(c.name, q)) {
                        results.push(c);
                        if (results.length >= 5) break;
                    }
                }
            }

            return results;
        }

        // =========================================================================
        // Frecency Scoring Engine
        // =========================================================================
        function recordClientSelection(clientId) {
            try {
                var frecencyMap = JSON.parse(localStorage.getItem(FRECENCY_KEY) || '{}');
                var item = frecencyMap[clientId] || { count: 0, lastUsed: 0 };
                item.count += 1;
                item.lastUsed = Date.now();
                frecencyMap[clientId] = item;
                localStorage.setItem(FRECENCY_KEY, JSON.stringify(frecencyMap));
            } catch (e) {}
        }

        function getFrecentClients(limit) {
            try {
                var frecencyMap = JSON.parse(localStorage.getItem(FRECENCY_KEY) || '{}');
                var now = Date.now();
                var scored = [];
                for (var idStr in frecencyMap) {
                    var id = parseInt(idStr, 10);
                    var c = clientsMap.get(id);
                    if (c && c.status === 1) {
                        var item = frecencyMap[idStr];
                        var deltaSec = Math.max(0, (now - item.lastUsed) / 1000);
                        var score = item.count / (1 + FRECENCY_LAMBDA * deltaSec);
                        scored.push({ client: c, score: score });
                    }
                }
                scored.sort(function(a, b) { return b.score - a.score; });
                return scored.slice(0, limit || 3).map(function(s) {
                    var clone = Object.assign({}, s.client);
                    clone._isRecent = true;
                    return clone;
                });
            } catch (e) {
                return [];
            }
        }

        // =========================================================================
        // Option A Pre-Distributed Envelope Address Injection (Zero Network!)
        // =========================================================================
        function populateAddressDropdownFromLoc(locList) {
            var $addressSelect = $('#address_id');
            var $addressShow = $('.address_show');
            if (!$addressSelect.length) return;

            if (locList && Array.isArray(locList) && locList.length > 0) {
                var html = '<option value="">Select Address</option>';
                locList.forEach(function(loc) {
                    var rawLabel = loc.l || loc.label || loc.address || 'Registered Location';
                    var label = formatTitleCase(rawLabel);
                    html += '<option value="' + loc.id + '">' + escapeHtml(label) + '</option>';
                });
                $addressSelect.html(html);
                $addressShow.hide();

                if (locList.length === 1) {
                    $addressSelect.val(locList[0].id).trigger('change');
                }
            } else {
                $addressSelect.html('<option value="">No Address</option>');
                $addressShow.show();
            }
        }

        // Fallback detail resolver for overflow/deep locations
        async function resolveClientDetails(clientId) {
            clientId = parseInt(clientId, 10);
            if (!clientId || clientId <= 0) return null;

            if (L0.detailsCache.has(clientId)) {
                return L0.detailsCache.get(clientId);
            }

            var cached = await idbGet('details_' + CURRENT_TENANT_ID + '_' + clientId);
            if (cached && cached.data) {
                L0.detailsCache.set(clientId, cached.data);
                return cached.data;
            }

            try {
                var url = DETAILS_URL_TEMPLATE.replace(':id', clientId);
                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                var etag = localStorage.getItem('jero_details_etag_' + CURRENT_TENANT_ID + '_' + clientId);
                if (etag) headers['If-None-Match'] = etag;

                var res = await fetch(url, { headers: headers });
                if (res.status === 304 && cached && cached.data) {
                    L0.detailsCache.set(clientId, cached.data);
                    return cached.data;
                }
                if (res.ok) {
                    var newEtag = res.headers.get('ETag');
                    if (newEtag) localStorage.setItem('jero_details_etag_' + CURRENT_TENANT_ID + '_' + clientId, newEtag);
                    var data = await res.json();
                    if (data && data.status === 'success') {
                        L0.detailsCache.set(clientId, data);
                        await idbSet('details_' + CURRENT_TENANT_ID + '_' + clientId, { data: data, timestamp: Date.now() });
                        return data;
                    }
                }
            } catch (err) {
                console.warn('[JeroClientSelectEngine] Details fallback note:', err);
            }
            return null;
        }

        // =========================================================================
        // DOM Virtualization Windowing Engine (Strictly 14 DOM Nodes for 50,000 Items)
        // =========================================================================
        function updateVirtualWindow() {
            var phantom = document.getElementById('jero_virtual_phantom');
            var win = document.getElementById('jero_virtual_window');
            var container = document.getElementById('jero_client_list_container');
            if (!phantom || !win || !container) return;

            var total = visibleItems.length;
            if (total === 0) {
                phantom.style.height = '0px';
                win.style.transform = 'translateY(0px)';
                win.innerHTML = '<div style="padding: 24px 16px; text-align: center; color: #7E8299; font-size: 12px;">' +
                    '<div style="font-weight: 500; margin-bottom: 4px; color: #181C32;">No organizations found</div>' +
                    '<div>No matching organizations for "' + escapeHtml(currentSearch) + '"</div>' +
                    '</div>';
                lastStartIndex = -1;
                lastEndIndex = -1;
                return;
            }

            phantom.style.height = (total * ITEM_HEIGHT) + 'px';

            var scrollTop = container.scrollTop;
            var startIndex = Math.max(0, Math.floor(scrollTop / ITEM_HEIGHT) - 4);
            var endIndex = Math.min(total, startIndex + WINDOW_SIZE);
            var offsetY = startIndex * ITEM_HEIGHT;

            win.style.transform = 'translateY(' + offsetY + 'px)';

            // Render strictly the items within the active virtual slice
            var html = '';
            for (var i = startIndex; i < endIndex; i++) {
                html += buildItemHtml(visibleItems[i], currentSearch, i);
            }
            win.innerHTML = html;

            lastStartIndex = startIndex;
            lastEndIndex = endIndex;
        }

        function onVirtualScroll() {
            if (!isScrollPending) {
                isScrollPending = true;
                requestAnimationFrame(function() {
                    updateVirtualWindow();
                    isScrollPending = false;
                });
            }
        }

        function renderVirtualList() {
            var q = currentSearch.trim();
            if (q) setState(STATES.SEARCHING);
            else if (currentState === STATES.SEARCHING) setState(STATES.OPEN);

            visibleItems = searchClientsFast(q, currentFilter);
            focusedIndex = -1;
            lastStartIndex = -1;
            lastEndIndex = -1;

            var container = document.getElementById('jero_client_list_container');
            if (container) container.scrollTop = 0;

            updateVirtualWindow();
        }

        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            var safeText = escapeHtml(text);
            var regex = new RegExp('(' + query.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&') + ')', 'gi');
            return safeText.replace(regex, '<mark>$1</mark>');
        }

        function buildItemHtml(c, query, index) {
            var isSel = (c.id === L0.selectedClientId);
            var isFocused = (index === focusedIndex);
            var col = getAvatarColor(c.name);
            var avatarHtml = '';
            if (c.logo) {
                avatarHtml = '<div class="jero-client-item-avatar" style="background:#FFF;"><img src="' + escapeHtml(c.logo) + '" alt=""></div>';
            } else {
                avatarHtml = '<div class="jero-client-item-avatar" style="background:' + col.bg + '; color:' + col.color + ';">' + getInitials(c.name) + '</div>';
            }

            var badgeText = c.self_company ? 'My Organization' : 'Client';
            var badgeClass = c.self_company ? 'badge-internal' : 'badge-client';
            if (c.status === 0) {
                badgeText = 'Inactive';
                badgeClass = 'badge-deactivated';
            } else if (c._isRecent) {
                badgeText = '★ Recent';
                badgeClass = 'badge-recent';
            }

            var subText = c.industry ? formatTitleCase(c.industry) : (c.self_company ? 'My Organization' : 'Client');
            if (c.loc && c.loc.length > 0) {
                subText = formatTitleCase(c.loc[0].l) || subText;
            }

            return '<div class="jero-client-item ' + (isSel ? 'selected ' : '') + (isFocused ? 'focused ' : '') + '" data-id="' + c.id + '" role="option" aria-selected="' + (isSel ? 'true' : 'false') + '">' +
                '<div class="jero-client-item-content">' +
                    avatarHtml +
                    '<div class="jero-client-item-info">' +
                        '<div class="jero-client-item-name">' + highlightMatch(c.name, query) + '</div>' +
                        '<div class="jero-client-item-sub">' + escapeHtml(subText) + '</div>' +
                    '</div>' +
                    '<span class="jero-client-badge ' + badgeClass + '">' + badgeText + '</span>' +
                '</div>' +
                (isSel ? '<span class="jero-client-item-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>' : '') +
            '</div>';
        }

        // =========================================================================
        // Selection Workflow & Validity Lifecycle
        // =========================================================================
        function setSelectionValidity(validity, client) {
            currentValidity = validity;
            var badgeEl = document.getElementById('jero_trigger_badge');
            var trigger = document.getElementById('jero_client_trigger');
            if (!badgeEl || !trigger) return;

            if (validity === VALIDITY_STATES.DEACTIVATED) {
                badgeEl.style.display = 'inline-block';
                badgeEl.className = 'jero-client-badge badge-deactivated';
                badgeEl.textContent = 'Client deactivated';
                trigger.classList.add('selection-invalid');
            } else if (validity === VALIDITY_STATES.DELETED) {
                badgeEl.style.display = 'inline-block';
                badgeEl.className = 'jero-client-badge badge-deleted';
                badgeEl.textContent = 'Client deleted';
                trigger.classList.add('selection-invalid');
            } else {
                trigger.classList.remove('selection-invalid');
            }
        }

        async function selectClient(id) {
            id = parseInt(id, 10);
            var client = clientsMap.get(id);
            if (!client) return;

            L0.selectedClientId = id;
            L0.selectedClient = client;
            setState(STATES.SELECTED);

            if (client.status === 0) {
                setSelectionValidity(VALIDITY_STATES.DEACTIVATED, client);
            } else {
                setSelectionValidity(VALIDITY_STATES.VALID, client);
            }

            updateTriggerUI(id);
            closePopover();
            recordClientSelection(id);
            if (typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('client_id');
            }

            // Option A Pre-Distributed Envelope Address Resolution (Synchronous, Zero Network!)
            if (client.loc && Array.isArray(client.loc) && client.loc.length > 0) {
                populateAddressDropdownFromLoc(client.loc);
            } else {
                // Fallback to async detail resolver only if locations were omitted
                var details = await resolveClientDetails(id);
                if (details && details.locations) {
                    populateAddressDropdownFromLoc(details.locations);
                }
            }

            // Sync hidden native select for full backward compatibility
            var $nativeSelect = $('#client_id');
            if ($nativeSelect.length) {
                if (!$nativeSelect.find('option[value="' + id + '"]').length) {
                    $nativeSelect.append(new Option(client.name, id, true, true));
                }
                $nativeSelect.val(id);
                window.__BYPASS_NEXT_CLIENT_AJAX__ = true;
                $nativeSelect.trigger('change');
            }

            // Dispatch decoupled enterprise custom event
            window.dispatchEvent(new CustomEvent('jero:client-selected', {
                detail: {
                    clientId: id,
                    client: client,
                    validity: currentValidity
                }
            }));
        }

        function clearSelection(e) {
            if (e) e.stopPropagation();
            L0.selectedClientId = 0;
            L0.selectedClient = null;
            setState(STATES.READY);
            currentValidity = VALIDITY_STATES.VALID;
            resetTriggerUI();

            var $nativeSelect = $('#client_id');
            if ($nativeSelect.length) {
                $nativeSelect.val('');
                $('#address_id').html('<option value="">Select Address</option>');
                $('.address_show').hide();
                window.__BYPASS_NEXT_CLIENT_AJAX__ = true;
                $nativeSelect.trigger('change');
            }

            window.dispatchEvent(new CustomEvent('jero:client-selected', {
                detail: {
                    clientId: 0,
                    client: null,
                    validity: VALIDITY_STATES.VALID
                }
            }));
        }

        function updateTriggerUI(id) {
            var client = clientsMap.get(id);
            var avatarEl = document.getElementById('jero_trigger_avatar');
            var labelEl = document.getElementById('jero_trigger_label');
            var subEl = document.getElementById('jero_trigger_sub');
            var badgeEl = document.getElementById('jero_trigger_badge');
            var clearEl = document.getElementById('jero_trigger_clear');

            if (!client) {
                resetTriggerUI();
                return;
            }

            L0.selectedClient = client;

            if (labelEl) {
                labelEl.textContent = client.name;
                labelEl.classList.remove('placeholder');
            }

            if (avatarEl) {
                if (client.logo) {
                    avatarEl.innerHTML = '<img src="' + escapeHtml(client.logo) + '" alt="' + escapeHtml(client.name) + '">';
                    avatarEl.style.background = '#FFFFFF';
                    avatarEl.style.color = 'inherit';
                } else {
                    var col = getAvatarColor(client.name);
                    avatarEl.textContent = getInitials(client.name);
                    avatarEl.style.background = col.bg;
                    avatarEl.style.color = col.color;
                }
            }

            if (badgeEl && currentValidity === VALIDITY_STATES.VALID) {
                badgeEl.style.display = 'inline-block';
                if (client.self_company) {
                    badgeEl.className = 'jero-client-badge badge-internal';
                    badgeEl.textContent = 'My Organization';
                } else {
                    badgeEl.className = 'jero-client-badge badge-client';
                    badgeEl.textContent = 'Client';
                }
            }

            if (subEl) {
                var locCount = (client.loc && Array.isArray(client.loc)) ? client.loc.length : 0;
                if (locCount > 0) {
                    subEl.textContent = locCount === 1 ? '1 Registered Address' : locCount + ' Registered Addresses';
                    subEl.style.display = 'block';
                } else if (client.industry) {
                    subEl.textContent = formatTitleCase(client.industry);
                    subEl.style.display = 'block';
                } else {
                    subEl.style.display = 'none';
                }
            }

            if (clearEl) {
                clearEl.style.display = 'flex';
            }

            var rvClient = document.getElementById('rvClient');
            if (rvClient) {
                rvClient.textContent = client.name;
            }
        }

        function resetTriggerUI() {
            var avatarEl = document.getElementById('jero_trigger_avatar');
            var labelEl = document.getElementById('jero_trigger_label');
            var subEl = document.getElementById('jero_trigger_sub');
            var badgeEl = document.getElementById('jero_trigger_badge');
            var clearEl = document.getElementById('jero_trigger_clear');
            var trigger = document.getElementById('jero_client_trigger');

            L0.selectedClientId = 0;
            L0.selectedClient = null;

            if (trigger) trigger.classList.remove('selection-invalid');

            if (labelEl) {
                labelEl.textContent = 'Select Hiring Organization';
                labelEl.classList.add('placeholder');
            }
            if (avatarEl) {
                avatarEl.innerHTML = '<svg width="14" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>';
                avatarEl.style.background = '#F1F1F4';
                avatarEl.style.color = '#7E8299';
            }
            if (badgeEl) badgeEl.style.display = 'none';
            if (subEl) subEl.style.display = 'none';
            if (clearEl) clearEl.style.display = 'none';

            var rvClient = document.getElementById('rvClient');
            if (rvClient) {
                rvClient.textContent = '-';
            }
        }

        // =========================================================================
        // Hydration & Manifest Synchronization
        // =========================================================================
        function seedInitial() {
            if (window.__INITIAL_CLIENTS__ && Array.isArray(window.__INITIAL_CLIENTS__)) {
                window.__INITIAL_CLIENTS__.forEach(function(raw) {
                    var c = normalizeClient(raw);
                    if (c) clientsMap.set(c.id, c);
                });
                clientsList = Array.from(clientsMap.values());
                buildInvertedIndex(clientsList);
                updateTabCounts();
            }
        }

        function updateTabCounts() {
            var total = clientsList.length;
            var internal = 0;
            var clients = 0;
            clientsList.forEach(function(c) {
                if (c.self_company) internal++;
                else clients++;
            });
            var elAll = document.getElementById('jero_count_all');
            var elInt = document.getElementById('jero_count_internal');
            var elCli = document.getElementById('jero_count_clients');
            if (elAll) elAll.textContent = total;
            if (elInt) elInt.textContent = internal;
            if (elCli) elCli.textContent = clients;
        }

        async function fetchClientsBundle(force) {
            setState(STATES.SYNCING);
            try {
                var etag = force ? '' : (localStorage.getItem('jero_client_etag_' + CURRENT_TENANT_ID) || '');
                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if (etag) headers['If-None-Match'] = etag;

                var res = await fetch(BUNDLE_URL, { headers: headers });
                if (res.status === 304) {
                    setState(STATES.READY);
                    return;
                }
                if (res.status === 200) {
                    var newEtag = res.headers.get('ETag');
                    if (newEtag) localStorage.setItem('jero_client_etag_' + CURRENT_TENANT_ID, newEtag);

                    var data = await res.json();
                    if (data && data.clients) {
                        currentManifestVersion = data.source_version || data.read_model_version || 1;
                        currentManifestId = data.manifest_id || '';

                        clientsMap.clear();
                        data.clients.forEach(function(raw) {
                            var c = normalizeClient(raw);
                            if (c) clientsMap.set(c.id, c);
                        });
                        clientsList = Array.from(clientsMap.values());
                        buildInvertedIndex(clientsList);

                        await idbSet('bundle_' + CURRENT_TENANT_ID, data);
                        scheduleIndexedDBMaintenance();
                        updateTabCounts();
                        renderVirtualList();

                        if (L0.selectedClientId > 0) {
                            var sel = clientsMap.get(L0.selectedClientId);
                            if (sel) {
                                if (sel.status === 0) setSelectionValidity(VALIDITY_STATES.DEACTIVATED, sel);
                                else setSelectionValidity(VALIDITY_STATES.VALID, sel);
                                updateTriggerUI(L0.selectedClientId);
                            } else {
                                setSelectionValidity(VALIDITY_STATES.DELETED, null);
                            }
                        }
                    }
                }
                setState(STATES.READY);
            } catch (err) {
                console.warn('[JeroClientSelectEngine] Bundle fetch notice:', err);
                setState(clientsList.length > 0 ? STATES.READY : STATES.ERROR);
            }
        }

        async function initCacheAndHydrate() {
            setState(STATES.HYDRATING);
            seedInitial();
            if (L0.selectedClientId > 0) {
                updateTriggerUI(L0.selectedClientId);
            }

            // Layer 2: Hydrate from IndexedDB
            var cached = await idbGet('bundle_' + CURRENT_TENANT_ID);
            if (cached && cached.clients && cached.clients.length > 0) {
                currentManifestVersion = cached.source_version || cached.read_model_version || 1;
                currentManifestId = cached.manifest_id || '';

                cached.clients.forEach(function(raw) {
                    var c = normalizeClient(raw);
                    if (c) clientsMap.set(c.id, c);
                });
                clientsList = Array.from(clientsMap.values());
                buildInvertedIndex(clientsList);
                updateTabCounts();

                if (L0.selectedClientId > 0) {
                    updateTriggerUI(L0.selectedClientId);
                }
            }

            setState(STATES.READY);

            // Layer 3: Background Stale-While-Revalidate with ETag
            fetchClientsBundle(false);
        }

        // =========================================================================
        // Reverb Operational Delta Invalidation & Gap Detection
        // =========================================================================
        function applyClientDelta(action, clientData, newVersion) {
            if (!clientData || !clientData.id) return;
            var normalized = normalizeClient(clientData);
            if (newVersion) currentManifestVersion = newVersion;

            if (action === 'deleted') {
                clientsMap.delete(normalized.id);
                if (L0.selectedClientId === normalized.id) {
                    setSelectionValidity(VALIDITY_STATES.DELETED, normalized);
                }
            } else if (action === 'deactivated' || normalized.status === 0) {
                clientsMap.set(normalized.id, normalized);
                if (L0.selectedClientId === normalized.id) {
                    setSelectionValidity(VALIDITY_STATES.DEACTIVATED, normalized);
                }
            } else {
                clientsMap.set(normalized.id, normalized);
                if (L0.selectedClientId === normalized.id) {
                    setSelectionValidity(VALIDITY_STATES.VALID, normalized);
                    updateTriggerUI(normalized.id);
                }
            }

            clientsList = Array.from(clientsMap.values());
            buildInvertedIndex(clientsList);
            updateTabCounts();
            renderVirtualList();
        }

        function handleClientMutationEvent(e) {
            if (!e) return;
            // Gap Detection: sequence mismatch triggers full bundle reload
            if (e.previousVersion && currentManifestVersion && e.previousVersion !== currentManifestVersion) {
                fetchClientsBundle(true);
                return;
            }

            applyClientDelta(e.action, e.client, e.version);
            broadcastToTabs('delta', { action: e.action, client: e.client, version: e.version });
        }

        function initReverbListener() {
            if (window.Echo) {
                try {
                    window.Echo.private('company.' + CURRENT_TENANT_ID)
                        .listen('.client.mutated', handleClientMutationEvent)
                        .listen('ClientMutatedEvent', handleClientMutationEvent);
                } catch (err) {
                    console.warn('[JeroClientSelectEngine] Reverb listener notice:', err);
                }
            }
        }

        // =========================================================================
        // 15.6" Viewport Boundary Clamping & Positioning
        // =========================================================================
        function positionPopover() {
            var wrap = document.getElementById('jero_client_select_wrap');
            var popover = document.getElementById('jero_client_popover');
            var listEl = document.getElementById('jero_client_list_container');
            if (!wrap || !popover) return;

            var rect = wrap.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            var spaceAbove = rect.top;
            var popoverHeight = 310;

            if (spaceBelow < popoverHeight && spaceAbove > spaceBelow) {
                popover.classList.add('flipped-up');
                if (listEl) {
                    var avail = Math.max(160, Math.min(VIEWPORT_HEIGHT, spaceAbove - 110));
                    listEl.style.height = avail + 'px';
                }
            } else {
                popover.classList.remove('flipped-up');
                if (listEl) {
                    var avail = Math.max(160, Math.min(VIEWPORT_HEIGHT, spaceBelow - 110));
                    listEl.style.height = avail + 'px';
                }
            }
        }

        function openPopover() {
            var popover = document.getElementById('jero_client_popover');
            var trigger = document.getElementById('jero_client_trigger');
            var searchInput = document.getElementById('jero_client_search_input');
            if (!popover || !trigger) return;

            positionPopover();
            popover.style.display = 'flex';
            trigger.classList.add('active');
            trigger.setAttribute('aria-expanded', 'true');
            setState(STATES.OPEN);

            renderVirtualList();

            if (searchInput) {
                searchInput.value = '';
                currentSearch = '';
                setTimeout(function() { searchInput.focus(); }, 35);
            }
        }

        function closePopover() {
            var popover = document.getElementById('jero_client_popover');
            var trigger = document.getElementById('jero_client_trigger');
            if (!popover || !trigger) return;

            popover.style.display = 'none';
            trigger.classList.remove('active');
            trigger.setAttribute('aria-expanded', 'false');
            if (currentState === STATES.OPEN || currentState === STATES.SEARCHING) {
                setState(L0.selectedClientId > 0 ? STATES.SELECTED : STATES.READY);
            }
        }

        function togglePopover() {
            var popover = document.getElementById('jero_client_popover');
            if (!popover) return;
            if (popover.style.display === 'none' || !popover.style.display) {
                openPopover();
            } else {
                closePopover();
            }
        }

        // =========================================================================
        // Keyboard Navigation & Interaction Listeners
        // =========================================================================
        function focusListItem(direction) {
            if (visibleItems.length === 0) return;

            focusedIndex += direction;
            if (focusedIndex >= visibleItems.length) focusedIndex = 0;
            if (focusedIndex < 0) focusedIndex = visibleItems.length - 1;

            var container = document.getElementById('jero_client_list_container');
            if (container) {
                var targetTop = focusedIndex * ITEM_HEIGHT;
                var viewTop = container.scrollTop;
                var viewBottom = viewTop + container.clientHeight;

                if (targetTop < viewTop) {
                    container.scrollTop = targetTop;
                } else if (targetTop + ITEM_HEIGHT > viewBottom) {
                    container.scrollTop = targetTop + ITEM_HEIGHT - container.clientHeight;
                }
            }

            updateVirtualWindow();
        }

        function selectFocusedItem() {
            if (focusedIndex >= 0 && focusedIndex < visibleItems.length) {
                selectClient(visibleItems[focusedIndex].id);
            } else if (visibleItems.length === 1) {
                selectClient(visibleItems[0].id);
            }
        }

        function initEventListeners() {
            var trigger = document.getElementById('jero_client_trigger');
            var clearBtn = document.getElementById('jero_trigger_clear');
            var searchInput = document.getElementById('jero_client_search_input');
            var searchClear = document.getElementById('jero_search_clear');
            var container = document.getElementById('jero_client_list_container');
            var tabsContainer = document.querySelector('.jero-client-tabs');

            if (trigger) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    togglePopover();
                });
                trigger.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        openPopover();
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', clearSelection);
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    currentSearch = this.value;
                    if (searchClear) searchClear.style.display = currentSearch ? 'flex' : 'none';
                    renderVirtualList();
                });
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closePopover();
                        if (trigger) trigger.focus();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        focusListItem(1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        focusListItem(-1);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        selectFocusedItem();
                    }
                });
            }

            if (searchClear) {
                searchClear.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        currentSearch = '';
                        this.style.display = 'none';
                        renderVirtualList();
                        searchInput.focus();
                    }
                });
            }

            if (tabsContainer) {
                tabsContainer.addEventListener('click', function(e) {
                    var btn = e.target.closest('.jero-tab-btn');
                    if (!btn) return;
                    tabsContainer.querySelectorAll('.jero-tab-btn').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    currentFilter = btn.getAttribute('data-filter') || 'all';
                    renderVirtualList();
                });
            }

            if (container) {
                container.addEventListener('scroll', onVirtualScroll, { passive: true });
                container.addEventListener('click', function(e) {
                    var item = e.target.closest('.jero-client-item');
                    if (!item) return;
                    var id = item.getAttribute('data-id');
                    if (id) {
                        selectClient(id);
                    }
                });
            }

            // Click outside handler
            document.addEventListener('click', function(e) {
                var wrap = document.getElementById('jero_client_select_wrap');
                if (wrap && !wrap.contains(e.target)) {
                    closePopover();
                }
            });

            // Address change auto-sanitization to maintain Title Case
            $(document).on('change', '#address_id', function() {
                sanitizeAddressSelectOptions();
            });
        }

        // Global export for backward compatibility & cross-module inspection
        window.JeroClientSelectEngine = {
            getState: function() { return currentState; },
            getValidity: function() { return currentValidity; },
            getSelectedId: function() { return L0.selectedClientId; },
            getSelectedClient: function() { return L0.selectedClient; },
            getSelectedName: function() {
                var c = clientsMap.get(L0.selectedClientId);
                return c ? c.name : '';
            },
            select: selectClient,
            clear: clearSelection,
            refresh: function() { return fetchClientsBundle(true); }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initCacheAndHydrate();
                initEventListeners();
                initReverbListener();
                sanitizeAddressSelectOptions();
            });
        } else {
            initCacheAndHydrate();
            initEventListeners();
            initReverbListener();
            sanitizeAddressSelectOptions();
        }
    })();
    </script>

    <!-- =========================================================================
         Job Title Intelligence Engine (Client-First Execution / 10K Concurrency)
         ========================================================================= -->
    <script id="jero_titles_seed_data" type="application/json">
        {!! json_encode(@$titlesManifest ?? ['manifest_id'=>'empty','version'=>1,'titles'=>[]]) !!}
    </script>

    <script>
    (function() {
        'use strict';

        var CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        var CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
        var BUNDLE_URL = "{{ route('service_company.jobs.jobTitlesBundle') }}";

        // Internal State
        var titlesList = [];
        var titlesMap = new Map();
        var tokenIndex = new Map();
        var currentSearch = '';
        var currentFilter = 'all'; // 'all' | 'company' | 'standard'
        var focusedIndex = -1;
        var currentMatches = [];
        var isOpen = false;
        var priorSnapshot = null;
        var undoTimer = null;
        var pendingTemplateItem = null;
        var indexBroadcast = null;
        var TAB_SESSION_ID = Math.random().toString(36).substring(2, 10);
        var lastSyncTimestamp = 0;
        var isApplyingTemplate = false;
        var isEventListenersInitialized = false;
        var inputDebounceTimer = null;
        var inputRafId = null;

        // Synonym Intelligence Dictionary
        var TITLE_SYNONYMS = {
            'sde': ['software development engineer', 'software engineer'],
            'dev': ['developer', 'engineer'],
            'sr': ['senior', 'lead'],
            'jr': ['junior', 'associate'],
            'pm': ['product manager', 'project manager'],
            'qa': ['quality assurance', 'test engineer', 'sdit'],
            'ui': ['user interface', 'product designer'],
            'ux': ['user experience', 'product designer'],
            'backend': ['back end', 'backend developer', 'api developer'],
            'frontend': ['front end', 'frontend developer', 'ui developer'],
            'fullstack': ['full stack', 'fullstack developer']
        };

        // Recency storage key
        var RECENT_KEY = 'jero_recent_titles_' + CURRENT_USER_ID;

        function getRecentTitles() {
            try {
                var raw = localStorage.getItem(RECENT_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentTitle(item) {
            try {
                var list = getRecentTitles();
                list = list.filter(function(r) { return r.id !== item.id && r.title.toLowerCase() !== item.title.toLowerCase(); });
                list.unshift({
                    id: item.id,
                    title: item.title,
                    dept_name: item.dept_name,
                    is_standard: item.is_standard,
                    timestamp: Date.now()
                });
                if (list.length > 5) list = list.slice(0, 5);
                localStorage.setItem(RECENT_KEY, JSON.stringify(list));
            } catch (e) {}
        }

        // =========================================================================
        // IndexedDB L1 Persistent Storage (JeroLocalStore_v2)
        // =========================================================================
        var DB_NAME = 'JeroLocalStore_v2';
        var STORE_NAME = 'job_titles';
        var idbInstance = null;

        function openDatabase() {
            return new Promise(function(resolve, reject) {
                if (idbInstance) return resolve(idbInstance);
                if (!window.indexedDB) return reject(new Error('IndexedDB not supported'));

                var req = window.indexedDB.open(DB_NAME, 3);
                req.onupgradeneeded = function(e) {
                    var db = e.target.result;
                    if (!db.objectStoreNames.contains('clients')) {
                        db.createObjectStore('clients', { keyPath: 'key' });
                    }
                    if (!db.objectStoreNames.contains(STORE_NAME)) {
                        db.createObjectStore(STORE_NAME, { keyPath: 'key' });
                    }
                    if (!db.objectStoreNames.contains('departments')) {
                        db.createObjectStore('departments', { keyPath: 'key' });
                    }
                };
                req.onsuccess = function(e) {
                    idbInstance = e.target.result;
                    resolve(idbInstance);
                };
                req.onerror = function(e) {
                    reject(e.target.error);
                };
            });
        }

        function idbGet(key) {
            return openDatabase().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        var tx = db.transaction(STORE_NAME, 'readonly');
                        var store = tx.objectStore(STORE_NAME);
                        var req = store.get(key);
                        req.onsuccess = function() { resolve(req.result ? req.result.value : null); };
                        req.onerror = function() { resolve(null); };
                    } catch (err) {
                        resolve(null);
                    }
                });
            }).catch(function() {
                try {
                    var fallback = sessionStorage.getItem('idb_fallback_' + key);
                    return fallback ? JSON.parse(fallback) : null;
                } catch (e) { return null; }
            });
        }

        function idbSet(key, value) {
            return openDatabase().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        var tx = db.transaction(STORE_NAME, 'readwrite');
                        var store = tx.objectStore(STORE_NAME);
                        store.put({ key: key, value: value });
                        tx.oncomplete = function() { resolve(true); };
                        tx.onerror = function() { resolve(false); };
                    } catch (err) {
                        resolve(false);
                    }
                });
            }).catch(function() {
                try {
                    sessionStorage.setItem('idb_fallback_' + key, JSON.stringify(value));
                    return true;
                } catch (e) { return false; }
            });
        }

        // =========================================================================
        // Token Indexing & Multi-Tier Safe Scoring Engine (Freeze-Proof Execution)
        // =========================================================================
        function buildInvertedIndex(list) {
            tokenIndex.clear();
            titlesMap.clear();

            if (!Array.isArray(list)) return;
            var maxItems = Math.min(list.length, 5000);

            for (var i = 0; i < maxItems; i++) {
                var item = list[i];
                if (!item || !item.id || !item.title) continue;
                titlesMap.set(item.id, item);

                var cleanText = ((item.title || '') + ' ' + (item.dept_name || '')).toLowerCase();
                var words = cleanText.split(/[^a-z0-9]+/i).filter(Boolean);

                for (var w = 0; w < words.length; w++) {
                    var word = words[w];
                    if (word.length >= 2) {
                        var idSet = tokenIndex.get(word);
                        if (!idSet) {
                            idSet = new Set();
                            tokenIndex.set(word, idSet);
                        }
                        idSet.add(item.id);
                    }
                }
            }
        }

        function levenshteinDistance(a, b, maxDist) {
            var al = a.length, bl = b.length;
            if (Math.abs(al - bl) > maxDist) return maxDist + 1;
            var v0 = new Array(bl + 1);
            var v1 = new Array(bl + 1);
            for (var i = 0; i <= bl; i++) v0[i] = i;
            for (var i = 0; i < al; i++) {
                v1[0] = i + 1;
                var minRow = v1[0];
                for (var j = 0; j < bl; j++) {
                    var cost = (a[i] === b[j]) ? 0 : 1;
                    v1[j + 1] = Math.min(v1[j] + 1, v0[j + 1] + 1, v0[j] + cost);
                    if (v1[j + 1] < minRow) minRow = v1[j + 1];
                }
                if (minRow > maxDist) return maxDist + 1;
                for (var j = 0; j <= bl; j++) v0[j] = v1[j];
            }
            return v0[bl];
        }

        function scoreCandidate(item, query, queryTokens, recentIdSet, allowFuzzy) {
            if (!item || !item.title) return 0;
            var score = 0;
            var titleLower = (item.title || '').toLowerCase();

            // 1. Exact Substring Match
            if (titleLower === query) {
                score += 150;
            } else if (titleLower.indexOf(query) === 0) {
                score += 100;
            } else if (titleLower.indexOf(query) > 0) {
                score += 70;
            }

            // 2. Multi-Token Set Match
            var allTokensMatched = true;
            for (var t = 0; t < queryTokens.length; t++) {
                var tok = queryTokens[t];
                if (titleLower.indexOf(tok) !== -1) {
                    score += 25;
                } else {
                    // Check synonyms
                    var synonyms = TITLE_SYNONYMS[tok] || [];
                    var synMatch = false;
                    for (var s = 0; s < synonyms.length; s++) {
                        if (titleLower.indexOf(synonyms[s]) !== -1) {
                            score += 20;
                            synMatch = true;
                            break;
                        }
                    }
                    if (!synMatch) allTokensMatched = false;
                }
            }
            if (allTokensMatched) score += 30;

            // 3. Organization / Standard Bonus
            if (item.is_standard === 0) score += 25; // Org boost
            if (item.has_template === 1) score += 15; // Template boost

            // 4. Recency Bonus
            if (recentIdSet.has(item.id)) score += 35;

            // 5. Typo tolerance fallback: strictly bounded and executed only when explicitly permitted
            if (allowFuzzy && score < 40 && query.length >= 4) {
                var words = titleLower.split(/\s+/);
                for (var w = 0; w < words.length; w++) {
                    var word = words[w];
                    if (Math.abs(word.length - query.length) <= 1) {
                        if (levenshteinDistance(word, query, 2) <= 1) {
                            score += 20;
                            break;
                        }
                    }
                }
            }

            return score;
        }

        function searchTitles(query, filter) {
            var baseList = titlesList;
            if (filter === 'company') {
                baseList = titlesList.filter(function(r) { return r && r.is_standard === 0; });
            } else if (filter === 'standard') {
                baseList = titlesList.filter(function(r) { return r && r.is_standard === 1; });
            }

            var recentItems = getRecentTitles();
            var recentIdSet = new Set(recentItems.map(function(r) { return r.id; }));

            var cleanQ = (query || '').trim().toLowerCase();
            if (!cleanQ) {
                // Empty query: Return recent items first, then popular standard titles
                return baseList.slice(0, 30);
            }

            var queryTokens = cleanQ.split(/[^a-z0-9]+/i).filter(Boolean);
            if (queryTokens.length === 0) return baseList.slice(0, 30);

            var scored = [];
            // Pass 1: Substring & token match (0ms latency, zero Levenshtein allocations)
            for (var i = 0; i < baseList.length; i++) {
                var item = baseList[i];
                var score = scoreCandidate(item, cleanQ, queryTokens, recentIdSet, false);
                if (score > 15) {
                    scored.push({ item: item, score: score });
                }
            }

            // Pass 2: Bounded fuzzy fallback ONLY if fewer than 5 candidates matched and query length >= 4
            if (scored.length < 5 && cleanQ.length >= 4) {
                var fuzzyChecked = 0;
                for (var j = 0; j < baseList.length && fuzzyChecked < 40; j++) {
                    var candidate = baseList[j];
                    var alreadyScored = false;
                    for (var k = 0; k < scored.length; k++) {
                        if (scored[k].item.id === candidate.id) {
                            alreadyScored = true;
                            break;
                        }
                    }
                    if (alreadyScored) continue;

                    fuzzyChecked++;
                    var fScore = scoreCandidate(candidate, cleanQ, queryTokens, recentIdSet, true);
                    if (fScore > 15) {
                        scored.push({ item: candidate, score: fScore });
                    }
                }
            }

            scored.sort(function(a, b) { return b.score - a.score; });
            return scored.slice(0, 30).map(function(s) { return s.item; });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function highlightMatch(text, query) {
            if (!query || !query.trim()) return escapeHtml(text);
            var safeText = escapeHtml(text);
            var safeQ = query.trim().replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
            var regex = new RegExp('(' + safeQ + ')', 'gi');
            return safeText.replace(regex, '<mark>$1</mark>');
        }

        // =========================================================================
        // Render & Dropdown UI Engine (Top 30 Slice, 0 Virtualization Flicker)
        // =========================================================================
        function updateTabCounts() {
            var stdCount = 0;
            var orgCount = 0;
            for (var i = 0; i < titlesList.length; i++) {
                if (titlesList[i].is_standard === 1) stdCount++;
                else orgCount++;
            }
            var elAll = document.getElementById('jero_title_count_all');
            var elStd = document.getElementById('jero_title_count_standard');
            var elComp = document.getElementById('jero_title_count_company');
            if (elAll) elAll.textContent = titlesList.length;
            if (elStd) elStd.textContent = stdCount;
            if (elComp) elComp.textContent = orgCount;
        }

        function renderDropdownList() {
            var container = document.getElementById('jero_title_list_container');
            var customWrap = document.getElementById('jero_title_custom_action');
            var customText = document.getElementById('jero_custom_title_text');
            if (!container) return;

            currentMatches = searchTitles(currentSearch, currentFilter);
            focusedIndex = -1;

            var q = currentSearch.trim();
            var exactMatchExists = false;

            for (var m = 0; m < currentMatches.length; m++) {
                if (currentMatches[m].title.toLowerCase() === q.toLowerCase()) {
                    exactMatchExists = true;
                    break;
                }
            }

            // Render Custom Title Action Pill if user has typed something non-empty and non-exact
            if (customWrap && customText) {
                if (q && !exactMatchExists) {
                    customText.textContent = q;
                    customWrap.style.display = 'block';
                } else {
                    customWrap.style.display = 'none';
                }
            }

            if (currentMatches.length === 0) {
                container.innerHTML = '<div class="jero-title-empty-state">' +
                    'No standardized titles match "' + escapeHtml(q) + '"' +
                    '</div>';
                adjustDropdownPosition();
                return;
            }

            var html = '';
            var recentItems = getRecentTitles();
            var recentIdSet = new Set(recentItems.map(function(r) { return r.id; }));
            var currentVal = ($('#title').val() || '').trim().toLowerCase();

            // When search is empty and viewing 'all', show RECENTLY USED header if available
            if (!q && currentFilter === 'all' && recentItems.length > 0) {
                html += '<div class="jero-title-section-header">Recently Used</div>';
                for (var r = 0; r < recentItems.length; r++) {
                    var rec = recentItems[r];
                    var isSel = (rec.title.toLowerCase() === currentVal);
                    html += buildItemHtml(rec, '', r, isSel, true);
                }
                html += '<div class="jero-title-section-header">Standard & Organization Library</div>';
            }

            for (var i = 0; i < currentMatches.length; i++) {
                var item = currentMatches[i];
                var isSelected = (item.title.toLowerCase() === currentVal);
                html += buildItemHtml(item, q, i, isSelected, false);
            }

            container.innerHTML = html;
            adjustDropdownPosition();
        }

        function buildItemHtml(item, query, index, isSelected, isRecentSection) {
            var badgeHtml = '';
            if (item.has_template === 1) {
                badgeHtml += '<span class="jero-title-badge badge-autofill">Auto-fill ✓</span>';
            }
            if (item.is_standard === 0) {
                badgeHtml += '<span class="jero-title-badge badge-source-org">My Org</span>';
            }

            var checkHtml = isSelected
                ? '<span class="jero-title-item-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>'
                : '';

            var itemIdxAttr = isRecentSection ? ('data-recent-idx="' + index + '"') : ('data-idx="' + index + '"');

            return '<div class="jero-title-item ' + (isSelected ? 'selected' : '') + '" ' + itemIdxAttr + ' data-id="' + item.id + '" role="option" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
                '<div class="jero-title-item-main">' +
                    '<div class="jero-title-item-name">' + highlightMatch(item.title, query) + '</div>' +
                    '<div class="jero-title-item-sub">' + escapeHtml(item.dept_name || 'Standard Library') + '</div>' +
                '</div>' +
                '<div class="jero-title-item-side">' +
                    badgeHtml +
                    checkHtml +
                '</div>' +
            '</div>';
        }

        function adjustDropdownPosition() {
            var wrap = document.getElementById('jero_title_select_wrap');
            var dropdown = document.getElementById('jero_title_dropdown');
            var container = document.getElementById('jero_title_list_container');
            if (!wrap || !dropdown || !isOpen) return;

            var rect = wrap.getBoundingClientRect();
            var viewportH = window.innerHeight || document.documentElement.clientHeight || 768;

            // Safe margins from viewport top/bottom edges (12px safe margin)
            var spaceBelow = Math.max(0, viewportH - rect.bottom - 12);
            var spaceAbove = Math.max(0, rect.top - 12);

            // Calculate height of fixed sections (tabs + custom title pill + footer + padding)
            var tabsEl = dropdown.querySelector('.jero-title-tabs');
            var customEl = document.getElementById('jero_title_custom_action');
            var footerEl = dropdown.querySelector('.jero-title-footer');

            var fixedH = (tabsEl ? tabsEl.offsetHeight : 34) +
                         ((customEl && customEl.style.display !== 'none') ? customEl.offsetHeight : 0) +
                         (footerEl ? footerEl.offsetHeight : 32) + 12;

            // Decision: Prefer drop DOWN unless space below is severely constrained (< 220px) AND space above is significantly larger
            var dropUp = false;
            if (spaceBelow < 220 && spaceAbove > spaceBelow && spaceAbove > 180) {
                dropUp = true;
            }

            if (dropUp) {
                dropdown.classList.add('drop-up');
                dropdown.style.top = 'auto';
                dropdown.style.bottom = 'calc(100% + 4px)';

                // Clamp max height so dropdown top NEVER extends above viewport top margin
                var maxAvailable = Math.max(160, Math.min(360, spaceAbove));
                dropdown.style.maxHeight = maxAvailable + 'px';
                if (container) {
                    var listH = Math.max(70, maxAvailable - fixedH);
                    container.style.maxHeight = listH + 'px';
                }
            } else {
                dropdown.classList.remove('drop-up');
                dropdown.style.top = 'calc(100% + 4px)';
                dropdown.style.bottom = 'auto';

                // Clamp max height so dropdown bottom NEVER extends below viewport bottom margin
                var maxAvailable = Math.max(160, Math.min(360, spaceBelow));
                dropdown.style.maxHeight = maxAvailable + 'px';
                if (container) {
                    var listH = Math.max(70, maxAvailable - fixedH);
                    container.style.maxHeight = listH + 'px';
                }
            }
        }

        function openDropdown() {
            var dropdown = document.getElementById('jero_title_dropdown');
            var wrap = document.getElementById('jero_title_select_wrap');
            var input = document.getElementById('title');
            if (!dropdown || !wrap) return;

            isOpen = true;
            wrap.classList.add('is-open');
            dropdown.style.display = 'flex';
            if (input) input.setAttribute('aria-expanded', 'true');

            adjustDropdownPosition();
            renderDropdownList();
        }

        function closeDropdown() {
            var dropdown = document.getElementById('jero_title_dropdown');
            var wrap = document.getElementById('jero_title_select_wrap');
            var container = document.getElementById('jero_title_list_container');
            var input = document.getElementById('title');
            if (!dropdown || !wrap) return;

            isOpen = false;
            wrap.classList.remove('is-open');
            dropdown.style.display = 'none';
            dropdown.style.maxHeight = '';
            if (container) container.style.maxHeight = '';
            if (input) input.setAttribute('aria-expanded', 'false');
            focusedIndex = -1;
        }

        // =========================================================================
        // Safe Auto-Fill Contract & Transactional Undo Protocol
        // =========================================================================
        function showUndoToast(msg) {
            var toast = document.getElementById('jero_autofill_undo_toast');
            if (!toast) return;

            clearTimeout(undoTimer);
            toast.querySelector('.undo-msg').textContent = msg || 'Template applied: Job description • Recommended skills';
            toast.style.display = 'flex';

            undoTimer = setTimeout(function() {
                toast.style.display = 'none';
            }, 5000);
        }

        function hideUndoToast() {
            var toast = document.getElementById('jero_autofill_undo_toast');
            if (toast) toast.style.display = 'none';
            clearTimeout(undoTimer);
        }

        function performUndo() {
            if (!priorSnapshot) return;

            $('#title').val(priorSnapshot.title);
            var ed = document.getElementById('jobDescription');
            if (ed) {
                ed.innerHTML = priorSnapshot.jd;
                if (typeof syncDescription === 'function') syncDescription();
            }

            if (Array.isArray(priorSnapshot.skills) && typeof skills !== 'undefined') {
                skills.length = 0;
                priorSnapshot.skills.forEach(function(s) { skills.push(s); });
                if (typeof renderSkills === 'function') renderSkills();
            }

            hideUndoToast();
            updateStatusChip(false);
            priorSnapshot = null;
        }

        function updateStatusChip(isActive, isCustom) {
            var chip = document.getElementById('jero_title_status_chip');
            var text = document.getElementById('jero_title_status_text');
            if (!chip || !text) return;

            if (isActive) {
                chip.style.display = 'inline-flex';
                chip.style.background = isCustom ? '#F1F5F9' : '#ECFDF5';
                chip.style.borderColor = isCustom ? '#CBD5E1' : '#A7F3D0';
                chip.style.color = isCustom ? '#475569' : '#10B981';
                text.textContent = isCustom ? 'Custom designation' : 'Template active';
            } else {
                chip.style.display = 'none';
            }
        }

        function applyTemplateSafely(item, forceOverwrite) {
            if (isApplyingTemplate) return;
            isApplyingTemplate = true;
            window._isApplyingTitleTemplate = true;

            try {
                var ed = document.getElementById('jobDescription');
                var currentDesc = ed ? (ed.innerText || ed.textContent || '').trim() : '';
                var currentSkills = (typeof skills !== 'undefined' && Array.isArray(skills)) ? [...skills] : [];

                priorSnapshot = {
                    title: $('#title').val(),
                    jd: ed ? ed.innerHTML : '',
                    skills: currentSkills
                };

                // Detect if user has manually entered custom copy in JD
                if (!forceOverwrite && currentDesc !== '' && item.jd && item.jd.trim() !== '') {
                    pendingTemplateItem = item;
                    var dialog = document.getElementById('jero_autofill_confirm_dialog');
                    if (dialog) dialog.style.display = 'block';
                    return;
                }

                // Apply Description
                if (item.jd && (currentDesc === '' || forceOverwrite)) {
                    if (ed) {
                        ed.innerHTML = item.jd;
                        if (typeof syncDescription === 'function') syncDescription();
                    }
                }

                // Apply Skills (Merge unique)
                if (item.skills && typeof skills !== 'undefined') {
                    var newSkills = item.skills.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
                    var existingSet = new Set(skills.map(function(s) { return s.toLowerCase(); }));
                    var added = false;
                    newSkills.forEach(function(s) {
                        if (!existingSet.has(s.toLowerCase())) {
                            skills.push(s);
                            existingSet.add(s.toLowerCase());
                            added = true;
                        }
                    });
                    if (added && typeof renderSkills === 'function') renderSkills();
                }

                showUndoToast('Template applied: Job description • Recommended skills');
                updateStatusChip(true, false);
            } finally {
                setTimeout(function() {
                    isApplyingTemplate = false;
                    window._isApplyingTitleTemplate = false;
                }, 150);
            }
        }

        function selectItem(item) {
            if (!item) return;

            $('#title').val(item.title);
            saveRecentTitle(item);
            closeDropdown();

            var clearBtn = document.getElementById('jero_title_clear_btn');
            if (clearBtn) clearBtn.style.display = 'flex';

            if (item.has_template === 1) {
                applyTemplateSafely(item, false);
            } else {
                updateStatusChip(true, true);
            }

            trackTelemetry('title_selected', { id: item.id, title: item.title, is_standard: item.is_standard });
        }

        function selectCustomTitle(customTitle) {
            var clean = (customTitle || '').trim();
            if (!clean) return;

            $('#title').val(clean);
            closeDropdown();

            var clearBtn = document.getElementById('jero_title_clear_btn');
            if (clearBtn) clearBtn.style.display = 'flex';

            updateStatusChip(true, true);
            trackTelemetry('custom_title_created', { title: clean });
        }

        function trackTelemetry(event, payload) {
            try {
                var buffer = JSON.parse(sessionStorage.getItem('jero_title_telemetry') || '[]');
                buffer.push({ event: event, payload: payload, ts: Date.now() });
                if (buffer.length > 20) buffer = buffer.slice(-20);
                sessionStorage.setItem('jero_title_telemetry', JSON.stringify(buffer));
            } catch (e) {}
        }

        function ingestManifest(manifest) {
            if (!manifest || !Array.isArray(manifest.titles)) return;
            titlesList = manifest.titles;
            buildInvertedIndex(titlesList);
            updateTabCounts();
        }

        async function initCacheAndSync() {
            var cacheKey = 'titles_tenant_' + CURRENT_TENANT_ID;

            // 1. Check Pre-Seeded HTML DOM script tag (0ms Cold Start)
            var seedEl = document.getElementById('jero_titles_seed_data');
            if (seedEl && seedEl.textContent.trim()) {
                try {
                    var seedManifest = JSON.parse(seedEl.textContent);
                    if (seedManifest && Array.isArray(seedManifest.titles) && seedManifest.titles.length > 0) {
                        ingestManifest(seedManifest);
                        idbSet(cacheKey, { manifest: seedManifest, timestamp: Date.now() });
                        checkServerVersion(seedManifest.version);
                        return;
                    }
                } catch (e) {}
            }

            // 2. Check IndexedDB L1 Persistent Store
            try {
                var cached = await idbGet(cacheKey);
                if (cached && cached.manifest && Array.isArray(cached.manifest.titles)) {
                    ingestManifest(cached.manifest);
                    checkServerVersion(cached.manifest.version);
                    return;
                }
            } catch (err) {}

            // 3. Fallback: Fetch bundle from server
            fetchBundleFromServer(1);
        }

        function checkServerVersion(localVersion) {
            fetch(BUNDLE_URL, {
                method: 'GET',
                headers: {
                    'If-None-Match': 'W/"tindex_' + CURRENT_TENANT_ID + '_s4_titles-tenant-' + CURRENT_TENANT_ID + '-v' + localVersion + '"'
                }
            }).then(function(res) {
                if (res.status === 304) return null;
                if (res.ok) return res.json();
                return null;
            }).then(function(newManifest) {
                if (newManifest && Array.isArray(newManifest.titles)) {
                    ingestManifest(newManifest);
                    idbSet('titles_tenant_' + CURRENT_TENANT_ID, { manifest: newManifest, timestamp: Date.now() });
                    if (indexBroadcast) {
                        indexBroadcast.postMessage({ type: 'TITLES_UPDATED', version: newManifest.version, tabId: TAB_SESSION_ID });
                    }
                }
            }).catch(function() {});
        }

        function fetchBundleFromServer(version) {
            var statusEl = document.getElementById('jero_title_sync_status');
            if (statusEl) statusEl.innerHTML = '<span style="font-size:10px;">Syncing latest catalog...</span>';

            fetch(BUNDLE_URL)
                .then(function(res) { return res.json(); })
                .then(function(manifest) {
                    if (manifest && Array.isArray(manifest.titles)) {
                        ingestManifest(manifest);
                        idbSet('titles_tenant_' + CURRENT_TENANT_ID, { manifest: manifest, timestamp: Date.now() });
                    }
                    if (statusEl) statusEl.innerHTML = '';
                })
                .catch(function() {
                    if (statusEl) statusEl.innerHTML = '';
                });
        }

        function initEventListeners() {
            if (isEventListenersInitialized) return;
            isEventListenersInitialized = true;

            var input = document.getElementById('title');
            var clearBtn = document.getElementById('jero_title_clear_btn');
            var tabsContainer = document.querySelector('.jero-title-tabs');
            var container = document.getElementById('jero_title_list_container');
            var customWrap = document.getElementById('jero_title_custom_action');
            var undoBtn = document.getElementById('jero_autofill_undo_btn');
            var keepBtn = document.getElementById('jero_confirm_keep_btn');
            var applyBtn = document.getElementById('jero_confirm_apply_btn');

            if (input) {
                input.addEventListener('focus', function() {
                    currentSearch = this.value;
                    openDropdown();
                });

                input.addEventListener('input', function() {
                    currentSearch = this.value;
                    if (clearBtn) clearBtn.style.display = currentSearch ? 'flex' : 'none';
                    if (!isOpen) openDropdown();

                    clearTimeout(inputDebounceTimer);
                    inputDebounceTimer = setTimeout(function() {
                        if (inputRafId) cancelAnimationFrame(inputRafId);
                        inputRafId = requestAnimationFrame(function() {
                            renderDropdownList();
                            inputRafId = null;
                        });
                    }, 35);
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeDropdown();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (!isOpen) { openDropdown(); return; }
                        moveFocus(1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (!isOpen) { openDropdown(); return; }
                        moveFocus(-1);
                    } else if (e.key === 'Enter') {
                        if (isOpen) {
                            e.preventDefault();
                            selectFocusedOrCustom();
                        }
                    } else if (e.key === 'Tab') {
                        if (isOpen) closeDropdown();
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                    currentSearch = '';
                    this.style.display = 'none';
                    updateStatusChip(false);
                    renderDropdownList();
                });
            }

            if (tabsContainer) {
                tabsContainer.addEventListener('click', function(e) {
                    var btn = e.target.closest('.jero-title-tab-btn');
                    if (!btn) return;
                    tabsContainer.querySelectorAll('.jero-title-tab-btn').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    currentFilter = btn.getAttribute('data-filter') || 'all';
                    renderDropdownList();
                });
            }

            if (container) {
                container.addEventListener('click', function(e) {
                    var itemEl = e.target.closest('.jero-title-item');
                    if (!itemEl) return;
                    var id = parseInt(itemEl.getAttribute('data-id'), 10);
                    var item = titlesMap.get(id);
                    if (!item && itemEl.hasAttribute('data-recent-idx')) {
                        var recentIdx = parseInt(itemEl.getAttribute('data-recent-idx'), 10);
                        item = getRecentTitles()[recentIdx];
                    }
                    if (item) selectItem(item);
                });
            }

            if (customWrap) {
                customWrap.addEventListener('click', function() {
                    var q = currentSearch.trim();
                    if (q) selectCustomTitle(q);
                });
            }

            if (undoBtn) {
                undoBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    performUndo();
                });
            }

            if (keepBtn) {
                keepBtn.addEventListener('click', function() {
                    var dialog = document.getElementById('jero_autofill_confirm_dialog');
                    if (dialog) dialog.style.display = 'none';
                    if (pendingTemplateItem) {
                        applyTemplateSafely(pendingTemplateItem, false);
                        pendingTemplateItem = null;
                    }
                });
            }

            if (applyBtn) {
                applyBtn.addEventListener('click', function() {
                    var dialog = document.getElementById('jero_autofill_confirm_dialog');
                    if (dialog) dialog.style.display = 'none';
                    if (pendingTemplateItem) {
                        applyTemplateSafely(pendingTemplateItem, true);
                        pendingTemplateItem = null;
                    }
                });
            }

            document.addEventListener('click', function(e) {
                var wrap = document.getElementById('jero_title_select_wrap');
                if (wrap && !wrap.contains(e.target)) {
                    closeDropdown();
                }
            });

            try {
                indexBroadcast = new BroadcastChannel('jero-titles-sync');
                indexBroadcast.onmessage = function(ev) {
                    if (ev && ev.data && ev.data.type === 'TITLES_UPDATED' && ev.data.tabId !== TAB_SESSION_ID) {
                        var now = Date.now();
                        if (now - lastSyncTimestamp < 8000) return;
                        lastSyncTimestamp = now;
                        idbGet('titles_tenant_' + CURRENT_TENANT_ID).then(function(cached) {
                            if (cached && cached.manifest) ingestManifest(cached.manifest);
                        });
                    }
                };
            } catch (e) {}
        }

        function moveFocus(direction) {
            var items = document.querySelectorAll('#jero_title_list_container .jero-title-item');
            var customPill = document.getElementById('jero_title_custom_pill');
            var total = items.length + (customPill && customPill.offsetParent !== null ? 1 : 0);
            if (total === 0) return;

            focusedIndex = focusedIndex + direction;
            if (focusedIndex >= total) focusedIndex = 0;
            if (focusedIndex < 0) focusedIndex = total - 1;

            items.forEach(function(el) { el.classList.remove('focused'); });
            if (customPill) customPill.classList.remove('focused');

            if (focusedIndex < items.length) {
                var el = items[focusedIndex];
                el.classList.add('focused');
                el.scrollIntoView({ block: 'nearest' });
            } else if (customPill) {
                customPill.classList.add('focused');
                customPill.scrollIntoView({ block: 'nearest' });
            }
        }

        function selectFocusedOrCustom() {
            var items = document.querySelectorAll('#jero_title_list_container .jero-title-item');
            if (focusedIndex >= 0 && focusedIndex < items.length) {
                var itemEl = items[focusedIndex];
                var id = parseInt(itemEl.getAttribute('data-id'), 10);
                var item = titlesMap.get(id);
                if (item) { selectItem(item); return; }
            }

            var customPill = document.getElementById('jero_title_custom_pill');
            if (customPill && customPill.classList.contains('focused')) {
                selectCustomTitle(currentSearch);
                return;
            }

            if (currentMatches.length > 0) {
                selectItem(currentMatches[0]);
            } else if (currentSearch.trim()) {
                selectCustomTitle(currentSearch.trim());
            }
        }

        // Global export for backward compatibility & testing
        window.JeroTitleIntelligenceEngine = {
            search: function(q) {
                currentSearch = q;
                renderDropdownList();
            },
            selectByTitle: function(title) {
                for (var i = 0; i < titlesList.length; i++) {
                    if (titlesList[i].title.toLowerCase() === (title || '').toLowerCase()) {
                        selectItem(titlesList[i]);
                        return;
                    }
                }
                selectCustomTitle(title);
            },
            hasLocalTemplate: function(title) {
                for (var i = 0; i < titlesList.length; i++) {
                    if (titlesList[i].title.toLowerCase() === (title || '').toLowerCase()) {
                        return titlesList[i].has_template === 1;
                    }
                }
                return false;
            },
            applyLocalTemplate: function(title) {
                for (var i = 0; i < titlesList.length; i++) {
                    if (titlesList[i].title.toLowerCase() === (title || '').toLowerCase()) {
                        applyTemplateSafely(titlesList[i], false);
                        return;
                    }
                }
            },
            refresh: function() {
                fetchBundleFromServer(1);
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initCacheAndSync();
                initEventListeners();
            });
        } else {
            initCacheAndSync();
            initEventListeners();
        }
    })();
    </script>

    <!-- =========================================================================
         Job Department Intelligence Engine (Client-First Execution / 10K Concurrency)
         ========================================================================= -->
    <script id="jero_dept_seed_data" type="application/json">
        {!! json_encode(@$departmentsManifest ?? ['manifest_id'=>'empty','version'=>1,'departments'=>[],'divisions'=>[]]) !!}
    </script>

    <script>
    (function() {
        'use strict';

        var CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        var CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
        var BUNDLE_URL = "{{ route('service_company.jobs.departmentsBundle') }}";

        // Internal State
        var deptList = [];
        var divisionsList = [];
        var divisionsMap = new Map();
        var deptMap = new Map();
        var currentSearch = '';
        var currentFilter = 'all'; // 'all' | 'company' | 'standard'
        var focusedIndex = -1;
        var currentMatches = [];
        var isOpen = false;
        var deptBroadcast = null;
        var localManifestVersion = 1;
        var lastFocusCheck = 0;

        // Recency storage key
        var RECENT_KEY = 'jero_recent_depts_' + CURRENT_USER_ID;

        // Division Icons Dictionary (Metronic & Modern SVG Icons)
        var DIVISION_ICONS = {
            1: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>', bg: '#eff6ff', color: '#2563eb' }, // Engineering & Tech
            2: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>', bg: '#fdf4ff', color: '#c026d3' }, // Product & Design
            3: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>', bg: '#ecfdf5', color: '#059669' }, // Sales & BD
            4: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>', bg: '#fffbeb', color: '#d97706' }, // Marketing & Growth
            5: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>', bg: '#ecfeff', color: '#0891b2' }, // Operations & Logistics
            6: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>', bg: '#f1f5f9', color: '#475569' }, // Finance & Legal
            7: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', bg: '#fff1f2', color: '#e11d48' }, // People & HR
            8: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>', bg: '#eef2ff', color: '#4f46e5' }, // Customer Success
            9: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>', bg: '#f0fdfa', color: '#0d9488' }, // Healthcare & Clinical
            10: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>', bg: '#f5f3ff', color: '#7c3aed' } // Corporate & Strategy
        };
        var DEFAULT_DIV_ICON = { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>', bg: '#f1f5f9', color: '#64748b' };

        function getRecentDepts() {
            try {
                var raw = localStorage.getItem(RECENT_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentDept(item) {
            try {
                var list = getRecentDepts();
                list = list.filter(function(r) { return r.id !== item.id; });
                list.unshift({
                    id: item.id,
                    name: item.name,
                    division_id: item.division_id,
                    division_name: item.division_name,
                    is_standard: item.is_standard,
                    timestamp: Date.now()
                });
                if (list.length > 5) list = list.slice(0, 5);
                localStorage.setItem(RECENT_KEY, JSON.stringify(list));
            } catch (e) {}
        }

        // =========================================================================
        // IndexedDB Persistent Storage (JeroLocalStore_v2 - departments)
        // =========================================================================
        var DB_NAME = 'JeroLocalStore_v2';
        var STORE_NAME = 'departments';
        var idbInstance = null;

        function openDatabase() {
            return new Promise(function(resolve, reject) {
                if (idbInstance) return resolve(idbInstance);
                if (!window.indexedDB) return reject(new Error('IndexedDB not supported'));

                try {
                    var req = window.indexedDB.open(DB_NAME, 3);
                    req.onupgradeneeded = function(e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains('clients')) {
                            db.createObjectStore('clients', { keyPath: 'key' });
                        }
                        if (!db.objectStoreNames.contains('job_titles')) {
                            db.createObjectStore('job_titles', { keyPath: 'key' });
                        }
                        if (!db.objectStoreNames.contains('departments')) {
                            db.createObjectStore('departments', { keyPath: 'key' });
                        }
                    };
                    req.onsuccess = function(e) {
                        idbInstance = e.target.result;
                        resolve(idbInstance);
                    };
                    req.onerror = function(e) {
                        reject(e.target ? e.target.error : new Error('IDB open error'));
                    };
                } catch (err) {
                    reject(err);
                }
            });
        }

        function idbGet(key) {
            return openDatabase().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        var tx = db.transaction(STORE_NAME, 'readonly');
                        var store = tx.objectStore(STORE_NAME);
                        var req = store.get(key);
                        req.onsuccess = function() { resolve(req.result ? req.result.value : null); };
                        req.onerror = function() { resolve(null); };
                    } catch (err) {
                        resolve(null);
                    }
                });
            }).catch(function() {
                try {
                    var fallback = sessionStorage.getItem('idb_fallback_dept_' + key);
                    return fallback ? JSON.parse(fallback) : null;
                } catch (e) { return null; }
            });
        }

        function idbSet(key, value) {
            return openDatabase().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        var tx = db.transaction(STORE_NAME, 'readwrite');
                        var store = tx.objectStore(STORE_NAME);
                        store.put({ key: key, value: value });
                        tx.oncomplete = function() { resolve(true); };
                        tx.onerror = function() { resolve(false); };
                    } catch (err) {
                        resolve(false);
                    }
                });
            }).catch(function() {
                try {
                    sessionStorage.setItem('idb_fallback_dept_' + key, JSON.stringify(value));
                } catch (e) {}
            });
        }

        // =========================================================================
        // In-Memory Search Engine (< 2ms Latency)
        // =========================================================================
        function scoreCandidate(item, query, queryTokens, recentIdSet) {
            var score = 0;
            var nameLower = (item.name || '').toLowerCase();
            var divLower = (item.division_name || '').toLowerCase();
            var kwLower = (item.keywords || '').toLowerCase();

            // 1. Exact Name Match
            if (nameLower === query) {
                score += 150;
            } else if (nameLower.indexOf(query) === 0) {
                score += 100;
            } else if (nameLower.indexOf(query) > 0) {
                score += 70;
            }

            // 2. Multi-Token Set Match
            var allTokensMatched = true;
            for (var t = 0; t < queryTokens.length; t++) {
                var tok = queryTokens[t];
                if (nameLower.indexOf(tok) !== -1) {
                    score += 25;
                } else if (divLower.indexOf(tok) !== -1) {
                    score += 15;
                } else if (kwLower.indexOf(tok) !== -1) {
                    score += 20;
                } else {
                    allTokensMatched = false;
                }
            }
            if (allTokensMatched) score += 30;

            // 3. Organization Boost
            if (item.is_standard === 0) score += 15;

            // 4. Recency Bonus
            if (recentIdSet.has(item.id)) score += 35;

            return score;
        }

        function searchDepartments(query, filter) {
            var baseList = deptList;
            if (filter === 'company') {
                baseList = deptList.filter(function(r) { return r.is_standard === 0; });
            } else if (filter === 'standard') {
                baseList = deptList.filter(function(r) { return r.is_standard === 1; });
            }

            var recentItems = getRecentDepts();
            var recentIdSet = new Set(recentItems.map(function(r) { return r.id; }));

            var cleanQ = (query || '').trim().toLowerCase();
            if (!cleanQ) {
                return baseList;
            }

            var queryTokens = cleanQ.split(/[^a-z0-9]+/i).filter(Boolean);
            if (queryTokens.length === 0) return baseList;

            var scored = [];
            for (var i = 0; i < baseList.length; i++) {
                var item = baseList[i];
                var score = scoreCandidate(item, cleanQ, queryTokens, recentIdSet);
                if (score > 15) {
                    scored.push({ item: item, score: score });
                }
            }

            scored.sort(function(a, b) { return b.score - a.score; });
            return scored.map(function(s) { return s.item; });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeRegex(str) {
            if (!str) return '';
            return String(str).replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
        }

        function highlightMatch(text, query) {
            if (!query || !query.trim()) return escapeHtml(text);
            var safeText = escapeHtml(text);
            var safeQ = escapeRegex(query.trim());
            try {
                var regex = new RegExp('(' + safeQ + ')', 'gi');
                return safeText.replace(regex, '<mark class="jero-dept-mark">$1</mark>');
            } catch (e) {
                return safeText;
            }
        }

        // =========================================================================
        // Render & Dropdown UI Engine
        // =========================================================================
        function updateTabCounts() {
            var stdCount = 0;
            var orgCount = 0;
            for (var i = 0; i < deptList.length; i++) {
                if (deptList[i].is_standard === 1) stdCount++;
                else orgCount++;
            }
            var elAll = document.getElementById('jero_dept_count_all');
            var elStd = document.getElementById('jero_dept_count_standard');
            var elComp = document.getElementById('jero_dept_count_company');
            if (elAll) elAll.textContent = deptList.length;
            if (elStd) elStd.textContent = stdCount;
            if (elComp) elComp.textContent = orgCount;
        }

        function updateRecentBar() {
            var bar = document.getElementById('jero_dept_recent_bar');
            if (!bar) return;

            var q = currentSearch.trim();
            if (q || currentFilter !== 'all') {
                bar.style.display = 'none';
                return;
            }

            var recentItems = getRecentDepts();
            if (!recentItems || recentItems.length === 0) {
                bar.style.display = 'none';
                return;
            }

            var html = '<span class="jero-dept-recent-label">Recent:</span>';
            var count = Math.min(recentItems.length, 3);
            for (var r = 0; r < count; r++) {
                var item = recentItems[r];
                var iconData = DIVISION_ICONS[item.division_id] || DEFAULT_DIV_ICON;
                html += '<span class="jero-dept-recent-chip" data-id="' + item.id + '" title="' + escapeHtml(item.division_name || '') + '">' +
                    '<span style="display:inline-flex;width:12px;height:12px;color:' + iconData.color + ';">' + iconData.icon + '</span> ' +
                    escapeHtml(item.name) +
                '</span>';
            }

            bar.innerHTML = html;
            bar.style.display = 'flex';
        }

        function renderDropdownList() {
            var container = document.getElementById('jero_dept_list_container');
            if (!container) return;

            updateRecentBar();
            currentMatches = searchDepartments(currentSearch, currentFilter);
            focusedIndex = -1;

            var q = currentSearch.trim();
            var html = '';

            // 1. Inline Quick-Add Card when user is searching and no exact match exists
            var exactMatchExists = false;
            if (q) {
                for (var d = 0; d < deptList.length; d++) {
                    if ((deptList[d].name || '').toLowerCase() === q.toLowerCase()) {
                        exactMatchExists = true;
                        break;
                    }
                }

                if (!exactMatchExists) {
                    html += '<div class="jero-dept-create-action" id="jero_dept_create_action" data-create-name="' + escapeHtml(q) + '" role="button" tabindex="0">' +
                        '<div class="jero-dept-create-left">' +
                            '<div class="jero-dept-create-plus">+</div>' +
                            '<div>' +
                                '<div class="jero-dept-create-title">Create "<strong>' + escapeHtml(q) + '</strong>" as custom department</div>' +
                                '<div class="jero-dept-create-desc">Add to your organization\'s department library</div>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="jero-dept-create-btn">+ Add</button>' +
                    '</div>';
                }
            }

            // 2. Empty State when 0 matches and no query
            if (currentMatches.length === 0 && !q) {
                container.innerHTML = '<div class="jero-dept-empty-state">No departments available in this category.</div>';
                adjustDropdownPosition();
                return;
            }

            // 3. Empty State with matches
            if (currentMatches.length === 0 && q) {
                container.innerHTML = html + '<div class="jero-dept-empty-state">No existing departments match "' + escapeHtml(q) + '". Click above to create it!</div>';
                adjustDropdownPosition();
                return;
            }

            var currentVal = ($('#category_id').val() || '').trim();

            // Group by division when query is empty
            var currentDivId = null;
            for (var i = 0; i < currentMatches.length; i++) {
                var item = currentMatches[i];
                if (!q && item.division_id !== currentDivId) {
                    currentDivId = item.division_id;
                    var divIconData = DIVISION_ICONS[item.division_id] || DEFAULT_DIV_ICON;
                    html += '<div class="jero-dept-section-header">' +
                        '<span class="jero-dept-div-icon" style="background:' + divIconData.bg + '; color:' + divIconData.color + ';">' +
                            divIconData.icon +
                        '</span>' +
                        '<span>' + escapeHtml(item.division_name || 'General') + '</span>' +
                    '</div>';
                }
                var isSelected = (String(item.id) === currentVal);
                html += buildItemHtml(item, q, i, isSelected);
            }

            container.innerHTML = html;
            adjustDropdownPosition();
        }

        function buildItemHtml(item, query, index, isSelected) {
            var badgeHtml = item.is_standard === 0
                ? '<span class="jero-dept-badge badge-dept-org">My Org</span>'
                : '<span class="jero-dept-badge badge-dept-std">Standard</span>';

            var checkHtml = isSelected
                ? '<span class="jero-dept-item-check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>'
                : '';

            return '<div class="jero-dept-item ' + (isSelected ? 'selected' : '') + '" data-idx="' + index + '" data-id="' + item.id + '" role="option" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
                '<div class="jero-dept-item-main">' +
                    '<div class="jero-dept-item-name">' + highlightMatch(item.name, query) + '</div>' +
                    '<div class="jero-dept-item-sub">' + escapeHtml(item.division_name || 'General') + '</div>' +
                '</div>' +
                '<div class="jero-dept-item-side">' +
                    badgeHtml +
                    checkHtml +
                '</div>' +
            '</div>';
        }

        function scrollItemIntoView(container, target) {
            if (!container || !target) return;
            var cTop = container.scrollTop;
            var cHeight = container.clientHeight;
            var elTop = target.offsetTop;
            var elHeight = target.offsetHeight;

            if (elTop < cTop) {
                container.scrollTop = elTop;
            } else if ((elTop + elHeight) > (cTop + cHeight)) {
                container.scrollTop = (elTop + elHeight) - cHeight;
            }
        }

        function adjustDropdownPosition() {
            var wrap = document.getElementById('jero_dept_select_wrap');
            var dropdown = document.getElementById('jero_dept_dropdown');
            var container = document.getElementById('jero_dept_list_container');
            if (!wrap || !dropdown || !isOpen) return;

            var rect = wrap.getBoundingClientRect();
            var viewportH = window.innerHeight || document.documentElement.clientHeight || 768;

            // Safe margins from viewport top/bottom edges
            var spaceBelow = Math.max(0, viewportH - rect.bottom - 12);
            var spaceAbove = Math.max(0, rect.top - 12);

            // Calculate height of fixed headers (tabs + recent bar + footer)
            var tabsEl = dropdown.querySelector('.jero-dept-tabs');
            var recentEl = document.getElementById('jero_dept_recent_bar');
            var footerEl = dropdown.querySelector('.jero-dept-footer');

            var fixedH = (tabsEl ? tabsEl.offsetHeight : 38) +
                         ((recentEl && recentEl.style.display !== 'none') ? recentEl.offsetHeight : 0) +
                         (footerEl ? footerEl.offsetHeight : 34) + 16;

            // Decision: Prefer drop DOWN unless space below is severely constrained (< 220px) AND space above is significantly larger
            var dropUp = false;
            if (spaceBelow < 220 && spaceAbove > spaceBelow && spaceAbove > 180) {
                dropUp = true;
            }

            if (dropUp) {
                dropdown.classList.add('drop-up');
                dropdown.style.top = 'auto';
                dropdown.style.bottom = 'calc(100% + 5px)';

                // Clamp max height so dropdown top never extends above viewport top margin
                var maxAvailable = Math.max(160, Math.min(340, spaceAbove));
                dropdown.style.maxHeight = maxAvailable + 'px';
                if (container) {
                    var listH = Math.max(90, maxAvailable - fixedH);
                    container.style.maxHeight = listH + 'px';
                }
            } else {
                dropdown.classList.remove('drop-up');
                dropdown.style.bottom = 'auto';
                dropdown.style.top = 'calc(100% + 5px)';

                // Clamp max height so dropdown bottom never extends past viewport bottom margin
                var maxAvailable = Math.max(160, Math.min(340, spaceBelow));
                dropdown.style.maxHeight = maxAvailable + 'px';
                if (container) {
                    var listH = Math.max(90, maxAvailable - fixedH);
                    container.style.maxHeight = listH + 'px';
                }
            }
        }

        function openDropdown() {
            var dropdown = document.getElementById('jero_dept_dropdown');
            var wrap = document.getElementById('jero_dept_select_wrap');
            var input = document.getElementById('category_id_display');
            var container = document.getElementById('jero_dept_list_container');
            if (!dropdown || !wrap) return;

            isOpen = true;
            wrap.classList.add('is-open');
            dropdown.style.display = 'flex';
            if (input) {
                input.setAttribute('aria-expanded', 'true');
                input.classList.remove('is-invalid');

                // When opening, clear search query so user can view and choose from all 10 divisions
                currentSearch = '';
                try {
                    input.select();
                } catch (e) {}
            }

            renderDropdownList();
            adjustDropdownPosition();

            // Scroll currently selected department into view inside list
            if (container) {
                var selectedEl = container.querySelector('.jero-dept-item.selected');
                if (selectedEl) {
                    scrollItemIntoView(container, selectedEl);
                }
            }
        }

        function closeDropdown() {
            var dropdown = document.getElementById('jero_dept_dropdown');
            var wrap = document.getElementById('jero_dept_select_wrap');
            var input = document.getElementById('category_id_display');
            if (!dropdown || !wrap) return;

            isOpen = false;
            wrap.classList.remove('is-open');
            dropdown.style.display = 'none';
            if (input) input.setAttribute('aria-expanded', 'false');
            focusedIndex = -1;
        }

        function moveFocus(delta) {
            var container = document.getElementById('jero_dept_list_container');
            if (!container) return;
            var items = container.querySelectorAll('.jero-dept-item, .jero-dept-create-action');
            if (items.length === 0) return;

            if (focusedIndex >= 0 && focusedIndex < items.length) {
                items[focusedIndex].classList.remove('focused');
            }

            focusedIndex += delta;
            if (focusedIndex < 0) focusedIndex = items.length - 1;
            if (focusedIndex >= items.length) focusedIndex = 0;

            var target = items[focusedIndex];
            target.classList.add('focused');
            scrollItemIntoView(container, target);
        }

        function selectFocusedItem() {
            var container = document.getElementById('jero_dept_list_container');
            if (!container) return;
            var items = container.querySelectorAll('.jero-dept-item, .jero-dept-create-action');
            if (focusedIndex >= 0 && focusedIndex < items.length) {
                var el = items[focusedIndex];
                if (el.classList.contains('jero-dept-create-action')) {
                    var nameToCreate = el.getAttribute('data-create-name');
                    inlineCreateDepartment(nameToCreate);
                    return;
                }
                var id = el.getAttribute('data-id');
                if (id) {
                    var item = deptMap.get(parseInt(id, 10));
                    if (item) selectItem(item);
                }
            } else if (currentMatches.length > 0) {
                selectItem(currentMatches[0]);
            } else if (currentSearch.trim()) {
                inlineCreateDepartment(currentSearch.trim());
            }
        }

        function updateStatusChip(item) {
            var wrap = document.getElementById('jero_dept_status_wrap');
            var chip = document.getElementById('jero_dept_status_chip');
            if (!wrap || !chip) return;
            if (!item) {
                wrap.style.display = 'none';
                return;
            }
            wrap.style.display = 'block';
            if (item.is_standard === 1) {
                chip.className = 'jero-dept-status-chip chip-std';
                chip.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Standard Taxonomy';
            } else {
                chip.className = 'jero-dept-status-chip chip-org';
                chip.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/></svg> Organization Dept';
            }
        }

        function selectItem(item) {
            if (!item) return;

            var select = document.getElementById('category_id');
            var displayInput = document.getElementById('category_id_display');
            var clearBtn = document.getElementById('jero_dept_clear_btn');

            // 1. Update native select (add option if not present)
            if (select) {
                var exists = false;
                for (var o = 0; o < select.options.length; o++) {
                    if (select.options[o].value == item.id) {
                        select.selectedIndex = o;
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    var opt = document.createElement('option');
                    opt.value = item.id;
                    opt.text = item.name;
                    opt.selected = true;
                    select.appendChild(opt);
                }
                $(select).val(item.id).trigger('change');
            }

            // 2. Update display input
            if (displayInput) {
                displayInput.value = item.name;
                displayInput.classList.remove('is-invalid');
            }

            // 3. Update status chip
            updateStatusChip(item);

            // 4. Update clear button
            if (clearBtn) {
                clearBtn.style.display = 'flex';
            }

            // 5. Update review text
            if (typeof setText === 'function') {
                setText('rvDepartment', item.name);
            }

            // 6. Recency
            saveRecentDept(item);
            closeDropdown();
        }

        function clearSelection() {
            var select = document.getElementById('category_id');
            var displayInput = document.getElementById('category_id_display');
            var clearBtn = document.getElementById('jero_dept_clear_btn');

            if (select) {
                select.selectedIndex = 0;
                $(select).val('').trigger('change');
            }
            if (displayInput) {
                displayInput.value = '';
                displayInput.focus();
            }
            if (clearBtn) {
                clearBtn.style.display = 'none';
            }
            updateStatusChip(null);

            if (typeof setText === 'function') {
                setText('rvDepartment', '-');
            }

            currentSearch = '';
            renderDropdownList();
        }

        // =========================================================================
        // God-Level Inline Quick-Create Department Handler
        // =========================================================================
        function inlineCreateDepartment(name) {
            if (!name || !name.trim()) return;
            var cleanName = name.trim();
            var createBtn = document.querySelector('.jero-dept-create-btn');
            if (createBtn) {
                createBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Saving...';
                createBtn.disabled = true;
            }

            var csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                : '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('service_company.account.storeDept') }}",
                type: 'POST',
                data: {
                    _token: csrfToken,
                    dept_name: cleanName
                },
                success: function(res) {
                    if (res && res.status === 'success' && res.category_id) {
                        var newDept = {
                            id: parseInt(res.category_id, 10),
                            name: res.category_name || cleanName,
                            division_id: 1,
                            division_name: 'General',
                            is_standard: res.is_standard ? 1 : 0,
                            keywords: cleanName
                        };

                        if (!deptMap.has(newDept.id)) {
                            deptList.unshift(newDept);
                            deptMap.set(newDept.id, newDept);
                        }

                        updateTabCounts();
                        selectItem(newDept);

                        // Cross-tab broadcast & localStorage sync
                        if (deptBroadcast) {
                            deptBroadcast.postMessage({ type: 'DEPT_UPDATED', timestamp: Date.now() });
                        }
                        try {
                            localStorage.setItem('jero_dept_sync_timestamp', Date.now());
                        } catch(e) {}

                        if (typeof showToast === 'function') {
                            var msg = res.already_exists
                                ? (res.is_standard ? 'Selected standard global department: ' + newDept.name : 'Selected existing organization department: ' + newDept.name)
                                : 'Department "' + newDept.name + '" successfully created and selected!';
                            showToast(msg, res.already_exists ? 'info' : 'success');
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(res.message || 'Error creating department', 'error');
                        }
                    }
                },
                error: function() {
                    if (typeof showToast === 'function') {
                        showToast('Failed to create department. Please try again.', 'error');
                    }
                },
                complete: function() {
                    if (createBtn) {
                        createBtn.innerHTML = '+ Add';
                        createBtn.disabled = false;
                    }
                }
            });
        }

        function ingestManifest(manifest) {
            if (!manifest || !Array.isArray(manifest.departments)) return;
            deptList = manifest.departments;
            divisionsList = manifest.divisions || [];
            deptMap.clear();
            divisionsMap.clear();
            localManifestVersion = manifest.version || 1;

            for (var d = 0; d < divisionsList.length; d++) {
                divisionsMap.set(divisionsList[d].id, divisionsList[d]);
            }
            for (var i = 0; i < deptList.length; i++) {
                deptMap.set(deptList[i].id, deptList[i]);
            }

            updateTabCounts();

            // Check if initial selection needs name resolution & status chip
            var select = document.getElementById('category_id');
            var displayInput = document.getElementById('category_id_display');
            if (select && select.value) {
                var curr = deptMap.get(parseInt(select.value, 10));
                if (curr) {
                    if (displayInput && !displayInput.value) {
                        displayInput.value = curr.name;
                    }
                    var clearBtn = document.getElementById('jero_dept_clear_btn');
                    if (clearBtn) clearBtn.style.display = 'flex';
                    updateStatusChip(curr);
                }
            }
        }

        async function initCacheAndSync() {
            var cacheKey = 'dept_tenant_' + CURRENT_TENANT_ID;

            // 1. Check Pre-Seeded HTML DOM script tag (0ms Cold Start)
            var seedEl = document.getElementById('jero_dept_seed_data');
            if (seedEl && seedEl.textContent.trim()) {
                try {
                    var seedManifest = JSON.parse(seedEl.textContent);
                    if (seedManifest && Array.isArray(seedManifest.departments) && seedManifest.departments.length > 0) {
                        ingestManifest(seedManifest);
                        idbSet(cacheKey, { manifest: seedManifest, timestamp: Date.now() });
                        checkServerVersion(seedManifest.version);
                        return;
                    }
                } catch (e) {}
            }

            // 2. Check IndexedDB L1 Persistent Store (< 5ms)
            try {
                var cached = await idbGet(cacheKey);
                if (cached && cached.manifest && Array.isArray(cached.manifest.departments)) {
                    ingestManifest(cached.manifest);
                    checkServerVersion(cached.manifest.version);
                    return;
                }
            } catch (err) {}

            // 3. Fallback: Fetch bundle from server
            fetchBundleFromServer(1);
        }

        function checkServerVersion(localVersion) {
            fetch(BUNDLE_URL, {
                method: 'GET',
                headers: {
                    'If-None-Match': 'W/"tindex_' + CURRENT_TENANT_ID + '_s4_dept-tenant-' + CURRENT_TENANT_ID + '-v' + localVersion + '"'
                }
            }).then(function(res) {
                if (res.status === 304) return null;
                if (res.ok) return res.json();
                return null;
            }).then(function(newManifest) {
                if (newManifest && Array.isArray(newManifest.departments)) {
                    ingestManifest(newManifest);
                    idbSet('dept_tenant_' + CURRENT_TENANT_ID, { manifest: newManifest, timestamp: Date.now() });
                    if (deptBroadcast) {
                        deptBroadcast.postMessage({ type: 'DEPT_UPDATED', version: newManifest.version });
                    }
                }
            }).catch(function() {});
        }

        function fetchBundleFromServer(version) {
            var statusEl = document.getElementById('jero_dept_sync_status');
            if (statusEl) {
                statusEl.innerHTML = '<span class="jero-dept-sync-dot"></span><span>Syncing...</span>';
            }

            fetch(BUNDLE_URL)
                .then(function(res) { return res.json(); })
                .then(function(manifest) {
                    if (manifest && Array.isArray(manifest.departments)) {
                        ingestManifest(manifest);
                        idbSet('dept_tenant_' + CURRENT_TENANT_ID, { manifest: manifest, timestamp: Date.now() });
                    }
                    if (statusEl) {
                        statusEl.innerHTML = '<span class="jero-dept-sync-dot"></span><span>Live Synced</span>';
                    }
                })
                .catch(function() {
                    if (statusEl) {
                        statusEl.innerHTML = '<span class="jero-dept-sync-dot" style="background:#f59e0b;"></span><span>Cached</span>';
                    }
                });
        }

        function initEventListeners() {
            var input = document.getElementById('category_id_display');
            var clearBtn = document.getElementById('jero_dept_clear_btn');
            var chevron = document.getElementById('jero_dept_chevron');
            var tabsContainer = document.querySelector('.jero-dept-tabs');
            var container = document.getElementById('jero_dept_list_container');
            var recentBar = document.getElementById('jero_dept_recent_bar');

            if (input) {
                input.addEventListener('focus', function() {
                    if (!isOpen) {
                        openDropdown();
                    }
                });

                input.addEventListener('click', function(e) {
                    if (!isOpen) {
                        openDropdown();
                    }
                });

                input.addEventListener('input', function() {
                    currentSearch = this.value;
                    if (clearBtn) clearBtn.style.display = currentSearch ? 'flex' : 'none';
                    if (!isOpen) openDropdown();
                    renderDropdownList();
                });

                // Real-World Defense: Prevent Enter key from submitting form prematurely
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        closeDropdown();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (!isOpen) { openDropdown(); return; }
                        moveFocus(1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (!isOpen) { openDropdown(); return; }
                        moveFocus(-1);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (isOpen) {
                            selectFocusedItem();
                        } else {
                            openDropdown();
                        }
                    } else if (e.key === 'Tab') {
                        if (isOpen) closeDropdown();
                    }
                });
            }

            if (chevron) {
                chevron.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (isOpen) {
                        closeDropdown();
                    } else {
                        openDropdown();
                        if (input) input.focus();
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clearSelection();
                });
            }

            if (tabsContainer) {
                tabsContainer.addEventListener('click', function(e) {
                    var btn = e.target.closest('.jero-dept-tab-btn');
                    if (!btn) return;
                    tabsContainer.querySelectorAll('.jero-dept-tab-btn').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    currentFilter = btn.getAttribute('data-filter') || 'all';
                    renderDropdownList();
                });
            }

            if (recentBar) {
                recentBar.addEventListener('click', function(e) {
                    var chip = e.target.closest('.jero-dept-recent-chip');
                    if (!chip) return;
                    var id = chip.getAttribute('data-id');
                    if (id) {
                        var item = deptMap.get(parseInt(id, 10));
                        if (item) selectItem(item);
                    }
                });
            }

            if (container) {
                container.addEventListener('click', function(e) {
                    // Check for inline create action
                    var createAction = e.target.closest('.jero-dept-create-action');
                    if (createAction) {
                        var nameToCreate = createAction.getAttribute('data-create-name');
                        inlineCreateDepartment(nameToCreate);
                        return;
                    }

                    // Check for department item click
                    var itemEl = e.target.closest('.jero-dept-item');
                    if (!itemEl) return;
                    var id = itemEl.getAttribute('data-id');
                    if (id) {
                        var item = deptMap.get(parseInt(id, 10));
                        if (item) selectItem(item);
                    }
                });
            }

            // Global Escape key: Closes dropdown even if focus was moved to list or page
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isOpen) {
                    e.preventDefault();
                    closeDropdown();
                    if (input) input.blur();
                }
            });

            // Bullet-Proof Outside Click Handler in Capture Phase
            // Catches clicks before any child or ancestor calls stopPropagation
            document.addEventListener('pointerdown', function(e) {
                var wrap = document.getElementById('jero_dept_select_wrap');
                if (wrap && !wrap.contains(e.target)) {
                    if (isOpen) closeDropdown();
                }
            }, true);

            // Auto-close on page scroll so dropdown never covers other cards or floats detached
            window.addEventListener('scroll', function() {
                if (isOpen) {
                    closeDropdown();
                }
            }, { passive: true });

            // Keyboard Shortcut: '/' to focus Department Search
            document.addEventListener('keydown', function(e) {
                if (e.key === '/' && !isOpen) {
                    var activeTag = (document.activeElement && document.activeElement.tagName) ? document.activeElement.tagName.toLowerCase() : '';
                    if (activeTag !== 'input' && activeTag !== 'textarea' && activeTag !== 'select' && !document.activeElement.isContentEditable) {
                        e.preventDefault();
                        if (input) {
                            input.focus();
                        }
                    }
                }
            });

            // Window resize auto-adjustment
            window.addEventListener('resize', function() {
                if (isOpen) {
                    adjustDropdownPosition();
                }
            }, { passive: true });
        }

        function initSyncListeners() {
            // 1. Cross-tab BroadcastChannel
            if ('BroadcastChannel' in window) {
                try {
                    deptBroadcast = new BroadcastChannel('jero_dept_channel');
                    deptBroadcast.onmessage = function(ev) {
                        if (ev.data && ev.data.type === 'DEPT_UPDATED') {
                            fetchBundleFromServer(ev.data.version || 1);
                        }
                    };
                } catch (e) {}
            }

            // 2. Storage event fallback for older / restricted browsers
            window.addEventListener('storage', function(e) {
                if (e.key === 'jero_dept_sync_timestamp') {
                    fetchBundleFromServer(1);
                }
            });

            // 3. Tab focus reconciliation
            window.addEventListener('focus', function() {
                var now = Date.now();
                if (now - lastFocusCheck > 25000) {
                    lastFocusCheck = now;
                    checkServerVersion(localManifestVersion);
                }
            });

            // 4. Real-time Reverb WebSocket listener
            if (window.Echo && CURRENT_TENANT_ID > 0) {
                try {
                    window.Echo.private('company.' + CURRENT_TENANT_ID)
                        .listen('.department.mutated', function(e) {
                            fetchBundleFromServer(e.version || 1);
                        });
                } catch (e) {}
            }
        }

        // Global export for backward compatibility & testing
        window.JeroDeptIntelligenceEngine = {
            open: openDropdown,
            close: closeDropdown,
            isOpen: function() { return isOpen; },
            search: function(q) {
                currentSearch = q;
                renderDropdownList();
            },
            selectById: function(id) {
                var item = deptMap.get(parseInt(id, 10));
                if (item) selectItem(item);
            },
            selectByName: function(name) {
                for (var i = 0; i < deptList.length; i++) {
                    if ((deptList[i].name || '').toLowerCase() === (name || '').toLowerCase()) {
                        selectItem(deptList[i]);
                        return;
                    }
                }
            },
            inlineCreate: function(name) {
                inlineCreateDepartment(name);
            },
            refresh: function() {
                fetchBundleFromServer(1);
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initCacheAndSync();
                initEventListeners();
                initSyncListeners();
            });
        } else {
            initCacheAndSync();
            initEventListeners();
            initSyncListeners();
        }
    })();
    </script>
    {{-- Create-only enhancement script — placed LAST so all globals defined in the inline
         scripts above (currentStep, goToStep, __indexUrl, validateStep, etc.) are available
         when this file runs. No defer needed here because the script is already at the
         bottom of <body>. The onReady() guard inside the file handles both the
         readyState === 'loading' case and the already-interactive/complete case. --}}
    @if(request()->routeIs('service_company.jobs.create') || request()->is('service_company/jobs/create*'))
        <script src="{{ asset('js/internal-job-create-design.js') }}?v=20261012d"></script>
    @endif
@endpush
