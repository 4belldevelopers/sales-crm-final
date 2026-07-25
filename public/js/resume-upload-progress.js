/* eslint-disable no-console */
/**
 * Phase 7 + Phase 8 — Enterprise Resume Upload Progress.
 *
 * Attaches to a file input (default `#resume`, falls back to `#resumeFile`)
 * and drives a non-blocking direct-to-R2 upload with realtime progress
 * (percent, speed, ETA). Exposes a Promise-based wait API so the form's
 * existing submit handler can hold the click until the upload resolves,
 * without disabling inputs or requiring a second click.
 *
 * Phase 8 added the parsing wait: when the recruiter picks a file,
 * the bytes are on R2 in ~3 s. Parsing is async on a dedicated
 * `parse_resumes` queue, but the recruiter may submit the form
 * before the parser worker returns. The phase 8 contract is that
 * if the candidate already exists (was submitted before parsing
 * finished), the parser silently fills only EMPTY columns on the
 * candidate row and then broadcasts `resume.parsed` over Reverb.
 * The form's submit handler bridges `whenReady()` for upload
 * (Phase 7) + `whenParsed()` (Phase 8) + `whenParsedInline()` (Phase 9).
 *
 * Phase 9 added the parallel instant-field-extraction pipeline:
 *   - The file pick handler now dispatches TWO independent network
 *     fan-outs simultaneously: the existing presign-R2-PUT finalizer
 *     AND a POST /api/resume-parser/parse with the same file blob.
 *   - The parser endpoint is read-only (NO session row, NO audit
 *     row, NO queue dispatch) and returns a structured JSON hint
 *     bag in <1 s for a typical 2 MB PDF.
 *   - On resolve, the same `populate-if-empty` contract as Phase 8
 *     (write only empty fields) is applied via the inline event —
 *     recruiters see form fields fill BEFORE the upload finishes.
 *   - On replace/abort, the parser XHR is cancelled with AbortController
 *     so we never parse the dropped file.
 *   - On submit, the handler awaits `whenParsedInline({timeoutMs:2500})`
 *     with a 2.5 s cap. If parse is still running past 2.5 s, the
 *     submit proceeds — the parser keeps going after submit because
 *     there is nothing server-side to stop it.
 *
 * Smart fallback ladder (mirrors Phase 4 `resume-presign.js`):
 *   1. Server flag OFF             → POST `/uploadresume` with multipart.
 *                                    xhr.upload.onprogress drives the bar.
 *   2. presign returns 422 + code=presign_unavailable
 *                                    → fall back to multipart (1 above).
 *   3. presign returns 200         → PUT bytes directly to R2.
 *                                    PUT network error / CORS → (1).
 *   4. PUT succeeds                 → finalize() flips row to Uploaded
 *                                    → QueuedForParsing + dispatches parser.
 *   5. finalize network error       → fall back to multipart (1) ONLY
 *                                    if bytes are not yet recorded server-side;
 *                                    we always read ready-state honestly so
 *                                    no submit-time double-spend occurs.
 *
 * Non-blocking contract:
 *   - `ResumeUpload.whenReady({ timeoutMs })` resolves:
 *       { ok: true,  state: 'completed'|'idle' }   — submit can proceed
 *       { ok: false, reason: 'timeout' }            — submit should abort
 *       { ok: false, reason: 'replaced'|'cancelled'|...'error' }
 *   - The promise NEVER rejects; submit handlers can await it without
 *     needing a `try/catch`. Internal errors are surfaced via the
 *     `#resumeError` element + structured `event()` calls the host may
 *     attach a listener to (`jobs.resume.upload.*` namespace).
 *
 * DOM hooks (all optional — the shim degrades gracefully when absent):
 *   - #resumeProgress       (Bootstrap-style bar container, from partial)
 *   - #resumeProgressBar    (the inner styled bar)
 *   - #resumeError          (alert container, from partial)
 *   - #resumeStatusBadge    (green "Resume Uploaded" badge, added in Phase 7)
 *   - window.setResumeProgress(stage)  — server-side blade wires this for the
 *                          submit-side stepper; using it here keeps stages
 *                          visually continuous with the rest of the form.
 *
 * Required config (set from server via @json in resume_presign_setup.blade.php):
 *   - csrfToken, presignUrl, finalizeUrl(uuid), legacyUrl
 *   - usePresigned   boolean — flips the entire ladder to multipart
 *   - jobId          optional server-side job context
 *   - maxBytes       default 10 * 1024 * 1024 (matches the Blade `accept`)
 *   - allowedExtensions default ['pdf','doc','docx']
 */
