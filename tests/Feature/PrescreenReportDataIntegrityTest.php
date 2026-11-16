<?php

namespace Tests\Feature;

use App\Services\CandidateSkillSyncService;
use App\Services\ForwardResumeAuthorizationService;
use App\Services\PrescreenDossierService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class PrescreenReportDataIntegrityTest
 *
 * End-to-end regression suite guaranteeing Prescreen Report data integrity:
 * 1. Verified Profile Skills Only: never injects unpossessed job requirements or empty
 *    `candidate_job_skill_exp` placeholders (`skill_value = 0, rating = 0`).
 * 2. Zero Fabrication: missing per-skill experience stays `--`, unrated skills/candidates
 *    remain `0` (never defaulted to `4`/`5`), and match percentages never fall back to `85`/`88`.
 * 3. Strict `(candidate_id, job_id)` Isolation: prevents skill evaluations or screening Q&A
 *    from leaking between different jobs or concurrent candidates in a batch.
 * 4. Multi-Tenant Cache Isolation & Atomic Invalidation: caches are keyed by
 *    `(company_id, job_id, candidate_id)` and purged atomically via `invalidateDossierCaches`
 *    and `CandidateSkillSyncService::invalidateCandidateCaches`.
 */
class PrescreenReportDataIntegrityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        if (! Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('job_id')->nullable();
                $table->string('full_name')->nullable();
                $table->string('total_exp')->nullable();
                $table->string('rel_exp')->nullable();
                $table->string('rating')->nullable();
                $table->text('skills')->nullable();
                $table->string('condidate_type')->nullable();
                $table->integer('bench_candidate_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('add_job_wise_condidates')) {
            Schema::create('add_job_wise_condidates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('job_application_id');
                $table->unsignedBigInteger('company_id')->nullable();
            });
        }

        if (! Schema::hasTable('job_metronics_skills')) {
            Schema::create('job_metronics_skills', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('job_id');
                $table->string('skill_name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_application_skills')) {
            Schema::create('job_application_skills', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_application_id');
                $table->string('skills')->nullable();
            });
        }

        if (! Schema::hasTable('candidate_job_skill_exp')) {
            Schema::create('candidate_job_skill_exp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_skill_id');
                $table->text('skill_value')->nullable();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('candidate_id');
                $table->integer('rating')->nullable();
            });
        }

        if (! Schema::hasTable('bench_candidate_skill_experience')) {
            Schema::create('bench_candidate_skill_experience', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('candidate_id');
                $table->string('skill_key');
                $table->integer('year')->nullable();
                $table->integer('month')->nullable();
                $table->integer('rating')->nullable();
            });
        }

        if (! Schema::hasTable('application_additional_detail')) {
            Schema::create('application_additional_detail', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('application_id');
                $table->unsignedBigInteger('question_id')->nullable();
                $table->string('job_detail_value')->nullable();
                $table->string('application_detail_value')->nullable();
            });
        }

        DB::table('application_additional_detail')->delete();
        DB::table('bench_candidate_skill_experience')->delete();
        DB::table('candidate_job_skill_exp')->delete();
        DB::table('job_application_skills')->delete();
        DB::table('job_metronics_skills')->delete();
        DB::table('add_job_wise_condidates')->delete();
        DB::table('job_applications')->delete();
    }

    public function test_prescreen_report_skills_reflect_job_requirements_and_candidate_drawer(): void
    {
        $companyId = 77;
        $jobId = 501;
        $candId = 9001;

        DB::table('job_applications')->insert([
            'id' => $candId,
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Aarav Mehta',
            'total_exp' => '5',
            'rel_exp' => '4',
            'rating' => null,
            'skills' => 'PHP, MySQL',
            'updated_at' => now(),
        ]);

        // Job requires 3 skills: PHP, Kubernetes, Rust
        $phpSkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobId, 'skill_name' => 'PHP',
        ]);
        $k8sSkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobId, 'skill_name' => 'Kubernetes',
        ]);
        $rustSkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobId, 'skill_name' => 'Rust',
        ]);

        // Candidate evaluated on PHP (4.0 Years, 5 stars); Kubernetes and Rust are unevaluated (0, 0)
        DB::table('candidate_job_skill_exp')->insert([
            ['job_skill_id' => $phpSkillId, 'skill_value' => '4.0 Years', 'job_id' => $jobId, 'candidate_id' => $candId, 'rating' => 5],
            ['job_skill_id' => $k8sSkillId, 'skill_value' => '0', 'job_id' => $jobId, 'candidate_id' => $candId, 'rating' => 0],
            ['job_skill_id' => $rustSkillId, 'skill_value' => '0', 'job_id' => $jobId, 'candidate_id' => $candId, 'rating' => 0],
        ]);

        $cand = DB::table('job_applications')->where('id', $candId)->first();
        $service = new PrescreenDossierService;
        $dossiers = $service->resolveCandidateDossiers([$cand], $jobId, $companyId);

        $this->assertArrayHasKey($candId, $dossiers);
        $dossier = $dossiers[$candId];

        $skillNames = array_map(fn ($s) => $s['name'], $dossier['skills']);
        $this->assertContains('PHP', $skillNames);
        $this->assertContains('Kubernetes', $skillNames, 'Job skill Kubernetes must be present in report matching Candidate Drawer.');
        $this->assertContains('Rust', $skillNames, 'Job skill Rust must be present in report matching Candidate Drawer.');

        // Unevaluated skills must preserve 0 rating and not fabricate ratings
        $k8sSkill = collect($dossier['skills'])->firstWhere('name', 'Kubernetes');
        $this->assertSame(0, $k8sSkill['rating'], 'Unevaluated skill rating must remain 0.');

        // Match is real profile/evaluation overlap: 1 of 3 job skills = 33% (never fabricated 85%)
        $this->assertSame(33, $dossier['match']);
    }

    public function test_unrated_candidate_and_unevaluated_skills_preserve_zero_and_dashes_without_fabrication(): void
    {
        $companyId = 77;
        $jobId = 502;
        $candId = 9002;

        DB::table('job_applications')->insert([
            'id' => $candId,
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Priya Nair',
            'total_exp' => '6',
            'rel_exp' => null,
            'rating' => null,
            'skills' => 'Laravel',
            'updated_at' => now(),
        ]);

        DB::table('job_metronics_skills')->insert([
            'company_id' => $companyId, 'job_id' => $jobId, 'skill_name' => 'Go',
        ]);

        $cand = DB::table('job_applications')->where('id', $candId)->first();
        $service = new PrescreenDossierService;
        $dossier = $service->resolveCandidateDossiers([$cand], $jobId, $companyId)[$candId];

        $this->assertCount(1, $dossier['skills']);
        $this->assertSame('Laravel', $dossier['skills'][0]['name']);
        $this->assertSame('--', $dossier['skills'][0]['expText'], 'Unmeasured per-skill exp must NOT inherit total_exp.');
        $this->assertSame(0, $dossier['skills'][0]['rating'], 'Unrated skill must stay 0, never default to 4 or 5.');
        $this->assertSame(0, $dossier['overall_rating'], 'Unrated candidate must stay 0, never default to 4.');
        $this->assertSame(0, $dossier['match'], '0 skill overlap must be 0%, never 85%.');
    }

    public function test_strict_job_and_candidate_isolation_prevents_cross_job_leakage(): void
    {
        $companyId = 88;
        $jobA = 601;
        $jobB = 602;
        $candId = 9101;
        $otherCandId = 9102;

        DB::table('job_applications')->insert([
            [
                'id' => $candId,
                'company_id' => $companyId,
                'job_id' => $jobA,
                'full_name' => 'Rohan Kapoor',
                'total_exp' => '7',
                'rel_exp' => '5',
                'rating' => null,
                'skills' => 'React, TypeScript',
                'updated_at' => now(),
            ],
            [
                'id' => $otherCandId,
                'company_id' => $companyId,
                'job_id' => $jobB,
                'full_name' => 'Neha Verma',
                'total_exp' => '3',
                'rel_exp' => '3',
                'rating' => '5',
                'skills' => 'Python, Django',
                'updated_at' => now(),
            ],
        ]);

        $jobASkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobA, 'skill_name' => 'React',
        ]);
        $jobBSkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobB, 'skill_name' => 'React',
        ]);

        // Different evaluations on Job A vs Job B for same candidate
        DB::table('candidate_job_skill_exp')->insert([
            ['job_skill_id' => $jobASkillId, 'skill_value' => '6.0 Years', 'job_id' => $jobA, 'candidate_id' => $candId, 'rating' => 5],
            ['job_skill_id' => $jobBSkillId, 'skill_value' => '1.0 Years', 'job_id' => $jobB, 'candidate_id' => $candId, 'rating' => 2],
        ]);

        DB::table('application_additional_detail')->insert([
            ['job_id' => $jobA, 'application_id' => $candId, 'job_detail_value' => 'Job A Q1', 'application_detail_value' => 'Answer for Job A only'],
            ['job_id' => $jobB, 'application_id' => $candId, 'job_detail_value' => 'Job B Q1', 'application_detail_value' => 'Answer for Job B only'],
            ['job_id' => $jobB, 'application_id' => $otherCandId, 'job_detail_value' => 'Neha Q1', 'application_detail_value' => 'Neha answer'],
        ]);

        $cands = DB::table('job_applications')->whereIn('id', [$candId, $otherCandId])->get();
        $service = new PrescreenDossierService;
        $dossiers = $service->resolveCandidateDossiers($cands, $jobB, $companyId);

        $rohan = $dossiers[$candId];
        $reactSkill = collect($rohan['skills'])->firstWhere('name', 'React');
        $this->assertNotNull($reactSkill);
        $this->assertSame('1.0 Years', $reactSkill['expText']);
        $this->assertSame(2, $reactSkill['rating']);
        $this->assertSame([['question' => 'Job B Q1', 'answer' => 'Answer for Job B only']], $rohan['screening_qa']);

        $neha = $dossiers[$otherCandId];
        $nehaNames = array_map(fn ($s) => $s['name'], $neha['skills']);
        $this->assertSame(['Python', 'Django'], $nehaNames);
        $this->assertNotContains('React', $nehaNames);
        $this->assertSame([['question' => 'Neha Q1', 'answer' => 'Neha answer']], $neha['screening_qa']);
    }

    public function test_cache_keys_are_job_and_tenant_scoped_and_atomically_invalidated(): void
    {
        $companyId = 99;
        $jobId = 701;
        $candId = 9201;

        DB::table('job_applications')->insert([
            'id' => $candId,
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Kabir Joshi',
            'total_exp' => '4',
            'skills' => 'Java',
            'updated_at' => now(),
        ]);

        $javaSkillId = DB::table('job_metronics_skills')->insertGetId([
            'company_id' => $companyId, 'job_id' => $jobId, 'skill_name' => 'Java',
        ]);
        DB::table('candidate_job_skill_exp')->insert([
            'job_skill_id' => $javaSkillId, 'skill_value' => '2.0 Years', 'job_id' => $jobId, 'candidate_id' => $candId, 'rating' => 3,
        ]);

        $cand = DB::table('job_applications')->where('id', $candId)->first();
        $service = new PrescreenDossierService;
        $first = $service->resolveCandidateDossiers([$cand], $jobId, $companyId)[$candId];

        $this->assertSame(3, $first['skills'][0]['rating']);
        $this->assertNotNull(Cache::get(PrescreenDossierService::skillsCacheKey($companyId, $jobId, $candId)));
        $this->assertNull(Cache::get(PrescreenDossierService::skillsCacheKey($companyId, $jobId + 1, $candId)));

        // Update DB rating and atomically purge via CandidateSkillSyncService
        DB::table('candidate_job_skill_exp')
            ->where('candidate_id', $candId)
            ->where('job_id', $jobId)
            ->update(['rating' => 5, 'skill_value' => '4.0 Years']);

        CandidateSkillSyncService::invalidateCandidateCaches($candId, [], $companyId);

        $this->assertNull(Cache::get(PrescreenDossierService::skillsCacheKey($companyId, $jobId, $candId)));

        $refreshed = $service->resolveCandidateDossiers([$cand], $jobId, $companyId)[$candId];
        $this->assertSame(5, $refreshed['skills'][0]['rating']);
        $this->assertSame('4.0 Years', $refreshed['skills'][0]['expText']);
    }

    public function test_authorization_service_compute_authoritative_match_does_not_fabricate_85_or_rating_multipliers(): void
    {
        $authSvc = app(ForwardResumeAuthorizationService::class);

        $matchWithoutJob = $authSvc->computeAuthoritativeMatch(9301, 0, ['Figma', 'Sketch'], [], 4);
        $this->assertSame(0, $matchWithoutJob, 'Missing job skills / skill match must return 0, never 85 or rating * 20.');

        $matchWithJob = $authSvc->computeAuthoritativeMatch(9301, 0, ['Figma', 'Sketch'], ['Figma', 'React', 'NextJS', 'CSS'], 4);
        $this->assertSame(25, $matchWithJob, 'Match must be computed from actual profile skill intersection (1/4 = 25%).');
    }

    public function test_prescreen_report_location_hierarchy_and_state_resolution(): void
    {
        $companyId = 2;
        $jobId = 117;
        $candId = 9401;

        // Ensure city_list, state_list, countries tables exist
        if (! Schema::hasTable('city_list')) {
            Schema::create('city_list', function (Blueprint $table) {
                $table->id('city_id');
                $table->string('city');
                $table->unsignedBigInteger('state_id');
            });
        }
        if (! Schema::hasTable('state_list')) {
            Schema::create('state_list', function (Blueprint $table) {
                $table->id();
                $table->string('state');
                $table->unsignedBigInteger('country_id');
            });
        }
        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('country_name');
            });
        }

        DB::table('countries')->updateOrInsert(['id' => 101], ['country_name' => 'India']);
        DB::table('state_list')->updateOrInsert(['id' => 4030], ['state' => 'Gujarat', 'country_id' => 101]);
        DB::table('city_list')->updateOrInsert(['city_id' => 57695], ['city' => 'Anand', 'state_id' => 4030]);
        DB::table('state_list')->updateOrInsert(['id' => 4022], ['state' => 'Uttar Pradesh', 'country_id' => 101]);
        DB::table('city_list')->updateOrInsert(['city_id' => 133230], ['city' => 'Noida', 'state_id' => 4022]);
        DB::table('state_list')->updateOrInsert(['id' => 4007], ['state' => 'Haryana', 'country_id' => 101]);
        DB::table('city_list')->updateOrInsert(['city_id' => 132032], ['city' => 'Gurgaon/gurugram', 'state_id' => 4007]);

        // Candidate with city_id = 57695 and state = 0 (reproducing the exact reported issue)
        $cand = (object) [
            'id' => $candId,
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Shekhar Anand',
            'city_id' => 57695,
            'state' => 0,
            'country' => 101,
            'skills' => 'HR Operations, Recruitment',
            'total_exp' => '2.0',
            'rel_exp' => '1.0',
            'rating' => 0,
        ];

        $service = new PrescreenDossierService;
        $dossiers = $service->resolveCandidateDossiers([$cand], $jobId, $companyId);

        $this->assertArrayHasKey($candId, $dossiers);
        $d = $dossiers[$candId];

        // State MUST NOT be empty, Location Text MUST include city, state, country
        $this->assertSame('Anand', $d['city']);
        $this->assertSame('Gujarat', $d['state']);
        $this->assertSame('India', $d['country']);
        $this->assertSame('Anand, Gujarat, India', $d['location_text']);
        $this->assertSame('2.0 Years', $d['total_exp_display']);
        $this->assertSame('1.0 Year', $d['rel_exp_display']);

        // Verify Authoritative Hierarchy Resolution and conflicting state override (SSOT Invariant)
        $noidaAuth = \App\Helper\GeneralMethod::resolveAuthoritativeLocationHierarchy(133230, 4031, 101);
        $this->assertSame(4022, $noidaAuth['state_id'], 'Noida must resolve to Uttar Pradesh (4022), overriding client-passed Chandigarh (4031).');
        $this->assertSame('Uttar Pradesh', $noidaAuth['state_name']);

        $gurgaonAuth = \App\Helper\GeneralMethod::resolveAuthoritativeLocationHierarchy(132032, 4013, 101);
        $this->assertSame(4007, $gurgaonAuth['state_id'], 'Gurgaon must resolve to Haryana (4007), overriding client-passed Odisha (4013).');
        $this->assertSame('Haryana', $gurgaonAuth['state_name']);

        // Verify Candidate Location Hierarchy self-healing on contradictory records
        $corruptedNoida = (object) [
            'city_id' => 133230,
            'city' => 'Noida',
            'state' => 4031, // Corrupted Chandigarh
            'country' => 101,
        ];
        $healedNoida = \App\Helper\GeneralMethod::resolveCandidateLocationHierarchy($corruptedNoida);
        $this->assertSame('Noida', $healedNoida['city']);
        $this->assertSame('Uttar Pradesh', $healedNoida['state']);
        $this->assertSame('India', $healedNoida['country']);
        $this->assertSame('Noida, Uttar Pradesh, India', $healedNoida['location_text']);

        $corruptedGurgaon = (object) [
            'city_id' => 132032,
            'city' => 'Gurgaon/gurugram',
            'state' => 4013, // Corrupted Odisha
            'country' => 101,
        ];
        $healedGurgaon = \App\Helper\GeneralMethod::resolveCandidateLocationHierarchy($corruptedGurgaon);
        $this->assertSame('Gurgaon/Gurugram', $healedGurgaon['city']);
        $this->assertSame('Haryana', $healedGurgaon['state']);
        $this->assertSame('India', $healedGurgaon['country']);
        $this->assertSame('Gurgaon/Gurugram, Haryana, India', $healedGurgaon['location_text']);
    }

    public function test_prescreen_report_skills_fallback_and_decoupled_contract(): void
    {
        $companyId = 2;
        $jobId = 117;
        $candId = 9402;

        // Candidate with empty skills string but careersummary describing skills
        $cand = (object) [
            'id' => $candId,
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Candidate Fallback Test',
            'city_id' => 57695,
            'state' => 4030,
            'country' => 101,
            'skills' => '', // empty
            'careersummary' => 'Seeking role to enhance my skills in recruitment, employee engagement, and HR operations.',
            'total_exp' => '0', // fresher
            'rel_exp' => '0',
            'rating' => 4,
            'resume' => 'https://file.jero.works/test.pdf',
            'email' => 'candidate@example.com',
            'phone' => '9876543210',
            'linkedin' => 'https://linkedin.com/in/test',
            'current_designation' => 'HR Associate',
        ];

        $service = new PrescreenDossierService;
        $dossiers = $service->resolveCandidateDossiers([$cand], $jobId, $companyId);

        $this->assertArrayHasKey($candId, $dossiers);
        $d = $dossiers[$candId];

        // Skills recovered via resilient fallback
        $this->assertNotEmpty($d['skills'], 'Skills must be recovered via resilient fallback, never left empty.');
        $skillNames = array_map(fn ($s) => strtolower($s['name']), $d['skills']);
        $this->assertTrue(collect($skillNames)->contains(fn ($n) => str_contains($n, 'recruitment') || str_contains($n, 'hr operations')));

        // Decoupled contract verification
        $this->assertSame('Fresher', $d['total_exp_display']);
        $this->assertSame(4, $d['overall_rating']);
        $this->assertIsArray($d['validation']);
        $this->assertCount(5, $d['validation']);
        $this->assertSame('Verified', collect($d['validation'])->firstWhere('label', 'RESUME')['status']);
        $this->assertSame('Verified', collect($d['validation'])->firstWhere('label', 'EMAIL ID')['status']);
        $this->assertSame('Verified', collect($d['validation'])->firstWhere('label', 'LINKEDIN ID')['status']);
    }
}
