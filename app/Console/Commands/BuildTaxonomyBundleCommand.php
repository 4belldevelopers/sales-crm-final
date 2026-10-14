<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BuildTaxonomyBundleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taxonomy:build-bundle {--dry-run : Run without writing files to disk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles canonical taxonomy into high-speed static JSON, Brotli, and Gzip bundles for Client-First 0% DB reads.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);
        $this->info("Starting JERO Taxonomy V2 Static Bundle Compilation...");

        $dryRun = $this->option('dry-run');
        $outputDir = public_path('designations');

        if (!$dryRun && !File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // 1. Fetch Active Canonical Designations
        $designations = DB::table('taxonomy_designations as d')
            ->join('taxonomy_departments as dept', 'd.department_id', '=', 'dept.id')
            ->join('taxonomy_role_families as rf', 'd.role_family_id', '=', 'rf.id')
            ->leftJoin('taxonomy_jd_templates as jdt', 'd.jd_template_id', '=', 'jdt.id')
            ->where('d.status', 1)
            ->select([
                'd.id',
                'd.canonical_title as title',
                'd.identity_normalized_key as identity_key',
                'd.search_normalized_key as search_key',
                'd.seniority_level as seniority',
                'd.specialization',
                'd.quality_score',
                'd.department_id',
                'dept.name as department_name',
                'dept.code as department_code',
                'd.role_family_id',
                'rf.name as role_family_name',
                'rf.code as role_family_code',
                'd.jd_template_id',
                'jdt.role_summary',
                'jdt.key_responsibilities',
                'jdt.must_have_skills',
                'jdt.technical_competencies',
                'jdt.nice_to_have_skills',
                'jdt.tools_and_platforms'
            ])
            ->orderBy('d.canonical_title')
            ->get();

        $this->info(" - Fetched " . $designations->count() . " active canonical designations.");

        // 2. Fetch Active Commercial Aliases
        $aliases = DB::table('taxonomy_aliases')
            ->where('status', 1)
            ->select([
                'id',
                'alias_string as alias',
                'canonical_designation_id as target_id',
                'alias_type',
                'seniority_override',
                'specialization_override',
                'primary_technology_override',
                'search_normalized_key as search_key'
            ])
            ->get();

        $this->info(" - Fetched " . $aliases->count() . " active commercial aliases.");

        // 3. Fetch Departments and Role Families metadata
        $departments = DB::table('taxonomy_departments')->where('status', 1)->get();
        $roleFamilies = DB::table('taxonomy_role_families')->where('status', 1)->get();

        // 4. Construct Search Index & Inverted Prefix Trie
        $this->info(" - Building Inverted Prefix Trie for sub-millisecond RAM lookups...");
        $trie = [];
        $itemsMap = [];

        foreach ($designations as $d) {
            $desigId = (int) $d->id;
            $itemsMap[$desigId] = [
                'id' => $desigId,
                'title' => $d->title,
                'search_key' => $d->search_key,
                'seniority' => $d->seniority,
                'specialization' => $d->specialization,
                'dept' => $d->department_name,
                'dept_id' => (int) $d->department_id,
                'family' => $d->role_family_name,
                'family_id' => (int) $d->role_family_id,
                'template_id' => (int) $d->jd_template_id,
                'summary' => $d->role_summary,
                'responsibilities' => json_decode($d->key_responsibilities ?? '[]', true),
                'must_have_skills' => json_decode($d->must_have_skills ?? '[]', true),
                'nice_have_skills' => json_decode($d->nice_to_have_skills ?? '[]', true),
                'tools' => json_decode($d->tools_and_platforms ?? '[]', true),
            ];

            // Index words and prefixes
            $this->indexStringToTrie($d->title, $desigId, $trie);
        }

        $aliasMap = [];
        foreach ($aliases as $a) {
            $aliasMap[] = [
                'alias' => $a->alias,
                'target_id' => (int) $a->target_id,
                'type' => $a->alias_type,
                'seniority' => $a->seniority_override,
                'tech' => $a->primary_technology_override
            ];
            $this->indexStringToTrie($a->alias, (int) $a->target_id, $trie);
        }

        // 5. Build Core Bundle Payload
        $corePayload = [
            'taxonomy_version' => '2.0.0',
            'schema_version' => 2,
            'generated_at' => now()->toIso8601String(),
            'total_designations' => count($itemsMap),
            'total_aliases' => count($aliasMap),
            'items' => $itemsMap,
            'aliases' => $aliasMap,
            'departments' => $departments,
            'role_families' => $roleFamilies,
            'trie' => $trie
        ];

        $rawJson = json_encode($corePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $rawSize = strlen($rawJson);
        $this->info(" - Core JSON payload size: " . number_format($rawSize / 1024, 2) . " KB");

        if (!$dryRun) {
            $coreJsonPath = $outputDir . '/core-taxonomy.json';
            File::put($coreJsonPath, $rawJson);

            // Gzip Compression (Level 9)
            $gzipContent = gzencode($rawJson, 9);
            $gzipSize = strlen($gzipContent);
            File::put($outputDir . '/core-taxonomy.json.gz', $gzipContent);
            $this->info(" - Gzip bundle written: " . number_format($gzipSize / 1024, 2) . " KB (Budget: <= 480 KB)");

            // Brotli Compression (Level 11)
            $brotliSize = 0;
            if (function_exists('brotli_compress')) {
                $brotliContent = brotli_compress($rawJson, 11);
                $brotliSize = strlen($brotliContent);
                File::put($outputDir . '/core-taxonomy.json.br', $brotliContent);
                $this->info(" - Brotli bundle written: " . number_format($brotliSize / 1024, 2) . " KB (Budget: <= 350 KB)");
            } else {
                // Node.js / Bun fallback for Brotli if PHP brotli extension not loaded
                $tempInput = str_replace('\\', '/', storage_path('framework/temp_core_tax.json'));
                $tempOutput = str_replace('\\', '/', $outputDir . '/core-taxonomy.json.br');
                File::put($tempInput, $rawJson);
                $nodeScript = "const zlib = require('zlib'); const fs = require('fs'); const input = fs.readFileSync('{$tempInput}'); const compressed = zlib.brotliCompressSync(input, { params: { [zlib.constants.BROTLI_PARAM_QUALITY]: 11 } }); fs.writeFileSync('{$tempOutput}', compressed);";
                $escapedScript = str_replace('"', '\"', $nodeScript);
                shell_exec("node -e \"{$escapedScript}\"");
                @unlink($tempInput);
                if (file_exists($tempOutput)) {
                    $brotliSize = filesize($tempOutput);
                    $this->info(" - Brotli bundle written via Node fallback: " . number_format($brotliSize / 1024, 2) . " KB (Budget: <= 350 KB)");
                }
            }

            // 6. Release Manifest
            $contentHash = hash('sha256', $rawJson);
            $bundleHash = $brotliSize > 0 ? hash_file('sha256', $outputDir . '/core-taxonomy.json.br') : hash_file('sha256', $outputDir . '/core-taxonomy.json.gz');
            $manifest = [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'title' => 'JeroTaxonomyReleaseManifest',
                'taxonomy_version' => '2.0.0',
                'schema_version' => 2,
                'build_id' => 'build_' . date('Ymd_His'),
                'generated_at' => now()->toIso8601String(),
                'content_hash' => 'sha256:' . $contentHash,
                'bundle_hash' => 'sha256:' . $bundleHash,
                'minimum_client_schema' => 2,
                'minimum_supported_version' => '2.0.0',
                'breaking_change' => false,
                'manifest_metrics' => [
                    'canonical_designations' => count($itemsMap),
                    'commercial_aliases' => count($aliasMap),
                    'departments' => $departments->count(),
                    'role_families' => $roleFamilies->count(),
                    'raw_size_bytes' => $rawSize,
                    'brotli_size_bytes' => $brotliSize,
                    'gzip_size_bytes' => $gzipSize
                ]
            ];

            File::put($outputDir . '/taxonomy-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info(" - Release manifest written: taxonomy-manifest.json");

            // Update taxonomy_versions in DB
            DB::table('taxonomy_versions')->updateOrInsert(
                ['version' => '2.0.0'],
                [
                    'schema_version' => 2,
                    'build_id' => $manifest['build_id'],
                    'content_hash' => $manifest['content_hash'],
                    'bundle_hash' => $manifest['bundle_hash'],
                    'canonical_count' => count($itemsMap),
                    'alias_count' => count($aliasMap),
                    'published_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $this->info("✅ Static Bundle Compilation completed in {$durationMs} ms.");

        return Command::SUCCESS;
    }

    protected function indexStringToTrie(string $text, int $id, array &$trie): void
    {
        $normalized = preg_replace('/[^a-z0-9]/', '', strtolower($text));
        $len = strlen($normalized);

        // Index prefixes from 2 to 10 chars
        for ($i = 2; $i <= min($len, 10); $i++) {
            $prefix = substr($normalized, 0, $i);
            if (!isset($trie[$prefix])) {
                $trie[$prefix] = [];
            }
            if (!in_array($id, $trie[$prefix])) {
                $trie[$prefix][] = $id;
            }
        }

        // Also index individual words in the title
        $words = preg_split('/[\s\-_,\.\/]+/', strtolower($text));
        foreach ($words as $w) {
            $wClean = preg_replace('/[^a-z0-9]/', '', $w);
            $wLen = strlen($wClean);
            for ($j = 2; $j <= min($wLen, 8); $j++) {
                $prefix = substr($wClean, 0, $j);
                if (!isset($trie[$prefix])) {
                    $trie[$prefix] = [];
                }
                if (!in_array($id, $trie[$prefix])) {
                    $trie[$prefix][] = $id;
                }
            }
        }
    }
}
