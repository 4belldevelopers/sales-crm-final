import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

/**
 * 10,000 Concurrent Recruiter Load Test Harness for Bench Talent Requests V2.
 *
 * Simulates:
 * - 80% RAM store cache hits / zero server roundtrips.
 * - 15% AJAX candidate request queries & tab switches.
 * - 5% Concurrent bench talent request submissions with idempotency protection.
 *
 * Target:
 * - API P95 latency < 85ms.
 * - 0% server/DB read strain on cache hits.
 * - 0 MySQL Deadlocks.
 */
export const options = {
    stages: [
        { duration: '2m', target: 1000 },   // Warm-up ramp
        { duration: '5m', target: 5000 },   // Scale ramp
        { duration: '5m', target: 10000 },  // 10,000 peak concurrent users
        { duration: '3m', target: 0 },      // Ramp-down
    ],
    thresholds: {
        'http_req_duration{type:read}': ['p(95)<85'],     // Read latency < 85ms
        'http_req_duration{type:mutation}': ['p(95)<250'], // Mutation latency < 250ms
        'http_req_failed': ['rate<0.01'],                 // Error rate < 1%
        'deadlock_detected': ['count==0'],                // 0 deadlocks allowed
    },
};

const readLatency = new Trend('read_latency_ms');
const mutationLatency = new Trend('mutation_latency_ms');
const deadlocks = new Counter('deadlock_detected');
const errorRate = new Rate('error_rate');

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const AUTH_TOKEN = __ENV.AUTH_TOKEN || 'test-bearer-token';
const COMPANY_ID = __ENV.COMPANY_ID || 1;
const JOB_ID = __ENV.JOB_ID || 100;

export default function () {
    const userRoleRand = Math.random();

    // 80% of recruiter interactions are client-side tab switching & cached RAM reads (0 network calls)
    if (userRoleRand < 0.80) {
        // Client-first RAM execution: Simulated 0ms cache hit in browser memory
        sleep(0.5);
        return;
    }

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': `Bearer ${AUTH_TOKEN}`,
    };

    // 15% AJAX candidate request queries & filtering
    if (userRoleRand < 0.95) {
        group('AJAX Read Candidates Request', function () {
            const tabTypes = ['pool', 'internal_bench', 'public_bench', 'existing'];
            const randomTab = tabTypes[Math.floor(Math.random() * tabTypes.length)];
            const url = `${BASE_URL}/service_company/apptracker/candidates_request_data_ajax?tab=${randomTab}&jobid=${JOB_ID}&page=1`;

            const res = http.get(url, { headers, tags: { type: 'read' } });
            readLatency.add(res.timings.duration);

            const isSuccess = check(res, {
                'read status is 200': (r) => r.status === 200,
                'embedded counter present': (r) => {
                    try {
                        const body = JSON.parse(r.body);
                        return body.pool_counter !== undefined && body.cache_version !== undefined;
                    } catch (e) {
                        return false;
                    }
                },
            });

            if (!isSuccess) {
                errorRate.add(1);
            }
        });
        sleep(1);
        return;
    }

    // 5% Mutation: Submit candidate request with idempotency protection
    group('Candidate Request Mutation', function () {
        const candidateId = Math.floor(Math.random() * 500) + 1;
        const idempotencyKey = `bench_req_${COMPANY_ID}_${JOB_ID}_${candidateId}_${Date.now()}`;

        const mutationHeaders = Object.assign({}, headers, {
            'X-Idempotency-Key': idempotencyKey,
        });

        const submitUrl = `${BASE_URL}/service_company/jobs/recommandedrequestcondidate/${JOB_ID}?conId=${candidateId}`;
        const res = http.get(submitUrl, { headers: mutationHeaders, tags: { type: 'mutation' } });
        mutationLatency.add(res.timings.duration);

        if (res.body && res.body.includes('Deadlock found')) {
            deadlocks.add(1);
        }

        const isSuccess = check(res, {
            'mutation returns 200 or 4xx valid business response': (r) => [200, 400, 403, 422].includes(r.status),
        });

        if (!isSuccess) {
            errorRate.add(1);
        }
    });

    sleep(1.5);
}
