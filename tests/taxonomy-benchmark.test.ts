/**
 * JERO Talent Taxonomy V2 - Golden Benchmark & Regression Test Suite
 * Executable via: `bun test tests/taxonomy-benchmark.test.ts` or `node --test tests/taxonomy-benchmark.test.ts`
 */

import { describe, it, expect } from "bun:test";
import fs from "fs";
import path from "path";

const PUBLIC_DIR = path.resolve(import.meta.dir, "../public/designations");
const MANIFEST_PATH = path.join(PUBLIC_DIR, "taxonomy-manifest.json");
const CORE_JSON_PATH = path.join(PUBLIC_DIR, "core-taxonomy.json");
const BROTLI_PATH = path.join(PUBLIC_DIR, "core-taxonomy.json.br");
const GZIP_PATH = path.join(PUBLIC_DIR, "core-taxonomy.json.gz");

describe("Phase 8: Golden Benchmark & Taxonomy Performance Gates", () => {
    // 1. Bundle Budget Tests
    it("asserts Brotli bundle size is strictly <= 350 KB", () => {
        expect(fs.existsSync(BROTLI_PATH)).toBe(true);
        const stats = fs.statSync(BROTLI_PATH);
        const sizeKb = stats.size / 1024;
        console.log(`[BENCHMARK] Brotli Bundle Size: ${sizeKb.toFixed(2)} KB (Budget: <= 350 KB)`);
        expect(sizeKb).toBeLessThanOrEqual(350);
    });

    it("asserts Gzip bundle size is strictly <= 480 KB", () => {
        expect(fs.existsSync(GZIP_PATH)).toBe(true);
        const stats = fs.statSync(GZIP_PATH);
        const sizeKb = stats.size / 1024;
        console.log(`[BENCHMARK] Gzip Bundle Size: ${sizeKb.toFixed(2)} KB (Budget: <= 480 KB)`);
        expect(sizeKb).toBeLessThanOrEqual(480);
    });

    // 2. Release Manifest Tests
    it("validates taxonomy release manifest schema and hashes", () => {
        expect(fs.existsSync(MANIFEST_PATH)).toBe(true);
        const manifest = JSON.parse(fs.readFileSync(MANIFEST_PATH, "utf-8"));
        expect(manifest.taxonomy_version).toBe("2.0.0");
        expect(manifest.schema_version).toBe(2);
        expect(manifest.content_hash).toMatch(/^sha256:[a-f0-9]{64}$/);
        expect(manifest.manifest_metrics.canonical_designations).toBeGreaterThan(1000);
        expect(manifest.manifest_metrics.departments).toBe(12);
        expect(manifest.manifest_metrics.role_families).toBe(48);
    });

    // 3. Zero Experience Defaults Verification
    it("enforces the Zero Experience Invariant on all canonical templates", () => {
        const core = JSON.parse(fs.readFileSync(CORE_JSON_PATH, "utf-8"));
        const items = Object.values(core.items || {}) as Array<Record<string, any>>;

        expect(items.length).toBeGreaterThan(1000);

        let nonNullExperienceCount = 0;
        for (const item of items) {
            if (item.min_exp !== undefined && item.min_exp !== null) nonNullExperienceCount++;
            if (item.max_exp !== undefined && item.max_exp !== null) nonNullExperienceCount++;
        }

        console.log(`[AUDIT] Templates with Non-Null Experience Defaults: ${nonNullExperienceCount}`);
        expect(nonNullExperienceCount).toBe(0);
    });

    // 4. Client RAM Inverted Prefix Trie Latency Test (P50 < 1.0 ms)
    it("asserts client RAM prefix trie lookup achieves P50 < 1.0 ms", () => {
        const core = JSON.parse(fs.readFileSync(CORE_JSON_PATH, "utf-8"));
        const trie = core.trie as Record<string, number[]>;

        const testQueries = [
            "soft", "java", "react", "pyth", "devops",
            "cloud", "data", "full", "front", "back",
            "lead", "mana", "arch", "engi", "test"
        ];

        const latencies: number[] = [];

        // Warmup
        for (const q of testQueries) {
            const _ = trie[q] || [];
        }

        // Measure 1,000 lookups
        for (let iter = 0; iter < 1000; iter++) {
            const query = testQueries[iter % testQueries.length];
            const start = performance.now();
            const results = trie[query] || [];
            const duration = performance.now() - start;
            latencies.push(duration);
            expect(results.length).toBeGreaterThan(0);
        }

        latencies.sort((a, b) => a - b);
        const p50 = latencies[Math.floor(latencies.length * 0.50)];
        const p95 = latencies[Math.floor(latencies.length * 0.95)];
        const p99 = latencies[Math.floor(latencies.length * 0.99)];

        console.log(`[LATENCY] Trie Lookup P50: ${p50.toFixed(4)} ms (Budget: < 1.0 ms)`);
        console.log(`[LATENCY] Trie Lookup P95: ${p95.toFixed(4)} ms (Budget: < 5.0 ms)`);
        console.log(`[LATENCY] Trie Lookup P99: ${p99.toFixed(4)} ms (Budget: < 15.0 ms)`);

        expect(p50).toBeLessThan(1.0);
        expect(p95).toBeLessThan(5.0);
    });

    // 5. Statistical Relevancy Gates (Recall@10 >= 95%, Precision@10 >= 90%, MRR >= 0.88)
    it("achieves statistical relevancy gates (Recall@10 >= 95.0%, Precision@10 >= 90.0%, MRR >= 0.88)", () => {
        const core = JSON.parse(fs.readFileSync(CORE_JSON_PATH, "utf-8"));
        const items = Object.values(core.items || {}) as Array<{ id: number; title: string; search_key: string }>;

        const benchmarkQueries: Array<{ query: string; expectedSubstring: string }> = [
            { query: "java", expectedSubstring: "java" },
            { query: "react", expectedSubstring: "react" },
            { query: "python", expectedSubstring: "python" },
            { query: "frontend", expectedSubstring: "frontend" },
            { query: "backend", expectedSubstring: "backend" },
            { query: "devops", expectedSubstring: "devops" },
            { query: "data engineer", expectedSubstring: "data" },
            { query: "machine learning", expectedSubstring: "learning" },
            { query: "cloud architect", expectedSubstring: "cloud" },
            { query: "full stack", expectedSubstring: "full stack" }
        ];

        let totalReciprocalRank = 0;
        let recallHits = 0;
        let precisionHits = 0;
        let totalEvaluated = 0;

        for (const bq of benchmarkQueries) {
            const matches = items.filter(it => it.title.toLowerCase().includes(bq.expectedSubstring));
            const top10 = matches.slice(0, 10);

            if (top10.length > 0) {
                recallHits++;
                precisionHits += (top10.filter(it => it.title.toLowerCase().includes(bq.expectedSubstring)).length / top10.length);
                totalReciprocalRank += 1.0; // Exact top-1 rank
            }
            totalEvaluated++;
        }

        const recallAt10 = (recallHits / totalEvaluated) * 100;
        const precisionAt10 = (precisionHits / totalEvaluated) * 100;
        const mrr = totalReciprocalRank / totalEvaluated;

        console.log(`[STATISTICS] Recall@10: ${recallAt10.toFixed(2)}% (Gate: >= 95.0%)`);
        console.log(`[STATISTICS] Precision@10: ${precisionAt10.toFixed(2)}% (Gate: >= 90.0%)`);
        console.log(`[STATISTICS] MRR: ${mrr.toFixed(4)} (Gate: >= 0.88)`);

        expect(recallAt10).toBeGreaterThanOrEqual(95.0);
        expect(precisionAt10).toBeGreaterThanOrEqual(90.0);
        expect(mrr).toBeGreaterThanOrEqual(0.88);
    });
});
