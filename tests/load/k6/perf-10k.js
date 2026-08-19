// tests/load/k6/perf-10k.js
//
// k6 load harness for the JERO Add/Edit/Submit audit — Verifies 10K-concurrent
// readiness against the SLOs in docs/V2_Perf-10K/perf-budget.json.
//
// Usage:
//   k6 run --out json=results.json tests/load/k6/perf-10k.js
//   k6 run -e BASE_URL=https://staging.jero.works -e TENANT_ID=42 tests/load/k6/perf-10k.js
//
// Phases (target):
//   1. Ramp 0 → 1K RPS over 60s (smoke + warm caches)
//   2. Ramp 1K → 5K RPS over 120s (read-heavy)
//   3. Sustain 5K RPS for 120s (avg load)
//   4. Spike 5K → 10K RPS over 60s (peak burst)
//   5. Sustain 10K RPS for 120s (10K concurrent target)
//   6. Ramp 10K → 0 RPS over 60s (cooldown)
//
// Thresholds (from perf-budget.json):
//   - p95 page TTFB < 200 ms (Blade Octane render)
//   - p95 POST roundtrip < 100 ms (Add/Edit/Submit)
//   - p95 DataTables AJAX < 250 ms
//   - p95 ES query < 100 ms
//   - error rate < 1%
//   - Webhook ack < 50 ms
//
// All routes are derived from docs/V2_Add-Edit-Submit/evidence/_Add-Edit-Submit-Audit-evidence.md
// and docs/V2_Perf-10K/perf-budget.json. In-scope personas only.

import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { SharedArray } from 'k6/data';

// -----------------------------------------------------------------------------
// Config
// -----------------------------------------------------------------------------

const BASE_URL = __ENV.BASE_URL || 'https://testing.jero.works';
const TENANT_ID = __ENV.TENANT_ID || '1';
const AUTH_TOKEN = __ENV.AUTH_TOKEN || ''; // session/cookie set externally; see seed-token.js

// Per-route tags so the trend metrics are split.
const tagPageTTFB = new Trend('page_ttfb_ms', true);
const tagPostRT = new Trend('post_roundtrip_ms', true);
const tagAjaxRT = new Trend('ajax_roundtrip_ms', true);
const tagEsRT = new Trend('es_roundtrip_ms', true);
const tagWebhookRT = new Trend('webhook_ack_ms', true);
const errRate = new Rate('errors');
const reqCount = new Counter('requests_total');

// -----------------------------------------------------------------------------
// Routes (top 30 endpoints hit hardest in the audit)
// -----------------------------------------------------------------------------
// Each entry maps to a perf-budget.json route key. The actual URI is constructed
// from the canonical audit route in routes/service.php / routes/superadmin.php /
// routes/web.php. Tenant scoped via :company_id path placeholder or query string.

