/**
 * service_company_dashboard.js — V2 Dashboard Phase 6 client module.
 *
 * Replaces inline <script> polling in index.blade.php with a structured module:
 *
 *   1. Fetches /api/v2/service-company/dashboard/* endpoints for live data.
 *   2. Connects to the Reverb private channel company.{companyId}.dashboard for
 *      real-time push events (CandidateStatusChanged, InterviewScheduled,
 *      OperationalCoinUpdated).
 *   3. Falls back to SWR (Stale-While-Revalidate) polling with exponential
 *      backoff when the WebSocket connection drops.
 *   4. Uses AbortController to cancel pending requests on tab hide / navigation.
 *   5. Caches the last-known KPI payload in sessionStorage for instant
 *      skeletal rendering on the next page visit.
 *
 * Usage (in the dashboard Blade shell, replacing inline polling scripts):
 *
 *   <script>
 *     const JERO_DASHBOARD = {
 *       companyId: {{ auth()->user()->company_id }},
 *       userId:    {{ auth()->user()->id }},
 *       csrfToken: '{{ csrf_token() }}',
 *       echoKey:   '{{ config("broadcasting.connections.reverb.key") }}',
 *       echoHost:  '{{ config("broadcasting.connections.reverb.options.host") }}',
 *       echoPort:  {{ config("broadcasting.connections.reverb.options.port") }},
 *     };
 *   </script>
 *   <script src="{{ asset('js/service_company_dashboard.js') }}" defer></script>
 */

'use strict';

(function () {
  /* ─── Config (injected by blade) ─────────────────────────────────────── */
  const cfg = window.JERO_DASHBOARD || {};
  const COMPANY_ID = cfg.companyId || 0;
  const CSRF_TOKEN = cfg.csrfToken || '';
  const STORAGE_KEY = `jero:dashboard:${COMPANY_ID}:kpis`;

  /* ─── Base URLs ───────────────────────────────────────────────────────── */
  const BASE = '/service_company/api/v2/service-company/dashboard';
  const ENDPOINTS = {
    kpis:         `${BASE}/kpis`,
    leaderboards: `${BASE}/leaderboards`,
    interviews:   `${BASE}/interviews`,
    invalidate:   `${BASE}/cache/invalidate`,
  };

  /* ─── Abort controller pool ───────────────────────────────────────────── */
  let activeControllers = [];

  function fetchJson(url) {
    const ctrl = new AbortController();
    activeControllers.push(ctrl);
    return fetch(url, {
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
      signal: ctrl.signal,
    })
      .then((r) => (r.ok ? r.json() : Promise.reject(r.status)))
      .finally(() => {
        activeControllers = activeControllers.filter((c) => c !== ctrl);
      });
  }

  function cancelAll() {
    activeControllers.forEach((c) => c.abort());
    activeControllers = [];
  }

  /* ─── Skeletal cache (sessionStorage) ────────────────────────────────── */
  function loadSkeleton() {
    try {
      const raw = sessionStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (_) {
      return null;
    }
  }

  function saveSkeleton(data) {
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (_) {}
  }

  /* ─── DOM helpers ─────────────────────────────────────────────────────── */
  function setText(selector, value) {
    const el = document.querySelector(selector);
    if (el) el.textContent = value;
  }

  /* ─── KPI renderer ────────────────────────────────────────────────────── */
  function renderKpis(data) {
    if (!data) return;
    setText('[data-kpi="active_jobs"]',      data.active_jobs      ?? '—');
    setText('[data-kpi="active_users"]',     data.active_users     ?? '—');
    setText('[data-kpi="today_interviews"]', data.today_interviews  ?? '—');
    setText('[data-kpi="operational_coins"]', data.operational_coins ?? '—');
    setText('[data-kpi="sales_coins"]',      data.sales_coins       ?? '—');
  }

  /* ─── SWR polling with exponential backoff ────────────────────────────── */
  let swrDelay = 30_000; // 30 s initial
  let swrTimer = null;

  function scheduleSwrRefresh() {
    clearTimeout(swrTimer);
    if (document.hidden) return; // pause while tab not visible
    swrTimer = setTimeout(async () => {
      try {
        const [kpisRes, interviewsRes] = await Promise.all([
          fetchJson(ENDPOINTS.kpis),
          fetchJson(ENDPOINTS.interviews),
        ]);
        if (kpisRes.status === 'ok') {
          renderKpis(kpisRes.data);
          saveSkeleton(kpisRes.data);
        }
        swrDelay = 30_000; // reset on success
      } catch (e) {
        if (e?.name !== 'AbortError') {
          swrDelay = Math.min(swrDelay * 2, 120_000); // max 2 min backoff
        }
      }
      scheduleSwrRefresh();
    }, swrDelay);
  }

  /* ─── WebSocket (Reverb / Echo) ───────────────────────────────────────── */
  function connectReverb() {
    if (!window.Echo || !COMPANY_ID) return;

    const channel = window.Echo.private(`company.${COMPANY_ID}.dashboard`);

    channel.listen('.CandidateStatusChanged', () => {
      fetchJson(ENDPOINTS.kpis).then((r) => {
        if (r.status === 'ok') { renderKpis(r.data); saveSkeleton(r.data); }
      }).catch(() => {});
    });

    channel.listen('.InterviewScheduled', () => {
      fetchJson(ENDPOINTS.interviews).then((r) => {
        // Detailed interview list rendering is handled by the existing Blade
        // component that already knows how to render rows — just trigger a re-fetch
        // by dispatching a custom event the Blade JS can hook into.
        if (r.status === 'ok') {
          document.dispatchEvent(new CustomEvent('jero:interviews-updated', { detail: r.data }));
          fetchJson(ENDPOINTS.kpis).then((kr) => {
            if (kr.status === 'ok') { renderKpis(kr.data); saveSkeleton(kr.data); }
          }).catch(() => {});
        }
      }).catch(() => {});
    });

    channel.listen('.OperationalCoinUpdated', (e) => {
      // Direct coin balance update — no extra fetch needed.
      const el = document.querySelector('[data-kpi="operational_coins"]');
      if (el) el.textContent = e.newBalance ?? '—';
      // Also refresh full KPI set to keep all counts consistent.
      fetchJson(ENDPOINTS.kpis).then((r) => {
        if (r.status === 'ok') { renderKpis(r.data); saveSkeleton(r.data); }
      }).catch(() => {});
    });

    // When WS reconnects after a drop, reset SWR backoff.
    window.Echo.connector?.pusher?.connection?.bind('connected', () => {
      swrDelay = 30_000;
    });
  }

  /* ─── Visibility change — pause/resume ────────────────────────────────── */
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      cancelAll();
      clearTimeout(swrTimer);
    } else {
      scheduleSwrRefresh(); // resume
    }
  });

  /* ─── Boot ────────────────────────────────────────────────────────────── */
  function init() {
    if (!COMPANY_ID) return; // outside service-company context

    // 1. Instant render from session cache (skeletal display).
    const cached = loadSkeleton();
    if (cached) renderKpis(cached);

    // 2. Fetch fresh KPIs immediately.
    fetchJson(ENDPOINTS.kpis).then((r) => {
      if (r.status === 'ok') { renderKpis(r.data); saveSkeleton(r.data); }
    }).catch(() => {});

    // 3. Subscribe to Reverb push channel.
    connectReverb();

    // 4. Start SWR polling as fallback (runs even when WS is active — no harm
    //    since the 60-second cache absorbs the request cost).
    scheduleSwrRefresh();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
