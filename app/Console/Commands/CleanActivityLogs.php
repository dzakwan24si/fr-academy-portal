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
    protected $signature = 'activity-log:clean {--days=120 : Rentang hari log yang disimpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up activity logs older than specified days (default 120 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Berhasil menghapus {$deletedCount} data activity log yang lebih tua dari {$days} hari (sebelum {$cutoffDate->format('Y-m-d H:i:s')}).");

        return Command::SUCCESS;
    }
}