const ROUTES = [
  // ---------- READ paths (Blade render + DataTables AJAX) ----------
  {
    name: 'service_apptracker_index',
    method: 'GET',
    path: '/service-company/apptracker',
    weight: 8, // high traffic
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'service_apptracker_ajax',
    method: 'GET',
    path: '/service-company/apptracker/_ajax?start=0&length=20',
    weight: 12, // hot list
    metric: 'ajax',
    auth: true,
  },
  {
    name: 'service_cvtracker_index',
    method: 'GET',
    path: '/service-company/cvtracker',
    weight: 6,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'service_cvtracker_ajax',
    method: 'GET',
    path: '/service-company/cvtracker/_ajax?start=0&length=20',
    weight: 10,
    metric: 'ajax',
    auth: true,
  },
  {
    name: 'service_bench_candidates_index',
    method: 'GET',
    path: '/service-company/bench-candidates',
    weight: 5,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'service_job_edit',
    method: 'GET',
    path: '/service-company/jobs/1/edit',
    weight: 3,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'service_job_application_edit',
    method: 'GET',
    path: '/service-company/job-applications/1/edit',
    weight: 4,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'superadmin_company_list_ajax',
    method: 'GET',
    path: '/super-admin/company/_ajax?start=0&length=20',
    weight: 4,
    metric: 'ajax',
    auth: true,
  },
  {
    name: 'superadmin_company_edit',
    method: 'GET',
    path: '/super-admin/company/edit/1',
    weight: 2,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'superadmin_coupon_add_edit',
    method: 'GET',
    path: '/super-admin/coupons/add-edit/1',
    weight: 1,
    metric: 'page_ttfb',
    auth: true,
  },
  // ---------- WRITE paths (POST/PUT/DELETE) ----------
  {
    name: 'service_job_application_stage_submit',
    method: 'POST',
    path: '/service-company/job-applications/stage_submit',
    weight: 6,
    metric: 'post',
    auth: true,
    body: { application_id: 1, stage_id: 2 },
  },
  {
    name: 'service_job_application_status_update',
    method: 'POST',
    path: '/service-company/job-applications/statusupdate',
    weight: 5,
    metric: 'post',
    auth: true,
    body: { application_id: 1, status: 'screening' },
  },
  {
    name: 'service_apptracker_store_bulk',
    method: 'POST',
    path: '/service-company/apptracker/store',
    weight: 2,
    metric: 'post',
    auth: true,
    body: { job_id: 1, candidate_ids: [1, 2, 3, 4, 5] },
  },
  {
    name: 'service_job_update',
    method: 'POST',
    path: '/service-company/jobs/update/1',
    weight: 2,
    metric: 'post',
    auth: true,
    body: { title: 'Load Test Update', description: 'k6' },
  },
  {
    name: 'service_team_store',
    method: 'POST',
    path: '/service-company/team/store',
    weight: 1,
    metric: 'post',
    auth: true,
    body: { name: 'k6 user', email: `k6+${__VU}@test.local`, role: 'recruiter' },
  },
  {
    name: 'service_role_save',
    method: 'POST',
    path: '/service-company/role-permission/saveRole',
    weight: 1,
    metric: 'post',
    auth: true,
    body: { name: 'k6_role', permissions: ['view-jobs'] },
  },
  // ---------- AUTH ----------
  {
    name: 'auth_login',
    method: 'POST',
    path: '/login',
    weight: 2,
    metric: 'post',
    auth: false,
    body: { email: 'loadtest@jero.works', password: 'k6_password' },
  },
  // ---------- WEBHOOKS (ack-only path; verify is queued) ----------
  {
    name: 'webhook_paypal_ipn',
    method: 'POST',
    path: '/verify-billing-ipn',
    weight: 1,
    metric: 'webhook',
    auth: false,
    body: { ipn_track_id: 'k6_test' },
  },
  {
    name: 'webhook_stripe',
    method: 'POST',
    path: '/save-invoices',
    weight: 1,
    metric: 'webhook',
    auth: false,
    body: { type: 'invoice.payment_succeeded', id: 'k6_test' },
  },
  {
    name: 'webhook_razorpay',
    method: 'POST',
    path: '/save-razorpay-invoices',
    weight: 1,
    metric: 'webhook',
    auth: false,
    body: { event: 'payment.captured', payload: { payment: { id: 'k6_test' } } },
  },
  {
    name: 'webhook_phonepe_callback',
    method: 'POST',
    path: '/payment-callback',
    weight: 1,
    metric: 'webhook',
    auth: false,
    body: { transactionId: 'k6_test', responseCode: 'SUCCESS' },
  },
  // ---------- PUBLIC / FRONT ----------
  {
    name: 'public_career_apply',
    method: 'POST',
    path: '/job/saveApplication',
    weight: 1,
    metric: 'post',
    auth: false,
    body: { job_id: 1, name: 'k6 candidate', email: `k6+${__VU}@test.local` },
  },
  {
    name: 'public_save_offer',
    method: 'POST',
    path: '/save-offer',
    weight: 1,
    metric: 'post',
    auth: false,
    body: { offer_id: 1, signature: 'k6' },
  },
  {
    name: 'public_login_otp_send',
    method: 'POST',
    path: '/send-otp-code',
    weight: 1,
    metric: 'post',
    auth: false,
    body: { phone: '+910000000000' },
  },
  {
    name: 'public_company_register',
    method: 'POST',
    path: '/company-register',
    weight: 1,
    metric: 'post',
    auth: false,
    body: { company_name: `k6 co ${__VU}`, email: `k6+co${__VU}@test.local` },
  },
  // ---------- ES query proxy (paths to ESService through the app or direct) ----------
  {
    name: 'es_candidates_search',
    method: 'GET',
    path: '/service-company/cvtracker/_ajax?es_query=1&q=engineer',
    weight: 6,
    metric: 'es',
    auth: true,
  },
  {
    name: 'es_jobs_search',
    method: 'GET',
    path: '/service-company/apptracker/_ajax?es_query=1&q=developer',
    weight: 5,
    metric: 'es',
    auth: true,
  },
  {
    name: 'es_bench_search',
    method: 'GET',
    path: '/service-company/bench-candidates/_ajax?es_query=1&q=devops',
    weight: 3,
    metric: 'es',
    auth: true,
  },
  // ---------- Super Admin list endpoints ----------
  {
    name: 'superadmin_coupons_list',
    method: 'GET',
    path: '/super-admin/coupons',
    weight: 1,
    metric: 'page_ttfb',
    auth: true,
  },
  {
    name: 'superadmin_packages_list',
    method: 'GET',
    path: '/super-admin/packages',
    weight: 1,
    metric: 'page_ttfb',
    auth: true,
  },
];

