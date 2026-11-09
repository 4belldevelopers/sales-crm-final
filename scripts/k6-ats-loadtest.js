/**
 * scripts/k6-ats-loadtest.js
 *
 * Grafana k6 High-Concurrency Virtual Recruiter Simulation for JERO ATS
 * Tests target concurrency, response latency thresholds (p95/p99), and error rates.
 *
 * Usage:
 *   .\tools\k6\k6.exe run scripts/k6-ats-loadtest.js
 */

import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '5s', target: 20 },  // Ramp-up to 20 concurrent recruiters
        { duration: '10s', target: 50 }, // Surge to 50 concurrent recruiters
        { duration: '5s', target: 0 },   // Ramp-down
    ],
    thresholds: {
        http_req_failed: ['rate<0.01'],    // Error rate must be under 1%
        http_req_duration: ['p(95)<400', 'p(99)<800'], // 95% of requests under 400ms, 99% under 800ms
    },
    insecureSkipTLSVerify: true,
};

const BASE_URL = __ENV.TARGET_URL || 'https://testing.jero.works';

export default function () {
    // 1. Visit Portal Root / Public Front
    const res = http.get(`${BASE_URL}/`, {
        headers: {
            'User-Agent': 'k6-JERO-ATS-Virtual-Recruiter/1.0',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
        timeout: '10s',
    });

    check(res, {
        'status is 200': (r) => r.status === 200,
        'response time < 500ms': (r) => r.timings.duration < 500,
    });

    sleep(0.5); // 500ms think time between recruiter interactions
}