(function (window) {
  'use strict';

  // ---- Internal state -----------------------------------------------------

  const DEFAULT_CFG = {
    fileInputId: 'resume',                  // primary attach target
    fallbackInputId: 'resumeFile',          // legacy forms
    csrfToken: '',
    presignUrl: '/service_company/api/resume-sessions/presign',
    finalizeUrl: function (uuid) {
      return '/service_company/api/resume-sessions/' + encodeURIComponent(uuid) + '/finalize';
    },
    legacyUrl: '/service_company/uploadresume',
    // Phase 9 — read-only instant-field-extraction endpoint. Sibling
    // to the upload fan-out; does NOT touch resume_uploads, audit,
    // or the parse queue. Returns a structured hint bag in <1 s.
    parserUrl: '/api/resume-parser/parse',
    // Phase 9 follow-up — submit-wait ceiling. Server-side default
    // is 2500 ms but the JS module reads from cfg.parserSubmitWaitMs
    // if supplied so ops can tune via env without rebuilding JS.
    parserSubmitWaitMs: 2500,
    usePresigned: false,
    jobId: null,
    resourceType: 'job_application',
    maxBytes: 10 * 1024 * 1024,
    allowedExtensions: ['pdf', 'doc', 'docx'],
    // Existing DOM target IDs produced by the Blade partial:
    progressBarContainerId: 'resumeProgress',
    progressBarInnerId: 'resumeProgressBar',
    errorElementId: 'resumeError',
    statusBadgeId: 'resumeStatusBadge',
    // Phase 9 — independent parallel parser progress block.
    parserProgressElementId: 'resumeParseProgress',
    parserProgressStepsId: 'resumeParseProgressSteps',
    // The submit-side stepper helper exposed by the Blade view:
    progressStage: function (stage) {
      if (typeof window.setResumeProgress === 'function') {
        try { window.setResumeProgress(stage); } catch (_) { /* ignore */ }
      }
    },
    // Phase 9 — parser-side stepper helper. Walks the 4 stages
    // (reading → extracting → skills → done). Initial 'idle' or
    // terminal 'failed' hides the block so it does not linger
    // after the parse is done. The host blade wires this to a
    // window.setResumeParseProgress function if available.
    parserProgressStage: function (stage) {
      if (typeof window.setResumeParseProgress === 'function') {
        try { window.setResumeParseProgress(stage); } catch (_) { /* ignore */ }
      }
    },
  };

  // One mutable state object per attached input instance.
  let _state = initialState();
  let _waiters = [];   // pending promises awaiting state === 'completed' / 'idle'
  let _inflightXhr = null;
  let _attachedInput = null;
  let _attachedCfg = null;

  function initialState() {
    return {
      stage: 'idle',                  // 'idle'|'presigning'|'uploading'|'finalizing'|'completed'|'failed'|'cancelled'|'replaced'
      percent: 0,
      uploadedBytes: 0,
      totalBytes: 0,
      speedBps: 0,
      etaMs: null,                    // null = "—" / unknown
      startedAt: 0,
      lastProgressAt: 0,
      lastLoadedDeltaWindow: [],      // [{ts, loaded}, ...] for rolling speed
      error: null,
      sessionUuid: null,
      r2Url: null,
      resolved: true,                 // true ⇔ whenReady() resolves immediately
      timedOut: false,
      // Phase 9 — parallel parser pipeline. Independent of the
      // upload state above: success/failure of the parser never
      // transitions the upload pipeline. The AbortController lets
      // a re-pick cancel the in-flight parser XHR — release notes
      // document this as "No duplicate parsing".
      parserAbortController: null,
      parserInflightXhr: null,
      parserResult: null,             // resolved hint bag, null until parser returns ok
      parserStage: 'idle',            // 'idle'|'reading'|'extracting'|'skills'|'done'|'failed'|'cancelled'|'replaced'
      parserStartedAt: 0,
      // Phase 9 — epoch guard for cross-fan-out safety. Each call to
      // _runInlineParserFanOut increments this; every closure created
      // inside that fan-out captures the value it observed and bails
      // out if _state.parserFanOutEpoch has moved on, so a stale
      // onload/onerror/onabort from a cancelled XHR cannot stomp on
      // the state set by the new fan-out. Counter only — monotonic,
      // resets on detach().
      parserFanOutEpoch: 0,
      // Phase 9.1 — network-resilience cache. We snapshot the picked
      // File + Idempotency-Key here so an explicit Retry (after a
      // dropped connection) can re-emit a POST to the same session
      // row. The cached File is NOT retained in production for
      // tenants with high-paranoia memory pressure — see detach()
      // and resetState() which both null these out explicitly.
      lastFile: null,
      lastIdempotencyKey: null,
      // Phase 9.2 — auto-retry counter for the `online` listener.
      // Counts re-emissions of the upload pipeline WITHOUT manual
      // recruiter intervention. Manual `Retry.upload()` clicks
      // RESET this back to 0 so a recruiter-driven retry is not
      // capped by the auto-retry ceiling. Hard cap is 3 attempts —
      // a 4th blip and we surface a calm message and stay quiet
      // until the recruiter acts.
      autoRetryAttempts: 0,
      // Phase 9.2 — recorder-controlled opt-out for auto-retry. If
      // the recruiter clicks Retry first, the next `online` event
      // MUST NOT auto-retry — they already showed intent. They can
      // re-click Retry if it failed. Set true by `Retry.upload()`.
      autoRetrySuppressed: false,
      // Phase 9.2 — parser rate-limit guard. Set when the parser
      // XHR returns 429 with Retry-After. While > now() the parser
      // pipeline rejects re-picks + manual retries; we re-pick ONCE
      // after the timestamp elapses.
      parserRateLimitedUntil: 0,
      parserRateLimitedRetryCount: 0,
      // Browser's network state. 'online' is the default; 'offline'
      // is set the moment the `offline` event fires. We do NOT auto-
      // retry on `online` because the upload pipeline and the
      // candidate-POST pipeline are not idempotent — the window's
      // own gateway might flap. Recruiter-confirmed retry is safer.
      networkStatus: (typeof navigator !== 'undefined' && navigator.onLine === false) ? 'offline' : 'online',
      networkLostAt: 0,
      networkRestoredAt: 0,
    };
  }

  // ---- Public API ---------------------------------------------------------

  window.ResumeUpload = {
    state: function () { return Object.assign({}, _state); },

    whenReady: function (opts) {
      const timeoutMs = (opts && typeof opts.timeoutMs === 'number') ? opts.timeoutMs : 30000;
      // Fast path — already resolved, no upload is in flight, OR upload
      // completed in the past. We never re-await a settled state.
      if (_state.stage === 'idle' || _state.stage === 'completed') {
        return Promise.resolve({ ok: true, state: _state.stage });
      }
      if (_state.stage === 'failed') {
        return Promise.resolve({ ok: false, reason: 'failed', error: _state.error });
      }
      if (_state.stage === 'cancelled' || _state.stage === 'replaced') {
        return Promise.resolve({ ok: false, reason: _state.stage });
      }
      return new Promise(function (resolve) {
        const startedAt = Date.now();
        const idx = _waiters.push({ resolve: resolve, startedAt: startedAt, timeoutMs: timeoutMs });
        // Lazy timeout — keeps the Promise resolved, never rejected.
        const tick = setInterval(function () {
          const w = _waiters[idx - 1];
          if (!w) { clearInterval(tick); return; }
          if (_state.stage === 'idle' || _state.stage === 'completed') {
            clearInterval(tick); _waiters.splice(idx - 1, 1);
            w.resolve({ ok: true, state: _state.stage });
            return;
          }
          if (_state.stage === 'failed' || _state.stage === 'cancelled' || _state.stage === 'replaced') {
            clearInterval(tick); _waiters.splice(idx - 1, 1);
            w.resolve({ ok: false, reason: _state.stage, error: _state.error });
            return;
          }
          if (Date.now() - startedAt >= w.timeoutMs) {
            clearInterval(tick); _waiters.splice(idx - 1, 1);
            w.resolve({ ok: false, reason: 'timeout' });
          }
        }, 200);
      });
    },

    /**
     * Phase 8 — wait for the parser to complete.
     *
     * Subscribes to the company-scoped private channel
     * `App.Models.Company.{cid}` and listens for the
     * `resume.parsed` event. Resolves when an event arrives whose
     * `session_uuid` matches the upload we're waiting for.
     *
     * If Echo is unavailable (Reverb not wired, `window.Echo` is
     * undefined, or the page does not include Echo's bootstrap
     * bundle) we fall through to polling the existing
     * `/api/resume-sessions/{uuid}` show endpoint every 5 s for
     * `parsed_data_json` non-empty or `status === 'ParseFailed'`.
     * The polling fallback is slower but always-available — the
     * contract is "we resolve eventually" not "we resolve fast".
     *
     * NEVER rejects. Promise resolves:
     *   { ok: true,  parsed: { fillable: {}, ... },  via: 'reverb'|'poll' }
     *   { ok: false, reason: 'timeout'|'no_session'|'parse_failed' }
     *   { ok: false, reason: 'no_echo_fallback', error: string }
     *
     * Default timeout is 60 s — vendor parses typically finish in
     * 4-30 s today; user-mounted Reverb is a few hundred ms. A
     * 60 s cap is a safety net for vendor outages.
     */
    whenParsed: function (opts) {
      const timeoutMs = (opts && typeof opts.timeoutMs === 'number') ? opts.timeoutMs : 60000;
      const sessionUuid = (opts && typeof opts.sessionUuid === 'string') ? opts.sessionUuid : _state.sessionUuid;
      if (! sessionUuid) {
        return Promise.resolve({ ok: false, reason: 'no_session' });
      }

      const sessionUrl = (opts && typeof opts.sessionUrl === 'string')
        ? opts.sessionUrl
        : (window.JERO_RESUME_CONFIG && window.JERO_RESUME_CONFIG.sessionShowUrlTpl
            ? window.JERO_RESUME_CONFIG.sessionShowUrlTpl.replace('__UUID__', encodeURIComponent(sessionUuid))
            : '/service_company/api/resume-sessions/' + encodeURIComponent(sessionUuid));

      return new Promise(function (resolve) {
        let resolved = false;
        let stopBroadcast = function () {};
        let stopPoller = function () {};

        const finish = function (payload) {
          if (resolved) { return; }
          resolved = true;
          stopBroadcast();
          stopPoller();
          resolve(payload);
        };

        // Reverb/Echo fast path.
        if (window.Echo && typeof window.Echo.private === 'function') {
          const cid = window.COMPANY_ID || 0;
          if (cid) {
            const channel = window.Echo.private('App.Models.Company.' + cid);
            channel.listen('.resume.parsed', function (e) {
              if (! e || e.session_uuid !== sessionUuid) { return; }
              finish({
                ok: e.status === 'Parsed',
                parsed: e,
                via: 'reverb',
                reason: e.status === 'ParseFailed' ? 'parse_failed' : undefined,
              });
            });
            stopBroadcast = function () {
              try { channel.stopListening('.resume.parsed'); } catch (_) {}
            };
          }
        }

        // Polling fallback. We poll every 5 s until the row shows
        // `status === 'Parsed'` OR `parsed_data_json` has been
        // populated, OR `status === 'ParseFailed'`. The endpoint
        // `/api/resume-sessions/{uuid}` already exists.
        const startedAt = Date.now();
        const poll = function () {
          if (Date.now() - startedAt >= timeoutMs) {
            finish({ ok: false, reason: 'timeout' });
            return;
          }
          fetch(sessionUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken() },
          })
            .then(function (r) {
              if (! r.ok) {
                throw new Error('polling failed: HTTP ' + r.status);
              }
              return r.json();
            })
            .then(function (json) {
              if (! json || ! json.status) {
                throw new Error('polling returned no status');
              }
              if (json.status === 'Parsed') {
                finish({ ok: true, parsed: json, via: 'poll' });
                return;
              }
              if (json.status === 'ParseFailed') {
                finish({ ok: false, reason: 'parse_failed', parsed: json, via: 'poll' });
                return;
              }
              // else: keep polling.
            })
            .catch(function () {
              // swallow — the next tick will retry.
            });
        };
        const pollerHandle = setInterval(poll, 5000);
        poll(); // first attempt immediately
        stopPoller = function () { clearInterval(pollerHandle); };

        // Hard timeout guard — the poller cannot escalate past
        // `timeoutMs`, but we belt-and-suspender an outer timer
        // so a stuck poll still resolves.
        setTimeout(function () { finish({ ok: false, reason: 'timeout' }); }, timeoutMs + 1000);
      });
    },

    cancel: function () {
      _abortInflight('cancelled');
      // Phase 9.2 — recruiter explicitly abandoned this pick.
      // Drop the localStorage session uuid so a future visit
      // does not surface a stale "Resume Uploaded" badge. The
      // R2 row remains for the GC window — that's fine.
      try { _clearSessionCache(); } catch (_) {}
    },

    /**
     * Phase 9.1 — Manually re-issue the upload pipeline using the
     * LAST picked file and the SAME Idempotency-Key. The server's
     * createOrPresign / createOrReplay path rejoins the existing
     * resume_uploads row when the idempotency key matches — no new
     * session row, no new R2 object, no new audit entries.
     *
     * Returns true if a retry was scheduled, false if there is no
     * cached file (recruiter must pick first). NEVER rejects.
     */
    retryUpload: function () {
      if (!_state.lastFile) {
        return Promise.resolve({ ok: false, reason: 'no_file' });
      }
      // Re-arm the pipeline with the cached File + idempotency
      // key. _attachCfg must be set or we have nothing to retry
      // against. The state is reset to initial() then re-run with
      // the same cfg the page originally wired.
      const file = _state.lastFile;
      const cfg = _attachedCfg || DEFAULT_CFG;
      _state.stage = 'presigning';
      _state.startedAt = Date.now();
      _state.error = null;
      // Phase 9.2 — recruiter-driven retry takes the wheel away
      // from auto-retry. The next `online` event will be a no-op
      // unless they refresh/decline the manual retry.
      _state.autoRetrySuppressed = true;
      _state.autoRetryAttempts = 0;
      _setProgressBar(0);
      _showProgressBar();
      _hideError();
      _emitEvent('upload.retry_started', { size: file.size });
      if (cfg.usePresigned) {
        _runPresignedFlow(file, cfg);
      } else {
        _runLegacyMultipartFlow(file, cfg);
      }
      return Promise.resolve({ ok: true, scheduled: true });
    },

    /**
     * Phase 9.1 — Re-run the parallel instant-field-extraction
     * pipeline against the LAST picked file. The parser endpoint is
     * read-only so a retry side-effect is the parser counter and
     * any field-fill events; no DB row, no audit row, no candidate
     * write. Safe to invoke arbitrarily.
     *
     * Returns { ok: false, reason: 'no_file'|'in_flight' } on
     * degenerate conditions.
     */
    retryParser: function () {
      if (! _state.lastFile) {
        return Promise.resolve({ ok: false, reason: 'no_file' });
      }
      if (_state.parserInflightXhr && _state.parserStage !== 'done'
          && _state.parserStage !== 'failed' && _state.parserStage !== 'cancelled'
          && _state.parserStage !== 'replaced' && _state.parserStage !== 'idle') {
        return Promise.resolve({ ok: false, reason: 'in_flight' });
      }
      const cfg = _attachedCfg || DEFAULT_CFG;
      if (typeof cfg.parserUrl !== 'string' || cfg.parserUrl.length === 0) {
        return Promise.resolve({ ok: false, reason: 'no_parser_url' });
      }
      _runInlineParserFanOut(_state.lastFile, cfg);
      return Promise.resolve({ ok: true, scheduled: true });
    },

    /**
     * Phase 9.1 — Network snapshot for the submit handler. Read-
     * only; the listeners below mutate `networkStatus` on browser
     * offline/online events. The submit handler consults this BEFORE
     * awaiting `whenReady` so the recruiter can be told "Network
     * lost — your upload is paused. Click again when Wi-Fi comes
     * back" without waiting for an XHR timeout.
     */
    network: function () {
      return {
        status: _state.networkStatus,
        lostAt: _state.networkLostAt,
        restoredAt: _state.networkRestoredAt,
      };
    },

    /**
     * Phase 9 — Wait for the parallel inline parser pipeline to
     * resolve. Fast path: parser already finished (≤1 s typical on
     * 2 MB PDF). Otherwise resolves once the parser stage hits
     * 'done' or 'failed'. Cap of 2.5 s by default — per the user's
     * spec, the submit handler holds the click for up to that long
     * and proceeds past it.
     *
     * NEVER rejects. Resolves:
     *   { ok: true,  values: { full_name, email, ... }, via: 'inline' }
     *   { ok: true,  values: {}, via: 'inline', reason: 'no_data' }  (parser returned ok but empty body — recruiter types manually)
     *   { ok: false, reason: 'timeout'|'failed'|'cancelled'|'replaced' }
     */
    whenParsedInline: function (opts) {
      // Phase 9 follow-up — submit-wait cap is read from runtime
      // config (`parserSubmitWaitMs` set by the Blade partial) so
      // ops can raise or lower the ceiling without a JS rebuild.
      // The const default below is the fallback when nothing is
      // wired.
      const cfgDefault = (_state && typeof _state.parserSubmitWaitMs === 'number')
        ? _state.parserSubmitWaitMs
        : 2500;
      const timeoutMs = (opts && typeof opts.timeoutMs === 'number') ? opts.timeoutMs : cfgDefault;
      if (_state.parserStage === 'done' && _state.parserResult) {
        return Promise.resolve({
          ok: true,
          values: _state.parserResult,
          via: 'inline',
        });
      }
      if (_state.parserStage === 'failed' || _state.parserStage === 'cancelled' || _state.parserStage === 'replaced') {
        return Promise.resolve({ ok: false, reason: _state.parserStage });
      }
      return new Promise(function (resolve) {
        const startedAt = Date.now();
        const tick = setInterval(function () {
          if (_state.parserStage === 'done' && _state.parserResult) {
            clearInterval(tick);
            resolve({ ok: true, values: _state.parserResult, via: 'inline' });
            return;
          }
          if (_state.parserStage === 'failed' || _state.parserStage === 'cancelled' || _state.parserStage === 'replaced') {
            clearInterval(tick);
            resolve({ ok: false, reason: _state.parserStage });
            return;
          }
          if (Date.now() - startedAt >= timeoutMs) {
            clearInterval(tick);
            // Per spec: parse is still running past the 2.5 s cap,
            // submit proceeds. We do NOT fail the submit; we just
            // signal that the parse will finish in the background.
            resolve({ ok: true, reason: 'still_running', values: null, via: 'inline' });
          }
        }, 80);
      });
    },

    /**
     * Phase 9 — Lightweight event subscription for the
     * populate-if-empty consumer. Returns an unsubscribe function.
     * `parse.completed` is fired when the parser stage hits 'done'
     * with `payload.values` populated from the inline pipeline.
     */
    on: function (eventName, handler) {
      if (typeof eventName !== 'string' || typeof handler !== 'function') {
        return function () {};
      }
      const wrapped = function (ev) {
        if (ev && ev.detail && ev.detail.name === eventName) {
          try { handler(ev.detail.payload || {}, ev.detail.state || _state); } catch (e) { /* ignore */ }
        }
      };
      window.addEventListener('resume-upload-progress', wrapped);
      return function () {
        try { window.removeEventListener('resume-upload-progress', wrapped); } catch (_) {}
      };
    },

    // Manual detach — used in tests or when the host view changes.
    detach: function () {
      _abortInflight('cancelled');
      _state = initialState();
      _waiters = [];
      _attachedInput = null;
      _attachedCfg = null;
    },

    /**
     * Phase 9.2 — Public accessor for the persisted session cache.
     * The submit handler can read this on boot to decide whether
     * the recruiter is returning to an already-uploaded form. The
     * server-side show() endpoint is the source of truth — this
     * is just a fast first-pass that avoids an unnecessary round-
     * trip.
     *
     * { session_uuid, expires_at, page } | null
     */
    cachedSession: function () {
      return _readSessionFromCache();
    },

    /**
     * Phase 9.2 — clear the localStorage cache. Bound to the
     * Cancel button so a recruiter who aborts does not leak a
     * session UUID into a future visit.
     */
    clearCachedSession: function () {
      _clearSessionCache();
    },
  };

  // ---- Public attach ------------------------------------------------------

  window.attachResumeUploadProgress = function (inputId, opts) {
    const cfg = Object.assign({}, DEFAULT_CFG, opts || {});
    const tryAttach = function (id) {
      const el = document.getElementById(id);
      if (!el) return null;
      if (el._resumeUploadAttached) return el;
      el._resumeUploadAttached = true;
      el.addEventListener('change', function (ev) { handleFile(ev, el, cfg); });
      _attachedInput = el;
      _attachedCfg = cfg;
      // Phase 9 follow-up — the submit-wait ceiling moved from a
      // JS constant to a server-driven config knob. We snapshot the
      // value into _state here so the `whenParsedInline` closure can
      // read it via a sync lookup without re-parsing cfg every tick.
      _state.parserSubmitWaitMs = (typeof cfg.parserSubmitWaitMs === 'number')
        ? cfg.parserSubmitWaitMs
        : 2500;
      return el;
    };
    // Try the primary id, then the legacy id.
    let attached = tryAttach(inputId);
    if (!attached && cfg.fallbackInputId) {
      attached = tryAttach(cfg.fallbackInputId);
    }
    if (!attached) {
      console.warn('[resume-upload-progress] no file input found for ids:', inputId, cfg.fallbackInputId);
    }
    // Phase 9.2 — wire the optional Cancel button. Backward compat:
    // if the host blade does NOT emit #resumeCancel, this is a no-op.
    _maybeAttachCancelButton();
    // Phase 9.2 — restore from session cache if present. The host
    // form may emit a `session.restored` listener to flip the badge
    // back on after a refresh.
    try {
      const cached = _readSessionFromCache();
      if (cached && cached.session_uuid) {
        _emitEvent('session.restored', cached);
      }
    } catch (_) {}
  };

  // ---- File pick handler --------------------------------------------------

  function handleFile(ev, input, cfg) {
    _abortInflight('replaced');
    _abortParserInflight('replaced', cfg);    // Phase 9 — kill any in-flight parse before starting a new one.
    const file = ev.target.files && ev.target.files[0];
    if (!file) {
      _resetState();
      _resetParserState();
      _notifyWaiters();
      return;
    }
    const validation = validateFile(file, cfg);
    if (validation.error) {
      _resetState();
      _state.stage = 'failed';
      _state.error = validation.error;
      _showError(validation.error);
      _notifyWaiters();
      return;
    }

    _state = initialState();
    _state.totalBytes = file.size;
    _state.startedAt = Date.now();
    // Phase 9.1 — retention for retry. The cached File + idempotency
    // key are the inputs that retryUpload() re-emits so the server's
    // createOrReplay path can rejoin the existing resume_uploads row.
    // The key is generated ONCE per pick (not per fan-out) so a
    // retry and a fresh upload are indistinguishable to the server.
    _state.lastFile = file;
    _state.lastIdempotencyKey = cryptoRandomIdempotencyKey();
    _hideError();
    _setProgressBar(0);
    _showProgressBar();
    _setStatusBadge(false);
    _resetParserState();
    _state.totalBytes = file.size;             // parser state mirrors the file size
    _state.parserStartedAt = Date.now();

    if (cfg.progressStage) cfg.progressStage('uploading');
    if (cfg.usePresigned) {
      _runPresignedFlow(file, cfg);
    } else {
      _runLegacyMultipartFlow(file, cfg);
    }

    // Phase 9 — Launch the parallel parser fan-out. Independent
    // HTTP request, AbortController for cancellation-on-replace,
    // failure-tolerant so an upload error never blocks the recruit
    // UX's instant-field-fill feedback.
    if (typeof cfg.parserUrl === 'string' && cfg.parserUrl.length > 0) {
      _runInlineParserFanOut(file, cfg);
    }
  }

  function validateFile(file, cfg) {
    const ext = (file.name.split('.').pop() || '').toLowerCase();
    if (cfg.allowedExtensions.indexOf(ext) === -1) {
      return { error: 'Only ' + cfg.allowedExtensions.join(', ').toUpperCase() + ' files are allowed.' };
    }
    if (file.size > cfg.maxBytes) {
      return { error: 'Maximum file size is ' + Math.round(cfg.maxBytes / (1024 * 1024)) + ' MB.' };
    }
    return { ok: true };
  }

  // ---- Presigned PUT flow -------------------------------------------------

  function _runPresignedFlow(file, cfg) {
    _state.stage = 'presigning';
    let presignResp;

    try {
      presignResp = _xhrOpen('POST', cfg.presignUrl, true);
      presignResp.setRequestHeader('Content-Type', 'application/json');
      presignResp.setRequestHeader('Accept', 'application/json');
      presignResp.setRequestHeader('X-CSRF-TOKEN', cfg.csrfToken);
      // Phase 9.1 — use the CACHED idempotency key. handleFile() (or
      // retryUpload()) populated _state.lastIdempotencyKey once per
      // pick; re-using it here is what makes the server's replay
      // path actually rejoin the same resume_uploads row. If we
      // minted a fresh key on each fan-out, retry would create a
      // duplicate session — exactly the failure the spec forbids.
      const idemKey = _state.lastIdempotencyKey || cryptoRandomIdempotencyKey();
      if (_state.lastIdempotencyKey === null) { _state.lastIdempotencyKey = idemKey; }
      presignResp.setRequestHeader('Idempotency-Key', idemKey);
      _inflightXhr = presignResp;
      presignResp.onload = function () { onPresignLoaded(file, presignResp, cfg); };
      presignResp.onerror = function () { onPresignFailed(file, cfg, 'Network error contacting server.'); };
      presignResp.send(JSON.stringify({
        original_filename: file.name,
        mime_type: file.type || undefined,
        size_bytes: file.size,
        job_id: cfg.jobId || null,
        resource_type: cfg.resourceType,
      }));
    } catch (_) {
      onPresignFailed(file, cfg, 'Unable to initialize presign request.');
    }
  }

  function onPresignLoaded(file, resp, cfg) {
    if (resp.status === 422) {
      let body = _safeJson(resp);
      if (body && body.code === 'presign_unavailable') {
        // Ladder step 2: server says it can't presign → legacy multipart.
        _runLegacyMultipartFlow(file, cfg);
        return;
      }
      _failWith('Server rejected the upload: ' + (body && body.message ? body.message : 'HTTP 422'));
      return;
    }
    if (!resp.ok) {
      // Ladder: any non-422 failure → preserve the form's existing behavior,
      // which is multipart. This keeps ALL existing test contracts intact.
      _runLegacyMultipartFlow(file, cfg);
      return;
    }
    let json = _safeJson(resp);
    if (!json || !json.upload_url || !json.session_uuid) {
      _failWith('Server returned an unreadable presign response.');
      return;
    }
    _state.sessionUuid = json.session_uuid;
    _state.r2Url = json.r2_url || null;
    // Phase 9.2 — snapshot the session UUID immediately on presign
    // success so a refresh during the PUT itself doesn't lose the
    // session. The server's expires_at is unknown here (presign
    // returns it) — we read it from JSON if present.
    try {
      const ttlMs = (json.expires_at ? (Date.parse(json.expires_at) - Date.now()) : 3600000);
      _persistSessionToCache(json.session_uuid, Math.max(60000, ttlMs));
    } catch (_) {}
    _putToR2(file, json, cfg);
  }

  function onPresignFailed(file, cfg, message) {
    // Ladder step 3: presign network error → legacy multipart.
    _runLegacyMultipartFlow(file, cfg);
    // Surface best-effort; the multipart will overwrite.
    _showError(message + ' — switching to direct upload.');
  }

  function _putToR2(file, presignJson, cfg) {
    _state.stage = 'uploading';
    _state.startedAt = Date.now();
    let xhr;
    try {
      xhr = _xhrOpen('PUT', presignJson.upload_url, true);
      xhr.withCredentials = false;
      if (file.type) xhr.setRequestHeader('Content-Type', file.type);
      const extra = presignJson.headers || {};
      Object.keys(extra).forEach(function (h) {
        if (h && h.toLowerCase() !== 'content-type') xhr.setRequestHeader(h, extra[h]);
      });
      xhr.upload.onprogress = function (e) { onUploadProgress(e); };
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
          _onPutSucceeded(file, presignJson, cfg);
        } else {
          _failWith('R2 PUT failed: HTTP ' + xhr.status);
        }
      };
      xhr.onerror = function () {
        // CORS or network — ladder: legacy multipart.
        _runLegacyMultipartFlow(file, cfg);
      };
      _inflightXhr = xhr;
      xhr.send(file);
    } catch (e) {
      _failWith('Unable to start direct upload: ' + (e && e.message ? e.message : 'unknown'));
    }
  }

  function _onPutSucceeded(file, presignJson, cfg) {
    _state.stage = 'finalizing';
    _state.percent = 100;
    _setProgressBar(100);

    let xhr;
    try {
      xhr = _xhrOpen('POST', cfg.finalizeUrl(presignJson.session_uuid), true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.setRequestHeader('X-CSRF-TOKEN', cfg.csrfToken);
      _inflightXhr = xhr;
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
          _state.stage = 'completed';
          _writeSessionUuidToForm(presignJson.session_uuid);
          // Phase 9.2 — refresh the cache TTL on finalize so
          // expires_at is more accurate than the presign-only TTL.
          try {
            const j = _safeJson(xhr) || {};
            const ttlMs = (j.expires_at ? (Date.parse(j.expires_at) - Date.now()) : 3600000);
            _persistSessionToCache(presignJson.session_uuid, Math.max(60000, ttlMs));
          } catch (_) {}
          // Phase 9.2 — auto-retry counter resets on success; a
          // successful finalize means we're past the unstable
          // window.
          _state.autoRetryAttempts = 0;
          _setStatusBadge(true);
          _emitEvent('upload.completed', {
            session_uuid: presignJson.session_uuid,
            bytes: file.size,
          });
          _notifyWaiters();
        } else {
          // Don't fall back here: the bytes ARE on R2. Submit handler
          // will see upload_session_uuid; if controller's headCheck
          // fails we have a recoverable fault NOT a UX regression.
          _failWith('Finalize failed: HTTP ' + xhr.status);
        }
      };
      xhr.onerror = function () {
        _failWith('Network error during finalize.');
      };
      xhr.send(JSON.stringify({ client_bytes: file.size }));
    } catch (e) {
      _failWith('Unable to initialize finalize: ' + (e && e.message ? e.message : 'unknown'));
    }
  }

  // ---- Legacy multipart fallback ------------------------------------------

  function _runLegacyMultipartFlow(file, cfg) {
    _state.stage = 'uploading';
    _state.startedAt = Date.now();

    let xhr;
    try {
      xhr = _xhrOpen('POST', cfg.legacyUrl, true);
      _inflightXhr = xhr;
      const fd = new FormData();
      fd.append('resume_file', file);
      if (cfg.jobId) fd.append('job_id', cfg.jobId);
      xhr.upload.onprogress = function (e) { onUploadProgress(e); };
      xhr.onload = function () {
        try {
          const data = _safeJson(xhr) || {};
          if (xhr.status >= 200 && xhr.status < 300) {
            _state.stage = 'completed';
            if (data.session_uuid) {
              _state.sessionUuid = data.session_uuid;
              _writeSessionUuidToForm(data.session_uuid);
            }
            _setStatusBadge(true);
            _emitEvent('upload.legacy_completed', {
              status: xhr.status,
              bytes: file.size,
            });
            _notifyWaiters();
          } else {
            _failWith(data.message || ('Legacy upload failed: HTTP ' + xhr.status));
          }
        } catch (_) {
          _failWith('Legacy upload returned an unreadable response.');
        }
      };
      xhr.onerror = function () { _failWith('Network error during upload.'); };
      xhr.send(fd);
    } catch (e) {
      _failWith('Unable to initialize upload: ' + (e && e.message ? e.message : 'unknown'));
    }
  }

  // ---- Common helpers -----------------------------------------------------

  function _xhrOpen(method, url, async) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, async !== false);
    return xhr;
  }

  function _safeJson(resp) {
    try { return JSON.parse(resp.responseText); } catch (_) { return null; }
  }

  function onUploadProgress(event) {
    if (!event.lengthComputable) return;
    const now = Date.now();
    _state.uploadedBytes = event.loaded;
    _state.totalBytes = event.total;
    _state.percent = Math.min(100, Math.max(0, (event.loaded / event.total) * 100));
    _state.lastProgressAt = now;

    // Rolling speed: average of byte-deltas over the last 6 progress
    // events (or last 1.5s, whichever is shorter). Stable under
    // throttled connections and burst-y networks.
    const window = _state.lastLoadedDeltaWindow;
    window.push({ ts: now, loaded: event.loaded });
    while (window.length > 6) window.shift();
    if (window.length >= 2) {
      const first = window[0];
      const last = window[window.length - 1];
      const dt = (last.ts - first.ts) / 1000;
      const db = last.loaded - first.loaded;
      const speed = dt > 0 ? db / dt : 0;
      _state.speedBps = speed;
      if (speed > 0) {
        _state.etaMs = ((event.total - event.loaded) / speed) * 1000;
      }
    }
    _setProgressBar(_state.percent);
    _setProgressLabel();
  }

  function _setProgressBar(percent) {
    try {
      const bar = document.getElementById(_attachedCfg.progressBarInnerId);
      if (bar) {
        bar.style.width = percent.toFixed(0) + '%';
        bar.setAttribute('aria-valuenow', percent.toFixed(0));
      }
      const container = document.getElementById(_attachedCfg.progressBarContainerId);
      if (container) {
        container.style.display = percent >= 100 ? 'none' : 'block';
      }
    } catch (_) { /* ignore */ }
  }

  function _showProgressBar() {
    try {
      const container = document.getElementById(_attachedCfg.progressBarContainerId);
      if (container) container.style.display = 'block';
      // Phase 9.2 — flip the optional Cancel button to visible
      // whenever an upload is in flight. Backward compat: if the
      // host blade does NOT emit #resumeCancel, no-op.
      const cancelBtn = document.getElementById('resumeCancel');
      if (cancelBtn) cancelBtn.style.display = '';
    } catch (_) {}
  }

  /**
   * Phase 9.2 — hide the Cancel button after an upload resolves
   * (success OR terminal failure). Called from _failWith, _onPutSucceeded,
   * and the cancel() path. Equivalent to a "completed" visual.
   */
  function _hideCancelButton() {
    try {
      const cancelBtn = document.getElementById('resumeCancel');
      if (cancelBtn) cancelBtn.style.display = 'none';
    } catch (_) {}
  }

  function _setProgressLabel() {
    try {
      const container = document.getElementById(_attachedCfg.progressBarContainerId);
      if (!container) return;
      // The container carries no label subnode by default — host blade may
      // sprinkle a #resumeProgressLabel. We only patch if present, so an
      // unchanged form still works.
      let label = document.getElementById('resumeProgressLabel');
      if (!label) {
        label = document.createElement('div');
        label.id = 'resumeProgressLabel';
        label.className = 'small text-muted text-end mt-1';
        container.parentNode && container.parentNode.appendChild(label);
      }
      const pct = _state.percent.toFixed(0) + '%';
      const speed = _formatRate(_state.speedBps);
      const eta = _formatEta(_state.etaMs);
      label.textContent = pct + ' · ' + speed + (eta !== '' ? ' · ' + eta + ' left' : '');
    } catch (_) {}
  }

  function _formatRate(bps) {
    if (!bps || bps <= 0) return '— MB/s';
    if (bps >= 1024 * 1024) return (bps / (1024 * 1024)).toFixed(2) + ' MB/s';
    return (bps / 1024).toFixed(0) + ' KB/s';
  }

  function _formatEta(ms) {
    if (!ms || !isFinite(ms) || ms < 0) return '';
    const totalSeconds = Math.floor(ms / 1000);
    if (totalSeconds < 1) return '<1s';
    if (totalSeconds < 60) return totalSeconds + 's';
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    return m + 'm' + (s > 0 ? ' ' + s + 's' : '');
  }

  function _writeSessionUuidToForm(uuid) {
    try {
      const field = document.getElementById('upload_session_uuid');
      if (field) field.value = uuid || '';
      // Mirror to the alt field any legacy form might still hold:
      const alt = document.getElementById('resumeSessionUuid');
      if (alt) alt.value = uuid || '';
    } catch (_) {}
    _state.sessionUuid = uuid || null;
  }

  function _setStatusBadge(visible) {
    try {
      const b = document.getElementById(_attachedCfg.statusBadgeId);
      if (!b) return;
      b.style.display = visible ? '' : 'none';
    } catch (_) {}
  }

  function _showError(message) {
    try {
      const e = document.getElementById(_attachedCfg.errorElementId);
      if (!e) { console.error('[resume-upload-progress]', message); return; }
      e.textContent = message;
      e.style.display = 'block';
    } catch (_) { console.error('[resume-upload-progress]', message); }
  }

  function _hideError() {
    try {
      const e = document.getElementById(_attachedCfg.errorElementId);
      if (!e) return;
      e.textContent = '';
      e.style.display = 'none';
    } catch (_) {}
  }

  function _failWith(message) {
    _state.stage = 'failed';
    _state.error = message;
    _showError(message);
    if (_attachedCfg && _attachedCfg.progressStage) _attachedCfg.progressStage('failed');
    _emitEvent('upload.failed', { message: message });
    _notifyWaiters();
    // Phase 9.2 — hide the Cancel button after terminal failure;
    // recruiter either fixes and retries or picks a new file.
    try { _hideCancelButton(); } catch (_) {}
  }

  function _abortInflight(reason) {
    if (_inflightXhr) {
      try { _inflightXhr.abort(); } catch (_) {}
      _inflightXhr = null;
    }
    if (_state.stage === 'completed' || _state.stage === 'idle') return;
    _state.stage = reason;
    _emitEvent('upload.' + reason, {});
    _notifyWaiters();
  }

  function _resetState() {
    _state = initialState();
    _setStatusBadge(false);
    _setProgressBar(0);
    _hideError();
  }

  // ------------------- Phase 9 — Parser pipeline helpers -------------------

  function _resetParserState() {
    _state.parserResult = null;
    _state.parserStage = 'idle';
    if (_attachedCfg && _attachedCfg.parserProgressStage) {
      _attachedCfg.parserProgressStage('idle');
    }
  }

  function _abortParserInflight(reason, cfg) {
    if (_state.parserAbortController) {
      try { _state.parserAbortController.abort(); } catch (_) {}
      _state.parserAbortController = null;
    }
    if (_state.parserInflightXhr) {
      try { _state.parserInflightXhr.abort(); } catch (_) {}
      _state.parserInflightXhr = null;
    }
    if (_state.parserStage !== 'idle' && _state.parserStage !== 'completed') {
      _state.parserStage = reason;     // 'cancelled' | 'replaced' etc.
      if (cfg && cfg.parserProgressStage) cfg.parserProgressStage(reason === 'replaced' ? 'idle' : 'failed');
      _emitEvent('parse.' + reason, {});
    }
  }

  /**
   * Phase 9 — Run the parallel instant-field-extraction fan-out.
   *
   * Independent of _runPresignedFlow / _runLegacyMultipartFlow; the
   * upload progresses via its own XHR while this one POSTs the
   * file blob directly to the read-only parser endpoint. The
   * AbortController lets handleFile()'s next invocation kill the
   * previous parse with a single .abort() call.
   *
   * Epoch guard: every closure (onload/onerror/onabort + the two
   * setTimeout-driven stage tickers) captures the value of
   * _state.parserFanOutEpoch WHEN THIS FAN-OUT STARTED. If a
   * later fan-out has incremented the counter, this fan-out's
   * terminal callbacks are stale and bail out WITHOUT touching
   * _state. That isolates the cross-pick race where the browser
   * delivers the OLD XHR's terminal callback AFTER the NEW
   * fan-out has already set parserStage='reading'.
   *
   * Failure policy: NEVER throws. NEVER blocks the upload. ANY error
   * surfaces as `parserStage='failed'` and a soft 'Unable to parse
   * resume. You can continue manually.' message via setResumeProgress
   * on the parser side; submission is unaffected.
   */
  function _runInlineParserFanOut(file, cfg) {
    const ac = ('AbortController' in window) ? new AbortController() : null;
    _state.parserAbortController = ac;
    _state.parserFanOutEpoch = (_state.parserFanOutEpoch || 0) + 1;
    const epoch = _state.parserFanOutEpoch;
    const stillOwns = function () { return _state.parserFanOutEpoch === epoch; };
    _state.parserStage = 'reading';
    if (cfg.parserProgressStage) cfg.parserProgressStage('reading');
    _emitEvent('parse.started', { size: file.size });

    const fd = new FormData();
    fd.append('file', file);

    const xhr = new XMLHttpRequest();
    _state.parserInflightXhr = xhr;
    xhr.open('POST', cfg.parserUrl, true);
    if (cfg.csrfToken) xhr.setRequestHeader('X-CSRF-TOKEN', cfg.csrfToken);
    xhr.setRequestHeader('Accept', 'application/json');
    if (cfg.jobId) xhr.setRequestHeader('X-Job-Id', String(cfg.jobId));
    xhr.setRequestHeader('X-Request-Id', (typeof crypto !== 'undefined' && crypto.randomUUID)
      ? crypto.randomUUID()
      : 'parser-' + Math.random().toString(36).slice(2));

    xhr.onload = function () {
      // Stale-check up-front — if a newer fan-out has already
      // taken over _state, do NOT touch it. The browser can fire
      // onload on a cancelled XHR if the response had already
      // arrived before abort() landed.
      if (! stillOwns()) { return; }
      try {
        const elapsed = Date.now() - _state.parserStartedAt;
        if (xhr.status >= 200 && xhr.status < 300) {
          let body = null;
          try { body = JSON.parse(xhr.responseText); } catch (_) { body = null; }
          if (body && body.parse_status === 'ok' && _state.parserAbortController && ! _state.parserAbortController.signal.aborted) {
            _state.parserResult = body;
            _state.parserStage = 'done';
            // BUG FIX: do NOT call _emitStage('extracting'|'skills')
            // here — they would unconditionally overwrite the
            // terminal 'done' state and break the late-loaded
            // populate-if-empty fallback that reads
            // `state.parserStage === 'done' && state.parserResult`.
            if (cfg.parserProgressStage) cfg.parserProgressStage('done');
            // Project only the column-mapped keys so the
            // populate-if-empty consumer never sees internal
            // payload noise (parse_status, duration_ms, etc.).
            const projected = {};
            const cols = ['full_name','email','phone','skills','experience','education','location','current_company','current_designation'];
            for (let i = 0; i < cols.length; i++) {
              const k = cols[i];
              if (body[k] !== null && body[k] !== '' && body[k] !== undefined) {
                projected[k] = body[k];
              }
            }
            _emitEvent('parse.completed', {
              size_bytes: file.size,
              parse_duration_ms: body.duration_ms || elapsed,
              values: projected,
            });
            _emitEvent('parseInline', { values: projected });
          } else {
            _state.parserStage = 'failed';
            const status = body && body.parse_status ? body.parse_status : 'failed';
            if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
            _emitEvent('parse.failed', {
              status: xhr.status,
              parse_status: status,
              size_bytes: file.size,
              parse_duration_ms: elapsed,
            });
          }
        } else if (xhr.status === 429) {
          // Phase 9.2 — honour Retry-After on parser 429s. Recruiter
          // pace-induced rate-limit (ResumeParserController throttle:
          // 10/min) and a multi-tenant overzealous user both land
          // here. The first 429 SETS the cooling window ONCE per
          // pick (parserRateLimitedRetryCount<2). Subsequent 429s
          // are no-ops — letting the recruiter hammer the button
          // would produce a cascade of 429s.
          if (_state.parserRateLimitedRetryCount < 2) {
            const secs = _parseRetryAfter429(xhr);
            _state.parserRateLimitedUntil = Date.now() + (secs * 1000);
            _state.parserRateLimitedRetryCount = (_state.parserRateLimitedRetryCount || 0) + 1;
            _state.parserStage = 'rate_limited';
            try {
              _showError('Parser briefly busy — retrying in ' + secs + 's.');
            } catch (_) {}
            if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
            _emitEvent('parse.rate_limited', {
              retry_after_seconds: secs,
              retry_after_iso: new Date(_state.parserRateLimitedUntil).toISOString(),
              size_bytes: file.size,
              attempt: _state.parserRateLimitedRetryCount,
            });
            // Fire-and-forget one auto-retry after the cooling.
            // This is the parser only — NEVER the candidate POST.
            setTimeout(function () {
              if (!stillOwns()) { return; }
              if (_respect429Cooling()) { return; }   // someone extended it
              _runInlineParserFanOut(file, cfg);
            }, (secs * 1000) + 500);
          } else {
            _state.parserStage = 'failed';
            if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
            _emitEvent('parse.failed', {
              status: 429,
              reason: 'rate_limited_exhausted',
              size_bytes: file.size,
            });
          }
        } else {
          _state.parserStage = 'failed';
          if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
          _emitEvent('parse.failed', { status: xhr.status, size_bytes: file.size });
        }
      } catch (_) { /* ignore — defensive */ }
      _state.parserInflightXhr = null;
    };
    xhr.onerror = function () {
      if (! stillOwns()) { return; }
      _state.parserStage = 'failed';
      if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
      _emitEvent('parse.failed', { reason: 'network', size_bytes: file.size });
      _state.parserInflightXhr = null;
    };
    xhr.onabort = function () {
      if (! stillOwns()) { return; }
      _state.parserStage = 'cancelled';
      _emitEvent('parse.cancelled', { size_bytes: file.size });
    };

    try {
      xhr.send(fd);
      // Drive the visible stage transitions while the request is in
      // flight so the UI shows "Reading…" / "Extracting…" regardless
      // of network round-trip time. Each ticker captures `epoch` so
      // it cannot stomp on a newer fan-out.
      setTimeout(function () {
        if (! stillOwns()) { return; }
        if (_state.parserStage === 'reading') {
          _state.parserStage = 'extracting';
          if (cfg.parserProgressStage) cfg.parserProgressStage('extracting');
        }
      }, 120);
      setTimeout(function () {
        if (! stillOwns()) { return; }
        if (_state.parserStage === 'extracting') {
          _state.parserStage = 'skills';
          if (cfg.parserProgressStage) cfg.parserProgressStage('skills');
        }
      }, 360);
    } catch (e) {
      _state.parserStage = 'failed';
      if (cfg.parserProgressStage) cfg.parserProgressStage('failed');
      _emitEvent('parse.failed', { reason: 'send_failed', message: (e && e.message) || 'unknown' });
    }
  }

  function _emitStage(stage, cfg) {
    if (stage === _state.parserStage) return;
    _state.parserStage = stage;
    if (cfg.parserProgressStage) cfg.parserProgressStage(stage);
  }

  function _notifyWaiters() {
    _waiters.forEach(function (w, i) {
      // _waiters is mutated by tickers; capture current and let them self-clean.
    });
    // Tickers read _state directly so we don't need to enumerate here.
  }

  function _emitEvent(name, payload) {
    // Non-blocking fire-and-forget; host may attach a listener via
    // window.addEventListener('resume-upload-progress', {detail:{stage:...}}).
    try {
      window.dispatchEvent(new CustomEvent('resume-upload-progress', {
        detail: { name: name, payload: payload, state: _state },
      }));
      if (typeof window.console !== 'undefined' && window.console.debug) {
        window.console.debug('[resume-upload-progress]', name, payload);
      }
    } catch (_) { /* ignore */ }
  }

  function cryptoRandomIdempotencyKey() {
    if (window.crypto && window.crypto.randomUUID) {
      return 'upg-' + window.crypto.randomUUID();
    }
    return 'upg-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
  }

  // ---- Phase 9.2 — localStorage session cache + cross-tab sync -----------

  /**
   * When the recruiter refreshes the page mid-upload, OR opens a
   * second tab with the same picker open, the in-memory `_state` is
   * wiped. The R2 bytes are NOT — they are server-side. The resume
   * row + session_uuid on the server is the contract that lets us
   * rehydrate the UI to "Resume already uploaded ✓".
   *
   * Strategy: write {session_uuid, original_filename, expires_at}
   * to `jero.resume.session.<company_id>.<user_id>` on every
   * successful presign OR finalize. On attach(), read the cache and
   * emit a `session.restored` event so the host can flip the badge
   * and pre-fill forms. The server-side show() endpoint already
   * returns 404 if the row is expired, so a stale value here
   * gracefully fails closed (no ghost data).
   */
  function _sessionCacheKey(cfg) {
    try {
      const cid = (typeof window.COMPANY_ID !== 'undefined' && window.COMPANY_ID) || 0;
      const uid = (_attachedCfg && _attachedCfg.csrfToken) ? 'r' : 'g';   // csrfToken is not user-id; we use a generic bucket
      return 'jero.resume.session.' + cid + '.' + uid + '.' + location.pathname.replace(/[^a-z0-9]/gi, '_');
    } catch (_) { return 'jero.resume.session.global'; }
  }

  function _persistSessionToCache(uuid, ttlMs) {
    try {
      if (!uuid) return;
      if (!window.localStorage) return;
      const key = _sessionCacheKey(_attachedCfg || DEFAULT_CFG);
      window.localStorage.setItem(key, JSON.stringify({
        session_uuid: uuid,
        stored_at: Date.now(),
        expires_at: Date.now() + (ttlMs || 3600000),  // 1 h ceiling
        page: location.pathname,
      }));
    } catch (_) { /* localStorage may be off (private mode); ignore */ }
  }

  function _readSessionFromCache() {
    try {
      if (!window.localStorage) return null;
      const key = _sessionCacheKey(_attachedCfg || DEFAULT_CFG);
      const raw = window.localStorage.getItem(key);
      if (!raw) return null;
      const parsed = JSON.parse(raw);
      if (!parsed || !parsed.session_uuid) return null;
      if (typeof parsed.expires_at === 'number' && parsed.expires_at < Date.now()) {
        // Stale — drop. Server-side show() also returns 404 so we
        // never even call it.
        window.localStorage.removeItem(key);
        return null;
      }
      return parsed;
    } catch (_) { return null; }
  }

  function _clearSessionCache() {
    try {
      if (!window.localStorage) return;
      const key = _sessionCacheKey(_attachedCfg || DEFAULT_CFG);
      window.localStorage.removeItem(key);
    } catch (_) {}
  }

  // ---- Phase 9.2 — Cancel button helper -------------------------------

  /**
   * The Blade partial can spawn a `<button id="resumeCancel">`
   * next to the progress bar. We attach a click handler IF the
   * element exists. Cancel → state='cancelled', XHR abort, error
   * chip flips to a calm tone. No-op if the button does not exist
   * — backward compat with older forms.
   */
  function _maybeAttachCancelButton() {
    try {
      const btn = document.getElementById('resumeCancel');
      if (!btn) return;
      // Avoid double-binding across multiple attach() calls.
      if (btn.__jeroAttached) return;
      btn.__jeroAttached = true;
      btn.addEventListener('click', function (ev) {
        ev.preventDefault();
        try {
          if (typeof window.ResumeUpload !== 'object'
              || typeof window.ResumeUpload.cancel !== 'function') { return; }
          window.ResumeUpload.cancel();
          try { _showError('Upload cancelled. Pick a new file or close the tab.'); } catch (_) {}
        } catch (_) {}
      });
    } catch (_) {}
  }

  // ---- Phase 9.2 — Parser 429 honouring --------------------------------

  /**
   * Parse `Retry-After` from a 429 response. Standards-compliant
   * values are either seconds (integer) or an HTTP-date. We accept
   * both for resilience: the spec allows both and vendors vary. If
   * the header is missing or unparseable, default to 8 s.
   */
  function _parseRetryAfter429(xhr) {
    try {
      const raw = xhr.getResponseHeader && xhr.getResponseHeader('Retry-After');
      if (!raw) return 8;
      if (/^\d+$/.test(raw.trim())) {
        const v = parseInt(raw.trim(), 10);
        return Math.max(1, Math.min(120, v));
      }
      // HTTP-date — Date.parse handles RFC1123/ISO.
      const t = Date.parse(raw);
      if (!isNaN(t)) {
        const delta = Math.round((t - Date.now()) / 1000);
        return Math.max(1, Math.min(120, delta));
      }
    } catch (_) {}
    return 8;
  }

  function _respect429Cooling() {
    const now = Date.now();
    return _state.parserRateLimitedUntil > now;
  }

  // Phase 8 — helper for whenParsed() to read CSRF token lazily
  // rather than capturing at upload time. The meta tag can be moved
  // between page navigations, and ResumeUpload may be re-instantiated
  // after a CSRF rotation.
  function _csrfToken() {
    try {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta && meta.content) return meta.content;
    } catch (_) {}
    return '';
  }
