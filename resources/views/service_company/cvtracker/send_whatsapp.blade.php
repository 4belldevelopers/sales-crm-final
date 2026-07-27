@php
    use App\Helper\GeneralMethod;
    $applicationDataID = '';
    if ($applicationData) { $applicationDataID = $applicationData->id; }
    $isConnected = !empty($connectedNumberGet);
@endphp
<style>
/* ── WhatsApp Drawer Layout ── */
.wa-wrap { display:flex; flex-direction:column; height:100%; font-family:'Poppins',sans-serif; font-size:13px; background:#fff; }

/* Header */
.wa-header { display:flex; align-items:center; justify-content:space-between; padding:10px 20px; border-bottom:1px solid #e4e6ef; flex-shrink:0; gap:10px; }
.wa-header-left { display:flex; align-items:center; gap:10px; }
.wa-back-btn { background:none; border:none; cursor:pointer; padding:4px 6px; color:#5e6278; border-radius:6px; display:flex; align-items:center; }
.wa-back-btn:hover { background:#f1f1f4; }
.wa-title { font-size:14px; font-weight:700; color:#181c32; margin:0; }
.wa-status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
.wa-status-badge.connected { background:#e8f8f0; color:#17c653; border:1px solid #b4edcc; }
.wa-status-badge.not-connected { background:#fff0f0; color:#f1416c; border:1px solid #ffd0d9; }
.wa-status-dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
.wa-sync { font-size:11px; color:#a1a5b7; display:flex; align-items:center; gap:4px; }
.wa-header-right { display:flex; align-items:center; gap:6px; }
.wa-icon-btn { background:none; border:none; cursor:pointer; padding:5px; border-radius:6px; color:#5e6278; display:flex; align-items:center; }
.wa-icon-btn:hover { background:#f1f1f4; color:#181c32; }

/* Body */
.wa-body { display:flex; flex:1; overflow:hidden; }
.wa-form-panel { flex:0 0 62%; overflow-y:auto; padding:20px; border-right:1px solid #e4e6ef; }
.wa-preview-panel { flex:0 0 38%; overflow-y:auto; padding:20px; background:#f8f9fa; display:flex; flex-direction:column; gap:12px; }

/* Connection card */
.wa-conn-card { border:1px solid #e4e6ef; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px; margin-bottom:18px; }
.wa-conn-icon { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; }
.wa-conn-icon.connected { background:#e8f8f0; color:#17c653; }
.wa-conn-icon.not-connected { background:#fff0f0; color:#f1416c; }
.wa-conn-info { flex:1; }
.wa-conn-info strong { font-size:12px; font-weight:700; color:#181c32; display:block; }
.wa-conn-info span { font-size:11px; color:#7e8299; }
.wa-conn-actions { display:flex; gap:8px; flex-shrink:0; }
.wa-btn-remove { border:1px solid #f1416c; color:#f1416c; background:#fff; border-radius:6px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px; }
.wa-btn-remove:hover { background:#fff0f0; }
.wa-btn-connect { border:1px solid #17c653; color:#17c653; background:#fff; border-radius:6px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px; }
.wa-btn-connect:hover { background:#e8f8f0; }
.wa-btn-connect-action { background:#1b84ff; color:#fff; border:none; border-radius:6px; padding:5px 12px; font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px; }
.wa-btn-connect-action:hover { background:#056ee9; }

/* Form rows */
.wa-form-section { margin-bottom:16px; }
.wa-form-label { font-size:12px; font-weight:600; color:#181c32; margin-bottom:5px; display:flex; align-items:center; gap:5px; }
.wa-form-label span { font-weight:400; color:#7e8299; }
.wa-from-to-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px; }
.wa-input { width:100%; border:1px solid #e4e6ef; border-radius:7px; padding:8px 12px; font-size:12px; font-family:'Poppins',sans-serif; color:#181c32; background:#f9f9f9; outline:none; }
.wa-input:focus { border-color:#1b84ff; background:#fff; }
.wa-select { width:100%; border:1px solid #e4e6ef; border-radius:7px; padding:8px 12px; font-size:12px; font-family:'Poppins',sans-serif; color:#181c32; background:#fff; appearance:none; outline:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23a1a5b7' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:32px; }
.wa-to-row { position:relative; }
.wa-to-input-wrap { display:flex; gap:8px; align-items:center; }
.wa-to-input { flex:1; }
.wa-add-candidate-btn { width:34px; height:34px; border:1px solid #e4e6ef; border-radius:7px; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#7e8299; flex-shrink:0; }
.wa-add-candidate-btn:hover { border-color:#1b84ff; color:#1b84ff; }
.wa-candidate-tag { display:inline-flex; align-items:center; gap:6px; background:#f1f1f4; border-radius:20px; padding:3px 10px 3px 6px; margin-top:6px; font-size:11px; font-weight:500; color:#181c32; }
.wa-candidate-tag img { width:20px; height:20px; border-radius:50%; object-fit:cover; }
.wa-candidate-tag .wa-tag-avatar { width:20px; height:20px; border-radius:50%; background:#1b84ff; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; }
.wa-candidate-tag .wa-tag-remove { cursor:pointer; color:#a1a5b7; margin-left:2px; font-size:13px; line-height:1; }
.wa-candidate-tag .wa-tag-remove:hover { color:#f1416c; }
.wa-hint { font-size:11px; color:#a1a5b7; margin-top:4px; }

/* Message editor */
.wa-msg-toolbar { display:flex; align-items:center; gap:2px; border:1px solid #e4e6ef; border-bottom:none; border-radius:7px 7px 0 0; padding:6px 8px; background:#f9f9f9; flex-wrap:wrap; }
.wa-tb-btn { display:flex; align-items:center; gap:4px; padding:4px 8px; border:none; background:none; border-radius:5px; font-size:11px; font-weight:500; color:#5e6278; cursor:pointer; }
.wa-tb-btn:hover { background:#e4e6ef; }
.wa-tb-btn.ai { color:#7239ea; }
.wa-tb-btn.ai:hover { background:#f8f5ff; }
.wa-tb-divider { width:1px; height:18px; background:#e4e6ef; margin:0 2px; }
.wa-tb-right { margin-left:auto; display:flex; gap:4px; }
.wa-textarea { width:100%; border:1px solid #e4e6ef; border-top:none; border-radius:0 0 7px 7px; padding:10px 12px; font-size:12px; font-family:'Poppins',sans-serif; color:#181c32; resize:vertical; min-height:280px; outline:none; line-height:1.6; }
.wa-textarea:focus { border-color:#1b84ff; }
.wa-char-bar { display:flex; align-items:center; gap:12px; margin-top:5px; }
.wa-char-count { font-size:10px; color:#a1a5b7; }
.wa-read-time { display:flex; align-items:center; gap:4px; font-size:10px; color:#17c653; }

/* Preview panel */
.wa-preview-title { font-size:13px; font-weight:700; color:#181c32; margin-bottom:10px; }
.wa-phone-mock { border-radius:14px; overflow:hidden; border:2px solid #e4e6ef; background:#ece5dd; flex:1; }
.wa-phone-header { background:#075e54; padding:10px 14px; display:flex; align-items:center; gap:10px; }
.wa-phone-header .wa-back-arr { color:#fff; font-size:16px; cursor:pointer; }
.wa-phone-avatar { width:34px; height:34px; border-radius:50%; background:#25d366; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
.wa-phone-contact { flex:1; }
.wa-phone-contact-name { color:#fff; font-size:13px; font-weight:600; line-height:1.2; }
.wa-phone-contact-status { color:#b2dfdb; font-size:10px; }
.wa-phone-actions { display:flex; gap:10px; }
.wa-phone-actions svg { color:#fff; }
.wa-phone-body { padding:12px; min-height:200px; display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
.wa-bubble { background:#fff; border-radius:8px 0 8px 8px; padding:10px 12px; max-width:90%; box-shadow:0 1px 2px rgba(0,0,0,.1); }
.wa-bubble-text { font-size:11px; color:#111; line-height:1.6; white-space:pre-wrap; word-break:break-word; max-height:320px; overflow-y:auto; }
.wa-bubble-time { text-align:right; font-size:9px; color:#a1a5b7; margin-top:4px; display:flex; align-items:center; justify-content:flex-end; gap:3px; }
.wa-preview-caption { font-size:11px; color:#7e8299; text-align:center; margin-top:8px; }

/* Footer */
.wa-footer { border-top:1px solid #e4e6ef; padding:12px 20px; display:flex; justify-content:flex-end; flex-shrink:0; }
.wa-btn-send-whatsapp { background:#1b84ff; color:#fff; border:none; border-radius:7px; padding:9px 20px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; }
.wa-btn-send-whatsapp:hover { background:#056ee9; }
.wa-btn-send-whatsapp .wa-send-dropdown { border-left:1px solid rgba(255,255,255,.35); margin-left:4px; padding-left:8px; }
</style>

<div class="wa-wrap">

    {{-- ── Header ── --}}
    <div class="wa-header">
        <div class="wa-header-left">
            <button class="wa-back-btn" id="kt_send_whatsapp_showdetail_close" type="button" title="Close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <h2 class="wa-title">Send WhatsApp Message</h2>
            @if($isConnected)
                <span class="wa-status-badge connected">
                    <span class="wa-status-dot"></span> WhatsApp Connected
                </span>
            @else
                <span class="wa-status-badge not-connected">
                    <span class="wa-status-dot"></span> Not Connected
                </span>
            @endif
            <span class="wa-sync">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                Last synced 2 min ago
            </span>
        </div>
        <div class="wa-header-right">
            <button class="wa-icon-btn" type="button" title="Expand">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
            </button>
            <button class="wa-icon-btn" type="button" id="kt_send_whatsapp_showdetail_close2" title="Close">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="wa-body">

        {{-- Left: Form --}}
        <div class="wa-form-panel">
            <form id="form1">
                <input type="hidden" value="{{ $jobid }}" name="jobid" id="jobid">
                <input type="hidden" value="{{ $applicationDataID }}" name="applicationDataId" id="applicationDataId">

                {{-- Connection Status Card --}}
                <div class="wa-conn-card">
                    <div class="wa-conn-icon {{ $isConnected ? 'connected' : 'not-connected' }}">
                        <i class="fa fa-whatsapp"></i>
                    </div>
                    <div class="wa-conn-info">
                        @if($isConnected)
                            <strong>WhatsApp Connected</strong>
                            <span>You can now send WhatsApp messages to candidates.</span>
                        @else
                            <strong>No WhatsApp Business Connected</strong>
                            <span>Connect your WhatsApp Business account to send messages.</span>
                        @endif
                    </div>
                    <div class="wa-conn-actions">
                        <button type="button" class="wa-btn-remove deleteBtn sendwhatsapp_connected_{{ auth()->user()->id }} {{ $connectBtndnone }}" data-userid="{{ auth()->user()->id }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            Remove WhatsApp
                        </button>
                        @if($isConnected)
                            <button type="button" class="wa-btn-connect sendwhatsapp_connected_{{ auth()->user()->id }} {{ $sectionOnednone }}">
                                <i class="fa fa-whatsapp"></i> WhatsApp Connected
                            </button>
                        @else
                            <button type="button" class="wa-btn-connect-action sendwhatsapp_panel sendwhatsapp_connect_{{ auth()->user()->id }} sendwhatsapp_text_{{ auth()->user()->id }} {{ $sectionTwodnone }}" data-userid="{{ auth()->user()->id }}">
                                <i class="fa fa-whatsapp"></i> Connect Your WhatsApp
                            </button>
                        @endif
                    </div>
                </div>

                {{-- From / To row --}}
                <div class="wa-from-to-grid">
                    <div class="wa-form-section">
                        <div class="wa-form-label">From <span>(Your WhatsApp Number)</span></div>
                        <select class="wa-select" name="f_phone" id="f_phone_{{ auth()->user()->id }}">
                            <option value="{{ $connectedNumberGet ?? auth()->user()->mobile }}">
                                {{ $connectedNumberGet ?? auth()->user()->mobile }}
                            </option>
                        </select>
                    </div>
                    <div class="wa-form-section">
                        <div class="wa-form-label">To <span>(Candidate Mobile Number)</span></div>
                        <div class="wa-to-row">
                            <div class="wa-to-input-wrap">
                                <input class="wa-input wa-to-input" type="text" name="phone" id="phone" value="{{ $applicationData->phone }}" placeholder="+91 XXXXX XXXXX">
                                <button type="button" class="wa-add-candidate-btn" title="Add Candidate">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                </button>
                            </div>
                            @if($applicationData->full_name ?? false)
                            <div style="margin-top:6px;">
                                <span class="wa-candidate-tag">
                                    <span class="wa-tag-avatar">{{ strtoupper(substr($applicationData->full_name, 0, 1)) }}</span>
                                    {{ $applicationData->full_name }}
                                    <span class="wa-tag-remove">&times;</span>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Template --}}
                <div class="wa-form-section">
                    <div class="wa-form-label">Template <span>(Optional)</span></div>
                    <select name="templatewhatsapp" id="templatewhatsapp" class="wa-select">
                        <option value="">Select WhatsApp Template (Optional)</option>
                        @if (!empty($templateData))
                            @foreach ($templateData as $tdata)
                                <option value="{{ $tdata->id }}">
                                    {{ str_replace(['[{COMPANY_NAME}]','[{JOB_TITLE}]','[{CLIENT_NAME}]'], [$companyTemp, $job_title, $clientTemp], $tdata->display_name) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="wa-hint">You can send using approved template or type custom message.</div>
                </div>

                {{-- Message --}}
                <div class="wa-form-section">
                    <div class="wa-form-label">Message</div>
                    <div class="wa-msg-toolbar">
                        <button type="button" class="wa-tb-btn" id="wa-emoji-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                            Emoji
                        </button>
                        <span class="wa-tb-divider"></span>
                        <button type="button" class="wa-tb-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            Attachment
                        </button>
                        <span class="wa-tb-divider"></span>
                        <button type="button" class="wa-tb-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                            Variables
                        </button>
                        <span class="wa-tb-divider"></span>
                        <button type="button" class="wa-tb-btn ai">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            AI Assist
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="wa-tb-right">
                            <button type="button" class="wa-tb-btn" id="wa-undo-btn" title="Undo">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                            </button>
                            <button type="button" class="wa-tb-btn" id="wa-redo-btn" title="Redo">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-3.51"/></svg>
                            </button>
                        </div>
                    </div>
                    <textarea class="wa-textarea" id="editorwhatsapp" name="editorwhatsapp" placeholder="Type your WhatsApp message here..."></textarea>
                    <div class="wa-char-bar">
                        <span class="wa-char-count" id="wa-char-count">Characters: 0 / 4096</span>
                        <span class="wa-read-time">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span id="wa-read-time-val">Estimated read time: ~0 sec</span>
                        </span>
                    </div>
                </div>

            </form>
        </div>

        {{-- Right: Preview --}}
        <div class="wa-preview-panel">
            <div class="wa-preview-title">Message Preview</div>
            <div class="wa-phone-mock">
                <div class="wa-phone-header">
                    <span class="wa-back-arr">&#8592;</span>
                    <div class="wa-phone-avatar">{{ strtoupper(substr($applicationData->full_name ?? 'C', 0, 1)) }}</div>
                    <div class="wa-phone-contact">
                        <div class="wa-phone-contact-name">{{ $applicationData->full_name ?? 'Candidate' }}</div>
                        <div class="wa-phone-contact-status">online</div>
                    </div>
                    <div class="wa-phone-actions">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18C1.61 2.1 2.47 1 3.55 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.5a16 16 0 0 0 6 6l.87-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.5 16z"/></svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </div>
                </div>
                <div class="wa-phone-body" id="wa-preview-body">
                    <div class="wa-bubble">
                        <div class="wa-bubble-text" id="wa-preview-text">Your message will appear here...</div>
                        <div class="wa-bubble-time">
                            <span id="wa-preview-time">{{ now()->format('g:i A') }}</span>
                            <svg width="14" height="10" viewBox="0 0 16 11" fill="none"><path d="M1 6l3.5 3.5L9 4M6 6l3.5 3.5L15 2" stroke="#53bdeb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wa-preview-caption">This is how your message will appear in WhatsApp.</div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="wa-footer">
        <button type="button" class="wa-btn-send-whatsapp" name="btnSubmitSendMail" id="btnSubmitSendMail" value="btnSubmitSendMail">
            <i class="fa fa-whatsapp" style="font-size:15px;"></i>
            <span class="indicator-label">Send WhatsApp</span>
            <span class="indicator-progress" style="display:none;">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            <span class="wa-send-dropdown">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
        </button>
    </div>
</div>

<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script>

    // ── Helpers ──
    function stripHtml(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        return (tmp.textContent || tmp.innerText || '').replace(/ /g, ' ').replace(/\n{3,}/g, '\n\n').trim();
    }

    // ── Character count + preview ──
    var $waTa = document.getElementById('editorwhatsapp');
    var $waCount = document.getElementById('wa-char-count');
    var $waReadTime = document.getElementById('wa-read-time-val');
    var $waPreviewText = document.getElementById('wa-preview-text');

    function updateWaPreview() {
        var txt = $waTa ? $waTa.value : '';
        var len = txt.length;
        if ($waCount) $waCount.textContent = 'Characters: ' + len + ' / 4096';
        var wpm = 238;
        var words = txt.trim().split(/\s+/).filter(function(w){ return w; }).length;
        var secs = Math.max(0, Math.round((words / wpm) * 60));
        if ($waReadTime) $waReadTime.textContent = 'Estimated read time: ~' + secs + ' sec';
        // Preview shows plain text with line breaks preserved
        if ($waPreviewText) {
            $waPreviewText.textContent = txt || 'Your message will appear here...';
        }
    }

    if ($waTa) {
        $waTa.addEventListener('input', updateWaPreview);
        updateWaPreview();
    }

    // Undo / redo
    document.getElementById('wa-undo-btn') && document.getElementById('wa-undo-btn').addEventListener('click', function(){ document.execCommand('undo'); });
    document.getElementById('wa-redo-btn') && document.getElementById('wa-redo-btn').addEventListener('click', function(){ document.execCommand('redo'); });

    // Close button alias
    document.getElementById('kt_send_whatsapp_showdetail_close2') && document.getElementById('kt_send_whatsapp_showdetail_close2').addEventListener('click', function(){
        document.getElementById('kt_send_whatsapp_showdetail_close').click();
    });

    // ── Template change ──
    $('#templatewhatsapp').change(function() {
        var template = $(this).val();
        if (template > 0) {
            var id = template;
            var url = "{{ route('service_company.cvtracker.email_template', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var applicationDataId = $('#applicationDataId').val();
            var jobid = $('#jobid').val();
            $.ajax({
                type: 'POST', url: url,
                data: { '_token': token, '_method': 'POST', 'template': template, 'selectedApplicantListing': [applicationDataId], 'mailtype': 9, 'jobID': jobid },
                success: function(response) {
                    if (response.status == "success") {
                        // Strip HTML — WhatsApp messages are plain text
                        var plainText = stripHtml(response.template_description);
                        $('#editorwhatsapp').val(plainText);
                        updateWaPreview();
                    }
                }
            });
        }
    });

    // ── Send ──
    document.getElementById("btnSubmitSendMail").addEventListener("click", function() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-danger' },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send WhatsApp!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = "{{ url('/whatsapp/send') }}";
                var token = "{{ csrf_token() }}";
                var phone = $('#phone').val();
                var templatewhatsapp = $('#templatewhatsapp').val();
                var message = $('#editorwhatsapp').val();
                var userid = "{{ auth()->user()->id }}";
                var application_id = "{{ $applicationData->id }}";
                var application_email = "{{ $applicationData->email }}";
                var job_id = "{{ $applicationData->jobID }}";

                $('.indicator-label').hide();
                $('.indicator-progress').show();

                $.ajax({
                    type: 'POST', url: url,
                    data: { '_token': token, '_method': 'POST', 'phone': phone, 'userid': userid, 'application_id': application_id, 'application_email': application_email, 'job_id': job_id, 'templatewhatsapp': templatewhatsapp, 'message': message },
                    success: function(response) {
                        $('.indicator-label').show();
                        $('.indicator-progress').hide();
                        if (response.status === 'error' || response.status === 'coinCheck') {
                            Swal.fire("Failed", response.message, "warning").then(function(){ if(response.status==='coinCheck') $(".btn-active-light-danger").click(); });
                            return;
                        }
                        if (response.status === "success") {
                            swalWithBootstrapButtons.fire('Success!', response.message, 'success');
                            $(".btn-active-light-danger").click();
                        }
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire('Cancelled', 'Selected request is safe :)', 'error');
            }
        });
    });

</script>
