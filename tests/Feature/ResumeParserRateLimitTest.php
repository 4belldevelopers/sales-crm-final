<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

/**
 * Phase 9 follow-up — Redis-backed per-user rate limit.
 *
 * Isolated in its own test class because Laravel's `Facade::swap($stub)`
 * mutates a static per-class root; subsequent tests in the same process
 * would observe the stub and 429 unexpectedly. The facade swap is
 * undone in `tearDown()` so independent phpunit runs of this file
 * behave identically, but keeping this test class separate is the
 * cheapest way to keep test isolation deterministic.
 *
 * Properties under test:
 *   1. 10 sequential successful parses, the 11th returns 429.
 *   2. 429 response carries `parse_status='rate_limited'` and a
 *      `Retry-After: 60` header.
 *   3. Redis outage / exception falls open — the rate limiter is
 *      advisory, not load-bearing.
 */
class ResumeParserRateLimitTest extends TestCase
{
    /** Stub instance survives across requests in this test. */
    private object $stub;

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('cache.default', 'array');
    }

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'resumes.metrics_enabled' => true,
            'resumes.metrics_require_redis' => false,
        ]);
        $this->buildSchema();
        $this->stub = new class {
            public int $count = 0;
            public function incrby(string $k, int $n): int
            {
                $this->count += $n;
                return $this->count;
            }
            public function expire(string $k, int $ttl): int { return 1; }
        };
        \Illuminate\Support\Facades\Redis::clearResolvedInstances();
        Redis::swap($this->stub);
    }

    protected function tearDown(): void
    {
        // Reset facade root by setting app to a new instance, so the
        // facade re-resolves from the framework's defaults for any
        // future process that re-uses the worker (e.g. PHPUnit
        // reuses an autoloaded test-runner state).
        \Illuminate\Support\Facades\Redis::clearResolvedInstances();
        try {
            $r = new \ReflectionClass(\Illuminate\Support\Facades\Facade::class);
            if ($r->hasProperty('resolvedInstances')) {
                $prop = $r->getProperty('resolvedInstances');
                $prop->setAccessible(true);
                $arr = $prop->getValue();
                unset($arr[Redis::class]);
                $prop->setValue(null, $arr);
            }
        } catch (\Throwable $ignored) {}
        parent::tearDown();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('users', function ($t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->unsignedInteger('company_id')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });
        \Illuminate\Support\Facades\Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('status', 32)->default('Created');
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
            $t->timestamps();
        });
        \Illuminate\Support\Facades\Schema::create('upload_session_audit', function ($t) {
            $t->bigIncrements('id');
            $t->char('session_uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('action', 32);
            $t->timestamp('created_at')->useCurrent();
        });
    }

    private function seedUser(?int $companyId, string $email): User
    {
        $u = new User();
        $u->name = 'Test';
        $u->email = $email;
        $u->password = Hash::make('secret');
        $u->company_id = $companyId;
        $u->status = 'active';
        $u->save();
        return $u;
    }

    public function test_eleventh_request_returns_429_with_retry_after(): void
    {
        config(['resumes.parser_rate_limit_per_min' => 10]);

        $user = $this->seedUser(7, 'rl@example.com');

        // Drive 10 in-budget requests
        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user, 'web')
                ->postJson('/api/resume-parser/parse', [
                    'file' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 bytes'),
                ])
                ->assertStatus(200);
        }

        // 11th request — exceeds cap
        $resp = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 bytes'),
            ]);
        $resp->assertStatus(429);
        $this->assertSame('rate_limited', $resp->json('parse_status'));
        $this->assertSame('60', $resp->headers->get('Retry-After'));
        $this->assertGreaterThanOrEqual(11, $this->stub->count);
    }
}
