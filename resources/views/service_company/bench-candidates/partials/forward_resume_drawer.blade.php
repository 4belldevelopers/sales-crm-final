@php 
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $cand = $applicationData[0] ?? null;
    $candName = $cand->full_name ?? 'Candidate';
    $candDesig = $cand->current_designation ?? 'Technical Specialist';
    $candExp = $cand->experience ?? '--';
    $candSkills = $cand->skills ?? '--';
    $candLoc = $cand->location ?? ($cand->city_display ?? 'Open to Relocation');
    $candWorkMode = $cand->work_mode ?? 'Hybrid';
    $candNotice = $cand->notice ?? 'Immediate';
    $candEngagement = $cand->engagement_model ?? ($cand->employmentType ?? 'Contract');
    $candCommercials = $cand->commercials_formatted ?? 'Competitive / Negotiable';
    $senderName = $userdata->name ?? 'Recruitment Team';
    $senderDesig = $userdata->designation ?? (\Illuminate\Support\Facades\DB::table('users_profile')->where('user_id', $userdata->id ?? 0)->value('designation') ?: 'Technical Recruitment Lead');
    $senderEmail = $userdata->email ?? '';
    $senderPhone = !empty($userdata->mobile) ? $userdata->mobile : (!empty($userdata->whatsapp_number) ? $userdata->whatsapp_number : '--');
    $compName = $companyName ?? 'Hiring Partner';
    $currentDate = now()->format('d M Y');

    // 1. Updated Inhouse Talent list + Tracker
    $tplBody1 = '<p>Hi [HR Name],</p>'
        . '<p>Sharing our current in-house bench talent pool for your review. The below professionals are available for consideration against your current or upcoming hiring requirements.</p>'
        . '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;font-family:Arial,Helvetica,sans-serif;font-size:12.5px;color:#1E293B;border:1px solid #CBD5E1;margin:14px 0;">'
        . '<thead>'
        . '<tr style="background:#F1F5F9;color:#334155;text-align:left;font-size:11.5px;font-weight:700;border-bottom:2px solid #CBD5E1;">'
        . '<th style="padding:8px 6px;text-align:center;width:35px;border:1px solid #CBD5E1;">#</th>'
        . '<th style="padding:8px 8px;border:1px solid #CBD5E1;">Candidate</th>'
        . '<th style="padding:8px 6px;text-align:center;border:1px solid #CBD5E1;">Exp.</th>'
        . '<th style="padding:8px 8px;border:1px solid #CBD5E1;">Designation</th>'
        . '<th style="padding:8px 8px;border:1px solid #CBD5E1;">Primary Skills</th>'
        . '<th style="padding:8px 8px;border:1px solid #CBD5E1;">Location</th>'
        . '<th style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">Work Mode</th>'
        . '<th style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">Availability</th>'
        . '<th style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">Engagement</th>'
        . '<th style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">Commercials</th>'
        . '<th style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">Profile</th>'
        . '</tr>'
        . '</thead>'
        . '<tbody>'
        . '<tr style="background:#FFFFFF;border-bottom:1px solid #E2E8F0;">'
        . '<td style="padding:8px 6px;text-align:center;font-weight:700;color:#0F766E;border:1px solid #CBD5E1;">01</td>'
        . '<td style="padding:8px 8px;font-weight:600;color:#0F172A;border:1px solid #CBD5E1;">' . htmlspecialchars($candName, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 6px;text-align:center;border:1px solid #CBD5E1;">' . htmlspecialchars($candExp, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;border:1px solid #CBD5E1;">' . htmlspecialchars($candDesig, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;border:1px solid #CBD5E1;">' . htmlspecialchars($candSkills, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;border:1px solid #CBD5E1;">' . htmlspecialchars($candLoc, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;"><span style="background:#EFF6FF;color:#1D4ED8;padding:2px 6px;border-radius:4px;font-size:11px;">' . htmlspecialchars($candWorkMode, ENT_QUOTES) . '</span></td>'
        . '<td style="padding:8px 8px;text-align:center;color:#059669;font-weight:600;border:1px solid #CBD5E1;">' . htmlspecialchars($candNotice, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">' . htmlspecialchars($candEngagement, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;">' . htmlspecialchars($candCommercials, ENT_QUOTES) . '</td>'
        . '<td style="padding:8px 8px;text-align:center;border:1px solid #CBD5E1;"><strong style="color:#0D9488;">[Attached CV]</strong></td>'
        . '</tr>'
        . '</tbody>'
        . '</table>'
        . '<p><strong>Resumes:</strong> Attached for the corresponding candidates.</p>'
        . '<p>All profiles are currently available with us and can be evaluated for relevant current or upcoming requirements.</p>'
        . '<p>Please let us know if any profile is relevant, and we will coordinate the next steps.</p>'
        . '<p>Regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($senderDesig, ENT_QUOTES) . '<br>'
        . htmlspecialchars($senderEmail, ENT_QUOTES) . ' | ' . htmlspecialchars($senderPhone, ENT_QUOTES) . '<br>'
        . '<strong>' . htmlspecialchars($compName, ENT_QUOTES) . '</strong></p>';

    // 2. Immediate Deployment Hotlist (Executive Spotlight)
    $tplBody2 = '<p>Dear [HR Name],</p>'
        . '<p>I would like to present <strong>' . htmlspecialchars($candName, ENT_QUOTES) . '</strong> (' . htmlspecialchars($candDesig, ENT_QUOTES) . '), who is currently available in our in-house bench pool for <strong>immediate onboarding</strong> (Zero-day notice).</p>'
        . '<div style="background:#F8FAFC;border:1px solid #E2E8F0;border-left:4px solid #0D9488;border-radius:6px;padding:12px 16px;margin:12px 0;">'
        . '<p style="margin:0 0 6px 0;"><strong>Candidate Spotlight:</strong></p>'
        . '<ul style="margin:0;padding-left:18px;">'
        . '<li><strong>Full Name:</strong> ' . htmlspecialchars($candName, ENT_QUOTES) . '</li>'
        . '<li><strong>Role &amp; Designation:</strong> ' . htmlspecialchars($candDesig, ENT_QUOTES) . '</li>'
        . '<li><strong>Total Experience:</strong> ' . htmlspecialchars($candExp, ENT_QUOTES) . '</li>'
        . '<li><strong>Core Technical Stack:</strong> ' . htmlspecialchars($candSkills, ENT_QUOTES) . '</li>'
        . '<li><strong>Current Location:</strong> ' . htmlspecialchars($candLoc, ENT_QUOTES) . ' (' . htmlspecialchars($candWorkMode, ENT_QUOTES) . ')</li>'
        . '<li><strong>Availability:</strong> <span style="color:#059669;font-weight:600;">' . htmlspecialchars($candNotice, ENT_QUOTES) . '</span></li>'
        . '<li><strong>Engagement Mode:</strong> ' . htmlspecialchars($candEngagement, ENT_QUOTES) . '</li>'
        . '<li><strong>Commercials:</strong> ' . htmlspecialchars($candCommercials, ENT_QUOTES) . '</li>'
        . '</ul>'
        . '</div>'
        . '<p>Please find the formatted profile attached for your review. We can coordinate an introductory technical call within 24 hours upon your confirmation.</p>'
        . '<p>Looking forward to your feedback.</p>'
        . '<p>Best regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($senderDesig, ENT_QUOTES) . '<br>'
        . htmlspecialchars($senderEmail, ENT_QUOTES) . ' | ' . htmlspecialchars($senderPhone, ENT_QUOTES) . '<br>'
        . '<strong>' . htmlspecialchars($compName, ENT_QUOTES) . '</strong></p>';

    // 3. Pre-Screened & Evaluated Talent Dossier
    $tplBody3 = '<p>Dear [HR Name],</p>'
        . '<p>Please find attached the <strong>In-House Evaluation Report and Verified Profile</strong> for <strong>' . htmlspecialchars($candName, ENT_QUOTES) . '</strong> for your hiring evaluation.</p>'
        . '<p>Our internal technical team has evaluated this candidate against enterprise engineering benchmarks:</p>'
        . '<ul>'
        . '<li><strong>Candidate:</strong> ' . htmlspecialchars($candName, ENT_QUOTES) . ' (' . htmlspecialchars($candDesig, ENT_QUOTES) . ')</li>'
        . '<li><strong>Relevant Experience:</strong> ' . htmlspecialchars($candExp, ENT_QUOTES) . '</li>'
        . '<li><strong>Key Evaluated Competencies:</strong> ' . htmlspecialchars($candSkills, ENT_QUOTES) . '</li>'
        . '<li><strong>Verification Status:</strong> Pre-screened, In-House Evaluated &amp; Ready for Client Interview</li>'
        . '<li><strong>Work Authorization &amp; Availability:</strong> ' . htmlspecialchars($candNotice, ENT_QUOTES) . ' (' . htmlspecialchars($candWorkMode, ENT_QUOTES) . ')</li>'
        . '</ul>'
        . '<p>The attached report includes detailed skill ratings, interview notes, and the candidate complete professional dossier. Please review and advise on interview availability.</p>'
        . '<p>Best regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($senderDesig, ENT_QUOTES) . '<br>'
        . htmlspecialchars($senderEmail, ENT_QUOTES) . ' | ' . htmlspecialchars($senderPhone, ENT_QUOTES) . '<br>'
        . '<strong>' . htmlspecialchars($compName, ENT_QUOTES) . '</strong></p>';

    // 4. Contract / C2H Bench Profile Presentation
    $tplBody4 = '<p>Dear [HR Name],</p>'
        . '<p>Submitting our in-house talent profile for your current contract / contract-to-hire (C2H) requirements:</p>'
        . '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;font-family:Arial,Helvetica,sans-serif;font-size:12px;border:1px solid #CBD5E1;margin:10px 0;">'
        . '<tr style="background:#F1F5F9;">'
        . '<td style="width:30%;font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Candidate Name</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">' . htmlspecialchars($candName, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Primary Skills</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">' . htmlspecialchars($candSkills, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '<tr style="background:#F8FAFC;">'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Total Experience</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">' . htmlspecialchars($candExp, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Notice Period / Availability</td>'
        . '<td style="padding:6px 10px;color:#059669;font-weight:bold;border:1px solid #CBD5E1;">' . htmlspecialchars($candNotice, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '<tr style="background:#F8FAFC;">'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Current / Preferred Location</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">' . htmlspecialchars($candLoc, ENT_QUOTES) . ' (' . htmlspecialchars($candWorkMode, ENT_QUOTES) . ')</td>'
        . '</tr>'
        . '<tr>'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Engagement Model</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">Contract / C2H / SOW via ' . htmlspecialchars($compName, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '<tr style="background:#F8FAFC;">'
        . '<td style="font-weight:bold;padding:6px 10px;border:1px solid #CBD5E1;">Billing / Commercials</td>'
        . '<td style="padding:6px 10px;border:1px solid #CBD5E1;">' . htmlspecialchars($candCommercials, ENT_QUOTES) . '</td>'
        . '</tr>'
        . '</table>'
        . '<p>The candidate is on our direct payroll and available for deployment immediately upon client selection.</p>'
        . '<p>Kindly confirm receipt and share next steps for client rounds.</p>'
        . '<p>Warm regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($senderDesig, ENT_QUOTES) . '<br>'
        . htmlspecialchars($senderEmail, ENT_QUOTES) . ' | ' . htmlspecialchars($senderPhone, ENT_QUOTES) . '<br>'
        . '<strong>' . htmlspecialchars($compName, ENT_QUOTES) . '</strong></p>';

    // 5. Shortlisting & Interview Availability Follow-up
    $tplBody5 = '<p>Dear [HR Name],</p>'
        . '<p>I am following up regarding the profile of <strong>' . htmlspecialchars($candName, ENT_QUOTES) . '</strong> (' . htmlspecialchars($candDesig, ENT_QUOTES) . ' - ' . htmlspecialchars($candExp, ENT_QUOTES) . ') shared with you for your requirements.</p>'
        . '<p>The candidate has expressed keen interest in joining your team and remains immediately available. We would appreciate if you could share feedback on the profile or propose suitable time slots for an introductory interview this week.</p>'
        . '<p>We are ready to align the candidate as per your preferred interview schedule.</p>'
        . '<p>Thank you for your time and consideration.</p>'
        . '<p>Best regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($senderDesig, ENT_QUOTES) . '<br>'
        . htmlspecialchars($senderEmail, ENT_QUOTES) . ' | ' . htmlspecialchars($senderPhone, ENT_QUOTES) . '<br>'
        . '<strong>' . htmlspecialchars($compName, ENT_QUOTES) . '</strong></p>';

    // 6. Standard Candidate Profile Pitch (Default)
    $tplBodyDefault = '<p>Dear [HR_NAME],</p>'
        . '<p>I would like to share the profile of <strong>' . htmlspecialchars($candName, ENT_QUOTES) . '</strong> (' . htmlspecialchars($candDesig, ENT_QUOTES) . ') with you for your hiring requirements.</p>'
        . '<p><strong>Candidate Summary:</strong></p>'
        . '<ul>'
        . '<li><strong>Total Experience:</strong> ' . htmlspecialchars($candExp, ENT_QUOTES) . '</li>'
        . '<li><strong>Current Location:</strong> ' . htmlspecialchars($candLoc, ENT_QUOTES) . '</li>'
        . '<li><strong>Notice Period:</strong> ' . htmlspecialchars($candNotice, ENT_QUOTES) . '</li>'
        . '<li><strong>Talent Skills &amp; Expertise:</strong> ' . htmlspecialchars($candSkills, ENT_QUOTES) . '</li>'
        . '</ul>'
        . '<p>Please find the formatted profile attached for your review. We look forward to your valuable feedback.</p>'
        . '<p>Best regards,<br>'
        . '<strong>' . htmlspecialchars($senderName, ENT_QUOTES) . '</strong><br>'
        . htmlspecialchars($compName, ENT_QUOTES) . '</p>';
@endphp 

@include('service_company.bench-candidates.partials._bench_forward_resume_engine') 
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
if (typeof CKEDITOR !== 'undefined' && CKEDITOR.env && CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

function initBenchFwdCkEditor() {
    if (typeof CKEDITOR === 'undefined') {
        setTimeout(initBenchFwdCkEditor, 100);
        return;
    }
    var editorElement = document.getElementById('bench_editor1');
    if (!editorElement) {
        setTimeout(initBenchFwdCkEditor, 100);
        return;
    }
    if (CKEDITOR.instances && CKEDITOR.instances['bench_editor1']) {
        try {
            CKEDITOR.instances['bench_editor1'].destroy(true);
        } catch(destroyErr) {}
    }
    try {
        CKEDITOR.config.height = 280;
        CKEDITOR.config.width = '100%';
        var inst = CKEDITOR.replace('bench_editor1', {
            height: 280,
            width: '100%'
        });
        inst.on('instanceReady', function() {
            $('#bench_editor1').val(inst.getData());
        });
        inst.on('change', function() {
            $('#bench_editor1').val(inst.getData());
        });
        inst.on('blur', function() {
            $('#bench_editor1').val(inst.getData());
        });
    } catch(err) {
        console.warn('CKEditor bench init error:', err);
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initBenchFwdCkEditor, 50);
} else {
    document.addEventListener('DOMContentLoaded', initBenchFwdCkEditor);
}
$(document).ready(function() {
    setTimeout(initBenchFwdCkEditor, 100);
});
setTimeout(initBenchFwdCkEditor, 250);
</script>

<div class="main_container" id="bench_fwd_main_container">
@php
$to_email = '';
$cc_email = '';
if(!empty($cc_email_count) && $cc_email_count > 0 && !empty($cc_email_data)){
    $cc_email = implode(', ', $cc_email_data);
}
@endphp

<style>
/* mm-* form design — isolated for bench candidates forward resume drawer */
.bench-mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; min-height: 100%; }
#bench_fwd_form { display: flex; flex-direction: column; height: 100%; flex: 1; min-height: 0; }
.bench-mm-form-body { flex: 1; overflow-y: auto; scrollbar-gutter: stable; }
.bench-mm-row { display: flex; align-items: flex-start; padding: 14px 24px; border-bottom: 1px solid #F3F4F6; gap: 16px; }
.bench-mm-label-col { width: 150px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding-top: 9px; font-size: 12.5px; font-weight: 600; color: #374151; }
.bench-mm-label-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bench-mm-label-icon svg { width: 14px; height: 14px; }
.bench-mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
.bench-mm-ic-green  { background: #DCFCE7; color: #16A34A; }
.bench-mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
.bench-mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
.bench-mm-ic-orange { background: #FFF7ED; color: #EA580C; }
.bench-mm-req { color: #EF4444; margin-left: 1px; }
.bench-mm-field-col { flex: 1; min-width: 0; position: relative; }
.bench-mm-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB; outline: none; transition: border-color 0.15s; }
.bench-mm-input:focus { border-color: #93C5FD; background: #fff; }
.bench-mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
.bench-mm-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; line-height: 1.4; }
.bench-mm-select { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: border-color 0.15s; }
.bench-mm-select:focus { border-color: #93C5FD; background-color: #fff; }

/* Custom Resume Format Dropdown - Exact Replica of Attached Image */
.bench-fwd-format-dropdown-wrap {
  position: relative;
  width: 100%;
}
.bench-fwd-format-trigger {
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  padding: 8px 12px;
  background: #FFFFFF;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 44px;
  transition: all 0.2s ease;
  user-select: none;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.bench-fwd-format-trigger:hover {
  border-color: #2563EB;
}
.bench-fwd-format-dropdown-menu {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  z-index: 1050;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  box-shadow: 0 16px 36px rgba(0,0,0,0.16), 0 4px 12px rgba(0,0,0,0.08);
  max-height: min(850px, calc(100vh - 180px));
  overflow-y: auto;
  overscroll-behavior: contain;
  padding: 8px;
  scrollbar-width: thin;
  scrollbar-color: #94A3B8 #F1F5F9;
}
.bench-fwd-format-dropdown-menu::-webkit-scrollbar {
  width: 8px;
}
.bench-fwd-format-dropdown-menu::-webkit-scrollbar-track {
  background: #F1F5F9;
  border-radius: 4px;
}
.bench-fwd-format-dropdown-menu::-webkit-scrollbar-thumb {
  background: #94A3B8;
  border-radius: 4px;
}
.bench-fwd-format-dropdown-menu::-webkit-scrollbar-thumb:hover {
  background: #64748B;
}

.bench-fwd-sec-card {
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 7px;
  background: #FFFFFF;
}
.bench-fwd-sec-card:last-child {
  margin-bottom: 0;
}
.bench-fwd-sec-header {
  padding: 5px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid transparent;
}
.bench-fwd-sec-title {
  font-size: 12.5px;
  font-weight: 700;
  letter-spacing: -0.1px;
}
.bench-fwd-sec-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 1.5px 9px;
  border-radius: 12px;
}

.bench-fwd-opt-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6.5px 12px;
  cursor: pointer;
  border-bottom: 1px solid #F1F5F9;
  background: #FFFFFF;
  transition: all 0.15s ease;
  user-select: none;
}
.bench-fwd-opt-row:last-child {
  border-bottom: none;
}
.bench-fwd-opt-row:hover {
  background: #F8FAFC;
}
.bench-fwd-opt-row.is-selected {
  background: #F0F7FF !important;
}

.bf-radio {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 1.8px solid #CBD5E1;
  background: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s ease;
}
.bf-radio.is-checked {
  border-color: #2563EB;
  background: #FFFFFF;
}
.bf-radio-inner {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #2563EB;
  display: none;
}
.bf-radio.is-checked .bf-radio-inner {
  display: block;
}

.bf-opt-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.bf-opt-meta {
  min-width: 0;
  flex: 1;
}
.bf-opt-title {
  font-size: 12px;
  font-weight: 600;
  color: #1E293B;
  line-height: 1.3;
}
.bf-opt-desc {
  font-size: 11px;
  color: #64748B;
  line-height: 1.3;
  margin-top: 1px;
}
.bf-default-badge {
  background: #2563EB;
  color: #FFFFFF;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 10px;
  margin-left: auto;
  flex-shrink: 0;
  letter-spacing: 0.2px;
}

.bench-mm-editor-wrap { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; background: #FFFFFF; }
.bench-mm-editor-wrap .cke { width: 100% !important; border: none !important; }
#bench_editor1 { width: 100% !important; min-height: 240px; padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; box-sizing: border-box; }
.bench-mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; z-index: 10; }
.bench-mm-add-email-btn:hover { color: #1d4ed8; }

/* Tag / Chip input container for To and CC email addresses */
.bench-mm-tag-box {
  width: 100%;
  min-height: 44px;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  padding: 5px 105px 5px 8px;
  font-size: 13px;
  font-family: 'Poppins', sans-serif;
  background: #F9FAFB;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  position: relative;
  cursor: text;
  transition: border-color 0.15s, background-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}
.bench-mm-tag-box.is-focused {
  border-color: #93C5FD;
  background-color: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25);
}
.bench-mm-tag-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
.bench-mm-email-tag {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  color: #1E40AF;
  font-size: 12px;
  font-weight: 500;
  padding: 3px 8px;
  border-radius: 6px;
  line-height: 1.35;
  transition: all 0.15s ease;
  user-select: none;
}
.bench-mm-email-tag.is-invalid {
  background: #FEF2F2;
  border-color: #FECACA;
  color: #DC2626;
}
.bench-mm-email-tag-text {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.bench-mm-email-tag-remove {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  color: #6B7280;
  cursor: pointer;
  font-size: 14px;
  font-weight: 700;
  line-height: 1;
  transition: all 0.15s;
  margin-left: 2px;
}
.bench-mm-email-tag-remove:hover {
  background: #DBEAFE;
  color: #1E3A8A;
}
.bench-mm-tag-input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  font-size: 12.5px !important;
  font-family: 'Poppins', sans-serif !important;
  color: #374151 !important;
  min-width: 160px;
  flex: 1;
  padding: 4px 2px !important;
  box-shadow: none !important;
}

/* Replicated Modern Header */
.bench-mm-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  background: #FFFFFF;
  border-bottom: 1px solid #E5E7EB;
  position: sticky;
  top: 0;
  z-index: 50;
  flex-shrink: 0;
  gap: 16px;
}
.bench-mm-header-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}
.bench-mm-header-icon-box {
  width: 42px;
  height: 42px;
  min-width: 42px;
  border-radius: 9px;
  background: #EEF2FF;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #E0E7FF;
}
.bench-mm-header-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 2px;
  min-width: 0;
}
.bench-mm-header-title {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #1E1B4B;
  letter-spacing: -0.2px;
  line-height: 1.25;
}
.bench-mm-header-subtitle {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 400;
  color: #6B7280;
  line-height: 1.3;
}
.bench-mm-header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}
.bench-mm-header-btn-cancel {
  padding: 7px 18px;
  border-radius: 7px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #374151;
  font-size: 13px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  transition: all 0.15s ease;
}
.bench-mm-header-btn-cancel:hover {
  background: #F9FAFB;
  border-color: #D1D5DB;
  color: #111827;
}
.bench-mm-header-btn-send {
  padding: 7px 20px;
  border-radius: 7px;
  border: none;
  background: #1B64F2;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-width: 125px;
  box-shadow: 0 1px 3px rgba(27, 100, 242, 0.25);
  transition: all 0.15s ease;
}
.bench-mm-header-btn-send:hover { background: #1550C9; }
.bench-mm-header-btn-send:disabled { background: #93C5FD !important; cursor: not-allowed !important; }

/* ══════════════════════════════════════════════════════════════════════════════
   BENCH RECIPIENT DRAWER STYLES
══════════════════════════════════════════════════════════════════════════════ */
#kt_bench_recipient_drawer {
  width: 480px;
  max-width: 100vw;
  height: 100vh;
  background: #FFFFFF !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  z-index: 10060 !important;
  box-shadow: -8px 0 32px rgba(0, 0, 0, 0.16);
  position: fixed !important;
  top: 0 !important;
  right: 0 !important;
  display: none;
}
@media (max-width: 576px) {
  #kt_bench_recipient_drawer { width: 100vw !important; }
}
.bench-rcp-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100%;
  background: #FFFFFF !important;
  overflow: hidden;
  position: relative;
  z-index: 1;
}
.bench-rcp-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 18px 24px 14px 24px;
  flex-shrink: 0;
  border-bottom: 1px solid #F3F4F6;
}
.bench-rcp-header-title {
  margin: 0;
  font-size: 16.5px;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.2px;
}
.bench-rcp-header-subtitle {
  margin: 3px 0 0 0;
  font-size: 12px;
  color: #6B7280;
  font-weight: 400;
}
.bench-rcp-close-btn {
  background: none;
  border: none;
  padding: 6px;
  margin: -4px -6px 0 0;
  border-radius: 6px;
  color: #9CA3AF;
  cursor: pointer;
}
.bench-rcp-close-btn:hover { background: #F3F4F6; color: #111827; }

/* Tabs */
.bench-rcp-tabs-wrap {
  display: flex;
  gap: 12px;
  padding: 14px 24px 12px 24px;
  flex-shrink: 0;
}
.bench-rcp-tab-card {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  cursor: pointer;
  transition: all 0.15s ease;
}
.bench-rcp-tab-card:hover { border-color: #CBD5E1; background: #F8FAFC; }
.bench-rcp-tab-card.active { border: 1.5px solid #2563EB; background: #FFFFFF; }
.bench-rcp-tab-title { font-size: 12.5px; font-weight: 700; color: #111827; line-height: 1.2; }
.bench-rcp-tab-sub { font-size: 11px; color: #6B7280; margin-top: 1px; }

/* Search */
.bench-rcp-search-wrap { padding: 0 24px 12px 24px; flex-shrink: 0; }
.bench-rcp-search-input {
  width: 100%;
  padding: 8px 12px 8px 34px;
  border: 1px solid #E5E7EB;
  border-radius: 7px;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  color: #1F2937;
  outline: none;
}
.bench-rcp-search-input:focus { border-color: #3B82F6; }

/* List */
.bench-rcp-list-wrap { flex: 1; overflow-y: auto; padding: 0 24px; }
.bench-rcp-contact-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #F3F4F6;
  gap: 12px;
}
.bench-rcp-contact-name { font-size: 12.5px; font-weight: 700; color: #111827; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bench-rcp-email-line { font-size: 11px; color: #6B7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bench-rcp-role-pill { background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; border-radius: 10px; padding: 1px 6px; font-size: 10px; font-weight: 500; }
.bench-rcp-toggle-btn {
  padding: 4px 14px;
  border-radius: 6px;
  border: 1px solid #D1D5DB;
  background: #FFFFFF;
  color: #2563EB;
  font-size: 11.5px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
}
.bench-rcp-toggle-btn:hover { background: #EFF6FF; border-color: #2563EB; }
.bench-rcp-toggle-btn.is-added { background: #DCFCE7; border-color: #86EFAC; color: #16A34A; }
.bench-rcp-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 24px;
  border-top: 1px solid #E5E7EB;
  background: #FFFFFF;
  flex-shrink: 0;
}
</style>

<div class="bench-mm-wrap">
<form id="bench_fwd_form" enctype="multipart/form-data">
  @csrf
  {{-- Replicated Header --}}
  <div class="bench-mm-header">
    <div class="bench-mm-header-left">
      <div class="bench-mm-header-icon-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="4" width="20" height="16" rx="2"/>
          <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
        </svg>
      </div>
      <div class="bench-mm-header-text">
        <h3 class="bench-mm-header-title">Forward In-House Talent Resume</h3>
        <p class="bench-mm-header-subtitle">Send candidate profile and formatted dossier to client or hiring stakeholder.</p>
      </div>
    </div>
    <div class="bench-mm-header-actions">
      <button type="button" class="bench-mm-header-btn-cancel" onclick="closeBenchFwdResumeDrawer()">Cancel</button>
      <button type="button" class="bench-mm-header-btn-send" id="btnBenchFwdSubmit" onclick="onBenchFwdSubmitClick(event)" disabled>
        <span class="indicator-label" style="display:inline-flex;align-items:center;gap:7px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Email
        </span>
        <span class="indicator-progress" style="display:none;align-items:center;gap:7px;">
          <span class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;display:inline-block;"></span>
          <span id="bench_fwd_btn_progress_text">Compiling resume PDF...</span>
        </span>
      </button>
    </div>
  </div>

  <div class="bench-mm-form-body">

    {{-- From + Reply To --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
        </span>
        From
      </div>
      <div class="bench-mm-field-col" style="display:flex;gap:12px;align-items:center;">
        <select class="bench-mm-select" name="from_email" id="bench_fwd_from_email" style="flex:1;min-width:0;">
          <option value="notify@jeroworks.com">{{ $userdata->name ?? $userdata->email }} &lt;notify@jeroworks.com&gt;</option>
        </select>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
          <span class="bench-mm-label-icon bench-mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
          </span>
          Reply To
        </div>
        <select class="bench-mm-select" name="reply_to_email" id="bench_fwd_reply_to_email" style="flex:1;min-width:0;">
          <option value="{{ $userdata->email }}">{{ $userdata->email }}</option>
        </select>
      </div>
    </div>

    {{-- To Email ID --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        To Email ID <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-tag-box" id="bench_to_email_tag_box" onclick="$('#bench_to_email_input').focus();">
          <div class="bench-mm-tag-list" id="bench_to_email_tags"></div>
          <input type="text" class="bench-mm-tag-input" id="bench_to_email_input" placeholder="Type recipient email and press Enter...">
          <a href="javascript:;" class="bench-mm-add-email-btn" onclick="openBenchRecipientDrawer('to')">+ Add Email</a>
        </div>
        <input type="hidden" name="to_email" id="bench_to_email" value="">
        <div class="bench-mm-hint">Type email address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- CC Email ID --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        CC Email ID
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-tag-box" id="bench_cc_email_tag_box" onclick="$('#bench_cc_email_input').focus();">
          <div class="bench-mm-tag-list" id="bench_cc_email_tags"></div>
          <input type="text" class="bench-mm-tag-input" id="bench_cc_email_input" placeholder="Type CC email and press Enter...">
          <a href="javascript:;" class="bench-mm-add-email-btn" onclick="openBenchRecipientDrawer('cc')">+ Add Email</a>
        </div>
        <input type="hidden" name="cc_email" id="bench_cc_email" value="{{ $cc_email }}">
        <div class="bench-mm-hint">Type CC address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- Template --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        Template
      </div>
      <div class="bench-mm-field-col">
        <select name="template" id="bench_fwd_template" class="bench-mm-select" style="font-weight:500;" onchange="onBenchFwdTemplateChange(this)">
          <option value="" selected disabled>Select Email Template...</option>
          <optgroup label="🌟 Enterprise Bench Talent Templates">
            <option value="bench_table_matrix">1. Updated Inhouse Talent list + Tracker</option>
            <option value="bench_immediate_deploy">2. Immediate Deployment Hotlist (Executive Spotlight)</option>
            <option value="bench_prescreen_dossier">3. Pre-Screened &amp; Evaluated Talent Dossier</option>
            <option value="bench_contract_c2h">4. Contract / C2H Bench Profile Presentation</option>
            <option value="bench_feedback_followup">5. Shortlisting &amp; Interview Availability Follow-up</option>
            <option value="default_pitch">6. Standard Candidate Profile Pitch (Default)</option>
          </optgroup>
        </select>
        <div class="bench-mm-hint">Choosing a template replaces the Subject and Body below</div>
      </div>
    </div>

    {{-- Subject --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </span>
        Subject <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <input class="bench-mm-input" name="subject" id="bench_fwd_subject" placeholder="Enter email subject" value="{{ $cand ? ('Candidate Profile: ' . $cand->full_name . ' - ' . ($cand->current_designation ?? 'Profile')) : '' }}">
      </div>
    </div>

    {{-- Resume Format Selector --}}
    <div class="bench-mm-row" style="position:relative;z-index:30;">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </span>
        Resume Format <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <input type="hidden" id="bench_fwd_attach_cv_toggle" name="attached_cv" value="1">
        <input type="hidden" name="attached_cv_format" id="bench_fwd_resume_format" value="46">

        <!-- Custom Resume Format Dropdown (Exact Replica of Reference UI) -->
        <div class="bench-fwd-format-dropdown-wrap" id="bench_fwd_format_dropdown_wrap">
          
          <!-- Dropdown Trigger -->
          <div class="bench-fwd-format-trigger" id="bench_fwd_format_trigger" onclick="toggleBenchFormatDropdown(event)">
            <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
              <span class="bf-opt-icon" id="bench_fwd_trigger_icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <polygon points="12 10 13.2 12.5 15.8 12.8 13.9 14.5 14.5 17 12 15.6 9.5 17 10.1 14.5 8.2 12.8 10.8 12.5 12 10"/>
                </svg>
              </span>
              <div style="min-width:0;flex:1;">
                <div id="bench_fwd_trigger_title" style="font-size:12.5px;font-weight:600;color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding
                </div>
                <div id="bench_fwd_trigger_desc" style="font-size:11px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  Evaluation report and resume with highlighted skills and company branding.
                </div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:12px;">
              <span id="bench_fwd_trigger_badge" class="bf-default-badge" style="display:inline-block;">Default!</span>
              <svg id="bench_fwd_trigger_caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform 0.2s ease;">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </div>
          </div>

          <!-- Dropdown Menu Panel -->
          <div class="bench-fwd-format-dropdown-menu" id="bench_fwd_format_dropdown_menu" style="display:none;">

            <!-- Section 1: Standard Resume Options -->
            <div class="bench-fwd-sec-card" style="border-color:#DBEAFE;">
              <div class="bench-fwd-sec-header" style="background:#EEF2FF; border-bottom:1px solid #E0E7FF;">
                <span class="bench-fwd-sec-title" style="color:#1E3A8A;">1. Standard Resume Options</span>
                <span class="bench-fwd-sec-badge" style="background:#DBEAFE; color:#1E40AF;">2 formats</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="1" data-title="Original Resume" data-sub="Original candidate resume without any modifications." data-desc="Download the candidate's uploaded resume exactly as received, with no edits or formatting changes." onclick="selectBenchResumeFormat(1, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Original Resume</div>
                  <div class="bf-opt-desc">Original candidate resume without any modifications.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="41" data-title="Resume + Company Branding" data-sub="Original resume with your company branding." data-desc="Add your company logo and shared-by header above the resume while keeping all candidate details visible." onclick="selectBenchResumeFormat(41, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <rect x="8" y="11" width="8" height="6" rx="1"/>
                    <line x1="10" y1="14" x2="14" y2="14"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Resume + Company Branding</div>
                  <div class="bf-opt-desc">Original resume with your company branding.</div>
                </div>
              </div>
            </div>

            <!-- Section 2: In-House Evaluation Report -->
            <div class="bench-fwd-sec-card" style="border-color:#D1FAE5;">
              <div class="bench-fwd-sec-header" style="background:#ECFDF5; border-bottom:1px solid #A7F3D0;">
                <span class="bench-fwd-sec-title" style="color:#065F46;">2. In-House Evaluation Report</span>
                <span class="bench-fwd-sec-badge" style="background:#D1FAE5; color:#065F46;">1 format</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="5" data-title="In-House Evaluation Report PDF" data-sub="AI/Team evaluation report in PDF format." data-desc="Generate a concise hiring report with fit summary, key skills, and verification status for recruiter review." onclick="selectBenchResumeFormat(5, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="13" y2="17"/>
                    <circle cx="15" cy="17" r="1" fill="#1E3A8A"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">In-House Evaluation Report PDF</div>
                  <div class="bf-opt-desc">AI/Team evaluation report in PDF format.</div>
                </div>
              </div>
            </div>

            <!-- Section 3: Recruiter Packages (Report + Resume) -->
            <div class="bench-fwd-sec-card" style="border-color:#FED7AA;">
              <div class="bench-fwd-sec-header" style="background:#FFF7ED; border-bottom:1px solid #FFEDD5;">
                <span class="bench-fwd-sec-title" style="color:#C2410C;">3. Recruiter Packages (Report + Resume)</span>
                <span class="bench-fwd-sec-badge" style="background:#FFEDD5; color:#C2410C;">2 formats</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="42" data-title="In-House Evaluation Report + Resume + Company Branding" data-sub="Evaluation report and resume with company branding." data-desc="Deliver one polished PDF with the in-house evaluation report first, followed by the candidate resume with company branding." onclick="selectBenchResumeFormat(42, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="17" x2="9" y2="14"/>
                    <line x1="12" y1="17" x2="12" y2="11"/>
                    <line x1="15" y1="17" x2="15" y2="8"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">In-House Evaluation Report + Resume + Company Branding</div>
                  <div class="bf-opt-desc">Evaluation report and resume with company branding.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="43" data-title="In-House Evaluation Report + Resume (No Branding)" data-sub="Evaluation report and resume without company branding." data-desc="Download the same report-and-resume package without company logo or company name on the report page." onclick="selectBenchResumeFormat(43, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="12" x2="15" y2="12"/>
                    <line x1="9" y1="16" x2="15" y2="16"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">In-House Evaluation Report + Resume (No Branding)</div>
                  <div class="bf-opt-desc">Evaluation report and resume without company branding.</div>
                </div>
              </div>
            </div>

            <!-- Section 4: Highlighted Skills -->
            <div class="bench-fwd-sec-card" style="border-color:#FEF08A;">
              <div class="bench-fwd-sec-header" style="background:#FEF9C3; border-bottom:1px solid #FEF08A;">
                <span class="bench-fwd-sec-title" style="color:#854D0E;">4. Highlighted Skills</span>
                <span class="bench-fwd-sec-badge" style="background:#FEF08A; color:#854D0E;">4 formats</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="44" data-title="Resume + Highlighted Keywords" data-sub="Resume with relevant skills highlighted." data-desc="Download a clean resume copy with the most relevant skills highlighted for quick recruiter review." onclick="selectBenchResumeFormat(44, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <polygon points="12 10 13.2 12.5 15.8 12.8 13.9 14.5 14.5 17 12 15.6 9.5 17 10.1 14.5 8.2 12.8 10.8 12.5 12 10"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Resume + Highlighted Keywords</div>
                  <div class="bf-opt-desc">Resume with relevant skills highlighted.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="45" data-title="Resume + Highlighted Keywords + Company Branding" data-sub="Resume with highlighted skills and your company branding." data-desc="Download a branded resume with job-matching keywords highlighted for faster recruiter screening." onclick="selectBenchResumeFormat(45, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <polygon points="12.5 9.5 9 14 12 14 11.5 18.5 15 13 12 13 12.5 9.5"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Resume + Highlighted Keywords + Company Branding</div>
                  <div class="bf-opt-desc">Resume with highlighted skills and your company branding.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row is-selected" data-opt-id="46" data-title="In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding" data-sub="Evaluation report and resume with highlighted skills and company branding." data-desc="Create the recruiter package: in-house evaluation report, branded resume pages, and highlighted job-matching skills." onclick="selectBenchResumeFormat(46, this)">
                <div class="bf-radio is-checked">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <polygon points="12 10 13.2 12.5 15.8 12.8 13.9 14.5 14.5 17 12 15.6 9.5 17 10.1 14.5 8.2 12.8 10.8 12.5 12 10"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding</div>
                  <div class="bf-opt-desc">Evaluation report and resume with highlighted skills and company branding.</div>
                </div>
                <span class="bf-default-badge">Default!</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="47" data-title="In-House Evaluation Report + Resume + Highlighted Keywords (No Branding)" data-sub="Evaluation report and resume with highlighted skills without company branding." data-desc="Create the recruiter package with report, resume, and highlighted skills, without company branding." onclick="selectBenchResumeFormat(47, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="12" x2="15" y2="12"/>
                    <line x1="9" y1="16" x2="13" y2="16"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">In-House Evaluation Report + Resume + Highlighted Keywords (No Branding)</div>
                  <div class="bf-opt-desc">Evaluation report and resume with highlighted skills without company branding.</div>
                </div>
              </div>
            </div>

            <!-- Section 5: Privacy & Anonymization (Anti-Poaching Shield) -->
            <div class="bench-fwd-sec-card" style="border-color:#D1FAE5;">
              <div class="bench-fwd-sec-header" style="background:#ECFDF5; border-bottom:1px solid #A7F3D0;">
                <span class="bench-fwd-sec-title" style="color:#065F46;">5. Privacy & Anonymization (Anti-Poaching Shield)</span>
                <span class="bench-fwd-sec-badge" style="background:#D1FAE5; color:#065F46;">7 formats</span>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="48" data-title="Hide Contact Details" data-sub="Remove phone, email and other contact details from resume." data-desc="Keep candidate name visible while hiding email, phone, mobile, contact and LinkedIn details in browser." onclick="selectBenchResumeFormat(48, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details</div>
                  <div class="bf-opt-desc">Remove phone, email and other contact details from resume.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="49" data-title="Hide Candidate Name & Contacts Details" data-sub="Remove candidate name and all contact details." data-desc="Hide candidate name, email, and phone numbers in a browser-only resume copy." onclick="selectBenchResumeFormat(49, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Candidate Name & Contacts Details</div>
                  <div class="bf-opt-desc">Remove candidate name and all contact details.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="50" data-title="Hide Contact Details + Company Branding" data-sub="Remove contact details and add your company branding." data-desc="Keep the name visible, hide contact and LinkedIn details, then add your company header." onclick="selectBenchResumeFormat(50, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details + Company Branding</div>
                  <div class="bf-opt-desc">Remove contact details and add your company branding.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="51" data-title="Hide Contact Details + In-House Evaluation Report + Company Branding" data-sub="Evaluation report and branded resume with contacts hidden." data-desc="Keep the name visible, hide contact and LinkedIn details, then merge the in-house evaluation report with company branding." onclick="selectBenchResumeFormat(51, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details + In-House Evaluation Report + Company Branding</div>
                  <div class="bf-opt-desc">Evaluation report and branded resume with contacts hidden.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="52" data-title="Hide Contact Details + In-House Evaluation Report without Company Branding" data-sub="Evaluation report and resume with contacts hidden (no branding)." data-desc="Keep the name visible, hide contact and LinkedIn details, then merge a no-brand report." onclick="selectBenchResumeFormat(52, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details + In-House Evaluation Report without Company Branding</div>
                  <div class="bf-opt-desc">Evaluation report and resume with contacts hidden (no branding).</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="53" data-title="Hide Contact Details + In-House Evaluation Report + Highlighted Keywords + Company Branding" data-sub="Full privacy package: masked contacts, highlighted skills, report and branding." data-desc="Full privacy package: masked contacts, highlighted skills, in-house evaluation report dossier, and company branding banner." onclick="selectBenchResumeFormat(53, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details + In-House Evaluation Report + Highlighted Keywords + Company Branding</div>
                  <div class="bf-opt-desc">Full privacy package: masked contacts, highlighted skills, report and branding.</div>
                </div>
              </div>
              <div class="bench-fwd-opt-row" data-opt-id="54" data-title="Hide Contact Details + In-House Evaluation Report + Highlighted Keywords without Company Branding" data-sub="Full privacy package without company branding." data-desc="Full privacy package without company logo: masked contacts, highlighted skills, and in-house evaluation report dossier — no branding applied." onclick="selectBenchResumeFormat(54, this)">
                <div class="bf-radio">
                  <div class="bf-radio-inner"></div>
                </div>
                <div class="bf-opt-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="bf-opt-meta">
                  <div class="bf-opt-title">Hide Contact Details + In-House Evaluation Report + Highlighted Keywords without Company Branding</div>
                  <div class="bf-opt-desc">Full privacy package without company branding.</div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Format Details Section: Unified Single Enterprise Attachment & Progress Card -->
        <div id="bench_fwd_format_details_section" style="display:none;margin-top:8px;">

          @if(!empty($cand) && empty($cand->has_resume))
            <div id="bench_fwd_missing_resume_banner" style="margin-bottom:8px;padding:8px 12px;background:#FFFBEB;border:1px solid #FCD34D;border-radius:6px;display:flex;align-items:center;gap:8px;">
              <span style="color:#D97706;font-size:14px;flex-shrink:0;">⚠</span>
              <div style="font-size:11.5px;color:#92400E;line-height:1.35;">
                <strong>Resume Notice:</strong> Candidate has no resume document uploaded. You can attach using Option 2.a (In-House Evaluation Report) or upload a CV.
              </div>
            </div>
          @endif

          <!-- Unified Single Master Attachment & Progress Card -->
          @if($cand)
            @php
              $hasResume = !empty($cand->has_resume);
              $candSafeName = $cand->safe_name ?? ('Candidate_' . $cand->id);
              $candDisplayName = $cand->full_name ?? 'Candidate';
              $candDesignation = $cand->current_designation ?? 'Inhouse Talent';
              $candResumeUrl = $cand->resume_r2_url ?? ($cand->resume_url ?? ($cand->download_url ?? ''));
            @endphp
            <div id="bench_fwd_cand_card_{{ $cand->id }}" data-cand-id="{{ $cand->id }}" data-has-resume="{{ $hasResume ? '1' : '0' }}" style="position:relative;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.03);transition:all 0.2s ease;">
              
              <!-- Card Body -->
              <div style="padding:9px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                
                <!-- Left: Badge + Candidate Meta + Filename -->
                <div style="display:flex;align-items:center;gap:9px;min-width:0;flex:1;">
                  <span style="width:30px;height:30px;border-radius:6px;background:#F1F5F9;border:1px solid #E2E8F0;color:#0F766E;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                    PDF
                  </span>
                  <div style="min-width:0;flex:1;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:nowrap;">
                      <span style="font-size:12.5px;font-weight:600;color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $candDisplayName }}
                      </span>
                      <span style="font-size:11px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        • {{ $candDesignation }}
                      </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:2px;">
                      <div id="bench_fwd_cand_filename" data-safename="{{ $candSafeName }}" style="font-size:11px;font-family:monospace;color:{{ $hasResume ? '#64748B' : '#B45309' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        @if($hasResume)
                          <span style="color:#64748B;display:inline-flex;align-items:center;gap:3px;" title="Select a format to compile resume">
                            📄 Jeroworks_{{ $candSafeName }}.pdf
                          </span>
                        @else
                          <span>No resume uploaded</span>
                        @endif
                      </div>
                      <span id="bench_fwd_compile_step_detail" style="display:none;font-size:10.5px;color:#0D9488;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
                    </div>
                  </div>
                </div>

                <!-- Right: Action Buttons & Live Status -->
                <div style="display:flex;align-items:center;gap:7px;flex-shrink:0;">
                  <button type="button" id="bench_fwd_view_cv_btn" class="btn btn-xs btn-light-info fw-bold disabled" disabled style="display:{{ $hasResume ? 'inline-flex' : 'none' }};font-size:11px;padding:3px 9px;border-radius:6px;align-items:center;gap:4px;opacity:0.45;cursor:not-allowed;pointer-events:none;" title="Select a format and wait for 100% conversion to complete">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    View CV
                  </button>
                  <div id="bench_fwd_cand_status_badge" style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:12px;background:{{ $hasResume ? '#F8FAFC' : '#FEF3C7' }};color:{{ $hasResume ? '#475569' : '#B45309' }};border:1px solid {{ $hasResume ? '#E2E8F0' : '#FDE68A' }};display:inline-flex;align-items:center;gap:4px;white-space:nowrap;">
                    {{ $hasResume ? '1 Resume Available' : '⚠ No Resume' }}
                  </div>
                </div>
              </div>

              <!-- Sleek Integrated 3px Baseline Progress Bar -->
              <div id="bench_fwd_compile_progress_container" style="display:none;height:3px;width:100%;background:#F1F5F9;overflow:hidden;">
                <div id="bench_fwd_compile_progress_bar" style="height:100%;width:0%;background:linear-gradient(90deg, #0D9488, #10B981);transition:width 0.25s ease;"></div>
              </div>
              
              <!-- Compatibility hidden anchors -->
              <span id="bench_fwd_compile_status_text" style="display:none;"></span>
              <span id="bench_fwd_compile_pct_badge" style="display:none;"></span>
              <span id="bench_fwd_preview_format_title" style="display:none;"></span>
              <span id="bench_fwd_preview_desc" style="display:none;"></span>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Message Body --}}
    <div class="bench-mm-row bench-mm-row-body">
      <div class="bench-mm-label-col" style="padding-top:10px;">
        <span class="bench-mm-label-icon bench-mm-ic-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </span>
        Message Body <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-editor-wrap">
          <textarea name="editor1" id="bench_editor1">
<p>Dear [HR_NAME],</p>
<p>I would like to share the profile of <strong>{{ $cand->full_name ?? 'Candidate' }}</strong> ({{ $cand->current_designation ?? 'Profile' }}) with you for your hiring requirements.</p>
<p><strong>Candidate Summary:</strong></p>
<ul>
  <li><strong>Total Experience:</strong> {{ $cand->experience ?? '--' }}</li>
  <li><strong>Current Location:</strong> {{ $cand->location ?? '--' }}</li>
  <li><strong>Notice Period:</strong> {{ $cand->notice ?? '--' }}</li>
  @if(!empty($cand->skills))
  <li><strong>Talent Skills & Expertise:</strong> {{ $cand->skills }}</li>
  @endif
  @if(!empty($cand->rm_comments))
  <li><strong>Relationship Manager Comments:</strong> {{ strip_tags($cand->rm_comments) }}</li>
  @endif
</ul>
<p>Please find the formatted profile attached for your review. We look forward to your valuable feedback.</p>
<p>Best regards,<br>
<strong>{{ $userdata->name ?? 'Recruitment Team' }}</strong><br>
{{ $companyName ?? 'Hiring Partner' }}</p>
          </textarea>
        </div>
        <div class="bench-mm-hint">Use <code>[HR_NAME]</code> to automatically insert recipient's name in personalized emails.</div>
      </div>
    </div>

  </div>

  <input type="hidden" name="candidate_id" id="bench_fwd_candidate_id" value="{{ $cand->id ?? 0 }}">
  <input type="hidden" name="arr[]" value="{{ $cand->id ?? 0 }}">
</form>
</div>

<!-- ==============================================================================
     SLIDE-OUT RECIPIENT PICKER DRAWER
============================================================================== -->
<div id="kt_bench_recipient_drawer">
  <div class="bench-rcp-container">
    <div class="bench-rcp-header">
      <div>
        <h4 class="bench-rcp-header-title">Select Email Recipient</h4>
        <p class="bench-rcp-header-subtitle">Choose from internal team or client contacts</p>
      </div>
      <button type="button" class="bench-rcp-close-btn" onclick="closeBenchRecipientDrawer()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Segmented Tabs -->
    <div class="bench-rcp-tabs-wrap">
      <div class="bench-rcp-tab-card active" id="bench_rcp_tab_internal" onclick="switchBenchRecipientTab('internal')">
        <div>
          <div class="bench-rcp-tab-title">Internal Team</div>
          <div class="bench-rcp-tab-sub">{{ count($internal_team_data ?? []) }} colleagues</div>
        </div>
      </div>
      <div class="bench-rcp-tab-card" id="bench_rcp_tab_client" onclick="switchBenchRecipientTab('client')">
        <div>
          <div class="bench-rcp-tab-title">Client Contacts</div>
          <div class="bench-rcp-tab-sub">{{ count($client_contacts_data ?? []) }} contacts</div>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="bench-rcp-search-wrap">
      <input type="text" class="bench-rcp-search-input" id="bench_rcp_search_input" placeholder="Search by name, role or email..." onkeyup="filterBenchRecipients(this.value)">
    </div>

    <!-- Contact Lists -->
    <div class="bench-rcp-list-wrap">
      <!-- Internal Team Roster -->
      <div id="bench_rcp_internal_list">
        @if(!empty($internal_team_data))
          @foreach($internal_team_data as $userItem)
            <div class="bench-rcp-contact-item" data-search="{{ strtolower(($userItem->name ?? '') . ' ' . ($userItem->email ?? '') . ' ' . ($userItem->role_name ?? '')) }}">
              <div style="min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="bench-rcp-contact-name">{{ $userItem->name ?? 'Team Member' }}</span>
                  <span class="bench-rcp-role-pill">{{ $userItem->role_name ?? 'Recruiter' }}</span>
                </div>
                <div class="bench-rcp-email-line">{{ $userItem->email }}</div>
              </div>
              <div>
                <button type="button" class="bench-rcp-toggle-btn" data-email="{{ $userItem->email }}" onclick="toggleBenchRecipient('{{ $userItem->email }}', this)">+ Add</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      <!-- Client Contacts Roster -->
      <div id="bench_rcp_client_list" style="display:none;">
        @if(!empty($client_contacts_data))
          @foreach($client_contacts_data as $clientItem)
            <div class="bench-rcp-contact-item" data-search="{{ strtolower(($clientItem->name ?? '') . ' ' . ($clientItem->email ?? '') . ' ' . ($clientItem->company_name ?? '')) }}">
              <div style="min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="bench-rcp-contact-name">{{ $clientItem->name ?? 'Contact Person' }}</span>
                  <span class="bench-rcp-role-pill" style="background:#F0FDF4;color:#16A34A;border-color:#DCFCE7;">{{ $clientItem->company_name ?? 'Client' }}</span>
                </div>
                <div class="bench-rcp-email-line">{{ $clientItem->email }}</div>
              </div>
              <div>
                <button type="button" class="bench-rcp-toggle-btn" data-email="{{ $clientItem->email }}" onclick="toggleBenchRecipient('{{ $clientItem->email }}', this)">+ Add</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>

    <!-- Footer -->
    <div class="bench-rcp-footer">
      <button type="button" class="bench-mm-header-btn-cancel" onclick="closeBenchRecipientDrawer()">Cancel</button>
      <button type="button" class="bench-mm-header-btn-send" style="min-width:100px;" onclick="closeBenchRecipientDrawer()">Done</button>
    </div>
  </div>
</div>

<script>
// Scoped recipient tag manager for Bench Forward Resume
var benchToEmails = [];
var benchCcEmails = [];
var activeBenchRecipientTarget = 'to'; // 'to' or 'cc'
var _benchCompiledAttachment = null;
var _benchCompilationSeq = 0;
var _benchIsCompiling = false;
var _benchCompiledPreviewUrl = null;

// Ensure View CV is only clickable once compilation reaches 100%
function disableBenchViewCvBtn(reason) {
    var titleMsg = reason || 'Preparing resume... available once 100% complete';
    $('#bench_fwd_view_cv_btn')
        .prop('disabled', true)
        .addClass('disabled')
        .css({
            opacity: '0.45',
            cursor: 'not-allowed',
            pointerEvents: 'none'
        })
        .attr('title', titleMsg)
        .removeAttr('onclick')
        .off('click');
}

function enableBenchViewCvBtn(url) {
    if (!url) return;
    _benchCompiledPreviewUrl = url;
    $('#bench_fwd_view_cv_btn')
        .prop('disabled', false)
        .removeClass('disabled')
        .css({
            display: 'inline-flex',
            opacity: '1',
            cursor: 'pointer',
            pointerEvents: 'auto'
        })
        .attr('title', 'Click to view resume')
        .removeAttr('onclick')
        .off('click')
        .on('click', function(e) {
            e.preventDefault();
            window.open(url, '_blank');
        });
}

// Initial state: View CV is disabled until 100% compilation
disableBenchViewCvBtn('Select a format to compile resume');

// Open candidate's authoritative original resume in a new tab via same-origin streaming proxy
window.openBenchOriginalResume = function(candId) {
    var candObj = @json($cand ?? []);
    var cId = candId || (candObj && candObj.id) || 0;
    var ext = (candObj.resume_extension || (candObj.resume_filename ? candObj.resume_filename.split('.').pop() : '')).toLowerCase();

    // For Word documents, open the pre-converted vector PDF in new tab for immediate visual inspection
    if (ext === 'doc' || ext === 'docx') {
        window.open('/service_company/bench-candidates/download/' + cId + '?as_pdf=1', '_blank');
        return;
    }

    // Stream original document directly from same-origin proxy (Content-Disposition: inline)
    window.open('/service_company/bench-candidates/download/' + cId, '_blank');
};

// Initialize chips if values exist
function renderBenchTags(target) {
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    var container = $('#bench_' + target + '_email_tags');
    var hiddenInput = $('#bench_' + target + '_email');
    container.empty();

    list.forEach(function(email, idx) {
        var isVal = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        var chip = $('<div class="bench-mm-email-tag ' + (isVal ? '' : 'is-invalid') + '">' +
            '<span class="bench-mm-email-tag-text">' + email + '</span>' +
            '<span class="bench-mm-email-tag-remove" onclick="removeBenchEmailTag(\'' + target + '\', ' + idx + ')">&times;</span>' +
            '</div>');
        container.append(chip);
    });

    hiddenInput.val(list.join(','));
    syncBenchRecipientDrawerButtons();
}

function addBenchEmailTag(target, email) {
    var clean = (email || '').trim().replace(/,/g, '');
    if (!clean) return;
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    if (list.indexOf(clean) === -1) {
        list.push(clean);
        renderBenchTags(target);
    }
}

function removeBenchEmailTag(target, idx) {
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    list.splice(idx, 1);
    renderBenchTags(target);
}

// Input keyboard handler (Enter, comma, blur)
$('#bench_to_email_input').on('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addBenchEmailTag('to', $(this).val());
        $(this).val('');
    } else if (e.key === 'Backspace' && !$(this).val() && benchToEmails.length > 0) {
        removeBenchEmailTag('to', benchToEmails.length - 1);
    }
}).on('blur', function() {
    if ($(this).val()) {
        addBenchEmailTag('to', $(this).val());
        $(this).val('');
    }
});

$('#bench_cc_email_input').on('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addBenchEmailTag('cc', $(this).val());
        $(this).val('');
    } else if (e.key === 'Backspace' && !$(this).val() && benchCcEmails.length > 0) {
        removeBenchEmailTag('cc', benchCcEmails.length - 1);
    }
}).on('blur', function() {
    if ($(this).val()) {
        addBenchEmailTag('cc', $(this).val());
        $(this).val('');
    }
});

// Recipient drawer slide-in / out
function openBenchRecipientDrawer(target) {
    activeBenchRecipientTarget = target || 'to';
    syncBenchRecipientDrawerButtons();
    $('#kt_bench_recipient_drawer').css('display', 'block');
}

function closeBenchRecipientDrawer() {
    $('#kt_bench_recipient_drawer').css('display', 'none');
}

function switchBenchRecipientTab(tab) {
    if (tab === 'internal') {
        $('#bench_rcp_tab_internal').addClass('active');
        $('#bench_rcp_tab_client').removeClass('active');
        $('#bench_rcp_internal_list').show();
        $('#bench_rcp_client_list').hide();
    } else {
        $('#bench_rcp_tab_client').addClass('active');
        $('#bench_rcp_tab_internal').removeClass('active');
        $('#bench_rcp_client_list').show();
        $('#bench_rcp_internal_list').hide();
    }
}

function filterBenchRecipients(query) {
    var q = (query || '').toLowerCase().trim();
    $('.bench-rcp-contact-item').each(function() {
        var str = $(this).attr('data-search') || '';
        $(this).toggle(!q || str.indexOf(q) !== -1);
    });
}

function toggleBenchRecipient(email, btn) {
    var list = (activeBenchRecipientTarget === 'to') ? benchToEmails : benchCcEmails;
    var idx = list.indexOf(email);
    if (idx !== -1) {
        list.splice(idx, 1);
    } else {
        list.push(email);
    }
    renderBenchTags(activeBenchRecipientTarget);
}

function syncBenchRecipientDrawerButtons() {
    var list = (activeBenchRecipientTarget === 'to') ? benchToEmails : benchCcEmails;
    $('.bench-rcp-toggle-btn').each(function() {
        var email = $(this).attr('data-email');
        if (list.indexOf(email) !== -1) {
            $(this).addClass('is-added').text('Added ✓');
        } else {
            $(this).removeClass('is-added').text('+ Add');
        }
    });
}

// Template registry & synchronization handler
window.benchFwdTemplatesMap = {
    "bench_table_matrix": {
        id: "bench_table_matrix",
        name: "1. Updated Inhouse Talent list + Tracker",
        subject: "Sharing Updated Bench List | " + @json($compName) + " | " + @json($currentDate),
        body: @json($tplBody1)
    },
    "bench_immediate_deploy": {
        id: "bench_immediate_deploy",
        name: "2. Immediate Deployment Hotlist (Executive Spotlight)",
        subject: "Available for Immediate Deployment | " + @json($candName) + " - " + @json($candDesig) + " (" + @json($candExp) + ")",
        body: @json($tplBody2)
    },
    "bench_prescreen_dossier": {
        id: "bench_prescreen_dossier",
        name: "3. Pre-Screened & Evaluated Talent Dossier",
        subject: "Pre-Screened Talent Dossier: " + @json($candName) + " | " + @json($candDesig) + " | In-House Verified",
        body: @json($tplBody3)
    },
    "bench_contract_c2h": {
        id: "bench_contract_c2h",
        name: "4. Contract / C2H Bench Profile Presentation",
        subject: "Contract / C2H Talent Profile: " + @json($candName) + " - " + @json($candDesig) + " | " + @json($compName),
        body: @json($tplBody4)
    },
    "bench_feedback_followup": {
        id: "bench_feedback_followup",
        name: "5. Shortlisting & Interview Availability Follow-up",
        subject: "Interview Availability & Feedback Follow-up: " + @json($candName) + " | " + @json($candDesig),
        body: @json($tplBody5)
    },
    "default_pitch": {
        id: "default_pitch",
        name: "6. Standard Candidate Profile Pitch (Default)",
        subject: "Candidate Profile: " + @json($candName) + " - " + @json($candDesig),
        body: @json($tplBodyDefault)
    }
};

function replaceBenchTemplatePlaceholders(text) {
    if (!text) return '';
    var candName = @json($candName);
    var candDesig = @json($candDesig);
    var candExp = @json($candExp);
    var candSkills = @json($candSkills);
    var candLoc = @json($candLoc);
    var candWorkMode = @json($candWorkMode);
    var candNotice = @json($candNotice);
    var candEngagement = @json($candEngagement);
    var candCommercials = @json($candCommercials);
    var compName = @json($compName);
    var currentDate = @json($currentDate);
    var senderName = @json($senderName);
    var senderDesig = @json($senderDesig);
    var senderEmail = @json($senderEmail);
    var senderPhone = @json($senderPhone);

    return text
        .replace(/\{Company Name\}/gi, compName)
        .replace(/\[Company Name\]/gi, compName)
        .replace(/\[COMPANY_NAME\]/gi, compName)
        .replace(/\[\{COMPANY_NAME\}\]/gi, compName)
        .replace(/\[\{CLIENT_NAME_COMPANY_NAME\}\]/gi, compName)
        .replace(/\{Date\}/gi, currentDate)
        .replace(/\[Date\]/gi, currentDate)
        .replace(/\[\{CURRENTDATE\}\]/gi, currentDate)
        .replace(/\[Candidate Name\]/gi, candName)
        .replace(/\[CANDIDATE_NAME\]/gi, candName)
        .replace(/\[\{CANDIDATE_NAME\}\]/gi, candName)
        .replace(/\[Designation\]/gi, candDesig)
        .replace(/\[DESIGNATION\]/gi, candDesig)
        .replace(/\[\{DESIGNATION\}\]/gi, candDesig)
        .replace(/\[\{JOB_TITLE\}\]/gi, candDesig)
        .replace(/\[Exp\.?\]/gi, candExp)
        .replace(/\[X Yrs\]/gi, candExp)
        .replace(/\[EXPERIENCE\]/gi, candExp)
        .replace(/\[Skills\]/gi, candSkills)
        .replace(/\[Primary Skills\]/gi, candSkills)
        .replace(/\[PRIMARY_SKILLS\]/gi, candSkills)
        .replace(/\[Location\]/gi, candLoc)
        .replace(/\[LOCATION\]/gi, candLoc)
        .replace(/\[Work Mode\]/gi, candWorkMode)
        .replace(/\[WORK_MODE\]/gi, candWorkMode)
        .replace(/\[WFO \/ Hybrid \/ Remote\]/gi, candWorkMode)
        .replace(/\[Availability\]/gi, candNotice)
        .replace(/\[Immediate \/ X Days\]/gi, candNotice)
        .replace(/\[AVAILABILITY\]/gi, candNotice)
        .replace(/\[Engagement\]/gi, candEngagement)
        .replace(/\[FT \/ Contract\]/gi, candEngagement)
        .replace(/\[ENGAGEMENT\]/gi, candEngagement)
        .replace(/\[Commercials\]/gi, candCommercials)
        .replace(/\[COMMERCIALS\]/gi, candCommercials)
        .replace(/\[Sender Name\]/gi, senderName)
        .replace(/\[SENDER_NAME\]/gi, senderName)
        .replace(/\[Sender Designation\]/gi, senderDesig)
        .replace(/\[SENDER_DESIGNATION\]/gi, senderDesig)
        .replace(/\[Sender Email\]/gi, senderEmail)
        .replace(/\[SENDER_EMAIL\]/gi, senderEmail)
        .replace(/\[Sender Phone\]/gi, senderPhone)
        .replace(/\[SENDER_PHONE\]/gi, senderPhone);
}

// Template selection handler with full bidirectional CKEditor synchronization
function onBenchFwdTemplateChange(sel) {
    var val = sel.value;
    if (!val || !window.benchFwdTemplatesMap || !window.benchFwdTemplatesMap[val]) {
        return;
    }
    var tpl = window.benchFwdTemplatesMap[val];

    // 1. Update Subject with replaced placeholders
    if (tpl.subject) {
        var parsedSubj = replaceBenchTemplatePlaceholders(tpl.subject);
        $('#bench_fwd_subject').val(parsedSubj);
    }

    // 2. Update Editor & Textarea with replaced placeholders
    if (tpl.body) {
        var parsedBody = replaceBenchTemplatePlaceholders(tpl.body);
        $('#bench_editor1').val(parsedBody);

        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['bench_editor1']) {
            var inst = CKEDITOR.instances['bench_editor1'];
            try {
                if (inst.status === 'ready' || inst.instanceReady) {
                    inst.setData(parsedBody, function() {
                        $('#bench_editor1').val(inst.getData());
                    });
                } else {
                    inst.on('instanceReady', function() {
                        inst.setData(parsedBody, function() {
                            $('#bench_editor1').val(inst.getData());
                        });
                    });
                }
            } catch(e) {
                console.warn('CKEditor setData notice:', e);
                $('#bench_editor1').val(parsedBody);
            }
        }
    }
}

// Dropdown toggle and menu handling
function toggleBenchFormatDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    var menu = $('#bench_fwd_format_dropdown_menu');
    if (menu.is(':visible')) {
        closeBenchFormatDropdown();
    } else {
        openBenchFormatDropdown();
    }
}

function openBenchFormatDropdown() {
    var menu = $('#bench_fwd_format_dropdown_menu');
    menu.show();
    $('#bench_fwd_trigger_caret').css('transform', 'rotate(180deg)');
    $('#bench_fwd_format_trigger').css('border-color', '#2563EB');

    // Smooth wheel scroll isolation to keep scrolling strictly within dropdown
    menu.off('wheel.benchMenu').on('wheel.benchMenu', function(e) {
        var oe = e.originalEvent || e;
        var d = oe.deltaY;
        var st = this.scrollTop;
        var sh = this.scrollHeight;
        var ch = this.clientHeight;
        if ((d > 0 && st + ch >= sh) || (d < 0 && st <= 0)) {
            e.preventDefault();
        }
        e.stopPropagation();
    });

    // Auto-scroll to selected option if needed
    var selectedRow = menu.find('.bench-fwd-opt-row.is-selected');
    if (selectedRow.length) {
        selectedRow[0].scrollIntoView({ block: 'nearest' });
    }
}

function closeBenchFormatDropdown() {
    $('#bench_fwd_format_dropdown_menu').hide();
    $('#bench_fwd_trigger_caret').css('transform', 'rotate(0deg)');
    $('#bench_fwd_format_trigger').css('border-color', '#CBD5E1');
}

$(document).on('click', function(e) {
    if (!$(e.target).closest('#bench_fwd_format_dropdown_wrap').length) {
        closeBenchFormatDropdown();
    }
});

// Format selection handler for custom replica dropdown
function selectBenchResumeFormat(optId, el, skipCompilation) {
    optId = parseInt(optId, 10);
    if (!optId || isNaN(optId)) return;

    if (!el) {
        el = $('.bench-fwd-opt-row[data-opt-id="' + optId + '"]')[0];
    }
    if (!el) return;

    // Update hidden form input
    $('#bench_fwd_resume_format').val(optId);

    // Update active highlight and radio indicator in dropdown list
    $('.bench-fwd-opt-row').removeClass('is-selected');
    $(el).addClass('is-selected');

    $('.bf-radio').removeClass('is-checked');
    $(el).find('.bf-radio').addClass('is-checked');

    // Update trigger UI (icon, title, subtitle, default badge)
    var iconHtml = $(el).find('.bf-opt-icon').html();
    var title = $(el).attr('data-title') || $(el).find('.bf-opt-title').text().trim();
    var sub = $(el).attr('data-sub') || $(el).find('.bf-opt-desc').text().trim();
    var desc = $(el).attr('data-desc') || sub;

    $('#bench_fwd_trigger_icon').html(iconHtml);
    $('#bench_fwd_trigger_title').text(title);
    $('#bench_fwd_trigger_desc').text(sub);

    if (optId === 46) {
        $('#bench_fwd_trigger_badge').show();
    } else {
        $('#bench_fwd_trigger_badge').hide();
    }

    closeBenchFormatDropdown();

    if (!skipCompilation) {
        onBenchFwdFormatSelect(optId, title, desc);
    }
}

// Backward compatibility helper
function onBenchFwdFormatChange(sel) {
    if (typeof sel === 'object' && sel && sel.options) {
        var opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            selectBenchResumeFormat(parseInt(opt.value, 10), null, false);
        }
        return;
    }
    if (typeof sel === 'number' || typeof sel === 'string') {
        selectBenchResumeFormat(parseInt(sel, 10), null, false);
    }
}

// Listen for programmatic changes on hidden input
$('#bench_fwd_resume_format').on('change', function() {
    var v = parseInt($(this).val(), 10);
    if (v) {
        selectBenchResumeFormat(v, null, false);
    }
});

// Primary format processing and compilation orchestrator
function onBenchFwdFormatSelect(optId, label, desc) {
    if (!optId) {
        $('#bench_fwd_format_details_section').hide();
        _benchCompiledAttachment = null;
        disableBenchViewCvBtn('Select a format to compile resume');
        var btn = document.getElementById('btnBenchFwdSubmit');
        if (btn) {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }
        return;
    }

    $('#bench_fwd_preview_format_title').text(label);
    $('#bench_fwd_preview_desc').text(desc);
    $('#bench_fwd_format_details_section').show();

    // Reset compiled preview state and strictly disable View CV until 100% complete
    if (_benchCompiledPreviewUrl && _benchCompiledPreviewUrl.startsWith('blob:')) {
        try { URL.revokeObjectURL(_benchCompiledPreviewUrl); } catch(e) {}
    }
    _benchCompiledPreviewUrl = null;
    disableBenchViewCvBtn('Preparing resume... available once 100% complete');

    var safeName = $('#bench_fwd_cand_filename').attr('data-safename') || 'Candidate';
    var candId = @json($cand->id ?? 0);
    var candHasResume = @json(!empty($cand->has_resume));
    var dispName = '';
    if (optId === 1) {
        var ext = @json(pathinfo($cand->resume_filename ?? '', PATHINFO_EXTENSION) ?: 'pdf');
        dispName = 'Jeroworks_' + safeName + '.' + ext;
    } else if (optId === 5) {
        dispName = 'Prescreen_Report_' + safeName + '.pdf';
    } else {
        dispName = 'Jeroworks_' + safeName + '_Formatted.pdf';
    }

    if (candHasResume || optId === 5) {
        // Plain text while compiling (not clickable until 100% complete)
        $('#bench_fwd_cand_filename').html(
            '<span style="color:#64748B;display:inline-flex;align-items:center;gap:4px;">' +
            '📄 ' + $('<div>').text(dispName).html() + '</span>'
        );
    } else {
        $('#bench_fwd_cand_filename').html('<span>No resume uploaded</span>');
    }

    if (!candHasResume && optId !== 5) {
        $('#bench_fwd_cand_status_badge')
            .html('⚠ No Resume')
            .css({ background: '#FEF3C7', color: '#B45309' });
        $('#bench_fwd_compile_progress_container').hide();
        var btn = document.getElementById('btnBenchFwdSubmit');
        if (btn) {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }
        Swal.fire({
            icon: 'warning',
            title: 'Resume Missing',
            text: 'This candidate has no uploaded resume document. Please select Option 2.a (In-House Evaluation Report PDF) to forward their candidate profile dossier.',
            confirmButtonText: 'Select Option 2.a',
            showCancelButton: true,
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                selectBenchResumeFormat(5, null, false);
            }
        });
        return;
    }

    // Immediately start the engine for conversion!
    triggerBenchResumeCompilation(optId);
}

// Trigger conversion engine immediately upon format selection
async function triggerBenchResumeCompilation(optId) {
    var seq = ++_benchCompilationSeq;
    _benchIsCompiling = true;
    _benchCompiledAttachment = null;
    disableBenchViewCvBtn('Compiling resume... available once 100% complete');

    var btn = document.getElementById('btnBenchFwdSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.cursor = 'not-allowed';
        $(btn).find('.indicator-label').hide();
        $(btn).find('.indicator-progress').css('display', 'inline-flex').show();
        $('#bench_fwd_btn_progress_text').text('Compiling resume PDF...');
    }

    $('#bench_fwd_compile_progress_container').show();
    $('#bench_fwd_compile_progress_bar').css({ width: '20%', background: 'linear-gradient(90deg, #0D9488, #10B981)' });
    $('#bench_fwd_compile_pct_badge').text('20%');
    $('#bench_fwd_compile_status_text').text('Compiling document...').css('color', '#0F766E');
    $('#bench_fwd_compile_step_detail').text('Compiling document...').show();

    $('#bench_fwd_cand_status_badge')
        .html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;color:#0D9488!important;display:inline-block;margin-right:3px;"></span> 20%')
        .css({ background: '#F0FDFA', color: '#0F766E', borderColor: '#99F6E4' });

    try {
        var candObj = @json($cand ?? []);
        var companyData = {
            companyName: @json($companyName ?? 'Hiring Partner'),
            companyLogo: @json($companyLogo ?? '')
        };
        var jobSkills = [];

        var engine = window.AgyBenchForwardResumeEngine;
        if (!engine || typeof engine.generateAttachmentForCandidate !== 'function') {
            throw new Error('Client WASM document engine is initializing. Please wait a moment.');
        }

        var blob = await engine.generateAttachmentForCandidate(
            candObj,
            optId,
            companyData,
            jobSkills,
            function(pct, statusText, stepDetail) {
                if (seq !== _benchCompilationSeq) return;
                if (pct !== undefined) {
                    $('#bench_fwd_compile_progress_bar').css('width', pct + '%');
                    $('#bench_fwd_compile_pct_badge').text(pct + '%');
                    $('#bench_fwd_cand_status_badge')
                        .html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;color:#0D9488!important;display:inline-block;margin-right:3px;"></span> ' + pct + '%')
                        .css({ background: '#F0FDFA', color: '#0F766E', borderColor: '#99F6E4' });
                }
                if (statusText || stepDetail) {
                    var msg = statusText || stepDetail;
                    $('#bench_fwd_compile_status_text').text(msg);
                    $('#bench_fwd_compile_step_detail').text(msg).show();
                }
            }
        );

        if (seq !== _benchCompilationSeq) return; // Superseded by another format selection

        if (!blob) {
            throw new Error('Document compiler returned empty output.');
        }

        var safeName = (candObj.full_name || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');
        var defaultName = (optId === 5) ? ('Prescreen_Report_' + safeName + '.pdf') : ('Jeroworks_' + safeName + '.pdf');
        var outName = (blob && blob.filename) ? blob.filename : defaultName;

        _benchCompiledAttachment = {
            blob: blob.blob || blob,
            filename: outName,
            formatId: optId
        };
        _benchIsCompiling = false;

        // Progress bar 100%
        $('#bench_fwd_compile_progress_bar').css({ width: '100%', background: '#10B981' });
        $('#bench_fwd_compile_pct_badge').text('100%');
        $('#bench_fwd_compile_status_text').text('✓ Document compiled successfully!').css('color', '#059669');
        $('#bench_fwd_compile_step_detail').hide();
        
        var previewUrl = null;
        try {
            var rawBlob = blob.blob || blob;
            if (rawBlob instanceof Blob) {
                previewUrl = URL.createObjectURL(rawBlob);
            }
        } catch (e) {}

        if (previewUrl) {
            $('#bench_fwd_cand_filename').html(
                '<a href="' + previewUrl + '" target="_blank" style="color:#059669;text-decoration:underline;cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Click to preview compiled resume">' +
                '📄 ' + $('<div>').text(outName).html() + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>'
            );
            enableBenchViewCvBtn(previewUrl);
        } else {
            $('#bench_fwd_cand_filename').html(
                '<span style="color:#64748B;display:inline-flex;align-items:center;gap:4px;">' +
                '📄 ' + $('<div>').text(outName).html() + '</span>'
            );
            disableBenchViewCvBtn('Preview unavailable');
        }

        $('#bench_fwd_cand_status_badge')
            .html('✓ Attached (100%)')
            .css({ background: '#DCFCE7', color: '#15803D', borderColor: '#BBF7D0' });

        // Enable Send Email button now that conversion is complete!
        if (btn) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }

    } catch (compileErr) {
        if (seq !== _benchCompilationSeq) return;
        _benchIsCompiling = false;
        _benchCompiledAttachment = null;
        console.error('[BenchForwardResume] Compilation failed:', compileErr);

        disableBenchViewCvBtn('Conversion incomplete or failed');

        if (btn) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }

        $('#bench_fwd_compile_status_text').text('Compilation notice: ' + (compileErr.message || 'Processing Error')).css('color', '#DC2626');
        $('#bench_fwd_compile_step_detail').text('Please choose another format or continue with Option 1.a.');
        $('#bench_fwd_cand_status_badge').html('⚠ Conversion Issue').css({ background: '#FEE2E2', color: '#DC2626' });

        Swal.fire({
            icon: 'warning',
            title: 'Format Notice',
            text: 'We encountered an issue preparing format #' + optId + ' (' + (compileErr.message || 'Processing Error') + '). Would you like to use Option 1.a (Original Resume) or Option 2.a (In-House Evaluation Report) instead?',
            showCancelButton: true,
            confirmButtonText: 'Use Option 1.a (Original)',
            cancelButtonText: 'Select Format'
        }).then(function(result) {
            if (result.isConfirmed) {
                $('#bench_fwd_resume_format').val('1').trigger('change');
            }
        });
    }
}

// Send forward email handler
async function onBenchFwdSubmitClick(e) {
    if (e) e.preventDefault();

    if (_benchIsCompiling) {
        Swal.fire('Please Wait', 'Resume conversion is still in progress. The email will be sent once conversion completes.', 'info');
        return;
    }

    var toEmail = $('#bench_to_email').val();
    if (!toEmail) {
        Swal.fire('Recipient Required', 'Please enter at least one recipient email address (To).', 'warning');
        $('#bench_to_email_input').focus();
        return;
    }

    var subject = $('#bench_fwd_subject').val().trim();
    if (!subject) {
        Swal.fire('Subject Required', 'Please enter an email subject line.', 'warning');
        $('#bench_fwd_subject').focus();
        return;
    }

    var formatId = parseInt($('#bench_fwd_resume_format').val(), 10);
    if (!formatId || isNaN(formatId)) {
        Swal.fire('Format Required', 'Please select a resume format before sending.', 'warning');
        $('#bench_fwd_resume_format').focus();
        return;
    }

    var candHasResume = @json(!empty($cand->has_resume));
    if (!candHasResume && formatId !== 5) {
        Swal.fire('Resume Missing', 'This candidate does not have an uploaded resume file to merge. Please select "Option 2.a: In-House Evaluation Report PDF" to generate their standalone dossier.', 'warning');
        return;
    }

    // Ensure attachment is compiled and ready in RAM
    if (!_benchCompiledAttachment || _benchCompiledAttachment.formatId !== formatId) {
        await triggerBenchResumeCompilation(formatId);
        if (!_benchCompiledAttachment) {
            Swal.fire('Attachment Error', 'Resume attachment could not be compiled. Please select another format.', 'error');
            return;
        }
    }

    var bodyHtml = '';
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['bench_editor1']) {
        bodyHtml = CKEDITOR.instances['bench_editor1'].getData();
    } else {
        bodyHtml = $('#bench_editor1').val();
    }

    var cleanBody = $('<div>').html(bodyHtml).text().trim();
    if (!cleanBody && bodyHtml.indexOf('<img') === -1) {
        Swal.fire('Message Required', 'Please provide a message body before sending.', 'warning');
        return;
    }

    var btn = document.getElementById('btnBenchFwdSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.cursor = 'not-allowed';
        $(btn).find('.indicator-label').hide();
        $(btn).find('.indicator-progress').css('display', 'inline-flex').show();
        $('#bench_fwd_btn_progress_text').text('Sending email...');
    }

    var formData = new FormData(document.getElementById('bench_fwd_form'));
    formData.set('editor1', bodyHtml);
    formData.set('subject', subject);
    formData.set('to_email', toEmail);
    formData.set('cc_email', $('#bench_cc_email').val());
    formData.set('attached_cv_format', formatId);

    var candObj = @json($cand ?? []);
    if (_benchCompiledAttachment && _benchCompiledAttachment.blob) {
        formData.append('custom_attachments[' + candObj.id + ']', _benchCompiledAttachment.blob, _benchCompiledAttachment.filename);
    }

    $.ajax({
        url: "{{ route('service_company.bench-candidates.fwd_resume_send') }}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                $(btn).find('.indicator-progress').hide();
                $(btn).find('.indicator-label').css('display', 'inline-flex').show();
            }
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Forwarded!',
                    text: res.message || 'Candidate resume forwarded successfully via email.',
                    confirmButtonText: 'Done',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(function() {
                    closeBenchFwdResumeDrawer();
                });
            } else if (res.status === 'coinCheck') {
                Swal.fire('Insufficient Coins', res.message || 'Please recharge your company coin balance.', 'warning');
            } else {
                Swal.fire('Failed', res.message || 'Could not send forward email. Please try again.', 'error');
            }
        },
        error: function(err) {
            if (btn) {
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                $(btn).find('.indicator-progress').hide();
                $(btn).find('.indicator-label').css('display', 'inline-flex').show();
            }
            var errMsg = 'Server error occurred while sending email.';
            if (err.responseJSON && err.responseJSON.message) {
                errMsg = err.responseJSON.message;
            }
            Swal.fire('Error', errMsg, 'error');
        }
    });
}

function closeBenchFwdResumeDrawer() {
    var drawerEl = document.getElementById('kt_bench_fwd_resume_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { 
            drawer.hide(); 
            return; 
        }
    }
    $('#kt_bench_fwd_resume_drawer').removeClass('drawer-on');
    var overlays = $('.drawer-overlay');
    if (overlays.length > 1) {
        overlays.last().remove();
    } else if (!$('#kt_activitiesBenchApplication').hasClass('drawer-on')) {
        overlays.remove();
    }
}

// Auto-initialize format selection matching default state in design (Option 4.c / ID 46)
setTimeout(function() {
    var hasResume = @json(!empty($cand->has_resume));
    if (hasResume) {
        selectBenchResumeFormat(46, null, false);
    } else {
        selectBenchResumeFormat(5, null, false);
    }
}, 250);
</script>
