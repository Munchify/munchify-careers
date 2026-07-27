<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckJobDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'munchify:check-job-deadlines';

    /**
     * The console command description.
     */
    protected $description = 'Closes published jobs that have passed their application deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking job listing application deadlines...');

        $expiredJobs = JobListing::published()
            ->whereNotNull('application_deadline')
            ->where('application_deadline', '<', Carbon::today())
            ->get();

        if ($expiredJobs->isEmpty()) {
            $this->info('No expired jobs found.');
            return;
        }

        $this->info("Found {$expiredJobs->count()} expired job(s). Closing now...");

        foreach ($expiredJobs as $job) {
            $job->update([
                'status' => 'closed',
                'closed_at' => Carbon::now(),
            ]);

            AuditLog::log(
                actorId: 1, // System admin
                action: 'job_closed_by_deadline',
                entityType: JobListing::class,
                entityId: $job->id,
                details: ['title' => $job->title, 'reason' => 'Application deadline passed.']
            );

            $this->info("Closed job: {$job->title} (Deadline was: {$job->application_deadline->format('Y-m-d')})");
        }

        $this->info('Deadline check completed.');
    }
}
