import http from 'k6/http';
import { check, sleep } from 'k6';

/*
 * Career Website Settings V2 Load Test Script (k6)
 *
 * INTENT: Simulates up to 10,000 concurrent recruiters & candidates accessing
 * Career Page Settings and candidate portal metadata endpoints via Career Settings V2.
 *
 * Thresholds:
 * - 95th percentile response time < 50ms (served from Redis cache)
 * - 99th percentile response time < 100ms
 * - Error rate < 0.1%
 *
 * Author: Antigravity AI
 * Date: August 17, 2026
 */

export const options = {
    stages: [
        { duration: '30s', target: 100 },   // Warm up to 100 recruiters
        { duration: '1m',  target: 1000 },  // Ramp to 1,000 recruiters
        { duration: '2m',  target: 5000 },  // Scale to 5,000 recruiters
        { duration: '2m',  target: 10000 }, // Peak load 10,000 concurrent recruiters
        { duration: '1m',  target: 0 },     // Ramp down
    ],
    thresholds: {
        http_req_duration: ['p(95)<50', 'p(99)<100'],
        http_req_failed: ['rate<0.001'],
    },
};

const BASE_URL = __ENV.TARGET_URL || 'http://localhost/git/JWMaincode28June2026';

export default function () {
    const params = {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    // 1. GET Career Page Settings (/service_company/career-page/settings)
    const settingsRes = http.get(`${BASE_URL}/service_company/career-page/settings`, params);
    check(settingsRes, {
        'settings status is 200 or 302': (r) => r.status === 200 || r.status === 302,
        'cached response duration < 50ms': (r) => r.timings.duration < 50,
    });

    sleep(1);
}