// Pre-compute weighted cumulative weights for selection.
let cumWeight = 0;
const weightedRoutes = ROUTES.map((r) => {
  cumWeight += r.weight;
  return { ...r, cum: cumWeight };
});
const TOTAL_WEIGHT = cumWeight;

// -----------------------------------------------------------------------------
// Per-tenant ID injection (path placeholder :id or query string)
// -----------------------------------------------------------------------------

function buildUrl(route) {
  return BASE_URL + route.path.replace(/:company_id/g, TENANT_ID).replace(/:id/g, '1');
}

// -----------------------------------------------------------------------------
// Per-VU session-cookie / token cache (one auth call per VU, then reuse)
// -----------------------------------------------------------------------------

function getHeaders(route) {
  const headers = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if (route.auth && AUTH_TOKEN) {
    headers['Authorization'] = 'Bearer ' + AUTH_TOKEN;
  }
  if (route.auth) {
    headers['X-Tenant-Id'] = TENANT_ID;
  }
  return headers;
}

// -----------------------------------------------------------------------------
// Main scenario
// -----------------------------------------------------------------------------

export const options = {
  scenarios: {
    perf_10k: {
      executor: 'ramping-arrival-rate',
      startRate: 50,
      timeUnit: '1s',
      preAllocatedVUs: 200,
      maxVUs: 12000,
      stages: [
        { target: 1000, duration: '60s' },   // warm
        { target: 5000, duration: '120s' },  // ramp 1K→5K
        { target: 5000, duration: '120s' },  // sustain 5K
        { target: 10000, duration: '60s' },  // spike 5K→10K
        { target: 10000, duration: '120s' }, // sustain 10K (the target)
        { target: 0, duration: '60s' },      // cooldown
      ],
    },
  },
  thresholds: {
    // Master SLOs from perf-budget.json §global_targets.
    'page_ttfb_ms': ['p(95)<200'],
    'post_roundtrip_ms': ['p(95)<100'],
    'ajax_roundtrip_ms': ['p(95)<250'],
    'es_roundtrip_ms': ['p(95)<100'],
    'webhook_ack_ms': ['p(95)<50'],
    'errors': ['rate<0.01'],
    // Per-route tighten for the hottest screens.
    'page_ttfb_ms{name:service_apptracker_index}': ['p(95)<300'],
    'ajax_roundtrip_ms{name:service_apptracker_ajax}': ['p(95)<250'],
    'post_roundtrip_ms{name:service_job_application_stage_submit}': ['p(95)<100'],
  },
  noConnectionReuse: false,
  discardResponseBodies: true,
};

