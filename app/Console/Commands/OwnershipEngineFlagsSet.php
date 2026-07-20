<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 — the "runtime configuration without code changes" mechanism.
 * Per Finding 7: this does NOT turn live processing on/off — that runs unconditionally,
 * stopped only by the kill switch (ownership-engine:kill-switch, a separate, dedicated,
 * harder-to-misuse command). This hierarchy is reserved for the not-yet-built
 * authoritative-cutover mechanism; setting it today has no live effect.
 */
class OwnershipEngineFlagsSet extends Command
{
    protected $signature = 'ownership-engine:flags:set
                           {--scope=global : global | company | job}
                           {--id= : company or job id (required for scope=company|job)}
                           {--value=inherit : on | off | inherit (inherit only valid for company/job scope)}';

    protected $description = 'Phase 8: set the (currently inert, reserved-for-cutover) authoritative flag at the global, company, or job level';

    public function handle(): int
    {
        $scope = $this->option('scope');
        $value = $this->option('value');
        $id = $this->option('id') !== null ? (int) $this->option('id') : null;

        if (!in_array($scope, ['global', 'company', 'job'], true)) {
            $this->error('--scope must be global, company, or job.');

            return self::FAILURE;
        }

        if ($scope === 'global') {
            if (!in_array($value, ['on', 'off'], true)) {
                $this->error('--value must be on or off for scope=global (global has no inherit state).');

                return self::FAILURE;
            }

            DB::table('ownership_engine_settings')->where('id', 1)->update([
                'globally_enabled' => $value === 'on',
                'updated_at' => now(),
            ]);

            $this->info('Global authoritative flag set to: '.strtoupper($value).' (no live effect yet — reserved for the future cutover mechanism, Finding 7).');

            return self::SUCCESS;
        }

        if ($id === null) {
            $this->error('--id is required for scope=company or scope=job.');

            return self::FAILURE;
        }

        if (!in_array($value, ['on', 'off', 'inherit'], true)) {
            $this->error('--value must be on, off, or inherit.');

            return self::FAILURE;
        }

        $column = 'ownership_engine_enabled';
        $newValue = match ($value) {
            'on' => true,
            'off' => false,
            'inherit' => null,
        };

        $table = $scope === 'company' ? 'companies' : 'jobs';
        $exists = DB::table($table)->where('id', $id)->exists();

        if (!$exists) {
            $this->error("No {$scope} with id {$id} found.");

            return self::FAILURE;
        }

        DB::table($table)->where('id', $id)->update([$column => $newValue]);

        $this->info(ucfirst($scope)." #{$id} authoritative override set to: ".strtoupper($value).' (no live effect yet — reserved for the future cutover mechanism, Finding 7).');

        return self::SUCCESS;
    }
}
