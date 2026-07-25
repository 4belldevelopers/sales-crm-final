{{--
  Phase 7 — Blade partial adds the `Resume Uploaed` status badge,
  exposes a JSON config object on `window.JERO_RESUME_CONFIG` so any
  form-level JS (the create/edit recruiter forms, the bench manager
  form, et al.) can read the same options without re-declaring them,
  and switches the default attach site from the legacy `#resumeFile`
  (which never matched the live form's `#resume`) to the new
  `attachResumeUploadProgress` helper.

  What this emits:
    1. The hidden session uuid input the controller's submit handler
       picks up server-side when the upload completes.
    2. The progress bar + error container (already present in Phase 4).
    3. A small "Resume Uploaded" status badge that flips visible once
       finalize succeeds. Replaces the inline tiny UI hint with a
       discoverable confirmation suitable for the recruiter persona.
    4. The wiring JS, fed from a server-side config() so JS respects
       the kill-switch `resumes.use_presigned_put`.

  Backward compat: when the server config flag is FALSE, the JS shim
  emits the multipart POST to `/uploadresume` and drives the bar via
  xhr.upload.onprogress — exactly the same as Phase 4 fallback ladder.
--}}

@php
    $usePresigned = (bool) config('resumes.use_presigned_put', false);

    $config = [
        'csrfToken'    => csrf_token(),
        'presignUrl'   => route('service_company.resume-sessions.presign'),
        'finalizeUrlTpl' => url('/') . '/service_company/api/resume-sessions/__UUID__/finalize',
        'legacyUrl'    => route('service_company.job-applications.uploadresume'),
        // Phase 9.1 — submit-side recovery verifier. After a network
        // failure during the candidate POST, the JS calls this URL to
        // ask the server whether a candidate row was already written.
        // The controller's show() returns `linked` + `candidate_id` so
        // the recovery modal can decide safely between "Submission
        // already received — you're done" and "Retry is safe to attempt".
        'sessionShowUrlTpl' => url('/') . '/service_company/api/resume-sessions/__UUID__',
        // Phase 9.2 — submit-side preflight. Pings the server with an
        // 800ms AbortController before awaiting the candidate POST.
        // A preflight failure flips the spinner to a calm "Server
        // slow, retrying..." badge instead of letting the user wait
        // up to 30s for an unresponsive app server.
        'preflightUrl' => route('service_company.resume.preflight'),
        // Phase 9 — read-only instant field-extraction endpoint.
        // Sibling of the upload fan-out — does not write to DB.
        'parserUrl'    => route('api.resume-parser.parse'),
        // Phase 9 follow-up — submit-handler wait cap. Server-side
        // config so ops tunes WITHOUT a JS rebuild. The JS module
        // uses this as the ceiling if the submit handler does not
        // pass an explicit `timeoutMs`. Default 2500 ms.
        'parserSubmitWaitMs' => (int) config('resumes.parser_submit_wait_ms', 2500),
        // Phase 9.2 — preflight timeout. Stable ceiling for the
        // abort; ops can tune WITHOUT a JS rebuild. 800ms is
        // comfortably above the prod p99 (~80ms) and below the
        // user-perceived "freeze" threshold (~1.5s).
        'preflightTimeoutMs' => (int) config('resumes.preflight_timeout_ms', 800),
        'usePresigned' => $usePresigned,
        'resourceType' => 'job_application',
    ];
@endphp

<input type="hidden" id="resumeSessionUuid" name="resume_session_uuid" value="">
<input type="hidden" id="resumeSessionUrl"   name="resume_session_url"   value="">
<input type="hidden" id="upload_session_uuid" name="upload_session_uuid" value="">

{{--
  Phase 9 — Independent progress block for the inline parse
  pipeline. Separate IDs from the upload bar (#resumeProgress)
  so the JS module can drive both simultaneously. Failed-cancelled
  stages hide the block via the parserProgressStage helper.
--}}
<div id="resumeParseProgress" class="mt-2" style="display:none">
  <div class="small text-muted mb-1 text-start">Parsing resume</div>
  <ol id="resumeParseProgressSteps"
      class="list-unstyled mb-0 text-start" style="font-size: 12px;">
    <li data-step="reading"   class="text-muted">Reading PDF…</li>
    <li data-step="extracting" class="text-muted">Extracting text…</li>
    <li data-step="skills"    class="text-muted">Extracting skills…</li>
    <li data-step="done"      class="text-muted">Complete ✓</li>
  </ol>
</div>

{{-- Phase 8 — company id is needed by JS subscribers before any input --}}
<script>
    window.COMPANY_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
</script>

<div id="resumeProgress" class="progress mt-2" style="height:8px;display:none" aria-hidden="true">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
         role="progressbar" style="width:0%" id="resumeProgressBar"
         aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
</div>

<div id="resumeError" class="alert alert-danger mt-2 d-flex align-items-center"
     style="display:none" role="alert"></div>

{{--
  Phase 9.2 — Cancel button. JavaScript (Phase 9.2 public API) attaches
  a click handler on this element IF it exists. Backward compat:
  forms that do not include this element still work — the JS
  helper is an explicit no-op without it. Sits next to the
  progress bar so the recruiter has an escape hatch during long
  uploads on slow connections.
--}}
<button type="button" id="resumeCancel"
        class="btn btn-sm btn-light mt-2"
        style="display:none">
    <i class="ki-duotone ki-cross fs-6 me-1"></i>Cancel upload
</button>

{{--
  Phase 7 — Status badge. Visible only after finalize() succeeds. Sufficiently
  prominent to satisfy "Display a clear 'Resume Uploaded' status" without
  adding visual noise when no file is selected.
--}}
<span id="resumeStatusBadge"
      class="badge badge-light-success d-inline-flex align-items-center gap-1 mt-2 px-3 py-2"
      style="display:none" role="status" aria-live="polite">
    <i class="ki-duotone ki-check-circle fs-6 text-success"></i>
    <span class="fw-semibold text-success">Resume Uploaded</span>
</span>

@once
    @push('scripts')
        <script src="{{ asset('js/resume-upload-progress.js') }}"></script>
        {{-- Phase 8 — populate-if-empty module subscribes to the
             company private channel and writes parsed values into
             empty inputs only. Loaded AFTER resume-upload-progress so
             its DOMContentLoaded handler runs after both: the parser
             wait and the live broadcast are wired together when a
             parser event lands while the recruiter is on the form. --}}
        <script src="{{ asset('js/resume-populate-if-empty.js') }}"></script>
        <script>
            (function () {
                if (!window.JERO_RESUME_CONFIG) {
                    window.JERO_RESUME_CONFIG = @json($config);
                }

                // Phase 9 — helper the upload-progress module calls
                // when the parser advance stage transitions land.
                // Same path-shape as setResumeProgress from Phase 7
                // so the host view can override it without touching
                // the module's internal stage vocabulary.
                window.setResumeParseProgress = function (stage) {
                    const el = document.getElementById('resumeParseProgress');
                    if (!el) { return; }
                    const order = ['reading', 'extracting', 'skills', 'done'];
                    if (stage === 'idle' || stage === 'failed' || stage === 'cancelled' || stage === 'replaced') {
                        el.style.display = 'none';
                        return;
                    }
                    el.style.display = '';
                    const idx = order.indexOf(stage);
                    el.querySelectorAll('li[data-step]').forEach(function (li) {
                        const step = li.getAttribute('data-step');
                        const stepIdx = order.indexOf(step);
                        if (stepIdx === -1) { return; }
                        if (stepIdx < idx) {
                            li.classList.add('text-success');
                            li.classList.remove('text-muted');
                            li.style.opacity = '1';
                        } else if (stepIdx === idx) {
                            li.classList.remove('text-muted');
                            li.style.opacity = '1';
                        } else {
                            li.classList.remove('text-success');
                            li.classList.add('text-muted');
                            li.style.opacity = '0.45';
                        }
                    });
                };

                window.addEventListener('DOMContentLoaded', function () {
                    if (typeof window.attachResumeUploadProgress !== 'function') return;
                    const cfg = window.JERO_RESUME_CONFIG;
                    window.attachResumeUploadProgress('resume', {
                        csrfToken: cfg.csrfToken,
                        presignUrl: cfg.presignUrl,
                        finalizeUrl: function (uuid) {
                            return (cfg.finalizeUrlTpl || '').replace('__UUID__', encodeURIComponent(uuid));
                        },
                        legacyUrl: cfg.legacyUrl,
                        parserUrl: cfg.parserUrl,
                        parserSubmitWaitMs: cfg.parserSubmitWaitMs || 2500,
                        preflightUrl: cfg.preflightUrl,
                        preflightTimeoutMs: cfg.preflightTimeoutMs || 800,
                        usePresigned: !!cfg.usePresigned,
                        resourceType: cfg.resourceType || 'job_application',
                        progressStage: window.setResumeProgress,
                        parserProgressStage: window.setResumeParseProgress,
                    });
                });
            })();
        </script>
    @endpush
@endonce
