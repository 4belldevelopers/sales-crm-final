/* eslint-disable no-console */
/**
 * Phase 4 — JS opt-in shim for presigned R2 PUT uploads.
 *
 * Why this exists (and why it's a thin ad-hoc shim, not a library):
 *   - We have three different form templates (recruiter ADD, recruiter EDIT,
 *     bench manager) at the time of writing, each with its own per-template
 *     JS handler. The plan was to wire presigned uploads through a single
 *     helper so the form-level migration is a copy/paste of three lines.
 *   - Phase 4 is opt-in. When the server flag `resumes.use_presigned_put`
 *     is FALSE, this shim ALSO short-circuits itself and lets the form's
 *     existing /uploadresume handler take over. That keeps backward compat
 *     with no breakage.
 *
 * Smart fallback ladder — applied in this order, with each step logged:
 *   1. Server flag OFF             → call uploadViaLegacy(formFile). STOP.
 *   2. presign endpoint returns 422
 *      with code=presign_unavailable
 *                                    → call uploadViaLegacy(formFile). STOP.
 *   3. presign endpoint returns 200 → PUT bytes directly to upload_url.
 *                                    If PUT throws (e.g. CORS) → fallback
 *                                    to uploadViaLegacy(formFile).
 *   4. PUT succeeds                 → finalize() flips session to Uploaded.
 *   5. The form's existing submit
 *      handler picks up the
 *      session_uuid from a hidden
 *      input and posts /store as
 *      normal. No change to the
 *      submit-side code path.
 *
 * Operational prerequisites (also documented in config/resumes.php):
 *   - R2 bucket CORS MUST allow PUT from app.jero.works with
 *     Content-Type and Content-Length headers.
 *   - Cloudflare R2 S3 endpoint is configured in services.env (already
 *     shipped).
 *
 * Usage from a Bootstrap 4 form (or any framework with a #resumeFile input):
 *
 *     <input type="file" id="resumeFile" name="resume_file" />
 *     <input type="hidden" id="resumeSessionUuid" name="resume_session_uuid" />
 *     <div id="resumeProgress" hidden><progress max="100" value="0"></progress></div>
 *     <div id="resumeError" class="alert alert-danger" hidden></div>
 *
 *     <script src="/js/resume-presign.js"></script>
 *     <script>
 *       window.JERO_RESUME_PRESIGN = window.JERO_RESUME_PRESIGN || {};
 *       window.JERO_RESUME_PRESIGN.csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
 *       window.JERO_RESUME_PRESIGN.presignUrl  = '/service_company/api/resume-sessions/presign';
 *       window.JERO_RESUME_PRESIGN.finalizeUrl = (uuid) => '/service_company/api/resume-sessions/' + uuid + '/finalize';
 *       window.JERO_RESUME_PRESIGN.legacyUrl   = '/service_company/uploadresume';
 *       window.JERO_RESUME_PRESIGN.jobId       = 123;  // optional
 *       window.JERO_RESUME_PRESIGN.onResult    = (result) => {
 *         // result = { session_uuid, r2_url, status, replayed } or null on legacy fallback
 *         if (result) {
 *           document.getElementById('resumeSessionUuid').value = result.session_uuid;
 *         }
 *       };
 *       window.attachResumePresign('resumeFile', window.JERO_RESUME_PRESIGN);
 *     </script>
 */
