import http from 'k6/http';
import { check, sleep } from 'k6';

/**
 * Phase 25 — k6 Endurance & Soak Test for Team View More V2.
 * 
 * Objective: Verify system resilience, connection pool stability, zero memory leaks
 * in FrankenPHP/Octane workers, Redis connection pool, and MySQL connection pool under
 * sustained continuous load over an extended soak period (2,000 requests/min).
 * 
 * Target Endpoints:
 * 1. Initial Page Shell: /service_company/team/viewmore/3
 * 2. Jobs Tab:           /service_company/team_ajax/jobdata?userId=3
 * 3. Candidate Tab:      /service_company/candidate_team_ajax/appdata?userId=3
 * 4. Interview Tracker:  /service_company/team_ajax_intdata/intdata?userId=3
 * 5. Joinings Tab:       /service_company/team_ajax_joiningdata/joiningdata?userId=3
 * 6. My Rank Tab:        /service_company/team_ajax/rankdata?userId=3
 * 7. My Dashboard Tab:   /service_company/team_ajax/dashboarddata?userId=3
 */
export const options = {
  stages: [
    { duration: '2m',  target: 50 },    // Ramp up to 50 sustained VUs (~2,000 RPM)
    { duration: '4h',  target: 50 },    // Sustained 4-hour soak test
    { duration: '2m',  target: 0 },     // Gentle ramp down
  ],
  thresholds: {
    http_req_failed: ['rate<0.0005'],   // Exceptionally low error tolerance (< 0.05%)
    http_req_duration: ['p(95)<100'],   // p95 latency must remain under 100ms across 4 hours
  },
};

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';

export default function () {
  const userId = 3;

  // 1. Light Page Shell GET
  const shellRes = http.get(`${BASE_URL}/service_company/team/viewmore/${userId}`);
  check(shellRes, {
    'soak shell status 200': (r) => r.status === 200,
    'soak shell latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1.5);

  // 2. Jobs Tab Lazy Load
  const jobsRes = http.get(`${BASE_URL}/service_company/team_ajax/jobdata?userId=${userId}`);
  check(jobsRes, {
    'soak jobs status 200': (r) => r.status === 200,
  });

  sleep(1.5);

  // 3. Candidate Tab Lazy Load
  const candidateRes = http.get(`${BASE_URL}/service_company/candidate_team_ajax/appdata?userId=${userId}`);
  check(candidateRes, {
    'soak candidate status 200': (r) => r.status === 200,
  });

  sleep(1.5);

  // 4. Interview Tracker Tab Lazy Load
  const interviewRes = http.get(`${BASE_URL}/service_company/team_ajax_intdata/intdata?userId=${userId}`);
  check(interviewRes, {
    'soak interview tracker status 200': (r) => r.status === 200,
  });

  sleep(1.5);

  // 5. Joinings Tab Lazy Load
  const joiningsRes = http.get(`${BASE_URL}/service_company/team_ajax_joiningdata/joiningdata?userId=${userId}`);
  check(joiningsRes, {
    'soak joinings status 200': (r) => r.status === 200,
  });

  sleep(1.5);

  // 6. My Rank Tab Lazy Load
  const rankRes = http.get(`${BASE_URL}/service_company/team_ajax/rankdata?userId=${userId}`);
  check(rankRes, {
    'soak my rank status 200': (r) => r.status === 200,
  });

  sleep(1.5);

  // 7. My Dashboard Tab Lazy Load
  const dashboardRes = http.get(`${BASE_URL}/service_company/team_ajax/dashboarddata?userId=${userId}`);
  check(dashboardRes, {
    'soak dashboard status 200': (r) => r.status === 200,
  });

  sleep(3);
}
