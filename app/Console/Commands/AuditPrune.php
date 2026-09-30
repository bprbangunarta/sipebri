<?php

namespace App\Console\Commands;

use App\Audit\Audit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditPrune extends Command
{
    protected $signature = 'audit:prune {--dry-run : Only report what would be removed}';

    protected $description = 'Remove audit entries older than the retention period, keeping the hash chain verifiable';

    public function handle(): int
    {
        $years = (int) config('security.audit_retention_years');

        if ($years < 1) {
            $this->error('AUDIT_RETENTION_YEARS must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = now()->subYears($years)->format('Y-m-d H:i:s.u');
        $old = DB::table('audit_logs')->where('created_at', '<', $cutoff);
        $count = (clone $old)->count();

        if ($count === 0) {
            $this->info("Nothing older than {$years} years.");

            return self::SUCCESS;
        }

        // Entries are written in time order, so the old ones are always the oldest ids.
        $last = (clone $old)->orderByDesc('id')->first(['id', 'hash']);

        if ($this->option('dry-run')) {
            $this->info("Would remove {$count} entries up to #{$last->id} (older than {$years} years).");

            return self::SUCCESS;
        }

        DB::transaction(function () use ($last, $count, $cutoff): void {
            DB::table('audit_logs')->where('id', '<=', $last->id)->delete();
            DB::table('audit_anchors')->insert([
                'pruned_through_id' => $last->id, 'pruned_count' => $count, 'last_hash' => $last->hash, 'cutoff' => substr($cutoff, 0, 19),
            ]);
        });

        Audit::record('audit_logs.pruned', 'audit_logs', 'pruned', context: ['removed' => $count, 'through_id' => $last->id, 'last_hash' => $last->hash, 'older_than_years' => $years], label: 'Audit retention');
        $this->info("Removed {$count} entries older than {$years} years.");

        return self::SUCCESS;
    }
}
