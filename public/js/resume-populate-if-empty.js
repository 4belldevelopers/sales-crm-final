/* eslint-disable no-console */
/**
 * Phase 8 + Phase 9 — empty-field auto-fill on resume.parsed
 * broadcast AND inline parse events.
 *
 * Two complementary ingress paths run through the same fill code:
 *   - Phase 8 (Reverb): subscribe to `App.Models.Company.{cid}`
 *     private channel for `resume.parsed` (the slow AI/vendor
 *     enrichment path that runs on the `parse_resumes` queue).
 *   - Phase 9 (inline parse): subscribe to `ResumeUpload.on('parseInline', …)`
 *     fired by the parallel instant-field-extraction pipeline
 *     that runs while the upload is still in flight. This is the
 *     fast UX path — fields fill within ~1 s of file pick.
 *
 * Contract — the same no-overwrite semantics as the server-side
 * {@see \App\Services\ResumeCandidateSync::COLUMN_MAP}:
 *   - Empty field + non-empty parsed value → write.
 *   - Pre-existing value (any non-empty string or pre-filled number)
 *     → leave alone. Recruiter wins.
 *   - Selector is `[name="<col>"]`; multi-valued form fields are
 *     NOT supported (the recruiter UI uses 1:1 input/column).
 *
 * Failure isolation — a fill that throws on one field MUST NOT
 * skip the rest of the payload. Each fill is wrapped in a try/catch.
 *
 * Reverb bootstrap dependency — Echo must be loaded by the host
 * layout for the slow path. The inline path depends only on
 * `window.ResumeUpload` (which `resume-upload-progress.js`
 * publishes) so it works on every page.
 */
(function (window) {
  'use strict';

  /**
   * Single fill walker used by EVERY ingress path. Walks a
   * `values` map (column => value) and writes only into empty
   * fields. Emits one `input` event per filled field so the
   * recruiter's downstream handlers (CTC currency, percent, etc.)
   * recompute as if the recruiter had typed the value by hand.
   */
  function applyValuesToForm(values) {
    if (! values || typeof values !== 'object') { return; }
    const keys = Object.keys(values);
    if (keys.length === 0) { return; }

    keys.forEach(function (column) {
      try {
        const field = document.querySelector('[name="' + column + '"]');
        if (! field) {
          return;       // field not present on this form
        }
        const current = (field.value || '').trim();
        if (current !== '') { return; }       // recruiter wins
        const raw = values[column];
        if (raw === null || raw === undefined) { return; }
        const value = Array.isArray(raw)
          ? raw.join(', ')                    // skill arrays → comma-joined
          : String(raw).trim();
        if (value === '') { return; }
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
      } catch (e) {
        console.warn('[resume-populate-if-empty] failed to fill', column, e && e.message);
      }
    });
  }

  /**
   * Phase 8 — Reverb broadcast handler.
   * Bound to `App.Models.Company.{cid}` private channel.
   * The handler reads the broadcast payload (Phase-8 shape) and
   * routes through the same fill walker.
   */
  function applyReverbPayload(e) {
    if (! e || ! e.session_uuid) { return; }
    if (e.status && e.status !== 'Parsed') { return; }
    const fillable = e.fillable || {};
    applyValuesToForm(fillable);
  }

  function attachReverb() {
    if (! window.Echo || typeof window.Echo.private !== 'function') {
      return;
    }
    const cid = window.COMPANY_ID;
    if (! cid) { return; }
    window.Echo.private('App.Models.Company.' + cid)
      .listen('.resume.parsed', applyReverbPayload);
  }

  /**
   * Phase 9 — Inline parse handler. Subscribes to
   * `ResumeUpload.on('parseInline', …)` so this layer does NOT
   * depend on Reverb or the slow AI vendor pipeline. The super-fast
   * fill path lands within ~1 s of file pick.
   */
  function attachInline() {
    if (typeof window.ResumeUpload === 'undefined') {
      // resume-upload-progress.js hasn't loaded yet — try once
      // more on window load so DNS-level script ordering still works.
      window.addEventListener('load', attachInline, { once: true });
      return;
    }
    if (typeof window.ResumeUpload.on !== 'function') {
      return;
    }
    window.ResumeUpload.on('parseInline', function (payload) {
      applyValuesToForm(payload && payload.values);
    });

    // If the inline parser already finished (e.g. the JS loaded
    // after the file pick), consume the cached result immediately.
    try {
      const cur = window.ResumeUpload.state ? window.ResumeUpload.state() : null;
      if (cur && cur.parserStage === 'done' && cur.parserResult) {
        const projected = {};
        const cols = ['full_name','email','phone','skills','experience','education','location','current_company','current_designation'];
        for (let i = 0; i < cols.length; i++) {
          const k = cols[i];
          if (cur.parserResult[k] !== null && cur.parserResult[k] !== '' && cur.parserResult[k] !== undefined) {
            projected[k] = cur.parserResult[k];
          }
        }
        if (Object.keys(projected).length > 0) {
          applyValuesToForm(projected);
        }
      }
    } catch (_) { /* ignore — state() may not exist on older builds */ }
  }

  function attach() {
    attachReverb();
    attachInline();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attach);
  } else {
    attach();
  }
})(window);
