<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmOfferingTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $suffix): array
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'CRM Offering Corp '.$suffix,
            'company_email' => 'crm_'.$suffix.'@example.test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'company_id' => $companyId,
            'name' => 'CRM User '.$suffix,
            'email' => 'crm_user_'.$suffix.'@example.test',
            'password' => bcrypt('Secret123!'),
            'type' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::findOrFail($userId);
        $role = DB::table('roles')->first();
        if ($role) {
            DB::table('role_user')->insertOrIgnore(['user_id' => $userId, 'role_id' => $role->id]);
            $user->assignRole($role->name);
        }

        return [$companyId, $user->fresh()];
    }

    public function test_crm_offering_product_service_wizard_and_isolation(): void
    {
        $this->withoutMiddleware(EnsureOneDeviceLogin::class);
        $_SERVER['REQUEST_URI'] = '/service_company/crm/products-services/create';

        [$companyA, $userA] = $this->makeUser('a_'.uniqid());
        [$companyB, $userB] = $this->makeUser('b_'.uniqid());

        // 1. Render 4-step wizard (both crm.offerings.create and jobs.create)
        $viewRes = $this->actingAs($userA)->get(route('service_company.crm.offerings.create'));
<<<<<<< HEAD
        $viewRes->assertStatus(200)->assertSee('Add Service')->assertSee('Service Name')->assertSee('Service Code / SKU');

        $jobsCreateRes = $this->actingAs($userA)->get(route('service_company.jobs.create'));
        $jobsCreateRes->assertStatus(200)->assertSee('Add Service')->assertSee('Service Name')->assertDontSee('Add Internal Jobs');
=======
        $viewRes->assertStatus(200)->assertSee('Add Product / Service')->assertSee('Product / Service Name')->assertSee('Product / Service SKU / Code');

        $jobsCreateRes = $this->actingAs($userA)->get(route('service_company.jobs.create'));
        $jobsCreateRes->assertStatus(200)->assertSee('Add Product / Service')->assertSee('Product / Service Name')->assertDontSee('Add Internal Jobs');
>>>>>>> 65b69b922441b3d50bd5c3c027c97c0a57e42a9b

        // 2. Create Product with Fixed Price (auto-locks frequency to One-Time) + idempotency key
        $opId = Str::uuid()->toString();
        $res = $this->actingAs($userA)->postJson(route('service_company.crm.offerings.store'), [
            'operation_id' => $opId,
            'offering_class' => 'product',
            'offering_type' => 'Software / SaaS',
            'name' => 'JeroCRM Enterprise',
            'product_url' => 'https://jero.works/crm-enterprise',
            'crm_category' => 'Software / SaaS',
            'available_capacity' => 50,
            'status' => 'active',
            'pricing_visibility' => 'public_catalog',
            'min_price' => 499.99,
            'max_price' => 999.99,
            'currency_code' => 'USD',
            'pricing_model' => 'Fixed Price',
            'pricing_frequency' => 'Monthly', // should be locked to One-Time by server
            'delivery_model' => 'Cloud-based',
            'service_availability' => '24 x 7',
            'sku' => 'JERO-ENT-01',
            'description' => '<p>Full CRM suite</p>',
            'locations' => ['Global / Worldwide'],
            'feature_tags' => ['API Integration', 'SSO / SAML'],
            'qualification_questions' => [['question' => 'Target budget?', 'answer_type' => 'short']],
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('offering.pricing_frequency', 'One-Time')
            ->assertJsonPath('offering.product_url', 'https://jero.works/crm-enterprise')
            ->assertJsonPath('offering.sku', 'JERO-ENT-01');

<<<<<<< HEAD
        $this->assertDatabaseHas('jobs', [
            'company_id' => $companyA,
            'title' => 'JeroCRM Enterprise',
        ]);

        $createdJobId = (int) $res->json('job_id');
        if ($createdJobId > 0) {
            $editView = $this->actingAs($userA)->get(route('service_company.jobs.edit', $createdJobId));
            $editView->assertStatus(200)->assertSee('Edit Service')->assertSee('JeroCRM Enterprise');

            $updateJobRes = $this->actingAs($userA)->postJson(route('service_company.crm.offerings.updateJob', $createdJobId), [
                'offering_class' => 'service',
                'offering_type' => 'Professional Service',
                'name' => 'JeroCRM Enterprise Updated',
                'sku' => 'JERO-ENT-01',
                'crm_category' => 'Software / SaaS',
                'available_capacity' => 60,
                'status' => 'active',
                'pricing_visibility' => 'public_catalog',
                'min_price' => 599,
                'currency_code' => 'USD',
                'pricing_model' => 'Fixed Price',
                'delivery_model' => 'Remote',
                'service_availability' => '24 x 7',
                'description' => '<p>Updated service</p>',
            ]);
            $updateJobRes->assertStatus(200)->assertJsonPath('status', 'success');
            $this->assertDatabaseHas('jobs', [
                'id' => $createdJobId,
                'title' => 'JeroCRM Enterprise Updated',
            ]);
        }

=======
>>>>>>> 65b69b922441b3d50bd5c3c027c97c0a57e42a9b
        $offeringId = (int) $res->json('offering.id');

        // 3. Idempotent replay with same operation_id returns same record
        $replay = $this->actingAs($userA)->postJson(route('service_company.crm.offerings.store'), [
            'operation_id' => $opId,
            'offering_class' => 'product',
            'offering_type' => 'Software / SaaS',
            'name' => 'JeroCRM Enterprise',
            'product_url' => 'https://jero.works/crm-enterprise',
            'crm_category' => 'Software / SaaS',
            'available_capacity' => 50,
            'status' => 'active',
            'pricing_visibility' => 'public_catalog',
            'min_price' => 499.99,
            'currency_code' => 'USD',
            'pricing_model' => 'Fixed Price',
            'delivery_model' => 'Cloud-based',
            'service_availability' => '24 x 7',
            'sku' => 'JERO-ENT-01',
            'description' => '<p>Full CRM suite</p>',
        ]);
        $replay->assertStatus(201)->assertJsonPath('offering.id', $offeringId);

        // 4. Create Service with Confidential pricing bypass & Unlimited capacity
        $svcRes = $this->actingAs($userA)->postJson(route('service_company.crm.offerings.store'), [
            'offering_class' => 'service',
            'offering_type' => 'Managed Service',
            'name' => '24/7 Cloud Operations',
            'crm_category' => 'IT & Cloud',
            'is_unlimited_capacity' => true,
            'status' => 'active',
            'pricing_visibility' => 'confidential',
            'currency_code' => 'USD',
            'pricing_model' => 'Subscription',
            'pricing_frequency' => 'Monthly',
            'delivery_model' => 'Remote',
            'service_availability' => '24 x 7',
            'service_duration' => '12 Months',
            'sla_details' => '99.99% Uptime',
            'description' => '<p>Managed cloud ops</p>',
        ]);
        $svcRes->assertStatus(201);
        $this->assertEquals(0.0, (float) $svcRes->json('offering.min_price'));
        $this->assertTrue((bool) $svcRes->json('offering.is_unlimited_capacity'));

        // 5. Cross-tenant isolation: Company B cannot update Company A's offering
        $crossEdit = $this->actingAs($userB)->postJson(route('service_company.crm.offerings.postUpdate', $offeringId), [
            'offering_class' => 'product',
            'offering_type' => 'Software / SaaS',
            'name' => 'Unauthorized',
            'crm_category' => 'Software / SaaS',
            'available_capacity' => 10,
            'status' => 'active',
            'pricing_visibility' => 'public_catalog',
            'min_price' => 100,
            'currency_code' => 'USD',
            'pricing_model' => 'Fixed Price',
            'delivery_model' => 'Remote',
            'service_availability' => 'Business Hours',
            'sku' => 'COMP-B-SKU',
            'description' => '<p>Test</p>',
        ]);
        $crossEdit->assertStatus(404);

        // 6. Edit view renders hydrated offering + Update saves service scope_of_work & deliverables
        $svcId = (int) $svcRes->json('offering.id');
        $editView = $this->actingAs($userA)->get(route('service_company.crm.offerings.edit', $svcId));
        $editView->assertStatus(200)
            ->assertSee('Edit Product / Service')
            ->assertSee('24/7 Cloud Operations');

        $updRes = $this->actingAs($userA)->postJson(route('service_company.crm.offerings.postUpdate', $svcId), [
            'offering_class' => 'service',
            'offering_type' => 'Managed Service',
            'name' => '24/7 Cloud Operations Pro',
            'crm_category' => 'IT & Cloud',
            'is_unlimited_capacity' => true,
            'status' => 'active',
            'pricing_visibility' => 'confidential',
            'currency_code' => 'USD',
            'pricing_model' => 'Subscription',
            'pricing_frequency' => 'Monthly',
            'delivery_model' => 'Remote',
            'service_availability' => '24 x 7',
            'scope_of_work' => 'Full Kubernetes & DB monitoring',
            'deliverables' => 'Monthly SLA & incident report',
            'description' => '<p>Updated managed cloud ops</p>',
            'qualification_questions' => [
                ['question' => 'Do you need SOC2?', 'answer_type' => 'yesno', 'is_required' => true],
            ],
        ]);
        $updRes->assertStatus(200)
            ->assertJsonPath('offering.scope_of_work', 'Full Kubernetes & DB monitoring')
            ->assertJsonPath('offering.deliverables', 'Monthly SLA & incident report')
            ->assertJsonPath('offering.questions.0.is_required', true);

        // 7. SKU check, readiness gate, and active catalog feed (tenant-scoped)
        $this->actingAs($userA)->getJson(route('service_company.crm.offerings.checkSku', ['sku' => 'JERO-ENT-01']))
            ->assertOk()->assertJsonPath('available', false);
        $this->actingAs($userA)->getJson(route('service_company.crm.offerings.checkSku', ['sku' => 'JERO-ENT-01', 'exclude_id' => $offeringId]))
            ->assertOk()->assertJsonPath('available', true);
        $this->actingAs($userB)->getJson(route('service_company.crm.offerings.checkSku', ['sku' => 'JERO-ENT-01']))
            ->assertOk()->assertJsonPath('available', true);

        $this->actingAs($userA)->getJson(route('service_company.crm.offerings.checkReady', $offeringId))
            ->assertOk()->assertJsonPath('ready', true);
        $this->actingAs($userB)->getJson(route('service_company.crm.offerings.checkReady', $offeringId))
            ->assertOk()->assertJsonPath('ready', false);

        $this->actingAs($userA)->getJson(route('service_company.crm.offerings.activeCatalog'))
            ->assertOk()->assertJsonCount(2, 'data');
        $this->actingAs($userB)->getJson(route('service_company.crm.offerings.activeCatalog'))
            ->assertOk()->assertJsonCount(0, 'data');
    }
}
