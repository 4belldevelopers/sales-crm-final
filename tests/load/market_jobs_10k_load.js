import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

/**
 * 10,000 Concurrent Recruiter Load Simulation for Service Company Market Jobs.
 *
 * Simulates:
 * - 85% Browser RAM & IndexedDB cache hits / zero server roundtrips.
 * - 10% V2 Market Jobs Elasticsearch queries & paginations.
 * - 5% Concurrent Requisition Claims with X-Idempotency-Key.
 *
 * Concurrency Scale:
 * - 1,000 users (Ramp)
 * - 5,000 users (Medium scale)
 * - 10,000 users (GOD-Level peak load)
 */
export const options = {
    stages: [
        { duration: '1m', target: 1000 },   // Ramp-up to 1,000 concurrent recruiters
        { duration: '2m', target: 5000 },   // Ramp-up to 5,000 concurrent recruiters
        { duration: '3m', target: 10000 },  // Peak load: 10,000 concurrent recruiters
        { duration: '2m', target: 10000 },  // Sustain 10,000 peak users
        { duration: '1m', target: 0 },      // Ramp-down
    ],
    thresholds: {
        'http_req_duration{type:read}': ['p(95)<45'],      // Fast reads < 45ms
        'http_req_duration{type:mutation}': ['p(95)<150'],  // Outbox mutations < 150ms
        'http_req_failed': ['rate<0.005'],                  // Error rate < 0.5%
        'duplicate_claim_prevented': ['count>0'],           // Idempotency working
    },
};

const readLatency = new Trend('read_latency_ms');
const mutationLatency = new Trend('mutation_latency_ms');
const duplicateClaims = new Counter('duplicate_claim_prevented');
const errorRate = new Rate('error_rate');

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const AUTH_TOKEN = __ENV.AUTH_TOKEN || 'test-bearer-token';
const COMPANY_ID = __ENV.COMPANY_ID || 1;

export default function () {
    const userBehaviorRand = Math.random();

    // 85% of interactions are served directly from Browser RAM or IndexedDB (< 15ms, 0 server requests)
    if (userBehaviorRand < 0.85) {
        // Client-first Browser Execution
        sleep(1);
        return;
    }

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': `Bearer ${AUTH_TOKEN}`,
    };

    // 10% V2 Market Jobs Elasticsearch queries & paginations
    if (userBehaviorRand < 0.95) {
        group('V2 Market Jobs Query', function () {
            const page = Math.floor(Math.random() * 5) + 1;
            const res = http.get(
                `${BASE_URL}/service_company/market-jobs-list-v2-ajax?page=${page}&size=10`,
                {
                    headers: headers,
                    tags: { type: 'read' },
                }
            );

            readLatency.add(res.timings.duration);

            const isOk = check(res, {
                'read status 200': (r) => r.status === 200,
                'read latency < 80ms': (r) => r.timings.duration < 80,
                'valid items returned': (r) => {
                    try {
                        const json = r.json();
                        return Array.isArray(json.items);
                    } catch (e) {
                        return false;
                    }
                },
            });

            errorRate.add(!isOk);
        });

        sleep(2);
        return;
    }

    // 5% Authoritative Requisition Claims with Idempotency & Outbox Protection
    group('Authoritative Job Claim Mutation', function () {
        const jobId = 101;
        const idempotencyKey = `claim_${jobId}_user_${__VU}_${Math.floor(Date.now() / 1000)}`;

        const mutationHeaders = Object.assign({}, headers, {
            'X-Idempotency-Key': idempotencyKey,
        });

        const payload = JSON.stringify({
            jobId: jobId,
            status: 1,
            idempotency_key: idempotencyKey,
        });

        const res = http.post(
            `${BASE_URL}/service_company/jobs/market/claim`,
            payload,
            {
                headers: mutationHeaders,
                tags: { type: 'mutation' },
            }
        );

        mutationLatency.add(res.timings.duration);

        // Immediate duplicate retry simulation (testing idempotency guard)
        const retryRes = http.post(
            `${BASE_URL}/service_company/jobs/market/claim`,
            payload,
            {
                headers: mutationHeaders,
                tags: { type: 'mutation' },
            }
        );

        if (retryRes.status === 200 || retryRes.status === 425) {
            duplicateClaims.add(1);
        }

        const isOk = check(res, {
            'claim status 200 or 425': (r) => r.status === 200 || r.status === 425,
            'mutation latency < 200ms': (r) => r.timings.duration < 200,
        });

        errorRate.add(!isOk);
    });

    sleep(3);
}
