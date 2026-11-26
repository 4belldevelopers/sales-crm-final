<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceCompanyProductTest extends TestCase
{
    use DatabaseTransactions;

    private function makeCompanyAndUser(string $suffix): array
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Test Sales Corp '.$suffix,
            'company_email' => 'corp_'.$suffix.'@example.test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'company_id' => $companyId,
            'name' => 'Sales User '.$suffix,
            'email' => 'sales_'.$suffix.'@example.test',
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

    public function test_product_creation_pricing_gst_and_draft_isolation(): void
    {
        $this->withoutMiddleware(EnsureOneDeviceLogin::class);
        [$companyA, $userA] = $this->makeCompanyAndUser('a_'.uniqid());
        [$companyB, $userB] = $this->makeCompanyAndUser('b_'.uniqid());

        $cat = ProductCategory::first();
        $this->assertNotNull($cat, 'Seeded product_categories should exist.');

        // 1. Create Active Product with 10% discount and 18% GST
        // Selling: 1000 -> -10% = 900 -> +18% GST = 1062.00; Purchase 600 -> Margin = 300.00
        $res = $this->actingAs($userA)->postJson(route('service_company.products.store'), [
            'name' => 'Enterprise CRM Suite',
            'sku' => 'CRM-TEST-001',
            'category_id' => $cat->id,
            'product_type' => 'physical',
            'currency' => 'INR',
            'purchase_price' => 600,
            'selling_price' => 1000,
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'tax_percentage' => 18,
            'opening_stock' => 25,
            'available_quantity' => 25,
            'min_stock_alert' => 5,
            'unit' => 'piece',
            'status' => 'active',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('product.sku', 'CRM-TEST-001');

        $productId = (int) $res->json('product.id');
        $product = Product::withoutGlobalScopes()->findOrFail($productId);
        $this->assertEquals(1062.00, (float) $product->final_selling_price);
        $this->assertEquals(300.00, (float) $product->profit_margin);
        $this->assertEquals('active', $product->status);

        // 2. Duplicate SKU prevention within same company (Company A)
        $dupRes = $this->actingAs($userA)->postJson(route('service_company.products.store'), [
            'name' => 'Duplicate SKU Product',
            'sku' => 'CRM-TEST-001',
            'category_id' => $cat->id,
            'product_type' => 'physical',
            'currency' => 'INR',
            'selling_price' => 500,
            'status' => 'active',
        ]);
        $dupRes->assertStatus(422)->assertJsonValidationErrors(['sku']);

        // 3. Same SKU allowed in Company B (multi-tenant isolation)
        $otherCompanyRes = $this->actingAs($userB)->postJson(route('service_company.products.store'), [
            'name' => 'Company B Product',
            'sku' => 'CRM-TEST-001',
            'category_id' => $cat->id,
            'product_type' => 'digital',
            'currency' => 'USD',
            'selling_price' => 200,
            'track_inventory' => false,
            'status' => 'draft',
        ]);
        $otherCompanyRes->assertStatus(201);
        $this->assertEquals('draft', $otherCompanyRes->json('product.status'));
        $this->assertEquals('not_applicable', $otherCompanyRes->json('product.stock_status'));

        // 4. Negative stock rejected
        $negStock = $this->actingAs($userA)->postJson(route('service_company.products.store'), [
            'name' => 'Bad Stock',
            'sku' => 'CRM-NEG-001',
            'category_id' => $cat->id,
            'product_type' => 'physical',
            'currency' => 'INR',
            'selling_price' => 100,
            'opening_stock' => -5,
            'status' => 'active',
        ]);
        $negStock->assertStatus(422)->assertJsonValidationErrors(['opening_stock']);

        // 5. Company B cannot edit Company A's product
        $crossTenantEdit = $this->actingAs($userB)->postJson(route('service_company.products.postUpdate', $productId), [
            'name' => 'Hacked Name',
            'sku' => 'UNIQUE-TO-B-999',
            'category_id' => $cat->id,
            'product_type' => 'physical',
            'currency' => 'INR',
            'selling_price' => 100,
            'status' => 'active',
        ]);
        $crossTenantEdit->assertStatus(404);

        // 6. Company A updates product with fixed discount & sets inactive
        $updateRes = $this->actingAs($userA)->postJson(route('service_company.products.postUpdate', $productId), [
            'name' => 'Enterprise CRM Suite v2',
            'sku' => 'CRM-TEST-001',
            'category_id' => $cat->id,
            'product_type' => 'physical',
            'currency' => 'INR',
            'purchase_price' => 600,
            'selling_price' => 1000,
            'discount_type' => 'fixed',
            'discount_value' => 200,
            'tax_percentage' => 18,
            'opening_stock' => 25,
            'available_quantity' => 25,
            'status' => 'inactive',
        ]);
        $updateRes->assertStatus(200);
        $updated = Product::withoutGlobalScopes()->findOrFail($productId);
        // 1000 - 200 = 800; +18% = 944.00; margin = 800 - 600 = 200.00
        $this->assertEquals(944.00, (float) $updated->final_selling_price);
        $this->assertEquals(200.00, (float) $updated->profit_margin);

        // 7. Inactive products excluded from activeCatalog for sales transactions
        $catalogRes = $this->actingAs($userA)->getJson(route('service_company.products.activeCatalog'));
        $catalogRes->assertStatus(200);
        $ids = collect($catalogRes->json('data'))->pluck('id')->all();
        $this->assertNotContains($productId, $ids);
    }

    public function test_product_image_upload_and_create_view_render(): void
    {
        $this->withoutMiddleware(EnsureOneDeviceLogin::class);
        $_SERVER['REQUEST_URI'] = '/service_company/products/create';
        [$companyA, $userA] = $this->makeCompanyAndUser('img_'.uniqid());
        $cat = ProductCategory::first();

        $viewRes = $this->actingAs($userA)->get(route('service_company.products.create'));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Add New Product');

        if (function_exists('imagecreatetruecolor')) {
            $img = UploadedFile::fake()->image('product.png', 200, 200);
            $res = $this->actingAs($userA)->postJson(route('service_company.products.store'), [
                'name' => 'Product With Image',
                'sku' => 'IMG-SKU-001',
                'category_id' => $cat->id,
                'product_type' => 'physical',
                'currency' => 'INR',
                'selling_price' => 500,
                'image' => $img,
                'status' => 'active',
            ]);
            $res->assertStatus(201);
            $path = $res->json('product.image_path');
            $this->assertNotEmpty($path);
            $fullPath = public_path($path);
            $this->assertFileExists($fullPath);
            File::deleteDirectory(public_path('user-uploads/products/'.$companyA));
        }
    }
}
