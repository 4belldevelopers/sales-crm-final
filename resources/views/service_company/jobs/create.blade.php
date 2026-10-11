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

    /* Job-title suggestions (backend) */
    #jrwrap #suggestions { top: 64px; position: absolute; z-index: 1000; background-color: white; border: 1px solid #ccc; border-radius: 4px; max-height: 200px; width: 100%; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    #jrwrap #suggestions-list { list-style: none; padding: 0; margin: 0; }
    #jrwrap #suggestions-list li { padding: 10px 15px; cursor: pointer; transition: background-color 0.3s; }
    #jrwrap #suggestions-list li:hover { background-color: #f0f0f0; }

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
    </style>
    {{-- Create-only presentation assets placed AFTER the base styles so all override tokens take precedence. --}}
    @if(request()->routeIs('service_company.jobs.create') || request()->is('service_company/jobs/create*'))
        <style>
            #kt_toolbar { display: none !important; height: 0 !important; min-height: 0 !important; padding: 0 !important; margin: 0 !important; }
            .header-fixed.toolbar-fixed #kt_wrapper, .toolbar-fixed #kt_wrapper { padding-top: 65px !important; }
            #jrwrap.internal-job-create { margin-top: 0 !important; padding-top: 0 !important; }
        </style>
        <link rel="stylesheet" href="{{ asset('css/internal-job-create-design.css') }}?v=20261011g">
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
                                <div class="form-group-cus">
                                    <label class="form-label required">Select Client</label>
                                    <?php if ($global_company_type == 3) { ?>
                                        <input type="text" class="form-input" value="<?php echo @$client[0]->name; ?>" readonly>
                                        <input type="hidden" id="client_id" name="client_id" value="<?php echo @$client[0]->id; ?>">
                                    <?php } else { ?>
                                    <select class="form-select" id="client_id" name="client_id">
                                    @if (@$direct_add)
                                        <option value="">Select Client</option>
                                    @endif
                                        <optgroup label="Internal">
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
                                     <span class="form-hint">*Please select the client with openings</span>
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
                                    <span class="form-hint">Please select the address of your company/client</span>
                                </div>
                                <div class="form-group-cus" style="position:relative;">
                                    <label class="form-label required">Job Title / Designation</label>
                                    <input type="text" class="form-input" id="title" name="title" value="<?php echo @$job->title; ?>" placeholder="e.g. Senior Java Developer" onkeyup="getSuggestions(this.value)" autocomplete="off">
                                    <div id="suggestions" style="display:none;">
                                        <ul id="suggestions-list"></ul>
                                    </div>
                                    <span class="form-hint">*Please enter the job title or designation of the openings.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Job Department</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Select Department</option>
                                        <?php
                                        foreach ($categories as $category) {
                                            $category_id = $sel = '';
                                            $category_id = @$job->category_id;
                                            if ($category_id == @$category->id) { $sel = 'selected="selected"'; }
                                            echo '<option value="' . @$category->id . '" ' . $sel . '>' . ucfirst($category->name) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="form-hint">*Please select the department for this job.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>Job Location or Work Location
                            </div>
                            <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">Select up to 15 cities where this job is based.</p>
                            <div class="form-group-cus full-width">
                                <div class="input-wrapper" style="position:relative;">
                                    <div class="input-with-icon">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                        <input type="text" class="form-input" id="citySearch" placeholder="Search for cities..." autocomplete="off">
                                    </div>
                                    <button type="button" class="chip-add" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);" onclick="addCustomCity()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14"/><path d="M12 5v14"/>
                                        </svg>Add More
                                    </button>
                                    <div class="search-dropdown" id="cityDropdown"></div>
                                </div>
                            </div>
                            {{-- <div style="margin-top:16px;">
                                <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:10px;">Recommended Cities</div>
                                <div class="chips-container" id="recommendedCities">
                                </div>
                            </div> --}}
                            <div class="selected-header">
                                <span class="selected-label">Selected Locations (<span id="selectedCount">0</span>/15)</span>
                                <button type="button" class="clear-all" onclick="clearAllCities()">Clear All</button>
                            </div>
                            <div class="chips-container" id="selectedCities"></div>
                            <input type="hidden" id="kt_tagify_work_location" name="kt_tagify_work_location" value="">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                    path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>Employment Details
                            </div>
                            <div class="form-grid form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label required">Experience Range</label>
                                    <div class="range-group">
                                        <div class="range-field">
                                            <select class="form-select" name="min_exp" id="min_exp">
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
                                            <select class="form-select" name="max_exp" id="max_exp">
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
                                <div class="form-group-cus">
                                    <label class="form-label required">No. of Positions</label>
                                    <select class="form-select" id="total_positions" name="total_positions">
                                        <?php
                                        $selPos = (int) (@$job->total_positions ?: 1);
                                        for ($p = 1; $p <= 500; $p++) {
                                            echo '<option value="' . $p . '"' . ($selPos == $p ? ' selected="selected"' : '') . '>' . $p . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="form-hint">Select the number of openings to be filled (1–500).</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label">Gender Diversity</label>
                                    <select class="form-select" name="required_columns" id="required_columns">
                                        <option value="Rather Not Say" {{ (@$job->required_columns === 'Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option>
                                        <option value="Male" {{ (@$job->required_columns === 'Male') ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ (@$job->required_columns === 'Female') ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <span class="form-hint">Encourage a diverse workplace.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Position Type</label>
                                    <select class="form-select" name="employment_type" id="employment_type">
                                        <option value="">Select Position Type</option>
                                        <?php
                                        foreach ($employmentType as $key => $data_value) {
                                            $sel = (@$job->employment_type == $key) ? 'selected="selected"' : '';
                                            echo '<option value="' . $key . '" ' . $sel . '>' . $data_value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Work Mode</label>
                                    <select name="work_from_home" id="work_from_home" class="form-select">
                                        <option value="no" <?php if (@$job->work_type == 'no') echo 'selected'; ?>>Work From Office</option>
                                        <option value="yes" <?php if (@$job->work_type == 'yes') echo 'selected'; ?>>Remote</option>
                                        <option value="other" <?php if (@$job->work_type == 'other') echo 'selected'; ?>>Hybrid</option>
                                    </select>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Work Shift</label>
                                    <select class="form-select" name="work_shifts" id="work_shifts">
                                        <option value="Day Shift" @if (@$job->work_shifts == 'Day Shift') selected @endif>Day Shift</option>
                                        <option value="Night Shift" @if (@$job->work_shifts == 'Night Shift') selected @endif>Night Shift</option>
                                        <option value="Rotational shift" @if (@$job->work_shifts == 'Rotational shift') selected @endif>Rotational shift</option>
                                        <option value="UK Timezone" @if (@$job->work_shifts == 'UK Timezone') selected @endif>UK Timezone</option>
                                        <option value="US Timezone" @if (@$job->work_shifts == 'US Timezone') selected @endif>US Timezone</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>
                                </svg>Compensation
                            </div>
                            <div class="form-grid form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label required">Minimum Salary</label>
                                    <div class="salary-input-wrapper">
                                        <select class="form-select currency-select" name="min_salary_currency" id="min_salary_currency">
                                            <?php
                                            foreach ($currency as $key => $val) {
                                                $sel = '';
                                                if (@$job->min_salary_currency === null || @$job->min_salary_currency === '') { if ($currency_code == $val) { $sel = 'selected="selected"'; } }
                                                else { if (@$job->min_salary_currency == $key) { $sel = 'selected="selected"'; } }
                                                $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                                $shortText = !empty($cMeta['symbol']) ? $cMeta['code'] . ' (' . $cMeta['symbol'] . ')' : $cMeta['code'];
                                                echo '<option value="' . $key . '" data-code="' . e($val) . '" data-short="' . e($shortText) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <input type="number" class="form-input" name="minimum_salary" id="minimum_salary" value="<?php echo @$job->min_salary; ?>" placeholder="Enter min salary">
                                    </div>
                                    <span class="form-hint minimum_salary_word">*Minimum salary offered for this position.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label required">Maximum Salary</label>
                                    <div class="salary-input-wrapper">
                                        <select class="form-select currency-select" name="max_salary_currency" id="max_salary_currency">
                                            <?php
                                            foreach ($currency as $key => $val) {
                                                $sel = '';
                                                if (@$job->max_salary_currency === null || @$job->max_salary_currency === '') { if ($currency_code == $val) { $sel = 'selected="selected"'; } }
                                                else { if (@$job->max_salary_currency == $key) { $sel = 'selected="selected"'; } }
                                                $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                                $shortText = !empty($cMeta['symbol']) ? $cMeta['code'] . ' (' . $cMeta['symbol'] . ')' : $cMeta['code'];
                                                echo '<option value="' . $key . '" data-code="' . e($val) . '" data-short="' . e($shortText) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <input type="number" class="form-input" name="maximum_salary" id="maximum_salary" value="<?php echo @$job->max_salary; ?>" placeholder="Enter max salary">
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
                            <span class="form-hint">Enter the expected CTC range for this position.</span>
                        </div>
                    </div>
                    <div class="card">
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
                    <div class="card">
                        <div class="card-body">
                            <div class="section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>
                                </svg>Additional Details
                            </div>
                            <div class="form-grid">
                                <div class="form-group-cus">
                                    <label class="form-label required">Qualification (Select up to 5 options)</label>
                                    <div class="input-wrapper" style="position:relative;">
                                        <div class="input-with-icon">
                                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                            </svg>
                                            <input type="text" class="form-input" id="qualSearch" placeholder="Search or type qualification..." autocomplete="off">
                                        </div>
                                        <button type="button" class="chip-add" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);" onclick="addCustomQual()">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/>
                                            </svg>Add More
                                        </button>
                                        <div class="search-dropdown" id="qualDropdown"></div>
                                    </div>
                                    <div class="chips-container" id="qualList" style="margin-top:10px;"></div>
                                    <input type="hidden" id="qualification" name="qualification" value="">
                                </div>
                                <div class="form-group-cus">
                                    @php $selected = (@$numberDays == '') ? 'selected' : ''; @endphp
                                    <label class="form-label required">Job automatically closed in</label>
                                    <select class="form-select" name="end_date" id="end_date">
                                        <option value="15" @if (@$numberDays == 15) selected @endif>15 Days</option>
                                        <option value="30" @if (@$numberDays == 30) selected @endif>30 Days</option>
                                        <option value="45" @if (@$numberDays == 45) selected @endif>45 Days</option>
                                        <option value="60" @if (@$numberDays == 60) selected @else {{ $selected }} @endif>60 Days</option>
                                    </select>
                                    <span class="form-hint">Job will be marked as closed after this duration.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                            </svg>Save Draft
                        </button>
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
                    <div class="job-badges" id="reviewBadges">
                        <span class="badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                            <span id="badgeEmployment">Full Time</span>
                        </span>
                        <span class="badge primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>
                            </svg>
                            <span id="badgeWorkMode">Hybrid</span>
                        </span>
                        <span class="badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span id="badgeExp">3 - 5 Years</span>
                        </span>
                        <span class="badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span id="badgeLocation">Location</span>
                        </span>
                        <span class="badge primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                <path d="M12 18V6"/>
                            </svg>
                            <span id="badgeSalary">Salary</span>
                        </span>
                    </div>
                    <div class="review-row">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <path d="M14 2v6h6"/>
                                            <path d="M16 13H8"/>
                                            <path d="M16 17H8"/>
                                            <path d="M10 9H8"/>
                                        </svg>Basic Information
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(1)">Edit</button>
                                </div>
                                <div class="review-row" style="gap:12px 20px;margin-bottom:0;">
                                    <div class="review-col" style="gap:12px;">
                                        <div class="review-item">
                                            <div class="review-label">Client</div>
                                            <div class="review-value" id="rvClient">-</div>
                                        </div>
                                        <div class="review-item">
                                            <div class="review-label">Department</div>
                                            <div class="review-value" id="rvDepartment">-</div>
                                        </div>
                                    </div>
                                    <div class="review-col" style="gap:12px;">
                                        <div class="review-item">
                                            <div class="review-label">Job Type</div>
                                            <div class="review-value" id="rvJobType">-</div>
                                        </div>
                                        <div class="review-item">
                                            <div class="review-label">Work Mode</div>
                                            <div class="review-value" id="rvWorkMode">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>Location &amp; Employment
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(1)">Edit</button>
                                </div>
                                <div class="review-row" style="gap:12px 20px;margin-bottom:0;">
                                    <div class="review-col" style="gap:12px;">
                                        <div class="review-item">
                                            <div class="review-label">Location</div>
                                            <div class="review-value" id="rvLocation">-</div>
                                        </div>
                                        <div class="review-item">
                                            <div class="review-label">Experience</div>
                                            <div class="review-value" id="rvExp">-</div>
                                        </div>
                                        <div class="review-item">
                                            <div class="review-label">Employment Type</div>
                                            <div class="review-value" id="rvEmployment">-</div>
                                        </div>
                                    </div>
                                    <div class="review-col" style="gap:12px;">
                                        <div class="review-item">
                                            <div class="review-label">Work Mode</div>
                                            <div class="review-value" id="rvWorkMode2">-</div>
                                        </div>
                                        <div class="review-item">
                                            <div class="review-label">Shift</div>
                                            <div class="review-value" id="rvShift">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="review-row">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                            <path d="M12 18V6"/>
                                        </svg>Compensation
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(1)">Edit</button>
                                </div>
                                <div class="review-compensation" id="rvCompensation">-</div>
                                <div class="review-compensation-note">Annual CTC</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                            <line x1="7" x2="7.01" y1="7" y2="7"/>
                                        </svg>Key Skills 
                                        <span id="rvSkillsCount">(0)</span>
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(2)">Edit</button>
                                </div>
                                <div class="review-tags" id="rvSkills"></div>
                            </div>
                        </div>
                    </div>
                    <div class="review-row">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <path d="M14 2v6h6"/>
                                            <path d="M16 13H8"/>
                                            <path d="M16 17H8"/>
                                            <path d="M10 9H8"/>
                                        </svg>Job Description
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(2)">Edit</button>
                                </div>
                                <div class="review-text" id="rvDescription">-</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-title">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                            <line x1="12" x2="12.01" y1="17" y2="17"/>
                                        </svg>Screening Questions 
                                        <span id="rvQuestionsCount">(0)</span>
                                    </div>
                                    <button type="button" class="card-edit" onclick="goToStep(3)">Edit</button>
                                </div>
                                <div class="review-questions-list" id="rvQuestions"></div>
                            </div>
                        </div>
                    </div>
                    <div style="font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:14px;">Publishing Settings</div>
                    <div class="publish-grid">
                        <div class="publish-card">
                            <div class="publish-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>Job Visibility
                            </div>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkbox-label">Publish on Company Career Page</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkbox-label">Publish on Agency Marketplace
                                    <span class="checkbox-sublabel">Visible to 250+ partner agencies 
                                        <span class="checkbox-badge">Recommended</span>
                                    </span>
                                </span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox">
                                <span class="checkbox-label">Publish Internally Only</span>
                            </label>
                            <div class="info-box">This job will be visible to approved agency partners and their recruiters.
                                <a onclick="showToast('Learn more','info')">Learn more about marketplace</a>
                            </div>
                        </div>
                        <div class="publish-card">
                            <div class="publish-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                    <path d="M12 18V6"/>
                                </svg>Coin Consumption
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px;">When published on Agency Marketplace</div>
                            <div class="coin-row">
                                <span class="coin-row-label">{{ @$job_coins->deductions }} Coins / Day</span>
                            </div>
                            <div class="coin-row">
                                <span class="coin-row-label">+ {{ @$candidates_coins->deductions }} Coin / Candidate Contacted</span>
                            </div>
                            <div class="coin-link" onclick="showToast('Coin terms','info')">View coin consumption terms
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" x2="21" y1="14" y2="3"/>
                                </svg>
                            </div>
                            <div class="coin-divider"></div>
                            <div class="coin-row">
                                <span class="coin-row-label">Estimated Monthly Usage</span>
                            </div>
                            <div class="coin-row">
                                <span class="coin-row-value green">{{ @$job_coins->deductions*30 }} Coins</span>
                            </div>
                            <div class="coin-row" style="margin-top:6px;">
                                <span class="coin-row-label">Current Operational Coins</span>
                            </div>
                            <div class="coin-row">
                                <span class="coin-row-value">{{ @$company_coins->coins }} Coins</span>
                            </div>
                            <div class="coin-alert">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>Sufficient Balance Available
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>Candidate Sources
                            </div>
                            <div class="summary-list">
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/>
                                    </svg>Direct Applications
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Agency Marketplace
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Internal Team
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Employee Referrals
                                </div>
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                    <line x1="12" x2="12.01" y1="17" y2="17"/>
                                </svg>Important Notes
                            </div>
                            <div class="summary-list">
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Job will auto close after 60 days
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Coins will be deducted daily
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>You can pause the job anytime
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Applications will be saved
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Extend duration anytime using coins
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>Candidate Sources
                            </div>
                            <div class="summary-list">
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/>
                                    </svg>Direct Applications
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Agency Marketplace
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Internal Team
                                </div>
                                <div class="summary-list-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>Employee Referrals
                                </div>
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" x2="18" y1="20" y2="10"/>
                                    <line x1="12" x2="12" y1="20" y2="4"/>
                                    <line x1="6" x2="6" y1="20" y2="14"/>
                                </svg>Estimated Results
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Duration</span>
                                <span class="summary-metric-value">60 Days</span>
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Est. Applications</span>
                                <span class="summary-metric-value">50 - 100</span>
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Visibility</span>
                                <span class="summary-metric-value">High</span>
                            </div>
                            <div class="summary-note">Estimates are based on similar roles and may vary.</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                    <path d="M12 18V6"/>
                                </svg>Estimated Coin Usage
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Daily Usage</span>
                                <span class="summary-metric-value">2 Coins</span>
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Per Candidate Contact</span>
                                <span class="summary-metric-value">1 Coin</span>
                            </div>
                            <div class="summary-metric">
                                <span class="summary-metric-label">Estimated Total</span>
                                <span class="summary-metric-value">120 Coins</span>
                            </div>
                            <div class="summary-note">Figures are estimates based on performance.</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                    <line x1="12" x2="12.01" y1="17" y2="17"/>
                                </svg>Important Notes
                            </div>
                            <div class="summary-list">
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Job will auto close after 60 days
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Coins will be deducted daily
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>You can pause the job anytime
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Applications will be saved
                                </div>
                                <div class="summary-list-item" style="color:var(--text-muted);font-size:11px;line-height:1.5;">
                                    <span style="color:var(--text-muted);margin-right:4px;">
                                        •</span>Extend duration anytime using coins
                                </div>
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>Compliance
                            </div>
                            <label class="summary-check">
                                <input type="checkbox" checked>
                                <span>I confirm job details are accurate</span>
                            </label>
                            <label class="summary-check">
                                <input type="checkbox" checked>
                                <span>I understand coin usage rules</span>
                            </label>
                            <label class="summary-check">
                                <input type="checkbox" checked>
                                <span>I agree to marketplace terms</span>
                            </label>
                            <div class="coin-link" style="margin-top:6px;" onclick="showToast('Terms &amp; Conditions','info')">View Terms &amp; Conditions
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" x2="21" y1="14" y2="3"/>
                                </svg>
                            </div>
                        </div>
                    </div> --}}
                    <div class="compact-summary">
                        <div class="compact-summary-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M16 13H8"/>
                                <path d="M16 17H8"/>
                                <path d="M10 9H8"/>
                            </svg>
                            Summary Before Publishing
                        </div>
                        <div class="compact-summary-subtitle">Review the details below before publishing your job.</div>
                        <div class="compact-grid">
                            <div class="compact-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                                <div class="compact-item-text">
                                    <strong>Job Title</strong><br>
                                    <span id="csTitle">-</span>
                                </div>
                            </div>
                            <div class="compact-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <div class="compact-item-text">
                                    <strong>Location</strong><br>
                                    <span id="csLocation">-</span>
                                </div>
                            </div>
                            <div class="compact-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <div class="compact-item-text">
                                    <strong>Experience</strong><br>
                                    <span id="csExp">-</span>
                                </div>
                            </div>
                            <div class="compact-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                                <div class="compact-item-text">
                                    <strong>Employment Type</strong><br>
                                    <span id="csEmployment">-</span>
                                </div>
                            </div>
                            <div class="compact-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                                    <path d="M2 12h20"/>
                                </svg>
                                <div class="compact-item-text">
                                    <strong>Work Mode</strong><br>
                                    <span id="csWorkMode">-</span>
                                </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                        <path d="M12 18V6"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Salary</strong><br>
                                        <span id="csSalary">-</span>
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Duration</strong><br>60 Days
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Visibility</strong><br>Agency Marketplace
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                        <path d="M12 18V6"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Daily Coin Cost</strong><br>2 Coins
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Per Candidate Contact</strong><br>1 Coin
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                                        <path d="M12 18V6"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Estimated Total Cost</strong><br>120 Coins
                                    </div>
                                </div>
                                <div class="compact-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <div class="compact-item-text">
                                        <strong>Auto Close</strong><br>After 60 Days
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="submit-card">
                            <div class="submit-card-title">Ready to Publish?</div>
                            <div class="submit-card-desc">Your job is ready to go live and start receiving applications.</div>
                            <div style="display:flex;gap:12px;justify-content:center;">
                                <button type="button" class="btn btn-secondary" onclick="goToStep(3)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14"/>
                                        <path d="m12 19-7-7 7-7"/>
                                    </svg>Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="publishJob()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                    </svg><?php echo @$buttontext ? $buttontext : 'Publish Job'; ?>
                                </button>
                            </div>
                            <div class="submit-card-footer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>By publishing, your job will be live and visible to selected audiences.
                            </div>
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
        // ---- Job title suggestions (original) ----
        function getSuggestions(query) {
            if (query.length > 2) {
                $.ajax({
                    url: "{{ url('service_company/') }}" + '/get-job-suggestions',
                    type: 'GET',
                    data: { search: query },
                    success: function(data) {
                        let suggestions = data;
                        $('#suggestions-list').empty();
                        if (suggestions.length > 0) {
                            $('#suggestions').show();
                            suggestions.forEach(function(suggestion) {
                                $('#suggestions-list').append('<li onclick="selectSuggestion(\'' + suggestion + '\')">' + suggestion + '</li>');
                            });
                        } else { $('#suggestions').hide(); }
                    }
                });
            } else { $('#suggestions').hide(); }
        }

        $(document).click(function(event) {
            const target = $(event.target);
            if (!target.closest('#suggestions').length && !target.is('#title')) { $('#suggestions').hide(); }
        });

        $(document).on('change', '#title', function() { changeautofillfield(); });

        function selectSuggestion(suggestion) {
            $('#title').val(suggestion);
            $('#suggestions').hide();
            changeautofillfield();
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
                    Swal.fire({ title: 'Warning', text: 'Please Select client', icon: 'warning', allowOutsideClick: false });
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
                $('#address_id').html('<option value="">Select the address</option>');
                var url = "{{ route('service_company.jobs.getContactPerson', ':id') }}?job_id={{ $job_id }}&&jobLocation={{ implode(',', $jobLocation) }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    success: function(response) {
                        if (response.address_show) { $('.address_show').show(); } else { $('.address_show').hide(); }
                        $('#address_id').html(response.locArr);
                    }
                });
            }).change();

            // ---- Salary words (original) ----
            $(document).on('keyup', '#minimum_salary', function() {
                var number = parseInt($('#minimum_salary').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));
            });
            $(document).on('keyup', '#maximum_salary', function() {
                var number = parseInt($('#maximum_salary').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));
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

        // ---- Salary words helpers (original) ----
        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        function capitalizeFirstLetter(string) { return string.charAt(0).toUpperCase() + string.slice(1); }
        function convertToWords(num) {
            if (isNaN(num)) return '';
            if (num < 10) { return ones[num]; }
            else if (num < 20) { return teens[num - 10]; }
            else if (num < 100) { return tens[Math.floor(num / 10)] + ' ' + ones[num % 10]; }
            else if (num < 1000) { return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100); }
            else if (num < 100000) { return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000); }
            else if (num < 10000000) { return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000); }
            else if (num < 1000000000) { return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000); }
            return "Number out of range";
        }
        function salary_check() {
            var minimum_salary = parseInt($("#minimum_salary").val());
            var maximum_salary = parseInt($("#maximum_salary").val());
            if (minimum_salary != '') {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({ title: 'Warning', text: 'Enter the maximum salary greater than the minimum salary', icon: 'warning', allowOutsideClick: false });
                    $("#maximum_salary").val('');
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

        // ---- Currency Select2 & mirroring ----
        if (window.jQuery && $.fn.select2) {
            $('.currency-select').select2({
                width: '140px',
                dropdownParent: $(document.body),
                dropdownCssClass: 'br-currency-select2-dropdown',
                placeholder: 'Search currency...',
                minimumResultsForSearch: 0,
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    var $el = $(data.element);
                    var short = $el.data('short') || $el.data('code');
                    return short ? short : data.text;
                },
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    var term = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    var $el = $(data.element);
                    var code = ($el.data('code') || '').toString().toLowerCase();
                    var searchKw = ($el.data('search') || '').toString().toLowerCase();

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                }
            });
        }

        $('body').on('change', '#min_salary_currency', function() {
            var val = $(this).val();
            if ($('#max_salary_currency').val() !== val) {
                $('#max_salary_currency').val(val).trigger('change.select2');
            }
            if ($('#salary_currency').length) {
                $('#salary_currency').val(val);
            }
        });
        $('body').on('change', '#max_salary_currency', function() {
            var val = $(this).val();
            if ($('#min_salary_currency').val() !== val) {
                $('#min_salary_currency').val(val).trigger('change.select2');
            }
            if ($('#salary_currency').length) {
                $('#salary_currency').val(val);
            }
        });
        $('body').on('change', '#min_sal_mode', function() {
            document.getElementById("max_sal_mode").selectedIndex = this.selectedIndex;
        });

        // ---- Auto-fill JD from title (original, adapted to contenteditable + skills chips) ----
        function changeautofillfield() {
            var data = new FormData();
            data.append("title", $('#title').val());
            $.ajax({
                url: '{{ route('service_company.jobs.jdDetails') }}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST", processData: false, contentType: false, data: data, redirect: true,
                success: function(data) {
                    if (data.status == 'success') {
                        var ed = document.getElementById('jobDescription');
                        // Only auto-fill description when it is empty (new job); skip on edit to preserve existing JD
                        var currentDesc = ed ? (ed.innerText || ed.textContent || '').trim() : '';
                        if (ed && currentDesc === '') {
                            ed.innerHTML = data.jd || '';
                            syncDescription();
                        }
                        $("#min_exp").val(data.minimum_exp).change();
                        $("#max_exp").val(data.maxmum_exp).change();
                        // Only auto-fill skills when none are already added
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
            initCitySearch(); initQualSearch(); initSkillsInput(); initCharCount();

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
        function goToStep(step) {
            if (step < 1 || step > 4) return;
            if (step > currentStep) {
                for (var s = currentStep; s < step; s++) {
                    if (!validateStep(s)) return;
                }
            }
            currentStep = step;
            document.querySelectorAll('.step-content').forEach(function(el) { el.classList.remove('active'); });
            document.getElementById('step' + step).classList.add('active');
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
        function validateStep(step) {
            var val = function(id) { var el = document.getElementById(id); return el ? (el.value || '').trim() : ''; };
            var focusEl = function(id) { var el = document.getElementById(id); if (el && typeof el.focus === 'function') { try { el.focus(); } catch (e) {} } };
            var fail = function(id, msg) { showToast(msg, 'error'); focusEl(id); return false; };

            if (step === 1) {
                if (!val('client_id'))          return fail('client_id', 'Please select a Client');
                if (!val('address_id'))         return fail('address_id', 'Please select an Address');
                if (!val('title'))              return fail('title', 'Please enter the Job Title / Designation');
                if (!val('category_id'))        return fail('category_id', 'Please select a Job Department');
                if (selectedCities.length === 0) { showToast('Please select at least one job location', 'error'); focusEl('citySearch'); return false; }
                if (val('min_exp') === '')      return fail('min_exp', 'Please select Minimum Experience');
                if (val('max_exp') === '')      return fail('max_exp', 'Please select Maximum Experience');
                if (!val('total_positions'))    return fail('total_positions', 'Please select No. of Positions');
                if (!val('employment_type'))    return fail('employment_type', 'Please select Position Type');
                if (!val('work_from_home'))     return fail('work_from_home', 'Please select Work Mode');
                if (!val('work_shifts'))        return fail('work_shifts', 'Please select Work Shift');
                if (!val('min_salary_currency')) return fail('min_salary_currency', 'Please select Minimum Salary currency');
                if (val('minimum_salary') === '') return fail('minimum_salary', 'Please enter Minimum Salary');
                if (!val('max_salary_currency')) return fail('max_salary_currency', 'Please select Maximum Salary currency');
                if (val('maximum_salary') === '') return fail('maximum_salary', 'Please enter Maximum Salary');
                if (!val('min_sal_mode'))       return fail('min_sal_mode', 'Please select Salary Type');
                if (!val('hiring_namager'))     return fail('hmSearchDisplay', 'Please select the Hiring Manager / HR');
                if (!val('recruiter'))          return fail('recSearchDisplay', 'Please select your team for this job');
                if (qualifications.length === 0) { showToast('Please add at least one Qualification', 'error'); focusEl('qualSearch'); return false; }
                if (!val('end_date'))           return fail('end_date', 'Please select the auto-close duration');
            }
            if (step === 2) {
                var descEl = document.getElementById('jobDescription');
                var descText = descEl ? (descEl.innerText || descEl.textContent || '').trim() : '';
                if (descText === '') { showToast('Please enter the Job Description', 'error'); focusEl('jobDescription'); return false; }
                if (skills.length === 0) { showToast('Please add at least one Key Skill', 'error'); focusEl('skillsInput'); return false; }
            }
            return true;
        }

        // ---------- CREATE OPTIONS (mock) ----------
        function selectOption(el, type) {
            document.querySelectorAll('.option-card').forEach(function(c) { c.classList.remove('selected'); });
            el.classList.add('selected');
            showToast(type === 'new' ? 'Creating new job from scratch' : type === 'template' ? 'Template selection coming soon' : 'Clone job coming soon', 'info');
        }

        // ---------- CITY SELECTION ----------
        function debounce(func, delay) { let timer; return function (...args) { clearTimeout(timer); timer = setTimeout(() => func.apply(this, args), delay); }; }
        var fetchCitySuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query), method: 'GET',
                success: function(data) {
                    var dd = document.getElementById('cityDropdown');
                    if (Array.isArray(data) && data.length) {
                        dd.innerHTML = data.map(function(c) { return '<div class="search-option" onclick="selectCity(\'' + String(c).replace(/'/g, "\\'") + '\')">' + escapeHtml(String(c)) + '</div>'; }).join('');
                    } else { dd.innerHTML = '<div class="search-option" style="color:var(--text-muted);cursor:default;">No matches</div>'; }
                    dd.classList.add('active');
                }
            });
        }, 200);
        function initCitySearch() {
            var input = document.getElementById('citySearch');
            var dropdown = document.getElementById('cityDropdown');
            input.addEventListener('input', function(e) {
                var v = e.target.value.trim();
                if (v.length < 2) { dropdown.classList.remove('active'); return; }
                fetchCitySuggestions(v);
            });
            document.addEventListener('click', function(e) { if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('active'); });
        }
        function selectCity(city) {
            if (selectedCities.indexOf(city) === -1) {
                if (selectedCities.length >= 15) { showToast('Maximum 15 cities allowed', 'error'); return; }
                selectedCities.push(city); renderSelectedCities();
            }
            document.getElementById('citySearch').value = '';
            document.getElementById('cityDropdown').classList.remove('active');
        }
        function toggleCity(el, city) {
            if (selectedCities.indexOf(city) !== -1) { selectedCities = selectedCities.filter(function(c) { return c !== city; }); el.classList.remove('selected'); }
            else { if (selectedCities.length >= 15) { showToast('Maximum 15 cities allowed', 'error'); return; } selectedCities.push(city); el.classList.add('selected'); }
            renderSelectedCities();
        }
        function addCustomCity() {
            var val = document.getElementById('citySearch').value.trim();
            if (val && selectedCities.indexOf(val) === -1) {
                if (selectedCities.length >= 15) { showToast('Maximum 15 cities allowed', 'error'); return; }
                selectedCities.push(val); renderSelectedCities(); document.getElementById('citySearch').value = '';
            }
        }
        function clearAllCities() { selectedCities = []; document.querySelectorAll('#recommendedCities .chip').forEach(function(c) { c.classList.remove('selected'); }); renderSelectedCities(); }
        function renderSelectedCities() {
            document.getElementById('selectedCount').textContent = selectedCities.length;
            document.getElementById('selectedCities').innerHTML = selectedCities.map(function(city) {
                var disp = String(city).split(' | ')[0];
                return '<span class="chip selected">' + escapeHtml(disp) + ' <span class="remove" onclick="removeCity(\'' + String(city).replace(/'/g, "\\'") + '\')">&times;</span></span>';
            }).join('');
            syncCities();
        }
        function removeCity(city) {
            selectedCities = selectedCities.filter(function(c) { return c !== city; });
            document.querySelectorAll('#recommendedCities .chip').forEach(function(el) { if (el.textContent.indexOf(String(city).split(' | ')[0]) !== -1) el.classList.remove('selected'); });
            renderSelectedCities();
        }
        function syncCities() { document.getElementById('kt_tagify_work_location').value = JSON.stringify(selectedCities.map(function(c) { return { value: c }; })); }

        // ---------- QUALIFICATIONS ----------
        function initQualSearch() {
            var input = document.getElementById('qualSearch');
            var dropdown = document.getElementById('qualDropdown');
            function render(list) {
                dropdown.innerHTML = list.map(function(q) { return '<div class="search-option" onclick="addQualification(\'' + String(q).replace(/'/g, "\\'") + '\')">' + escapeHtml(String(q)) + '</div>'; }).join('');
            }
            input.addEventListener('focus', function() { render(qualWhitelist.slice(0, 50)); dropdown.classList.add('active'); });
            input.addEventListener('input', function(e) {
                var v = e.target.value.toLowerCase();
                var filtered = qualWhitelist.filter(function(q) { return String(q).toLowerCase().indexOf(v) !== -1; }).slice(0, 50);
                render(filtered); dropdown.classList.add('active');
            });
            document.addEventListener('click', function(e) { if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('active'); });
        }
        function addQualification(qual) {
            if (qualifications.indexOf(qual) !== -1) return;
            if (qualifications.length >= 5) { showToast('Maximum 5 qualifications allowed', 'error'); return; }
            qualifications.push(qual); renderQualifications();
            document.getElementById('qualSearch').value = '';
            document.getElementById('qualDropdown').classList.remove('active');
        }
        function addCustomQual() { var val = document.getElementById('qualSearch').value.trim(); if (val) addQualification(val); }
        function removeQualification(qual) { qualifications = qualifications.filter(function(q) { return q !== qual; }); renderQualifications(); }
        function renderQualifications() {
            document.getElementById('qualList').innerHTML = qualifications.map(function(q) {
                return '<span class="chip selected">' + escapeHtml(q) + ' <span class="remove" onclick="removeQualification(\'' + String(q).replace(/'/g, "\\'") + '\')">&times;</span></span>';
            }).join('');
            syncQual();
        }
        function syncQual() { document.getElementById('qualification').value = JSON.stringify(qualifications.map(function(q) { return { value: q }; })); }

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
            setText('rvClient', clientName());
            setText('rvDepartment', getSelText('category_id'));
            setText('rvJobType', getSelText('employment_type'));
            setText('rvWorkMode', getSelText('work_from_home'));
            setText('rvLocation', cities);
            setText('rvExp', expText());
            setText('rvEmployment', getSelText('employment_type'));
            setText('rvWorkMode2', getSelText('work_from_home'));
            setText('rvShift', getSelText('work_shifts'));
            setText('rvCompensation', salaryText());
            // skills
            document.getElementById('rvSkillsCount').textContent = '(' + skills.length + ')';
            document.getElementById('rvSkills').innerHTML = skills.map(function(s) { return '<span class="review-tag">' + escapeHtml(s) + '</span>'; }).join('') || '<span style="color:var(--text-muted);font-size:12px;">No skills added</span>';
            // description
            var descPlain = (document.getElementById('jobDescription').innerText || '').trim();
            setText('rvDescription', descPlain || '-');
            // questions
            document.getElementById('rvQuestionsCount').textContent = '(' + questions.length + ')';
            document.getElementById('rvQuestions').innerHTML = questions.map(function(q, i) {
                return '<div class="review-question-item"><div class="review-question-num">' + (i + 1) + '</div><div class="review-question-text">' + escapeHtml(q.text) + '</div></div>';
            }).join('') || '<span style="color:var(--text-muted);font-size:12px;">No screening questions</span>';
            // badges
            setText('badgeEmployment', getSelText('employment_type') || 'Job Type');
            setText('badgeWorkMode', getSelText('work_from_home') || 'Work Mode');
            setText('badgeExp', expText() || 'Experience');
            setText('badgeLocation', cities || 'Location');
            setText('badgeSalary', salaryText() || 'Salary');
            // compact summary
            setText('csTitle', document.getElementById('title').value);
            setText('csLocation', cities);
            setText('csExp', expText());
            setText('csEmployment', getSelText('employment_type'));
            setText('csWorkMode', getSelText('work_from_home'));
            setText('csSalary', salaryText());
        }

        // ---------- SYNC ALL HIDDEN INPUTS ----------
        function syncAll() { syncCities(); syncSkills(); syncQual(); syncDescription(); syncQuestions(); }

        // ---------- PUBLISH (real submit) ----------
        function publishJob() {
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
    </script>
    {{-- Create-only enhancement script — placed LAST so all globals defined in the inline
         scripts above (currentStep, goToStep, __indexUrl, validateStep, etc.) are available
         when this file runs. No defer needed here because the script is already at the
         bottom of <body>. The onReady() guard inside the file handles both the
         readyState === 'loading' case and the already-interactive/complete case. --}}
    @if(request()->routeIs('service_company.jobs.create') || request()->is('service_company/jobs/create*'))
        <script src="{{ asset('js/internal-job-create-design.js') }}?v=20261011g"></script>
    @endif
@endpush
