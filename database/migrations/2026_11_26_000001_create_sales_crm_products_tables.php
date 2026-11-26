<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create Sales CRM product_categories, product_subcategories, and products tables.
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('name', 150);
                $table->string('slug', 160);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->unique(['company_id', 'slug'], 'uq_prod_cat_company_slug');
            });
        }

        if (! Schema::hasTable('product_subcategories')) {
            Schema::create('product_subcategories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedBigInteger('category_id');
                $table->string('name', 150);
                $table->string('slug', 160);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->foreign('category_id', 'fk_prod_subcat_category')
                    ->references('id')
                    ->on('product_categories')
                    ->onDelete('cascade');
                $table->index(['category_id', 'company_id'], 'idx_prod_subcat_cat_comp');
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();

                // 1. Basic Product Information
                $table->string('name', 255);
                $table->string('sku', 100);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('subcategory_id')->nullable();
                $table->enum('product_type', ['physical', 'digital', 'service'])->default('physical');
                $table->string('brand', 150)->nullable();
                $table->longText('description')->nullable();
                $table->string('image_path', 500)->nullable();

                // 2. Pricing Information
                $table->string('currency', 10)->default('INR');
                $table->decimal('purchase_price', 15, 2)->default(0.00);
                $table->decimal('selling_price', 15, 2)->default(0.00);
                $table->decimal('mrp', 15, 2)->nullable();
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('discount_value', 15, 2)->default(0.00);
                $table->decimal('tax_percentage', 5, 2)->default(0.00);
                $table->string('hsn_sac_code', 30)->nullable();
                $table->decimal('final_selling_price', 15, 2)->default(0.00);
                $table->decimal('profit_margin', 15, 2)->default(0.00);

                // 3. Inventory Information
                $table->boolean('track_inventory')->default(true);
                $table->unsignedInteger('opening_stock')->default(0);
                $table->unsignedInteger('available_quantity')->default(0);
                $table->unsignedInteger('min_stock_alert')->default(0);
                $table->string('unit', 50)->default('piece');
                $table->string('warehouse_location', 150)->nullable();
                $table->enum('stock_status', ['in_stock', 'low_stock', 'out_of_stock', 'not_applicable'])->default('in_stock');

                // 4. Product Specifications
                $table->string('model_number', 100)->nullable();
                $table->string('barcode', 100)->nullable();
                $table->string('warranty_period', 100)->nullable();
                $table->string('weight', 60)->nullable();
                $table->string('dimensions', 120)->nullable();
                $table->text('features')->nullable();
                $table->json('specifications')->nullable();
                $table->json('tags')->nullable();

                // 5. Sales Configuration
                $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
                $table->enum('visibility', ['public', 'internal', 'hidden'])->default('public');
                $table->boolean('is_featured')->default(false);
                $table->enum('availability', ['available', 'pre_order', 'discontinued'])->default('available');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['company_id', 'sku'], 'uq_products_company_sku');
                $table->index(['company_id', 'status'], 'idx_products_company_status');
                $table->index(['company_id', 'product_type'], 'idx_products_company_type');
                $table->index(['company_id', 'category_id'], 'idx_products_company_cat');

                $table->foreign('category_id', 'fk_products_category')
                    ->references('id')
                    ->on('product_categories')
                    ->onDelete('set null');
                $table->foreign('subcategory_id', 'fk_products_subcategory')
                    ->references('id')
                    ->on('product_subcategories')
                    ->onDelete('set null');
            });
        }

        // Seed default global CRM product categories & subcategories if empty
        if (DB::table('product_categories')->count() === 0) {
            $now = now();
            $seed = [
                'Software & SaaS' => ['CRM & Sales Tools', 'Cloud Subscriptions', 'Enterprise Licenses', 'API & Integrations'],
                'IT Hardware' => ['Laptops & Workstations', 'Networking Equipment', 'Servers & Storage', 'Peripherals & Accessories'],
                'Professional Services' => ['Consulting & Advisory', 'Implementation & Onboarding', 'Support & Maintenance', 'Training & Workshops'],
                'Digital Assets' => ['E-Books & Guides', 'Templates & Kits', 'Media & Content Packs'],
                'Office & Industrial Supplies' => ['Office Equipment', 'Packaging Materials', 'Consumables'],
            ];

            foreach ($seed as $catName => $subNames) {
                $catSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($catName, '-')));
                $catId = DB::table('product_categories')->insertGetId([
                    'company_id' => null,
                    'name' => $catName,
                    'slug' => $catSlug,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($subNames as $subName) {
                    $subSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($subName, '-')));
                    DB::table('product_subcategories')->insert([
                        'company_id' => null,
                        'category_id' => $catId,
                        'name' => $subName,
                        'slug' => $subSlug,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_subcategories');
        Schema::dropIfExists('product_categories');
    }
};
