import http from 'k6/http';
import { check, sleep } from 'k6';

/**
 * Phase 15 — k6 10,000 Concurrent User Load Simulation for Client Details V2.
 * Simulates 10,000 active recruiters opening Client Details drawers and switching tabs.
 */
export const options = {
  stages: [
    { duration: '30s', target: 500 },   // Ramp-up to 500 concurrent recruiters
    { duration: '1m',  target: 2000 },  // Ramp-up to 2,000 concurrent recruiters
    { duration: '2m',  target: 10000 }, // Peak load: 10,000 active concurrent recruiters
    { duration: '1m',  target: 10000 }, // Sustain peak 10K active recruiters
    { duration: '30s', target: 0 },     // Ramp-down
  ],
  thresholds: {
    http_req_failed: ['rate<0.001'],    // Error rate must be under 0.1%
    http_req_duration: ['p(95)<100'],   // p95 latency must be under 100ms
  },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
  const clientId = 1;

  // 1. Initial Drawer Shell Open (Phase 1 V2 Shell < 60ms)
  const shellRes = http.get(`${BASE_URL}/service_company/clients/viewMore/${clientId}`);
  check(shellRes, {
    'drawer shell status 200': (r) => r.status === 200,
    'drawer shell latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 2. Tab Switch: Lazy Dashboard Analytics (Phase 8 Async API)
  const analyticsRes = http.get(`${BASE_URL}/service_company/clients/dashboard-analytics/${clientId}`);
  check(analyticsRes, {
    'analytics status 200': (r) => r.status === 200,
    'analytics latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 3. Tab Switch: Contacts DataTables (Phase 3 Indexed Query)
  const contactsRes = http.get(`${BASE_URL}/service_company/clients/contactdata?clientId=${clientId}`);
  check(contactsRes, {
    'contacts status 200': (r) => r.status === 200,
  });

  sleep(2);
}
