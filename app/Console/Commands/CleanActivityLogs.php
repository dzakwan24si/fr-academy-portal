<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:clean {--days= : Rentang hari log yang disimpan} {--limit=1000 : Batas maksimal data log yang disimpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up activity logs to maintain max limit (default 1000) or older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = 0;

        if ($this->option('days')) {
            $days = (int) $this->option('days');
            $cutoffDate = now()->subDays($days);
            $deletedCount += ActivityLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("Berhasil menghapus data activity log yang lebih tua dari {$days} hari.");
        }

        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : ActivityLog::MAX_LOGS;
        if ($limit > 0) {
            $pruned = ActivityLog::pruneOldLogs($limit);
            $deletedCount += $pruned;
            if ($pruned > 0) {
                $this->info("Berhasil memangkas {$pruned} data activity log agar tetap maksimal {$limit} data.");
            } else {
                $this->info("Data activity log sudah sesuai batas maksimal ({$limit} data).");
            }
        }

        return Command::SUCCESS;
    }
}
