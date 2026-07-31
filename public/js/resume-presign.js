/**
 * Intent: v2 resume upload — presign + direct R2 PUT (zero PHP bytes).
 *
 * Flow per file pick (INV-1: each pick = new session):
 *   1. Generate crypto.randomUUID() as Idempotency-Key (fresh per call)
 *   2. POST /resume/v2/sessions with metadata → receive presigned PUT URL
 *   3. PUT file directly to R2 via XHR (real progress events)
 *   4. Store session UUID for submit handler (Phase 4 atomically finalizes)
 *
 * Transport retry safety (INV-4):
 *   Same Idempotency-Key header is sent on every presign retry so the server
 *   replays the same session rather than creating a duplicate.
 *
 * Phase 6 — Network resilience:
 *   - R2 PUT retry: up to 3 attempts with exponential back-off (1s, 2s, 4s).
 *     Network errors and HTTP 5xx are retried; 4xx (bad presign, expired URL) are not.
 *   - Offline detection: retries pause on navigator.onLine === false and resume
 *     automatically on the browser 'online' event — no lost uploads when briefly offline.
 *   - recover(): reads the localStorage UUID → pings the status endpoint → restores
 *     UPLOADED state if the file is still in R2. Survives page refresh mid-upload.
 *
 * Feature flag: window.RESUMES_FEATURE_BROWSER_UPLOAD (set by blade).
 * Presign URL:  window.RESUME_PRESIGN_URL (set by blade via route()).
 * Config overrides (optional): window.RESUMES_CONFIG { maxPutAttempts, putBackoffMs }
 */

