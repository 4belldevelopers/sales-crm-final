@php 
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
@endphp 

<style>
.test-fwd-container { font-family: 'Poppins', sans-serif; }
.test-fwd-badge { background: #ECFDF5; border: 1px solid #10B981; color: #047857; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
.test-fwd-card { border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px; background: #FFFFFF; transition: all 0.2s ease; margin-bottom: 8px; }
.test-fwd-card:hover { border-color: #10B981; }
.test-fwd-roster-badge { font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 600; }
</style>

<div class="test-fwd-container p-5">
    {{-- Header Banner: Clearly marks this as an Isolated Test Environment --}}
    <div class="d-flex align-items-center justify-content-between pb-4 border-bottom mb-4">
        <div>
            <div class="test-fwd-badge mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                ISOLATED TEST FORWARD RESUME PIPELINE
            </div>
            <h5 class="fw-bolder text-gray-900 mb-0">Next-Gen Client WASM + Direct Cloudflare R2 Delivery</h5>
            <div class="text-muted fs-7">0% Server CPU · 0% MySQL Load · Zero Local Disk Writes · Auto R2 Cleanup</div>
        </div>
        <button type="button" class="btn btn-sm btn-icon btn-light-danger" id="kt_test_fwd_resume_drawer_close" onclick="closeTestFwdResumeDrawer()">
            <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/><rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/></svg>
            </span>
        </button>
    </div>

    {{-- Step 1: Format Selector --}}
    <div class="mb-4">
        <label class="form-label fw-bold text-gray-700 fs-7">Select Resume Attachment Format</label>
        <select class="form-select form-select-solid fs-7" id="test_fwd_resume_format">
            <option value="1" data-desc="Attach original candidate resume directly from storage.">Option 1: Original Candidate Resume</option>
            <option value="5" selected data-desc="Compile comprehensive executive candidate dossier with skill matrix, ratings, and branding.">Option 5: Executive Prescreen Report (Client WASM)</option>
            <option value="49" data-desc="Compile standard branded Jeroworks vector resume with contact redaction.">Option 49: Jeroworks Branded Vector Resume</option>
        </select>
        <div class="form-text fs-8 text-muted mt-1" id="test_fwd_format_hint">Compiles in browser RAM and uploads directly to Cloudflare R2 bucket.</div>
    </div>

    {{-- Candidate Roster & Real-time Compilation Status --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label fw-bold text-gray-700 fs-7 mb-0">Selected Candidates Roster (Max 5)</label>
            <span class="badge badge-light-primary fs-8 fw-bolder" id="test_fwd_candidate_count">{{ count($applicationData ?? []) }} Candidates</span>
        </div>

        {{-- Overall Progress Container --}}
        <div id="test_fwd_progress_wrap" class="p-3 bg-light-primary rounded border border-primary border-dashed mb-3" style="display:none;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fs-7 fw-bold text-primary" id="test_fwd_progress_status">Processing...</span>
                <span class="fs-8 fw-bolder text-primary" id="test_fwd_progress_pct">0%</span>
            </div>
            <div class="progress h-6px bg-light-primary">
                <div class="progress-bar bg-primary" id="test_fwd_progress_bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div class="text-muted fs-9 mt-1" id="test_fwd_progress_detail">Initializing client-side document engine...</div>
        </div>

        <div id="test_fwd_roster_container">
            @foreach($applicationData ?? [] as $cand)
                <div class="test-fwd-card" id="test_cand_card_{{ $cand->id }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="symbol symbol-30px symbol-circle bg-light-primary">
                                <span class="symbol-label text-primary fw-bolder fs-8">{{ strtoupper(substr($cand->full_name ?? 'C', 0, 1)) }}</span>
                            </div>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">{{ $cand->full_name ?? 'Candidate' }}</div>
                                <div class="text-muted fs-8">{{ $cand->current_designation ?? 'Professional' }} · {{ $cand->experience ?? 'Experience' }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="test-fwd-roster-badge bg-light-warning text-warning" id="test_cand_status_{{ $cand->id }}">Pending Compilation</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Step 2: Recipient Details --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <label class="form-label fw-bold text-gray-700 fs-7 mb-0 required">Send To (Email Address)</label>
            <button type="button" class="btn btn-xs btn-link text-primary fw-bolder p-0" id="test_btn_add_quick_contact">Quick Add Contact</button>
        </div>
        <input type="text" class="form-control form-control-solid fs-7" id="test_to_email" placeholder="client@company.com, manager@domain.com" />
        <div class="form-text fs-8 text-muted">Separate multiple emails with commas.</div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold text-gray-700 fs-7">CC (Carbon Copy)</label>
        <input type="text" class="form-control form-control-solid fs-7" id="test_cc_email" placeholder="recruiter@team.com" />
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold text-gray-700 fs-7 required">Email Subject</label>
        <input type="text" class="form-control form-control-solid fs-7" id="test_email_subject" value="Forwarded Candidate Profiles: {{ !empty($applicationData[0]->full_name) ? $applicationData[0]->full_name : 'Profiles' }}" />
    </div>

    {{-- Step 3: Message Body --}}
    <div class="mb-4">
        <label class="form-label fw-bold text-gray-700 fs-7">Message Content</label>
        <textarea class="form-control form-control-solid fs-7" id="test_email_body" rows="6">Dear Hiring Team,

Please review the attached pre-screened candidate profile(s) for your open requisition. The vector resumes and executive prescreen dossiers are attached for your immediate evaluation.

Best regards,
{{ auth()->user()->name ?? 'Recruitment Team' }}
{{ $companyName ?? 'JERO Hiring Partner' }}</textarea>
    </div>

    {{-- Step 4: Actions --}}
    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
        <button type="button" class="btn btn-sm btn-light" onclick="closeTestFwdResumeDrawer()">Cancel</button>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light-primary fw-bolder" id="test_btn_recompile" onclick="startTestCompilation()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                Recompile PDFs
            </button>
            <button type="button" class="btn btn-sm btn-success fw-bolder" id="test_btn_send" onclick="sendTestForwardResume()">
                <span class="indicator-label d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Send Test Email (Direct R2)
                </span>
                <span class="indicator-progress" style="display:none;">
                    <span class="spinner-border spinner-border-sm align-middle me-2"></span>
                    <span id="test_btn_spinner_text">Sending...</span>
                </span>
            </button>
        </div>
    </div>
</div>

{{-- Load the Isolated Document Engines --}}
@include('service_company.cvtracker._forward_resume_engine')
@include('service_company.cvtracker._test_forward_resume_engine')

<script>
window.testCompiledAttachments = window.testCompiledAttachments || {};
window.testR2UploadedUuids = window.testR2UploadedUuids || [];
var _testGlobalToken = 0;

window.closeTestFwdResumeDrawer = function() {
    _testGlobalToken++;
    var drawerEl = document.getElementById('kt_test_fwd_resume_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { drawer.hide(); }
    }
    var drawerAptkEl = document.getElementById('kt_test_fwd_resume_drawer_aptk');
    if (drawerAptkEl && typeof KTDrawer !== 'undefined') {
        var drawerAptk = KTDrawer.getInstance(drawerAptkEl);
        if (drawerAptk) { drawerAptk.hide(); }
    }
    $('#application_test_fwd_resume_body_content').html('');
};

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

async function startTestCompilation() {
    var optId = parseInt($('#test_fwd_resume_format').val(), 10) || 5;
    var candidates = @json($applicationData ?? []);
    var companyData = {
        companyName: @json($companyName ?? 'Hiring Partner'),
        companyLogo: @json($companyLogo ?? '')
    };
    var jobSkills = @json($jobSkills ?? []);
    var thisToken = ++_testGlobalToken;

    $('#test_fwd_progress_wrap').show();
    $('#test_fwd_progress_bar').css('width', '5%');
    $('#test_fwd_progress_pct').text('5%');
    $('#test_fwd_progress_status').text('Compiling Vector Documents in Browser...');

    window.testCompiledAttachments[optId] = {};
    window.testR2UploadedUuids = [];

    var intentUrl = "{{ route('service_company.cvtracker.test_fwd_resume_intent') }}";
    var csrfToken = "{{ csrf_token() }}";
    var total = candidates.length;

    try {
        for (var i = 0; i < total; i++) {
            if (thisToken !== _testGlobalToken) return;

            var cand = candidates[i];
            var candName = cand.full_name || ('Candidate #' + (i + 1));
            var safeName = candName.replace(/[\\/:*?"<>|]+/g, ' ').replace(/\s+/g, '_').trim();
            var filename = 'Prescreen_Report_' + safeName + '.pdf';
            if (optId === 49) filename = 'Jeroworks_' + safeName + '.pdf';
            if (optId === 1) filename = 'Original_' + safeName + '.pdf';

            $('#test_cand_status_' + cand.id).attr('class', 'test-fwd-roster-badge bg-light-primary text-primary').text('Rendering WASM...');

            // Step 1: Vector Compilation via WASM
            var pdfBlob = await window.AgyTestForwardResumeEngine.compileCandidate(cand, optId, companyData, jobSkills, function(subMsg) {
                if (thisToken !== _testGlobalToken) return;
                $('#test_fwd_progress_detail').text(candName + ': ' + subMsg);
            });

            if (!pdfBlob) throw new Error('Failed to generate vector PDF for ' + candName);

            // Step 2: Direct-to-R2 Presigned Upload
            $('#test_cand_status_' + cand.id).attr('class', 'test-fwd-roster-badge bg-light-info text-info').text('Uploading to R2...');

            var uploadResult = await window.AgyTestForwardResumeEngine.uploadToR2(
                cand.id,
                pdfBlob,
                filename,
                intentUrl,
                csrfToken,
                function(upMsg) {
                    if (thisToken !== _testGlobalToken) return;
                    $('#test_fwd_progress_detail').text(candName + ': ' + upMsg);
                }
            );

            window.testCompiledAttachments[optId][cand.id] = {
                blob: pdfBlob,
                r2_uuid: uploadResult.attachment_uuid,
                filename: filename
            };
            window.testR2UploadedUuids.push(uploadResult.attachment_uuid);

            var pct = Math.round(((i + 1) / total) * 100);
            $('#test_fwd_progress_bar').css('width', pct + '%');
            $('#test_fwd_progress_pct').text(pct + '%');
            $('#test_cand_status_' + cand.id)
                .attr('class', 'test-fwd-roster-badge bg-light-success text-success')
                .text('✓ Ready in R2 (' + formatBytes(pdfBlob.size) + ')');
        }

        $('#test_fwd_progress_status').text('✓ All candidate resumes ready in Cloudflare R2!');
        $('#test_fwd_progress_detail').text('Zero server disk writes. Ready to send test email.');
    } catch(err) {
        console.error('[TestForwardResume Error]', err);
        $('#test_fwd_progress_status').text('Compilation or Upload Error');
        $('#test_fwd_progress_detail').text(err.message || 'An error occurred during vector rendering.');
        Swal.fire({
            title: 'Test Engine Notice',
            text: err.message || 'Could not complete client-side compilation or R2 upload.',
            icon: 'warning',
            confirmButtonColor: '#10B981'
        });
    }
}

async function sendTestForwardResume() {
    var toEmail = $.trim($('#test_to_email').val());
    var ccEmail = $.trim($('#test_cc_email').val());
    var subject = $.trim($('#test_email_subject').val());
    var body = $.trim($('#test_email_body').val());

    if (!toEmail) {
        Swal.fire({ title: 'Recipient Required', text: 'Please enter at least one recipient email address.', icon: 'warning', confirmButtonColor: '#10B981' });
        return;
    }

    if (!subject) {
        Swal.fire({ title: 'Subject Required', text: 'Please enter an email subject.', icon: 'warning', confirmButtonColor: '#10B981' });
        return;
    }

    if (!window.testR2UploadedUuids || window.testR2UploadedUuids.length === 0) {
        Swal.fire({
            title: 'Attachments Not Ready',
            text: 'Resumes are still uploading to R2. Please click "Recompile PDFs" or wait for upload to complete.',
            icon: 'info',
            confirmButtonColor: '#10B981'
        });
        return;
    }

    // UI Loading State
    var btn = $('#test_btn_send');
    btn.prop('disabled', true);
    btn.find('.indicator-label').hide();
    btn.find('.indicator-progress').show();

    var sendUrl = "{{ route('service_company.cvtracker.test_fwd_resume_send') }}";
    var candIds = @json(array_map('strval', $arr ?? []));

    try {
        var resp = await $.ajax({
            type: 'POST',
            url: sendUrl,
            data: {
                _token: "{{ csrf_token() }}",
                to_email: toEmail,
                cc_email: ccEmail,
                subject: subject,
                editor1: body,
                attachment_uuids: window.testR2UploadedUuids,
                arr: candIds
            }
        });

        if (resp.status === 'success') {
            Swal.fire({
                title: 'Test Email Dispatched!',
                html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                      '<p style="margin-bottom:8px;">Your test email with <strong>' + window.testR2UploadedUuids.length + ' vector resume(s)</strong> was queued successfully.</p>' +
                      '<p style="color:#059669;font-weight:600;margin:0;">Files will be automatically deleted from Cloudflare R2 upon delivery.</p>' +
                      '</div>',
                icon: 'success',
                confirmButtonColor: '#10B981'
            }).then(function() {
                closeTestFwdResumeDrawer();
            });
        } else {
            throw new Error(resp.message || 'Dispatch rejected by server.');
        }
    } catch(sendErr) {
        console.error('[TestForwardResume Send Error]', sendErr);
        Swal.fire({
            title: 'Send Error',
            text: (sendErr.responseJSON && sendErr.responseJSON.message) ? sendErr.responseJSON.message : sendErr.message,
            icon: 'error',
            confirmButtonColor: '#EF4444'
        });
    } finally {
        btn.prop('disabled', false);
        btn.find('.indicator-label').show();
        btn.find('.indicator-progress').hide();
    }
}

// Quick add contact helper
$('#test_btn_add_quick_contact').on('click', function() {
    var quickEmail = prompt('Enter recipient email address:');
    if (quickEmail && quickEmail.indexOf('@') !== -1) {
        var current = $.trim($('#test_to_email').val());
        if (current) {
            $('#test_to_email').val(current + ', ' + quickEmail);
        } else {
            $('#test_to_email').val(quickEmail);
        }
    }
});

// Format change triggers compilation
$('#test_fwd_resume_format').on('change', function() {
    startTestCompilation();
});

// Automatically trigger compilation on initial drawer load
$(document).ready(function() {
    setTimeout(startTestCompilation, 200);
});
</script>
