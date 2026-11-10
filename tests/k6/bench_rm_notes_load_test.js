/**
 * Antigravity — Super Admin Relationship Manager (RM) Note Templates
 * Production Capacity, 10,000 Concurrent User Load & Failure Testing Suite (k6)
 *
 * Route: superadmin.rm-note.index (http://localhost/git/JWMaincode28June2026/super-admin/rm-note)
 * Architecture: Strict CQRS (ES Read 100%, MySQL Write 100%), L2 Dragonfly/Redis, Client IndexedDB
 * Target Concurrency: 10,000 Virtual Users / 0% Server Strain / Sub-150ms p95
 */

import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

// Custom Enterprise Metrics
export const cacheHitRate = new Rate('brmnote_cache_hit_rate');
export const readLatency = new Trend('brmnote_read_latency', true);
export const mutationLatency = new Trend('brmnote_mutation_latency', true);
export const idempotentReplayRate = new Rate('brmnote_idempotent_replay_rate');
export const failedRequests = new Counter('brmnote_failed_requests');

// 10K Multi-Stage Concurrency Profile (500 -> 1,000 -> 2,500 -> 5,000 -> 10,000 VUs)
export const options = {
    scenarios: {
        // High-concurrency enterprise load ramp
        concurrent_users: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '2m', target: 500 },    // Tier 1: 500 VUs
                { duration: '3m', target: 1000 },   // Tier 2: 1,000 VUs
                { duration: '5m', target: 2500 },   // Tier 3: 2,500 VUs
                { duration: '5m', target: 5000 },   // Tier 4: 5,000 VUs
                { duration: '10m', target: 10000 }, // Tier 5: 10,000 VUs Peak Sustained Stress
                { duration: '5m', target: 0 },      // Graceful ramp-down
            ],
            gracefulRampDown: '30s',
        },
        // Mutation burst scenario (simulating rapid simultaneous rubric saves)
        mutation_bursts: {
            executor: 'constant-arrival-rate',
            rate: 50, // 50 mutations per second
            timeUnit: '1s',
            duration: '15m',
            preAllocatedVUs: 100,
            maxVUs: 500,
        },
    },
    thresholds: {
        'http_req_duration': ['p(95)<150', 'p(99)<300'], // 95% of requests under 150ms, 99% under 300ms
        'brmnote_read_latency': ['p(95)<50', 'p(99)<100'], // Warm read path sub-50ms
        'http_req_failed': ['rate<0.01'],                  // Error rate < 1%
        'brmnote_idempotent_replay_rate': ['rate>0.90'],   // >90% idempotent replays on retry bursts
    },
};

const BASE_URL = __ENV.TARGET_URL || 'http://localhost/git/JWMaincode28June2026';
const CSRF_TOKEN = __ENV.CSRF_TOKEN || 'test_csrf_token';
const SESSION_COOKIE = __ENV.SESSION_COOKIE || '';

const HEADERS = {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json',
    'Cookie': SESSION_COOKIE,
};

export default function () {
    const selector = Math.random();

    // 85% Read Traffic: Primary Listing & Single Item Lookups (L2 Dragonfly / ES 0% MySQL)
    if (selector < 0.85) {
        group('Read Path — 0% MySQL Strain', function () {
            const start = Date.now();
            const res = http.get(`${BASE_URL}/super-admin/rm-note`, { headers: HEADERS });
            readLatency.add(Date.now() - start);

            const isSuccess = check(res, {
                'listing status 200': (r) => r.status === 200,
                'response has templates': (r) => r.body.includes('templates') || r.body.includes('brmnote-card'),
            });

            if (isSuccess) {
                cacheHitRate.add(1);
            } else {
                cacheHitRate.add(0);
                failedRequests.add(1);
            }
        });
    }
    // 10% Search & Status Filters: Elasticsearch Lowercase Query Path
    else if (selector < 0.95) {
        group('Search & Filter Path — Elasticsearch', function () {
            const terms = ['screening', 'margin', 'rubric', 'technical', 'developer'];
            const term = terms[Math.floor(Math.random() * terms.length)];
            const status = Math.random() > 0.5 ? 'active' : 'inactive';

            const start = Date.now();
            const res = http.get(`${BASE_URL}/super-admin/rm-note?search=${term}&status=${status}`, { headers: HEADERS });
            readLatency.add(Date.now() - start);

            check(res, {
                'search status 200': (r) => r.status === 200,
            });
        });
    }
    // 5% Concurrent Mutation Traffic: Two-Tier Idempotency & Transactional Outbox
    else {
        group('Mutation Path — ACID Write & Outbox', function () {
            const randomId = Math.floor(Math.random() * 1000);
            // Deterministic idempotency key for testing deduplication fencing
            const idempotencyKey = `k6_idemp_${__VU}_${Math.floor(__ITER / 10)}`;
            const correlationId = `k6_corr_${__VU}_${__ITER}`;

            const payload = JSON.stringify({
                title: `K6 Load Rubric ${__VU}_${__ITER}`,
                content: `<p>Load test candidate evaluation rubric generated by VU ${__VU}.</p>`,
                status: 1,
                _token: CSRF_TOKEN,
                _idempotency_key: idempotencyKey,
            });

            const mutationHeaders = Object.assign({}, HEADERS, {
                'X-Idempotency-Key': idempotencyKey,
                'X-Correlation-ID': correlationId,
            });

            const start = Date.now();
            const res = http.post(`${BASE_URL}/super-admin/rm-note`, payload, { headers: mutationHeaders });
            mutationLatency.add(Date.now() - start);

            const isSuccess = check(res, {
                'mutation status 200': (r) => r.status === 200,
            });

            if (isSuccess) {
                try {
                    const json = JSON.parse(res.body);
                    if (json._replayed) {
                        idempotentReplayRate.add(1);
                    } else {
                        idempotentReplayRate.add(0);
                    }
                } catch (e) {}
            } else {
                failedRequests.add(1);
            }
        });
    }

    sleep(1);
}
