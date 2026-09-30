<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Establish high-precision FX decimal fields, monthly FX snapshots,
     * and commercial FX audit trails for JERO's global multi-currency ATS & marketplace.
     */
    public function up(): void
    {
        // 1. Upgrade precision of currencies table to DECIMAL(20,10)
        if (Schema::hasTable('currencies')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `currencies` MODIFY COLUMN `usd_based_rates` DECIMAL(20,10) NOT NULL DEFAULT 1.0000000000');
                DB::statement('ALTER TABLE `currencies` MODIFY COLUMN `inr_based_rates` DECIMAL(20,10) NOT NULL DEFAULT 1.0000000000');
            }
        }

        // 2. Upgrade precision of currency_exchange_rates table to DECIMAL(20,10)
        if (Schema::hasTable('currency_exchange_rates')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `currency_exchange_rates` MODIFY COLUMN `rate` DECIMAL(20,10) NOT NULL DEFAULT 1.0000000000');
            }
        }

        // 3. Create currency_snapshots table for monthly immutable reference rates
        if (! Schema::hasTable('currency_snapshots')) {
            Schema::create('currency_snapshots', function (Blueprint $table) {
                $table->id();
                $table->string('version', 20)->unique(); // e.g. '202609-001'
                $table->string('base_currency', 3)->default('USD');
                $table->json('rates_json');
                $table->string('rates_hash', 64);
                $table->timestamp('effective_from');
                $table->timestamp('effective_to')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('source', 50)->default('open_exchange_rates');
                $table->timestamps();

                $table->index(['is_active', 'effective_from'], 'idx_active_snapshot');
            });
        }

        // 4. Create commercial_fx_snapshots table for legally immutable placement & invoice rates
        if (! Schema::hasTable('commercial_fx_snapshots')) {
            Schema::create('commercial_fx_snapshots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('entity_type', 50); // 'job_application', 'bench_place', 'invoice'
                $table->unsignedBigInteger('entity_id');
                $table->enum('contract_fx_type', ['REFERENCE_SNAPSHOT', 'CONTRACTUAL_FIXED', 'CORRIDOR_PEGGED'])->default('REFERENCE_SNAPSHOT');
                $table->decimal('contract_fx_agreed_rate', 20, 10)->nullable();
                $table->text('contract_fx_clause')->nullable();
                $table->date('contract_fx_effective_from')->nullable();
                $table->date('contract_fx_effective_to')->nullable();
                $table->string('reference_snapshot_version', 20)->nullable();
                $table->enum('tax_treatment', ['DOMESTIC_TAXED', 'EXPORT_ZERO_RATED', 'REVERSE_CHARGE', 'EXEMPT'])->default('DOMESTIC_TAXED');
                $table->string('tax_currency', 3)->default('USD');
                $table->decimal('tax_rate_percentage', 5, 2)->default(0.00);
                $table->decimal('tax_amount', 20, 6)->default(0.00);
                $table->timestamps();

                $table->index(['company_id', 'entity_type', 'entity_id'], 'idx_company_entity_commercial');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_fx_snapshots');
        Schema::dropIfExists('currency_snapshots');
    }
};
