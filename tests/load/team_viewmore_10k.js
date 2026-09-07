import http from 'k6/http';
import { check, sleep } from 'k6';

/**
 * Phase 24 — k6 10,000 Concurrent User Load Simulation for Team View More V2.
 * 
 * Objective: Validate system performance under 10,000 active concurrent recruiters
 * opening Team View More page shell and lazy loading all 6 individual tabs on demand.
 * 
 * Endpoints Tested:
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

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';

export default function () {
  const userId = 3;

  // 1. Initial Page Shell Open (< 85ms light shell, 0 unopened tab queries)
  const shellRes = http.get(`${BASE_URL}/service_company/team/viewmore/${userId}`);
  check(shellRes, {
    'page shell status 200': (r) => r.status === 200,
    'page shell latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 2. Tab Switch: Lazy Jobs Tab
  const jobsRes = http.get(`${BASE_URL}/service_company/team_ajax/jobdata?userId=${userId}`);
  check(jobsRes, {
    'jobs tab status 200': (r) => r.status === 200,
    'jobs tab latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 3. Tab Switch: Lazy Candidate Tab
  const candidateRes = http.get(`${BASE_URL}/service_company/candidate_team_ajax/appdata?userId=${userId}`);
  check(candidateRes, {
    'candidate tab status 200': (r) => r.status === 200,
    'candidate tab latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 4. Tab Switch: Lazy Interview Tracker Tab
  const interviewRes = http.get(`${BASE_URL}/service_company/team_ajax_intdata/intdata?userId=${userId}`);
  check(interviewRes, {
    'interview tracker status 200': (r) => r.status === 200,
    'interview tracker latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 5. Tab Switch: Lazy Joinings Tab
  const joiningsRes = http.get(`${BASE_URL}/service_company/team_ajax_joiningdata/joiningdata?userId=${userId}`);
  check(joiningsRes, {
    'joinings tab status 200': (r) => r.status === 200,
    'joinings tab latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 6. Tab Switch: Lazy My Rank Tab
  const rankRes = http.get(`${BASE_URL}/service_company/team_ajax/rankdata?userId=${userId}`);
  check(rankRes, {
    'my rank tab status 200': (r) => r.status === 200,
    'my rank tab latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(1);

  // 7. Tab Switch: Lazy My Dashboard Tab
  const dashboardRes = http.get(`${BASE_URL}/service_company/team_ajax/dashboarddata?userId=${userId}`);
  check(dashboardRes, {
    'my dashboard tab status 200': (r) => r.status === 200,
    'my dashboard tab latency < 100ms': (r) => r.timings.duration < 100,
  });

  sleep(2);
}