(function (window) {
    'use strict';

    async function attachResumePresign(fileInputId, opts) {
        const cfg = Object.assign({
            csrfToken: '',
            presignUrl: '/service_company/api/resume-sessions/presign',
            finalizeUrl: (uuid) => '/service_company/api/resume-sessions/' + uuid + '/finalize',
            legacyUrl: '/service_company/uploadresume',
            usePresigned: false,        // set from server-side via inline JS var
            progressElId: null,
            errorElId: null,
            jobId: null,
            resourceType: 'job_application',
            onResult: function () {},
            onError: function () {},
        }, opts || {});

        const fileInput = document.getElementById(fileInputId);
        if (!fileInput) {
            console.warn('[resume-presign] file input not found:', fileInputId);
            return;
        }

        const progressEl = cfg.progressElId ? document.getElementById(cfg.progressElId) : null;
        const errorEl    = cfg.errorElId    ? document.getElementById(cfg.errorElId)    : null;
        const showError  = (msg) => {
            if (!errorEl) { console.error('[resume-presign]', msg); return; }
            errorEl.textContent = msg;
            errorEl.hidden = false;
        };
        const clearError = () => {
            if (!errorEl) return;
            errorEl.textContent = '';
            errorEl.hidden = true;
        };
        const showProgress = (val) => {
            if (!progressEl) return;
            const bar = progressEl.querySelector('progress') || progressEl;
            if ('value' in bar) bar.value = val;
            progressEl.hidden = val >= 100;
        };

        fileInput.addEventListener('change', async (ev) => {
            clearError();
            const file = ev.target.files && ev.target.files[0];
            if (!file) {
                cfg.onResult(null);
                return;
            }

            // Step 0: pay attention to the server-side flag. If the
            // config block disables presign, we never call the endpoint.
            if (!cfg.usePresigned) {
                console.log('[resume-presign] disabled by config; falling back to legacy');
                await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                return;
            }

            // Step 1: ask the server for a presigned URL.
            let presignResp;
            try {
                showProgress(5);
                presignResp = await fetch(cfg.presignUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrfToken,
                        'Idempotency-Key': cryptoRandomIdempotencyKey(),
                    },
                    body: JSON.stringify({
                        original_filename: file.name,
                        mime_type: file.type || undefined,
                        size_bytes: file.size,
                        job_id: cfg.jobId,
                        resource_type: cfg.resourceType,
                    }),
                });
            } catch (networkErr) {
                showProgress(0);
                showError('Network error contacting server; falling back.');
                await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                return;
            }

            showProgress(15);

            if (presignResp.status === 422) {
                // Typed error from server (e.g. env has no S3 adapter).
                let body;
                try { body = await presignResp.json(); } catch (_) { body = {}; }
                if (body && body.code === 'presign_unavailable') {
                    console.log('[resume-presign] server says presign unavailable; falling back');
                    showError('Using direct upload (server lacked R2 signing capability).');
                    await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                    return;
                }
                showError('Server rejected the upload: ' + (body.message || presignResp.status));
                cfg.onError(new Error('presign rejected'));
                return;
            }

            if (!presignResp.ok) {
                showError('Server error (' + presignResp.status + '); falling back.');
                await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                return;
            }

            let presignJson;
            try {
                presignJson = await presignResp.json();
            } catch (e) {
                showError('Server returned an unreadable response.');
                cfg.onError(e);
                return;
            }

            // Step 2: PUT bytes directly to R2 via the signed URL.
            showProgress(20);
            try {
                await putToPresignedUrl(
                    presignJson.upload_url,
                    presignJson.headers || {},
                    file,
                    (pct) => showProgress(20 + Math.floor(pct * 0.7)),
                );
            } catch (putErr) {
                console.warn('[resume-presign] PUT failed; falling back', putErr);
                showError('Direct upload blocked; retrying through server.');
                await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                return;
            }

            showProgress(90);

            // Step 3: tell the server the bytes landed; flip status=Uploaded.
            try {
                const finResp = await fetch(cfg.finalizeUrl(presignJson.session_uuid), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrfToken,
                    },
                    body: JSON.stringify({ client_bytes: file.size }),
                });
                if (!finResp.ok) {
                    let body;
                    try { body = await finResp.json(); } catch (_) { body = {}; }
                    showError('Server could not verify the upload (' + (body.reason || 'unknown') + ').');
                    cfg.onError(new Error('finalize failed'));
                    return;
                }
            } catch (networkErr) {
                console.warn('[resume-presign] finalize network error; legacy fallback', networkErr);
                await uploadViaLegacy(file, cfg, cfg.onResult, showError);
                return;
            }

            showProgress(100);
            cfg.onResult({
                session_uuid: presignJson.session_uuid,
                r2_url: presignJson.r2_url,
                status: 'Uploaded',
                replayed: !!presignJson.replayed,
            });
        });
    }

    function putToPresignedUrl(url, extraHeaders, file, onProgress) {
        return new Promise((resolve, reject) => {
            try {
                const xhr = new XMLHttpRequest();
                xhr.open('PUT', url, true);
                xhr.withCredentials = false; // R2 doesn't accept browser creds.

                if (file.type) {
                    xhr.setRequestHeader('Content-Type', file.type);
                }
                Object.keys(extraHeaders || {}).forEach((h) => {
                    if (h && h.toLowerCase() !== 'content-type') {
                        xhr.setRequestHeader(h, extraHeaders[h]);
                    }
                });

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable && typeof onProgress === 'function') {
                        onProgress(e.loaded / e.total);
                    }
                };
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve();
                    } else {
                        reject(new Error('R2 PUT failed: HTTP ' + xhr.status));
                    }
                };
                xhr.onerror = () => reject(new Error('R2 PUT network error'));
                xhr.send(file);
            } catch (e) {
                reject(e);
            }
        });
    }

    async function uploadViaLegacy(file, cfg, onResult, showError) {
        showError('Using direct upload (server fallback)');
        const fd = new FormData();
        fd.append('resume_file', file);
        if (cfg.jobId) fd.append('job_id', cfg.jobId);
        try {
            const r = await fetch(cfg.legacyUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrfToken,
                },
                body: fd,
            });
            if (!r.ok) {
                let msg = 'Legacy upload failed (' + r.status + ')';
                try {
                    const j = await r.json();
                    if (j && j.message) msg = j.message;
                } catch (_) { /* ignore */ }
                showError(msg);
                return;
            }
            const data = await r.json().catch(() => ({}));
            onResult({
                session_uuid: data.session_uuid || null,
                r2_url: data.file_path || data.url || null,
                status: 'Uploaded',
                replayed: !!data.replayed,
            });
        } catch (e) {
            showError('Network error during legacy upload.');
            console.error('[resume-presign]', e);
        }
    }

    function cryptoRandomIdempotencyKey() {
        // Don't burn a high-quality UUID format for an idempotency key —
        // the server stores up to 128 chars and looks for collisions only.
        if (window.crypto && window.crypto.randomUUID) {
            return 'ui-' + window.crypto.randomUUID();
        }
        return 'ui-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    }

    window.attachResumePresign = attachResumePresign;
})(window);
