@extends('layouts.service_company_layout')

@push('head-script')
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/internal-job-create-design.css') }}?v=20261015_salvis_godlevel_v2">
    <style>
        /* Product Form Scoped Layout Helpers (matching Job Create 1:1) */
        #jrwrap.internal-job-create .content { padding: 0 20px 32px !important; }
        #jrwrap .step-content { display: none; }
        #jrwrap .step-content.active { display: block; }
        #jrwrap .card {
            background: #ffffff;
            border: 1px solid #ebd99f;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 20, 59, 0.04);
            margin-bottom: 16px;
            overflow: hidden;
        }
        #jrwrap .card-body { padding: 0; }
        #jrwrap .ij-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
            background: linear-gradient(180deg, #fffdf7 0%, #fff9df 100%);
            border-bottom: 1px solid #ebd99f;
        }
        #jrwrap .ij-card-head-left { display: flex; align-items: center; gap: 12px; }
        #jrwrap .ij-card-head-icon {
            width: 32px; height: 32px; border-radius: 8px; background: #ffe394; color: #9a6500;
            display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;
        }
        #jrwrap .ij-card-head-title { font-size: 13px; font-weight: 600; color: #10143b; line-height: 1.3; }
        #jrwrap .ij-card-head-sub { font-size: 11px; color: #687795; line-height: 1.3; }
        #jrwrap .ij-card-inner { padding: 18px 20px; }

        #jrwrap .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 22px; }
        #jrwrap .form-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 22px; }
        #jrwrap .form-group-cus { display: flex; flex-direction: column; gap: 5px; }
        #jrwrap .form-group-cus.full-width { grid-column: 1 / -1; }
        #jrwrap .form-label { font-size: 12px; font-weight: 500; color: #10143b; margin: 0; display: flex; align-items: center; gap: 4px; }
        #jrwrap .required::after { content: '*'; color: #ef4444; margin-left: 2px; }
        #jrwrap .form-hint { font-size: 11px; color: #687795; margin-top: 2px; }
        #jrwrap .form-error { font-size: 11px; color: #dc2626; display: none; }
        #jrwrap .form-group-cus.has-error .form-error { display: block; }
        #jrwrap .form-group-cus.has-error .form-input,
        #jrwrap .form-group-cus.has-error .form-select { border-color: #dc2626; background: #fef2f2; }

        #jrwrap .form-input, #jrwrap .form-select, #jrwrap .form-textarea {
            width: 100%; height: 38px; padding: 8px 12px; border: 1px solid #dce2ed; border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 13px; color: #10143b; background: #ffffff; outline: none;
        }
        #jrwrap .form-textarea { height: auto; min-height: 82px; resize: vertical; }
        #jrwrap .form-input:focus, #jrwrap .form-select:focus, #jrwrap .form-textarea:focus {
            border-color: #e6af00; box-shadow: 0 0 0 3px rgba(230, 175, 0, 0.14);
        }

        /* Inline Currency + Price Input (Screenshot 2) */
        #jrwrap .ij-price-group {
            display: flex; align-items: stretch; border: 1px solid #dce2ed; border-radius: 8px; overflow: hidden; background: #fff; height: 38px;
        }
        #jrwrap .ij-price-group:focus-within { border-color: #e6af00; box-shadow: 0 0 0 3px rgba(230, 175, 0, 0.14); }
        #jrwrap .ij-curr-select {
            border: none; border-right: 1px solid #dce2ed; background: #f8fafc; padding: 0 10px; font-size: 12px; font-weight: 600; color: #10143b; outline: none; cursor: pointer;
        }
        #jrwrap .ij-price-input {
            border: none; flex: 1; padding: 0 12px; font-size: 13px; color: #10143b; outline: none; min-width: 0;
        }

        /* Choice Cards with Radio Dot + Icon + Title + Subtitle (Screenshot 3) */
        #jrwrap .ij-choice-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-grid-5 { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-card {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #dce2ed; border-radius: 10px;
            background: #ffffff; cursor: pointer; text-align: left; transition: border-color 0.15s, background-color 0.15s;
        }
        #jrwrap .ij-choice-card:hover { border-color: #e6af00; background: #fffdf7; }
        #jrwrap .ij-choice-card.is-selected { border: 1.5px solid #e6af00; background: #fffdf5; }
        #jrwrap .ij-radio-dot {
            width: 16px; height: 16px; border-radius: 50%; border: 2px solid #cbd5e1; flex-shrink: 0; position: relative;
        }
        #jrwrap .ij-choice-card.is-selected .ij-radio-dot { border-color: #d97706; }
        #jrwrap .ij-choice-card.is-selected .ij-radio-dot::after {
            content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #d97706;
        }
        #jrwrap .ij-choice-ico {
            width: 30px; height: 30px; border-radius: 7px; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;
            display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;
        }
        #jrwrap .ij-choice-card.is-selected .ij-choice-ico { background: #fff9df; border-color: #ebd99f; color: #9a6500; }
        #jrwrap .ij-choice-title { font-size: 12.5px; font-weight: 600; color: #10143b; line-height: 1.25; }
        #jrwrap .ij-choice-sub { font-size: 11px; color: #687795; line-height: 1.25; margin-top: 2px; }

        /* Popular Hubs Pill Row & City Search Autocomplete (1:1 Screenshot Match) */
        #jrwrap .ij-hubs-bar {
            display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 10px 14px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 12px;
        }
        #jrwrap .ij-hub-pill {
            padding: 5px 12px; border-radius: 999px; border: 1px solid #cbd5e1; background: #fff;
            font-size: 12px; font-weight: 500; color: #1e293b; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
        }
        #jrwrap .ij-hub-pill:hover { border-color: #2563eb; color: #2563eb; }
        #jrwrap .ij-hub-pill.is-selected {
            border-color: #0066ff; background: #0066ff; color: #ffffff; font-weight: 600;
        }
        #jrwrap .ij-city-search-wrap { position: relative; margin-bottom: 12px; }
        #jrwrap .ij-city-search-ico {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none;
        }
        #jrwrap .ij-city-search-input {
            width: 100%; height: 42px; padding: 8px 38px 8px 40px; border: 1px solid #cbd5e1; border-radius: 10px;
            font-family: 'Poppins', sans-serif; font-size: 13px; color: #10143b; background: #fff; outline: none;
        }
        #jrwrap .ij-city-search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
        #jrwrap .ij-city-clear-btn {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;
            border-radius: 50%; border: none; background: #cbd5e1; color: #fff; font-size: 12px; line-height: 1;
            display: none; align-items: center; justify-content: center; cursor: pointer;
        }
        #jrwrap .ij-city-dropdown {
            position: absolute; left: 0; right: 0; top: calc(100% + 4px); background: #fff; border: 1px solid #cbd5e1;
            border-radius: 10px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12); max-height: 220px; overflow-y: auto;
            z-index: 40; display: none;
        }
        #jrwrap .ij-city-option {
            padding: 9px 14px; font-size: 13px; color: #10143b; cursor: pointer; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }
        #jrwrap .ij-city-option:hover { background: #eff6ff; color: #1d4ed8; }
        #jrwrap .ij-selected-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        #jrwrap .ij-city-chip {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px;
            background: #ffffff; border: 1px solid #bfdbfe; font-size: 12.5px; color: #475569;
        }
        #jrwrap .ij-city-chip strong { color: #0052cc; font-weight: 600; }
        #jrwrap .ij-city-chip button {
            width: 16px; height: 16px; border-radius: 50%; border: none; background: #cbd5e1; color: #fff;
            font-size: 11px; line-height: 1; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;
        }
        #jrwrap .ij-city-chip button:hover { background: #94a3b8; }
        #jrwrap .chips-box {
            border: 1px solid #dce2ed; border-radius: 8px; padding: 7px 12px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; min-height: 40px; background: #fff;
        }
        #jrwrap .chip-item {
            display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px;
            background: #fff9df; border: 1px solid #ebd99f; color: #9a6500; font-size: 12px; font-weight: 500;
        }
        #jrwrap .chip-item button { background: none; border: none; color: #9a6500; cursor: pointer; padding: 0; font-size: 13px; }
        #jrwrap .chip-input-bare { border: none; outline: none; flex: 1; min-width: 160px; font-family: 'Poppins', sans-serif; font-size: 13px; }

        /* Rich Editor & Footer Buttons */
        #jrwrap .editor-toolbar { display: flex; gap: 4px; padding: 8px 12px; background: #fffdf7; border: 1px solid #dce2ed; border-bottom: none; border-radius: 8px 8px 0 0; }
        #jrwrap .toolbar-btn { width: 28px; height: 28px; border: none; background: transparent; border-radius: 5px; color: #475569; cursor: pointer; }
        #jrwrap .toolbar-btn:hover { background: #ffe394; color: #9a6500; }
        #jrwrap .rich-editor { min-height: 150px; padding: 12px; border: 1px solid #dce2ed; border-radius: 0 0 8px 8px; background: #fff; outline: none; }
        #jrwrap .rich-editor:empty::before { content: attr(data-placeholder); color: #94a3b8; }

        #jrwrap .form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }
        #jrwrap .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; }
        #jrwrap .btn-secondary { background: #fff; border-color: #dce2ed; color: #10143b; }
        #jrwrap .btn-primary { background: #9a6500; border-color: #9a6500; color: #fff; }
        #jrwrap .btn-primary:hover { background: #7a5000; }

        #jrwrap .r4-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; align-items: start; }
        #jrwrap .r4-col { display: flex; flex-direction: column; gap: 16px; }
        #jrwrap .r4-edit-btn {
            padding: 4px 10px; border-radius: 999px; border: 1px solid #cbd5e1; background: #fff;
            color: #475569; font-size: 11.5px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
        }
        #jrwrap .r4-edit-btn:hover { border-color: #9a6500; color: #9a6500; }
        #jrwrap .rv-row-ico {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;
        }
        #jrwrap .rv-row-ico:last-child { border-bottom: none; }
        #jrwrap .rv-ico-left { display: flex; align-items: center; gap: 10px; color: #64748b; }
        #jrwrap .rv-ico-badge {
            width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 11.5px; flex-shrink: 0;
        }
        #jrwrap .rv-desc-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px;
            font-size: 12.5px; color: #334155; min-height: 90px; max-height: 180px; overflow-y: auto;
        }
        #jrwrap .rv-tag-pill {
            display: inline-flex; padding: 4px 10px; border-radius: 999px; border: 1px solid #ebd99f;
            background: #fffdf5; color: #9a6500; font-size: 11.5px; font-weight: 500;
        }
        #jrwrap .rv-q-row {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;
        }
        #jrwrap .rv-q-num {
            width: 20px; height: 20px; border-radius: 5px; background: #fff9df; color: #9a6500;
            font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        #jrwrap .rv-info-box {
            margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: #eff6ff; border: 1px solid #bfdbfe;
            font-size: 11.5px; color: #1e40af; display: flex; gap: 8px; align-items: flex-start;
        }
        #jrwrap .rv-notes-list { list-style: none; padding: 0; margin: 0; }
        #jrwrap .rv-notes-list li { display: flex; align-items: center; gap: 8px; padding: 5px 0; font-size: 12px; color: #334155; }
        #jrwrap .rv-notes-dot { width: 6px; height: 6px; border-radius: 50%; background: #e6af00; flex-shrink: 0; }
        #jrwrap .step2-split-grid { display: grid; grid-template-columns: 1.55fr 1fr; gap: 16px; align-items: start; }
        #jrwrap .step3-split-grid { display: grid; grid-template-columns: 2.1fr 1fr; gap: 16px; align-items: start; }
        #jrwrap .ij-q-item {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; margin-bottom: 10px;
        }
        #jrwrap .ij-q-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        #jrwrap .ij-q-grip { color: #cbd5e1; font-size: 14px; flex-shrink: 0; }
        #jrwrap .ij-q-title { font-size: 13px; font-weight: 600; color: #10143b; margin-bottom: 5px; }
        #jrwrap .ij-q-badges { display: flex; align-items: center; gap: 8px; }
        #jrwrap .ij-q-type-pill {
            padding: 2px 9px; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 500;
        }
        #jrwrap .ij-q-req-pill {
            padding: 2px 9px; border-radius: 6px; background: #fef2f2; color: #dc2626; font-size: 11px; font-weight: 500;
        }
        #jrwrap .ij-q-opt-pill {
            padding: 2px 9px; border-radius: 6px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 500;
        }
        #jrwrap .ij-q-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        #jrwrap .ij-q-btn-edit {
            padding: 5px 11px; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12px; cursor: pointer;
        }
        #jrwrap .ij-q-btn-del {
            width: 30px; height: 30px; border-radius: 6px; border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; font-size: 12px; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        #jrwrap .ij-add-q-box {
            border: 1.5px dashed #cbd5e1; border-radius: 12px; padding: 28px 16px; text-align: center; cursor: pointer;
            background: #ffffff; transition: border-color 0.15s, background-color 0.15s;
        }
        #jrwrap .ij-add-q-box:hover { border-color: #9a6500; background: #fffdf7; }
        #jrwrap .ij-add-q-circle {
            width: 40px; height: 40px; border-radius: 50%; background: #9a6500; color: #fff;
            display: inline-flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 10px;
        }
        #jrwrap .ij-help-card {
            background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 18px; margin-bottom: 16px;
        }
        #jrwrap .ij-tpl-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        #jrwrap .ij-tpl-head { padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; color: #10143b; }
        #jrwrap .ij-tpl-row {
            display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 11px 16px;
            border-bottom: 1px solid #f1f5f9; cursor: pointer; font-size: 12px; color: #1e293b;
        }
        #jrwrap .ij-tpl-row:hover { background: #f8fafc; }
        #jrwrap .ij-tpl-plus {
            width: 24px; height: 24px; border-radius: 50%; border: 1px solid #cbd5e1; color: #64748b;
            display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px;
        }
        #jrwrap .ij-selected-tags-card {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-top: 12px; margin-bottom: 14px;
        }
        #jrwrap .ij-selected-tags-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        #jrwrap .ij-tags-count-pill {
            display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 999px;
            background: #e0f2fe; color: #0284c7; font-size: 11px; font-weight: 600; margin-left: 6px;
        }
        #jrwrap .ij-tag-chip {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px;
            background: #e0f2fe; border: 1px solid #bae6fd; color: #0369a1; font-size: 12px; font-weight: 500;
        }
        #jrwrap .ij-tag-chip button { background: none; border: none; color: #0369a1; cursor: pointer; padding: 0; font-size: 13px; line-height: 1; }
        #jrwrap .ij-rec-label { font-size: 10.5px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 8px; }
        #jrwrap .ij-rec-pills { display: flex; flex-wrap: wrap; gap: 7px; }
        #jrwrap .ij-rec-pill {
            padding: 5px 11px; border-radius: 999px; border: 1px solid #cbd5e1; background: #fff;
            font-size: 12px; color: #1e293b; cursor: pointer; transition: border-color 0.15s, background-color 0.15s;
        }
        #jrwrap .ij-rec-pill:hover { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; }
        #jrwrap .rv-row { display: flex; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        #jrwrap .rv-label { color: #687795; }
        #jrwrap .rv-val { font-weight: 500; color: #10143b; text-align: right; }

        /* Service Team 2-Column Picker & Cards (1:1 Hiring Team Match) */
        #jrwrap .ij-team-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        #jrwrap .ij-team-search-wrap { position: relative; }
        #jrwrap .ij-team-search-box {
            display: flex; align-items: center; gap: 8px; border: 1px solid #dce2ed; border-radius: 8px;
            background: #ffffff; padding: 4px 6px 4px 12px; min-height: 40px;
        }
        #jrwrap .ij-team-search-box:focus-within { border-color: #e6af00; box-shadow: 0 0 0 3px rgba(230, 175, 0, 0.14); }
        #jrwrap .ij-team-search-input { border: none; outline: none; flex: 1; font-size: 13px; color: #10143b; background: transparent; min-width: 0; }
        #jrwrap .ij-team-add-btn {
            padding: 5px 12px; border-radius: 6px; border: 1px solid #f3d06b; background: #fffbeb;
            color: #9a6500; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;
            display: inline-flex; align-items: center; gap: 5px;
        }
        #jrwrap .ij-team-add-btn:hover { background: #fef3c7; border-color: #e6af00; }
        #jrwrap .ij-team-dropdown {
            position: absolute; left: 0; right: 0; top: calc(100% + 4px); z-index: 40; background: #ffffff;
            border: 1px solid #dce2ed; border-radius: 10px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            max-height: 240px; overflow-y: auto; display: none;
        }
        #jrwrap .ij-team-dd-item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 12px;
            cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 12.5px;
        }
        #jrwrap .ij-team-dd-item:last-child { border-bottom: none; }
        #jrwrap .ij-team-dd-item:hover { background: #fffdf5; }
        #jrwrap .ij-team-cards { display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
        #jrwrap .ij-team-card {
            display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px;
            border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff;
        }
        #jrwrap .ij-team-card-left { display: flex; align-items: center; gap: 11px; min-width: 0; }
        #jrwrap .ij-team-avatar, .ij-side-drawer .ij-team-avatar {
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #ffffff; font-size: 13px; font-weight: 700; flex-shrink: 0; text-transform: uppercase;
        }
        #jrwrap .ij-team-name { font-size: 13px; font-weight: 600; color: #10143b; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        #jrwrap .ij-team-role { font-size: 11.5px; color: #687795; margin-top: 1px; }
        #jrwrap .ij-lead-badge, .ij-side-drawer .ij-lead-badge {
            display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px;
            background: #fef3c7; border: 1px solid #fde68a; color: #b45309; font-size: 11px; font-weight: 600; white-space: nowrap;
        }
        #jrwrap .ij-team-remove, .ij-side-drawer .ij-team-remove {
            background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 16px; padding: 2px 6px; line-height: 1;
        }
        #jrwrap .ij-team-remove:hover, .ij-side-drawer .ij-team-remove:hover { color: #dc2626; }

        /* Sliding Right Drawers for Account Owner & Delivery Team (1:1 Drawer Match) */
        .ij-drawer-backdrop {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.35); z-index: 1090;
            opacity: 0; pointer-events: none; transition: opacity 0.2s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .ij-drawer-backdrop.is-open { opacity: 1; pointer-events: auto; }
        .ij-side-drawer {
            position: fixed; top: 0; right: 0; bottom: 0; width: min(560px, 95vw); background: #ffffff;
            z-index: 1095; box-shadow: -12px 0 36px rgba(15, 23, 42, 0.16);
            display: flex; flex-direction: column;
            transform: translateX(100%); transition: transform 0.26s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .ij-side-drawer.is-open { transform: translateX(0); }
        @media (prefers-reduced-motion: reduce) {
            .ij-drawer-backdrop, .ij-side-drawer { transition: none; }
        }
        .ij-drawer-head {
            display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 20px;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        .ij-drawer-head-title { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 700; color: #10143b; }
        .ij-drawer-head-sub { font-size: 12px; font-weight: 500; color: #64748b; }
        .ij-drawer-close {
            width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;
            color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px;
        }
        .ij-drawer-close:hover { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
        .ij-drawer-assigned { padding: 14px 20px; border-bottom: 1px solid #e2e8f0; background: #ffffff; }
        .ij-drawer-sec-bar {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;
            font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px;
        }
        .ij-drawer-chip-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .ij-drawer-chip {
            display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 10px;
            border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff;
        }
        .ij-drawer-chip.is-lead { border-color: #facc15; background: #fffdf5; }
        .ij-drawer-chip-btn {
            padding: 2px 7px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc;
            font-size: 10.5px; font-weight: 600; color: #475569; cursor: pointer; white-space: nowrap;
        }
        .ij-drawer-chip-btn:hover { border-color: #d97706; color: #b45309; background: #fffbeb; }
        .ij-drawer-search-bar {
            padding: 12px 20px; border-bottom: 1px solid #e2e8f0; background: #ffffff;
            display: flex; flex-direction: column; gap: 10px;
        }
        .ij-drawer-search-input {
            width: 100%; height: 38px; padding: 0 12px 0 34px; border: 1.5px solid #3b82f6; border-radius: 8px;
            font-size: 13px; color: #10143b; outline: none;
        }
        .ij-drawer-filter-pills { display: flex; flex-wrap: wrap; gap: 6px; }
        .ij-drawer-filter-pill {
            padding: 4px 11px; border-radius: 999px; border: 1px solid #cbd5e1; background: #ffffff;
            font-size: 11.5px; font-weight: 600; color: #475569; cursor: pointer;
        }
        .ij-drawer-filter-pill.is-active { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
        .ij-drawer-list { flex: 1; overflow-y: auto; padding: 8px 20px 20px; }
        .ij-drawer-row {
            display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9; border-radius: 8px;
        }
        .ij-drawer-row.is-assigned { background: #ecfdf5; }
        .ij-drawer-assign-btn {
            padding: 5px 12px; border-radius: 8px; border: 1px solid #bfdbfe; background: #ffffff;
            color: #2563eb; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;
        }
        .ij-drawer-assign-btn:hover { background: #eff6ff; }
        .ij-drawer-assign-btn.is-assigned {
            border-color: #a7f3d0; background: #ffffff; color: #059669;
        }

        @media (max-width: 1199px) {
            #jrwrap .ij-choice-grid-5 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            #jrwrap .ij-choice-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            #jrwrap .r4-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            #jrwrap .form-grid, #jrwrap .form-grid-3, #jrwrap .ij-choice-grid-3, #jrwrap .ij-choice-grid-4, #jrwrap .ij-choice-grid-5, #jrwrap .r4-grid, #jrwrap .step2-split-grid, #jrwrap .step3-split-grid, #jrwrap .ij-team-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    @php
        $isEdit = ($page_event ?? 'ADD') === 'EDIT' && isset($offering);
        $currencyList = $currency ?? [];
        $defaultCurrency = $offering->currency_code ?? ($currency_code ?? 'INR');
        $tz = $tenantTimezone ?? 'UTC';
    @endphp

    <div id="jrwrap" class="internal-job-create">
        <div class="content">
            {{-- Edge-to-Edge Sticky Header + 4-Step Stepper --}}
            <div class="ij-header">
                <div class="ij-heading">
                    <div>
                        <h1>{{ $isEdit ? 'Edit Service' : 'Add Service' }}</h1>
                        <p>Enter service details and publish</p>
                    </div>
                </div>

                <div class="stepper-v2" id="stepper">
                    <div class="step-v2 current" data-step="1" onclick="crmGoToStep(1)">
                        <div class="step-v2-icon">1</div>
                        <div class="step-v2-info"><div class="step-v2-label">Basic Details</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="2" onclick="crmGoToStep(2)">
                        <div class="step-v2-icon">2</div>
                        <div class="step-v2-info"><div class="step-v2-label">Service Description</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="3" onclick="crmGoToStep(3)">
                        <div class="step-v2-icon">3</div>
                        <div class="step-v2-info"><div class="step-v2-label">Qualification</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="4" onclick="crmGoToStep(4)">
                        <div class="step-v2-icon">4</div>
                        <div class="step-v2-info"><div class="step-v2-label">Review &amp; Publish</div></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-secondary" id="btnTopSaveDraft" style="padding:6px 12px; font-size:12px;">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary" id="btnTopPublish" style="padding:6px 14px; font-size:12px;">
                        <span>Next Page</span> <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <form id="crmOfferingForm" method="POST" action="javascript:void(0);" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="operation_id" id="operation_id" value="{{ $offering->operation_id ?? \Illuminate\Support\Str::uuid()->toString() }}">

                {{-- ================= STEP 1: BASIC DETAILS ================= --}}
                <div class="step-content active" id="step1">
                    {{-- Card 1: Basic Information (Matches Screenshot 1) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-box-open"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Basic Information</div>
                                    <div class="ij-card-head-sub">Enter the basic details for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid">
                                <div class="form-group-cus" data-field="offering_class">
                                    <label class="form-label required" for="offering_class">Select Offering (Service / Product)</label>
                                    <select class="form-select" id="offering_class" name="offering_class" required>
                                        <option value="service" {{ ($offering->offering_class ?? 'service') === 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="product" {{ ($offering->offering_class ?? 'service') === 'product' ? 'selected' : '' }}>Product</option>
                                    </select>
                                    <span class="form-hint">*Choose whether you are adding a Service or a Product.</span>
                                </div>

                                <div class="form-group-cus" data-field="product_url">
                                    <label class="form-label" for="product_url">Service URL</label>
                                    <input type="url" class="form-input" id="product_url" name="product_url" maxlength="500" value="{{ $offering->product_url ?? '' }}" placeholder="https://example.com/service">
                                    <span class="form-hint">Optional public landing page or service brochure URL.</span>
                                    <span class="form-error">Please enter a valid URL (e.g. https://...).</span>
                                </div>

                                <div class="form-group-cus" data-field="name">
                                    <label class="form-label required" for="offering_name">Service Name</label>
                                    <input type="text" class="form-input" id="offering_name" name="name" maxlength="255" value="{{ $offering->name ?? '' }}" placeholder="Enter service name" required>
                                    <span class="form-hint">*Enter the full name of this service.</span>
                                    <span class="form-error">Service Name is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="sku">
                                    <label class="form-label required" for="sku">Service Code / SKU</label>
                                    <input type="text" class="form-input" id="sku" name="sku" value="{{ $offering->sku ?? '' }}" placeholder="Enter service code (e.g. SRV-001)" style="text-transform: uppercase;">
                                    <span class="form-hint">*Unique service code within your company catalog.</span>
                                    <span class="form-error" id="skuError">Service code already exists in your company catalog.</span>
                                </div>

                                <div class="form-group-cus" data-field="crm_category">
                                    <label class="form-label required" for="crm_category">Service Category</label>
                                    <select class="form-select" id="crm_category" name="crm_category" required>
                                        <option value="">Select Category</option>
                                        <optgroup label="Service Categories">
                                            <option value="IT & Cloud">IT &amp; Cloud</option>
                                            <option value="Software / SaaS">Software / SaaS</option>
                                            <option value="AI & Automation">AI &amp; Automation</option>
                                            <option value="Consulting & Advisory">Consulting &amp; Advisory</option>
                                            <option value="Managed Services">Managed Services</option>
                                        </optgroup>
                                        @if(!empty($industries))
                                            <optgroup label="Industries">
                                                @foreach($industries as $ind)
                                                    <option value="{{ $ind['name'] ?? ($ind['title'] ?? '') }}" data-industry-id="{{ $ind['id'] ?? '' }}">
                                                        {{ $ind['name'] ?? ($ind['title'] ?? '') }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                    <input type="hidden" name="industry_id" id="industry_id" value="">
                                    <span class="form-hint">*Please select the category for this service.</span>
                                    <span class="form-error">Service Category is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="offering_type">
                                    <label class="form-label required" for="offering_type">Service Type</label>
                                    <select class="form-select" id="offering_type" name="offering_type">
                                        <option value="Professional Service" selected>Professional Service</option>
                                        <option value="Consulting">Consulting</option>
                                        <option value="Managed Service">Managed Service</option>
                                        <option value="Staffing / Recruitment Service">Staffing / Recruitment Service</option>
                                        <option value="Software / SaaS">Software / SaaS</option>
                                        <option value="Subscription">Subscription</option>
                                        <option value="Digital Product">Digital Product</option>
                                        <option value="Physical Product">Physical Product</option>
                                    </select>
                                    <span class="form-hint">Select the service or product delivery type.</span>
                                </div>

                                <div class="form-group-cus" data-field="owner_client_id">
                                    <label class="form-label" for="owner_client_id">Organization / Brand Owner</label>
                                    <select class="form-select" id="owner_client_id" name="owner_client_id">
                                        @foreach(($clients ?? collect()) as $cl)
                                            <option value="{{ $cl->id }}" {{ $cl->self_company ? 'selected' : '' }}>
                                                {{ $cl->name }} {{ $cl->self_company ? '(My Organization)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint">Select your organization or vendor/partner owner.</span>
                                </div>

                                <div class="form-group-cus" data-field="address_id">
                                    <label class="form-label" for="address_id">Warehouse / Origin Address</label>
                                    <select class="form-select" id="address_id" name="address_id">
                                        <option value="">Select Address (Optional for Digital)</option>
                                        @foreach(($clientAddresses ?? collect()) as $addr)
                                            <option value="{{ $addr->id }}"
                                                    data-client-id="{{ $addr->client_id }}"
                                                    data-city="{{ $addr->city }}"
                                                    {{ (int) ($offering->address_id ?? 0) === (int) $addr->id ? 'selected' : '' }}>
                                                {{ trim(($addr->address ?? '') . ($addr->city ? ', ' . $addr->city : '') . ($addr->postcode ? ' - ' . $addr->postcode : ''), ', ') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint">Filtered automatically by the selected Organization / Brand Owner.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Service Capacity & Status --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-cubes"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Capacity &amp; Status</div>
                                    <div class="ij-card-head-sub">Specify service capacity, billing unit, and catalog status for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus" data-field="unit">
                                    <label class="form-label" for="unit">Service Billing Unit</label>
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="project">Project / Engagement</option>
                                        <option value="hour">Hour</option>
                                        <option value="day">Day</option>
                                        <option value="month">Monthly Retainer</option>
                                        <option value="milestone">Milestone</option>
                                        <option value="license">License / Seat</option>
                                    </select>
                                    <span class="form-hint">Unit used on service quotations &amp; contracts.</span>
                                </div>

                                <div class="form-group-cus" data-field="available_capacity">
                                    <label class="form-label required" for="available_capacity">Service Capacity / Active Slots</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" min="0" class="form-input" id="available_capacity" name="available_capacity" value="100">
                                        <label class="d-flex align-items-center gap-1 mb-0" style="white-space:nowrap; font-size:12px; cursor:pointer;">
                                            <input type="checkbox" id="is_unlimited_capacity" name="is_unlimited_capacity" value="1"> Unlimited
                                        </label>
                                    </div>
                                    <span class="form-hint">Concurrent client capacity (or Unlimited for ongoing services).</span>
                                </div>

                                <div class="form-group-cus" data-field="status">
                                    <label class="form-label required" for="offering_status">Service Status</label>
                                    <select class="form-select" id="offering_status" name="status">
                                        <option value="active" selected>Active</option>
                                        <option value="draft">Draft</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="temporarily_unavailable">Temporarily Unavailable</option>
                                        <option value="discontinued">Discontinued</option>
                                    </select>
                                    <span class="form-hint">Inactive services cannot be used in new proposals or deals.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Service Pricing --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-rupee-sign"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Pricing</div>
                                    <div class="ij-card-head-sub">Define the pricing and billing details for this service.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:12px; font-weight:500; color:#475569;">Pricing Visibility</span>
                                <select class="form-select" id="pricing_visibility" name="pricing_visibility" style="width:auto; height:32px; padding:4px 10px; font-size:12px;">
                                    <option value="public_catalog">Visible Everywhere</option>
                                    <option value="client_portal">Client Portal &amp; Quotes</option>
                                    <option value="internal_only">Internal Sales Only</option>
                                    <option value="confidential">Price on Request</option>
                                </select>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus pricing-input-row" data-field="min_price">
                                    <label class="form-label required" for="min_price">Base / Minimum Price</label>
                                    <div class="ij-price-group">
                                        <select class="ij-curr-select" id="currency_code" name="currency_code">
                                            @foreach($currencyList as $cCode)
                                                <option value="{{ $cCode }}" {{ strtoupper((string)$cCode) === strtoupper((string)$defaultCurrency) ? 'selected' : '' }}>{{ $cCode }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" min="0" class="ij-price-input" id="min_price" name="min_price" placeholder="Enter base service price">
                                    </div>
                                    <span class="form-hint">*Starting or standard price offered for this service.</span>
                                    <span class="form-error">Base / Minimum Price is required.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row" data-field="max_price">
                                    <label class="form-label" for="max_price">Maximum / Package Price</label>
                                    <div class="ij-price-group">
                                        <span class="ij-curr-select" id="maxCurrMirror" style="display:flex;align-items:center;">{{ $defaultCurrency }}</span>
                                        <input type="number" step="0.01" min="0" class="ij-price-input" id="max_price" name="max_price" placeholder="Enter max package price">
                                    </div>
                                    <span class="form-hint">Optional upper price range for tiered services.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row">
                                    <label class="form-label required" for="pricing_frequency">Billing Cycle</label>
                                    <select class="form-select" id="pricing_frequency" name="pricing_frequency">
                                        <option value="One-Time">One-Time</option>
                                        <option value="Hourly">Hourly</option>
                                        <option value="Daily">Daily</option>
                                        <option value="Weekly">Weekly</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Annual">Annual</option>
                                        <option value="Per Milestone">Per Milestone</option>
                                    </select>
                                    <span class="form-hint">Billing frequency for this service.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Service Delivery Coverage --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Delivery Coverage</div>
                                    <div class="ij-card-head-sub">Select up to 15 regions, cities, or remote zones where this service is delivered.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="ij-hubs-bar" id="popularHubsPills">
                                <span style="font-size:12px; font-weight:500; color:#475569;">Popular Hubs:</span>
                                @foreach([
                                    ['label' => 'Bengaluru', 'canonical' => 'Bengaluru (Karnataka, India)'],
                                    ['label' => 'Mumbai', 'canonical' => 'Mumbai (Maharashtra, India)'],
                                    ['label' => 'Delhi NCR', 'canonical' => 'Delhi NCR (India)'],
                                    ['label' => 'Hyderabad', 'canonical' => 'Hyderabad (Telangana, India)'],
                                    ['label' => 'Pune', 'canonical' => 'Pune (Maharashtra, India)'],
                                    ['label' => 'London', 'canonical' => 'London (United Kingdom)'],
                                    ['label' => 'New York', 'canonical' => 'New York (United States)'],
                                    ['label' => 'Singapore', 'canonical' => 'Singapore (Singapore)'],
                                ] as $hub)
                                    <button type="button" class="ij-hub-pill" data-label="{{ $hub['label'] }}" data-val="{{ $hub['canonical'] }}">+ {{ $hub['label'] }}</button>
                                @endforeach
                            </div>
                            <div class="ij-city-search-wrap">
                                <i class="fas fa-search ij-city-search-ico"></i>
                                <input type="text" class="ij-city-search-input" id="locationInput" placeholder="Search for cities (e.g. Noida, Bengaluru, Mumbai, London)..." autocomplete="off">
                                <button type="button" class="ij-city-clear-btn" id="btnCitySearchClear" title="Clear">&times;</button>
                                <div class="ij-city-dropdown" id="citySearchDropdown"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size:12.5px;">
                                <span id="selectedLocationsCount" style="color:#475569; font-weight:500;">Selected Coverage Areas (0/15)</span>
                                <button type="button" id="btnClearLocations" style="background:none;border:none;color:#9a6500;font-weight:600;cursor:pointer;">Clear All</button>
                            </div>
                            <div class="ij-selected-chips" id="locationsBox"></div>
                            <input type="hidden" name="locations" id="locations_json" value="[]">
                        </div>
                    </div>

                    {{-- Card 5: Pricing Model (Matches Screenshot 3 Position Type) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-tags"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Pricing Model <span style="color:#ef4444;">*</span></div>
                                    <div class="ij-card-head-sub">Select the commercial pricing structure for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="pricing_model" id="pricing_model" value="Fixed Price">
                            <div class="ij-choice-grid-4" id="pricingModelSelector">
                                <button type="button" class="ij-choice-card is-selected" data-val="Fixed Price">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-tag"></i></span>
                                    <div><div class="ij-choice-title">Fixed Price</div><div class="ij-choice-sub">Fixed-scope engagement fee</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Subscription">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-sync-alt"></i></span>
                                    <div><div class="ij-choice-title">Subscription</div><div class="ij-choice-sub">Recurring billing plan</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Per Unit">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-cubes"></i></span>
                                    <div><div class="ij-choice-title">Per Unit</div><div class="ij-choice-sub">Quantity / seat-based pricing</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Custom / Negotiable">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-handshake"></i></span>
                                    <div><div class="ij-choice-title">Custom / Negotiable</div><div class="ij-choice-sub">Quote-based enterprise pricing</div></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6: Delivery Mode (Matches Screenshot 3 Work Mode) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-globe"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Delivery Mode</div>
                                    <div class="ij-card-head-sub">Choose whether this service is delivered Remote, Hybrid, or On-site.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="delivery_model" id="delivery_model" value="Remote">
                            <div class="ij-choice-grid-3" id="deliveryModelSelector">
                                <button type="button" class="ij-choice-card is-selected" data-val="Remote">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-cloud"></i></span>
                                    <div><div class="ij-choice-title">Remote / Cloud</div><div class="ij-choice-sub">Delivered online or remotely</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Hybrid">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-layer-group"></i></span>
                                    <div><div class="ij-choice-title">Hybrid</div><div class="ij-choice-sub">On-site + Remote delivery</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="On-site">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-building"></i></span>
                                    <div><div class="ij-choice-title">On-site</div><div class="ij-choice-sub">Delivered at client location</div></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Card 7: Service Availability --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Support &amp; Availability</div>
                                    <div class="ij-card-head-sub">Specify the operational support window for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="service_availability" id="service_availability" value="Business Hours">
                            <div class="ij-choice-grid-5" id="availabilitySelector">
                                @foreach([
                                    ['val' => '24 x 7', 'sub' => 'Round-the-clock support', 'ico' => 'fa-headset'],
                                    ['val' => 'Business Hours', 'sub' => 'Standard daytime hours', 'ico' => 'fa-sun'],
                                    ['val' => 'Flexible Hours', 'sub' => 'Custom SLA schedule', 'ico' => 'fa-clock'],
                                    ['val' => 'On Demand', 'sub' => 'Scheduled per request', 'ico' => 'fa-bolt'],
                                    ['val' => 'Not Applicable', 'sub' => 'No ongoing support window', 'ico' => 'fa-minus-circle'],
                                ] as $sa)
                                    <button type="button" class="ij-choice-card {{ $sa['val'] === 'Business Hours' ? 'is-selected' : '' }}" data-val="{{ $sa['val'] }}">
                                        <span class="ij-radio-dot"></span>
                                        <div><div class="ij-choice-title">{{ $sa['val'] }}</div><div class="ij-choice-sub">{{ $sa['sub'] }}</div></div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Card 8: Service Specifications, SLA & Media --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-shield-alt"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Specifications &amp; Media</div>
                                    <div class="ij-card-head-sub">Specify service duration, implementation timeline, SLA, brochure, and validity period.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label" for="service_duration">Service Duration</label>
                                    <input type="text" class="form-input" id="service_duration" name="service_duration" value="{{ $offering->service_duration ?? '' }}" placeholder="e.g. 6 Months / Ongoing">
                                    <span class="form-hint">Standard engagement or contract duration.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="implementation_timeline">Implementation Timeline</label>
                                    <input type="text" class="form-input" id="implementation_timeline" name="implementation_timeline" value="{{ $offering->implementation_timeline ?? '' }}" placeholder="e.g. 2-4 Weeks">
                                    <span class="form-hint">Expected onboarding or kickoff lead time.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="sla_details">Support &amp; Maintenance / SLA</label>
                                    <input type="text" class="form-input" id="sla_details" name="sla_details" value="{{ $offering->sla_details ?? '' }}" placeholder="e.g. 99.9% Uptime / 4h Response">
                                    <span class="form-hint">Response time or service-level commitment.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="warranty">Warranty / Guarantee</label>
                                    <input type="text" class="form-input" id="warranty" name="warranty" value="{{ $offering->warranty ?? '' }}" placeholder="e.g. 12 Months Support Guarantee">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="brochure">Service Brochure / Media</label>
                                    <input type="file" class="form-input" id="brochure" name="brochure" accept=".png,.jpg,.jpeg,.webp,.pdf" style="padding-top:6px;">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="validity_days">Catalog Validity</label>
                                    <select class="form-select" id="validity_days" name="validity_days">
                                        <option value="ongoing" selected>Ongoing / No Expiry</option>
                                        <option value="30">30 Days</option>
                                        <option value="60">60 Days</option>
                                        <option value="90">90 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 9: Service Team (Matches Hiring Team 2-Column Layout) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-users"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Team</div>
                                    <div class="ij-card-head-sub">Assign the account owner / client contact and internal delivery team for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="ij-team-grid">
                                {{-- Left: Account Owner | Service Representative --}}
                                <div>
                                    <label class="form-label" for="accountOwnerSearchInput">Account Owner | Service Representative</label>
                                    <div class="ij-team-search-wrap">
                                        <div class="ij-team-search-box">
                                            <i class="fas fa-search" style="color:#94a3b8;font-size:13px;"></i>
                                            <input type="text" class="ij-team-search-input" id="accountOwnerSearchInput" placeholder="Search account owner / contact..." autocomplete="off">
                                            <button type="button" class="ij-team-add-btn" id="btnShowOwnerDropdown"><i class="fas fa-plus"></i> Add More</button>
                                        </div>
                                        <div class="ij-team-dropdown" id="accountOwnerDropdown"></div>
                                    </div>
                                    <span class="form-hint">Select the Account Owner or client representative responsible for this service.</span>
                                    <input type="hidden" name="hiring_namager" id="hiring_namager" value="">
                                    <input type="hidden" name="hiring_manager_id" id="hiring_manager_id" value="">
                                    <div class="ij-team-cards" id="accountOwnerCardList"></div>
                                </div>

                                {{-- Right: Service Delivery Team --}}
                                <div>
                                    <label class="form-label" for="deliveryTeamSearchInput">Service Delivery Team</label>
                                    <div class="ij-team-search-wrap">
                                        <div class="ij-team-search-box">
                                            <i class="fas fa-search" style="color:#94a3b8;font-size:13px;"></i>
                                            <input type="text" class="ij-team-search-input" id="deliveryTeamSearchInput" placeholder="Search team members..." autocomplete="off">
                                            <button type="button" class="ij-team-add-btn" id="btnShowTeamDropdown"><i class="fas fa-plus"></i> Add More</button>
                                        </div>
                                        <div class="ij-team-dropdown" id="deliveryTeamDropdown"></div>
                                    </div>
                                    <span class="form-hint">Assign the internal leads or team members responsible for delivering this service.</span>
                                    <input type="hidden" name="recruiter" id="recruiter_names_hidden" value="">
                                    <input type="hidden" name="recruiter_id" id="recruiter_ids_hidden" value="">
                                    <input type="hidden" name="team_members" id="team_members_hidden" value="[]">
                                    <div class="ij-team-cards" id="deliveryTeamCardList"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <span></span>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(2)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 2: SERVICE DESCRIPTION & FEATURES ================= --}}
                <div class="step-content" id="step2">
                    <div class="step2-split-grid">
                        {{-- Left Column: Service Description --}}
                        <div class="card mb-0">
                            <div class="ij-card-head">
                                <div class="ij-card-head-left">
                                    <div class="ij-card-head-icon"><i class="fas fa-file-alt"></i></div>
                                    <div>
                                        <div class="ij-card-head-title">Service Description <span style="color:#ef4444;">*</span></div>
                                        <div class="ij-card-head-sub">Provide a detailed and clear service description.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="ij-card-inner">
                                <div class="editor-toolbar">
                                    <button type="button" class="toolbar-btn" data-cmd="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="underline" title="Underline"><i class="fas fa-underline"></i></button>
                                    <span style="width:1px;height:18px;background:#dce2ed;margin:0 4px;"></span>
                                    <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                    <span style="width:1px;height:18px;background:#dce2ed;margin:0 4px;"></span>
                                    <button type="button" class="toolbar-btn" data-cmd="justifyLeft" title="Align Left"><i class="fas fa-align-left"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="justifyCenter" title="Align Center"><i class="fas fa-align-center"></i></button>
                                    <span style="width:1px;height:18px;background:#dce2ed;margin:0 4px;"></span>
                                    <button type="button" class="toolbar-btn" data-cmd="removeFormat" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                                </div>
                                <div class="rich-editor" id="crmDescriptionEditor" contenteditable="true" style="min-height: 270px;" data-placeholder="Enter detailed service description, scope, deliverables, and customer benefits..."></div>
                                <textarea name="description" id="crm_description" class="d-none"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:11.5px; color:#64748b;">
                                    <span>Use the toolbar to format your description</span>
                                    <span id="descCharCount">0 characters</span>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Service Features & Tags --}}
                        <div class="card mb-0">
                            <div class="ij-card-head">
                                <div class="ij-card-head-left">
                                    <div class="ij-card-head-icon"><i class="fas fa-tag"></i></div>
                                    <div>
                                        <div class="ij-card-head-title">Service Features &amp; Tags</div>
                                        <div class="ij-card-head-sub">Add the key features &amp; capabilities for this service.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="ij-card-inner">
                                <label class="form-label required mb-2" for="featureTagInput">Add features for this service (press Enter or comma to add)</label>
                                <div class="ij-city-search-wrap mb-0">
                                    <i class="fas fa-search ij-city-search-ico"></i>
                                    <input type="text" class="ij-city-search-input" id="featureTagInput" placeholder="Type a feature (e.g. 24/7 Support, API Integration, SLA...)" autocomplete="off">
                                </div>

                                <div class="ij-selected-tags-card">
                                    <div class="ij-selected-tags-head">
                                        <div style="font-size:12px; font-weight:600; color:#10143b;">
                                            <i class="fas fa-check me-1 text-muted"></i> Selected Features
                                            <span class="ij-tags-count-pill" id="featureTagsCountBadge">0/10 added</span>
                                        </div>
                                        <button type="button" id="btnClearFeatureTags" style="background:none;border:none;color:#dc2626;font-size:11.5px;font-weight:600;cursor:pointer;display:none;">Clear All</button>
                                    </div>
                                    <div id="featureTagsBox" style="display:flex; flex-wrap:wrap; gap:6px; min-height:24px;">
                                        <span id="emptyFeatureTagsMsg" style="font-size:11.5px; color:#94a3b8;">No features added yet.</span>
                                    </div>
                                </div>

                                <div class="ij-rec-label">Recommended &amp; Popular Features:</div>
                                <div class="ij-rec-pills" id="recommendedFeaturesList">
                                    @foreach(['24/7 Support', 'API Integration', 'Custom SLA', 'Cloud Native', 'ISO 27001', 'Multi-Tenant', 'SSO / SAML', 'Dedicated Account Manager', 'Onboarding & Training', 'Real-Time Analytics', 'White-Label', 'Automated Reporting'] as $feat)
                                        <button type="button" class="ij-rec-pill" data-val="{{ $feat }}">+ {{ $feat }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="feature_tags" id="feature_tags_json" value="[]">
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(3)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 3: CUSTOMER QUALIFICATION QUESTIONS ================= --}}
                <div class="step-content" id="step3">
                    <div class="step3-split-grid">
                        {{-- Left Column: Qualification Questions Card --}}
                        <div class="card mb-0">
                            <div class="ij-card-head">
                                <div class="ij-card-head-left">
                                    <div class="ij-card-head-icon"><i class="fas fa-question-circle"></i></div>
                                    <div>
                                        <div class="ij-card-head-title">Customer Qualification Questions</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" id="btnAutoGenerateQ" style="padding:6px 12px; font-size:12px;">
                                    <i class="fas fa-magic"></i> Auto-Add Discovery Questions
                                </button>
                            </div>
                            <div class="ij-card-inner">
                                <p style="font-size:12px; color:#64748b; margin-bottom:14px;">Add questions to qualify leads and prospects before preparing a quote. You can set questions as required or optional.</p>
                                <div id="questionsContainer" class="mb-3"></div>

                                {{-- Inline Question Composer (toggled by Add Qualification Question box) --}}
                                <div id="questionComposer" class="d-none p-3 mb-3 border rounded" style="background:#f8fafc;">
                                    <div class="form-grid mb-2">
                                        <div class="form-group-cus full-width">
                                            <label class="form-label required" for="customQuestionInput">Question</label>
                                            <input type="text" class="form-input" id="customQuestionInput" placeholder="e.g. What is your target budget range?">
                                        </div>
                                        <div class="form-group-cus">
                                            <label class="form-label" for="customQuestionType">Answer Type</label>
                                            <select class="form-select" id="customQuestionType">
                                                <option value="short">Short Text</option>
                                                <option value="paragraph">Paragraph</option>
                                                <option value="yesno">Yes / No</option>
                                                <option value="number">Number</option>
                                                <option value="date">Date</option>
                                            </select>
                                        </div>
                                        <div class="form-group-cus justify-content-end">
                                            <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                                <input type="checkbox" id="customQuestionRequired" checked> Required question
                                            </label>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" id="btnCancelQuestion" style="padding:6px 12px; font-size:12px;">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="btnAddQuestion" style="padding:6px 14px; font-size:12px;">Save Question</button>
                                    </div>
                                </div>

                                <div class="ij-add-q-box" id="addQuestionDropzone">
                                    <div class="ij-add-q-circle"><i class="fas fa-plus"></i></div>
                                    <div style="font-size:13.5px; font-weight:600; color:#10143b;">Add Qualification Question</div>
                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">Create a custom question to qualify leads &amp; prospects</div>
                                </div>
                                <input type="hidden" name="qualification_questions" id="qualification_questions_json" value="[]">
                            </div>
                        </div>

                        {{-- Right Column: Help & Tips + Popular Templates --}}
                        <div>
                            <div class="ij-help-card">
                                <div style="font-size:13px; font-weight:600; color:#10143b; margin-bottom:6px;">
                                    <i class="fas fa-lightbulb me-1" style="color:#d97706;"></i> Help &amp; Tips
                                </div>
                                <p style="font-size:12px; color:#475569; margin-bottom:8px;">Qualification questions help you pre-qualify prospects before preparing quotes or proposals.</p>
                                <div style="font-size:12px; color:#10143b; margin-bottom:4px;">Recommended: <strong style="color:#059669;">3-5 questions</strong></div>
                                <p style="font-size:11.5px; color:#64748b;">Keep questions simple and relevant to the service.</p>
                            </div>

                            <div class="ij-tpl-card">
                                <div class="ij-tpl-head">Popular Templates</div>
                                <div id="discoveryTemplates">
                                    @foreach([
                                        ['q' => 'What is your target budget range?', 'ico' => 'fa-rupee-sign', 't' => 'short'],
                                        ['q' => 'What is your expected go-live date?', 'ico' => 'fa-calendar-alt', 't' => 'date'],
                                        ['q' => 'How many user seats or units do you require?', 'ico' => 'fa-users', 't' => 'number'],
                                        ['q' => 'What is your existing tech stack or setup?', 'ico' => 'fa-layer-group', 't' => 'short'],
                                        ['q' => 'Do you require a custom NDA or compliance review?', 'ico' => 'fa-shield-alt', 't' => 'yesno'],
                                        ['q' => 'Who is the primary decision maker for this project?', 'ico' => 'fa-user-tie', 't' => 'short'],
                                    ] as $tpl)
                                        <div class="ij-tpl-row" data-q="{{ $tpl['q'] }}" data-type="{{ $tpl['t'] }}">
                                            <span class="d-flex align-items-center gap-2"><i class="fas {{ $tpl['ico'] }} text-muted"></i> {{ $tpl['q'] }}</span>
                                            <span class="ij-tpl-plus"><i class="fas fa-plus"></i></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(4)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 4: REVIEW & PUBLISH (3-Column 7-Card Layout) ================= --}}
                <div class="step-content" id="step4">
                    <div class="r4-grid mb-4">
                        {{-- Column 1: Basic Information + Service Description --}}
                        <div class="r4-col">
                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-file-alt"></i></div>
                                        <div>
                                            <div class="ij-card-head-title">Basic Information</div>
                                            <div class="ij-card-head-sub">Core details for this service.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="crmGoToStep(1)"><i class="fas fa-pen"></i> Edit</button>
                                </div>
                                <div class="ij-card-inner">
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-building"></i></span> Organization</span>
                                        <span class="rv-val" id="rvOwner">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#f3e8ff;color:#9333ea;"><i class="fas fa-th-large"></i></span> Category</span>
                                        <span class="rv-val" id="rvCategory">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#fef3c7;color:#d97706;"><i class="fas fa-tag"></i></span> Service Name</span>
                                        <span class="rv-val" id="rvName">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#fef9c3;color:#ca8a04;"><i class="fas fa-barcode"></i></span> Service Code / SKU</span>
                                        <span class="rv-val" id="rvSku">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#e0e7ff;color:#4f46e5;"><i class="fas fa-rupee-sign"></i></span> Pricing</span>
                                        <span class="rv-val" id="rvPricing">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-align-left"></i></div>
                                        <div>
                                            <div class="ij-card-head-title">Service Description</div>
                                            <div class="ij-card-head-sub">Provide a detailed and clear service description.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="crmGoToStep(2)"><i class="fas fa-pen"></i> Edit</button>
                                </div>
                                <div class="ij-card-inner">
                                    <div class="rv-desc-box" id="rvDescPreview">-</div>
                                </div>
                            </div>
                        </div>

                        {{-- Column 2: Delivery & Commercials + Service Features + Qualification Questions --}}
                        <div class="r4-col">
                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-map-marker-alt"></i></div>
                                        <div>
                                            <div class="ij-card-head-title">Delivery &amp; Commercials</div>
                                            <div class="ij-card-head-sub">Coverage and service terms.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="crmGoToStep(1)"><i class="fas fa-pen"></i> Edit</button>
                                </div>
                                <div class="ij-card-inner">
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-map-marker-alt"></i></span> Coverage</span>
                                        <span class="rv-val" id="rvCoverage">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-globe"></i></span> Delivery Mode</span>
                                        <span class="rv-val" id="rvDelivery">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#e0e7ff;color:#2563eb;"><i class="fas fa-clock"></i></span> Availability</span>
                                        <span class="rv-val" id="rvAvailability">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#f3e8ff;color:#9333ea;"><i class="fas fa-briefcase"></i></span> Pricing Model</span>
                                        <span class="rv-val" id="rvModel">-</span>
                                    </div>
                                    <div class="rv-row-ico">
                                        <span class="rv-ico-left"><span class="rv-ico-badge" style="background:#fef3c7;color:#d97706;"><i class="fas fa-check-circle"></i></span> Status</span>
                                        <span class="rv-val" id="rvStatus">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-tag"></i></div>
                                        <div>
                                            <div class="ij-card-head-title" id="rvTagsTitle">Service Features &amp; Tags (0)</div>
                                            <div class="ij-card-head-sub">Key capabilities for this service.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="crmGoToStep(2)"><i class="fas fa-pen"></i> Edit</button>
                                </div>
                                <div class="ij-card-inner">
                                    <div id="rvTagsList" style="display:flex; flex-wrap:wrap; gap:6px;"><span class="text-muted" style="font-size:12px;">No tags added</span></div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-tasks"></i></div>
                                        <div>
                                            <div class="ij-card-head-title" id="rvQuestionsTitle">Qualification Questions (0)</div>
                                            <div class="ij-card-head-sub">Pre-sales discovery questions.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="r4-edit-btn" onclick="crmGoToStep(3)"><i class="fas fa-pen"></i> Edit</button>
                                </div>
                                <div class="ij-card-inner" id="rvQuestionsList">
                                    <span class="text-muted" style="font-size:12px;">No qualification questions added</span>
                                </div>
                            </div>
                        </div>

                        {{-- Column 3: Publishing Visibility + Important Notes --}}
                        <div class="r4-col">
                            <div class="card mb-0" style="border-color:#bfdbfe;">
                                <div class="ij-card-head" style="background:linear-gradient(180deg,#f0f7ff 0%,#e0f2fe 100%); border-bottom-color:#bfdbfe;">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon" style="background:#dbeafe; color:#1d4ed8;"><i class="fas fa-bullhorn"></i></div>
                                        <div>
                                            <div class="ij-card-head-title">Catalog Visibility</div>
                                            <div class="ij-card-head-sub">Control where this service is published and who can view it.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ij-card-inner">
                                    <label class="d-flex align-items-center gap-2 mb-3" style="font-size:12.5px; font-weight:500; cursor:pointer;">
                                        <input type="checkbox" name="visibility_internal_crm" value="1" checked> Internal Sales Team (CRM)
                                    </label>
                                    <label class="d-flex align-items-start gap-2 mb-3" style="font-size:12.5px; cursor:pointer;">
                                        <input type="checkbox" name="visibility_client_portal" value="1" checked style="margin-top:3px;">
                                        <span>
                                            <strong style="font-weight:500;">Client Proposals &amp; Quotes</strong>
                                            <span class="badge bg-success-subtle text-success ms-1" style="font-size:10px;">Recommended</span>
                                            <div style="font-size:11px; color:#64748b;">Available to add directly into client deals, quotes &amp; invoices.</div>
                                        </span>
                                    </label>
                                    <label class="d-flex align-items-start gap-2 mb-2" style="font-size:12.5px; cursor:pointer;">
                                        <input type="checkbox" name="visibility_public_catalog" value="1" style="margin-top:3px;">
                                        <span>
                                            <strong style="font-weight:500;">Public Service Catalog</strong>
                                            <div style="font-size:11px; color:#64748b;">Visible on your public-facing service catalog.</div>
                                        </span>
                                    </label>
                                    <div class="rv-info-box">
                                        <i class="fas fa-info-circle mt-1"></i>
                                        <span>This service is available to your internal sales team and client proposals.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="ij-card-head">
                                    <div class="ij-card-head-left">
                                        <div class="ij-card-head-icon"><i class="fas fa-info-circle"></i></div>
                                        <div>
                                            <div class="ij-card-head-title">Important Notes</div>
                                            <div class="ij-card-head-sub">Key points to keep in mind.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ij-card-inner">
                                    <ul class="rv-notes-list">
                                        <li><span class="rv-notes-dot"></span><span id="rvValidityNote">Catalog validity: Ongoing / No Expiry</span></li>
                                        <li><span class="rv-notes-dot"></span><span>No coin deduction for internal CRM catalog</span></li>
                                        <li><span class="rv-notes-dot"></span><span>You can pause or deactivate the service anytime</span></li>
                                        <li><span class="rv-notes-dot"></span><span>Linked deals, quotations &amp; orders are preserved</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(3)"><i class="fas fa-arrow-left"></i> Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" id="btnBottomSaveDraft"><i class="fas fa-save"></i> Save Draft</button>
                            <button type="button" class="btn btn-primary" id="btnBottomPublish"><i class="fas fa-paper-plane"></i> {{ $isEdit ? 'Update Service' : 'Publish Service' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Sliding Right Drawers: Account Owner & Service Delivery Team (1:1 Drawer Match) --}}
    <div class="ij-drawer-backdrop" id="crmDrawerBackdrop"></div>

    {{-- Drawer 1: Account Owner / Organization Contacts --}}
    <div class="ij-side-drawer" id="crmOwnerDrawer" aria-hidden="true">
        <div class="ij-drawer-head">
            <div class="ij-drawer-head-title">
                <span style="width:32px;height:32px;border-radius:8px;background:#e0f2fe;color:#0284c7;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-building"></i></span>
                <div>
                    <span id="crmOwnerDrawerOrg">Organization</span>
                    <span class="ij-drawer-head-sub"> (Account Owner / Contact)</span>
                </div>
            </div>
            <button type="button" class="ij-drawer-close" id="btnCloseOwnerDrawer" title="Close">&times;</button>
        </div>
        <div class="ij-drawer-assigned">
            <div class="ij-drawer-sec-bar">
                <span id="crmOwnerAssignedCount">ASSIGNED ACCOUNT OWNERS (0)</span>
            </div>
            <div class="ij-drawer-chip-grid" id="crmOwnerDrawerAssignedGrid"></div>
        </div>
        <div class="ij-drawer-search-bar">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;"></i>
                <input type="text" class="ij-drawer-search-input" id="crmOwnerDrawerSearch" placeholder="Search contacts by name, email, designation...">
            </div>
        </div>
        <div class="ij-drawer-list">
            <div class="ij-drawer-sec-bar" style="margin-top:8px;">
                <span id="crmOwnerAvailCount">ORGANIZATION CONTACTS (0)</span>
            </div>
            <div id="crmOwnerDrawerList"></div>
        </div>
    </div>

    {{-- Drawer 2: Service Delivery Team --}}
    <div class="ij-side-drawer" id="crmTeamDrawer" aria-hidden="true">
        <div class="ij-drawer-head">
            <div class="ij-drawer-head-title">
                <span style="width:32px;height:32px;border-radius:8px;background:#fef3c7;color:#b45309;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-users"></i></span>
                <div>
                    <span>{{ auth()->user()->company->company_name ?? 'Your Organization' }}</span>
                    <span class="ij-drawer-head-sub"> (Team Member)</span>
                </div>
            </div>
            <button type="button" class="ij-drawer-close" id="btnCloseTeamDrawer" title="Close">&times;</button>
        </div>
        <div class="ij-drawer-assigned">
            <div class="ij-drawer-sec-bar">
                <span id="crmTeamAssignedCount">ASSIGNED TEAM (0)</span>
                <span style="font-size:11px;font-weight:500;text-transform:none;color:#64748b;">Earliest assigned member acts as Service Lead</span>
            </div>
            <div class="ij-drawer-chip-grid" id="crmTeamDrawerAssignedGrid"></div>
        </div>
        <div class="ij-drawer-search-bar">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;"></i>
                <input type="text" class="ij-drawer-search-input" id="crmTeamDrawerSearch" placeholder="Search team members by name, role...">
            </div>
            <div class="ij-drawer-filter-pills" id="crmTeamFilterPills">
                <button type="button" class="ij-drawer-filter-pill is-active" data-filter="all">All (0)</button>
                <button type="button" class="ij-drawer-filter-pill" data-filter="available">Available (&lt; 3 services)</button>
                <button type="button" class="ij-drawer-filter-pill" data-filter="high">High Load (&gt; 3 services)</button>
            </div>
        </div>
        <div class="ij-drawer-list">
            <div class="ij-drawer-sec-bar" style="margin-top:8px;">
                <span id="crmTeamAvailCount">AVAILABLE TEAM MEMBERS (0)</span>
            </div>
            <div id="crmTeamDrawerList"></div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        (function () {
            'use strict';

            const EDIT_JOB_ID = @json((int) ($editJobId ?? 0));
            const SUBMIT_URL = @json($isEdit ? (($editJobId ?? 0) > 0 ? route('service_company.crm.offerings.updateJob', $editJobId) : route('service_company.crm.offerings.postUpdate', $offering->id)) : route('service_company.crm.offerings.store'));
            const CHECK_SKU_URL = @json(route('service_company.crm.offerings.checkSku'));
            const EDIT_ID = @json($isEdit ? (int) $offering->id : 0);
            const CSRF_TOKEN = @json(csrf_token());

            let currentStep = 1;
            const topActionBtn = document.getElementById('btnTopPublish');
            const isEditMode = @json($isEdit);

            function updateTopActionBtn(stepNum) {
                if (stepNum < 4) {
                    topActionBtn.innerHTML = '<span>Next Page</span> <i class="fas fa-arrow-right"></i>';
                } else {
                    topActionBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>' + (isEditMode ? 'Update Service' : 'Publish Service') + '</span>';
                }
            }

            function validateCrmStep(stepNum) {
                let ok = true;
                document.querySelectorAll('#step' + stepNum + ' .form-group-cus').forEach(function (g) { g.classList.remove('has-error'); });

                if (stepNum === 1) {
                    const nameEl = document.getElementById('offering_name');
                    const catEl = document.getElementById('crm_category');
                    const skuEl = document.getElementById('sku');
                    const minPriceEl = document.getElementById('min_price');
                    const isConf = document.getElementById('pricing_visibility').value === 'confidential';

                    if (!nameEl.value.trim()) { nameEl.closest('.form-group-cus').classList.add('has-error'); ok = false; }
                    if (!skuEl.value.trim()) { skuEl.closest('.form-group-cus').classList.add('has-error'); document.getElementById('skuError').textContent = 'Product / Service SKU / Code is required.'; ok = false; }
                    if (!catEl.value) { catEl.closest('.form-group-cus').classList.add('has-error'); ok = false; }
                    if (!isConf && (minPriceEl.value === '' || parseFloat(minPriceEl.value) < 0)) {
                        minPriceEl.closest('.form-group-cus').classList.add('has-error');
                        ok = false;
                    }
                } else if (stepNum === 2) {
                    const descTxt = (document.getElementById('crmDescriptionEditor').textContent || '').trim();
                    if (!descTxt) {
                        if (typeof toastr !== 'undefined') toastr.error('Please enter Product / Service Description.');
                        else alert('Please enter Product / Service Description.');
                        ok = false;
                    }
                }

                if (!ok) {
                    const firstErr = document.querySelector('#step' + stepNum + ' .has-error');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return ok;
            }

            window.crmGoToStep = function (stepNum) {
                if (stepNum > currentStep && !validateCrmStep(currentStep)) {
                    return;
                }
                currentStep = stepNum;
                document.querySelectorAll('#jrwrap .step-content').forEach(function (el) { el.classList.remove('active'); });
                document.getElementById('step' + stepNum).classList.add('active');
                document.querySelectorAll('#stepper .step-v2').forEach(function (el) {
                    const s = parseInt(el.getAttribute('data-step'), 10);
                    el.classList.remove('current', 'completed', 'pending');
                    if (s === stepNum) el.classList.add('current');
                    else if (s < stepNum) el.classList.add('completed');
                    else el.classList.add('pending');
                });
                updateTopActionBtn(stepNum);
                if (stepNum === 4) populateReview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            document.getElementById('offering_type').addEventListener('change', function () {
                document.getElementById('offering_class').value = (this.value === 'Professional Service') ? 'service' : 'product';
            });

            // Organization -> Dependent Warehouse / Origin Address Filter
            const ownerClientSelect = document.getElementById('owner_client_id');
            const addressSelect = document.getElementById('address_id');
            const allAddressOptions = Array.from(addressSelect.querySelectorAll('option[data-client-id]')).map(function (opt) {
                return opt.cloneNode(true);
            });

            function syncAddressesForOwner(clientId, preserveCurrent) {
                const currentVal = preserveCurrent ? addressSelect.value : '';
                addressSelect.innerHTML = '<option value="">Select Address (Optional for Digital)</option>';
                const matching = allAddressOptions.filter(function (opt) {
                    return String(opt.getAttribute('data-client-id')) === String(clientId);
                });
                matching.forEach(function (opt) {
                    const cloned = opt.cloneNode(true);
                    if (currentVal && String(cloned.value) === String(currentVal)) {
                        cloned.selected = true;
                    }
                    addressSelect.appendChild(cloned);
                });
                if (matching.length > 0 && (!preserveCurrent || !currentVal)) {
                    addressSelect.value = matching[0].value;
                }
            }

            if (ownerClientSelect) {
                ownerClientSelect.addEventListener('change', function () {
                    syncAddressesForOwner(this.value, false);
                });
                syncAddressesForOwner(ownerClientSelect.value, true);
            }

            function bindChoiceCards(containerId, hiddenId, onChange) {
                const container = document.getElementById(containerId);
                const hidden = document.getElementById(hiddenId);
                container.querySelectorAll('.ij-choice-card').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        container.querySelectorAll('.ij-choice-card').forEach(function (b) { b.classList.remove('is-selected'); });
                        btn.classList.add('is-selected');
                        hidden.value = btn.getAttribute('data-val');
                        if (onChange) onChange(hidden.value);
                    });
                });
            }

            const freqSelect = document.getElementById('pricing_frequency');
            bindChoiceCards('deliveryModelSelector', 'delivery_model');
            bindChoiceCards('availabilitySelector', 'service_availability');
            bindChoiceCards('pricingModelSelector', 'pricing_model', function (val) {
                if (val === 'Fixed Price') { freqSelect.value = 'One-Time'; freqSelect.disabled = true; }
                else { freqSelect.disabled = false; }
            });
            freqSelect.disabled = true;

            document.getElementById('currency_code').addEventListener('change', function () {
                document.getElementById('maxCurrMirror').textContent = this.value;
            });

            document.getElementById('is_unlimited_capacity').addEventListener('change', function () {
                document.getElementById('available_capacity').disabled = this.checked;
            });

            document.getElementById('pricing_visibility').addEventListener('change', function () {
                const isConf = this.value === 'confidential';
                document.querySelectorAll('.pricing-input-row').forEach(function (el) { el.style.display = isConf ? 'none' : 'flex'; });
            });

            // Locations / Popular Hubs + Live City Autocomplete (1:1 Screenshot Match)
            const locations = [];
            const locBox = document.getElementById('locationsBox');
            const locInput = document.getElementById('locationInput');
            const locHidden = document.getElementById('locations_json');
            const cityDropdown = document.getElementById('citySearchDropdown');
            const cityClearBtn = document.getElementById('btnCitySearchClear');
            const CITY_SEARCH_URL = @json(url('/city-job-suggestions'));
            let citySearchTimer = null;

            function syncHubPillsState() {
                document.querySelectorAll('#popularHubsPills .ij-hub-pill').forEach(function (btn) {
                    const label = btn.getAttribute('data-label');
                    const canonical = btn.getAttribute('data-val');
                    const isSel = locations.some(function (l) {
                        return l.toLowerCase() === canonical.toLowerCase() || l.toLowerCase().indexOf(label.toLowerCase()) === 0;
                    });
                    btn.classList.toggle('is-selected', isSel);
                    btn.textContent = (isSel ? '✓ ' : '+ ') + label;
                });
            }

            function renderLocations() {
                locBox.innerHTML = '';
                locations.forEach(function (v, i) {
                    const chip = document.createElement('span');
                    chip.className = 'ij-city-chip';
                    const parenIdx = v.indexOf('(');
                    if (parenIdx > 0) {
                        const mainPart = v.slice(0, parenIdx).trim();
                        const subPart = v.slice(parenIdx).trim();
                        chip.innerHTML = '<strong></strong><span></span><button type="button" title="Remove">&times;</button>';
                        chip.querySelector('strong').textContent = mainPart;
                        chip.querySelector('span').textContent = subPart;
                    } else {
                        chip.innerHTML = '<strong></strong><button type="button" title="Remove">&times;</button>';
                        chip.querySelector('strong').textContent = v;
                    }
                    chip.querySelector('button').addEventListener('click', function () {
                        locations.splice(i, 1);
                        renderLocations();
                    });
                    locBox.appendChild(chip);
                });
                locHidden.value = JSON.stringify(locations);
                document.getElementById('selectedLocationsCount').textContent = 'Selected Coverage Areas (' + locations.length + '/15)';
                syncHubPillsState();
            }

            function addLocation(val) {
                const v = (val || '').trim();
                if (!v || locations.length >= 15) return;
                if (locations.indexOf(v) === -1) {
                    locations.push(v);
                    renderLocations();
                }
            }

            function toggleHubLocation(label, canonical) {
                const idx = locations.findIndex(function (l) {
                    return l.toLowerCase() === canonical.toLowerCase() || l.toLowerCase().indexOf(label.toLowerCase()) === 0;
                });
                if (idx !== -1) {
                    locations.splice(idx, 1);
                    renderLocations();
                } else {
                    addLocation(canonical);
                }
            }

            document.querySelectorAll('#popularHubsPills .ij-hub-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    toggleHubLocation(btn.getAttribute('data-label'), btn.getAttribute('data-val'));
                });
            });

            function formatCityResult(item) {
                if (typeof item === 'string') return item;
                if (item.canonical) return item.canonical;
                const name = item.city || item.name || item.label || '';
                const state = item.state || '';
                const country = item.country || '';
                const suffix = [state, country].filter(Boolean).join(', ');
                return suffix ? (name + ' (' + suffix + ')') : name;
            }

            function runCityAutocomplete(query) {
                const q = query.trim();
                if (q.length < 2) {
                    cityDropdown.style.display = 'none';
                    return;
                }
                fetch(CITY_SEARCH_URL + '?q=' + encodeURIComponent(q) + '&format=canonical&limit=20', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    const list = Array.isArray(data) ? data : (data.data || data.results || data.cities || []);
                    cityDropdown.innerHTML = '';
                    if (!list.length) {
                        cityDropdown.style.display = 'none';
                        return;
                    }
                    list.slice(0, 15).forEach(function (item) {
                        const label = formatCityResult(item);
                        if (!label) return;
                        const opt = document.createElement('div');
                        opt.className = 'ij-city-option';
                        opt.textContent = label;
                        opt.addEventListener('click', function () {
                            addLocation(label);
                            locInput.value = '';
                            cityClearBtn.style.display = 'none';
                            cityDropdown.style.display = 'none';
                        });
                        cityDropdown.appendChild(opt);
                    });
                    cityDropdown.style.display = cityDropdown.children.length ? 'block' : 'none';
                })
                .catch(function () { cityDropdown.style.display = 'none'; });
            }

            locInput.addEventListener('input', function () {
                cityClearBtn.style.display = this.value ? 'flex' : 'none';
                clearTimeout(citySearchTimer);
                const val = this.value;
                citySearchTimer = setTimeout(function () { runCityAutocomplete(val); }, 180);
            });
            locInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addLocation(locInput.value.replace(/,/g, ''));
                    locInput.value = '';
                    cityClearBtn.style.display = 'none';
                    cityDropdown.style.display = 'none';
                }
            });
            cityClearBtn.addEventListener('click', function () {
                locInput.value = '';
                cityClearBtn.style.display = 'none';
                cityDropdown.style.display = 'none';
                locInput.focus();
            });
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.ij-city-search-wrap')) cityDropdown.style.display = 'none';
            });
            document.getElementById('btnClearLocations').addEventListener('click', function () { locations.length = 0; renderLocations(); });

            // Tags / Features (1:1 Screenshot Match: 0/10 added + Clear All + Recommended Pills)
            const tags = [];
            const tagBox = document.getElementById('featureTagsBox');
            const tagInput = document.getElementById('featureTagInput');
            const tagHidden = document.getElementById('feature_tags_json');
            const tagCountBadge = document.getElementById('featureTagsCountBadge');
            const clearTagsBtn = document.getElementById('btnClearFeatureTags');

            function renderTags() {
                tagBox.innerHTML = '';
                if (!tags.length) {
                    tagBox.innerHTML = '<span style="font-size:11.5px; color:#94a3b8;">No features added yet.</span>';
                } else {
                    tags.forEach(function (v, i) {
                        const chip = document.createElement('span');
                        chip.className = 'ij-tag-chip';
                        chip.innerHTML = '<span></span><button type="button" title="Remove">&times;</button>';
                        chip.querySelector('span').textContent = v;
                        chip.querySelector('button').addEventListener('click', function () { tags.splice(i, 1); renderTags(); });
                        tagBox.appendChild(chip);
                    });
                }
                tagHidden.value = JSON.stringify(tags);
                tagCountBadge.textContent = tags.length + '/10 added';
                clearTagsBtn.style.display = tags.length ? 'inline-block' : 'none';
            }

            function addFeatureTag(val) {
                const v = (val || '').trim();
                if (v && tags.length < 10 && tags.indexOf(v) === -1) {
                    tags.push(v);
                    renderTags();
                }
            }

            tagInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addFeatureTag(tagInput.value.replace(/,/g, ''));
                    tagInput.value = '';
                }
            });
            clearTagsBtn.addEventListener('click', function () { tags.length = 0; renderTags(); });
            document.querySelectorAll('#recommendedFeaturesList .ij-rec-pill').forEach(function (btn) {
                btn.addEventListener('click', function () { addFeatureTag(btn.getAttribute('data-val')); });
            });

            // Rich Text Editor + Live Character Count
            const descEditor = document.getElementById('crmDescriptionEditor');
            const descHidden = document.getElementById('crm_description');
            const descCharCount = document.getElementById('descCharCount');
            document.querySelectorAll('#step2 .toolbar-btn').forEach(function (btn) {
                btn.addEventListener('click', function () { document.execCommand(btn.getAttribute('data-cmd'), false, null); descEditor.focus(); });
            });
            descEditor.addEventListener('input', function () {
                const len = (descEditor.textContent || '').trim().length;
                descCharCount.textContent = len + ' characters';
            });

            // Qualification Questions (1:1 Screenshot Match: Cards + Edit/Delete + Popular Templates)
            const questions = [];
            let editingQuestionIdx = -1;
            const qContainer = document.getElementById('questionsContainer');
            const qHidden = document.getElementById('qualification_questions_json');
            const qComposer = document.getElementById('questionComposer');
            const qInput = document.getElementById('customQuestionInput');
            const qType = document.getElementById('customQuestionType');
            const qReq = document.getElementById('customQuestionRequired');
            const qTypeLabels = { short: 'A Short Text', paragraph: 'Paragraph', yesno: 'Yes / No', number: 'Number', date: 'Date' };

            function renderQuestions() {
                qContainer.innerHTML = '';
                questions.forEach(function (q, idx) {
                    const card = document.createElement('div');
                    card.className = 'ij-q-item';
                    card.innerHTML =
                        '<div class="ij-q-left">' +
                            '<span class="ij-q-grip"><i class="fas fa-grip-vertical"></i></span>' +
                            '<div>' +
                                '<div class="ij-q-title"></div>' +
                                '<div class="ij-q-badges">' +
                                    '<span class="ij-q-type-pill"></span>' +
                                    '<span class="' + (q.is_required ? 'ij-q-req-pill' : 'ij-q-opt-pill') + '">' + (q.is_required ? '• Required' : 'Optional') + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="ij-q-actions">' +
                            '<button type="button" class="ij-q-btn-edit"><i class="fas fa-pen me-1"></i> Edit</button>' +
                            '<button type="button" class="ij-q-btn-del" title="Delete"><i class="fas fa-trash"></i></button>' +
                        '</div>';
                    card.querySelector('.ij-q-title').textContent = q.question;
                    card.querySelector('.ij-q-type-pill').textContent = qTypeLabels[q.answer_type] || 'Short Text';
                    card.querySelector('.ij-q-btn-edit').addEventListener('click', function () {
                        editingQuestionIdx = idx;
                        qInput.value = q.question;
                        qType.value = q.answer_type || 'short';
                        qReq.checked = !!q.is_required;
                        qComposer.classList.remove('d-none');
                        qInput.focus();
                    });
                    card.querySelector('.ij-q-btn-del').addEventListener('click', function () {
                        questions.splice(idx, 1);
                        renderQuestions();
                    });
                    qContainer.appendChild(card);
                });
                qHidden.value = JSON.stringify(questions);
            }

            document.getElementById('addQuestionDropzone').addEventListener('click', function () {
                editingQuestionIdx = -1;
                qInput.value = '';
                qType.value = 'short';
                qReq.checked = true;
                qComposer.classList.remove('d-none');
                qInput.focus();
            });
            document.getElementById('btnCancelQuestion').addEventListener('click', function () {
                qComposer.classList.add('d-none');
                editingQuestionIdx = -1;
            });
            document.getElementById('btnAddQuestion').addEventListener('click', function () {
                const txt = qInput.value.trim();
                if (!txt) return;
                const item = { question: txt, answer_type: qType.value, is_required: qReq.checked };
                if (editingQuestionIdx >= 0) questions[editingQuestionIdx] = item;
                else questions.push(item);
                editingQuestionIdx = -1;
                qComposer.classList.add('d-none');
                renderQuestions();
            });
            document.querySelectorAll('#discoveryTemplates .ij-tpl-row').forEach(function (row) {
                row.addEventListener('click', function () {
                    const qText = row.getAttribute('data-q');
                    if (!questions.some(function (q) { return q.question === qText; })) {
                        questions.push({ question: qText, answer_type: row.getAttribute('data-type') || 'short', is_required: true });
                        renderQuestions();
                    }
                });
            });
            document.getElementById('btnAutoGenerateQ').addEventListener('click', function () {
                [
                    { question: 'What is your target budget range?', answer_type: 'short', is_required: true },
                    { question: 'What is your expected go-live date?', answer_type: 'date', is_required: true },
                    { question: 'Do you require a custom NDA or compliance review?', answer_type: 'yesno', is_required: false }
                ].forEach(function (item) {
                    if (!questions.some(function (q) { return q.question === item.question; })) questions.push(item);
                });
                renderQuestions();
            });

            // SKU Uniqueness Check
            const skuInput = document.getElementById('sku');
            skuInput.addEventListener('blur', function () {
                const v = skuInput.value.trim().toUpperCase();
                if (!v) return;
                fetch(CHECK_SKU_URL + '?sku=' + encodeURIComponent(v) + '&exclude_id=' + EDIT_ID, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        const grp = document.querySelector('[data-field="sku"]');
                        if (res.available === false) grp.classList.add('has-error');
                        else grp.classList.remove('has-error');
                    });
            });

            // Service Team Engine (Account Owner + Service Delivery Team, Client-First Roster)
            const OWNER_CONTACTS = @json($clientContacts ?? []);
            const DELIVERY_ROSTER = @json($teamRoster ?? []);
            const AVATAR_PALETTE = ['#0066ff', '#059669', '#0284c7', '#7c3aed', '#dc2626', '#d97706'];
            let selectedOwners = [];
            let selectedDeliveryTeam = [];

            const ownerSearchInput = document.getElementById('accountOwnerSearchInput');
            const ownerDropdown = document.getElementById('accountOwnerDropdown');
            const ownerCardList = document.getElementById('accountOwnerCardList');
            const teamSearchInput = document.getElementById('deliveryTeamSearchInput');
            const teamDropdown = document.getElementById('deliveryTeamDropdown');
            const teamCardList = document.getElementById('deliveryTeamCardList');

            function getInitials(name) {
                const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
                if (!parts.length) return '?';
                return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
            }

            function avatarColor(idx) {
                return AVATAR_PALETTE[idx % AVATAR_PALETTE.length];
            }

            function syncTeamHiddenInputs() {
                document.getElementById('hiring_namager').value = selectedOwners.map(function (o) { return o.name; }).join(',');
                document.getElementById('hiring_manager_id').value = selectedOwners.map(function (o) { return o.id; }).join(',');
                document.getElementById('recruiter_names_hidden').value = selectedDeliveryTeam.map(function (m) { return m.name; }).join(',');
                document.getElementById('recruiter_ids_hidden').value = selectedDeliveryTeam.map(function (m) { return m.id; }).join(',');

                const payload = [];
                selectedOwners.forEach(function (o) {
                    payload.push({ user_id: null, name: o.name, role_type: 'owner', is_lead: false });
                });
                selectedDeliveryTeam.forEach(function (m, idx) {
                    payload.push({ user_id: m.id, name: m.name, role_type: 'collaborator', is_lead: !!(m.is_lead || idx === 0) });
                });
                document.getElementById('team_members_hidden').value = JSON.stringify(payload);
            }

            function renderTeamCards() {
                ownerCardList.innerHTML = '';
                selectedOwners.forEach(function (o, idx) {
                    const card = document.createElement('div');
                    card.className = 'ij-team-card';
                    card.innerHTML =
                        '<div class="ij-team-card-left">' +
                            '<div class="ij-team-avatar" style="background:' + avatarColor(idx) + ';"></div>' +
                            '<div><div class="ij-team-name"></div><div class="ij-team-role"></div></div>' +
                        '</div>' +
                        '<button type="button" class="ij-team-remove" title="Remove">&times;</button>';
                    card.querySelector('.ij-team-avatar').textContent = getInitials(o.name);
                    card.querySelector('.ij-team-name').textContent = o.name;
                    card.querySelector('.ij-team-role').textContent = o.designation || 'Account Owner';
                    card.querySelector('.ij-team-remove').addEventListener('click', function () {
                        selectedOwners.splice(idx, 1);
                        renderTeamCards();
                    });
                    ownerCardList.appendChild(card);
                });

                teamCardList.innerHTML = '';
                selectedDeliveryTeam.forEach(function (m, idx) {
                    const isLead = !!(m.is_lead || idx === 0);
                    const card = document.createElement('div');
                    card.className = 'ij-team-card';
                    card.innerHTML =
                        '<div class="ij-team-card-left">' +
                            '<div class="ij-team-avatar" style="background:' + avatarColor(idx + 1) + ';"></div>' +
                            '<div><div class="ij-team-name"><span></span>' + (isLead ? '<span class="ij-lead-badge"><i class="fas fa-star" style="font-size:9px;"></i> Lead</span>' : '') + '</div><div class="ij-team-role"></div></div>' +
                        '</div>' +
                        '<button type="button" class="ij-team-remove" title="Remove">&times;</button>';
                    card.querySelector('.ij-team-avatar').textContent = getInitials(m.name);
                    card.querySelector('.ij-team-name span').textContent = m.name;
                    card.querySelector('.ij-team-role').textContent = isLead ? ('Lead • ' + (m.designation || 'Service Delivery')) : (m.designation || 'Team Member');
                    card.querySelector('.ij-team-remove').addEventListener('click', function () {
                        selectedDeliveryTeam.splice(idx, 1);
                        if (selectedDeliveryTeam.length && !selectedDeliveryTeam.some(function (x) { return x.is_lead; })) {
                            selectedDeliveryTeam[0].is_lead = true;
                        }
                        renderTeamCards();
                    });
                    teamCardList.appendChild(card);
                });

                syncTeamHiddenInputs();
            }

            function showOwnerDropdown(query) {
                const q = (query || '').trim().toLowerCase();
                const ownerClientId = String(document.getElementById('owner_client_id').value || '');
                let list = OWNER_CONTACTS.filter(function (c) {
                    return !selectedOwners.some(function (s) { return String(s.id) === String(c.id); });
                });
                const clientFiltered = ownerClientId ? list.filter(function (c) { return String(c.client_id) === ownerClientId; }) : list;
                if (clientFiltered.length) list = clientFiltered;
                if (q) {
                    list = list.filter(function (c) {
                        return (c.name || '').toLowerCase().indexOf(q) !== -1 || (c.designation || '').toLowerCase().indexOf(q) !== -1;
                    });
                }
                ownerDropdown.innerHTML = '';
                if (!list.length) {
                    ownerDropdown.innerHTML = '<div class="ij-team-dd-item" style="color:#94a3b8;cursor:default;">No matching contacts found</div>';
                } else {
                    list.slice(0, 20).forEach(function (c) {
                        const row = document.createElement('div');
                        row.className = 'ij-team-dd-item';
                        row.innerHTML = '<div><strong style="color:#10143b;"></strong> <span style="color:#64748b;font-size:11.5px;"></span></div><span style="color:#d97706;font-size:11px;font-weight:600;">+ Add</span>';
                        row.querySelector('strong').textContent = c.name || 'Contact';
                        row.querySelector('span').textContent = c.designation ? ('(' + c.designation + ')') : '';
                        row.addEventListener('click', function () {
                            selectedOwners.push({ id: c.id, name: c.name, designation: c.designation || 'Account Owner' });
                            ownerSearchInput.value = '';
                            ownerDropdown.style.display = 'none';
                            renderTeamCards();
                        });
                        ownerDropdown.appendChild(row);
                    });
                }
                ownerDropdown.style.display = 'block';
            }

            function showTeamDropdown(query) {
                const q = (query || '').trim().toLowerCase();
                let list = DELIVERY_ROSTER.filter(function (m) {
                    return !selectedDeliveryTeam.some(function (s) { return String(s.id) === String(m.id); });
                });
                if (q) {
                    list = list.filter(function (m) {
                        return (m.name || '').toLowerCase().indexOf(q) !== -1 || (m.designation || '').toLowerCase().indexOf(q) !== -1;
                    });
                }
                teamDropdown.innerHTML = '';
                if (!list.length) {
                    teamDropdown.innerHTML = '<div class="ij-team-dd-item" style="color:#94a3b8;cursor:default;">No matching team members</div>';
                } else {
                    list.slice(0, 25).forEach(function (m) {
                        const row = document.createElement('div');
                        row.className = 'ij-team-dd-item';
                        row.innerHTML = '<div><strong style="color:#10143b;"></strong> <span style="color:#64748b;font-size:11.5px;"></span></div><span style="color:#d97706;font-size:11px;font-weight:600;">+ Add</span>';
                        row.querySelector('strong').textContent = m.name || 'Member';
                        row.querySelector('span').textContent = m.designation ? ('(' + m.designation + ')') : '';
                        row.addEventListener('click', function () {
                            selectedDeliveryTeam.push({
                                id: m.id,
                                name: m.name,
                                designation: m.designation || 'Team Member',
                                is_lead: selectedDeliveryTeam.length === 0
                            });
                            teamSearchInput.value = '';
                            teamDropdown.style.display = 'none';
                            renderTeamCards();
                        });
                        teamDropdown.appendChild(row);
                    });
                }
                teamDropdown.style.display = 'block';
            }

            const drawerBackdrop = document.getElementById('crmDrawerBackdrop');
            const ownerDrawer = document.getElementById('crmOwnerDrawer');
            const teamDrawer = document.getElementById('crmTeamDrawer');
            const ownerDrawerSearch = document.getElementById('crmOwnerDrawerSearch');
            const teamDrawerSearch = document.getElementById('crmTeamDrawerSearch');
            let teamLoadFilter = 'all';

            function closeCrmTeamDrawers() {
                drawerBackdrop.classList.remove('is-open');
                ownerDrawer.classList.remove('is-open');
                teamDrawer.classList.remove('is-open');
            }

            function renderOwnerDrawer() {
                const ownerSel = document.getElementById('owner_client_id');
                const orgLabel = (ownerSel && ownerSel.selectedIndex >= 0) ? ownerSel.options[ownerSel.selectedIndex].textContent.trim() : 'Organization';
                document.getElementById('crmOwnerDrawerOrg').textContent = orgLabel.replace(/\s*\(My Organization\)\s*/i, '');

                document.getElementById('crmOwnerAssignedCount').textContent = 'ASSIGNED ACCOUNT OWNERS (' + selectedOwners.length + ')';
                const assignedGrid = document.getElementById('crmOwnerDrawerAssignedGrid');
                assignedGrid.innerHTML = '';
                selectedOwners.forEach(function (o, idx) {
                    const chip = document.createElement('div');
                    chip.className = 'ij-drawer-chip';
                    chip.innerHTML =
                        '<div style="display:flex;align-items:center;gap:9px;min-width:0;flex:1;">' +
                            '<div class="ij-team-avatar" style="width:32px;height:32px;font-size:12px;background:' + avatarColor(idx) + ';"></div>' +
                            '<div style="min-width:0;flex:1;">' +
                                '<div class="ij-d-name" style="font-size:12.5px;font-weight:600;color:#10143b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>' +
                                '<div class="ij-d-role" style="font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="ij-team-remove" title="Remove">&times;</button>';
                    chip.querySelector('.ij-team-avatar').textContent = getInitials(o.name);
                    chip.querySelector('.ij-d-name').textContent = o.name;
                    chip.querySelector('.ij-d-role').textContent = o.designation || 'Account Owner';
                    chip.querySelector('.ij-team-remove').addEventListener('click', function () {
                        selectedOwners.splice(idx, 1);
                        renderTeamCards();
                        renderOwnerDrawer();
                    });
                    assignedGrid.appendChild(chip);
                });

                const q = (ownerDrawerSearch.value || '').trim().toLowerCase();
                const ownerClientId = String(ownerSel ? ownerSel.value : '');
                let list = OWNER_CONTACTS.slice();
                const clientFiltered = ownerClientId ? list.filter(function (c) { return String(c.client_id) === ownerClientId; }) : list;
                if (clientFiltered.length) list = clientFiltered;
                if (q) {
                    list = list.filter(function (c) {
                        return (c.name || '').toLowerCase().indexOf(q) !== -1
                            || (c.designation || '').toLowerCase().indexOf(q) !== -1
                            || (c.email || '').toLowerCase().indexOf(q) !== -1;
                    });
                }

                document.getElementById('crmOwnerAvailCount').textContent = 'ORGANIZATION CONTACTS (' + list.length + ')';
                const listEl = document.getElementById('crmOwnerDrawerList');
                listEl.innerHTML = '';
                if (!list.length) {
                    listEl.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No contacts found for this organization.</div>';
                    return;
                }
                list.forEach(function (c, idx) {
                    const isAssigned = selectedOwners.some(function (s) { return String(s.id) === String(c.id); });
                    const row = document.createElement('div');
                    row.className = 'ij-drawer-row' + (isAssigned ? ' is-assigned' : '');
                    row.innerHTML =
                        '<div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">' +
                            '<div class="ij-team-avatar" style="width:38px;height:38px;font-size:13px;background:' + avatarColor(idx) + ';"></div>' +
                            '<div style="min-width:0;">' +
                                '<div class="ij-d-name" style="font-size:13px;font-weight:600;color:#10143b;"></div>' +
                                '<div class="ij-d-role" style="font-size:11.5px;color:#64748b;"></div>' +
                                '<div class="ij-d-meta" style="font-size:11px;color:#64748b;margin-top:2px;"></div>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="ij-drawer-assign-btn' + (isAssigned ? ' is-assigned' : '') + '">' + (isAssigned ? '&#10003; Assigned' : '+ Assign') + '</button>';
                    row.querySelector('.ij-team-avatar').textContent = getInitials(c.name);
                    row.querySelector('.ij-d-name').textContent = c.name || 'Contact';
                    row.querySelector('.ij-d-role').textContent = c.designation || 'Account Owner';
                    row.querySelector('.ij-d-meta').textContent = [c.email, c.phone].filter(Boolean).join('  •  ');
                    row.querySelector('.ij-drawer-assign-btn').addEventListener('click', function () {
                        if (isAssigned) {
                            selectedOwners = selectedOwners.filter(function (s) { return String(s.id) !== String(c.id); });
                        } else {
                            selectedOwners.push({ id: c.id, name: c.name, designation: c.designation || 'Account Owner' });
                        }
                        renderTeamCards();
                        renderOwnerDrawer();
                    });
                    listEl.appendChild(row);
                });
            }

            function renderTeamDrawer() {
                document.getElementById('crmTeamAssignedCount').textContent = 'ASSIGNED TEAM (' + selectedDeliveryTeam.length + ')';
                const assignedGrid = document.getElementById('crmTeamDrawerAssignedGrid');
                assignedGrid.innerHTML = '';
                selectedDeliveryTeam.forEach(function (m, idx) {
                    const isLead = !!(m.is_lead || idx === 0);
                    const chip = document.createElement('div');
                    chip.className = 'ij-drawer-chip' + (isLead ? ' is-lead' : '');
                    chip.innerHTML =
                        '<div style="display:flex;align-items:center;gap:9px;min-width:0;flex:1;">' +
                            '<div class="ij-team-avatar" style="width:32px;height:32px;font-size:12px;background:' + avatarColor(idx + 1) + ';"></div>' +
                            '<div style="min-width:0;flex:1;">' +
                                '<div class="ij-d-name" style="font-size:12.5px;font-weight:600;color:#10143b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>' +
                                '<div class="ij-d-role" style="font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">' +
                            (isLead ? '<span class="ij-lead-badge"><i class="fas fa-star" style="font-size:9px;"></i> Lead</span>' : '<button type="button" class="ij-drawer-chip-btn"><i class="far fa-star" style="font-size:9px;margin-right:3px;"></i>Make Lead</button>') +
                            '<button type="button" class="ij-team-remove" title="Remove">&times;</button>' +
                        '</div>';
                    chip.querySelector('.ij-team-avatar').textContent = getInitials(m.name);
                    chip.querySelector('.ij-d-name').textContent = m.name;
                    chip.querySelector('.ij-d-role').textContent = m.designation || 'Team Member';
                    const makeLeadBtn = chip.querySelector('.ij-drawer-chip-btn');
                    if (makeLeadBtn) {
                        makeLeadBtn.addEventListener('click', function () {
                            selectedDeliveryTeam.forEach(function (x, i) { x.is_lead = (i === idx); });
                            renderTeamCards();
                            renderTeamDrawer();
                        });
                    }
                    chip.querySelector('.ij-team-remove').addEventListener('click', function () {
                        selectedDeliveryTeam.splice(idx, 1);
                        if (selectedDeliveryTeam.length && !selectedDeliveryTeam.some(function (x) { return x.is_lead; })) {
                            selectedDeliveryTeam[0].is_lead = true;
                        }
                        renderTeamCards();
                        renderTeamDrawer();
                    });
                    assignedGrid.appendChild(chip);
                });

                const allBtn = document.querySelector('#crmTeamFilterPills [data-filter="all"]');
                if (allBtn) allBtn.textContent = 'All (' + DELIVERY_ROSTER.length + ')';

                const q = (teamDrawerSearch.value || '').trim().toLowerCase();
                let list = DELIVERY_ROSTER.slice();
                if (teamLoadFilter === 'available') {
                    list = list.filter(function (m) { return Number(m.active_jobs || 0) < 3; });
                } else if (teamLoadFilter === 'high') {
                    list = list.filter(function (m) { return Number(m.active_jobs || 0) >= 3; });
                }
                if (q) {
                    list = list.filter(function (m) {
                        return (m.name || '').toLowerCase().indexOf(q) !== -1 || (m.designation || '').toLowerCase().indexOf(q) !== -1;
                    });
                }

                document.getElementById('crmTeamAvailCount').textContent = 'AVAILABLE TEAM MEMBERS (' + list.length + ')';
                const listEl = document.getElementById('crmTeamDrawerList');
                listEl.innerHTML = '';
                if (!list.length) {
                    listEl.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No matching team members found.</div>';
                    return;
                }
                list.forEach(function (m, idx) {
                    const isAssigned = selectedDeliveryTeam.some(function (s) { return String(s.id) === String(m.id); });
                    const activeCount = Number(m.active_jobs || 0);
                    const row = document.createElement('div');
                    row.className = 'ij-drawer-row' + (isAssigned ? ' is-assigned' : '');
                    row.innerHTML =
                        '<div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">' +
                            '<div class="ij-team-avatar" style="width:38px;height:38px;font-size:13px;background:' + avatarColor(idx + 1) + ';"></div>' +
                            '<div style="min-width:0;"><div class="ij-d-name" style="font-size:13px;font-weight:600;color:#10143b;"></div><div class="ij-d-role" style="font-size:11.5px;color:#64748b;"></div></div>' +
                        '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">' +
                            '<span style="font-size:11px;font-weight:600;padding:3px 9px;border-radius:6px;background:#ecfdf5;color:#059669;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-briefcase" style="font-size:10px;"></i>' + activeCount + ' active</span>' +
                            '<button type="button" class="ij-drawer-assign-btn' + (isAssigned ? ' is-assigned' : '') + '">' + (isAssigned ? '&#10003; Assigned' : '+ Assign') + '</button>' +
                        '</div>';
                    row.querySelector('.ij-team-avatar').textContent = getInitials(m.name);
                    row.querySelector('.ij-d-name').textContent = m.name || 'Member';
                    row.querySelector('.ij-d-role').textContent = m.designation || 'Team Member';
                    row.querySelector('.ij-drawer-assign-btn').addEventListener('click', function () {
                        if (isAssigned) {
                            selectedDeliveryTeam = selectedDeliveryTeam.filter(function (s) { return String(s.id) !== String(m.id); });
                            if (selectedDeliveryTeam.length && !selectedDeliveryTeam.some(function (x) { return x.is_lead; })) {
                                selectedDeliveryTeam[0].is_lead = true;
                            }
                        } else {
                            selectedDeliveryTeam.push({
                                id: m.id,
                                name: m.name,
                                designation: m.designation || 'Team Member',
                                is_lead: selectedDeliveryTeam.length === 0
                            });
                        }
                        renderTeamCards();
                        renderTeamDrawer();
                    });
                    listEl.appendChild(row);
                });
            }

            function openOwnerDrawer() {
                closeCrmTeamDrawers();
                renderOwnerDrawer();
                drawerBackdrop.classList.add('is-open');
                ownerDrawer.classList.add('is-open');
                ownerDrawerSearch.focus();
            }

            function openTeamDrawer() {
                closeCrmTeamDrawers();
                renderTeamDrawer();
                drawerBackdrop.classList.add('is-open');
                teamDrawer.classList.add('is-open');
                teamDrawerSearch.focus();
            }

            ownerSearchInput.addEventListener('click', openOwnerDrawer);
            document.getElementById('btnShowOwnerDropdown').addEventListener('click', openOwnerDrawer);
            teamSearchInput.addEventListener('click', openTeamDrawer);
            document.getElementById('btnShowTeamDropdown').addEventListener('click', openTeamDrawer);

            document.getElementById('btnCloseOwnerDrawer').addEventListener('click', closeCrmTeamDrawers);
            document.getElementById('btnCloseTeamDrawer').addEventListener('click', closeCrmTeamDrawers);
            drawerBackdrop.addEventListener('click', closeCrmTeamDrawers);

            ownerDrawerSearch.addEventListener('input', renderOwnerDrawer);
            teamDrawerSearch.addEventListener('input', renderTeamDrawer);
            document.querySelectorAll('#crmTeamFilterPills .ij-drawer-filter-pill').forEach(function (pill) {
                pill.addEventListener('click', function () {
                    document.querySelectorAll('#crmTeamFilterPills .ij-drawer-filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
                    this.classList.add('is-active');
                    teamLoadFilter = this.getAttribute('data-filter') || 'all';
                    renderTeamDrawer();
                });
            });

            function hydrateServiceTeam(ownersArr, teamArr, crmTeamArr) {
                selectedOwners = [];
                selectedDeliveryTeam = [];
                if (Array.isArray(ownersArr) && ownersArr.length) {
                    ownersArr.forEach(function (o) {
                        if (o.name) selectedOwners.push({ id: o.id || '', name: o.name, designation: o.designation || 'Account Owner' });
                    });
                } else if (Array.isArray(crmTeamArr)) {
                    crmTeamArr.filter(function (t) { return t.role_type === 'owner'; }).forEach(function (t) {
                        selectedOwners.push({ id: t.user_id || '', name: t.member_name, designation: 'Account Owner' });
                    });
                }
                if (Array.isArray(teamArr) && teamArr.length) {
                    teamArr.forEach(function (m, idx) {
                        const rosterMatch = DELIVERY_ROSTER.find(function (r) { return String(r.id) === String(m.id); });
                        selectedDeliveryTeam.push({
                            id: m.id,
                            name: m.name,
                            designation: (rosterMatch && rosterMatch.designation) ? rosterMatch.designation : 'Team Member',
                            is_lead: !!(m.is_lead || idx === 0)
                        });
                    });
                } else if (Array.isArray(crmTeamArr)) {
                    crmTeamArr.filter(function (t) { return t.role_type !== 'owner'; }).forEach(function (t, idx) {
                        selectedDeliveryTeam.push({
                            id: t.user_id || '',
                            name: t.member_name,
                            designation: 'Team Member',
                            is_lead: !!(t.is_lead || idx === 0)
                        });
                    });
                }
                renderTeamCards();
            }

            @if($isEdit)
                (function hydrateEditOffering() {
                    const o = @json($offering);
                    if (o.offering_class) document.getElementById('offering_class').value = o.offering_class;
                    if (o.product_url !== null && o.product_url !== undefined) document.getElementById('product_url').value = o.product_url;
                    const catSel = document.getElementById('crm_category');
                    if (o.crm_category) {
                        let found = Array.from(catSel.options).some(function (opt) { return opt.value === o.crm_category; });
                        if (!found) {
                            const opt = document.createElement('option');
                            opt.value = o.crm_category;
                            opt.textContent = o.crm_category;
                            catSel.appendChild(opt);
                        }
                        catSel.value = o.crm_category;
                    }
                    if (o.offering_type) document.getElementById('offering_type').value = o.offering_type;
                    if (o.owner_client_id) {
                        document.getElementById('owner_client_id').value = o.owner_client_id;
                        if (typeof syncAddressesForOwner === 'function') syncAddressesForOwner(o.owner_client_id, true);
                    }
                    if (o.address_id) document.getElementById('address_id').value = o.address_id;
                    if (o.unit) document.getElementById('unit').value = o.unit;
                    if (o.available_capacity !== null && o.available_capacity !== undefined) document.getElementById('available_capacity').value = o.available_capacity;
                    if (o.is_unlimited_capacity) {
                        document.getElementById('is_unlimited_capacity').checked = true;
                        document.getElementById('available_capacity').disabled = true;
                    }
                    if (o.status) document.getElementById('offering_status').value = o.status;
                    if (o.pricing_visibility) {
                        document.getElementById('pricing_visibility').value = o.pricing_visibility;
                        if (o.pricing_visibility === 'confidential') {
                            document.querySelectorAll('.pricing-input-row').forEach(function (el) { el.style.display = 'none'; });
                        }
                    }
                    if (o.min_price !== null && o.min_price !== undefined) document.getElementById('min_price').value = o.min_price;
                    if (o.max_price !== null && o.max_price !== undefined) document.getElementById('max_price').value = o.max_price;
                    if (o.currency_code) {
                        document.getElementById('currency_code').value = o.currency_code;
                        document.getElementById('maxCurrMirror').textContent = o.currency_code;
                    }
                    if (o.pricing_model) {
                        document.getElementById('pricing_model').value = o.pricing_model;
                        document.querySelectorAll('#pricingModelSelector .ij-choice-card').forEach(function (b) {
                            b.classList.toggle('is-selected', b.getAttribute('data-val') === o.pricing_model);
                        });
                        freqSelect.disabled = (o.pricing_model === 'Fixed Price');
                    }
                    if (o.pricing_frequency) freqSelect.value = o.pricing_frequency;
                    if (o.delivery_model) {
                        document.getElementById('delivery_model').value = o.delivery_model;
                        document.querySelectorAll('#deliveryModelSelector .ij-choice-card').forEach(function (b) {
                            b.classList.toggle('is-selected', b.getAttribute('data-val') === o.delivery_model);
                        });
                    }
                    if (o.service_availability) {
                        document.getElementById('service_availability').value = o.service_availability;
                        document.querySelectorAll('#availabilitySelector .ij-choice-card').forEach(function (b) {
                            b.classList.toggle('is-selected', b.getAttribute('data-val') === o.service_availability);
                        });
                    }
                    if (o.service_duration !== null && o.service_duration !== undefined) document.getElementById('service_duration').value = o.service_duration;
                    if (o.implementation_timeline !== null && o.implementation_timeline !== undefined) document.getElementById('implementation_timeline').value = o.implementation_timeline;
                    if (o.sla_details !== null && o.sla_details !== undefined) document.getElementById('sla_details').value = o.sla_details;
                    if (o.warranty !== null && o.warranty !== undefined) document.getElementById('warranty').value = o.warranty;
                    if (o.validity_days) document.getElementById('validity_days').value = o.validity_days;
                    if (o.description) {
                        descEditor.innerHTML = o.description;
                        descHidden.value = o.description;
                        descCharCount.textContent = (descEditor.textContent || '').trim().length + ' characters';
                    }
                    const locList = Array.isArray(o.locations) ? o.locations : (o.locations && typeof o.locations === 'object' ? Object.values(o.locations) : []);
                    locList.forEach(function (l) { addLocation(l.location_name || l); });
                    const tagList = Array.isArray(o.tags) ? o.tags : (o.tags && typeof o.tags === 'object' ? Object.values(o.tags) : []);
                    tagList.forEach(function (t) { addFeatureTag(t.tag_name || t); });
                    const qList = Array.isArray(o.questions) ? o.questions : (o.questions && typeof o.questions === 'object' ? Object.values(o.questions) : []);
                    if (qList.length) {
                        qList.forEach(function (q) {
                            if (q.question) questions.push({ question: q.question, answer_type: q.answer_type || 'short', is_required: !!q.is_required });
                        });
                        renderQuestions();
                    }
                    const initOwners = @json($assignedOwners ?? []);
                    const initTeam = @json($assignedTeam ?? []);
                    if (typeof hydrateServiceTeam === 'function') {
                        hydrateServiceTeam(initOwners, initTeam, Array.isArray(o.team) ? o.team : []);
                    }
                })();
            @endif

            function populateReview() {
                const ownerSel = document.getElementById('owner_client_id');
                document.getElementById('rvOwner').textContent = (ownerSel && ownerSel.selectedIndex >= 0) ? ownerSel.options[ownerSel.selectedIndex].textContent.trim() : '-';
                document.getElementById('rvName').textContent = document.getElementById('offering_name').value || '-';
                document.getElementById('rvSku').textContent = skuInput.value || '-';
                document.getElementById('rvCategory').textContent = document.getElementById('crm_category').value || '-';
                document.getElementById('rvStatus').textContent = document.getElementById('offering_status').value;

                const isConf = document.getElementById('pricing_visibility').value === 'confidential';
                const minP = document.getElementById('min_price').value || '0';
                const maxP = document.getElementById('max_price').value;
                const curr = document.getElementById('currency_code').value;
                document.getElementById('rvPricing').textContent = isConf ? 'Price on Request' : (curr + ' ' + minP + (maxP ? (' - ' + curr + ' ' + maxP) : '') + ' ' + freqSelect.value);

                document.getElementById('rvCoverage').textContent = locations.length ? locations.join(', ') : 'Global / Remote';
                document.getElementById('rvModel').textContent = document.getElementById('pricing_model').value + ' (' + freqSelect.value + ')';
                document.getElementById('rvDelivery').textContent = document.getElementById('delivery_model').value;
                document.getElementById('rvAvailability').textContent = document.getElementById('service_availability').value;

                document.getElementById('rvDescPreview').innerHTML = descEditor.innerHTML || '<span class="text-muted">No description entered</span>';

                document.getElementById('rvTagsTitle').textContent = 'Service Features & Tags (' + tags.length + ')';
                const rvTagsBox = document.getElementById('rvTagsList');
                rvTagsBox.innerHTML = '';
                if (!tags.length) {
                    rvTagsBox.innerHTML = '<span class="text-muted" style="font-size:12px;">No tags added</span>';
                } else {
                    tags.forEach(function (t) {
                        const sp = document.createElement('span');
                        sp.className = 'rv-tag-pill';
                        sp.textContent = t;
                        rvTagsBox.appendChild(sp);
                    });
                }

                document.getElementById('rvQuestionsTitle').textContent = 'Qualification Questions (' + questions.length + ')';
                const rvQBox = document.getElementById('rvQuestionsList');
                rvQBox.innerHTML = '';
                if (!questions.length) {
                    rvQBox.innerHTML = '<span class="text-muted" style="font-size:12px;">No qualification questions added</span>';
                } else {
                    questions.forEach(function (q, idx) {
                        const row = document.createElement('div');
                        row.className = 'rv-q-row';
                        row.innerHTML = '<div class="d-flex align-items-center gap-2"><span class="rv-q-num">' + (idx + 1) + '</span><span class="rv-qt"></span></div>' +
                            '<span class="' + (q.is_required ? 'ij-q-req-pill' : 'ij-q-opt-pill') + '">' + (q.is_required ? 'Required' : 'Optional') + '</span>';
                        row.querySelector('.rv-qt').textContent = q.question;
                        rvQBox.appendChild(row);
                    });
                }

                const valSel = document.getElementById('validity_days');
                document.getElementById('rvValidityNote').textContent = 'Catalog validity: ' + ((valSel && valSel.selectedIndex >= 0) ? valSel.options[valSel.selectedIndex].textContent : 'Ongoing');
            }

            function submitOffering(isDraft) {
                descHidden.value = descEditor.innerHTML;
                if (isDraft) document.getElementById('offering_status').value = 'draft';
                freqSelect.disabled = false;
                const formData = new FormData(document.getElementById('crmOfferingForm'));
                if (document.getElementById('pricing_model').value === 'Fixed Price') freqSelect.disabled = true;

                fetch(SUBMIT_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(async function (res) {
                    const data = await res.json().catch(function () { return {}; });
                    if (res.ok && data.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(data.message);
                        else alert(data.message);
                        window.location.href = data.redirect_url || @json(route('service_company.jobs.index'));
                    } else {
                        if (data.errors) {
                            let firstFieldEl = null;
                            Object.keys(data.errors).forEach(function (k) {
                                const grp = document.querySelector('.form-group-cus[data-field="' + k + '"]');
                                if (grp) {
                                    grp.classList.add('has-error');
                                    if (!firstFieldEl) firstFieldEl = grp;
                                }
                            });
                            if (firstFieldEl) {
                                const stepEl = firstFieldEl.closest('.step-content');
                                if (stepEl) {
                                    const sNum = parseInt(stepEl.id.replace('step', ''), 10);
                                    if (sNum && sNum !== currentStep) {
                                        currentStep = sNum;
                                        document.querySelectorAll('#jrwrap .step-content').forEach(function (el) { el.classList.remove('active'); });
                                        stepEl.classList.add('active');
                                        updateTopActionBtn(sNum);
                                    }
                                }
                                firstFieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                        const msg = data.message || 'Validation failed. Please check required fields.';
                        if (typeof toastr !== 'undefined') toastr.error(msg);
                        else alert(msg);
                    }
                })
                .catch(function () { alert('Network error while saving product.'); });
            }

            document.getElementById('btnTopSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnBottomSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnTopPublish').addEventListener('click', function () {
                if (currentStep < 4) {
                    crmGoToStep(currentStep + 1);
                } else {
                    submitOffering(false);
                }
            });
            document.getElementById('btnBottomPublish').addEventListener('click', function () { submitOffering(false); });
        })();
    </script>
@endpush
