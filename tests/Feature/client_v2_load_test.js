import http from 'k6/http';
import { check, sleep } from 'k6';

/*
 * Client V2 Load Test Script (k6)
 *
 * INTENT: Simulates up to 10,000 concurrent recruiters accessing Add Client, Edit Client,
 * and Domain Registration Validation endpoints via Client Module V2.
 *
 * Thresholds:
 * - 95th percentile response time < 100ms
 * - 99th percentile response time < 200ms
 * - Error rate < 1%
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
        http_req_duration: ['p(95)<100', 'p(99)<200'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = __ENV.TARGET_URL || 'http://localhost/git/JWMaincode28June2026';

export default function () {
    const params = {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    // 1. Add Client Page Load (/service_company/clients/create)
    const createRes = http.get(`${BASE_URL}/service_company/clients/create`, params);
    check(createRes, {
        'create status is 200 or 302': (r) => r.status === 200 || r.status === 302,
        'create response duration < 100ms': (r) => r.timings.duration < 100,
    });

    // 2. Domain Register Check AJAX (/saasfront/client-domain-register-chk)
    const domains = ['google.com', 'microsoft.com', 'jero.works', 'apple.com', 'amazon.com'];
    const randomDomain = domains[Math.floor(Math.random() * domains.length)];

    const domainPayload = JSON.stringify({ website: `https://www.${randomDomain}` });
    const jsonParams = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    const domainRes = http.post(`${BASE_URL}/saasfront/client-domain-register-chk`, domainPayload, jsonParams);
    check(domainRes, {
        'domain check status is 200 or 302': (r) => r.status === 200 || r.status === 302,
    });

    sleep(1);
}