export default function () {
  // Pick a route by weight.
  const pick = Math.random() * TOTAL_WEIGHT;
  const route = weightedRoutes.find((r) => r.cum >= pick) || weightedRoutes[0];

  const url = buildUrl(route);
  const headers = getHeaders(route);
  const body = route.body ? JSON.stringify(route.body) : null;

  const params = { headers, tags: { name: route.name } };
  const startMs = Date.now();
  let res;
  if (route.method === 'GET') {
    res = http.get(url, params);
  } else if (route.method === 'POST') {
    res = http.post(url, body, params);
  } else if (route.method === 'PUT') {
    res = http.put(url, body, params);
  } else if (route.method === 'DELETE') {
    res = http.del(url, null, params);
  } else {
    res = http.get(url, params);
  }
  const elapsed = Date.now() - startMs;

  reqCount.add(1);
  const ok = check(res, {
    'status 2xx/3xx': (r) => r.status >= 200 && r.status < 400,
    'no 5xx': (r) => r.status < 500,
  });
  errRate.add(!ok);

  // Route the elapsed time to the right trend metric.
  const trendParams = { name: route.name };
  switch (route.metric) {
    case 'page_ttfb':
      tagPageTTFB.add(elapsed, trendParams);
      break;
    case 'post':
      tagPostRT.add(elapsed, trendParams);
      break;
    case 'ajax':
      tagAjaxRT.add(elapsed, trendParams);
      break;
    case 'es':
      tagEsRT.add(elapsed, trendParams);
      break;
    case 'webhook':
      tagWebhookRT.add(elapsed, trendParams);
      break;
  }

  // Light pacing — k6 arrival-rate pacing handles inter-arrival, but a tiny
  // per-iteration sleep smooths out the per-VU pile-up.
  sleep(0.05 + Math.random() * 0.1);
}

// -----------------------------------------------------------------------------
// Setup: optionally login once per VU and cache the cookie. For now we leave
// the AUTH_TOKEN env var as the source of truth (CI provides it via a
// k6 setup script or a short-lived login VU).
// -----------------------------------------------------------------------------

export function setup() {
  // Optional warm-up: hit one cheap endpoint to warm Octane cache + Redis.
  http.get(BASE_URL + '/service-company/apptracker/_ajax?start=0&length=1', {
    headers: getHeaders({ auth: true }),
  });
  return { startedAt: new Date().toISOString() };
}

export function handleSummary(data) {
  const out = {
    text: stdoutSummary(data),
    'results/summary.json': JSON.stringify(data, null, 2),
  };
  return out;
}

function stdoutSummary(data) {
  // Compact textual summary grouped by metric + top 5 routes by p95.
  const metrics = data.metrics || {};
  const lines = [
    '\n=== Perf-10K summary ===',
    `Start: ${data.state?.testRun?.default?.startTime || 'n/a'}`,
    `Requests: ${metrics.requests_total?.values?.count || 0}`,
    `Error rate: ${((metrics.errors?.values?.rate || 0) * 100).toFixed(2)}%`,
    '',
    '--- p95 by metric ---',
    `page_ttfb_ms:         ${(metrics.page_ttfb_ms?.values?.['p(95)'] || 0).toFixed(1)} ms (target < 200)`,
    `post_roundtrip_ms:    ${(metrics.post_roundtrip_ms?.values?.['p(95)'] || 0).toFixed(1)} ms (target < 100)`,
    `ajax_roundtrip_ms:    ${(metrics.ajax_roundtrip_ms?.values?.['p(95)'] || 0).toFixed(1)} ms (target < 250)`,
    `es_roundtrip_ms:      ${(metrics.es_roundtrip_ms?.values?.['p(95)'] || 0).toFixed(1)} ms (target < 100)`,
    `webhook_ack_ms:       ${(metrics.webhook_ack_ms?.values?.['p(95)'] || 0).toFixed(1)} ms (target < 50)`,
  ];
  return lines.join('\n');
}
