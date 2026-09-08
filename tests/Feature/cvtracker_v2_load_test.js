import http from 'k6/http';
import { check, sleep } from 'k6';

/*
 * CV Tracker V2 Load Test Script (k6)
 *
 * INTENT: Simulates up to 10,000 concurrent recruiters searching, filtering, and paging through
 * candidate listings via /service_company/all-candidate-list-v2 endpoint.
 *
 * Thresholds:
 * - 95th percentile response time < 100ms
 * - 99th percentile response time < 200ms
 * - Error rate < 1%
 */

export const options = {
    stages: [
        { duration: '30s', target: 100 },   // Warm up to 100 users
        { duration: '1m',  target: 1000 },  // Ramp to 1,000 users
        { duration: '2m',  target: 5000 },  // Scale to 5,000 users
        { duration: '2m',  target: 10000 }, // Peak load 10,000 concurrent recruiters
        { duration: '1m',  target: 0 },     // Ramp down
    ],
    thresholds: {
        http_req_duration: ['p(95)<100', 'p(99)<200'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = __ENV.TARGET_URL || 'http://localhost/git/JWMaincode28June2026';

export default function () {
    const pages = [1, 2, 3, 4, 5];
    const sorts = [1, 2, 3, 4, 5, 6, 21, 22];
    const randomPage = pages[Math.floor(Math.random() * pages.length)];
    const randomSort = sorts[Math.floor(Math.random() * sorts.length)];

    const url = `${BASE_URL}/service_company/all-candidate-list-v2?page=${randomPage}&sortingfilter=${randomSort}&size=20`;

    const params = {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    const res = http.get(url, params);

    check(res, {
        'status is 200': (r) => r.status === 200,
        'response time < 100ms': (r) => r.timings.duration < 100,
        'contains status success': (r) => r.json() && r.json().status === 'success',
    });

    sleep(1);
}