window.ResumePresign = (function () {
    'use strict';

    const STATE = {
        IDLE:      'idle',
        UPLOADING: 'uploading',
        UPLOADED:  'uploaded',
        FAILED:    'failed',
        CANCELLED: 'cancelled',
    };

    let _state         = STATE.IDLE;
    let _sessionUuid   = null;
    let _uploadXHR     = null;
    const _listeners   = [];

    // Runtime config — blade can inject overrides via window.RESUMES_CONFIG.
    const _cfg = Object.assign({
        maxPutAttempts: 3,
        putBackoffMs:   1000,   // base delay; doubled each retry (1s → 2s → 4s)
    }, window.RESUMES_CONFIG || {});

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Start a fresh upload for the given File object.
     * Generates a new Idempotency-Key → always a new session (INV-1).
     * Returns a Promise that resolves with the session object on success.
     */
    async function startUpload(file) {
        if (_state === STATE.UPLOADING) {
            throw new Error('Upload already in progress');
        }

        _sessionUuid = null;
        _setSessionUuidGlobal(null);
        _setState(STATE.UPLOADING, 0);

        const idempotencyKey = crypto.randomUUID();
        const sha256Hex = await _sha256(file).catch(() => null);

        try {
            const session = await _presign(file, sha256Hex, idempotencyKey);
            _sessionUuid = session.uuid;
            _setSessionUuidGlobal(session.uuid);
            _persistSession(session.uuid, idempotencyKey);

            await _putToR2(file, session.upload_url);

            _setState(STATE.UPLOADED, 100);
            return session;
        } catch (err) {
            _setState(STATE.FAILED, 0, err.message);
            throw err;
        }
    }

    /**
     * Abort an in-progress upload and reset state.
     * Safe to call at any time, including when idle.
     */
    function cancelUpload() {
        if (_uploadXHR) {
            _uploadXHR.abort();
            _uploadXHR = null;
        }
        _sessionUuid = null;
        _setSessionUuidGlobal(null);
        _clearPersistedSession();
        _setState(STATE.CANCELLED, 0);
    }

    /** Returns the session UUID of the last successful upload, or null. */
    function getSessionUuid() {
        return _sessionUuid;
    }

    /**
     * Subscribe to state changes.
     * Callback receives: { state, progress, error, sessionUuid }
     * Returns an unsubscribe function.
     */
    function onStateChange(callback) {
        _listeners.push(callback);
        return function () {
            var i = _listeners.indexOf(callback);
            if (i > -1) _listeners.splice(i, 1);
        };
    }

    /**
     * Phase 6 — Recover a previous upload session from localStorage.
     *
     * Reads the stored UUID, pings GET /resume/v2/sessions/{uuid}, and restores
     * UPLOADED state if the file is still in R2. The submit handler can then
     * claim the session atomically without re-uploading.
     *
     * Call once on DOM-ready when the feature flag is ON.
     * Returns the session object on success, or null if nothing to recover.
     */
    async function recover() {
        var storedUuid = null;
        try { storedUuid = localStorage.getItem('resume_v2_session_uuid'); } catch (_e) {}

        if (!storedUuid || _sessionUuid) return null;   // nothing stored, or active session

        var baseUrl = window.RESUME_PRESIGN_URL;
        if (!baseUrl) return null;

        try {
            var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            var statusUrl = baseUrl.replace(/\/+$/, '') + '/' + storedUuid;
            var resp = await fetch(statusUrl, {
                method:  'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN':     csrfToken,
                },
            });

            if (!resp.ok) {
                _clearPersistedSession();
                return null;
            }

            var body = await resp.json();

            if (body.status === 'Uploaded' || body.status === 'Completed') {
                // File is in R2 — restore state so the submit handler can claim it.
                _sessionUuid = storedUuid;
                _setSessionUuidGlobal(storedUuid);
                _setState(STATE.UPLOADED, 100);
                return body;
            }

            // Terminal or unknown state — clear stale localStorage entry.
            _clearPersistedSession();
            return null;
        } catch (_e) {
            // Network error during recovery check — non-fatal, fresh upload is available.
            return null;
        }
    }

    // ─── Internal ─────────────────────────────────────────────────────────────

    async function _presign(file, sha256Hex, idempotencyKey) {
        const url = window.RESUME_PRESIGN_URL;
        if (!url) throw new Error('RESUME_PRESIGN_URL not set');

        const csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content;
        if (!csrfToken) throw new Error('CSRF token not found in page');

        let lastErr;
        for (var attempt = 0; attempt < 3; attempt++) {
            if (attempt > 0) await _sleep(300 * Math.pow(2, attempt));

            let resp;
            try {
                resp = await fetch(url, {
                    method:  'POST',
                    headers: {
                        'Content-Type':    'application/json',
                        'X-CSRF-TOKEN':    csrfToken,
                        'Idempotency-Key': idempotencyKey,  // same key on retry (INV-4)
                    },
                    body: JSON.stringify({
                        original_filename: file.name,
                        file_size_bytes:   file.size,
                        mime_type:         file.type || 'application/octet-stream',
                        sha256_hex:        sha256Hex,
                    }),
                });
            } catch (networkErr) {
                lastErr = networkErr;
                continue;
            }

            if (resp.status === 429) throw new Error('Upload rate limit reached. Please wait a moment and try again.');
            if (resp.status === 404) throw new Error('Upload feature is not enabled.');

            let body;
            try { body = await resp.json(); } catch (_) { body = {}; }

            if (!resp.ok) {
                throw new Error(body.message || ('Presign request failed (' + resp.status + ')'));
            }

            return body;
        }

        throw lastErr || new Error('Presign request failed after retries');
    }

    /**
     * PUT the file to the presigned R2 URL with automatic retry.
     *
     * Retry policy:
     *   - Network errors (xhr.onerror) and HTTP 5xx → retryable
     *   - HTTP 4xx (expired presign URL, bad content-type) → not retryable
     *   - Cancelled state → not retryable
     *   - Back-off: putBackoffMs × 2^(attempt-1)  (1s, 2s, 4s with defaults)
     *   - Pauses automatically when navigator.onLine is false
     */
    async function _putToR2(file, uploadUrl) {
        for (var attempt = 0; attempt < _cfg.maxPutAttempts; attempt++) {
            if (attempt > 0) {
                await _waitUntilOnline();
                await _sleep(_cfg.putBackoffMs * Math.pow(2, attempt - 1));
                // Reset progress bar to show retry is starting fresh.
                if (_state !== STATE.CANCELLED) {
                    _setState(STATE.UPLOADING, 0);
                }
            }

            try {
                await _putToR2Once(file, uploadUrl);
                return;     // success — exit loop
            } catch (err) {
                var isLast = (attempt === _cfg.maxPutAttempts - 1);
                if (isLast || !err.retryable) throw err;
                // else: loop continues with the next attempt
            }
        }
    }

    /** Single PUT attempt — resolves on 2xx, rejects with err.retryable set. */
    function _putToR2Once(file, uploadUrl) {
        return new Promise(function (resolve, reject) {
            if (_state === STATE.CANCELLED) {
                var cancelled = new Error('Upload cancelled');
                cancelled.retryable = false;
                reject(cancelled);
                return;
            }

            var xhr = new XMLHttpRequest();
            _uploadXHR = xhr;

            xhr.open('PUT', uploadUrl, true);
            xhr.setRequestHeader('Content-Type', file.type || 'application/octet-stream');

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    _setState(STATE.UPLOADING, Math.round((e.loaded / e.total) * 100));
                }
            };

            xhr.onload = function () {
                _uploadXHR = null;
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve();
                } else {
                    // Only 5xx (transient server error) is retryable.
                    // 4xx usually means the presigned URL has expired or is malformed.
                    var err = new Error('R2 upload failed (' + xhr.status + '). Please try again.');
                    err.retryable = (xhr.status >= 500);
                    reject(err);
                }
            };

            xhr.onerror = function () {
                _uploadXHR = null;
                var err = new Error('Network error during upload. Check your connection and try again.');
                err.retryable = true;
                reject(err);
            };

            xhr.ontimeout = function () {
                _uploadXHR = null;
                var err = new Error('Upload timed out. Please try again.');
                err.retryable = true;
                reject(err);
            };

            xhr.send(file);
        });
    }

    /**
     * Resolves immediately if online, otherwise waits for the browser 'online' event.
     * Prevents retries from firing into a dead network stack.
     */
    function _waitUntilOnline() {
        return new Promise(function (resolve) {
            if (navigator.onLine !== false) { resolve(); return; }
            var handler = function () {
                window.removeEventListener('online', handler);
                resolve();
            };
            window.addEventListener('online', handler);
        });
    }

    async function _sha256(file) {
        var buf = await file.arrayBuffer();
        var hashBuf = await crypto.subtle.digest('SHA-256', buf);
        return Array.from(new Uint8Array(hashBuf))
            .map(function (b) { return b.toString(16).padStart(2, '0'); })
            .join('');
    }

    function _setState(state, progress, error) {
        _state = state;
        var payload = { state: state, progress: progress || 0, error: error || null, sessionUuid: _sessionUuid };
        _listeners.forEach(function (fn) { try { fn(payload); } catch (_) {} });
    }

    function _setSessionUuidGlobal(uuid) {
        window.__resumeV2SessionUuid = uuid || null;
        var el = document.getElementById('resume_session_uuid');
        if (el) el.value = uuid || '';
    }

    function _persistSession(uuid, key) {
        try {
            localStorage.setItem('resume_v2_session_uuid', uuid);
            localStorage.setItem('resume_v2_idempotency_key', key);
        } catch (_) {}
    }

    function _clearPersistedSession() {
        try {
            localStorage.removeItem('resume_v2_session_uuid');
            localStorage.removeItem('resume_v2_idempotency_key');
        } catch (_) {}
    }

    function _sleep(ms) {
        return new Promise(function (r) { setTimeout(r, ms); });
    }

    // ─── Public surface ───────────────────────────────────────────────────────

    return {
        startUpload:    startUpload,
        cancelUpload:   cancelUpload,
        getSessionUuid: getSessionUuid,
        onStateChange:  onStateChange,
        recover:        recover,        // Phase 6: page-refresh session recovery
        STATE:          STATE,
    };
})();
