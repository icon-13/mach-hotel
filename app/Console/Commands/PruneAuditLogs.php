<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;
use Carbon\Carbon;

class PruneAuditLogs extends Command
{
    protected $signature = 'audit:prune {days=90}';
    protected $description = 'Delete audit logs older than N days';

    public function handle(): int
    {
        $days = (int)$this->argument('days');
        $cutoff = Carbon::now()->subDays(max(1, $days));

        $count = AuditLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} audit logs older than {$days} days.");
        return self::SUCCESS;
    }
}
