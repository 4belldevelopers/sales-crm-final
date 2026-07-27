<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .custmbg {
        background-color: #f8f9fa !important;
    }
    .custmbg, .custmbg * {
        font-family: 'Poppins', sans-serif;
    }
    /* Keep FontAwesome icons on their own font — the Poppins rule above would blank them out */
    .custmbg .fa, .custmbg .fas, .custmbg .fa-solid { font-family: "Font Awesome 6 Free" !important; font-weight: 900 !important; }
    .custmbg .far, .custmbg .fa-regular { font-family: "Font Awesome 6 Free" !important; font-weight: 400 !important; }
    .custmbg .fab, .custmbg .fa-brands { font-family: "Font Awesome 6 Brands" !important; font-weight: 400 !important; }
    .custmbg .fa, .custmbg .fas, .custmbg .far, .custmbg .fab,
    .custmbg .fa-solid, .custmbg .fa-regular, .custmbg .fa-brands {
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
    }

    /* Rating dropdown (replaces clickable star icons for easier selection) */
    .rating-select {
        width: 100%;
        max-width: 160px;
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        color: #334155;
        background: #ffffff;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s;
        display: inline-block;
        vertical-align: middle;
    }
    .rating-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.1); }
    .rating-select option { color: #334155; }
    /* Golden star display shown next to each rating dropdown */
    .rating-stars { display: inline-block; vertical-align: middle; margin-right: 8px; font-size: 15px; letter-spacing: 1px; line-height: 1; white-space: nowrap; }
    .rating-stars .on { color: #f59e0b; }
    .rating-stars .off { color: #e2e8f0; }

    /* Font caps — max 15px */
    .custmbg .fs-1, .custmbg .fs-2, .custmbg .fs-3, .custmbg .fs-4 { font-size: 15px !important; }
    .custmbg .fs-5 { font-size: 14px !important; }
    .custmbg .fs-6 { font-size: 13px !important; }
    .custmbg .fs-7 { font-size: 11px !important; }
    .custmbg .fw-bolder { font-weight: 600; }
    .custmbg .fw-bold { font-weight: 500; }

    .feedback-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .feedback-header {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 18px 20px;
        font-family: "Poppins", sans-serif;
    }
    /* Robust hero layout: candidate block + an auto-fitting info grid that never reflows
       awkwardly when the JD/Resume drawer opens (no Bootstrap breakpoint dependency). */
    .hero-flex { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 16px 22px; }
    .hero-candidate { display: flex; align-items: center; gap: 12px; flex: 0 0 215px; min-width: 200px; }
    .hero-name { font-size: 14px; font-weight: 700; color: #1e293b; line-height: 1.25; }
    .hero-meta { font-size: 11.5px; color: #94a3b8; font-weight: 400; margin-top: 1px; }
    .candidate-avatar {
        width: 48px; height: 48px; border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 600; flex-shrink: 0;
    }
    .info-grid {
        flex: 1 1 360px; min-width: 0;
        display: grid; grid-template-columns: repeat(auto-fit, minmax(195px, 1fr)); gap: 14px 18px;
    }
    .info-item { display: flex; align-items: flex-start; gap: 8px; min-width: 0; }
    .info-icon {
        width: 30px; height: 30px; border-radius: 6px; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        color: #64748b; font-size: 12px; flex-shrink: 0;
    }
    .info-item > div:last-child { min-width: 0; }
    .info-label { font-size: 9.5px; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px; }
    /* Never let a value stack onto 3 lines — keep it on one line, truncate with … if absurdly long */
    .info-value { font-size: 13px; color: #1e293b; font-weight: 600; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .info-sub { font-size: 11px; color: #64748b; font-weight: 400; line-height: 1.25; word-break: normal; overflow-wrap: break-word; }
    .section-card {
        background: #ffffff; border-radius: 12px; border: 1px solid #e9ecef;
        margin-bottom: 16px; padding: 20px;
    }
    .section-title {
        font-size: 14px; font-weight: 600; color: #1e293b;
        display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
    }
    .section-number {
        width: 24px; height: 24px; border-radius: 6px; background: #eff6ff;
        color: #3b82f6; display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
    }
    .status-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; gap: 5px; }
    .status-completed { background: #dcfce7; color: #16a34a; }
    .radio-card-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)); gap: 10px; align-items: stretch; }
    .radio-card { position: relative; min-width: 0; display: flex; }
    .radio-card input[type="radio"] { position: absolute; opacity: 0; }
    .radio-card label {
        display: flex; align-items: center; gap: 8px; padding: 12px 14px;
        border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer;
        transition: all 0.2s; font-size: 13px; font-weight: 500; color: #475569;
        background: #ffffff; width: 100%; min-height: 52px; margin-bottom: 0;
    }
    .radio-card label:hover { border-color: #cbd5e1; background: #f8fafc; }
    .radio-card input[type="radio"]:checked + label { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }
    .radio-card .radio-circle {
        width: 16px; height: 16px; border: 2px solid #cbd5e1; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;
    }
    .radio-card input[type="radio"]:checked + label .radio-circle { border-color: #3b82f6; background: #3b82f6; }
    .radio-card .radio-circle::after { content: ''; width: 5px; height: 5px; background: white; border-radius: 50%; display: none; }
    .radio-card input[type="radio"]:checked + label .radio-circle::after { display: block; }
    .radio-card .radio-icon { font-size: 14px; width: 18px; text-align: center; }
    .skills-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .skills-table th {
        text-align: left; padding: 10px 12px; font-size: 10px; font-weight: 600;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
        background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .skills-table th:first-child { border-radius: 6px 0 0 0; }
    .skills-table th:last-child { border-radius: 0 6px 0 0; }
    .skills-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .skill-name { font-size: 13px; font-weight: 600; color: #1e293b; }
    .skill-exp { font-size: 11px; color: #64748b; font-weight: 400; }

    /* Star Rating - Pure CSS */
    .star-rating-wrap { display: flex; align-items: center; gap: 8px; }
    .star-rating { display: inline-flex; flex-direction: row-reverse; gap: 3px; align-items: center; }
    .star-rating input { display: none; }
    .star-rating label { cursor: pointer; font-size: 18px; color: #e2e8f0; transition: color 0.15s; line-height: 1; margin-bottom: 0; padding: 2px; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #fbbf24; }
    .rating-reset { font-size: 11px; color: #94a3b8; cursor: pointer; text-decoration: underline; user-select: none; white-space: nowrap; }
    .rating-reset:hover { color: #64748b; }

    .comment-input { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; color: #334155; background: #ffffff; transition: border-color 0.2s; }
    .comment-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }
    .comment-input::placeholder { color: #94a3b8; }
    .char-count { font-size: 10px; color: #94a3b8; text-align: right; margin-top: 3px; }
    .add-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border: 1px dashed #cbd5e1; border-radius: 6px; color: #3b82f6; font-size: 12px; font-weight: 500; cursor: pointer; background: transparent; transition: all 0.2s; margin-top: 10px; }
    .add-btn:hover { background: #eff6ff; border-color: #3b82f6; }
    .compensation-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 16px; }
    .comp-input-group { display: flex; flex-direction: column; gap: 5px; }
    .comp-label { font-size: 11px; font-weight: 600; color: #475569; }
    .comp-input-wrap { display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #ffffff; }
    .comp-input-wrap .prefix { padding: 8px 10px; background: #f8fafc; border-right: 1px solid #e2e8f0; color: #64748b; font-size: 12px; font-weight: 500; display: flex; align-items: center; justify-content: center; }
    .comp-input-wrap input, .comp-input-wrap select { flex: 1; border: none; padding: 8px 10px; font-size: 12px; color: #334155; outline: none; background: transparent; }
    .comp-input-wrap .suffix { padding: 8px 10px; color: #94a3b8; font-size: 11px; font-weight: 500; white-space: nowrap; }
    .comp-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .comp-option-group { display: flex; flex-direction: column; gap: 8px; }
    .comp-option-title { font-size: 12px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
    .comp-option-item { display: flex; align-items: flex-start; gap: 8px; padding: 8px; border-radius: 6px; cursor: pointer; transition: background 0.2s; margin-bottom: 0; }
    .comp-option-item:hover { background: #f8fafc; }
    .comp-option-item input[type="radio"], .comp-option-item input[type="checkbox"] { margin-top: 2px; accent-color: #3b82f6; width: 14px; height: 14px; flex-shrink: 0; }
    .comp-option-text { display: flex; flex-direction: column; }
    .comp-option-label { font-size: 12px; font-weight: 500; color: #334155; line-height: 1.3; }
    .comp-option-desc { font-size: 10px; color: #94a3b8; margin-top: 1px; line-height: 1.3; }
    .recommendation-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
    .rec-card { position: relative; }
    .rec-card input { position: absolute; opacity: 0; }
    .rec-card label { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 14px 10px; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s; text-align: center; height: 100%; margin-bottom: 0; }
    .rec-card label:hover { border-color: #cbd5e1; background: #f8fafc; }
    .rec-card input:checked + label { border-color: #3b82f6; background: #eff6ff; }
    .rec-icon { font-size: 20px; margin-bottom: 2px; }
    .rec-title { font-size: 12px; font-weight: 600; color: #334155; }
    .rec-desc { font-size: 10px; color: #94a3b8; line-height: 1.3; }
    .rec-card input:checked + label .rec-title { color: #1d4ed8; }
    .final-textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #334155; resize: vertical; min-height: 80px; transition: border-color 0.2s; }
    .final-textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }
    .form-actions { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; gap: 10px; }
    .btn-save-draft { padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-save-draft:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-submit { padding: 8px 18px; border: none; border-radius: 6px; background: #2563eb; color: #ffffff; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit:hover { background: #1d4ed8; }
    .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
    .btn-submit .sfb-progress { align-items: center; gap: 6px; }
    .sfb-spinner { display: inline-block; width: 13px; height: 13px; border: 2px solid rgba(255,255,255,0.45); border-top-color: #ffffff; border-radius: 50%; animation: sfbspin .6s linear infinite; }
    @keyframes sfbspin { to { transform: rotate(360deg); } }
    .btn-back { padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-back:hover { background: #f8fafc; }

    /* Interviewers Modal */
    /* This view is injected into the legacy #interview_feedback Bootstrap modal. Hide that modal's
       chrome (its dialog box + "Interview Feedback" header) so only our own card is visible and the
       old modal no longer shows behind the new one. */
    #interview_feedback .modal-header { display: none !important; }
    #interview_feedback .modal-content { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
    #interview_feedback .modal-dialog { max-width: 1080px !important; }
    /* The feedback form supplies its own card now that the modal chrome is gone */
    #feedbackFormWrapper { background: #f6f7f9; border-radius: 14px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 18px 18px 4px; }

    .interviewers-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px);
        z-index: 1070; display: none; align-items: center; justify-content: center; padding: 16px;
    }
    .interviewers-modal-overlay.active { display: flex; }
    .interviewers-modal-content {
        background: #ffffff; border-radius: 14px; width: 100%; max-width: 1080px;
        max-height: 92vh; overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalSlideIn 0.25s ease-out;
    }
    /* Keep the header visible while the body scrolls on shorter laptop screens */
    .interviewers-modal-header { position: sticky; top: 0; background: #ffffff; z-index: 2; border-radius: 14px 14px 0 0; }
    @keyframes modalSlideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .interviewers-modal-header { padding: 18px 22px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
    .interviewers-modal-title { font-size: 15px; font-weight: 600; color: #1e293b; }
    .interviewers-modal-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: all 0.2s; }
    .interviewers-modal-close:hover { background: #e2e8f0; color: #334155; }
    .interviewers-modal-body { padding: 20px 22px; }
    .summary-cards { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-bottom: 18px; }
    .summary-card { background: #ffffff; border: 1px solid #e9ecef; border-radius: 10px; padding: 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .summary-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .summary-label { font-size: 10px; color: #64748b; font-weight: 500; }
    .summary-value { font-size: 18px; font-weight: 700; color: #1e293b; }
    .interviewer-card { background: #ffffff; border: 1px solid #e9ecef; border-radius: 10px; padding: 16px; display: flex; flex-direction: column; gap: 10px; transition: border-color 0.2s; }
    .interviewer-card:hover { border-color: #cbd5e1; }
    .interviewer-card.active { border-color: #3b82f6; background: #fafafa; }
    .interviewer-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: white; flex-shrink: 0; }
    .interviewer-name { font-size: 13px; font-weight: 600; color: #1e293b; }
    .interviewer-role { font-size: 11px; color: #64748b; }
    .interviewer-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .status-submitted { background: #dcfce7; color: #16a34a; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .interviewer-rating { display: flex; gap: 2px; font-size: 14px; }
    .custmbg .star-filled, .star-filled { color: #fbbf24 !important; }
    .custmbg .star-empty, .star-empty { color: #e2e8f0 !important; }
    .result-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .result-selected { background: #dcfce7; color: #16a34a; }
    .result-not-selected { background: #fee2e2; color: #dc2626; }
    .result-pending { background: #f1f5f9; color: #64748b; }
    .result-hold { background: #fef3c7; color: #d97706; }
    .result-rejected { background: #fee2e2; color: #dc2626; }
    .rec-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .rec-strong-hire { background: #dcfce7; color: #16a34a; }
    .rec-hire { background: #eff6ff; color: #2563eb; }
    .rec-consider { background: #fef3c7; color: #d97706; }
    .rec-no-hire { background: #fee2e2; color: #dc2626; }
    .interviewer-card { height: 100%; }
    .ic-head { display: flex; align-items: center; gap: 10px; }
    .ic-id { min-width: 0; }
    .ic-badges { display: flex; flex-wrap: wrap; gap: 5px; }
    .ic-foot { margin-top: auto; }
    .rating-label { font-size: 11px; color: #64748b; font-weight: 500; margin-bottom: 3px; }
    .interviewer-rating { display: flex; align-items: center; gap: 2px; font-size: 15px; line-height: 1; }
    .rating-num { font-size: 11px; color: #64748b; font-weight: 600; margin-left: 5px; }
    .star-muted, .star-muted span { color: #e2e8f0 !important; }
    .submitted-on { font-size: 10px; color: #94a3b8; }
    .submitted-on b { color: #475569; font-weight: 600; }
    .you-tag { display: inline-block; background: #3b82f6; color: #ffffff; font-size: 9px; font-weight: 600; padding: 1px 7px; border-radius: 10px; margin-left: 6px; vertical-align: middle; }
    .btn-action { width: 100%; padding: 8px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; text-align: center; transition: all 0.2s; border: 1px solid transparent; }
    .btn-action-primary { background: #2563eb; color: white; border-color: #2563eb; }
    .btn-action-primary:hover { background: #1d4ed8; }
    .btn-action-outline { background: #ffffff; color: #3b82f6; border-color: #e2e8f0; }
    .btn-action-outline:hover { background: #eff6ff; border-color: #3b82f6; }
    .modal-footer-note { padding: 14px 22px; border-top: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
    .note-text { font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 6px; }
    .btn-close-footer { padding: 6px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-close-footer:hover { background: #f8fafc; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .page-title { font-size: 15px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; }
    .by-text { font-size: 12px; color: #64748b; font-weight: 400; }
    .by-name { color: #3b82f6; font-weight: 500; }
    .btn-download-report { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; color: #475569; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s; cursor: pointer; }
    .btn-feedback-close { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; color: #64748b; cursor: pointer; transition: all 0.2s; }
    .btn-feedback-close:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .btn-download-report:hover { background: #f8fafc; border-color: #cbd5e1; }

    @media (max-width: 991px) {
        .info-grid { grid-template-columns: repeat(2, 1fr); }
        .compensation-grid { grid-template-columns: repeat(2, 1fr); }
        .comp-options { grid-template-columns: 1fr; }
        .recommendation-grid { grid-template-columns: repeat(3, 1fr); }
        .radio-card-group { grid-template-columns: repeat(2, 1fr); }
        .summary-cards { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 576px) {
        .info-grid { grid-template-columns: 1fr; }
        .compensation-grid { grid-template-columns: 1fr; }
        .recommendation-grid { grid-template-columns: repeat(2, 1fr); }
        .summary-cards { grid-template-columns: repeat(2, 1fr); }
    }

    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
        .no-print { display: none !important; }
    }
</style>

<div class="card mb-5 mb-xl-5 mx-2 custmbg">
    <div class="card-body pt-5 pb-0 px-5">
        <form class="ajax-form" method="POST" id="ajax-form" name="ajax-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="interview_id" id="interview_id" value="{{ @$interviewSchedule->interview_schedules_id }}" />
            <input type="hidden" name="application_id" id="application_id" value="{{ @$interviewSchedule->id }}" />

            @php
                $schedule_date = $schedule_time = "";
                $schedule = @$interviewSchedule->schedule_date;
                $schedule_date = date("d M, Y", strtotime($schedule));
                $schedule_time = date("H:i A", strtotime($schedule));
                $candidateInitial = strtoupper(substr(@$interviewSchedule->full_name, 0, 1));
                $currentUserName = auth()->user()->name ?? @$interviewSchedule->upload_by_name ?? '';
            @endphp

            <!-- Interviewers & Feedback Summary Modal -->
            <div class="interviewers-modal-overlay" id="interviewersModal">
                <div class="interviewers-modal-content">
                    <div class="interviewers-modal-header">
                        <div class="interviewers-modal-title">Interviewers & Feedback Summary</div>
                        <button type="button" class="interviewers-modal-close" onclick="closeInterviewersModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="interviewers-modal-body">
                        @php
                            $submittedCount = $pendingCount = $strongHire = $hire = $consider = $noHire = 0;
                            $interviewerData = [];
                            $bgColors = ['#667eea', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
                            $myFeedbackId = isset($myFeedbackId) ? (int) $myFeedbackId : 0;
                            $canGiveFeedback = (bool) ($canGiveFeedback ?? false);
                            // Salary View permission gates the Compensation Assessment section
                            $canViewSalary = (bool) ($canViewSalary ?? false);
                            $captainName = $captainName ?? '';
                            $captainUserId = (int) ($captainUserId ?? 0);
                            $captainDesignation = $captainDesignation ?? '';
                            $captainCompany = $captainCompany ?? '';
                            $currentUserName = $currentUserName ?? (auth()->user()->name ?? '');

                            // Overall recommendation (section 6) and interview result (section 5) -> label + colour class
                            $recMap = [
                                'strong_hire'    => ['Strong Hire', 'rec-strong-hire'],
                                'hire'           => ['Hire', 'rec-hire'],
                                'consider'       => ['Consider', 'rec-consider'],
                                'no_hire'        => ['No Hire', 'rec-no-hire'],
                                'strong_no_hire' => ['Strong No Hire', 'rec-no-hire'],
                            ];
                            $resultMap = [
                                'selected'         => ['Selected', 'result-selected', 'fa-check'],
                                'hold'             => ['Hold', 'result-hold', 'fa-pause'],
                                'rejected'         => ['Rejected', 'result-rejected', 'fa-times'],
                                'fake_proxy'       => ['Fake / Proxy', 'result-rejected', 'fa-user-slash'],
                                'feedback_pending' => ['Feedback Pending', 'result-pending', 'fa-hourglass-half'],
                            ];

                            // Feedback-givers = CURRENT captain + selected interviewers + anyone who already
                            // submitted (a previous captain's feedback still shows so the new captain can view it).
                            // The recruiter who scheduled the interview is NOT a giver.
                            $givers = []; // key = lowercase name => ['name','role','designation','company']
                            $addGiver = function ($name, $role, $designation = '', $company = '') use (&$givers) {
                                $name = trim((string) $name);
                                if ($name === '') return;
                                $k = strtolower($name);
                                if (!isset($givers[$k])) { $givers[$k] = ['name' => $name, 'role' => $role, 'designation' => $designation, 'company' => $company]; }
                            };
                            if (!empty($captainName)) { $addGiver($captainName, 'Captain', $captainDesignation, $captainCompany); }
                            if (!empty($interviewSchedule->interviewers)) {
                                foreach (explode(',', $interviewSchedule->interviewers) as $n) { $addGiver($n, 'Interviewer'); }
                            }
                            if (!empty($feedbackMapped)) {
                                foreach ($feedbackMapped as $fb) { $addGiver($fb['by_name'] ?? '', 'Interviewer'); }
                            }

                            $idx = 0;
                            foreach ($givers as $k => $g) {
                                $name = $g['name'];
                                $fdata = null; $matchedFid = 0;
                                if (!empty($feedbackMapped)) {
                                    foreach ($feedbackMapped as $mfid => $m) {
                                        if (strtolower(trim($m['by_name'] ?? '')) === $k) { $fdata = $m; $matchedFid = $mfid; break; }
                                    }
                                }
                                $hasFeedback = !empty($fdata);
                                $isCurrentUser = (strtolower(trim($currentUserName)) === $k);
                                $feedbackRating = $hasFeedback ? intval($fdata['star_ratings'] ?? 0) : 0;
                                $feedbackRecKey = $hasFeedback ? strtolower(trim($fdata['overall_recommendation'] ?? '')) : '';
                                $feedbackResultKey = $hasFeedback ? strtolower(trim($fdata['interview_result'] ?? '')) : '';
                                if ($feedbackResultKey === '' && $hasFeedback) { $st = intval($fdata['interviewStatus'] ?? 0); $feedbackResultKey = $st==1 ? 'selected' : ($st==2 ? 'rejected' : ''); }
                                $initials = '';
                                foreach (explode(' ', $name) as $p) { $initials .= strtoupper(mb_substr(trim($p), 0, 1)); }
                                $initials = mb_substr(($initials ?: 'I'), 0, 2);
                                if ($hasFeedback) {
                                    $submittedCount++;
                                    if ($feedbackRecKey === 'strong_hire') $strongHire++;
                                    elseif ($feedbackRecKey === 'hire') $hire++;
                                    elseif ($feedbackRecKey === 'consider') $consider++;
                                    elseif ($feedbackRecKey === 'no_hire' || $feedbackRecKey === 'strong_no_hire') $noHire++;
                                    elseif ($feedbackRating >= 5) $strongHire++;
                                    elseif ($feedbackRating >= 4) $hire++;
                                    elseif ($feedbackRating >= 3) $consider++;
                                    elseif ($feedbackRating >= 1) $noHire++;
                                } else {
                                    $pendingCount++;
                                }
                                $interviewerData[] = [
                                    'name' => $name,
                                    'initials' => $initials,
                                    'bg' => $isCurrentUser ? '#3b82f6' : $bgColors[$idx % count($bgColors)],
                                    'hasFeedback' => $hasFeedback,
                                    'rating' => $feedbackRating,
                                    'result' => $feedbackResultKey,
                                    'rec' => $feedbackRecKey,
                                    'date' => $hasFeedback ? ($fdata['created_at'] ?? null) : null,
                                    'isCurrentUser' => $isCurrentUser,
                                    'designation' => $hasFeedback ? ($fdata['designation'] ?: ($g['designation'] ?: $g['role'])) : ($g['designation'] ?: $g['role']),
                                    'company' => ($g['company'] ?? '') ?: ($interviewSchedule->client_name ?? ($interviewSchedule->company_name ?? '')),
                                    'role' => $g['role'],
                                    'feedback_id' => $matchedFid,
                                ];
                                $idx++;
                            }
                        @endphp

                        <div id="interviewersGridView">
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-users"></i></div>
                                <div class="summary-label">Total Interviewers</div>
                                <div class="summary-value">{{ count($interviewerData) }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-check-circle"></i></div>
                                <div class="summary-label">Submitted</div>
                                <div class="summary-value" style="color: #16a34a;">{{ $submittedCount }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #fef3c7; color: #d97706;"><i class="fas fa-clock"></i></div>
                                <div class="summary-label">Pending</div>
                                <div class="summary-value" style="color: #d97706;">{{ $pendingCount }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-thumbs-up"></i></div>
                                <div class="summary-label">Strong Hire</div>
                                <div class="summary-value" style="color: #16a34a;">{{ $strongHire }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-handshake"></i></div>
                                <div class="summary-label">Hire</div>
                                <div class="summary-value" style="color: #3b82f6;">{{ $hire }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #fef3c7; color: #d97706;"><i class="fas fa-user-check"></i></div>
                                <div class="summary-label">Consider</div>
                                <div class="summary-value" style="color: #d97706;">{{ $consider }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-ban"></i></div>
                                <div class="summary-label">No Hire</div>
                                <div class="summary-value" style="color: #dc2626;">{{ $noHire }}</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            @forelse($interviewerData as $interviewer)
                                <div class="col-md-3 col-sm-6">
                                    <div class="interviewer-card {{ $interviewer['isCurrentUser'] ? 'active' : '' }}">
                                        @php
                                            $recInfo = $recMap[$interviewer['rec']] ?? null;
                                            $resInfo = $resultMap[$interviewer['result']] ?? null;
                                        @endphp
                                        <div class="ic-head">
                                            <div class="interviewer-avatar" style="background: {{ $interviewer['bg'] }};">{{ $interviewer['initials'] }}</div>
                                            <div class="ic-id">
                                                <div class="interviewer-name">{{ $interviewer['name'] }}@if($interviewer['isCurrentUser'])<span class="you-tag">You</span>@endif</div>
                                                <div class="interviewer-role">{{ $interviewer['designation'] }}</div>
                                                <div class="interviewer-role">{{ $interviewer['company'] }}</div>
                                            </div>
                                        </div>

                                        <div>
                                            @if($interviewer['hasFeedback'])
                                                <span class="interviewer-status status-submitted"><i class="fas fa-check-circle"></i> Submitted</span>
                                            @else
                                                <span class="interviewer-status status-pending"><i class="fas fa-clock"></i> Pending</span>
                                            @endif
                                        </div>

                                        @if($interviewer['hasFeedback'])
                                            <div class="ic-badges">
                                                @if($recInfo)
                                                    <span class="rec-badge {{ $recInfo[1] }}">{{ $recInfo[0] }}</span>
                                                @endif
                                                @if($resInfo)
                                                    <span class="result-badge {{ $resInfo[1] }}"><i class="fas {{ $resInfo[2] }}"></i> {{ $resInfo[0] }}</span>
                                                @endif
                                                @if(!$recInfo && !$resInfo)
                                                    <span class="result-badge result-pending">No recommendation</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="rating-label">Rating</div>
                                                <div class="interviewer-rating">
                                                    @for($i=1; $i<=5; $i++)
                                                        <span class="{{ $i <= $interviewer['rating'] ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                                                    @endfor
                                                    @if($interviewer['rating'])<span class="rating-num">{{ number_format($interviewer['rating'],1) }}/5</span>@endif
                                                </div>
                                            </div>
                                            <div class="submitted-on">Submitted on<br><b>{{ $interviewer['date'] ? date('d M Y, h:i A', strtotime($interviewer['date'])) : '--' }}</b></div>
                                        @else
                                            <div>
                                                <div class="rating-label">Rating</div>
                                                <div class="interviewer-rating star-muted">&#9733;&#9733;&#9733;&#9733;&#9733; <span class="rating-num">— —</span></div>
                                            </div>
                                            <div class="submitted-on">Submitted on<br><b>--</b></div>
                                        @endif

                                        <div class="ic-foot">
                                        @if($interviewer['isCurrentUser'] && $canGiveFeedback)
                                            <button type="button" class="btn-action btn-action-primary" onclick="enterFeedbackForm()">
                                                <i class="fas fa-edit"></i> {{ $interviewer['hasFeedback'] ? 'View / Edit Feedback' : 'Add Your Feedback' }}
                                            </button>
                                        @elseif($interviewer['hasFeedback'])
                                            <button type="button" class="btn-action btn-action-outline" onclick="viewFeedbackDetail({{ $interviewer['feedback_id'] }})">
                                                <i class="fas fa-eye"></i> View Feedback
                                            </button>
                                        @else
                                            <button type="button" class="btn-action btn-action-outline" onclick="remindInterviewer('{{ addslashes($interviewer['name']) }}')">
                                                <i class="fas fa-paper-plane"></i> Remind Interviewer
                                            </button>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    @if($canGiveFeedback)
                                        <p class="text-muted mb-2">No feedback submitted yet.</p>
                                        <button type="button" class="btn-action btn-action-primary" style="max-width:220px;margin:0 auto;" onclick="enterFeedbackForm()">
                                            <i class="fas fa-edit"></i> Add Your Feedback
                                        </button>
                                    @else
                                        <p class="text-muted mb-1"><i class="fas fa-info-circle"></i> Feedback not submitted yet.</p>
                                        <p class="text-muted" style="font-size:12px;">You are not allowed to submit feedback for this interview.</p>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                        </div><!-- /#interviewersGridView -->

                        {{-- Read-only per-interviewer feedback detail --}}
                        <div id="feedbackDetailView" style="display:none;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <button type="button" class="btn-back" onclick="backToInterviewersGrid()">
                                    <i class="fas fa-arrow-left"></i> Back to Interviewers
                                </button>
                                <button type="button" class="btn-download-report" onclick="downloadCurrentReport(window.currentDetailFid)">
                                    <i class="fas fa-download"></i> Download Report
                                </button>
                            </div>
                            @php
                                $attLabelMap = [1=>'Attended', 2=>'No Show', 3=>'Rescheduled', 4=>'Cancelled by Company', 6=>'Other Reason', 7=>'Cancelled by Candidate'];
                            @endphp
                            @if(!empty($feedbackMapped))
                                @foreach($feedbackMapped as $fid => $fb)
                                    @php
                                        $fbInitials = '';
                                        foreach(explode(' ', trim($fb['by_name'] ?? 'I')) as $p){ $fbInitials .= strtoupper(mb_substr(trim($p),0,1)); }
                                        $fbInitials = mb_substr($fbInitials, 0, 2);
                                        $fbStatusId = intval($fb['interviewStatus'] ?? 0);
                                        $fbAttId = intval($fb['candidateStatus'] ?? 0);
                                        $fbAttLabel = $attLabelMap[$fbAttId] ?? '';
                                    @endphp
                                    @php
                                        // Split saved skills into Technical vs Behavioral
                                        $behavioralNames = ['communication skills','problem solving','leadership','cultural fit'];
                                        $techSkills = []; $behSkills = [];
                                        foreach(($fb['feedbackdata'] ?? []) as $sk){
                                            $nm = strtolower(trim($sk['skills'] ?? ''));
                                            if($nm === '') continue;
                                            if(in_array($nm, $behavioralNames)) $behSkills[] = $sk; else $techSkills[] = $sk;
                                        }
                                        $fbRisk = !empty($fb['risk_factors']) ? array_filter(array_map('trim', explode(',', $fb['risk_factors']))) : [];
                                        $riskLabels = ['multiple_offers'=>'Holding Multiple Offers','counter_offer'=>'Counter Offer Expected','relocation'=>'Relocation Concern','salary_gap'=>'Salary Gap','notice_period'=>'Long Notice Period','role_preference'=>'Role / Domain Preference','others'=>'Others'];
                                        $compFitLabels = ['within_budget'=>'Within Budget','slightly_above'=>'Slightly Above Budget','significantly_above'=>'Significantly Above Budget','negotiable'=>'Negotiable','not_discussed'=>'Not Discussed'];
                                        $joinProbLabels = ['high'=>'High (80 - 100%)','medium'=>'Medium (50 - 80%)','low'=>'Low (< 50%)'];
                                        $resultLabels = ['selected'=>'Selected','hold'=>'Hold','rejected'=>'Rejected','fake_proxy'=>'Fake / Proxy','feedback_pending'=>'Feedback Pending'];
                                        $recLabels = ['strong_hire'=>'Strong Hire','hire'=>'Hire','consider'=>'Consider','no_hire'=>'No Hire','strong_no_hire'=>'Strong No Hire'];
                                        $noticeLabels = ['15'=>'15 Days','30'=>'30 Days','60'=>'60 Days','90'=>'90 Days'];
                                        $fbResult = $fb['interview_result'] ?? '';
                                        $fbReco = $fb['overall_recommendation'] ?? '';
                                    @endphp
                                    <div class="fb-detail-panel" id="fbDetail_{{ $fid }}" style="display:none;">

                                        {{-- Interviewer header --}}
                                        <div class="feedback-card">
                                            <div class="feedback-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="interviewer-avatar" style="width:48px;height:48px;font-size:18px;background:#3b82f6;">{{ $fbInitials }}</div>
                                                    <div>
                                                        <div class="fw-bolder fs-5 text-dark">{{ ucwords($fb['by_name'] ?? 'Interviewer') }}</div>
                                                        <div class="text-muted fs-7">{{ $fb['designation'] ?? 'Interviewer' }}</div>
                                                        <div class="text-muted fs-7"><i class="fas fa-clock me-1"></i>{{ !empty($fb['created_at']) ? date('d M Y, h:i A', strtotime($fb['created_at'])) : '--' }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($fbStatusId == 1)
                                                        <span class="result-badge result-selected"><i class="fas fa-check"></i> Selected</span>
                                                    @elseif($fbStatusId == 2)
                                                        <span class="result-badge result-not-selected"><i class="fas fa-times"></i> Not Selected</span>
                                                    @else
                                                        <span class="result-badge result-pending">{{ ucwords($fb['feedback_name'] ?? 'Submitted') }}</span>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1 justify-content-end mt-2">
                                                        @for($i=1;$i<=5;$i++)
                                                            <i class="fas fa-star {{ $i <= intval($fb['star_ratings'] ?? 0) ? 'star-filled' : 'star-empty' }}"></i>
                                                        @endfor
                                                        <span style="font-size:11px;color:#64748b;margin-left:4px;">({{ intval($fb['star_ratings'] ?? 0) }}/5)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 1. Interview Attendance --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">1</div><i class="fas fa-calendar-check text-muted me-1"></i> Interview Attendance</div>
                                            <span class="status-badge {{ $fbAttId == 1 ? 'status-completed' : '' }}" style="{{ $fbAttId == 1 ? '' : 'background:#fef3c7;color:#d97706;' }}">
                                                <i class="fas {{ $fbAttId == 1 ? 'fa-check-circle' : 'fa-info-circle' }}"></i> {{ $fbAttLabel ?: 'Not specified' }}
                                            </span>
                                        </div>

                                        {{-- 2. Technical Assessment --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">2</div><i class="fas fa-code text-muted me-1"></i> Technical Assessment</div>
                                            @if(!empty($techSkills))
                                                <table class="skills-table">
                                                    <thead><tr><th style="width:26%">Skill</th><th style="width:20%">Experience</th><th style="width:24%">Rating</th><th style="width:30%">Comment</th></tr></thead>
                                                    <tbody>
                                                        @foreach($techSkills as $sk)
                                                            <tr>
                                                                <td><div class="skill-name">{{ ucfirst($sk['skills'] ?? '') }}</div></td>
                                                                <td><div class="skill-exp">{{ $sk['experience'] ?: '--' }}</div></td>
                                                                <td><div class="interviewer-rating">@for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i <= intval($sk['ratings'] ?? 0) ? 'star-filled' : 'star-empty' }}"></i>@endfor</div></td>
                                                                <td><div class="skill-exp">{{ $sk['remarks'] ?: '--' }}</div></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="info-sub">No technical assessment provided.</div>
                                            @endif
                                        </div>

                                        {{-- 3. Behavioral Assessment --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">3</div><i class="fas fa-users text-muted me-1"></i> Behavioral Assessment</div>
                                            @if(!empty($behSkills))
                                                <table class="skills-table">
                                                    <thead><tr><th style="width:30%">Competency</th><th style="width:30%">Rating</th><th style="width:40%">Comment</th></tr></thead>
                                                    <tbody>
                                                        @foreach($behSkills as $sk)
                                                            <tr>
                                                                <td><div class="skill-name">{{ ucwords($sk['skills'] ?? '') }}</div></td>
                                                                <td><div class="interviewer-rating">@for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i <= intval($sk['ratings'] ?? 0) ? 'star-filled' : 'star-empty' }}"></i>@endfor</div></td>
                                                                <td><div class="skill-exp">{{ $sk['remarks'] ?: '--' }}</div></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="info-sub">No behavioral assessment provided.</div>
                                            @endif
                                        </div>

                                        @if($canViewSalary)
                                        {{-- 4. Compensation Assessment (hidden when the role has no Salary View permission) --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">4</div><i class="fas fa-money-bill-wave text-muted me-1"></i> Compensation Assessment</div>
                                            <div class="compensation-grid">
                                                <div><div class="info-label">Current CTC</div><div class="info-value">{{ $fb['current_ctc'] ?: '--' }}</div></div>
                                                <div><div class="info-label">Expected CTC</div><div class="info-value">{{ $fb['expected_ctc'] ?: '--' }}</div></div>
                                                <div><div class="info-label">Offered CTC</div><div class="info-value">{{ $fb['offered_ctc'] ?: '--' }}</div></div>
                                                <div><div class="info-label">Maximum Budget</div><div class="info-value">{{ $fb['max_budget'] ?: '--' }}</div></div>
                                                <div><div class="info-label">Notice Period</div><div class="info-value">{{ $noticeLabels[$fb['notice_period']] ?? ($fb['notice_period'] ?: '--') }}</div></div>
                                                <div><div class="info-label">Potential Joining Date</div><div class="info-value">{{ $fb['joining_date'] ? date('d M Y', strtotime($fb['joining_date'])) : '--' }}</div></div>
                                            </div>
                                            <div class="comp-options mt-3">
                                                <div><div class="info-label">Compensation Fit</div><div class="info-value">{{ $compFitLabels[$fb['compensation_fit']] ?? '--' }}</div></div>
                                                <div><div class="info-label">Joining Probability</div><div class="info-value">{{ $joinProbLabels[$fb['joining_probability']] ?? '--' }}</div></div>
                                                <div>
                                                    <div class="info-label">Risk Factors</div>
                                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                                                        @forelse($fbRisk as $rf)
                                                            <span class="tag" style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:6px;padding:2px 8px;font-size:11px;">{{ $riskLabels[$rf] ?? ucwords(str_replace('_',' ',$rf)) }}</span>
                                                        @empty
                                                            <span class="info-value">--</span>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!empty($fb['compensation_comments']))
                                                <div class="mt-3"><div class="info-label">Compensation Comments</div><div class="info-sub" style="margin-top:4px;line-height:1.5;">{{ $fb['compensation_comments'] }}</div></div>
                                            @endif
                                        </div>
                                        @endif

                                        {{-- 5. Interview Result --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">5</div><i class="fas fa-clipboard-check text-muted me-1"></i> Interview Result</div>
                                            <span class="result-badge {{ $fbResult=='selected' ? 'result-selected' : ($fbResult=='rejected' || $fbResult=='fake_proxy' ? 'result-not-selected' : 'result-pending') }}">
                                                {{ $resultLabels[$fbResult] ?? '--' }}
                                            </span>
                                        </div>

                                        {{-- 6. Overall Recommendation --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">6</div><i class="fas fa-star text-muted me-1"></i> Overall Recommendation</div>
                                            @if($fbReco)
                                                <span class="result-badge {{ in_array($fbReco,['strong_hire','hire']) ? 'result-selected' : (in_array($fbReco,['no_hire','strong_no_hire']) ? 'result-not-selected' : 'result-pending') }}">
                                                    <i class="fas {{ in_array($fbReco,['strong_hire','hire']) ? 'fa-thumbs-up' : (in_array($fbReco,['no_hire','strong_no_hire']) ? 'fa-thumbs-down' : 'fa-handshake') }}"></i>
                                                    {{ $recLabels[$fbReco] ?? '--' }}
                                                </span>
                                            @else
                                                <span class="info-value">--</span>
                                            @endif
                                        </div>

                                        {{-- 7. Final Comments --}}
                                        <div class="section-card">
                                            <div class="section-title"><div class="section-number">7</div><i class="fas fa-comment-dots text-muted me-1"></i> Final Comments</div>
                                            <div class="info-sub" style="line-height:1.6;">{{ $fb['message'] ?: '--' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer-note">
                        <div class="note-text"><i class="fas fa-info-circle"></i> You can edit only your feedback. Other interviewers' feedback is view-only.</div>
                        <button type="button" class="btn-close-footer" onclick="closeInterviewersModal()">Close</button>
                    </div>
                </div>
            </div>

            @php
                // Pre-fill the editable form with the CURRENT user's previously saved feedback (if any)
                $myEntryForm = null;
                if(!empty($myFeedbackId)){
                    foreach($feedbackMapped as $fid => $fb){ if($fid == $myFeedbackId){ $myEntryForm = $fb; break; } }
                }
                $mySkillPrefill = [];
                $myOverallRating = 0;
                $myFeedbackComment = '';
                $myAttendance = 0;
                if($myEntryForm){
                    $myOverallRating  = intval($myEntryForm['star_ratings'] ?? 0);
                    $myFeedbackComment = $myEntryForm['message'] ?? '';
                    $myAttendance     = intval($myEntryForm['candidateStatus'] ?? 0);
                    foreach(($myEntryForm['feedbackdata'] ?? []) as $sk){
                        $k = strtolower(trim($sk['skills'] ?? ''));
                        if($k !== ''){ $mySkillPrefill[$k] = ['rating' => intval($sk['ratings'] ?? 0), 'remark' => ($sk['remarks'] ?? '')]; }
                    }
                }
                // helpers to look up a saved skill's rating / remark by name
                $preRating = function($name) use ($mySkillPrefill){ return $mySkillPrefill[strtolower(trim($name))]['rating'] ?? 0; };
                $preRemark = function($name) use ($mySkillPrefill){ return $mySkillPrefill[strtolower(trim($name))]['remark'] ?? ''; };
                $attendancePrefill = $myEntryForm ? $myAttendance : intval(@$interviewSchedule->available_status);

                // Compensation / Result / Recommendation prefill (per-interviewer, from interview_feedback)
                $myResult       = $myEntryForm['interview_result'] ?? '';
                if ($myResult === '' && $myEntryForm) {
                    // Fall back to the legacy feedback id so the result still pre-selects on reopen
                    $fbToResult = [1=>'selected', 2=>'hold', 3=>'rejected', 4=>'fake_proxy', 5=>'feedback_pending'];
                    $myResult = $fbToResult[intval($myEntryForm['interviewStatus'] ?? 0)] ?? '';
                }
                $myReco         = $myEntryForm['overall_recommendation'] ?? '';
                $myCompFit      = $myEntryForm['compensation_fit'] ?? '';
                $myJoinProb     = $myEntryForm['joining_probability'] ?? '';
                $myNotice       = $myEntryForm['notice_period'] ?? '';
                $myCurrentCtc   = $myEntryForm['current_ctc'] ?? '';
                $myExpectedCtc  = $myEntryForm['expected_ctc'] ?? '';
                $myOfferedCtc   = $myEntryForm['offered_ctc'] ?? '';
                $myMaxBudget    = $myEntryForm['max_budget'] ?? '';
                $myJoiningDate  = $myEntryForm['joining_date'] ?? '';
                $myCompComments = $myEntryForm['compensation_comments'] ?? '';
                $myRiskArr      = !empty($myEntryForm['risk_factors']) ? array_filter(array_map('trim', explode(',', $myEntryForm['risk_factors']))) : [];
            @endphp

            <!-- Main Feedback Form -->
            <div id="feedbackFormWrapper" style="display:none;">
                <div id="print-area">
                    <div class="top-bar no-print">
                        <div class="page-title">
                            <i class="fas fa-clipboard-list text-muted me-2"></i>
                            Interview Feedback
                            <span class="by-text">by <span class="by-name">{{ ucwords($currentUserName ?: (@$interviewSchedule->upload_by_name ?? 'Interviewer')) }}</span></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button type="button" class="btn-download-report" onclick="viewJobJD({{ (int) (@$interviewSchedule->interview_job_id ?? 0) }})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                View JD
                            </button>
                            <button type="button" class="btn-download-report" onclick="viewCandidateResume({{ (int) (@$interviewSchedule->job_application_id ?? 0) }})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                View Resume
                            </button>
                            <button type="button" class="btn-download-report" onclick="downloadCurrentReport({{ (int) ($myFeedbackId ?? 0) }})">
                                <i class="fas fa-download"></i> Download Report
                            </button>
                            <button type="button" class="btn-feedback-close" onclick="closeInterviewersModal()" title="Close" aria-label="Close">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="feedback-card">
                        <div class="feedback-header">
                            <div class="hero-flex">
                                <div class="hero-candidate">
                                    <div class="candidate-avatar">{{ $candidateInitial }}</div>
                                    <div style="min-width:0;">
                                        <div class="hero-name">{{ ucwords(@$interviewSchedule->full_name) }}</div>
                                        <div class="hero-meta"><i class="fas fa-map-marker-alt me-1"></i> {{ @$interviewSchedule->location ?? 'Bangalore, India' }}</div>
                                        <div class="hero-meta"><i class="fas fa-briefcase me-1"></i> {{ @$interviewSchedule->experience ?? '5.4' }} Years Experience</div>
                                    </div>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-id-badge"></i></div><div><div class="info-label">Position</div><div class="info-value">{{ ucwords(@$interviewSchedule->job_title) }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-hashtag"></i></div><div><div class="info-label">Job ID</div><div class="info-value">{{ @$interviewSchedule->job_id ?? 'JR-2026-1045' }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-building"></i></div><div><div class="info-label">Company</div><div class="info-value">{{ ucwords(@$interviewSchedule->client_name) }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-layer-group"></i></div><div><div class="info-label">Stage</div><div class="info-value">{{ @$interviewSchedule->master_status_name ?? 'L2 Technical' }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-video"></i></div><div><div class="info-label">Interview Type</div><div class="info-value">{{ ucwords(@$interviewSchedule->mode_name) }}</div><div class="info-sub">{{ ucwords(@$interviewSchedule->mode_lavel_name) }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-calendar-alt"></i></div><div><div class="info-label">Interview Date &amp; Time</div><div class="info-value">{{ @$schedule_date }}</div><div class="info-sub">{{ @$schedule_time }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-user-tie"></i></div><div><div class="info-label">Interviewer</div><div class="info-value">{{ ucwords(@$interviewSchedule->interviewers ?? 'Swapnil') }}</div><div class="info-sub">{{ @$interviewSchedule->interviewer_designation ?? 'Senior Technical Lead' }} | {{ ucwords(@$interviewSchedule->client_name) }}</div></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fas fa-user"></i></div><div><div class="info-label">Recruiter (Ownership)</div><div class="info-value">{{ ucwords(@$interviewSchedule->upload_by_name) }}</div><div class="info-sub">{{ @$interviewSchedule->company_name }}</div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 1. Interview Attendance -->
                    <div class="section-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="section-title">
                                <div class="section-number">1</div>
                                <i class="fas fa-calendar-check text-muted me-1"></i>
                                Interview Attendance
                            </div>
                            <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span>
                        </div>
                        <div class="radio-card-group">
                            @php
                                // Relabel only — underlying ids/values stay the same (1=Available, 2=Not Available, 4=Cancel Interview)
                                $attLabelMapForm = [1=>'Attended', 2=>'No Show', 3=>'Rescheduled', 4=>'Cancelled by Company', 6=>'Other Reason', 7=>'Cancelled by Candidate'];
                            @endphp
                            @foreach ($available_status as $c_status)
                                @php
                                    $row = (object) $c_status;
                                    $sel = '';
                                    if($attendancePrefill == $row->id){
                                        $sel = 'checked="checked"';
                                    }
                                    $attDisplay = $attLabelMapForm[$row->id] ?? ucwords($row->name);
                                @endphp
                                <div class="radio-card">
                                    <input type="radio" class="btn-check app_int_status" name="app_int_status" value="{{$row->id}}" id="app_int_status{{$row->id}}" {{@$sel}}>
                                    <label for="app_int_status{{$row->id}}">
                                        <span class="radio-circle"></span>
                                        <span>{{ $attDisplay }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Technical Assessment -->
                    <div class="section-card skillAssessment">
                        <div class="section-title">
                            <div class="section-number">2</div>
                            <i class="fas fa-code text-muted me-1"></i>
                            Technical Assessment
                        </div>
                        @if($jobSkill)
                        <table class="skills-table">
                            <thead>
                                <tr>
                                    <th style="width: 22%">Skill</th>
                                    <th style="width: 18%">Experience Used</th>
                                    <th style="width: 28%">Rating</th>
                                    <th style="width: 32%">Comment (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobSkill as $skill_val)
                                    @php
                                        $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                        $experience = floatval(@$skillValue);
                                        $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;
                                        $expir = $formattedExperience . ' Years Experience';
                                        $preR = $preRating(ucfirst($skill_val->skill_name));
                                        $preRmk = $preRemark(ucfirst($skill_val->skill_name));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="skill-name">{{ ucfirst($skill_val->skill_name) }}</div>
                                            <div class="skill-exp">{{ $expir }}</div>
                                            <input type="hidden" name="skills_{{ $skill_val->id }}" value="{{ ucfirst($skill_val->skill_name) }}" />
                                            <input type="hidden" name="experience_{{ $skill_val->id }}" value="{{ $expir }}" />
                                            <input type="hidden" name="skillsId_{{ $skill_val->id }}" value="{{ $skill_val->id }}" />
                                        </td>
                                        <td><div class="skill-exp">{{ $formattedExperience }}+ Years</div></td>
                                        <td>
                                            <select name="rating_{{ $skill_val->id }}" class="rating-select">
                                                <option value="0" {{ $preR == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                                <option value="1" {{ $preR == 1 ? 'selected' : '' }}>1 - Bad</option>
                                                <option value="2" {{ $preR == 2 ? 'selected' : '' }}>2 - Poor</option>
                                                <option value="3" {{ $preR == 3 ? 'selected' : '' }}>3 - Average</option>
                                                <option value="4" {{ $preR == 4 ? 'selected' : '' }}>4 - Best</option>
                                                <option value="5" {{ $preR == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="remark_{{ $skill_val->id }}" class="comment-input" placeholder="Add comment (optional)" maxlength="200" value="{{ $preRmk }}">
                                            <div class="char-count">{{ strlen($preRmk) }}/200</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        <button type="button" class="add-btn"><i class="fas fa-plus"></i> Add Skill</button>
                    </div>

                    <!-- 3. Behavioral Assessment -->
                    <div class="section-card skillAssessment">
                        <div class="section-title">
                            <div class="section-number">3</div>
                            <i class="fas fa-users text-muted me-1"></i>
                            Behavioral Assessment
                        </div>
                        @php
                            $comR = $preRating('Communication Skills'); $comRmk = $preRemark('Communication Skills');
                            $psR  = $preRating('Problem Solving');       $psRmk  = $preRemark('Problem Solving');
                            $leadR = $preRating('Leadership');           $leadRmk = $preRemark('Leadership');
                            $culfR = $preRating('Cultural Fit');         $culfRmk = $preRemark('Cultural Fit');
                        @endphp
                        <table class="skills-table">
                            <thead>
                                <tr>
                                    <th style="width: 22%">Competency</th>
                                    <th style="width: 28%">Rating</th>
                                    <th style="width: 50%">Comment (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="skill-name">Communication</div>
                                        <input type="hidden" name="skills_com" value="Communication Skills" />
                                        <input type="hidden" name="experience_com" value="" />
                                    </td>
                                    <td>
                                        <select name="rating_com" class="rating-select">
                                            <option value="0" {{ $comR == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                            <option value="1" {{ $comR == 1 ? 'selected' : '' }}>1 - Bad</option>
                                            <option value="2" {{ $comR == 2 ? 'selected' : '' }}>2 - Poor</option>
                                            <option value="3" {{ $comR == 3 ? 'selected' : '' }}>3 - Average</option>
                                            <option value="4" {{ $comR == 4 ? 'selected' : '' }}>4 - Best</option>
                                            <option value="5" {{ $comR == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remark_com" class="comment-input" placeholder="Add comment (optional)" maxlength="200" value="{{ $comRmk }}">
                                        <div class="char-count">0/200</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="skill-name">Problem Solving</div>
                                        <input type="hidden" name="skills_ps" value="Problem Solving" />
                                        <input type="hidden" name="experience_ps" value="" />
                                    </td>
                                    <td>
                                        <select name="rating_ps" class="rating-select">
                                            <option value="0" {{ $psR == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                            <option value="1" {{ $psR == 1 ? 'selected' : '' }}>1 - Bad</option>
                                            <option value="2" {{ $psR == 2 ? 'selected' : '' }}>2 - Poor</option>
                                            <option value="3" {{ $psR == 3 ? 'selected' : '' }}>3 - Average</option>
                                            <option value="4" {{ $psR == 4 ? 'selected' : '' }}>4 - Best</option>
                                            <option value="5" {{ $psR == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remark_ps" class="comment-input" placeholder="Add comment (optional)" maxlength="200" value="{{ $psRmk }}">
                                        <div class="char-count">0/200</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="skill-name">Leadership</div>
                                        <input type="hidden" name="skills_lead" value="Leadership" />
                                        <input type="hidden" name="experience_lead" value="" />
                                    </td>
                                    <td>
                                        <select name="rating_lead" class="rating-select">
                                            <option value="0" {{ $leadR == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                            <option value="1" {{ $leadR == 1 ? 'selected' : '' }}>1 - Bad</option>
                                            <option value="2" {{ $leadR == 2 ? 'selected' : '' }}>2 - Poor</option>
                                            <option value="3" {{ $leadR == 3 ? 'selected' : '' }}>3 - Average</option>
                                            <option value="4" {{ $leadR == 4 ? 'selected' : '' }}>4 - Best</option>
                                            <option value="5" {{ $leadR == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remark_lead" class="comment-input" placeholder="Add comment (optional)" maxlength="200" value="{{ $leadRmk }}">
                                        <div class="char-count">0/200</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="skill-name">Cultural Fit</div>
                                        <input type="hidden" name="skills_culf" value="Cultural Fit" />
                                        <input type="hidden" name="experience_culf" value="" />
                                    </td>
                                    <td>
                                        <select name="rating_culf" class="rating-select">
                                            <option value="0" {{ $culfR == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                            <option value="1" {{ $culfR == 1 ? 'selected' : '' }}>1 - Bad</option>
                                            <option value="2" {{ $culfR == 2 ? 'selected' : '' }}>2 - Poor</option>
                                            <option value="3" {{ $culfR == 3 ? 'selected' : '' }}>3 - Average</option>
                                            <option value="4" {{ $culfR == 4 ? 'selected' : '' }}>4 - Best</option>
                                            <option value="5" {{ $culfR == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remark_culf" class="comment-input" placeholder="Add comment (optional)" maxlength="200" value="{{ $culfRmk }}">
                                        <div class="char-count">0/200</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="add-btn"><i class="fas fa-plus"></i> Add Competency</button>
                    </div>

                    @if($canViewSalary)
                    <!-- 4. Compensation Assessment (hidden when the role has no Salary View permission) -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="section-number">4</div>
                            <i class="fas fa-money-bill-wave text-muted me-1"></i>
                            Compensation Assessment
                        </div>
                        <div class="compensation-grid">
                            <div class="comp-input-group">
                                <label class="comp-label">Current CTC</label>
                                <div class="comp-input-wrap">
                                    <span class="prefix"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="text" name="current_ctc" placeholder="12,00,000" value="{{ $myCurrentCtc }}">
                                    <span class="suffix">INR / Annum</span>
                                </div>
                            </div>
                            <div class="comp-input-group">
                                <label class="comp-label">Expected CTC</label>
                                <div class="comp-input-wrap">
                                    <span class="prefix"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="text" name="expected_ctc" placeholder="15,00,000" value="{{ $myExpectedCtc }}">
                                    <span class="suffix">INR / Annum</span>
                                </div>
                            </div>
                            <div class="comp-input-group">
                                <label class="comp-label">Offered CTC (If Any)</label>
                                <div class="comp-input-wrap">
                                    <span class="prefix"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="text" name="offered_ctc" placeholder="-" value="{{ $myOfferedCtc }}">
                                    <span class="suffix">INR / Annum</span>
                                </div>
                            </div>
                            <div class="comp-input-group">
                                <label class="comp-label">Maximum Budget</label>
                                <div class="comp-input-wrap">
                                    <span class="prefix"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="text" name="max_budget" placeholder="14,00,000" value="{{ $myMaxBudget }}">
                                    <span class="suffix">INR / Annum</span>
                                </div>
                            </div>
                            <div class="comp-input-group">
                                <label class="comp-label">Notice Period</label>
                                <div class="comp-input-wrap">
                                    <select name="notice_period">
                                        <option value="">Select</option>
                                        <option value="15" {{ ($myNotice == '15') ? 'selected' : '' }}>15 Days</option>
                                        <option value="30" {{ ($myNotice == '30') ? 'selected' : '' }}>30 Days</option>
                                        <option value="60" {{ ($myNotice == '60') ? 'selected' : '' }}>60 Days</option>
                                        <option value="90" {{ ($myNotice == '90') ? 'selected' : '' }}>90 Days</option>
                                    </select>
                                </div>
                            </div>
                            <div class="comp-input-group">
                                <label class="comp-label">Potential Joining Date</label>
                                <div class="comp-input-wrap">
                                    <input type="date" name="joining_date" value="{{ $myJoiningDate }}">
                                    <span class="suffix"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="comp-options">
                            <div class="comp-option-group">
                                <div class="comp-option-title">Compensation Fit</div>
                                <label class="comp-option-item">
                                    <input type="radio" name="compensation_fit" value="within_budget" {{ ($myCompFit == 'within_budget') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label">Within Budget</span><span class="comp-option-desc">Expected within the approved budget.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="compensation_fit" value="slightly_above" {{ ($myCompFit == 'slightly_above') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label">Slightly Above Budget</span><span class="comp-option-desc">Up to 10% above the approved budget.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="compensation_fit" value="significantly_above" {{ ($myCompFit == 'significantly_above') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label">Significantly Above Budget</span><span class="comp-option-desc">More than 10% above the approved budget.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="compensation_fit" value="negotiable" {{ ($myCompFit == 'negotiable') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label">Negotiable</span><span class="comp-option-desc">Open to negotiation.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="compensation_fit" value="not_discussed" {{ ($myCompFit == 'not_discussed') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label">Not Discussed</span><span class="comp-option-desc">Compensation not discussed.</span></div>
                                </label>
                            </div>
                            <div class="comp-option-group">
                                <div class="comp-option-title">Joining Probability</div>
                                <label class="comp-option-item">
                                    <input type="radio" name="joining_probability" value="high" {{ ($myJoinProb == 'high') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label" style="color: #16a34a;">High (80 - 100%)</span><span class="comp-option-desc">High chance of joining.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="joining_probability" value="medium" {{ ($myJoinProb == 'medium') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label" style="color: #ca8a04;">Medium (50 - 80%)</span><span class="comp-option-desc">Moderate chance of joining.</span></div>
                                </label>
                                <label class="comp-option-item">
                                    <input type="radio" name="joining_probability" value="low" {{ ($myJoinProb == 'low') ? 'checked' : '' }}>
                                    <div class="comp-option-text"><span class="comp-option-label" style="color: #dc2626;">Low (< 50%)</span><span class="comp-option-desc">Low chance of joining.</span></div>
                                </label>
                            </div>
                            <div class="comp-option-group">
                                <div class="comp-option-title">Risk Factors <span style="font-weight: 400; color: #94a3b8;">(Select all that apply)</span></div>
                                <label class="comp-option-item">
                                    <input type="checkbox" id="select_all_risk" onchange="toggleAllRisks(this)">
                                    <span class="comp-option-label" style="font-weight: 600; color: #3b82f6;">Select All</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="multiple_offers" {{ (is_array($myRiskArr) && in_array('multiple_offers', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Holding Multiple Offers</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="counter_offer" {{ (is_array($myRiskArr) && in_array('counter_offer', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Counter Offer Expected</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="relocation" {{ (is_array($myRiskArr) && in_array('relocation', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Relocation Concern</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="salary_gap" {{ (is_array($myRiskArr) && in_array('salary_gap', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Salary Gap</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="notice_period" {{ (is_array($myRiskArr) && in_array('notice_period', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Long Notice Period</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="role_preference" {{ (is_array($myRiskArr) && in_array('role_preference', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Role / Domain Preference</span>
                                </label>
                                <label class="comp-option-item">
                                    <input type="checkbox" name="risk_factors[]" value="others" {{ (is_array($myRiskArr) && in_array('others', $myRiskArr)) ? 'checked' : '' }}>
                                    <span class="comp-option-label">Others</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="comp-label">Compensation Comments (Optional)</label>
                            <textarea name="compensation_comments" class="final-textarea" rows="2" placeholder="Candidate expects a hike of around 20-25%. Open to negotiation based on role and growth opportunity." maxlength="500">{{ $myCompComments }}</textarea>
                            <div class="char-count">{{ strlen($myCompComments ?? '') }}/500</div>
                        </div>
                    </div>
                    @endif

                    <!-- 5. Interview Result -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="section-number">5</div>
                            <i class="fas fa-clipboard-check text-muted me-1"></i>
                            Interview Result
                        </div>
                        <div class="radio-card-group">
                            <div class="radio-card">
                                <input type="radio" name="interview_result" value="selected" id="result_selected" {{ ($myResult == 'selected') ? 'checked' : '' }}>
                                <label for="result_selected">
                                    <span class="radio-icon" style="color: #16a34a;"><i class="fas fa-check-circle"></i></span>
                                    <span class="radio-circle"></span>
                                    <span>Selected</span>
                                </label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" name="interview_result" value="hold" id="result_hold" {{ ($myResult == 'hold') ? 'checked' : '' }}>
                                <label for="result_hold">
                                    <span class="radio-icon" style="color: #ca8a04;"><i class="fas fa-pause-circle"></i></span>
                                    <span class="radio-circle"></span>
                                    <span>Hold</span>
                                </label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" name="interview_result" value="rejected" id="result_rejected" {{ ($myResult == 'rejected') ? 'checked' : '' }}>
                                <label for="result_rejected">
                                    <span class="radio-icon" style="color: #dc2626;"><i class="fas fa-times-circle"></i></span>
                                    <span class="radio-circle"></span>
                                    <span>Rejected</span>
                                </label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" name="interview_result" value="fake_proxy" id="result_fake" {{ ($myResult == 'fake_proxy') ? 'checked' : '' }}>
                                <label for="result_fake">
                                    <span class="radio-icon" style="color: #dc2626;"><i class="fas fa-user-secret"></i></span>
                                    <span class="radio-circle"></span>
                                    <span>Fake / Proxy</span>
                                </label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" name="interview_result" value="feedback_pending" id="result_pending" {{ (empty($myResult) || $myResult == 'feedback_pending') ? 'checked' : '' }}>
                                <label for="result_pending">
                                    <span class="radio-icon" style="color: #64748b;"><i class="fas fa-clock"></i></span>
                                    <span class="radio-circle"></span>
                                    <span>Feedback Pending</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Overall Recommendation -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="section-number">6</div>
                            <i class="fas fa-star text-muted me-1"></i>
                            Overall Recommendation <span class="text-danger">*</span>
                        </div>
                        <div class="recommendation-grid">
                            <div class="rec-card">
                                <input type="radio" name="overall_recommendation" value="strong_hire" id="rec_strong_hire" {{ ($myReco == 'strong_hire') ? 'checked' : '' }}>
                                <label for="rec_strong_hire">
                                    <div class="rec-icon"><i class="fas fa-thumbs-up" style="color: #16a34a;"></i></div>
                                    <div class="rec-title">Strong Hire</div>
                                    <div class="rec-desc">Outstanding candidate. Strongly recommended.</div>
                                </label>
                            </div>
                            <div class="rec-card">
                                <input type="radio" name="overall_recommendation" value="hire" id="rec_hire" {{ ($myReco == 'hire') ? 'checked' : '' }}>
                                <label for="rec_hire">
                                    <div class="rec-icon"><i class="fas fa-thumbs-up" style="color: #3b82f6;"></i></div>
                                    <div class="rec-title">Hire</div>
                                    <div class="rec-desc">Good candidate. Recommended for the role.</div>
                                </label>
                            </div>
                            <div class="rec-card">
                                <input type="radio" name="overall_recommendation" value="consider" id="rec_consider" {{ ($myReco == 'consider') ? 'checked' : '' }}>
                                <label for="rec_consider">
                                    <div class="rec-icon"><i class="fas fa-handshake" style="color: #ca8a04;"></i></div>
                                    <div class="rec-title">Consider</div>
                                    <div class="rec-desc">Average candidate. May fit with some reservations.</div>
                                </label>
                            </div>
                            <div class="rec-card">
                                <input type="radio" name="overall_recommendation" value="no_hire" id="rec_no_hire" {{ ($myReco == 'no_hire') ? 'checked' : '' }}>
                                <label for="rec_no_hire">
                                    <div class="rec-icon"><i class="fas fa-ban" style="color: #dc2626;"></i></div>
                                    <div class="rec-title">No Hire</div>
                                    <div class="rec-desc">Not recommended for the role.</div>
                                </label>
                            </div>
                            <div class="rec-card">
                                <input type="radio" name="overall_recommendation" value="strong_no_hire" id="rec_strong_no_hire" {{ ($myReco == 'strong_no_hire') ? 'checked' : '' }}>
                                <label for="rec_strong_no_hire">
                                    <div class="rec-icon"><i class="fas fa-thumbs-down" style="color: #991b1b;"></i></div>
                                    <div class="rec-title">Strong No Hire</div>
                                    <div class="rec-desc">Does not meet the requirements.</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Final Comments -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="section-number">7</div>
                            <i class="fas fa-comment-dots text-muted me-1"></i>
                            Final Comments <span class="text-muted" style="font-weight: 400;">(Optional)</span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="text-dark fw-bolder fs-6">Overall Rating</label>
                                <select name="rating" class="rating-select">
                                    <option value="0" {{ $myOverallRating == 0 ? 'selected' : '' }}>Rate (0 - 5)</option>
                                    <option value="1" {{ $myOverallRating == 1 ? 'selected' : '' }}>1 - Bad</option>
                                    <option value="2" {{ $myOverallRating == 2 ? 'selected' : '' }}>2 - Poor</option>
                                    <option value="3" {{ $myOverallRating == 3 ? 'selected' : '' }}>3 - Average</option>
                                    <option value="4" {{ $myOverallRating == 4 ? 'selected' : '' }}>4 - Best</option>
                                    <option value="5" {{ $myOverallRating == 5 ? 'selected' : '' }}>5 - Very Best</option>
                                </select>
                            </div>
                        </div>
                        <textarea name="feedback_comment" id="feedback_comment" class="final-textarea" rows="3" placeholder="Share any additional feedback, observations or suggestions." maxlength="1000">{{ $myFeedbackComment }}</textarea>
                        <div class="char-count" id="feedback_char_count">{{ strlen($myFeedbackComment) }}/1000</div>
                    </div>

                    <div class="form-actions no-print">
                        <button type="button" class="btn-save-draft" onclick="saveDraft()">
                            <i class="fas fa-save"></i> Save Draft
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <a href="javascript:void(0);" id="app_interview_schedule_reschedule" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#interview_schedule" rel="{{@$interviewSchedule->wise_con_id}}" data-bs-dismiss="modal">Rescheduled</a>
                            <button type="button " id="submit-frm-btn" class="btn-submit">
                                <span class="sfb-label"><i class="fas fa-paper-plane"></i> Submit Feedback</span>
                                <span class="sfb-progress" style="display:none;"><span class="sfb-spinner"></span> Saving feedback...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Ensure FontAwesome is present in <head> (a <link> injected into the body via AJAX can be unreliable)
    (function () {
        if (!document.getElementById('fa6-cdn-head')) {
            var l = document.createElement('link');
            l.id = 'fa6-cdn-head';
            l.rel = 'stylesheet';
            l.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';
            document.head.appendChild(l);
        }
    })();

    // Permission: only the job captain (SPOC) and panel interviewers may give feedback
    window.canGiveFeedback = {{ ($canGiveFeedback ?? false) ? 'true' : 'false' }};
    window.fbFeedbackCount = {{ (int) count($feedbackMapped ?? []) }};

    $(document).ready(function() {
        $('#interviewersModal').addClass('active');
        backToInterviewersGrid(); // ensure grid view is the default
        // View-only users with nothing submitted get a clear heads-up
        if (!window.canGiveFeedback && window.fbFeedbackCount === 0) {
            setTimeout(function () {
                showFeedbackWarnToast('Feedback not submitted yet — you are not allowed to submit feedback for this interview.');
            }, 400);
        }
    });

    function closeInterviewersModal() {
        $('#interviewersModal').removeClass('active');
        // also close the outer Bootstrap feedback modal so nothing empty is left behind
        $('#interview_feedback .data-bs-dismiss').first().trigger('click');
    }

    function enterFeedbackForm() {
        if (!window.canGiveFeedback) {
            showFeedbackWarnToast('You are not allowed to submit feedback. Only the job captain and interviewers can give feedback.');
            return;
        }
        $('#interviewersModal').removeClass('active');
        $('#feedbackFormWrapper').fadeIn(200, applyAttendanceGate);
    }

    // Sections 2-7 are only usable when Interview Attendance = "Attended" (value 1).
    // Any other attendance option disables them (no skill/feedback needed).
    function applyAttendanceGate() {
        var $form = $('#feedbackFormWrapper');
        if (!$form.length) return;
        var v = $form.find('input[name="app_int_status"]:checked').val();
        var attended = (String(v) === '1');

        // Hide the "Rescheduled" action button when attendance = Attended.
        $('#app_interview_schedule_reschedule').toggle(!attended);

        var $rest = $form.find('.section-card').slice(1); // skip section 1 (Attendance)
        if (attended) {
            $rest.css({ opacity: '', pointerEvents: '' });
            $rest.find('input, textarea, select, button').prop('disabled', false);
        } else {
            $rest.css({ opacity: '0.45', pointerEvents: 'none' });
            $rest.find('input, textarea, select, button').prop('disabled', true);
        }
    }
    $(document).off('change.attgate').on('change.attgate', '#feedbackFormWrapper input[name="app_int_status"]', applyAttendanceGate);

    // Show one interviewer's saved, read-only feedback inside the popup
    window.currentDetailFid = 0;
    function viewFeedbackDetail(fid) {
        window.currentDetailFid = parseInt(fid) || 0; // remember which interviewer is being viewed (for per-person download)
        $('#interviewersGridView').hide();
        $('.fb-detail-panel').hide();
        var $panel = $('#fbDetail_' + fid);
        if ($panel.length) {
            $panel.show();
        }
        $('#feedbackDetailView').show();
    }

    function backToInterviewersGrid() {
        $('#feedbackDetailView').hide();
        $('.fb-detail-panel').hide();
        $('#interviewersGridView').show();
    }

    function remindInterviewer(name) {
        Swal.fire({ title: 'Reminder Sent', text: 'A reminder has been sent to ' + name, icon: 'success', confirmButtonText: 'OK', customClass: { confirmButton: 'btn btn-primary btn-sm' } });
    }

    document.querySelectorAll('.comment-input').forEach(input => {
        input.addEventListener('input', function() {
            const max = this.getAttribute('maxlength') || 200;
            const count = this.value.length;
            const counter = this.parentElement.querySelector('.char-count');
            if(counter) counter.textContent = count + '/' + max;
        });
    });

    // Golden stars next to each rating dropdown (stars only are gold; dropdown text stays normal)
    function ratingStarsHtml(v) {
        v = parseInt(v) || 0;
        var h = '';
        for (var i = 1; i <= 5; i++) { h += '<span class="' + (i <= v ? 'on' : 'off') + '">&#9733;</span>'; }
        return h;
    }
    document.querySelectorAll('.rating-select').forEach(function (sel) {
        if (sel.previousElementSibling && sel.previousElementSibling.classList && sel.previousElementSibling.classList.contains('rating-stars')) {
            sel.previousElementSibling.innerHTML = ratingStarsHtml(sel.value);
            return; // already injected
        }
        var span = document.createElement('span');
        span.className = 'rating-stars';
        span.innerHTML = ratingStarsHtml(sel.value);
        sel.parentNode.insertBefore(span, sel);
        sel.addEventListener('change', function () { span.innerHTML = ratingStarsHtml(sel.value); });
    });

    var feedbackComment = document.getElementById('feedback_comment'); // var (not const) so re-injecting this view doesn't throw a redeclaration error
    if(feedbackComment) {
        feedbackComment.addEventListener('input', function() {
            const max = this.getAttribute('maxlength') || 1000;
            const count = this.value.length;
            const counter = document.getElementById('feedback_char_count');
            if(counter) counter.textContent = count + '/' + max;
        });
    }

    function toggleAllRisks(source) {
        document.querySelectorAll('input[name="risk_factors[]"]').forEach(cb => cb.checked = source.checked);
    }

    // Open the JD in a self-contained fixed overlay panel (cannot push/shatter the feedback modal)
    function viewJobJD(jobId) {
        jobId = parseInt(jobId) || 0;
        if (!jobId) { showFeedbackWarnToast('Job not found for this interview.'); return; }
        var url = "{{ route('service_company.jobs.viewmore', ':jid') }}".replace(':jid', jobId);
        if (typeof openJdPanel === 'function') { openJdPanel('Job Description', url); }
    }
    // Open the candidate profile in the same fixed overlay panel
    function viewCandidateResume(appId) {
        appId = parseInt(appId) || 0;
        if (!appId) { showFeedbackWarnToast('Candidate not found for this interview.'); return; }
        var url = "{{ route('service_company.job-applications.show', ':aid') }}".replace(':aid', appId);
        if (typeof openJdPanel === 'function') { openJdPanel('Candidate Profile', url); }
    }

    function downloadCurrentReport(feedId) {
        var id = $('#interview_id').val();
        if (!id) { Swal.fire({ text: 'Unable to find the interview to download.', icon: 'warning', confirmButtonText: 'Ok' }); return; }
        feedId = parseInt(feedId) || 0; // 0 = whole interview; a feedback id = that one person only
        var url = "{{ route('service_company.interview-schedule.skillshowfeedbackdownload', ['id'=>':id', 'feed'=>':feed']) }}";
        url = url.replace(':id', id).replace(':feed', feedId);
        // Server returns the PDF as an attachment, so this triggers a download without leaving the page
        window.location.href = url;
    }

    function saveDraft() {
        Swal.fire({ title: 'Draft Saved', text: 'Your feedback has been saved as draft.', icon: 'success', confirmButtonText: 'OK', customClass: { confirmButton: 'btn btn-primary btn-sm' } });
    }

    function setSubmitLoading(on) {
        var $b = $("#submit-frm-btn");
        $b.prop("disabled", on);
        $b.find(".sfb-label").css('display', on ? 'none' : '');
        $b.find(".sfb-progress").css('display', on ? 'inline-flex' : 'none');
    }

    // Success toast styled like the "Job published successfully!" notification (white card, green accent + check)
    // Shared toast builder with a manual close (X) button + auto-close
    function fbBuildToast(opts) {
        var wrap = document.getElementById('fbToastWrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'fbToastWrap';
            wrap.style.cssText = 'position:fixed;top:22px;right:22px;z-index:2147483000;display:flex;flex-direction:column;gap:10px;pointer-events:none;max-width:92vw;';
            document.body.appendChild(wrap);
        }
        var t = document.createElement('div');
        t.style.cssText = 'pointer-events:auto;min-width:270px;max-width:400px;background:#ffffff;border-radius:10px;box-shadow:0 10px 28px rgba(2,6,23,0.14);border:1px solid ' + opts.border + ';display:flex;align-items:flex-start;overflow:hidden;transform:translateX(120%);opacity:0;transition:transform .35s cubic-bezier(.22,1,.36,1),opacity .35s;';
        t.innerHTML =
            '<span style="width:5px;align-self:stretch;background:' + opts.accent + ';flex:0 0 5px;"></span>' +
            '<span style="flex:0 0 auto;width:26px;height:26px;border-radius:50%;background:' + opts.iconBg + ';display:inline-flex;align-items:center;justify-content:center;margin:14px 0 14px 13px;">' + opts.icon + '</span>' +
            '<span style="flex:1 1 auto;color:#1e293b;font-size:13px;font-weight:500;line-height:1.35;padding:14px 6px 14px 12px;">' + opts.message + '</span>' +
            '<button type="button" aria-label="Close" class="fb-toast-x" style="flex:0 0 auto;border:none;background:transparent;color:#94a3b8;cursor:pointer;font-size:18px;line-height:1;padding:10px 12px 10px 4px;">&times;</button>';
        wrap.appendChild(t);
        requestAnimationFrame(function () { t.style.transform = 'translateX(0)'; t.style.opacity = '1'; });
        var closed = false, timer;
        function dismiss() {
            if (closed) return; closed = true;
            clearTimeout(timer);
            t.style.transform = 'translateX(120%)'; t.style.opacity = '0';
            setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 400);
        }
        t.querySelector('.fb-toast-x').addEventListener('click', dismiss);
        timer = setTimeout(dismiss, opts.duration || 4000);
    }

    function showFeedbackSuccessToast(message) {
        fbBuildToast({ message: message, accent: '#16a34a', border: '#eef2f7', iconBg: '#dcfce7', duration: 3500,
            icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>' });
    }
    function showFeedbackWarnToast(message) {
        fbBuildToast({ message: message, accent: '#d97706', border: '#fde9c8', iconBg: '#fef3c7', duration: 6000,
            icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' });
    }
    $('#app_interview_schedule_reschedule').click(async function(e){
            $('#submit-frm-btn').click();
    });

    $('#submit-frm-btn').click(async function(e){
        // Show the loader immediately so the click is acknowledged
        setSubmitLoading(true);
        const connected = await isInternetAvailableSave();
        if (!connected) {
            setSubmitLoading(false);
            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning btn-sm" } });
            return;
        }
        var form = $('#ajax-form');
        var formData = form.serialize();
        var id = $('#interview_id').val();
        var url = "{{ route('service_company.interview-schedule.updateInterview',':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'POST', url: url,
            data: formData,
            success: function (response) {
                if (response.status == "success") {
                    // Sidebar notification styled like the "Job published successfully!" toast
                    showFeedbackSuccessToast('Interview feedback submitted successfully! 🎉');
                    var page = $('#pagination .active_disabled').data('page');
                    if (typeof fetchCandidates === 'function') fetchCandidates(page);
                    $(".data-bs-dismiss").click(); // close the popup
                    setSubmitLoading(false);
                } else {
                    setSubmitLoading(false);
                    var emsg = (response && response.msg) ? response.msg : 'Could not submit feedback. Please try again.';
                    if (typeof showToastr === 'function') { showToastr('error', emsg, 'Error'); }
                    else { Swal.fire({ title: "Error", text: emsg, icon: "error", confirmButtonText: "Ok" }); }
                }
            },
            error: function() {
                setSubmitLoading(false);
                if (typeof showToastr === 'function') { showToastr('error', 'Server error while saving feedback. Please try again.', 'Error'); }
            }
        });
    });

    async function isInternetAvailableSave() {
        try {
            await fetch("https://www.gstatic.com/generate_204", { method: "GET", cache: "no-cache", mode: "no-cors" });
            return true;
        } catch (error) { return false; }
    }
    // Rescheduled button: close current modal first, then open #interview_schedule with AJAX content
    $(document).off('click.reschedule').on('click.reschedule', '#app_interview_schedule_reschedule', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var conId    = $(this).attr('rel');
        var $current = $(this).closest('.modal');

        function openScheduleModal() {
            if (!conId) return;
            var url      = "{{ route('service_company.job-applications.create-schedule', ':id') }}".replace(':id', conId);
            var loader   = "{{ asset('assets/images/Loading2.gif') }}";
            $('#app_interview_schedule_content').html('<img src="' + loader + '" height="35px" width="100%">');
            // $('#interview_schedule').modal('show');
            (function(){ var _el=document.getElementById('interview_schedule_drawer'); if(!_el)return; if(!_el.classList.contains('drawer'))_el.classList.add('drawer','drawer-end'); _el.classList.add('drawer-on'); _el.style.transform='none'; })();
              window.isdShellSetStep('main');
            $.get(url, function(resp) {
                $('#app_interview_schedule_content').html(resp.view || resp);
            });
        }

        if ($current.length && $current.hasClass('show')) {
            $current.one('hidden.bs.modal', openScheduleModal);
            $current.modal('hide');
        } else {
            // Feedback is shown in custom overlay, not a Bootstrap modal
            $('#interviewersModal').removeClass('active');
            $('#feedbackFormWrapper').hide();
            setTimeout(openScheduleModal, 300);
        }
    });
</script>