<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'offering_class')) {
                $table->string('offering_class', 20)->nullable()->default('service')->after('title');
            }
            if (! Schema::hasColumn('jobs', 'offering_type')) {
                $table->string('offering_type', 80)->nullable()->after('offering_class');
            }
            if (! Schema::hasColumn('jobs', 'product_url')) {
                $table->string('product_url', 500)->nullable()->after('offering_type');
            }
            if (! Schema::hasColumn('jobs', 'sku')) {
                $table->string('sku', 100)->nullable()->after('product_url');
            }
            if (! Schema::hasColumn('jobs', 'unit')) {
                $table->string('unit', 50)->nullable()->after('sku');
            }
            if (! Schema::hasColumn('jobs', 'is_unlimited_capacity')) {
                $table->boolean('is_unlimited_capacity')->default(false)->after('total_positions');
            }
            if (! Schema::hasColumn('jobs', 'pricing_model')) {
                $table->string('pricing_model', 60)->nullable()->after('salary_visibility');
            }
            if (! Schema::hasColumn('jobs', 'service_availability')) {
                $table->string('service_availability', 60)->nullable()->after('work_shifts');
            }
            if (! Schema::hasColumn('jobs', 'service_duration')) {
                $table->string('service_duration', 120)->nullable()->after('service_availability');
            }
            if (! Schema::hasColumn('jobs', 'implementation_timeline')) {
                $table->string('implementation_timeline', 120)->nullable()->after('service_duration');
            }
            if (! Schema::hasColumn('jobs', 'sla_details')) {
                $table->string('sla_details', 255)->nullable()->after('implementation_timeline');
            }
            if (! Schema::hasColumn('jobs', 'warranty')) {
                $table->string('warranty', 150)->nullable()->after('sla_details');
            }
            if (! Schema::hasColumn('jobs', 'brochure_path')) {
                $table->string('brochure_path', 500)->nullable()->after('warranty');
            }
            if (! Schema::hasColumn('jobs', 'validity_days')) {
                $table->string('validity_days', 20)->nullable()->after('brochure_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $cols = [
                'offering_class', 'offering_type', 'product_url', 'sku', 'unit',
                'is_unlimited_capacity', 'pricing_model', 'service_availability',
                'service_duration', 'implementation_timeline', 'sla_details',
                'warranty', 'brochure_path', 'validity_days',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('jobs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
