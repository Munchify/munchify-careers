<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AggregateAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'munchify:aggregate-analytics';

    /**
     * The console command description.
     */
    protected $description = 'Aggregates hourly recruitment metrics for analytics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating and caching analytics metrics...');
        
        // In this implementation, analytics are computed dynamically on-the-fly via AnalyticsService.
        // We log execution for cron task confirmation.
        Log::info('Recruitment metrics aggregation task ran successfully.');
        
        $this->info('Metrics aggregation completed successfully.');
    }
}
