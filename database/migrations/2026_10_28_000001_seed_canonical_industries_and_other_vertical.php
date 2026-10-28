<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Seed Canonical Industries and Universal 'Other' Vertical.
 *
 * INTENT:
 * 1. Seeds canonical IDs 1001-1014 (Modern 2026 Tech & Digital Economy Verticals) into `td_industries`.
 * 2. Seeds canonical ID 147 ('Other / Diversified Services') into `td_industries`.
 * 3. Migrates legacy clients with industry=0 or NULL to canonical ID 147.
 * 4. Ensures zero ind_name = NULL records during Elasticsearch CQRS projections.
 *
 * TENANCY NOTE: Global master reference catalog (td_industries) shared across all tenants.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure primary key on td_industries (ind_id)
        if (Schema::hasTable('td_industries')) {
            $hasPk = DB::select("SHOW KEYS FROM td_industries WHERE Key_name = 'PRIMARY'");
            if (empty($hasPk)) {
                try {
                    DB::statement('ALTER TABLE td_industries ADD PRIMARY KEY (ind_id)');
                } catch (\Throwable $e) {
                    // Ignore if already set
                }
            }
        }

        // 2. Canonical Industries Dataset
        $canonicalRecords = [
            // Universal 'Other' Vertical (Canonical ID 147)
            [
                'ind_id' => 147,
                'ind_name' => 'Other / Diversified Services',
                'status' => 'Y',
            ],

            // Modern 2026 Tech & Digital Economy Verticals (Synthetic IDs 1001-1014)
            [
                'ind_id' => 1001,
                'ind_name' => 'Artificial Intelligence & Machine Learning (AI/ML)',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1002,
                'ind_name' => 'FinTech, Payments & Digital Banking',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1003,
                'ind_name' => 'E-Commerce & Direct-to-Consumer (D2C)',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1004,
                'ind_name' => 'Cybersecurity & Data Privacy',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1005,
                'ind_name' => 'Cloud Computing, DevOps & Infrastructure',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1006,
                'ind_name' => 'HealthTech & Digital Health',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1007,
                'ind_name' => 'EdTech & Digital Learning',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1008,
                'ind_name' => 'CleanTech, ClimateTech & Renewable Energy',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1009,
                'ind_name' => 'Gaming, Esports & Interactive Entertainment',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1010,
                'ind_name' => 'PropTech & Real Estate Technology',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1011,
                'ind_name' => 'AgriTech & Smart Farming',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1012,
                'ind_name' => 'HRTech & Talent Acquisition Platforms',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1013,
                'ind_name' => 'LegalTech & Regulatory Technology (RegTech)',
                'status' => 'Y',
            ],
            [
                'ind_id' => 1014,
                'ind_name' => 'SpaceTech, Aerospace & Autonomous Systems',
                'status' => 'Y',
            ],
        ];

        foreach ($canonicalRecords as $record) {
            $existing = DB::table('td_industries')->where('ind_id', $record['ind_id'])->first();
            if ($existing) {
                DB::table('td_industries')
                    ->where('ind_id', $record['ind_id'])
                    ->update([
                        'ind_name' => $record['ind_name'],
                        'status' => 'Y',
                        'modified' => now(),
                    ]);
            } else {
                DB::table('td_industries')->insert([
                    'ind_id' => $record['ind_id'],
                    'ind_name' => $record['ind_name'],
                    'status' => 'Y',
                    'created' => now(),
                    'modified' => now(),
                ]);
            }
        }

        // 3. Migrate legacy clients with industry = 0 or NULL to canonical ID 147 ('Other / Diversified Services')
        if (Schema::hasTable('clients')) {
            DB::table('clients')
                ->where(function ($query) {
                    $query->where('industry', 0)
                        ->orWhereNull('industry');
                })
                ->update(['industry' => 147]);
        }

        // 4. Invalidate global taxonomy manifest cache
        try {
            cache()->forget('global:dataset:industry_taxonomy:v2');
            cache()->forget('global_td_industries_all');
            cache()->forget('global_td_industries_master');
            if (class_exists(\App\Services\IndustryTaxonomyCompiler::class)) {
                \App\Services\IndustryTaxonomyCompiler::compile();
            }
        } catch (\Throwable $e) {
            // Non-blocking cache flush
        }
    }

    public function down(): void
    {
        // Reversible cleanup of seeded canonical IDs
        if (Schema::hasTable('td_industries')) {
            DB::table('td_industries')
                ->whereIn('ind_id', [147, 1001, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013, 1014])
                ->delete();
        }
    }
};
