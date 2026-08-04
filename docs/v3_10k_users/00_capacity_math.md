# 00 — Capacity Math

> **Intent:** Derive the exact per-second numbers for every target before any
> architectural decision is made. Engineers who skip this step over-provision or
> under-provision. Both are expensive.

---

## The 3 Targets — Decomposed

### Target 1: 1,000,000 Candidate Uploads Per Day

```
1,000,000 / 86,400 seconds = 11.6 uploads/second (24h average)

Assume: 8-hour active workday (recruiters work 9am–5pm)
1,000,000 / 28,800 seconds = 34.7 uploads/second (workday average)

Assume: peak hour = 3× workday average
34.7 × 3 = ~105 uploads/second (peak)
```

At V2 (browser → R2 direct PUT, PHP sees zero bytes):

| Operation | PHP time per upload | Concurrent PHP requests at 105/s peak |
|---|---|---|
| POST /resume-sessions (presign) | ~150 ms | 105 × 0.15 = **~16 concurrent** |
| POST /job-applications (submit) | ~300 ms | 105 × 0.30 = **~32 concurrent** |
| **Total peak PHP concurrency** | — | **~48 concurrent requests** |

> **48 concurrent PHP requests** handles 1M uploads/day at V2. Not 10,000.

---

### Target 2: 10,000 Concurrent Users

"Concurrent users" means 10,000 browser sessions open simultaneously.
They are NOT all hitting PHP at the same time.

A recruiter's session timeline:

```
[0ms]     picks file → browser parser runs (pdf.js + re_test()) — NO PHP
[200ms]   POST /resume-sessions (presign) — PHP: 150ms
[350ms]   browser starts PUT to R2 — NO PHP
[8,350ms] PUT complete → POST /resume-sessions/{uuid}/finalize — PHP: 150ms
[8,500ms] recruiter fills form — NO PHP (60–120 seconds)
[~90s]    POST /job-applications (submit) — PHP: 300ms
[~90.3s]  done

Total session: ~90 seconds
PHP involvement: 150 + 150 + 300 = 600ms
PHP % of session: 600 / 90,000 = 0.67%
```

At 10,000 concurrent sessions:
```
Average concurrent PHP requests: 10,000 × 0.67% = 67
Burst concurrent PHP requests:   10,000 × 2%    = 200
Worst-case burst (everyone submits together): 10,000 × (300ms/5s) = 600
```

> The number you need to design for is **67–200 concurrent PHP requests**, not 10,000.
> The 10,000 are mostly idle (filling forms, waiting on R2).

---

### Target 3: 1,000 Job Updates Per Day

```
1,000 / 86,400 = 0.012 updates/second
Peak: 0.012 × 3 = 0.036 updates/second

MySQL write: < 1 ms
ES index: < 5 ms
```

> This is completely trivial. A single MySQL thread handles this with 99.99% idle time.
> No architectural work needed specifically for job updates.

---

## The Numbers That Matter — Summary

| Metric | Value | Design for |
|---|---|---|
| PHP concurrent requests — 1M/day average | 48 | 60 Octane workers sufficient |
| PHP concurrent requests — 10K concurrent average | 67 | 80 Octane workers sufficient |
| PHP concurrent requests — 10K concurrent burst | 200 | 220 Octane workers needed |
| PHP concurrent requests — worst-case spike | 600 | Queueing + rate limiting |
| MySQL writes/second peak | 105/s | MySQL handles 10,000+/s — trivial |
| ES index ops/second peak | 105/s | ES handles 10,000+/s — trivial |
| Redis ops/second peak | ~1,050/s | Redis handles 100,000+/s — trivial |
| R2 bandwidth peak | 630 MB/s | Cloudflare managed — zero server cost |
| PHP bandwidth | ~7 KB/s | Completely trivial |

---

## What These Numbers Tell You

1. **Bandwidth is not your problem.** V2 routes all file bytes to R2 directly.
2. **MySQL and ES are not your problem.** They are both at <1% capacity.
3. **Redis is not your problem** (throughput). It is a reliability problem (single point of failure).
4. **The app server IS your problem** — specifically, Octane worker count × RAM per worker.
5. **The god controllers are the root cause** — they inflate RAM per worker from 128 MB to 512 MB, reducing worker count by 4×.

---

## V1 vs V2 vs V3 — Request Rate Comparison

| Version | PHP req/s at 10K concurrent | Bytes through PHP/s | Realistic ceiling |
|---|---|---|---|
| V1 (current) | 50,000 req/s | 60 GB/s | ~500 concurrent (collapses) |
| V2 (planned) | ~6,000 req/s metadata | 0 bytes | 10,000 concurrent ✅ |
| V2.5 (planned) | ~2,000 req/s | 0 bytes | 30,000+ concurrent |
| V3 (Cloudflare Worker presign) | ~100 req/s | 0 bytes | 100,000+ concurrent |

> Source: [`../future_resume_upload_2.0/95_god_mode_spec.md`](../future_resume_upload_2.0/95_god_mode_spec.md)