// ---- Phase 9.1 — Offline / Online listeners (module-scoped) ----------

  (function _installNetworkListeners () {
    // We attach the listeners EXACTLY ONCE per page-load even though
    // attach() may run more than once (e.g. re-loading the Blade
    // partial). The flag lives on window because the IIFE itself
    // re-runs only on full page reload.
    if (window.__JERO_RESUME_NETWORK_LISTENERS_INSTALLED) { return; }
    window.__JERO_RESUME_NETWORK_LISTENERS_INSTALLED = true;

    /**
     * `offline` fires when navigator.onLine flips to false. We jump
     * the upload pipeline's visible badge to "Network Lost" within
     * the same tick so the recruiter does not wait for an XHR
     * timeout to learn their connection dropped. We DO NOT auto-
     * retry the candidate POST — that endpoint is not idempotent
     * and we cannot prove the server did not write the candidate.
     */
    window.addEventListener('offline', function () {
      _state.networkStatus = 'offline';
      _state.networkLostAt = Date.now();
      _emitEvent('network.lost', {
        inflight_upload: !!_inflightXhr,
        inflight_parser: !!_state.parserInflightXhr,
      });
      // Surface the failure on the existing error chip so the
      // recruiter sees an immediate visual cue. We pick a calm tone
      // (warning, not error) because the pipeline will retry on
      // its own when `online` lands if the recruiter confirms.
      try {
        _showError('Network lost. Upload paused — it will resume when your connection returns.');
        // flip the existing progress stage to a clear paused state
        if (_attachedCfg && _attachedCfg.progressStage) {
          try { _attachedCfg.progressStage('paused'); } catch (_) {}
        }
      } catch (_) { /* ignore — defensive */ }
      // If upload is in-flight, abort it so the XHR's onerror does
      // not race with our offline status. onload will then NOT fire
      // even if a tiny response was buffered — the state moves to a
      // single, deterministic 'network_lost' with no ambiguity.
      if (_inflightXhr) {
        try { _inflightXhr.abort(); } catch (_) {}
      }
      if (_state.parserInflightXhr) {
        try { _state.parserInflightXhr.abort(); } catch (_) {}
        _state.parserStage = 'cancelled';
      }
      if (_state.stage !== 'idle' && _state.stage !== 'completed') {
        _state.stage = 'network_lost';
        _state.error = 'Network lost during upload.';
        _emitEvent('upload.network_lost', {});
        _notifyWaiters();
      }
    });

    /**
     * `online` flips navigator.onLine back to true. We mark
     * networkStatus='online' AND emit `network.restored` so the
     * submit handler can enable its Retry affordance.
     *
     * Phase 9.2 — auto-retry with exponential backoff. If the most
     * recent interruption was an upload (not a parser) AND the
     * recruiter has NOT taken manual control via `Retry.upload()`
     * AND we have < 3 prior attempts, we re-emit the upload
     * pipeline against the cached File + Idempotency-Key so the
     * server rejoins the same session.
     *
     * Backoff: 1 s, 3 s, 9 s. If the recruiter clicked Retry first
     * OR if we already burned all 3 attempts OR if there is no
     * cached File, we degrade gracefully: emit `network.restored`
     * and let the recruiter click Retry themselves. The parser
     * pipeline is NOT auto-retried here — re-reads of the same
     * file after a dropped parse would race with the pending
     * submit. The recruiter explicitly clicks Retry for that.
     */
    window.addEventListener('online', function () {
      const wasOffline = _state.networkStatus === 'offline';
      _state.networkStatus = 'online';
      _state.networkRestoredAt = Date.now();
      try { _hideError(); } catch (_) {}
      if (_attachedCfg && _attachedCfg.progressStage) {
        try { _attachedCfg.progressStage('idle'); } catch (_) {}
      }

      const isUploadInterrupted = _state.stage === 'network_lost'
        || _state.stage === 'failed'
        || _state.stage === 'uploading'
        || _state.stage === 'presigning'
        || _state.stage === 'finalizing';
      const hasCachedFile = !!_state.lastFile;
      const recruiterTookOver = _state.autoRetrySuppressed;
      const underCap = _state.autoRetryAttempts < 3;

      _emitEvent('network.restored', {
        wasOffline: wasOffline,
        willAutoRetry: isUploadInterrupted && hasCachedFile && !recruiterTookOver && underCap,
        attempt: _state.autoRetryAttempts + 1,
      });

      if (!isUploadInterrupted || !hasCachedFile || recruiterTookOver || !underCap) {
        return;
      }
      // Backoff — schedule for `delay` ms so a flapping gateway
      // that bounces 3 times in 200ms cannot pile-on retries.
      const delay = Math.pow(3, _state.autoRetryAttempts) * 1000;   // 1s, 3s, 9s
      _state.autoRetryAttempts += 1;
      try {
        _showError('Connection restored. Retrying upload (attempt ' + _state.autoRetryAttempts + ' of 3)…');
      } catch (_) {}
      setTimeout(function () {
        // Bail again at fire-time: a new pick, manual retry, or
        // another offline event during the wait window should
        // cancel our scheduled re-emission.
        if (_state.networkStatus !== 'online') { return; }
        if (!_state.lastFile) { return; }
        if (typeof window.ResumeUpload !== 'object'
            || typeof window.ResumeUpload.retryUpload !== 'function') { return; }
        try {
          window.ResumeUpload.retryUpload();
        } catch (_) { /* ignore — recruiter can still click Retry */ }
      }, delay);
    });

    // Phase 9.2 — cross-tab cache sync. When tab A writes to
    // localStorage, tab B (same recruiter, same machine) receives a
    // `storage` event with the new value. We emit `session.restored`
    // for the host to flip its "Resume Uploaded" badge and pre-fill
    // forms the recruiter already uploaded in another tab.
    //
    // Note: storage event fires in OTHER tabs only — the writing
    // tab will not see its own write. We do NOT write again to avoid
    // an echo loop.
    window.addEventListener('storage', function (ev) {
      try {
        if (!ev || !ev.key) { return; }
        const myKey = _sessionCacheKey(_attachedCfg || DEFAULT_CFG);
        if (ev.key !== myKey) { return; }
        if (ev.newValue === null) {
          _emitEvent('session.cleared', {});
          return;
        }
        const parsed = JSON.parse(ev.newValue);
        if (!parsed || !parsed.session_uuid) { return; }
        if (typeof parsed.expires_at === 'number' && parsed.expires_at < Date.now()) { return; }
        _emitEvent('session.restored', parsed);
      } catch (_) { /* ignore — malformed payload */ }
    });
  })();

})(window);
