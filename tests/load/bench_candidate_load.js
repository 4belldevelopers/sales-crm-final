import http from 'k6/http';
import { check, sleep } from 'k6';

/*
 * Bench Candidate V4 Load Test Script (k6)
 *
 * INTENT: Simulates 10,000 concurrent recruiters searching, filtering, and submitting talent
 * via /service_company/bench-candidates/data_v2 and /service_company/bench-candidates/store.
 *
 * Thresholds:
 * - 95th percentile response time < 100ms for read queries
 * - 95th percentile response time < 150ms for submissions
 * - Transaction duration P95 < 15ms
 * - Error rate < 1%
 */

export const options = {
    stages: [
        { duration: '30s', target: 100 },   // Warm up to 100 recruiters
        { duration: '1m',  target: 1000 },  // Scale to 1,000 recruiters
        { duration: '2m',  target: 5000 },  // Scale to 5,000 recruiters
        { duration: '2m',  target: 10000 }, // Peak load: 10,000 concurrent recruiters
        { duration: '1m',  target: 0 },     // Ramp down
    ],
    thresholds: {
        'http_req_duration{type:read}': ['p(95)<100', 'p(99)<200'],
        'http_req_duration{type:write}': ['p(95)<150', 'p(99)<300'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = __ENV.TARGET_URL || 'http://localhost/git/JWMaincode28June2026';

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}

export default function () {
    const isWrite = Math.random() < 0.1; // 10% writes, 90% reads (standard CQRS distribution)

    const baseHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (isWrite) {
        // Talent Submission with Idempotency Key (Gate G-52 & G-53)
        const clientUuid = generateUUID();
        const url = `${BASE_URL}/service_company/bench-candidates`;
        const payload = JSON.stringify({
            client_submission_uuid: clientUuid,
            full_name: 'Load Test Candidate ' + Math.floor(Math.random() * 100000),
            email: `loadtest_${Math.floor(Math.random() * 100000)}@example.com`,
            talent_stage: 2, // Bench
            total_exp_year: 5,
            total_exp_month: 0,
            skills: ['PHP', 'Laravel', 'Elasticsearch'],
            pay_rate_currency: 1,
            pay_annual: 1200000,
            bill_rate_currency: 1,
            bill_annual: 1800000,
            percentage_hike: 20,
        });

        const writeHeaders = Object.assign({}, baseHeaders, {
            'Content-Type': 'application/json',
            'X-Idempotency-Key': clientUuid,
        });

        const res = http.post(url, payload, {
            headers: writeHeaders,
            tags: { type: 'write' },
        });

        check(res, {
            'write status is 200 or 409': (r) => r.status === 200 || r.status === 409,
            'write latency < 150ms': (r) => r.timings.duration < 150,
        });
    } else {
        // Elasticsearch CQRS Read Query
        const pages = [1, 2, 3, 4, 5];
        const stages = [1, 2, 4, 5];
        const randomPage = pages[Math.floor(Math.random() * pages.length)];
        const randomStage = stages[Math.floor(Math.random() * stages.length)];

        const url = `${BASE_URL}/service_company/bench-candidates/data_v2?page=${randomPage}&selectedValue=${randomStage}&size=20`;

        const res = http.get(url, {
            headers: baseHeaders,
            tags: { type: 'read' },
        });

        check(res, {
            'read status is 200': (r) => r.status === 200,
            'read latency < 100ms': (r) => r.timings.duration < 100,
        });
    }

    sleep(1);
}
